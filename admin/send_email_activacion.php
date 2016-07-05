<?
//SEGURIDAD
include_once("seguridad.php");

//LIBRERIAS
include_once("fckeditor/fckeditor.php");

if (isset($_POST["submit"])){

	include_once("lib/common/class.phpmailer.php");

	$mail = new PHPMailer();

	$to = $_POST["to"];
	$from = $_POST["from"];
	$asunto = $_POST["asunto"];
	$mensaje = $_POST["mensaje"];
	$quien = $_POST["quien"];

	$mail->IsSMTP(); // set mailer to use SMTP

	$mail->From = $from;
	$mail->FromName = $quien;

	$mail->AddAddress($to);

	$mail->WordWrap = 50;                                 // set word wrap to 50 characters
	$mail->IsHTML(true);                                  // set email format to HTML

	$mail->Subject = $asunto;
	$mail->Body    = $mensaje;

	if(!$mail->Send()){
		?>
		<script>
		alert("No se ha podido enviar el correo, error: <?=$mail->ErrorInfo?>");
		</script>
		<?
	}
	else{
		?>
		<script>
		alert("Correo enviado con exito!");
		window.close();
		</script>
		<?
	}
}
else{
	if (isset($_GET["to"])){
	  $to = $_GET["to"];
	}

	if (isset($_GET["from"])){
	  $from = $_GET["from"];
	}

	if (isset($_GET["asunto"])){
	  $asunto = $_GET["asunto"];
	}

	if (isset($_GET["quien"])){
	  $quien = $_GET["quien"];
	}

	if (isset($_GET["login"])){
	  $login = $_GET["login"];
	}

	if (isset($_GET["pass"])){
	  $pass = $_GET["pass"];
	}
}

$body = "Apreciado(A)
<br><br>
El usuario que ha solicitado para el Sistema Integrado de Informaci&oacute;n - SIDI ya se encuentra activado.
<br><br>
Su usuario es: <b>$login</b><br>
Su contrase&ntilde;a es: <b>$pass</b>
<br><br>
Recuerde que para acceder al sistema debe ir a la p&aacute;gina: <a href='http://sidi.umaic.org'>sidi.umaic.org</a>.
<br><br>
Esperamos le sea &uacute;til esta herramienta y nos informe de los problemas que pueda tener trabajando con ella.
<br><br>
Cordial saludo,
<br><br>
$quien<br>
Unidad de Manejo y An&aacute;lisis de Informaci&oacute;n Colombia (UMAIC) <br>
Una iniciativa de PNUD, OCR, y OCHA <br>
Cra. 13 No. 93 - 12 Of. 402 <br>
Bogota, Colombia <br>
http://www.umaic.org";

//INTRO
$oFCKeditor_intro = new FCKeditor('mensaje') ;
$oFCKeditor_intro->BasePath = 'fckeditor/';
$oFCKeditor_intro->Width  = '600';
$oFCKeditor_intro->Height = '350';
$oFCKeditor_intro->Value = $body;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>SIDI - Envio de Correo</title>
<link href="../style/consulta.css" rel="stylesheet" type="text/css" />
<script src="js/general.js"></script>
</head>
<body><br>
<form method="POST">
<table align="center" border="0" cellpadding="5" cellspacing="3" width="600" class="table_login">
	<tr>
		<td colspan="2">
			<table width="100%">
				<tr>
					<td colspan="2" align="center" class="titulo_registro" valign="bottom">
						<img src="images/icono_email.jpg">&nbsp;Envio de correo<br>
						<font class="titulo_registro_12">Sistema Integrado de Informaci&oacute;n para Colombia</font>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="right" width="100">Para:</td>
		<td align="left"><input type="text" id="to" name="to" class="textfield" size="40" value=<?=$to?> /></td>
	</tr>
	<tr>
		<td align="right" width="100">De:</td>
		<td align="left"><input type="text" id="from" name="from" class="textfield" size="40" value="<?=$from?>" /></td>
	</tr>
	<tr>
		<td align="right" width="100">Asunto:</td>
		<td align="left"><input type="text" id="asunto" name="asunto" class="textfield" size="40" value="<?=$asunto?>" /></td>
	</tr>
	<tr>
		<td colspan="2" align="center">
			<? $oFCKeditor_intro->Create() ; ?>
		</td>
	</tr>
  <tr>
  	 <td colspan="2" align='center'>
  	 	<input type="hidden" name="quien" value="<?=$quien?>">
	   	<input type="submit" name="submit" value="Enviar" class="boton" onclick="return validar_forma('to,Para,from,De,asunto,Asunto,mensaje,Mensaje','');" />
	</td>
  </tr>
</table>
</form>
</body>
</html>
