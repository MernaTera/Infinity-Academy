let currentLeadId = null;
let currentSelect = null;



async function updateLeadStatus(el, leadId, newStatus) {
    document.querySelectorAll('.status-dropdown').forEach(d => d.style.display='none');

    const status = (newStatus || el?.value)?.trim();
    if (!status) return;

    if (status === 'Call_Again') {
        const modal = document.getElementById('callModal');
        if (modal) {
            modal.style.display = 'flex';
            modal._leadId = leadId;
        }
        return;
    }

    if (status === 'Registered') {
        const ok = await infConfirm.show({
            label:   'Lead Registration',
            title:   'Register This Lead?',
            message: 'This will convert the lead into a registered student and open the registration form.',
            okText:  'Register Now'
        });
        if (ok) window.location.href = `/registration/from-lead/${leadId}`;
        return;
    }

    fetch(`/leads/${leadId}`, {
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'Accept':'application/json',
            'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,
            'X-HTTP-Method-Override':'PUT'
        },
        body:JSON.stringify({ _method:'PUT', status })
        }).then(r=>r.json()).then(d=>{ if(d.success) location.reload(); });
    }
function sendUpdate(select, leadId, data) {
    fetch(`/leads/${leadId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-HTTP-Method-Override': 'PUT',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            _method: 'PUT',
            ...data
        })
    })
    .then(res => {
        if (!res.ok) throw new Error(res.status);
        return res.json();
    })
    .then(() => {
        select.style.borderColor = "#22C55E";
        setTimeout(() => location.reload(), 600);
    })
    .catch(err => {
        select.style.borderColor = "#DC2626";
        console.error('Update failed:', err);
    });
}
function closeModal() {
    document.getElementById('callModal').style.display = 'none';
}

function confirmCall() {
    const modal  = document.getElementById('callModal');
    const leadId = modal?._leadId;
    const input  = document.getElementById('callDate').value;

    if (!leadId) { alert('Error: Lead not found'); return; }
    if (!input)  { alert('Please select date & time'); return; }

    fetch(`/leads/${leadId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-HTTP-Method-Override': 'PUT'
        },
        body: JSON.stringify({
            _method: 'PUT',
            status: 'Call_Again',
            next_call_at: input
        })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) { modal.style.display='none'; location.reload(); }
    });
}

// ── Notes popup ──
function openNotes(btn){
    const note    = btn.getAttribute('data-note') || '';
    const student = btn.getAttribute('data-student') || 'Note';
    document.getElementById('notesModalBody').textContent = note;
    document.getElementById('notesModalStudent').textContent = student;
    document.getElementById('notesOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeNotes(){
    document.getElementById('notesOverlay').classList.remove('open');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if(e.key === 'Escape') closeNotes(); });

