<?php

namespace Seshac\Otp;

use Illuminate\Support\ServiceProvider;

class OtpServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/otp-generator.php' => config_path('otp-generator.php'),
            ], 'config');

            $migrationFileName = 'create_otps_table.php';
            if (! $this->migrationFileExists($migrationFileName)) {
                $this->publishes([
                    __DIR__ . "/../database/migrations/{$migrationFileName}.stub" => database_path('migrations/' . date('Y_m_d_His', time()) . '_' . $migrationFileName),
                ], 'migrations');
            }
        }
    }

    public function register()
    {
        $this->app->alias(OtpGenerator::class, 'otp-generator');
        $this->mergeConfigFrom(__DIR__ . '/../config/otp-generator.php', 'otp-generator');
    }

    public static function migrationFileExists(string $migrationFileName): bool
    {
        $len = strlen($migrationFileName);
        foreach (glob(database_path("migrations/*.php")) as $filename) {
            if ((substr($filename, -$len) === $migrationFileName)) {
                return true;
            }
        }

        return false;
    }
}
