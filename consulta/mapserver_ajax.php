<?
//LIBRERIAS
include_once("consulta/lib/libs_mapa_i.php");

//INICIALIZACION DE VARIABLES
$clase_dao = New ClaseDesplazamientoDAO();
$fuente_dao = New FuenteDAO();
$tipo_dao = New TipoDesplazamientoDAO();
$desplazamiento_dao = New DesplazamientoDAO();
$cat_dao = New CategoriaDatoSectorDAO();
$dato_sectorial_dao = New DatoSectorialDAO();
$periodo_dao = New PeriodoDAO();
$depto_dao = New DeptoDAO();
$sector_dao = New SectorDAO();
$tipo_org_dao = New TipoOrganizacionDAO();
$pob_dao = New PoblacionDAO();
$enfoque_dao = New EnfoqueDAO();
$cat_eve_dao = New CatEventoConflictoDAO();

$mes = array("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");

//Define si viene del website
$sidih = 1;
$left_map_ini = 380;
$left_all_info = 385;
$left_map_ref = 590;
$width_page = 700;	
$onload = '';
$case = '';

//Eventos onload para la api
if (isset($_GET["case"]) && in_array($_GET["case"],array("desplazamiento","dato_sectorial","org","evento_c","proyecto_undaf"))){
	$sidih = 0;
	$left_map_ini = 5;
	$left_all_info = 10;
	$left_map_ref = 224;
	$width_page = 700;	
	$case = $_GET["case"];
	switch ($case){
		case 'desplazamiento':
			$id_fuente = $_GET["id_fuente"];
			$id_clase = $_GET["id_clase"];
			$id_tipo = $_GET["id_tipo"];
			$id_periodo = $_GET["id_periodo"];
			$id_tipo_periodo = $_GET["id_tipo_periodo"];
			$id_depto_filtro = $_GET["id_depto_filtro"];
			$variacion = $_GET["variacion"];
			$tasa = $_GET["tasa"];
			
			$onload = ($id_depto_filtro > 0) ? "onload=setExtentByDepto('$id_depto_filtro',id_hidden);setTimeout('mapaDesplazamiento()',500);" : "onload=setTimeout('mapaDesplazamiento()',500);";
			//$onload = "onload=setExtentByDepto('$id_depto_filtro',id_hidden);setTimeout('mapaDesplazamiento()',500);";
		break;
		case 'evento_c':
			$reporte = $_GET["reporte"];
			$id_cats = $_GET["id_cats"];
			$id_subcats = $_GET["id_subcats"];
			$f_ini = $_GET["f_ini"];
			$f_fin = $_GET["f_fin"];
			$id_depto_filtro = $_GET["id_depto_filtro"];
			
			$onload = ($id_depto_filtro > 0) ? "onload=setExtentByDepto('$id_depto_filtro',id_hidden);setTimeout('mapaEventoC()',500);" : "onload=setTimeout('mapaEventoC()',500);";
		break;
		case 'dato_sectorial':
			$id_dato = $_GET["id_dato"];
			$aaaa = $_GET["aaaa"];
			$id_depto_filtro = $_GET["id_depto_filtro"];
			$variacion = $_GET["variacion"];
			$tasa = $_GET["tasa"];
			
			$onload = ($id_depto_filtro > 0) ? "onload=setExtentByDepto('$id_depto_filtro',id_hidden);setTimeout('mapaDatoSectorial()',500);" : "onload=setTimeout('mapaDatoSectorial()',500);";
		break;
		case 'proyecto_undaf':
			$filtro = $_GET["filtro"];
			$id_filtro = $_GET["id_filtro"];
			$id_depto_filtro = $_GET["id_depto_filtro"];
			$id_proy = $_GET["id_proy"];
			
			$onload = ($id_depto_filtro > 0) ? "onload=setExtentByDepto('$id_depto_filtro',id_hidden);setTimeout('mapaProyectoUndaf()',500);document.body.style.background='#fff3c4'" : "onload=setTimeout('mapaProyectoUndaf()',500);;document.body.style.background='#fff3c4'";

			$width_page = 850;
		break;
		
		case 'org':
			$filtro = $_GET["filtro"];
			$id_filtro = $_GET["id_filtro"];
			$id_depto_filtro = $_GET["id_depto_filtro"];
			$id_org = $_GET["id_org"];
			$id_tipo = "''";
			$id_sector = "''";
			$id_pob = "''";
			$id_enfoque = "''";

			switch ($filtro){
				case 'sector':
					$id_sector = $id_filtro; 
				break;

				case 'poblacion':
					$id_pob = $id_filtro; 
				break;
			}
			
			$onload = ($id_depto_filtro > 0) ? "onload=setExtentByDepto('$id_depto_filtro',id_hidden);setTimeout('mapaOrg()',500);document.body.style.background='#fff3c4'" : "onload=setTimeout('mapaOrg()',500);;document.body.style.background='#fff3c4'";

			$width_page = 850;
		break;
	}
}
?>

<html>
<head>
<title>SIDI UMAIC - Colombia</title>

<link href="style/consulta.css" rel="stylesheet" type="text/css" />
<script src="js/mscross-1.1.9.js" type="text/javascript"></script>
<script src="t/js/general.js" type="text/javascript"></script>
<script src="js/mapserver.js" type="text/javascript"></script>
<script src="t/js/ajax.js" type="text/javascript"></script>

<link href="style/accordions.css" rel="stylesheet" type="text/css" />

<? if ($sidih == 1){ ?>
<link href="js/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css" type="text/css" rel="stylesheet" media="screen" />
<script src="js/rico/rico.js" type="text/javascript"></script>
<script src="js/filterlist.js" type="text/javascript" ></script>
<script src="js/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js" type="text/javascript"></script>
<script type="text/javascript">

Rico.loadModule('Accordion');

Rico.onLoad( function() {
  new Rico.Accordion( $$('div.panelheader'), $$('div.panelContent'),
                      {panelHeight:530, hoverClass: 'mdHover', selectedClass: 'mdSelected'});
});
</script>
<? } ?>

<script type="text/javascript">
var server = "http://<?=$_SERVER["SERVER_NAME"] ?>/";
var caso;
var debug_map = 0;
var extent_org = '-161112.1,1653895,-469146,1386463';
var id_hidden = 'map_extent';   //Input hidden con el valor del extent

