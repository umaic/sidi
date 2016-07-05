<?php
ini_set ( "memory_limit", "64M");

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
$redad_dao = New RangoEdadDAO();
$estado_dao = New EstadoMinaDAO();
$condicion_dao = New CondicionMinaDAO();
$scondicion_dao = New SubCondicionDAO();
$sexo_dao = New SexoDAO();
$etnia_dao = New EtniaDAO();
$setnia_dao = New SubEtniaDAO();
$ocupacion_dao = New OcupacionDAO();


$id = $evento_dao->getMaxID() + 1;

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

$fecha = getdate();
//$hoy = $fecha["year"]."-".$fecha["mon"]."-".$fecha["mday"];
//$evento_vo->fecha_evento = "2005-11-07";
$evento_vo->fecha_registro = $fecha["year"]."-".$fecha["mon"]."-".$fecha["mday"];

$calendar = new DHTML_Calendar('js/calendar/','es', 'calendar-win2k-1', false);
$calendar->load_files();

$id_depto = "";
$id_depto_s = "";
$id_cat = 0;
$chk_conf = "";
if (isset($_GET['id_depto'])){
	$id_depto_s = $_GET['id_depto'];
	$id_depto = explode(",",$_GET['id_depto']);
	$num_deptos = count($id_depto);
}

if (isset($_GET['id_mun'])){
	$id_mun_s = $_GET['id_mun'];
	$id_mun = explode(",",$_GET['id_mun']);
	$evento_vo->id_muns = $id_mun;
}

$id_tipo = "";
$id_tipo_s = "";
if (isset($_GET['id_tipo'])){
	$id_tipo_s = $_GET['id_tipo'];
	$id_tipo = explode(",",$_GET['id_tipo']);
	$num_tipos = count($id_tipo);
}

if (isset($_GET['id_cat'])){
	$id_cat = $_GET['id_cat'];
}


//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
	$evento_vo = $evento_dao->Get($id);
	
	//Descripciones
	$desc_evento = $evento_dao->getDescripcionEvento($id);
	
	//Fuentes
	$fuentes = $evento_dao->getFuenteEvento($id);
	$num_fuentes = $fuentes['num'];
	
	//Localizaciones
	$locas = $evento_dao->getLocalizacionEvento($id);
	$num_locas = $locas['num'];

	
}


?>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/tabber.js"></script>
<script type="text/javascript" src="../js/wz_tooltip.js"></script>

<script type="text/javascript">
var numero_vict = new Array(); 
var top_v = 330;
var navegador = navigator.appName;

<?
//Si es actualizar inicializa la variable en el número de desc
$numero = ($accion == 'insertar') ? 0 : $desc_evento['num'] - 1;
$numero_vict_total = ($accion == 'insertar') ? 0 : $desc_evento['num_victimas_total'] - 1;
$numero_fuentes = ($accion == 'insertar') ? 0 : $num_fuentes - 1;
$numero_locas = ($accion == 'insertar') ? 0 : $num_locas - 1;

if ($numero < 0)			$numero = 0;
if ($numero_vict_total < 0)	$numero_vict_total = 0;

echo "var numero = $numero;"; 
echo "var numero_vict_total = $numero_vict_total;"; 
echo "var numero_fuente = $numero_fuentes;"; 
echo "var numero_lugar = $numero_locas;"; 

if ($accion == 'insertar'){
	echo "numero_vict[0] = 0;";
}
else{
	$v = 0;
	foreach ($desc_evento['num_victimas'] as $num){
		$n = ($num > 0) ? $num - 1 : 0;
		echo "numero_vict[$v] = $n;";
		$v++;
	}
}

?>

 
evento = function (evt) { //esta funcion nos devuelve el tipo de evento disparado
	return (!evt) ? event : evt;
}

//Aqui se hace la magia... jejeje
addCampo = function (div_name) {

	numero++;
	numero_vict[numero] = 0;
	
	document.getElementById('num_reg').value = numero;

	//COMBO DE CATEGORIA
	catCombo = document.createElement('select');
	catCombo.name = 'id_cat[]';
	catCombo.id = numero;
	catCombo.className = 'select';
	catCombo.onchange = addSubcatCombobox;

	var choice = document.createElement('option');
	choice.value = '';
	choice.appendChild(document.createTextNode('[ Seleccione ]'));
	catCombo.appendChild(choice);

	<?
	$vo = $cat_dao->GetAllArray('');
	foreach ($vo as $vo){
		?>
		choice = document.createElement('option');
		choice.value = <?=$vo->id?>;
		choice.appendChild(document.createTextNode('<?=$vo->nombre?>'));
		catCombo.appendChild(choice);
		<?
	}
	?>
	
	actorCombo = document.createElement('select');
	actorCombo.name = 'id_abuelo[]';
	actorCombo.className = 'select';
	actorCombo.id = 'id_abuelo'+numero;
	//actorCombo.onchange = addSubactorCombobox;	
	actorCombo.setAttribute('multiple','multiple');
	actorCombo.setAttribute('size','7');
	
	<?
	$vo = $actor_dao->GetAllArray('nivel=1');
	foreach ($vo as $vo){
		?>
		choice = document.createElement('option');
		choice.value = <?=$vo->id?>;
		choice.appendChild(document.createTextNode('<?=$vo->nombre?>'));
		actorCombo.appendChild(choice);
		<?
	}
	?>
	
	//SUB ACTOR
	subactorCombo = document.createElement('select');
	subactorCombo.name = 'id_papa[]';
	subactorCombo.className = 'select';
	subactorCombo.id = 'id_papa'+numero;
	
	<?
	$vo = $actor_dao->GetAllArray('id_papa = 20');
	foreach ($vo as $vo){
		?>
		choice = document.createElement('option');
		choice.value = <?=$vo->id?>;
		choice.appendChild(document.createTextNode('<?=$vo->nombre?>'));
		subactorCombo.appendChild(choice);
		<?
	}
	?>
	
	//SUB SUB ACTOR
	subsubactorCombo = document.createElement('select');
	subsubactorCombo.name = 'id_hijo[]';
	subsubactorCombo.className = 'select';
	subsubactorCombo.id = "id_hijo" + numero;
	
	<?
	$vo = $actor_dao->GetAllArray('id_papa = 320');
	foreach ($vo as $vo){
		?>
		choice = document.createElement('option');
		choice.value = <?=$vo->id?>;
		choice.appendChild(document.createTextNode('<?=$vo->nombre?>'));
		subsubactorCombo.appendChild(choice);
		<?
	}
	?>	

	var theDiv = document.getElementById('div_desc');

	var theTable = document.createElement('table');
	theTable.id = 'tabla_desc_' + numero;
	theTable.setAttribute('cellpadding', '5'); 
	theTable.setAttribute('align', 'center');
	if (navegador == "Microsoft Internet Explorer")
		theTable.setAttribute('className', 'tabla_input_desc'); 
	else 
		theTable.setAttribute('class', 'tabla_input_desc'); 

	tb = document.createElement('tbody');
	theTable.appendChild(tb);
	
	var num_t = 1;
	
	num_t += numero;
	
	tr = document.createElement('tr');
	td = document.createElement('td');
	
	td.setAttribute('align', 'center');
	td.innerHTML = "DESCRIPCION # " + num_t;
	
	if (navegador == "Microsoft Internet Explorer"){
		td.setAttribute('className', 'titulo_lista'); 
		td.setAttribute('colSpan', '3');
	}
	else {
		td.setAttribute('class', 'titulo_lista');
		td.setAttribute('colspan', '3');
	}
	tr.appendChild(td);
	
	if (navegador == "Microsoft Internet Explorer")
		theTable.tBodies[0].appendChild(tr);
	else
		theTable.appendChild(tr);
		
	tr = document.createElement('tr');

	td = document.createElement('td');
	a_E = document.createElement('a');
	a_E.name = theTable.id;
	a_E.href = 'javascript:return false;';
	a_E.onclick = elimCamp;
	a_E.innerHTML = 'Eliminar';
	
	//APPEND DEL LINK ELIMINAR
	td.appendChild(a_E);
	tr.appendChild(td);

	//APPEND DEL COMBO DE TIPO DE EVENTO
	td = document.createElement('td');
	td.align = 'right';
	td.innerHTML = "<b>Categor&iacute;a<b>";
	td.setAttribute('width','200');
	tr.appendChild(td);
	
	td = document.createElement('td');
	td.appendChild(catCombo);
	td.setAttribute('width','600');
	tr.appendChild(td);
	
	if (navegador == "Microsoft Internet Explorer")
		theTable.tBodies[0].appendChild(tr);
	else
		theTable.appendChild(tr);
	
		
	//SEGUNDA FILA	
	tr = document.createElement('tr');
	
	//Link para adicionar victimas
	td = document.createElement('td');
	a_V = document.createElement('a');
	a_V.href = '#';
	a_V.onclick = showDivVictimas2;
	a_V.innerHTML = "Adicionar<br>V&iacute;ctimas";
	td.appendChild(a_V);
	tr.appendChild(td);

	//APPEND LA TD CON EL ID PARA CARGAR SUBCATS
	td = document.createElement('td');
	td.align = 'right';
	td.innerHTML = "<b>Sub Categor&iacute;a<b>";
	tr.appendChild(td);
	
	td = document.createElement('td');
	td.id = 'comboBoxSubcategoria' + numero;
	tr.appendChild(td);
	
	if (navegador == "Microsoft Internet Explorer")
		theTable.tBodies[0].appendChild(tr);
	else
		theTable.appendChild(tr);
	
	//TERCERA FILA	
	tr = document.createElement('tr');
	td = document.createElement('td');	
	tr.appendChild(td);
	
	//APPEND DEL COMBO DE ACTOR
	td = document.createElement('td');
	td.align = 'right';
	td.innerHTML = "<b>Actor/Presunto Perpretador<b>";
	tr.appendChild(td);
	
	td = document.createElement('td');
	td.appendChild(actorCombo);
	
	//Link para listar sub actores
	a_S_A = document.createElement('a');
	a_S_A.href = '#';
	a_S_A.onclick = enviarActor;
	a_S_A.innerHTML = "&nbsp;<img src='images/evento_c/listar.png' border=0>&nbsp;Listar Sub Actor";

	td.appendChild(a_S_A);
	tr.appendChild(td);
	
	if (navegador == "Microsoft Internet Explorer")
		theTable.tBodies[0].appendChild(tr);
	else
		theTable.appendChild(tr);
	
	//CUARTA FILA	
	tr = document.createElement('tr');
	td = document.createElement('td');	
	tr.appendChild(td);

	//APPEND DEL COMBO DE SUBACTOR
	td = document.createElement('td');
	td.align = 'right';
	td.innerHTML = "<b>Sub Actor/Presunto Perpretador<b>";
	tr.appendChild(td);
	
	a_B = document.createElement('a');
	a_B.href = '#';
	a_B.onclick = subactorOcurrencia;
	a_B.innerHTML = "<br><img src='images/icono_search.png' border='0'>&nbsp;Buscar";

	td = document.createElement('td');
	//td.appendChild(subactorCombo);
	td.id = 'comboBoxSubactor' + numero;
	td.appendChild(subactorCombo);
	td.appendChild(a_B);
	tr.appendChild(td);
	
	if (navegador == "Microsoft Internet Explorer")
		theTable.tBodies[0].appendChild(tr);
	else
		theTable.appendChild(tr);
	
	//5 FILA	
	tr = document.createElement('tr');
	td = document.createElement('td');	
	tr.appendChild(td);

	//APPEND DEL COMBO DE SUBACTOR
	a_B = document.createElement('a');
	a_B.href = '#';
	a_B.onclick = subsubactorOcurrencia;
	a_B.innerHTML = "<br><img src='images/icono_search.png' border='0'>&nbsp;Buscar";
		
	//APPEND DEL COMBO DE SUBSUBACTOR
	td = document.createElement('td');
	td.align = 'right';
	td.innerHTML = "<b>Sub-Sub Actor/Presunto Perpretador<b>";
	tr.appendChild(td);
	
	td = document.createElement('td');
	td.appendChild(subsubactorCombo);
	td.appendChild(a_B);
	td.id = 'comboBoxSubSubactor' + numero;
	tr.appendChild(td);
	
	if (navegador == "Microsoft Internet Explorer")
		theTable.tBodies[0].appendChild(tr);
	else
		theTable.appendChild(tr);
	
	br = document.createElement('br');
	theDiv.appendChild(br);
	theDiv.appendChild(theTable);
	
	//Crea el Div de Victimas
	if (numero > 0){
		div_v = document.createElement('div');
		top_v += 420;
				
		//div_v.setAttribute('style', 'display:none;z-index:10;position:absolute;width:800px;height:150px;top:'+top_v+'px;left:150px');
		div_v.id = 'div_victimas_' + numero; 
		
		theDiv.appendChild(div_v);
		
		document.getElementById(div_v.id).style.display = 'none';
		document.getElementById(div_v.id).style.zIndex = 10;
		document.getElementById(div_v.id).style.position = 'absolute';
		document.getElementById(div_v.id).style.width = '800px';
		document.getElementById(div_v.id).style.height = '210px';
		document.getElementById(div_v.id).style.left = '150px';
		document.getElementById(div_v.id).style.top = top_v + 'px';
		
		iframe_v = document.createElement('iframe');
		//iframe_v.setAttribute('style', 'style="display:none;z-index:5;position:absolute;width:800px;height:150px;top:330px;left:600px');
		iframe_v.id = 'iframe_victimas_' + numero; 
		iframe_v.setAttribute('frameborder', '0'); 
		
		theDiv.appendChild(iframe_v);
		
		document.getElementById(iframe_v.id).style.display = 'none';
		document.getElementById(iframe_v.id).style.zIndex = 5;
		document.getElementById(iframe_v.id).style.position = 'absolute';
		document.getElementById(iframe_v.id).style.width = '800px';
		document.getElementById(iframe_v.id).style.height = '210px';
		document.getElementById(iframe_v.id).style.left = '150px';
		document.getElementById(iframe_v.id).style.top = top_v + 'px';
		
		addCampoVictima2();
	}
}

