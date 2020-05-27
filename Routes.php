<?php 

namespace Root;

class Routes 
{

    CONST ROUTES = [
        
        '/api/crawler/jawapos' => [ 'CrawlerController@jawaPos', 'GET' ],
        '/api/crawler/tribun' => [ 'CrawlerController@tribun', 'GET' ],
        '/api/crawler/pantura' => [ 'CrawlerController@pantura', 'GET' ],
        '/api/crawler/wartabromo' => [ 'CrawlerController@warmo', 'GET' ],
        '/api/crawler/jatimtimes' => [ 'CrawlerController@jatimTimes', 'GET' ],
        '/api/crawler/beritajatim' => [ 'CrawlerController@beritaJatim', 'GET' ]

    ];
}