<?
//INICIALIZACION DE VARIABLES
$usuario_dao = New UsuarioDAO();
$usuario_vo = New Usuario();
$tipo_usuario_dao = New TipoUsuarioDAO();
$tipo_usuario_vo = New TipoUsuario();
$chk_cnrr = array('no' => ' checked ', 'si' => '');
$chk_activo = array('no' => ' checked ', 'si' => '');
$org_dao = New OrganizacionDAO();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

$id_tipo = 0;
if (isset($_GET["id_tipo"])){
	$id_tipo = $_GET["id_tipo"];
}

//Define los tipos de usuario que deben ir asociados con una org
$tipo_rel_org = array(27);
$display_tr_org = "none";

//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
	$usuario_vo = $usuario_dao->Get($id);

	if ($usuario_vo->cnrr == 1)	$chk_cnrr['si'] = ' checked ';
	if ($usuario_vo->activo == 1)	$chk_activo['si'] = ' checked ';
	if ($usuario_vo->id_org > 0)	$display_tr_org = '';

}
if($usuario_vo->id_tipo == 0)	$usuario_vo->id_tipo = 18;

//Para envio de correo
$quien = $usuario_dao->get($_SESSION["id_usuario_s"]);

?>
<form method="POST" onsubmit="submitForm(event);return false;">
  <!--
  <table border="0" cellpadding="3" cellspacing="1" width="70%" align="center">
      <tr>
	  	<td align="center"><img src="/sissh/admin/images/icono_enviar_email.jpg">&nbsp;<a href="#" onclick="window.open('send_email_activacion.php?to=<?=$usuario_vo->email?>&asunto=Confirmación Activación - OCHA sidih&from=<?=$quien->email?>&login=<?=$usuario_vo->login?>&pass=<?=$usuario_vo->pass?>&quien=<?=$quien->nombre?>','','top=20,left=20,width=650,height=650');">Enviar correo de confirmaci&oacute;n</a></td>
	  </tr>
	</table>
	<br>
    -->
	<table class="tabla_insertar_usuario">
		<tr>
			  <td align="right" width="30%">Tipo de Usuario</td>
				<td width="70%">
				  <select name="id_tipo" class="select" onchange="setOrg(this.value)">
						<? $tipo_usuario_dao->ListarCombo('combo',$usuario_vo->id_tipo,'id_tipo_usuario IN (31,32,40)'); ?>						
					</select>
				</td>
			</tr>				
		<tr><td align="right">Nombre</td><td><input type="text" id="nombre" name="nombre" size="40" value="<?=$usuario_vo->nombre;?>" class="textfield" /></td></tr>
		<tr><td align="right">Login</td><td><input type="text" id="login" name="login" size="40" value="<?=$usuario_vo->login;?>" class="textfield" /></td></tr>
		<tr><td align="right">Password</td><td><input type="password" id="pass" name="pass" size="40" value="<?=$usuario_vo->pass;?>" class="textfield" /></td></tr>
		<tr><td align="right">Email</td><td><input type="text" id="email" name="email" size="40" value="<?=$usuario_vo->email;?>" class="textfield" /></td></tr>
		<tr><td align="right">Organizaci&oacute;n</td><td><input type="text" id="org" name="org" size="40" value="<?=$usuario_vo->org;?>" class="textfield" /></td></tr>
		<tr><td align="right">Persona de cont&aacute;cto en UNICEF</td><td><input type="text" id="punto_contacto" name="punto_contacto" size="40" value="<?=$usuario_vo->punto_contacto;?>" class="textfield" /></td></tr>
		<tr><td align="right">Tel&eacute;fono</td><td><input type="text" id="tel" name="tel" size="40" value="<?=$usuario_vo->tel;?>" class="textfield" /></td></tr>
		<tr><td align="right">Activo</td><td><input type="radio" name="activo" value=0 <?=$chk_activo['no']?> />&nbsp;No&nbsp;<input type="radio" name="activo" value=1 <?=$chk_activo['si']?> />&nbsp;Si</td></tr>
		<!-- <tr><td align="right">Usuario CNRR</td><td>No<input type="radio" name="cnrr" value=0 <?=$chk_cnrr['no']?> />&nbsp;Si<input type="radio" name="cnrr" value=1 <?=$chk_cnrr['si']?> /></td></tr>-->
		<tr id="tr_org" style="display:<?=$display_tr_org?>">
			<td align="right">Organizaci&oacute;n</td>
			<td>
				<select id="id_org" name="id_org" class="select">
					<option value=0></option>
					<? $org_dao->ListarCombo('combo',$usuario_vo->id_org,'id_tipo=4'); ?>						
				</select>
			</td>
		</tr>
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
		        <input type="hidden" name="cnrr" value=0>
    			<input type="hidden" name="id" value="<?=$usuario_vo->id;?>" />									
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('nombre,Nombre,login,Login,pass,Password',document.getElementById('email').value);" />
			</td>
		</tr>
	</table>
</form>	
