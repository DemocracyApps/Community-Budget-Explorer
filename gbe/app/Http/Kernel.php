<?php namespace DemocracyApps\GB\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel {

	/**
	 * The application's global HTTP middleware stack.
	 *
	 * @var array
	 */
	protected $middleware = [
		'Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode',
		'Illuminate\Cookie\Middleware\EncryptCookies',
		'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
		'Illuminate\Session\Middleware\StartSession',
		'Illuminate\View\Middleware\ShareErrorsFromSession',
		'DemocracyApps\GB\Http\Middleware\VerifyCsrfToken',
	];

	/**
	 * The application's route middleware.
	 *
	 * @var array
	 */
	protected $routeMiddleware = [
		'auth' => 'DemocracyApps\GB\Http\Middleware\Authenticate',
		'auth.basic' => 'Illuminate\Auth\Middleware\AuthenticateWithBasicAuth',
		'guest' => 'DemocracyApps\GB\Http\Middleware\RedirectIfAuthenticated',
        'cnp.auth' => 'DemocracyApps\GB\Http\Middleware\VerifyLoggedIn',
        'cnp.admin' => 'DemocracyApps\GB\Http\Middleware\VerifyAdminAccess',
        'cnp.project' => 'DemocracyApps\GB\Http\Middleware\VerifyProjectAccess',
        'cnp.system' => 'DemocracyApps\GB\Http\Middleware\VerifySystemAccess',
	];

}
