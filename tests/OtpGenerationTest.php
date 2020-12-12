<?php

namespace Seshac\Otp\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Seshac\Otp\Otp;

class OtpGenerationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_able_to_generate_and_validate_the_otp()
    {
        $identifier = Str::random(12);
        $otp = Otp::generate($identifier);
        $validator = Otp::validate($identifier, $otp->token);
        dd($validator);
        $this->assertEquals($validator->status, true);
    }

    /** @test */
    public function cant_able_to_verify_the_opt_once_get_expired()
    {
        $identifier = Str::random(12);
        $otp = Otp::generate($identifier);
        $this->travel(config('otp-generator.validity') + 1)->minutes();
        $validator = Otp::validate($identifier, $otp->token);
        $this->assertEquals($validator->status, false);
    }

    /** @test */
    public function can_able_to_regenerate_and_validate_the_otp()
    {
        $identifier = Str::random(12);
        Otp::generate($identifier);
        $secondTime = Otp::generate($identifier);
        $validator = Otp::validate($identifier, $secondTime->token);
        $this->assertEquals($validator->status, true);
        $this->assertDatabaseCount('otps', 1);
    }

    /** @test */
    public function cant_able_to_generate_the_otp_more_than_the_maximum_specified_time()
    {
        $identifier = Str::random(12);
        $limit = config('otp-generator.maximum_otps_allowed');
      
        for ($i = 0; $i < $limit ; $i++) {
            Otp::generate($identifier);
        }
        $otp = Otp::generate($identifier);

        $this->assertEquals($otp->status, false);
    }

    /** @test */
    public function the_otps_will_be_deleted_after_spceifed_amount_of_time()
    {
        $identifier = Str::random(12);
        $otp = Otp::generate($identifier);
        $this->travel(config('otp-generator.deleteOldOtps'))->minutes();
        $validator = Otp::validate($identifier, $otp->token);
        $this->assertEquals($validator->status, false);
        $this->assertDatabaseCount('otps', 1);
        Otp::generate(Str::random(13));
        $this->assertDatabaseCount('otps', 1);
        $this->travelBack();
    }

    /** @test */
    public function cant_able_to_verify_the_otp_once_reach_the_maximum_allowed_attempts()
    {
        $identifier = Str::random(12);
        $otp = Otp::generate($identifier);
        $allowed_attempts = config('otp-generator.allowed_attempts');
        for ($i = 0; $i < $allowed_attempts ; $i++) {
            Otp::validate($identifier, 'wrongToken');
        }
        $validator = Otp::validate($identifier, $otp->token);
        $this->assertEquals($validator->status, false);
    }
}
