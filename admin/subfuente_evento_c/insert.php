<?
//INICIALIZACION DE VARIABLES
$fuente_dao = New FuenteEventoConflictoDAO();
$fuente_vo = New FuenteEventoConflicto();
$subfuente_dao = New SubFuenteEventoConflictoDAO();
$subfuente_vo = New SubFuenteEventoConflicto();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
	$subfuente_vo = $subfuente_dao->Get($id);

}

?>

<form method="POST" onsubmit="submitForm(event);return false;">
	<table class="tabla_insertar">
	  <tr>
		  <td width="30%" align="right">Fuente</td>
			<td>
				<select id="id_fuente" name="id_fuente" class="select">
				  <option value="">Seleccione alguna</option>
					<? $fuente_dao->ListarCombo('combo',$subfuente_vo->id_fuente,''); ?>						
				</select>
			</td>
		</tr>
		<tr><td align="right">Nombre</td><td><input type="text" id="nombre" name="nombre" size="40" value="<?=$subfuente_vo->nombre;?>" class="textfield" /></td></tr>
        <tr><td colspan="2" align="center"><br />Este formulario tambien crea la fuente en MONITOR!</td></tr>
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
				<input type="hidden" name="id" value="<?=$subfuente_vo->id;?>" />									
				<input type="submit" name="submit" value="Aceptar" class="" onclick="return validar_forma('id_fuente,Fuente,nombre,Nombre','');" />
			</td>
		</tr>
	</table>
</form>	
