document.addEventListener('DOMContentLoaded', function () {

    const courseSelect   = document.getElementById('course_select');
    const levelSelect    = document.getElementById('level_select');
    const sublevelSelect = document.getElementById('sublevel_select');

    // ─────────────────────────────────────────
    // Fetch levels for a course
    // ─────────────────────────────────────────
    function fetchLevels(courseId, selectedLevelId = null) {
        if (!levelSelect) return;

        levelSelect.innerHTML = '<option value="">— Select Level —</option>';
        if (sublevelSelect) sublevelSelect.innerHTML = '<option value="">— Select Sublevel —</option>';

        if (!courseId) return;

        fetch(`/levels/${courseId}`)
            .then(r => r.json())
            .then(levels => {
                levels.forEach(l => {
                    const opt = document.createElement('option');
                    opt.value       = l.level_id;
                    opt.textContent = l.name;
                    if (selectedLevelId && l.level_id == selectedLevelId) {
                        opt.selected = true;
                    }
                    levelSelect.appendChild(opt);
                });

                // If we had a pre-selected level, fetch its sublevels too
                if (selectedLevelId) {
                    fetchSublevels(selectedLevelId, currentSublevelId);
                }
            })
            .catch(err => console.error('Levels fetch error:', err));
    }

    // ─────────────────────────────────────────
    // Fetch sublevels for a level
    // ─────────────────────────────────────────
    function fetchSublevels(levelId, selectedSublevelId = null) {
        if (!sublevelSelect) return;

        sublevelSelect.innerHTML = '<option value="">— Select Sublevel —</option>';

        if (!levelId) return;

        fetch(`/sublevels/${levelId}`)
            .then(r => r.json())
            .then(sublevels => {
                if (!sublevels.length) return;
                sublevels.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value       = s.sublevel_id;
                    opt.textContent = s.name;
                    if (selectedSublevelId && s.sublevel_id == selectedSublevelId) {
                        opt.selected = true;
                    }
                    sublevelSelect.appendChild(opt);
                });
            })
            .catch(err => console.error('Sublevels fetch error:', err));
    }

    // ─────────────────────────────────────────
    // Read pre-selected values (edit mode)
    // ─────────────────────────────────────────
    const currentCourseId   = courseSelect?.value || null;
    const currentLevelId    = levelSelect?.dataset.selected || levelSelect?.value || null;
    const currentSublevelId = sublevelSelect?.dataset.selected || sublevelSelect?.value || null;

    // ─────────────────────────────────────────
    // On page load — if edit mode with existing values
    // ─────────────────────────────────────────
    if (currentCourseId && levelSelect) {
        // Check if levels are already rendered (edit mode from controller)
        const hasLevels = levelSelect.options.length > 1;

        if (!hasLevels) {
            // No levels rendered — fetch them (create mode or old edit)
            fetchLevels(currentCourseId, currentLevelId);
        } else {
            // Levels already rendered by blade — just handle sublevels
            const selectedLevel = levelSelect.value;
            if (selectedLevel && sublevelSelect) {
                const hasSublevels = sublevelSelect.options.length > 1;
                if (!hasSublevels) {
                    fetchSublevels(selectedLevel, currentSublevelId);
                }
            }
        }
    }

    // ─────────────────────────────────────────
    // Course change → reload levels
    // ─────────────────────────────────────────
    if (courseSelect) {
        courseSelect.addEventListener('change', function () {
            fetchLevels(this.value);
        });
    }

    // ─────────────────────────────────────────
    // Level change → reload sublevels
    // ─────────────────────────────────────────
    if (levelSelect) {
        levelSelect.addEventListener('change', function () {
            fetchSublevels(this.value);
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
            if (!show) {
                const input = dateField.querySelector('input');
                if (input) input.value = '';
            }
        }
        toggleDateField();
        prefSelect.addEventListener('change', toggleDateField);
    }

    // ─────────────────────────────────────────
    // Required field highlight on submit
    // ─────────────────────────────────────────
    // The lead form's submit handling (double-submit guard + required-field
    // highlight) lives inline in leads/partials/form.blade.php so it always
    // runs regardless of this file's cache state. Nothing to bind here.

});