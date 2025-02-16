<?php
namespace App\Http\Controllers\Api\V1\Auth;

use App\Enum\Config\ApiResponseKey;
use App\Enum\Config\Common;
use App\Exceptions\SecurityException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthRequest;
use App\Http\Resources\ApiResource;
use App\Traits\Loggable;
use App\Services\Interfaces\Auth\AuthServiceInterface as AuthService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Auth\AuthenticationException;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;
use Illuminate\Http\Request;

class AuthController extends Controller {

    use Loggable;

    private AuthService $authService;
    public function __construct(
        AuthService $authService
    ){
        $this->authService = $authService;
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
        } catch(SecurityException $e) {
            return ApiResource::messages($e->getMessage(), Response::HTTP_FORBIDDEN);
        } catch (ModelNotFoundException $e) {
            return ApiResource::messages($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleLogException($e);
        }
    }
}
