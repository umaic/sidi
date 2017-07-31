<?
//LIBRERIAS
include_once ('js/calendar/calendar.php');

//INICIALIZACION DE VARIABLES
$dato_dao = New DatoSectorialDAO();
$unidad_dao = New UnidadDatoSectorDAO();
$calendar = new DHTML_Calendar('js/calendar/','es', 'calendar-win2k-1', false);
$calendar->load_files();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

?>

<form action="<?=$_SERVER['PHP_SELF']?>" method="POST" enctype="multipart/form-data">
<table cellspacing="1" cellpadding="5" class="tabla_consulta" width="600" align="center">
	<tr><td align='center' class='titulo_lista'>IMPORTAR DATOS SECTORIALES</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><b>Seleccione el Dato (Se listan solo los datos que no son formulados)</b></td></tr>
	<tr>
		<td>
			<select name="id_dato" class="select">
			<? $dato_dao->ListarCombo('combo','',"FORMULA_DATO=''"); ?>
			</select>
		</td>
	</tr>
	<tr><td><b>Seleccione la Unidad</b></td></tr>
	<tr>
		<td>
			<select name="id_unidad" class="select">
			<? $unidad_dao->ListarCombo('combo','',''); ?>
			</select>
		</td>
	</tr>
	<tr><td><b>Periodo</b></td></tr>
	<tr>
		<td>
			Desde&nbsp;
			<? $calendar->make_input_field(
			// calendar options go here; see the documentation and/or calendar-setup.js
			array('firstDay'       => 1, // show Monday first
			     'ifFormat'       => '%Y-%m-%d',
			     'timeFormat'     => '12'),
			// field attributes go here
			array('class'       => 'textfield',
				  'size'		=> '10',
				  //'value'		=> '200-1-1',
			      'name'        => 'f_ini'));

			?>&nbsp;&nbsp;
			Hasta&nbsp;
			<? $calendar->make_input_field(
			 // calendar options go here; see the documentation and/or calendar-setup.js
			 array('firstDay'       => 1, // show Monday first
			       'ifFormat'       => '%Y-%m-%d',
			       'timeFormat'     => '12'),
			 // field attributes go here
			 array('class'       => 'textfield',
			 		'size'		=> '10',
			 		//'value'		=> '200-12-31',
			       'name'        => 'f_fin'));
			?>
		</td>
	</tr>
	<tr><td><b>Dato Departamental o Municipal?</b></td></tr>
    <tr>
        <td>
            Si el dato que va a cargar tiene informaci&oacute;n a nivel departamental y municipal (e.j. IRSH), 
            <b>PRIMERO</b> se debe cargar la informaci&oacute;n <b>departamental</b> y luego la municipal.  De lo contrario
            el sistema no le dejar&aacute; cargar la info departamental. <br /><br />
            Si existe informaci&oacute;n a nivel municipal y necesita cargar departamental, borre los datos del dato para el periodo indicado usando la
            opci&oacute;n Borrar valores para en la administraci&oacute;n de datos sectoriales.  
            M&aacute;s en el <a href="http://sidi.umaic.org/wiki/4W" target="_blank">WIKI</a>
        </td></tr>
	<tr><td><input type='radio' name='dato_para' value=1>&nbsp;Departamentos&nbsp;<input type='radio' name='dato_para' value=2 checked>&nbsp;Municipios</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><b>Seleccione el archivo CSV</b></td></tr>
	<tr>
		<td>
			<input id="archivo_csv" name="archivo_csv" type="file" class="textfield" size="60">
			<br><br><a href="#" onclick="window.open('dato_s_valor/col_csv_help.htm','','top=100,left=200,width=800,height=500,scrollbars=1')">? Ver las columnas que debe tener del archivo CSV </a>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><b>Notas</b><br><br>
			1. La columna que tiene el valor del dato no debe tener separadores de mil, debe ser solo el número, ej. 34083 y <b>NO</b> 34,083
			<br><br>
			2. Los valores con unidad en Porcentaje, deben estar dados en la escala 1 a 100.
			<br><br>
			3. Los valores decimales deben tener como separador de decimal el caracter punto "."
			<br><br>
			4. La primera fila del archivo CSV deben ser los titulos de las columnas
			
	</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><b>Luego de importar un bloque de datos, es necesario Totalizar los datos Nacionales y Departamentales, para esto ejecute la siguiente opci&oacute;n <img src="images/home/totalizar.png">&nbsp;<a href="#" onclick="if(confirm('Esta opción calculará los totales Departamentales y Nacionales con los datos actuales en el sistema, esta seguro que desea ejecutar esta opción ?\n\nEl proceso tardará varios minutos...')){window.open('../cron_jobs/totalizar_d_sectorial.php','','top=200,left=250,width=400,height=150');}">Totalizar</a> o ejecutela desde el men&uacute; anterior como se oberva en la gr&aacute;fica</b>.</td></tr>
	<tr><td><img src="images/dato_sectorial/paso_totalizar.png"></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
	  <td align='center'>
		  <input type="hidden" name="accion" value="<?=$accion?>" />
		  <input type="submit" name="submit" value="Importar" class="boton" onclick="return validar_forma('f-calendar-field-1,Periodo Desde,f-calendar-field-2,Periodo Hasta,archivo_csv,Archivo CSV','');" />
	  </td>
	</tr>
</table>
</form>
