<!-- resources/views/categories/index.blade.php -->

@extends('layouts.app')

@section('title', 'Categories')

@section('header', 'Category Management')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Categories</h2>
            <button class="btn btn-primary" id="addCategoryBtn">Add Category</button>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="categoryTableBody">
                    
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="categoryForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Add Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="categoryId">
                    <div class="form-group">
                        <label for="categoryName">Name</label>
                        <input type="text" class="form-control" id="categoryName" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveCategoryBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
      
        fetchCategories();

       
        $('#addCategoryBtn').click(function() {
            $('#categoryForm')[0].reset();
            $('#categoryId').val('');
            $('#categoryModalLabel').text('Add Category');
            $('#categoryModal').modal('show');
        });

        
        $('#categoryForm').submit(function(e) {
            e.preventDefault();
            let id = $('#categoryId').val();
            let url = id ? `/categories/${id}` : `/categories`;
            let method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: {
                    name: $('#categoryName').val()
                },
                success: function(response) {
                    $('#categoryModal').modal('hide');
                    fetchCategories();
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        });

        
        function fetchCategories() {
            $.ajax({
                url: '/fetch-categories',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        let rows = '';
                        response.categories.forEach((category, index) => {
                            rows += `
                                <tr>
                                    <td>${index + 1}</td> <!-- Dynamic ID based on table data -->
                                    <td>${category.name}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm editBtn" data-id="${category.id}">Edit</button>
                                        <button class="btn btn-danger btn-sm deleteBtn" data-id="${category.id}">Delete</button>
                                    </td>
                                </tr>
                            `;
                        });
                        $('#categoryTableBody').html(rows);
                    }
                }
            });
        }



      
        $(document).on('click', '.editBtn', function() {
            let id = $(this).data('id');
            $.ajax({
                url: `/categories/${id}`,
                method: 'GET',
                success: function(category) {
                    $('#categoryId').val(category.id);
                    $('#categoryName').val(category.name);
                    $('#categoryModalLabel').text('Edit Category');
                    $('#categoryModal').modal('show');
                }
            });
        });

       
        $(document).on('click', '.deleteBtn', function() {
            let id = $(this).data('id');
            if (confirm('Are you sure you want to delete this category?')) {
                $.ajax({
                    url: `/categories/${id}`,
                    method: 'DELETE',
                    success: function() {
                        fetchCategories();
                    }
                });
            }
        });
    });
</script>
@endpush
