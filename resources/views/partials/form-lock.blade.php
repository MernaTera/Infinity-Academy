<style>
.is-locked{pointer-events:none !important;opacity:0.6 !important;cursor:progress !important;}
</style>
<script>
(function () {
    if (window.__formLockInit) return;
    window.__formLockInit = true;

    function isPost(form) {
        return (form.getAttribute('method') || 'get').toLowerCase() === 'post';
    }

    function submitButtons(form) {
        var list = Array.prototype.slice.call(
            form.querySelectorAll('button:not([type]), button[type="submit"], input[type="submit"]')
        );
        if (form.id) {
            list = list.concat(Array.prototype.slice.call(
                document.querySelectorAll('[form="' + form.id + '"]')
            ));
        }
        return list;
    }

    function unlock(form) {
        delete form.dataset.locked;
        submitButtons(form).forEach(function (b) {
            b.classList.remove('is-locked');
            b.removeAttribute('aria-busy');
        });
    }

    function lock(form) {
        form.dataset.locked = '1';
        submitButtons(form).forEach(function (b) {
            b.classList.add('is-locked');
            b.setAttribute('aria-busy', 'true');
        });
        var wait = (form.target && form.target !== '_self') ? 4000 : 20000;
        setTimeout(function () { unlock(form); }, wait);
    }

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!(form instanceof HTMLFormElement) || !isPost(form)) return;
        if (form.hasAttribute('data-no-lock')) return;
        if (form.dataset.locked) { e.preventDefault(); return; }
        if (e.defaultPrevented) return;
        lock(form);
    });

    var nativeSubmit = HTMLFormElement.prototype.submit;
    HTMLFormElement.prototype.submit = function () {
        if (isPost(this) && !this.hasAttribute('data-no-lock')) {
            if (this.dataset.locked) return;
            lock(this);
        }
        return nativeSubmit.call(this);
    };

    window.addEventListener('pageshow', function (e) {
        if (!e.persisted) return;
        document.querySelectorAll('form[data-locked]').forEach(unlock);
    });
})();
</script>
