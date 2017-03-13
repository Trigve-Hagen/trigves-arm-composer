<?php

namespace Trigves\Arm;

use Arm\Development\Arm;
use Illuminate\Support\ServiceProvider;

class ArmServiceProvider extends ServiceProvider
{
	
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
		include __DIR__.'/routes.php';
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Arm::class', function() {
			return Arm();
		});
    }
}
