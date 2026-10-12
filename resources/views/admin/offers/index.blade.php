@extends('admin.layouts.app')
@section('title', 'Offers')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-2:#2D6FDB;--orange:#F5911E;--orange-dk:#C47010;--green:#059669;--green-dk:#15803D;--red:#DC2626;--text:#16233F;--muted:#5A6A85;--faint:#93A3BC;--card:rgba(255,255,255,0.5);--glass-bd:rgba(255,255,255,0.6);--glass-sh:0 8px 30px rgba(23,45,90,0.07);--bg:#F8F6F2;}
.off-page{background:var(--bg);min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:var(--text)}
.orb{position:absolute;border-radius:50%;filter:blur(70px);opacity:0.1;z-index:0;pointer-events:none}
.orb-2{width:300px;height:300px;background:radial-gradient(circle,#1B4FA8,transparent 70%);top:30px;right:1%}
.orb-3{width:380px;height:380px;background:radial-gradient(circle,#7C3AED,transparent 70%);bottom:-140px;left:35%}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--blue);margin-bottom:4px;font-weight:600}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:var(--text);margin:0}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px}

.btn-primary{display:inline-flex;align-items:center;gap:8px;padding:10px 22px;background:linear-gradient(120deg,var(--blue),var(--blue-2));border:none;border-radius:12px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:13px;letter-spacing:3px;cursor:pointer;position:relative;overflow:hidden;transition:transform 0.2s,box-shadow 0.2s;box-shadow:0 10px 24px rgba(27,79,168,0.28)}
.btn-primary::before{display:none}
.btn-primary:hover::before{transform:none}
.btn-primary:hover{color:#fff;transform:translateY(-2px);box-shadow:0 14px 30px rgba(27,79,168,0.36)}
.btn-primary span,.btn-primary svg{position:relative;z-index:1}

.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px}
.kpi-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:16px;padding:16px 20px;position:relative;overflow:hidden;box-shadow:var(--glass-sh)}
.kpi-card::before{display:none}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);margin-bottom:5px;font-weight:600}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:30px;letter-spacing:2px;color:var(--kc,var(--blue));line-height:1}

.two-col{display:grid;grid-template-columns:1fr 400px;gap:20px;align-items:start}

.offers-list{display:flex;flex-direction:column;gap:12px}
.offer-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:18px;overflow:hidden;position:relative;transition:transform 0.2s,box-shadow 0.2s;box-shadow:var(--glass-sh)}
.offer-card:hover{transform:translateY(-2px);box-shadow:0 16px 38px rgba(23,45,90,0.13)}
.offer-card.inactive{opacity:0.6}
.offer-card.active-now::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,var(--green-dk),transparent)}
.offer-card.upcoming::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,var(--blue),transparent)}
.offer-card.expired::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:rgba(122,138,154,0.3)}

.oc-header{padding:16px 20px;display:flex;justify-content:space-between;align-items:flex-start;border-bottom:1px solid rgba(27,79,168,0.06)}
.oc-name{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:2px;color:var(--text)}
.oc-discount{display:flex;align-items:baseline;gap:4px}
.oc-disc-val{font-family:'Bebas Neue',sans-serif;font-size:32px;color:var(--orange);letter-spacing:1px;line-height:1}
.oc-disc-unit{font-size:12px;color:var(--orange-dk);font-weight:600}

.oc-body{padding:14px 20px}
.oc-meta{display:flex;gap:16px;flex-wrap:wrap;margin-bottom:12px}
.oc-meta-item{display:flex;flex-direction:column;gap:2px}
.oc-meta-label{font-size:8px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);font-weight:600}
.oc-meta-val{font-size:12px;color:var(--text);font-weight:500}

.course-tags{display:flex;flex-wrap:wrap;gap:6px}
.course-tag{display:inline-block;padding:3px 10px;background:rgba(27,79,168,0.07);border:1px solid rgba(27,79,168,0.15);border-radius:6px;font-size:10px;color:var(--blue);letter-spacing:0.5px;font-weight:600}

