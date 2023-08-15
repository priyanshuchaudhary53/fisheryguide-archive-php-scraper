<?php

function placeDetailsJson($liNode)
{
    $placeArr = [];

    foreach ($liNode as $li) {

        $title = null;
        $url = null;
        $slug = null;
        $img = null;

        // title and url
        $titleLink = $li->find('.wp-block-latest-posts__post-title')[0];
        if ($titleLink) {
            $url = $titleLink->getAttribute('href');
            $title = strip_tags($titleLink);
        }

        // slug
        if ($url) {
            $parseUrl = parse_url($url);
            $path = $parseUrl['path'];
            $path = rtrim($path, '/');
            $pathSegements = explode('/', $path);
            $slug = end($pathSegements);
        }

        // image
        $imgTag = $li->find('img')[0];
        if ($imgTag) {
            $img = $imgTag->getAttribute('src');
        }


        $arr = ['name' => $title, 'image' => $img, 'URL' => $url, 'slug' => $slug];
        $json = json_encode($arr);


        $placeArr[] = $json;
    }

    $details = json_encode(['places' => $placeArr]);

    return $details;
}