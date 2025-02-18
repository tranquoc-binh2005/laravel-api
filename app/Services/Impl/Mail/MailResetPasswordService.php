<?php

namespace App\Services\Impl\Mail;

use App\Enum\Config\Common;
use App\Http\Requests\Auth\ResetPassRequest;
use App\Http\Resources\ApiResource;
use App\Models\ResetPassword;
use App\Notifications\ResetPasswordRequest;
use App\Repositories\User\UserRepositories;
use App\Services\Interfaces\Mail\MailResetPasswordServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class MailResetPasswordService implements MailResetPasswordServiceInterface
{
    private UserRepositories $userRepositories;
    public function __construct(
        UserRepositories $userRepositories
    )
    {
        $this->userRepositories = $userRepositories;
    }

    /**
     * @throws UserNotDefinedException
     */
    public function sendEmailResetPassword(ResetPassRequest $request): JsonResponse
    {
        $user = $this->userRepositories->findByEmail($request->input('email'));
        if(!$user) {
            throw new UserNotDefinedException(Lang::get('auth.not_found'));
        }
        $passwordReset = ResetPassword::updateOrCreate(
            ['email' => $user->email],
            ['token' => Str::random(60)]
        );
        if ($passwordReset) {
            $user->notify(new ResetPasswordRequest($passwordReset->token));
        }
        return ApiResource::messages(Common::SUCCESS);
    }
}
