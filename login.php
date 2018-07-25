<?
session_start();

include_once("admin/lib/common/mysqldb.class.php");
include_once("admin/lib/common/sessionusuario.class.php");

//MODEL

//DAO

//INICIALIZCION DE VARIABLES

//REGISTRA EL MODULO GENERAL
if (!isset($_SESSION["m_g"]) || $_SESSION["m_g"] == ""){

	$_SESSION["m_g"] = (isset($_GET["m_g"])) ? $_GET["m_g"] : "";
}

//AUTH0
$vu = New SessionUsuario();
$vu->ValidarUsuarioAuth0('index.php?m_e=home','login.php');
exit;

?>