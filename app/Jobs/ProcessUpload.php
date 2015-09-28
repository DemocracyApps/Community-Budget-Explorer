<?php

namespace DemocracyApps\GB\Jobs;

use DemocracyApps\GB\Data\DataSource;
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

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    $params = $this->dataSource->getProperty('upload_parameters');

    $url = getenv('CBE_DATASERVER') . '/doit';

    echo "Going to the URL " . $url . PHP_EOL;

    //$url = 'http://gbe.dev:53821/doit';

    $returnValue = CurlUtilities::curlAjaxPost($url, json_encode($params));
    var_dump($returnValue);
  }
}
