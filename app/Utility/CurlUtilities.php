<?php namespace DemocracyApps\GB\Utility;


class CurlUtilities
{

  public static function curlJsonPost ($url, $jsonContent)
  {

    $session = curl_init($url);
//    curl_setopt($session, CURLOPT_HEADER, false);
//    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
//    curl_setopt($session, CURLOPT_POST, 1);
//    curl_setopt($session, CURLOPT_POSTFIELDS, $jsonContent);
    curl_setopt($session, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($session, CURLOPT_POSTFIELDS, $jsonContent);
    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($session, CURLOPT_TIMEOUT, 0);
    curl_setopt($session, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonContent))
    );

    $returnValue = curl_exec($session);
    curl_close($session);
    return $returnValue;
  }
}