//Aqui se hace la magia... jejeje
addCampoVictima = function () {

	numero_vict[numero]++;
	numero_vict_total++;
	
	//COMBO DE CONDICION
	condicionCombo = document.createElement('select');
	condicionCombo.name = 'id_condicion[]';
	condicionCombo.className = 'select';
	condicionCombo.id = "condicion_" + numero + '_' + numero_vict[numero];
	condicionCombo.onchange = addSubcondicionCombobox;	

	var choice = document.createElement('option');
	choice.value = 0;
	choice.appendChild(document.createTextNode('[ Seleccione ]'));
	condicionCombo.appendChild(choice);

	<?
	$vo = $condicion_dao->GetAllArray('');
	foreach ($vo as $vo){
		?>
		choice = document.createElement('option');
		choice.value = <?=$vo->id?>;
		choice.appendChild(document.createTextNode('<?=$vo->nombre?>'));
		condicionCombo.appendChild(choice);
		<?
	}
	?>	
	
	//COMBO DE ESTADO
	estadoCombo = document.createElement('select');
	estadoCombo.name = 'id_estado[]';
	estadoCombo.className = 'select';

	var choice = document.createElement('option');
	choice.value = 0;
	choice.appendChild(document.createTextNode('[ Seleccione ]'));
	estadoCombo.appendChild(choice);

	<?
	$vo = $estado_dao->GetAllArray('');
	foreach ($vo as $vo){
		?>
		choice = document.createElement('option');
		choice.value = <?=$vo->id?>;
		choice.appendChild(document.createTextNode('<?=$vo->nombre?>'));
		estadoCombo.appendChild(choice);
		<?
	}
	?>	

	//COMBO DE EDAD
	edadCombo = document.createElement('select');
	edadCombo.name = 'id_edad[]';
	edadCombo.className = 'select';
	edadCombo.id = "edad_" + numero + '_' + numero_vict[numero];
	edadCombo.onchange = addRangoEdadCombobox;


	var choice = document.createElement('option');
	choice.value = 0;
	choice.appendChild(document.createTextNode('[ Seleccione ]'));
	edadCombo.appendChild(choice);

	<?
	$vo = $edad_dao->GetAllArray('');
	foreach ($vo as $vo){
		?>
		choice = document.createElement('option');
		choice.value = <?=$vo->id?>;
		choice.appendChild(document.createTextNode('<?=$vo->nombre?>'));
		edadCombo.appendChild(choice);
		<?
	}
	?>		
	
	//COMBO DE SEXO
	sexoCombo = document.createElement('select');
	sexoCombo.name = 'id_sexo[]';
	sexoCombo.className = 'select';

	var choice = document.createElement('option');
	choice.value = 0;
	choice.appendChild(document.createTextNode('[ Seleccione ]'));
	sexoCombo.appendChild(choice);

	<?
	$vo = $sexo_dao->GetAllArray('');
	foreach ($vo as $vo){
		?>
		choice = document.createElement('option');
		choice.value = <?=$vo->id?>;
		choice.appendChild(document.createTextNode('<?=$vo->nombre?>'));
		sexoCombo.appendChild(choice);
		<?
	}
	?>		

	//COMBO DE ETNIA
	etniaCombo = document.createElement('select');
	etniaCombo.name = 'id_etnia[]';
	etniaCombo.className = 'select';
	etniaCombo.id = "etnia_" + numero + '_' + numero_vict[numero];
	etniaCombo.onchange = addSubetniaCombobox;

	var choice = document.createElement('option');
	choice.value = 0;
	choice.appendChild(document.createTextNode('[ Seleccione ]'));
	etniaCombo.appendChild(choice);

	<?
	$vo = $etnia_dao->GetAllArray('');
	foreach ($vo as $vo){
		?>
		choice = document.createElement('option');
		choice.value = <?=$vo->id?>;
		choice.appendChild(document.createTextNode('<?=$vo->nombre?>'));
		etniaCombo.appendChild(choice);
		<?
	}
	?>		
	
	//COMBO DE OCUPACION
	ocupacionCombo = document.createElement('select');
	ocupacionCombo.name = 'id_ocupacion[]';
	ocupacionCombo.className = 'select';

	var choice = document.createElement('option');
	choice.value = 0;
	choice.appendChild(document.createTextNode('[ Seleccione ]'));
	ocupacionCombo.appendChild(choice);

	<?
	$vo = $ocupacion_dao->GetAllArray('');
	foreach ($vo as $vo){
		?>
		choice = document.createElement('option');
		choice.value = <?=$vo->id?>;
		choice.appendChild(document.createTextNode('<?=$vo->nombre?>'));
		ocupacionCombo.appendChild(choice);
		<?
	}
	?>		
	
	var theDiv = document.getElementById('div_victimas_' + numero);

	theTable = document.createElement('table');
	theTable.id = 'tabla_vict_' + numero + '_' + numero_vict[numero];
	theTable.setAttribute('cellPadding', '5');
	theTable.className = 'tabla_input_victima'; 
	
	tb = document.createElement('tbody');
	theTable.appendChild(tb);
	
	tr = document.createElement('tr');
	a_E = document.createElement('a');
	a_E.name = theTable.id;
	a_E.href = '#';
	a_E.onclick = elimCampVict;
	a_E.innerHTML = 'Eliminar';
	
	victInput = document.createElement('input')
	victInput.type = "text";
	victInput.name = "num_victimas[]";
	victInput.className = "textfield";
	victInput.size = "4";
	victInput.id = "num_victimas" + numero_vict_total;

	//APPEND DEL NUMERO DE VICTIMAS
	td = document.createElement('td');
	td.innerHTML = "<b>Cantidad<b>";
	tr.appendChild(td);
	
	td = document.createElement('td');
	td.appendChild(victInput);
	tr.appendChild(td);
	
	if (navegador == "Microsoft Internet Explorer")
		theTable.tBodies[0].appendChild(tr);
	else
		theTable.appendChild(tr);
	
	tr = document.createElement('tr');

	//APPEND DEL COMBO DE EDAD
	td = document.createElement('td');
	td.innerHTML = "<b>Grupo Etareo<b>";
	tr.appendChild(td);
	
	td = document.createElement('td');
	td.appendChild(edadCombo);
	tr.appendChild(td);
	
	//APPEND LA TD CON EL ID PARA CARGAR RANGO DE EDAD
	td = document.createElement('td');
	td.innerHTML = "<b>Rango de Edad<b>";
	tr.appendChild(td);
	
	td = document.createElement('td');
	td.id = 'comboBoxRangoEdad_edad_' + numero + '_' + numero_vict[numero];
	tr.appendChild(td);
	
	
	if (navegador == "Microsoft Internet Explorer")
		theTable.tBodies[0].appendChild(tr);
	else
		theTable.appendChild(tr);
	
	tr = document.createElement('tr');

	//APPEND DEL COMBO DE CONDICION
	td = document.createElement('td');
	td.innerHTML = "<b>Condici&oacute;n<b>";
	tr.appendChild(td);
		
	td = document.createElement('td');
	td.appendChild(condicionCombo);
	tr.appendChild(td);
	
	//APPEND DEL COMBO DE ESTADO
	td = document.createElement('td');
	td.innerHTML = "<b>Estado<b>";
	tr.appendChild(td);

	td = document.createElement('td');
	td.appendChild(estadoCombo);
	tr.appendChild(td);
	
	if (navegador == "Microsoft Internet Explorer")
		theTable.tBodies[0].appendChild(tr);
	else
		theTable.appendChild(tr);
	
	tr = document.createElement('tr');
	
	//APPEND LA TD CON EL ID PARA CARGAR SUBCONDICIONES
	td = document.createElement('td');
	td.innerHTML = "<b>Sub Condici&oacute;n<b>";
	tr.appendChild(td);

	td = document.createElement('td');
	td.id = 'comboBoxSubcondicion_condicion_' + numero + '_' + numero_vict[numero];
	tr.appendChild(td);		

	//APPEND DEL COMBO DE SEXO
	td = document.createElement('td');
	td.innerHTML = "<b>Sexo<b>";
	tr.appendChild(td);

	td = document.createElement('td');
	td.appendChild(sexoCombo);
	tr.appendChild(td);
	
	if (navegador == "Microsoft Internet Explorer")
		theTable.tBodies[0].appendChild(tr);
	else
		theTable.appendChild(tr);
	
	tr = document.createElement('tr');

	//APPEND DEL COMBO DE ETNIA
	td = document.createElement('td');
	td.innerHTML = "<b>G. Poblacional<b>";
	tr.appendChild(td);
	
	td = document.createElement('td');
	td.appendChild(etniaCombo);
	tr.appendChild(td);
	
	//APPEND LA TD CON EL ID PARA CARGAR SUBETNIA
	td = document.createElement('td');
	td.innerHTML = "<b>Sub Etnia<b>";
	tr.appendChild(td);
	
	td = document.createElement('td');
	td.id = 'comboBoxSubetnia_etnia_' + numero + '_' + numero_vict[numero];
	tr.appendChild(td);
	
	if (navegador == "Microsoft Internet Explorer")
		theTable.tBodies[0].appendChild(tr);
	else
		theTable.appendChild(tr);
	
	tr = document.createElement('tr');

	//APPEND DEL COMBO DE OCUPACION
	td = document.createElement('td');
	td.innerHTML = "<b>Ocupaci&oacute;n<b>";
	tr.appendChild(td);
	
	td = document.createElement('td');
	td.appendChild(ocupacionCombo);
	tr.appendChild(td);	
	
	//APPEND DEL LINK ELIMINAR
	td = document.createElement('td');
	td.appendChild(a_E);
	tr.appendChild(td);


	if (navegador == "Microsoft Internet Explorer")
		theTable.tBodies[0].appendChild(tr);
	else
		theTable.appendChild(tr);

	theDiv.style.height = (numero_vict[numero] + 1)*210 + 'px' ;
	theDiv.appendChild(theTable);
	
	//Cambiar altura al iframe para ie
	iframe = document.getElementById('iframe_victimas_' + numero);
	iframe.style.height = numero_vict[numero]*210 + 'px';
	
	if (numero > 0 && numero_vict[numero] == 0){
		showDivVictimas2();
	}
	
	return false;
}

