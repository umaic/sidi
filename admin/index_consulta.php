<?
//SEGURIDAD
include_once("seguridad.php");

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
  case "evento":
    include_once("lib/libs_evento.php");	
    include_once ('js/calendar/calendar.php');
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

//ACCION DE LA FORMA
if (isset($_POST["submit"])){
  //Controlador
  $ct = New ControladorPagina($_POST["accion"]);
}

//LOGOUT
if (isset($_GET["accion"]) && $_GET["accion"] == "logout"){
  $s = New SessionUsuario();
  $s->Logout();
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>SIDI UMAIC - Colombia</title>
<link href="../images/consulta.css" rel="stylesheet" type="text/css" />
<script src="js/general.js"></script>
</head>
<body>
<h1 class="info">SIDI UMAIC - Colombia</h1>
<div id="cabecera"></div>
<div id="navgral">
	  <? include_once("include/navegaciongeneral.php"); ?>
</div>
<div id="navesp">
	  <? include_once("include/navegaciongeneral_sub.php"); ?>
</div>
<div id="cuerpo">
	<!-- CONTENIDO : INICIO -->
  <div id="cont">
	  <?
		if ($accion != ""){
			if ($accion == "reportar"){
			  if ($class == "EventoDAO"){
  			  /////REPORTE DIARIO
  				if ($method == "ReporteDiario"){
  				  if (!isset($_POST['f_ini'])){
    				  include_once($_SESSION["m_e"]."/index.php");
  					}
  					else{
      			  $obj = New EventoDAO();
              $f_ini = $_POST['f_ini'];
        			$obj->ReporteDiarioHTML($f_ini);
  					}
  				}
  			  /////REPORTE SEMANAL
  				else if ($method == "ReporteSemanal"){
  				  if (!isset($_POST['f_ini']) && !isset($_GET['id_depto'])){
    				  include_once($_SESSION["m_e"]."/index.php");
  					}
  					else{
      			  $obj = New EventoDAO();
							if (isset($_POST["f_ini"])){
                $f_ini = $_POST['f_ini'];
  							$f_fin = $_POST['f_fin'];
							}
							else if (isset($_GET["f_ini"])){
                $f_ini = $_GET['f_ini'];
  							$f_fin = $_GET['f_fin'];
							}
        			$obj->ReporteSemanalHTML($f_ini,$f_fin);
  					}
  				}
				}
			  else if ($class == "OrganizacionDAO"){
  				if ($method == "ReporteCoberturaGeografica"){
  				  if (!isset($_POST['columna'])){
    				  include_once($_SESSION["m_e"]."/index.php");
  					}
  					else{
      			  $obj = New OrganizacionDAO();
              $columna = $_POST['columna'];
							$id_depto = $_POST['id_depto'];
							$id_muns = $_POST['id_muns'];
		
        			$obj->ReporteCoberturaGeograficaHTML($id_depto,$id_muns,$columna);
  					}
  				}
				}
			}
		}
		?>
  </div>
	<!-- CONTENIDO : FIN-->
</div>
</body>
</html>