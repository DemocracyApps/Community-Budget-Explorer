<?php namespace DemocracyApps\GB\Console\Commands;
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
use DemocracyApps\GB\Commands\CreateUser;
use DemocracyApps\GB\Organizations\GovernmentOrganization;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Create extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'gb:create';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a user, organization, etc..';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$objectType = $this->argument('object');
		$this->info("Create a new " . $objectType);

		if ($objectType == 'user') {

			$name = $this->ask('Enter the user name: ');
			$email = $this->ask('Enter the user email:');
			$ok = false;
			while (! $ok) {
				$pwd = $this->secret('Password');
				$pwd2 = $this->secret('Enter the password again');
				if ($pwd == $pwd2) $ok = true;
				if (!$ok) $this->error('Passwords do not match. Try again.');
			}
			$this->info("Got the password: " . $pwd);
			$super = $this->ask('Should this user be a superuser? (y/N)');
			$project = $this->ask('Should this user be able to create projects? (y/N)');

			$superUser = false;
			if ($super != null && strtolower($super) == 'y') $superUser = true;
			$this->info("Superuser = " . $superUser);
			$projectCreator = false;
			if ($project != null && strtolower($project) == 'y') $projectCreator = true;
			$this->info("Project creator = " . $projectCreator);
			\Bus::dispatch(new CreateUser($name, $email, $pwd, $projectCreator, $superUser));
		}
		else if ($objectType == 'organization') {
			$name = $this->ask('Enter the organization name: ');
			$org = new GovernmentOrganization();
			$org->name = $name;
			$org->setProperty('abc', 'One world');
			$org->save();
		}
		else if ($objectType == 'test') {
			$thing = GovernmentOrganization::find(1);
			$value = $thing->getProperty('def');
			dd($value);
		}



	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['object', InputArgument::REQUIRED, 'The object type to be created (user, organization, ...).'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['name', null, InputOption::VALUE_REQUIRED, 'The name of the object.', null],
			['super', null, InputOption::VALUE_NONE, 'For users, whether they have superuser privileges'],
			['project', null, InputOption::VALUE_NONE, 'For users, whether they have project creation privileges'],
		];
	}

}