//Aqui se hace la magia... jejeje
addCampoVictima2 = function () {

	numero_vict_total++;
	
	//COMBO DE CONDICION
	condicionCombo = document.createElement('select');
	condicionCombo.name = 'id_condicion[]';
	condicionCombo.className = 'select';
	condicionCombo.id = "condicion_" + numero + '_' + numero_vict[numero];
	condicionCombo.onchange = addSubcondicionCombobox;	

	var choice = document.createElement('option');
	choice.value = 0;
	choice.appendChild(document.createTextNode('[ Seleccione ]'));
	condicionCombo.appendChild(choice);

	<?
	$vo = $condicion_dao->GetAllArray('');
	foreach ($vo as $vo){
		?>
		choice = document.createElement('option');
		choice.value = <?=$vo->id?>;
		choice.appendChild(document.createTextNode('<?=$vo->nombre?>'));
		condicionCombo.appendChild(choice);
		<?
	}
	?>	
	
	//COMBO DE ESTADO
	estadoCombo = document.createElement('select');
	estadoCombo.name = 'id_estado[]';
	estadoCombo.id = 'estado_' + numero + '_' + numero_vict[numero];
	estadoCombo.className = 'select';

	var choice = document.createElement('option');
	choice.value = 0;
	choice.appendChild(document.createTextNode('[ Seleccione ]'));
	estadoCombo.appendChild(choice);

	<?
	$vo = $estado_dao->GetAllArray('');
	foreach ($vo as $vo){
		?>
		choice = document.createElement('option');
		choice.value = <?=$vo->id?>;
		choice.appendChild(document.createTextNode('<?=$vo->nombre?>'));
		estadoCombo.appendChild(choice);
		<?
	}
	?>	

	//COMBO DE EDAD
	edadCombo = document.createElement('select');
	edadCombo.name = 'id_edad[]';
	edadCombo.className = 'select';
	edadCombo.id = "edad_" + numero + '_' + numero_vict[numero];
	edadCombo.onchange = addRangoEdadCombobox;
	

	var choice = document.createElement('option');
	choice.value = 0;
	choice.appendChild(document.createTextNode('[ Seleccione ]'));
	edadCombo.appendChild(choice);

	<?
	$vo = $edad_dao->GetAllArray('');
	foreach ($vo as $vo){
		?>
		choice = document.createElement('option');
		choice.value = <?=$vo->id?>;
		choice.appendChild(document.createTextNode('<?=$vo->nombre?>'));
		edadCombo.appendChild(choice);
		<?
	}
	?>		
	
	//COMBO DE SEXO
	sexoCombo = document.createElement('select');
	sexoCombo.name = 'id_sexo[]';
	sexoCombo.id = 'sexo_' + numero + '_' + numero_vict[numero];
	sexoCombo.className = 'select';

	var choice = document.createElement('option');
	choice.value = 0;
	choice.appendChild(document.createTextNode('[ Seleccione ]'));
	sexoCombo.appendChild(choice);

	<?
	$vo = $sexo_dao->GetAllArray('');
	foreach ($vo as $vo){
		?>
		choice = document.createElement('option');
		choice.value = <?=$vo->id?>;
		choice.appendChild(document.createTextNode('<?=$vo->nombre?>'));
		sexoCombo.appendChild(choice);
		<?
	}
	?>		

	//COMBO DE ETNIA
	etniaCombo = document.createElement('select');
	etniaCombo.name = 'id_etnia[]';
	etniaCombo.className = 'select';
	etniaCombo.id = "etnia_" + numero + '_' + numero_vict[numero];
	etniaCombo.onchange = addSubetniaCombobox;

	var choice = document.createElement('option');
	choice.value = 0;
	choice.appendChild(document.createTextNode('[ Seleccione ]'));
	etniaCombo.appendChild(choice);

	<?
	$vo = $etnia_dao->GetAllArray('');
	foreach ($vo as $vo){
		?>
		choice = document.createElement('option');
		choice.value = <?=$vo->id?>;
		choice.appendChild(document.createTextNode('<?=$vo->nombre?>'));
		etniaCombo.appendChild(choice);
		<?
	}
	?>		
	
	//COMBO DE OCUPACION
	ocupacionCombo = document.createElement('select');
	ocupacionCombo.name = 'id_ocupacion[]';
	ocupacionCombo.className = 'select';
	ocupacionCombo.id = "ocupacion_" + numero + '_' + numero_vict[numero];

	var choice = document.createElement('option');
	choice.value = 0;
	choice.appendChild(document.createTextNode('[ Seleccione ]'));
	ocupacionCombo.appendChild(choice);

	<?
	$vo = $ocupacion_dao->GetAllArray('');
	foreach ($vo as $vo){
		?>
		choice = document.createElement('option');
		choice.value = <?=$vo->id?>;
		choice.appendChild(document.createTextNode('<?=$vo->nombre?>'));
		ocupacionCombo.appendChild(choice);
		<?
	}
	?>		
	
	var theDiv = document.getElementById('div_victimas_' + numero);

	theTable = document.createElement('table');
	theTable.id = 'tabla_vict_' + numero + '_' + numero_vict[numero];
	theTable.setAttribute('cellPadding', '5');
	theTable.className = 'tabla_input_victima';
	
	tb = document.createElement('tbody');
	theTable.appendChild(tb);
	
	tr = document.createElement('tr');
	td = document.createElement('td');
	td.setAttribute('align', 'center');
	td.innerHTML = "VICTIMAS DEL EVENTO";
	td.className = 'titulo_lista_victima'; 
	td.setAttribute('colSpan', '4');
	
	tr.appendChild(td);
	
	td = document.createElement('td');
	td.setAttribute('align', 'right');
	td.className = 'titulo_lista_victima';
	
	a_X = document.createElement('a');
	a_X.href = '#';
	a_X.onclick = cancelarVictimas;
	a_X.innerHTML = 'Cancelar';
	a_X.id = 'cancelar_v_' + numero_vict_total;
	
	td.appendChild(a_X);

	a_X = document.createElement('span');
	a_X.innerHTML = '&nbsp;|&nbsp;';
	
	td.appendChild(a_X);
	
	a_X = document.createElement('a');
	a_X.href = '#';
	a_X.onclick = showDivVictimas2;
	a_X.innerHTML = 'Guardar';

	td.appendChild(a_X);
	
	
	tr.appendChild(td);
	
	if (navegador == "Microsoft Internet Explorer")
		theTable.tBodies[0].appendChild(tr);
	else
		theTable.appendChild(tr);
		
	tr = document.createElement('tr');
	a_E = document.createElement('a');
	a_E.name = theTable.id;
	a_E.href = '#';
	a_E.onclick = addCampoVictima;
	a_E.innerHTML = 'Adicionar Victimas';
	
	victInput = document.createElement('input')
	victInput.type = "text";
	victInput.name = "num_victimas[]";
	victInput.className = "textfield";
	victInput.size = "4";
	victInput.id = "num_victimas" + numero_vict_total;

	//APPEND DEL NUMERO DE VICTIMAS
	td = document.createElement('td');
	td.innerHTML = "<b>Cantidad<b>";
	tr.appendChild(td);
	
	td = document.createElement('td');
	td.appendChild(victInput);
	tr.appendChild(td);
	
	if (navegador == "Microsoft Internet Explorer")
		theTable.tBodies[0].appendChild(tr);
	else
		theTable.appendChild(tr);

	tr = document.createElement('tr');
	
	//APPEND DEL COMBO DE EDAD
	td = document.createElement('td');
	td.innerHTML = "<b>Grupo Etareo<b>";
	tr.appendChild(td);
	
	td = document.createElement('td');
	td.appendChild(edadCombo);
	tr.appendChild(td);
	
	//APPEND LA TD CON EL ID PARA CARGAR RANGO DE EDAD
	td = document.createElement('td');
	td.innerHTML = "<b>Rango de Edad<b>";
	tr.appendChild(td);
	
	td = document.createElement('td');
	td.id = 'comboBoxRangoEdad_edad_' + numero + '_' + numero_vict[numero];
	tr.appendChild(td);
	
	if (navegador == "Microsoft Internet Explorer")
		theTable.tBodies[0].appendChild(tr);
	else
		theTable.appendChild(tr);
	
	tr = document.createElement('tr');

	//APPEND DEL COMBO DE CONDICION
	td = document.createElement('td');
	td.innerHTML = "<b>Condici&oacute;n<b>";
	tr.appendChild(td);
		
	td = document.createElement('td');
	td.appendChild(condicionCombo);
	tr.appendChild(td);
	
	//APPEND DEL COMBO DE ESTADO
	td = document.createElement('td');
	td.innerHTML = "<b>Estado<b>";
	tr.appendChild(td);

	td = document.createElement('td');
	td.appendChild(estadoCombo);
	tr.appendChild(td);
	
	if (navegador == "Microsoft Internet Explorer")
		theTable.tBodies[0].appendChild(tr);
	else
		theTable.appendChild(tr);
	
	tr = document.createElement('tr');
	
	//APPEND LA TD CON EL ID PARA CARGAR SUBCONDICIONES
	td = document.createElement('td');
	td.innerHTML = "<b>Sub Condici&oacute;n<b>";
	tr.appendChild(td);

	td = document.createElement('td');
	td.id = 'comboBoxSubcondicion_condicion_' + numero + '_' + numero_vict[numero];
	tr.appendChild(td);		

	//APPEND DEL COMBO DE SEXO
	td = document.createElement('td');
	td.innerHTML = "<b>Sexo<b>";
	tr.appendChild(td);

	td = document.createElement('td');
	td.appendChild(sexoCombo);
	tr.appendChild(td);
	
	if (navegador == "Microsoft Internet Explorer")
		theTable.tBodies[0].appendChild(tr);
	else
		theTable.appendChild(tr);
	
	tr = document.createElement('tr');

	//APPEND DEL COMBO DE ETNIA
	td = document.createElement('td');
	td.innerHTML = "<b>G. Poblacional<b>";
	tr.appendChild(td);
	
	td = document.createElement('td');
	td.appendChild(etniaCombo);
	tr.appendChild(td);
	
	//APPEND LA TD CON EL ID PARA CARGAR SUBETNIA
	td = document.createElement('td');
	td.innerHTML = "<b>Sub Etnia<b>";
	tr.appendChild(td);
	
	td = document.createElement('td');
	td.id = 'comboBoxSubetnia_etnia_' + numero + '_' + numero_vict[numero];
	tr.appendChild(td);
	
	if (navegador == "Microsoft Internet Explorer")
		theTable.tBodies[0].appendChild(tr);
	else
		theTable.appendChild(tr);
	
	tr = document.createElement('tr');

	//APPEND DEL COMBO DE OCUPACION
	td = document.createElement('td');
	td.innerHTML = "<b>Ocupaci&oacute;n<b>";
	tr.appendChild(td);
	
	td = document.createElement('td');
	td.appendChild(ocupacionCombo);
	tr.appendChild(td);	
	
	//APPEND DEL LINK ELIMINAR
	td = document.createElement('td');
	td.appendChild(a_E);
	tr.appendChild(td);


	if (navegador == "Microsoft Internet Explorer")
		theTable.tBodies[0].appendChild(tr);
	else
		theTable.appendChild(tr);

	theDiv.style.height = (numero_vict[numero] + 1)*210 + 'px' ;
	theDiv.appendChild(theTable);
	
}

//Aqui se hace la magia... jejeje
addCampoLugar = function (div_name) {

	numero_lugar++;
	
	//COMBO DE DEPTO
	deptoCombo = document.createElement('select');
	deptoCombo.name = 'id_depto[]';
	deptoCombo.id = numero_lugar;
	deptoCombo.className = 'select';
	deptoCombo.onchange = addMunicipioCombobox;

	var choice = document.createElement('option');
	choice.value = '';
	choice.appendChild(document.createTextNode('[ Seleccione ]'));
	deptoCombo.appendChild(choice);

	<?
	$vo = $depto_dao->GetAllArray('');
	foreach ($vo as $vo){
		?>
		choice = document.createElement('option');
		choice.value = "<?=$vo->id?>";
		choice.appendChild(document.createTextNode('<?=$vo->nombre?>'));
		deptoCombo.appendChild(choice);
		<?
	}
	?>

	//LUGAR
	lugarInput = document.createElement('textarea')
	lugarInput.name = "lugar[]";
	lugarInput.className = "textfield";
	lugarInput.size = "30";
	lugarInput.innerHTML = " ";
	lugarInput.cols = 40;
	lugarInput.rows = 2;
	
	
	var theTable = document.getElementById('tabla_depto');

	tr = document.createElement('tr');
	tr.id = 'depto' + numero_lugar;
	tr.className = "tabla_input_desc";

	td = document.createElement('td');
	td.align = "center";
	a_E = document.createElement('a');
	a_E.name = tr.id;
	a_E.href = '#';
	a_E.onclick = elimCamp;
	a_E.innerHTML = 'Eliminar';
	
	//APPEND DEL LINK ELIMINAR
	td.appendChild(a_E);
	tr.appendChild(td);
	
	//APPEND DEL COMBO DE DEPTO
	td = document.createElement('td');
	td.appendChild(deptoCombo);
	tr.appendChild(td);
	
	//APPEND LA TD CON EL ID PARA CARGAR MUN
	td = document.createElement('td');
	td.id = 'comboBoxMunicipio' + numero_lugar;
	tr.appendChild(td);
	
	//LUGAR
	td = document.createElement('td');
	td.appendChild(lugarInput);
	tr.appendChild(td);

	
	var navegador = navigator.appName;
	if (navegador == "Microsoft Internet Explorer")
	theTable.tBodies[0].appendChild(tr);
	else
	theTable.appendChild(tr);
	
	
}

