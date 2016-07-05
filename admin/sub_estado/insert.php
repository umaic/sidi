<?
//INICIALIZACION DE VARIABLES
$estado_dao = New EstadoMinaDAO();
$dao = New SubEstadoDAO();
$vo = New SubEstado();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
	$vo = $dao->Get($id);

	$chk_info = ($vo->info_vict == 1) ? 'checked' : '';
}

?>

<form method="POST" onsubmit="submitForm(event);return false;">
  <table border="0" cellpadding="3" cellspacing="1" width="70%" align="center">
	  <tr><td align="center"><b><?=ucfirst($accion)?> Sub Estado</b></td></tr>
	</table>
	<br>
	<table class="tabla_insertar">
	  <tr>
		  <td width="30%" align="right">Estado</td>
			<td>
				<select id="id_estado" name="id_estado" class="select">
				  <option value="">Seleccione alguna</option>
					<? $estado_dao->ListarCombo('combo',$vo->id_estado,''); ?>						
				</select>
			</td>
		</tr>
		<tr><td align="right">Nombre</td><td><input type="text" id="nombre" name="nombre" size="40" value="<?=$vo->nombre;?>" class="textfield" /></td></tr>
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
				<input type="hidden" name="id" value="<?=$vo->id;?>" />									
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('id_estado,Estado,nombre,Nombre','');" />
			</td>
		</tr>
	</table>
</form>	