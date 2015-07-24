<?php namespace App\Http\Middleware;

use Closure;

class ApiSession {
    public function handle($request, Closure $next){
        $path = $request->getPathInfo();

        if(strpos($path, '/api/') === 0){
            \Config::set('session.driver', 'array');
            \Config::set('cookie.driver', 'array');
        }

        return $next($request);
    }
}