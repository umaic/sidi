<?
//LIBRERIAS
include_once ('js/calendar/calendar.php');

//INICIALIZACION DE VARIABLES
$fuente_dao = New FuenteDAO();
$clase_dao = New ClaseDesplazamientoDAO();
$tipo_dao = New TipoDesplazamientoDAO();
$periodo_dao = New PeriodoDAO();
$calendar = new DHTML_Calendar('js/calendar/','es', 'calendar-win2k-1', false);
$calendar->load_files();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

?>
<script>
function copiarCombos(){
	CopiarOpcionesCombo(document.getElementById('id_periodo'),document.getElementById('id_periodo_h'));
}
function  preCarga(){
	copiarCombos();

	document.getElementById('div_preCarga').style.visibility = 'visible';
	document.getElementById('iframe_preCarga').style.visibility = 'visible';

	if(validar_forma('archivo_csv,Archivo CSV','')){
		getDataV1('preCargaDesplazamiento','ajax_data.php?object=preCargaDesplazamiento&periodos='+document.getElementById('id_periodo_h').value,'iframe_preCarga');
	}
	else{
		return false;
	}

}


</script>
<script src="js/ajax.js"></script>

<form action="<?=$_SERVER['PHP_SELF']?>" method="POST" enctype="multipart/form-data">
<table cellspacing="1" cellpadding="5" class="tabla_consulta" width="600" align="center">
	<tr><td align='center' colspan="2" class='titulo_lista'>IMPORTAR DATOS DE DESPLAZAMIENTO</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td><b>Seleccione la clase</b>
		<td>
			<select name="id_clase" class="select">
			<? $clase_dao->ListarCombo('combo','','id_clase_despla=3'); ?>
			</select>
		</td>
	</tr>
	<tr>
		<td><b>Seleccione el tipo</b></td>
		<td>
			<select name="id_tipo" class="select">
			<? $tipo_dao->ListarCombo('combo','','id_tipo_despla=3'); ?>
			</select>
		</td>
	</tr>
	<tr>
		<td><b>Seleccione la fuente</b></td>
		<td>
			<select name="id_fuente" class="select">
			<? $fuente_dao->ListarCombo('combo','','id_fuedes=1'); ?>
			</select>
		</td>
	</tr>
	<tr>
		<td><b>Fecha de Corte</b></td>
		<td>
			<? $calendar->make_input_field(
			// calendar options go here; see the documentation and/or calendar-setup.js
			array('firstDay'       => 1, // show Monday first
			     'ifFormat'       => '%Y-%m-%d',
			     'timeFormat'     => '12'),
			// field attributes go here
			array('class'       => 'textfield',
				  'size'		=> '10',
				  'value'		=> '2008-12-31',
			      'name'        => 'f_corte'));

			?>
		</td>
	</tr>	<tr>
		<td colspan="2"><input type='radio' name='acc' value='borrar'>&nbsp;Borrar los registros existentes para esta Clase-Tipo-Fuente y cargar nueva info
			<br><input type='radio' name='acc' value='adicionar' checked>&nbsp;Adicionar registros</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><b>Seleccione el archivo CSV</b></td></tr>
	<tr>
		<td colspan="2">
			<input id="archivo_csv" name="archivo_csv" type="file" class="textfield" size="60">
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td colspan="2">
			<table class="tabla_consulta" cellspacing="1" cellpadding="5">
				<tr><td class="titulo_lista" align="center" colspan="3">SINCRONIZACION DE PERIODOS</td></tr>
				<tr><td colspan="2">Seleccione y ordene los periodos que tiene el archivo que va a importar, el orden de esta lista debe coincidi con el orden de las columnas de periodos en el archivo</td></tr>
				<tr>
					<td align="center">
						<select id="id_periodo_t" name="id_periodo_t" size="30" multiple class="select">
							<?
							//PERIODO
							$periodo_dao->ListarCombo('combo','','');
							?>
						</select>&nbsp;&nbsp;<input type="button" value=">>" class="boton" onclick="AddOption(document.getElementById('id_periodo_t').options[document.getElementById('id_periodo_t').selectedIndex].text,document.getElementById('id_periodo_t').options[document.getElementById('id_periodo_t').selectedIndex].value,document.getElementById('id_periodo'))"><br><br>
					</td>
					<td align="center" valign="top">
						<select id="id_periodo" name="id_periodo" size="30" multiple class="select" style="width:150px">
						<?
						//RETORNA DEL AVISO DE LAS COLUMNAS DE PERIODO NO COINCIDEN
						if (isset($_GET["id_periodos"])){
							$id_periodo = split("[|]",$_GET["id_periodos"]);
							foreach ($id_periodo as $id_per){
								$periodo = $periodo_dao->Get($id_per);
								echo "<option value=$id_per>$periodo->nombre</option>";
							}
						}
						
						//AYUDA PARA IMPORTACION MASIVA
						
						//TIPO INDIVIDUAL
						//$aa = array(66,68,80,92,208,104,116,128,211,44,140,152,164,214,175,186,197,217,45,69,81,93,209,105,117,129,212,46,141,153,165,215,176,187,198,218,47,70,82,94,1,106,118,130,2,48,142,154,166,3,177,188,199,4,49,71,83,95,5,107,119,131,6,50,143,155,167,7,178,189,200,8,51,72,84,96,9,108,120,132,10,52,144,156,168,11,179,190,201,12,53,73,85,97,13,109,121,133,14,54,145,157,169,15,180,191,202,16,55,74,86,98,17,110,122,134,18,56,146,158,170,19,181,192,203,20,57,75,87,99,21,111,123,135,22,58,147,159,171,23,182,193,204,24,59,76,88,100,25,112,124,136,26,60,148,160,172,27,183,194,205,28,61,77,89,101,219,113,125,137,221,62,149,161,173,223,184,195,206,224,63,78,90,102,220,114,126,138,222,64,150,162);
						
						//TIPO MASIVO
						/*
						$aa = array(66,92,208,128,211,44,140,214,45,69,93,209,105,117,129,212,46,141,215,187,198,218,47,82,1,130,2,48,142,154,166,3,199,4,49,71,83,95,5,107,119,131,6,50,143,155,167,7,178,189,200,8,51,72,84,96,9,108,120,132,10,52,144,156,168,11,179,190,201,12,53,73,85,97,13,109,121,133,14,54,145,157,169,15,180,191,202,16,55,74,86,98,17,110,122,134,18,56,146,158,170,19,181,192,203,20,57,75,87,99,21,111,123,135,22,58,147,159,171,23,182,193,204,24,59,76,88,100,25,112,124,136,26,60,148,160,172,27,183,194,205,28,61,77,89,101,219,113,125,137,221,62,149,161,173,223,184,195,206,224,63,78,90,102,220,114,126,138,222,64,150,162);
						
						foreach ($aa as $id_per){
							$periodo = $periodo_dao->Get($id_per);
							echo "<option value=$id_per>$periodo->nombre</option>";
						}
						*/
						
						?>
						
						
						</select>
					</td>
					<td align="left">
					<input type="button" value="Mover a " class="boton" onclick="moveOption(document.getElementById('id_periodo'),'moveTo',document.getElementById('posX').value)">&nbsp;&nbsp;Pos:&nbsp;<input id="posX" type="text" size="3" class="textfield"><br><br>
					<input type="button" value="Mover Arriba" class="boton" onclick="moveOption(document.getElementById('id_periodo'),'up','')"><br><br>
					<input type="button" value="Mover Abajo" class="boton" onclick="moveOption(document.getElementById('id_periodo'),'down','')"><br><br>
					<input type="button" value="Borrar Opción del listado" class="boton" onclick="delete_option(document.getElementById('id_periodo'))">
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
			</table>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>
			* No olvide revisar que la carpeta consulta/resumen/desplazamiento/ quede vac&iacute;a (eliminado cache) 
			luedo de realizada la importaci&oacute;n, el sistema realiza esta operaci&oacute;n autom&aacute;ticamente
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
	  <td align='center' colspan="2">
		  <input type="hidden" name="accion" value="<?=$accion?>" />
		  <input type="hidden" id="id_periodo_h" name="id_periodo_h" />
		  <!--input type="button" name="submit" value="Importar" class="boton" onclick="preCarga();" />-->
		  <input type="submit" name="submit" value="Importar" class="boton" onclick="copiarCombos();return validar_forma('f-calendar-field-1,Fecha de Corte,archivo_csv,Archivo CSV','')" />
		  </td>
	</tr>
</table>
</form>
