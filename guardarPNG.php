<?php
//No seamos locos, esto es para que IE muestre el dialogo de Guardar sin errores
if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
    session_cache_limiter("public");
}
session_start();

//LIBRERIAS
include("admin/lib/common/archivo.class.php");


//INICIALIZACION DE VARIABLES
$archivo = New Archivo();
$filename = str_replace(" ","_",$_SESSION["titulo_grafica"]) ."-OCHA-Colombia";


header("Content-Type: octet/stream");
header("Content-Disposition: attachment; filename=$filename.png");

$im = $GLOBALS['HTTP_RAW_POST_DATA'];

echo $im;
?>