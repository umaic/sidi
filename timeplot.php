<?
include ("consulta/lib/libs_evento_c.php");

$conn = MysqlDb::getInstance();
$s_cat_dao = new SubCatEventoConflictoDAO();
$archivo = new Archivo();
$evento_c_dao = new EventoConflictoDAO();

$id_s_c = (isset($_GET["id_s_c"])) ? $_GET["id_s_c"] : 1;

//Consulta los años en los que existen eventos
$sql = "SELECT DISTINCT(YEAR(fecha_reg_even)) as a FROM evento_c JOIN descripcion_evento USING(id_even) WHERE id_scateven = $id_s_c ORDER BY a DESC";
$rs = $conn->OpenRecordset($sql);
while ($row = $conn->FetchRow($rs)){
	$anios[] = $row[0];
}
$aaaa = (isset($_GET["aaaa"])) ? $_GET["aaaa"] : $anios[0];

//Consulta las sub categorias con eventos
$sql = "SELECT DISTINCT(id_scateven) FROM descripcion_evento JOIN subcat_even USING (id_scateven) ORDER BY nom_scateven";
$rs = $conn->OpenRecordset($sql);
while ($row = $conn->FetchRow($rs)){
	$s_c_combo[] = $s_cat_dao->Get($row[0]);
}

//Consulta el maximo de eventos para el año, para colocar el rango en la gráfica
$sql = "SELECT COUNT(id_even) as num FROM evento_c JOIN descripcion_evento USING(id_even) WHERE id_scateven = $id_s_c 
AND YEAR(fecha_reg_even) = $aaaa GROUP BY (fecha_reg_even) ORDER BY num DESC";
$rs = $conn->OpenRecordset($sql);
$row = $conn->FetchRow($rs);
$max_eventos = $row[0];

$digits   = strlen(round($max_eventos));
$interval = pow(10, ($digits-1));

$max_eventos = ceil($max_eventos/$interval)*$interval + ($interval);

$file_txt = 'consulta/timeplot_txt/'.$id_s_c.'_'.$aaaa.'.txt';
$file_xml_eventos = "consulta/timeplot_txt/eventos_$aaaa.xml";
$load_xml_eventos = file_exists($file_xml_eventos) ? 1 : 0;

//Si el mes coincide con el mes actual, debe generar el xml para ese mes, con el objetivo de manerlo actualizado
if (!$archivo->Existe($_SERVER["DOCUMENT_ROOT"]."/sissh/$file_txt") || ($aaaa == date('Y') && date('j',$archivo->fechaModificacion($_SERVER["DOCUMENT_ROOT"]."/sissh/$file_txt")) < date('j') ) ){
	$evento_c_dao->genTxtTimePlot($id_s_c,$aaaa);
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
 <html>
   <head>
     <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
     <script src="http://api.simile-widgets.org/timeplot/1.1/timeplot-api.js" type="text/javascript"></script>
	 <script type="text/javascript">
	 function onLoad(){
		 var eventSource = new Timeplot.DefaultEventSource(); 
		 var eventSource_Eventos = new Timeplot.DefaultEventSource();
		 var red = new Timeplot.Color('#B9121B');

		 var timeGeometry = new Timeplot.DefaultTimeGeometry({
				gridColor: new Timeplot.Color("#000000"),
                axisLabelsPlacement: "top"
            });


		var plotInfo = [
			Timeplot.createPlotInfo({
			  id: "plot1",
			  dataSource: new Timeplot.ColumnSource(eventSource,1),
			  valueGeometry: new Timeplot.DefaultValueGeometry({
				gridColor: "#000000",
				min: 0,
				max: <?=$max_eventos?>
			  }),
			  timeGeometry: timeGeometry,
			  
			  lineColor: "#0099ff",
			  //dotColor: "#0099ff",
			  //fillColor: "#0066ff",
			  showValues: true
			}),
			Timeplot.createPlotInfo({
                    id: "Eventos",
                    eventSource: eventSource_Eventos,
                    timeGeometry: timeGeometry,
                    lineColor: red
                })

		  ];

		   timeplot = Timeplot.create(document.getElementById("div_timeplot"), plotInfo);
		   timeplot.loadText("<?=$file_txt?>", ",", eventSource);
		   <?
		   if ($load_xml_eventos == 1)
		   	echo 'timeplot.loadXML("'.$file_xml_eventos.'", eventSource_Eventos);';
			?>

	 }


 function aplicarFiltro(caso){
	var combo_s_c = document.getElementById('id_s_c'); 
	var id_s_c = combo_s_c.options[combo_s_c.selectedIndex].value;

	var combo_a = document.getElementById('aaaa'); 
	var aaaa = combo_a.options[combo_a.selectedIndex].value;
	
	var url = 'timeplot.php?id_s_c='+id_s_c;
	if (caso == 'aaaa')	url += '&aaaa='+aaaa;

	location.href = url;
 }
</script>

	<link rel='stylesheet' href='style/consulta.css' type='text/css' />
	<link rel='stylesheet' href='style/timeplot.css' type='text/css' />
</head>
<body onLoad="onLoad()" onResize="onResize()">
	<div id="#top">
		<h2>SIDIH -  GRAFICA DE TIEMPO EVENTOS DEL CONFLICTO</h2>
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
	   A&ntilde;o &nbsp;<select class="select" id="aaaa" onchange="aplicarFiltro('aaaa')">
		<?
		foreach($anios as $i){
			echo "<option value='$i'";
			if ($aaaa == $i)	echo " selected ";
			echo ">$i</option>";
		}
		?>
	   </select>
	</div><br />Click sobre las lineas o zonas rojas (si existen) para obtener detalle de eventos importantes
   <div id="div_timeplot"></div>
	<noscript>
	This page uses Javascript to show you a Timeline. Please enable Javascript in your browser to see the full page. Thank you.
	</noscript>
   </body>
 </html>
