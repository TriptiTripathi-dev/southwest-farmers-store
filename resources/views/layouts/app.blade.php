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
        .tp-link.active, .tp-link:hover {
            background-color: #019934 !important;
            color: #fff !important;
        }
        .tp-link.active .nav-icon iconify-icon, .tp-link:hover .nav-icon iconify-icon {
            color: #fff !important;
        }
        .menu-arrow {
            color: inherit !important;
        }
    </style>
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
    @stack('scripts')

</body>

</html>
