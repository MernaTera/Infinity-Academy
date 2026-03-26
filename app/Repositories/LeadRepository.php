<?php

namespace App\Repositories;

use App\Models\Leads\Lead;
use App\Interfaces\LeadRepositoryInterface;

class LeadRepository extends BaseRepository implements LeadRepositoryInterface
{

    public function __construct(Lead $model)
    {
        parent::__construct($model);
    }

    public function getAll()
    {
        return $this->model
            ->with(['courseTemplate','level','sublevel','owner'])
            ->latest()
            ->paginate(20);
    }

    public function find($id)
    {
        return $this->model
            ->with(['leadCallLogs','leadHistories'])
            ->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update( $id, array $data)
    {
        $lead = $this->find($id);
        $lead->update($data);
        return $lead;
    }

    public function delete( $id)
    {
        $lead = $this->find($id);
        $lead->delete();
        return $lead;
    }

    public function search(string $term)
    {
        return $this->model
            ->where('full_name','LIKE',"%$term%")
            ->orWhere('phone','LIKE',"%$term%")
            ->orWhere('location','LIKE',"%$term%")
            ->latest()
            ->paginate(20);
    }

    public function myLeads(int $employeeId)
    {
        return $this->model
            ->whereNotNull('owner_cs_id')
            ->ownedBy($employeeId)
            ->active()
            ->latest()
            ->paginate(20);
    }

    public function publicLeads()
    {
        return $this->model
            ->whereNull('owner_cs_id')
            ->where('is_active', true)
            ->latest()
            ->paginate(20);
    }

    public function archivedLeads()
    {
        return $this->model
            ->where('status', 'Archived')
            ->where('is_active', false)
            ->latest()
            ->paginate(20);
    }

    public function dueCalls()
    {
        return $this->model
            ->dueCalls()
            ->active()
            ->latest()
            ->paginate(20);
    }

    public function releaseExpiredLeads()
    {
        return $this->model
            ->whereNotNull('owner_cs_id')
            ->where('updated_at','<=',now()->subDays(4))
            ->update(['owner_cs_id' => null ]);
    }

}