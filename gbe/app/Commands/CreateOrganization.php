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

use DemocracyApps\GB\Organization;
use DemocracyApps\GB\User;
use Illuminate\Contracts\Bus\SelfHandling;

class CreateOrganization extends Command implements SelfHandling {
    protected $name = null;
    protected $slug = null;
    protected $description;
    protected $admin;


    /**
     * @param string $name
     * @param string $description
     * @param User $admin
     */
    public function __construct($name, $slug, $description, $admin)
    {
        $this->name = $name;
        $this->slug = $slug;
        $this->description = $description;
        $this->admin = $admin;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $org = new Organization();
        $org->name = $this->name;
        $org->slug = $this->slug;
        $org->description = $this->description;
        $org->save();
        $org->addMember($this->admin, 9);

    }

}
