<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// This is basically "Event" but the table/route names ended up as posts/events
// mixed together since thats how the assessment brief called it. Not renaming
// it now, too many places already depend on the name.
class Post extends Model
{
    protected $fillable = ['user_id', 'title', 'subtitle', 'event_date', 'location', 'overview'];

    /**
     * One event can have many pricing tiers (sponsorships, golf only, etc).
     * See PostPackage.
     */
    public function packages()
    {
        return $this->hasMany(PostPackage::class);
    }

    /**
     * Who created the event (the admin that used the "Create Event" form).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
