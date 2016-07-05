<?
//LIBRERIAS
include_once("consulta/lib/libs_evento_c.php");

//INICIALIZACION DE VARIABLES
$evento_vo = New EventoConflicto();
$evento_dao = New EventoConflictoDAO();
$municipio_vo = New Municipio();
$municipio_dao = New MunicipioDAO();
$depto_vo = New Depto();
$depto_dao = New DeptoDAO();
$actor_vo = New Actor();
$actor_dao = New ActorDAO();
$fuente_vo = New FuenteEventoConflicto();
$fuente_dao = New FuenteEventoConflictoDAO();
$subfuente_vo = New SubFuenteEventoConflicto();
$subfuente_dao = New SubFuenteEventoConflictoDAO();
$cat_vo = New CatEventoConflicto();
$cat_dao = New CatEventoConflictoDAO();
$subcat_vo = New SubCatEventoConflicto();
$subcat_dao = New SubCatEventoConflictoDAO();
$edad_dao = New EdadDAO();
$estado_dao = New EstadoMinaDAO();
$condicion_dao = New CondicionMinaDAO();
$sexo_dao = New SexoDAO();
$etnia_dao = New EtniaDAO();
$ocupacion_dao = New OcupacionDAO();

//ACCION DE LA FORMA
if (isset($_POST["submit"])){
  $evento_dao->Reportar();
  die;
}

?>
<script src="admin/js/ajax.js"></script>
<link type="text/css" rel="stylesheet" href="js/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css" media="screen"></link>
<script type="text/javascript" src="js/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js"></script>
<script type="text/javascript" src="admin/js/tabber.js"></script>
	
<script>
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
  	var error_fecha = 0;
  	var filtros = Array('id_cat','id_depto');

  	obj = document.getElementsByName('reporte');
  	reporte = getRadioCheck(obj);

  	if (reporte == 7){
	  	for (f=0;f<filtros.length;f++){
		  	if (validarComboM(document.getElementById(filtros[f]))){
			    error = 0;
			}
		}
	
		msg = "No ha seleccionado ningún criterio, desea generar el listado de TODOS los eventos en el sistema? (Tardará varios minutos)";
		
		f1_ini = document.getElementById('date1_ini');
		f1_fin = document.getElementById('date1_fin');
		
		if (f1_ini.value == '' && f1_fin.value == ''){
			error_fecha = 1;
		}
		
	  	if (error == 1 && error_fecha == 1){
		    return confirm(msg);
		}
		else{
			return true;
		}
  	}
}

function listarSubtipos(id_combo_cat){

	selected = new Array();
	ob = document.getElementById(id_combo_cat);
	for (var i = 0; i < ob.options.length; i++){
		if (ob.options[ i ].selected)
		selected.push(ob.options[ i ].value);
	}
	var id_cats = selected.join(",");

	if (selected.length == 0){
		alert("Debe seleccionar alguna categoría");
	}
	else{
		getDataV1('comboBoxSubcategoria','admin/ajax_data.php?object=comboBoxSubcategoria&multiple=10&separador=1&id_cat='+id_cats,'comboBoxSubcategoria')
	}
}

function listarSubcondicion(id_combo_cond){

	selected = new Array();
	ob = document.getElementById(id_combo_cond);
	for (var i = 0; i < ob.options.length; i++){
		if (ob.options[ i ].selected)
		selected.push(ob.options[ i ].value);
	}
	var id_conds = selected.join(",");

	if (selected.length == 0){
		alert("Debe seleccionar alguna condición");
	}
	else{
		getDataV1('comboBoxSubcondicion','admin/ajax_data.php?object=comboBoxSubcondicion&multiple=10&separador=1&id_condicion='+id_conds,'comboBoxSubcondicion')
	}
}

