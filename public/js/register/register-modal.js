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

document.getElementById('course_select').addEventListener('change', function () {

    let courseId = this.value;

    fetch(`/patch-options/${courseId}`)
        .then(res => res.json())
        .then(options => {

            let patchSelect = document.getElementById('patch_select');
            patchSelect.innerHTML = '';

            options.forEach(opt => {
                patchSelect.innerHTML += `
                    <option value="${opt.type}" data-patch="${opt.patch_id ?? ''}">
                        ${opt.label}
                    </option>
                `;
            });

        });
});

document.getElementById('patch_select').addEventListener('change', function () {

    let selectedOption = this.options[this.selectedIndex];
    let patchId = selectedOption.dataset.patch;

    document.getElementById('patch_id').value = patchId || '';

    let selected = this.value;
    let dateInput = document.getElementById('custom_date');

    if (selected === 'custom') {
        dateInput.style.display = 'block';
    } else {
        dateInput.style.display = 'none';
    }

});

document.getElementById('type_select').addEventListener('change', function () {

    let isPrivate = this.value === 'private';

    document.getElementById('private_section').style.display = isPrivate ? 'block' : 'none';
    document.getElementById('group_section').style.display = isPrivate ? 'none' : 'block';
});

function calculatePrice() {

    fetch('/calculate-price', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            course_template_id: document.getElementById('course_select').value,
            level_id: document.getElementById('level_select').value,
            sublevel_id: document.getElementById('sublevel_select').value,
            discount_value: document.getElementById('discount').value
        })
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('base_price').value = data.base_price;
        document.getElementById('final_price').value = data.final_price;
    });
}

document.getElementById('course_select').addEventListener('change', calculatePrice);
document.getElementById('level_select').addEventListener('change', calculatePrice);
document.getElementById('sublevel_select').addEventListener('change', calculatePrice);
document.getElementById('discount').addEventListener('input', calculatePrice);

function loadTeachers() {

    fetch('/available-teachers', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            course_template_id: course_select.value,
            level_id: level_select.value,
            sublevel_id: sublevel_select.value,
            day: day_select.value,
            time_slot_id: time_slot_select.value
        })
    })
    .then(res => res.json())
    .then(teachers => {

        let select = document.getElementById('teacher_select');
        select.innerHTML = '';

        if (teachers.length) {

            teachers.forEach(t => {
                select.innerHTML += `<option value="${t.teacher_id}">${t.name}</option>`;
            });

            select.style.display = 'block';
            recommended_date.style.display = 'none';

        } else {

            select.style.display = 'none';
            recommended_date.style.display = 'block';

            let d = new Date();
            d.setDate(d.getDate() + 7);

            recommended_date.value = d.toISOString().split('T')[0];
        }
    });
}

['day_select','time_slot_select'].forEach(id =>
    document.getElementById(id).addEventListener('change', loadTeachers)
);