<? if ($sidih == 1 || ($sidih == 0 && $case == 'desplazamiento')){ ?>
function mapaDesplazamiento(){
	
	<?
	if ($sidih == 1){ ?>
		var id_tipo_periodo = getRadioCheck(document.getElementsByName('t_periodo'));
		
		if (id_tipo_periodo == 'aaaa')	var id_periodo = getOptionsCheckBox(document.getElementsByName('id_periodo_'+id_tipo_periodo));
		else							var id_periodo = comboToString(document.getElementById('select_'+id_tipo_periodo));
		
		//var id_tipo_periodo = 'aaaa';
		
		var id_fuente = document.getElementById('id_fuente').value;
		var id_clase = document.getElementById('id_clase').value;
	
		var id_depto_filtro = document.getElementById('id_depto_filtro').value;
		
		var variacion = 0;
		if (document.getElementById('variacion').checked == true)	variacion = 1;
	
		var tasa = 0;
		if (document.getElementById('tasa').checked == true)	tasa = 1;
		
		if (id_fuente == 2){
			var id_tipo = getOptionsCheckBox(document.getElementsByName('id_tipo[]'));
		}
		else{
			var id_tipo = 2;
		}
		
	<?
	}
	else{ ?>
		var id_tipo_periodo = '<?=$id_tipo_periodo?>';
		var id_periodo = '<?=$id_periodo?>';
		var id_fuente = <?=$id_fuente?>;
		var id_clase = <?=$id_clase?>;
		var id_depto_filtro = <?=$id_depto_filtro?>;
		var variacion = <?=$variacion?>;
		var tasa = <?=$tasa?>;
		var id_tipo = '<?=$id_tipo?>';
		
	<?		
	}
	?>
	
	var map_extent = parseMapExtent(id_hidden);
	
	//Check de lo obligatorio
	msg = '';
	if (id_fuente == '')	msg += "Seleccione alguna Fuente\n";
	if (id_clase == '')	msg += "Seleccione alguna Clase\n";
	if (id_fuente == 2 && id_tipo == '')	msg += "Seleccione algún Tipo\n";
	if (id_periodo == '')	msg += "Seleccione algún Periodo\n";
	
	//Check de 2 periodos para variacion
	if (variacion == 1){
		num_per = id_periodo.split(",").length; 
		if(num_per != 2){
			msg += "Para calcular variación, seleccione 2 periodos!";
		}
	}

	//Check de 1 periodo para tasa
	if (tasa == 1){
		num_per = id_periodo.split(",").length; 
		if(num_per > 1){
			msg += "Para calcular tasa, seleccione 1 periodo!";
		}
	}
	
	//Check de clase depende de la fuente
	if (id_fuente == 1 && id_clase != 3){
		msg += "Para CODHES seleccion Estimado Llegadas!";
	}

	if (id_fuente == 2 && id_clase == 3){
		msg += "Para Acción Social seleccion Recepción o Expulsión!";
	}
	
	if (msg != ''){
		alert(msg);
		return false;
	}
	
	//oculta div ini
	//document.getElementById('ini').style.display = 'none';
	document.getElementById('div_info').style.display = 'none';
	
	//Vacia el div del mapa por si tiene alguno
	document.getElementById('map_tag').innerHTML = '';
	document.getElementById('map_ref_tag').innerHTML = '';
	
	myMap = new msMap(document.getElementById('map_tag'),'standard');
	myMap.setCgi(server + 'sissh/consulta/mapserver_mpio.php');
	//alert('case=desplazamiento&id_fuente='+id_fuente+'&id_clase='+id_clase+'&id_tipo='+id_tipo+'&id_periodo='+id_periodo+'&id_tipo_periodo='+id_tipo_periodo+'&id_depto_filtro='+id_depto_filtro);
	myMap.setArgs('case=desplazamiento&id_fuente='+id_fuente+'&id_clase='+id_clase+'&id_tipo='+id_tipo+'&id_periodo='+id_periodo+'&id_tipo_periodo='+id_tipo_periodo+'&id_depto_filtro='+id_depto_filtro+'&variacion='+variacion+'&tasa='+tasa);
	myMap.setFullExtent(map_extent[0],map_extent[1],map_extent[2],map_extent[3]); // (xmin,xmax,ymin,ymax)
		
	myMap2 = new msMap(document.getElementById('map_ref_tag'));
	myMap2.setCgi(server + 'sissh/consulta/mapserver_mpio.php');
	myMap2.setArgs('case=desplazamiento&id_fuente='+id_fuente+'&id_clase='+id_clase+'&id_tipo='+id_tipo+'&id_periodo='+id_periodo+'&id_tipo_periodo='+id_tipo_periodo+'&id_depto_filtro='+id_depto_filtro+'&map_ref=1');
	myMap2.setFullExtent(map_extent[0],map_extent[1],map_extent[2],map_extent[3]); // (xmin,xmax,ymin,ymax)
	
	myMap.setReferenceMap(myMap2);
	
	if (debug_map == 1)	myMap.debug();
	myMap.redraw();
	myMap2.redraw();
	
	//Activa la opcion de mostrar map_ref
	document.getElementById('a_map_ref').style.display = '';
	document.getElementById('div_ino_map_ref').style.display = '';
	
	caso = 'desplazamiento';
	
}
function changeComboPeriodo(caso){
	document.getElementById('select_aaaa').style.display = 'none';
	document.getElementById('a_select_aaaa').style.display = 'none';
	document.getElementById('select_mes').style.display = 'none';
	document.getElementById('a_select_mes').style.display = 'none';
	document.getElementById('select_trim').style.display = 'none';
	document.getElementById('a_select_trim').style.display = 'none';
	document.getElementById('select_sem').style.display = 'none';
	document.getElementById('a_select_sem').style.display = 'none';
	
	document.getElementById(caso).style.display = '';
	document.getElementById('a_'+caso).style.display = '';
}
function checkFuente(id_fuente){
	//CODHES
	if (id_fuente == 1){
		document.getElementById('id_tipo_1').disabled = 'true';
		document.getElementById('id_tipo_2').disabled = 'true';
	}
	else{
		document.getElementById('id_tipo_1').disabled = '';
		document.getElementById('id_tipo_2').disabled = '';
	}
}

<?
} 
if ($sidih == 1 || ($sidih == 0 && $case == 'dato_sectorial')){ ?>

function mapaDatoSectorial(){
	
	<?
	if ($sidih == 1){ ?>

		var id_dato = document.getElementById('id_dato').value;
		var id_depto_filtro = document.getElementById('id_depto_filtro').value;
		var aaaa = getOptionsCheckBox(document.getElementsByName('aaaa_dato'));
		
		var variacion = 0;
		if (document.getElementById('variacion_dato').checked == true)	variacion = 1;
	
		var tasa = 0;
		if (document.getElementById('tasa_dato').checked == true)	tasa = 1;
	<?
	}
	else{ ?>
		var id_dato = <?=$id_dato?>;
		var id_depto_filtro = <?=$id_depto_filtro?>;
		var aaaa = '<?=$aaaa?>';
		
		var variacion = <?=$variacion?>;
		var tasa = <?=$tasa?>;
	<?	
	}
	?>
	
	var map_extent = parseMapExtent(id_hidden);
	
	//Check de lo obligatorio
	msg = '';
	if (id_dato == '-1' || id_dato == '')	msg += "Seleccione algún Dato Sectorial\n";
	if (aaaa == '')	msg += "Seleccione algún Periodo\n";
	
	//Check de 2 periodos para variacion
	if (variacion == 1){
		num_per = aaaa.split(",").length; 
		if(num_per != 2){
			msg += "Para calcular variación, seleccione 2 periodos, si el dato solo tiene un periodo disponible no es posible calcular la variación";
		}
	}

	//Check de 1 periodo para tasa
	if (tasa == 1){
		num_per = aaaa.split(",").length; 
		if(num_per > 1){
			msg += "Para calcular tasa, seleccione 1 periodo!";
		}
	}
	
	if (msg != ''){
		alert(msg);
		return false;
	}
	
	//Vacia el div del mapa por si tiene alguno
	document.getElementById('map_tag').innerHTML = '';
	
	//oculta div ini
	//document.getElementById('ini').style.display = 'none';
	document.getElementById('div_info').style.display = 'none';
	
	myMap = new msMap(document.getElementById('map_tag'),'standard');
	myMap.setCgi(server + 'sissh/consulta/mapserver_mpio.php');
	myMap.setArgs('case=dato_sectorial&id_dato='+id_dato+'&id_depto_filtro='+id_depto_filtro+'&aaaa='+aaaa+'&variacion='+variacion+'&tasa='+tasa);
	myMap.setFullExtent(map_extent[0],map_extent[1],map_extent[2],map_extent[3]); // (xmin,xmax,ymin,ymax)

	myMap2 = new msMap(document.getElementById('map_ref_tag'));
	myMap2.setCgi(server + 'sissh/consulta/mapserver_mpio.php');
	myMap2.setArgs('case=dato_sectorial&id_dato='+id_dato+'&id_depto_filtro='+id_depto_filtro+'&map_ref=1');
	myMap2.setActionNone();
	myMap2.setFullExtent(map_extent[0],map_extent[1],map_extent[2],map_extent[3]); // (xmin,xmax,ymin,ymax)
	
	myMap.setReferenceMap(myMap2);
	
	if (debug_map == 1)	myMap.debug();
	myMap.redraw();
	myMap2.redraw();
	
	//Activa la opcion de mostrar map_ref
	document.getElementById('a_map_ref').style.display = '';
	document.getElementById('div_ino_map_ref').style.display = '';
	
	caso = 'dato_sectorial';

}
function getAniosDato(id_dato,innerobject){
	
	document.getElementById(innerobject).style.display = '';
	
	getDataV1('','t/ajax_data.php?object=getAniosDatoSectorialToMapa&id_dato='+id_dato,innerobject);
}

function getDefinicionDato(id_dato,innerobject){
	
	document.getElementById(innerobject).style.display = '';
	
	getDataV1('','t/ajax_data.php?object=getDefinicionDatoSectorial&id_dato='+id_dato,innerobject);
}
<?
} 
if ($sidih == 1 || ($sidih == 0 && $case == 'org')){ ?>

function mapaOrg(){

	<?
	if ($sidih == 1){ ?>
		var id_tipo = document.getElementById('id_tipo').value;
		var id_sector = document.getElementById('id_sector').value;
		var id_pob = document.getElementById('id_pob').value;
		var id_enfoque = document.getElementById('id_enfoque').value;
		var id_depto_filtro = document.getElementById('id_depto_filtro').value;
	<?	
	}
	else{ ?>
		var id_tipo = <?=$id_tipo?>;
		var id_sector = <?=$id_sector?>;
		var id_pob = <?=$id_pob?>;
		var id_enfoque = <?=$id_enfoque?>;
		var id_depto_filtro = <?=$id_depto_filtro?>;

	<?
	}
	?>
	
	id_org = getRadioCheck(document.getElementsByName('id_org'));
	if (id_org == undefined)	id_org = '';
		
	var map_extent = parseMapExtent(id_hidden);
	
	//Check de lo obligatorio
	msg = '';
	if (id_tipo == '' && id_sector == '' && id_pob == '' && id_enfoque == '' && id_org == ''){
		if (!confirm('No ha seleccionado filtros, desea generar el mapa para todas las organizaciones?')){
			return false;
		}
	}
	
	//Vacia el div del mapa por si tiene alguno
	document.getElementById('map_tag').innerHTML = '';
	document.getElementById('map_ref_tag').innerHTML = '';
	
	//oculta div ini
	//document.getElementById('ini').style.display = 'none';
	document.getElementById('div_info').style.display = 'none';
	
	myMap = new msMap(document.getElementById('map_tag'),'standard');
	myMap.setCgi(server + 'sissh/consulta/mapserver_mpio.php');
	myMap.setArgs('case=org&id_tipo='+id_tipo+'&id_sector='+id_sector+'&id_pob='+id_pob+'&id_enfoque='+id_enfoque+'&id_depto_filtro='+id_depto_filtro+'&id_org='+id_org);
	myMap.setFullExtent(map_extent[0],map_extent[1],map_extent[2],map_extent[3]); // (xmin,xmax,ymin,ymax)

	myMap2 = new msMap(document.getElementById('map_ref_tag'));
	myMap2.setCgi(server + 'sissh/consulta/mapserver_mpio.php');
	myMap2.setArgs('case=desplazamiento&id_tipo='+id_tipo+'&id_sector='+id_sector+'&id_pob='+id_pob+'&id_enfoque='+id_enfoque+'&map_ref=1&id_depto_filtro='+id_depto_filtro);
	myMap2.setFullExtent(map_extent[0],map_extent[1],map_extent[2],map_extent[3]); // (xmin,xmax,ymin,ymax)
	
	myMap.setReferenceMap(myMap2);
	
	if (debug_map == 1)	myMap.debug();
	myMap.redraw();
	myMap2.redraw();
	
	//Activa la opcion de mostrar map_ref
	document.getElementById('a_map_ref').style.display = '';
	document.getElementById('div_ino_map_ref').style.display = '';
	
	caso = 'org';
	
}
function checkFiltrosOrg(id_combo){

	var id_combos = Array('id_tipo','id_sector','id_pob','id_enfoque');

	for(i=0;i<id_combos.length;i++){
		if (id_combo != id_combos[i]){
			document.getElementById(id_combos[i]).selectedIndex = "";
		}
	}
	
	//Borra los resultados de ocurrencias de Orgs
	document.getElementById('ocurrenciasOrg').innerHTML = '';
}

function buscarOrgs(e){
	
	texto = document.getElementById('s_org').value;
	
	keyNum = e.keyCode;
		
	if (keyNum == 8){  //Backspace
		texto = texto.slice(0, -1);  //Borra el ultimo caracter
	}
	else{
		keyChar = String.fromCharCode(keyNum);
		texto +=  keyChar;
	}
	
	var donde = 'comience';
	if (document.getElementById('contenga').checked == true)	donde = 'contenga';
	
	if (texto.length > 1){
		//Oculta los combos de filtros para fix en IE
		document.getElementById('id_tipo').style.display = 'none';
		document.getElementById('id_sector').style.display = 'none';
		document.getElementById('id_pob').style.display = 'none';
		document.getElementById('id_enfoque').style.display = 'none';	
		document.getElementById('ocurrenciasOrg').style.display='';
		
		getDataV1('','t/ajax_data.php?object=ocurrenciasOrgMapa&case='+document.getElementById('nom_sig_org').options[document.getElementById('nom_sig_org').selectedIndex].value+'&s='+texto+'&donde='+donde,'ocurrenciasOrg')
	}
}

function cerrarDivOcurrenciasOrg(){
	document.getElementById('id_tipo').style.display = '';
	document.getElementById('id_sector').style.display = '';
	document.getElementById('id_pob').style.display = '';
	document.getElementById('id_enfoque').style.display = '';

	document.getElementById('ocurrenciasOrg').style.display = 'none';
}

//Reporta de la opcion info al dar click en un mpio
function reporteOrg(id_depto,id_mun,caso,id){
	
	var url = 'id_depto[]=' + id_depto;
	url += '&id_muns[]=' + id_mun;
	url += '&' + caso + '[]=' + id;
	url += '&cobertura=on&sede=on';
	url += 'accion=consultar&todas=0&m_e=org&accion=consultar&class=OrganizacionDAO&submit=Consultar';
	
	opener.document.location.href='index.php?' + url;
	window.opener.focus();

}

<?
} 
if ($sidih == 1 || ($sidih == 0 && $case == 'evento_c')){ ?>

function mapaEventoC(){
	
	<? if ($sidih == 1){ ?>
		var reporte = getRadioCheck(document.getElementsByName('reporte_evento_c'));
		
		var id_cats = comboToString(document.getElementById('id_cat_evento_c'));
		
		var id_subcats = '';
		if (document.getElementById('id_subcat')){
			id_subcats = comboToString(document.getElementById('id_subcat'));
		}
		
		var f_ini = document.getElementById('f_ini').value;
		var f_fin = document.getElementById('f_fin').value;
		
		var id_depto_filtro = document.getElementById('id_depto_filtro').value;
		
		//Check de lo obligatorio
		msg = '';
		if (id_cats == '')	msg += "Seleccione alguna Categoria\n";
		if (f_ini == '' || f_fin == '')	msg += "Especifique el periodo\n";
		
		//Check del año del periodo inicial >= 2007
		if (f_ini != ''){
			var tmp = f_ini.split("-");
			
			if (1*tmp[0] < 2007){
				msg += 'Solo está disponible información desde 2007';
			}  
		}
		
		if (msg != ''){
			alert(msg);
			return false;
		}
	<?
	} 
	else { ?>
		var reporte = <?=$reporte ?>;
		var id_cats = <?=$id_cats ?>;
		var id_subcats = '<?=$id_subcats ?>';
		var f_ini = '<?=$f_ini ?>';
		var f_fin = '<?=$f_fin ?>';
		var id_depto_filtro = <?=$id_depto_filtro ?>;
		
	<? } ?>
	
	var map_extent = parseMapExtent(id_hidden);
	
	//Vacia el div del mapa por si tiene alguno
	document.getElementById('map_tag').innerHTML = '';
	
	//oculta div ini
	//document.getElementById('ini').style.display = 'none';
	document.getElementById('div_info').style.display = 'none';
	
	myMap = new msMap(document.getElementById('map_tag'),'standard');
	myMap.setCgi(server + 'sissh/consulta/mapserver_mpio.php');
	myMap.setArgs('case=evento_c&reporte='+reporte+'&id_cats='+id_cats+'&id_subcats='+id_subcats+'&f_ini='+f_ini+'&f_fin='+f_fin+'&id_depto_filtro='+id_depto_filtro);
	myMap.setFullExtent(map_extent[0],map_extent[1],map_extent[2],map_extent[3]); // (xmin,xmax,ymin,ymax)

	myMap2 = new msMap(document.getElementById('map_ref_tag'));
	myMap2.setArgs('case=evento_c&reporte='+reporte+'&id_cats='+id_cats+'&id_subcats='+id_subcats+'&f_ini='+f_ini+'&f_fin='+f_fin+'&id_depto_filtro='+id_depto_filtro+'&map_ref=1');
	myMap2.setCgi(server + 'sissh/consulta/mapserver_mpio.php');
	myMap2.setActionNone();
	myMap2.setFullExtent(map_extent[0],map_extent[1],map_extent[2],map_extent[3]); // (xmin,xmax,ymin,ymax)
	
	myMap.setReferenceMap(myMap2);
	
	if (debug_map == 1)	myMap.debug();
	myMap.redraw();
	myMap2.redraw();
	
	//Activa la opcion de mostrar map_ref
	document.getElementById('a_map_ref').style.display = '';
	document.getElementById('div_ino_map_ref').style.display = '';
	
	caso = 'evento_c';

}

function listarSubtipos(id_combo_cat){

	var id_cats = comboToString(document.getElementById(id_combo_cat));

	if (selected.length == 0){
		alert("Debe seleccionar alguna categoría");
	}
	else{
		getDataV1('comboBoxSubcategoria','t/ajax_data.php?object=comboBoxSubcategoria&separador=1&multiple=10&id_cat='+id_cats,'comboBoxSubcategoria')
	}
}
<? } ?>

