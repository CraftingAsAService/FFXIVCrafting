<?php

namespace App\Http\Middleware;

use Closure;

class ThemeDetection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $theme = $request->get('theme');
        if (in_array($theme, ['light', 'dark']))
            session(['theme' => $theme]);

        return $next($request);
    }
}
