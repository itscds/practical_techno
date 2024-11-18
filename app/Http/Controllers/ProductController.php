<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\ProductSize;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

      
        public function index()
        {
           
            $products = Product::with(['category', 'subcategory', 'images'])->paginate(10); 
            return view('product.index', compact('products'));
        }

        
        public function create()
        {
            $categories = Category::all();
            return view('product.create', compact('categories'));
        }

       
        public function store(Request $request)
        {
            $request->validate([
                'category_id' => 'required|exists:categories,id',
                'subcategory_id' => 'required|exists:subcategories,id',
                'name' => 'required|string|max:255',
                'regular_price' => 'required|numeric|min:0',
                'sizes.*.size' => 'required|string',
                'sizes.*.price' => 'required|numeric|min:0',
                'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            DB::beginTransaction();
            try {
                
                $product = Product::create($request->only('category_id', 'subcategory_id', 'name', 'regular_price'));

               
                if ($request->sizes) {
                    foreach ($request->sizes as $size) {
                        $product->sizes()->create($size);
                    }
                }

               
                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $index => $image) {
                        $path = $image->store('product_images', 'public');
                        $product->images()->create([
                            'path' => $path,
                            'is_thumbnail' => $index === 0, 
                        ]);
                    }
                }

                DB::commit();
                return response()->json(['message' => 'Product created successfully!'], 201);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
            }
        }

       
        public function edit(Product $product)
        {
            $categories = Category::all();
            $subcategories = Subcategory::where('category_id', $product->category_id)->get();

            return view('product.edit', compact('product', 'categories', 'subcategories'));
        }

       
        public function update(Request $request, Product $product)
        {
            $request->validate([
                'category_id' => 'required|exists:categories,id',
                'subcategory_id' => 'required|exists:subcategories,id',
                'name' => 'required|string|max:255',
                'regular_price' => 'required|numeric|min:0',
                'sizes.*.size' => 'required|string',
                'sizes.*.price' => 'required|numeric|min:0',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            DB::beginTransaction();
            try {
               
                $product->update($request->only('category_id', 'subcategory_id', 'name', 'regular_price'));

                
                $product->sizes()->delete();
                if ($request->sizes) {
                    foreach ($request->sizes as $size) {
                        $product->sizes()->create($size);
                    }
                }

               
                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $index => $image) {
                        $path = $image->store('product_images', 'public');
                        $product->images()->create([
                            'path' => $path,
                            'is_thumbnail' => $index === 0,
                        ]);
                    }
                }

                DB::commit();
                return response()->json(['message' => 'Product updated successfully!'], 200);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
            }
        }

        
        public function deleteImage($imageId)
        {
            try {
                
                $image = ProductImage::findOrFail($imageId);
                                
                Storage::disk('public')->delete($image->path);
                $image->delete();

                return response()->json(['message' => 'Image deleted successfully']);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Error deleting image: ' . $e->getMessage()], 500);
            }
        }

        public function deleteSize($sizeId)
        {
            try {
            
                $size = ProductSize::findOrFail($sizeId);
                $size->delete();

                return response()->json(['message' => 'Size removed successfully']);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Error removing size: ' . $e->getMessage()], 500);
            }
        }
}
