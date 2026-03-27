function updateStatus(el, leadId, newStatus) {

    // 🟢 لو Registered → redirect فقط
    if (newStatus === 'Registered') {

        if (confirm("Are you sure you want to register this lead?")) {

            window.location.href = `/registration/from-lead/${leadId}`;

        }

        return; // مهم جدًا
    }

    // 🔵 باقي الحالات → update عادي
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
            location.reload(); // عشان الـ badge يتحدث
        }
    })
    .catch(err => {
        console.error(err);
        alert("Error updating status");
    });
}