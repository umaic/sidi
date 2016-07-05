<?
//INICIALIZACION DE VARIABLES
$cat_d_s_dao = New ContactoColOpDAO();
$cat_d_s_vo = New ContactoColOp();
$contacto_col_dao = New ContactoColDAO();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}


$id_cat = 0;
if (isset($_GET["id_cat"])){
  $id_cat = $_GET["id_cat"];
}

//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
  $cat_d_s_vo = $cat_d_s_dao->Get($id);
}

?>

<form method="POST" onsubmit="submitForm(event);return false;">
  <table border="0" cellpadding="3" cellspacing="1" width="70%" align="center">
	  <tr><td align="center"><b><?=ucfirst($accion)?> Opciones de las caracter&iacute;sticas de los contactos	</b></td></tr>
	</table>
	<br>
	<table class="tabla_insertar">
		<tr>
			<td align="right">Caracter&iacute;stica</td>
			<td>
				<select name="id_contacto_col" class="select">
					<? $contacto_col_dao->ListarCombo('combo',$cat_d_s_vo->id_contacto_col,''); ?>
				</select>
		<tr><td align="right">Opci&oacute;n</td><td><input type="text" id="nombre" name="nombre" size="40" value="<?=$cat_d_s_vo->nombre;?>" class="textfield" /></td></tr>
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
				<input type="hidden" name="id" value="<?=$cat_d_s_vo->id;?>" />
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('nombre,Opcion','');" />
			</td>
		</tr>
	</table>
</form>	
