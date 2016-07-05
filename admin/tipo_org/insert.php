<?
//INICIALIZACION DE VARIABLES
$tipo_org_dao = New TipoOrganizacionDAO();
$tipo_org_vo = New TipoOrganizacion();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
	$tipo_org_vo = $tipo_org_dao->Get($id);
}

?>

<form method="POST" onsubmit="submitForm(event);return false;">
	<table class="tabla_insertar">
		<tr><td align="right">Nombre en Español</td><td><input type="text" id="nombre_es" name="nombre_es" size="40" value="<?=$tipo_org_vo->nombre_es;?>" class="textfield" /></td></tr>
		<tr><td align="right">Nombre en Inglés</td><td><input type="text" id="nombre_in" name="nombre_in" size="40" value="<?=$tipo_org_vo->nombre_in;?>" class="textfield" /></td></tr>				
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
			<input type="hidden" name="id" value="<?=$tipo_org_vo->id;?>" />									
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('nombre_es,Nombre en Español,nombre_in,Nombre en Inglés','');" />
			</td>
		</tr>
	</table>
</form>	
