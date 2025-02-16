<?php
namespace App\Enum\Config;

enum Common: string
{
    public const NETWORK_ERROR = "Network Error";
    public const ERROR_MESSAGE = "Error Message: ";
    public const API = "api";
    public const SUCCESS = "Success";
    public const REFRESH_TOKEN_COOKIE_NAME = "refresh_token";
}
