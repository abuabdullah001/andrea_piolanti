<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommunityPost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'slug',
        'user_id',
        'title',
        'subtitle',
        'topic_id',
        'category_id',
        'content',
        'image',
        'status',
    ];

    protected $casts = [
        'slug'        => 'string',
        'user_id'     => 'integer',
        'title'       => 'string',
        'subtitle'    => 'string',
        'topic_id'    => 'integer',
        'category_id' => 'integer',
        'content'     => 'string',
        'image'       => 'string',
        'status'      => 'boolean',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function topic()
    {
        return $this->belongsTo(Topic::class, 'topic_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function comments()
    {
        return $this->hasMany(CommunityComment::class, 'community_post_id');
    }
}
