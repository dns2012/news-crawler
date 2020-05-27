<?php

namespace Service;

use Library\DomCrawler;

class TribunService
{
    public static function crawler()
    {
        return self::getContent(self::getSitemap());
    }

    public static function getSitemap()
    {
        $request = CurlService::request("https://jatim.tribunnews.com/jatim/sitemap_news.xml");

        $dom = new DomCrawler;
        $result = $dom->str_get_html($request);
        $locs = $result->find('loc');

        $sitemaps = [];
        foreach ($locs as $index => $loc) {
            if ($index >= 20) {
                break;
            }
            
            $explodePreLink = explode('<![CDATA[', $loc->plaintext);
            $explodePostLink = explode(']]>', $explodePreLink[1]);

            $explodePreTitle = explode('<![CDATA[', $result->find('news:title')[$index]->plaintext);
            $explodePostTitle = explode(']]>', $explodePreTitle[1]);

            $explodePublishedAt = explode('<![CDATA[', $result->find('news:publication_date')[$index]->plaintext);
            $explodePublishedAt = explode(']]>', $explodePublishedAt[1]);

            $sitemaps[] = [
                'link' => $explodePostLink[0],
                'title' => $explodePostTitle[0],
                'published_at' => strtotime($explodePublishedAt[0])
            ];
        }

        return $sitemaps;
    }

    public static function getContent($sitemaps = [])
    {
        foreach ($sitemaps as $sitemap) {
            $request = CurlService::request($sitemap['link']);
            $dom = new DomCrawler;
            $result = $dom->str_get_html($request);

            $contents = $result->find('div.side-article.txt-article > p');

            $post_content = '';
            foreach ($contents as $content) {
                $post_content .= $content;
            }

            DatabaseService::createPost([
                'title' => $sitemap['title'],
                'content' => $post_content,
                'image' => $result->find('img.imgfull', 0)->src,
                'link' => $sitemap['link'],
                'author' => 'tribun jatim',
                'published_at' => $sitemap['published_at']
            ]);
        }

        return true;
    }
}
