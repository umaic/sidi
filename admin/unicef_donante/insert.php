<?
//INICIALIZACION DE VARIABLES
$donante_dao = New UnicefDonanteDAO();
$donante_vo = New UnicefDonante();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
    $id = $_GET["id"];
    $donante_vo = $donante_dao->Get($id);
}

?>

<form method="POST" onsubmit="submitForm(event);return false;">
	<table class="tabla_insertar">
		<tr><td><label>C&oacute;digo Donante</label></td></tr>
        <tr><td><input type="text" name="codigo" id="codigo" class="textfield" value="<?=$donante_vo->codigo?>"></td></tr>
        <tr><td>&nbsp;</td></tr>
		<tr><td><label>Nombre Donante</label></td></tr>
        <tr><td><textarea id="nombre" name="nombre" style="width:650px;height:100px" class="textfield"><?=$donante_vo->nombre;?></textarea></td></tr>
        <tr><td>&nbsp;</td></tr>
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
                <input type="hidden" name="id" value="<?=$donante_vo->id;?>" />									
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('nombre,Nombre,codigo,Código','');" />
			</td>
		</tr>
	</table>
</form>	
