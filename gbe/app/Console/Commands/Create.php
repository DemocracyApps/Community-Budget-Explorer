<?php namespace DemocracyApps\GB\Console\Commands;

use DemocracyApps\GB\Commands\CreateUser;
use DemocracyApps\GB\Organization;
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
			$org = new Organization();
			$org->name = $name;
			$org->save();
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
