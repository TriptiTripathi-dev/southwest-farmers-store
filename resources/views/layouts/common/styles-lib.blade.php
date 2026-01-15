
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

@php
   $favicon = asset('assets/images/logo.jpg'); // Default Favicon
    
    // Check if store user is logged in and has a custom favicon
    if(auth()->check()) {
        $settings = \App\Models\StoreSetting::where('store_id', auth()->id())->first();
        if($settings && $settings->favicon) {
            $favicon = asset('storage/' . $settings->favicon);
        }
    }
@endphp

<link rel="shortcut icon" href="{{ $favicon }}">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />

<link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ asset('assets/css/app.css') }}" rel="stylesheet" type="text/css" />

<script src="{{ asset('assets/js/head.js') }}"></script>