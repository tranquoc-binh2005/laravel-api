<?php

namespace App\Services\Interfaces\Auth;

use App\Http\Requests\Auth\AuthRequest;
use App\Http\Requests\Auth\PasswordConfirmRequest;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\ResetPassRequest;

interface AuthServiceInterface
{
    public function login(AuthRequest $request);
    public function me();
    public function refreshAccessToken(Request $request);
    public function resetPassword(PasswordConfirmRequest $request, string $token = '');
}
