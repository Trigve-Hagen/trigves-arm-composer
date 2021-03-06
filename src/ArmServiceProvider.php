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
		
		$this->publishes([
			__DIR__.'/Config/arm.php' => config_path('arm.php'),
		], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
		// Config
		$this->mergeConfigFrom( __DIR__.'/Config/arm.php', 'arm');
		
        $this->app->singleton('Arm::class', function() {
			return Arm();
		});
    }
}
