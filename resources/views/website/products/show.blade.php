@extends('layouts.app')

@section('content')
    <h1>{{ $product->name }}</h1>
    <p>{{ $product->description }}</p>
    <p>Price: {{ number_format($product->price, 2) }}</p>
    <form action="{{ route('website.cart.store') }}" method="POST">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <label>Quantity:</label>
        <input type="number" name="quantity" value="1" min="1">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2">Add to cart</button>
    </form>
@endsection
