<?
//INICIALIZACION DE VARIABLES
$tipo_usuario_dao = New TipoUsuarioDAO();
$tipo_usuario_vo = New TipoUsuario();
$chk_cnrr = array('no' => ' checked ', 'si' => '');

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}

$id_cat = 0;
if (isset($_GET["id_cat"])){
	$id_cat = $_GET["id_cat"];
}

//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
	$tipo_usuario_vo = $tipo_usuario_dao->Get($id);
	
	if ($tipo_usuario_vo->cnrr == 1)	$chk_cnrr['si'] = ' checked ';	
}

?>
<form method="POST" onsubmit="submitForm(event);return false;">
	<table class="tabla_insertar">
		<tr><td align="right">Nombre</td><td><input type="text" id="nombre" name="nombre" size="40" value="<?=$tipo_usuario_vo->nombre;?>" class="textfield" /></td></tr>
		<!--<tr><td align="right">Tipo para CNRR</td><td>No<input type="radio" name="cnrr" value=0 <?=$chk_cnrr['no']?> />&nbsp;Si<input type="radio" name="cnrr" value=1 <?=$chk_cnrr['si']?> /></td></tr>-->
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
			<input type="hidden" name="id" value="<?=$tipo_usuario_vo->id;?>" />									
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('nombre,Nombre','');" />
			</td>
		</tr>
	</table>
</form>	
