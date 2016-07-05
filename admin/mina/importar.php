<?

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

?>

<form action="<?=$_SERVER['PHP_SELF']?>" method="POST" enctype="multipart/form-data">
<table cellspacing="1" cellpadding="5" class="tabla_consulta" width="600" align="center">
	<tr><td align='center' class='titulo_lista'>IMPORTAR EVENTOS CON MINAS</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><b>Seleccion el tipo de acción</b></td></tr>
	<tr><td><input type='radio' name='id_op' value=1>&nbsp;Borrar todo y cargar todos los eventos&nbsp;<input type='radio' name='id_op' value=2 checked>&nbsp;Adicionar registros nuevos</td></tr>
	<tr><td><b>Caracter separador de columnas</b>&nbsp;&nbsp;<input type="text" name="separador" size="2" value=";"></td></tr>
	<tr><td><b>Seleccione el archivo CSV</b></td></tr>
	<tr>
		<td>
			<input id="archivo_csv" name="archivo_csv" type="file" class="textfield" size="60"><br><br>
			<a href="#" onclick="window.open('mina/col_csv_help.htm','','top=100,left=200,width=800,height=500,scrollbars=1')">? Ver las columnas que debe tener del archivo CSV </a>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
	  <td align='center'>
		  <input type="hidden" name="accion" value="<?=$accion?>" />
		  <input type="submit" name="submit" value="Importar" class="boton" onclick="return validar_forma('archivo_csv,Archivo CSV','');" />	
	  </td>
	</tr>
</table>
</form>