function openHistoryModal(leadId) {
    document.getElementById('historyModal').style.display = 'flex';
    document.getElementById('historyContent').innerHTML = `
        <div style="text-align:center;padding:32px 0;color:#AAB8C8;font-size:12px;letter-spacing:1px;">
            Loading timeline...
        </div>`;

    fetch(`/leads/${leadId}/history`)
        .then(res => res.json())
        .then(data => {
            if (!data.length) {
                document.getElementById('historyContent').innerHTML = `
                    <div style="text-align:center;padding:40px 0;">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="1" style="margin:0 auto 12px;display:block;">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                        <div style="font-size:12px;color:#AAB8C8;letter-spacing:1px;">No activity yet</div>
                    </div>`;
                return;
            }

            // Action type metadata
            const actionMeta = {
                'Created':          { color: '#059669', bg: 'rgba(5,150,105,0.08)',   label: 'Lead Created',       icon: '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>' },
                'Status_Changed':   { color: '#1B4FA8', bg: 'rgba(27,79,168,0.07)',    label: 'Status Changed',     icon: '<path d="M23 4v6h-6"/><path d="M1 20v-6h6"/><path d="M20.49 9A9 9 0 005.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 013.51 15"/>' },
                'Data_Updated':     { color: '#F5911E', bg: 'rgba(245,145,30,0.08)',   label: 'Data Updated',       icon: '<path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>' },
                'Owner_Changed':    { color: '#7C3AED', bg: 'rgba(124,58,237,0.07)',   label: 'Owner Changed',      icon: '<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>' },
                'Call_Logged':     { color: '#0891B2', bg: 'rgba(8,145,178,0.08)',    label: 'Call Logged',        icon: '<path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.12.85.33 1.68.63 2.47a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.62-1.62a2 2 0 012.11-.45c.79.3 1.62.51 2.47.63A2 2 0 0122 16.92z"/>' },
                'Registered':       { color: '#059669', bg: 'rgba(5,150,105,0.08)',    label: 'Registered',         icon: '<polyline points="20 6 9 17 4 12"/>' },
                'Archived':         { color: '#7A8A9A', bg: 'rgba(122,138,154,0.1)',  label: 'Archived',           icon: '<polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/>' },
                'Restored':         { color: '#0891B2', bg: 'rgba(8,145,178,0.08)',    label: 'Restored',           icon: '<path d="M3 12a9 9 0 019-9 9.75 9.75 0 016.74 2.74L21 8"/><path d="M21 3v5h-5"/>' },
                'Note_Added':       { color: '#7C3AED', bg: 'rgba(124,58,237,0.07)',   label: 'Note Added',         icon: '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>' },
                'Auto_Public':      { color: '#F5911E', bg: 'rgba(245,145,30,0.08)',   label: 'Made Public (Auto)', icon: '<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/>' },
                'Auto_Archived':    { color: '#DC2626', bg: 'rgba(220,38,38,0.06)',    label: 'Archived (Auto)',    icon: '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>' },
                'Interest_Updated': { color: '#F5911E', bg: 'rgba(245,145,30,0.08)',   label: 'Interest Updated',   icon: '<path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>' },
            };

            // Format status for display
            const formatStatus = (s) => (s ?? '').replace(/_/g, ' ');

            // Format date/time
            const formatDate = (d) => d
                ? new Date(d).toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' })
                : '—';

            // Relative time
            const relativeTime = (d) => {
                if (!d) return '';
                const diff = (Date.now() - new Date(d).getTime()) / 1000;
                if (diff < 60) return 'just now';
                if (diff < 3600) return `${Math.floor(diff/60)}m ago`;
                if (diff < 86400) return `${Math.floor(diff/3600)}h ago`;
                if (diff < 604800) return `${Math.floor(diff/86400)}d ago`;
                return '';
            };

            let html = '<div style="position:relative;padding-left:8px;">';

            data.forEach((item, idx) => {
                const meta = actionMeta[item.action_type] || actionMeta['Status_Changed'];
                const isLast = idx === data.length - 1;

                html += `
                <div style="display:flex;gap:14px;position:relative;padding-bottom:${isLast ? '0' : '18px'};">
                    <!-- Timeline vertical line + dot -->
                    <div style="flex-shrink:0;position:relative;width:32px;">
                        <div style="width:32px;height:32px;border-radius:50%;background:${meta.bg};
                                    border:2px solid #fff;display:flex;align-items:center;justify-content:center;
                                    box-shadow:0 2px 8px rgba(27,79,168,0.1);position:relative;z-index:2;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="${meta.color}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                ${meta.icon}
                            </svg>
                        </div>
                        ${!isLast ? `<div style="position:absolute;left:15px;top:32px;bottom:-18px;width:2px;background:rgba(27,79,168,0.08);"></div>` : ''}
                    </div>

                    <!-- Content -->
                    <div style="flex:1;padding:2px 0;min-width:0;">
                        <!-- Action label + relative time -->
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;flex-wrap:wrap;">
                            <span style="font-family:'Bebas Neue',sans-serif;font-size:12px;letter-spacing:2px;
                                         color:${meta.color};font-weight:600;">
                                ${meta.label}
                            </span>
                            ${relativeTime(item.changed_at) ? `<span style="font-size:10px;color:#AAB8C8;letter-spacing:0.5px;">· ${relativeTime(item.changed_at)}</span>` : ''}
                        </div>

                        <!-- Status change badges (only for Status_Changed) -->
                        ${(item.action_type === 'Status_Changed' || item.action_type === 'Registered' || item.action_type === 'Archived' || item.action_type === 'Restored' || item.action_type === 'Auto_Public' || item.action_type === 'Auto_Archived') && (item.old_status || item.new_status) ? `
                        <div style="display:flex;align-items:center;gap:6px;margin-bottom:8px;flex-wrap:wrap;">
                            <span style="font-size:9px;letter-spacing:1px;text-transform:uppercase;
                                         background:rgba(122,138,154,0.1);border:1px solid rgba(122,138,154,0.2);
                                         color:#7A8A9A;padding:3px 8px;border-radius:3px;font-weight:600;">
                                ${formatStatus(item.old_status) || '—'}
                            </span>
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="2">
                                <path d="M5 12h14M13 6l6 6-6 6"/>
                            </svg>
                            <span style="font-size:9px;letter-spacing:1px;text-transform:uppercase;
                                         background:${meta.bg};border:1px solid ${meta.color}33;
                                         color:${meta.color};padding:3px 8px;border-radius:3px;font-weight:600;">
                                ${formatStatus(item.new_status) || '—'}
                            </span>
                        </div>` : ''}

                        <!-- Call outcome -->
                        ${item.call_outcome ? `
                        <div style="margin-bottom:8px;">
                            <span style="font-size:9px;letter-spacing:1px;text-transform:uppercase;
                                         background:rgba(8,145,178,0.08);border:1px solid rgba(8,145,178,0.2);
                                         color:#0891B2;padding:3px 8px;border-radius:3px;font-weight:600;">
                                📞 ${formatStatus(item.call_outcome)}
                            </span>
                        </div>` : ''}

                        <!-- Changed fields (Data_Updated / Owner_Changed / Interest_Updated) -->
                        ${item.changed_fields && item.changed_fields.length ? `
                        <div style="background:#F8F6F2;border-radius:6px;padding:10px 12px;margin-bottom:8px;">
                            ${item.changed_fields.map(f => `
                                <div style="display:flex;align-items:center;gap:8px;padding:4px 0;font-size:11px;flex-wrap:wrap;">
                                    <span style="color:#7A8A9A;font-weight:600;min-width:110px;">${f.label}:</span>
                                    <span style="color:#AAB8C8;text-decoration:line-through;font-family:monospace;font-size:11px;">${f.from}</span>
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                                    <span style="color:#1A2A4A;font-weight:600;font-family:monospace;font-size:11px;">${f.to}</span>
                                </div>
                            `).join('')}
                        </div>` : ''}

                        <!-- Reason -->
                        ${item.reason ? `
                        <div style="font-size:11.5px;color:#3A4A6A;margin-bottom:6px;line-height:1.5;padding:6px 10px;background:rgba(27,79,168,0.03);border-left:2px solid ${meta.color};border-radius:3px;">
                            <strong style="color:${meta.color};font-size:9px;letter-spacing:1.5px;text-transform:uppercase;">Reason:</strong>
                            <span style="margin-left:5px;">${item.reason}</span>
                        </div>` : ''}

                        <!-- Notes -->
                        ${item.notes ? `
                        <div style="font-size:11px;color:#7A8A9A;margin-bottom:6px;font-style:italic;line-height:1.5;">
                            "${item.notes}"
                        </div>` : ''}

                        <!-- Footer: who + when + IP -->
                        <div style="display:flex;align-items:center;gap:8px;font-size:10px;color:#AAB8C8;letter-spacing:0.3px;flex-wrap:wrap;">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <span style="color:#7A8A9A;font-weight:500;">${item.changed_by_name}</span>
                            <span>·</span>
                            <span>${formatDate(item.changed_at)}</span>
                            ${item.ip_address ? `<span>·</span><span title="IP: ${item.ip_address}" style="font-family:monospace;">${item.ip_address}</span>` : ''}
                        </div>
                    </div>
                </div>`;
            });

            html += '</div>';
            document.getElementById('historyContent').innerHTML = html;
        })
        .catch((err) => {
            console.error(err);
            document.getElementById('historyContent').innerHTML = `
                <div style="text-align:center;padding:32px;color:#DC2626;font-size:12px;">
                    Failed to load activity timeline
                </div>`;
        });
}

