@extends('layouts.app')

@section('title', 'Products')

@section('header', 'Product Management')

@section('content')
<div class="container">
    <h2>Create Product</h2>
    
    <!-- Card for Create Product Form -->
    <div class="card">
        <div class="card-header">
            <strong>Create a New Product</strong>
        </div>
        <div class="card-body">
            <form id="productForm" enctype="multipart/form-data">
                @csrf

                <!-- Category -->
                <div class="mb-3">
                    <label for="category_id" class="form-label">Category</label>
                    <select name="category_id" id="category_id" class="form-control">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Subcategory -->
                <div class="mb-3">
                    <label for="subcategory_id" class="form-label">Subcategory</label>
                    <select name="subcategory_id" id="subcategory_id" class="form-control">
                        <option value="">Select Subcategory</option>
                    </select>
                    @error('subcategory_id')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}">
                    @error('name')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

               
                <div class="mb-3">
                    <label for="regular_price" class="form-label">Regular Price</label>
                    <input type="number" step="0.01" name="regular_price" id="regular_price" class="form-control" value="{{ old('regular_price') }}">
                    @error('regular_price')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Dynamic Sizes and Prices -->
                <div id="sizeContainer">
                    <div class="row mb-3">
                        <div class="col-md-5">
                            <input type="text" name="sizes[0][size]" class="form-control" placeholder="Size" value="{{ old('sizes.0.size') }}">
                            @error('sizes.0.size')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-5">
                            <input type="number" step="0.01" name="sizes[0][price]" class="form-control" placeholder="Price" value="{{ old('sizes.0.price') }}">
                            @error('sizes.0.price')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-success" id="addSize">Add</button>
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="mb-3">
                    <label for="images" class="form-label">Product Images</label>
                    <input type="file" name="images[]" id="images" class="form-control" multiple>
                    @error('images.*')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Create Product</button>
            </form>
        </div>
    </div>
</div>


@endsection


@push('scripts')
<script>
    $(document).ready(function () {
       
        $('#category_id').change(function () {
            const categoryId = $(this).val();
            if (categoryId) {
                $.get(`/fetch-subcategories/${categoryId}`, function (data) {
                    let options = '<option value="">Select Subcategory</option>';
                    data.forEach(function (subcategory) {
                        options += `<option value="${subcategory.id}">${subcategory.name}</option>`;
                    });
                    $('#subcategory_id').html(options);
                });
            } else {
                $('#subcategory_id').html('<option value="">Select Subcategory</option>');
            }
        });

       
        let sizeIndex = 1;
        $('#addSize').click(function () {
            const newSizeRow = `
                <div class="row mb-3">
                    <div class="col-md-5">
                        <input type="text" name="sizes[${sizeIndex}][size]" class="form-control" placeholder="Size">
                    </div>
                    <div class="col-md-5">
                        <input type="number" step="0.01" name="sizes[${sizeIndex}][price]" class="form-control" placeholder="Price">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger removeSize">Remove</button>
                    </div>
                </div>`;
            $('#sizeContainer').append(newSizeRow);
            sizeIndex++;
        });

       
        $(document).on('click', '.removeSize', function () {
            $(this).closest('.row').remove();
        });

        
        $('#productForm').submit(function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            $.ajax({
                url: "{{ route('products.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    alert(response.message);
                    window.location.href = "{{ route('products.index') }}";
                },
                error: function (xhr) {
                    if (xhr.responseJSON.errors) {
                        for (const key in xhr.responseJSON.errors) {
                            alert(xhr.responseJSON.errors[key][0]);
                        }
                    } else {
                        alert('Something went wrong.');
                    }
                }
            });
        });
    });
</script>

@endpush
