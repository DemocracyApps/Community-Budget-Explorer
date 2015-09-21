<?php namespace DemocracyApps\GB\Http\Middleware;
/**
 *
 * This file is part of the Government Budget Explorer (GBE).
 *
 *  The GBE is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GBE is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the GBE.  If not, see <http://www.gnu.org/licenses/>.
 */
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