.validity-wrap{margin-top:10px}
.validity-bar{background:rgba(27,79,168,0.08);border-radius:6px;height:4px;overflow:hidden;margin:6px 0}
.validity-fill{height:4px;border-radius:6px;transition:width .6s cubic-bezier(.16,1,.3,1)}
.validity-dates{display:flex;justify-content:space-between;font-size:9px;color:var(--faint)}

.oc-footer{padding:10px 20px;border-top:1px solid rgba(27,79,168,0.06);display:flex;gap:8px;background:rgba(255,255,255,0.3)}

.edit-panel{display:none;padding:16px 20px;background:rgba(255,255,255,0.35);border-top:1px solid rgba(27,79,168,0.06)}
.edit-panel.show{display:block;animation:panelIn .3s ease both}
@keyframes panelIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}
.edit-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px}
.form-field{display:flex;flex-direction:column;gap:4px}
.form-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);font-weight:600}
.form-control{width:100%;padding:8px 10px;border:1px solid rgba(27,79,168,0.12);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:12px;color:var(--text);background:rgba(255,255,255,0.7);outline:none;box-sizing:border-box}
.form-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(27,79,168,0.08)}

.badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 9px;border-radius:20px;font-weight:600}
.badge::before{content:'';width:4px;height:4px;border-radius:50%;background:currentColor;flex-shrink:0}
.badge-active{color:var(--green-dk);background:rgba(5,150,105,0.10);border:1px solid rgba(5,150,105,0.2)}
.badge-upcoming{color:var(--blue);background:rgba(27,79,168,0.08);border:1px solid rgba(27,79,168,0.18)}
.badge-expired{color:var(--muted);background:rgba(122,138,154,0.10);border:1px solid rgba(122,138,154,0.2)}
.badge-disabled{color:var(--red);background:rgba(220,38,38,0.08);border:1px solid rgba(220,38,38,0.18)}

.btn-sm{display:inline-flex;align-items:center;gap:4px;padding:5px 12px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:8px;border:1px solid;background:rgba(255,255,255,0.5);cursor:pointer;font-family:'DM Sans',sans-serif;font-weight:600;transition:all 0.2s}
.btn-edit{color:var(--blue);border-color:rgba(27,79,168,0.22)}
.btn-edit:hover{background:rgba(27,79,168,0.08)}
.btn-disable{color:var(--red);border-color:rgba(220,38,38,0.22)}
.btn-disable:hover{background:rgba(220,38,38,0.07)}
.btn-enable{color:var(--green-dk);border-color:rgba(5,150,105,0.25)}
.btn-enable:hover{background:rgba(5,150,105,0.08)}
.btn-save{padding:9px 20px;background:linear-gradient(120deg,var(--blue),var(--blue-2));border:none;border-radius:10px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:12px;letter-spacing:2px;cursor:pointer;box-shadow:0 6px 16px rgba(27,79,168,0.25);transition:transform 0.2s,box-shadow 0.2s}
.btn-save:hover{transform:translateY(-1px);box-shadow:0 10px 22px rgba(27,79,168,0.32)}

.create-panel{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:18px;overflow:hidden;position:relative;top:0;box-shadow:var(--glass-sh)}
.create-panel::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,var(--blue),transparent)}
.cp-header{padding:14px 18px;border-bottom:1px solid rgba(27,79,168,0.08)}
.cp-title{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:2px;color:var(--text)}
.cp-body{padding:18px}
.sec-label{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:var(--muted);margin-bottom:10px;display:block;font-weight:600}
.info-box{background:rgba(245,145,30,0.08);border:1px solid rgba(245,145,30,0.25);border-radius:12px;padding:10px 14px;font-size:11px;color:var(--orange-dk);margin-bottom:14px;line-height:1.5}

.course-checks{display:flex;flex-direction:column;gap:6px;max-height:200px;overflow-y:auto;padding:4px 0}
.course-check-item{display:flex;align-items:center;gap:8px;padding:7px 10px;border:1px solid rgba(27,79,168,0.10);border-radius:10px;cursor:pointer;transition:all 0.2s;background:rgba(255,255,255,0.5)}
.course-check-item:hover{background:rgba(27,79,168,0.05);border-color:rgba(27,79,168,0.2)}
.course-check-item input{accent-color:var(--blue);flex-shrink:0}
.course-check-label{font-size:12px;color:var(--text)}

