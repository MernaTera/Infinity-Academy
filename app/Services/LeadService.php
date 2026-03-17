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

    /*
    |--------------------------------------------------------------------------
    | Create Lead
    |--------------------------------------------------------------------------
    */

    public function createLead(array $data): Lead
    {
        $data['owner_cs_id'] = Auth::user()->employees->first()->employee_id ?? null;

        $lead = $this->leadRepository->create($data);

        $this->logHistory($lead, 'Lead Created');

        return $lead;
    }

    /*
    |--------------------------------------------------------------------------
    | Assign Lead
    |--------------------------------------------------------------------------
    */

    public function assignLead(int $leadId, int $employeeId)
    {
        $lead = $this->leadRepository->find($leadId);

        $lead->assignTo($employeeId);

        $this->logHistory($lead, "Assigned to CS #{$employeeId}");

        return $lead;
    }

    /*
    |--------------------------------------------------------------------------
    | Schedule Call
    |--------------------------------------------------------------------------
    */

    public function scheduleCall(int $leadId, $datetime)
    {
        $lead = $this->leadRepository->find($leadId);

        $lead->scheduleCall($datetime);

        LeadCallLog::create([
            'lead_id' => $lead->lead_id,
            'call_date' => now(),
            'notes' => 'Call scheduled'
        ]);

        $this->logHistory($lead, "Call scheduled for {$datetime}");

        return $lead;
    }

    /*
    |--------------------------------------------------------------------------
    | Call Again
    |--------------------------------------------------------------------------
    */

    public function markCallAgain(int $leadId, $datetime)
    {
        $lead = $this->leadRepository->find($leadId);

        $lead->markCallAgain($datetime);

        LeadCallLog::create([
            'lead_id' => $lead->lead_id,
            'call_date' => now(),
            'notes' => 'Marked as call again'
        ]);

        $this->logHistory($lead, "Call again scheduled for {$datetime}");

        return $lead;
    }

    /*
    |--------------------------------------------------------------------------
    | Mark as Registered
    |--------------------------------------------------------------------------
    */

    public function markRegistered(int $leadId)
    {
        $lead = $this->leadRepository->find($leadId);

        $lead->update([
            'status' => 'Registered'
        ]);

        $this->logHistory($lead, "Lead converted to student");

        return $lead;
    }

    /*
    |--------------------------------------------------------------------------
    | Not Interested
    |--------------------------------------------------------------------------
    */

    public function markNotInterested(int $leadId)
    {
        $lead = $this->leadRepository->find($leadId);

        $lead->update([
            'status' => 'Not_Interested'
        ]);

        $this->logHistory($lead, "Lead marked as not interested");

        return $lead;
    }

    /*
    |--------------------------------------------------------------------------
    | Archive Lead
    |--------------------------------------------------------------------------
    */

    public function archiveLead(int $leadId)
    {
        $lead = $this->leadRepository->find($leadId);

        $lead->archive();

        $this->logHistory($lead, "Lead archived");

        return $lead;
    }

    /*
    |--------------------------------------------------------------------------
    | Auto Public Leads (4 days rule)
    |--------------------------------------------------------------------------
    */

    public function releaseExpiredPrivateLeads()
    {
        $leads = Lead::whereNotNull('owner_cs_id')
            ->where('created_at','<=',now()->subDays(4))
            ->where('status','Waiting')
            ->get();

        foreach ($leads as $lead) {

            $lead->update([
                'owner_cs_id' => null
            ]);

            $this->logHistory($lead,"Lead became public (4 days rule)");
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
        $leads = Lead::where('created_at','<=',now()->subDays(30))
            ->whereNotIn('status',['Registered','Archived'])
            ->get();

        foreach ($leads as $lead) {

            $lead->archive();

            $this->logHistory($lead,"Lead auto archived (30 days rule)");
        }

        return $leads;
    }

    /*
    |--------------------------------------------------------------------------
    | History Logger
    |--------------------------------------------------------------------------
    */

    protected function logHistory(Lead $lead, string $action)
    {
        LeadHistory::create([
            'lead_id' => $lead->lead_id,
            'action' => $action,
            'owner_cs_id' => Auth::id(),
            'changed_by' => Auth::id()
        ]);
    }


}