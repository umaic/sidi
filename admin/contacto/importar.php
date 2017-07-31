<form action="<?=$_SERVER['PHP_SELF']?>" method="POST" enctype="multipart/form-data">
<table cellspacing="1" cellpadding="5" class="tabla_consulta" width="600" align="center">
	<tr><td align='center' colspan="2" class='titulo_lista'>IMPORTAR CONTACTOS</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><b>Seleccione el archivo de texto separado por | (barra)</b></td></tr>
	<tr>
		<td colspan="2">
			<input id="archivo_csv" name="userfile" type="file" class="textfield" size="60">
		</td>
	</tr>
	<tr>
        <td>
            La primera fila del archivo son los nombres de las columnas y el 
            archivo debe ser exportado desde la hoja de c&aacute;lculo separado por el caracter | (barra vertical) y encoding UTF-8
            <br /><br />
            <a href="https://sidi.umaic.org/sissh/OCHA_formato_contactos.xls">Consulte aqu&iacute; el formato a diligenciar para importaci&oacute;n</a>
        </td>
    </tr>
    <tr>
        <td colspan="2">Importar los contactos?
            <input type="radio" name="go" value="no" checked />No
            &nbsp;<input type="radio" name="go" value="si" />Si
        </td>
    </tr>
	<tr>
	  <td align='center' colspan="2">
		  <input type="hidden" name="accion" value="importar" />
		  <input type="submit" name="submit" value="Importar" class="boton" onclick="return validar_forma('archivo_csv,Archivo CSV','')" />
		  </td>
	</tr>
</table>
</form>
