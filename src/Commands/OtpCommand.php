<?php

namespace Seshac\Otp\Commands;

use Illuminate\Console\Command;

class OtpCommand extends Command
{
    public $signature = 'otp-generator';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
