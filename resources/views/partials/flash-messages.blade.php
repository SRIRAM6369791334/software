@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: document.documentElement.dataset.theme === 'dark' ? '#1e293b' : '#ffffff',
                color: document.documentElement.dataset.theme === 'dark' ? '#f8fafc' : '#0f172a',
            });
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}",
                confirmButtonColor: '#ef4444',
                background: document.documentElement.dataset.theme === 'dark' ? '#1e293b' : '#ffffff',
                color: document.documentElement.dataset.theme === 'dark' ? '#f8fafc' : '#0f172a',
            });
        });
    </script>
@endif

@if($errors->any())
    <style>
        .cm-swal-popup { border-radius: 16px !important; padding: 2rem !important; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important; }
        .cm-swal-title { font-size: 1.35rem !important; font-weight: 700 !important; color: #1e293b !important; }
        .cm-error-item { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 500; display: flex; align-items: flex-start; gap: 8px; text-align: left; margin-bottom: 8px; }
        .cm-error-item svg { flex-shrink: 0; margin-top: 2px; }
        .cm-swal-btn { background: #0f172a !important; color: #ffffff !important; padding: 0.6rem 1.5rem !important; border-radius: 8px !important; font-weight: 600 !important; font-size: 0.9rem !important; border: none !important; margin-top: 0.5rem !important; cursor: pointer; transition: opacity 0.2s; }
        .cm-swal-btn:hover { opacity: 0.9; }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'warning',
                title: 'Almost there!',
                html: `
                    <div style="margin-top: 1rem; display: flex; flex-direction: column;">
                        @foreach($errors->all() as $error)
                            <div class="cm-error-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <span>{{ addslashes($error) }}</span>
                            </div>
                        @endforeach
                    </div>
                `,
                customClass: {
                    popup: 'cm-swal-popup',
                    title: 'cm-swal-title',
                    confirmButton: 'cm-swal-btn'
                },
                buttonsStyling: false,
                confirmButtonText: 'Got it, let me fix it',
                background: document.documentElement.dataset.theme === 'dark' ? '#1e293b' : '#ffffff',
                color: document.documentElement.dataset.theme === 'dark' ? '#f8fafc' : '#0f172a',
            });
        });
    </script>
@endif
