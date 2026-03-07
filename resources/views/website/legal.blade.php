<x-website-layout title="{{ $page->title }}">
    <div class="py-5" style="background: linear-gradient(135deg, #019934 0%, #004d1b 100%); color: white;">
        <div class="container py-5 text-center">
            <h1 class="display-3 fw-black mb-0">{{ $page->title }}</h1>
        </div>
    </div>

    <div class="py-5 bg-white">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4">
                        <div class="prose">
                            {!! $page->content !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-website-layout>
