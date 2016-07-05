<?
//INICIALIZACION DE VARIABLES
$cons_hum_dao = New ConsHumDAO();
$cons_hum_vo = New ConsHum();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
	$cons_hum_vo = $cons_hum_dao->Get($id);
	
}
?>
<form method="POST" onsubmit="submitForm(event);return false;">
<table class="tabla_insertar">
	<tr><td align="right">Nombre</td><td><input type="text" id="nombre" name="nombre" size="40" value="<?=$cons_hum_vo->nombre;?>" class="textfield" /></td></tr>
	<tr>
		<td align="right">Icono</td>
		<td>
			<input type="text" id="icono" name="icono" size="40" value="<?=$cons_hum_vo->icono;?>" class="textfield" />
			<input type="button" name="to_images" value="Seleccionar Imagen" class="boton" onclick="window.open('select_image.php?field=icono&dir=cons_hum','','width=700,height=400,left=150,top=60,scrollbars=1,status=1')">
		</td>
	</tr>
	<?
	if ($accion == 'actualizar' && strlen($cons_hum_vo->icono) > 0){ ?>
		<tr>
			<td align="right">Icono actual</td>
			<td>
				<img src='../<?=$cons_hum_vo->icono?>'>
			</td>
		</tr>
	<?
	}
	?>
	<tr>
	  <td colspan="2" align='center'>
		  <br>
			<input type="hidden" name="accion" value="<?=$accion?>" />
			<input type="hidden" name="id" value="<?=$cons_hum_vo->id;?>" />
			<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('nombre,Nombre','');" />
		</td>
	</tr>
</table>
</form>
