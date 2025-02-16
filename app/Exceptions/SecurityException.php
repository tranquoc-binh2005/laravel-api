<?php
namespace App\Exceptions;

use Exception;

class SecurityException extends Exception
{
    public function __construct($message = "", $code = 403)
    {
        parent::__construct($message, $code);
    }
}
