<?
@session_start();
//SEGURIDAD
include_once 'lib/libs_perfil_usuario.php';
include_once 'lib/dao/log.class.php';

//REGISTRA EL MODULO ESPECIFICO
if (isset($_GET["m_e"])){
	$_SESSION["m_e"] = $_GET["m_e"];
}
else if (!isset($_SESSION["m_e"]) && !isset($_GET["m_e"])){
	$_SESSION["m_e"] = "";
}

//LIBRERIAS
if ($_SESSION["m_e"] != 'home'){
	switch ($_SESSION["m_e"]){
		default:
			if (isset($_SESSION["m_e"]) && $_SESSION["m_e"] != ''){
				include_once($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/libs_".$_SESSION["m_e"].".php");
            }
		break;
	}
}

// Para la nueva interfaz de administración con Ajax, es necesario decodificar UTF-8 todo lo que sean enviado... AJAX codifica todo en utf-8
function decodeUTF8($array) {
 
        foreach ($array as $k => $postTmp) {
                if (is_array($postTmp)) {
                        $array[$k]= decodeUTF8($postTmp);
                }else{
                        $array[$k] = utf8_decode($postTmp);
                }
        }
 
        return $array;
}
 
$_POST = decodeUTF8($_POST);


//ACCION DE LA FORMA
if (isset($_POST["submit"])){

	$accion = $_POST["accion"];

	//Controlador
	$ct = New ControladorPagina($accion);
	
	if ($accion == 'actualizar'){

		echo 'Registro actualizado con &eacute;xito';
	}

	return;
}
//INICIALIZACION DE VARIABLES
$accion = "";
if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

if (isset($_GET["class"]) || isset($_GET["class_v"])){
	$class = isset($_GET["class"]) ? $_GET["class"] : $_GET["class_v"];
}
else if (isset($_POST["class"])){
	$class = $_POST["class"];
}

if (isset($_GET["method"])){
	$method = $_GET["method"];
}
else if (isset($_POST["method"])){
	$method = $_POST["method"];
}

$param = '';
if (isset($_GET["param"])){
	$param = $_GET["param"];
}
else if (isset($_POST["param"])){
	$param = $_POST["param"];
}

?>

<?php
if ($accion != ""){
    if ($accion == "listar" || $accion == "borrar" || $accion == "reportar"){
		$obj = New $class();
		$obj->{$method}($param);
	
        // Alerta de borrado exitoso
        if ($accion == 'borrar'){
            echo 'Registro eliminado con &eacute;xito';
            return;
        }
	}
	else if ($accion == "insertar" || $accion == "actualizar"){
        echo '<div id="cont">';
		include_once($_SERVER['DOCUMENT_ROOT'].'/sissh/admin/'. $_SESSION["m_e"].'/insert.php');
	}
	else if ($accion == "consultar"){
        echo '<div id="cont">';
		include_once("consulta/index.php");
	}

}
?>
