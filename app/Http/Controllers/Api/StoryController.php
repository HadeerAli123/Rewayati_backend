<?php

namespace App\Http\Controllers\Api;

use App\Models\Story;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\StoryResource;
use App\Models\Category;
use App\Models\Reviews;
use App\Models\ReadLater;
use App\Models\Reading;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getAllStories(){
        $stories=Story::with('category','tags')->select('id', 'title', 'cover_image', 'description', 'status', 'content_type', 'created_at')
        ->orderBy('created_at', 'desc')->get();

        if ($stories->isEmpty()) {
            return response()->json(['message' => 'No stories found.'], 404);
        }
    
  
        return response()->json($stories, 200);
    }
    
    ////////////////////////////////////////////////////////////////////////////
  
    public function getStoriesByCategory(Category $category)/// categeory not found
{
    $stories = Story::where('category_id', $category->id)
        ->with(['category','tags:id,name'])
        ->leftJoin('reviews', 'stories.id', '=', 'reviews.story_id')
        ->leftJoin('readings', 'stories.id', '=', 'readings.story_id')
        ->leftJoin('chapters', 'stories.id', '=', 'chapters.story_id')  
        ->select(
            'stories.id',
            'stories.title',
            'stories.description',
            'stories.cover_image',
            'stories.status',
            'stories.content_type',
            'stories.category_id', 
            DB::raw('COUNT(DISTINCT CASE WHEN reviews.has_voted = 1 THEN reviews.id END) as votes_count'),
            DB::raw('COUNT(DISTINCT readings.id) as readers_count'),  
            DB::raw('COUNT(DISTINCT chapters.part_number) as chapters_count')  
        )
        ->groupBy(
            'stories.id',
            'stories.title',
            'stories.description',
            'stories.cover_image',
            'stories.status',
            'stories.content_type',
            'stories.category_id'
        )

        ->get();
    
    if ($stories->isEmpty()) {
        return response()->json(['message' => 'No stories found in this category.'], 404);
    }
    
    return response()->json($stories, 200);
}

/////////////////////////////////////////////////////////////////////////////////
public function getTopViewedStoriesWithTags($categoryId)
{

    $category = Category::find($categoryId);

    if (!$category) {
        return response()->json(['message' => 'Please select a valid category.'], 404);
    }

    $stories = Story::where('category_id', $categoryId)
        ->with(['tags'=>function($query){
            $query->select('tags.id','tags.name');
        }])
        ->leftJoin('readings', 'stories.id', '=', 'readings.story_id') 
        ->select(
            'stories.id', 
            'stories.cover_image',
            DB::raw('COUNT(readings.id) as views') 
        )
        ->groupBy('stories.id', 'stories.cover_image') 
        ->orderByDesc('views') 
        ->limit(10)
        ->get();

 
    if ($stories->isEmpty()) {
        return response()->json(['message' => 'No stories found in this category.'], 404);
    }

    return response()->json($stories, 200);
}


///////////////////////////////////////////////////////////////////////////
public function getStoriesByTag($tagId)
{
    $tag = Tag::find($tagId);

    if (!$tag) {
        return response()->json(['message' => 'Please select a valid tag.'], 400);
    }

    $stories = Story::whereHas('tags', function ($query) use ($tagId) {
        $query->where('tags.id', $tagId); 
    })
        ->with(['tags' => function ($query) {
            $query->select('tags.id', 'tags.name');
        }])
        ->select(
            'id', 
            'title', 
            'description', 
            'content_type', 
            'cover_image', 
            'status' 
        )
        ->get();

    if ($stories->isEmpty()) {
        return response()->json(['message' => 'No stories found for this tag.'], 404);
    }

    return response()->json($stories, 200);
}

////////////////////////////////////////////////////////////////////

    public function storiesinReadLater()
    {

        $readlaterstories = ReadLater::where('user_id',Auth::id())->get()->pluck('story_id');
      
        $stories = Story::with(['category' ])
        ->whereIn('id', $readlaterstories)
        ->get();
        return StoryResource::collection($stories);
    }
