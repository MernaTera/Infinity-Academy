@auth
@php
    $rtEmployeeId = \App\Models\HR\Employee::where('user_id', auth()->id())->value('employee_id');
@endphp

@if($rtEmployeeId)
<style>
@keyframes rtToastIn { from { opacity:0; transform: translateX(20px) scale(0.98); } to { opacity:1; transform:none; } }
@keyframes rtToastOut { to { opacity:0; transform: translateX(20px); } }
@keyframes rtPulse { 0%,100% { transform: scale(1); } 50% { transform: scale(1.05); } }

.rt-toast {
    position: fixed;
    right: 24px;
    z-index: 99999;
    display: flex;
    flex-direction: column;
    padding: 0;
    background: #fff;
    border: 1px solid rgba(27,79,168,0.1);
    border-left: 3px solid var(--rt-accent, #F5911E);
    border-radius: 10px;
    box-shadow: 0 12px 40px rgba(15,31,61,0.2);
    font-family: 'DM Sans', sans-serif;
    min-width: 340px;
    max-width: 420px;
    cursor: pointer;
    animation: rtToastIn 0.4s cubic-bezier(0.16,1,0.3,1) both;
    transition: transform 0.15s, box-shadow 0.15s;
    overflow: hidden;
}
.rt-toast:hover { 
    transform: translateY(-2px); 
    box-shadow: 0 16px 48px rgba(15,31,61,0.28);
}
.rt-toast-head {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 16px;
    background: linear-gradient(135deg, rgba(245,145,30,0.04), transparent);
}
.rt-toast-icon {
    width: 34px; height: 34px;
    border-radius: 50%;
    background: rgba(245,145,30,0.1);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    animation: rtPulse 2s ease-in-out infinite;
}
.rt-toast-body {
    padding: 12px 16px;
    background: #F8F6F2;
    border-top: 1px solid rgba(27,79,168,0.05);
    font-size: 11px;
    color: #3A4A6A;
}
.rt-toast-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 3px 0;
    gap: 10px;
}
.rt-toast-label {
    color: #7A8A9A;
    font-size: 9.5px;
    letter-spacing: 1px;
    text-transform: uppercase;
    font-weight: 600;
}
.rt-toast-value {
    color: #1A2A4A;
    font-weight: 600;
    text-align: right;
    font-size: 11.5px;
}
.rt-toast-value.mono { font-family: 'Bebas Neue', sans-serif; letter-spacing: 1px; font-size: 14px; }
.rt-toast-cta {
    padding: 10px 16px;
    background: linear-gradient(135deg, #0F1F3D 0%, #1A2A4A 100%);
    color: #fff;
    font-size: 10px;
    letter-spacing: 2.5px;
    text-transform: uppercase;
    font-weight: 600;
    text-align: center;
    font-family: 'Bebas Neue', sans-serif;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let attempts = 0;
    const maxAttempts = 20;
    let toastStack = 0;

    const waitForEcho = setInterval(() => {
        attempts++;
        if (typeof window.Echo !== 'undefined') {
            clearInterval(waitForEcho);
            initRealtimeNotifications();
        } else if (attempts >= maxAttempts) {
            clearInterval(waitForEcho);
            console.warn('Echo not available after ' + maxAttempts + ' attempts');
        }
    }, 200);

    function initRealtimeNotifications() {
        window.Echo.private('employee.{{ $rtEmployeeId }}')
            .listen('.NotificationCreated', (data) => {
                playSound(data.priority === 'high');
                showToast(data);
                incrementBadge();

                window.dispatchEvent(new CustomEvent('notification-received', { detail: data }));
            });
    }

    function playSound(isHigh) {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.value = isHigh ? 720 : 520;
            gain.gain.setValueAtTime(0.25, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.5);
            osc.start(ctx.currentTime);
            osc.stop(ctx.currentTime + 0.5);

            if (isHigh) {
                setTimeout(() => {
                    const osc2 = ctx.createOscillator();
                    const gain2 = ctx.createGain();
                    osc2.connect(gain2);
                    gain2.connect(ctx.destination);
                    osc2.frequency.value = 900;
                    gain2.gain.setValueAtTime(0.2, ctx.currentTime);
                    gain2.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.4);
                    osc2.start(ctx.currentTime);
                    osc2.stop(ctx.currentTime + 0.4);
                }, 200);
            }
        } catch(e) {}
    }

    function getAccentColor(entityType) {
        const map = {
            'installment_request':  '#F5911E',
            'installment_approved': '#059669',
            'installment_rejected': '#DC2626',
            'refund_request':       '#F5911E',
            'refund_approved':      '#059669',
            'refund_rejected':      '#DC2626',
            'report_submitted':     '#1B4FA8',
        };
        return map[entityType] || '#F5911E';
    }

    function getIcon(entityType) {
        const map = {
            'installment_request': '<path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>',
            'installment_approved': '<polyline points="20 6 9 17 4 12"/>',
            'installment_rejected': '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>',
            'refund_request': '<path d="M3 10h10a8 8 0 0 1 8 8v2M3 10l6 6m-6-6l6-6"/>',
        };
        return map[entityType] || '<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>';
    }

    function showToast(data) {
        toastStack++;
        const stackOffset = 24 + (toastStack - 1) * 12; 
        const isHigh = data.priority === 'high';
        const accent = getAccentColor(data.entity_type);
        const icon = getIcon(data.entity_type);
        const meta = data.metadata || {};

        const t = document.createElement('div');
        t.className = 'rt-toast';
        t.style.cssText = `--rt-accent:${accent}; bottom:${stackOffset}px;`;
        t.onclick = () => {
            if (data.url && data.url !== '#') window.location = data.url;
        };

        let metaRows = '';
        if (meta.cs_name)           metaRows += buildRow('CS',           meta.cs_name);
        if (meta.student_name)      metaRows += buildRow('Student',      meta.student_name);
        if (meta.course_name)       metaRows += buildRow('Course',       meta.course_name);
        if (meta.plan_name)         metaRows += buildRow('Plan',         meta.plan_name);
        if (meta.total_price)       metaRows += buildRow('Total',        meta.total_price + ' LE', true);
        if (meta.deposit_amount)    metaRows += buildRow('Deposit',      meta.deposit_amount + ' LE', true);
        if (meta.remaining_amount)  metaRows += buildRow('Remaining',    meta.remaining_amount + ' LE', true);
        if (meta.installment_count) metaRows += buildRow('Installments', meta.installment_count + '×');

        const messageText = escapeHtml(data.message || '').replace(/\n/g, '<br>');

        t.innerHTML = `
            <div class="rt-toast-head">
                <div class="rt-toast-icon" style="background:${accent}1a;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="${accent}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">${icon}</svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:${accent};margin-bottom:4px;font-weight:700;">${escapeHtml(data.title || 'Notification')}</div>
                    <div style="font-size:12px;color:#1A2A4A;line-height:1.5;">${messageText}</div>
                </div>
                <button onclick="event.stopPropagation();closeToast(this.closest('.rt-toast'));"
                        style="background:none;border:none;cursor:pointer;color:#AAB8C8;
                               font-size:18px;line-height:1;padding:0 2px;flex-shrink:0;">×</button>
            </div>
            ${metaRows ? `<div class="rt-toast-body">${metaRows}</div>` : ''}
            ${data.url && data.url !== '#' ? `<div class="rt-toast-cta">Click to Review →</div>` : ''}
        `;

        document.body.appendChild(t);

        if (!isHigh) {
            setTimeout(() => closeToast(t), 6000);
        }
    }

    function buildRow(label, value, isMono = false) {
        return `
            <div class="rt-toast-row">
                <span class="rt-toast-label">${escapeHtml(label)}</span>
                <span class="rt-toast-value ${isMono ? 'mono' : ''}">${escapeHtml(String(value))}</span>
            </div>
        `;
    }

    window.closeToast = function(el) {
        if (!el) return;
        el.style.animation = 'rtToastOut 0.3s ease forwards';
        setTimeout(() => {
            el.remove();
            toastStack = Math.max(0, toastStack - 1);
        }, 300);
    };

    function incrementBadge() {
        ['bellBadge', 'abellBadge', 'tBellBadge', 'scBellBadge'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.display = 'block';
        });
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
});
</script>
@endif
@endauth