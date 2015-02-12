<?php namespace DemocracyApps\GB\Commands;

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
