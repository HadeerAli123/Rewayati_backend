<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
class Chapter extends Model
{
    use HasFactory;
    protected $fillable=[
'title','content','payment_status','image','part_number','story_id',
    ];
    public function story(){
        return $this->belongsTo(Story::class);
    }
//     public function purchases()
// {
//     return $this->hasMany(Purchase::class);
// }
}
