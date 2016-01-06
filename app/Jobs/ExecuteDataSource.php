<?php namespace DemocracyApps\GB\Jobs;

use DemocracyApps\GB\Data\DataUtilities;
use DemocracyApps\GB\Utility\CurlUtilities;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExecuteDataSource extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $parameters = null;

    public function __construct($parameters)
    {
        $this->parameters = $parameters;
    }

    public function handle()
    {
        $url = DataUtilities::getDataserverEndpoint($this->parameters->organization).'/api/v1/datasources/'.$this->parameters->datasource.'/execute';
        $returnValue = CurlUtilities::curlJsonGet($url);
        \Log::info("What we got in return: " . json_encode($returnValue));
    }
}