// ═══════════════════════════════════════════════════════════════
// Infinity Academy — register-modal.js
// Clean pricing: courseFinalPrice | materialPrice | totalInvoice
// ═══════════════════════════════════════════════════════════════

// ─────────────────────────────────────────
// Branded Confirm Modal
// ─────────────────────────────────────────
(function injectConfirmModal() {
    if (document.getElementById('inf-confirm-overlay')) return;

    const style = document.createElement('style');
    style.textContent = `
        #inf-confirm-overlay {
            display:none; position:fixed; inset:0;
            background:rgba(10,20,40,0.45); backdrop-filter:blur(6px);
            z-index:99999; align-items:center; justify-content:center;
        }
        #inf-confirm-overlay.active { display:flex; animation:infOverlayIn 0.2s ease both; }
        @keyframes infOverlayIn { from{opacity:0} to{opacity:1} }
        .inf-confirm-box {
            background:rgba(255,255,255,0.97); backdrop-filter:blur(20px);
            border-radius:10px; width:420px; max-width:calc(100vw - 32px);
            overflow:hidden; position:relative;
            box-shadow:0 24px 60px rgba(27,79,168,0.15),0 4px 16px rgba(0,0,0,0.08);
            animation:infBoxIn 0.35s cubic-bezier(0.16,1,0.3,1) both;
        }
        @keyframes infBoxIn { from{opacity:0;transform:scale(0.94) translateY(12px)} to{opacity:1;transform:none} }
        .inf-confirm-box::before { content:''; position:absolute; top:0; left:0; right:0; height:2px;
            background:linear-gradient(90deg,transparent,#F5911E,#1B4FA8,transparent); }
        .inf-confirm-icon { display:flex; align-items:center; justify-content:center; padding:32px 32px 0; }
        .inf-confirm-icon-wrap { width:52px; height:52px; border-radius:50%;
            background:rgba(245,145,30,0.08); border:1px solid rgba(245,145,30,0.2);
            display:flex; align-items:center; justify-content:center; position:relative; }
        .inf-confirm-icon-pulse { position:absolute; inset:-8px; border-radius:50%;
            border:1px solid rgba(245,145,30,0.15); animation:infPulse 2s ease-in-out infinite; }
        @keyframes infPulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0;transform:scale(1.2)} }
        .inf-confirm-body { padding:20px 32px 28px; text-align:center; }
        .inf-confirm-label { font-family:'Bebas Neue',sans-serif; font-size:11px; letter-spacing:5px; color:#F5911E; text-transform:uppercase; margin-bottom:8px; }
        .inf-confirm-title { font-family:'Bebas Neue',sans-serif; font-size:22px; letter-spacing:3px; color:#1B4FA8; margin-bottom:10px; line-height:1.1; }
        .inf-confirm-message { font-family:'DM Sans',sans-serif; font-size:13px; font-weight:300; color:#7A8A9A; line-height:1.6; margin-bottom:28px; }
        .inf-confirm-actions { display:flex; gap:10px; justify-content:center; }
        .inf-confirm-cancel { flex:1; max-width:140px; padding:11px 20px; background:transparent;
            border:1px solid rgba(27,79,168,0.15); border-radius:4px; color:#7A8A9A;
            font-family:'DM Sans',sans-serif; font-size:11px; letter-spacing:2px; text-transform:uppercase;
            cursor:pointer; transition:all 0.2s; }
        .inf-confirm-cancel:hover { border-color:rgba(27,79,168,0.3); color:#1A2A4A; }
        .inf-confirm-ok { flex:1; max-width:180px; padding:11px 20px; background:transparent;
            border:1.5px solid #1B4FA8; border-radius:4px; color:#1B4FA8;
            font-family:'Bebas Neue',sans-serif; font-size:14px; letter-spacing:4px;
            cursor:pointer; position:relative; overflow:hidden; transition:color 0.35s,border-color 0.35s; }
        .inf-confirm-ok::before { content:''; position:absolute; inset:0;
            background:linear-gradient(90deg,#1B4FA8,#2D6FDB);
            transform:scaleX(0); transform-origin:left;
            transition:transform 0.35s cubic-bezier(0.16,1,0.3,1); }
        .inf-confirm-ok:hover::before { transform:scaleX(1); }
        .inf-confirm-ok:hover { color:#fff; border-color:#2D6FDB; }
        .inf-confirm-ok span { position:relative; z-index:1; }
        .inf-confirm-footer { padding:12px 32px; border-top:1px solid rgba(27,79,168,0.06);
            display:flex; align-items:center; gap:6px;
            font-family:'DM Sans',sans-serif; font-size:10px; color:#C8D4E0; letter-spacing:0.5px; }
    `;
    document.head.appendChild(style);

    const overlay = document.createElement('div');
    overlay.id = 'inf-confirm-overlay';
    overlay.innerHTML = `
        <div class="inf-confirm-box">
            <div class="inf-confirm-icon">
                <div class="inf-confirm-icon-wrap">
                    <div class="inf-confirm-icon-pulse"></div>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#F5911E" stroke-width="1.5">
                        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </div>
            </div>
            <div class="inf-confirm-body">
                <div class="inf-confirm-label"  id="inf-confirm-label">Confirm Action</div>
                <div class="inf-confirm-title"  id="inf-confirm-title">Are you sure?</div>
                <div class="inf-confirm-message" id="inf-confirm-message">This action cannot be undone.</div>
                <div class="inf-confirm-actions">
                    <button class="inf-confirm-cancel" id="inf-confirm-cancel">Cancel</button>
                    <button class="inf-confirm-ok" id="inf-confirm-ok"><span id="inf-confirm-ok-text">Confirm</span></button>

                </div>
            </div>
            <div class="inf-confirm-footer">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                Infinity Academy — Internal System
            </div>
        </div>`;
    document.body.appendChild(overlay);
    overlay.addEventListener('click', e => { if (e.target === overlay) infConfirm.reject(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') infConfirm.reject(); });
})();

const infConfirm = {
    _resolve: null,
    show({ label='Confirm Action', title='Are you sure?', message='This action cannot be undone.', okText='Confirm' } = {}) {
        return new Promise(resolve => {
            this._resolve = resolve;
            document.getElementById('inf-confirm-label').textContent   = label;
            document.getElementById('inf-confirm-title').textContent   = title;
            document.getElementById('inf-confirm-message').textContent = message;
            document.getElementById('inf-confirm-ok-text').textContent = okText;
            document.getElementById('inf-confirm-overlay').classList.add('active');
            document.getElementById('inf-confirm-ok').onclick     = () => this.confirm();
            document.getElementById('inf-confirm-cancel').onclick  = () => this.reject();
        });
    },
    confirm() { document.getElementById('inf-confirm-overlay').classList.remove('active'); if (this._resolve) this._resolve(true);  this._resolve = null; },
    reject()  { document.getElementById('inf-confirm-overlay').classList.remove('active'); if (this._resolve) this._resolve(false); this._resolve = null; }
};

// ─────────────────────────────────────────
// Leads index helpers
// ─────────────────────────────────────────
async function updateLeadStatus(el, leadId, newStatus) {
    document.querySelectorAll('.status-dropdown').forEach(d => d.style.display = 'none');
    if (newStatus === 'Registered') {
        const ok = await infConfirm.show({ label:'Lead Registration', title:'Register This Lead?',
            message:'This will convert the lead into a registered student and open the registration form.', okText:'Register Now' });
        if (ok) window.location.href = `/registration/from-lead/${leadId}`;
        return;
    }
    fetch(`/leads/${leadId}`, {
        method:'PUT',
        headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content, 'Accept':'application/json' },
        body: JSON.stringify({ status: newStatus })
    }).then(r=>r.json()).then(d=>{ if(d.success) location.reload(); });
}

function toggleDropdown(badge) {
    const dd = badge.nextElementSibling;
    const open = dd.style.display === 'block';
    document.querySelectorAll('.status-dropdown').forEach(d => d.style.display = 'none');
    if (!open) dd.style.display = 'block';
    setTimeout(() => {
        document.addEventListener('click', function c(e) {
            if (!dd.contains(e.target) && e.target !== badge) dd.style.display = 'none';
            document.removeEventListener('click', c);
        });
    }, 0);
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delete-lead-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const ok = await infConfirm.show({ label:'Delete Lead', title:'Delete This Lead?',
                message:'This will permanently remove the lead and all its history.', okText:'Delete' });
            if (ok) form.submit();
        });
    });
});

