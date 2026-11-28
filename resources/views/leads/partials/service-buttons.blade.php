<button type="button" class="btn-action btn-svc-test"
        onclick="openServiceModal('Test', this)"
        data-lead-id="{{ $lead->lead_id }}"
        data-lead-name="{{ $lead->full_name }}"
        data-lead-phone="{{ $lead->phone }}">
    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
    Test
</button>
<button type="button" class="btn-action btn-svc-cert"
        onclick="openServiceModal('Certificate', this)"
        data-lead-id="{{ $lead->lead_id }}"
        data-lead-name="{{ $lead->full_name }}"
        data-lead-phone="{{ $lead->phone }}">
    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
    Certificate
</button>
@if(!empty($enrollmentId))
<button type="button" class="btn-action btn-svc-mat"
        onclick="openServiceModal('Material', this)"
        data-lead-id="{{ $lead->lead_id }}"
        data-lead-name="{{ $lead->full_name }}"
        data-lead-phone="{{ $lead->phone }}"
        data-enrollment-id="{{ $enrollmentId }}">
    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
    Material
</button>
@endif
