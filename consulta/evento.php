<?
//LIBRERIAS
include_once("consulta/lib/libs_evento.php");

//INICIALIZACION DE VARIABLES
$evento_dao = New EventoDAO();
$depto_dao = New DeptoDAO();
$municipio_dao = New MunicipioDAO();
$tipo_dao = New TipoEventoDAO();
$actor_dao = New ActorDAO();
$cat_tipo_dao = New CatTipoEventoDAO();
$cons_hum_dao = New ConsHumDAO();
$cons_hum_vo = New ConsHum();
$riesgo_hum_dao = New RiesgoHumDAO();
$riesgo_hum_vo = New RiesgoHum();
$calendar = new DHTML_Calendar('admin/js/calendar/','es', 'calendar-win2k-1', false);
$calendar->load_files();

$id_depto = Array();
$num_deptos = 0;
if (isset($_GET['id_depto'])){
	$id_depto_s = split(",",$_GET['id_depto']);
	$id_depto = $id_depto_s[0];
	$num_deptos = count($id_depto_s);
	$id_depto = $id_depto_s;
	$id_depto_url = $id_depto;
}

//ACCION DE LA FORMA
if (isset($_POST["submit"])){
  $evento_dao->Reportar();
}

//REPORTE EXCEL
if (isset($_POST["reportar"])){
  $evento_dao->ReporteEvento($_POST['id_eventos'],$_POST['pdf'],$_POST['basico']);
}

//VER DETALLES
if (isset($_GET["method"]) && $_GET["method"] == "Ver"){
  $evento_dao->Ver($_GET["param"])  ;
}

//INFORME DIARIO
if (isset($_POST["informe_diario"])){
  $evento_dao->ReporteDiarioHTML($_POST['f_ini']);
  die;
}

//INFORME SEMANAL
if (isset($_POST["informe_semanal"]) || isset($_GET["f_ini"])){
  if (isset($_POST["informe_semanal"])){
    $f_ini = $_POST['f_ini'];
    $f_fin = $_POST['f_fin'];
  }
  if (isset($_GET["f_ini"])){
    $f_ini = $_GET['f_ini'];
    $f_fin = $_GET['f_fin'];
  }

  $evento_dao->ReporteSemanalHTML($f_ini,$f_fin);
  die;
}

?>

<script>
function unHide(td,img){
  if (document.getElementById(td).style.display == 'none'){
	  document.getElementById(td).style.display = '';
	  document.getElementById(img).src = 'images/flecha_down.gif';
  }
  else{
	  document.getElementById(td).style.display = 'none';
	  document.getElementById(img).src = 'images/flecha.gif';
  }
}

function validarComboM(ob){
  	selected = new Array();
	for (var i = 0; i < ob.options.length; i++){
	  if (ob.options[ i ].selected)
		  selected.push(ob.options[ i ].value);
	}

	if (selected.length == 0){
	  return false;
	}
	else{
	  return true;
	}
}
function validar_criterios(){

  	var error = 1;
  	var filtros = Array('id_actor','id_cons','id_riesgo','id_depto');

  	for (f=0;f<filtros.length;f++){
	  	if (validarComboM(document.getElementById(filtros[f]))){
		    error = 0;
		}
	}

  	if (document.getElementById('id_cat').value != ""){
	    error = 0;
	}

	if (document.getElementById('f-calendar-field-1').value != "" && document.getElementById('f-calendar-field-2').value != ""){
	    error = 0;
	}

  	if (error == 1){
	    alert("Seleccione algún criterio");
	    return false;
	}
	else{
	  return true;
	}

}

function limpiarFiltros(){

  	var filtros = Array('id_tipo','id_actor','id_cons','id_riesgo','id_depto');

  	for (f=0;f<filtros.length;f++){
  	    if (document.getElementById(filtros[f])){
		    ob = document.getElementById(filtros[f]);
		    for (var i = 0; i < ob.options.length; i++){
			   ob.options[ i ].selected = false;
			}
		}
	}

	document.getElementById('f-calendar-field-1').value = "";
	document.getElementById('f-calendar-field-2').value = "";

	alert('Se limpiaron los filtros.');
}

function mostrarFiltros(accion){
  	var filtros = Array('tipo','actor','cons','riesgo','cobertura');

  	for (f=0;f<filtros.length;f++){
		if (accion == 'mostrar'){
		  document.getElementById('td_'+filtros[f]).style.display = '';
		  document.getElementById('img_'+filtros[f]).src = 'images/flecha_down.gif';
		}
		else{
		  document.getElementById('td_'+filtros[f]).style.display = 'none';
		  document.getElementById('img_'+filtros[f]).src = 'images/flecha.gif';
		}
	}
}

