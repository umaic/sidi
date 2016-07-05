<?
//INICIALIZACION DE VARIABLES
$producto_dao = New UnicefProductoCpapDAO();
$producto_vo = New UnicefProductoCpap();
$periodo_dao = new UnicefPeriodoDAO();
$resultado_dao = new UnicefResultadoDAO();

$periodo = $periodo_dao->GetAllArray('activo=1');
$aaaa_ini = $periodo[0]->aaaa_ini;
$aaaa_fin = $periodo[0]->aaaa_fin;
$id = '';

$accion = '';
if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

$id_papa = (isset($_GET["id_papa"])) ? $_GET["id_papa"] : 0;
//$id_papa = $resultado_dao->GetFieldValue($id_resultado,'id_sub_componente');

//Caso de Actualizacion
if ($accion == 'actualizar'){
	$id = $_GET["id"];
}

$random_num = rand();

?>
<iframe src="t/unicef_producto_cpap/iframe.php?accion=<?=$accion?>&id=<?=$id?>&id_papa=<?=$id_papa?>&rand=<?=$random_num?>" style="width:860px;height:1350px" frameborder="0" ></iframe> 
