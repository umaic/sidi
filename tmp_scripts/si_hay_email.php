<?
if (isset($_POST["submit"])){
	$para = $_POST["para"];
	$from = "contacto@umaic.org";
	$asunto = "prueba smtp";
/*	$message = "
   Apreciado(A)
&lt;br&gt;&lt;br&gt;
El usuario que ha solicitado para SIDI UMAIC - Colombia ya se encuentra activado.
&lt;br&gt;&lt;br&gt;
Su usuario es: &lt;b&gt;&lt;/b&gt;&lt;br&gt;
Su contraseña es: &lt;b&gt;&lt;/b&gt;&lt;br&gt;
&lt;br&gt;&lt;br&gt;
Recuerde que para acceder al sistema debe ir a la página de UMAIC Colombia umaic.org y hacer clic en el botón de sidi o directamente en la dirección umaic.org/sidi.
&lt;br&gt;&lt;br&gt;
Esperamos le sea útil esta herramienta y nos informe de los problemas que pueda tener trabajando con ella.
&lt;br&gt;&lt;br&gt;
Eventualmente estaremos enviando correos informando sobre datos que han sido actualizada o nuevas funcionalidades.
&lt;br&gt;&lt;br&gt;
Cordial saludo
&lt;br&gt;&lt;br&gt;
UMAIC - Colombia&lt;br&gt;
Colombia Tel: +57 1 6221100 &lt;br&gt;
e-mail: contacto@umaic.org&lt;br&gt;
https://umaic.org";
*/

$message = 'Hola';	
	require $_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/common/class.phpmailer.php";
	require $_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/common/phpmailer.lang-es.php";
	
	$mail = new PHPMailer();
	
	$mail->IsSMTP(); // set mailer to use SMTP
	
	$mail->From = $from;
	$mail->FromName = 'Prueba Sidi';
	//$mail->Username = '';
	//$mail->Password = '';
	//$mail->Host = '';
	
	$mail->AddAddress($para, "Usuario");
	
	$mail->WordWrap = 50;                                 // set word wrap to 50 characters
	$mail->IsHTML(true);                                  // set email format to HTML
	
	$mail->Subject = $asunto;
	$mail->Body    = $message;
	//$mail->AltBody = "This is the body in plain text for non-HTML mail clients";
	if($mail->Send() === false){
		echo "Error al enviar el mensaje. <p>";
		echo "Error: " . $mail->ErrorInfo;
		exit;
	}
	else{
		echo "<b>Enviado</b>";
	}
}
?>
<br />Prueba envio email por SMTP
<form method="POST">
Para: <input type="text" name="para"><br>
<input type="submit" name="submit" value="enviar">
</form>
