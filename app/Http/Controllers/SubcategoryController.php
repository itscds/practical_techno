<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
   
    public function index()
    {
       
        return view('subcategories.index');
    }

  
    public function fetchSubcategories()
    {
        $subcategories = Subcategory::with('category')->get(); 
        $subcategories->map(function($subcategory) {
            $subcategory->category_name = $subcategory->category ? $subcategory->category->name : 'No category';
            return $subcategory;
        });

        return response()->json($subcategories);  
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id', 
        ]);

        Subcategory::create([
            'name' => $request->name,
            'category_id' => $request->category_id,  
        ]);

        return response()->json(['success' => true, 'message' => 'Subcategory added successfully.']);
    }

   
    public function show($id)
    {
        $subcategory = Subcategory::find($id);
        return response()->json($subcategory);
    }

    
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        $subcategory = Subcategory::findOrFail($id);
        $subcategory->update([
            'name' => $request->name,
            'category_id' => $request->category_id, 
        ]);

        return response()->json(['success' => true, 'message' => 'Subcategory updated successfully.']);
    }

   
    public function destroy($id)
    {
        Subcategory::destroy($id);
        return response()->json(['success' => true, 'message' => 'Subcategory deleted successfully.']);
    }

   
    public function fetchCategories()
    {
        $categories = Category::all();
        return response()->json(['success' => true, 'categories' => $categories]);
    }
}
