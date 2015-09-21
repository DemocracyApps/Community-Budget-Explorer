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
use DemocracyApps\GB\Commands\Command;

use DemocracyApps\GB\User;
use Illuminate\Contracts\Bus\SelfHandling;
use PhpParser\Node\Scalar\String;

class CreateUser extends Command implements SelfHandling {
	protected $name = null;
	protected $email = null;
	protected $password = null;
	protected $super = false;
	protected $project = false;

	/**
	 * Create a new user.
	 *
	 * @param string $name
	 * @param boolean $super
	 * @param boolean $project
	 */
	public function __construct($name, $email, $pwd, $super, $project)
	{
		$this->name = $name;
		$this->email = $email;
		if ($pwd != null) $this->password = \Hash::make($pwd);
		$this->super = $super;
		$this->project = $project;
	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle()
	{
		$user = new User();
		$user->name = $this->name;
		if ($this->email != null) $user->email = $this->email;
		if ($this->password != null) $user->password = $this->password;
		$user->superuser = $this->super;
		$user->projectcreator = $this->project;
		$user->save();
	}

}
