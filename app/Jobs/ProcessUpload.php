<?php

namespace DemocracyApps\GB\Jobs;

use DemocracyApps\GB\Data\DataSource;
use DemocracyApps\GB\Data\DataUtilities;
use DemocracyApps\GB\Utility\CurlUtilities;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessUpload extends Job implements SelfHandling, ShouldQueue
{
  use InteractsWithQueue, SerializesModels;

  protected $dataSource = null;

  public function __construct(DataSource $dataSource)
  {
    $this->dataSource = $dataSource;
  }

  protected function readCSVFile ($filePath)
  {
    $myFile = false;
    $fileData = null;
    ini_set("auto_detect_line_endings", true); // Deal with Mac line endings
    if ( !file_exists($filePath)) {
      \Log::info("ProcessUpload Job: The file " . $filePath . " does not exist");
    }
    else {
      $myFile = fopen($filePath, "r");
    }
    if (!$myFile) {
      \Log::info("ProcessUpload Job: Unable to open file $filePath");
    }
    else {
      $fileData = [];
      while (!feof($myFile)) {
        $columns = fgetcsv($myFile);
        $fileData[] = $columns;
      }
    }
    return $fileData;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    $params = $this->dataSource->getProperty('upload_parameters');

    $url = DataUtilities::getDataserverEndpoint($params['organization']) . '/api/v1/upload';

    \Log::info("The file path to the data is " . $params['file_path']);
    $fileData = $this->readCSVFile($params['file_path']);
    \Log::info("Going to the URL " . $url . " with file data of length ". sizeof($fileData) . PHP_EOL);

    $params['fileData'] = $fileData;
    $returnValue = CurlUtilities::curlJsonPost($url, json_encode($params));
    \Log::info("What we got in return: " . json_encode($returnValue));
  }
}
