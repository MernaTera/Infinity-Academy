<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Events\NotificationCreated;

class NotificationService
{
    /**
     * Send a notification to an employee (DB + real-time broadcast)
     */
    public static function send(
        int $employeeId,
        string $title,
        string $message,
        ?string $entityType = null,
        ?int $entityId = null
    ): int {
        // Insert into DB
        $id = DB::table('user_notification')->insertGetId([
            'employee_id'         => $employeeId,
            'title'               => $title,
            'message'             => $message,
            'related_entity_type' => $entityType,
            'related_entity_id'   => $entityId,
            'is_read'             => false,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        // Compute URL for the notification
        $url = self::computeUrl($employeeId, $entityType);

        // Broadcast in real-time
        try {
            broadcast(new NotificationCreated([
                'id'          => $id,
                'title'       => $title,
                'message'     => $message,
                'entity_type' => $entityType,
                'entity_id'   => $entityId,
                'url'         => $url,
                'created_at'  => now()->toIso8601String(),
            ], $employeeId));
        } catch (\Exception $e) {
            // Silently fail if broadcast unavailable — notification still in DB
            \Log::warning('Broadcast failed: ' . $e->getMessage());
        }

        return $id;
    }

    /**
     * Compute the URL for a notification based on entity type and employee role
     */
    private static function computeUrl(int $employeeId, ?string $entityType): string
    {
        $role = DB::table('employee')
            ->join('users', 'employee.user_id', '=', 'users.id')
            ->join('role', 'users.role_id', '=', 'role.role_id')
            ->where('employee.employee_id', $employeeId)
            ->value('role.role_name');

        return match ($entityType ?? '') {
            'refund_request', 'refund_approved', 'refund_rejected' =>
                $role === 'Admin' ? url('/admin/refunds') : url('/refunds'),

            'installment_request', 'installment_approved', 'installment_rejected' =>
                $role === 'Admin' ? url('/admin/installments') : url('/leads'),

            'course_instance' => url('/teacher/courses'),
            'report_approved', 'report_rejected' => url('/teacher/reports'),
            'waiting_list' => url('/student-care/waiting-list'),

            default => '#',
        };
    }
}