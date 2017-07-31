<?
include ("consulta/lib/libs_evento_c.php");

$conn = MysqlDb::getInstance();
$s_cat_dao = new SubCatEventoConflictoDAO();
$evento_c_dao = new EventoConflictoDAO();
$archivo = new Archivo();

//Consulta las sub categorias con eventos
$sql = "SELECT DISTINCT(id_scateven) FROM descripcion_evento JOIN subcat_even USING (id_scateven) ORDER BY nom_scateven";
$rs = $conn->OpenRecordset($sql);
while ($row = $conn->FetchRow($rs)){
	$s_c_combo[] = $s_cat_dao->Get($row[0]);
}

$id_s_c = (isset($_GET["id_s_c"])) ? $_GET["id_s_c"] : 1;

//Consulta los años en los que existen eventos
$sql = "SELECT DISTINCT(YEAR(fecha_reg_even)) as a FROM evento_c JOIN descripcion_evento USING(id_even) WHERE id_scateven = $id_s_c ORDER BY a DESC";
$rs = $conn->OpenRecordset($sql);
while ($row = $conn->FetchRow($rs)){
	$anios[] = $row[0];
}
$aaaa = (isset($_GET["aaaa"])) ? $_GET["aaaa"] : $anios[0];


//Consulta los meses en los que existen eventos
$sql = "SELECT DISTINCT(MONTH(fecha_reg_even)) AS a FROM evento_c JOIN descripcion_evento USING(id_even) WHERE id_scateven = $id_s_c AND YEAR(fecha_reg_even) = $aaaa ORDER BY a";
$rs = $conn->OpenRecordset($sql);
while ($row = $conn->FetchRow($rs)){
	$meses[] = $row[0];
}
$mes = (isset($_GET["m"])) ? $_GET["m"] : $meses[count($meses)-1];


//Consulta el primer dia del mes del año en el que existen eventos
$sql = "SELECT DAY(fecha_reg_even) AS d FROM evento_c JOIN descripcion_evento USING(id_even) WHERE id_scateven = $id_s_c AND YEAR(fecha_reg_even) = $aaaa AND MONTH(fecha_reg_even) = $mes ORDER BY d";
$rs = $conn->OpenRecordset($sql);
$row = $conn->FetchRow($rs);
$dia_inicial_band = $row[0];

//Consulta el numero de eventos
$sql = "SELECT COUNT(id_even) FROM evento_c JOIN descripcion_evento USING(id_even) WHERE id_scateven = $id_s_c AND YEAR(fecha_reg_even) = $aaaa AND MONTH(fecha_reg_even) = $mes";
$rs = $conn->OpenRecordset($sql);
$row = $conn->FetchRow($rs);
$num_eventos = $row[0];

$mes_corto = array('','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
$mes_largo = array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

$fecha_ini_band = $mes_corto[$mes].' '.$dia_inicial_band.' '.$aaaa.' 00:00:00 GMT';

$file_xml = 'consulta/timeline_xml/'.$id_s_c.'_'.$mes.'_'.$aaaa.'.xml';

//Si el mes coincide con el mes actual, debe generar el xml para ese mes, con el objetivo de manerlo actualizado
if (!$archivo->Existe($_SERVER["DOCUMENT_ROOT"]."/sissh/$file_xml") || ($mes == date('m') && date('j',$archivo->fechaModificacion($_SERVER["DOCUMENT_ROOT"]."/sissh/$file_xml")) < date('j') ) ){
	$evento_c_dao->genXmlTimeLine($id_s_c,$mes,$aaaa);
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
 <html>
   <head>
     <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />

    <script>
      Timeline_ajax_url="js/timeline/timeline_ajax/simile-ajax-api.js";
      Timeline_urlPrefix='js/timeline/timeline_js/';       
      Timeline_parameters='bundle=true';
    </script>
     <script src="js/timeline/timeline-api.js" type="text/javascript"></script>
	 <script type="text/javascript">
	 var tl;
 function onLoad() {
   var eventSource = new Timeline.DefaultEventSource();
   var bandInfos = [
     Timeline.createBandInfo({
         eventSource:    eventSource,
         date:           "<?=$fecha_ini_band?>",
         width:          "90%", 
         intervalUnit:   Timeline.DateTime.DAY, 
         intervalPixels: 200
     }),
     Timeline.createBandInfo({
         date:           "Feb 1 2007 00:00:00 GMT",
         width:          "10%", 
         intervalUnit:   Timeline.DateTime.MONTH, 
         intervalPixels: 300
     })
   ];
   bandInfos[1].syncWith = 0;
   bandInfos[1].highlight = true;
   
   tl = Timeline.create(document.getElementById("div_timeline"), bandInfos);
   Timeline.loadXML("<?=$file_xml?>", function(xml, url) { eventSource.loadXML(xml, url); });
 }

  var resizeTimerID = null;
 function onResize() {
     if (resizeTimerID == null) {
         resizeTimerID = window.setTimeout(function() {
             resizeTimerID = null;
             tl.layout();
         }, 500);
     }
 }

 function aplicarFiltro(caso){
	var combo_s_c = document.getElementById('id_s_c'); 
	var id_s_c = combo_s_c.options[combo_s_c.selectedIndex].value;

	var combo_m = document.getElementById('mes'); 
	var m = combo_m.options[combo_m.selectedIndex].value;
	
	var combo_a = document.getElementById('aaaa'); 
	var aaaa = combo_a.options[combo_a.selectedIndex].value;
	
	var url = 'timeline.php?id_s_c='+id_s_c;
	if (caso != 'id_s')	url += '&m='+m+'&aaaa='+aaaa;

	location.href = url;
 }
</script>

	<link rel='stylesheet' href='style/consulta.css' type='text/css' />
	<link rel='stylesheet' href='style/timeline.css' type='text/css' />
</head>
<body onLoad="onLoad()" onResize="onResize()">
	<div id="#top">
		<h2>SIDI - LINEA DE TIEMPO EVENTOS DEL CONFLICTO</h2>
	</div>
	<div>
	   Tipo Evento&nbsp;<select id="id_s_c" onchange="aplicarFiltro('id_s')" class="select">
		<?
		foreach ($s_c_combo as $s_c){
			echo "<option value=$s_c->id";
			if ($s_c->id == $id_s_c)	echo " selected ";
			echo ">$s_c->nombre</option>";
		}
		?>
	    </select>&nbsp;
	    Mes &nbsp;<select class="select" id="mes" onchange="aplicarFiltro('m')">
		<? 
		foreach ($meses as $i){
			echo "<option value='$i'";
			if ($mes == $i)	echo " selected ";
			echo ">$mes_largo[$i]</option>";
			}
		?>
	   </select>
	   A&ntilde;o &nbsp;<select class="select" id="aaaa" onchange="aplicarFiltro('aaaa')">
		<?
		foreach($anios as $i){
			echo "<option value='$i'";
			if ($aaaa == $i)	echo " selected ";
			echo ">$i</option>";
		}
		?>
	   </select>&nbsp; <? echo "$num_eventos Evento(s)"?>
	</div><br /> Desplace la l&iacute;nea de tiempo arrastr&aacute;ndola o con el scroll del mouse
   <div id="div_timeline"></div>
	<noscript>
	This page uses Javascript to show you a Timeline. Please enable Javascript in your browser to see the full page. Thank you.
	</noscript>
   </body>
 </html>
