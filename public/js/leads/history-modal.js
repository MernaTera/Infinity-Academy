let currentLeadId = null;
let currentSelect = null;



function updateLeadStatus(select, leadId, newStatus) {
    
    document.querySelectorAll('.status-dropdown').forEach(d => d.style.display = 'none');

    const status = (newStatus || (select ? select.value : null))?.trim();
    if (!status) return;

    if (status === 'Call_Again') {
        currentLeadId = leadId;
        currentSelect = select;
        document.getElementById('callModal').style.display = 'flex';
        return;
    }

    sendUpdate(select, leadId, { status });
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
    let input = document.getElementById('callDate').value;

    if (!input) {
        alert("Please select date & time");
        return;
    }

    let formatted = input;

    sendUpdate(currentSelect, currentLeadId, {
        status: 'Call_Again',
        next_call_at: formatted
    });

    closeModal();
}


function openHistoryModal(leadId) {
    document.getElementById('historyModal').style.display = 'flex';
    document.getElementById('historyContent').innerHTML = `
        <div style="text-align:center;padding:32px 0;color:#AAB8C8;font-size:12px;letter-spacing:1px;">
            Loading...
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
                        <div style="font-size:12px;color:#AAB8C8;letter-spacing:1px;">No history found</div>
                    </div>`;
                return;
            }

            let html = '';
            data.forEach(item => {
                const oldS = (item.old_status ?? '—').replace(/_/g,' ');
                const newS = (item.new_status ?? '—').replace(/_/g,' ');
                const date = item.changed_at
                    ? new Date(item.changed_at).toLocaleDateString('en-GB', {day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'})
                    : '—';

                html += `
                <div style="display:flex;gap:14px;padding:14px 0;border-bottom:1px solid rgba(27,79,168,0.06);">
                    <div style="flex-shrink:0;margin-top:3px;">
                        <div style="width:8px;height:8px;border-radius:50%;background:#1B4FA8;opacity:0.5;"></div>
                    </div>
                    <div style="flex:1;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                            <span style="font-size:10px;letter-spacing:1px;text-transform:uppercase;
                                         background:rgba(122,138,154,0.1);border:1px solid rgba(122,138,154,0.2);
                                         color:#7A8A9A;padding:2px 8px;border-radius:3px;">
                                ${oldS}
                            </span>
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="2">
                                <path d="M5 12h14M13 6l6 6-6 6"/>
                            </svg>
                            <span style="font-size:10px;letter-spacing:1px;text-transform:uppercase;
                                         background:rgba(27,79,168,0.07);border:1px solid rgba(27,79,168,0.15);
                                         color:#1B4FA8;padding:2px 8px;border-radius:3px;">
                                ${newS}
                            </span>
                        </div>
                        ${item.notes ? `<div style="font-size:11px;color:#4A5A7A;margin-bottom:4px;">${item.notes}</div>` : ''}
                        <div style="font-size:10px;color:#AAB8C8;letter-spacing:0.5px;">${date}</div>
                    </div>
                </div>`;
            });

            document.getElementById('historyContent').innerHTML = html;
        })
        .catch(() => {
            document.getElementById('historyContent').innerHTML = `
                <div style="text-align:center;padding:32px;color:#DC2626;font-size:12px;">
                    Failed to load history
                </div>`;
        });
}

function closeHistoryModal() {
    document.getElementById('historyModal').style.display = 'none';
}

function toggleDropdown(badge) {
    const dropdown = badge.nextElementSibling;
    const isOpen = dropdown.style.display === 'block';
    document.querySelectorAll('.status-dropdown').forEach(d => d.style.display = 'none');
    dropdown.style.display = isOpen ? 'none' : 'block';
}

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