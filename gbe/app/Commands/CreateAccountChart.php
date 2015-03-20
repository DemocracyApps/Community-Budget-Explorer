<?php namespace DemocracyApps\GB\Commands;
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

use DemocracyApps\GB\Accounts\AccountChart;
use DemocracyApps\GB\Organization;
use DemocracyApps\GB\User;
use Illuminate\Contracts\Bus\SelfHandling;

class CreateAccountChart extends Command implements SelfHandling {
    protected $name = null;
    protected $organization;


    /**
     * @param string $name
     * @param string $description
     * @param User $admin
     */
    public function __construct($name, $organization)
    {
        $this->name = $name;
        $this->description = $organization;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $chart = new AccountChart();
        $chart->name = $this->name;
        $chart->organization = $this->organization->id;
        $chart->save();
    }

}
