<?
session_start();

//error_reporting(0);

//LIBRERIAS
include_once 'consulta/lib/libs_perfil_usuario.php';

if(!isset($_SESSION["id_usuario_s"])){
	header("Location: login.php?m_g=consulta");
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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Sistema de Informaci&oacute;n Central OCHA - Colombia</title>
<link href="style/consulta.css" rel="stylesheet" type="text/css" />
<script src="admin/js/general.js"></script>
<?
if ($accion == "consultar"){
	?>
	<script src="js/general.js"></script>
	<script src="js/mootools.js"></script>
	<script>
		window.addEvent('domready', function(){
		    var myTips = new Tips($$('.TipExport'), {
				maxTitleChars: 80   //I like my captions a little long
	    	});
		});
	</script>	
	<?
}
?>
</head>
<?
if ($accion == "graficar"){
	echo "<body onLoad=\"if(document.getElementById('texto_ubicacion')){ document.getElementById('texto_ubicacion').innerHTML = '';}\">";
}
else{
	echo "<body>";
}
?>
<h1 class="info">Sistema de Informaci&oacute;n  Central &ndash; OCHA &ndash; Naciones Unidas &ndash; Colombia</h1>
<div id="cabecera"></div>
<div id="cuerpo">

  <?
  if (!isset($_POST["informe_diario"]) && !isset($_POST["informe_semanal"]) && !isset($_GET["f_ini"]) && $_SESSION["m_e"] != "mapa_i" && $method != "graficaConteo" && $method != "graficaConteoDeptoMpio" && $accion != 'consultar' && !isset($_POST["minificha"]) && $_SESSION["m_e"] != "home"){	?>
	  <div id="navlat">
		  <? include_once("include/menulat.php"); ?>
	  </div>
<? } ?>
  <div id="cont">
	<?
	//Home - Menu
	if (isset($_SESSION["m_e"]) && $_SESSION["m_e"] == 'home'){
		include('home.php');
		die;
	}

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
</body>
</html>