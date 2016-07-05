<?php
session_start();

//SEGURIDAD
if (count($_GET) == 0){
	if(!isset($_SESSION["id_usuario_s"])){
		header("Location: login_unicef.php?m_g=home");
	}
}
// LIBRERIAS
include('admin/lib/common/mysqldb.class.php');
include('admin/lib/dao/factory.class.php');

// INICIALIZACION
$proy_eje = $_GET['proy_eje'];
$unicef_dao = FactoryDAO::factory('unicef');
$filtros['filtro'] = $_GET['filtro'];
$filtros['id_filtro'] = $_GET['id_filtro'];

if ($proy_eje == 'proyectado'){
    $filtros['aaaa'] = $_GET['aaaa'];
    $unicef_dao->reporteProyectado('que',$filtros);
}
else{
    $filtros['fecha_inicio_ini'] = (isset($_GET['fecha_inicio_ini'])) ? $_GET['fecha_inicio_ini'] : '';
    $filtros['fecha_inicio_fin'] = (isset($_GET['fecha_inicio_fin'])) ? $_GET['fecha_inicio_fin'] : '';
    $filtros['fecha_finalizacion_ini'] = (isset($_GET['fecha_finalizacion_ini'])) ? $_GET['fecha_finalizacion_ini'] : '';
    $filtros['fecha_finalizacion_fin'] = (isset($_GET['fecha_finalizacion_fin'])) ? $_GET['fecha_finalizacion_fin'] : '';
    $unicef_dao->reporteEjecutado('que',$filtros);
}
?>
