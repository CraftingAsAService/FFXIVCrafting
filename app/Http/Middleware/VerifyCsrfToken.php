<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		// Only enable CSRF on these routes:
		$enable_for = [
			// Frankly I'm just disabling it completely for now
			// 'path/to/route'
		];

		foreach ($enable_for as $route)
			if ($request->is($route))
				return parent::handle($request, $next);

		return parent::addCookieToResponse($request, $next($request));
	}

}