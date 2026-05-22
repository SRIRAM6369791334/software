import './bootstrap';

const hideSkeleton = () => {
    const skeleton = document.getElementById('page-skeleton');
    if (!skeleton) return;

    skeleton.classList.add('page-skeleton--hidden');
    window.setTimeout(() => skeleton.remove(), 380);
};

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

const showSkeleton = () => {
    if (document.getElementById('page-skeleton')) return;

    const skeleton = document.createElement('div');
    skeleton.id = 'page-skeleton';
    skeleton.className = 'page-skeleton fixed inset-0 z-[999] bg-white/90 backdrop-blur-md';
    skeleton.innerHTML = `
        <div class="flex h-full">
            <div class="hidden w-72 border-r border-emerald-100 bg-gradient-to-b from-emerald-50 to-sky-50 p-5 lg:block">
                <div class="mb-8 flex items-center gap-3">
                    <div class="skeleton-pulse h-12 w-12 rounded-2xl"></div>
                    <div class="space-y-2">
                        <div class="skeleton-pulse h-4 w-28 rounded-full"></div>
                        <div class="skeleton-pulse h-3 w-36 rounded-full"></div>
                    </div>
                </div>
                <div class="space-y-3">${Array.from({ length: 10 }, () => '<div class="skeleton-pulse h-10 rounded-xl"></div>').join('')}</div>
            </div>
            <div class="flex flex-1 flex-col">
                <div class="h-20 border-b border-emerald-100 bg-white/80 px-8 py-5">
                    <div class="flex items-center justify-between">
                        <div class="skeleton-pulse h-10 w-80 rounded-2xl"></div>
                        <div class="flex gap-3">
                            <div class="skeleton-pulse h-10 w-10 rounded-xl"></div>
                            <div class="skeleton-pulse h-10 w-10 rounded-xl"></div>
                        </div>
                    </div>
                </div>
                <div class="grid gap-6 p-8 md:grid-cols-2 xl:grid-cols-4">
                    ${Array.from({ length: 8 }, () => `
                        <div class="rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm">
                            <div class="skeleton-pulse mb-5 h-12 w-12 rounded-2xl"></div>
                            <div class="skeleton-pulse mb-3 h-4 w-28 rounded-full"></div>
                            <div class="skeleton-pulse h-8 w-36 rounded-full"></div>
                        </div>
                    `).join('')}
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(skeleton);
};

window.addEventListener('load', () => {
    window.setTimeout(hideSkeleton, 260);
});

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('theme-toggle')?.addEventListener('click', () => {
        const current = document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light';
        applyTheme(current === 'dark' ? 'light' : 'dark');
    });
});

document.addEventListener('click', (event) => {
    const link = event.target.closest('a[href]');
    if (!link) return;

    const url = new URL(link.href, window.location.href);
    const isModified = event.metaKey || event.ctrlKey || event.shiftKey || event.altKey;
    const isDownload = link.hasAttribute('download') || link.classList.contains('download') || link.getAttribute('title')?.toLowerCase().includes('download');
    const target = link.getAttribute('target');
    const isPdfOrExport = url.pathname.includes('pdf') || url.pathname.includes('export') || url.pathname.includes('download') || url.searchParams.get('export') === 'pdf';

    if (isModified || isDownload || isPdfOrExport || target === '_blank' || url.origin !== window.location.origin || url.hash) {
        return;
    }

    showSkeleton();
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

    if (form?.dataset?.skipSkeleton === 'true') return;
    showSkeleton();
}, true);
