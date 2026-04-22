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
        $data['owner_cs_id'] = Auth::user()->employee->first()->employee_id ?? null;
        
        $lead = $this->leadRepository->create($data);

        $this->logHistory(
            $lead,
            null,
            $lead->status,
            'Lead Created'
        );

        return $lead;
    }

    /*
    |--------------------------------------------------------------------------
    | Assign Lead
    |--------------------------------------------------------------------------
    */

    public function assignLead(int $leadId)
    {
        $lead = $this->leadRepository->find($leadId);

        $employeeId = auth()->user()->employee->first()->employee_id;

        $old = $lead->status;

        $lead->assignTo($employeeId);

        $this->logHistory(
            $lead,
            $old,           
            $lead->status, 
            "Assigned to CS #{$employeeId}"  
        );

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

        $this->authorizeLead($lead);

        $lead->scheduleCall($datetime);

        LeadCallLog::create([
            'lead_id' => $lead->lead_id,
            'cs_id' => auth()->user()->employee->first()->employee_id,
            'call_datetime' => now(),
            'outcome' => 'Follow_Up_Scheduled',
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

        $this->authorizeLead($lead);

        $lead->markCallAgain($datetime);

        LeadCallLog::create([
            'lead_id' => $lead->lead_id,
            'cs_id' => auth()->user()->employee->first()->employee_id,
            'call_datetime' => now(),
            'outcome' => 'Follow_Up_Scheduled',
            'notes' => 'Call scheduled'
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

        $this->authorizeLead($lead);

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

        $this->authorizeLead($lead);

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

        $this->authorizeLead($lead);

        $lead->archive();

        $this->logHistory(
            $lead,
            $lead->status,
            'Archived',
            'Lead archived'
        );

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
            ->where('updated_at','<=',now()->subDays(4))
            ->get();

        foreach ($leads as $lead) {

            $lead->update([
                'owner_cs_id' => null
            ]);

            $this->logHistory(
                $lead,
                $lead->status,
                'Waiting',
                'Lead set public (passed 4 days since last update)'
            );
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
        $leads = Lead::where('updated_at','<=',now()->subDays(30))
            ->whereNotIn('status',['Registered','Archived'])
            ->get();

        foreach ($leads as $lead) {

            $lead->update([
                'status' => 'Archived',
                'is_active' => false,
                'owner_cs_id' => null 
            ]);

            $this->logHistory(
                $lead,
                $lead->status,
                'Archived',
                'Lead auto archived (30 days rule)'
            );
        }

        return $leads;
    }

    /*
    |--------------------------------------------------------------------------
    | History Logger
    |--------------------------------------------------------------------------
    */

    public function logHistory($lead, $old = null, $new = null, $note = null)
    {
        LeadHistory::create([
            'lead_id' => $lead->lead_id,
            'old_status' => $old,
            'new_status' => $new,
            'notes' => $note,
            'changed_by' => auth()->user()->employee->first()->employee_id,
            'changed_at' => now()
        ]);
    }   

    private function authorizeLead($lead)
    {
        $employeeId = auth()->user()->employee->first()->employee_id;

        if ($lead->owner_cs_id && $lead->owner_cs_id != $employeeId) {
            abort(403, 'Unauthorized');
        }
    }

}