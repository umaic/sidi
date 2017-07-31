<?
include_once("../admin/lib/common/cadena.class.php");
include_once("../admin/lib/common/mysqldb.class.php");
include_once("../admin/lib/dao/dato_sectorial.class.php");
include_once("../admin/lib/dao/municipio.class.php");
include_once("../admin/lib/dao/depto.class.php");
include_once("../admin/lib/model/municipio.class.php");
include_once("../admin/lib/model/depto.class.php");
include_once("../admin/lib/model/dato_sectorial.class.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>SIDI UMAIC - Colombia</title>
<link href="../style/consulta.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="cont">

<table align="center" cellpadding="0" cellspacing="10" border="0">
	<tr><td>Totalizando Valores Nacionales...</td></tr>
	<tr><td id='total_nal'>--</td></tr>
	<tr><td>Totalizando Valores Departamentales...</td></tr>
	<tr><td id='total_deptal'>--</td></tr>
</table>
</div>

</body>
</html>

<?
$dato_dao = New DatoSectorialDAO();

if (isset($_GET['id_dato'])) {
	// Nacional
	$dato_dao->totalizarUnDato(true, $_GET['id_dato']);

	// Departamental
	//$dato_dao->totalizarUnDato(false, $_GET['id_dato']);
} else {
	$dato_dao->Totalizar();
	$dato_dao->TotalizarDepto();
}


?>
