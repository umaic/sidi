<?
session_start();
include_once("admin/lib/common/mysqldb.class.php");
include_once("admin/lib/control/ctlusuario.class.php");
include_once("admin/lib/dao/usuario.class.php");
include_once("admin/lib/model/usuario.class.php");


$nombre = "";
$login = "";
$email = "";
$org = "";
$tel = "";
$contacto = "";

//ACCION DE LA FORMA
$error_sec = "";
if (isset($_POST["submit"])){
	if( $_SESSION['security_code'] == $_POST['security_code']){
		$ct = New ControladorPagina($_POST["accion"]);
	}
	else{
		$nombre = $_POST["nombre"];
		$login = $_POST["login"];
		$email = $_POST["email"];
		$org = $_POST["org"];
		$tel = $_POST["tel"];
		$contacto = $_POST["punto_contacto"];
		
		$error_sec = "Código de seguridad incorrecto!";
	}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>SIDI UMAIC - Colombia</title>
<link href="style/consulta.css" rel="stylesheet" type="text/css" />
<script src="admin/js/general.js"></script>
<script type="text/javascript">
function validar(){
	pass_1 = document.getElementById('pass').value;
	pass_2 = document.getElementById('pass_2').value;
	login = document.getElementById('login').value;
	sec_code = document.getElementById('security_code').value;
	
	if (validar_forma('nombre,Nombre Completos,org,Organizaci\xf3n,tel,Tel\xe9fono,email,Email,punto_contacto,Persona de cont\xe1cto en UNICEF,login,Nombre de Usuario,pass,Contrase\xf1a,security_code,C\xf3digo de Seguridad',document.getElementById('email').value)){
		
		if (login.indexOf(" ") > 0){
			alert('El Nombre de Usuario (Login) no puede contener espacios, ej. alopez');
			return false;
		}
		
		if (pass_1 != pass_2){
			alert('Las Contraseñas no coinciden');
			return false;
		}
	}
	else{
		return false;
	}
}
</script>
</head>

<body>
<div id="cuerpo">
  <div id="cont" align="center"><br>
    <form action="<? echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <table align="center" border="0" cellpadding="5" cellspacing="3" width="100%" class="table_registro">
		<?
		if (!isset($_POST["submit"]) || $error_sec != ""){ ?>
			<tr><td align="center" colspan="2"><font color="Red"><b><?=$error_sec?></b></font></td></tr>
			<tr>
			  <td align="right" width="40%">Nombres Completos</td>
				<td align="left"><input type="text" id="nombre" name="nombre" class="textfield" size="25" value="<?=$nombre?>" /></td>
			</tr>
			<tr>
			  <td align="right" width="40%">Organizaci&oacute;n</td>
				<td align="left"><input type="text" id="org" name="org" class="textfield" size="25" value="<?=$org?>"/></td>
			</tr>
			<tr>
			  <td align="right" width="40%">Tel&eacute;fono</td>
				<td align="left"><input type="text" id="tel" name="tel" class="textfield" size="25" value="<?=$tel?>" /></td>
			</tr>
			<tr>
			  <td align="right" width="40%">Email</td>
				<td align="left"><input type="text" id="email" name="email" class="textfield" size="25" value="<?=$email?>" /></td>
			</tr>
			<tr>
			  <td align="right" width="40%">Persona de cont&aacute;cto en UNICEF</td>
				<td align="left"><input type="text" id="punto_contacto" name="punto_contacto" class="textfield" size="25" value="<?=$contacto?>" /></td>
			</tr>			
			<tr>
			  <td align="right" width="40%">Nombre de Usuario (Login)</td>
				<td align="left"><input type="text" id="login" name="login" class="textfield" size="25" value="<?=$login?>" /></td>
			</tr>
			<tr>
	         <td align="right">Contrase&ntilde;a</td>
	      	 <td align="left"><input type="password" id="pass" name="pass" class="textfield"  size="25" /></td>
	      </tr>
			<tr>
	         <td align="right">Repetir Contrase&ntilde;a</td>
	      	 <td align="left"><input type="password" id="pass_2" name="pass_2" class="textfield"  size="25" /></td>
	      </tr>
		   <tr>
	         <td align="right">
	         	<img src="include/captcha/CaptchaSecurityImages.php?width=100&height=40&characters=5" /><br />
				Ingrese el c&oacute;digo en min&uacute;scula
	         </td>
	      	 <td align="left"><input type="text" id="security_code" name="security_code" class="textfield"  size="25" />
	      	 	
	      	 </td>
	      </tr>
	      <tr><td>&nbsp;</td></tr>
	      <tr>
	      	 <td colspan="2" align='center'>
			   <input type="hidden" name="accion" value="unicef_registrar" />
			   <input type="hidden" name="cnrr" value="0" />
			   <input type="hidden" name="id_tipo" value="32" />
			   <input type="hidden" name="activo" value="0" />
			   <input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar();" />
			</td>
	      </tr>
	    <?
		}
		else {?>
		<tr>
			 <td align="center" class="titulo_login">Registro realizado con éxito, el Administrador fue notificado para realizar la activación del usuario.</td>
		</tr>
		<?
		}
		?>
    </table>
	</form>
  </div>
</div>
