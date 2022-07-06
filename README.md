# Adeel Search Engine
 
Installation:

1) Open cmd as Administrator
2) Goto to root directory
3) Create an empty file called "composer.json"
4) Run "composer require adeel/searchenginer:dev-main" command

Usage:

After installation include following lines in your respected php file.

require_once __DIR__ . '/vendor/autoload.php';
use adeel\searchengine\SearchEngine;

$client = new SearchEngine();

//You can set your search engine here
$client->setEngine("google.com");

//Pass your keywords as an array in the following line
$results = $client->search(["best hosting","best movie"]);

echo "<pre>";

// Your results will be available in $results variable as an array having the status of the operation and the data retrieved
var_dump($results); 

echo "</pre>";