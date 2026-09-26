@props(['user', 'size' => 32])
{{-- Staff photo if one was uploaded, otherwise their initials in a colored circle. --}}
@php
    $photoUrl = $user?->profile_photo_url;
    $initials = $user?->initials ?? '?';
    $palette = ['#2e7d32', '#1565c0', '#6a1b9a', '#c62828', '#ef6c00', '#00838f', '#4e342e', '#37474f'];
    $bg = $palette[($user?->id ?? 0) % count($palette)];
@endphp
@if ($photoUrl)
    <img src="{{ $photoUrl }}" alt="{{ $user->name }}" {{ $attributes->merge(['class' => 'rounded-circle']) }}
         style="width: {{ $size }}px; height: {{ $size }}px; object-fit: cover;">
@else
    <span {{ $attributes->merge(['class' => 'rounded-circle d-inline-flex align-items-center justify-content-center fw-bold text-white']) }}
          style="width: {{ $size }}px; height: {{ $size }}px; background: {{ $bg }}; font-size: {{ max(10, round($size * 0.4)) }}px; line-height: 1;"
          title="{{ $user?->name }}">{{ $initials }}</span>
@endif
