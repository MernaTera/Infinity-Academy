<style>
    /* ══════════════════════════════════════════
       INF INVOICE MODAL — matches leads form theme
    ══════════════════════════════════════════ */
    .inf-modal-backdrop {
        display: none;
        position: fixed; inset: 0; z-index: 1050;
        background: rgba(209, 216, 231, 0.55);
        align-items: center; justify-content: center;
        padding: 24px;
        font-family: 'DM Sans', sans-serif;
    }
    .inf-modal-backdrop.show { display: flex; }

    .inf-modal {
        width: 100%; max-width: 680px;
        background: #F8F6F2;
        border: 1px solid rgba(27,79,168,0.15);
        border-radius: 8px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 20px 60px rgba(27,79,168,0.18);
        max-height: 90vh;
        display: flex; flex-direction: column;
    }
    .inf-modal::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, transparent, #F5911E, #1B4FA8, transparent);
        z-index: 1;
    }

    /* ── Header ── */
    .inf-modal-header {
        padding: 22px 28px 18px;
        border-bottom: 1px solid rgba(27,79,168,0.08);
        display: flex; align-items: flex-end; justify-content: space-between;
        flex-wrap: wrap; gap: 8px; flex-shrink: 0;
    }
    .inf-modal-eyebrow {
        font-size: 9px; letter-spacing: 4px; text-transform: uppercase;
        color: #F5911E; margin-bottom: 3px;
    }
    .inf-modal-title {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 26px; letter-spacing: 4px; color: #1B4FA8; line-height: 1;
    }
    .inf-modal-id {
        font-size: 10px; letter-spacing: 2px; text-transform: uppercase;
        color: #7A8A9A; background: rgba(27,79,168,0.06);
        padding: 5px 10px; border-radius: 3px;
        border: 1px solid rgba(27,79,168,0.1);
    }

    /* ── Body ── */
    .inf-modal-body {
        padding: 22px 28px;
        overflow-y: auto; flex: 1;
    }

    /* ── Sections ── */
    .inf-section { margin-bottom: 20px; }
    .inf-section:last-child { margin-bottom: 0; }

    .inf-section-label {
        font-size: 9px; letter-spacing: 4px; text-transform: uppercase;
        color: #F5911E; margin-bottom: 12px; padding-bottom: 8px;
        border-bottom: 1px solid rgba(245,145,30,0.15);
    }

    /* ── Rows ── */
    .inf-row {
        display: flex; justify-content: space-between; align-items: baseline;
        padding: 5px 0; border-bottom: 1px solid rgba(27,79,168,0.05);
    }
    .inf-row:last-child { border-bottom: none; }

    .inf-key {
        font-size: 11px; letter-spacing: 1px; color: #7A8A9A; font-weight: 400;
    }
    .inf-val {
        font-size: 12px; color: #1A2A4A; font-weight: 500; text-align: right;
    }
    .inf-val.accent  { color: #F5911E; }
    .inf-val.blue    { color: #1B4FA8; }
    .inf-val.success { color: #059669; }

    /* ── Divider ── */
    .inf-divider { height: 1px; background: rgba(27,79,168,0.06); margin: 14px 0; }

    /* ── Total block ── */
    .inf-total-block {
        background: rgba(27,79,168,0.04);
        border: 1px solid rgba(27,79,168,0.1);
        border-radius: 5px; padding: 14px 16px;
        display: flex; justify-content: space-between; align-items: baseline;
    }
    .inf-total-label {
        font-size: 9px; letter-spacing: 4px; text-transform: uppercase; color: #7A8A9A;
    }
    .inf-total-val {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 26px; letter-spacing: 3px; color: #1B4FA8;
    }

    /* ── Installments table ── */
    .inf-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .inf-table th {
        font-size: 8px; letter-spacing: 3px; text-transform: uppercase;
        color: #7A8A9A; padding: 6px 8px; text-align: left;
        border-bottom: 1px solid rgba(27,79,168,0.1);
    }
    .inf-table td {
        font-size: 12px; color: #1A2A4A; font-weight: 300;
        padding: 7px 8px; border-bottom: 1px solid rgba(27,79,168,0.05);
    }
    .inf-table td:last-child { text-align: right; color: #F5911E; }
    .inf-table tr:last-child td { border-bottom: none; }

    /* ── Warning badge ── */
    .inf-badge-warn {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 9px; letter-spacing: 2px; text-transform: uppercase;
        color: #92400E; background: rgba(245,145,30,0.12);
        border: 1px solid rgba(245,145,30,0.25);
        border-radius: 3px; padding: 4px 9px; margin-top: 8px;
    }

    /* ── Footer ── */
    .inf-modal-footer {
        padding: 16px 28px 22px;
        border-top: 1px solid rgba(27,79,168,0.07);
        display: flex; align-items: center; justify-content: flex-end;
        gap: 10px; flex-shrink: 0;
    }

    .btn-inf-cancel {
        padding: 9px 20px; background: transparent;
        border: 1px solid rgba(27,79,168,0.15); border-radius: 4px;
        color: #7A8A9A; font-family: 'DM Sans', sans-serif;
        font-size: 10px; letter-spacing: 3px; text-transform: uppercase;
        cursor: pointer; transition: all 0.3s;
    }
    .btn-inf-cancel:hover { border-color: rgba(27,79,168,0.3); color: #1B4FA8; }

    .btn-inf-confirm {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 26px; background: transparent;
        border: 1.5px solid #1B4FA8; border-radius: 4px;
        color: #1B4FA8; font-family: 'Bebas Neue', sans-serif;
        font-size: 14px; letter-spacing: 4px;
        cursor: pointer; position: relative; overflow: hidden; transition: color 0.4s;
    }
    .btn-inf-confirm::before {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(90deg, #1B4FA8, #2D6FDB);
        transform: scaleX(0); transform-origin: left;
        transition: transform 0.4s cubic-bezier(0.16,1,0.3,1);
    }
    .btn-inf-confirm:hover::before { transform: scaleX(1); }
    .btn-inf-confirm:hover { color: #fff; }
    .btn-inf-confirm span,
    .btn-inf-confirm svg { position: relative; z-index: 1; }

    @media (max-width: 600px) {
        .inf-modal-header, .inf-modal-body, .inf-modal-footer { padding-left: 16px; padding-right: 16px; }
        .inf-modal-title { font-size: 22px; }
    }
</style>

{{-- ══════════════════════════════════════════
     INVOICE MODAL
══════════════════════════════════════════ --}}
<div class="inf-modal-backdrop" id="invoiceModal">
    <div class="inf-modal">

        {{-- Header --}}
        <div class="inf-modal-header">
            <div>
                <div class="inf-modal-eyebrow">Registration</div>
                <div class="inf-modal-title">Invoice Preview</div>
            </div>
            <div class="inf-modal-id" id="inv_ref">INV-PREVIEW</div>
        </div>

        {{-- Body --}}
        <div class="inf-modal-body">

            {{-- 1. Student --}}
            <div class="inf-section">
                <div class="inf-section-label">Student Information</div>
                <div id="inv_student"></div>
            </div>

            {{-- 2. Course --}}
            <div class="inf-section">
                <div class="inf-section-label">Course Details</div>
                <div id="inv_course"></div>
            </div>

            {{-- 3. Pricing --}}
            <div class="inf-section">
                <div class="inf-section-label">Pricing Breakdown</div>
                <div id="inv_pricing"></div>
                <div class="inf-divider"></div>
                <div class="inf-total-block">
                    <span class="inf-total-label">Total Due</span>
                    <span class="inf-total-val" id="inv_total_val">0 LE</span>
                </div>
            </div>

            {{-- 4. Payment --}}
            <div class="inf-section">
                <div class="inf-section-label">Payment Plan</div>
                <div id="inv_payment"></div>
            </div>

            {{-- 5. Schedule --}}
            <div class="inf-section" id="inv_schedule_section" style="display:none;">
                <div class="inf-section-label">Schedule</div>
                <div id="inv_schedule"></div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="inf-modal-footer">
            <button type="button" class="btn-inf-cancel" id="inv_close_btn">Cancel</button>
            <button type="button" class="btn-inf-confirm" id="confirm_register_btn">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span>Confirm &amp; Register</span>
            </button>
        </div>

    </div>
</div>


<script>
    /* ══════════════════════════════════════════
       INVOICE MODAL LOGIC
    ══════════════════════════════════════════ */

    /* ── helpers ── */
    function infRow(key, val, valClass = '') {
        return `<div class="inf-row">
            <span class="inf-key">${key}</span>
            <span class="inf-val ${valClass}">${val}</span>
        </div>`;
    }

    function infOpenModal()  { document.getElementById('invoiceModal').classList.add('show'); }
    function infCloseModal() { document.getElementById('invoiceModal').classList.remove('show'); }

    /* close on backdrop click */
    document.getElementById('invoiceModal').addEventListener('click', function (e) {
        if (e.target === this) infCloseModal();
    });
    document.getElementById('inv_close_btn').addEventListener('click', infCloseModal);

    /* ── confirm → submit form ── */
    document.getElementById('confirm_register_btn').addEventListener('click', function () {
        document.querySelector('form').requestSubmit();
    });

    /* ── open trigger (same id as before) ── */
    document.addEventListener('DOMContentLoaded', function () {
        const previewBtn = document.getElementById('preview_invoice_btn');
        if (previewBtn) {
            previewBtn.addEventListener('click', buildInvoice);
        }
    });

    /* ══════════════════════════════════════════
       buildInvoice  — same function name, same logic,
       new theme-aware HTML output
    ══════════════════════════════════════════ */
    function buildInvoice() {

        const course    = document.getElementById('course_select');
        const level     = document.getElementById('level_select');
        const sublevel  = document.getElementById('sublevel_select');

        const materialCheck       = document.getElementById('material_check');
        const materialPriceHidden = document.getElementById('material_price_hidden');
        const paymentSelect       = document.getElementById('payment_plan_id');
        const teacher             = document.getElementById('teacher_select');
        const daySelect           = document.getElementById('day_select');

        /* ── 1. Student ── */
        const studentName  = document.getElementById('student_name')?.value || '—';
        const studentPhone = document.getElementById('student_phone')?.value || '—';

        document.getElementById('inv_student').innerHTML =
            infRow('Full Name', studentName) +
            infRow('Phone', studentPhone);

        /* ── 2. Course ── */
        const courseText  = course?.options[course.selectedIndex]?.text   || '—';
        const levelText   = level?.options[level.selectedIndex]?.text     || '—';
        const subText     = sublevel?.options[sublevel.selectedIndex]?.text || '—';

        document.getElementById('inv_course').innerHTML =
            infRow('Course',   courseText,  'blue') +
            infRow('Level',    levelText) +
            infRow('Sublevel', subText);

        /* ── 3. Pricing ── */
        const base     = parseFloat(document.getElementById('base_price')?.value?.replace(' LE','')) || 0;
        const discount = parseFloat(document.getElementById('discount_hidden')?.value) || 0;
        const material = materialCheck?.checked
            ? parseFloat(materialPriceHidden?.value || 0) : 0;
        const testFees = parseFloat(document.querySelector('[name="test_fee"]')?.value || 0);
        const total    = base - discount + material + testFees;

        document.getElementById('inv_pricing').innerHTML =
            infRow('Base Price',     base.toLocaleString('en-EG') + ' LE') +
            infRow('Discount',       '−' + discount.toLocaleString('en-EG') + ' LE', 'success') +
            infRow('Material',       '+' + material.toLocaleString('en-EG') + ' LE') +
            infRow('Placement Test', '+' + testFees.toLocaleString('en-EG') + ' LE');

        document.getElementById('inv_total_val').textContent =
            total.toLocaleString('en-EG') + ' LE';

        /* ── 4. Payment ── */
        const selected     = paymentSelect?.options[paymentSelect.selectedIndex];
        const deposit      = parseFloat(selected?.dataset.deposit     || 0);
        const installments = parseInt(selected?.dataset.installments  || 0);
        const grace        = parseInt(selected?.dataset.grace         || 0);
        const needsApproval= selected?.dataset.approval == 1;

        const depositAmount = (total * deposit) / 100;
        const remaining     = total - depositAmount;

        let payHTML =
            infRow('Plan',    selected?.text || '—') +
            infRow('Deposit', deposit + '% = ' + depositAmount.toFixed(2) + ' LE', 'accent');

        if (needsApproval) {
            payHTML += `<div class="inf-badge-warn">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                Requires Admin Approval
            </div>`;
        }

        if (installments > 0) {
            const installmentAmount = remaining / installments;
            let tableHTML = `<table class="inf-table">
                <thead><tr><th>#</th><th>Amount</th><th>Due Date</th></tr></thead>
                <tbody>`;

            for (let i = 1; i <= installments; i++) {
                const date = new Date();
                date.setDate(date.getDate() + (grace * i));
                tableHTML += `<tr>
                    <td>${i}</td>
                    <td>${installmentAmount.toFixed(2)} LE</td>
                    <td>${date.toISOString().split('T')[0]}</td>
                </tr>`;
            }

            tableHTML += `</tbody></table>`;
            payHTML   += tableHTML;
        }
        document.getElementById('final_price_hidden').value = total;
        document.getElementById('inv_payment').innerHTML = payHTML;

        /* ── 5. Schedule ── */
        const typeInput = document.querySelector('input[name="type"]:checked');
        const schedSection = document.getElementById('inv_schedule_section');

        if (typeInput && typeInput.value === 'private') {
            const teacherText = teacher?.options[teacher.selectedIndex]?.text || '—';
            const dayText     = daySelect?.value || '—';

            document.getElementById('inv_schedule').innerHTML =
                infRow('Teacher', teacherText, 'blue') +
                infRow('Days',    dayText);

            schedSection.style.display = '';
        } else {
            schedSection.style.display = 'none';
        }

        /* ── ref number (optional cosmetic) ── */
        const ts = Date.now().toString().slice(-6);
        document.getElementById('inv_ref').textContent = 'INV-' + ts;

        /* ── open ── */
        infOpenModal();
    }
</script>