<?php
/*
 * Archivo para ver el perfil html desde el website, se crea en la raiz, para que las imagenes
 * se incluyan correctamente, pues al generarlo estan relativos al index
 */

//LIBRERIAS
include_once("consulta/lib/libs_mapa_i.php");
include_once("admin/lib/common/archivo.class.php");
include_once("admin/lib/dao/log.class.php");

//log fisico para ataques
$log = new LogUsuarioDAO();
$log->insertarLogFisico('ver_perfil_html');
// fin log fisico

//INICIALIZACION DE VARIABLES
$depto_dao = New DeptoDAO();
$mpio_dao = New MunicipioDAO();

//divipola de depto o mpio
$id = $_GET["id"];

$ids = (strlen($id) == 2) ? $depto_dao->GetAllArrayID('') : $mpio_dao->GetAllArrayID('','');

if (ereg("[0-9]{2,5}",$id) && in_array($id,$ids)){

	//path relatico a sissh/index.php
	$file = "perfiles/perfil_$id.htm";
	
	
	?>
	
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Sistema de Informaci&oacute;n Central OCHA - Colombia</title>
	<link href="style/consulta.css" rel="stylesheet" type="text/css" />
	</head>
	<body>
	<div id='menu_regresar'><img src="images/back.gif" />&nbsp;<a href="javascript:history.back(1)">Regresar al mapa</a></div>
	<div id='cont'>
	
	<? include ($file); ?>
	
	</div>
	</body>
	
	</html>
	
<?
}
?>