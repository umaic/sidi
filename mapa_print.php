<?php
session_start();

//SEGURIDAD
if(!isset($_SESSION["id_usuario_s"])){
	header("Location: login.php?m_g=home");
}

include ("consulta/mapserver_print.php");
?>