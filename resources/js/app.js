import './bootstrap';

const applyTheme = (theme) => {
    document.documentElement.dataset.theme = theme;
    localStorage.setItem('poultrypro-theme', theme);

    const icon = document.querySelector('#theme-toggle .material-symbols-rounded');
    if (icon) {
        icon.textContent = theme === 'dark' ? 'light_mode' : 'dark_mode';
    }
};

const initialTheme = localStorage.getItem('poultrypro-theme') || 'light';
applyTheme(initialTheme);

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('theme-toggle')?.addEventListener('click', () => {
        const current = document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light';
        applyTheme(current === 'dark' ? 'light' : 'dark');
    });
});

document.addEventListener('submit', (event) => {
    const form = event.target;

    // Intercept native confirm() handlers and replace with SweetAlert2
    const onsubmitAttr = form.getAttribute('onsubmit');
    if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
        event.preventDefault();
        event.stopPropagation();

        // Extract the message from confirm('...')
        let message = "Are you sure you want to perform this action?";
        const match = onsubmitAttr.match(/confirm\(['"](.*?)['"]\)/);
        if (match && match[1]) {
            message = match[1];
        }

        // Show SweetAlert2
        Swal.fire({
            title: 'Confirmation',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#10b981', // emerald-600
            cancelButtonColor: '#ef4444',  // red-500
            confirmButtonText: 'Yes, proceed',
            cancelButtonText: 'Cancel',
            background: document.documentElement.dataset.theme === 'dark' ? '#1e293b' : '#ffffff',
            color: document.documentElement.dataset.theme === 'dark' ? '#f8fafc' : '#0f172a',
        }).then((result) => {
            if (result.isConfirmed) {
                // Temporarily strip the attribute to avoid re-triggering and submit form
                form.removeAttribute('onsubmit');
                form.submit();
            }
        });
        return;
    }

}, true);

document.addEventListener('alpine:init', () => {
    Alpine.data('ajaxTabs', () => ({
        cache: {},
        
        prefetchTab(e) {
            let link = e.target.closest('a');
            if (!link || !link.href || this.cache[link.href]) return;
            if (link.target === '_blank' || link.hasAttribute('download') || link.href.includes('pdf')) return;
            
            fetch(link.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                    let doc = new DOMParser().parseFromString(html, 'text/html');
                    let newEl = doc.getElementById(this.$el.id);
                    if (newEl) {
                        this.cache[link.href] = newEl.innerHTML;
                    }
                }).catch(() => {});
        },

        handleTabClick(e) {
            let link = e.target.closest('a');
            if (!link || !link.href) return;
            
            if (link.target === '_blank' || link.hasAttribute('download') || link.href.includes('pdf')) return;
            
            e.preventDefault();
            
            if (this.cache[link.href]) {
                this.$el.innerHTML = this.cache[link.href];
                window.history.pushState({}, '', link.href);
                return;
            }
            
            this.$el.style.opacity = '0.5';
            this.$el.style.pointerEvents = 'none';
            
            fetch(link.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                    let doc = new DOMParser().parseFromString(html, 'text/html');
                    let newEl = doc.getElementById(this.$el.id);
                    if (newEl) {
                        this.cache[link.href] = newEl.innerHTML;
                        this.$el.innerHTML = newEl.innerHTML;
                        window.history.pushState({}, '', link.href);
                    } else {
                        window.location.href = link.href;
                    }
                })
                .catch(() => {
                    window.location.href = link.href;
                })
                .finally(() => {
                    this.$el.style.opacity = '1';
                    this.$el.style.pointerEvents = 'auto';
                });
        }
    }));
});