function addUrl(){
	var url = "";
	var filtros = Array("id_cat","id_tipo","id_actor","id_cons","id_riesgo");

	for(f=0;f<filtros.length;f++){
		ele = document.getElementById(filtros[f]);

		if (ele != null && ele.value != ""){
			url += "&" + filtros[f] + "=" + ele.value;
		}
	}

	return url;
}
</script>
<?
if (!isset($_GET["method"])){
	if (!isset($_POST["submit"]) && !isset($_POST["id_eventos"])){
	  ?>

	<form action="index.php?m_e=evento&accion=consultar&class=EventoDAO" method="POST">
	<table align='center' cellspacing="1" cellpadding="3" border="0" width="750">
		<tr><td align='center' class='titulo_lista' colspan=2>CONSULTA DE EVENTOS</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td colspan=2>
			Puede consultar Eventos usando uno o varios de los siguiente filtros., Haga click sobre el título para desplegarlo.<br><br>
			En cada opción puede seleccionar uno o varios criterios usando la tecla Ctrl y el click izquierdo del mouse.
			<br>&nbsp;
		</td></tr>
		<tr><td><img src="images/consulta/clean.gif">&nbsp;&nbsp;<a href="#" onclick="limpiarFiltros();">Limpiar Filtros</a>&nbsp;&nbsp;<img src="images/consulta/expandir.gif">&nbsp;&nbsp;<a href="#" onclick="mostrarFiltros('mostrar');">Mostrar</a> | <a href="#" onclick="mostrarFiltros('ocultar');">Ocultar</a> Todos los Filtros</a></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><b>Especifique el rango de fecha de la consulta de eventos, o deje los campos vacíos para no aplicar este filtro</b></td></tr>
		<tr>
			<td id="td_fecha">
				Fecha desde&nbsp;
				<? $calendar->make_input_field(
				// calendar options go here; see the documentation and/or calendar-setup.js
				array('firstDay'       => 1, // show Monday first
				     'ifFormat'       => '%Y-%m-%d',
				     'timeFormat'     => '12'),
				// field attributes go here
				array('class'       => 'textfield',
				     'name'        => 'f_ini'));

				?>&nbsp;&nbsp;&nbsp;&nbsp;
				Fecha hasta&nbsp;
				<? $calendar->make_input_field(
				 // calendar options go here; see the documentation and/or calendar-setup.js
				 array('firstDay'       => 1, // show Monday first
				       'ifFormat'       => '%Y-%m-%d',
				       'timeFormat'     => '12'),
				 // field attributes go here
				 array('class'       => 'textfield',
				       'name'        => 'f_fin'));
				?>
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><img id="img_tipo" src="images/flecha.gif">&nbsp;&nbsp;<a href="#" onclick="unHide('td_tipo','img_tipo');return false;"><b>Filtrar por Tipo de Evento</b></a></td></tr>
		<?
		$display = 'none';
		$id_cat = 0;
		if (isset($_GET['id_cat'])){
		  	$display = '';
		  	$id_cat = $_GET['id_cat'];
		}
		$id_tipo = 0;
		if (isset($_GET['id_tipo'])){
		  	$id_tipo = $_GET['id_tipo'];
		}
		?>
		<tr>
			<td id="td_tipo" style='display:<?=$display?>'>
				&nbsp;&nbsp;&nbsp;&nbsp;<b>Seleccione la Categoria del Tipo de Evento</b><br><br>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<select id="id_cat" name="id_cat" class="select" onchange="location.href='index.php?accion=consultar&class=EventoDAO&id_cat='+this.value">
					<option value=''>Seleccione alguna...</option>
					<? $cat_tipo_dao->ListarCombo('',$id_cat,''); ?>
				</select>
				<?
				if (isset($_GET["id_cat"])){
					?>
					<br><br>&nbsp;&nbsp;&nbsp;&nbsp;
					<b>Seleccione el Tipo de Evento</b><br><br>
					&nbsp;&nbsp;&nbsp;&nbsp;
					<select id="id_tipo" name="id_tipo[]" multiple size="8" class="select">
						<? $tipo_dao->ListarCombo('',$id_tipo,'ID_CAT_TIPO_EVE = '.$id_cat); ?>
					</select>
				<? } ?>
			</td>
		</tr>
		<tr><td><img id="img_actor" src="images/flecha.gif">&nbsp;&nbsp;<a href="#" onclick="unHide('td_actor','img_actor');return false;"><b>Filtrar por Actor</b></a></td></tr>
		<?
		$display = 'none';
		$id_actor = 0;
		if (isset($_GET['id_actor'])){
		  	$display = '';
		  	$id_actor = $_GET['id_actor'];
		}
		?>
		<tr>
			<td id="td_actor" style='display:<?=$display?>'>&nbsp;&nbsp;&nbsp;&nbsp;
				<select id="id_actor" name="id_actor[]" multiple size="8" class="select">
					<? $actor_dao->ListarCombo('',$id_actor,''); ?>
				</select>
			</td>
		</tr>
		<tr><td><img id="img_cons" src="images/flecha.gif">&nbsp;&nbsp;<a href="#" onclick="unHide('td_cons','img_cons');return false;"><b>Filtrar por Consecuencias Humanitarias</b></a></td></tr>
		<?
		$display = 'none';
		$id_cons = 0;
		if (isset($_GET['id_cons'])){
		  	$display = '';
		  	$id_cons = $_GET['id_cons'];
		}
		?>
		<tr>
			<td id="td_cons" style='display:<?=$display?>'>&nbsp;&nbsp;&nbsp;&nbsp;
				<select id="id_cons" name="id_cons[]" multiple size="10" class="select">
					<? $cons_hum_dao->ListarCombo('',$id_cons,''); ?>
				</select>
			</td>
		</tr>
		<tr><td><img id="img_riesgo" src="images/flecha.gif">&nbsp;&nbsp;<a href="#" onclick="unHide('td_riesgo','img_riesgo');return false;"><b>Filtrar por Riegos Humanitarios</b></a></td></tr>
		<?
		$display = 'none';
		$id_riesgo = 0;
		if (isset($_GET['id_riesgo'])){
		  	$display = '';
		  	$id_riesgo = $_GET['id_riesgo'];
		}
		?>
		<tr>
			<td id="td_riesgo" style='display:<?=$display?>'>&nbsp;&nbsp;&nbsp;&nbsp;
				<select id="id_riesgo" name="id_riesgo[]" multiple size="10" class="select" style="width:400px">
					<? $riesgo_hum_dao->ListarCombo('',$id_riesgo,''); ?>
				</select>
			</td>
		</tr>
		<tr><td><img id="img_cobertura" src="images/flecha.gif">&nbsp;&nbsp;<a href="#" onclick="unHide('td_cobertura','img_cobertura');return false;"><b>Filtrar por Ubicación Geográfica</b></a></td></tr>
		<?
		$display = 'none';
		if (isset($_GET['id_depto']))	$display = '';
		?>
		<tr>
			<td id="td_cobertura" style='display:<?=$display?>'>
				<table cellspacing="0" cellpadding="0" border="0" width="95%" align="right">
					<tr><td>&nbsp;</td></tr>
					<tr><td><b>Seleccione el Departamento</b></td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td>
							<select id="id_depto" name="id_depto[]"  multiple size="8" class="select">
								<?
								//DEPTO
								$depto_dao->ListarCombo('combo',$id_depto,'');
								?>
							</select>
						</td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
					  <td>
					  <a href="#" onclick="enviar_deptos('index.php?accion=consultar&class=EventoDAO' + addUrl());return false;">Listar Muncipios del Depto. para refinar la consulta</a>
					  </td>
					</tr>
					<tr><td>&nbsp;</td></tr>

					<? if (isset($_GET['id_depto'])){ ?>
					<tr><td><b>Seleccione el Municipio</b></td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td>
							<select id="id_muns" name="id_muns[]" multiple size="8" class="select">
								<?
								//MUNICIPIO
								for($d=0;$d<$num_deptos;$d++){
								  $id = "'".$id_depto[$d]."'";
									$depto = $depto_dao->Get($id);
									$muns = $municipio_dao->GetAllArray('ID_DEPTO ='.$id);

									echo "<option value='' disabled>-------- ".$depto->nombre." --------</option>";
									foreach ($muns as $mun){
									  echo "<option value='".$mun->id."'>".$mun->nombre."</option>";
									}

								}
								?>
				  			</select>
						</td>
					</tr>
					<tr>
				    <? } ?>

				</table>
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td align='center'>
			<input type="hidden" name="accion" value="consultar" />
			<input type="submit" name="submit" value="Consultar Eventos" onclick="return validar_criterios()" class="boton" />
		</td></tr>
		</table>
	</form>
	<?
	}

	if (isset($_POST["id_eventos"]) && !isset($_POST["reportar"])){

		if ($_POST['pdf'] == 1){
			//echo "<form action='admin/reporte_pdf.php' method='POST' target='_blank'>";
			echo "<form action='index.php?m_e=evento&accion=consultar&class=EventoDAO' method='POST'>";
			$t = "PDF";
		}
		else{
			echo "<form action='index.php?m_e=evento&accion=consultar&class=EventoDAO' method='POST'>";
			$t = "CSV (Excel)";
		}
	    ?>
		<table align='center' cellspacing="1" cellpadding="3" border="0" width="750">
			<tr><td align='center' class='titulo_lista' colspan=2>REPORTAR EVENTOS EN FORMATO <?=$t?></td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td colspan=2>
				Seleccione el formato del reporte:<br>&nbsp;
			</td></tr>
			<tr><td><input type="radio" name="basico" value="1" checked>&nbsp;<b>Reporte Básico</b>: Muestra los datos básico de la Eventos</td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td align='center'>
				<input type="hidden" name="pdf" value="<?=$_POST['pdf']?>" />
				<input type="hidden" name="id_eventos" value="<?=$_POST['id_eventos']?>" />
				<input type="hidden" name="class" value="EventoDAO" />
				<input type="hidden" name="method" value="ReporteEvento" />
				<input type="submit" name="reportar" value="Siguiente" class="boton" />
			</td></tr>
		</table>
		</form>
	<?
	}
}

