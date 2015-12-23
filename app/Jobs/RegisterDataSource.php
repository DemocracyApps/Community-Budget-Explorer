<?php

namespace DemocracyApps\GB\Jobs;

use DemocracyApps\GB\Data\DataSource;
use DemocracyApps\GB\Data\DataUtilities;
use DemocracyApps\GB\Utility\CurlUtilities;
use DemocracyApps\GB\Organizations\GovernmentOrganization;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisterDataSource extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $dataSource = null;

    public function __construct(DataSource $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    public function handle()
    {
        $parameters = $this->dataSource->getProperty('source_parameters');
        $parameters['sourceType'] = $this->dataSource->source_type;
        $parameters['datasource'] = $this->dataSource->name;
        $parameters['datasourceId'] = $this->dataSource->id;
        $organization = GovernmentOrganization::find($this->dataSource->organization);
        $parameters['entity'] = $organization->name;
        $parameters['entityId'] = $organization->id;

        $url = DataUtilities::getDataserverEndpoint($organization->id) . '/api/v1/register_data_source';

        \Log::info("The parameters for registering the datasource are " . json_encode($parameters));
        $returnValue = CurlUtilities::curlJsonPost($url, json_encode($parameters));
        \Log::info("What we got in return: " . json_encode($returnValue));
    }
}
