<?
session_start();
include_once("admin/lib/common/mysqldb.class.php");
include_once("admin/lib/common/sessionusuario.class.php");

//REGISTRA EL MODULO GENERAL
if (!isset($_SESSION["m_g"]) || $_SESSION["m_g"] == ""){

	 if (isset($_GET["m_g"]))	$_SESSION["m_g"] = $_GET["m_g"];
}

//ACCION DE LA FORMA
if (isset($_POST["login_submit"])){
  $vu = New SessionUsuario();
  $vu->ValidarUsuario($_POST["login"],$_POST["password"],"index_cnrr.php?m_g=".$_POST["m_g"],'login_cnrr.php',0);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Sistema de Informaci&oacute;n Organizaciones CNRR</title>
<link href="style/consulta_cnrr.css" rel="stylesheet" type="text/css" />
<script src="admin/js/general.js"></script>
</head>
<body onload="document.getElementById('login').focus()">
<div id="cont" align="center">
<table align='center' cellspacing="0" cellpadding="0" border="0" >
	<tr><td align="center"><img src="images/cnrr/titulo.jpg"><img src="images/cnrr/relleno_up.jpg"><img src="images/cnrr/tituloup3.jpg"></td></tr>
	<?
	if (isset($_SESSION['id_usuario_s']))	echo "<tr><td align='right'><a href='".$_SERVER['PHP_SELF']."?accion=logout'>Logout</a></td></tr>";
	else "<tr><td><img src='images/spacer.gif' height='12'></td></tr>";
	?>
</table>
<br><br>
<form action="<? echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <table align="center" border="0" cellpadding="5" cellspacing="3" width="500" class="tabla_consulta">
      <tr>
      	<td align='center' colspan='2' class="titulo_lista">
			<b>Iniciar Sesi�n de Usuario </b></td>
		</td>
      </tr>
      <tr><td>&nbsp;</td></tr>
      	<tr>
      	  <td align="right" width="50%">Login</td>
      		<td align="left"><input type="text" id="login" name="login" class="textfield" /></td>
      	</tr>
      <tr>
         <td align="right">Password</td>
      	 <td align="left"><input type="password" id="password" name="password" class="textfield" /></td>
      </tr>
      <tr><td>&nbsp;</td></tr>
      <tr>
      	 <td colspan="2" align='center'>
      	 	<input type="hidden" name="m_g" value="<?=$_SESSION["m_g"]?>" />
		   <input type="submit" name="login_submit" value="Aceptar" class="boton" onclick="return validar_forma('login,Login,password,Password','');" />
		</td>
      </tr>
      <tr>
      	<td align="left">
      		<img src="images/reg_org.gif">&nbsp;<a href="registro_org_cnrr.php">Registrar Organizaci&oacute;n</a>
      	</td>
      </tr>
    </table>
</form>
</div>
</body>
</html>