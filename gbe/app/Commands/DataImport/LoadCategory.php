<?php namespace DemocracyApps\GB\Commands\DataImport;

use DemocracyApps\GB\Accounts\AccountCategory;
use DemocracyApps\GB\Commands\Command;

use Illuminate\Contracts\Bus\SelfHandling;

class LoadCategory extends Command implements SelfHandling {
    private $filePath;
    private $categoryId;
    private $deleteFile;

    /**
     * Create a new command instance.
     *
     * @param $filePath
     * @param $chartId
     * @param bool $deleteFile
     */
    public function __construct($filePath, $categoryId, $deleteFile = false)
    {
        $this->filePath = $filePath;
        $this->categoryId = $categoryId;
        $this->deleteFile = $deleteFile;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $msgs = AccountCategory::processCSVInput($this->filePath, $this->categoryId);
        if ($msgs != null) \Log::info($msgs);
        if ($this->deleteFile) unlink($this->filePath);
    }

}