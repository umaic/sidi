<?
//INICIALIZACION DE VARIABLES
$indicador_dao = New UnicefIndicadorDAO();
$indicador_vo = New UnicefIndicador();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

$check_resultado = '';
$check_producto = ' checked ';

//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
    $id = $_GET["id"];
    $indicador_vo = $indicador_dao->Get($id);

    $check_resultado = ($indicador_vo->resultado == 1) ? ' checked ' : '';
    $check_producto = ($indicador_vo->producto_cpap == 1) ? ' checked ' : '';
}

?>

<form method="POST" onsubmit="submitForm(event);return false;">
	<table class="tabla_insertar">
		<tr><td><label>Indicador</label></td></tr>
        <tr><td><textarea id="nombre" name="nombre" style="width:650px;height:100px" class="textfield"><?=$indicador_vo->nombre;?></textarea></td></tr>
        <tr><td>&nbsp;</td></tr>
		<tr><td><label>Este Indicador aplica para</label></td></tr>
        <tr><td><input type="radio" name="aplica" value="resultado" <?=$check_resultado?>>&nbsp;Resultado CPD&nbsp;&nbsp;<input type="radio" name="aplica" value="producto" <?=$check_producto?>>Producto CPAP&nbsp;</td></tr>
        <tr><td>&nbsp;</td></tr>
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
			<input type="hidden" name="id" value="<?=$indicador_vo->id;?>" />									
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('nombre,Nombre','');" />
			</td>
		</tr>
	</table>
</form>	
