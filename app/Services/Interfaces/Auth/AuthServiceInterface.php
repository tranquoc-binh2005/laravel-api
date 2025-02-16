<?php

namespace App\Services\Interfaces\Auth;

use App\Http\Requests\Auth\AuthRequest;
use Illuminate\Http\Request;

interface AuthServiceInterface
{
    public function login(AuthRequest $request);
    public function me();
    public function refreshAccessToken(Request $request);
}
