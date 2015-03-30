<?php namespace DemocracyApps\GB\Http\Middleware;

use Closure;
use DemocracyApps\GB\Organizations\MediaOrganization;

class VerifyMediaAccess {

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

        $mediaOrg = null;

        $id = $request->segment(2);
        if (is_integer($id)) {
            $mediaOrg = MediaOrganization::find($id);
        }

        if ($mediaOrg != null) {

            if (!$mediaOrg->userHasAccess(\Auth::user(), 9)) {
                return redirect('/');
            }
        }
        else {
            // A superuser may be creating a new media company, so let it pass
            if (!\Auth::user()->superuser) {
                return redirect ('/');
            }
        }
        return $next($request);
    }

}