function showHideMapRef(){
	var td_map_ref = document.getElementById('td_map_ref');
	link_a = document.getElementById('a_map_ref');
	
	if (td_map_ref.style.display == 'none'){
		td_map_ref.style.display = '';
		link_a.innerHTML = "[ Ocultar mapa de referencia ]";
	}
	else{
		td_map_ref.style.display = 'none';
		link_a.innerHTML = "[ Mostrar mapa de referencia ]";
	}
}

<?
if ($sidih == 1 || ($sidih == 0 && $case == 'proyecto_undaf')){ ?>

function mapaProyectoUndaf(){
	
	<? 
	if ($sidih == 1){ ?>
		var id_filtro = document.getElementById('id_filtro').value;
		var filtro = document.getElementById('filtro').value;
		var id_depto_filtro = document.getElementById('id_depto_filtro').value;

	<?
	}
	else{ ?>
		var id_filtro = <?=$id_filtro?>;
		var filtro = '<?=$filtro?>';
		var id_proy = '<?=$id_proy?>';
		var id_depto_filtro = '<?=$id_depto_filtro?>';
	<?	
	}
	?>
	
	var map_extent = parseMapExtent(id_hidden);
	
	//Vacia el div del mapa por si tiene alguno
	document.getElementById('map_tag').innerHTML = '';
	document.getElementById('map_ref_tag').innerHTML = '';
	
	//oculta div ini
	//document.getElementById('ini').style.display = 'none';
	document.getElementById('div_info').style.display = 'none';
	
	myMap = new msMap(document.getElementById('map_tag'),'standard');
	myMap.setCgi(server + 'sissh/consulta/mapserver_mpio.php');
	myMap.setArgs('case=proyecto_undaf&id_filtro='+id_filtro+'&filtro='+filtro+'&id_depto_filtro='+id_depto_filtro+'&id_proy='+id_proy);
	myMap.setFullExtent(map_extent[0],map_extent[1],map_extent[2],map_extent[3]); // (xmin,xmax,ymin,ymax)

	myMap2 = new msMap(document.getElementById('map_ref_tag'));
	myMap2.setCgi(server + 'sissh/consulta/mapserver_mpio.php');
	myMap2.setArgs('case=proyecto_undaf&id_filtro='+id_filtro+'&filtro='+filtro+'&id_depto_filtro='+id_depto_filtro+'&id_proy='+id_proy+'&map_ref=1');
	myMap2.setFullExtent(map_extent[0],map_extent[1],map_extent[2],map_extent[3]); // (xmin,xmax,ymin,ymax)
	
	myMap.setReferenceMap(myMap2);
	
	if (debug_map == 1)	myMap.debug();
	myMap.redraw();
	myMap2.redraw();
	
	//Activa la opcion de mostrar map_ref
	document.getElementById('a_map_ref').style.display = '';
	document.getElementById('div_ino_map_ref').style.display = '';
	
	caso = 'proyecto_undaf';
	
}
<?
}
?>

