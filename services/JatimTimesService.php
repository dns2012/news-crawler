<?php

namespace Service;

use Library\DomCrawler;

class JatimTimesService
{
    public static function crawler()
    {
        return self::getContent(self::getSitemap());
    }

    public static function getSitemap()
    {
        $request = CurlService::request("https://jatimtimes.com/sitemap.xml");
        $parseRequest = simplexml_load_string($request);

        $index = 0;
        $sitemaps = [];
        foreach ($parseRequest->channel->item as $parsed) {
            $sitemaps[] = [
                'title' => (string) $parsed->title,
                'link' => (string) $parsed->link,
                'published_at' => strtotime($parsed->pubDate),
                'image' => (string) str_replace('.th.', '.md.', $parsed->description->img['src']) 
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

            $contents = $result->find('div.isi > p');

            $post_content = '';
            foreach ($contents as $content) {
                $post_content .= $content;
            }

            DatabaseService::createPost([
                'title' => $sitemap['title'],
                'content' => $post_content,
                'image' => $sitemap['image'],
                'link' => $sitemap['link'],
                'author' => 'jatim times',
                'published_at' => $sitemap['published_at']
            ]);
        }

        return true;
    }
}
