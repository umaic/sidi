<?
session_start();

//SEGURIDAD
if (count($_GET) == 0){
	if(!isset($_SESSION["id_usuario_s"])){
		header("Location: login.php?m_g=home");
	}
}

//die("Realizando ajustes.....");
include("consulta/mapserver_ajax.php");
?>
