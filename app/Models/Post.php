<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    protected $guarded = [];
    protected $casts = [
        'published' => 'boolean',
        'tags' => 'array',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
   
    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_user')
        ->withPivot(['orders'])
        ->withTimestamps();
    }
}
