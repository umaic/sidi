<?
//INICIALIZACION DE VARIABLES
$socio_dao = New UnicefSocioDAO();
$socio_vo = New UnicefSocio();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
    $id = $_GET["id"];
    $socio_vo = $socio_dao->Get($id);
}

?>

<form method="POST" onsubmit="submitForm(event);return false;">
	<table class="tabla_insertar">
		<tr><td><label>Socio Implementador</label></td></tr>
        <tr><td><textarea id="nombre" name="nombre" style="width:650px;height:100px" class="textfield"><?=$socio_vo->nombre;?></textarea></td></tr>
        <tr><td>&nbsp;</td></tr>
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
			<input type="hidden" name="id" value="<?=$socio_vo->id;?>" />									
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('nombre,Nombre','');" />
			</td>
		</tr>
	</table>
</form>	
