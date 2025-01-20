<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Story extends Model
{
    use HasFactory ,SoftDeletes;

    protected $fillable=[
'user_id','title','description','language','maincharacters','copyright','cover_image','advertisement_image','content_type','status','category_id'
    ];
    protected $dates = ['deleted_at'];
    public function isDraft()
    {
        return $this->publication_status === 'draft';
    }

    public function publish()
    {
        $this->publication_status = 'published';
        $this->save();
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }
public function tags(){
    return $this->belongsToMany(Tag::class ,'story_tag');
}
public function user()
{
    return $this->belongsTo(User::class);
              
              
}
public function review()
{
    return $this->hasMany(Review::class);
}
public function usersWhoSaved()
{
    return $this->belongsToMany(User::class, 'read_later');
}
public function mainCharacters()
{
    return $this->hasMany(MainCharacters::class);
}
public function readings()
{
    return $this->hasMany(Reading::class);
}
}
