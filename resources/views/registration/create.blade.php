```blade
@extends('layouts.app')

@section('content')

<div class="container py-4">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-dark text-white rounded-top-4">
            <h4 class="mb-0">🎓 Student Registration</h4>
        </div>

        <div class="card-body p-4">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="/register">
                @csrf

                {{-- PERSONAL --}}
                <h5 class="text-primary">👤 Personal</h5>

                <input type="text" name="full_name" placeholder="Full Name" class="form-control mb-2">
                <input type="email" name="email" placeholder="Email" class="form-control mb-2">
                <input type="text" name="phones[]" placeholder="Phone" class="form-control mb-2">
                <input type="text" name="location" placeholder="Location" class="form-control mb-3">

                <hr>

                {{-- COURSE --}}
                <h5 class="text-primary">📚 Course</h5>

                <select id="course" name="course_template_id" class="form-control mb-2">
                    <option value="">Select Course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->course_template_id }}">
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>

                <select id="level" name="level_id" class="form-control mb-2">
                    <option value="">Select Level</option>
                </select>

                <div id="sublevelContainer" style="display:none;">
                    <select id="sublevel" name="sublevel_id" class="form-control mb-2"></select>
                </div>

                <select name="type" class="form-control mb-2">
                    <option value="group">Group</option>
                    <option value="private">Private</option>
                </select>

                <select name="mode" class="form-control mb-3">
                    <option value="online">Online</option>
                    <option value="offline">Offline</option>
                </select>

                {{-- PATCH --}}
                <h5 class="text-primary">📅 Patch</h5>

                <select id="patchSelect" name="patch_option" class="form-control mb-2"></select>

                <div id="customDateContainer" style="display:none;">
                    <input type="datetime-local" name="custom_date" class="form-control mb-2">
                </div>

                <input type="hidden" name="patch_id" id="patch_id">

                {{-- PAYMENT --}}
                <select name="payment_plan_id" class="form-control mb-2">
                    <option value="">Select Plan</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}">
                            {{ $plan->name }}
                        </option>
                    @endforeach
                </select>

                <input type="text" id="price" class="form-control mb-3" placeholder="Final Price" readonly>

                {{-- TEST --}}
                <h5 class="text-primary">🧪 Test</h5>

                <input type="number" name="test_score" class="form-control mb-2" placeholder="Score">
                <input type="number" name="test_fee" class="form-control mb-3" placeholder="Fee">

                <button class="btn btn-success w-100">Register</button>
            </form>

        </div>
    </div>
</div>

<script>

// Course → Levels
document.getElementById('course').addEventListener('change', function () {

    let courseId = this.value;
    let level = document.getElementById('level');

    level.innerHTML = '<option>Loading...</option>';

    fetch(`/levels/${courseId}`)
        .then(res => res.json())
        .then(data => {

            level.innerHTML = '<option value="">Select Level</option>';

            data.forEach(l => {
                level.innerHTML += `<option value="${l.level_id}">${l.name}</option>`;
            });
        });
});


// Level → Sublevels + Patch + Price
document.getElementById('level').addEventListener('change', function () {

    let levelId = this.value;

    // Sublevels
    fetch(`/sublevels/${levelId}`)
        .then(res => res.json())
        .then(data => {

            let container = document.getElementById('sublevelContainer');
            let sub = document.getElementById('sublevel');

            sub.innerHTML = '';

            if (data.length > 0) {
                container.style.display = 'block';

                data.forEach(s => {
                    sub.innerHTML += `<option value="${s.sublevel_id}">${s.name}</option>`;
                });

            } else {
                container.style.display = 'none';
            }
        });

    // Patch
    let patch = document.getElementById('patchSelect');

    patch.innerHTML = '<option>Loading...</option>';

    fetch(`/patch-options/${levelId}`)
        .then(r => r.json())
        .then(options => {

            patch.innerHTML = '<option value="">Select Patch</option>';

            options.forEach(o => {
                patch.innerHTML += `<option value="${o.type}">${o.label}</option>`;
            });

            patch.innerHTML += `<option value="custom">Specific Date</option>`;
        });

    // Price
    fetch('/calculate-price', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ level_id: levelId })
    })
    .then(res => res.json())
    .then(price => {
        document.getElementById('price').value = price + ' EGP';
    });

});


// Patch change
document.getElementById('patchSelect').addEventListener('change', function () {

    let type = this.value;

    document.getElementById('customDateContainer').style.display =
        type === 'custom' ? 'block' : 'none';
});

</script>

@endsection
```
