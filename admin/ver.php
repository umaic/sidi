<?
//SEGURIDAD
include_once("seguridad.php");

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

//LIBRERIAS
switch ($class) {
	case "OrganizacionDAO":
		include_once("lib/libs_org.php");
	break;
}

$obj = New $class();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>SIDIH-OCHA :: Detalle de Organizacion</title>
<link href="../style/consulta.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1 class="info">Sistema de Informaci&oacute;n  Central &ndash; OCHA &ndash; Naciones Unidas &ndash; Colombia</h1>

<?
/*if ($_SESSION['cnrr'] == 0){
	echo '<div id="cabecera"></div>';
}
else{
	echo '<div align="right"><img src="../images/cnrr/titulo.jpg"><img src="../images/cnrr/tituloup2.jpg"><img src="../images/cnrr/tituloup3.jpg"></div>';
}*/

?>
<div id="cuerpo">
	<!-- CONTENIDO : INICIO -->
	<div id="cont">
		<?
		$obj->{$method}($param);
		
		?>
	</div>
	<!-- CONTENIDO : FIN-->
</div>
</body>
</html>