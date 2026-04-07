function updateStatus(el, leadId, newStatus) {

    // 🟢 لو Registered → redirect فقط
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
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload(); 
        }
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

    // ================= TYPE =================
    document.querySelectorAll('input[name="type"]').forEach(radio => {
        radio.addEventListener('change', function () {

            let isPrivate = this.value === 'private';

            document.getElementById('private_section').style.display =
                isPrivate ? 'block' : 'none';

            document.getElementById('group_section').style.display =
                isPrivate ? 'none' : 'block';
        });
    });

    // ================= COURSE → LEVEL =================
    course.addEventListener('change', () => {

        fetch(`/levels/${courseId}`)
        .then(res => res.json())
        .then(data => {

            level.innerHTML = '<option>Select Level</option>';

            data.forEach(l => {
                level.innerHTML += `<option value="${l.level_id}">${l.name}</option>`;
            });
        });

    });

    // ================= LEVEL → SUBLEVEL =================
    level.addEventListener('change', () => {

        fetch(`/sublevels/${levelId}`)
        .then(res => res.json())
        .then(data => {

            sublevel.innerHTML = '';

            data.forEach(s => {
                sublevel.innerHTML += `<option value="${s.sublevel_id}">${s.name}</option>`;
            });

        });

        loadPatch();
        calculatePrice();
    });

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

    patch.addEventListener('change', () => {

        let selected = patch.options[patch.selectedIndex];

        patchId.value = selected.dataset.id;

        customDate.style.display =
            patch.value === 'custom' ? 'block' : 'none';
    });

    // ================= PRICING =================

    course.addEventListener('change', () => {
        calculatePrice();
        loadPatch();
    });

    level.addEventListener('change', calculatePrice);
    sublevel.addEventListener('change', calculatePrice);

    function calculatePrice() {

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
                bundle_id: document.getElementById('bundle_select')?.value,
                discount_value: document.getElementById('discount').value
            })
        })
        .then(res => res.json())
        .then(data => {
            console.log(data);
            document.getElementById('base_price').value = data.base_price;
            document.getElementById('final_price').value = data.final_price;

        });
    }

    document.getElementById('discount').addEventListener('input', calculatePrice);
    document.getElementById('bundle_select')?.addEventListener('change', calculatePrice);
    document.querySelectorAll('input[name="type"]').forEach(radio => {

        radio.addEventListener('change', function () {

            calculatePrice();
        });

    });
    // ================= PRIVATE =================
    function loadTeachers() {

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
                time_slot_id: document.getElementById('time_slot_select').value
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

                teacher.style.display = 'none';
                recommended.style.display = 'block';

                let d = new Date();
                d.setDate(d.getDate() + 7);

                recommended.value = d.toISOString().split('T')[0];
            }

        });
    }

    document.getElementById('bundle_select').addEventListener('change', function () {

        let price = this.options[this.selectedIndex].dataset.price;

        document.getElementById('bundle_price').value = price;

    });

    ['day_select','time_slot_select'].forEach(id =>
        document.getElementById(id)?.addEventListener('change', loadTeachers)
    );

});
    window.addEventListener('load', function () {

        document.getElementById('course_select').dispatchEvent(new Event('change'));
        document.getElementById('level_select').dispatchEvent(new Event('change'));

        setTimeout(() => {
            if (typeof calculatePrice === 'function') {
                calculatePrice();
            }
        }, 200);

    });

    function loadMaterials() {

    fetch('/materials', {
        method: 'POST',
        body: JSON.stringify({
            course_template_id: course.value,
            level_id: level.value,
            sublevel_id: sublevel.value
        }),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(res => res.json())
    .then(data => {

        let html = '';

        data.forEach(m => {
            html += `
                <label>
                    <input type="checkbox" name="materials[]" value="${m.material_id}" ${m.is_mandatory ? 'checked disabled' : ''}>
                    ${m.name} (${m.price} LE)
                </label><br>
            `;
        });

        document.getElementById('materials_section').innerHTML = html;

    });
}