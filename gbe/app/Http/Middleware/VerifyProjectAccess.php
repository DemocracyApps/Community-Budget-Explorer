<?php namespace DemocracyApps\GB\Http\Middleware;

use Closure;
use DemocracyApps\CNP\Project\Project;

class VerifyProjectAccess {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$projectId = $request->segment(1);
		$project = Project::find($projectId);
		if ($project == null) {
			return view('admin.project404', array('project' => $projectId));
		}
		else { // They must at least have view access
			// the 'authorize' route is special - allow them in, even if not confirmed
			if ($request->segment(2) != 'authorize') {
				$access = $project->viewAuthorization(\Auth::id());
				if (!$access->allowed) {
					if ($access->reason == 'authorization') {
						return redirect()->guest('/' . $projectId . '/authorize');
					} else {
						return redirect()->guest('/user/noconfirm');
					}
				}
			}
		}
		return $next($request);
	}

}
