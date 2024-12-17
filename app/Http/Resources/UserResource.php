<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
      return [
        'user_id' => $this->id,
        'email' => $this->email,
        'username'=>$this->username,
        'image' => $this->image,
        'role' => $this->role,
        'gender' => $this->gender,
        'email_verified_at' => $this->email_verified_at,
      ];
    }
}
