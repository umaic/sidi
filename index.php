<?
session_start();

// PHP >= 5.3
//date_default_timezone_set('America/Bogota');

//error_reporting(0);

//LIBRERIAS
include_once 'consulta/lib/libs_perfil_usuario.php';
//include_once 'admin/lib/common/error.php';
include_once 'admin/lib/dao/log.class.php';

if(!isset($_SESSION["id_usuario_s"])){
    header("Location: http://".$_SERVER['HTTP_HOST']."/sissh/login.php?m_g=consulta");
    exit;
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
$style = "consulta.css";

$title = "Sistema Integrado de Informaci&oacute;n Transversal de Colombia";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $title ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Sistema de Informaci&oacute;n Central OCHA - Colombia</title>

<link href="style/<?=$style ?>" rel="stylesheet" type="text/css" />
<link href="favicon.ico" rel="shortcut icon" type="image/x-icon" />

<?
if ($class == "P4W"){ ?>
    <link href="style/p4w_consulta.css" rel="stylesheet" type="text/css" />
    <link href="style/jquery-ui.min.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui-1.10.4.min.js"></script>
    <script type="text/javascript">
    $j = jQuery.noConflict();
    </script>
<?php
}
else {
    include("include/head.php");
}

if ($accion == "consultar"){
	?>
	<script src="js/general.js"></script>
	<!--<script src="js/mootools.js"></script>
	<script>
		window.addEvent('domready', function(){
		    var myTips = new Tips($$('.TipExport'), {
				maxTitleChars: 80
	    	});
		});
	</script>	-->
	<?

	//Graficas
	if ($_SESSION["m_e"] == 'tabla_grafico'){
		?>
		<script type="text/javascript" src="js/swfobject.js"></script>
		<?
	}
}
?>
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-48294292-4', 'salahumanitaria.co');
      ga('send', 'pageview');
    </script>
</head>
<?
if ($accion == "graficar"){
	echo "<body onLoad=\"if(document.getElementById('texto_ubicacion')){ document.getElementById('texto_ubicacion').innerHTML = '';}\">";
}
else{
	echo "<body>";
}

//Tool Tips para graficas y resumenes, van en body
if ($accion == "consultar"){
	echo '<script src="js/wz_tooltip.js"></script>';
}
?>
<!--
<div id="cabecera">
    <div id="ir_a">
        <select onchange="location.href='/sissh/index.php?accion=consultar&' + this.value">
            <option selected>Ir a</option>
            <optgroup label="Reportes">
            <option value="m_e=dato_sector&class=DatoSectorialDAO">Datos Sectoriales</option>
            <option value="m_e=desplazamiento&class=DesplazamientoDAO">Desplazamiento</option>
            <option value="m_e=evento_c&class=EventoConflictoDAO">Eventos del Conflicto</option>
            <option value="m_e=dato_sector&class=DatoSectorialDAO&method=ReportarMetadatos">Metadatos</option>
            <option value="m_e=org&class=OrganizacionDAO">Organizaciones</option>
            </optgroup>
        </select>
    </div>
</div>
-->

<?php include('include/header.php') ?>

<div id="navgral">
<? include_once("include/navegaciongeneral.php"); ?>
</div>
<?

//Home - Menu
if (isset($_SESSION["m_e"]) && $_SESSION["m_e"] == 'home'){
	echo '<div id="cont_home">';
	include('home.php');
	echo "</div>";
}
else {
?>


<div id="cuerpo">

  <?
  if (!isset($_POST["informe_diario"]) && !isset($_POST["informe_semanal"]) && !isset($_GET["f_ini"]) && $_SESSION["m_e"] != "mapa_i" && $method != "graficaConteo" && $method != "graficaConteoDeptoMpio" && $accion != 'consultar' && !isset($_POST["minificha"]) && $_SESSION["m_e"] != "home" && !isset($_GET["id_depto_minificha"]) && !isset($_GET["id_mun_minificha"])){	?>
	  <div id="navlat">
		  <? include_once("include/menulat.php"); ?>
	  </div>
<? }

  echo '<div id="cont">';
	if (isset($_SESSION["m_g"]) && $_SESSION["m_g"] == 'consulta'){
		if ($accion != ""){
			if ($accion == "consultar"){
				//ORG
				if ($class == "OrganizacionDAO"){
					include('consulta/org.php');
				}
				//PROY
				if ($class == "ProyectoDAO"){
					include('consulta/proyecto.php');
				}
				//EVENTOS
				if ($class == "EventoDAO"){
					include('consulta/evento.php');
				}
				//EVENTOS CONFLICTO
				if ($class == "EventoConflictoDAO"){
					include('consulta/evento_c.php');
				}
				//DATO SECTORIAL
				if ($class == "DatoSectorialDAO"){
					if ($method == ''){
						include('consulta/dato_sectorial.php');
					}
					else{
						include_once("consulta/lib/libs_dato_sectorial.php");

						$obj = New $class;

						$id = 0;
						if (isset($_GET["id"]))	$id = $_GET["id"];

						$obj->$method($id);
					}
				}
				//DESPLAZAMIENTO
				if ($class == "DesplazamientoDAO"){
					include('consulta/desplazamiento.php');
				}
				//MINA
				if ($class == "MinaDAO"){
					include('consulta/mina.php');
				}
				//MAPA I
				if ($class == "MapaI"){
					include('consulta/mapa_i.php');
				}
				//TABLAS Y Graficos
				if ($class == "TablaGrafico"){
					include('consulta/tabla_grafico.php');
				}
				// 4W
				if ($class == "P4W"){
					include('consulta/p4w.php');
				}
			}
			else if ($accion == "generar"){
				//MINIFICHA
				if ($class == "Minificha"){
					include('consulta/minificha.php');
				}
			}
			else if ($accion == "graficar"){
				//ORG
				switch ($class){
					case "OrganizacionDAO":
						include("consulta/grafica_org.php");
					break;
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
<?php
}

if ($class != "P4W"){
    include('include/footer.php');
}
?>
</body>
</html>
