@extends('layouts.app')

@section('content')
    <h1>Products</h1>
    <div class="grid grid-cols-3 gap-4">
        @foreach($products as $product)
            <div class="border p-4">
                <h2>{{ $product->name }}</h2>
                <p>{{ $product->description }}</p>
                <a href="{{ route('website.products.show', $product) }}" class="text-blue-500">View</a>
            </div>
        @endforeach
    </div>
    {{ $products->links() }}
@endsection
