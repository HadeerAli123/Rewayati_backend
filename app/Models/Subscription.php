<?php
// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class Subscription extends Model
// {
//     use HasFactory;

  
//     protected $fillable = [
//         'user_id', 
//         'is_active', 
//         'expires_at',
//     ];


//     public function user()
//     {
//         return $this->belongsTo(User::class);
//     }

   
//     public function isActive()
//     {
//         return $this->is_active && now()->lessThanOrEqualTo($this->expires_at);
//     }
// }

