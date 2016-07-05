<?
//INICIALIZACION DE VARIABLES
$condicion_dao = New CondicionMinaDAO();
$dao = New SubCondicionDAO();
$vo = New SubCondicion();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
	$vo = $dao->Get($id);

	$chk_info = ($vo->info_vict == 1) ? 'checked' : '';
}

?>

<form method="POST" onsubmit="submitForm(event);return false;">
	<table class="tabla_insertar">
	  <tr>
		  <td width="30%" align="right">Condici&oacute;n</td>
			<td>
				<select id="id_condicion" name="id_condicion" class="select">
				  <option value="">Seleccione alguna</option>
					<? $condicion_dao->ListarCombo('combo',$vo->id_condicion,''); ?>						
				</select>
			</td>
		</tr>
		<tr><td align="right">Nombre</td><td><input type="text" id="nombre" name="nombre" size="40" value="<?=$vo->nombre;?>" class="textfield" /></td></tr>
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
				<input type="hidden" name="id" value="<?=$vo->id;?>" />									
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('id_condicion,Condicion,nombre,Nombre','');" />
			</td>
		</tr>
	</table>
</form>	
