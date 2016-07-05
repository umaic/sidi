<?
//SEGURIDAD
include_once 'seguridad.php';
include_once 'lib/libs_perfil_usuario.php';
include_once 'lib/dao/log.class.php';

//LOGOUT
if (isset($_GET["accion"]) && $_GET["accion"] == "logout"){
	$s = New SessionUsuario();
	$s->Logout('index_undaf.php');
	die;
}

//REGISTRA EL MODULO GENERAL
if (isset($_GET["m_g"])){
	$_SESSION["m_g"] = $_GET["m_g"];
}
else if (!isset($_SESSION["m_g"]) && !isset($_GET["m_g"])){
	$_SESSION["m"] = "";
}
//REGISTRA EL MODULO
if (isset($_GET["m"])){
	$_SESSION["m"] = $_GET["m"];
	$_SESSION["m_e"] = "";
}
else if (!isset($_SESSION["m"]) && !isset($_GET["m"])){
	$_SESSION["m"] = "";
}

//REGISTRA EL MODULO ESPECIFICO
if (isset($_GET["m_e"])){
	$_SESSION["m_e"] = $_GET["m_e"];
}
else if (!isset($_SESSION["m_e"]) && !isset($_GET["m_e"])){
	$_SESSION["m_e"] = "";
}

//LIBRERIAS
switch ($_SESSION["m_e"]){
	case "tipo_usuario":
		include_once("lib/libs_tipo_usuario.php");
		break;
	case "proyecto":
		include_once("lib/libs_proyecto.php");
		include_once ('js/calendar/calendar.php');
		break;
	case "usuario":
		include_once("lib/libs_usuario.php");
		break;
	case "org":
		include_once("lib/libs_org.php");
		break;
}

//INICIALIZACION DE VARIABLES
$accion = "";
if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

if (isset($_GET["class"])){
	$class = $_GET["class"];
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

if (isset($_GET["param"])){
	$param = $_GET["param"];
}
else if (isset($_POST["param"])){
	$param = $_POST["param"];
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html
	xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Sistema de Informaci&oacute;n Central OCHA - Colombia</title>
<?
$style = "consulta_undaf.css";
echo "<link href='../style/$style' rel='stylesheet' type='text/css' />";
?>

<!--[if lte IE 6]>
<![endif]-->

<!--[if lte IE 6]>
<link rel="stylesheet" type="text/css" media="screen" href="../style/ie_admin.css" />    
<link rel="stylesheet" type="text/css" href="../style/consulta_undaf_ie.css" />    
<![endif]-->

<!--[if IE 7]>
<link rel="stylesheet" type="text/css" media="screen" href="../style/ie_admin.css" />    
<link rel="stylesheet" type="text/css" href="../style/consulta_undaf_ie.css" />    
<![endif]-->
<script src="js/general.js"></script>
<script src="js/ajax.js"></script>
</head>

<?
//LOG
if (isset($_POST["submit"]) || $accion == 'borrar'){

	$log = New LogUsuarioDAO();
	$log->RegistrarAdmin();

}
//ACCION DE LA FORMA
if (isset($_POST["submit"])){
	//Controlador
	$ct = New ControladorPagina($_POST["accion"]);
}
?>
<body>


<h1 class="info">Sistema de Informaci&oacute;n Central &ndash; OCHA
&ndash; Naciones Unidas &ndash; Colombia</h1>
<div id="cabecera"></div>
<div id="navgral" align="right"><? include_once("../include/navegaciongeneral_undaf.php"); ?> </div>
<div id="cuerpo">
<?
/*if ($accion != "insertar" && $accion != "actualizar" && $accion != "importar")  {
	echo '<div id="navlat">';
	include_once("include/menulat.php");
	echo "</div>";

}*/
?> <!-- CONTENIDO : INICIO -->
<div id="cont"><?
if ($accion != ""){
	if ($accion == "listar" || $accion == "borrar" || $accion == "reportar" || $accion == "publicar"){
		//ORG
		if ($class == "OrganizacionDAO"){
			$obj = New $class();

			/////LISTA ORGS
			if ($accion == "listar"){
				if (!isset($_POST['consultar']) && !isset($_POST['buscar']) && !isset($_GET['page']) && !isset($_GET['criterio']) && !isset($_GET['col_orden'])){
					include_once($_SESSION["m_e"]."/index.php");
				}
				else{
					$obj->ListarTabla();
				}
			}
			else if ($accion == "publicar"){
				$obj->ListarOrgPublicar();
			}
			else if ($accion == "borrar"){
				$obj->Borrar($param,0);
			}
			else{
				$obj->{$method}($param);
			}
		}
		//PROYECTO
		else if ($class == "ProyectoDAO"){
			$obj = New $class();

			/////LISTA PROYS
			if ($accion == "listar"){
				if (!isset($_POST['consultar']) && !isset($_POST['buscar']) && !isset($_GET['page']) && !isset($_GET['criterio']) && !isset($_GET['col_orden'])){
					include_once($_SESSION["m_e"]."/index.php");
				}
				else{
					$obj->ListarTabla();
				}
			}
			else if ($accion == "borrar"){
				$obj->Borrar($param,0);
			}
			else{
				$obj->{$method}($param);
			}
		}
		else{
			$obj = New $class();
			$obj->{$method}($param);

		}
	}
	else if ($accion == "insertar" || $accion == "actualizar"){
		include_once($_SESSION["m_e"]."/insert.php");
	}
	else if ($accion == "insertar_test"){
		include_once($_SESSION["m_e"]."/insert_test.php");
	}
	else if ($accion == "consultar"){
		include_once("consulta/index.php");
	}
	else if ($accion == "importar"){
		include_once($_SESSION["m_e"]."/importar.php");
	}

}

?></div>
<!-- CONTENIDO : FIN--></div>
<!-- <div id="cierre"><a href="index.php?accion=logout">Salir</a></div> -->
</body>
</html>
