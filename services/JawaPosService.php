<?php

namespace Service;

use Library\DomCrawler;

class JawaPosService
{
    public static function crawler()
    {
        return self::getContent(self::getSitemap());
    }

    public static function getSitemap()
    {
        $date = date('Y-m');
        $request = CurlService::request("https://radarbromo.jawapos.com/sitemap-pt-post-$date.xml");
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

            $contents = $result->find('div.entry-content > p');

            $post_content = '';
            foreach ($contents as $content) {
                $post_content .= $content;
            }

            DatabaseService::createPost([
                'title' => $result->find('h1.entry-title', 0)->plaintext,
                'content' => $post_content,
                'image' => $result->find('figure.single-thumb > img', 0)->src,
                'link' => $sitemap['link'],
                'author' => 'radabromo jawapos',
                'published_at' => $sitemap['published_at']
            ]);
        }

        return true;
    }
}
