// ═══════════════════════════════════════════════════════
// Infinity Academy — Confirm Modal
// Replaces native browser confirm() with branded UI
// ═══════════════════════════════════════════════════════

// ─────────────────────────────────────────
// Inject modal HTML + styles once
// ─────────────────────────────────────────
(function injectConfirmModal() {
    if (document.getElementById('inf-confirm-overlay')) return;

    const style = document.createElement('style');
    style.textContent = `
        #inf-confirm-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(10, 20, 40, 0.45);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            z-index: 99999;
            align-items: center;
            justify-content: center;
        }
        #inf-confirm-overlay.active {
            display: flex;
            animation: infOverlayIn 0.2s ease both;
        }
        @keyframes infOverlayIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        .inf-confirm-box {
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(20px);
            border-radius: 10px;
            width: 420px;
            max-width: calc(100vw - 32px);
            overflow: hidden;
            position: relative;
            box-shadow:
                0 24px 60px rgba(27,79,168,0.15),
                0 4px 16px rgba(0,0,0,0.08);
            animation: infBoxIn 0.35s cubic-bezier(0.16,1,0.3,1) both;
        }
        @keyframes infBoxIn {
            from { opacity: 0; transform: scale(0.94) translateY(12px); }
            to   { opacity: 1; transform: none; }
        }

        /* top accent */
        .inf-confirm-box::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #F5911E, #1B4FA8, transparent);
        }

        .inf-confirm-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 32px 0;
        }

        .inf-confirm-icon-wrap {
            width: 52px; height: 52px;
            border-radius: 50%;
            background: rgba(245,145,30,0.08);
            border: 1px solid rgba(245,145,30,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .inf-confirm-icon-pulse {
            position: absolute;
            inset: -8px;
            border-radius: 50%;
            border: 1px solid rgba(245,145,30,0.15);
            animation: infPulse 2s ease-in-out infinite;
        }
        @keyframes infPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0; transform: scale(1.2); }
        }

        .inf-confirm-body {
            padding: 20px 32px 28px;
            text-align: center;
        }

        .inf-confirm-label {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 11px;
            letter-spacing: 5px;
            color: #F5911E;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .inf-confirm-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 22px;
            letter-spacing: 3px;
            color: #1B4FA8;
            margin-bottom: 10px;
            line-height: 1.1;
        }

        .inf-confirm-message {
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 300;
            color: #7A8A9A;
            line-height: 1.6;
            margin-bottom: 28px;
        }

        .inf-confirm-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .inf-confirm-cancel {
            flex: 1;
            max-width: 140px;
            padding: 11px 20px;
            background: transparent;
            border: 1px solid rgba(27,79,168,0.15);
            border-radius: 4px;
            color: #7A8A9A;
            font-family: 'DM Sans', sans-serif;
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.2s;
        }
        .inf-confirm-cancel:hover {
            border-color: rgba(27,79,168,0.3);
            color: #1A2A4A;
        }

        .inf-confirm-ok {
            flex: 1;
            max-width: 180px;
            padding: 11px 20px;
            background: transparent;
            border: 1.5px solid #1B4FA8;
            border-radius: 4px;
            color: #1B4FA8;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 14px;
            letter-spacing: 4px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: color 0.35s, border-color 0.35s;
        }
        .inf-confirm-ok::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(90deg, #1B4FA8, #2D6FDB);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.35s cubic-bezier(0.16,1,0.3,1);
        }
        .inf-confirm-ok:hover::before  { transform: scaleX(1); }
        .inf-confirm-ok:hover { color: #fff; border-color: #2D6FDB; }
        .inf-confirm-ok span { position: relative; z-index: 1; }

        .inf-confirm-footer {
            padding: 12px 32px;
            border-top: 1px solid rgba(27,79,168,0.06);
            display: flex;
            align-items: center;
            gap: 6px;
            font-family: 'DM Sans', sans-serif;
            font-size: 10px;
            color: #C8D4E0;
            letter-spacing: 0.5px;
        }
    `;
    document.head.appendChild(style);

    const overlay = document.createElement('div');
    overlay.id = 'inf-confirm-overlay';
    overlay.innerHTML = `
        <div class="inf-confirm-box">
            <div class="inf-confirm-icon">
                <div class="inf-confirm-icon-wrap">
                    <div class="inf-confirm-icon-pulse"></div>
                    <svg id="inf-confirm-icon-svg" width="22" height="22" viewBox="0 0 24 24"
                         fill="none" stroke="#F5911E" stroke-width="1.5"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </div>
            </div>
            <div class="inf-confirm-body">
                <div class="inf-confirm-label" id="inf-confirm-label">Confirm Action</div>
                <div class="inf-confirm-title"  id="inf-confirm-title">Are you sure?</div>
                <div class="inf-confirm-message" id="inf-confirm-message">This action cannot be undone.</div>
                <div class="inf-confirm-actions">
                    <button class="inf-confirm-cancel" id="inf-confirm-cancel">Cancel</button>
                    <button class="inf-confirm-ok"     id="inf-confirm-ok"><span id="inf-confirm-ok-text">Confirm</span></button>
                </div>
            </div>
            <div class="inf-confirm-footer">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                Infinity Academy — Internal System
            </div>
        </div>
    `;
    document.body.appendChild(overlay);

    // Close on overlay click
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) infConfirm.reject();
    });

    // ESC to close
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') infConfirm.reject();
    });
})();