//REPORTE DIARIO Y SEMANAL
else if ($_GET["method"] == "ReporteDiario"){
  ?>
	<form action="index.php?m_e=evento&accion=consultar&class=EventoDAO" method="POST">
	<table align='center' cellspacing="1" cellpadding="3" border="0" width="750">
		<tr><td align='center' class='titulo_lista' colspan=2>INFORME DIARIO DE EVENTOS</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td colspan=2>
			Especifíque la fecha del informe
			<br>&nbsp;
		</td></tr>

			<tr>
			  <td colspan='3'>
				Fecha:
				<? $calendar->make_input_field(
               // calendar options go here; see the documentation and/or calendar-setup.js
               array('firstDay'       => 1, // show Monday first
                     'ifFormat'       => '%Y-%m-%d',
                     'timeFormat'     => '12'),
               // field attributes go here
               array('class'       => 'textfield',
                     'name'        => 'f_ini'));

      		?>&nbsp;&nbsp;&nbsp;
				<input type="hidden" name="class" value="EventoDAO" />
				<input type="hidden" name="method" value="ReporteDiario" />
				<input type="hidden" name="accion" value="consultar" />
				<input type="submit" name="informe_diario" value="Siguiente" class="boton" onclick="return validar_forma('f-calendar-field-1,Fecha','')" />
			</td></tr>
    </table>
		</form>
<?
}
else if ($_GET["method"] == "ReporteSemanal"){
	?>
	<form action="index.php?m_e=evento&accion=consultar&class=EventoDAO" method="POST">
	<table align='center' cellspacing="1" cellpadding="3" border="0" width="750">
		<tr><td align='center' class='titulo_lista' colspan=2>INFORME SEMANAL DE EVENTOS</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td colspan=2>
			Especifíque el periodo que desea consutlar
			<br>&nbsp;
		</td></tr>

			<tr>
			  <td colspan='3'>
				<b>Fecha Inicio</b>:
				<? $calendar->make_input_field(
               // calendar options go here; see the documentation and/or calendar-setup.js
               array('firstDay'       => 1, // show Monday first
                     'ifFormat'       => '%Y-%m-%d',
                     'timeFormat'     => '12'),
               // field attributes go here
               array('class'       => 'textfield',
                     'name'        => 'f_ini'));
					?>
  				&nbsp;&nbsp;<b>Fecha Fin</b>:
  				<? $calendar->make_input_field(
                 // calendar options go here; see the documentation and/or calendar-setup.js
                 array('firstDay'       => 1, // show Monday first
                       'ifFormat'       => '%Y-%m-%d',
                       'timeFormat'     => '12'),
                 // field attributes go here
                 array('class'       => 'textfield',
                       'name'        => 'f_fin'));
    		?></td>
			</tr>
			<tr>
			  <td align='center'><br>
				<input type="hidden" name="class" value="EventoDAO" />
				<input type="hidden" name="method" value="ReporteSemanal" />
				<input type="hidden" name="accion" value="consultar" />
				<input type="submit" name="informe_semanal" value="Siguiente" class="boton" onclick="return validar_forma('f-calendar-field-1,Fecha Inicio,f-calendar-field-2,Fecha Fin','')" />
			</td></tr>
    </table>
		</form>
<?
}


?>