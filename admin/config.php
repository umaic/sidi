<?php

//Variables para Perfil
$conf['perfil']['dias_cache'] = 15;  //Dias en que se mantiente el cache

//Variables Mapas interfaz consulta
$conf['mapserver']['dir_cache_consulta'] = $_SERVER["DOCUMENT_ROOT"]."/sissh/images/cache_mapserver/consulta";
$conf['mapserver']['dir_cache_perfil'] = $_SERVER["DOCUMENT_ROOT"]."/sissh/images/cache_mapserver/perfil";

// Cache mapas tematicos
$conf['mapserver']['dir_cache_tematico'] = $_SERVER["DOCUMENT_ROOT"]."/sissh/images/cache_mapserver/tematico";

//Variables Proyectos
$conf['proyecto']['dir_cache'] = $_SERVER["DOCUMENT_ROOT"]."/sissh/consulta/cache_proyecto";

//Static cache
$conf['static_cache'] = $_SERVER["DOCUMENT_ROOT"]."/sissh/static/";

//Variables 4W
$conf['4w']['horas_cache'] = 2;  //Horas cache de paginas estaticas, como home 4w
?>
