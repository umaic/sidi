<?
//session_start();

//LIBRERIAS
include_once 'seguridad.php';
include_once 'consulta/lib/libs_perfil_usuario.php';
include_once 'admin/lib/dao/log.class.php';

if(!isset($_SESSION["id_usuario_s"])){
	header("Location: login_unicef.php?m_g=consulta");
}

//LOGOUT
if (isset($_GET["accion"]) && $_GET["accion"] == "logout"){
	$s = New SessionUsuario();
	$s->Logout();
	die;
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

$print = (isset($_SESSION["m_g"]) && $_SESSION["m_g"] == 'a_print') ? true : false;

//Hoja de estilos
$style = "consulta_unicef.css";
$title = "UNICEF - Sistema de Informaci&oacute;n Colombia";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?=$title?></title>

<link href="style/<?=$style ?>" rel="stylesheet" type="text/css" />

<!--[if lte IE 6]>
<link rel="stylesheet" type="text/css" href="style/ie.css" />    
<![endif]-->

<!--[if IE 7]>
<link rel="stylesheet" type="text/css" href="style/ie.css" />    
<![endif]-->

<?php 
if (!$print){
?>
    <link href="fsmenu/listmenu_h_unicef.css" rel="stylesheet" type="text/css" id="listmenu-h"/>
    <link href='t/style/tree-ext3.css' rel='stylesheet' type='text/css' />
    <link href='t/style/ext-all.css' rel='stylesheet' type='text/css' />
    <script type="text/javascript" src="t/js/general.js"></script>
    <script type="text/javascript" src="t/js/ajax.js"></script>
    <script type="text/javascript" src="t/js/ext_3/ext-base.js"></script>
    <script type="text/javascript" src="t/js/ext_3/ext-all.js"></script>
    <script type="text/javascript" src="t/js/ext_3/ext-lang-es.js"></script>
    <script type="text/javascript" src="t/js/ext_3/tree-av.js"></script>

    <script type="text/javascript">
    //<![CDATA[
    // Función para inicializar las entradas que son fecha, se invoca en oclick del input text
    function changeToDateField(el){
        var dateField = new Ext.form.DateField({format:'Y-m-d',applyTo:el});
    }

    function chkTR(chk_state,id_tr){

        var style = (chk_state) ? 'tr_chk': '';

        document.getElementById(id_tr).className = style; 
    }

    function in_array(que,donde){
        for(var i=0;i<donde.length;i++){
            if (que == donde[i])    return true;
        }

        return false;
    }
    function sel11Deptos(){
        var id_deptos_11 = ['44','47','13','23','27','19','52','41','15','05','91'];

        var check_deptos = document.getElementsByName('id_depto[]');

        for (var i=0;i<check_deptos.length;i++){
            if (in_array(check_deptos[i].value,id_deptos_11))    check_deptos[i].checked = true;
        }
    }
    </script>
<?
}

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
<body>
<h1 class="info"><?=$title?></h1>
<div id="cabecera">
    <span>Sistema de Informaci&oacute;n Colombia</span>
    <a id="logo" title="UNICEF" href="http://www.unicef.org/spanish/index.php" target="_blank"> <img alt="UNICEF" src="images/unicef/unicef_logo.gif"/> </a>
    <div id="navgral"></div>
</div>
<div id="navgral_menu"><? if (!$print)   include_once("include/navegaciongeneral_unicef.php"); ?></div>
<?
//Home - Menu
if (isset($_SESSION["m_e"]) && $_SESSION["m_e"] == 'home'){
	echo '<div id="cont_home">';
	include('home_unicef.php');
	echo "</div>";
	die;
}
?>

<div id="cuerpo">
<?
  echo '<div id="cont">';
	if (isset($_SESSION["m_g"])){
        
        switch($_SESSION["m_g"]){
            
            case 'admin':
               include('admin/index_parser_unicef.php');  
            break; 
            
            case 'alimentacion':
               include('admin/unicef_proyecto/index.php');  
            break; 
            
            case 'a_print':
               include('admin/unicef_proyecto/print.php');
            break; 
            
            case 'consulta':
                if ($accion != ""){
                    if ($accion == "consultar"){
                        //ORG
                        if ($class == "ProyectoDAO"){
                            include('consulta/org.php');
                        }
                        //PROYECTO-unicef
                        if ($class == "Proyectounicef"){
                            include('consulta/proyecto_unicef.php');
                        }
                    }
                }
            break;
            
            default:
                include('consulta/index.php');
            break;
            
        }
	}

	?>
  </div>
  </div>
</body>
</html>
