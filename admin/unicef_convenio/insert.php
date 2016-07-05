<?
//INICIALIZACION DE VARIABLES
$convenio_dao = New UnicefConvenioDAO();
$convenio_vo = New UnicefConvenio();
$funcionario_dao = new UnicefFuncionarioDAO();
$fuente_dao = new UnicefFuentePbaDAO();
$estado_dao = new UnicefEstadoDAO();
$socio_dao = new UnicefSocioDAO();
$donante_dao = new UnicefDonanteDAO();
$actividad_dao = new UnicefActividadAwpDAO();
$depto_dao = new DeptoDAO();
$municipio_dao = new MunicipioDAO();

// Se define en /admin/unicef_proyecto/get_nodes.php
$id_node_papa = $_SESSION['id_node_papa_click'];
$id = '';

$accion = '';
if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

$id_actividad = (isset($_GET["id_papa"])) ? $_GET["id_papa"] : 0;
$id_papa = $id_actividad;
//$id_papa = $actividad_dao->GetFieldValue($id_actividad,'id_actividad');

//Caso de Actualizacion
if ($accion == 'actualizar'){
	$id = $_GET["id"];
}

$random_num = rand();

?>
<iframe src="t/unicef_convenio/iframe.php?accion=<?=$accion?>&id=<?=$id?>&id_papa=<?=$id_papa?>&rand=<?=$random_num?>" style="width:860px;height:2600px" frameborder="0" ></iframe> 
