@props(['icon' => null, 'title' => null, 'description' => null, 'breadcrumbs' => null])
{{--
    The one page header used across the store app (client 9/11 list, item 10):
    breadcrumb path, icon + bold title, grey description, and the page's own
    action buttons (the slot) on the right, in the same white card everywhere.

    With no props it reads config/navigation.php for the current route, so the
    title and path always match the sidebar. Any prop can still be passed to
    override (e.g. a create/edit page that isn't in the sidebar). A named
    <x-slot:subtitle> replaces the description when it needs markup.
--}}
@php
    $routeName = \Illuminate\Support\Facades\Route::currentRouteName();
    // Route names contain dots, so look the key up directly -- config('navigation.' . $name)
    // would read the dots as nested keys.
    $nav = config('navigation')[$routeName] ?? [];

    $title ??= $nav['title'] ?? null;
    $icon ??= $nav['icon'] ?? null;
    $description ??= $nav['description'] ?? null;

    if ($breadcrumbs === null) {
        $breadcrumbs = [];
        if ($routeName !== 'dashboard') {
            $breadcrumbs[] = ['label' => 'Dashboard', 'url' => route('dashboard')];
        }
        if (! empty($nav['section'])) {
            // The section links to its first page in the sidebar.
            $sectionRoute = collect(config('navigation'))
                ->filter(fn ($p) => ($p['section'] ?? null) === $nav['section'])
                ->keys()->first();
            $breadcrumbs[] = [
                'label' => $nav['section'],
                'url' => $sectionRoute && $sectionRoute !== $routeName && \Illuminate\Support\Facades\Route::has($sectionRoute) ? route($sectionRoute) : null,
            ];
        }
        $breadcrumbs[] = ['label' => $title, 'url' => null];
    }
@endphp

<div {{ $attributes->merge(['class' => 'page-header-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 mt-3 bg-white p-3 rounded shadow-sm']) }}>
    <div>
        {{-- A one-item path (the Dashboard itself) would just repeat the title. --}}
        @if (count($breadcrumbs) > 1)
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                    @foreach ($breadcrumbs as $i => $crumb)
                        @if ($i === count($breadcrumbs) - 1 || empty($crumb['url']))
                            <li class="breadcrumb-item text-muted" @if ($i === count($breadcrumbs) - 1) aria-current="page" @endif>{{ $crumb['label'] }}</li>
                        @else
                            <li class="breadcrumb-item"><a href="{{ $crumb['url'] }}" class="page-breadcrumb-link">{{ $crumb['label'] }}</a></li>
                        @endif
                    @endforeach
                </ol>
            </nav>
        @endif

        <h4 class="page-header-title fw-bold mb-0 text-dark">
            @if ($icon)<i class="{{ $icon }} text-primary me-2"></i>@endif{{ $title }}
        </h4>
        @isset($subtitle)
            <small class="text-muted d-block mt-1">{{ $subtitle }}</small>
        @elseif ($description)
            <small class="text-muted d-block mt-1">{{ $description }}</small>
        @endisset
    </div>

    @if (trim($slot) !== '')
        <div class="d-flex flex-wrap gap-2 align-items-center">
            {{ $slot }}
        </div>
    @endif
</div>
