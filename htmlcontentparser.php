<?php

require 'vendor/autoload.php';

use PHPHtmlParser\Dom\TextNode;

function processHtmlContent($domNode)
{
    $entryContent = $domNode;

    // Remove ul element
    $uls = $entryContent->find('ul');
    foreach ($uls as $ul) {
        $ul->delete();
    }
    unset($uls);

    // Remove ul element
    $tables = $entryContent->find('table');
    foreach ($tables as $table) {
        $table->delete();
    }
    unset($tables);

    // Remove all span elements with id starting with 'ezoic-'
    $ezoicSpans = $entryContent->find('span[id^="ezoic-"]');
    foreach ($ezoicSpans as $span) {
        $span->delete();
    }
    unset($ezoicSpans);

    // Remove ezoicDivs elements
    $ezoicDivs = $entryContent->find('div[id^="ezoic-pub-ad-"]');
    foreach ($ezoicDivs as $div) {
        $div->delete();
    }
    unset($ezoicDivs);

    // Find all 'a' tags in the HTML content
    $aTags = $entryContent->find('a');
    foreach ($aTags as $a) {
        $id = $a->id();
        $innerhtml = $a->innerHtml;
        $textnode = new TextNode($innerhtml);
        $a->getParent()->replaceChild($id, $textnode);
    }
    unset($aTags);

    // Return the modified HTML content
    return $entryContent;
}