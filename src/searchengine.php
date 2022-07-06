<?php
namespace adeel\searchengine;
use DOMDocument;
use DOMXPath;

class SearchEngine
{

    public $searchengine = '';

    public function setEngine($searchengine = "google.com")
    {
        $this->searchengine = $searchengine;
    }

    public function search($keywords = array(
        'best game'
    ))
    {
        require_once "vendor/autoload.php";
        $httpClient = new \GuzzleHttp\Client();

        $combined = array();
        $combinedarr_count = 0;
        

        foreach ($keywords as $key => $keyword)
        {

            $ranking_count = 1;
            $searchdescriptionsarr = array();

            $response = $httpClient->get($this->searchengine . '/search?q=' . $keyword . '', ['http_errors' => false]);
            $statuscode = $response->getStatusCode();

            if (200 === $statuscode)
            {

            }
            elseif (304 === $statuscode)
            {
                return array(
                    'status' => 'error',
                    'message' => "304 Not Modified"
                );
                die();
            }
            elseif (404 === $statuscode)
            {
                return array(
                    'status' => 'error',
                    'message' => "Page not Found"
                );
                die();
            }
            else
            {
                return array(
                    'status' => 'error',
                    'message' => "Something went wrong"
                );
                die();
            }

            for ($pagestart = 0;$pagestart <= (10 * 5);$pagestart += 10)
            {
                $response = $httpClient->get($this->searchengine . '/search?q=' . $keyword . '&start=' . $pagestart, ['http_errors' => false]);

                $htmlString = (string)$response->getBody();

                libxml_use_internal_errors(true);
                $doc = new DOMDocument();
                $doc->loadHTML($htmlString);
                $xpath = new DOMXPath($doc);

                $adstitles = $xpath->evaluate('//*[@id="main"]//div//div//div//div//a//div[@role="heading"]//span');
                $adsdescriptions = $xpath->evaluate('//*[@id="main"]//div//div//div//div[@class="w1C3Le"]//div//div');
                $adslinks = $xpath->evaluate('//*[@id="main"]//div//div//div//div//a//div//span[@role="text"]');

                $searchtitles = $xpath->evaluate('//*[@id="main"]//div//div//div[@class="egMi0 kCrYT"]//a//h3[@class="zBAuLc l97dzf"]//div');
                $searchdescriptions = $xpath->evaluate('//*[@id="main"]/div/div[@class="Gx5Zad fP1Qef xpd EtOod pkphOe"]/div[@class="kCrYT"]/div/div[@class="BNeawe s3v9rd AP7Wnd"]/div');
                $searchlinks = $xpath->evaluate('//*[@id="main"]//div//div//div[@class="egMi0 kCrYT"]//a//@href');

                foreach ($searchdescriptions as $key => $single)
                {
                    if (!in_array($single->textContent, array(
                        ' · '
                    )))
                    {
                        $searchdescriptionsarr[] = $single->textContent;
                    }
                }

                foreach ($adstitles as $key => $single)
                {
                    $combined[$combinedarr_count]['keyword'] = $keyword;
                    $combined[$combinedarr_count]['title'] = $single->textContent;
                    $combined[$combinedarr_count]['description'] = $adsdescriptions[$key]->textContent;
                    $combined[$combinedarr_count]['link'] = $adslinks[$key]->textContent;
                    $combined[$combinedarr_count]['promoted'] = 1;
                    $combined[$combinedarr_count]['ranking'] = $ranking_count;

                    $combinedarr_count++;
                    $ranking_count++;
                }

                foreach ($searchtitles as $key => $single)
                {
                    $combined[$combinedarr_count]['keyword'] = $keyword;
                    $combined[$combinedarr_count]['title'] = $single->textContent;
                    $combined[$combinedarr_count]['description'] = $searchdescriptionsarr[$key];
                    $combined[$combinedarr_count]['link'] = str_replace('/url?q=', '', $searchlinks[$key]->textContent);
                    $combined[$combinedarr_count]['promoted'] = 0;
                    $combined[$combinedarr_count]['ranking'] = $ranking_count;

                    $combinedarr_count++;
                    $ranking_count++;
                }

            }

        }

        return array(
            'status' => 'success',
            'data' => $combined
        );

    }
}

