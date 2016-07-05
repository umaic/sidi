<?
session_start();

$case = $_GET["case"];
$nom = $_GET["nombre_archivo"];

if (isset($_GET['pdf']) && $_GET['pdf'] == 1){
	header("Content-Type: application/pdf");
	header("Content-Disposition: attachment; filename=\"".$nom.".pdf\"");
}
else{
	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=\"".$nom.".xls\"");
}

switch ($case){
	case 'xls_session':
		echo $_SESSION["xls"];
		break;
	
	//Muestra el codigo pdf que este en sesion
	case 'pdf_session':
		echo $_SESSION["pdf"];
		break;

}
?>
