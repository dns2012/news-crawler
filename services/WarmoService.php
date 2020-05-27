<?php

namespace Service;

use Library\DomCrawler;

class WarmoService
{
    public static function crawler()
    {
        return self::getContent(self::getSitemap());
    }

    public static function getSitemap()
    {
        $request = CurlService::request("https://www.wartabromo.com/post-sitemap24.xml");
        $parseRequest = simplexml_load_string($request);

        $index = 0;
        $sitemaps = [];
        foreach ($parseRequest as $parsed) {
            $sitemaps[] = [
                'link' => (string) $parsed->loc,
                'published_at' => strtotime($parsed->lastmod)
            ];
            $index += 1;
            if ($index >= 20) {
                break;
            }
        }

        return $sitemaps;
    }

    public static function getContent($sitemaps = [])
    {
        foreach ($sitemaps as $sitemap) {
            $request = CurlService::request($sitemap['link']);
            $dom = new DomCrawler;
            $result = $dom->str_get_html($request);

            $contents = $result->find('div.td-post-content > p');

            $post_content = '';
            foreach ($contents as $content) {
                $post_content .= $content;
            }

            DatabaseService::createPost([
                'title' => $result->find('h1.entry-title', 0)->plaintext,
                'content' => $post_content,
                'image' => $result->find('img.entry-thumb.td-modal-image', 0)->src,
                'link' => $sitemap['link'],
                'author' => 'warta bromo',
                'published_at' => $sitemap['published_at']
            ]);
        }

        return true;
    }
}
