<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasQuery;

class UserCatalogue extends Model
{
    use HasQuery;
    protected $table = 'user_catalogues';
    protected $fillable = [
        'name',
        'canonical',
        'publish',
        'user_id',
    ];
}