function listarRangoEdad(id_combo_edad){

	selected = new Array();
	ob = document.getElementById(id_combo_edad);
	for (var i = 0; i < ob.options.length; i++){
		if (ob.options[ i ].selected)
		selected.push(ob.options[ i ].value);
	}
	var id_edad = selected.join(",");

	if (selected.length == 0){
		alert("Debe seleccionar algún Grupo Etareo");
	}
	else{
		getDataV1('comboBoxRangoEdad','admin/ajax_data.php?object=comboBoxRangoEdad&multiple=10&separador=1&id_edad='+id_edad,'comboBoxRangoEdad')
	}
}

function listarSubetnia(id_combo_etnia){

	selected = new Array();
	ob = document.getElementById(id_combo_etnia);
	for (var i = 0; i < ob.options.length; i++){
		if (ob.options[ i ].selected)
		selected.push(ob.options[ i ].value);
	}
	var id_etnia = selected.join(",");

	if (selected.length == 0){
		alert("Debe seleccionar algún Grupo Poblacional");
	}
	else{
		getDataV1('comboBoxSubetnia','admin/ajax_data.php?object=comboBoxSubetnia&multiple=10&separador=1&id_etnia='+id_etnia,'comboBoxSubetnia')
	}
}

function listarMunicipios(combo_depto){

	selected = new Array();
	ob = document.getElementById(combo_depto);
	for (var i = 0; i < ob.options.length; i++){
		if (ob.options[ i ].selected)
		selected.push(ob.options[ i ].value);
	}
	var id_deptos = selected.join(",");

	if (selected.length == 0){
		alert("Debe seleccionar algún departamento");
	}
	else{
		getDataV1('comboBoxMunicipio','admin/ajax_data.php?object=comboBoxMunicipio&multiple=10&id_deptos='+id_deptos,'comboBoxMunicipio')
	}
}

var numero = 0;
var numero_periodo = 1;
var navegador = navigator.appName;

//Aqui se hace la magia... jejeje
addCombinacion = function (div_name) {

	numero++;
	
	//COMBO ACTOR 1
	actorCombo = document.createElement('select');
	actorCombo.name = 'id_actor1[]';
	actorCombo.id = numero;
	actorCombo.className = 'select';

	/*var choice = document.createElement('option');
	choice.value = '';
	choice.appendChild(document.createTextNode('[ Seleccione ]'));
	deptoCombo.appendChild(choice);*/

	<?
	$vo = $actor_dao->GetAllArray('id_papa=0');
	foreach ($vo as $vo){
		?>
		choice = document.createElement('option');
		choice.value = "<?=$vo->id?>";
		choice.appendChild(document.createTextNode('<?=$vo->nombre?>'));
		actorCombo.appendChild(choice);
		<?
	}
	?>

	//COMBO ACTOR 2
	actorCombo2 = document.createElement('select');
	actorCombo2.name = 'id_actor2[]';
	actorCombo2.id = numero;
	actorCombo2.className = 'select';

	/*var choice = document.createElement('option');
	choice.value = '';
	choice.appendChild(document.createTextNode('[ Seleccione ]'));
	deptoCombo.appendChild(choice);*/

	<?
	$vo = $actor_dao->GetAllArray('id_papa=0');
	foreach ($vo as $vo){
		?>
		choice = document.createElement('option');
		choice.value = "<?=$vo->id?>";
		choice.appendChild(document.createTextNode('<?=$vo->nombre?>'));
		actorCombo2.appendChild(choice);
		<?
	}
	?>

	var theTable = document.getElementById('table_actor');

	tr = document.createElement('tr');
	tr.id = 'actor' + numero;

	a_E = document.createElement('a');
	a_E.name = tr.id;
	a_E.onclick = elimCamp;
	a_E.innerHTML = 'Eliminar';
	
	span = document.createElement('span');
	span.innerHTML = '&nbsp;&nbsp;-&nbsp;&nbsp;';
	
	//APPEND DEL COMBO DE ACTOR 1
	td = document.createElement('td');
	td.appendChild(actorCombo);
	tr.appendChild(td);
	
	td.appendChild(span);
	
	//APPEND DEL COMBO DE ACTOR 2
	td.appendChild(actorCombo2);
	tr.appendChild(td);
	
	span = document.createElement('span');
	span.innerHTML = '&nbsp;&nbsp;';
	td.appendChild(span);
	
	//APPEND DEL LINK ELIMINAR
	td.appendChild(a_E);
	tr.appendChild(td);
	
	
	var navegador = navigator.appName;
	if (navegador == "Microsoft Internet Explorer")
		theTable.tBodies[0].appendChild(tr);
	else
		theTable.appendChild(tr);
	
	
}

