<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Models\Chapter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ChapterResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ChapterController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     *
     */////////DONE
    public function index($story_id)
    {
        $chapters = Chapter:: 
        where('story_id', $story_id) 
        ->get();

    return ChapterResource::collection($chapters);
    }

    /**
     * Store a newly created resource in storage.
     */
  
    public function store(Request $request)
    {
        try{
       
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'payment_status' => 'required|in:paid,notpaid',
            'image' =>'nullable|file|image|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
    'part_number' => [
    'required',
    'integer',
    'min:1',
    Rule::unique('chapters', 'part_number')->where(function ($query) {
        return $query->where('story_id', request('story_id'));
    })
],


            'story_id' => 'required|exists:stories,id',
        ], [
            'title.required' => 'The title is required.',
            'title.string' => 'The title must be a string.',
            'title.max' => 'The title may not be greater than 255 characters.',
            
            'content.required' => 'Content is required.',
            'content.string' => 'Content must be a string.',
            
            'payment_status.required' => 'Payment status is required.',
            'payment_status.in' => 'The payment status must be either "paid" or "notpaid".',
            
            'image.image' => 'The image must be a valid image file.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif,webp,avif.',
            'image.max' => 'The image size must not exceed 2MB.',
            
            'part_number.required' => 'Part number is required.',
            'part_number.unique' => 'Part number is unique.',
            'part_number.integer' => 'Part number must be an integer.',
            'part_number.min' => 'Part number must be at least 1.',
            
            'story_id.required' => 'Story ID is required.',
            'story_id.exists' => 'The selected story does not exist.',
        ]);
        
   
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $image_path = '';
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('chapters', 'stories');
            $image_path = asset('uploads/stories/' . $path);
        }
     
        $chapter = new Chapter();
        $chapter->title = $request->title;
        $chapter->content = $request->content;
        $chapter->payment_status = $request->payment_status;
        $chapter->part_number = $request->part_number;
        $chapter->story_id = $request->story_id;
        $chapter->image = $image_path;
        $chapter->save();

        return response()->json([
            'message' => 'Chapter added successfully',
            'chapter' => $chapter,
        ], 201);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
    }
    
    

    /**
     * Display the specified resource.
     */
 
    public function show($story_id, $chapter_id)
    {
        $chapter = Chapter::where('id', $chapter_id)
                          ->where('story_id', $story_id)
                          ->first();
    
        if (!$chapter) {
      return response()->json(['error' => 'Chapter not found for the specified story'], 404);
  }
    
        return new ChapterResource($chapter);
    }
    

    /**
     * Update the specified resource in storage.
     */
 
    public function update(Request $request,$id)
    {
     
        $validator=Validator::make($request->all(),[
               'title' => 'required|string|max:255',
               'content' => 'required|string',
               'payment_status' => 'required|in:paid,notpaid',
               'image' =>'nullable|file|image|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
               'part_number' => 'required|integer|min:1',
               'story_id' => 'required|exists:stories,id',
        ]);
        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $chapter=Chapter::find($id);
        if(! $chapter){
            return response()->json(['error' => 'notfound'], 404);

        }
        $my_path = $chapter->image ;
        if(request()->hasFile("image")){

            $url = $chapter->image;

            $relativePath = str_replace(url('uploads/chapters/').'/' , '', $url);
            
            if (Storage::disk('stories')->exists($relativePath)) {
                Storage::disk('stories')->delete($relativePath);
            }

            $image = request()->file("image");
            $my_path=$image->store('chapters','stories');
            $my_path= asset('uploads/stories/' . $my_path); 
        }

        $chapter->title=$request->title;
        $chapter->content=$request->content;
        $chapter->payment_status=$request->payment_status;
        $chapter->image = $my_path;
        $chapter->part_number=$request->part_number;
        $chapter->story_id=$request->story_id;
    $chapter->save();
    
    return response()->json(['message' => 'chapter updated successfully', 'chapter' => $chapter], 200);
        }
    

    /**
     * Remove the specified resource from storage.
     */
 
   
     public function destroy($story_id, $chapter_id)
     {
         $validator = Validator::make(['story_id' => $story_id], [
             'story_id' => 'required|exists:stories,id',
         ]);
     
         if ($validator->fails()) {
             return response()->json(['errors' => $validator->errors()], 422);
         }
     
      
         $chapter = Chapter::where('id', $chapter_id)
                           ->where('story_id', $story_id)
                           ->first();
     
         
         if (!$chapter) {
             return response()->json(['error' => 'Chapter not found for the specified story'], 404);
         }
     
         
         try {
             $chapter->delete();
             return response()->json(['message' => 'Chapter deleted successfully'], 200);
         } catch (\Exception $e) {
             return response()->json(['error' => $e->getMessage()], 500);
         }
     }
     
}