<?php

namespace Service;

use Library\DomCrawler;

class BeritaJatimService
{
    public static function crawler()
    {
        return self::getContent(self::getSitemap());
    }

    public static function getSitemap()
    {
        $date = date('Y-m');
        $request = CurlService::request("https://beritajatim.com/sitemap-pt-post-$date.xml");
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
                'image' => $result->find('div.post-thumbnail > img', 0)->src,
                'link' => $sitemap['link'],
                'author' => 'berita jatim',
                'published_at' => $sitemap['published_at']
            ]);
        }

        return true;
    }
}