function closeHistoryModal() {
    document.getElementById('historyModal').style.display = 'none';
}

function toggleDropdown(badge) {
    // Close all others
    document.querySelectorAll('.status-dropdown').forEach(d => {
        if (d !== badge.nextElementSibling) d.style.display = 'none';
    });

    const dropdown = badge.nextElementSibling;
    const isOpen   = dropdown.style.display === 'block';

    if (isOpen) {
        dropdown.style.display = 'none';
        return;
    }

    const rect = badge.getBoundingClientRect();
    dropdown.style.position = 'fixed';
    dropdown.style.top      = (rect.bottom + 6) + 'px';
    dropdown.style.left     = rect.left + 'px';
    dropdown.style.zIndex   = '9999';
    dropdown.style.display  = 'block';
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.status-badge') && !e.target.closest('.status-dropdown')) {
        document.querySelectorAll('.status-dropdown').forEach(d => d.style.display = 'none');
    }
});

document.addEventListener('click', e => {
    if (!e.target.closest('.status-badge') && !e.target.closest('.status-dropdown')) {
        document.querySelectorAll('.status-dropdown').forEach(d => d.style.display = 'none');
    }
});

function filterByStatus(status) {
    document.querySelectorAll('.status-dropdown').forEach(d => d.style.display = 'none');

    document.querySelectorAll('.stat-card').forEach(card => {
        card.classList.remove('active-filter');
        if (card.dataset.filter === status) {
            card.classList.add('active-filter');
        }
    });

    document.querySelectorAll('tbody tr[data-status]').forEach(row => {
        if (status === 'all') {
            row.style.display = '';
        } else {
            row.style.display = row.dataset.status === status ? '' : 'none';
        }
    });
}

function searchLeads(query) {
    const q = query.toLowerCase().trim();
    const activeFilter = document.querySelector('.stat-card.active-filter');
    const currentFilter = activeFilter ? activeFilter.dataset.filter : 'all';

    document.querySelectorAll('tbody tr[data-status]').forEach(row => {
        const name  = row.querySelector('.lead-name')?.textContent.toLowerCase() ?? '';
        const phone = row.querySelector('.lead-phone')?.textContent.toLowerCase() ?? '';

        const matchesSearch = q === '' || name.includes(q) || phone.includes(q);
        const matchesFilter = currentFilter === 'all' || row.dataset.status === currentFilter;

        row.style.display = (matchesSearch && matchesFilter) ? '' : 'none';
    });
}