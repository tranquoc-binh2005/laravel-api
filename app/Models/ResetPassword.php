<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResetPassword extends Model
{
    protected $table = 'password_reset_tokens';
    public $incrementing = false;
    protected $primaryKey = 'email';

    protected $fillable = [
        'email',
        'token'
    ];
}
