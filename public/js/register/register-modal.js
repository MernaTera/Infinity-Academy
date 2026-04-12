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

        });
    }

    patch.addEventListener('change', function () {

        let selected = patch.options[patch.selectedIndex];

        patchId.value = selected.dataset.id;

        customDate.style.display =
            patch.value === 'custom' ? 'block' : 'none';

        loadTeachers(); // 🔥 مهم
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
            console.log("PRICE:", data);
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

        let type = document.querySelector('input[name="type"]:checked').value;

        if (type !== 'private') return;

        if (patch.value !== 'current') {

            document.getElementById('teacher_block').style.display = 'none';

            recommended.style.display = 'block';

            let d = new Date();
            d.setDate(d.getDate() + 7);

            recommended.value = d.toISOString().split('T')[0];

            return;
        }


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
                day: document.getElementById('day_select').value,
                time_slot_id: document.getElementById('time_slot_select').value,
                patch_option: patch.value
            })
        })
        .then(res => res.json())
        .then(data => {

            teacher.innerHTML = '';

            if (data.length) {

                data.forEach(t => {
                    teacher.innerHTML += `<option value="${t.teacher_id}">${t.name}</option>`;
                });

                teacher.style.display = 'block';
                recommended.style.display = 'none';

            } else {

                document.getElementById('teacher_block').style.display = 'none';
                recommended.style.display = 'block';

                let d = new Date();
                d.setDate(d.getDate() + 7);

                recommended.value = d.toISOString().split('T')[0];
            }

        });
    }

    ['day_select','time_slot_select'].forEach(id =>
        document.getElementById(id)?.addEventListener('change', loadTeachers)
    );

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

            console.log("MATERIAL:", data); 

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




