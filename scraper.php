<?php

require 'vendor/autoload.php';
require 'location.php';
require 'place.php';

use GuzzleHttp\Client;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\TextNode;

$url_file_path = 'urls.txt';

$urls = [];

$file_handle = fopen($url_file_path, 'r');

// push array with each urls from urls.txt
if ($file_handle) {
    while (($line = fgets($file_handle)) !== false) {
        $urls[] = trim($line);
    }

    fclose($file_handle);
} else {
    echo "Error opening the file.";
}

$db = new PDO('sqlite:database.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (empty($urls)) {
    exit('URLs not found!');
}

$dataAdded = 0;

foreach ($urls as $url) {

    $type = 'collection';

    $stmt = $db->prepare('SELECT COUNT(*) FROM main WHERE source_url = :source_url');
    $stmt->bindParam(':source_url', $url, PDO::PARAM_STR);
    $stmt->execute();
    $rowCount = $stmt->fetchColumn();

    // skip if url already exists in db
    if ($rowCount > 0) {
        continue;
    }

    $httpClient = new Client();
    $res = $httpClient->get($url);
    $htmlString = (string) $res->getBody();
    libxml_use_internal_errors(true);

    $dom = new Dom;
    $dom->loadStr($htmlString);

    $articleDiv = $dom->find('article .inside-article')[0];

    // page title
    $headerDiv = $articleDiv->find('header');
    $titleH1 = $headerDiv->find('h1');
    $titleInnerSpan = $titleH1->find('span');
    foreach ($titleInnerSpan as $span) {
        $span->delete();
    }
    unset($titleInnerSpan);
    $page_title = strip_tags($titleH1);

    // page text
    $entryContentDiv = $articleDiv->find('.entry-content')[0];
    $pageTextP = $entryContentDiv->find('p')[0];
    $pageText_aTags = $pageTextP->find('a');
    foreach ($pageText_aTags as $a) {
        $id = $a->id();
        $innerhtml = $a->innerHtml;
        $textnode = new TextNode($innerhtml);
        $a->getParent()->replaceChild($id, $textnode);
    }
    $page_text = strip_tags($pageTextP);

    // location
    $location = null;
    $locationArr = json_decode($locationJson, true);
    $city = getCity($locationArr, $page_title);
    $county = getCounty($locationArr, $city);
    if ($city & $county) {
        $location = $county . '::' . $city;
    }

    // place details
    $placeUl = $entryContentDiv->find('ul')[0];
    $placeListItem = $placeUl->find('li');
    $placeJson = placeDetailsJson($placeListItem);

    // prepare insert query
    $stmt = $db->prepare('INSERT INTO main (source_url, type, title, page_text, location, place_details) VALUES (:source_url, :type, :title, :page_text, :location, :place_details)');

    // bind parameters
    $stmt->bindParam(':source_url', $url, PDO::PARAM_STR);
    $stmt->bindParam(':type', $type, PDO::PARAM_STR);
    $stmt->bindParam(':title', $page_title, PDO::PARAM_STR);
    $stmt->bindParam(':page_text', $page_text, PDO::PARAM_STR);
    $stmt->bindParam(':location', $location, PDO::PARAM_STR);
    $stmt->bindParam(':place_details', $placeJson, PDO::PARAM_STR);


    $stmt->execute();

    $dataAdded++;

    echo 'Added query for url: ' . $url . PHP_EOL;
}

echo 'Execution finished! ' . $dataAdded . ' new query added into the database.';