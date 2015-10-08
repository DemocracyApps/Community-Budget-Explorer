<?php namespace DemocracyApps\GB\Data;


class DataUtilities
{
  public static function getDataserverEndpoint($organizationId)
  {
    return getenv('CBE_DATASERVER');

  }
}