<?
if (!$_GET['filtro'])  die;

//LIBRERIAS
include("admin/lib/dao/factory.class.php");
include("admin/lib/common/mysqldb.class.php");	

//INICIALIZACION DE VARIABLES
$mes = array("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
$depto_dao = FactoryDAO::factory('depto');

//Define si viene del website
$left_map_ini = 0;
$left_all_info = 10;
$left_map_ref = 180;
$width_page = 700;	
$onload = '';
$id_depto_filtro = isset($_GET["id_depto_filtro"]) ? $_GET["id_depto_filtro"] : 0;
$proy_eje = $_GET['proy_eje'];

$param_fecha = '';
if ($proy_eje == 'proyectado'){
    $aaaa = isset($_GET["aaaa"]) ? $_GET["aaaa"] : die('Falta parámetro de aaaa en proyectado');
    $param_fecha .= "&aaaa=$aaaa";
}
else{
    $fecha_inicio_ini = isset($_GET["fecha_inicio_ini"]) ? $_GET["fecha_inicio_ini"] : '';
    $fecha_inicio_fin = isset($_GET["fecha_inicio_fin"]) ? $_GET["fecha_inicio_fin"] : '';
    $fecha_finalizacion_ini = isset($_GET["fecha_finalizacion_ini"]) ? $_GET["fecha_finalizacion_ini"] : '';
    $fecha_finalizacion_fin = isset($_GET["fecha_finalizacion_fin"]) ? $_GET["fecha_finalizacion_fin"] : '';

    if ($fecha_inicio_ini != '' && $fecha_inicio_fin != ''){
        $param_fecha .= "&fecha_inicio_ini=$fecha_inicio_ini&fecha_inicio_fin=$fecha_inicio_fin";
    }
    
    if ($fecha_finalizacion_ini != '' && $fecha_finalizacion_fin != ''){
        $param_fecha .= "&fecha_finalizacion_ini=$fecha_finalizacion_ini&fecha_finalizacion_fin=$fecha_finalizacion_fin";
    }

}

$id_filtro = $_GET['id_filtro'];
$filtro = $_GET['filtro'];
$mdgd = $_GET['mdgd'];

$onload = ($id_depto_filtro > 0) ? "setExtentByDepto('$id_depto_filtro',id_hidden);setTimeout('mapaProyecto()',500);" : "setTimeout('mapaProyecto()',500);";

?>

<html>
<head>
<title></title>

<link href="style/consulta_unicef.css" rel="stylesheet" type="text/css" />
<script src="js/unicef_mscross-1.1.9.js" type="text/javascript"></script>
<script src="js/mapserver.js" type="text/javascript"></script>
<script src="t/js/ajax.js" type="text/javascript"></script>

<script type="text/javascript">
var server = "http://<?=$_SERVER["SERVER_NAME"] ?>/";
var debug_map = 1;
var extent_org = '-161112.1,1653895,-469146,1386463';
var id_hidden = 'map_extent';   //Input hidden con el valor del extent
var mdgd = '<?php echo $mdgd; ?>';
var proy_eje = '<?php echo $proy_eje; ?>';
var param_fecha = '<?php echo $param_fecha; ?>';

function mapaProyecto(){
	
    var id_filtro = '<?php echo $id_filtro; ?>';
    var filtro = '<?php echo $filtro; ?>';
    var id_depto_filtro = document.getElementById('id_depto_filtro').value;
	
	var map_extent = parseMapExtent(id_hidden);
	
	//Vacia el div del mapa por si tiene alguno
	document.getElementById('map_tag').innerHTML = '';
	document.getElementById('map_ref_tag').innerHTML = '';
	
	//oculta div ini
	//document.getElementById('ini').style.display = 'none';
	document.getElementById('div_info').style.display = 'none';

    var url = 'id_filtro='+id_filtro+'&filtro='+filtro+'&id_depto_filtro='+id_depto_filtro+'&mdgd='+mdgd+param_fecha+'&proy_eje='+proy_eje;
	
	myMap = new msMap(document.getElementById('map_tag'),'standard');
	myMap.setCgi(server + 'sissh/consulta/unicef_mapserver_mpio.php');
	myMap.setArgs(url);
	myMap.setFullExtent(map_extent[0],map_extent[1],map_extent[2],map_extent[3]); // (xmin,xmax,ymin,ymax)

	myMap2 = new msMap(document.getElementById('map_ref_tag'));
	myMap2.setCgi(server + 'sissh/consulta/unicef_mapserver_mpio.php');
	myMap2.setArgs(url+'&map_ref=1');
	myMap2.setFullExtent(map_extent[0],map_extent[1],map_extent[2],map_extent[3]); // (xmin,xmax,ymin,ymax)
	
	myMap.setReferenceMap(myMap2);
	
	if (debug_map == 1)	myMap.debug();
	myMap.redraw();
	myMap2.redraw();
	
	//Activa la opcion de mostrar map_ref
	document.getElementById('a_map_ref').style.display = '';
	document.getElementById('div_ino_map_ref').style.display = '';
	
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
</script>
</head>

<body onload="<?=$onload ?>">
<!-- EXTENT PARA ENVIAR A MSCROSS DE TODO COLOMBIA-->
<input type="hidden" id="map_extent" value="-161112.1,1653895,-469146,1386463"> 
<!-- DIV PARA INFO AL CLICKEAR UN MPIO -->
<div id="div_info" class="div_all_info"></div>
<!-- DIV PARA VER TODA LA INFO -->
<div id="div_all_info" class="div_all_info"></div>
<table border="0" cellpadding="3" cellspacing="0" border="1">
    <tr>
        <td>
            <!-- MAPA -->
            <div id="map_tag" style="width: 500px; height: 510px; border:1px solid #CCCCCC; z-index:1; background: #FFFFFF;">
                <!-- IMAGEN INICIAL -->
                <div id="ini" >
                    <img src="images/consulta/home_mapa.gif">
                </div>
            </div>
            <div style="background-color:#D9D9D9">
                <table cellpadding="0" border="0">
                    <tr style="font-size:12px">
                        <?php
                        if ($mdgd == 'mpal'){ ?>
                            <td><img src="images/mscross/important.png"></td>
                            <td>
                                <b>Filtrar por Departamento</b>
                                <!-- setExtentByDeptoUnicef definida en mapserver.js -->
                                <select id='id_depto_filtro' class='select' onchange="setExtentByDeptoUnicef(this.value,'map_extent');">
                                    <option value=0>Todo Colombia</option>
                                    <? $depto_dao->ListarCombo('combo','',"id_depto <> '00'"); ?>
                                </select>
                            </td>
                            <td>&nbsp;&nbsp;&nbsp;</td>
                        <?php
                        }
                        else { echo "<input type='hidden' id='id_depto_filtro' value=0>"; }
                        ?>
                        <td align='right'>
                            <a href="#" id="a_map_ref" onclick="showHideMapRef()" style="display:none">[ Mostrar mapa de referencia ]</a>
                        </td>
                    </tr>
                </table>
            </div>
            <div align='right' id="div_ino_map_ref" style="display:none">
                <table cellspacing=0 cellpadding=0 border=0 width="500">
                    <tr>
                        <td><img src="images/flecha.gif"><img src="images/flecha.gif">&nbsp;<font class='nota_gris'><a href='#' onclick="window.open('unicef_mapa_print.php?caso='+caso+'&mdgd='+mdgd,'','top=0,left=0,width=900,height=700,scrollbars=1');">Versi&oacute;n para impresi&oacute;n (alta resoluci&oacute;n)</a></font></td>
                    </tr>
                </table>
            </div>
            <div>
                <table cellpadding="0">
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
            <table class="opciones_map" cellspacing="2">
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
</body>
</html>
