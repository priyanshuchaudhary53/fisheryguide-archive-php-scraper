<?php

require 'vendor/autoload.php';
require 'location.php';
require 'place.php';
require 'htmlcontentparser.php';
require 'stripattributes.php';

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
    $page_title = null;
    $headerDiv = $articleDiv->find('header');
    if ($headerDiv) {
        $titleH1 = $headerDiv->find('h1');
        if ($titleH1) {
            $titleInnerSpan = $titleH1->find('span');
            foreach ($titleInnerSpan as $span) {
                $span->delete();
            }
            unset($titleInnerSpan);
            $page_title = strip_tags($titleH1);
        }
    }


    // location
    $location = null;
    if ($page_title && $page_title !== '') {
        $location = findLocation($page_title);
    }

    // place details
    $placeJson = null;
    $entryContent = $dom->find('.entry-content')[0];
    $placeUl = $entryContent->find('ul')[0];
    if ($placeUl) {
        $placeListItem = $placeUl->find('li');
        $placeJson = placeDetailsJson($placeListItem);
    }

    // table
    $table = null;
    $entryContent = $dom->find('.entry-content')[0];
    $tableTag = $entryContent->find('table')[0];
    if ($tableTag) {
        $table = stripAttributes($tableTag);
    }

    // page text
    $page_text = null;
    $entryContentDiv = $articleDiv->find('.entry-content')[0];
    if ($entryContentDiv) {
        $pageHtml = processHtmlContent($entryContentDiv);
        $page_text = trim(strip_tags($pageHtml->innerHtml));
    }

    // prepare insert query
    $stmt = $db->prepare('INSERT INTO main (source_url, type, title, page_text, location, place_details, html_table) VALUES (:source_url, :type, :title, :page_text, :location, :place_details, :html_table)');

    // bind parameters
    $stmt->bindParam(':source_url', $url, PDO::PARAM_STR);
    $stmt->bindParam(':type', $type, PDO::PARAM_STR);
    $stmt->bindParam(':title', $page_title, PDO::PARAM_STR);
    $stmt->bindParam(':page_text', $page_text, PDO::PARAM_STR);
    $stmt->bindParam(':location', $location, PDO::PARAM_STR);
    $stmt->bindParam(':place_details', $placeJson, PDO::PARAM_STR);
    $stmt->bindParam(':html_table', $table, PDO::PARAM_STR);


    $stmt->execute();

    $dataAdded++;

    echo 'Added query for url: ' . $url . PHP_EOL;
}

echo 'Execution finished! ' . $dataAdded . ' new query added into the database.';