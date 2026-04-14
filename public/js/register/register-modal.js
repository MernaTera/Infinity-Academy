function updateStatus(el, leadId, newStatus) {

    if (newStatus === 'Registered') {
        if (confirm("Are you sure you want to register this lead?")) {
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
        alert("Error updating status");
    });
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

    // ================= LEVEL → SUBLEVEL =================
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

    sublevel.addEventListener('change', calculatePrice);

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
    // ================= PRICING =================
    function calculatePrice() {

        const materialCheck = document.getElementById('material_check');
        const materialPriceHidden = document.getElementById('material_price_hidden');

        fetch('/calculate-price', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                type: document.querySelector('input[name="type"]:checked').value,
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

    // 🔥 أهم سطر
    if (patch.value !== 'current') {

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

    course.addEventListener('change', loadTeachers);
    level.addEventListener('change', loadTeachers);

    // ================= INIT =================
    window.addEventListener('load', function () {

        course.dispatchEvent(new Event('change'));

        setTimeout(() => {
            calculatePrice();
            loadTeachers();
        }, 200);
    });

        function loadMaterial() {

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

    course.addEventListener('change', loadMaterial);
    level.addEventListener('change', loadMaterial);
    sublevel.addEventListener('change', loadMaterial);

    materialCheck.addEventListener('change', function () {
        materialPriceBlock.style.display =
            this.checked ? 'block' : 'none';

        calculatePrice();
    });

    setTimeout(() => {
        loadMaterial();
    }, 300);

});


    /* ══ PAYMENT DETAILS LOGIC ══ */
    const paymentSelect = document.getElementById('payment_plan_id');
 
    paymentSelect.addEventListener('change', loadPaymentDetails);
 
    function loadPaymentDetails() {
 
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
    