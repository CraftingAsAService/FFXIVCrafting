<?php

namespace App\Providers;

use Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

	/**
	 * Register any application services.
	 *
	 * This service provider is a great spot to register your various container
	 * bindings with the application. As you can see, we are registering our
	 * "Registrar" implementation here. You can add your own bindings too!
	 *
	 * @return void
	 */
	public function register()
	{
		// $this->app->bind(
		// 	'Illuminate\Contracts\Auth\Registrar',
		// 	'App\Services\Registrar'
		// );

		/*
		// Thanks to `hedronium/spaceless-blade`
        // Register @spaceless, the Starting Tag
        Blade::directive('spaceless', function() {
            return '<?php ob_start() ?>';
        });
        // Register @endspaceless, the Ending Tag
        Blade::directive('endspaceless', function() {
            return "<?php echo preg_replace('/>\\s+</', '><', ob_get_clean()); ?>";
        });
        */
	}

}
