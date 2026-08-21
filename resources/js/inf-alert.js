/* =========================================================
   INF ALERT — global branded alert dialog
   A styled, on-brand replacement for the native alert().
   Loaded in the main bundle, so window.infAlert is available
   on every page.

   Usage:
     infAlert('Something went wrong');                 // simple
     infAlert({ title:'Heads up', message:'...' });    // full
     infAlert({ type:'success', message:'Saved!' });   // success variant
     await infAlert('...')                             // resolves when dismissed

   Variants (type): 'error' (default), 'warning', 'success', 'info'.

   The look matches the existing register-page confirm dialog
   (gradient top bar, Bebas Neue heading, blue/orange accents).
   ========================================================= */
(function () {
    if (window.infAlert) return; // don't double-define

    const VARIANTS = {
        error:   { accent: '#DC2626', soft: 'rgba(220,38,38,0.08)',  border: 'rgba(220,38,38,0.2)',  label: 'Error' },
        warning: { accent: '#F5911E', soft: 'rgba(245,145,30,0.08)', border: 'rgba(245,145,30,0.2)', label: 'Heads up' },
        success: { accent: '#059669', soft: 'rgba(5,150,105,0.08)',  border: 'rgba(5,150,105,0.2)',  label: 'Success' },
        info:    { accent: '#1B4FA8', soft: 'rgba(27,79,168,0.08)',  border: 'rgba(27,79,168,0.2)',  label: 'Notice' },
    };

    const ICONS = {
        error:   '<line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/><circle cx="12" cy="12" r="10"/>',
        warning: '<path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
        success: '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
        info:    '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>',
    };

    function injectStyle() {
        if (document.getElementById('inf-alert-style')) return;
        const style = document.createElement('style');
        style.id = 'inf-alert-style';
        style.textContent = `
        #inf-alert-overlay { display:none; position:fixed; inset:0; background:rgba(10,20,40,0.45); backdrop-filter:blur(6px); z-index:100000; align-items:center; justify-content:center; }
        #inf-alert-overlay.active { display:flex; animation:infAlertOverlayIn 0.2s ease both; }
        @keyframes infAlertOverlayIn { from{opacity:0} to{opacity:1} }
        .inf-alert-box { background:rgba(255,255,255,0.97); backdrop-filter:blur(20px); border-radius:10px; width:420px; max-width:calc(100vw - 32px); overflow:hidden; position:relative; box-shadow:0 24px 60px rgba(27,79,168,0.15); animation:infAlertBoxIn 0.35s cubic-bezier(0.16,1,0.3,1) both; }
        @keyframes infAlertBoxIn { from{opacity:0;transform:scale(0.94) translateY(12px)} to{opacity:1;transform:none} }
        .inf-alert-box::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; background:linear-gradient(90deg,transparent,#F5911E,#1B4FA8,transparent); }
        .inf-alert-icon { display:flex; align-items:center; justify-content:center; padding:32px 32px 0; }
        .inf-alert-icon-wrap { width:52px; height:52px; border-radius:50%; display:flex; align-items:center; justify-content:center; position:relative; }
        .inf-alert-icon-pulse { position:absolute; inset:-8px; border-radius:50%; animation:infAlertPulse 2s ease-in-out infinite; }
        @keyframes infAlertPulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0;transform:scale(1.2)} }
        .inf-alert-body { padding:20px 32px 28px; text-align:center; }
        .inf-alert-label { font-family:'Bebas Neue',sans-serif; font-size:11px; letter-spacing:5px; text-transform:uppercase; margin-bottom:8px; }
        .inf-alert-title { font-family:'Bebas Neue',sans-serif; font-size:22px; letter-spacing:3px; color:#1B4FA8; margin-bottom:10px; line-height:1.15; }
        .inf-alert-message { font-family:'DM Sans',sans-serif; font-size:13px; font-weight:300; color:#7A8A9A; line-height:1.6; margin-bottom:28px; white-space:pre-line; }
        .inf-alert-actions { display:flex; gap:10px; justify-content:center; }
        .inf-alert-ok { min-width:150px; padding:11px 26px; background:transparent; border:1.5px solid #1B4FA8; border-radius:4px; color:#1B4FA8; font-family:'Bebas Neue',sans-serif; font-size:14px; letter-spacing:4px; cursor:pointer; position:relative; overflow:hidden; transition:color 0.35s; }
        .inf-alert-ok::before { content:''; position:absolute; inset:0; background:linear-gradient(90deg,#1B4FA8,#2D6FDB); transform:scaleX(0); transform-origin:left; transition:transform 0.35s cubic-bezier(0.16,1,0.3,1); }
        .inf-alert-ok:hover::before { transform:scaleX(1); }
        .inf-alert-ok:hover { color:#fff; }
        .inf-alert-ok span { position:relative; z-index:1; }
        .inf-alert-footer { padding:12px 32px; border-top:1px solid rgba(27,79,168,0.06); display:flex; align-items:center; justify-content:center; gap:6px; font-family:'DM Sans',sans-serif; font-size:10px; color:#C8D4E0; }
        `;
        document.head.appendChild(style);
    }

    function buildOverlay() {
        let overlay = document.getElementById('inf-alert-overlay');
        if (overlay) return overlay;
        overlay = document.createElement('div');
        overlay.id = 'inf-alert-overlay';
        overlay.innerHTML = `<div class="inf-alert-box" role="alertdialog" aria-modal="true">
            <div class="inf-alert-icon">
                <div class="inf-alert-icon-wrap" id="inf-alert-icon-wrap">
                    <div class="inf-alert-icon-pulse" id="inf-alert-icon-pulse"></div>
                    <svg id="inf-alert-icon-svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></svg>
                </div>
            </div>
            <div class="inf-alert-body">
                <div class="inf-alert-label" id="inf-alert-label">Notice</div>
                <div class="inf-alert-title" id="inf-alert-title">Heads up</div>
                <div class="inf-alert-message" id="inf-alert-message"></div>
                <div class="inf-alert-actions">
                    <button class="inf-alert-ok" id="inf-alert-ok"><span id="inf-alert-ok-text">OK</span></button>
                </div>
            </div>
            <div class="inf-alert-footer"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>Infinity Academy</div>
        </div>`;
        document.body.appendChild(overlay);
        return overlay;
    }

    let resolver = null;

    function close() {
        const overlay = document.getElementById('inf-alert-overlay');
        if (overlay) overlay.classList.remove('active');
        document.removeEventListener('keydown', onKey, true);
        const r = resolver; resolver = null;
        if (r) r();
    }

    function onKey(e) {
        if (e.key === 'Escape' || e.key === 'Enter') {
            e.stopPropagation();
            close();
        }
    }

    window.infAlert = function (opts) {
        // Allow a bare string: infAlert('message')
        if (typeof opts === 'string') opts = { message: opts };
        opts = opts || {};

        const type = VARIANTS[opts.type] ? opts.type : 'error';
        const v = VARIANTS[type];
        const title = opts.title || (type === 'error' ? 'Something went wrong'
            : type === 'success' ? 'Done'
            : type === 'warning' ? 'Heads up'
            : 'Notice');
        const label = opts.label || v.label;
        const message = opts.message || '';
        const okText = opts.okText || 'OK';

        injectStyle();
        const overlay = buildOverlay();

        // apply variant colours
        const iconWrap  = overlay.querySelector('#inf-alert-icon-wrap');
        const iconPulse = overlay.querySelector('#inf-alert-icon-pulse');
        const iconSvg   = overlay.querySelector('#inf-alert-icon-svg');
        iconWrap.style.background = v.soft;
        iconWrap.style.border = '1px solid ' + v.border;
        iconPulse.style.border = '1px solid ' + v.border;
        iconSvg.setAttribute('stroke', v.accent);
        iconSvg.innerHTML = ICONS[type];

        overlay.querySelector('#inf-alert-label').style.color = v.accent;
        overlay.querySelector('#inf-alert-label').textContent   = label;
        overlay.querySelector('#inf-alert-title').textContent   = title;
        overlay.querySelector('#inf-alert-message').textContent = message;
        overlay.querySelector('#inf-alert-ok-text').textContent = okText;

        overlay.classList.add('active');

        const okBtn = overlay.querySelector('#inf-alert-ok');
        okBtn.onclick = close;
        overlay.onclick = function (e) { if (e.target === overlay) close(); };
        document.addEventListener('keydown', onKey, true);
        setTimeout(function () { okBtn.focus(); }, 50);

        return new Promise(function (resolve) { resolver = resolve; });
    };

    // Convenience variants
    window.infAlert.success = function (message, opts) { return window.infAlert(Object.assign({ type: 'success', message }, opts || {})); };
    window.infAlert.error   = function (message, opts) { return window.infAlert(Object.assign({ type: 'error',   message }, opts || {})); };
    window.infAlert.warning = function (message, opts) { return window.infAlert(Object.assign({ type: 'warning', message }, opts || {})); };
    window.infAlert.info    = function (message, opts) { return window.infAlert(Object.assign({ type: 'info',    message }, opts || {})); };
})();
