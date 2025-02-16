<?php
namespace App\Enum\Config;

enum ApiResponseKey: string
{
    public const STATUS = 'status';
    public const DATA = 'data';
    public const CODE = 'code';
    public const MESSAGE = 'message';
    public const ERRORS = 'errors';
    public const TIMESTAMP = 'timestamp';
    public const ACCESS_TOKEN = 'access_token';
    public const REFRESH_TOKEN = 'refresh_token';
    public const EXPIRES_AT = 'expiresAt';
    public const AUTH_COOKIE = 'auth_cookie';
}
