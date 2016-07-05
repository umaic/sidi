<?
//INICIALIZACION DE VARIABLES
$municipio_dao = New MunicipioDAO();
$municipio_vo = New Municipio();
$depto_dao = New DeptoDAO();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

$readonly = "";
//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
  	$municipio_vo = $municipio_dao->Get($id);
  	$readonly = " readonly ";
}

?>

<form method="POST" onsubmit="submitForm(event);return false;">
	<table class="tabla_insertar">
		<tr><td align="right">ID</td><td><input type="text" id="id" name="id" size="6" value="<?=$municipio_vo->id;?>" class="textfield" <?=$readonly;?> /></td></tr>
		<tr>
			<td align="right">Departamento</td>
			<td>
				<select name="id_depto" class="select">
				<? $depto_dao->ListarCombo('combo',$municipio_vo->id_depto,''); ?>
				</select>
			</td>
		</tr>
		<tr><td align="right">Nombre</td><td><input type="text" id="nombre" name="nombre" size="40" value="<?=$municipio_vo->nombre;?>" class="textfield" /></td></tr>
		<tr><td align="right">Número de Manzanas</td><td><input type="text" id="manzanas" name="manzanas" size="40" value="<?=$municipio_vo->manzanas;?>" class="textfield" /></td></tr>
		<tr><td align="right">Año de creación</td><td><input type="text" id="nacimiento" name="nacimiento" size="40" value="<?=$municipio_vo->nacimiento;?>" class="textfield" /></td></tr>
		<tr><td align="right">Acto Administrativo mediante <br>el cual se creo el municipio</td><td><textarea id="acto_admin" name="acto_admin" cols="40" rows="5" class="textfield"><?=$municipio_vo->acto_admin;?></textarea></td></tr>
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('id,ID,nombre,Nombre','');" />
			</td>
		</tr>
	</table>
</form>	
