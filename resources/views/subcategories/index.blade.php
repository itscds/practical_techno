@extends('layouts.app')

@section('title', 'Subcategories')

@section('header', 'Subcategory Management')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Subcategories</h2>
            <button class="btn btn-primary" id="addSubcategoryBtn">Add Subcategory</button>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th> <!-- New category column -->
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="subcategoryTableBody">
                    <!-- Subcategories will be dynamically populated here -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Subcategory Modal -->
<div class="modal fade" id="subcategoryModal" tabindex="-1" aria-labelledby="subcategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="subcategoryForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="subcategoryModalLabel">Add Subcategory</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="subcategoryId">
                    <div class="form-group">
                        <label for="subcategoryName">Name</label>
                        <input type="text" class="form-control" id="subcategoryName" required>
                    </div>
                    <div class="form-group">
                        <label for="subcategoryCategory">Category</label>
                        <select class="form-control" id="subcategoryCategory" required>
                            <option value="">Select a Category</option>
                            <!-- Categories will be dynamically populated here -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveSubcategoryBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
   $(document).ready(function() {
   
    fetchSubcategories();

    function populateCategories() {
        $.ajax({
            url: '/fetch-categories',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    let options = '<option value="">Select a Category</option>';
                    response.categories.forEach(category => {
                        options += `<option value="${category.id}">${category.name}</option>`;
                    });
                    $('#subcategoryCategory').html(options);
                }
            }
        });
    }

   
    $('#addSubcategoryBtn').click(function() {
        populateCategories();
        $('#subcategoryForm')[0].reset();
        $('#subcategoryId').val('');
        $('#subcategoryModalLabel').text('Add Subcategory');
        $('#subcategoryModal').modal('show');
    });

    
    $('#subcategoryForm').submit(function(e) {
        e.preventDefault();
        let id = $('#subcategoryId').val();
        let url = id ? `/subcategories/${id}` : `/subcategories`;
        let method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: {
                name: $('#subcategoryName').val(),
                category_id: $('#subcategoryCategory').val()  
            },
            success: function(response) {
                $('#subcategoryModal').modal('hide');
                fetchSubcategories();
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    });

    
    function fetchSubcategories() {
        $.ajax({
            url: '/fetch-subcategories',  
            method: 'GET',
            success: function(subcategories) {
                let rows = '';
                subcategories.forEach(subcategory => {
                    rows += `
                        <tr>
                            <td>${subcategory.id}</td>
                            <td>${subcategory.name}</td>
                            <td>${subcategory.category_name}</td>  <!-- Display category name -->
                            <td>
                                <button class="btn btn-warning btn-sm editBtn" data-id="${subcategory.id}">Edit</button>
                                <button class="btn btn-danger btn-sm deleteBtn" data-id="${subcategory.id}">Delete</button>
                            </td>
                        </tr>
                    `;
                });
                $('#subcategoryTableBody').html(rows);
            }
        });
    }

    
    $(document).on('click', '.editBtn', function() {
        let id = $(this).data('id');
        $.ajax({
            url: `/subcategories/${id}`,
            method: 'GET',
            success: function(subcategory) {
                $('#subcategoryId').val(subcategory.id);
                $('#subcategoryName').val(subcategory.name);
                $('#subcategoryCategory').val(subcategory.category_id);  
                $('#subcategoryModalLabel').text('Edit Subcategory');
                $('#subcategoryModal').modal('show');
            }
        });
    });

   
    $(document).on('click', '.deleteBtn', function() {
        let id = $(this).data('id');
        if (confirm('Are you sure you want to delete this subcategory?')) {
            $.ajax({
                url: `/subcategories/${id}`,
                method: 'DELETE',
                success: function() {
                    fetchSubcategories();
                }
            });
        }
    });
});

</script>
@endpush
