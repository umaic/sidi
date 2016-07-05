<?php
//SEGURIDAD
include_once 'seguridad.php';
include_once 'lib/libs_perfil_usuario.php';
include_once 'lib/dao/log.class.php';

//LOGOUT
if (isset($_GET["accion"]) && $_GET["accion"] == "logout"){
	$s = New SessionUsuario();
	$s->Logout();
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

if ($_SESSION["m_e"] != 'home'){
	switch ($_SESSION["m_e"]){
		case "evento":
			include_once("lib/libs_evento.php");
			include_once ('js/calendar/calendar.php');
			break;
		case "perfil_usuario":
			include_once("lib/control/ctlperfil_usuario.class.php");
			break;
		case "proyecto":
			include_once("lib/libs_proyecto.php");
			include_once ('js/calendar/calendar.php');
			break;
		case "dato_sectorial":
			include_once("lib/libs_dato_sectorial.php");
			include_once ('js/calendar/calendar.php');
			break;
		case "dato_s_valor":
			include_once("lib/libs_dato_sectorial.php");
			break;
		case "log_consulta":
			include_once("lib/libs_log.php");
			break;
		case "log_admin":
			include_once("lib/libs_log.php");
			break;
		default:
			if (isset($_SESSION["m_e"]) && $_SESSION["m_e"] != '' && $_SESSION["m_e"] != 'tabla_grafico' && $_SESSION['m_e'] != 'mapa')
				include_once("lib/libs_".$_SESSION["m_e"].".php");
		break;
	}
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

$param = '';
if (isset($_GET["param"])){
	$param = $_GET["param"];
}
else if (isset($_POST["param"])){
	$param = $_POST["param"];
}

$title = "Sistema Integrado de Informaci&oacute;n Transversal de Colombia";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $title ?></title>
<link href="../favicon.ico" rel="shortcut icon" type="image/x-icon" />
<?php
//CSS
switch ($_SESSION["m_g"]){
	case "admin":
        ?>

		<link href='../style/admin.css' rel='stylesheet' type='text/css' />
		<link rel="stylesheet" type="text/css" id="listmenu-h" href="fsmenu/listmenu_h.css" />
		<link href='style/tabs-ext3.css' rel='stylesheet' type='text/css' />
		<link href='style/ext-all.css' rel='stylesheet' type='text/css' />
		<link type="text/css" rel="stylesheet" href="../js/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css" media="screen"></link>

		<script type="text/javascript" src="js/ext_3/ext-base.js"></script>
		<script type="text/javascript" src="js/ext_3/ext-all.js"></script>
		<script type="text/javascript" src="js/ext_3/TabCloseMenu.js"></script>
		<script type="text/javascript" src="js/ext_3/tabs-adv.js"></script>
        <script type="text/javascript" src="../js/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js"></script>
		<?php

		break;
	case "alimentacion":
        ?>
        <link rel="stylesheet" type="text/css" id="listmenu-h" href="fsmenu/listmenu_h.css" />
        <?php
        echo "<link href='../style/input.css' rel='stylesheet' type='text/css' />";
		echo "<link href='../style/bootstrap.min.css' rel='stylesheet' type='text/css' />";
        //echo '<link rel="stylesheet" type="text/css" id="listmenu-h" href="fsmenu/listmenu_h_input.css" />';

    break;
	case "consulta":
		//Hoja de estilos
		//$style = ($_SESSION["undaf"] == 1) ? "consulta_undaf.css" : "consulta.css";
		$style = "consulta.css";

		echo "<link href='../style/$style' rel='stylesheet' type='text/css' />";

        // 4w
        if ($_SESSION['m_e'] == 'p4w') { ?>
            <link rel="stylesheet" type="text/css" href="../style/p4w.css" />
            <link rel="stylesheet" type="text/css" href="../style/fileuploader.css" />
            <link rel="stylesheet" type="text/css" href="../style/jquery-ui.css" />
            <link href='style/ext-all.css' rel='stylesheet' type='text/css' />
            <script type="text/javascript" src="../js/jquery-1.11.0.min.js"></script>
            <script type="text/javascript" src="../js/jquery-ui-1.10.4.min.js"></script>
            <script type="text/javascript" src="js/ext_3/ext-base.js"></script>
            <script type="text/javascript" src="js/ext_3/ext-all.js"></script>
            <script type="text/javascript" src="js/p4w/ext3.js"></script>
            <script type="text/javascript" src="js/fileuploader.js"></script>
            <script type="text/javascript">
            $j = jQuery.noConflict();
            </script>

        <?php
        }

    break;
}
?>

<script type="text/javascript" src="fsmenu/fsmenu.js"></script>
<script type="text/javascript">
//<![CDATA[

var listMenu = new FSMenu('listMenu', true, 'display', 'block', 'none');

listMenu.showDelay = 1;
listMenu.hideDelay = 100;
listMenu.animations[listMenu.animations.length] = FSMenu.animFade;
listMenu.animations[listMenu.animations.length] = FSMenu.animSwipeDown;

var arrow = null;
if (document.createElement && document.documentElement)
{
	arrow = document.createElement('img');
	arrow.src = 'fsmenu/flecha.gif';
	arrow.style.borderWidth = '0';
	arrow.className = 'subind';
}
addEvent(window, 'load', new Function('listMenu.activateMenu("listMenuRoot", arrow)'));
</script>

<!--[if lte IE 6]>
<link rel="stylesheet" type="text/css" media="screen" href="../style/ie_admin.css" />
<![endif]-->

<script src="js/general.js"></script>
<script src="js/ajax.js"></script>
<?php
if ($accion == "reportar_admin"){
	echo "<script src='js/consulta_org.js'></script>";
}
?>
</head>

<?php
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

if ($accion == "reportar_admin"){
	echo "<body onLoad=\"if(document.getElementById('texto_ubicacion')){ document.getElementById('texto_ubicacion').innerHTML = '';}\">";
}
else{
	echo "<body>";
}
?>

<?php include('../include/header.php') ?>

<div id="navgral" align="right"><?php include_once("include/navegaciongeneral.php"); ?>
</div>
<!--<div id="navesp" align="right"><?php include_once("include/navegaciongeneral_sub.php"); ?></div>-->
<div id="cuerpo">

<?php
// Div para Tabs de Admin
if ($_SESSION['m_g'] == 'admin')
	echo '<div id="tabs" style="margin:0;"></div>';

?>
<!-- CONTENIDO : INICIO -->
<div id="cont"><?php
if ($accion != ""){
	if ($accion == "listar" || $accion == "borrar" || $accion == "reportar" || $accion == "publicar"){
		//EVENTO
		if ($class == "EventoDAO"){

			$obj = New $class();

			/////LISTA EVENTOS
			if ($accion == "listar"){
				if (!isset($_POST['f_ini']) && !isset($_GET['page'])){
					include_once($_SESSION["m_e"]."/index.php");
				}
				else{

					////FECHA INICIAL
					if (isset($_POST["f_ini"])){
						$f_ini = $_POST['f_ini'];
					}
					else if (isset($_GET["f_ini"])){
						$f_ini = $_GET['f_ini'];
					}

					////FECHA FINAL
					if (isset($_POST["f_fin"])){
						$f_fin = $_POST['f_fin'];
					}
					else if (isset($_GET["f_fin"])){
						$f_fin = $_GET['f_fin'];
					}

					$where = "FECHA_REGISTRO between '".$f_ini."' AND '".$f_fin."'";
					$obj->ListarTabla($where);
				}
			}
			else if ($accion == "borrar"){
				$obj->Borrar($param,0);
			}
			else{
				$obj->{$method}($param);
			}
		}
		//EVENTO CONFLICTO
		else if ($class == "EventoConflictoDAO"){

			$obj = New $class();

			/////LISTA EVENTOS
			if ($accion == "listar"){
				if (!isset($_GET['f_ini']) && !isset($_GET['page'])){
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
		//ORG
		else if ($class == "OrganizacionDAO"){
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
				$obj->ListarTabla();
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
		$file = ($_SESSION['m_e'] == 'proyecto') ? 'insert_sidih.php' : 'insert.php' ;
		include_once($_SESSION["m_e"]."/$file");
	}
	else if ($accion == "insertar_dev"){
		include_once($_SESSION["m_e"]."/insert_dev.php");
	}
	else if ($accion == "consultar"){
		include_once("consulta/index.php");
	}
	else if ($accion == "importar"){
		include_once($_SESSION["m_e"]."/importar.php");
	}
	// Importacion de datos de desplazamiento accion social = sipod
	else if ($accion == "importar_sipod"){
		include_once($_SESSION["m_e"]."/importar_sipod.php");
	}
	else if ($accion == "reportar_admin"){
		include_once($_SESSION["m_e"]."/reportar.php");
	}
	else if ($accion == "insertarDatoValor" || $accion == "actualizarDatoValor"){
		include_once($_SESSION["m_e"]."/insert.php");
	}
	else if ($accion == "fechaCorte"){
		include_once($_SESSION["m_e"]."/fecha_corte.php");
	}
	else if ($accion == "sincro_cnrr"){
		include_once($_SESSION["m_e"]."/sincro_cnrr.php");
	}

}
//MUESTRA EL MAPA DE ACCION DEL MODULO GENERAL
else if ($accion == ""){
	switch ($_SESSION["m_g"]){
		case "admin":
			// Se carga home_admin.php desde js/ext_3/tab-adv.js
		break;

		case 'alimentacion';
			include ("home_alimentacion.php");
		break;
	}
}
?></div>
<!-- CONTENIDO : FIN--></div>
<!-- <div id="cierre"><a href="index.php?accion=logout">Salir</a></div> -->
<?php include('../include/footer.php') ?>
</body>
</html>