//Habilita los años de desplazamiento de acuerdo a la fuente seleccionada
function checkAAAADesplazamiento(id_fuente){
	
	<?
	$id_fuentes = $fuente_dao->GetAllArrayID('');
	echo "var aaaa = new Array(".count($id_fuentes).");";
	foreach ($id_fuentes as $id_f){
		echo "aaaa[$id_f] = new Array(".implode(",",$desplazamiento_dao->getAAAAByFuente($id_f)).");";
	}
	?>

	var chks_aaaa = document.getElementsByName('id_periodo_aaaa');

	for(var c=0;c<chks_aaaa.length;c++){
		var chk = chks_aaaa[c];
		chk.disabled = false;
		var esta = 0;
		<?
		echo "for(var i=0;i<aaaa[id_fuente].length;i++){";
		echo "if (chk.value == aaaa[id_fuente][i]){esta = 1;}";
		echo "}";
		echo "if (esta == 0){chk.disabled = true;}";
		?>
	}
}
</script>

</head>
<body <?=$onload ?>>
<!-- EXTENT PARA ENVIAR A MSCROSS DE TODO COLOMBIA-->
<input type="hidden" id="map_extent" value="-161112.1,1653895,-469146,1386463"> 
<!-- DIV PARA INFO AL CLICKEAR UN MPIO -->
<div id="div_info" style="position:absolute;background-color:#f1f1f1;border:1px solid #CCCCCC;width:220px;min-height:80px;height:auto;z-index:10;display:none;padding:5px;">
</div>

<!-- DIV PARA VER TODA LA INFO -->
<div id="div_all_info" class="div_all_info" style="left:<?=$left_all_info ?>px"></div>


