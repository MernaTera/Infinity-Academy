const courseSelect = document.getElementById('course_select');
if (courseSelect) {
    courseSelect.addEventListener('change', function() {
        const courseId = this.value;
        const levelSelect = document.getElementById('level_select');
        const sublevelSelect = document.getElementById('sublevel_select');
        
        levelSelect.innerHTML = '<option value="">— Select Level —</option>';
        sublevelSelect.innerHTML = '<option value="">— Select Sublevel —</option>';
        
        if (!courseId) return;
        
        fetch(`/levels/${courseId}`)
            .then(r => r.json())
            .then(levels => {
                levels.forEach(l => {
                    levelSelect.innerHTML += 
                        `<option value="${l.level_id}">${l.name}</option>`;
                });
            });
    });
}
const sublevelSelect = document.getElementById('sublevel_select');
if (sublevelSelect) {
    document.getElementById('level_select').addEventListener('change', function() {
        const levelId = this.value;
        const sublevelSelect = document.getElementById('sublevel_select');
        
        sublevelSelect.innerHTML = '<option value="">— Select Sublevel —</option>';
        
        if (!levelId) return;
        
        fetch(`/sublevels/${levelId}`)
            .then(r => r.json())
            .then(sublevels => {
                if (sublevels.length === 0) return; 
                sublevels.forEach(s => {
                    sublevelSelect.innerHTML += 
                        `<option value="${s.sublevel_id}">${s.name}</option>`;
                });
            });
    });
    }

const prefSelect = document.querySelector('[name="start_preference_type"]');
const dateField = document.getElementById('specific_date_field');

if (prefSelect && dateField) {
    function toggleDateField() {
        if (prefSelect.value === 'Specific Date') {
            dateField.style.display = 'block';
        } else {
            dateField.style.display = 'none';
        }
    }

    toggleDateField();
    prefSelect.addEventListener('change', toggleDateField);
}