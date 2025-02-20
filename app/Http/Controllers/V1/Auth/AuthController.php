<?php
namespace App\Http\Controllers\V1\Auth;

use App\Enum\Config\ApiResponseKey;
use App\Enum\Config\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthRequest;
use App\Http\Requests\Auth\PasswordConfirmRequest;
use App\Http\Requests\Auth\ResetPassRequest;
use App\Http\Resources\ApiResource;
use App\Services\Impl\Mail\MailResetPasswordService as MailResetPasswordService;
use App\Services\Interfaces\Auth\AuthServiceInterface as AuthService;
use App\Traits\Loggable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class AuthController extends Controller {

    use Loggable;

    private AuthService $authService;
    private MailResetPasswordService $resetPasswordService;
    public function __construct(
        AuthService $authService,
        MailResetPasswordService $resetPasswordService
    ){
        $this->authService = $authService;
        $this->resetPasswordService = $resetPasswordService;
    }

    public function authenticate(AuthRequest $request): JsonResponse
    {
        try {

            $response = $this->authService->login($request);
            return ApiResource::ok($response[ApiResponseKey::DATA], Common::SUCCESS)->withCookie($response[ApiResponseKey::AUTH_COOKIE]);

        } catch (AuthenticationException $e){
            return ApiResource::messages($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Exception $e){
            return $this->handleLogException($e); // Write Loggable
        }
    }

    public function me(): JsonResponse
    {
        try {
            $auth = $this->authService->me();
            return ApiResource::ok($auth, Common::SUCCESS);
        } catch (UserNotDefinedException $e){
            return ApiResource::messages($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
        catch (\Exception $e) {
            return $this->handleLogException($e);
        }
    }

    public function refresh(Request $request): JsonResponse
    {
        try {
            $response = $this->authService->refreshAccessToken($request);
            return ApiResource::ok($response[ApiResponseKey::DATA], Common::SUCCESS)
                ->withCookie($response[ApiResponseKey::AUTH_COOKIE]);
        } catch (ModelNotFoundException $e) {
            return ApiResource::messages($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch(UnauthorizedException $e){
            return ApiResource::messages($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return $this->handleLogException($e);
        }
    }

    public function forgot(ResetPassRequest $request): JsonResponse
    {
        try {
            $response = $this->resetPasswordService->sendEmailResetPassword($request);
            return ApiResource::ok($response, Common::SUCCESS);
        }
        catch (UserNotDefinedException $e){
            return ApiResource::messages($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleLogException($e);
        }
    }

    public function reset(PasswordConfirmRequest $request, string $token = ''): JsonResponse
    {
        try {
            $response = $this->authService->resetPassword($request, $token);
            return ApiResource::ok($response, Common::SUCCESS);
        } catch (TokenExpiredException|ModelNotFoundException $e) {
            return ApiResource::messages($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (UserNotDefinedException $e) {
            return ApiResource::messages($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleLogException($e);
        }
    }
}
