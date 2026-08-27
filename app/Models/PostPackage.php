<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostPackage extends Model
{
    protected $fillable = ['post_id', 'type', 'name', 'price', 'capacity', 'description'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
