<?php

namespace App\Services;

use App\Models\Leads\Lead;
use App\Models\Leads\LeadHistory;
use App\Models\HR\Employee;
use Illuminate\Support\Facades\Request;


class LeadActivityLogger
{
    protected Lead $lead;
    protected array $payload = [];

    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    public static function for(Lead $lead): self
    {
        return new self($lead);
    }

    public function action(string $type): self
    {
        $this->payload['action_type'] = $type;
        return $this;
    }

    public function status(?string $old, ?string $new): self
    {
        $this->payload['old_status'] = $old;
        $this->payload['new_status'] = $new;
        return $this;
    }

    public function changed(array $fields): self
    {
        $this->payload['changed_fields'] = $fields;
        return $this;
    }

    public function reason(?string $reason): self
    {
        $this->payload['reason'] = $reason;
        return $this;
    }

    public function callOutcome(?string $outcome): self
    {
        $this->payload['call_outcome'] = $outcome;
        return $this;
    }

    public function notes(?string $notes): self
    {
        $this->payload['notes'] = $notes;
        return $this;
    }

    public function record(): LeadHistory
    {
        return LeadHistory::create(array_merge([
            'lead_id'    => $this->lead->lead_id,
            'changed_by' => $this->currentEmployeeId(),
            'changed_at' => now(),
            'ip_address' => Request::ip(),
            'user_agent' => substr(Request::userAgent() ?? '', 0, 500),
        ], $this->payload));
    }

    protected function currentEmployeeId(): ?int
    {
        return auth()->check()
            ? Employee::where('user_id', auth()->id())->value('employee_id')
            : null;
    }

    public static function detectChanges(Lead $lead, array $newData, array $trackedFields): array
    {
        $changes = [];
        foreach ($trackedFields as $field) {
            if (!array_key_exists($field, $newData)) continue;

            $old = $lead->getOriginal($field);
            $new = $newData[$field];

            if ((string) $old !== (string) $new) {
                $changes[$field] = [
                    'from' => $old,
                    'to'   => $new,
                ];
            }
        }
        return $changes;
    }
}