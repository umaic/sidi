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
		$log->insertarLogFisico('login_unicef',"$login|$pass");
	}
	// fin log fisico	
	
	$pag_exito = "index_unicef.php?m_e=home";
	$vu = New SessionUsuario();
	$vu->ValidarUsuario($login,$pass,$pag_exito,'login_unicef.php',0);
	
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Sistema de Informaci&oacute;n UNICEF</title>
<link href="style/consulta_unicef.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]>
<link rel="stylesheet" type="text/css" href="style/ie.css" />    
<![endif]-->

<!--[if IE 7]>
<link rel="stylesheet" type="text/css" href="style/ie.css" />    
<![endif]-->
<link href='t/style/ext-all.css' rel='stylesheet' type='text/css' />
<script type="text/javascript" src="t/js/general.js"></script>
<script type="text/javascript" src="t/js/ext_3/ext-base.js"></script>
<script type="text/javascript" src="t/js/ext_3/ext-all.js"></script>
<script type="text/javascript" src="t/js/ext_3/ext-lang-es.js"></script>
<script type="text/javascript">
function showRegistro(){
    
    var w_width = 600;
    var w_height = 500;

    new Ext.Window({
        id               : 'Registro',
        html             : '<iframe src="unicef_registro.php" frameborder="0" width="'+w_width+'" height="'+w_height+'"></iframe>',
        width            : w_width,
        height           : w_height,
        title            : 'Registro de Usuario',
        modal            : true,
        closeable        : true
    }).show();

    return false;
}
</script>
</head>

<body onload="document.getElementById('login').focus()">
<h1 class="info">Sistema de Informaci&oacute;n UNICEF</h1>
<div id="cabecera">
    <span>Sistema de Informaci&oacute;n Colombia</span>
    <a id="logo" title="UNICEF" href="http://www.unicef.org/spanish/index.php" target="_blank">
    <img alt="UNICEF" src="images/unicef/unicef_logo.gif"/>
    </a>
</div>
<div id="cuerpo">
  <div id="cont">
  	<div id="login_div">
	    <div class="login_titulo">Bienvenido, por favor ingrese los datos</div><br />
    	<div class="fields">
    		<form action="" method="post">
			<fieldset>
                <p>
					<label for="login">Nombre de Usuario</label><br />
					<input type="text" id="login" name="login" class="textfield" />
                </p>
                <p>
				    <label for="password">Contrase&ntilde;a</label><br />
					<input type="password" id="password" name="password" class="textfield" />
                </p>
                <p>
					<input type="hidden" name="m_g" value="<?=$_SESSION["m_g"]?>" />
					<input type="submit" name="submit" value="Ingresar" class="boton" onclick="return validar_forma('login,Nombre de Usuario,password,Contrase\xf1a','');" />
                </p>
			</fieldset>
			</form>
		</div>
        <br /> 
        <div class="login_footer">
            <p><a href='#' onclick='return showRegistro()'><img src="images/unicef/boton_registro.png" border="0"></a>
            <p></p>
            <p>Este sitio requiere <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" target="_blank"><img src="images/flash.png" border="0" />&nbsp;Flash player</a> 9 o superior. Sugerimos <a href="http://www.firefox.com" target="_blank"><img src="images/firefox.png" border="0" /></a> para una &oacute;ptima visualizaci&oacute;n </p>
            <br />
            <p>Copyright 2010. UNICEF Colombia. Todos los Derechos Reservados.</p>
            <p>&nbsp;</p>
        </div>
    </div>
</div>
</body>
</html>
