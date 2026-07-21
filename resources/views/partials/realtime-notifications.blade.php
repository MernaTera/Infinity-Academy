@auth
@php
    $rtEmployeeId = \App\Models\HR\Employee::where('user_id', auth()->id())->value('employee_id');
@endphp

@if($rtEmployeeId)
<style>
@keyframes rtToastIn { from { opacity:0; transform: translateX(20px); } to { opacity:1; transform:none; } }
@keyframes rtToastOut { to { opacity:0; transform: translateX(20px); } }
.rt-toast {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 99999;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 18px;
    background: rgba(255,255,255,0.99);
    border: 1px solid rgba(27,79,168,0.1);
    border-left: 3px solid #F5911E;
    border-radius: 8px;
    box-shadow: 0 8px 32px rgba(27,79,168,0.15);
    font-family: 'DM Sans', sans-serif;
    min-width: 280px;
    max-width: 400px;
    cursor: pointer;
    animation: rtToastIn 0.4s cubic-bezier(0.16,1,0.3,1) both;
    transition: transform 0.15s;
}
.rt-toast:hover { transform: translateY(-2px); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Wait for Echo to be available
    let attempts = 0;
    const maxAttempts = 20;
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
                playSound();
                showToast(data);
                incrementBadge();
            });
    }

    function playSound() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.value = 520;
            gain.gain.setValueAtTime(0.2, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.4);
            osc.start(ctx.currentTime);
            osc.stop(ctx.currentTime + 0.4);
        } catch(e) {}
    }

    function showToast(data) {
        const t = document.createElement('div');
        t.className = 'rt-toast';
        t.onclick = () => window.location = data.url || '#';
        t.innerHTML = `
            <div style="width:32px;height:32px;border-radius:50%;background:rgba(245,145,30,0.1);
                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#F5911E" stroke-width="2">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:9px;letter-spacing:3px;text-transform:uppercase;
                            color:#F5911E;margin-bottom:3px;">${escapeHtml(data.title || 'Notification')}</div>
                <div style="font-size:12px;color:#1A2A4A;line-height:1.4;">${escapeHtml(data.message || '')}</div>
            </div>
            <button onclick="event.stopPropagation();this.closest('.rt-toast').remove()"
                    style="background:none;border:none;cursor:pointer;color:#AAB8C8;
                           font-size:18px;line-height:1;padding:0 2px;">×</button>
        `;
        document.body.appendChild(t);
        setTimeout(() => {
            t.style.animation = 'rtToastOut 0.3s ease forwards';
            setTimeout(() => t.remove(), 300);
        }, 5000);
    }

    function incrementBadge() {
        // Show badge dots on all possible bell icons
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