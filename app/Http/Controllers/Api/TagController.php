<?php

namespace App\Http\Controllers\Api;
use App\Models\Story;
use App\Models\Category;
use App\Models\Tag;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   

public function getTagsByCategory($categoryId)
{
  
    $tags = Tag::whereHas('stories', function($query) use ($categoryId) {
        $query->where('category_id', $categoryId);
    })->get();
    if ($tags->isEmpty()) {
        return response()->json(['error' => 'No tags found for this category'], 404);
    }

    return response()->json(['tags' => $tags]);
}


    /**
     * Store a newly created resource in storage.
     */

//  public function getStoriesByTags(Request $request)
//  {
 
//      $request->validate([
//          'tag_id' => 'required|array',
//          'tag_id.*' => 'exists:tags,id'
//      ]);
 
    
//      $stories = Story::whereHas('tags', function($query) use ($request) {
//          $query->whereIn('tags.id', $request->tag_id);
//      })->get();
 
//      return response()->json(['stories' => $stories]);
//  }

  
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:tags,name',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
    
            $tag = new Tag();
            $tag->name = $request->name;
            $tag->save();
            return response()->json([
                'message' => 'Tag created successfully',
                'tag' => $tag
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
///////////DONE
    public function storeStoryTags(Request $request, $story_id)
    {
        $validator = Validator::make($request->all(), [
            'tag_id' => 'required|array',
            'tag_id.*' => 'exists:tags,id', 
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
    
        $story = Story::find($story_id);
        if (!$story) {
            return response()->json(['error' => 'Story not found'], 404);
        }
    
        
        $story->tags()->syncWithoutDetaching($request->tag_id);
    
        return response()->json(['message' => 'Tags added successfully'], 200);
    }
    
    
    /**
     * Display the specified resource.
     */
    public function show(Tag $tag)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

     public function update(Request $request, $tagId) {
        $tag = Tag::findOrFail($tagId);
        $tag->name = $request->name;
        $tag->save();
        return response()->json(['status' => 'success', 'message' => 'Tag updated successfully']);
    }
   
    public function updateStoryTags(Request $request, Story $story)
    {
        $validator = Validator::make($request->all(), [
            'old_tag_id' => 'required|array',
            'new_tag_id' => 'required|array',
            'old_tag_id.*' => 'required|integer|exists:tags,id',
            'new_tag_id.*' => 'required|integer|exists:tags,id',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
    
        if (count($request->old_tag_id) !== count($request->new_tag_id)) {
            return response()->json(['error' => 'old_tag_id and new_tag_id counts must match'], 400);
        }
    
        foreach ($request->old_tag_id as $index => $oldTagId) {
            $newTagId = $request->new_tag_id[$index];
    
            
            $story->tags()->detach($oldTagId);
    
           
            $story->tags()->attach($newTagId);
        }
    
        return response()->json(['message' => 'Tags updated successfully'], 200);
    }
    
    /**
     * Remove the specified resource from storage.
     */
  
    public function removeTagsFromStory($storyId, Request $request)
    {
        $story = Story::find($storyId);
        if (!$story) {
            return response()->json(['error' => 'Story not found'], 404);
        }
    
        $validator = Validator::make($request->all(), [
            'tag_id' => 'required|array',
            'tag_id.*' => 'exists:tags,id',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        try {
            $story->tags()->detach($request->tag_id);

            return response()->json(['message' => 'Tags successfully removed from the story'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while removing tags.'], 500);
        }
    }

    public function destroy($tagId)
{
    $tag = Tag::find($tagId);

    if (!$tag) {
        return response()->json(['error' => 'Tag not found'], 404);
    }

    try {
        $tag->delete();

        return response()->json(['message' => 'Tag deleted successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'An error occurred while deleting the tag.'], 500);
    }
    
}}