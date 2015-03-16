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
use DemocracyApps\GB\Accounts\Dataset;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Validate extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'gb:validate';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Validate a dataset file .';

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
        $filePath = $this->argument('input-file');
        $chart = $this->argument('chart-id');
        $messages = Dataset::validateCSVInput($filePath, $chart);
        foreach ($messages as $msg) {
            echo $msg . PHP_EOL;
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
            ['chart-id', InputArgument::REQUIRED, 'Chart ID.'],
            ['input-file', InputArgument::REQUIRED, 'Input data file.'],
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
			['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}

}