// ═══════════════════════════════════════════════════════════════
// Registration Form
// ═══════════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', function () {

    // ── DOM refs ──
    const course    = document.getElementById('course_select');
    if (!course) return; // not on registration page

    const level     = document.getElementById('level_select');
    const sublevel  = document.getElementById('sublevel_select');
    const patch     = document.getElementById('patch_select');
    const patchId   = document.getElementById('patch_id');
    const customDateWrap = document.getElementById('custom_date_wrap');
    const customDate     = document.getElementById('custom_date');
    const teacher        = document.getElementById('teacher_select');
    const teacherBlock   = document.getElementById('teacher_block');
    const bundle         = document.getElementById('bundle_select');
    const materialSection    = document.getElementById('material_section');
    const materialCheck      = document.getElementById('material_check');
    const materialPriceBlock = document.getElementById('material_price_block');
    const materialPriceHidden = document.getElementById('material_price_hidden');
    const paymentSelect  = document.getElementById('payment_plan_id');
    const registerBtn    = document.getElementById('register_btn');

    // ── Pricing state — single source of truth ──
    const pricing = {
        courseBasePrice:  0,   // raw price from sublevel/level/course
        courseDiscount:   0,   // offer discount
        courseFinalPrice: 0,   // courseBasePrice - courseDiscount  (OR package price)
        materialPrice:    0,   // material (separate from course)
        testFee:          0,   // test fee (separate)
        isPackage:        false,
        packageId:        null,
    };

    function fmt(n) { return parseFloat(n || 0).toFixed(2) + ' LE'; }

    // ── Update all price display fields from pricing state ──
    function updatePriceDisplay() {
        const base  = document.getElementById('base_price');
        const disc  = document.getElementById('discount');
        const final = document.getElementById('final_price');
        const finalH = document.getElementById('final_price_hidden');
        const discH  = document.getElementById('discount_hidden');

        if (pricing.isPackage) {
            if (base)   base.value   = fmt(pricing.courseFinalPrice);
            if (disc)   disc.value   = '0.00 LE';
            if (final)  final.value  = fmt(pricing.courseFinalPrice);
        } else {
            if (base)   base.value   = fmt(pricing.courseBasePrice);
            if (disc)   disc.value   = fmt(pricing.courseDiscount);
            if (final)  final.value  = fmt(pricing.courseFinalPrice);
        }

        if (finalH) finalH.value = pricing.courseFinalPrice;
        if (discH)  discH.value  = pricing.courseDiscount;

        // After price update — refresh deposit section and payment summary
        refreshPaymentSummary();
        refreshDepositSection();
    }

    // ── Fetch course price from backend ──
    function calculatePrice() {
        if (pricing.isPackage) return; // package overrides — don't recalculate

        const typeInput = document.querySelector('input[name="type"]:checked');
        if (!typeInput || !course.value) return;

        fetch('/calculate-price', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({
                type:               typeInput.value,
                course_template_id: course.value,
                level_id:           level.value,
                sublevel_id:        sublevel.value,
                bundle_id:          bundle?.value || null,
            })
        })
        .then(r => r.json())
        .then(data => {
            pricing.courseBasePrice  = parseFloat(data.base_price  || 0);
            pricing.courseDiscount   = parseFloat(data.discount     || 0);
            pricing.courseFinalPrice = parseFloat(data.final_price  || 0);
            updatePriceDisplay();
        });
    }

    // ─────────────────────────────────────────
    // Payment summary — on course price ONLY
    // ─────────────────────────────────────────
    function refreshPaymentSummary() {
        const detailsEl = document.getElementById('payment_details');
        if (!paymentSelect || !paymentSelect.value || pricing.courseFinalPrice <= 0) {
            if (detailsEl) detailsEl.style.display = 'none';
            return;
        }

        const selected      = paymentSelect.options[paymentSelect.selectedIndex];
        const depositPct    = parseFloat(selected.dataset.deposit      || 0);
        const installments  = parseInt(selected.dataset.installments   || 0);
        const grace         = parseInt(selected.dataset.grace          || 0);
        const needsApproval = selected.dataset.approval == 1;

        const coursePrice   = pricing.courseFinalPrice;
        const depositAmt    = (coursePrice * depositPct) / 100;
        const remaining     = coursePrice - depositAmt;
        

        // Summary rows
        let html =
            infPayRow('Plan',         selected.text) +
            infPayRow('Course Price', fmt(coursePrice), 'blue') +
            infPayRow('Deposit',      depositPct + '% = ' + fmt(depositAmt), 'accent') +
            infPayRow('Remaining',    fmt(remaining), 'blue');

        if (pricing.materialPrice > 0) {
            html += infPayRow('Material (full)', fmt(pricing.materialPrice));
        }
        if (pricing.testFee > 0) {
            html += infPayRow('Test Fee (full)', fmt(pricing.testFee));
        }

        const totalDueNow   = depositAmt + pricing.materialPrice + pricing.testFee;
        html += `<div style="height:1px;background:rgba(27,79,168,0.08);margin:8px 0;"></div>`;
        html += infPayRow('Due Now', fmt(totalDueNow), 'accent');

        if (needsApproval) {
            html += `<div style="display:inline-flex;align-items:center;gap:5px;font-size:9px;
                letter-spacing:2px;text-transform:uppercase;color:#92400E;
                background:rgba(245,145,30,0.12);border:1px solid rgba(245,145,30,0.25);
                border-radius:3px;padding:4px 9px;margin-top:8px;">⚠ Requires Admin Approval</div>`;
        }

        document.getElementById('payment_summary').innerHTML = html;

        // Installment table
        const table = document.getElementById('installments_table');
        const label = document.getElementById('installments_label');
        const tbody = table?.querySelector('tbody');
        if (tbody) tbody.innerHTML = '';

        if (installments > 0 && tbody) {
            const amt = remaining / installments;
            for (let i = 1; i <= installments; i++) {
                const due = new Date();
                due.setDate(due.getDate() + grace * i);
                tbody.innerHTML += `<tr><td>${i}</td><td>${fmt(amt)}</td><td>${due.toISOString().split('T')[0]}</td></tr>`;
            }
            if (table) table.style.display = 'table';
            if (label) label.style.display = 'block';
        } else {
            if (table) table.style.display = 'none';
            if (label) label.style.display = 'none';
        }

        if (detailsEl) detailsEl.style.display = 'block';
    }

    function infPayRow(key, val, cls='') {
        return `<div class="inf-pay-row"><span class="inf-pay-key">${key}</span><span class="inf-pay-val ${cls}">${val}</span></div>`;
    }

    // ─────────────────────────────────────────
    // Deposit section — based on course price only
    // ─────────────────────────────────────────
    function refreshDepositSection() {
        const section     = document.getElementById('deposit_section');
        const amountEl    = document.getElementById('deposit_required_amount');
        if (!section || !paymentSelect?.value) { if (section) section.style.display = 'none'; return; }

        const selected   = paymentSelect.options[paymentSelect.selectedIndex];
        const depositPct = parseFloat(selected.dataset.deposit || 0);
        const coursePrice = pricing.courseFinalPrice;

        if (depositPct > 0 && coursePrice > 0) {
            const depositAmt     = (coursePrice * depositPct / 100);
            const totalRequired  = depositAmt + pricing.materialPrice + pricing.testFee;
            const totalStr = totalRequired.toFixed(2);
            section.style.display = 'block';
            if (amountEl) amountEl.textContent = totalStr + ' LE';
            section.dataset.required = totalStr; 
            updatePaymentTotal();
        } else {
            section.style.display = 'none';
        }
    }

    // ─────────────────────────────────────────
    // Level Package
    // ─────────────────────────────────────────
    let selectedPackageData = null;

    function loadPackages(courseId) {
        const section   = document.getElementById('package_section');
        const container = document.getElementById('package_options');
        if (!section || !container) return;

        if (!courseId) { section.style.display = 'none'; return; }

        fetch(`/level-packages/${courseId}`)
            .then(r => r.json())
            .then(packages => {
                if (!packages.length) { section.style.display = 'none'; return; }

                section.style.display = 'block';
                container.innerHTML = '';

                // No Package card
                const noneCard = createPackageCard(null, null, null);
                noneCard.classList.add('selected');
                noneCard.innerHTML = `
                    <div class="package-card-label">No Package</div>
                    <div class="package-card-levels">—</div>
                    <div class="package-card-price">Regular price</div>
                    <div class="package-card-check"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>`;
                noneCard.addEventListener('click', () => applyPackage(null));
                container.appendChild(noneCard);

                packages.forEach(pkg => {
                    const card = createPackageCard(pkg.package_id, pkg.package_price, pkg.levels_count);
                    card.innerHTML = `
                        <div class="package-card-label">Package</div>
                        <div class="package-card-levels">${pkg.levels_count} <span style="font-size:16px;">Levels</span></div>
                        <div class="package-card-price">${parseFloat(pkg.package_price).toFixed(2)} LE</div>
                        <div class="package-card-per">${(pkg.package_price / pkg.levels_count).toFixed(0)} LE / level</div>
                        <div class="package-card-check"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>`;
                    card.addEventListener('click', () => applyPackage(pkg));
                    container.appendChild(card);
                });
            });
    }

    function createPackageCard(id, price, levels) {
        const card = document.createElement('div');
        card.className = 'package-card';
        if (id) { card.dataset.packageId = id; card.dataset.packagePrice = price; card.dataset.levelsCount = levels; }
        return card;
    }

    function applyPackage(pkg) {
        document.querySelectorAll('.package-card').forEach(c => c.classList.remove('selected'));
        const notice = document.getElementById('package_selected_notice');
        const pkgHidden = document.getElementById('package_id_hidden');

        if (!pkg) {
            // Revert to regular pricing
            pricing.isPackage        = false;
            pricing.packageId        = null;
            selectedPackageData      = null;
            if (pkgHidden) pkgHidden.value = '';
            if (notice)    notice.style.display = 'none';

            // Select "No Package" card
            document.querySelector('.package-card')?.classList.add('selected');

            calculatePrice(); // recalculate regular price
            return;
        }

        // Apply package
        pricing.isPackage        = true;
        pricing.packageId        = pkg.package_id;
        pricing.courseBasePrice  = parseFloat(pkg.package_price);
        pricing.courseDiscount   = 0;
        pricing.courseFinalPrice = parseFloat(pkg.package_price);
        selectedPackageData      = pkg;

        if (pkgHidden) pkgHidden.value = pkg.package_id;

        // Select this card
        document.querySelectorAll('.package-card').forEach(c => {
            if (c.dataset.packageId == pkg.package_id) c.classList.add('selected');
        });

        if (notice) {
            notice.style.display = 'flex';
            notice.style.gap = '8px';
            notice.style.alignItems = 'center';
            notice.innerHTML = `
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                Package applied — <strong>${pkg.levels_count} Levels</strong> at <strong>${parseFloat(pkg.package_price).toFixed(2)} LE</strong>`;
        }

        updatePriceDisplay();
    }

    // ─────────────────────────────────────────
    // Material
    // ─────────────────────────────────────────
    function loadMaterial() {
        if (!course.value) return;
        fetch('/get-material', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ course_template_id:course.value, level_id:level.value, sublevel_id:sublevel.value })
        })
        .then(r => r.json())
        .then(data => {
            if (!data?.material_id) {
                if (materialSection) materialSection.style.display = 'none';
                pricing.materialPrice = 0;
                updatePriceDisplay();
                return;
            }
            if (materialSection) materialSection.style.display = 'block';

            const nameEl  = document.getElementById('material_name');
            const priceEl = document.getElementById('material_price');
            const splitBadge = document.getElementById('material_split_badge');
            const splitText  = document.getElementById('material_split_text');

            if (nameEl)  nameEl.value  = data.name;
            if (priceEl) priceEl.value = parseFloat(data.price).toFixed(2) + ' LE';
            if (materialPriceHidden) materialPriceHidden.value = data.price;

            if (splitBadge && splitText && data.cs_percentage > 0) {
                const csAmt  = (data.price * data.cs_percentage / 100).toFixed(2);
                const acadAmt = (data.price - csAmt).toFixed(2);
                splitText.textContent = `CS commission: ${data.cs_percentage}% = ${csAmt} LE · Academy: ${acadAmt} LE`;
                splitBadge.style.display = 'flex';
            } else if (splitBadge) {
                splitBadge.style.display = 'none';
            }
        });
    }

    if (materialCheck) {
        materialCheck.addEventListener('change', function() {
            if (materialPriceBlock) materialPriceBlock.style.display = this.checked ? 'block' : 'none';
            pricing.materialPrice = this.checked ? parseFloat(materialPriceHidden?.value || 0) : 0;
            updatePriceDisplay();
        });
    }

    // ─────────────────────────────────────────
    // Test fee change
    // ─────────────────────────────────────────
    const testFeeInput = document.querySelector('[name="test_fee"]');
    if (testFeeInput) {
        testFeeInput.addEventListener('input', function() {
            pricing.testFee = parseFloat(this.value || 0);
            refreshPaymentSummary();
        });
    }

    // ─────────────────────────────────────────
    // Course / Level / Sublevel changes
    // ─────────────────────────────────────────
    course.addEventListener('change', async function() {
        level.innerHTML    = '<option value="">— Select Level —</option>';
        sublevel.innerHTML = '<option value="">— Select Sublevel —</option>';

        // Reset package
        applyPackage(null);

        if (!this.value) return;

        const res  = await fetch(`/levels/${this.value}`);
        const data = await res.json();
        data.forEach(l => { level.innerHTML += `<option value="${l.level_id}">${l.name}</option>`; });

        loadPatch();
        calculatePrice();
        loadMaterial();
        loadPackages(this.value);
    });

    level.addEventListener('change', async function() {
        sublevel.innerHTML = '<option value="">— Select Sublevel —</option>';
        if (!this.value) { calculatePrice(); return; }

        const res  = await fetch(`/sublevels/${this.value}`);
        const data = await res.json();
        data.forEach(s => { sublevel.innerHTML += `<option value="${s.sublevel_id}">${s.name}</option>`; });

        loadPatch();
        calculatePrice();
        loadMaterial();
    });

    sublevel.addEventListener('change', () => { calculatePrice(); loadMaterial(); });

    // ─────────────────────────────────────────
    // Type (group/private)
    // ─────────────────────────────────────────
    document.querySelectorAll('input[name="type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('private_extra').style.display = this.value === 'private' ? 'block' : 'none';
            calculatePrice();
            loadTeachers();
        });
    });

    bundle?.addEventListener('change', calculatePrice);
    paymentSelect?.addEventListener('change', () => { refreshPaymentSummary(); refreshDepositSection(); });

    // ─────────────────────────────────────────
    // Patch
    // ─────────────────────────────────────────
    function loadPatch() {
        if (!course.value) return;
        fetch(`/patch-options/${course.value}`)
            .then(r => r.json())
            .then(options => {
                patch.innerHTML = '';
                options.forEach(o => { patch.innerHTML += `<option value="${o.type}" data-id="${o.patch_id||''}">${o.label}</option>`; });
                patch.dispatchEvent(new Event('change'));
            });
    }

    if (patch) {
        patch.addEventListener('change', function() {
            const val = this.value;
            const sel = patch.options[patch.selectedIndex];
            patchId.value = sel?.dataset?.id || '';
            if (customDateWrap) { customDateWrap.style.display = val==='custom'?'block':'none'; if(customDate) customDate.required=val==='custom'; }
            if (teacherBlock)   teacherBlock.style.display = val==='current'?'block':'none';
            if (val !== 'current' && teacher) teacher.innerHTML = '<option value="">— Select Teacher —</option>';
            if (val === 'current') loadTeachers();
        });
    }

    // ─────────────────────────────────────────
    // Teachers
    // ─────────────────────────────────────────
    function loadTeachers() {
        if (!patch || patch.value !== 'current' || !teacherBlock) return;
        fetch('/available-teachers', {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
            body: JSON.stringify({ course_template_id:course.value, level_id:level.value, sublevel_id:sublevel.value, patch_id:patchId.value, patch_option:'current' })
        }).then(r=>r.json()).then(data => {
            teacher.innerHTML = '<option value="">— Select Teacher —</option>';
            if (!data.length) { teacher.innerHTML='<option disabled>No teachers available</option>'; return; }
            data.forEach(t => { teacher.innerHTML+=`<option value="${t.teacher_id}">Teacher #${t.teacher_id}</option>`; });
        });
    }

    if (teacher) {
        teacher.addEventListener('change', function() {
            if (!this.value) return;
            fetch('/teacher-schedule',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},body:JSON.stringify({teacher_id:this.value})})
            .then(r=>r.json()).then(data => {
                const ds = document.getElementById('day_select');
                if (!ds) return;
                ds.innerHTML='';
                [...new Set(data.map(a=>a.day_of_week))].forEach(d=>{ds.innerHTML+=`<option value="${d}">${d}</option>`;});
            });
        });
    }

    // ─────────────────────────────────────────
    // Deposit payment methods
    // ─────────────────────────────────────────
    let methodRowCount = 1;

    window.addPaymentMethod = function() {
        const container = document.getElementById('payment_methods_container');
        const idx = methodRowCount++;
        const row = document.createElement('div');
        row.className = 'payment-method-row'; row.id = `method_row_${idx}`;
        row.innerHTML = `
            <div class="form-field"><label class="form-label">Method</label>
                <select name="deposit_methods[${idx}][method]" class="form-control-inf method-select">
                    <option value="Cash">Cash</option><option value="Instapay">Instapay</option><option value="Vodafone_Cash">Vodafone Cash</option>
                </select></div>
            <div class="form-field"><label class="form-label">Amount (LE)</label>
                <input type="number" name="deposit_methods[${idx}][amount]" class="form-control-inf method-amount" placeholder="0.00" step="0.01" min="0" oninput="updatePaymentTotal()"></div>
            <button type="button" class="btn-remove-method" onclick="removePaymentMethod(${idx})">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>`;
        container.appendChild(row);
    };

    window.removePaymentMethod = function(idx) {
        const row = document.getElementById(`method_row_${idx}`);
        if (row) { row.style.transition='all 0.2s'; row.style.opacity='0'; setTimeout(()=>{row.remove();updatePaymentTotal();},200); }
    };

    window.updatePaymentTotal = function() {
        const amounts = document.querySelectorAll('.method-amount');
        const totalEl = document.getElementById('payment_total_display');
        const msgEl   = document.getElementById('payment_validation_msg');
        const section = document.getElementById('deposit_section');
        if (!totalEl || !section) return;

        let total = 0;
        amounts.forEach(i => { total += parseFloat(i.value||0); });
        const required = parseFloat(section.dataset.required || 0);
        totalEl.textContent = total.toFixed(2) + ' LE';

        if (required > 0) {
            const diff = Math.abs(total - required);
            if (diff < 0.01) {
                totalEl.className = 'payment-total-value success';
                msgEl.className   = 'payment-validation-msg success';
                msgEl.textContent = '✓ Payment amounts match the required deposit.';
            } else if (total > required) {
                totalEl.className = 'payment-total-value error';
                msgEl.className   = 'payment-validation-msg error';
                msgEl.textContent = `Amount (${total.toFixed(2)} LE) exceeds required deposit (${required} LE).`;
            } else {
                totalEl.className = 'payment-total-value error';
                msgEl.className   = 'payment-validation-msg error';
                msgEl.textContent = `Still need ${(required-total).toFixed(2)} LE more to complete the deposit.`;
            }
            msgEl.style.display = 'block';
        }
    };

    // ─────────────────────────────────────────
    // Confirm & Register
    // ─────────────────────────────────────────
    if (registerBtn) {
        registerBtn.addEventListener('click', async function() {
            if (!course?.value) {
                await infConfirm.show({ label:'Validation', title:'Missing Course', message:'Please select a course before registering.', okText:'OK' }); return;
            }
            if (!paymentSelect?.value) {
                await infConfirm.show({ label:'Validation', title:'Missing Payment Plan', message:'Please select a payment plan before registering.', okText:'OK' }); return;
            }

            // Deposit validation
            const depositSection = document.getElementById('deposit_section');
            if (depositSection && depositSection.style.display !== 'none') {
                const required = parseFloat(depositSection.dataset.required || 0);
                let total = 0;
                document.querySelectorAll('.method-amount').forEach(i => { total += parseFloat(i.value||0); });
                if (Math.abs(total - required) > 0.01) {
                    await infConfirm.show({ label:'Validation', title:'Deposit Incomplete',
                        message:`The deposit total (${total.toFixed(2)} LE) must equal the required deposit (${required.toFixed(2)} LE).`, okText:'OK' });
                    return;
                }
            }

            const studentName = document.getElementById('student_name')?.value || 'this student';
            const ok = await infConfirm.show({
                label:   'Confirm Registration',
                title:   'Register Student?',
                message: `Register ${studentName} with a course price of ${fmt(pricing.courseFinalPrice)}${pricing.materialPrice > 0 ? ' + material ' + fmt(pricing.materialPrice) : ''}. This will create a student profile and enrollment record.`,
                okText:  'Confirm & Register',
            });
            if (ok) document.getElementById('main_form').submit();
        });
    }

    // ─────────────────────────────────────────
    // Init
    // ─────────────────────────────────────────
    setTimeout(() => {
        loadPatch();
        calculatePrice();
        loadMaterial();
        if (course?.value) loadPackages(course.value);
    }, 250);
});