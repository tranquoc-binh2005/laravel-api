<?php

namespace App\Http\Middleware;

use App\Enum\Config\Common;
use App\Traits\Loggable;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\ApiResource;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class JWT
{
    use Loggable;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if(!$request->hasHeader('Authorization')) {
                return ApiResource::messages(Lang::get('auth.authorization_not_found'));
            }
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            return ApiResource::messages(Lang::get('auth.token_expired'), Response::HTTP_UNAUTHORIZED);
        } catch (TokenInvalidException $e) {
            return ApiResource::messages(Lang::get('auth.token_invalid'), Response::HTTP_UNAUTHORIZED);
        } catch (JWTException $e) {
            return ApiResource::messages(Common::NETWORK_ERROR, Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            $this->handleLogException($e);
        }
        return $next($request);
    }
}
