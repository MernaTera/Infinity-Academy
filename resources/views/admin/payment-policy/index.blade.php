@extends('admin.layouts.app')
@section('title', 'Payment Policy')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.pp-page{background:#F8F6F2;min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:#1A2A4A}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:4px}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#1B4FA8;margin:0}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px}

.kpi-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:24px}
.kpi-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:6px;padding:16px 20px;position:relative;overflow:hidden}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,#1B4FA8)}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;margin-bottom:5px}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:30px;letter-spacing:2px;color:var(--kc,#1B4FA8);line-height:1}

.two-col{display:grid;grid-template-columns:1fr 380px;gap:20px;align-items:start}

/* Plan Cards */
.plan-list{display:flex;flex-direction:column;gap:12px}
.plan-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;overflow:hidden;position:relative;transition:box-shadow 0.2s}
.plan-card:hover{box-shadow:0 4px 20px rgba(27,79,168,0.08)}
.plan-card.inactive{opacity:0.6}
.plan-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#1B4FA8,transparent)}
.pc-header{padding:16px 20px;display:flex;justify-content:space-between;align-items:flex-start;border-bottom:1px solid rgba(27,79,168,0.06)}
.pc-name{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:2px;color:#1B4FA8}
.pc-body{padding:14px 20px;display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
.pc-meta-label{font-size:8px;letter-spacing:2px;text-transform:uppercase;color:#AAB8C8;margin-bottom:4px}
.pc-meta-val{font-family:'Bebas Neue',sans-serif;font-size:20px;color:#1A2A4A;letter-spacing:1px;line-height:1}
.pc-meta-unit{font-size:9px;color:#AAB8C8;letter-spacing:1px}
.pc-footer{padding:10px 20px;border-top:1px solid rgba(27,79,168,0.06);display:flex;gap:8px;align-items:center}

/* Edit inline form */
.edit-panel{display:none;padding:14px 20px;background:rgba(27,79,168,0.02);border-top:1px solid rgba(27,79,168,0.06)}
.edit-panel.show{display:block}
.edit-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:10px}
.form-field{display:flex;flex-direction:column;gap:4px}
.form-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A}
.form-control{width:100%;padding:8px 10px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:12px;color:#1A2A4A;background:#fff;outline:none;box-sizing:border-box}
.form-control:focus{border-color:#1B4FA8;box-shadow:0 0 0 3px rgba(27,79,168,0.07)}

.badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:2px 8px;border-radius:3px}
.badge-active{color:#059669;background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.15)}
.badge-inactive{color:#7A8A9A;background:rgba(122,138,154,0.08);border:1px solid rgba(122,138,154,0.15)}
.badge-approval{color:#C47010;background:rgba(245,145,30,0.08);border:1px solid rgba(245,145,30,0.2)}

.btn-sm{display:inline-flex;align-items:center;gap:4px;padding:5px 12px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:3px;border:1px solid;background:transparent;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s}
.btn-edit{color:#1B4FA8;border-color:rgba(27,79,168,0.25)}
.btn-edit:hover{background:rgba(27,79,168,0.07)}
.btn-toggle-on{color:#059669;border-color:rgba(5,150,105,0.2)}
.btn-toggle-on:hover{background:rgba(5,150,105,0.06)}
.btn-toggle-off{color:#DC2626;border-color:rgba(220,38,38,0.2)}
.btn-toggle-off:hover{background:rgba(220,38,38,0.06)}
.btn-save{padding:8px 18px;background:#1B4FA8;border:none;border-radius:3px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:12px;letter-spacing:2px;cursor:pointer}

/* Create Panel */
.create-panel{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;overflow:hidden;position:relative}
.create-panel::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#F5911E,#1B4FA8,transparent)}
.create-header{padding:14px 18px;border-bottom:1px solid rgba(27,79,168,0.07)}
.create-title{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:2px;color:#1B4FA8}
.create-body{padding:16px 18px}
.sec-label{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:#F5911E;margin-bottom:10px;display:block}
.info-box{background:rgba(245,145,30,0.04);border:1px solid rgba(245,145,30,0.15);border-radius:4px;padding:10px 14px;font-size:11px;color:#C47010;margin-bottom:14px;line-height:1.5}
.btn-submit{width:100%;padding:10px;background:transparent;border:1.5px solid #1B4FA8;border-radius:4px;color:#1B4FA8;font-family:'Bebas Neue',sans-serif;font-size:13px;letter-spacing:3px;cursor:pointer;position:relative;overflow:hidden;transition:color 0.4s;margin-top:8px}
.btn-submit::before{content:'';position:absolute;inset:0;background:linear-gradient(90deg,#1B4FA8,#2D6FDB);transform:scaleX(0);transform-origin:left;transition:transform 0.4s cubic-bezier(0.16,1,0.3,1)}
.btn-submit:hover::before{transform:scaleX(1)}
.btn-submit:hover{color:#fff}

.toggle-wrap{display:flex;align-items:center;gap:10px;margin-top:8px}
.toggle-label{font-size:11px;color:#4A5A7A}

@media(max-width:1024px){.two-col{grid-template-columns:1fr}.pp-page{padding:18px 14px}.kpi-grid{grid-template-columns:repeat(3,1fr)}}
</style>

<div class="pp-page">

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Admin Panel</div>
            <h1 class="page-title">Payment Policy</h1>
        </div>
    </div>

    @if(session('success'))
    <div style="background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);color:#059669;padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px">{{ session('success') }}</div>
    @endif

    {{-- KPIs --}}
    <div class="kpi-grid">
        <div class="kpi-card" style="--kc:#1B4FA8"><div class="kpi-label">Total Plans</div><div class="kpi-val">{{ $stats['total'] }}</div></div>
        <div class="kpi-card" style="--kc:#059669"><div class="kpi-label">Active</div><div class="kpi-val">{{ $stats['active'] }}</div></div>
        <div class="kpi-card" style="--kc:#C47010"><div class="kpi-label">Needs Approval</div><div class="kpi-val">{{ $stats['approval'] }}</div></div>
    </div>

    <div class="two-col">

        {{-- LEFT — Plans --}}
        <div>
            <span class="sec-label">Payment Plans</span>
            <div class="plan-list">
                @forelse($plans as $plan)
                <div class="plan-card {{ !$plan->is_active ? 'inactive' : '' }}">
                    <div class="pc-header">
                        <div>
                            <div class="pc-name">{{ $plan->name }}</div>
                            <div style="display:flex;gap:6px;margin-top:4px;flex-wrap:wrap">
                                @if($plan->is_active)
                                    <span class="badge badge-active">Active</span>
                                @else
                                    <span class="badge badge-inactive">Inactive</span>
                                @endif
                                @if($plan->requires_admin_approval)
                                    <span class="badge badge-approval">Requires Approval</span>
                                @endif
                            </div>
                        </div>
                        <div style="font-size:10px;color:#AAB8C8">
                            by {{ $plan->createdBy?->full_name ?? '—' }}
                        </div>
                    </div>

                    <div class="pc-body">
                        <div>
                            <div class="pc-meta-label">Deposit</div>
                            <div class="pc-meta-val">{{ $plan->deposit_percentage }}<span class="pc-meta-unit">%</span></div>
                        </div>
                        <div>
                            <div class="pc-meta-label">Installments</div>
                            <div class="pc-meta-val">{{ $plan->installment_count }}</div>
                        </div>
                        <div>
                            <div class="pc-meta-label">Grace Period</div>
                            <div class="pc-meta-val">{{ $plan->grace_period_days }}<span class="pc-meta-unit"> days</span></div>
                        </div>
                        <div>
                            <div class="pc-meta-label">Split</div>
                            <div class="pc-meta-val" style="font-size:14px;font-family:'DM Sans',sans-serif;font-weight:500">
                                @if($plan->installment_count == 0)
                                    Full
                                @elseif($plan->deposit_percentage == 50 && $plan->installment_count == 2)
                                    50/25/25
                                @else
                                    Custom
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="pc-footer">
                        <button class="btn-sm btn-edit" onclick="toggleEdit('edit_{{ $plan->payment_plan_id }}')">
                            <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Edit
                        </button>
                        <form method="POST" action="{{ route('admin.payment-plans.toggle', $plan->payment_plan_id) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-sm {{ $plan->is_active ? 'btn-toggle-off' : 'btn-toggle-on' }}">
                                {{ $plan->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </div>

                    {{-- Inline Edit --}}
                    <div class="edit-panel" id="edit_{{ $plan->payment_plan_id }}">
                        <form method="POST" action="{{ route('admin.payment-plans.update', $plan->payment_plan_id) }}">
                            @csrf @method('PUT')
                            <div class="edit-grid">
                                <div class="form-field">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ $plan->name }}" required>
                                </div>
                                <div class="form-field">
                                    <label class="form-label">Deposit %</label>
                                    <input type="number" name="deposit_percentage" class="form-control" value="{{ $plan->deposit_percentage }}" min="0" max="100" step="0.01" required>
                                </div>
                                <div class="form-field">
                                    <label class="form-label">Grace Days</label>
                                    <input type="number" name="grace_period_days" class="form-control" value="{{ $plan->grace_period_days }}" min="0" required>
                                </div>
                            </div>
                            <div class="toggle-wrap">
                                <input type="checkbox" name="requires_admin_approval" value="1" id="req_{{ $plan->payment_plan_id }}" {{ $plan->requires_admin_approval ? 'checked' : '' }}>
                                <label class="toggle-label" for="req_{{ $plan->payment_plan_id }}">Requires Admin Approval</label>
                            </div>
                            <div style="margin-top:10px">
                                <button type="submit" class="btn-save">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
                @empty
                <div style="text-align:center;padding:40px;color:#AAB8C8;background:#fff;border:1px solid rgba(27,79,168,0.08);border-radius:8px">
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;margin-bottom:6px">No Plans Yet</div>
                    <div style="font-size:12px">Create your first payment plan</div>
                </div>
                @endforelse
            </div>
        </div>

        {{-- RIGHT — Create New Plan --}}
        <div class="create-panel">
            <div class="create-header">
                <div class="create-title">New Payment Plan</div>
            </div>
            <div class="create-body">
                <div class="info-box">
                    ⚠ Plan changes affect new registrations only. Existing enrollments keep their original plan unless manually updated.
                </div>
                <form method="POST" action="{{ route('admin.payment-plans.store') }}">
                    @csrf
                    <div class="form-field" style="margin-bottom:10px">
                        <label class="form-label">Plan Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Standard 50/25/25" required>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px">
                        <div class="form-field">
                            <label class="form-label">Deposit % *</label>
                            <input type="number" name="deposit_percentage" class="form-control" placeholder="50" min="0" max="100" step="0.01" required>
                        </div>
                        <div class="form-field">
                            <label class="form-label">Installments *</label>
                            <input type="number" name="installment_count" class="form-control" placeholder="2" min="0" required>
                        </div>
                    </div>
                    <div class="form-field" style="margin-bottom:12px">
                        <label class="form-label">Grace Period (days) *</label>
                        <input type="number" name="grace_period_days" class="form-control" placeholder="0" min="0" required>
                    </div>
                    <div class="toggle-wrap" style="margin-bottom:8px">
                        <input type="checkbox" name="requires_admin_approval" value="1" id="req_new">
                        <label class="toggle-label" for="req_new">Requires Admin Approval</label>
                    </div>
                    <button type="submit" class="btn-submit">Create Plan</button>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
function toggleEdit(id) {
    const panel = document.getElementById(id);
    panel.classList.toggle('show');
}
</script>
@endsection