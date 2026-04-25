<style>
    .inf-modal-backdrop {
        display:none; position:fixed; inset:0; z-index:1050;
        background:rgba(8,15,35,0.7); backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px);
        align-items:center; justify-content:center; padding:20px; font-family:'DM Sans',sans-serif;
    }
    .inf-modal-backdrop.show { display:flex; animation:backdropIn 0.3s ease both; }
    @keyframes backdropIn { from{opacity:0} to{opacity:1} }

    .inf-modal {
        width:100%; background:#fff; border-radius:12px; 
        position:relative; box-shadow:0 40px 100px rgba(0,0,0,0.25),0 8px 24px rgba(27,79,168,0.15);
        display:flex; flex-direction:column;
        animation:modalIn 0.4s cubic-bezier(0.16,1,0.3,1) both;
    }
    @keyframes modalIn { from{opacity:0;transform:scale(0.93) translateY(24px)} to{opacity:1;transform:none} }
    .inf-modal::before { content:''; position:absolute; top:0; left:0; right:0; height:4px;
        background:linear-gradient(90deg,#F5911E 0%,#1B4FA8 50%,#2D6FDB 100%); z-index:2; }

    .inf-modal-header {
        padding:24px 28px 20px; background:linear-gradient(135deg,#1A2A4A 0%,#1B4FA8 100%);
        display:flex; align-items:flex-start; justify-content:space-between; flex-shrink:0; position:relative;
    }
    .inf-modal-header::after { content:''; position:absolute; bottom:0; left:0; right:0; height:1px; background:rgba(255,255,255,0.1); }
    .inf-modal-eyebrow { font-size:9px; letter-spacing:5px; text-transform:uppercase; color:rgba(255,255,255,0.5); margin-bottom:4px; }
    .inf-modal-title { font-family:'Bebas Neue',sans-serif; font-size:30px; letter-spacing:5px; color:#fff; line-height:1; margin-bottom:6px; }
    .inf-modal-meta { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
    .inf-modal-id { font-size:10px; letter-spacing:2px; text-transform:uppercase; color:rgba(255,255,255,0.6);
        background:rgba(255,255,255,0.1); padding:4px 10px; border-radius:20px; border:1px solid rgba(255,255,255,0.15); }
    .inf-modal-date { font-size:10px; color:rgba(255,255,255,0.4); letter-spacing:0.5px; }
    .inf-modal-close { width:32px; height:32px; display:flex; align-items:center; justify-content:center;
        background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.15); border-radius:50%;
        cursor:pointer; color:rgba(255,255,255,0.6); transition:all 0.2s; flex-shrink:0; margin-top:2px; }
    .inf-modal-close:hover { background:rgba(220,38,38,0.3); border-color:rgba(220,38,38,0.5); color:#fff; }

    .inf-modal-body { padding:0; overflow-y:auto; flex:1; scroll-behavior:smooth; min-height:0; }

    .inf-body-grid { display:grid; grid-template-columns:1fr 1fr; }
    .inf-col { padding:24px 28px; border-right:1px solid rgba(27,79,168,0.07); }
    .inf-col:last-child { border-right:none; }
    .inf-col-full { grid-column:1/-1; padding:20px 28px; border-top:1px solid rgba(27,79,168,0.07); }

    .inf-sec-label { font-size:8px; letter-spacing:4px; text-transform:uppercase; color:#F5911E;
        margin-bottom:12px; display:flex; align-items:center; gap:8px; }
    .inf-sec-label::after { content:''; flex:1; height:1px; background:linear-gradient(90deg,rgba(245,145,30,0.2),transparent); }

    .inf-row { display:flex; justify-content:space-between; align-items:baseline; padding:6px 0; border-bottom:1px solid rgba(27,79,168,0.05); }
    .inf-row:last-child { border-bottom:none; }
    .inf-key { font-size:11px; color:#7A8A9A; }
    .inf-val { font-size:12px; color:#1A2A4A; font-weight:500; text-align:right; }
    .inf-val.blue   { color:#1B4FA8; } .inf-val.orange { color:#F5911E; }
    .inf-val.green  { color:#059669; } .inf-val.red    { color:#DC2626; }

    .inf-pricing-table { width:100%; border-collapse:collapse; }
    .inf-pricing-table th { font-size:8px; letter-spacing:3px; text-transform:uppercase; color:#AAB8C8;
        padding:8px 12px; text-align:left; border-bottom:1px solid rgba(27,79,168,0.08); font-weight:400; }
    .inf-pricing-table th:last-child { text-align:right; }
    .inf-pricing-table td { padding:10px 12px; font-size:13px; color:#1A2A4A; border-bottom:1px solid rgba(27,79,168,0.05); vertical-align:middle; }
    .inf-pricing-table td:last-child { text-align:right; font-weight:500; }
    .inf-pricing-table tr:last-child td { border-bottom:none; }
    .inf-pricing-table tr:hover td { background:rgba(27,79,168,0.02); }

    .inf-price-tag { display:inline-block; font-size:9px; letter-spacing:1px; text-transform:uppercase; padding:2px 7px; border-radius:3px; }
    .inf-price-tag.course   { background:rgba(27,79,168,0.08);  color:#1B4FA8; }
    .inf-price-tag.material { background:rgba(245,145,30,0.08); color:#C47010; }
    .inf-price-tag.test     { background:rgba(5,150,105,0.08);  color:#059669; }
    .inf-price-tag.discount { background:rgba(5,150,105,0.08);  color:#059669; }

    .inf-totals-section { background:rgba(248,246,242,0.8); border-top:1px solid rgba(27,79,168,0.08); padding:16px 28px; }
    .inf-totals-grid { display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; }
    .inf-total-card { background:#fff; border:1px solid rgba(27,79,168,0.1); border-radius:8px; padding:14px 16px; position:relative; overflow:hidden; }
    .inf-total-card::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; background:var(--tc,#1B4FA8); }
    .inf-total-card-label { font-size:8px; letter-spacing:3px; text-transform:uppercase; color:#7A8A9A; margin-bottom:6px; }
    .inf-total-card-val { font-family:'Bebas Neue',sans-serif; font-size:22px; letter-spacing:2px; color:var(--tc,#1B4FA8); line-height:1; }
    .inf-total-card-sub { font-size:10px; color:#AAB8C8; margin-top:3px; }

    .inf-payment-section { padding:20px 28px; border-top:1px solid rgba(27,79,168,0.07); }
    .inf-plan-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; }
    .inf-plan-name { font-size:13px; color:#1A2A4A; font-weight:500; }
    .inf-plan-badge { font-size:9px; letter-spacing:2px; text-transform:uppercase; padding:3px 9px; border-radius:20px;
        background:rgba(27,79,168,0.07); color:#1B4FA8; border:1px solid rgba(27,79,168,0.15); }

    .inf-inst-table { width:100%; border-collapse:collapse; }
    .inf-inst-table th { font-size:8px; letter-spacing:3px; text-transform:uppercase; color:#AAB8C8;
        padding:7px 10px; text-align:left; border-bottom:1px solid rgba(27,79,168,0.08); font-weight:400; background:rgba(27,79,168,0.02); }
    .inf-inst-table td { font-size:12px; color:#1A2A4A; font-weight:300; padding:8px 10px; border-bottom:1px solid rgba(27,79,168,0.04); }
    .inf-inst-table td:nth-child(2) { font-weight:500; }
    .inf-inst-table td:last-child { text-align:right; color:#F5911E; font-weight:500; }
    .inf-inst-table tr:last-child td { border-bottom:none; }

    .inf-approval-badge { display:inline-flex; align-items:center; gap:6px; background:rgba(245,145,30,0.08);
        border:1px solid rgba(245,145,30,0.2); border-radius:6px; padding:8px 12px; margin-top:12px;
        font-size:10px; letter-spacing:1px; text-transform:uppercase; color:#92400E; }

    .inf-modal-footer { padding:14px 28px 16px; border-top:1px solid rgba(27,79,168,0.08);
        display:flex; align-items:center; justify-content:space-between; flex-shrink:0; background:#fff; }
    .inf-footer-note { display:flex; align-items:center; gap:6px; font-size:10px; color:#AAB8C8; }
    .inf-footer-actions { display:flex; gap:10px; }

    .btn-inf-back { padding:10px 20px; background:transparent; border:1px solid rgba(27,79,168,0.15); border-radius:6px;
        color:#7A8A9A; font-family:'DM Sans',sans-serif; font-size:11px; letter-spacing:2px; text-transform:uppercase;
        cursor:pointer; transition:all 0.2s; }
    .btn-inf-back:hover { border-color:rgba(27,79,168,0.3); color:#1B4FA8; }

    .btn-inf-confirm { display:inline-flex; align-items:center; gap:8px; padding:11px 28px; background:#1B4FA8;
        border:none; border-radius:6px; color:#fff; font-family:'Bebas Neue',sans-serif; font-size:15px; letter-spacing:4px;
        cursor:pointer; transition:background 0.3s,transform 0.1s; box-shadow:0 4px 14px rgba(27,79,168,0.3); }
    .btn-inf-confirm:hover  { background:#2D6FDB; transform:translateY(-1px); box-shadow:0 6px 20px rgba(27,79,168,0.4); }
    .btn-inf-confirm:active { transform:translateY(0); }

    @media (max-width:640px) {
        .inf-body-grid { grid-template-columns:1fr; }
        .inf-col { border-right:none; border-bottom:1px solid rgba(27,79,168,0.07); padding:18px; }
        .inf-col-full,.inf-totals-section,.inf-payment-section,.inf-modal-footer,.inf-modal-header { padding-left:18px; padding-right:18px; }
        .inf-totals-grid { grid-template-columns:1fr; }
        .inf-modal-title { font-size:24px; }
        .inf-footer-note { display:none; }
    }
</style>

<div class="inf-modal-backdrop" id="invoiceModal">
    <div class="inf-modal">
        <div class="inf-modal-header">
            <div>
                <div class="inf-modal-eyebrow">Infinity Academy · Registration</div>
                <div class="inf-modal-title">Invoice Preview</div>
                <div class="inf-modal-meta">
                    <div class="inf-modal-id" id="inv_ref">INV-——</div>
                    <div class="inf-modal-date" id="inv_date"></div>
                </div>
            </div>
            <button type="button" class="inf-modal-close" id="inv_close_btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <div class="inf-modal-body" id="invoiceModalBody">
            <div class="inf-body-grid">
                <div class="inf-col">
                    <div class="inf-sec-label">Student</div>
                    <div id="inv_student"></div>
                </div>
                <div class="inf-col">
                    <div class="inf-sec-label">Course Details</div>
                    <div id="inv_course"></div>
                </div>
            </div>

            <div class="inf-col-full">
                <div class="inf-sec-label">Pricing Breakdown</div>
                <table class="inf-pricing-table">
                    <thead><tr><th>Item</th><th>Type</th><th>Amount</th></tr></thead>
                    <tbody id="inv_pricing_tbody"></tbody>
                </table>
            </div>

            <div class="inf-totals-section">
                <div class="inf-totals-grid">
                    <div class="inf-total-card" style="--tc:#1B4FA8">
                        <div class="inf-total-card-label">Course Price</div>
                        <div class="inf-total-card-val" id="inv_course_total">— LE</div>
                        <div class="inf-total-card-sub" id="inv_course_total_sub"></div>
                    </div>
                    <div class="inf-total-card" style="--tc:#F5911E">
                        <div class="inf-total-card-label">Total Amount</div>
                        <div class="inf-total-card-val" id="inv_total_val">— LE</div>
                        <div class="inf-total-card-sub">Course + material + test</div>
                    </div>
                    <div class="inf-total-card" style="--tc:#059669">
                        <div class="inf-total-card-label">Due Now</div>
                        <div class="inf-total-card-val" id="inv_due_val">— LE</div>
                        <div class="inf-total-card-sub" id="inv_due_sub">Deposit + extras</div>
                    </div>
                </div>
            </div>

            <div class="inf-payment-section">
                <div class="inf-sec-label">Payment Plan</div>
                <div id="inv_payment"></div>
            </div>

            <div class="inf-payment-section" id="inv_schedule_section" style="display:none;border-top:1px solid rgba(27,79,168,0.07);">
                <div class="inf-sec-label">Schedule</div>
                <div id="inv_schedule"></div>
            </div>
        </div>

        <div class="inf-modal-footer">
            <div class="inf-footer-note">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                Review all details carefully before confirming
            </div>
            <div class="inf-footer-actions">
                <button type="button" class="btn-inf-back" id="inv_close_btn_2">← Back to Edit</button>
                <button type="button" class="btn-inf-confirm" id="confirm_register_btn">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Confirm &amp; Register
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function infRow(key, val, cls='') {
        return `<div class="inf-row"><span class="inf-key">${key}</span><span class="inf-val ${cls}">${val}</span></div>`;
    }
    function fmtLE(n) {
        return parseFloat(n||0).toLocaleString('en-EG',{minimumFractionDigits:2,maximumFractionDigits:2}) + ' LE';
    }

    window.infOpenModal = function() {
        document.getElementById('invoiceModal').classList.add('show');
        setTimeout(() => { document.getElementById('invoiceModalBody').scrollTop = 0; }, 50);
    };
    window.infCloseModal = function() {
        document.getElementById('invoiceModal').classList.remove('show');
    };

    document.getElementById('invoiceModal').addEventListener('click', e => {
        if (e.target === document.getElementById('invoiceModal')) infCloseModal();
    });
    document.getElementById('inv_close_btn').addEventListener('click', infCloseModal);
    document.getElementById('inv_close_btn_2').addEventListener('click', infCloseModal);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') infCloseModal(); });

    document.getElementById('confirm_register_btn').addEventListener('click', function() {
        document.getElementById('main_form').submit();
    });

    // ── buildInvoice — called from register-modal.js ──
    window.buildInvoice = function() {
        // ── Bug fix: declare all vars ONCE at top, no redeclaration ──
        const courseEl      = document.getElementById('course_select');
        const levelEl       = document.getElementById('level_select');
        const sublevelEl    = document.getElementById('sublevel_select');
        const matCheckEl    = document.getElementById('material_check');
        const matPriceHid   = document.getElementById('material_price_hidden');
        const paySelectEl   = document.getElementById('payment_plan_id');
        const teacherEl     = document.getElementById('teacher_select');
        const daySelectEl   = document.getElementById('day_select');
        const typeInput     = document.querySelector('input[name="type"]:checked');
        const modeEl        = document.querySelector('[name="mode"]');
        const patchEl       = document.getElementById('patch_select');

        // Ref + Date
        document.getElementById('inv_ref').textContent  = 'INV-' + Date.now().toString().slice(-6);
        document.getElementById('inv_date').textContent = new Date().toLocaleDateString('en-EG',{day:'2-digit',month:'short',year:'numeric'});

        // 1. Student
        document.getElementById('inv_student').innerHTML =
            infRow('Full Name', document.getElementById('student_name')?.value  || '—') +
            infRow('Phone',     document.getElementById('student_phone')?.value || '—') +
            infRow('Degree',    '{{ $lead->degree ?? "—" }}') +
            infRow('Location',  '{{ $lead->location ?? "—" }}');

        // 2. Course
        const courseText = courseEl?.options[courseEl.selectedIndex]?.text || '—';
        const levelText  = levelEl?.value    ? (levelEl.options[levelEl.selectedIndex]?.text    || '—') : '—';
        const subText    = sublevelEl?.value ? (sublevelEl.options[sublevelEl.selectedIndex]?.text || '—') : '—';
        document.getElementById('inv_course').innerHTML =
            infRow('Course',   courseText, 'blue') +
            infRow('Level',    levelText !== '— Select Level —'    ? levelText : '—') +
            infRow('Sublevel', subText   !== '— Select Sublevel —' ? subText   : '—') +
            infRow('Type',     typeInput?.value === 'private' ? 'Private' : 'Group') +
            infRow('Mode',     modeEl?.options[modeEl.selectedIndex]?.text || '—') +
            infRow('Start',    patchEl?.options[patchEl.selectedIndex]?.text || '—');

        // 3. Pricing — Bug fix: read testFee from input directly, matPrice from checkbox
        const p = typeof pricing !== 'undefined'
            ? pricing
            : { courseBasePrice:0, courseDiscount:0, courseFinalPrice:0, isPackage:false };

        const matPrice  = matCheckEl?.checked ? parseFloat(matPriceHid?.value || 0) : 0;
        const testFee   = parseFloat(document.querySelector('[name="test_fee"]')?.value || 0); // ← read from input
        const courseAmt = p.courseFinalPrice;

        let tbody = '';
        if (p.isPackage) {
            tbody += `<tr><td><strong>Level Package</strong><br><small style="color:#7A8A9A;font-size:10px;">${courseText}</small></td><td><span class="inf-price-tag course">Package</span></td><td>${fmtLE(courseAmt)}</td></tr>`;
        } else {
            tbody += `<tr><td><strong>Course Fee</strong><br><small style="color:#7A8A9A;font-size:10px;">${courseText}${levelText !== '—' ? ' · '+levelText : ''}</small></td><td><span class="inf-price-tag course">Course</span></td><td>${fmtLE(p.courseBasePrice)}</td></tr>`;
            if (p.courseDiscount > 0) {
                tbody += `<tr><td><strong>Discount</strong></td><td><span class="inf-price-tag discount">Offer</span></td><td style="color:#059669;">− ${fmtLE(p.courseDiscount)}</td></tr>`;
            }
        }
        if (matPrice > 0) {
            const matName = document.getElementById('material_name')?.value || 'Study Material';
            tbody += `<tr><td><strong>${matName}</strong><br><small style="color:#7A8A9A;font-size:10px;">Full payment required</small></td><td><span class="inf-price-tag material">Material</span></td><td>${fmtLE(matPrice)}</td></tr>`;
        }
        if (testFee > 0) {
            tbody += `<tr><td><strong>Placement Test</strong><br><small style="color:#7A8A9A;font-size:10px;">Full payment required</small></td><td><span class="inf-price-tag test">Test</span></td><td>${fmtLE(testFee)}</td></tr>`;
        }
        document.getElementById('inv_pricing_tbody').innerHTML = tbody;

        // 4. Totals
        const totalAmount = courseAmt + matPrice + testFee;
        document.getElementById('inv_course_total').textContent     = fmtLE(courseAmt);
        document.getElementById('inv_course_total_sub').textContent = p.isPackage ? 'Package price' : (p.courseDiscount > 0 ? `After ${fmtLE(p.courseDiscount)} discount` : 'Regular price');
        document.getElementById('inv_total_val').textContent        = fmtLE(totalAmount);

        // 5. Payment Plan
        const selOpt        = paySelectEl?.options[paySelectEl.selectedIndex];
        const depositPct    = parseFloat(selOpt?.dataset.deposit     || 0);
        const installments  = parseInt(selOpt?.dataset.installments  || 0);
        const grace         = parseInt(selOpt?.dataset.grace         || 0);
        const needsApproval = selOpt?.dataset.approval == 1;
        const depositAmt    = (courseAmt * depositPct) / 100;
        const remaining     = courseAmt - depositAmt;
        const dueNow        = depositAmt + matPrice + testFee;

        document.getElementById('inv_due_val').textContent = fmtLE(dueNow);
        document.getElementById('inv_due_sub').textContent =
            `Deposit ${fmtLE(depositAmt)}${matPrice>0?' + mat '+fmtLE(matPrice):''}${testFee>0?' + test '+fmtLE(testFee):''}`;

        let payHTML = `<div class="inf-plan-header">
            <div class="inf-plan-name">${selOpt?.text || '—'}</div>
            <div class="inf-plan-badge">${depositPct}% Deposit</div>
        </div>`;
        payHTML += infRow('Deposit on Course', `${depositPct}% × ${fmtLE(courseAmt)} = ${fmtLE(depositAmt)}`, 'orange');
        if (matPrice > 0) payHTML += infRow('Material (full)', fmtLE(matPrice));
        if (testFee  > 0) payHTML += infRow('Test Fee (full)',  fmtLE(testFee));
        payHTML += infRow('Remaining (installments)', fmtLE(remaining), 'blue');

        if (needsApproval) {
            payHTML += `<div class="inf-approval-badge"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/></svg>This plan requires Admin Approval before activation</div>`;
        }

        if (installments > 0 && remaining > 0) {
            const amt = remaining / installments;
            payHTML += `<div style="margin-top:14px;"><div class="inf-sec-label" style="margin-bottom:10px;">Installment Schedule</div>
                <table class="inf-inst-table"><thead><tr><th>#</th><th>Amount</th><th>Due Date</th><th>Status</th></tr></thead><tbody>`;
            for (let i = 1; i <= installments; i++) {
                const d = new Date(); d.setDate(d.getDate() + grace * i);
                payHTML += `<tr><td style="color:#7A8A9A;">${i}</td><td>${fmtLE(amt)}</td><td>${d.toLocaleDateString('en-EG',{day:'2-digit',month:'short',year:'numeric'})}</td><td><span style="font-size:9px;letter-spacing:1px;text-transform:uppercase;color:#AAB8C8;background:rgba(122,138,154,0.1);padding:2px 7px;border-radius:3px;">Pending</span></td></tr>`;
            }
            payHTML += `</tbody></table></div>`;
        }
        document.getElementById('inv_payment').innerHTML = payHTML;
        document.getElementById('final_price_hidden').value = courseAmt;

        // 6. Schedule (private only)
        const schedSection = document.getElementById('inv_schedule_section');
        if (typeInput?.value === 'private') {
            const bundleEl   = document.getElementById('bundle_select');
            const bundleText = bundleEl?.value ? (bundleEl.options[bundleEl.selectedIndex]?.text || null) : null;
            document.getElementById('inv_schedule').innerHTML =
                infRow('Teacher', teacherEl?.options[teacherEl.selectedIndex]?.text || '—', 'blue') +
                infRow('Days',    daySelectEl?.value || '—') +
                (bundleText ? infRow('Bundle', bundleText) : '');
            schedSection.style.display = '';
        } else {
            schedSection.style.display = 'none';
        }

        infOpenModal();
    };
</script>