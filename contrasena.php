<?
session_start();
include_once("admin/lib/common/mysqldb.class.php");
include_once("admin/lib/control/ctlusuario.class.php");
include_once("admin/lib/dao/usuario.class.php");
include_once("admin/lib/model/usuario.class.php");


$email = "";

//ACCION DE LA FORMA
$error_sec = "";
if (isset($_POST["submit"])){
	$email = $_POST["email"];
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Sistema de Informaci&oacute;n Central OCHA - Colombia</title>
<link href="style/consulta.css" rel="stylesheet" type="text/css" />
<script src="admin/js/general.js"></script>
<script>
function validar(){
	pass_1 = document.getElementById('pass').value;
	pass_2 = document.getElementById('pass_2').value;
	login = document.getElementById('login').value;
	sec_code = document.getElementById('security_code').value;
	
	if (validar_forma(email,Email)){
		
		if (login.indexOf(" ") > 0){
			alert('El Nombre de Usuario (Login) no puede contener espacios, ej. alopez');
			return false;
		}
		
		if (pass_1 != pass_2){
			alert('Las Contrase�as no coinciden');
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
<h1 class="info">Sistema de Información  Central &ndash; OCHA &ndash; Naciones Unidas &ndash; Colombia</h1>
<div id="cabecera"></div>
<div id="navgral">
  <ul id="navegaciongeneral">
  </ul>
</div>
<div id="navesp">
  <ul id="navegaciongeneral">
  </ul>
</div>
<div id="cuerpo">
  <div id="cont" align="center"><br>
    <form action="<? echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <table align="center" border="0" cellpadding="5" cellspacing="3" width="600" class="table_login">
		<?
		if (!isset($_POST["submit"]) || $error_sec != ""){ ?>
			<tr>
				<td colspan="2">
					<table width="100%">
						<tr>
							<td><img src="images/logo_small.gif"></td>
							<td align="left" class="titulo_registro" valign="bottom">
								Restaurar Contraseña<br>
								<font class="titulo_registro_12">Sistema Integrado de Información Humanitaria para Colombia</font>
							</td>
						</tr>
					</table>
				</td>
			</tr>			
			<tr><td>&nbsp;</td></tr>
			<tr>
			  <td align="right" width="50%">Email</td>
				<td align="left"><input type="text" id="email" name="email" class="textfield" size="25" value="<?=$email?>" /></td>
			</tr>
	      <tr><td>&nbsp;</td></tr>
	      <tr>
	      	 <td colspan="2" align='center'>
			   <input type="hidden" name="accion" value="restaurar" />
			   <input type="hidden" name="cnrr" value="0" />
			   <input type="hidden" name="id_tipo" value="0" />
			   <input type="hidden" name="activo" value="0" />
			   <input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar();" />
			</td>
	      </tr>
	    <?
		}
		else {?>
		<tr>
			 <td align="center" class="titulo_login">Registro realizado con Éxito, el Administrador fue notificado para realizar la activación del usuario.</td>
		</tr>
		<?
		}
		?>
    </table>
	</form>
  </div>
</div>
</body>
</html>