<?php

namespace Seshac\Otp;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Seshac\Otp\Otp
 */
class Otp extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'otp-generator';
    }
}
