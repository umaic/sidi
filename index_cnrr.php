<?
session_start();

//LOGOUT
if (isset($_GET["accion"]) && $_GET["accion"] == "logout"){
	@$_SESSION = array();
	@session_destroy();
}

if (!isset($_SESSION["id_usuario_s"])){
	header("Location: login_cnrr.php");
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Sistema de Informaci&oacute;n Organizaciones CNRR</title>
<link href="style/consulta_cnrr.css" rel="stylesheet" type="text/css" />
<script src="admin/js/general.js"></script>
<script src="admin/js/ajax.js"></script>
<script src="admin/js/consulta_org.js"></script>
<?
if ($accion == "consultar"){
	?>
	<script src="js/general.js"></script>
	<script src="js/mootools.js"></script>
	<script>
		window.addEvent('domready', function(){
		    var myTips = new Tips($$('.TipExport'), {
				maxTitleChars: 80
	    	});
		});
	</script>	
	<?
}
?>
</head>
<body onLoad="if(document.getElementById('texto_ubicacion')){document.getElementById('texto_ubicacion').innerHTML = '';}">
<div id="cont" align="center">
<table align='center' cellspacing="0" cellpadding="0" border="0" width="940">
	<tr><td align="right"><img src="images/cnrr/titulo.jpg"><img src="images/cnrr/relleno_up.jpg"><img src="images/cnrr/tituloup3.jpg"></td></tr>
	<?
	if (isset($_SESSION['id_usuario_s']))	echo "<tr><td align='right'><a href='".$_SERVER['PHP_SELF']."?accion=logout'>Logout</a></td></tr>";
	else "<tr><td><img src='images/spacer.gif' height='12'></td></tr>";
	?>
</table>
<?
echo "<table><tr><td align='left'>";
include_once("consulta/org_cnrr.php");
echo "</td></tr></table>";
?>
</div>
</body>
</html>