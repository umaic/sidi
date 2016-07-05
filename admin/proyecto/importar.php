<script>
var innerObject = 'preview_import';
function startUpload(){
    document.getElementById(innerObject).innerHTML = "<font style='font-family:Arial;font-size:12px'><img src='/sissh/images/ajax/loading_ind.gif'>&nbsp;Generando...</font>";
}
</script>

<form action="<?=$_SERVER['PHP_SELF']?>" method="POST" enctype="multipart/form-data">
<table cellspacing="1" cellpadding="5" class="tabla_consulta" width="600" align="center">
	<tr><td align='center' class='titulo_lista'>IMPORTAR PROYECTOS</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>1. Seleccione el archivo CSV</td></tr>
	<tr><td><input id="archivo_csv" name="archivo_csv" type="file" class="textfield" size="60"><br><br><a href="#" onclick="window.open('org/col_csv_help.htm','','top=100,left=200,width=800,height=500,scrollbars=1')">? Ver Columnas del archivo CSV </a></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><b>Nota:</b> Si el archivo CSV contiene Organizaciones que ya existen en el sistema, este módulo actualizará la información de la Organización de acuerdo a la información contenida en el archivo.</td></tr>
	<tr>
	  <td align='center'>
		  <input type="hidden" name="accion" value="<?=$accion?>" />
		  <input type="submit" name="submit" value="Importar" class="boton" onclick="startUpload()" />	
	  </td>
	</tr>
	<tr><td id='preview_import'></td></tr>
</table>
<iframe id="import_iframe" name="import_iframe" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe> 
</form>