////////////////////////////////////////////////////////////////////

   

     public function getCompletedStoriesByCategory(Category $category)//// categoery not found
     {
         $completedStories = Story::where('category_id', $category->id) 
             ->where('status', 'completed')
             ->with([
                 'tags' => function ($query) {
                     $query->select('tags.id', 'tags.name');
                 }
             ])
             ->select('id', 'title', 'cover_image') 
             ->orderBy('updated_at', 'desc')
             ->get();
     
         if ($completedStories->isEmpty()) {
             return response()->json(['message' => 'No completed stories found in this category.'], 404);
         }
     
         return response()->json($completedStories, 200);
     }
     
///////////////////////////////////////////////////////////////////////////

public function getStoriesWithNotPaidChapters($category_id)
{
    $stories = Story::where('category_id', $category_id) 
        ->whereHas('chapters', function ($query) {
            $query->where('payment_status', 'NotPaid');
        })
        ->with([
            'tags' => function ($query) { 
                $query->select('tags.id', 'tags.name');
            }
        ])
        ->select('id', 'title', 'cover_image') 
        ->get();

    if ($stories->isEmpty()) {
        return response()->json(['message' => 'No stories with unpaid chapters found in this category.'], 404);
    }

    return response()->json($stories, 200);
}
//////////////////////////////////////////////////////////////////////////

public function getadvertisementStoryByLatestStory(){
    $stories=Story::orderBy('created_at','desc')->take(10)
    ->select('id','title', 'advertisement_image','created_at')
    ->get();

    if ($stories->isEmpty()) {
        return response()->json(['message' => 'No new stories found.'], 404);
    }


    return response()->json($stories, 200);
}
//////////////////////////////////////////////////////////////////

public function storyDetails($id)
{
   
    $story = Story::with(['category', 'tags', 'MainCharacters'])
        ->leftJoin('reviews', 'stories.id', '=', 'reviews.story_id')
        ->leftJoin('readings', 'stories.id', '=', 'readings.story_id')
        ->leftJoin('chapters', 'stories.id', '=', 'chapters.story_id')
        ->select(
            'stories.id',
            'stories.title',
            'stories.description',
            'stories.cover_image',
            'stories.status',
            'stories.content_type',
            'stories.category_id',
            DB::raw('COUNT(DISTINCT CASE WHEN reviews.has_voted = 1 THEN reviews.id END) as votes_count'),
            DB::raw('COUNT(DISTINCT readings.id) as readers_count'),
            DB::raw('COUNT(DISTINCT reviews.feedback) as comments_count'),
            DB::raw('COUNT(DISTINCT chapters.part_number) as chapters_count')
        )
        ->groupBy(
            'stories.id',
            'stories.title',
            'stories.description',
            'stories.cover_image',
            'stories.status',
            'stories.content_type',
            'stories.category_id'
        )
        ->where('stories.id', $id) 
        ->first();

    
    if (!$story) {
        return response()->json(['message' => 'Story not found.'], 404);
    }

    return response()->json($story, 200);
}

////////////////////////////////////////////////////////

