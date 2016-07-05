<?
session_start();

if (!isset($_SESSION["xls"]) || !isset($_GET['f']))	die;

// Enviamos los encabezados de hoja de calculo
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$_GET['f'].".xls");

echo $_SESSION["xls"];
?>