<table border="0" width="<?=$width_page?>" cellpadding=0>
	<?
	if ($case == 'proyecto_undaf'){
		echo '<tr><td style="font-size:13px;padding:5px 0px 10px 5px;">Los proyectos incluidos en el conteo para la construcción del mapa, son aquellos que tienen cobertura regional y NO COBERTURA NACIONAL</td></tr>';
	}

	?>
	<tr>
		<!-- OPCIONES DE CONSULTA -->
		<? if ($sidih == 1){ ?>
		<td valign="top">
			<table border="0" cellpadding=0>
				<tr>
					<td valign='top'>
						<div style="position:relative;  width: 370px; height: 680px; border:0px solid #CCCCCC; overflow:auto">
							<div id="opciones_consulta"><!--Parent of the Accordion-->
								<!--<div align="center" class="titulo_filtro" style="padding:5px">Opciones de Consulta</div>-->
								
								<!--Inicio Desplazamiento-->
								<div>
								  <div class="panelheader"><img src="images/mscross/item.gif">&nbsp;&nbsp;DESPLAZAMIENTO</div>
								  <div class="panelContent" align="center"><!--DIV which show/hide on click of header-->
							    	<table cellpadding="0" cellspacing="2" width="330" align="center" border="0">
							    		<tr><td>&nbsp;</td></tr>
							    		<tr>
							    			<td><b>FUENTE</b></td>
							    			<td>
								    			<select id="id_fuente" name="id_fuente" class="select" style="width:180px" onchange="checkFuente(this.value);checkAAAADesplazamiento(this.value)">
													<option value="">[ Seleccione ]</option>
													<? $fuente_dao->ListarCombo('','',''); ?>
												</select>
							    			</td>
							    		</tr>
							    		<tr>
							    			<td colspan=2>
							    				<table>
							    					<tr>
							    						<td><img src="images/pwd.png"></td>
							    						<td>
								    						<?
															$fuentes = $fuente_dao->GetAllArray('');
															$f = 0;
															foreach ($fuentes as $fuente){
																$f_corte = $desplazamiento_dao->GetFechaCorte($fuente->id);
																$f_corte = split("-",$f_corte);
																$txt_fecha_corte = $f_corte[2]." ".$mes[$f_corte[1]*1]." ".$f_corte[0];
									
																echo "<b>Fecha de Corte</b> ".$fuente->nombre.": $txt_fecha_corte</br>";
																$f++;
															}
															?>
							    						</td>
							    					</tr>
							    				</table>
							    			</td>
							    		</tr>
							    		<tr><td class="td_dotted_top" colspan="2">&nbsp;</td></tr>
							    		<tr>
							    			<td><b>CLASE</b></td>
							    			<td>
								    			<select id="id_clase" name="id_clase" class="select" style="width:180px">
										    		<option value="">[ Seleccione ]</option>
													<? $clase_dao->ListarCombo('','',''); ?>
												</select>
							    			</td>
							    		</tr>
							    		<tr>
							    			<td colspan=2>
							    				<table>
							    					<tr>
							    						<td><img src="images/pwd.png"></td>
							    						<td>
															<b>Unidad de atenci&oacute;n</b> : Expulsi&oacute;n y Recepci&oacute;n<br>
															<b>CODHES</b> : Estimado LLegadas
							    						</td>
							    					</tr>
							    				</table>
							    			</td>
							    		</tr>
							    		<tr><td class="td_dotted_top" colspan="2">&nbsp;</td></tr>
							    		<tr>
							    			<td><b>TIPO</b><br>&nbsp;</td>
							    			<td>
							    				<input type="checkbox" id="id_tipo_1" name="id_tipo[]" value="1">&nbsp;Individual&nbsp;
							    				<input type="checkbox" id="id_tipo_2" name="id_tipo[]" value="2">&nbsp;Masivo&nbsp;
							    				<font class='nota_gris'>( Aplica para Acci&oacute;n Social )<br>&nbsp;</font>
							    			</td>
							    		</tr>
							    		<tr>
							    			<td class="td_dotted_top"><b>PERIODO</b></td>
							    			<td class="td_dotted_top" align='right'><a id="a_select_aaaa" href="#" onclick="selectAllCheckboxObj(document.getElementsByName('id_periodo_aaaa'))"><img src='images/mscross/todos.png' border=0>&nbsp;Todos</a></td>
							    		</tr>
							    		<tr>
							    			<td colspan='2'><span id="select_aaaa">
							    				<!--<select id="select_aaaa" name="id_periodo_aaaa" multiple size="8" class="select" style="width:110px">-->
							    				<?
												$date = getdate();
												$a_actual = $date["year"];
												$j = 1;
												for ($i=1997;$i<=$a_actual;$i++){
													echo "<input type='checkbox' value=$i name='id_periodo_aaaa' disabled>&nbsp;$i&nbsp;";
													if (fmod($j,7) == 0 && $i < $a_actual)	echo "<br><br>";
													$j++;
													//echo "<option value=$i>".$i."</option>";
												}
												?>
												</span>
												<!-- </select> -->
												<!--
												<select id="select_mes" name="id_periodo_mes" multiple size="8" class="select" style="width:110px;display:none">
													<? $periodo_dao->ListarCombo('','',"desc_perio REGEXP 'enero|febrero|marzo|abril|mayo|junio|julio|agosto|septiembre|octubre|noviembre|diciembre'"); ?>
												</select>&nbsp;<a id="a_select_mes" href="#" onclick="selectAll(document.getElementById('select_mes'))" style="display:none"><img src='images/mscross/todos.png' border=0>&nbsp;Todos</a>
												<select id="select_trim" name="id_periodo_trim" multiple size="8" class="select" style="width:110px;display:none">
													<? $periodo_dao->ListarCombo('','',"desc_perio REGEXP 'trim'"); ?>
												</select>&nbsp;<a id="a_select_trim" href="#" onclick="selectAll(document.getElementById('select_trim'))" style="display:none"><img src='images/mscross/todos.png' border=0>&nbsp;Todos</a>
												<select id="select_sem" name="id_periodo_sem" multiple size="8" class="select" style="width:120px;display:none">
													<? $periodo_dao->ListarCombo('','',"desc_perio REGEXP 'semestre'"); ?>
												</select>&nbsp;<a id="a_select_sem" href="#" onclick="selectAll(document.getElementById('select_sem'))" style="display:none"><img src='images/mscross/todos.png' border=0>&nbsp;Todos</a>
												 -->
												<br><br><font class='nota_gris'>Si selecciona todos, el tiempo de consulta aumentará proporcionalmente.</font>
							    			</td>
							    		</tr>
							    		<tr style="display:none">
							    			<td colspan="2">
							    				<input type="radio" name="t_periodo" value="aaaa" checked onclick="changeComboPeriodo('select_aaaa')">&nbsp;A&ntilde;os&nbsp;
							    				<input type="radio" name="t_periodo" value="mes" onclick="changeComboPeriodo('select_mes')">&nbsp;Meses&nbsp;
							    				<input type="radio" name="t_periodo" value="trim" onclick="changeComboPeriodo('select_trim')">&nbsp;Trimestres&nbsp;
							    				<input type="radio" name="t_periodo" value="sem" onclick="changeComboPeriodo('select_sem')">&nbsp;Semestres
							    			</td>
							    		</tr>
							    		
							    		<tr>
							    			<td colspan=2 class="td_dotted_top">
							    				<table>
							    					<tr>
							    						<td width=28><img src="images/mscross/variacion.png"></td>
							    						<td>Calcular Variaci&oacute;n&nbsp;<input type="radio" id="variacion" name='despla_tasa_variacion'></td>
							    					</tr>
							    					<tr>
							    						<td colspan=2>
							    							<font class='nota_gris'>Si marca esta opci&oacute;n es necesario que seleccione 2 periodos (deben estar dentro de las fechas de corte para que la incidencia sea completa)</font>
							    						</td>
							    					</tr>
							    				</table>
							    			</td>
							    		</tr>
							    		<tr>
							    			<td colspan=2 class="td_dotted_top">
							    				<table>
							    					<tr>
							    						<td width=28><img src="images/mscross/tasa.png"></td>
							    						<td>Calcular Tasa por 100.000 Habitantes&nbsp;<input type="radio" id="tasa" name='despla_tasa_variacion'></td>
							    					</tr>
							    					<tr>
							    						<td colspan=2>
							    							<font class='nota_gris'>Si marca esta opci&oacute;n es necesario que seleccione 1 periodo (Si desea comparar 2 periodos, el indicador mas relevante es la Variaci&oacute;n)</font>
							    						</td>
							    					</tr>
							    				</table>
							    			</td>
							    		</tr>
							    		<tr><td colspan="2" align="center"><br><input type="button" id="btn_generar_despla" value="Generar Mapa" onclick="mapaDesplazamiento();" class="boton"></td></tr>
							    	</table>
								  </div>
								</div>
								<!--Fin Desplazamiento -->
								<div> 
								  <div class="panelheader" ><img src="images/mscross/item.gif">&nbsp;&nbsp;DATOS SECTORIALES</div><!--Heading of the accordion ( clicked to show n hide ) -->
								    <div class="panelContent" align='center'>
								    	<table cellpadding="0" cellspacing="2" width="330" align="center" border="0">
								    		<tr><td>&nbsp;</td></tr>
								    		<tr>
								    			<td colspan="2">
									    			<select id="id_dato" name="id_dato" class="select" size="13" style="width:300px" onclick="if(this.value != -1){getAniosDato(this.value,'td_aaaa_dato');getDefinicionDato(this.value,'td_definicion_dato');}">
														<?
														$conn = MysqlDb::getInstance();
														$d = 0;
														$id_cate = 0;
														$sql = "SELECT m.ID_CATE, m.ID_DATO FROM minificha_datos_resumen as m, dato_sector as d WHERE m.id_dato = d.id_dato AND desagreg_geo = 'municipal' ORDER BY m.ID_CATE";
														$rs = $conn->OpenRecordset($sql);
														while ($row_rs = $conn->FetchRow($rs)){
															if ($id_cate != $row_rs[0]){
																$id_cate = $row_rs[0];
															}
													
															$id_datos_resumen[$id_cate][$d] = $row_rs[1];
															$d++;
														}
														
														foreach ($id_datos_resumen as $categoria => $datos_m){
															$cat = $cat_dao->Get($categoria);
															echo "<option value='-1' style='background:#CCCCCC;color:#FFFFFF;' disabled>----------- Categoria: $cat->nombre -----------</option>";
															foreach ($datos_m as $id_dato){
																$dato = $dato_sectorial_dao->Get($id_dato);
																echo "<option value=$dato->id>$dato->nombre</option>";
															}
														}
														/*$id_cats_dato = $cat_dao->GetAllArray('');
														foreach ($id_cats_dato as $cate){
															echo "<option value='' style='background:#CCCCCC;color:#FFFFFF;' disabled>----------- Categoria: $cate->nombre -----------</option>";
															$dato_sectorial_dao->ListarCombo('combo','','ID_CATE = '.$cate->id);
														}*/
														?>
													</select>
													<!-- Filtrar datos sectoriales 
													<script type="text/javascript">
													var myfilter = new filterlist(document.getElementById('id_dato'));
													</script>
													-->
								    			</td>
								    		</tr>
								    		<tr><td colspan="2"><b>DEFINICION</b></td></tr>
								    		<tr><td colspan="2"><p id="td_definicion_dato" class="nota_gris" style="width:300px;">Se consulta al seleccionar un Dato</p></td></tr>
								    		<!--<tr>
								    			<td>
													<A HREF="javascript:myfilter.reset()" TITLE="Clear the filter">Todos</A>&nbsp;|
													<A HREF="javascript:myfilter.set('^A')" TITLE="Show items starting with A">A</A>
													<A HREF="javascript:myfilter.set('^B')" TITLE="Show items starting with B">B</A>
													<A HREF="javascript:myfilter.set('^C')" TITLE="Show items starting with C">C</A>
													<A HREF="javascript:myfilter.set('^D')" TITLE="Show items starting with D">D</A>
													<A HREF="javascript:myfilter.set('^E')" TITLE="Show items starting with E">E</A>
													<A HREF="javascript:myfilter.set('^F')" TITLE="Show items starting with F">F</A>
													<A HREF="javascript:myfilter.set('^G')" TITLE="Show items starting with G">G</A>
													<A HREF="javascript:myfilter.set('^H')" TITLE="Show items starting with H">H</A>
													<A HREF="javascript:myfilter.set('^I')" TITLE="Show items starting with I">I</A>
													<A HREF="javascript:myfilter.set('^J')" TITLE="Show items starting with J">J</A>
													<A HREF="javascript:myfilter.set('^K')" TITLE="Show items starting with K">K</A>
													<A HREF="javascript:myfilter.set('^L')" TITLE="Show items starting with L">L</A>
													<A HREF="javascript:myfilter.set('^M')" TITLE="Show items starting with M">M</A>
													<A HREF="javascript:myfilter.set('^N')" TITLE="Show items starting with N">N</A>
													<A HREF="javascript:myfilter.set('^O')" TITLE="Show items starting with O">O</A>
													<A HREF="javascript:myfilter.set('^P')" TITLE="Show items starting with P">P</A>
													<A HREF="javascript:myfilter.set('^Q')" TITLE="Show items starting with Q">Q</A>
													<A HREF="javascript:myfilter.set('^R')" TITLE="Show items starting with R">R</A>
													<A HREF="javascript:myfilter.set('^S')" TITLE="Show items starting with S">S</A>
													<A HREF="javascript:myfilter.set('^T')" TITLE="Show items starting with T">T</A>
													<A HREF="javascript:myfilter.set('^U')" TITLE="Show items starting with U">U</A>
													<A HREF="javascript:myfilter.set('^V')" TITLE="Show items starting with V">V</A>
													<A HREF="javascript:myfilter.set('^W')" TITLE="Show items starting with W">W</A>
													<A HREF="javascript:myfilter.set('^X')" TITLE="Show items starting with X">X</A>
													<A HREF="javascript:myfilter.set('^Y')" TITLE="Show items starting with Y">Y</A>
													<A HREF="javascript:myfilter.set('^Z')" TITLE="Show items starting with Z">Z</A>
								    			</td>
								    		</tr>
								    		
								    		<tr>
								    			<td>
													Filtrar por expresi&oacute;n:&nbsp;
													<input id="regexp" name="regexp" onKeyUp="myfilter.set(this.value)" size="15">
													&nbsp;<a href="javascript:myfilter.reset()" TITLE="Clear the filter">Todos</a>	
								    			</td>
								    		</tr>-->
								    		<tr><td class="td_dotted_top" colspan="2">&nbsp;</td></tr>
								    		<tr>
												<td><b>PERIODOS DISPONIBLES</b><br>&nbsp;</td>
							    				<td align='right'><a id="a_select_aaaa" href="#" onclick="selectAllCheckboxObj(document.getElementsByName('aaaa_dato'))"><img src='images/mscross/todos.png' border=0>&nbsp;Todos</a></td>
											</tr>	
								    		<tr><td id='td_aaaa_dato' colspan="2"><font class='nota_gris'>Se consultan al seleccionar un Dato</font></td></tr>
									    	<tr>
								    			<td colspan=2 class="td_dotted_top">
								    				<table>
								    					<tr>
								    						<td width=28><img src="images/mscross/variacion.png"></td>
								    						<td>Calcular Variaci&oacute;n&nbsp;<input type="checkbox" id="variacion_dato"></td>
								    					</tr>
								    					<tr>
								    						<td colspan=2>
								    							<font class='nota_gris'>Si marca esta opci&oacute;n es necesario que seleccione 2 periodos (deben estar dentro de las fechas de corte para que la incidencia sea completa)</font>
								    						</td>
								    					</tr>
								    				</table>
								    			</td>
								    		</tr>
								    		<tr>
								    			<td colspan=2 class="td_dotted_top">
								    				<table>
								    					<tr>
								    						<td width=28><img src="images/mscross/tasa.png"></td>
								    						<td>Calcular Tasa por 100.000 Habitantes&nbsp;<input type="checkbox" id="tasa_dato"></td>
								    					</tr>
								    					<tr>
								    						<td colspan=2>
								    							<font class='nota_gris'>Si marca esta opci&oacute;n es necesario que seleccione 1 periodo (Si desea comparar 2 periodos, el indicador mas relevante es la Variaci&oacute;n)</font>
								    						</td>
								    					</tr>
								    				</table>
								    			</td>
								    		</tr>
								    		<tr><td colspan="2" align="center"><br><input type="button" value="Generar Mapa" onclick="mapaDatoSectorial();" class="boton"></td></tr>
								    	</table>
								    </div>
								  </div>
								<!--Fin Desplazamiento --> 
								
								<!--Inicio Orgs -->
								<div>
							    	<div class="panelheader"><img src="images/mscross/item.gif">&nbsp;&nbsp;ORGANIZACIONES</div>
							  		<div class="panelContent">
							    		<table cellpadding="0" cellspacing="2" width="330" align="center" border="0">
								    		<tr><td>&nbsp;</td></tr>
								    		<tr>
								    			<td>&nbsp;</td>
								    			<td colspan="2">
								    				1. Puede generar el mapa de sede y cobertura para una organización espec&iacute;fica, para esto
								    				digite el nombre o la sigla en el siguiente campo, el sistema mostrar&aacute; el listado de organizaciones, seleccione la
								    				que desee y genere el mapa.
								    			</td>
								    		</tr>
											<tr>
												<td>&nbsp;</td>
												<td colspan=2>
													<select id='nom_sig_org' class='select'>
														<option value='nombre'>Nombre</option>
														<option value='sigla'>Sigla</option>
													</select>&nbsp;&nbsp;
													<input type="text" id='s_org' name='s_org' class='textfield' style="width:240px" onkeydown="buscarOrgs(event);">
												</td>
											</tr>
											<tr>
												<td>&nbsp;</td>
												<td colspan=2 align="right">
													<input type='radio' id="comience" name="donde" value='comience' checked>&nbsp;Que <b>comience</b> con&nbsp;&nbsp;&nbsp;<input type='radio' id="contenga" name="donde" value='contenga'>&nbsp;Que <b>contenga</b> a
													&nbsp;&nbsp;&nbsp;&nbsp;
												</td>
											</tr>
	  										<tr><td colspan="2"><div id='ocurrenciasOrg' class='ocurrenciasOrgMapa' style="display:none"></div></td></tr>								    		
											<tr><td>&nbsp;</td></tr>
								    		<tr><td class="td_dotted_top" colspan="3">&nbsp;</td></tr>
								    		<tr>
								    			<td>&nbsp;</td>
								    			<td colspan="2">
								    				2. Puede generar el mapa de presencia de Organizaciones (conteo) aplicando alguno de los siguiente filtros.<br>
								    				Para obtener resultados claros, se ha restringido el uso de un filtro a la vez.
								    			</td>
								    		</tr>
								    		<tr><td>&nbsp;</td></tr>
								    		<tr>
								    			<td>&nbsp;</td>
								    			<td class="titulo_filtro_mapa">TIPO</td>
								    			<td>
									    			<select id="id_tipo" name="id_tipo" class="select" style="width:200px" onchange="checkFiltrosOrg('id_tipo')">
														<option value="">[ Seleccione ]</option>
														<? $tipo_org_dao->ListarCombo('','',''); ?>
													</select>
								    			</td>
								    		</tr>
								    		<tr><td>&nbsp;</td></tr>
								    		<tr>
								    			<td>&nbsp;</td>
								    			<td class="titulo_filtro_mapa">SECTOR</td>
								    			<td>
									    			<select id="id_sector" name="id_sector" class="select" style="width:200px" onchange="checkFiltrosOrg('id_sector')">
														<option value="">[ Seleccione ]</option>
														<? $sector_dao->ListarCombo('','',''); ?>
													</select>
								    			</td>
								    		</tr>
								    		<tr>
								    			<td>&nbsp;</td>
								    			<td class="nota_gris" colspan=3>Temas en los que trabaja la Organización</td>
								    		</tr>
								    		<tr><td>&nbsp;</td></tr>
								    		<tr>
								    			<td>&nbsp;</td>
								    			<td class="titulo_filtro_mapa">POBLACION</td>
								    			<td>
									    			<select id="id_pob" name="id_pob" class="select" style="width:200px" onchange="checkFiltrosOrg('id_pob')">
														<option value="">[ Seleccione ]</option>
														<? $pob_dao->ListarCombo('','',''); ?>
													</select>
								    			</td>
								    		</tr>
								    		<tr>
								    			<td>&nbsp;</td>
								    			<td class="nota_gris" colspan=3>Tipos de población en los que la Organización focaliza sus actividades</td>
								    		</tr>
								    		<tr><td>&nbsp;</td></tr>
								    		<tr>
								    			<td>&nbsp;</td>
								    			<td class="titulo_filtro_mapa">ENFOQUE</td>
								    			<td>
									    			<select id="id_enfoque" name="id_enfoque" class="select" style="width:200px" onchange="checkFiltrosOrg('id_enfoque')">
														<option value="">[ Seleccione ]</option>
														<? $enfoque_dao->ListarCombo('','',''); ?>
													</select>
								    			</td>
								    		</tr>
								    		<tr>
								    			<td>&nbsp;</td>
								    			<td class="nota_gris" colspan=3>Enfoque de las acciones de la Organización</td>
								    		</tr>
								    		<tr><td colspan="3" align="center"><br><br><input type="button" id="btn_generar_org" value="Generar Mapa" onclick="mapaOrg();" class="boton"></td></tr>
								    	</table>
							  		</div>
							  	</div>
								<!--Fin Orgs--> 								
								<!--Inicio Eventos C-->
								<div class="last_accordion">
									<div class="panelheader"><img src="images/mscross/item.gif">&nbsp;&nbsp;EVENTOS DEL CONFLICTO</div>
									<div class="panelContent">
								    	<table cellpadding="2" border="0">
								    		<tr>
								    			<td>
								    				SIDIH dispone de informaci&oacute;n de Eventos del Conflicto del 2007 en adelante, tenga encuenta esto
								    				para el periodo a consultar. A continuaci&oacute;n seleccione el tipo de mapa y los demas filtros que desee
								    			</td>
								    		</tr>
								    		<tr>
								    			<td>
								    				<input type="radio" name="reporte_evento_c" value="1" checked>&nbsp;Generar Mapa de n&uacute;mero de eventos<br>
								    				<input type="radio" name="reporte_evento_c" value="2">&nbsp;Generar Mapa de n&uacute;mero de v&iacute;ctimas
								    			</td>
								    		</tr>
								    		<tr><td>&nbsp;</td></tr>
								    		<tr><td class="titulo_filtro_mapa"><b>PERIODO</b></td></tr>
											<tr>
								    			<td>
								    				<font class='nota_gris'>
								    					Es obligatorio especificar el periodo de tiempo de los eventos que desea <br>mostrar en el mapa.
								    					Click sobre el reloj para ver el calendario
								    				</font>
								    			</td>
								    		</tr>
											<tr>								    		
												<td>
													Desde: <input type="text" id="f_ini" class="textfield" style="width:90px">
													<a href="#" onclick="displayCalendar(document.getElementById('f_ini'),'yyyy-mm-dd',this);return false;"><img src="images/mscross/calendar.png" border="0"></a>
													&nbsp;Hasta: <input type="text" id="f_fin" class="textfield" style="width:90px">
													<a href="#" onclick="displayCalendar(document.getElementById('f_fin'),'yyyy-mm-dd',this);return false;"><img src="images/mscross/calendar.png" border="0"></a>
												</td>
											</tr>
											<tr><td class="titulo_filtro_mapa"><b>CATEGORIA</b></td></tr>
								    		<tr>
								    			<td>
								    				<font class='nota_gris'>
								    					Puede generar el mapa de Eventos del Conflicto para una o varias <br>Categorias, o si 
								    					desea puede refinar el mapa seleccionado sub categorias.
								    				</font>
								    			</td>
								    		</tr>
											<tr>
												<td>
													<select id='id_cat_evento_c' multiple size="6" class="select">
														<? $cat_eve_dao->ListarCombo('combo',1,''); ?>
													</select><br><br>
													<span id="link_a_subcat"><img src="images/mscross/mostrar_combo.png">&nbsp;<a href="#" onclick="listarSubtipos('id_cat_evento_c');return false;">Listar Subcategor&iacute;a</a></span>
												</td>
											</tr>
											<tr>	
												<td class="titulo_filtro_mapa"><b>SUBCATEGORIA</b></td>
											</tr>
											<tr>
												<td id="comboBoxSubcategoria" valign="top">
													Seleccione alguna categoria y use la opción Listar<br><br>
												</td>
											</tr>
											<tr><td colspan="2" align="center"><br><input type="button" id="btn_generar_despla" value="Generar Mapa" onclick="mapaEventoC();" class="boton"></td></tr>
										</table>
								    </div>
								</div>
								<!--Fin Eventos C-->								
							</div>
						</div>
					</td>
				</tr>
			</table>
		</td>
		<? } ?>
		<td valign="top">
			<table cellpadding=0>
				<tr>
					<td>
						<!-- MAPA -->
						<div id="map_tag" style="width: 500px; height: 510px; border:1px solid #CCCCCC; z-index:1; background: #FFFFFF;">
							<!-- IMAGEN INICIAL -->
							<div id="ini" >
								<img src="images/consulta/home_mapa.gif">
							</div>
						</div>
						<? if ($sidih == 1){ ?>
						<div style="background-color:#D9D9D9">
							<table cellpadding=0 border=0>
								<tr style="font-size:12px">
									<td><img src="images/mscross/important.png"></td>
									<td>
										<b>Filtrar por Departamento</b>
										<select id='id_depto_filtro' class='select' onchange="setExtentByDepto(this.value,'map_extent');">
											<option value=0>Todo Colombia</option>
											<? $depto_dao->ListarCombo('combo','',"id_depto <> '00'"); ?>
										</select>
									</td>
									<td>&nbsp;&nbsp;&nbsp;</td>
									<td align='right'>
										<a href="#" id="a_map_ref" onclick="showHideMapRef()" style="display:none">[ Mostrar mapa de referencia ]</a>
									</td>
								</tr>
							</table>
						</div>
						<div align='right' id="div_ino_map_ref" style="display:none">
							<table cellspacing=0 cellpadding=0 border=0 width="500">
								<tr>
									<td><img src="images/flecha.gif"><img src="images/flecha.gif">&nbsp;<font class='nota_gris'><a href='#' onclick="window.open('mapa_print.php?caso='+caso,'','top=0,left=0,width=900,height=700,scrollbars=1');">Versi&oacute;n para impresi&oacute;n (alta resoluci&oacute;n)</a></font></td>
									<td align='right'>&nbsp;<img src="images/flecha.gif">&nbsp;<font class='nota_gris'>Ver el mapa de referencia es &uacute;til cuando use alg&uacute;n Zoom</font></td>
								</tr>
							</table>
						</div>
						<? }
						else{ ?>
							<div  align='right' style="background-color:#D9D9D9">
								<table cellpadding="0" border="0">
									<tr style="font-size:12px">
										<td align='right'>
											<a href="#" id="a_map_ref" onclick="showHideMapRef()" style="display:none">[ Mostrar mapa de referencia ]</a>
										</td>
									</tr>
								</table>
							</div>
							<div align='right' id="div_ino_map_ref" style="display:none">
								<table cellspacing=0 cellpadding=0 border=0 width="500">
									<tr>
										<td align='right'>&nbsp;<img src="images/flecha.gif">&nbsp;<font class='nota_gris'>Ver el mapa de referencia es &uacute;til cuando use alg&uacute;n Zoom</font></td>
									</tr>
								</table>
							</div>
						<?	
						}
						?>
						
						<div>
							<table cellpadding=0>
								<tr>
									<td>
										<!-- MAPA DE REFERENCIA -->
										<div id="td_map_ref" style="display:none;border:1px solid #CCCCCC;z-index:10;position:absolute;top:228px;left:<?=$left_map_ref?>px">
											<div id="map_ref_tag" style="width:280px; height:286px"></div>
										</div>
									</td>
								</tr>
							</table>
						</div>
					</td>
					<td valign="top">
						<table class="opciones_map" cellspacing="2" border=0>
							<tr>
								<td>
									<table>
										<tr>
											<td valign="top"><img src="images/mscross/icn_alpha_button_fullExtent.png"></td>
											<td><b>Mapa Completo</b></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table>
										<tr>
											<td valign="top"><img src="images/mscross/icn_alpha_button_pan.png"></td>
											<td><b>Paneo</b><br>Arrastre el mapa con el mouse</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table>
										<tr>
											<td valign="top"><img src="images/mscross/icn_alpha_button_zoombox.png"></td>
											<td><b>Zoom Area</b><br>Acercar area</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table>
										<tr>
											<td valign="top"><img src="images/mscross/icn_alpha_button_zoomIn.png"></td>
											<td><b>Zoom In</b><br>Acercar</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table>
										<tr>
											<td valign="top"><img src="images/mscross/icn_alpha_button_zoomOut.png"></td>
											<td><b>Zoom Out</b><br>Alejar</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table>
										<tr>
											<td valign="top"><img src="images/mscross/icn_alpha_button_identify.png"></td>
											<td><b>Valores</b><br>Click sobre un Municipio para obtener informaci&oacute;n</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table>
										<tr>
											<td valign="top"><img src="images/mscross/all_info.png"></td>
											<td><b>Info Completa</b><br>Consulte toda la información relacionada</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table>
										<tr>
											<td valign="top"><img src="images/mscross/icn_save.png"></td>
											<td><b>Guardar</b><br>Descargue el mapa generado</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table>
										<tr>
											<td valign="top"><img src="images/mscross/ooo-calc.png"></td>
											<td><b>Exportar</b><br>Descargar la informaci&oacute;n para hoja de calculo </td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</body>
</html>
