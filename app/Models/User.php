<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\CustomVerifyEmail;
use App\Notifications\MyResetPasswordNotification;
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'image',
        'role',
        'gender',
        'username',


    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function stories()
    {
        return $this->hasMany(Story::class);
                 
                   
    }
    
    public function review()
    {
        return $this->hasMany(Review::class);
    }
public function readings()
{
    return $this->hasMany(Reading::class);
}
// public function subscriptions()
// {
//     return $this->hasMany(Subscription::class);
// }
// public function purchases()
// {
//     return $this->hasMany(Purchase::class);
// }
public function sendEmailVerificationNotification()
{
    $this->notify(new CustomVerifyEmail());

}
public function sendPasswordResetNotification($token)
{
    $this->notify(new MyResetPasswordNotification($token));
}
}




