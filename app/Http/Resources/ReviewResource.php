<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'review_id' => $this->id,
            'has_voted' => $this->rating,
            'feedback' => $this->feedback,
            'story' => new StoryResource($this->whenLoaded('story')),/// يعني هنا بيحمل تفاصيل الاستوري بالكامل من الاستوري ريسورس    
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
