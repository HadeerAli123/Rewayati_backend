<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChapterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return[
        'chapter_id'=>$this->id,
        'title'=> $this->title,
      'content'=>$this->content,
      'payment_status'=>$this->payment_status,
      'image'=>$this->image,
   'part_number'=>$this->part_number,
  'story' => new StoryResource($this->whenLoaded('story')),
   'created_at' => $this->created_at->toDateTimeString(), 
   'updated_at' => $this->updated_at->toDateTimeString(), 
       ];
    }
}
