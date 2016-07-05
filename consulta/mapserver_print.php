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

$mes = array("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
?>

<html>
<head>
<title>Sistema de Informaci&oacute;n Central OCHA - Colombia</title>
<link href="style/consulta.css" rel="stylesheet" type="text/css" />
<script src="js/mscross-1.1.9.js" type="text/javascript"></script>
<script src="js/mapserver.js" type="text/javascript"></script>
<script src="admin/js/general.js" type="text/javascript"></script>
<script src="admin/js/ajax.js" type="text/javascript"></script>
<script>
var server = "http://<?=$_SERVER["SERVER_NAME"] ?>/";
var myMap;
var id_hidden = 'map_extent';   //Input hidden con el valor del extent
function mapaDesplazamiento(){
	
	id_tipo_periodo = 'aaaa';
	var id_periodo = getOptionsCheckBox(opener.document.getElementsByName('id_periodo_'+id_tipo_periodo));
	var id_fuente = opener.document.getElementById('id_fuente').value;
	var id_clase = opener.document.getElementById('id_clase').value;
	
	if (id_fuente == 2){
		var id_tipo = getOptionsCheckBox(opener.document.getElementsByName('id_tipo[]'));
	}
	else{
		var id_tipo = 2;
	}
	var id_depto_filtro = opener.document.getElementById('id_depto_filtro').value;
	
	var variacion = 0;
	if (opener.document.getElementById('variacion').checked == true)	variacion = 1;

	var tasa = 0;
	if (opener.document.getElementById('tasa').checked == true)	tasa = 1;
	
	var map_extent = parseMapExtentPrint(id_hidden);
	
	myMap = new msMap(document.getElementById('map_tag'),'');
	myMap.setCgi(server + 'sissh/consulta/mapserver_mpio.php');
	myMap.setArgs('case=desplazamiento&id_fuente='+id_fuente+'&id_clase='+id_clase+'&id_tipo='+id_tipo+'&id_periodo='+id_periodo+'&id_tipo_periodo='+id_tipo_periodo+'&id_depto_filtro='+id_depto_filtro+'&variacion='+variacion+'&tasa='+tasa+'&print=1');
	myMap.setFullExtent(map_extent[0],map_extent[1],map_extent[2],map_extent[3]); // (xmin,xmax,ymin,ymax)
	
	myMap.redraw();
	
	return myMap;
	
}
function mapaDatoSectorial(){
	
	var id_dato = opener.document.getElementById('id_dato').value;
	var id_depto_filtro = opener.document.getElementById('id_depto_filtro').value;
	var aaaa = getOptionsCheckBox(opener.document.getElementsByName('aaaa_dato'));
	
	var variacion = 0;
	if (opener.document.getElementById('variacion_dato').checked == true)	variacion = 1;

	var tasa = 0;
	if (opener.document.getElementById('tasa_dato').checked == true)	tasa = 1;
	
	var map_extent = parseMapExtentPrint(id_hidden);
	
	//Vacia el div del mapa por si tiene alguno
	document.getElementById('map_tag').innerHTML = '';
	
	//oculta div ini
	//document.getElementById('ini').style.display = 'none';
	//document.getElementById('div_info').style.display = 'none';
	
	myMap = new msMap(document.getElementById('map_tag'),'');
	myMap.setCgi(server + 'sissh/consulta/mapserver_mpio.php');
	myMap.setArgs('case=dato_sectorial&id_dato='+id_dato+'&id_depto_filtro='+id_depto_filtro+'&aaaa='+aaaa+'&variacion='+variacion+'&tasa='+tasa+'&print=1');
	myMap.setFullExtent(map_extent[0],map_extent[1],map_extent[2],map_extent[3]); // (xmin,xmax,ymin,ymax)

	myMap.redraw();
	
	return myMap;
	
}

function mapaOrg(){
	
	var id_tipo = opener.document.getElementById('id_tipo').value;
	var id_sector = opener.document.getElementById('id_sector').value;
	var id_pob = opener.document.getElementById('id_pob').value;
	var id_enfoque = opener.document.getElementById('id_enfoque').value;
	var id_depto_filtro = opener.document.getElementById('id_depto_filtro').value;
	
	id_org = getRadioCheck(opener.document.getElementsByName('id_org'));
	if (id_org == undefined)	id_org = '';
		
	var map_extent = parseMapExtentPrint(id_hidden);
	
	//Vacia el div del mapa por si tiene alguno
	document.getElementById('map_tag').innerHTML = '';
	
	myMap = new msMap(document.getElementById('map_tag'),'');
	myMap.setCgi(server + 'sissh/consulta/mapserver_mpio.php');
	myMap.setArgs('case=org&id_tipo='+id_tipo+'&id_sector='+id_sector+'&id_pob='+id_pob+'&id_enfoque='+id_enfoque+'&id_depto_filtro='+id_depto_filtro+'&id_org='+id_org+'&print=1');
	myMap.setFullExtent(map_extent[0],map_extent[1],map_extent[2],map_extent[3]); // (xmin,xmax,ymin,ymax)

	myMap.redraw();
	//myMap.debug();
	
	return myMap;
	
}

function mapaEventoC(){
	
	var reporte = getRadioCheck(opener.document.getElementsByName('reporte_evento_c'));
	
	var id_cats = comboToString(opener.document.getElementById('id_cat_evento_c'));
	
	var id_subcats = '';
	if (opener.document.getElementById('id_subcat')){
		id_subcats = comboToString(opener.document.getElementById('id_subcat'));
	}
	
	var f_ini = opener.document.getElementById('f_ini').value;
	var f_fin = opener.document.getElementById('f_fin').value;
	
	var id_depto_filtro = opener.document.getElementById('id_depto_filtro').value;
	
	var map_extent = parseMapExtentPrint(id_hidden);
	
	//Vacia el div del mapa por si tiene alguno
	document.getElementById('map_tag').innerHTML = '';
	
	myMap = new msMap(document.getElementById('map_tag'),'');
	myMap.setCgi(server + 'sissh/consulta/mapserver_mpio.php');
	myMap.setArgs('case=evento_c&reporte='+reporte+'&id_cats='+id_cats+'&id_subcats='+id_subcats+'&f_ini='+f_ini+'&f_fin='+f_fin+'&id_depto_filtro='+id_depto_filtro+'&print=1');
	myMap.setFullExtent(map_extent[0],map_extent[1],map_extent[2],map_extent[3]); // (xmin,xmax,ymin,ymax)

	myMap.redraw();
	
	return myMap;

}

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
function getAniosDato(id_dato,innerobject){
	
	document.getElementById(innerobject).style.display = '';
	
	getDataV1('','admin/ajax_data.php?object=getAniosDatoSectorialToMapa&id_dato='+id_dato,innerobject);
}
function getDefinicionDato(id_dato,innerobject){
	
	document.getElementById(innerobject).style.display = '';
	
	getDataV1('','admin/ajax_data.php?object=getDefinicionDatoSectorial&id_dato='+id_dato,innerobject);
}
function init(caso){
	if (caso == 'desplazamiento'){
		myMap = mapaDesplazamiento();
	}
	else if (caso == 'dato_sectorial'){
		myMap = mapaDatoSectorial();
	}
	else if (caso == 'org'){
		myMap = mapaOrg();
	}
	else if (caso == 'evento_c'){
		myMap = mapaEventoC();
	}
}
</script>

</head>
<body onload="init('<?=$_GET["caso"] ?>')">
<!-- EXTENT PARA ENVIAR A MSCROSS DE TODO COLOMBIA-->
<input type="hidden" id="map_extent" value="-161112.1,1653895,-469146"> 
<table border="0" width="740">
	<tr>
		<td>
			<!-- MAPA -->
			<div style="overflow:auto;width:770px;height:600px">
				<div id="map_tag" style="width:1654px; height:2338px; border:1px solid #CCCCCC; z-index:1"></div>
			</div>
			<div style="background-color:#D9D9D9">
				<table cellpadding=3 border=0 width=610>
					<tr><td style="font-size:11px">Use las barras de desplazamiento para ver el mapa.</td></tr>
					<tr style="font-size:12px;display:none">
						<td align='right'>
							<a href="#" id="a_map_ref" onclick="showHideMapRef()" style="display:none">[ Mostrar mapa de referencia ]</a>
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
								<td valign="top"><img src="images/mscross/icn_save.png"></td>
								<td><b><a href='#' onclick="myMap.download();">Guardar</a></b><br>Descargue el mapa generado</td>
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
