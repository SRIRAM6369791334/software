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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Validation Failed',
                html: `<ul style="text-align: left; list-style-type: disc; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ addslashes($error) }}</li>
                    @endforeach
                </ul>`,
                confirmButtonColor: '#ef4444',
                background: document.documentElement.dataset.theme === 'dark' ? '#1e293b' : '#ffffff',
                color: document.documentElement.dataset.theme === 'dark' ? '#f8fafc' : '#0f172a',
            });
        });
    </script>
@endif