//Aqui se hace la magia... jejeje
addCampoFuente = function (div_name) {

	numero_fuente++;
	
	//COMBO DE FUENTE
	fuenteCombo = document.createElement('select');
	fuenteCombo.name = 'id_fuente[]';
	fuenteCombo.id = numero_fuente;
	fuenteCombo.className = 'select';
	fuenteCombo.onchange = addSubfuenteCombobox;

	var choice = document.createElement('option');
	choice.value = '';
	choice.appendChild(document.createTextNode('[ Seleccione ]'));
	fuenteCombo.appendChild(choice);

	<?
	$vo = $fuente_dao->GetAllArray('');
	foreach ($vo as $vo){
		?>
		choice = document.createElement('option');
		choice.value = <?=$vo->id?>;
		choice.appendChild(document.createTextNode('<?=$vo->nombre?>'));
		fuenteCombo.appendChild(choice);
		<?
	}
	?>
	var theTable = document.getElementById('tabla_fuente');

	tr = document.createElement('tr');
	tr.id = 'fuente' + numero_fuente;
	tr.setAttribute("class","tabla_input_desc");

	td = document.createElement('td');
	td.align = "center";
	a_E = document.createElement('a');
	a_E.name = tr.id;
	a_E.href = '#';
	a_E.onclick = elimCamp;
	a_E.innerHTML = 'Eliminar';
	
	descInput = document.createElement('textarea');
	descInput.className = "textarea_fuente_desc";
	descInput.name = "desc_fuente[]";
	descInput.innerHTML = "&nbsp;";
	
	referInput = document.createElement('textarea');
	referInput.className = "textarea_fuente_refer";
	referInput.name = "refer_fuente[]";
	referInput.innerHTML = "&nbsp;";
	
	//APPEND DEL LINK ELIMINAR
	td.appendChild(a_E);
	tr.appendChild(td);

	//APPEND DEL COMBO DE TIPO DE FUENTE
	td = document.createElement('td');
	td.appendChild(fuenteCombo);
	tr.appendChild(td);

	//APPEND LA TD CON EL ID PARA CARGAR SUB
	td = document.createElement('td');
	td.id = 'comboBoxSubfuente' + numero_fuente;
	tr.appendChild(td);

	//APPEND DE LA DESC. FUENTE
	td = document.createElement('td');
	td.appendChild(descInput);
	tr.appendChild(td);
	
	//APPEND DE LA FECHA FUENTE
	td = document.createElement('td');
	td.innerHTML = '<input type="text" name="fecha_fuente[]" class="textfield" size=13 value="'+document.getElementById('f-calendar-field-2').value+'">';
	tr.appendChild(td);
	
	//APPEND DE LA REFERENCIA
	td = document.createElement('td');
	td.appendChild(referInput);
	tr.appendChild(td);	
	
	var navegador = navigator.appName;
	if (navegador == "Microsoft Internet Explorer")
	theTable.tBodies[0].appendChild(tr);
	else
	theTable.appendChild(tr);
	
	return false;
	
	
}

//con esta función eliminamos el campo cuyo link de eliminación sea presionado
elimCamp = function (evt){
	evt = evento(evt);
	nCampo = rObj(evt);
	div = document.getElementById(nCampo.name);
	div.parentNode.removeChild(div);
	
	numero--;
	
	document.getElementById('num_reg').value = numero;
	
	return false;
	
}

//con esta función eliminamos el campo cuyo link de eliminación sea presionado
elimCamp2 = function (id){
	div = document.getElementById(id);
	div.parentNode.removeChild(div);
	
	numero--;
	
	document.getElementById('num_reg').value = numero;
	
	return false;
	
}

//con esta función eliminamos el campo cuyo link de eliminación sea presionado
elimCampVict = function (evt){
	evt = evento(evt);
	nCampo = rObj(evt);
	div = document.getElementById(nCampo.name);
	div.parentNode.removeChild(div);
	
	numero_vict[numero]--;
	
	//Cambiar altura al iframe para ie
	iframe = document.getElementById('iframe_victimas_' + numero);
	//iframe.style.height = numero_vict[numero]*195 + 'px';

	
	return false;
	
}

//con esta función eliminamos el campo cuyo link de eliminación sea presionado
elimCampVict2 = function (id){
	div = document.getElementById(id);
	div.parentNode.removeChild(div);
	
	numero_vict[numero]--;
	
	//Cambiar altura al iframe para ie
	iframe = document.getElementById('iframe_victimas_' + numero);
	iframe.style.height = numero_vict[numero]*195 + 'px';

	
	return false;
	
}

//Construye combo de subcategoria dinámicamente para la fila adicionada
addMunicipioCombobox = function (evt){
	evt = evento(evt);
	nCampo = rObj(evt);
	id_td_combo = "comboBoxMunicipio" + nCampo.id;
	
	id_depto = document.getElementById(nCampo.id).options[document.getElementById(nCampo.id).selectedIndex].value;
	
	getDataV1('comboBoxMunicipio','ajax_data.php?object=comboBoxMunicipioEvento&multiple=0&titulo=0&separador_depto=0&id_deptos='+id_depto,id_td_combo)

}

//Construye combo de subcategoria dinámicamente para la fila adicionada
addSubcatCombobox = function (evt){
	evt = evento(evt);
	nCampo = rObj(evt);

	id_td_combo = "comboBoxSubcategoria" + nCampo.id;
	
	id_cat = document.getElementById(nCampo.id).options[document.getElementById(nCampo.id).selectedIndex].value;
	
	getDataV1('comboBoxSubcategoria','ajax_data.php?object=comboBoxSubcategoria&id_cat='+id_cat,id_td_combo);

}

//Construye combo de subcondicion dinámicamente para la fila adicionada
addSubcondicionCombobox = function (evt){
	evt = evento(evt);
	nCampo = rObj(evt);
	id_td_combo = "comboBoxSubcondicion_" + nCampo.id;
	
	var combo_papa = document.getElementById(nCampo.id);
	id_condicion = combo_papa.options[combo_papa.selectedIndex].value;
	getDataV1('comboBoxSubcondicion','ajax_data.php?object=comboBoxSubcondicion&id_condicion='+id_condicion+'&id_field=id_subcondicion'+numero+ '_' + numero_vict[numero],id_td_combo);

}

//Construye combo de rango de edad dinámicamente para la fila adicionada
addRangoEdadCombobox = function (evt){
	evt = evento(evt);
	nCampo = rObj(evt);
	id_td_combo = "comboBoxRangoEdad_" + nCampo.id;

	var combo_papa = document.getElementById(nCampo.id);
	id_edad = combo_papa.options[combo_papa.selectedIndex].value;
	
	getDataV1('comboBoxRangoEdad','ajax_data.php?object=comboBoxRangoEdad&id_edad='+id_edad+'&id_field=id_rango_edad'+numero+ '_' + numero_vict[numero],id_td_combo);

}

//Construye combo de subetnia dinámicamente para la fila adicionada
addSubetniaCombobox = function (evt){
	evt = evento(evt);
	nCampo = rObj(evt);
	id_td_combo = "comboBoxSubetnia_" + nCampo.id;
	
	var combo_papa = document.getElementById(nCampo.id);
	id_etnia = combo_papa.options[combo_papa.selectedIndex].value;
	
	getDataV1('comboBoxSubetnia','ajax_data.php?object=comboBoxSubetnia&id_etnia='+id_etnia+'&id_field=id_subetnia'+numero+ '_' + numero_vict[numero],id_td_combo);

}

//Construye combo de subfuente dinámicamente para la fila adicionada
addSubfuenteCombobox = function (evt){
	evt = evento(evt);
	nCampo = rObj(evt);
	id_td_combo = "comboBoxSubfuente" + nCampo.id;
	
	id_fuente = document.getElementById(nCampo.id).options[document.getElementById(nCampo.id).selectedIndex].value;
	
	getDataV1('comboBoxSubfuente','ajax_data.php?object=comboBoxSubfuente&id_fuente='+id_fuente,id_td_combo)
}

//Construye combo de subactor dinámicamente para la fila adicionada
addSubactorCombobox = function (evt){
	evt = evento(evt);
	nCampo = rObj(evt);
	id_td_combo = "comboBoxSubactor" + numero;
	
	id_papa = document.getElementById(nCampo.id).options[document.getElementById(nCampo.id).selectedIndex].value;
	
	getDataV1('comboBoxActor','ajax_data.php?object=comboBoxActor&onchange=1&name_field=id_papa&numero_fila='+numero+'&id_papa='+id_papa,id_td_combo)
}

//Construye combo de subsubactor dinámicamente para la fila adicionada
addSubSubactorCombobox = function (evt){
	evt = evento(evt);
	nCampo = rObj(evt);
	id_td_combo = "comboBoxSubSubactor" + numero;
	
	id_papa = document.getElementById(nCampo.id).options[document.getElementById(nCampo.id).selectedIndex].value;
	
	getDataV1('comboBoxActor','ajax_data.php?object=comboBoxActor&onchange=1&name_field=id_hijo&numero_fila='+numero+'&id_papa='+id_papa,id_td_combo)
}

//Ocurrencias del Subactor
subactorOcurrencia = function(evt){
	evt = evento(evt)
	showDivOcurrencia('papa',evt);
	return false;
}

//Ocurrencias del SubSubactor
subsubactorOcurrencia = function(evt){
	evt = evento(evt)
	showDivOcurrencia('hijo',evt);
	return false;
}

//con esta función recuperamos una instancia del objeto que disparo el evento
rObj = function (evt) {
	return evt.srcElement ?  evt.srcElement : evt.target;
}

function showDivOcurrencia(caso,e){
	
	var IE = document.all ? true:false;
	var offset_x = 150;
	var offset_y = 20;

	if (IE) { // grab the x-y pos.s if browser is IE
		tempX = event.clientX + document.body.scrollLeft
		tempY = event.clientY + document.body.scrollTop
	} else {  // grab the x-y pos.s if browser is NS
		tempX = e.pageX
		tempY = e.pageY
	}
	// catch possible negative values in NS4
	if (tempX < 0){tempX = 0}
	if (tempY < 0){tempY = 0}
	// show the position values in the form named Show
	// in the text fields named MouseX and MouseY

	
	
	if (caso == 'papa'){
		
		document.getElementById('ocurrenciasActorPapa').innerHTML='';
		document.getElementById('ocurrenciasActorPapa').style.display='none';
		
		document.getElementById('buscar_papa').style.display == 'none' ? dis = '' : dis = 'none';
		
		document.getElementById('buscar_papa').style.left = tempX - offset_x + 'px';
		document.getElementById('buscar_papa').style.top = tempY + offset_y + 'px';
		document.getElementById('buscar_papa').style.display=dis;
	}
	
	else if (caso == 'hijo'){
		
		document.getElementById('ocurrenciasActorHijo').innerHTML='';
		document.getElementById('ocurrenciasActorHijo').style.display='none';
		
		document.getElementById('buscar_hijo').style.display == 'none' ? dis = '' : dis = 'none';
		
		document.getElementById('buscar_hijo').style.left = tempX - offset_x + 'px';
		document.getElementById('buscar_hijo').style.top = tempY + offset_y + 'px';
		document.getElementById('buscar_hijo').style.display=dis;
	}
	
}

function showDivVictimas(id){
	div = document.getElementById('div_'+id);
	iframe = document.getElementById('iframe_'+id);
	
	if (div.style.display == 'none'){
		div.style.display = '';
		iframe.style.display = '';
	}
	else{
		div.style.display = 'none';
		iframe.style.display = 'none';
	}
}

function cancelarVictimas(evt){
	
	evt = evento(evt);
	nCampo = rObj(evt);
	num = nCampo.id;

	if (numero_vict[numero] > 0){
		alert("Si desea cancelar todos los sub-bloques de víctimas, primero elimine uno a uno del segundo en adelante y luego vuelva a hacer click en cancelar");
		return false;
	}
	else{
		document.getElementById('num_victimas'+num).value = '';
		
		document.getElementById('id_edad'+num).value = 0;
		if (document.getElementById('id_rango_edad'+num)){
			document.getElementById('id_rango_edad'+num).value = 0;
		}
		document.getElementById('id_condicion'+num).value = 0;
		if (document.getElementById('id_subcondicion'+num)){
			document.getElementById('id_subcondicion'+num).value = 0;
		}
		document.getElementById('id_estado'+num).value = 0;
		document.getElementById('id_sexo'+num).value = 0;
		document.getElementById('id_ocupacion'+num).value = 0;
		document.getElementById('id_etnia'+num).value = 0;
		if (document.getElementById('id_subetnia'+num)){
			document.getElementById('id_subetnia'+num).value = 0;
		}
		
		showDivVictimas('victimas_'+num);
		
		return false;
	}
}

