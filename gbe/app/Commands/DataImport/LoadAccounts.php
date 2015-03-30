<?php namespace DemocracyApps\GB\Commands\DataImport;

use DemocracyApps\GB\Budget\Account;
use DemocracyApps\GB\Commands\Command;

use Illuminate\Contracts\Bus\SelfHandling;

class LoadAccounts extends Command implements SelfHandling {
    private $filePath;
    private $chartId;
    private $deleteFile;

    /**
     * Create a new command instance.
     *
     * @param $filePath
     * @param $chartId
     * @param bool $deleteFile
     */
	public function __construct($filePath, $chartId, $deleteFile = false)
	{
        $this->filePath = $filePath;
        $this->chartId = $chartId;
        $this->deleteFile = $deleteFile;
    }

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle()
	{
        $msgs = Account::processCSVInput($this->filePath, $this->chartId);
        if ($msgs != null) \Log::info($msgs);
        if ($this->deleteFile) unlink($this->filePath);
    }

}
