<?php namespace DemocracyApps\GB\Http\Middleware;

use Closure;
use DemocracyApps\GB\Organizations\GovernmentOrganization;

class VerifyGovernmentAccess {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (\Auth::guest()) return redirect()->guest('/auth/login');

        $government = null;

        $id = $request->segment(2);
        \Log::info("In verifyGovernment with id " . $id);
        if (is_integer($id)) {
            $government = GovernmentOrganization::find($id);
        }

        if ($government != null) {

            if (!$government->userHasAccess(\Auth::user(), 9)) {
                return redirect('/');
            }
        }
        else {
            // A superuser may be creating a new government, so let it pass
            if (!\Auth::user()->superuser) {
                return redirect ('/');
            }
        }
        return $next($request);
    }

}
