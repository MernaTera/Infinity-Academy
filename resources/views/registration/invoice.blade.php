{{-- ═══════════════════════════════════════════════════════
    Infinity Academy — Invoice Modal (Enterprise Edition)
    Preserve: infOpenModal, infCloseModal, buildInvoice, infRow, fmtLE
═══════════════════════════════════════════════════════ --}}

<style>
*::before,*::after{pointer-events:none;}

/* ── Backdrop ── */
.inf-modal-backdrop{
    display:none;position:fixed;inset:0;z-index:1050;
    background:rgba(5,10,25,0.80);
    backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);
    font-family:'DM Sans',sans-serif;
    pointer-events:auto;
    align-items:center;
    justify-content:center;
    padding:20px;
}
.inf-modal-backdrop.show{display:flex;animation:infBackdropIn 0.25s ease both;}
@keyframes infBackdropIn{from{opacity:0}to{opacity:1}}

/* ── Centered Modal ── */
.inf-modal{
    position:relative;
    width:min(820px,100%);
    max-height:calc(100vh - 40px);
    background:#F8F6F2;
    display:flex;flex-direction:column;
    border-radius:16px;
    box-shadow:0 32px 80px rgba(0,0,0,0.40),0 8px 24px rgba(27,79,168,0.15);
    animation:infModalIn 0.35s cubic-bezier(0.16,1,0.3,1) both;
    overflow:hidden;
}
@keyframes infModalIn{from{transform:scale(0.92) translateY(24px);opacity:0}to{transform:scale(1) translateY(0);opacity:1}}
.inf-modal-backdrop.show .inf-modal{pointer-events:auto;}

/* Top gradient bar */
.inf-modal::before{
    content:'';position:absolute;top:0;left:0;right:0;height:3px;z-index:10;border-radius:16px 16px 0 0;
    background:linear-gradient(90deg,#F5911E 0%,#1B4FA8 40%,#2D6FDB 70%,#F5911E 100%);
    background-size:200% auto;
    animation:infGradMove 4s linear infinite;
}
@keyframes infGradMove{to{background-position:200% center}}

/* ── Header ── */
.inf-modal-header{
    background:linear-gradient(135deg,#0F1D3A 0%,#1B4FA8 55%,#2D6FDB 100%);
    padding:24px 28px 20px;
    display:flex;align-items:flex-start;justify-content:space-between;
    flex-shrink:0;position:relative;overflow:hidden;
}
.inf-modal-eyebrow{
    font-size:9px;letter-spacing:5px;text-transform:uppercase;
    color:rgba(255,255,255,0.45);margin-bottom:6px;display:flex;align-items:center;gap:8px;
}
.inf-modal-eyebrow::before{content:'';width:20px;height:1px;background:rgba(245,145,30,0.6);}
.inf-modal-title{
    font-family:'Bebas Neue',sans-serif;font-size:32px;letter-spacing:6px;
    color:#fff;line-height:1;margin-bottom:10px;
}
.inf-modal-meta{display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.inf-modal-id{
    font-size:10px;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,0.7);
    background:rgba(255,255,255,0.1);padding:5px 12px;border-radius:20px;
    border:1px solid rgba(255,255,255,0.15);font-weight:500;
}
.inf-modal-date{font-size:10px;color:rgba(255,255,255,0.35);letter-spacing:0.5px;}
.inf-modal-status{
    font-size:9px;letter-spacing:2px;text-transform:uppercase;
    background:rgba(5,150,105,0.2);color:#4ADE80;
    padding:4px 10px;border-radius:20px;border:1px solid rgba(74,222,128,0.2);
}
.inf-header-actions{
    display:flex;align-items:center;gap:8px;position:relative;z-index:1;flex-shrink:0;
}
.inf-modal-close{
    width:34px;height:34px;display:flex;align-items:center;justify-content:center;
    background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);
    border-radius:8px;cursor:pointer;color:rgba(255,255,255,0.5);
    transition:all 0.2s;pointer-events:auto;
}
.inf-modal-close:hover{background:rgba(220,38,38,0.25);border-color:rgba(220,38,38,0.4);color:#fff;}
.inf-print-btn{
    display:flex;align-items:center;gap:6px;
    padding:8px 16px;
    background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);
    border-radius:8px;cursor:pointer;color:rgba(255,255,255,0.7);
    font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:2px;text-transform:uppercase;
    transition:all 0.2s;pointer-events:auto;
}
.inf-print-btn:hover{background:rgba(255,255,255,0.15);color:#fff;}

/* ── Body ── */
.inf-modal-body{
    flex:1;overflow-y:auto;overflow-x:hidden;scroll-behavior:smooth;
    scrollbar-width:thin;scrollbar-color:rgba(27,79,168,0.15) transparent;
}
.inf-modal-body::-webkit-scrollbar{width:4px;}
.inf-modal-body::-webkit-scrollbar-thumb{background:rgba(27,79,168,0.15);border-radius:2px;}

.inf-section{padding:20px 28px;border-bottom:1px solid rgba(27,79,168,0.06);}
.inf-section:last-child{border-bottom:none;}
.inf-sec-label{
    font-size:8px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;
    margin-bottom:14px;display:flex;align-items:center;gap:8px;
}
.inf-sec-label::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,rgba(245,145,30,0.2),transparent);}

