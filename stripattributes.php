<?php

require 'vendor/autoload.php';

use PHPHtmlParser\Dom\TextNode;

function stripAttributes($tableNode)
{
    // remove attributes from table
    if ($tableNode->hasAttribute('class')) {
        $tableNode->removeAttribute('class');
    }

    if ($tableNode->hasAttribute('id')) {
        $tableNode->removeAttribute('id');
    }

    // remove attributes from table head
    $tableHead = $tableNode->find('thead')[0];

    if ($tableHead && $tableHead->hasAttribute('class')) {
        $tableHead->removeAttribute('class');
    }

    if ($tableHead && $tableHead->hasAttribute('id')) {
        $tableHead->removeAttribute('id');
    }

    // remove attributes from table body
    $tableBody = $tableNode->find('tbody')[0];

    if ($tableBody && $tableBody->hasAttribute('class')) {
        $tableBody->removeAttribute('class');
    }

    if ($tableBody && $tableBody->hasAttribute('id')) {
        $tableBody->removeAttribute('id');
    }

    // remove attributes from table row
    $tableRow = $tableNode->find('tr');

    foreach ($tableRow as $tr) {
        if ($tr->hasAttribute('class')) {
            $tr->removeAttribute('class');
        }

        if ($tr->hasAttribute('id')) {
            $tr->removeAttribute('id');
        }
    }

    // remove attributes from table heading
    $tableHeading = $tableNode->find('th');

    foreach ($tableHeading as $th) {
        if ($th->hasAttribute('class')) {
            $th->removeAttribute('class');
        }

        if ($th->hasAttribute('id')) {
            $th->removeAttribute('id');
        }
    }

    // remove attributes from table cell
    $tableCell = $tableNode->find('td');

    foreach ($tableCell as $td) {
        if ($td->hasAttribute('class')) {
            $td->removeAttribute('class');
        }

        if ($td->hasAttribute('id')) {
            $td->removeAttribute('id');
        }
    }

    // remove all <font> tags
    $fontTags = $tableNode->find('font');
    foreach ($fontTags as $tag) {
        $id = $tag->id();
        $innerhtml = $tag->innerHtml;
        $textnode = new TextNode($innerhtml);
        $tag->getParent()->replaceChild($id, $textnode);
    }

    return $tableNode;

}