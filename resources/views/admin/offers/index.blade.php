@extends('admin.layouts.app')
@section('title', 'Offers')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.off-page{background:#F8F6F2;min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:#1A2A4A}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:4px}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#1B4FA8;margin:0}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px}

.btn-primary{display:inline-flex;align-items:center;gap:8px;padding:10px 22px;background:transparent;border:1.5px solid #1B4FA8;border-radius:4px;color:#1B4FA8;font-family:'Bebas Neue',sans-serif;font-size:13px;letter-spacing:3px;cursor:pointer;position:relative;overflow:hidden;transition:color 0.4s}
.btn-primary::before{content:'';position:absolute;inset:0;background:linear-gradient(90deg,#1B4FA8,#2D6FDB);transform:scaleX(0);transform-origin:left;transition:transform 0.4s cubic-bezier(0.16,1,0.3,1)}
.btn-primary:hover::before{transform:scaleX(1)}
.btn-primary:hover{color:#fff}
.btn-primary span,.btn-primary svg{position:relative;z-index:1}

.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px}
.kpi-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:6px;padding:16px 20px;position:relative;overflow:hidden}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,#1B4FA8)}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;margin-bottom:5px}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:30px;letter-spacing:2px;color:var(--kc,#1B4FA8);line-height:1}

.two-col{display:grid;grid-template-columns:1fr 400px;gap:20px;align-items:start}

/* Offer Cards */
.offers-list{display:flex;flex-direction:column;gap:12px}
.offer-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;overflow:hidden;position:relative;transition:box-shadow 0.2s}
.offer-card:hover{box-shadow:0 4px 20px rgba(27,79,168,0.08)}
.offer-card.inactive{opacity:0.6}
.offer-card.active-now::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#059669,transparent)}
.offer-card.upcoming::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#1B4FA8,transparent)}
.offer-card.expired::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:rgba(122,138,154,0.3)}

.oc-header{padding:16px 20px;display:flex;justify-content:space-between;align-items:flex-start;border-bottom:1px solid rgba(27,79,168,0.06)}
.oc-name{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:2px;color:#1B4FA8}
.oc-discount{display:flex;align-items:baseline;gap:4px}
.oc-disc-val{font-family:'Bebas Neue',sans-serif;font-size:32px;color:#F5911E;letter-spacing:1px;line-height:1}
.oc-disc-unit{font-size:12px;color:#C47010;font-weight:500}

.oc-body{padding:14px 20px}
.oc-meta{display:flex;gap:16px;flex-wrap:wrap;margin-bottom:12px}
.oc-meta-item{display:flex;flex-direction:column;gap:2px}
.oc-meta-label{font-size:8px;letter-spacing:2px;text-transform:uppercase;color:#AAB8C8}
.oc-meta-val{font-size:12px;color:#1A2A4A;font-weight:500}

/* Course tags */
.course-tags{display:flex;flex-wrap:wrap;gap:6px}
.course-tag{display:inline-block;padding:3px 10px;background:rgba(27,79,168,0.06);border:1px solid rgba(27,79,168,0.12);border-radius:3px;font-size:10px;color:#1B4FA8;letter-spacing:0.5px}

/* Validity bar */
.validity-wrap{margin-top:10px}
.validity-bar{background:#F0F0F0;border-radius:3px;height:4px;overflow:hidden;margin:6px 0}
.validity-fill{height:4px;border-radius:3px}
.validity-dates{display:flex;justify-content:space-between;font-size:9px;color:#AAB8C8}

.oc-footer{padding:10px 20px;border-top:1px solid rgba(27,79,168,0.06);display:flex;gap:8px}

/* Edit Panel */
.edit-panel{display:none;padding:16px 20px;background:rgba(27,79,168,0.02);border-top:1px solid rgba(27,79,168,0.06)}
.edit-panel.show{display:block}
.edit-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px}
.form-field{display:flex;flex-direction:column;gap:4px}
.form-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A}
.form-control{width:100%;padding:8px 10px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:12px;color:#1A2A4A;background:#fff;outline:none;box-sizing:border-box}
.form-control:focus{border-color:#1B4FA8;box-shadow:0 0 0 3px rgba(27,79,168,0.07)}

.badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 8px;border-radius:3px;font-weight:500}
.badge::before{content:'';width:4px;height:4px;border-radius:50%;background:currentColor;flex-shrink:0}
.badge-active{color:#059669;background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.15)}
.badge-upcoming{color:#1B4FA8;background:rgba(27,79,168,0.07);border:1px solid rgba(27,79,168,0.15)}
.badge-expired{color:#7A8A9A;background:rgba(122,138,154,0.08);border:1px solid rgba(122,138,154,0.15)}
.badge-disabled{color:#DC2626;background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15)}

.btn-sm{display:inline-flex;align-items:center;gap:4px;padding:5px 12px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:3px;border:1px solid;background:transparent;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s}
.btn-edit{color:#1B4FA8;border-color:rgba(27,79,168,0.25)}
.btn-edit:hover{background:rgba(27,79,168,0.07)}
.btn-disable{color:#DC2626;border-color:rgba(220,38,38,0.2)}
.btn-disable:hover{background:rgba(220,38,38,0.06)}
.btn-enable{color:#059669;border-color:rgba(5,150,105,0.2)}
.btn-enable:hover{background:rgba(5,150,105,0.06)}
.btn-save{padding:8px 18px;background:#1B4FA8;border:none;border-radius:3px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:12px;letter-spacing:2px;cursor:pointer}

/* Create Panel */
.create-panel{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;overflow:hidden;position:relative;top:0}
.create-panel::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#F5911E,#1B4FA8,transparent)}
.cp-header{padding:14px 18px;border-bottom:1px solid rgba(27,79,168,0.07)}
.cp-title{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:2px;color:#1B4FA8}
.cp-body{padding:18px}
.sec-label{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:#F5911E;margin-bottom:10px;display:block}
.info-box{background:rgba(245,145,30,0.04);border:1px solid rgba(245,145,30,0.15);border-radius:4px;padding:10px 14px;font-size:11px;color:#C47010;margin-bottom:14px;line-height:1.5}

/* Course checkboxes */
.course-checks{display:flex;flex-direction:column;gap:6px;max-height:200px;overflow-y:auto;padding:4px 0}
.course-check-item{display:flex;align-items:center;gap:8px;padding:7px 10px;border:1px solid rgba(27,79,168,0.08);border-radius:4px;cursor:pointer;transition:all 0.2s}
.course-check-item:hover{background:rgba(27,79,168,0.03);border-color:rgba(27,79,168,0.15)}
.course-check-item input{accent-color:#1B4FA8;flex-shrink:0}
.course-check-label{font-size:12px;color:#1A2A4A}

.discount-preview{background:rgba(245,145,30,0.06);border:1px solid rgba(245,145,30,0.15);border-radius:4px;padding:10px 14px;font-size:12px;color:#C47010;margin-top:8px;display:none}

.btn-submit{width:100%;padding:10px;background:transparent;border:1.5px solid #1B4FA8;border-radius:4px;color:#1B4FA8;font-family:'Bebas Neue',sans-serif;font-size:13px;letter-spacing:3px;cursor:pointer;position:relative;overflow:hidden;transition:color 0.4s;margin-top:10px}
.btn-submit::before{content:'';position:absolute;inset:0;background:linear-gradient(90deg,#1B4FA8,#2D6FDB);transform:scaleX(0);transform-origin:left;transition:transform 0.4s cubic-bezier(0.16,1,0.3,1)}
.btn-submit:hover::before{transform:scaleX(1)}
.btn-submit:hover{color:#fff}

@media(max-width:1024px){.two-col{grid-template-columns:1fr}.off-page{padding:18px 14px}.kpi-grid{grid-template-columns:repeat(2,1fr)}}
</style>

<div class="off-page">

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Admin Panel</div>
            <h1 class="page-title">Offers & Pricing</h1>
        </div>
    </div>

    @if(session('success'))
    <div style="background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);color:#059669;padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div style="background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15);color:#DC2626;padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px">{{ session('error') }}</div>
    @endif

    {{-- KPIs --}}
    <div class="kpi-grid">
        <div class="kpi-card" style="--kc:#1B4FA8"><div class="kpi-label">Total Offers</div><div class="kpi-val">{{ $stats['total'] }}</div></div>
        <div class="kpi-card" style="--kc:#059669"><div class="kpi-label">Active Now</div><div class="kpi-val">{{ $stats['active'] }}</div></div>
        <div class="kpi-card" style="--kc:#1B6FA8"><div class="kpi-label">Upcoming</div><div class="kpi-val">{{ $stats['upcoming'] }}</div></div>
        <div class="kpi-card" style="--kc:#7A8A9A"><div class="kpi-label">Expired</div><div class="kpi-val">{{ $stats['expired'] }}</div></div>
    </div>

    <div class="two-col">

        {{-- LEFT — Offers List --}}
        <div>
            <span class="sec-label">All Offers</span>
            <div class="offers-list">
                @forelse($offers as $offer)
                @php
                    $today     = now()->toDateString();
                    $isActive  = $offer->isActive();
                    $isExpired = $offer->end_date && $offer->end_date < $today;
                    $isUpcoming= $offer->start_date && $offer->start_date > $today;
                    $cardClass = !$offer->is_active ? 'inactive' : ($isActive ? 'active-now' : ($isUpcoming ? 'upcoming' : 'expired'));

                    // Validity progress
                    $start     = \Carbon\Carbon::parse($offer->start_date);
                    $end       = \Carbon\Carbon::parse($offer->end_date);
                    $totalDays = max(1, $start->diffInDays($end));
                    $elapsed   = max(0, min($totalDays, $start->diffInDays(now())));
                    $pct       = round($elapsed / $totalDays * 100);
                    $fillColor = $isActive ? '#059669' : ($isExpired ? '#7A8A9A' : '#1B4FA8');
                @endphp
                <div class="offer-card {{ $cardClass }}">
                    <div class="oc-header">
                        <div>
                            <div class="oc-name">{{ $offer->offer_name }}</div>
                            <div style="display:flex;gap:6px;margin-top:5px;flex-wrap:wrap">
                                @if(!$offer->is_active)
                                    <span class="badge badge-disabled">Disabled</span>
                                @elseif($isActive)
                                    <span class="badge badge-active">Active Now</span>
                                @elseif($isUpcoming)
                                    <span class="badge badge-upcoming">Upcoming</span>
                                @else
                                    <span class="badge badge-expired">Expired</span>
                                @endif
                                <span style="font-size:9px;letter-spacing:1px;text-transform:uppercase;color:#AAB8C8;padding:3px 0">
                                    {{ $offer->discount_type }}
                                </span>
                            </div>
                        </div>
                        <div class="oc-discount">
                            <div class="oc-disc-val">{{ $offer->discount_value }}</div>
                            <div class="oc-disc-unit">{{ $offer->discount_type === 'Percentage' ? '%' : 'LE' }}</div>
                        </div>
                    </div>

                    <div class="oc-body">
                        <div class="oc-meta">
                            <div class="oc-meta-item">
                                <span class="oc-meta-label">Start</span>
                                <span class="oc-meta-val">{{ \Carbon\Carbon::parse($offer->start_date)->format('d M Y') }}</span>
                            </div>
                            <div class="oc-meta-item">
                                <span class="oc-meta-label">End</span>
                                <span class="oc-meta-val">{{ \Carbon\Carbon::parse($offer->end_date)->format('d M Y') }}</span>
                            </div>
                            <div class="oc-meta-item">
                                <span class="oc-meta-label">Duration</span>
                                <span class="oc-meta-val">{{ $totalDays }} days</span>
                            </div>
                            <div class="oc-meta-item">
                                <span class="oc-meta-label">Courses</span>
                                <span class="oc-meta-val">{{ $offer->courseTemplates->count() }}</span>
                            </div>
                        </div>

                        {{-- Course Tags --}}
                        @if($offer->courseTemplates->isNotEmpty())
                        <div class="course-tags">
                            @foreach($offer->courseTemplates as $ct)
                            <span class="course-tag">{{ $ct->name }}</span>
                            @endforeach
                        </div>
                        @endif

                        {{-- Validity Progress --}}
                        <div class="validity-wrap">
                            <div class="validity-bar">
                                <div class="validity-fill" style="width:{{ $pct }}%;background:{{ $fillColor }}"></div>
                            </div>
                            <div class="validity-dates">
                                <span>{{ \Carbon\Carbon::parse($offer->start_date)->format('d M') }}</span>
                                <span style="color:{{ $fillColor }}">{{ $pct }}%</span>
                                <span>{{ \Carbon\Carbon::parse($offer->end_date)->format('d M') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="oc-footer">
                        @if(!$isExpired)
                        <button class="btn-sm btn-edit" onclick="toggleEdit('edit_{{ $offer->offer_id }}')">
                            <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Edit
                        </button>
                        @endif
                        <form method="POST" action="{{ route('admin.offers.toggle', $offer->offer_id) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-sm {{ $offer->is_active ? 'btn-disable' : 'btn-enable' }}">
                                {{ $offer->is_active ? 'Disable' : 'Enable' }}
                            </button>
                        </form>
                    </div>

                    {{-- Edit Panel --}}
                    <div class="edit-panel" id="edit_{{ $offer->offer_id }}">
                        <form method="POST" action="{{ route('admin.offers.update', $offer->offer_id) }}">
                            @csrf @method('PUT')
                            <div class="edit-grid">
                                <div class="form-field" style="grid-column:1/-1">
                                    <label class="form-label">Offer Name</label>
                                    <input type="text" name="offer_name" class="form-control" value="{{ $offer->offer_name }}" required>
                                </div>
                                <div class="form-field">
                                    <label class="form-label">Discount Type</label>
                                    <select name="discount_type" class="form-control">
                                        <option value="Percentage" {{ $offer->discount_type === 'Percentage' ? 'selected' : '' }}>Percentage %</option>
                                        <option value="Fixed" {{ $offer->discount_type === 'Fixed' ? 'selected' : '' }}>Fixed LE</option>
                                    </select>
                                </div>
                                <div class="form-field">
                                    <label class="form-label">Discount Value</label>
                                    <input type="number" name="discount_value" class="form-control" value="{{ $offer->discount_value }}" step="0.01" min="0.01" required>
                                </div>
                                <div class="form-field">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" name="start_date" class="form-control" value="{{ $offer->start_date->format('Y-m-d') }}" required>
                                </div>
                                <div class="form-field">
                                    <label class="form-label">End Date</label>
                                    <input type="date" name="end_date" class="form-control" value="{{ $offer->end_date->format('Y-m-d') }}" required>
                                </div>
                            </div>
                            <button type="submit" class="btn-save">Save Changes</button>
                        </form>
                    </div>
                </div>
                @empty
                <div style="text-align:center;padding:48px;color:#AAB8C8;background:#fff;border:1px solid rgba(27,79,168,0.08);border-radius:8px">
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;margin-bottom:6px">No Offers Yet</div>
                    <div style="font-size:12px">Create your first seasonal offer</div>
                </div>
                @endforelse
            </div>
        </div>

        {{-- RIGHT — Create New Offer --}}
        <div class="create-panel">
            <div class="cp-header">
                <div class="cp-title">New Offer</div>
            </div>
            <div class="cp-body">
                <div class="info-box">
                    ⚠ No overlapping active offers allowed for the same course in the same period.
                </div>
                <form method="POST" action="{{ route('admin.offers.store') }}" id="createOfferForm">
                    @csrf

                    <div class="form-field" style="margin-bottom:10px">
                        <label class="form-label">Offer Name *</label>
                        <input type="text" name="offer_name" class="form-control" placeholder="e.g. Summer 2026 Offer" required>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px">
                        <div class="form-field">
                            <label class="form-label">Discount Type *</label>
                            <select name="discount_type" class="form-control" id="discType" onchange="updatePreview()">
                                <option value="Percentage">Percentage %</option>
                                <option value="Fixed">Fixed LE</option>
                            </select>
                        </div>
                        <div class="form-field">
                            <label class="form-label">Value *</label>
                            <input type="number" name="discount_value" id="discValue" class="form-control" placeholder="e.g. 20" step="0.01" min="0.01" required onchange="updatePreview()">
                        </div>
                    </div>

                    <div id="discPreview" class="discount-preview"></div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px;margin-top:10px">
                        <div class="form-field">
                            <label class="form-label">Start Date *</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="form-field">
                            <label class="form-label">End Date *</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-field" style="margin-bottom:10px">
                        <label class="form-label">Apply to Courses * (select one or more)</label>
                        <div class="course-checks">
                            @foreach($courses as $course)
                            <label class="course-check-item">
                                <input type="checkbox" name="course_ids[]" value="{{ $course->course_template_id }}">
                                <span class="course-check-label">{{ $course->name }}</span>
                                @if($course->price)
                                <span style="margin-left:auto;font-size:10px;color:#1B4FA8;font-family:monospace">{{ number_format($course->price) }} LE</span>
                                @endif
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Create Offer</button>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
function toggleEdit(id) {
    document.getElementById(id)?.classList.toggle('show');
}

function updatePreview() {
    const type  = document.getElementById('discType').value;
    const val   = parseFloat(document.getElementById('discValue').value);
    const prev  = document.getElementById('discPreview');

    if (!val || val <= 0) { prev.style.display = 'none'; return; }

    if (type === 'Percentage') {
        prev.textContent = `Students get ${val}% off — e.g. 4,000 LE course → ${(4000 * (1 - val/100)).toLocaleString()} LE`;
    } else {
        prev.textContent = `Students save ${val.toLocaleString()} LE — e.g. 4,000 LE course → ${Math.max(0, 4000 - val).toLocaleString()} LE`;
    }
    prev.style.display = 'block';
}
</script>
@endsection