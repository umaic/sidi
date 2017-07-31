<?php 
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) die('l');

$id = $_GET['id'];
$w_dir = "rp_s/$id/";

// Idioma
$idioma = (isset($_GET['i']) && in_array($_GET['i'],array('esp','ing'))) ? $_GET['i'] : 'esp';

$link_idioma = 'Spanish';
$i_link = 'esp';
if ($idioma == 'esp'){
    $link_idioma = 'Ingl&eacute;s';
    $i_link = 'ing';
}

$a_titulo = "titulo_$idioma.htm";
$a_destacado = "destacado_$idioma.htm";
$a_contenido = "contenido_$idioma.htm";
$a_draw_charts = "draw_charts_$idioma.htm";

$labels['esp']['boletin'] = 'BOLETIN HUMANITARIO';
$labels['ing']['boletin'] = 'HUMANITARIAN BULLETIN';
$labels['esp']['destacados'] = 'Destacados';
$labels['ing']['destacados'] = 'Highlights';
$labels['esp']['contacto'] = 'Contacto';
$labels['ing']['contacto'] = 'Contact';
$labels['esp'][''] = '';
$labels['ing'][''] = '';
$labels['esp'][''] = '';
$labels['ing'][''] = '';
$labels['esp'][''] = '';
$labels['ing'][''] = '';
$labels['esp'][''] = '';
$labels['ing'][''] = '';

$labels_i = $labels[$idioma];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="style/rp_s.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="style/openlayers/theme/default/style.css" type="text/css" />
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/openlayers.js"></script>
<script type="text/javascript" src="js/sidih_openlayers.js"></script>
<script type="text/javascript">
    function init(){

        OpenLayers.ImgPath = "images/openlayers/";
        var extent		= new OpenLayers.Bounds(53818,-469146,1411170,1379510);

         map = new OpenLayers.Map('map',
        {
            maxExtent: extent,
            restrictedExtent: extent,
            maxResolution: 'auto',
            units:"dd",
            projection: new OpenLayers.Projection("EPSG:4326"),
            numZoomLevels: zoom,
            controls: [new OpenLayers.Control.Navigation({'zoomWheelEnabled': false}) ]
        }
        );

        layer = new OpenLayers.Layer.WMS( "Departamentos", "/cgi-bin/col",
        {	layers: 'depto', layername: 'depto', format: 'image/png', request: 'getmap', }, { visibility: true, isBaseLayer: true });
                
        map.addLayer(layer);

        layer = new OpenLayers.Layer.WMS( "Municipios", "/cgi-bin/col",
        {	layers: 'mpio', format: 'image/png', request: 'getmap', }, { visibility: false});
                
        map.addLayer(layer);
        
        
        var style  =new OpenLayers.Style({
            pointRadius: "${radius}", // sized according to type attribute
            fillColor: "${color}",
            fillOpacity: "0.35",
            strokeColor: "${color}",
            strokeWidth: 1,
            fontColor: "#FFFFFF",
            fontSize: "10px",
            fontWeight: "bold"
        });

        var Styles = new OpenLayers.StyleMap({
                "default": style,
                "select": style
                });

        var num_cats_mapa = 6;
        var cat_mapa = ['Acciones Belicas','Ataques a poblacion civil','Ataque a objetivos ilicitos de guerra','APM/UXO/IEA Victimas','Desplazamiento','Categorias Complementarias'];
        var json_file;
        $.each(cat_mapa, function (i, cat_nombre){
            json_file = '<?php echo $w_dir ?>map_json_cat_' + i + '.htm';
            var TmpLayer = new OpenLayers.Layer.Vector( cat_nombre, {styleMap: Styles, format: OpenLayers.Format.GeoJSON}); 	
            map.addLayer(TmpLayer);

            selectControl = new OpenLayers.Control.SelectFeature(TmpLayer,
                { hover: true, onSelect: onFeatureSelect, onUnselect: onFeatureUnselect });

            map.addControl(selectControl);
            selectControl.activate();

            $.getJSON(json_file, function(json){
                var geojson = new OpenLayers.Format.GeoJSON( { 'internalProjection': map.baseLayer.projection, 'externalProjection':  map.baseLayer.projection});

                var features =  geojson.read(json);
                $('.olControlLoadingPanel:first').css({width: '0px', height: '0px', display: 'none'});
                TmpLayer.addFeatures(features);
            });

        });

        map.setCenter(extent.getCenterLonLat(), 3);
        var switcher =  new OpenLayers.Control.LayerSwitcher();
        map.addControl(switcher);
        switcher.maximizeControl();
    }

      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawCharts);
      function drawCharts() {
          var data_dep = new google.visualization.DataTable();
          var data_cat = new google.visualization.DataTable();
          var data_trend = new google.visualization.DataTable();

          <?php include($w_dir.$a_draw_charts) ?>
    }
</script>
</head>
<body onload="init()">
<div id="logo"></div>
<div id="titulo">
    <h1><?php echo $labels_i['boletin'] ?></h1>
    <h2><?php include($w_dir.$a_titulo); ?> | COLOMBIA</h2>
</div>
<div id="idioma"><?php echo "<a href='rp_s.php?id=$id&i=$i_link'>$link_idioma</a>"; ?></div>
<div id="clear"></div>
<div id="destacado"><h1><?php echo $labels_i['destacados'] ?></h1><?php include($w_dir.$a_destacado); ?></div>
<br />
<div id="map" class="smallmap"></div><div id="legend"></div>
<div id="contenido"><?php include($w_dir.$a_contenido); ?></div>
<div id="footer">
    <?php echo $labels_i['contacto']; ?>: UMAIC Colombia | Carrera 13 #93-12 Oficina 402 | Pbx: 57 + 1 6221100 umaic.org
</div>
</body>
