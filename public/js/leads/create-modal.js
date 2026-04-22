document.addEventListener('DOMContentLoaded', function () {

    // ─────────────────────────────────────────
    // Course → Level dynamic fetch
    // ─────────────────────────────────────────
    const courseSelect   = document.getElementById('course_select');
    const levelSelect    = document.getElementById('level_select');
    const sublevelSelect = document.getElementById('sublevel_select');

    if (courseSelect && levelSelect) {
        courseSelect.addEventListener('change', function () {
            const courseId = this.value;

            // Reset both
            levelSelect.innerHTML    = '<option value="">— Select Level —</option>';
            sublevelSelect.innerHTML = '<option value="">— Select Sublevel —</option>';

            if (!courseId) return;

            fetch(`/levels/${courseId}`)
                .then(r => r.json())
                .then(levels => {
                    if (!levels.length) return;
                    levels.forEach(l => {
                        levelSelect.innerHTML +=
                            `<option value="${l.level_id}">${l.name}</option>`;
                    });
                })
                .catch(err => console.error('Levels fetch error:', err));
        });
    }

    // ─────────────────────────────────────────
    // Level → Sublevel dynamic fetch
    // ─────────────────────────────────────────
    if (levelSelect && sublevelSelect) {
        levelSelect.addEventListener('change', function () {
            const levelId = this.value;

            sublevelSelect.innerHTML = '<option value="">— Select Sublevel —</option>';

            if (!levelId) return;

            fetch(`/sublevels/${levelId}`)
                .then(r => r.json())
                .then(sublevels => {
                    if (!sublevels.length) return;
                    sublevels.forEach(s => {
                        sublevelSelect.innerHTML +=
                            `<option value="${s.sublevel_id}">${s.name}</option>`;
                    });
                })
                .catch(err => console.error('Sublevels fetch error:', err));
        });
    }

    // ─────────────────────────────────────────
    // Start Preference → show/hide Specific Date
    // ─────────────────────────────────────────
    const prefSelect = document.querySelector('[name="start_preference_type"]');
    const dateField  = document.getElementById('specific_date_field');

    if (prefSelect && dateField) {
        function toggleDateField() {
            const show = prefSelect.value === 'Specific Date';
            dateField.style.display = show ? 'block' : 'none';

            // Clear value when hiding
            if (!show) {
                const input = dateField.querySelector('input');
                if (input) input.value = '';
            }
        }

        toggleDateField(); // run on load (handles edit mode where value is pre-set)
        prefSelect.addEventListener('change', toggleDateField);
    }

    // ─────────────────────────────────────────
    // Required field — highlight empty on submit
    // ─────────────────────────────────────────
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function () {
            form.querySelectorAll('[required]').forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#DC2626';
                    field.style.boxShadow   = '0 0 0 3px rgba(220,38,38,0.1)';

                    field.addEventListener('input', function fix() {
                        field.style.borderColor = '';
                        field.style.boxShadow   = '';
                        field.removeEventListener('input', fix);
                    }, { once: true });
                }
            });
        });
    }

});