.inf-two-col{display:grid;grid-template-columns:1fr 1fr;gap:0;}
.inf-col-box{padding:20px 28px;border-right:1px solid rgba(27,79,168,0.06);}
.inf-col-box:last-child{border-right:none;}

.inf-row{display:flex;justify-content:space-between;align-items:baseline;padding:6px 0;border-bottom:1px solid rgba(27,79,168,0.04);}
.inf-row:last-child{border-bottom:none;}
.inf-key{font-size:11px;color:#7A8A9A;}
.inf-val{font-size:12px;color:#1A2A4A;font-weight:500;text-align:right;max-width:60%;}
.inf-val.blue{color:#1B4FA8;}.inf-val.orange{color:#F5911E;}
.inf-val.green{color:#059669;}.inf-val.red{color:#DC2626;}

.inf-pricing-table{width:100%;border-collapse:collapse;}
.inf-pricing-table th{
    font-size:8px;letter-spacing:3px;text-transform:uppercase;color:#AAB8C8;
    padding:9px 12px;text-align:left;
    border-bottom:2px solid rgba(27,79,168,0.08);font-weight:400;
    background:rgba(27,79,168,0.02);
}
.inf-pricing-table th:last-child{text-align:right;}
.inf-pricing-table td{
    padding:10px 12px;font-size:12px;color:#1A2A4A;
    border-bottom:1px solid rgba(27,79,168,0.04);vertical-align:middle;
}
.inf-pricing-table td:last-child{text-align:right;font-weight:600;color:#1B4FA8;}
.inf-pricing-table tr:last-child td{border-bottom:none;}
.inf-pricing-table tr:hover td{background:rgba(27,79,168,0.02);}

.inf-price-tag{display:inline-block;font-size:8px;letter-spacing:1px;text-transform:uppercase;padding:3px 8px;border-radius:3px;font-weight:500;}
.inf-price-tag.course  {background:rgba(27,79,168,0.08); color:#1B4FA8;}
.inf-price-tag.material{background:rgba(245,145,30,0.08);color:#C47010;}
.inf-price-tag.test    {background:rgba(5,150,105,0.08);color:#059669;}
.inf-price-tag.discount{background:rgba(5,150,105,0.08);color:#059669;}
.inf-price-tag.package {background:rgba(127,119,221,0.1);color:#7F77DD;}

.inf-totals-strip{
    background:linear-gradient(135deg,#0F1D3A 0%,#1B4FA8 100%);
    padding:18px 28px;
    display:grid;grid-template-columns:repeat(3,1fr);
}
.inf-total-item{
    padding:14px 16px;text-align:center;
    border-right:1px solid rgba(255,255,255,0.06);
}
.inf-total-item:last-child{border-right:none;}
.inf-total-label{font-size:8px;letter-spacing:3px;text-transform:uppercase;color:rgba(255,255,255,0.4);margin-bottom:6px;}
.inf-total-val{font-family:'Bebas Neue',sans-serif;font-size:24px;letter-spacing:2px;color:#fff;line-height:1;}
.inf-total-val.orange{color:#FFB347;}.inf-total-val.green{color:#4ADE80;}
.inf-total-sub{font-size:9px;color:rgba(255,255,255,0.3);margin-top:4px;}

.inf-plan-header{
    display:flex;align-items:center;justify-content:space-between;
    margin-bottom:14px;padding:12px 14px;
    background:rgba(27,79,168,0.04);border:1px solid rgba(27,79,168,0.1);border-radius:8px;
}
.inf-plan-name{font-size:13px;color:#1A2A4A;font-weight:600;}
.inf-plan-badge{
    font-size:9px;letter-spacing:2px;text-transform:uppercase;padding:4px 10px;border-radius:20px;
    background:rgba(27,79,168,0.08);color:#1B4FA8;border:1px solid rgba(27,79,168,0.15);
}

.inf-inst-table{width:100%;border-collapse:collapse;margin-top:10px;}
.inf-inst-table th{
    font-size:8px;letter-spacing:3px;text-transform:uppercase;color:#AAB8C8;
    padding:8px 10px;text-align:left;border-bottom:1px solid rgba(27,79,168,0.08);
    font-weight:400;background:rgba(27,79,168,0.02);
}
.inf-inst-table td{font-size:12px;color:#1A2A4A;font-weight:300;padding:9px 10px;border-bottom:1px solid rgba(27,79,168,0.04);}
.inf-inst-table td:nth-child(2){font-weight:600;color:#1B4FA8;}
.inf-inst-table td:last-child{text-align:right;color:#F5911E;}
.inf-inst-table tr:last-child td{border-bottom:none;}

.inf-approval-badge{
    display:inline-flex;align-items:center;gap:8px;
    background:rgba(245,145,30,0.07);border:1px solid rgba(245,145,30,0.2);border-left:3px solid #F5911E;
    border-radius:6px;padding:10px 14px;margin-top:14px;font-size:11px;color:#92400E;line-height:1.4;
}

/* ── Footer ── */
.inf-modal-footer{
    padding:14px 28px;border-top:1px solid rgba(27,79,168,0.08);
    display:flex;align-items:center;justify-content:space-between;
    flex-shrink:0;background:#fff;box-shadow:0 -4px 20px rgba(27,79,168,0.06);
    border-radius:0 0 16px 16px;
}
.inf-footer-note{display:flex;align-items:center;gap:7px;font-size:10px;color:#AAB8C8;}
.inf-footer-actions{display:flex;gap:10px;pointer-events:auto;}

.btn-inf-back{
    padding:10px 20px;background:transparent;border:1px solid rgba(27,79,168,0.15);border-radius:6px;
    color:#7A8A9A;font-family:'DM Sans',sans-serif;font-size:11px;letter-spacing:2px;text-transform:uppercase;
    cursor:pointer;transition:all 0.2s;pointer-events:auto;
}
.btn-inf-back:hover{border-color:rgba(27,79,168,0.3);color:#1B4FA8;}

.btn-inf-confirm{
    display:inline-flex;align-items:center;gap:8px;padding:11px 26px;
    background:linear-gradient(135deg,#1B4FA8,#2D6FDB);
    border:none;border-radius:6px;color:#fff;
    font-family:'Bebas Neue',sans-serif;font-size:15px;letter-spacing:4px;
    cursor:pointer;transition:all 0.25s;box-shadow:0 4px 16px rgba(27,79,168,0.3);pointer-events:auto;
}
.btn-inf-confirm:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(27,79,168,0.4);}
.btn-inf-confirm:active{transform:translateY(0);}
.btn-inf-confirm:disabled{opacity:0.7;cursor:not-allowed;transform:none;}

/* ════════════════════════════════════════
   PRINT STYLES — ورقة واحدة A4 professional
   ════════════════════════════════════════ */
@media print{
    @page{margin:8mm 10mm;size:A4 portrait;}

    body>*{display:none!important;}
    #inf-print-area{display:block!important;}

    #inf-print-area{
        position:static!important;
        width:100%!important;
        font-family:'DM Sans',sans-serif!important;
        zoom:1.0;                        
    }

    #inf-print-area .inf-modal{
        position:static!important;width:100%!important;max-height:none!important;
        box-shadow:none!important;animation:none!important;
        border-radius:0!important;display:block!important;overflow:visible!important;
    }

    #inf-print-area .inf-modal-footer,
    #inf-print-area .inf-header-actions,
    #inf-print-area .inf-print-btn,
    #inf-print-area .inf-modal::before{display:none!important;}

    #inf-print-area .inf-modal-header{
        background:#0F1D3A!important;
        -webkit-print-color-adjust:exact!important;print-color-adjust:exact!important;
        padding:14px 18px!important;border-radius:0!important;
    }
    #inf-print-area .inf-modal-title{font-size:24px!important;letter-spacing:4px!important;color:#fff!important;}
    #inf-print-area .inf-modal-id{color:rgba(255,255,255,0.8)!important;}
    #inf-print-area .inf-modal-status{color:#4ADE80!important;}

    #inf-print-area .inf-modal-body{overflow:visible!important;max-height:none!important;}

    #inf-print-area .inf-two-col{display:grid!important;grid-template-columns:1fr 1fr!important;}
    #inf-print-area .inf-col-box{padding:10px 14px!important;}
    #inf-print-area .inf-section{padding:10px 14px!important;}

    #inf-print-area .inf-totals-strip{
        background:linear-gradient(135deg,#0F1D3A 0%,#1B4FA8 100%)!important;
        -webkit-print-color-adjust:exact!important;print-color-adjust:exact!important;
        display:grid!important;grid-template-columns:repeat(3,1fr)!important;
        padding:12px 14px!important;
    }
    #inf-print-area .inf-total-val{font-size:18px!important;}
    #inf-print-area .inf-total-val.orange{color:#FFB347!important;}
    #inf-print-area .inf-total-val.green{color:#4ADE80!important;}

    #inf-print-area .inf-sec-label{color:#F5911E!important;margin-bottom:8px!important;}
    #inf-print-area .inf-val.blue{color:#1B4FA8!important;}
    #inf-print-area .inf-val.orange{color:#F5911E!important;}
    #inf-print-area .inf-val.green{color:#059669!important;}

    #inf-print-area .inf-pricing-table td,
    #inf-print-area .inf-pricing-table th{padding:5px 8px!important;font-size:10px!important;}
    #inf-print-area .inf-pricing-table td:last-child{color:#1B4FA8!important;}
    #inf-print-area .inf-price-tag.course{background:rgba(27,79,168,0.1)!important;color:#1B4FA8!important;}
    #inf-print-area .inf-price-tag.test{background:rgba(5,150,105,0.1)!important;color:#059669!important;}
    #inf-print-area .inf-price-tag.material{background:rgba(245,145,30,0.1)!important;color:#C47010!important;}
    #inf-print-area .inf-price-tag.discount{background:rgba(5,150,105,0.1)!important;color:#059669!important;}

    #inf-print-area .inf-row{padding:3px 0!important;}
    #inf-print-area .inf-key{font-size:9px!important;}
    #inf-print-area .inf-val{font-size:10px!important;}

    #inf-print-area .inf-plan-header{
        background:rgba(27,79,168,0.06)!important;
        -webkit-print-color-adjust:exact!important;print-color-adjust:exact!important;
        padding:8px 10px!important;margin-bottom:8px!important;
    }
    #inf-print-area .inf-plan-name{color:#1A2A4A!important;font-size:12px!important;}
    #inf-print-area .inf-plan-badge{color:#1B4FA8!important;background:rgba(27,79,168,0.1)!important;font-size:8px!important;}

    #inf-print-area .inf-inst-table td,
    #inf-print-area .inf-inst-table th{padding:5px 7px!important;font-size:10px!important;}

    #inf-print-area::after{
        content:'Infinity Academy — Confidential Invoice';
        display:block;text-align:center;
        font-size:7px;letter-spacing:3px;text-transform:uppercase;
        color:#AAB8C8;padding-top:6px;
        border-top:1px solid rgba(27,79,168,0.1);margin-top:6px;
    }
}

/* ── Responsive ── */
@media(max-width:640px){
    .inf-modal{width:100%;max-height:calc(100vh - 20px);border-radius:12px;}
    .inf-modal-header,.inf-section,.inf-col-box,.inf-modal-footer{padding-left:16px;padding-right:16px;}
    .inf-modal-title{font-size:24px;}
    .inf-two-col{grid-template-columns:1fr;}
    .inf-col-box{border-right:none;border-bottom:1px solid rgba(27,79,168,0.06);}
    .inf-totals-strip{grid-template-columns:1fr;padding:14px 16px;}
    .inf-total-item{border-right:none;border-bottom:1px solid rgba(255,255,255,0.06);padding:10px 0;}
    .inf-footer-note{display:none;}
    .inf-print-btn span{display:none;}
}

@keyframes spin{to{transform:rotate(360deg)}}
</style>

<div class="inf-modal-backdrop" id="invoiceModal">
    <div class="inf-modal" id="invoicePanel">

        <div class="inf-modal-header">
            <div style="position:relative;z-index:1;">
                <div class="inf-modal-eyebrow">Infinity Academy · Registration</div>
                <div class="inf-modal-title">Invoice Preview</div>
                <div class="inf-modal-meta">
                    <div class="inf-modal-id" id="inv_ref">INV-——</div>
                    <div class="inf-modal-date" id="inv_date"></div>
                    <div class="inf-modal-status">Customer Service : {{ Auth::user()->employee?->full_name ?? Auth::user()->name }}</div>
                    <input type="hidden" id="cs_name_hidden" value="{{ Auth::user()->employee?->full_name ?? Auth::user()->name }}">
                </div>
            </div>
            <div class="inf-header-actions">
                <button type="button" class="inf-print-btn" onclick="infPrintInvoice()">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    <span>Print</span>
                </button>
                <button type="button" class="inf-modal-close" id="inv_close_btn">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
        </div>

        <div class="inf-modal-body" id="invoiceModalBody">

            <div class="inf-two-col">
                <div class="inf-col-box">
                    <div class="inf-sec-label">Student</div>
                    <div id="inv_student"></div>
                </div>
                <div class="inf-col-box">
                    <div class="inf-sec-label">Course Details</div>
                    <div id="inv_course"></div>
                </div>
            </div>

            <div class="inf-section">
                <div class="inf-sec-label">Pricing Breakdown</div>
                <table class="inf-pricing-table">
                    <thead><tr><th style="width:45%;">Item</th><th>Type</th><th style="text-align:right;">Amount</th></tr></thead>
                    <tbody id="inv_pricing_tbody"></tbody>
                </table>
            </div>

            <div class="inf-totals-strip">
                <div class="inf-total-item">
                    <div class="inf-total-label">Course Price</div>
                    <div class="inf-total-val" id="inv_course_total">— LE</div>
                    <div class="inf-total-sub" id="inv_course_total_sub">Final price</div>
                </div>
                <div class="inf-total-item">
                    <div class="inf-total-label">Total Amount</div>
                    <div class="inf-total-val orange" id="inv_total_val">— LE</div>
                    <div class="inf-total-sub">Course + extras</div>
                </div>
                <div class="inf-total-item">
                    <div class="inf-total-label">Due Now</div>
                    <div class="inf-total-val green" id="inv_due_val">— LE</div>
                    <div class="inf-total-sub" id="inv_due_sub">Deposit + extras</div>
                </div>
            </div>

            <div class="inf-section">
                <div class="inf-sec-label">Payment Plan</div>
                <div id="inv_payment"></div>
            </div>

            <div class="inf-section" id="inv_schedule_section" style="display:none;">
                <div class="inf-sec-label">Schedule</div>
                <div id="inv_schedule"></div>
            </div>

        </div>

        <div class="inf-modal-footer">
            <div class="inf-footer-note">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Review all details carefully before confirming
            </div>
            <div class="inf-footer-actions">
                <button type="button" class="btn-inf-back" id="inv_close_btn_2">← Back to Edit</button>
                <button type="button" class="btn-inf-confirm" id="confirm_register_btn">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Confirm &amp; Register
                </button>
            </div>
        </div>

    </div>
</div>

<script>
function infRow(key,val,cls=''){return`<div class="inf-row"><span class="inf-key">${key}</span><span class="inf-val ${cls}">${val}</span></div>`;}
function fmtLE(n){return parseFloat(n||0).toLocaleString('en-EG',{minimumFractionDigits:2,maximumFractionDigits:2})+' LE';}

const depositSection = document.getElementById('deposit_section');
if (depositSection && depositSection.style.display !== 'none') {
    const methods = [...document.querySelectorAll('.payment-method-row')].map(row => {
        const method = row.querySelector('.method-select')?.value || '—';
        const amount = parseFloat(row.querySelector('.method-amount')?.value || 0);
        return { method, amount };
    }).filter(m => m.amount > 0);

    if (methods.length) {
        let methodsHTML = `<div style="margin-top:14px;"><div class="inf-sec-label" style="margin-bottom:10px;">Deposit Payment Methods</div>`;
        methods.forEach(m => {
            methodsHTML += infRow(
                m.method.replace('_', ' '),
                fmtLE(m.amount),
                'green'
            );
        });
        const totalPaid = methods.reduce((s, m) => s + m.amount, 0);
        methodsHTML += `<div style="height:1px;background:rgba(27,79,168,0.07);margin:6px 0;"></div>`;
        methodsHTML += infRow('Total Paid Now', fmtLE(totalPaid), 'green');
        methodsHTML += '</div>';

        document.getElementById('inv_payment').innerHTML += methodsHTML;
    }
}
window.infOpenModal = function() {
    document.getElementById('invoiceModal').classList.add('show');
    document.body.style.overflow = 'hidden';
    document.getElementById('invoiceModalBody').scrollTop = 0;
};

window.infCloseModal = function() {
    const b = document.getElementById('invoiceModal');
    b.classList.remove('show');
    document.body.style.overflow = '';
};

/* ── إغلاق لما يضغط على الـ backdrop (بره الـ modal) ── */
document.getElementById('invoiceModal').addEventListener('click', function(e) {
    if (e.target === this) infCloseModal();
});
document.getElementById('inv_close_btn').addEventListener('click', infCloseModal);
document.getElementById('inv_close_btn_2').addEventListener('click', infCloseModal);
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') infCloseModal(); });

document.getElementById('confirm_register_btn').addEventListener('click', function() {
    this.disabled = true;
    this.innerHTML = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg><span>Processing...</span>';
    document.getElementById('main_form').submit();
});

/* ── Move modal to body on load ── */
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('invoiceModal');
    if (modal && modal.parentElement !== document.body) {
        document.body.appendChild(modal);
    }
});

/* ── Print — ورقة واحدة A4 ── */
window.infPrintInvoice = function() {
    // جهز الـ print area
    let printArea = document.getElementById('inf-print-area');
    if (!printArea) {
        printArea = document.createElement('div');
        printArea.id = 'inf-print-area';
        document.body.appendChild(printArea);
    }

    // Clone الـ panel
    const panel = document.getElementById('invoicePanel').cloneNode(true);

    // شيل العناصر اللي مش محتاجينها في الطباعة
    panel.querySelector('.inf-modal-footer')?.remove();
    panel.querySelector('.inf-header-actions')?.remove();

    // Reset الـ style
    panel.style.cssText = 'position:static;width:100%;box-shadow:none;animation:none;border-radius:0;max-height:none;';

    // شيل الـ display:none من الـ schedule section لو موجود
    const schedSection = panel.querySelector('#inv_schedule_section');
    if (schedSection && schedSection.style.display === 'none') {
        schedSection.remove();
    }

    printArea.innerHTML = '';
    printArea.appendChild(panel);

    window.print();
};

/* ── Build Invoice ── */
window.buildInvoice = function() {
    const courseEl     = document.getElementById('course_select');
    const levelEl      = document.getElementById('level_select');
    const sublevelEl   = document.getElementById('sublevel_select');
    const matCheckEl   = document.getElementById('material_check');
    const matPriceHid  = document.getElementById('material_price_hidden');
    const paySelectEl  = document.getElementById('payment_plan_id');
    const teacherEl    = document.getElementById('teacher_select');
    const daySelectEl  = document.getElementById('day_select');
    const typeInput    = document.querySelector('input[name="type"]:checked');
    const modeEl       = document.querySelector('[name="mode"]');
    const patchEl      = document.getElementById('patch_select');

    document.getElementById('inv_ref').textContent  = 'INV-' + Date.now().toString().slice(-6);
    document.getElementById('inv_date').textContent = new Date().toLocaleDateString('en-EG', {day:'2-digit',month:'short',year:'numeric'});

    document.getElementById('inv_student').innerHTML =
        infRow('Full Name', document.getElementById('student_name')?.value || '—') +
        infRow('Phone',     document.getElementById('student_phone')?.value || '—') +
        infRow('Degree',   '{{ $lead->degree ?? "—" }}') +
        infRow('Location', '{{ $lead->location ?? "—" }}');
        infRow('Registered By', document.getElementById('cs_name_hidden')?.value || '—')

    const courseText = courseEl?.options[courseEl.selectedIndex]?.text || '—';
    const levelText  = levelEl?.value ? (levelEl.options[levelEl.selectedIndex]?.text || '—') : '—';
    const subText    = sublevelEl?.value ? (sublevelEl.options[sublevelEl.selectedIndex]?.text || '—') : '—';
    document.getElementById('inv_course').innerHTML =
        infRow('Course',  courseText, 'blue') +
        infRow('Level',   levelText  !== '— Select Level —'   ? levelText  : '—') +
        infRow('Sublevel',subText    !== '— Select Sublevel —' ? subText    : '—') +
        infRow('Type',    typeInput?.value === 'private' ? 'Private' : 'Group') +
        infRow('Mode',    modeEl?.options[modeEl.selectedIndex]?.text || '—') +
        infRow('Start',   patchEl?.options[patchEl.selectedIndex]?.text || '—');

    const p = typeof pricing !== 'undefined' ? pricing : {courseBasePrice:0,courseDiscount:0,courseFinalPrice:0,isPackage:false};
    const matPrice = matCheckEl?.checked ? parseFloat(matPriceHid?.value || 0) : 0;
    const testFee  = parseFloat(document.querySelector('[name="test_fee"]')?.value || 0);
    const courseAmt = p.courseFinalPrice;

    let tbody = '';
    if (p.isPackage) {
        tbody += `<tr><td><strong>Level Package</strong><br><small style="color:#7A8A9A;font-size:11px;">${courseText}</small></td><td><span class="inf-price-tag package">Package</span></td><td>${fmtLE(courseAmt)}</td></tr>`;
    } else {
        tbody += `<tr><td><strong>Course Fee</strong><br><small style="color:#7A8A9A;font-size:11px;">${courseText}${levelText !== '—' ? ' · ' + levelText : ''}</small></td><td><span class="inf-price-tag course">Course</span></td><td>${fmtLE(p.courseBasePrice)}</td></tr>`;
        if (p.courseDiscount > 0) tbody += `<tr><td><strong>Discount Applied</strong></td><td><span class="inf-price-tag discount">Offer</span></td><td style="color:#059669;">− ${fmtLE(p.courseDiscount)}</td></tr>`;
    }
    if (matPrice > 0) {
        const mn = document.getElementById('material_name')?.value || 'Study Material';
        tbody += `<tr><td><strong>${mn}</strong><br><small style="color:#7A8A9A;font-size:11px;">Full payment required</small></td><td><span class="inf-price-tag material">Material</span></td><td>${fmtLE(matPrice)}</td></tr>`;
    }
    if (testFee > 0) tbody += `<tr><td><strong>Placement Test</strong><br><small style="color:#7A8A9A;font-size:11px;">Full payment required</small></td><td><span class="inf-price-tag test">Test</span></td><td>${fmtLE(testFee)}</td></tr>`;

    const grandTotal = courseAmt + matPrice + testFee;
    tbody += `<tr style="background:rgba(27,79,168,0.03);"><td colspan="2" style="font-weight:600;font-size:12px;color:#1A2A4A;letter-spacing:1px;text-transform:uppercase;">Grand Total</td><td style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:#1B4FA8;letter-spacing:1px;">${fmtLE(grandTotal)}</td></tr>`;
    document.getElementById('inv_pricing_tbody').innerHTML = tbody;

    document.getElementById('inv_course_total').textContent      = fmtLE(courseAmt);
    document.getElementById('inv_course_total_sub').textContent  = p.isPackage ? 'Package price' : (p.courseDiscount > 0 ? `After ${fmtLE(p.courseDiscount)} discount` : 'Regular price');
    document.getElementById('inv_total_val').textContent         = fmtLE(grandTotal);

    const selOpt        = paySelectEl?.options[paySelectEl.selectedIndex];
    const depositPct    = parseFloat(selOpt?.dataset.deposit || 0);
    const installments  = parseInt(selOpt?.dataset.installments || 0);
    const grace         = parseInt(selOpt?.dataset.grace || 0);
    const needsApproval = selOpt?.dataset.approval == 1;
    const depositAmt    = (courseAmt * depositPct) / 100;
    const remaining     = courseAmt - depositAmt;
    const dueNow        = depositAmt + matPrice + testFee;

    document.getElementById('inv_due_val').textContent = fmtLE(dueNow);
    document.getElementById('inv_due_sub').textContent = `Deposit ${fmtLE(depositAmt)}${matPrice > 0 ? ' + material' : ''}${testFee > 0 ? ' + test' : ''}`;

    let payHTML = `<div class="inf-plan-header"><div class="inf-plan-name">${selOpt?.text || '—'}</div><div class="inf-plan-badge">${depositPct}% Deposit</div></div>`;
    payHTML += infRow('Deposit on Course', `${depositPct}% × ${fmtLE(courseAmt)} = ${fmtLE(depositAmt)}`, 'orange');
    if (matPrice > 0) payHTML += infRow('Material (full payment)', fmtLE(matPrice));
    if (testFee > 0)  payHTML += infRow('Test Fee (full payment)', fmtLE(testFee));
    payHTML += `<div style="height:1px;background:rgba(27,79,168,0.07);margin:8px 0;"></div>`;
    payHTML += infRow('Total Due Now',         fmtLE(dueNow),   'green');
    payHTML += infRow('Remaining (installments)', fmtLE(remaining), 'blue');

    if (needsApproval) payHTML += `<div class="inf-approval-badge"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#C47010" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/></svg>This plan requires <strong>Admin Approval</strong> before activation</div>`;

    if (installments > 0 && remaining > 0) {
        const amt = remaining / installments;
        payHTML += `<div style="margin-top:16px;"><div class="inf-sec-label" style="margin-bottom:10px;">Installment Schedule</div><table class="inf-inst-table"><thead><tr><th>#</th><th>Amount</th><th>Estimated Due</th><th>Status</th></tr></thead><tbody>`;
        for (let i = 1; i <= installments; i++) {
            const d = new Date(); d.setDate(d.getDate() + grace * i);
            payHTML += `<tr><td style="color:#AAB8C8;">${i}</td><td>${fmtLE(amt)}</td><td>${d.toLocaleDateString('en-EG',{day:'2-digit',month:'short',year:'numeric'})}</td><td><span style="font-size:8px;letter-spacing:1.5px;text-transform:uppercase;color:#AAB8C8;background:rgba(122,138,154,0.1);padding:3px 8px;border-radius:3px;">Pending</span></td></tr>`;
        }
        payHTML += `</tbody></table></div>`;
    }
    document.getElementById('inv_payment').innerHTML = payHTML;
    document.getElementById('final_price_hidden').value = courseAmt;

    const schedSection = document.getElementById('inv_schedule_section');
    if (typeInput?.value === 'private') {
        const bundleEl   = document.getElementById('bundle_select');
        const bundleText = bundleEl?.value ? (bundleEl.options[bundleEl.selectedIndex]?.text || null) : null;
        document.getElementById('inv_schedule').innerHTML =
            infRow('Teacher',       teacherEl?.options[teacherEl.selectedIndex]?.text || '—', 'blue') +
            infRow('Preferred Days', daySelectEl?.value || '—') +
            (bundleText ? infRow('Bundle', bundleText) : '');
        schedSection.style.display = '';
    } else {
        schedSection.style.display = 'none';
    }

    infOpenModal();
};
</script>