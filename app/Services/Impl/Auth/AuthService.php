<?php
namespace App\Services\Impl\Auth;

use App\Enum\Config\ApiResponseKey;
use App\Enum\Config\Common;
use App\Http\Requests\Auth\AuthRequest;
use App\Services\Interfaces\Auth\AuthServiceInterface;
use http\Exception\RuntimeException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;
use App\Models\RefreshToken;
use Illuminate\Support\Facades\Cookie;
use App\Repositories\Auth\RefreshTokenRepositories;
use Illuminate\Support\Facades\DB;
use App\Traits\Loggable;
use App\Exceptions\SecurityException;
use App\Repositories\User\UserRepositories;
class AuthService implements AuthServiceInterface
{
    use Loggable;
    private $auth;

    private RefreshTokenRepositories $refreshRepository;
    private UserRepositories $userRepositories;
    private const ACCESS_TOKEN_TIME_TO_LIVE = 5;
    private const REFRESH_TOKEN_TIME_TO_LIVE = 15;
    public function __construct(
        RefreshTokenRepositories $refreshRepository,
        UserRepositories $userRepositories,
    )
    {
        /**
         *
         */
        $this->auth = auth(Common::API);
        $this->refreshRepository = $refreshRepository;
        $this->userRepositories = $userRepositories;
    }

    /**
     * @throws AuthenticationException
     */
    public function login(AuthRequest $request)
    {
        $accessToken = $this->attemptLogin($request);
        $refreshToken = $this->createRefreshToken();
        return $this->authResponse($accessToken, $refreshToken);
    }

    /**
     * @throws AuthenticationException
     */
    private function attemptLogin($request): string
    {
        $credentials = [
            'email' => $request->string('email'),
            'password' => $request->string('password'),
        ];

        $this->auth->setTTL(self::ACCESS_TOKEN_TIME_TO_LIVE);
        if(!$accessToken = $this->auth->attempt($credentials)) {
            throw new AuthenticationException(Lang::get('auth.failed'));
        }
        return $accessToken;
    }

    private function createRefreshToken(): string
    {
        $payload = [
            'refresh_token' => Str::uuid(),
            'expires_at' => now()->addDay(self::REFRESH_TOKEN_TIME_TO_LIVE),
            'user_id' => $this->auth->user()->id,
        ];

        if(!$result = $this->refreshRepository->create($payload)) {
            throw new RuntimeException(Lang::get('auth.refresh_token_create_failed'));
        }

        return $result->refresh_token;
    }

    private function authResponse(string $accessToken = '', string $refresh_token = ''): array
    {
        return [
            ApiResponseKey::DATA => [
                ApiResponseKey::ACCESS_TOKEN => $accessToken,
                ApiResponseKey::EXPIRES_AT => $this->auth->factory()->getTTL() * 60,
            ],
            ApiResponseKey::AUTH_COOKIE => Cookie::make(Common::REFRESH_TOKEN_COOKIE_NAME,$refresh_token, self::REFRESH_TOKEN_TIME_TO_LIVE * 24 * 60, '/', null, false, true, false, 'Lax')
        ];
    }

    /**
     * @throws UserNotDefinedException
     */
    public function me()
    {
        $user = $this->auth->user();
        if(!$user) {
            throw new UserNotDefinedException(Lang::get('auth.not_found'));
        }
        return $user;
    }

    public function refreshAccessToken(Request $request): mixed
    {
        DB::beginTransaction();
        try {
            $refreshToken = $request->cookie(Common::REFRESH_TOKEN_COOKIE_NAME) ?? '';
            $result = $this->refreshRepository->findValidRefreshToken($refreshToken);
            if(!$result = $this->refreshRepository->findValidRefreshToken($refreshToken)){
                throw new ModelNotFoundException(Lang::get('auth.not_found'));
            }

            if($this->checkRefreshTokenReused($result)){
                $this->refreshRepository->revokeAllUserRefreshTokenAll($result->user_id);
                DB::commit();
                throw new SecurityException(Lang::get('auth.refresh_token_used_detected'));
            }

            $result->update(['was_used' => true]);
            $user = $this->userRepositories->findById($result->user_id);
            $token = $this->auth->login($user);
            $newRefreshToken = $this->createRefreshToken();
            DB::commit();

            return $this->authResponse($token, $newRefreshToken);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function checkRefreshTokenReused(RefreshToken $refreshToken): bool
    {
        if($refreshToken->was_used || $refreshToken->is_revoked){
            $this->securityLogException($refreshToken);
            return true;
        }
        return false;
    }
}
