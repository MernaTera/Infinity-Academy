<div id="scheduleModal" style="display:none">

<form method="POST" action="" id="scheduleForm">
@csrf

<select name="day_of_week" id="daySelect"></select>

<select name="time_slot_id" id="slotSelect"></select>

<input type="time" name="start_time" required>

<button type="submit">Save</button>

</form>

</div>
<script>
function openScheduleModal(instanceId) {

    console.log("CLICKED", instanceId);

    fetch(`/student-care/instance/${instanceId}/schedule-data`)
        .then(res => res.json())
        .then(data => {

            const daySelect = document.getElementById('daySelect');
            const slotSelect = document.getElementById('slotSelect');

            daySelect.innerHTML = '';
            slotSelect.innerHTML = '';

            data.forEach(a => {
                daySelect.innerHTML += `<option value="${a.day_of_week}">
                    ${a.day_of_week}
                </option>`;

                slotSelect.innerHTML += `<option value="${a.time_slot_id}">
                    ${a.time_slot.name}
                </option>`;
            });

            document.getElementById('scheduleForm').action =
                `/student-care/instance/${instanceId}/schedule`;

            document.getElementById('scheduleModal').style.display = 'block';
        });
}
</script>   