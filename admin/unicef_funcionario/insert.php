<?
//INICIALIZACION DE VARIABLES
$funcionario_dao = New UnicefFuncionarioDAO();
$funcionario_vo = New UnicefFuncionario();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
    $id = $_GET["id"];
    $funcionario_vo = $funcionario_dao->Get($id);
}

?>

<form method="POST" onsubmit="submitForm(event);return false;">
	<table class="tabla_insertar">
		<tr><td><label>Cargo</label></td></tr>
        <tr><td><input type="text" id="nombre" name="nombre" size="40" value="<?=$funcionario_vo->nombre;?>" class="textfield" /></td></tr>
        <!--
        <tr><td>&nbsp;</td></tr>
		<tr><td><label>Apellido</td></tr>
        <tr> <td><input type="text" id="apellido" name="apellido" size="40" value="<?=$funcionario_vo->apellido;?>" class="textfield" /></td></tr>
        -->
        <tr><td>&nbsp;</td></tr>
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
			<input type="hidden" name="id" value="<?=$funcionario_vo->id;?>" />									
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('nombre,Nombre','');" />
			</td>
		</tr>
	</table>
</form>	