public function getMyStory()
{
    $stories = Auth::user()->stories()->with('category')->get();
   
    return StoryResource::collection($stories);
}


    



    //////////////////////////////////////////////////////////////////////
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                
    'title' => 'required|string|max:255',
    'description' => 'required|string|max:1000',
    'language' => 'required|string|max:255',
    'cover_image' => 'required|file|image|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
    'maincharacters' => 'required|array|min:1',
    'maincharacters.*' => 'string|max:255',
    'copyright' => 'required|string|max:255',
    'advertisement_image' => 'required|file|image|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
    'content_type' => 'required|string|in:mature,general',
    'status' => 'required|string|in:ongoing,completed',
    'category_id' => 'required|integer|exists:categories,id',
], [
    // Message for 'title' field
    'title.required' => 'The title is required.',
    'title.string' => 'The title must be a string.',
    'title.max' => 'The title must not exceed 255 characters.',
    
    // Message for 'description' field
    'description.required' => 'The description is required.',
    'description.string' => 'The description must be a string.',
    'description.max' => 'The description must not exceed 1000 characters.',
    
    // Message for 'language' field
    'language.required' => 'The language is required.',
    'language.string' => 'The language must be a string.',
    'language.max' => 'The language must not exceed 255 characters.',
    
    // Message for 'cover_image' field
    'cover_image.required' => 'A cover image is required.',
    'cover_image.file' => 'The cover image must be a file.',
    'cover_image.image' => 'The cover image must be an image.',
    'cover_image.mimes' => 'The cover image must be a jpeg, png, jpg, gif, webp, or avif image.',
    'cover_image.max' => 'The cover image must not exceed 2 MB.',
    
    // Message for 'maincharacters' field
    'maincharacters.required' => 'The main characters are required.',
    'maincharacters.array' => 'The main characters must be an array.',
    'maincharacters.min' => 'The main characters must contain at least one character.',
    'maincharacters.*.string' => 'Each character must be a string.',
    'maincharacters.*.max' => 'Each character name must not exceed 255 characters.',
    
    // Message for 'copyright' field
    'copyright.required' => 'The copyright information is required.',
    'copyright.string' => 'The copyright must be a string.',
    'copyright.max' => 'The copyright must not exceed 255 characters.',
    
    // Message for 'advertisement_image' field
    'advertisement_image.required' => 'An advertisement image is required.',
    'advertisement_image.file' => 'The advertisement image must be a file.',
    'advertisement_image.image' => 'The advertisement image must be an image.',
    'advertisement_image.mimes' => 'The advertisement image must be a jpeg, png, jpg, gif, webp, or avif image.',
    'advertisement_image.max' => 'The advertisement image must not exceed 2 MB.',
    
    // Message for 'content_type' field
    'content_type.required' => 'The content type is required.',
    'content_type.in' => 'The content type must be either "mature" or "general".',
    
    // Message for 'status' field
    'status.required' => 'The status is required.',
    'status.in' => 'The status must be either "ongoing" or "completed".',
    
    // Message for 'category_id' field
    'category_id.required' => 'The category is required.',
    'category_id.integer' => 'The category must be an integer.',
    'category_id.exists' => 'The selected category does not exist.',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()],400);
            }
    
            $data = $validator->validated();

            $category = Category::find($data['category_id']);

            if (!$category) {
                return response()->json(['error' => 'Invalid category ID'], 400);
            }
    
          
            $image_path ='';
            $advertisement_image_path = '';
            if ($request->hasFile('cover_image')) {

                
                    $path = $data['cover_image'] ->store('cover_images', 'stories');
                    $path= asset('uploads/stories/' . $path); 
                    $image_path = $path;
               
            }

            if ($request->hasFile('advertisement_image')) {
                $ad_path = $data['advertisement_image']->store('advertisement_images', 'stories');
                $ad_path = asset('uploads/stories/' . $ad_path);
                $advertisement_image_path = $ad_path;
            }
            
           
        $story = new Story();
        
        $story->title = $data['title'];
        $story->language = $data['language'];
        $story->description = $data['description'];
        $story->copyright = $data['copyright'];
        $story->cover_image = $image_path;
        $story->advertisement_image = $advertisement_image_path;
        $story->status = $data['status'];
        $story->publication_status = 'draft';
        $story->content_type = $data['content_type'];
        $story->category_id = $data['category_id'];
        $story->user_id = Auth::id();

        $story->save();

        foreach ($data['maincharacters'] as $character) {
            $story->mainCharacters()->create(['name' => $character]);
        }
        $story->load('mainCharacters');

        return response()->json(['message' => 'Story saved as draft', 'story' => $story], 201);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
        
    

public function publishStory($storyId)
{
 
    $story = Auth::user()->stories()->find($storyId); 

    if (!$story) {
        return response()->json(['message' => 'Story not found or not owned by the user'], 404);
    }

    if ($story->isDraft()) {
        $story->publish(); 
        return response()->json(['message' => 'Story published successfully'], 200);
    }

    return response()->json(['message' => 'Story is already published'], 400);
}


  

