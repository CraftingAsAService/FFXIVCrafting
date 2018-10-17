<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Config;
use Request;

class RouteServiceProvider extends ServiceProvider {

	/**
	 * This namespace is applied to the controller routes in your routes file.
	 *
	 * In addition, it is set as the URL generator's root namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'App\Http\Controllers';

	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 * @return void
	 */
	public function boot()
	{
		preg_match("/^(\w\w\.)?(.*)$/", Request::getHost(), $parts);

		$parts[1] = substr($parts[1], 0, 2);

		$is_real = in_array($parts[1], Config::get('site.available_languages'));

		Config::set('language', $is_real ? $parts[1] : Config::get('site.default_language'));

		// English isn't "real", which helps us out here
		Config::set('language_base_url', ($is_real ? $parts[2] : $parts[0]) . '/' . Request::path());

		parent::boot();

		// Route Model Binding
		// $router->model('item', 'App\Models\Garland\Item');
		// $router->model('leve', 'App\Models\Garland\Leve');

		//
	}

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }

}
