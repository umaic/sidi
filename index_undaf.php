<?
session_start();

//LIBRERIAS
include_once 'consulta/lib/libs_perfil_usuario.php';
//include_once 'admin/lib/common/error.php';
include_once 'admin/lib/dao/log.class.php';

if(!isset($_SESSION["id_usuario_s"])){
	header("Location: login_undaf.php?m_g=consulta");
}

//CONSULTA EL PERFIL DE USUARIO
$perfil_dao = New PerfilUsuarioDAO();
$perfil = New PerfilUsuario();
if (isset($_SESSION["id_tipo_usuario_s"])){
	$perfil = $perfil_dao->GetAllArray('ID_TIPO_USUARIO = '.$_SESSION["id_tipo_usuario_s"]);
}

$_SESSION["m"] = "";
$_SESSION["m_e"] = "";

//REGISTRA EL MODULO GENERAL
if (isset($_GET["m_g"])){
	$_SESSION["m_g"] = $_GET["m_g"];
}
else if (!isset($_SESSION["m_g"]) && !isset($_GET["m_g"])){
	$_SESSION["m"] = "";
}

//REGISTRA EL MODULO ESPECIFICO
if (isset($_GET["m_e"])){
	$_SESSION["m_e"] = $_GET["m_e"];
}
else if (!isset($_SESSION["m_e"]) && !isset($_GET["m_e"])){
	$_SESSION["m_e"] = "";
}

//INICIALIZACION DE VARIABLES
$accion = "";
if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

$class = "";
if (isset($_GET["class"])){
	$class = $_GET["class"];
}
else if (isset($_POST["class"])){
	$class = $_POST["class"];
}
$method = "";
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

//LOG
if (isset($_POST["submit"]) || isset($_POST["minificha"])){
	$log = New LogUsuarioDAO();
	$log->RegistrarFrontend();
}


//Hoja de estilos
$style = "consulta_undaf.css";
$title = "Sistema Integrado de Informaci&oacute;n sobre iniciativas, proyectos y programas del SNU";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?=$title?></title>

<link href="style/<?=$style ?>" rel="stylesheet" type="text/css" />

<!--[if lte IE 6]>
<link rel="stylesheet" type="text/css" href="style/ie.css" />    
<link rel="stylesheet" type="text/css" href="style/consulta_undaf_ie.css" />    
<![endif]-->

<!--[if IE 7]>
<link rel="stylesheet" type="text/css" href="style/ie.css" />    
<link rel="stylesheet" type="text/css" href="style/consulta_undaf_ie.css" />    
<![endif]-->

<script type="text/javascript" src="t/js/general.js"></script>
<script type="text/javascript" src="t/js/ajax.js"></script>

<?
if ($accion == "consultar"){
	?>
	<script src="js/general.js"></script>
	<?
	
	//Graficas
	if ($_SESSION["m_e"] == 'tabla_grafico'){
		?>
		<script type="text/javascript" src="js/swfobject.js"></script>
		<?	
	}
}
?>
</head>
<body bgcolor="#efefef">
<?
//Tool Tips para graficas y resumenes, van en body
if ($accion == "consultar"){
	echo '<script src="js/wz_tooltip.js"></script>';
}
?>
<h1 class="info"><?=$title?></h1>
<div id="cabecera"></div>

    <div id="navgral">
	    <? include_once("include/navegaciongeneral_undaf.php"); ?>
    </div>

<?
//Home - Menu
if (isset($_SESSION["m_e"]) && $_SESSION["m_e"] == 'home'){
	echo '<div id="cont_home">';
	include('home_undaf.php');
	echo "</div>";
	die;
}
?>

<div id="cuerpo">
<?
  echo '<div id="cont">';
	if (isset($_SESSION["m_g"]) && $_SESSION["m_g"] == 'consulta'){
		if ($accion != ""){
			if ($accion == "consultar"){
				//ORG
				if ($class == "ProyectoDAO"){
					include('consulta/org.php');
				}
				//PROYECTO-UNDAF
				if ($class == "ProyectoUndaf"){
					include('consulta/proyecto_undaf.php');
				}
			}
		}
		else {
			include('consulta/index.php');
		}
	}

	?>
  </div>
  </div>
</body>
</html>
