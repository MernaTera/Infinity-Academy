<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Reports\Report;
use App\Models\Academic\CourseInstance;
use App\Services\NotificationService;
use Carbon\Carbon;

class CheckReportDeadlines extends Command
{
    protected $signature = 'reports:check-deadlines';
    protected $description = 'Check report deadlines and send reminders/alerts to teachers';

    public function handle()
    {
        $this->info('Checking report deadlines...');
        $this->checkSubmitDeadlines();
        $this->checkSendDeadlines();
        $this->info('Done.');
    }


    private function checkSubmitDeadlines()
    {
        $instances = CourseInstance::with([
            'courseTemplate', 'teacher.employee',
            'enrollments.report', 'enrollments.student',
        ])
        ->where('status', 'Completed')
        ->whereNotNull('end_date')
        ->get();

        foreach ($instances as $inst) {
            $deadline = Carbon::parse($inst->end_date)->addDays(3);
            $daysLeft = (int) now()->startOfDay()->diffInDays($deadline->startOfDay(), false);

            $teacherEmpId = $inst->teacher?->employee_id;
            $courseName   = $inst->courseTemplate?->name ?? 'course';
            if (!$teacherEmpId) continue;

            foreach ($inst->enrollments as $enr) {
                $status = $enr->report?->status;

                if (in_array($status, ['Submitted','Approved','Sent'])) continue;

                $studentName = $enr->student?->full_name ?? 'a student';
                $entityId    = $enr->enrollment_id;

                if ($daysLeft === 1) {
                    $this->notifyOnce($teacherEmpId,
                        '📝 Report Due Tomorrow',
                        "Report for {$studentName} in {$courseName} is due tomorrow.",
                        'report_submit_soon', $entityId
                    );
                } elseif ($daysLeft === 0) {
                    $this->notifyOnce($teacherEmpId,
                        '⚠️ Report Due Today',
                        "Submit report for {$studentName} before end of day.",
                        'report_submit_today', $entityId
                    );
                } elseif ($daysLeft < 0) {
                    $late = abs($daysLeft);
                    $this->notifyOnce($teacherEmpId,
                        '🚨 Report Overdue',
                        "Report for {$studentName} is {$late} day" . ($late>1?'s':'') . " overdue.",
                        'report_submit_overdue', $entityId
                    );
                }
            }
        }
    }

    private function checkSendDeadlines()
    {
        $reports = Report::with(['teacher.employee', 'enrollment.student'])
            ->where('status', 'Approved')
            ->whereNotNull('approved_at')
            ->get();

        foreach ($reports as $report) {
            $approvedAt   = Carbon::parse($report->approved_at);
            $deadline     = $approvedAt->copy()->addDay(); 
            $hoursLeft    = (int) now()->diffInHours($deadline, false);

            $teacherEmpId = $report->teacher?->employee_id;
            $studentName  = $report->enrollment?->student?->full_name ?? 'a student';
            if (!$teacherEmpId) continue;

            if ($hoursLeft > 0 && $hoursLeft <= 12) {
                $this->notifyOnce($teacherEmpId,
                    '⏳ Send Report — Deadline Approaching',
                    "You have {$hoursLeft} hour" . ($hoursLeft>1?'s':'') . " left to send {$studentName}'s report.",
                    'report_send_soon', $report->report_id
                );
            } elseif ($hoursLeft <= 0) {
                $lateHours = abs($hoursLeft);
                $this->notifyOnce($teacherEmpId,
                    '🚨 Send Report Overdue',
                    "You should have sent {$studentName}'s report {$lateHours} hour" . ($lateHours>1?'s':'') . " ago. Send it immediately.",
                    'report_send_overdue', $report->report_id
                );
            }
        }
    }


    private function notifyOnce(int $employeeId, string $title, string $message, string $type, int $entityId): void
    {
        $exists = DB::table('user_notification')
            ->where('employee_id', $employeeId)
            ->where('related_entity_type', $type)
            ->where('related_entity_id', $entityId)
            ->whereDate('created_at', now()->toDateString())
            ->exists();

        if (!$exists) {
            NotificationService::send($employeeId, $title, $message, $type, $entityId);
        }
    }
}