//Aqui se hace la magia... jejeje
addPeriodo = function () {

	numero_periodo++;
	
	span_desde = document.createElement('span');
	span_desde.innerHTML = 'Fecha desde&nbsp;';

	//FECHA INI
	f_ini = document.createElement('input');
	f_ini.type = 'text';
	f_ini.name = 'f_ini[]';
	f_ini.className = 'textfield';
	f_ini.size = 12;
	f_ini.id = 'f_ini' + numero_periodo + '_input';
	
	//IMAGEN CALENDARIO
	a_calendar = document.createElement('a');
	a_calendar.href= "#";
	a_calendar.onclick = displayCalendarX;
	a_calendar.innerHTML = "&nbsp;<img id='f_ini" + numero_periodo + "' src='images/calendar.png' border=0>";
	
	//FECHA HASTA
	f_hasta = document.createElement('input');
	f_hasta.type = 'text';
	f_hasta.name = 'f_fin[]';
	f_hasta.className = 'textfield';
	f_hasta.size = 12;
	f_hasta.id = 'f_hasta' + numero_periodo + '_input';
	
	//IMAGEN CALENDARIO
	a_calendar2 = document.createElement('a');
	a_calendar2.href= "#";
	a_calendar2.onclick = displayCalendarX;
	a_calendar2.innerHTML = "&nbsp;<img id='f_hasta" + numero_periodo + "' src='images/calendar.png' border=0>";

	
	var theTable = document.getElementById('table_periodo');

	tr = document.createElement('tr');
	tr.id = 'periodo' + numero_periodo;

	a_E = document.createElement('a');
	a_E.name = tr.id;
	a_E.href = '#';
	a_E.onclick = elimCamp;
	a_E.innerHTML = 'Eliminar';
	
	
	td = document.createElement('td');
	td.appendChild(f_ini);
	td.appendChild(a_calendar);
	tr.appendChild(td);
	
	td = document.createElement('td');
	td.appendChild(f_hasta);
	td.appendChild(a_calendar2);
	tr.appendChild(td);	
	
	//APPEND DEL LINK ELIMINAR
	td = document.createElement('td');
	td.appendChild(a_E);
	tr.appendChild(td);
	
	
	var navegador = navigator.appName;
	if (navegador == "Microsoft Internet Explorer")
		theTable.tBodies[0].appendChild(tr);
	else
		theTable.appendChild(tr);
	
}


//con esta función eliminamos el campo cuyo link de eliminación sea presionado
elimCamp = function (evt){
	evt = evento(evt);
	nCampo = rObj(evt);
	div = document.getElementById(nCampo.name);
	div.parentNode.removeChild(div);
	
	numero--;
	
	return false;
	
}

evento = function (evt) { //esta funcion nos devuelve el tipo de evento disparado
	return (!evt) ? event : evt;
}

//con esta función recuperamos una instancia del objeto que disparo el evento
rObj = function (evt) {
	return evt.srcElement ?  evt.srcElement : evt.target;
}

function displayCalendarX(evt){
	evt = evento(evt);
	nCampo = rObj(evt);

	theDate = document.getElementById(nCampo.id + '_input');
	
	displayCalendar(theDate,'yyyy-mm-dd',nCampo);
	
	return false;
	
}

