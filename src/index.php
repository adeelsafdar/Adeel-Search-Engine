<?php 

namespace adeel\searchengine;

class SearchEngine
{

    public $searchengine = '';

    public function setEngine($searchengine = "google.com")
    {
        $this->searchengine = $searchengine;
    }

    public function search($keywords = array('best game'))
    {
         require_once "vendor/autoload.php";
         $httpClient = new \GuzzleHttp\Client();
        $response = $httpClient->get('https://www.google.com/search?q=best+hosting');
        $htmlString = (string) $response->getBody();

        echo "<pre>";
        print_r($htmlString);
        echo "</pre>";
    }
}