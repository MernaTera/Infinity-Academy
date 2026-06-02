<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessOutstandingStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-outstanding-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        InstallmentSchedule::where('status', 'Pending')
            ->where('due_date', '<', today())
            ->update(['status' => 'Overdue']);

        $this->info('Installments marked Overdue.');

        // $overdueEnrollmentIds = InstallmentSchedule::where('status', 'Overdue')
        //     ->pluck('enrollment_id')
        //     ->unique();

        // foreach ($overdueEnrollmentIds as $enrollmentId) {
        //     $enrollment = Enrollment::find($enrollmentId);
        //     if (!$enrollment || $enrollment->status === 'Restricted') continue;

        //     $enrollment->update([
        //         'status'             => 'Restricted',
        //         'restriction_flag'   => true,
        //         'restriction_reason' => 'OVERDUE_INSTALLMENT',
        //     ]);

        //     RestrictionLog::create([
        //         'enrollment_id' => $enrollmentId,
        //         'triggered_by'  => 'System',
        //         'reason'        => 'overdue_installment',
        //         'triggered_at'  => now(),
        //     ]);
        // }

        // $this->info('Enrollments restricted.');
    }
}