function showFiltros(reporte){
	
	showLinkAddPeriodo('ocultar');
	showFiltroActor('ocultar');
	showFiltroVictima('ocultar');
    
    $('#id_actor2,#actor_texto,#actor_span').hide();
	
	if (reporte == 1){
	}
	else if (reporte == 2){
        $('#id_actor2,#actor_texto,#actor_span').show();
	}
	else if (reporte == 3){
		showLinkAddPeriodo('mostrar');
	}
	else if (reporte == 4){
		showLinkAddPeriodo('mostrar');
	}
	else if (reporte == 5){
		showLinkAddPeriodo('mostrar');
        $('#id_actor2,#actor_texto,#actor_span').show();
	}
	else if (reporte == 6 || reporte == 7){
		showFiltroVictima('mostrar');
	}
	
}

function showLinkAddPeriodo(accion){
	
	id = 'link_add_periodo';
	
	if (accion == 'mostrar'){
		document.getElementById(id).style.display = '';
	}
	else{
		document.getElementById(id).style.display = 'none';
	}
}

function showFiltroActor(accion){
	
}

function showFiltroVictima(accion){
	id = 'filtro_victimas';
	
	if (accion == 'mostrar'){
		document.getElementById(id).style.display = '';
	}
	else{
		document.getElementById(id).style.display = 'none';
	}
}

$(function(){
    $('#id_actor2,#actor_texto,#actor_span').hide();
});

</script>
<?


