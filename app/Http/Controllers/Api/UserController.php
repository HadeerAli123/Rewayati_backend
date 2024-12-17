<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; 
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use App\Models\Story;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),//   بتجيب كل الداتا الي مدخلها اليوزر الي هنتحقق منها عن طريق الفاليديشن 
             [
                'email' => 'required|email',
                'password' => 'required|string|min:8',
                'device_name' => 'required|string|max:255',

    
            ], [
                "email.required" => "Email is required.",
                "email.email" => "The email format you provided is invalid.",
                "password.required" => "Password is required.",
                "password.min" => "Password must be at least 8 characters.",
            ]);
            
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => 'Email not found.'], 404);
        }
        
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'Invalid password.'], 401);
        }

        return response()->json([
            'token' => $user->createToken($request->email),
            'user' => $user,
        
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
    
        }
    
    
   
    
   
     public function sendResetLinkEmail(Request $request)
     {
         $request->validate(['email' => 'required|email']);
         $user = User::where('email', $request->email)->first();
 
         if (!$user) {
             return response()->json([
                 "message"=>"Email does not exist in our records."
             ], 404);
         }
 
         $status = Password::sendResetLink(
             $request->only(keys: 'email')
         );
 
         if ($status === Password::RESET_LINK_SENT) {
 
             return response()->json([
                 "message"=>"Password reset link sent!"
             ], 200);
 
         } elseif ($status === Password::RESET_THROTTLED) {
 
             return response()->json([
                 "message"=>"Password reset link sent!"
             ], 200);
         } else {
 
             return response()->json([
                 "message"=>"Unable to send reset link to the provided email."
             ], 500);
 
         }
     }

    /**
     * Update the specified resource in storage.
     */
    public function resetPassword(Request $request)
    {
        
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $resetPassword = DB::table('password_reset_tokens')->where('email',$request->email)->first();
        if (Hash::check($request->token,$resetPassword->token )) {
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->password = Hash::make($password);
                    $user->save();
                }
            );
        } else {
            return response()->json(['message' => 'Invalid token.'], 401);
        }

        if ($status === Password::PASSWORD_RESET) {
        
            return response()->json([
                "message"=>"Password reset successful!"
            ] , 200);
        } else {
        
            return response()->json([
                "message"=>"Invalid token or email."
            ] , 404);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
   
     public function updatePassword(Request $request)
     {
     
         $std_validator = Validator::make($request->all(), [
             'currentPassword' => 'required',
             'newPassword' => 'required|min:8',
             'password_confirmation' => 'required|same:newPassword',
         ]);
     
         if ($std_validator->fails()) {
             return response()->json(['errors' => $std_validator->errors()], 400);
         }
     
     
         if (!Hash::check($request->currentPassword, Auth::user()->password)) {
             return response()->json(['error' => 'Invalid current password.'], 401);
         }
     
     
         $user = Auth::user();
         $user->password = Hash::make($request->newPassword);
         $user->save();
     
         return response()->json(['message' => 'Password updated successfully.'], 200);
     
     }
     
    
    function register(Request $request) {
        try {
        $std_validator = Validator::make($request->all(), [
        
            'device_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gender' => 'required|string|in:male,female,other',
            'username' => 'required|string|min:3|max:50|unique:users,username',
            'role' => 'required|string|max:50',
        ], [
            "email.required" => "You must add an email to log in.",
            "email.email" => "The email format you provided is invalid.",
            "password.confirmed" => "The password confirmation does not match.",
            "image.image" => "The image must be a valid image file.",
            "image.mimes" => "The image must be in jpeg, png, jpg, or gif format.",
            "image.max" => "The image size should not exceed 2MB.",
        ]);
        
    
    $my_path = '';
    if(request()->hasFile("image")){
        $image = request()->file("image");
        $my_path=$image->store('users','uploads');
        $my_path= asset('uploads/' . $my_path); 
    }

    $user = new User();//ده لإنشاء كائن جديد من موديل User.
    $user->image = $my_path; 
    $user->email = $request->email;
    $user->role = $request->role;
    $user->gender = $request->gender;
    $user->username = $request->username;
    $user->password = Hash::make($request->password);
    $user->save();

    
$token = $user->createToken($request->device_name)->plainTextToken;
$user->sendEmailVerificationNotification();

return response()->json(['token' => $token , 'user' => new UserResource($user)], 201); 
} catch (\Exception $e) {
return response()->json(['error' => $e->getMessage()], 500);
}
}

public function logoutFromOneDevice(Request $request)
{
    try {
        auth()->user()->currentAccessToken()->delete(); 
        return response()->json(['message' => 'Successfully logged out from this device'], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error logging out from this device', 'error' => $e->getMessage()], 500);
    }
}

public function getUser(User $user)
{

    $user = auth()->user();


    return new UserResource($user);

 }
 ////////////////
public function updateUser(Request $request) {
    $user = Auth::user(); 

    $validator = Validator::make($request->all(), [
        'email' => 'nullable|email|unique:users,email,' . $user->id,
        'gender' => 'nullable|string|in:male,female,other',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'username' => 'required|string|min:3|max:50|unique:users,username',
        
    ]);
    
if ($validator->fails()) {
    return response()->json(['errors' => $validator->errors()], 400);
}

if ($request->hasFile('image')) {
    $image = $request->file('image');
    $my_path = $image->store('users', 'uploads');
    $user->image = $my_path;
}

$user->email = $request->email ?? $user->email;
$user->gender = $request->gender ?? $user->gender;
$user->username = $request->username??$user->username;
$user->save();

    
return response()->json(['message' => 'User updated successfully', 'user' => $user]);
}
public function selectCategoriesAndGetStories(Request $request)///////////////
{
    $validated = $request->validate([
        'categories' => 'required|array|min:3|max:3', 
        'categories.*' => 'exists:categories,id'
    ], [
        'categories.required' => 'You must select the categories.',
        'categories.array' => 'Categories must be an array.',
        'categories.min' => 'You must select exactly 3 categories.',
        'categories.max' => 'You must select exactly 3 categories.',
        'categories.*.exists' => 'One of the categories you selected does not exist.'
    ]);

    $stories = Story::whereIn('category_id', $validated['categories'])->get();

    return response()->json([
        'message' => 'Categories selected successfully!',
        'stories' => $stories 
    ]);
}




    public function index(Request $request)
    {
      
        $users = User::all();
        return response()->json($users);
    }

}