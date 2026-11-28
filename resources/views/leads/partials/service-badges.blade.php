@php $svc = ($serviceCounts ?? [])[$lead->lead_id] ?? []; @endphp
@if(!empty($svc))
    <div class="svc-pills">
        @if(($svc['Test'] ?? 0) > 0)
            <span class="svc-pill svc-pill-test">Test{{ $svc['Test'] > 1 ? ' ×'.$svc['Test'] : '' }}</span>
        @endif
        @if(($svc['Certificate'] ?? 0) > 0)
            <span class="svc-pill svc-pill-cert">Certificate{{ $svc['Certificate'] > 1 ? ' ×'.$svc['Certificate'] : '' }}</span>
        @endif
        @if(($svc['Material'] ?? 0) > 0)
            <span class="svc-pill svc-pill-mat">Material{{ $svc['Material'] > 1 ? ' ×'.$svc['Material'] : '' }}</span>
        @endif
    </div>
@endif
