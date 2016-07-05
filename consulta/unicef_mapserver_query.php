<?
session_start();

//Si viene de la opcion showAllInfo, solo muestra el contenido de la sesion
if (isset($_GET["show_all_info"])){
	die("<table align='right'><tr><td align='right' style='background-color:#f1f1f1'><a href='#' onclick=\"document.getElementById('div_all_info').style.display='none'\">Cerrar</a>&nbsp;&nbsp;</td></tr></table><br>".$_SESSION["html_info_mapserver"]);
}

include("../admin/lib/common/mysqldb.class.php");	
include("../admin/lib/common/postgresdb.class.php");	
include("../admin/lib/common/mapserver.class.php");
include("../admin/lib/common/cadena.class.php");
include("../admin/lib/dao/factory.class.php");

//INICIALIZACION DE VARIABLES
$pgConn = New PgDBConn(); 
$myConn = MysqlDb::getInstance(); 
$actividad_dao = FactoryDAO::factory('unicef_actividad_awp');
$meses = array ("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
$depto_dao = FactoryDAO::factory('depto');
$mpio_dao = FactoryDAO::factory('municipio');
$mdgd = $_GET["mdgd"];
$id_filtro = $_GET["id_filtro"];
$filtro = $_GET["filtro"];
$proy_eje = $_GET['proy_eje'];
$socio_dao = FactoryDAO::factory('unicef_socio');
$donante_dao = FactoryDAO::factory('unicef_donante');


//Point
$x = $_GET["x_real"];
$y = $_GET["y_real"];

$tabla_pg = 'depto';
//$col_pg = 'departamen';
if ($mdgd == 'mpal'){
    $tabla_pg = 'mpio';
    //$col_pg = 'municipio';
}

$sql = "SELECT codane2 FROM $tabla_pg WHERE st_dwithin(the_geom, 'POINT($x $y)',5)";
$rs = $pgConn->OpenRecordset($sql);

if ($pgConn->RowCount($rs) > 0){

	$row = $pgConn->FetchRow($rs);
	
	$id_depto_mpio = $row[0];
	//$nombre = $row[1];
	
    if ($mdgd == 'mpal'){
        $vo_depto_mpio = $mpio_dao->Get($id_depto_mpio);
        $id_depto = $mun_vo->id_depto;
        $municipio = $mpio_dao->Get($id_depto_mpio);
        $nombre = $municipio->nombre;
    }
    else{
        $vo_depto_mpio = $depto_dao->Get($id_depto_mpio);
        $depto = $depto_dao->Get($id_depto_mpio);
        $nombre = $depto->nombre;
    }

if ($filtro == 'comps'){
    $cond = ' id_componente IN ('.$id_filtro.')';
}

// Socios
else if (strpos($filtro,'socio') !== false){
    $tmp = explode('-',$id_filtro);
    $id_socio = $tmp[1];
    $socio = $socio_dao->Get($id_socio);
    $nombre .= '<br /> '.$socio->nombre;
    $cond = ' id_componente IN ('.$tmp[0].') AND id_socio = '.$id_socio;
}

// Donantes
else if (strpos($filtro,'donante') !== false){
    $tmp = explode('-',$id_filtro);
    $id_donante = $tmp[1];
    $donante = $donante_dao->Get($id_donante);
    $nombre .= '<br /> '.$donante->nombre;
    $cond = ' id_componente IN ('.$tmp[0].') AND id_donante = '.$id_donante;
}

// Filtro fecha
if ($proy_eje == 'proyectado'){
    $class_name = 'actividad_awp';
    $cond_fecha_a_awp = ' act.aaaa = '.$_GET['aaaa'];  // act nombre en el sql de actividad_dao->getIdByCobertura
    $cond_fecha_p_awp = ' aaaa = '.$_GET['aaaa'];  
}
else{
    $class_name = 'convenio';
    
    $cond_fecha = '';
    if (isset($_GET['fecha_inicio_fin']) && $_GET['fecha_inicio_fin'] != '' && isset($_GET['fecha_inicio_ini']) && $_GET['fecha_inicio_ini'] != ''){
        $cond_fecha .= ' AND fecha_ini BETWEEN '.$_GET['fecha_inicio_ini'].' AND '.$_GET['fecha_inicio_fin'];
    }
    if (isset($_GET['fecha_finalizacion_fin']) && $_GET['fecha_finalizacion_fin'] != '' && isset($_GET['fecha_finalizacion_ini']) && $_GET['fecha_finalizacion_ini'] != ''){
        $cond_fecha .= ' AND fecha_ini BETWEEN '.$_GET['fecha_finalizacion_ini'].' AND '.$_GET['fecha_finalizacion_fin'];
    }
}

$dao = FactoryDAO::factory('unicef_'.$class_name);


    if ($proy_eje == 'proyectado'){
        
        // Condicion para A. AWP y P. AWP
        $condicion_a_awp = "$cond AND $cond_fecha_a_awp";
        $condicion_p_awp = $cond_fecha_p_awp;

        $ids = $dao->getIdByCobertura($mdgd,$id_depto_mpio,$condicion_a_awp);
        $ids_nal = $dao->getIdByCobertura('nal',0,$condicion_a_awp);
        $valor = count($ids) + count($ids_nal);

        // Genera codigo para descarga hoja de calculo
        $dao->reporteInfoMapa($nombre,$valor,$ids,$ids_nal,$mdgd,$id_depto_mpio,$condicion_p_awp);

        $unidad_html = "Proyecto(s)";
        $c_pdf = 6; // Parametro para /download_pdf.php
    }
    else{
        
        $unicef_dao = FactoryDAO::factory('unicef');
        $cond_conv = $cond;
        
        if ($cond_fecha != '' ) $cond_conv .= "AND $cond_fecha";
        
        $ids = $dao->getIdByCobertura($mdgd,$id_depto_mpio,$cond_conv);
        $ids_nal = $dao->getIdByCobertura('nal',0,$cond_conv);
        $valor = count($ids) + count($ids_nal);

        // Genera codigo para descarga hoja de calculo
        //$convenio_dao->reporteInfoMapa($nombre,$valor,$ids,$ids_nal,$mdgd,$id_depto_mpio,$condicion_p_awp);
        $filtros['filtro'] = $_GET['filtro'];
        $filtros['id_filtro'] = $_GET['id_filtro'];
        $filtros['mapa'] = 1; // Para no mostrar todas las opciones del reporte
        $filtros['mdgd'] = $mdgd;
        $filtros['id_depto_mpio'] = $id_depto_mpio;
        $filtros['fecha_inicio_ini'] = (isset($_GET['fecha_inicio_ini'])) ? $_GET['fecha_inicio_ini'] : '';
        $filtros['fecha_inicio_fin'] = (isset($_GET['fecha_inicio_ini'])) ? $_GET['fecha_inicio_ini'] : '';
        $filtros['fecha_finalizacion_ini'] = (isset($_GET['fecha_finalizacion_ini'])) ? $_GET['fecha_finalizacion_ini'] : '';
        $filtros['fecha_finalizacion_fin'] = (isset($_GET['fecha_finalizacion_ini'])) ? $_GET['fecha_finalizacion_ini'] : '';

        // Genera codigo para descarga hoja de calculo
        $unicef_dao->reporteEjecutado('que',$filtros);

        $unidad_html = "Convenio(s)";
        $c_pdf = 7; // Parametro para /download_pdf.php
    }

    $class_titulo = "titulo_query_12";
		
}
else{
	$titulo_html = "Error!";
	$nombre = "Lugar NO v&aacute;lido, por favor haga click sobre alg&uacute;n municipio.";
	$valor = "";
	$unidad_html = "";
}

?>
<div align="right" style="top:5px;right:5px;position:absolute;font-size:16px"><a href='#' onclick="document.getElementById('div_info').style.display='none'">Cerrar</a></div>
<div><b><?=$nombre?><br /><?=$valor?> <?=$unidad_html?></b><br /><a href="unicef_export_data.php?case=xls_session&nombre_archivo=Listado_<?=$nombre?>" target="_blank">Descargar Listado</a></div>
<br />
<div style="height:350px;overflow:auto;padding:0">
<table cellspacig="1" cellpadding="5" width="430">
    <?php 
    foreach ($ids as $id){
        $nombre = $dao->GetFieldValue($id,'nombre');
        $codigo = $dao->GetFieldValue($id,'codigo');
        echo "<tr><td><img src='images/unicef/pdf.gif'>&nbsp;<a href='download_pdf.php?c=$c_pdf&id=$id' target='_blank'>Ficha</a>&nbsp;$codigo&nbsp;$nombre</td></tr>";
    }
    
    foreach ($ids_nal as $id){
        $nombre = $dao->GetFieldValue($id,'nombre');
        $codigo = $dao->GetFieldValue($id,'codigo');
        echo "<tr><td>*** Nal&nbsp;<img src='images/unicef/pdf.gif'>&nbsp;<a href='download_pdf.php?c=$c_pdf&id=$id' target='_blank'>Ficha</a>&nbsp;$codigo&nbsp;$nombre</td></tr>";

    }
    ?>
</table>
</div>
