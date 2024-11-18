@extends('layouts.app')

@section('title', 'Products')

@section('header', 'Product Management')

@section('content')
<div class="container">
    <h2>Edit Product</h2>

    <!-- Card for Edit Product Form -->
    <div class="card">
        <div class="card-header">
            <strong>Edit Product: {{ $product->name }}</strong>
        </div>
        <div class="card-body">
            <form id="edit-product-form">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="category">Category</label>
                    <select class="form-control" id="category_id" name="category_id" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $category->id == $product->category_id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="subcategory">Subcategory</label>
                    <select class="form-control" id="subcategory_id" name="subcategory_id" required>
                        @foreach($subcategories as $subcategory)
                            <option value="{{ $subcategory->id }}" {{ $subcategory->id == $product->subcategory_id ? 'selected' : '' }}>{{ $subcategory->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $product->name }}" required>
                </div>
                <div class="form-group">
                    <label for="regular_price">Regular Price</label>
                    <input type="number" class="form-control" id="regular_price" name="regular_price" value="{{ $product->regular_price }}" required>
                </div>

                <!-- Dynamic Sizes and Prices -->
                <div id="sizes-container">
                    <h4>Sizes and Prices</h4>
                    <button type="button" class="btn btn-success mb-2" id="add-size">Add Size</button>
                    @foreach($product->sizes as $key => $size)
                        <div class="form-row mb-2 size-row" data-id="{{ $size->id }}">
                            <div class="col">
                                <input type="text" class="form-control" name="sizes[{{ $key }}][size]" value="{{ $size->size }}" required>
                            </div>
                            <div class="col">
                                <input type="number" class="form-control" name="sizes[{{ $key }}][price]" value="{{ $size->price }}" required>
                            </div>
                            <button type="button" class="btn btn-danger remove-size" data-id="{{ $size->id }}">Remove</button>
                        </div>
                    @endforeach
                </div>

                <!-- Image Uploads -->
                <div class="form-group">
                    <label for="images">Product Images</label>
                    <input type="file" class="form-control" id="images" name="images[]" multiple>
                    <div class="mt-2" id="images-container">
                        @foreach($product->images as $image)
                            <div class="image-row" data-id="{{ $image->id }}">
                                <img src="{{ asset('storage/' . $image->path) }}" alt="Image" width="50" height="50">
                                <button type="button" class="btn btn-danger btn-sm delete-image" data-id="{{ $image->id }}">Delete</button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Product</button>
            </form>
        </div>
    </div>
</div>    
</div>

@endsection

@push('scripts')

<script>
   
    $('#category_id').on('change', function () {
        let categoryId = $(this).val();
        $('#subcategory_id').html('<option value="">Loading...</option>');
        if (categoryId) {
            $.ajax({
                url: '/fetch-subcategories/' + categoryId,
                method: 'GET',
                success: function (data) {
                    let options = '<option value="">Select Subcategory</option>';
                    data.forEach(function (subcategory) {
                        options += `<option value="${subcategory.id}">${subcategory.name}</option>`;
                    });
                    $('#subcategory_id').html(options);
                }
            });
        } else {
            $('#subcategory_id').html('<option value="">Select Subcategory</option>');
        }
    });

    
    $('#edit-product-form').on('submit', function (e) {
        e.preventDefault();
        let formData = new FormData(this);

        $.ajax({
            url: "{{ route('products.update', $product->id) }}",
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                alert(response.message);
                window.location.href = "{{ route('products.index') }}";
            },
            error: function (xhr) {
                alert('Something went wrong. Please check your input.');
            }
        });
    });

    $(document).on('click', '.remove-size', function () {
            let sizeId = $(this).data('id');
            let sizeRow = $(this).closest('.size-row'); 

            if (confirm('Are you sure you want to remove this size?')) {
                $.ajax({
                    url: '/products/delete-size/' + sizeId, 
                    method: 'DELETE',
                    success: function (response) {
                        sizeRow.remove();
                        alert('Size removed successfully');
                    },
                    error: function () {
                        alert('Error removing size');
                    }
                });
            }
        });

   
    $(document).on('click', '.delete-image', function () {
        let imageId = $(this).data('id');
        let imageRow = $(this).closest('.image-row');

        if (confirm('Are you sure you want to delete this image?')) {
            $.ajax({
                url: '/products/delete-image/' + imageId, 
                method: 'DELETE',
                success: function (response) {
                    imageRow.remove();
                    alert('Image deleted successfully');
                },
                error: function () {
                    alert('Error deleting image');
                }
            });
        }
    });
</script>

@endpush
