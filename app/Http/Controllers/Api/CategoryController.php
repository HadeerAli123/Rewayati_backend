<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Models\Tag;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\CategoryResource;
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $categories=Category::all();
        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
 
    public function store(Request $request)
    {
        try{
            if (auth()->user()->role!=='admin'){
                return response()->json(['error' => 'Admin only can store categories'], 403);
            }
           
        $validator = Validator::make($request->all(),[
                'category_name' => 'required|string|max:255|unique:categories,category_name',

            ],[
                'category_name.required' => 'Category name is required.',
                'category_name.string' => 'Category name must be a string.',
                'category_name.max' => 'Category name may not be greater than 255 characters.',
                'category_name.unique' => 'Category name must be unique.',
            ]); 
            if($validator->fails()){
                return response()->json(['errors' => $validator->errors()], 422);

            }
           $category=new Category();
           $category->category_name=$request->category_name;
         
           
           $category->save();
           return response()->json(['message'=>'Category created successfully','category'=>$category],200);
    }
    catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
    }
    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
    return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     */
    
     public function update(Request $request, $id)
     {
        if(auth()->user()->role !=='admin'){
         return response()->json(['error' => 'Admin only can  update  categories'], 403);

        }
         $validator = Validator::make($request->all(), [
             'category_name' => 'required|string|max:255|unique:categories,category_name,' . $id,
         ]);
     
         if ($validator->fails()) {
             return response()->json(['errors' => $validator->errors()], 422);
         }
     
         $category = Category::find($id);
         if (!$category) {
             return response()->json(['error' => 'Category not found'], 404);
         }
     
         $category->category_name = $request->category_name;
         $category->save();
     
         return response()->json(['message' => 'Category updated successfully', 'category' => $category], 200);
     }
     



    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Category $category)
    {
        if(auth()->user()->role !=='admin'){
            return response()->json(['error' => 'Admin only can  delete   category'], 403);
   
           }
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
    
}
