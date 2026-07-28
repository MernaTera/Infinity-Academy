<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Events\NotificationCreated;

class NotificationService
{
    /**
     * Send a notification to an employee (DB + real-time broadcast)
     * 
     * @param int $employeeId
     * @param string $title
     * @param string $message
     * @param string|null $entityType
     * @param int|null $entityId
     * @param array $metadata Optional extra details for rich notifications
     * @param string $priority normal | high (high = persistent toast)
     */
    public static function send(
        int $employeeId,
        string $title,
        string $message,
        ?string $entityType = null,
        ?int $entityId = null,
        array $metadata = [],
        string $priority = 'normal'
    ): int {
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

        $url = self::computeUrl($employeeId, $entityType);

        try {
            broadcast(new NotificationCreated([
                'id'          => $id,
                'title'       => $title,
                'message'     => $message,
                'entity_type' => $entityType,
                'entity_id'   => $entityId,
                'url'         => $url,
                'metadata'    => $metadata,
                'priority'    => $priority,
                'created_at'  => now()->toIso8601String(),
            ], $employeeId));
        } catch (\Exception $e) {
            \Log::warning('Broadcast failed: ' . $e->getMessage());
        }

        return $id;
    }

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

            'report_submitted' =>
                $role === 'Admin' ? url('/admin/reports') : url('/teacher/reports'),
            'report_approved', 'report_rejected' =>
                url('/teacher/reports'),

            'course_instance' => url('/teacher/courses'),
            'waiting_list'    => url('/student-care/waiting-list'),
            'report_submit_soon', 'report_submit_today', 'report_submit_overdue',
            'report_send_soon', 'report_send_overdue' => url('/teacher/reports'),
            default => '#',
        };
    }
}