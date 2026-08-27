<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// A "package" is one row of the pricing table on the event page, e.g
// "Title Sponsor - $15,000" or "Foursome - $1,800". type just groups
// them (sponsorship vs golf_only) for display, its not an enum in the DB.
class PostPackage extends Model
{
    protected $fillable = ['post_id', 'type', 'name', 'price', 'capacity', 'description'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
