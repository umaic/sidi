<?
//INICIALIZACION DE VARIABLES
$sub_dao = new UnicefSubComponenteDAO();
$resultado_dao = New UnicefResultadoDAO();
$resultado_vo = New UnicefResultado();
$indicador_dao = new UnicefIndicadorDAO();
$periodo_dao = new UnicefPeriodoDAO();

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

$id_sub_componente = (isset($_GET["id_papa"])) ? $_GET["id_papa"] : 0;
$sub_componente = $sub_dao->Get($id_sub_componente);
$id_papa = $sub_componente->id_componente;

//Caso de Actualizacion
if ($accion == 'actualizar'){
	$id = $_GET["id"];
}


$random_num = rand();

?>
<iframe src="t/unicef_resultado/iframe.php?accion=<?=$accion?>&id=<?=$id?>&id_papa=<?=$id_sub_componente?>&rand=<?=$random_num?>" style="width:860px;height:950px" frameborder="0" ></iframe> 