function cancelarVictimas2(num_desc,num_vict){
	
	if (numero_vict[num_desc] > 0){
		alert("Si desea cancelar todos los sub-bloques de víctimas, primero elimine uno a uno del segundo en adelante y luego vuelva a hacer click en cancelar");
		return false;
	}
	else{
		document.getElementById('num_victimas'+num_vict).value = '';
		
		document.getElementById('id_edad'+num_vict).value = 0;
		if (document.getElementById('id_rango_edad'+num_vict)){
			document.getElementById('id_rango_edad'+num_vict).value = 0;
		}
		document.getElementById('id_condicion'+num_vict).value = 0;
		if (document.getElementById('id_subcondicion'+num_vict)){
			document.getElementById('id_subcondicion'+num_vict).value = 0;
		}
		document.getElementById('id_estado'+num_vict).value = 0;
		document.getElementById('id_sexo'+num_vict).value = 0;
		document.getElementById('id_ocupacion'+num_vict).value = 0;
		document.getElementById('id_etnia'+num_vict).value = 0;
		if (document.getElementById('id_subetnia'+num_vict)){
			document.getElementById('id_subetnia'+num_vict).value = 0;
		}
		
		showDivVictimas('victimas_'+num_vict);
		
		return false;
	}
}

function showDivVictimas2(){

	id = 'victimas_' + numero;
	
	div = document.getElementById('div_'+id);
	iframe = document.getElementById('iframe_'+id);
	
	if (div.style.display == 'none'){
		div.style.display = '';
		iframe.style.display = '';
	}
	else{
		div.style.display = 'none';
		iframe.style.display = 'none';
	}
	
	return false;
	
}

function enviarActor(){
	
	selected = new Array();
	ob = document.getElementById('id_abuelo'+numero);
	
	for (var i = 0; i < ob.options.length; i++){
		if (ob.options[ i ].selected)
		selected.push(ob.options[ i ].value);
	}
	
	var id_papa = selected.join(",");
	
	if (selected.length == 0){
		alert("Debe seleccionar algún Actor");
	}
	else{
		getDataV1('comboBoxActor','ajax_data.php?object=comboBoxActor&onchange=1&name_field=id_papa&numero_fila='+numero+'&multiple=7&separador=1&id_papa='+id_papa,'comboBoxSubactor'+numero)
	}
	
	return false;
}
function enviarSubActor(){
	
	selected = new Array();
	ob = document.getElementById('id_papa'+numero);
	
	for (var i = 0; i < ob.options.length; i++){
		if (ob.options[ i ].selected)
		selected.push(ob.options[ i ].value);
	}
	
	var id_papa = selected.join(",");
	
	if (selected.length == 0){
		alert("Debe seleccionar algún Sub Actor");
	}
	else{
		getDataV1('comboBoxActor','ajax_data.php?object=comboBoxActor&onchange=0&name_field=id_hijo&numero_fila='+numero+'&multiple=7&separador=1&id_papa='+id_papa,'comboBoxSubSubactor'+numero);
	}
	
	return false;
}
function submitForm(){
	
	var num_abuelos = new Array();
	var num_papas = new Array();
	var num_hijos = new Array();
	
	//Copia el arreglo con el número de víctimas por descripción a la variable hidden
	document.getElementById('num_vict_desc').value = numero_vict.join();
	
	//Numero de actores, sub y subsub por descripción
	var actor_text = "";
	for(ni=0;ni<=numero;ni++){

		var selected1 = new Array();
		var selected2 = new Array();
		var selected3 = new Array();
		
		//Abuelo
		chk = 0;
		ob = document.getElementById('id_abuelo'+ni);
		for (var i = 0; i < ob.options.length; i++){
			if (ob.options[i].selected){
				selected1.push(ob.options[i].value);
				
				if (chk == 0){
					if (ni > 0){
						actor_text += " - " + ob.options[i].text;
					}
					else{
						actor_text += ob.options[i].text;
					}
				}
				else{
					actor_text += " Vs " + ob.options[i].text;
				}
				
				chk++;
			}
		}
		num_abuelos[ni] = selected1.length;

		//Papas
		if (document.getElementById('id_papa'+ni)){
			ob = document.getElementById('id_papa'+ni);
			for (var i = 0; i < ob.options.length; i++){
				if (ob.options[ i ].selected)
				selected2.push(ob.options[ i ].value);
			}
		}
		num_papas[ni] = selected2.length;

		//Nietos
		if (document.getElementById('id_hijo'+ni)){
			ob = document.getElementById('id_hijo'+ni);
			for (var i = 0; i < ob.options.length; i++){
				if (ob.options[ i ].selected)
				selected3.push(ob.options[ i ].value);
			}
		}
		num_hijos[ni] = selected3.length;
	}
	
	document.getElementById('num_actores_desc').value = num_abuelos.join();
	document.getElementById('num_subactores_desc').value = num_papas.join();
	document.getElementById('num_subsubactores_desc').value = num_hijos.join();
	
	if (validar_forma('f-calendar-field-1,Fecha Evento,id_muns,Localización,id_subcat,Descripción del Evento,id_subfuente,Fuente','')){
		
		//Resumen automatico
		//Subcategorias
		var obs = document.getElementsByName("id_subcat[]");
		var subcat_text;
		for (i=0;i<obs.length;i++){
			ob = obs.item(i);
			
			if (i == 0){
				subcat_text = ob.options[ob.selectedIndex].text;
			}
			else{
				subcat_text += "-" + ob.options[ob.selectedIndex].text;
			}
		}
		
		//Mpios & Deptos
		var obs = document.getElementsByName("id_muns[]");
		var obs_deptos = document.getElementsByName("id_depto[]");
		
		var muns_text;
		var deptos_text;
		var loca_text;
		
		for (i=0;i<obs.length;i++){
			ob = obs.item(i);
			ob_depto = obs_deptos.item(i);
	
			muns_text = ob.options[ob.selectedIndex].text;
			deptos_text = ob_depto.options[ob_depto.selectedIndex].text;
			
			if (i == 0){
				loca_text = muns_text + ", " + deptos_text;
				
			}
			else{
				loca_text += " - " + muns_text + ", " + deptos_text;
			}
		}
		
		var fecha_even = document.getElementById('f-calendar-field-1').value;
		var m_names = new Array("","Enero", "Febrero", "Marzo", 
								"Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", 
								"Octubre", "Noviembre", "Diciembre");
								
		fe = fecha_even.split("-");
		
		fe_final = fe[2] + " de " + m_names[fe[1]*1] + " de " + fe[0];
		
		var sint = subcat_text;
		if (actor_text != '')	sint += ". " + actor_text;
		if (loca_text != '')	sint += ". " + loca_text;
		if (fe_final != '')		sint += ". " + fe_final;
		
		
		//Coloca el valor si esta vacio
		if (document.getElementById('sintesis').value == ''){
			document.getElementById('sintesis').value = sint;
		}
		
		//Si el usuario desea revisar el resumen
		if (document.getElementById('check_resumen_si').checked == true){
			document.getElementById('tab_info_basica').tabber.tabShow(0);		
			document.getElementById('sintesis').focus();
			
			return false;
		}
				
		//Victimas
		error_vict = 1;
		
		for(i=0;i<=numero_vict_total;i++){
			if (document.getElementById('num_victimas'+i).value != ""){
				error_vict = 0;
			}
		}
		
		if (error_vict == 1){
			return confirm("No se ha insertado información sobre víctimas. ¿Desea continuar?");
		}
		else{
			return true;
		}
		
	}
	else	return false;
	
}

function buscarActores(e,id_div_resultado,inner_object,id_input,caso){

	texto = document.getElementById(id_input).value;
	
	keyNum = e.keyCode;
		
	if (keyNum == 8){  //Backspace
		texto = texto.slice(0, -1);  //Borra el ultimo caracter
	}
	else{
		keyChar = String.fromCharCode(keyNum);
		texto +=  keyChar;
	}
	
	if (texto.length > 1){
		document.getElementById(id_div_resultado).style.display='';
		getDataV1('ocurrenciasActor','ajax_data.php?object=ocurrenciasActor&numero_fila='+numero+'&case='+caso+'&s='+texto,inner_object)
	}
	
	//El valor de donde, se coloca en js/ajax.js
}

//-->


</script>	

<body>
<form action="<?=$_SERVER['PHP_SELF']?>" method="POST">

<!--DIV DE BUSQUEDA DE OCURRENCIAS DE SUBACTORES -->
<div id="buscar_papa" style="display:none;z-index:10;position:absolute;background-color:#efefef;width:280px;border:1px solid #000000">
	<br>&nbsp;&nbsp;<input type="text" id='s_papa' name='s' class='textfield' size="25" onkeydown="buscarActores(event,'ocurrenciasActorPapa','ocurrenciasActorPapa','s_papa','papa')">
	<br><br><input type='radio' id="comience" name="donde" value='comience' checked>Que <b>comience</b> con&nbsp;<input type='radio' id="contenga" name="donde" value='contenga'>Que <b>contenga</b> a
	<br><br>
	<div id='ocurrenciasActorPapa' class='ocurrenciasActor' style='display:none'></div>
</div>

<!--DIV DE BUSQUEDA DE OCURRENCIAS DE SUBSUBACTORES -->
<div id="buscar_hijo" style="display:none;z-index:10;position:absolute;background-color:#efefef;width:280px;border:1px solid #000000">
	<br>&nbsp;&nbsp;<input type="text" id='s_hijo' name='s' class='textfield' size="25" onkeydown="buscarActores(event,'ocurrenciasActorHijo','ocurrenciasActorHijo','s_hijo','hijo')">
	<br><br><input type='radio' id="comience" name="donde" value='comience' checked>Que <b>comience</b> con&nbsp;<input type='radio' id="contenga" name="donde" value='contenga'>Que <b>contenga</b> a
	<br><br>
	<div id='ocurrenciasActorHijo' class='ocurrenciasActor' style='display:none'></div>
</div>