///////////////////////////////////////////////////////////
//////////DONE
public function getPublishedStories()
{
    
    $stories = Auth::user()->stories()->where('publication_status', 'published')->get();
    return response()->json(StoryResource::collection($stories), 200);
}


    /**
     * Display the specified resource.
     */

    ///////////////////////DONE
    public function show(Story $story)//
    {
        return new StoryResource($story->load('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    ////////////DONE
    public function updateStory(Request $request)//
    {
        try {
        
    
            $validator = Validator::make($request->all(), [
        'id' => 'required|exists:stories,id',
                'title' => 'string|max:255',
                'description' => 'string|max:1000',
                'language' => 'string|max:255',
                'cover_image' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
                'maincharacters' => 'array|min:1',
                'maincharacters.*' => 'string|max:255',
                'copyright' => 'string|max:255',
                'advertisement_image' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
                'content_type' => 'string|in:mature,general',
                'status' => 'string|in:ongoing,completed',
                'category_id' => 'integer|exists:categories,id',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }
    
            $data = $validator->validated();
            $story = Auth::user()->stories()->withTrashed()->find($data['id']);
            if (!$story) {
                return response()->json(['message' => 'Story not found or not owned by the user'], 404);
            }
            $category = Category::find($data['category_id']);

            if (!$category) {
                return response()->json(['error' => 'Invalid category ID'], 404);
            }
           
            if ($request->hasFile('cover_image')) {
            
                if ($story->cover_image) {
                    $oldPath = str_replace(asset('uploads/stories/'), '', $story->cover_image);
                    if (Storage::disk('stories')->exists($oldPath)) {
                        Storage::disk('stories')->delete($oldPath);
                    }
                }
              
                $path = $request->file('cover_image')->store('cover_images', 'stories');
                $story->cover_image = asset('uploads/stories/' . $path);
            }
            
            if ($request->hasFile('advertisement_image')) {
              
                if ($story->advertisement_image) {
                    $oldAdPath = str_replace(asset('uploads/stories/'), '', $story->advertisement_image);
                    if (Storage::disk('stories')->exists($oldAdPath)) {
                        Storage::disk('stories')->delete($oldAdPath);
                    }
                }
               
                $adPath = $request->file('advertisement_image')->store('advertisement_images', 'stories');
                $story->advertisement_image = asset('uploads/stories/' . $adPath);
            }
          
            
    
            $story->title = $data['title'] ?? $story->title;
            $story->description = $data['description'] ?? $story->description;
            $story->language = $data['language'] ?? $story->language;
            $story->copyright = $data['copyright'] ?? $story->copyright;
            $story->content_type = $data['content_type'] ?? $story->content_type;
            $story->status = $data['status'] ?? $story->status;
            $story->category_id = $data['category_id'] ?? $story->category_id;
    
            $story->save();
    
        
            if (isset($data['maincharacters'])) {
                $story->mainCharacters()->delete(); // حذف الشخصيات القديمة
                foreach ($data['maincharacters'] as $character) {
                    $story->mainCharacters()->create(['name' => $character]);
                }
            }
    
            return response()->json(['message' => 'Story updated successfully', 'story' => $story], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    
    

    /**
     * Remove the specified resource from storage.
     */



    public function restore($story_id)//
         {
             $story = Auth:: user()->stories()->withTrashed()->find($story_id);
             if (!$story) {
                return response()->json(['message' => 'Story not found or not owned by the user'], 404);
            }
             $story->restore();
             return response()->json(['message' => 'done suuccessfully.'], 200);
         }
         
    


   
public function destroy($storyId)
{
    $story = Auth::user()->stories()->find($storyId);

    if (!$story) {
        return response()->json(['message' => 'Story not found or not owned by the user'], 404);
    }

    $story->delete();

    return response()->json(['message' => 'Story soft deleted successfully.']);
}
/////////////////////////////////////////////////////////////
public function forceDestroy($id)
{
   

    $userStory = Auth::user()->stories()->withTrashed()->find($id);
    if (!$userStory) {
        return response()->json(['message' => 'Story not found or not owned by the user'], 404);
    }

    $userStory->forceDelete();

    return response()->json(['message' => 'Story hard deleted successfully.']);
}


     public function getAlldeleted()////
     {
   $stories = Story::onlyTrashed()->with(['category'])->get();    
   return StoryResource::collection($stories);
     }
 }