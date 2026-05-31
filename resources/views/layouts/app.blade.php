@props(['title' => 'Fitx Admin'])
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>{{ $title }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Fitx Admin Panel" name="description" />
    <link rel="icon" href="{{ asset('logo.png') }}?v={{ time() }}" type="image/png">

    @include('layouts.common.styles-lib')
    @stack('styles-lib')
    <style>
        :root {
            --bs-primary: #019934;
            --bs-sidebar-item-hover: #019934;
            --bs-sidebar-item-active: #fff;
            --bs-sidebar-item-active-bg: #019934;
        }
        /* Hide icons before they load to prevent flickering placeholder boxes */
        iconify-icon:not(:defined) {
            display: none !important;
        }
        iconify-icon[status="loading"] {
            opacity: 0 !important;
        }
        .tp-link.active, .tp-link:hover, .tp-link:focus, .tp-link[aria-expanded="true"], .menuitem-active > .tp-link {
            background-color: #019934 !important;
            color: #fff !important;
        }
        .tp-link.active .nav-icon iconify-icon, .tp-link:hover .nav-icon iconify-icon, .tp-link:focus .nav-icon iconify-icon, .tp-link[aria-expanded="true"] .nav-icon iconify-icon, .menuitem-active > .tp-link .nav-icon iconify-icon {
            color: #fff !important;
        }
        .tp-link.active .sidebar-text, .tp-link:hover .sidebar-text, .tp-link:focus .sidebar-text, .tp-link[aria-expanded="true"] .sidebar-text, .menuitem-active > .tp-link .sidebar-text {
            color: #fff !important;
        }
        .menu-arrow {
            color: inherit !important;
        }
        
        /* Hide MDI icons until fonts are fully loaded to prevent boxes */
        html:not(.fonts-loaded) .mdi::before {
            color: transparent !important;
        }
    </style>
    <script>
        // Add fonts-loaded class when all web fonts have finished loading
        document.fonts.ready.then(function() {
            document.documentElement.classList.add('fonts-loaded');
        });
    </script>
    @stack('styles')
</head>

<body data-menu-color="light" data-sidebar="default">

    <div id="app-layout">

        @include('layouts.partials.header')

        @if(!auth()->user()->hasRole('Cashier'))
            @include('layouts.partials.sidebar')
        @endif

        <div class="content-page" @if(auth()->user()->hasRole('Cashier')) style="margin-left: 0 !important; padding: 0 !important;" @endif>
            <div class="content">

                {{ $slot }}

            </div>

            @include('layouts.partials.footer')

        </div>
    </div>

    @include('layouts.common.scripts-lib')
    @stack('scripts-lib')

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const forms = document.querySelectorAll('.needs-validation');

            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {

                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    form.classList.add('was-validated');

                }, false);
            });

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                showCloseButton: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            @if (session('success'))
                Toast.fire({
                    icon: 'success',
                    title: @json(session('success'))
                });
            @endif

            @if (session('error'))
                Toast.fire({
                    icon: 'error',
                    title: @json(session('error'))
                });
            @endif

            @if ($errors->any())
                Toast.fire({
                    icon: 'error',
                    title: @json($errors->first())
                });
            @endif
        });
    </script>

    <script>
        if (typeof $ !== 'undefined' && $.fn && $.fn.dataTable) {
            $.fn.dataTable.ext.errMode = function(settings, helpPage, message) {
                console.warn('DataTables Ajax Error:', message);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data Load Issue',
                        text: 'Unable to fetch data right now. Please try again or check your connection.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000
                    });
                }
            };
        }
    </script>
    <script>
        document.addEventListener('click', function(e) {
            const deleteBtn = e.target.closest('.delete-btn');
            if (deleteBtn) {
                e.preventDefault();
                const form = deleteBtn.closest('form');
                if (!form) return;

                const title = deleteBtn.getAttribute('title') || 'Are you sure?';
                
                Swal.fire({
                    title: title,
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, delete it!',
                    customClass: {
                        confirmButton: 'btn btn-danger rounded-pill px-4 fw-bold',
                        cancelButton: 'btn btn-secondary rounded-pill px-4 ms-2 fw-bold'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }
        });

        // Global data-confirm handler using SweetAlert
        document.addEventListener('click', function(e) {
            let el = e.target.closest('[data-confirm]');
            if (el && el.tagName !== 'FORM' && el.type !== 'submit') {
                e.preventDefault();
                let msg = el.getAttribute('data-confirm');
                Swal.fire({
                    title: 'Are you sure?',
                    text: msg,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, proceed!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (el.tagName === 'A') {
                            window.location.href = el.href;
                        }
                    }
                });
            }
        });

        document.addEventListener('submit', function(e) {
            let form = e.target;
            if (form.hasAttribute('data-confirm')) {
                e.preventDefault();
                let msg = form.getAttribute('data-confirm');
                Swal.fire({
                    title: 'Are you sure?',
                    text: msg,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, proceed!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.removeAttribute('data-confirm');
                        form.submit();
                    }
                });
            }
        });

        // Global interceptor for .status-toggle switches
        document.addEventListener('click', function(e) {
            if (e.target.matches('.status-toggle')) {
                if (e.target.dataset.swalConfirmed) {
                    e.target.removeAttribute('data-swal-confirmed');
                    return; // Let the click proceed normally
                }
                
                e.preventDefault(); // Stop immediate toggle
                
                Swal.fire({
                    title: 'Change Status?',
                    text: 'Are you sure you want to change the status?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981', // success
                    cancelButtonColor: '#6c757d', // secondary
                    confirmButtonText: 'Yes, change it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        e.target.dataset.swalConfirmed = "true";
                        e.target.click(); // Programmatically trigger click to continue
                    }
                });
            }
        });
    </script>
    @include('layouts.partials._import-progress-scripts')
    @stack('scripts')

</body>

</html>