if (!isset($_POST["submit"])){
  ?>

<form action="index.php?m_e=evento&accion=consultar&class=EventoConflictoDAO" method="POST">
<table align='center' cellspacing="1" cellpadding="5" border="0" width="900">
	<tr class='pathway'>
		<td>
			&nbsp;<img src='images/user-home.png'>&nbsp;<a href='index.php?m_g=consulta&m_e=home'>Home</a> &gt; <a href="index.php?m_g=consulta">Reportes</a> &gt; Eventos del Conflicto
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>
		Puede consultar Eventos usando uno o varios de los siguiente filtros.
		En cada opción puede seleccionar uno o varios criterios usando la tecla Ctrl y el click izquierdo del mouse.
		<br>&nbsp;
	</td></tr>
<!--		<tr><td><img src="images/consulta/clean.gif">&nbsp;&nbsp;<a href="#" onclick="limpiarFiltros();">Limpiar Filtros</a></td></tr>-->
	<tr><td><img src="images/ir_reporte.gif">&nbsp;<b>SELECCIONE EL REPORTE QUE DESEA GENERAR</b>&nbsp;</td></tr> 
	<tr><td>[ Detalle hace referencia a la información que va a ir en las filas del reporte, ej, Detalle por Zona Geogr&aacute;fica mostrará un listado de los Departamentos o Municipios y en las columnas la información que se seleccione: Categorias, Actores, etc ]</td></tr> 
	<tr>
		<td>
			<table cellpadding="5" cellspacing="0">
				<tr>
					<td>
						<table cellpadding="5" cellspacing="0">
							<tr>
								<td width="200" class="titulo_lista">Detalle por Zona Geogr&aacute;fica</td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<td class="table_login" colspan="2">
									<input type="radio" name="reporte" value=1 checked onclick="showFiltros(1);">&nbsp;1. Conteo de eventos por Categor&iacute;a/Subcategor&iacute;a<br>
									<input type="radio" name="reporte" value=2 onclick="showFiltros('2');">&nbsp;2. Conteo de eventos por confrontaciones entre dos actores<br>
									<input type="radio" name="reporte" value=3 onclick="showFiltros('3');">&nbsp;3. Conteo de eventos por periodo de tiempo&nbsp;<img src="images/date_tb.png"><br>
									<input type="radio" name="reporte" value=6 onclick="showFiltros('6');">&nbsp;4. Cantidad de víctimas por&nbsp;
									<select name="cat_victima_localizacion" class="select">
										<option value="no" selected>Categor&iacute;as</option>
										<option value="sexo">Sexo</option>
										<option value="condicion">Condici&oacute;n/Subcondici&oacute;n</option>
										<option value="edad">Grupo Etareo/Rango de edad</option>
										<option value="ocupacion">Ocupaci&oacute;n</option>
										<option value="etnia">Grupo Poblacional/Etnia</option>
										<option value="estado">Estado</option>
									<br>
								</td>
							</tr>
							<tr>
								<td class="important_evento" colspan="2">
									<table>
										<tr>
											<td><img src="images/consulta/evento/important.png"></td>
											<td>
												<b>Reporte a nivel</b>:&nbsp;<select name="nivel_localizacion" class="select">
													<option value="deptal">Departamental</option>
													<option value="mpal">Municipal</option>
												</select>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
					<td valign="top">
						<table cellpadding="5" cellspacing="0">
							<tr>
								<td width="200" class="titulo_lista">Detalle por Periodo</td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<td class="table_login" colspan="2">
									<input type="radio" name="reporte" value=4 onclick="showFiltros('4');">&nbsp;5. Conteo de eventos por categor&iacute;a&nbsp;<img src="images/date_tb.png"><br>
									<input type="radio" name="reporte" value=5 onclick="showFiltros('5');">&nbsp;6. Conteo de eventos por confrontaciones entre dos actores&nbsp;<img src="images/date_tb.png"><br>
								</td>
							</tr>
						</table>
						<br>
						<table cellpadding="5" cellspacing="0">
							<tr>
								<td width="200" class="titulo_lista">Detalle por Evento</td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<td class="table_login" colspan="2">
									<input type="radio" name="reporte" value=7 onclick="showFiltros('7');">&nbsp;7. Reporte general con toda la información asociada a un evento (Este reporte debe ser usado junto a un filtro dada la cantidad de registros)<br>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>			
	<tr><td>&nbsp;</td></tr>
	<tr><td class="td_dotted_CCCCCC"><img src='images/ir.png'>&nbsp;Ir a filtro de: <a href='#cat'>Categor&iacute;a</a> | <a href='#actor'>Actores</a> | <a href='#ubi'>Ubicación Geográfica</a></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><img src="images/fecha.jpg">&nbsp;<b>Especifique el rango de fecha de la consulta de eventos, o deje los campos vacíos para no aplicar este filtro, si el reporte <br> es el 3,5,6 (marcados con <img src="images/date_tb.png">) puede agregar los periodos que desee. Para seleccionar la fecha click sobre el icono de calendario.</b></td></tr>
	<tr>
		<td id="td_fecha"><a name='fecha'></a>
			<table border="0" id='table_periodo' cellspacing="2" cellpadding="3">
				<tr class="titulo_lista">
					<td align="center">FECHA DESDE</td>
					<td align="center">FECHA HASTA</td>
				</tr>
				<tr>
					<td>
						<input type="text" id="date1_ini" name="f_ini[]" class="textfield" size="12">
						<a href="#" onclick="displayCalendar(document.getElementById('date1_ini'),'yyyy-mm-dd',this);return false;"><img src="images/calendar.png" border="0"></a>
		
					</td>
					<td>
						<input type="text" id="date1_fin" name="f_fin[]" class="textfield" size="12">
						<a href="#" onclick="displayCalendar(document.getElementById('date1_fin'),'yyyy-mm-dd',this);return false;"><img src="images/calendar.png" border="0"></a>
					</td>
					<td>
						<span id="link_add_periodo" style="display:none">&nbsp;&nbsp;<img src="images/add.png">&nbsp;<a href="#" onClick="addPeriodo();return false;">Adicionar Periodo</a></span>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td class="td_dotted_CCCCCC"><a name='cat'></a>
			<table>
				<tr>
					<td class="titulo_filtro" width="220">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Categor&iacute;a</td>
					<td rowspan="5"><img src="images/spacer.gif" width="15"></td>
					<td class="titulo_filtro" width="270">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Subcategor&iacute;a</td>
				</tr>
				<tr>
					<td>
						<select id='id_cat' name='id_cat[]' multiple size="10" class="select">
							<? $cat_dao->ListarCombo('combo','',''); ?>
						</select><br><br>
						<img src="images/consulta/mostrar_combo.png">&nbsp;<a href="#" onclick="listarSubtipos('id_cat');return false;">Listar Subcategor&iacute;a</a>
					</td>
					<td id="comboBoxSubcategoria" valign="top">
						Seleccione algún tipo y use la opción Listar<br><br>
						Puede seleccionar varios con la tecla Ctrl y botón izquierdo del mouse
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td class="td_dotted_CCCCCC"><a name='actor'></a>
			<table border="0" id='table_actor'>
				<tr>
					<td class="titulo_filtro">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Actores</td>
				</tr>
				<tr>
					<td id="actor_texto">Defina las combinaciones de actores, puede agregar cuantas desee.</td>
				</tr>
				<tr>
					<td>
						<select id='id_actor1' name='id_actor1[]' class="select" multiple size="14">
                            <option value=""></option>
							<? $actor_dao->ListarCombo('combo','','id_papa=0'); ?>
						</select>&nbsp;&nbsp;-&nbsp;&nbsp;<select id='id_actor2' name='id_actor2[]' class="select">
                            <option value=""></option>
							<? $actor_dao->ListarCombo('combo','','id_papa=0'); ?>
                        </select>&nbsp;&nbsp;
                        <span id="actor_span">
                            <img src="images/add.png">&nbsp;<a href="#" onClick="addCombinacion('actores');return false;">Adicionar Combinaci&oacute;n</a>
                        </span>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr id="filtro_victimas" style="display:none">
		<td class="td_dotted_CCCCCC">
			<table border="0">
				<tr>
					<td class="titulo_filtro">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Categorizaci&oacute;n de V&iacute;ctimas</td>
				</tr>
				<tr>
					<td>Selecciones los filtros que desee aplicar al reporte de cantidad de V&iacute;ctimas.</td>
				</tr>
				<tr>
					<td>
						<div class="tabber">
							<div class="tabbertab">
								<h2>&nbsp;Sexo</h2><br>
								<table>
									<tr>
										<td class="titulo_filtro" width="150">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Sexo</td>
									</tr>
									<tr>
										<td valign="top">
											<select id='id_sexo' name='id_sexo[]' multiple size="10" class="select">
												<? $sexo_dao->ListarCombo('combo','',''); ?>
											</select>
										</td>
									</tr>
								</table>
							</div>
							<div class="tabbertab">
								<h2>&nbsp;Condici&oacute;n</h2><br>
								<table>
									<tr>
										<td class="titulo_filtro" width="270">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Condici&oacute;n</td>
										<td rowspan="5"><img src="images/spacer.gif" width="15"></td>
										<td class="titulo_filtro" width="340">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Subcondici&oacute;n</td>
									</tr>
									<tr>
										<td>
											<select id='id_condicion' name='id_condicion[]' multiple size="10" class="select">
												<? $condicion_dao->ListarCombo('combo','',''); ?>
											</select><br><br>
											<img src="images/consulta/mostrar_combo.png">&nbsp;<a href="#" onclick="listarSubcondicion('id_condicion');return false;">Listar Subcondici&oacute;n</a>
										</td>
										<td id="comboBoxSubcondicion" valign="top">
											Seleccione alguna condici&oacute;n y use la opción Listar<br><br>
											Puede seleccionar varios con la tecla Ctrl y botón izquierdo del mouse
										</td>
									</tr>
								</table>
							</div>
							<div class="tabbertab">
								<h2>&nbsp;Grupo Etareo</h2><br>
								<table>
									<tr>
										<td class="titulo_filtro" width="270">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Grupo Etareo</td>
										<td rowspan="5"><img src="images/spacer.gif" width="15"></td>
										<td class="titulo_filtro" width="340">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Rango de edad</td>
									</tr>
									<tr>
										<td>
											<select id='id_edad' name='id_edad[]' multiple size="10" class="select">
												<? $edad_dao->ListarCombo('combo','',''); ?>
											</select><br><br>
											<img src="images/consulta/mostrar_combo.png">&nbsp;<a href="#" onclick="listarRangoEdad('id_edad');return false;">Listar Rango de edad</a>
										</td>
										<td id="comboBoxRangoEdad" valign="top">
											Seleccione alg&uacute; Grupo Etareo y use la opción Listar<br><br>
											Puede seleccionar varios con la tecla Ctrl y botón izquierdo del mouse
										</td>
									</tr>
								</table>
							</div>
							<div class="tabbertab">
								<h2>&nbsp;Grupo Poblacional</h2><br>
								<table>
									<tr>
										<td class="titulo_filtro" width="270">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Grupo Poblacional</td>
										<td rowspan="5"><img src="images/spacer.gif" width="15"></td>
										<td class="titulo_filtro" width="340">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Etnia</td>
									</tr>
									<tr>
										<td>
											<select id='id_etnia' name='id_etnia[]' multiple size="10" class="select">
												<? $etnia_dao->ListarCombo('combo','',''); ?>
											</select><br><br>
											<img src="images/consulta/mostrar_combo.png">&nbsp;<a href="#" onclick="listarSubetnia('id_etnia');return false;">Listar Etnia</a>
										</td>
										<td id="comboBoxSubetnia" valign="top">
											Seleccione alg&uacute; Grupo Poblacional y use la opción Listar<br><br>
											Puede seleccionar varios con la tecla Ctrl y botón izquierdo del mouse
										</td>
									</tr>
								</table>
							</div>
							<div class="tabbertab">
								<h2>&nbsp;Estado</h2><br>
								<table>
									<tr>
										<td class="titulo_filtro" width="150">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Estado</td>
									</tr>
									<tr>
										<td valign="top">
											<select id='id_estado' name='id_estado[]' multiple size="10" class="select">
												<? $estado_dao->ListarCombo('combo','',''); ?>
											</select>
										</td>
									</tr>
								</table>
							</div>
							<div class="tabbertab">
								<h2>&nbsp;Ocupaci&oacute;n</h2><br>
								<table>
									<tr>
										<td class="titulo_filtro" width="150">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Ocupaci&oacute;n</td>
									</tr>
									<tr>
										<td valign="top">
											<select id='id_ocupacion' name='id_ocupacion[]' multiple size="10" class="select">
												<? $ocupacion_dao->ListarCombo('combo','',''); ?>
											</select>
										</td>
									</tr>
								</table>
							</div>
						</div>
					
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td valign="top" class="td_dotted_CCCCCC"><a name='ubi'></a>
			<table border="0">
				<tr><td colspan="2" class="titulo_filtro" width="250">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Ubicaci&oacute;n Geogr&aacute;fica</td></tr>
				<tr>
					<td width="200">
						<table>
							<tr>
								<td><b>Departamento</b><br>
									<select id="id_depto" name="id_depto[]"  multiple size="10" class="select">
										<?
										//DEPTO
										$depto_dao->ListarCombo('combo','','');
										?>
									</select><br><br>
									<img src="images/consulta/mostrar_combo.png">&nbsp;<a href="#" onclick="listarMunicipios('id_depto');return false;">Listar Muncipios</a>
								</td>
							</tr>
						</table>
					</td>
					<td width="200" valign="top">
						<table>
							<tr>
								<td id="comboBoxMunicipio"></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>						
		</td>		
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td align='center'>
		<input type="hidden" name="accion" value="consultar" />
		<input type="submit" name="submit" value="Consultar Eventos" onclick="return validar_criterios()" class="boton" />
	</td></tr>
	</table>
</form>
<?
}

?>
