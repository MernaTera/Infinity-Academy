<?php

namespace App\Services;

use App\Models\Leads\Lead;
use App\Models\Leads\LeadHistory;
use App\Models\Leads\LeadCallLog;
use App\Interfaces\LeadRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class LeadService
{
    protected $leadRepository;

    public function __construct(LeadRepositoryInterface $leadRepository)
    {
        $this->leadRepository = $leadRepository;
    }

    // ─────────────────────────────────────────
    // Helper — get current employee_id safely
    // ─────────────────────────────────────────
    private function currentEmployeeId(): int
    {
        $employee = Auth::user()->employee; // hasOne → single model
        if (!$employee) abort(403, 'No employee profile linked to this account.');
        return $employee->employee_id;
    }

    /*
    |--------------------------------------------------------------------------
    | Create Lead
    |--------------------------------------------------------------------------
    */
    public function createLead(array $data): Lead
    {
        $data['owner_cs_id'] = $this->currentEmployeeId();

        $lead = $this->leadRepository->create($data);

        $this->logHistory($lead, null, $lead->status, 'Lead Created');

        return $lead;
    }

    /*
    |--------------------------------------------------------------------------
    | Assign Lead
    |--------------------------------------------------------------------------
    */
    public function assignLead(int $leadId): Lead
    {
        $lead       = $this->leadRepository->find($leadId);
        $employeeId = $this->currentEmployeeId();
        $old        = $lead->status;

        $lead->assignTo($employeeId);

        $this->logHistory($lead, $old, $lead->status, "Assigned to CS #{$employeeId}");

        return $lead;
    }

    /*
    |--------------------------------------------------------------------------
    | Schedule Call
    |--------------------------------------------------------------------------
    */
    public function scheduleCall(int $leadId, $datetime): Lead
    {
        $lead = $this->leadRepository->find($leadId);
        $this->authorizeLead($lead);

        $lead->scheduleCall($datetime);

        LeadCallLog::create([
            'lead_id'       => $lead->lead_id,
            'cs_id'         => $this->currentEmployeeId(),
            'call_datetime' => now(),
            'outcome'       => 'Follow_Up_Scheduled',
            'notes'         => 'Call scheduled',
        ]);

        $this->logHistory($lead, $lead->status, 'Scheduled_Call', "Call scheduled for {$datetime}");

        return $lead;
    }

    /*
    |--------------------------------------------------------------------------
    | Call Again
    |--------------------------------------------------------------------------
    */
    public function markCallAgain(int $leadId, $datetime): Lead
    {
        $lead = $this->leadRepository->find($leadId);
        $this->authorizeLead($lead);

        $lead->markCallAgain($datetime);

        LeadCallLog::create([
            'lead_id'       => $lead->lead_id,
            'cs_id'         => $this->currentEmployeeId(),
            'call_datetime' => now(),
            'outcome'       => 'Call_Again',
            'notes'         => 'Call again scheduled',
        ]);

        $this->logHistory($lead, $lead->status, 'Call_Again', "Call again scheduled for {$datetime}");

        return $lead;
    }

    /*
    |--------------------------------------------------------------------------
    | Mark as Registered
    |--------------------------------------------------------------------------
    */
    public function markRegistered(int $leadId): Lead
    {
        $lead = $this->leadRepository->find($leadId);
        $this->authorizeLead($lead);
        $old = $lead->status;

        $lead->update(['status' => 'Registered']);

        $this->logHistory($lead, $old, 'Registered', 'Lead converted to student');

        return $lead;
    }

    /*
    |--------------------------------------------------------------------------
    | Not Interested
    |--------------------------------------------------------------------------
    */
    public function markNotInterested(int $leadId): Lead
    {
        $lead = $this->leadRepository->find($leadId);
        $this->authorizeLead($lead);
        $old = $lead->status;

        $lead->update(['status' => 'Not_Interested']);

        $this->logHistory($lead, $old, 'Not_Interested', 'Lead marked as not interested');

        return $lead;
    }

    /*
    |--------------------------------------------------------------------------
    | Archive Lead
    |--------------------------------------------------------------------------
    */
    public function archiveLead(int $leadId): Lead
    {
        $lead = $this->leadRepository->find($leadId);
        $this->authorizeLead($lead);
        $old = $lead->status;

        $lead->archive();

        $this->logHistory($lead, $old, 'Archived', 'Lead archived');

        return $lead;
    }

    /*
    |--------------------------------------------------------------------------
    | Auto Public Leads (4 days rule)
    |--------------------------------------------------------------------------
    */
    public function releaseExpiredLeads()
    {
        $leads = Lead::whereNotNull('owner_cs_id')
            ->where('updated_at', '<=', now()->subDays(4))
            ->get();

        foreach ($leads as $lead) {
            $lead->update(['owner_cs_id' => null]);
            $this->logHistory($lead, $lead->status, 'Waiting', 'Lead set public (4 days rule)');
        }

        return $leads;
    }

    /*
    |--------------------------------------------------------------------------
    | Auto Archive Leads (30 days rule)
    |--------------------------------------------------------------------------
    */
    public function archiveOldLeads()
    {
        $leads = Lead::where('updated_at', '<=', now()->subDays(30))
            ->whereNotIn('status', ['Registered', 'Archived'])
            ->get();

        foreach ($leads as $lead) {
            $old = $lead->status;
            $lead->update([
                'status'      => 'Archived',
                'is_active'   => false,
                'owner_cs_id' => null,
            ]);
            $this->logHistory($lead, $old, 'Archived', 'Lead auto archived (30 days rule)');
        }

        return $leads;
    }

    /*
    |--------------------------------------------------------------------------
    | History Logger
    |--------------------------------------------------------------------------
    */
    public function logHistory($lead, $old = null, $new = null, $note = null): void
    {
        LeadHistory::create([
            'lead_id'    => $lead->lead_id,
            'old_status' => $old,
            'new_status' => $new,
            'notes'      => $note,
            'changed_by' => $this->currentEmployeeId(),
            'changed_at' => now(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Authorization
    |--------------------------------------------------------------------------
    */
    private function authorizeLead($lead): void
    {
        $employeeId = $this->currentEmployeeId();
        if ($lead->owner_cs_id && $lead->owner_cs_id != $employeeId) {
            abort(403, 'You are not authorized to modify this lead.');
        }
    }
}