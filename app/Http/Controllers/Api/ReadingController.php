<?php

namespace App\Http\Controllers\Api;

use App\Models\Reading;
use App\Models\Story;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReadingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function startReading($storyId)
    {
        try {
           
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }
    
           
            $story = Story::find($storyId);
            if (!$story) {
                return response()->json(['error' => 'Story not found'], 404);
            }
    
       
            $readingExists = Reading::where('user_id', $user->id)
                                    ->where('story_id', $storyId)
                                    ->exists();
    
            if ($readingExists) {
                return response()->json(['message' => 'You are already reading this story'], 200);
            }
    
            
            $reading = new Reading();
            $reading->user_id = $user->id;
            $reading->story_id = $storyId;
            $reading->save();
    
            return response()->json(['message' => 'Reading started successfully'], 201);
    
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    
    /**
     * Display the specified resource.
     */
    public function show(Reading $reading)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reading $reading)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reading $reading)
    {
        //
    }
}
