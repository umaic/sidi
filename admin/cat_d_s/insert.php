<?
//INICIALIZACION DE VARIABLES
$cat_d_s_dao = New CategoriaDatoSectorDAO();
$cat_d_s_vo = New CategoriaDatoSector();
$sector_dao = New SectorDAO();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
	$cat_d_s_vo = $cat_d_s_dao->Get($id);
}

?>

<form method="POST" onsubmit="submitForm(event);return false;">
	<table class="tabla_insertar">
		<tr>
			<td align="right">Sector</td>
			<td>
				<select name="id_sector" class="select">
					<? $sector_dao->ListarCombo('combo',$cat_d_s_vo->id_sector,''); ?>
				</select>
		<tr><td align="right">Nombre</td><td><input type="text" id="nombre" name="nombre" size="40" value="<?=$cat_d_s_vo->nombre;?>" class="textfield" /></td></tr>
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
				<input type="hidden" name="id" value="<?=$cat_d_s_vo->id;?>" />
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('nombre,Nombre','');" />
			</td>
		</tr>
	</table>
</form>	
