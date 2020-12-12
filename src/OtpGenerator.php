<?php

namespace Seshac\Otp;

use Carbon\Carbon;
use Seshac\Otp\Models\Otp as OtpModel;

class OtpGenerator
{

    /**
     * Length of the generated OTP
     *
     * @var [integer]
     */
    public $length;

    /**
     * Generated OPT type
     *
     * @var [bool]
     */
    public $onlyDigits;

    /**
     * use same token to resending opt
     * 
     *  @var [bool]
     */
    public $useSameToken;

    /**
     * Otp Validity time
     *
     * @var [integer]
     */
    public $validity;

    /**
     * Delete old otps
     *
     * @var [integer]
     */
    public $deleteOldOtps;

    /**
     * Maximum otps allowed to generate
     * 
     *  @var [integer]
     */
    public $maximum_otps_allowed;

    public function __construct()
    {
        $this->length = config('otp-generator.length');
        $this->onlyDigits = config('otp-generator.onlyDigits');
        $this->useSameToken = config('otp-generator.useSameToken');
        $this->validity = config('otp-generator.validity');
        $this->deleteOldOtps = config('otp-generator.deleteOldOtps');
        $this->maximum_otps_allowed = config('otp-generator.maximum_otps_allowed');
        $this->allowed_attempts = config('otp-generator.allowed_attempts');
    }

    public function generate(string $identifier): object
    {
        $this->deleteOldOtps();

        $token = $this->createPin();

        $otp = OtpModel::updateOrCreate(
            ['identifier' => $identifier],
            [
                'token' => $token,
                'validity' => $this->validity,
                'generated_at' => Carbon::now(),
            ]
        );

        if ($otp->no_times_generated == $this->maximum_otps_allowed) {
            return (object) [
                'status' => false,
                'message' => "Reached the maximum times to generate OTP",
            ];
        }

        $otp->increment('no_times_generated');


        return (object) [
            'status' => true,
            'token' => $otp->token,
            'message' => "OTP generated",
        ];
    }

    public function validate(string $identifier, string $token): object
    {
        $otp = OtpModel::where('identifier', $identifier)->first();

        if (!$otp) {
            return (object) [
                'status' => false,
                'message' => 'OTP does not exists, Please generate new OTP',
            ];
        }

        if ($otp->isExpired()) {
            return (object) [
                'status' => false,
                'message' => 'OTP is expired',
            ];
        }

        if ($otp->no_times_attempted == $this->allowed_attempts) {
            return (object) [
                'status' => false,
                'message' => "Reached the maximum allowed attempts",
            ];
        }


        if ($otp->token == $token) {
            return (object) [
                'status' => true,
                'message' => 'OTP is valid',
            ];
        }

        $otp->increment('no_times_attempted');

        return (object) [
            'status' => false,
            'message' => 'OTP does not match',
        ];
    }

    private function deleteOldOtps()
    {
        OtpModel::where('expired', true)
            ->orWhere('created_at', '<', Carbon::now()->subMinutes($this->deleteOldOtps))
            ->delete();
    }

    private function createPin(): string
    {
        if ($this->onlyDigits) {
            $characters = '0123456789';
        } else {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        $length = strlen($characters);
        $pin = '';
        for ($i = 0; $i < $this->length; $i++) {
            $pin .= $characters[rand(0, $length - 1)];
        }

        return $pin;
    }
}
