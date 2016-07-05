<?
include_once ('lib/libs_municipio.php');

//INICIALIZACION DE VARIABLES

$mpio_dao = New MunicipioDAO();

if (isset($_GET["id_depto"])  && $_GET["id_depto"] != ''){
	$depto_dao = New DeptoDAO();
	
	$id_depto = $_GET["id_depto"];
	$depto = $depto_dao->Get($id_depto);
	echo ": <b>Departamento</b>: ".$depto->nombre;
}

if (isset($_GET["id_mpio"]) && $_GET["id_mpio"] != ''){
	$mpio_dao = New MunicipioDAO();
	
	$id_mpio = $_GET["id_mpio"];
	$mpio = $mpio_dao->Get($id_mpio);
	echo " -> <b>Municipio</b>: ".$mpio->nombre;
}

?>