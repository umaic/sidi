<?
//INICIALIZACION DE VARIABLES
$riesgo_hum_dao = New RiesgoHumDAO();
$riesgo_hum_vo = New RiesgoHum();
$accion = $_GET["accion"];

//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
	$riesgo_hum_vo = $riesgo_hum_dao->Get($id);
	
}
?>
<form method="POST" onsubmit="submitForm(event);return false;">
<table class="tabla_insertar">
	<tr><td align="right">Nombre</td><td><input type="text" id="nombre" name="nombre" size="40" value="<?=$riesgo_hum_vo->nombre;?>" class="textfield" /></td></tr>
	<tr>
		<td align="right">Icono</td>
		<td>
			<input type="text" id="icono" name="icono" size="40" value="<?=$riesgo_hum_vo->icono;?>" class="textfield" />
			<input type="button" name="to_images" value="Seleccionar Imagen" class="boton" onclick="window.open('select_image.php?field=icono&dir=riesgo_hum','','width=700,height=400,left=150,top=60,scrollbars=1,status=1')">
		</td>
	</tr>
	<?
	if ($accion == 'actualizar' && strlen($riesgo_hum_vo->icono) > 0){ ?>
		<tr>
			<td align="right">Icono actual</td>
			<td>
				<img src='../<?=$riesgo_hum_vo->icono?>'>
			</td>
		</tr>
	<?
	}
	?>
	<tr>
	  <td colspan="2" align='center'>
		  <br>
			<input type="hidden" name="accion" value="<?=$accion?>" />
			<input type="hidden" name="id" value="<?=$riesgo_hum_vo->id;?>" />
			<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('nombre,Nombre','');" />
		</td>
	</tr>
</table>
</form>
