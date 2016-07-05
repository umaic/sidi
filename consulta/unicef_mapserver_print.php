<?
//LIBRERIAS

//INICIALIZACION DE VARIABLES
$mes = array("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
$mdgd = $_GET["mdgd"];
$caso = $_GET["caso"];
?>

<html>
<head>
<title></title>
<link href="style/consulta_unicef.css" rel="stylesheet" type="text/css" />
<script src="js/unicef_mscross-1.1.9.js" type="text/javascript"></script>
<script src="js/mapserver.js" type="text/javascript"></script>
<script>
var server = "http://<?=$_SERVER["SERVER_NAME"] ?>/";
var myMap;
var id_hidden = 'map_extent';   //Input hidden con el valor del extent

function mapaProyecto(){
	
    var id_filtro = '';
    var filtro = '';
    var mdgd = '<?php echo $mdgd; ?>';
    
    <?php 
    if ($caso != 'donde'){
    ?>
        var id_filtro = document.getElementById('id_filtro').value;
        var filtro = document.getElementById('filtro').value;
    <?php } ?> 
    
    var id_depto_filtro = opener.document.getElementById('id_depto_filtro').value;
	
	var map_extent = parseMapExtentPrint(id_hidden);
	
	myMap = new msMap(document.getElementById('map_tag'),'standard');
	myMap.setCgi(server + 'sissh/consulta/unicef_mapserver_mpio.php');
	myMap.setArgs('case=donde&id_filtro='+id_filtro+'&filtro='+filtro+'&id_depto_filtro='+id_depto_filtro+'&mdgd='+mdgd+'&print=1');
	myMap.setFullExtent(map_extent[0],map_extent[1],map_extent[2],map_extent[3]); // (xmin,xmax,ymin,ymax)

	myMap.redraw();

    return myMap;
	
}

function init(caso){
	if (caso == 'donde'){
		myMap = mapaProyecto();
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
