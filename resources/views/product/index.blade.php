@extends('layouts.app')

@section('title', 'Products')

@section('header', 'Product Management')

@section('content')

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Product List</h2>
            <a href="{{ route('products.create') }}" class="btn btn-primary mb-3">Add New Product</a>
        <div class="card-body">
   
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Subcategory</th>
                        <th>Regular Price</th>
                        <th>Thumbnail</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $index => $product)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category->name ?? 'N/A' }}</td>
                            <td>{{ $product->subcategory->name ?? 'N/A' }}</td>
                            <td>${{ number_format($product->regular_price, 2) }}</td>
                            <td>
                                @if($product->images->isNotEmpty())
                                    <img src="{{ asset('storage/' . $product->images->firstWhere('is_thumbnail', true)->path) }}" alt="Thumbnail" class="img-thumbnail" width="80" height="80">
                                @else
                                    No thumbnail
                                @endif
                            </td>
                            <td>
                                <!-- Edit & Delete Actions -->
                                <button class="btn btn-primary" onclick="editProduct({{ $product->id }})">Edit</button>
                                <button class="btn btn-danger" onclick="deleteProduct({{ $product->id }})">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
    
    <!-- Pagination if needed -->
    {{ $products->links() }}
             </div>
    </div>
</div>


@endsection


@push('scripts')

<script>
    function editProduct(productId) {      
        window.location.href = '/products/' + productId + '/edit';
    }

    function deleteProduct(productId) {
        if (confirm('Are you sure you want to delete this product?')) {
            $.ajax({
                url: '/products/' + productId,
                type: 'DELETE',
                success: function(response) {
                    location.reload(); 
                },
                error: function(error) {
                    alert('Error deleting product');
                }
            });
        }
    }
</script>

@endpush