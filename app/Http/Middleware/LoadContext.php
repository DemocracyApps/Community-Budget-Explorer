<?php namespace DemocracyApps\GB\Http\Middleware;

use Closure;

class LoadContext {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
        $domain = $request->getHttpHost();
		return $next($request);
	}

}
