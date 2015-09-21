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
use DemocracyApps\GB\Sites\Site;

class VerifySiteAdminAccess {

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
        $slug = $request->segment(2);
        \Log::info("The segment is " . $slug);
        $site = Site::where('slug','=',$slug)->first();


        if ($site != null) {
            if (!$site->userHasAccess(\Auth::user(), 9)) {
                return redirect('/');
            }
        }
        else {
            return redirect ('/');
        }
        return $next($request);



        return $next($request);
    }

}