// ─────────────────────────────────────────
// infConfirm — public API
// ─────────────────────────────────────────
const infConfirm = {
    _resolve: null,

    show({ label = 'Confirm Action', title = 'Are you sure?', message = 'This action cannot be undone.', okText = 'Confirm' } = {}) {
        return new Promise((resolve) => {
            this._resolve = resolve;

            document.getElementById('inf-confirm-label').textContent   = label;
            document.getElementById('inf-confirm-title').textContent   = title;
            document.getElementById('inf-confirm-message').textContent = message;
            document.getElementById('inf-confirm-ok-text').textContent = okText;

            const overlay = document.getElementById('inf-confirm-overlay');
            overlay.classList.add('active');

            document.getElementById('inf-confirm-ok').onclick = () => this.confirm();
            document.getElementById('inf-confirm-cancel').onclick = () => this.reject();
        });
    },

    confirm() {
        document.getElementById('inf-confirm-overlay').classList.remove('active');
        if (this._resolve) this._resolve(true);
        this._resolve = null;
    },

    reject() {
        document.getElementById('inf-confirm-overlay').classList.remove('active');
        if (this._resolve) this._resolve(false);
        this._resolve = null;
    }
};

// ─────────────────────────────────────────
// updateLeadStatus — used from index.blade
// ─────────────────────────────────────────
async function updateLeadStatus(el, leadId, newStatus) {

    // Close any open dropdowns
    document.querySelectorAll('.status-dropdown').forEach(d => d.style.display = 'none');

    if (newStatus === 'Registered') {
        const confirmed = await infConfirm.show({
            label:   'Lead Registration',
            title:   'Register This Lead?',
            message: 'This will convert the lead into a registered student and open the registration form.',
            okText:  'Register Now',
        });

        if (confirmed) {
            window.location.href = `/registration/from-lead/${leadId}`;
        }
        return;
    }

    fetch(`/leads/${leadId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) location.reload();
    })
    .catch(err => {
        console.error(err);
        infConfirm.show({
            label:   'Error',
            title:   'Something Went Wrong',
            message: 'Failed to update the lead status. Please try again.',
            okText:  'OK',
        });
    });
}

// ─────────────────────────────────────────
// toggleDropdown — status dropdown toggle
// ─────────────────────────────────────────
function toggleDropdown(badge) {
    const dropdown = badge.nextElementSibling;
    const isOpen   = dropdown.style.display === 'block';

    // Close all
    document.querySelectorAll('.status-dropdown').forEach(d => d.style.display = 'none');

    if (!isOpen) dropdown.style.display = 'block';

    // Close on outside click
    setTimeout(() => {
        document.addEventListener('click', function closeDD(e) {
            if (!dropdown.contains(e.target) && e.target !== badge) {
                dropdown.style.display = 'none';
            }
            document.removeEventListener('click', closeDD);
        });
    }, 0);
}
document.addEventListener('DOMContentLoaded', function () {

    const course = document.getElementById('course_select');
    const level = document.getElementById('level_select');
    const sublevel = document.getElementById('sublevel_select');

    const patch = document.getElementById('patch_select');
    const patchId = document.getElementById('patch_id');
    const customDate = document.getElementById('custom_date');

    const teacher = document.getElementById('teacher_select');
    const recommended = document.getElementById('recommended_date');
    const daySelect = document.getElementById('day_select');
    const timeSlot = document.getElementById('time_slot_select');
    const teacherBlock = document.getElementById('teacher_block');
    const dayBlock = document.getElementById('day_block');

    const bundle = document.getElementById('bundle_select');

    const materialSection = document.getElementById('material_section');
    const materialCheck = document.getElementById('material_check');
    const materialPriceBlock = document.getElementById('material_price_block');
    const materialPriceHidden = document.getElementById('material_price_hidden');

        /* ══ PAYMENT DETAILS LOGIC ══ */
    const paymentSelect = document.getElementById('payment_plan_id');

    if (paymentSelect) {
        paymentSelect.addEventListener('change', loadPaymentDetails);
    }

    // ================= TYPE =================
    document.querySelectorAll('input[name="type"]').forEach(radio => {
        radio.addEventListener('change', function () {

            let isPrivate = this.value === 'private';

            document.getElementById('private_extra').style.display =
                isPrivate ? 'block' : 'none';

            // reset
            teacher.innerHTML = '';
            bundle.value = '';
            calculatePrice();
            loadTeachers();
        });
    });

    // ================= COURSE → LEVEL =================
    if (course) {
        course.addEventListener('change', async function () {

            let courseId = this.value;

            level.innerHTML = '<option value="">Select Level (optional)</option>';
            sublevel.innerHTML = '<option value="">Select Sublevel (optional)</option>';

            let res = await fetch(`/levels/${courseId}`);
            let data = await res.json();

            data.forEach(l => {
                level.innerHTML += `<option value="${l.level_id}">${l.name}</option>`;
            });

            loadPatch();
            calculatePrice();
        });
    }
    
    // ================= LEVEL → SUBLEVEL =================
    if (level) {
        level.addEventListener('change', async function () {

            let levelId = this.value;

            sublevel.innerHTML = '<option value="">Select Sublevel (optional)</option>';

            if (!levelId) {
                calculatePrice();
                return;
            }

            let res = await fetch(`/sublevels/${levelId}`);
            let data = await res.json();

            data.forEach(s => {
                sublevel.innerHTML += `<option value="${s.sublevel_id}">${s.name}</option>`;
            });

            loadPatch();
            calculatePrice();
        });
    }
    if (sublevel) {
        sublevel.addEventListener('change', calculatePrice);
    }

    // ================= PATCH =================
    function loadPatch() {

        fetch(`/patch-options/${course.value}`)
        
        .then(res => res.json())
        .then(options => {

            patch.innerHTML = '';

            options.forEach(o => {
                patch.innerHTML += `<option value="${o.type}" data-id="${o.patch_id || ''}">${o.label}</option>`;
            });
            patch.dispatchEvent(new Event('change'));
        });
    }
if (patch) {
patch.addEventListener('change', function () {

    let value = this.value;

    if (value === 'current') {

        if (teacherBlock) teacherBlock.style.display = 'block';
        if (dayBlock) dayBlock.style.display = 'none';

        loadTeachers();

    } else {

        if (teacherBlock) teacherBlock.style.display = 'none';
        if (dayBlock) dayBlock.style.display = 'block';

        if (teacher) {
            teacher.innerHTML = '<option value="">Select Teacher</option>';
        }
    }

    let selected = patch.options[patch.selectedIndex];
    patchId.value = selected?.dataset?.id || '';

    let isCustom = value === 'custom';

    if (customDate) {
        customDate.style.display = isCustom ? 'block' : 'none';
        customDate.required = isCustom;
    }

});
}
if(teacher) {
teacher.addEventListener('change', function () {

    fetch('/teacher-schedule', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            teacher_id: this.value
        })
    })
    .then(res => res.json())
    .then(data => {

        daySelect.innerHTML = '';

        let uniqueDays = [...new Set(data.map(a => a.day_of_week))];

        uniqueDays.forEach(day => {
            daySelect.innerHTML += `<option value="${day}">${day}</option>`;
        });

    });
});
}
    // ================= PRICING =================
    function calculatePrice() {

        const materialCheck = document.getElementById('material_check');
        const materialPriceHidden = document.getElementById('material_price_hidden');
        const typeInput = document.querySelector('input[name="type"]:checked');

        if (!typeInput || !course || !level || !sublevel) {
            return; 
        }
        fetch('/calculate-price', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                type: typeInput.value,
                course_template_id: course.value,
                level_id: level.value,
                sublevel_id: sublevel.value,
                bundle_id: bundle?.value,
                material_price: materialCheck.checked ? materialPriceHidden.value : 0
            })
        })
        .then(res => res.json())
        .then(data => {
            let materialValue = 0;

            if (materialCheck.checked) {
                materialValue = parseFloat(materialPriceHidden.value || 0);
            }

            document.getElementById('base_price').value = data.base_price + " LE";
            document.getElementById('discount').value = data.discount + " LE";
            document.getElementById('discount_hidden').value = data.discount;
            document.getElementById('final_price').value = data.final_price + " LE";

        });
        
    }

    bundle?.addEventListener('change', calculatePrice);
    document.querySelectorAll('input[name="type"]').forEach(r => r.addEventListener('change', calculatePrice));

    // ================= PRIVATE =================
function loadTeachers() {

    const daySelect = document.getElementById('day_select');
    const timeSlot = document.getElementById('time_slot_select');

    if(!patch) return;
    if (patch?.value !== 'current') {

        teacherBlock.style.display = 'none';

        teacher.innerHTML =
            '<option disabled selected>No teachers in this patch</option>';

        return;
    }
    teacherBlock.style.display = 'block';

    fetch('/available-teachers', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            course_template_id: course.value,
            level_id: level.value,
            sublevel_id: sublevel.value,
            patch_id: patchId.value,
            patch_option: 'current'
        })
    })
    .then(res => res.json())
    .then(data => {

        teacher.innerHTML = '<option value="">Select Teacher</option>';

        if (!data.length) {
            teacher.innerHTML =
                '<option disabled selected>No teachers available in current patch</option>';
            return;
        }

        data.forEach(t => {
            let option = document.createElement('option');
            option.value = t.teacher_id;
            option.textContent = `Teacher #${t.teacher_id}`;
            teacher.appendChild(option);
        });

    });
}

    // ['day_select','time_slot_select'].forEach(id =>
    //     document.getElementById(id)?.addEventListener('change', loadTeachers)
    // );

    if(course) course.addEventListener('change', loadTeachers);
    if(level) level.addEventListener('change', loadTeachers);

    // ================= INIT =================
    window.addEventListener('load', function () {
        if (course) course.dispatchEvent(new Event('change'));

        setTimeout(() => {
            calculatePrice();
            loadTeachers();
        }, 200);
    });

        function loadMaterial() {
            if(!course) return;

            fetch('/get-material', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    course_template_id: course.value,
                    level_id: level.value,
                    sublevel_id: sublevel.value
                })
            })
            .then(res => res.json())
            .then(data => {


            if (!data || !data.material_id) {
                materialSection.style.display = 'block';

                document.getElementById('material_name').value = "No material available";
                document.getElementById('material_price').value = " -";
                materialCheck.style.display = 'none';

                return;
            }

                materialSection.style.display = 'block';


                document.getElementById('material_name').value = data.name;
                document.getElementById('material_price').value = data.price + " LE";
                materialPriceHidden.value = data.price;

            });
    }

    if(course) course.addEventListener('change', loadMaterial);
    if(level) level.addEventListener('change', loadMaterial);
    if(sublevel)sublevel.addEventListener('change', loadMaterial);
    if(materialCheck) {
    materialCheck.addEventListener('change', function () {
        materialPriceBlock.style.display =
            this.checked ? 'block' : 'none';

        calculatePrice();
    });
    }
    setTimeout(() => {
        loadMaterial();
    }, 300);

    function loadPaymentDetails() {
        
        const paymentSelect = document.getElementById('payment_plan_id'); 
        if (!paymentSelect) return;
        const selected     = paymentSelect.options[paymentSelect.selectedIndex];
        const deposit      = parseFloat(selected.dataset.deposit      || 0);
        const installments = parseInt(selected.dataset.installments   || 0);
        const grace        = parseInt(selected.dataset.grace          || 0);
        const needsApproval= selected.dataset.approval == 1;
 
        const finalPrice = parseFloat(
            document.getElementById('final_price')?.value?.replace(' LE', '') || 0
        );
 
        if (!finalPrice || !selected.value) {
            document.getElementById('payment_details').style.display = 'none';
            return;
        }
 
        const depositAmount = (finalPrice * deposit) / 100;
        const remaining     = finalPrice - depositAmount;
 
        /* ── summary rows ── */
        let summaryHTML =
            infPayRow('Plan',      selected.text) +
            infPayRow('Deposit',   deposit + '% = ' + depositAmount.toFixed(2) + ' LE', 'accent') +
            infPayRow('Remaining', remaining.toFixed(2) + ' LE', 'blue');
 
        if (needsApproval) {
            summaryHTML += `<div style="display:inline-flex;align-items:center;gap:5px;
                font-size:9px;letter-spacing:2px;text-transform:uppercase;
                color:#92400E;background:rgba(245,145,30,0.12);
                border:1px solid rgba(245,145,30,0.25);
                border-radius:3px;padding:4px 9px;margin-top:8px;">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                Requires Admin Approval
            </div>`;
        }
 
        document.getElementById('payment_summary').innerHTML = summaryHTML;
 
        /* ── installments table ── */
        const table = document.getElementById('installments_table');
        const label = document.getElementById('installments_label');
        const tbody = table.querySelector('tbody');
 
        tbody.innerHTML = '';
 
        if (installments > 0) {
            const installmentAmount = remaining / installments;
 
            for (let i = 1; i <= installments; i++) {
                const dueDate = new Date();
                dueDate.setDate(dueDate.getDate() + (grace * i));
 
                tbody.innerHTML += `<tr>
                    <td>${i}</td>
                    <td>${installmentAmount.toFixed(2)} LE</td>
                    <td>${dueDate.toISOString().split('T')[0]}</td>
                </tr>`;
            }
 
            table.style.display = 'table';
            label.style.display = 'block';
        } else {
            table.style.display = 'none';
            label.style.display = 'none';
        }
 
        document.getElementById('payment_details').style.display = 'block';
    }
 
    function infPayRow(key, val, valClass = '') {
        return `<div class="inf-pay-row">
            <span class="inf-pay-key">${key}</span>
            <span class="inf-pay-val ${valClass}">${val}</span>
        </div>`;
    }


    window.Echo.channel('waiting-list')
    .listen('.WaitingListUpdated', (e) => {
        console.log('Updated:', e.waiting);

        refreshWaitingList(); 
    });

});



 

