<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCatalogue extends Model
{
    protected $table = 'user_catalogues';
    protected $fillable = [
        'name',
        'canonical',
        'publish',
        'user_id',
    ];
}