<!--DIV ADICIONAR VICTIMAS -->
<?
if ($accion == 'insertar' || ($accion == 'actualizar') && count($desc_evento['id']) == 0){ ?>
	<iframe id="iframe_victimas_0" style="display:none;z-index:5;position:absolute;width:800px;height:195px;top:330px;left:150px" frameborder="0"></iframe>
	<div id="div_victimas_0" style="display:none;z-index:10;position:absolute;width:800px;height:210px;top:330px;left:150px">
		<table class="tabla_input_victima" cellpadding="5" cellspacing="0">
			<tr class="titulo_lista_victima">
				<td colspan="3" align="center">VICTIMAS DEL EVENTO</td>
				<td align="right"><a href='#' onclick='cancelarVictimas2(0,0);return false;'>Cancelar</a>&nbsp;|&nbsp;<a href='#' onclick='showDivVictimas("victimas_0");return false;'>Guardar</a></td></tr>
			<tr>
				<td><b>Cantidad</b>&nbsp;<img src="images/icono_info.png" onmouseover="Tip('<b>Cantidad</b><br>Número de personas afectadas por el evento')" onmouseout="UnTip()"></td>
				<td><input type="text" id='num_victimas0' name='num_victimas[]' class="textfield" size="7"></td>
			</tr>
			<tr>
				<td><b>Grupo Etareo</b></td>
				<td>
					<select id='id_edad0' name='id_edad[]' class="select" onchange="getDataV1('comboBoxRangoEdad','ajax_data.php?object=comboBoxRangoEdad&id_edad='+this.value+'&id_field=id_rango_edad'+numero,'comboBoxRangoEdad')">
						<option value=0>[ Seleccione ]</option>
						<? $edad_dao->ListarCombo('combo','',''); ?>
					</select>
				</td>
				<td><b>Rango de Edad</b></td>
				<td id="comboBoxRangoEdad">
					<select id='id_rango_edad0' name='id_rango_edad[]' class="select">
						<option value=0>[ Seleccione ]</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><b>Condici&oacute;n</b></td>
				<td>
					<select id='id_condicion0' name='id_condicion[]' class="select" onchange="getDataV1('comboBoxSubcondicion','ajax_data.php?object=comboBoxSubcondicion&id_condicion='+this.value+'&id_field=id_subcondicion'+numero,'comboBoxSubcondicion')">
						<option value=0>[ Seleccione ]</option>
						<? $condicion_dao->ListarCombo('combo','',''); ?>
					</select>
				</td>
				<td><b>Estado</b></td>
				<td>
					<select id='id_estado0' name='id_estado[]' class="select">
						<option value=0>[ Seleccione ]</option>
						<? $estado_dao->ListarCombo('combo','',''); ?>
					</select>
				</td>
			</tr>
			<tr>
				<td><b>Sub Condici&oacute;n</b></td>
				<td id="comboBoxSubcondicion">
					<select id='id_subcondicion0' name='id_subcondicion[]' class="select">
						<option value=0>[ Seleccione ]</option>
					</select>
				</td>
				
				<td><b>Sexo</b></td>
				<td>
					<select id='id_sexo0' name='id_sexo[]' class="select">
						<option value=0>[ Seleccione ]</option>
						<? $sexo_dao->ListarCombo('combo','',''); ?>
					</select>
				</td>
			</tr>
			<tr>
				<td><b>G. Poblacional</b></td>
				<td>
					<select id='id_etnia0' name='id_etnia[]' class="select" onchange="getDataV1('comboBoxSubetnia','ajax_data.php?object=comboBoxSubetnia&id_etnia='+this.value+'&id_field=id_subetnia'+numero,'comboBoxSubetnia')">
						<option value=0>[ Seleccione ]</option>
						<? $etnia_dao->ListarCombo('combo','',''); ?>
					</select>
				</td>
				<td><b>Sub Etnia</b></td>
				<td id="comboBoxSubetnia">
					<select id='id_subetnia0' name='id_subetnia[]' class="select">
						<option value=0>[ Seleccione ]</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><b>Ocupaci&oacute;n</b></td>
				<td>
					<select id='id_ocupacion0' name='id_ocupacion[]' class="select">
						<option value=0>[ Seleccione ]</option>
						<? $ocupacion_dao->ListarCombo('combo','',''); ?>
					</select>
				</td>
				<td><a href="#" onClick="addCampoVictima('');return false;">Adicionar V&iacute;ctimas</a>&nbsp;<img src="images/icono_info.png" onmouseover="Tip('<b>Adicionar Registro</b><br>Esta opción permite adicionar otro registro de víctimas')" onmouseout="UnTip()"></td>
			</tr>
		</table>
	</div>
	<?
}
else {
	
	$d = 0;
	$v = 0;
	$top_v = 330;
	foreach($desc_evento['id'] as $id_deseven){	
		
		$dd = $d + 1;
		
		$victimas = $evento_dao->getVictimaDescripcionEvento($id_deseven);
		$num_vict_x_desc = $victimas['num'];

		if ($num_vict_x_desc > 0){
			
			$height = 210 * $num_vict_x_desc;
			$height_i = 220 * $num_vict_x_desc - 5*$num_vict_x_desc;
			
			?>
			<iframe id="iframe_victimas_<?=$d?>" style="display:none;z-index:5;position:absolute;width:800px;height:<?=$height_i?>px;top:<?=$top_v?>px;left:150px" frameborder="0"></iframe>
			<div id="div_victimas_<?=$d?>" style="display:none;z-index:10;position:absolute;width:800px;height:<?=$height?>px;top:<?=$top_v?>px;left:150px">
			<?			
			
			for ($i=0;$i<$num_vict_x_desc;$i++){
				//$top_v += 420;
				?>
					<table class="tabla_input_victima" cellpadding="5" cellspacing="0" id="tabla_vict_<?=$d?>_<?=$i?>;">
						<?
						if ($i == 0) { ?>
							<tr class="titulo_lista_victima">
								<td colspan="3" align="center">VICTIMAS DEL EVENTO</td>
								<td align="right"><a href='#' onclick='cancelarVictimas2(<?=$d?>,<?=$v?>);return false;'>Cancelar</a>&nbsp;|&nbsp;<a href='#' onclick='showDivVictimas("victimas_<?=$d?>");return false;'>Guardar</a></td>
							</tr>
						<? } ?>
						<tr>
							<td><b>Cantidad</b>&nbsp;<img src="images/icono_info.png"onmouseover="Tip('<b>Víctimas</b><br>Numero de personas afectadas por el evento')" onmouseout="UnTip()"></td>
							<td><input type="text" id='num_victimas<?=$v?>' name='num_victimas[]' class="textfield" size="7" value=<?=$victimas['cant'][$i]?>></td>
						</tr>
						<tr>
							<td><b>Grupo Etareo</b></td>
							<td>
								<select id='id_edad<?=$v?>' name='id_edad[]' class="select" onchange="getDataV1('comboBoxRangoEdad','ajax_data.php?object=comboBoxRangoEdad&id_edad='+this.value+'&id_field=id_rango_edad<?=$v?>','comboBoxRangoEdad<?=$v?>')">
									<option value=0>[ Seleccione ]</option>
									<? $edad_dao->ListarCombo('combo',$victimas['edad'][$i],''); ?>
								</select>
							</td>
							<td><b>Rango de Edad</b></td>
							<td id="comboBoxRangoEdad<?=$v?>">
								<select id='id_rango_edad<?=$v?>' name='id_rango_edad[]' class="select">
									<option value=0>[ Seleccione ]</option>
									<? $redad_dao->ListarCombo('combo',$victimas['redad'][$i],'id_edad='.$victimas['edad'][$i]); ?>
								</select>
							</td>
						</tr>
						<tr>
							<td><b>Condici&oacute;n</b></td>
							<td>
								<select id='id_condicion<?=$v?>' name='id_condicion[]' class="select" onchange="getDataV1('comboBoxSubcondicion','ajax_data.php?object=comboBoxSubcondicion&id_condicion='+this.value+'&id_field=id_subcondicion<?=$v?>','comboBoxSubcondicion<?=$v?>')">
									<option value=0>[ Seleccione ]</option>
									<? $condicion_dao->ListarCombo('combo',$victimas['condicion'][$i],''); ?>
								</select>
							</td>
							<td><b>Estado</b></td>
							<td>
								<select id='id_estado<?=$v?>' name='id_estado[]' class="select">
									<option value=0>[ Seleccione ]</option>
									<?
                                    $val = (isset($victimas['estado'][$i])) ? $victimas['estado'][$i] : 0;
                                    $estado_dao->ListarCombo('combo',$val,''); ?>
								</select>
							</td>
						</tr>
						<tr>
							<td><b>Sub Condici&oacute;n</b></td>
							<td id="comboBoxSubcondicion<?=$v?>">
								<?
								$num = $scondicion_dao->numRecords("id_condicion IN (".$victimas['condicion'][$i].")");
								if ($num > 0){
									?>
									<select id='id_subcondicion<?=$v?>' name='id_subcondicion[]' class="select">
										<? $scondicion_dao->ListarCombo('combo',$victimas['scondicion'][$i],'id_condicion='.$victimas['condicion'][$i]); ?>
									</select>
								<?}?>
							</td>
							
							<td><b>Sexo</b></td>
							<td>
								<select id='id_sexo<?=$v?>' name='id_sexo[]' class="select">
									<option value=0>[ Seleccione ]</option>
									<? $sexo_dao->ListarCombo('combo',$victimas['sexo'][$i],''); ?>
								</select>
							</td>
						</tr>
						<tr>
							<td><b>G. Poblacional</b></td>
							<td>
								<select id='id_etnia<?=$v?>' name='id_etnia[]' class="select" onchange="getDataV1('comboBoxSubetnia','ajax_data.php?object=comboBoxSubetnia&id_etnia='+this.value+'&id_field=id_subetnia<?=$v?>','comboBoxSubetnia<?=$v?>')">
									<option value=0>[ Seleccione ]</option>
									<? 
                                    $val = (isset($victimas['etnia'][$i])) ? $victimas['etnia'][$i] : 0;
                                    $etnia_dao->ListarCombo('combo',$val,''); ?>
								</select>
							</td>
							<td><b>Sub Etnia</b></td>
							<td id="comboBoxSubetnia<?=$v?>">
								<select id='id_subetnia<?=$v?>' name='id_subetnia[]' class="select">
									<option value=0>[ Seleccione ]</option>
									<? 
									if (isset($victimas['etnia'][$i])){
										$setnia_dao->ListarCombo('combo',$victimas['setnia'][$i],'id_etnia='.$victimas['etnia'][$i]); 
									}	
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td><b>Ocupaci&oacute;n</b></td>
							<td>
								<select id='id_ocupacion<?=$v?>' name='id_ocupacion[]' class="select">
									<option value=0>[ Seleccione ]</option>
									<? $ocupacion_dao->ListarCombo('combo',$victimas['ocupacion'][$i],''); ?>
								</select>
							</td>
							<td>
								<?
								if ($i == 0){ ?>
									<a href="#" onClick="addCampoVictima('');return false;">Adicionar V&iacute;ctimas</a>&nbsp;<img src="images/icono_info.png" onmouseover="Tip('<b>Adicionar Registro</b><br>Esta opción permite adicionar otro registro de víctimas')" onmouseout="UnTip()"></td>
								<?
								}
								else{ ?>
									<a href="#" onClick="elimCampVict2(this.id);return false;" id="tabla_vict_<?=$d?>_<?=$i?>;">Eliminar</a></td>
								<?
								}
								?>
							</td>
						</tr>
					</table>
				<?
				$v++;
			}
			echo "</div>";
		}
		else {
			?>
			<iframe id="iframe_victimas_<?=$v?>" style="display:none;z-index:5;position:absolute;width:800px;height:195px;top:<?=$top_v?>px;left:150px" frameborder="0"></iframe>
			<div id="div_victimas_<?=$v?>" style="display:none;z-index:10;position:absolute;width:800px;height:210px;top:<?=$top_v?>px;left:150px">
				<table class="tabla_input_victima" cellpadding="5" cellspacing="0">
					<tr class="titulo_lista_victima">
						<td colspan="3" align="center">VICTIMAS DEL EVENTO</td>
						<td align="right"><a href='#' onclick='cancelarVictimas2(<?=$d?>,<?=$v?>);return false;'>Cancelar</a>&nbsp;|&nbsp;<a href='#' onclick='showDivVictimas("victimas_0");return false;'>Guardar</a></td></tr>
					<tr>
						<td><b>Cantidad</b>&nbsp;<img src="images/icono_info.png" class='TipExport' title='<b>Víctimas</b><br>Numero de personas afectadas por el evento'></td>
						<td><input type="text" id='num_victimas<?=$v?>' name='num_victimas[]' class="textfield" size="7" value=<?=$victimas['cant'][$i]?>></td>
					</tr>
					<tr>
						<td><b>Grupo Etareo</b></td>
						<td>
							<select id='id_edad<?=$v?>' name='id_edad[]' class="select" onchange="getDataV1('comboBoxRangoEdad','ajax_data.php?object=comboBoxRangoEdad&id_edad='+this.value+'&id_field=id_rango_edad'+numero,'comboBoxRangoEdad')">
								<option value=0>[ Seleccione ]</option>
								<? $edad_dao->ListarCombo('combo','',''); ?>
							</select>
						</td>
						<td><b>Rango de Edad</b></td>
						<td id="comboBoxRangoEdad">
							<select id='id_rango_edad<?=$v?>' name='id_rango_edad[]' class="select">
								<option value=0>[ Seleccione ]</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><b>Condici&oacute;n</b></td>
						<td>
							<select id='id_condicion<?=$v?>' name='id_condicion[]' class="select" onchange="getDataV1('comboBoxSubcondicion','ajax_data.php?object=comboBoxSubcondicion&id_condicion='+this.value+'&id_field=id_subcondicion'+numero,'comboBoxSubcondicion')">
								<option value=0>[ Seleccione ]</option>
								<? $condicion_dao->ListarCombo('combo','',''); ?>
							</select>
						</td>
						<td><b>Estado</b></td>
						<td>
							<select id='id_estado<?=$v?>' name='id_estado[]' class="select">
								<option value=0>[ Seleccione ]</option>
								<? $estado_dao->ListarCombo('combo','',''); ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><b>Sub Condici&oacute;n</b></td>
						<td id="comboBoxSubcondicion">
							<select id='id_subcondicion<?=$v?>' name='id_subcondicion[]' class="select">
								<option value=0>[ Seleccione ]</option>
							</select>
						</td>
						
						<td><b>Sexo</b></td>
						<td>
							<select id='id_sexo<?=$v?>' name='id_sexo[]' class="select">
								<option value=0>[ Seleccione ]</option>
								<? $sexo_dao->ListarCombo('combo','',''); ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><b>G. Poblacional</b></td>
						<td>
							<select id='id_etnia<?=$v?>' name='id_etnia[]' class="select" onchange="getDataV1('comboBoxSubetnia','ajax_data.php?object=comboBoxSubetnia&id_etnia='+this.value+'&id_field=id_subetnia'+numero,'comboBoxSubetnia')">
								<option value=0>[ Seleccione ]</option>
								<? $etnia_dao->ListarCombo('combo','',''); ?>
							</select>
						</td>
						<td><b>Sub Etnia</b></td>
						<td id="comboBoxSubetnia">
							<select id='id_subetnia<?=$v?>' name='id_subetnia[]' class="select">
								<option value=0>[ Seleccione ]</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><b>Ocupaci&oacute;n</b></td>
						<td>
							<select id='id_ocupacion<?=$v?>' name='id_ocupacion[]' class="select">
								<option value=0>[ Seleccione ]</option>
								<? $ocupacion_dao->ListarCombo('combo','',''); ?>
							</select>
						</td>
						<td><a href="#" onClick="addCampoVictima('');return false;">Adicionar V&iacute;ctimas</a>&nbsp;<img src="images/icono_info.png" onmouseover="Tip('<b>Adicionar Registro</b><br>Esta opción permite adicionar otro registro de víctimas')" onmouseout="UnTip()"></td>
					</tr>
				</table>
			</div>
			<?
			$v++;
		}
		$d++;
		$top_v += 380;

	}	
}
?>
<!--<div><img src="images/home/evento_conflicto.jpg"></div>-->
<?
if ($accion == 'actualizar'){ ?>
	<div class="pathway"><img src='images/back.gif'>&nbsp;<a href='javascript:history.back()'>Regresar al Listado</a></div>
<? } ?>

