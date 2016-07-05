<?
include_once ('lib/libs_municipio.php');

//INICIALIZACION DE VARIABLES
$mpio_dao = New MunicipioDAO();

if (isset($_POST["id_depto"])){
	$id_depto = $_POST["id_depto"];
	$mpios = $mpio_dao->GetAllArray("id_depto = ".$id_depto."");
	
	$m=0;
	foreach ($mpios as $mpio){
		echo "&nom_mpio_".$m."=".$mpio->nombre."&id_mpio_".$m."=".$mpio->id;
		$m++;
	}
	
	echo "&n=".$m;

}

?>