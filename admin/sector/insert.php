<?
//INICIALIZACION DE VARIABLES
$sector_dao = New SectorDAO();
$sector_vo = New Sector();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}
$return = 0;
if (isset($_GET["return"])){
  $return = 1;
}
//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
  $sector_vo = $sector_dao->Get($id);
}

?>

<form method="POST" onsubmit="submitForm(event);return false;">
	<table class="tabla_insertar">
		<tr><td align="right">Nombre en Español</td><td><input type="text" id="nombre_es" name="nombre_es" size="40" value="<?=$sector_vo->nombre_es;?>" class="textfield" /></td></tr>
		<tr><td align="right">Nombre en Ingl&eacute;s</td><td><input type="text" id="nombre_in" name="nombre_in" size="40" value="<?=$sector_vo->nombre_in;?>" class="textfield" /></td></tr>				
		<tr><td align="right">Definici&oacute;n</td><td><textarea name="def" cols="50" rows="5" class="textfield"><?=$sector_vo->def;?></textarea></td></tr>				
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
				<input type="hidden" name="id" value="<?=$sector_vo->id;?>" />
				<input type="hidden" name="return" value="<?=$return;?>" />								
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('nombre_es,Nombre en Español,nombre_in,Nombre en Inglés','');" />
			</td>
		</tr>
	</table>
</form>	
