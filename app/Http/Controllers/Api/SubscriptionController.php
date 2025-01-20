<?php

// namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;

// class SubscriptionController extends Controller
// {
    
//     public function subscribe(Request $request)
// {
//     $user = Auth::user();
//     $paymentStatus = $this->processPayment($request->amount); 

//     if ($paymentStatus) {
//         $user->subscription()->updateOrCreate(
//             ['user_id' => $user->id],
//             [
//                 'is_active' => true,
//                 'expires_at' => now()->addMonth(), 
//             ]
//         );

//         return response()->json(['message' => 'Subscription activated!'], 200);
//     }

//     return response()->json(['message' => 'Payment failed.'], 400);
// }

// }
