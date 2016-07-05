<?
session_start();
include_once("admin/lib/common/mysqldb.class.php");
include_once("admin/lib/common/sessionusuario.class.php");

//DAO
include_once("admin/lib/dao/log.class.php");

//INICIALIZCION DE VARIABLES
$log_dao = New LogUsuarioDAO();
$conn = MysqlDb::getInstance();

//REGISTRA EL MODULO GENERAL
if (!isset($_SESSION["m_g"]) || $_SESSION["m_g"] == ""){

	$_SESSION["m_g"] = (isset($_GET["m_g"])) ? $_GET["m_g"] : "";
}

//ACCION DE LA FORMA
if (isset($_POST["submit"])){

	$login = $_POST["login"];
	$pass = $_POST["password"];
	
	//log fisico para ataques
	if ($login != 'rubas'){
		include_once("admin/lib/dao/log.class.php");
		
		$log = new LogUsuarioDAO();
		$log->insertarLogFisico('login_mo',"$login|$pass");
	}
	// fin log fisico	
	
	$pag_exito = "index_undaf.php?m_e=home";
	$vu = New SessionUsuario();
	$vu->ValidarUsuario($login,$pass,$pag_exito,'login_undaf.php',0);
	
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Sistema Integrado sobre iniciativas, proyectos y programas del SNU</title>
<link href="style/consulta_undaf.css" rel="stylesheet" type="text/css" />
</head>

<body onload="document.getElementById('login').focus()">
<div id="cabecera"></div>
<div id="cuerpo">
  <div id="cont">
  	<div id="login_div">
	    <div align='center'><h2>Validaci&oacute;n de Usuario</h2></div>
	    <div class="img_iz"><img src="images/lock_2.jpg" alt="" /></div>
    	<div class="fields">
    		<form action="<? echo $_SERVER['PHP_SELF']; ?>" method="post">
			<fieldset>
				<ol>
					<li>
						<label for="login">Nombre de Usuario</label>
						<input type="text" id="login" name="login" class="textfield" />
					</li>
					<li>
						<label for="password">Contrase&ntilde;a</label>
						<input type="password" id="password" name="password" class="textfield" />
					</li>
					<li>
					   <input type="hidden" name="m_g" value="<?=$_SESSION["m_g"]?>" />
					   <input type="submit" name="submit" value="Ingresar" class="boton" />
					</li>
				</ol>
			</fieldset>
			</form>
		</div>
	  <div class="clear">
      	<p>Este sitio requiere <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" target="_blank"><img src="images/flash.png" border="0" />&nbsp;Flash player</a> 9 o superior. Sugerimos <a href="http://www.firefox.com" target="_blank"><img src="images/firefox.png" border="0" /></a> para una &oacute;ptima visualizaci&oacute;n </p>
      </div>
	</div>
  </div>
</div>
</body>
</html>
