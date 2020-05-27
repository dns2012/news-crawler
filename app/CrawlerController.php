<?php

namespace App;

use Config\Response;
use Service\BeritaJatimService;
use Service\JatimTimesService;
use Service\JawaPosService;
use Service\PanturaService;
use Service\TribunService;
use Service\WarmoService;

class CrawlerController
{
    public function index()
    {
        
    }

    public function jawaPos()
    {
        return Response::success(JawaPosService::crawler(), 200);
    }

    public function tribun()
    {
        return Response::success(TribunService::crawler(), 200);
    }

    public function pantura()
    {
        return Response::success(PanturaService::crawler(), 200);
    }

    public function warmo()
    {
        return Response::success(WarmoService::crawler(), 200);
    }

    public function jatimTimes()
    {
        return Response::success(JatimTimesService::crawler(), 200);
    }

    public function beritaJatim()
    {
        return Response::success(BeritaJatimService::crawler(), 200);
    }
}
