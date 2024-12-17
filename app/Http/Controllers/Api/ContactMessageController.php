<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Validator;
use App\Models\ContactMessage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   
    public function index()
    {
        try{
            $messages = ContactMessage::all();
            return response()->json(['data' => $messages ], 200);
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
                'email' => 'required|email',
                'phone' => 'required|string|min:9',
                'name' => 'required|string|max:255',
                'message' => 'required|string|max:255',
    
            ]);
        
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }
            $message =new ContactMessage();
            $message->email = $request->email;
            $message->phone = $request->phone;
            $message->name = $request->name;
            $message->message = $request->message;
            $message->save();

        
        return response()->json(['message' => 'saved successfully'], 200);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        
    }

    /**
     * Display the specified resource.
     */
    public function show(ContactMessage $contactMessage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContactMessage $contactMessage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
  
    public function destroy(String $id)
    {
        try{

            $message = ContactMessage::find($id);
            if (!$message){
                return response()->json(['error' => 'message not found' ], 404);

            }
            $message->delete();
            return response()->json(['message' => 'Deleted successfully' ], 200);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
    
    }
}
