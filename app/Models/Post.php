<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['user_id', 'title', 'subtitle', 'event_date', 'location', 'overview'];

    public function packages()
    {
        return $this->hasMany(PostPackage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
