<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Interfaces\LeadRepositoryInterface;
use App\Repositories\LeadRepository;
use App\Observers\AuditObserver;

// ── Models to audit ──
use App\Models\Leads\Lead;
use App\Models\Student\Student;
use App\Models\Enrollment\Enrollment;
use App\Models\Finance\FinancialTransaction;
use App\Models\Finance\InstallmentSchedule;
use App\Models\Finance\InstallmentApprovalLog;
use App\Models\Finance\RefundRequest;
use App\Models\Finance\Offer;
use App\Models\Finance\PaymentPlan;
use App\Models\Finance\PrivateBundle;
use App\Models\Academic\CourseTemplate;
use App\Models\Academic\Level;
use App\Models\Academic\Sublevel;
use App\Models\Academic\CourseInstance;
use App\Models\Academic\Patch;
use App\Models\Academic\TimeSlot;
use App\Models\Academic\BreakSlot;
use App\Models\HR\Employee;
use App\Models\HR\Teacher;
use App\Models\Reports\Report;
use App\Models\Enrollment\RestrictionLog;
use App\Models\Auth\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

use App\Http\Controllers\Admin\PaymentPolicyController;
use App\Http\Controllers\Admin\InstallmentApprovalController;
use App\Http\Controllers\RegistrationController;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            LeadRepositoryInterface::class,
            LeadRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ── Blade directive ──
        \Illuminate\Support\Facades\Blade::if('cando', function (string $permission) {
            return auth()->check() && auth()->user()->canDo($permission);
        });

        // ── Audit Observers ──
        // Each model listed here will have Create/Update/Delete logged automatically

        Lead::observe(AuditObserver::class);
        Student::observe(AuditObserver::class);
        Enrollment::observe(AuditObserver::class);
        FinancialTransaction::observe(AuditObserver::class);
        InstallmentSchedule::observe(AuditObserver::class);
        InstallmentApprovalLog::observe(AuditObserver::class);
        RefundRequest::observe(AuditObserver::class);
        Offer::observe(AuditObserver::class);
        PaymentPlan::observe(AuditObserver::class);
        PrivateBundle::observe(AuditObserver::class);
        CourseTemplate::observe(AuditObserver::class);
        Level::observe(AuditObserver::class);
        Sublevel::observe(AuditObserver::class);
        CourseInstance::observe(AuditObserver::class);
        Patch::observe(AuditObserver::class);
        TimeSlot::observe(AuditObserver::class);
        BreakSlot::observe(AuditObserver::class);
        Employee::observe(AuditObserver::class);
        Teacher::observe(AuditObserver::class);
        Report::observe(AuditObserver::class);
        RestrictionLog::observe(AuditObserver::class);
        User::observe(AuditObserver::class);


        View::composer(['layouts.*', 'admin.layouts.*', 'teacher.layouts.*', 'student-care.layouts.*'], function ($view) {
            if (auth()->check()) {

                $employeeId = Employee::where('user_id', auth()->id())
                    ->value('employee_id');

                $userRole = auth()->user()->role?->role_name ?? '';

                $navNotifications = DB::table('user_notification')
                    ->where('employee_id', $employeeId)
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->get()
                    ->map(function ($notif) use ($userRole) {
                        $type = $notif->related_entity_type ?? '';
                        
                        $notif->url = match (true) {
                            in_array($type, ['refund_request', 'refund_approved', 'refund_rejected']) => 
                                $userRole === 'Admin' ? url('/admin/refunds') : url('/refunds'),
                            
                            in_array($type, ['installment_request', 'installment_approved', 'installment_rejected']) => 
                                $userRole === 'Admin' ? url('/admin/installments') : url('/leads'),
                            
                            $type === 'course_instance' => url('/teacher/courses'),
                            
                            in_array($type, ['report_approved', 'report_rejected']) => url('/teacher/reports'),
                            
                            $type === 'waiting_list' => url('/student-care/waiting-list'),
                            
                            default => '#',
                        };
                        
                        return $notif;
                    });

                $navUnreadCount = DB::table('user_notification')
                    ->where('employee_id', $employeeId)
                    ->where('is_read', false)
                    ->count();

                $view->with([
                    'navNotifications' => $navNotifications,
                    'navUnreadCount'   => $navUnreadCount,
                    'navPrevUnread'    => session('prev_unread_count', 0),
                ]);

                session(['prev_unread_count' => $navUnreadCount]);
            }
        });
    }
}