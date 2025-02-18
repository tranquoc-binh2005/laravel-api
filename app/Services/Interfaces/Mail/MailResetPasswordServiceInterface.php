<?php
namespace App\Services\Interfaces\Mail;
use App\Http\Requests\Auth\ResetPassRequest;
interface MailResetPasswordServiceInterface
{
    public function sendEmailResetPassword(ResetPassRequest $request);
}
