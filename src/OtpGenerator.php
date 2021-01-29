<?php

namespace Seshac\Otp;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Seshac\Otp\Models\Otp as OtpModel;

class OtpGenerator
{

    /**
     * Length of the generated OTP
     *
     * @var int
     */
    protected $length;

    /**
     * Generated OPT type
     *
     * @var bool
     */
    protected $onlyDigits;

    /**
     * use same token to resending opt
     *
     *  @var bool
     */
    protected $useSameToken;

    /**
     * Otp Validity time
     *
     * @var int
     */
    protected $validity;

    /**
     * Delete old otps
     *
     * @var int
     */
    protected $deleteOldOtps;

    /**
     * Maximum otps allowed to generate
     *
     *  @var int
     */
    protected $maximumOtpsAllowed;

    /**
     * Maximum number of times to allowed to validate
     *
     * @var int
     */
    protected $allowedAttempts;
    
    public function __construct()
    {
        $this->length = config('otp-generator.length');
        $this->onlyDigits = config('otp-generator.onlyDigits');
        $this->useSameToken = config('otp-generator.useSameToken');
        $this->validity = config('otp-generator.validity');
        $this->deleteOldOtps = config('otp-generator.deleteOldOtps');
        $this->maximumOtpsAllowed = config('otp-generator.maximumOtpsAllowed');
        $this->allowedAttempts = config('otp-generator.allowedAttempts');
    }

    /**
     * When a method is called, look for the 'set' prefix and attempt to set the
     * matching property to the value passed to the method and return a chainable
     * object to the caller.
     *
     * @param string $method
     * @param mixed $params
     * @return mixed
     */
    public function __call(string $method, $params)
    {
        if (substr($method, 0, 3) != 'set') {
            return;
        }

        $property = Str::camel(substr($method, 3));


        // Does the property exist on this object?
        if (! property_exists($this, $property)) {
            return;
        }

        $this->{$property} = $params[0] ?? null;

        return $this;
    }

    public function generate(string $identifier): object
    {
        $this->deleteOldOtps();

        $otp = OtpModel::where('identifier', $identifier)->first();

        if ($otp == null) {
            $otp = OtpModel::create([
                'identifier' => $identifier,
                'token' => $this->createPin(),
                'validity' => $this->validity,
                'generated_at' => Carbon::now(),
            ]);
        } else {
            $otp->update([
                'identifier' => $identifier,
                'token' => $this->useSameToken ?  $otp->token :  $this->createPin(),
                'validity' => $this->validity,
                'generated_at' => Carbon::now(),
            ]);
        }

        if ($otp->no_times_generated == $this->maximumOtpsAllowed) {
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

        if (! $otp) {
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

        if ($otp->no_times_attempted == $this->allowedAttempts) {
            return (object) [
                'status' => false,
                'message' => "Reached the maximum allowed attempts",
            ];
        }

        $otp->increment('no_times_attempted');

        if ($otp->token == $token) {
            return (object) [
                'status' => true,
                'message' => 'OTP is valid',
            ];
        }

        return (object) [
            'status' => false,
            'message' => 'OTP does not match',
        ];
    }

    public function expiredAt(string $identifier): object
    {
        $otp = OtpModel::where('identifier', $identifier)->first();
       
        if (! $otp) {
            return (object) [
                'status' => false,
                'message' => 'OTP does not exists, Please generate new OTP',
            ];
        }
       
        return (object) [
            'status' => true,
            'expired_at' => $otp->expiredAt(),
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
            $characters = '123456789abcdefghABCDEFGH';
        }
        $length = strlen($characters);
        $pin = '';
        for ($i = 0; $i < $this->length; $i++) {
            $pin .= $characters[rand(0, $length - 1)];
        }

        return $pin;
    }
}
