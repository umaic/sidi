<?
//INICIALIZACION DE VARIABLES
$pais_dao = New PaisDAO();
$depto_dao = New DeptoDAO();
$depto_vo = New Depto();

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
  $depto_vo = $depto_dao->Get($id);
  	$readonly = " readonly ";  
}

?>

<form method="POST" onsubmit="submitForm(event);return false;">
	<table class="tabla_insertar">
		<tr><td align="right">ID</td><td><input type="text" id="id" name="id" size="6" value="<?=$depto_vo->id;?>" class="textfield" <?=$readonly;?> /></td></tr>
		<tr>
			<td align="right">Pais</td>
			<td>
				<select name="id_pais" class="select">
					<? $pais_dao->ListarCombo("combo",$depto_vo->id_pais,""); ?>
				</select>
			</td>
		</tr>
		<tr><td align="right">Nombre</td><td><input type="text" id="nombre" name="nombre" size="40" value="<?=$depto_vo->nombre;?>" class="textfield" /></td></tr>
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('id,ID,nombre,Nombre','');" />
			</td>
		</tr>
	</table>
</form>	
