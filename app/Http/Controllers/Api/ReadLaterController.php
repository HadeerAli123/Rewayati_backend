<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\ReadLater;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class ReadLaterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

///////////////////القصة دي موجودة في الريد ليتر ولا 
    public function inReadlater(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'story_id' => 'required|integer|exists:stories,id',
            ]);
            
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            if (ReadLater::where('story_id', $request->input('story_id'))
            ->where('user_id', Auth::id())->exists()) {
            return response()->json(['message' => true], 200); 
        }
            return response()->json(['message' => false], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
 
    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'story_id' => 'required|integer|exists:stories,id',
            ]);
            
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }
            if (ReadLater::where('story_id', $request->input('story_id'))
            ->where('user_id', Auth::id())->exists()) {
            return response()->json(['message' => 'story is already in your wishlist'], 409); 
        }

            $item = new ReadLater();
            $item->story_id = $request->input('story_id');
            $item->user_id = Auth::id();
            $item->save();
    
            return response()->json(['message' => 'story added successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    

    /**
     * Display the specified resource.
     */
    public function show(ReadLater $readLater)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReadLater $readLater)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
 
    public function removeReadLaterlist($story_id)
    {

        $readlater = ReadLater::where('story_id',$story_id)->first();
        if (!$readlater) {
            return response()->json(['message' => 'story not found.'], 404);
        }

        $readlater->delete();
        return response()->json(['message' => 'All done.'], 200);
        
    }


    
}
