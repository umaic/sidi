<?
//INICIALIZACION DE VARIABLES
$cat_dao = New CatEventoConflictoDAO();
$cat_vo = New CatEventoConflicto();
$subcat_dao = New SubCatEventoConflictoDAO();
$subcat_vo = New SubCatEventoConflicto();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
	$subcat_vo = $subcat_dao->Get($id);

	$chk_info = ($subcat_vo->info_vict == 1) ? 'checked' : '';
}

?>

<form method="POST" onsubmit="submitForm(event);return false;">
	<table class="tabla_insertar">
	  <tr>
		  <td width="30%" align="right">Categoria</td>
			<td>
				<select id="id_cat" name="id_cat" class="select">
				  <option value="">Seleccione alguna</option>
					<? $cat_dao->ListarCombo('combo',$subcat_vo->id_cat,''); ?>						
				</select>
			</td>
		</tr>
		<tr><td align="right">Nombre</td><td><input type="text" id="nombre" name="nombre" size="40" value="<?=$subcat_vo->nombre;?>" class="textfield" /></td></tr>
		<tr><td align="right">Incluye la Info de caracteristicas de Victimas</td><td><input type="checkbox" name="info_vict" value="1" <?=$chk_info?>></td></tr>
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
				<input type="hidden" name="id" value="<?=$subcat_vo->id;?>" />									
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('id_cat,Categoria,nombre,Nombre','');" />
			</td>
		</tr>
	</table>
</form>	
