<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Generated OPT Expire time by minutes/Seconds
    |--------------------------------------------------------------------------
    |
    | By default = 10 minutes/Seconds
    |
    */
    'expiry' => [
        'time' => env('OTP_EXPIRY_TIME', 10),
        'type' => env('OTP_EXPIRY_TYPE', 'minutes')
    ],
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
    |  maximum OTPs allowed being generated during the expiration time.
    |--------------------------------------------------------------------------
    |
    | Once the limit reached, the end-user can't able to generate OPT until the OTP expiration time is over.
    |
    */
    'maximum_otps' => env('MAXIMUM_OTPS_GENERATED', 5), 

     /*
    |--------------------------------------------------------------------------
    | Attempts count time in minutes
    |--------------------------------------------------------------------------
    |
    | The field used to count allowed attempts by end user.
    |
    */
    'attempts_count_time' => env('OTP_ATTEMPTS_COUNT_TIME', 10), 

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
];
