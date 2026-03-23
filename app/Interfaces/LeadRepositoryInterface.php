<?php

namespace App\Interfaces;


use App\Models\Leads\Lead;
use Illuminate\Database\Eloquent\Collection;


interface LeadRepositoryInterface
{
    public function getAll();
    public function find(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function search(string $term);
    public function myLeads(int $employeeId);
    public function publicLeads();
    public function dueCalls();
    public function releaseExpiredLeads();
}