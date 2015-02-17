<?php namespace DemocracyApps\GB\Console\Commands;

use DemocracyApps\GB\Import\DataProcessor;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Process extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'gb:process';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Process input file and import to DB or output to another file.';

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
		$filename = $this->argument('input-file');
        $outputFile = $this->argument('output-file');
		$instructions = $this->option('instructions');
		$this->info("Processing " . $filename);
		$dp = new DataProcessor($filename, $instructions, $outputFile);
		$dp->run();
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
            ['input-file', InputArgument::REQUIRED, 'Input file to be processed.'],
            ['output-file', InputArgument::REQUIRED, 'Output file.'],
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
			['instructions', null, InputOption::VALUE_REQUIRED, 'File with processing instructions.', null],
			['org', null, InputOption::VALUE_REQUIRED, 'Organization to associate data with', null],
		];
	}

}
