<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Auth;
use App\Models\Review;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ReviewResource;
use Illuminate\Support\Facades\Validator;
use App\Notifications\BlockedFeedbackNotification;
class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */

   
    public function getAllReviews($story_id)
    {
        $reviews = Review::with(['story', 'user'])
            ->where('story_id', $story_id)
            ->get();
    
        return ReviewResource::collection($reviews);
    }
        

    /**
     * Store a newly created resource in storage.
     */
   
//     public function store(Request $request)
//     {
//         try {

//             $validator = Validator::make($request->all(), [
//                 'story_id' => 'required|integer|exists:stories,id',
//                 'feedback' => 'nullable|string|max:255',
//                 'has_voted' => 'required|boolean',

//             ]);
            
//             if ($validator->fails()) {
//                 return response()->json(['errors' => $validator->errors()], 400);
//             }
//             $existingReview = Review::where('story_id', $request->input('story_id'))
//             ->where('user_id', Auth::id())
//             ->first();

//         if ($existingReview) {
          
//             if ($request->has('feedback')) {
//                 $existingReview->feedback = $request->input('feedback');
//             }

//             if ($request->has('has_voted')) {
//                 $existingReview->has_voted = $request->input('has_voted');
//             }

//             $existingReview->save();

//             return response()->json([
//                 'message' => 'Review updated successfully'
//             ], 200);
// } else {


//             $item=new Review();

//             $item->story_id = $request->input('story_id');
//             $item->has_voted = $request->input('has_voted');
//             $item->feedback = $request->input('feedback');

//             $item->user_id = Auth::id();
//             $item->save();
    
//             return response()->json(['message' => 'review added successfully'], 201);
// }
//         } catch (\Exception $e) {
//             return response()->json(['error' => $e->getMessage()], 500);
//         }
    
  //  }
    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */

    //  //////SURE /////////SmallEdit ////Delete feedback///////////////////////////////////////////
    // public function destroy($story_id, $review_id, Request $request)
    // {
       
    //     $validator = Validator::make(['story_id' => $story_id], [
    //         'story_id' => 'required|exists:stories,id',
    //     ]);
    
    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }
    
    
    //     $review = Review::where('id', $review_id)
    //                     ->where('story_id', $story_id)
    //                     ->first();
    
    
    //     if (!$review) {
    //         return response()->json(['error' => 'Review not found for the specified story'], 404);
    //     }
    
      
    //     if ($review->user_id != auth()->id()) {
    //         return response()->json(['error' => 'You can only delete your own review'], 403); // 403 Forbidden
    //     }
    
       
    //     try {
    //         $review->delete();
    //         \Log::info('Review deleted successfully', ['review_id' => $review_id]); // سجل في الـ log
    //         return response()->json(['message' => 'Review deleted successfully'], 200);
    //     } catch (\Exception $e) {
    //         \Log::error('Error deleting review', ['error' => $e->getMessage()]);
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }
    public function toggleVote($storyId)
    {

        $review = Review::firstOrNew([
            'story_id' => $storyId,
            'user_id' => auth()->id(),
        ]);
    
   
        $review->has_voted = !$review->has_voted;
    
        $review->save();
    
        return response()->json([
            'message' => $review->has_voted ? 'Vote added successfully' : 'Vote removed successfully',
            'has_voted' => $review->has_voted,
        ], 200);
    }
    
    
    public function addFeedback(Request $request, $storyId)
    {
        
        $review = Review::where('story_id', $storyId)
                        ->where('user_id', auth()->id())
                        ->first();
    
        if ($review && $review->feedback) {
            return response()->json(['error' => 'You already added feedback. Editing is not allowed'], 400);
        }
  
        $review = Review::firstOrNew([
            'story_id' => $storyId,
            'user_id' => auth()->id(),
        ]);
    
        $review->feedback = $request->feedback;
        $review->save();
    
        return response()->json(['message' => 'Feedback added successfully', 'feedback' => $review->feedback], 200);
    }
    
    public function deleteFeedback($storyId)
    {
      
        $review = Review::where('story_id', $storyId)
                        ->where('user_id', auth()->id())
                        ->first();
    
        if (!$review || !$review->feedback) {
            return response()->json(['error' => 'No feedback found to delete'], 404);
        }
    
      
        $review->feedback = null;
        $review->save();
    
        return response()->json(['message' => 'Feedback deleted successfully'], 200);
    }
    
//     ///////////////////////تاكيد
//     public function deleteReview($storyId)
// {
//     $review = Review::where('story_id', $storyId)
//                     ->where('user_id', auth()->id())
//                     ->first();

//     if (!$review) {
//         return response()->json(['error' => 'No review found to delete'], 404);
//     }

    
//     $review->has_voted = false; 
//     $review->feedback = null;  

//     $review->save();

//     return response()->json(['message' => 'Vote and feedback deleted successfully'], 200);
// }
public function blockFeedback($reviewId){
    $review = Review::findOrFail($reviewId);

   
    $user = $review->user; 
    $review->feedback_status = 'blocked';
    $review->save();
    $user->notify(new BlockedFeedbackNotification($review->feedback));
    return response()->json(['message' => 'Feedback blocked and notification sent to user.']);
}

public function getFeedbackActive($storyId){
    $reviews=Review::where('story_id',$storyId)->where('feedback_status','active')->get();
    return response()->json([
        'feedback_status' => 'success',
        'data' => $reviews
    ]);
}
}
