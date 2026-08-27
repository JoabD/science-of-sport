<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Just 'admin' and 'user' for now, seeded in RoleAndUserSeeder. Kept as
// its own table instead of a string/enum column on users so we can add
// more roles later without a migration touching the users table again.
class Role extends Model
{
    protected $fillable = ['name'];
}
