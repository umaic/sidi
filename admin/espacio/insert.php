<?
//INICIALIZACION DE VARIABLES
$espacio_dao = New EspacioDAO();
$espacio_vo = New Espacio();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}


$id_cat = 0;
if (isset($_GET["id_cat"])){
  $id_cat = $_GET["id_cat"];
}

//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
  $espacio_vo = $espacio_dao->Get($id);
}

?>

<form method="POST" onsubmit="submitForm(event);return false;">
	<table class="tabla_insertar">
		<tr>
			<td align="right" width="30%">Papa</td>
			<td>
				<select name="id_papa" class="select">
					<option value=0>No aplica</option>
					<?
					$espacio_dao->ListarCombo('combo',$espacio_vo->id_papa,"id_papa=0 AND id_esp <> $id");
					?>
				</select>	
			</td>
		</tr>
		<tr><td align="right" width="30%">Nombre</td><td><input type="text" id="nombre" name="nombre" size="40" value="<?=$espacio_vo->nombre;?>" class="textfield" /></td></tr>
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
				<input type="hidden" name="id" value="<?=$espacio_vo->id;?>" />									
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('nombre,Nombre','');" />
			</td>
		</tr>
	</table>
</form>	
