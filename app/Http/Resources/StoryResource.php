<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
    return[
        'story_id'=>$this->id,
        'title'=>$this->title,
        'description'=>$this->description,
        'language'=>$this->language,
        'maincharacters'=>$this->maincharacters,
        'copyright'=>$this->copyright,
        'cover_image'=>$this->cover_image,
        'content_type'=>$this->content_type,
        'status'=>$this->status,
        'deleted_at' => $this->deleted_at, 
        'category'=>new CategoryResource($this->whenLoaded('category')),
        'user' => new UserResource($this->whenLoaded('user')),
        'created_at' => $this->created_at->toDateTimeString(), 
        'updated_at' => $this->updated_at->toDateTimeString(), 

    ];
    }
}