.discount-preview{background:rgba(245,145,30,0.08);border:1px solid rgba(245,145,30,0.25);border-radius:12px;padding:10px 14px;font-size:12px;color:var(--orange-dk);margin-top:8px;display:none}

.btn-submit{width:100%;padding:11px;background:linear-gradient(120deg,var(--blue),var(--blue-2));border:none;border-radius:12px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:13px;letter-spacing:3px;cursor:pointer;position:relative;overflow:hidden;transition:transform 0.2s,box-shadow 0.2s;margin-top:10px;box-shadow:0 10px 24px rgba(27,79,168,0.28)}
.btn-submit::before{display:none}
.btn-submit:hover::before{transform:none}
.btn-submit:hover{color:#fff;transform:translateY(-2px);box-shadow:0 14px 30px rgba(27,79,168,0.36)}

.flash{padding:12px 16px;border-radius:12px;margin-bottom:20px;font-size:13px;border:1px solid;-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px)}
.flash-success{background:rgba(5,150,105,0.08);border-color:rgba(5,150,105,0.2);color:var(--green-dk)}
.flash-error{background:rgba(220,38,38,0.06);border-color:rgba(220,38,38,0.2);color:var(--red)}
.empty-offers{text-align:center;padding:48px;color:var(--faint);background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:18px;box-shadow:var(--glass-sh)}

@media(max-width:1024px){.two-col{grid-template-columns:1fr}.off-page{padding:18px 14px}.kpi-grid{grid-template-columns:repeat(2,1fr)}}
</style>

<div class="off-page">
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Admin Panel</div>
            <h1 class="page-title">Offers & Pricing</h1>
        </div>
    </div>

    @if(session('success'))
    <div class="flash flash-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="flash flash-error">{{ session('error') }}</div>
    @endif

    <div class="kpi-grid">
        <div class="kpi-card" style="--kc:var(--blue)"><div class="kpi-label">Total Offers</div><div class="kpi-val">{{ $stats['total'] }}</div></div>
        <div class="kpi-card" style="--kc:var(--green-dk)"><div class="kpi-label">Active Now</div><div class="kpi-val">{{ $stats['active'] }}</div></div>
        <div class="kpi-card" style="--kc:var(--blue-2)"><div class="kpi-label">Upcoming</div><div class="kpi-val">{{ $stats['upcoming'] }}</div></div>
        <div class="kpi-card" style="--kc:var(--muted)"><div class="kpi-label">Expired</div><div class="kpi-val">{{ $stats['expired'] }}</div></div>
    </div>

    <div class="two-col">

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

                    $start     = \Carbon\Carbon::parse($offer->start_date);
                    $end       = \Carbon\Carbon::parse($offer->end_date);
                    $totalDays = max(1, $start->diffInDays($end));
                    $elapsed   = max(0, min($totalDays, $start->diffInDays(now())));
                    $pct       = round($elapsed / $totalDays * 100);
                    $fillColor = $isActive ? '#15803D' : ($isExpired ? '#93A3BC' : '#1B4FA8');
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
                                <span style="font-size:9px;letter-spacing:1px;text-transform:uppercase;color:var(--faint);padding:3px 0">
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

                        @if($offer->courseTemplates->isNotEmpty())
                        <div class="course-tags">
                            @foreach($offer->courseTemplates as $ct)
                            <span class="course-tag">{{ $ct->name }}</span>
                            @endforeach
                        </div>
                        @endif

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
                <div class="empty-offers">
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;margin-bottom:6px;color:var(--muted)">No Offers Yet</div>
                    <div style="font-size:12px">Create your first seasonal offer</div>
                </div>
                @endforelse
            </div>
        </div>

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
                                <span style="margin-left:auto;font-size:10px;color:var(--blue);font-family:monospace">{{ number_format($course->price) }} LE</span>
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