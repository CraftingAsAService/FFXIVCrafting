<?php namespace App\Providers;

use Illuminate\Routing\Router;
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
	public function boot(Router $router)
	{
		preg_match("/^(\w\w\.)?(.*)$/", Request::getHost(), $parts);

		$parts[1] = substr($parts[1], 0, 2);

		$is_real = in_array($parts[1], Config::get('site.available_languages'));

		Config::set('language', $is_real ? $parts[1] : Config::get('site.default_language'));

		// English isn't "real", which helps us out here
		Config::set('language_base_url', ($is_real ? $parts[2] : $parts[0]) . '/' . Request::path());

		parent::boot($router);

		// Route Model Binding
		$router->model('item', 'App\Models\Garland\Item');
		$router->model('leve', 'App\Models\Garland\Leve');

		//
	}

	/**
	 * Define the routes for the application.
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 * @return void
	 */
	public function map(Router $router)
	{
		$router->group(['namespace' => $this->namespace], function($router)
		{
			require app_path('Http/routes.php');
		});
	}

}
