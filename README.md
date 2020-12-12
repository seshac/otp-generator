# OTP Generator and Validator for Laravel Applications

[![Latest Version on Packagist](https://img.shields.io/packagist/v/seshac/otp-generator.svg?style=flat-square)](https://packagist.org/packages/seshac/otp-generator)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/seshac/otp-generator/run-tests?label=tests)](https://github.com/seshac/otp-generator/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/seshac/otp-generator.svg?style=flat-square)](https://packagist.org/packages/seshac/otp-generator)


## Installation

You can install the package via composer:

```bash
composer require seshac/otp-generator
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Seshac\Otp\OtpServiceProvider" --tag="migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --provider="Seshac\Otp\OtpServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Generated OPT Validity time by minutes
    |--------------------------------------------------------------------------
    |
    | By default = 10 minutes
    |
    */
    'validity' => env('OTP_VALIDITY_TIME', 10),
    /*
    |--------------------------------------------------------------------------
    |  Length of the generated OTP
    |--------------------------------------------------------------------------
    |
    | By default = 6 digits used
    |
    */
    'length' => env('OPT_LENGTH', 6),

    /*
    |--------------------------------------------------------------------------
    | Allowed attempts within duration of attempts_count_time
    |--------------------------------------------------------------------------
    |
    | This filed will be used when validating the generated OTP token.
    |
    */
    'allowed_attempts' => env('OTP_ALLOWED_ATTEMPTS', 5),


    /*
    |--------------------------------------------------------------------------
    | Generated OPT Type
    |-------------------------------------------------------------------------
    |
    | if true the geneated OTP contains only digits. ex : 654321
    | f false the geneated OTP contains only alpanumeric. ex : 21ab43
    */
    'onlyDigits' => true,

    /*
    |--------------------------------------------------------------------------
    | Use same token to resend the OTP
    |-------------------------------------------------------------------------
    |
    | if true the the second time onwards geneated OTPs same a the first one (Only OTP validation time)
    | if false each time unique OPT will be generated
    */
    'useSameToken' => false,

    /*
    |--------------------------------------------------------------------------
    | Delete old otps older than specified minutes
    |-------------------------------------------------------------------------
    |
    | Default 30 minutes.
    */
    'deleteOldOtps' => 30,

    /*
    |--------------------------------------------------------------------------
    |  maximum OTPs allowed being generated during the deleteOldOtps time.
    |--------------------------------------------------------------------------
    |
    | Once the limit reached, the end-user can't able to generate OPT until the OTP deleteOldOtps time is over.
    |
    */
    'maximum_otps_allowed' => env('MAXIMUM_OTPS_ALLOWED', 5),
];

```

## Usage

```php
use Seshac\Otp\Otp;
.
.
$otp =  Otp::generate($identifier);
.
$verify = Otp::validate($identifier, $otp->token);
// response
{
  "status": true
  "message": "OTP is valid"
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [sesha](https://github.com/seshac)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