<!--<div><img src="images/home/listar.png">&nbsp;<a href="index.php?m_e=evento_c&accion=listar&class=EventoConflictoDAO&method=ListarTabla&param=">Listar Eventos</a></div>-->
<div class="tabber" id="tab_info_basica">
	<div class="tabbertab">
		<h2>&nbsp;<img src="images/evento_c/info_basica.gif" border="0"></h2><br>
		<table border="0" cellpadding="5" cellspacing="1" width="800" align="center">
			<tr class="titulo_lista"><td colspan="2" align="center">INFORMACION BASICA DEL EVENTO</td></tr>
		 	<tr>
				<td><b>ID del Evento:</b>&nbsp;<?=$id?></td>
				<td><b>Fecha Evento:</b> (*)&nbsp;
				<? $calendar->make_input_field(
				// calendar options go here; see the documentation and/or calendar-setup.js
				array('firstDay'       => 1, // show Monday first
				'ifFormat'       => '%Y-%m-%d',
				'timeFormat'     => '12'),
				// field attributes go here
				array('class'       => 'textfield',
				'size'			=> 12,
				'value'			 => $evento_vo->fecha_evento,
				'name'        => 'fecha_evento'));
				?>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/icono_info.png" onmouseover="Tip('<b>Fecha Evento</b><br>Fecha en la que sucedió el evento')" onmouseout="UnTip()">
				</td>
			</tr>
			<tr>
				<td align="left" colspan="2">
					<b>Resumen del Evento (*)</b>
					<br>(Este campo es generado autom&aacute;ticamente, despu&eacute;s de ingresar toda la informaci&oacute;n del evento puede revisarlo)
					<br><br><textarea id='sintesis' name="sintesis" class="textfield" cols="100" rows="10"><?=$evento_vo->sintesis?></textarea>
				</td>
			</tr>
		</table>
	</div>

	<div class="tabbertab" id="div_desc">
		<h2>&nbsp;<img src="images/evento_c/desc.gif" border="0"></h2><br>
		<table border="0" cellpadding="5" cellspacing="1" width="950" align="center">
			<tr class="titulo_lista"><td colspan="2" align="center">DESCRIPCION DEL EVENTO</td></tr>
			<tr>
				<td><img src="images/adver.jpg"></td>
				<td>
					<b>Nota: para un correcto funcionamiento es necesario que primero diligencie completamente la primera descripci&oacute;n con las v&iacute;ctimas si es el caso y luego si desea, agregue otra descripci&oacute;n<br>
					<br>Para seleccionar varias opciones de un combo, use la tecla Ctrl + click izquierdo del mouse, si desea desmarcar una opción ya seleccionada use la misma combinaci&oacute;n Ctrl + click izquierdo</b>
				</td>
			</tr>
		</table>
		<?
		if ($accion == 'insertar' || ($accion == 'actualizar') && count($desc_evento['id']) == 0){ ?>
			<table border="0" cellpadding="5" cellspacing="1" class="tabla_input_desc" id="tabla_desc" align="center">
				<tr class="titulo_lista"><td colspan="3" align="center"><b>DESCRIPCION # 1</b></td></tr>			
				<tr>
					<td><a href="#" onClick="addCampo('registros');return false;">Adicionar Descripci&oacute;n</a>&nbsp;<img src="images/icono_info.png" onmouseover="Tip('<b>Adicionar Descripción</b><br>Esta opción permite adicionar otra descripci&oacute;n al evento')" onmouseout="UnTip()"></td>
					<td align="right" width="200"><b>Categoria (*)</b></td>
					<td width="600">
						<select id='id_cat' name='id_cat[]' class="select" onchange="getDataV1('comboBoxSubcategoria','ajax_data.php?object=comboBoxSubcategoria&id_cat='+this.value,'comboBoxSubcategoria')">
							<option value=''>[ Seleccione ]</option>
							<? $cat_dao->ListarCombo('combo','',''); ?>
						</select>
					</td>
				</tr>
				<tr>
					<td  class="link_victima"><a href="#" onClick="showDivVictimas('victimas_0');return false;">Adicionar<br>V&iacute;ctimas</a>&nbsp;<img src="images/icono_info.png" onmouseover="Tip('<b>Adicionar Víctimas</b><br>Esta opción permite adicionar las víctimas de la descripción del evento')" onmouseout="UnTip()"></td>
					<td align="right"><b>Subcategoria (*)</td>
					<td id="comboBoxSubcategoria"><input type="hidden" id="id_subcat" name="id_subcat" value=""></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td align="right"><b>Actor/Presunto Perpretador</b></td>
					<td>
						<select id='id_abuelo0' name='id_abuelo[]' multiple size="7" class="select">
							<? $actor_dao->ListarCombo('combo','','nivel=1'); ?>
						</select>
						&nbsp;<img src="images/evento_c/listar.png">&nbsp;<a href="#" onclick="enviarActor(0);return false;">Listar Sub Actor</a>
					</td>
				</tr>				
				<tr>
					<td>&nbsp;</td>
					<td align="right"><b>Sub Actor/Presunto Perpretador</b></td>
					<td>
						<table width="600">
							<tr>
								<td id="comboBoxSubactor0">
									<select id="id_papa0" name="id_papa[]" class="select">
										<? $actor_dao->ListarCombo('combo',320,'id_papa=20'); ?>
									</select>&nbsp;&nbsp;
									<a href="#" onclick="showDivOcurrencia('papa',event);return false;"><img src="images/icono_search.png" border="0">&nbsp;Buscar</a>
								</td>
							</tr>
						</table>
					</td>
				</tr>				
				<tr>
					<td>&nbsp;</td>
					<td align="right"><b>Sub-Sub Actor/Presunto Perpretador</b></td>
					<td>
						<table width="600">
							<tr>
								<td id="comboBoxSubSubactor0">
									<select id="id_hijo0" name="id_hijo[]" class="select">
										<? $actor_dao->ListarCombo('combo',321,'id_papa=320'); ?>
									</select>&nbsp;&nbsp;
									<a href="#" onclick="showDivOcurrencia('hijo',event);return false;"><img src="images/icono_search.png" border="0">&nbsp;Buscar</a></td>
							</tr>
						</table>
					</td>
				</tr>				
			</table>
		<?
		}
		else {
			$d = 1;
			foreach ($desc_evento['id_cat'] as $id_cat){
				$dd = $d - 1;
				$id_de = $desc_evento['id'][$dd];
				
				$actores = $evento_dao->getActorEvento($id_de,1);
				$sactores = $evento_dao->getActorEvento($id_de,2);
				$ssactores = $evento_dao->getActorEvento($id_de,3);
				
				?>
				<table border="0" cellpadding="5" cellspacing="1" class="tabla_input_desc" id="tabla_desc_<?=$dd?>" align="center">
					<tr class="titulo_lista"><td colspan="3" align="center"><b>DESCRIPCION # <?=$d?></b></td></tr>			
					<tr>
						<?

						if ($d == 1){
							?>
							<td><a href="#" onClick="addCampo('registros');return false;">Adicionar Descripci&oacute;n</a>&nbsp;<img src="images/icono_info.png" onmouseover="Tip('<b>Adicionar Descripción</b><br>Esta opción permite adicionar otra descripci&oacute;n al evento')" onmouseout="UnTip()"></td>
							<?
						}
						else {
							?>
							<td><a href="#" onClick="elimCamp2(this.id);return false;" id="tabla_desc_<?=$dd?>">Eliminar</a></td>
							<?
						}
						?>
						<td align="right" width="200"><b>Categoria (*)</b></td>
						<td width="600">
							<select id='id_cat_<?php echo $d ?>' name='id_cat[]' class="select" onchange="getDataV1('comboBoxSubcategoria','ajax_data.php?object=comboBoxSubcategoria&id_cat='+this.value,'comboBoxSubcategoria_<?php echo $d; ?>')">
								<option value=''>[ Seleccione ]</option>
								<? $cat_dao->ListarCombo('combo',$id_cat,''); ?>
							</select>
						</td>
					</tr>
					<tr>
						<td  class="link_victima"><a href="#" onClick="showDivVictimas('victimas_<?=$dd?>');return false;">Adicionar<br>V&iacute;ctimas</a>&nbsp;<img src="images/icono_info.png" onmouseover="Tip('<b>Adicionar Víctimas</b><br>Esta opción permite adicionar las víctimas de la descripción del evento')" onmouseout="UnTip()"></td>
						<td align="right"><b>Subcategoria (*)</td>
						<td id="comboBoxSubcategoria_<?php echo $d ?>">
							<select id='id_subcat' name='id_subcat[]' class="select">
								<option value=''>[ Seleccione ]</option>
								<? $subcat_dao->ListarCombo('combo',$desc_evento['id_scat'][$dd],"id_cateven=$id_cat"); ?>
							</select>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td align="right"><b>Actor/Presunto Perpretador</b></td>
						<td>
							<select id='id_abuelo<?=$dd?>' name='id_abuelo[]' multiple size="7" class="select">
								<? $actor_dao->ListarCombo('combo',$actores['id'],'nivel=1'); ?>
							</select>
							&nbsp;<img src="images/evento_c/listar.png">&nbsp;<a href="#" onclick="enviarActor(0);return false;">Listar Sub Actor</a>
						</td>
					</tr>				
					<tr>
						<td>&nbsp;</td>
						<td align="right"><b>Sub Actor/Presunto Perpretador</b></td>
						<td>
							<table width="600">
								<tr>
									<td id="comboBoxSubactor0">
											<?
											if (count($sactores['id']) > 0 && $sactores['id'][0] != 320 && $sactores['id'][0] != ''){
												
												echo '<select id="id_papa'.$dd.'" name="id_papa[]" class="select" multiple size="7">';
												
												$id_s = implode(",",$actores['id']);
												
												$vos = $actor_dao->GetAllArray("ID_ACTOR IN ($id_s)");
												foreach ($vos as $vo){
													echo "<option value='' disabled>-------- ".$vo->nombre." --------</option>";
													$sacs = $actor_dao->GetAllArray("id_papa = $vo->id");
													foreach ($sacs as $vo_h){
														echo "<option value='".$vo_h->id."'";
														if (in_array($vo_h->id,$sactores['id']))	echo " selected ";
														echo ">".$vo_h->nombre."</option>";
													}
												}
											}
											else{
												echo '<select id="id_papa'.$dd.'" name="id_papa[]" class="select">';
												$actor_dao->ListarCombo('combo',320,'id_papa=20');
											}
											?>
										</select>&nbsp;&nbsp;
										<a href="#" onclick="showDivOcurrencia('papa',event);return false;"><img src="images/icono_search.png" border="0">&nbsp;Buscar</a>
									</td>
								</tr>
							</table>
						</td>
					</tr>				
					<tr>
						<td>&nbsp;</td>
						<td align="right"><b>Sub-Sub Actor/Presunto Perpretador</b></td>
						<td>
							<table width="600">
								<tr>
									<td id="comboBoxSubSubactor0">
											<?
											if (count($ssactores['id']) > 0 && $ssactores['id'][0] != '' && $ssactores['id'][0] != 321){
												
												echo '<select id="id_hijo'.$dd.'" name="id_hijo[]" class="select" multiple size="7">';
												$id_s = implode(",",$sactores['id']);
												
												$vos = $actor_dao->GetAllArray("ID_ACTOR IN ($id_s)");
												foreach ($vos as $vo){
													echo "<option value='' disabled>-------- ".$vo->nombre." --------</option>";
													$sacs = $actor_dao->GetAllArray("id_papa = $vo->id");
													foreach ($sacs as $vo_h){
														echo "<option value='".$vo_h->id."'";
														if (in_array($vo_h->id,$ssactores['id']))	echo " selected ";
														echo ">".$vo_h->nombre."</option>";
													}
												}
											}
											else{
												echo '<select id="id_hijo'.$dd.'" name="id_hijo[]" class="select">';
												$actor_dao->ListarCombo('combo',321,'id_papa=320');
											}
											?>
										</select>&nbsp;&nbsp;
										<a href="#" onclick="showDivOcurrencia('hijo',event);return false;"><img src="images/icono_search.png" border="0">&nbsp;Buscar</a></td>
								</tr>
							</table>
						</td>
					</tr>				
				</table>
				<br>
				<?
				$d++;
			}
			
		}
		?>

	</div>

     <div class="tabbertab">
		<h2>&nbsp;<img src="images/evento_c/fuente.gif" border="0"></h2><br>
		<div style="overflow-x:auto;width:950px" align="center">
		<table border="0" cellpadding="2" cellspacing="2" id="tabla_fuente" align="center" width="950">
			<tr class="titulo_lista"><td colspan="6" align="center">FUENTE DEL EVENTO</td></tr>
			<!--<tr>
				<td><img src="images/spacer.gif" width="60" height="1"></td>
				<td><img src="images/spacer.gif" width="100" height="1"></td>
				<td><img src="images/spacer.gif" width="150" height="1"></td>
				<td><img src="images/spacer.gif" width="200" height="1"></td>
				<td><img src="images/spacer.gif" width="130" height="1"></td>
				<td><img src="images/spacer.gif" height="1"></td>
			</tr>-->
			<tr class="tabla_input_desc">
				<td>&nbsp;</td>
				<td align="center"><b>Tipo de Fuente</b></td>
				<td align="center"><b>Fuente</b> (*)</td>
				<td align="center"><b>Descripci&oacute;n del Evento</b> (*)&nbsp;<img src="images/icono_info.png" onmouseover="Tip('<b>Descripción del Evento</b><br>Descripción del evento según reporte de la fuente')" onmouseout="UnTip()"></td>
				<td align="center"><b>Fecha Fuente&nbsp;</b> (*)&nbsp;<img src="images/icono_info.png" onmouseover="Tip('<b>Fecha Fuente</b><br>Fecha del evento reportado por la fuente')" onmouseout="UnTip()"></td>
				<td align="center"><b>Medio</b>&nbsp; (*)&nbsp;<img src="images/icono_info.png" onmouseover="Tip('<b>Medio</b><br>Descripción del medio que uso para la publicación (Página, columna, etc)<br><br><i>(Nombre del artículo, Página, sección, link)</i>')" onmouseout="UnTip()"></td>
			</tr>
			<?
            if ($accion == 'insertar' || ($accion == 'actualizar') && count($fuentes['id_fuente']) == 0){ ?>
				<tr class="tabla_input_desc">
					<td><a href="#" onClick="addCampoFuente('fuentes');return false;">Adicionar<br> Fuente</a>&nbsp;<img src="images/icono_info.png" onmouseover="Tip('<b>Adicionar Fuente</b><br>Esta opción permite adicionar una fuente extra y toda la información asociada a esta')" onmouseout="UnTip()"></td>
					<td>
						<select id='id_fuente' name='id_fuente[]' class="select" onchange="getDataV1('comboBoxSubfuente','ajax_data.php?object=comboBoxSubfuente&id_fuente='+this.value,'comboBoxSubfuente')">
							<option value=''>[ Seleccione ]</option>
							<? $fuente_dao->ListarCombo('combo','',''); ?>
							</select>
					</td>
					<td id="comboBoxSubfuente"><input type="hidden" id="id_subfuente" value=""></td>
					<td>
						<textarea id='desc_fuente' name="desc_fuente[]" class="textarea_fuente_desc">&nbsp;</textarea>
					</td>
					<td>
						<? $calendar->make_input_field(
						// calendar options go here; see the documentation and/or calendar-setup.js
						array('firstDay'       => 1, // show Monday first
						'ifFormat'       => '%Y-%m-%d',
						'timeFormat'     => '12'),
						// field attributes go here
						array('class'       => 'textfield',
						'size'			=> 12,
						//			'value'			 => $evento_vo->fecha_fuente,
						'name'        => 'fecha_fuente[]'));
						?>
					</td>
					<td>
						<textarea id='refer_fuente' name="refer_fuente[]" class="textarea_fuente_refer">&nbsp;</textarea>
					</td>
				</tr>	
			<?
			}
			else{
				$l = 0;
				foreach ($fuentes['id_fuente'] as $id_fuente){
					$ll = $l + 1;
					?>
					<tr class="tabla_input_desc" id="fuente<?=$ll?>">
						<?
						if ($l == 0){
							?>
							<td><a href="#" onClick="addCampoFuente('fuentes');return false;">Adicionar<br> Fuente</a>&nbsp;<img src="images/icono_info.png" onmouseover="Tip('<b>Adicionar Fuente</b><br>Esta opción permite adicionar una fuente extra y toda la información asociada a esta')" onmouseout="UnTip()"></td>
						<?}
						else{
							?>
							<td align="center"><a href="#" onClick="elimCamp2('fuente<?=$ll?>');return false;">Eliminar</a></td>
						<?							
						}
						?>
						<td>
							<select id='id_fuente' name='id_fuente[]' class="select" onchange="getDataV1('comboBoxSubfuente','ajax_data.php?object=comboBoxSubfuente&id_fuente='+this.value,'comboBoxSubfuente<?=$l?>')">
								<option value=''>[ Seleccione ]</option>
								<? $fuente_dao->ListarCombo('combo',$id_fuente,''); ?>
							</select>
						</td>
						<td id="comboBoxSubfuente<?=$l?>">
							<select id='id_subfuente' name='id_subfuente[]' class="select" style="width:200px">
								<option value=''>[ Seleccione ]</option>
								<? $subfuente_dao->ListarCombo('combo',$fuentes['id_sfuente'][$l],"id_fueven=$id_fuente"); ?>
							</select>

						</td>
						<td>
							<textarea id='desc_fuente' name="desc_fuente[]" class="textarea_fuente_desc"><?=$fuentes['desc'][$l]?></textarea>
						</td>
						<td>
							<? $calendar->make_input_field(
							// calendar options go here; see the documentation and/or calendar-setup.js
							array('firstDay'       => 1, // show Monday first
							'ifFormat'       => '%Y-%m-%d',
							'timeFormat'     => '12'),
							// field attributes go here
							array('class'       => 'textfield',
							'size'			=> 12,
							'value'			 => $fuentes['fecha'][$l],
							'name'        => 'fecha_fuente[]'));
							?>
						</td>
						<td>
							<textarea id='refer_fuente' name="refer_fuente[]" class="textarea_fuente_refer"><?=$fuentes['medio'][$l]?></textarea>
						</td>
					</tr>
					<?
					$l++;
				}	
			}
			?>
		</table>
		</div>
     </div>

     <div class="tabbertab">
	  <h2>&nbsp;<img src="images/evento_c/localizacion.gif" border="0"></h2><br>
	  <table border="0" cellpadding="5" cellspacing="1" width="950" id="tabla_depto" align="center">
	  	<tr class="titulo_lista"><td colspan="6" align="center">LOCALIZACION GEOGRAFICA DEL EVENTO</td></tr>
			<tr class="tabla_input_desc">
				<td>&nbsp;</td>
				<td width="25%"><b>Departamento (*)</b></td>
				<td width="25%"><b>Municipio (*)</b></td>
				<td width="30%"><b>Lugar</b></td>
			</tr>
			<?
            if ($accion == 'insertar' || ($accion == 'actualizar') && count($locas['mpios']) == 0){ ?>
				<tr class="tabla_input_desc">
					<td><a href="#" onClick="addCampoLugar('lugares');return false;">Adicionar Localizaci&oacute;n</a>&nbsp;<img src="images/icono_info.png" onmouseover="Tip('<b>Adicionar Registro</b><br>Esta opción permite especificar otro lugar donde ocurrió el evento')" onmouseout="UnTip()"></td>
					<td>
					<select id='id_depto' name="id_depto[]" class="select" onchange="getDataV1('comboBoxMunicipio','ajax_data.php?object=comboBoxMunicipioEvento&multiple=0&titulo=0&separador_depto=0&id_deptos='+this.value,'comboBoxMunicipio')">
					<option value=''>[ Seleccione ]</option>
					<? $depto_dao->ListarCombo('combo','',''); ?>
					</select>
					</td>
					<td id="comboBoxMunicipio"><input type="hidden" id="id_muns" name="id_muns[]" value=""></td>
					<td><textarea id="lugar" name="lugar[]" class="textfield" cols=40 rows="2">&nbsp;</textarea></td>
				</tr>
			<?
			}
			else{
				$l = 0;
				foreach ($locas['mpios'] as $id_mun){
					$ll = $l + 1;
					$mun = $municipio_dao->Get($id_mun);
					?>
					<tr class="tabla_input_desc" id='depto<?=$ll?>'>
						<?
						if ($l == 0){
							?>
							<td><a href="#" onClick="addCampoLugar('lugares');return false;">Adicionar Localizaci&oacute;n</a>&nbsp;<img src="images/icono_info.png" onmouseover="Tip('<b>Adicionar Registro</b><br>Esta opción permite especificar otro lugar donde ocurrió el evento')" onmouseout="UnTip()"></td>
						<?
						}
						else{
							?><td align="center"><a href="#" onClick="elimCamp2('depto<?=$ll?>');return false;">Eliminar</a></td><?
						}
						?>
						<td>
							<select id="id_depto" name="id_depto[]" class="select" onchange="getDataV1('comboBoxMunicipio','ajax_data.php?object=comboBoxMunicipioEvento&multiple=0&titulo=0&separador_depto=0&id_deptos='+this.value,'comboBoxMunicipio<?=$l?>')">
								<option value=''>[ Seleccione ]</option>
								<? $depto_dao->ListarCombo('combo',$mun->id_depto,''); ?>
							</select>
						</td>
						<td id="comboBoxMunicipio<?=$l?>">
							<select id="id_muns" name="id_muns[]" class="select">
								<option value=''>[ Seleccione ]</option>
								<? $municipio_dao->ListarCombo('combo',$id_mun,''); ?>
							</select>
						</td>
						<td><textarea id="lugar" name="lugar[]" class="textfield" cols=40 rows="2"><?=$locas['lugar'][$l]?></textarea></td>
					</tr>					
					<?
					$l++;
				}
			}
			?>
		</table>
     </div>
     
</div>
<br>
<div align="center">
	<!-- Check resumen -->
	Desea revisar el resumen del evento generado autom&aacute;ticamente&nbsp;
	<input type="radio" id="check_resumen_si" name="check" checked>&nbsp;Si&nbsp;
	<input type="radio" id="check_resumen_no" name="check">&nbsp;No&nbsp;&nbsp;
</div>
<br>
<div align="center">
	<input type="hidden" name="accion" value="<?=$accion?>">
	<input type="hidden" name="id" value="<?=$id;?>" />
	<input type="hidden" id="num_reg" name="num_reg" value=0>
	<input type="hidden" id="num_vict_desc" name="num_vict_desc">
	<input type="hidden" id="num_actores_desc" name="num_actores_desc">
	<input type="hidden" id="num_subactores_desc" name="num_subactores_desc">
	<input type="hidden" id="num_subsubactores_desc" name="num_subsubactores_desc">
	<input type="submit" value="Aceptar" name="submit" class="boton" onclick="return submitForm();">&nbsp; (*) Campos Requeridos
</div>
</form>
