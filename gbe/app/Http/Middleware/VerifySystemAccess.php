<?php namespace DemocracyApps\GB\Http\Middleware;

use Closure;

class VerifySystemAccess {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if (\Auth::guest() || !\Auth::user()->superuser) {
			return redirect('/');
		}

		return $next($request);
	}

}
