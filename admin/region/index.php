<?
//SEGURIDAD
include_once("../seguridad.php");

//LIBRERIAS
include_once("../lib/libs_region.php");

//ACCION DE LA FORMA
if (isset($_POST["submit"])){
  //Controlador
  $ct = New ControladorPagina($_POST["accion"]);
}

if (isset($_GET["accion"]) && $_GET["accion"] == "borrar"){
  //Controlador
  $ct = New ControladorPagina("borrar");
}

//Inicialización de Variables
$accion = "Insertar";
$municipio_vo = New Municipio();
$municipio_dao = New MunicipioDAO();
$depto_vo = New Depto();
$depto_dao = New DeptoDAO();
$region_vo = New Region();
$region_dao = New RegionDAO();

?>

<html>
<head>
<title>OCHA - SISSH</title>
<link href="../../naz.css" rel="stylesheet" type="text/css">
<link href="../style/style.css" rel="stylesheet" type="text/css">
<script src="../js/general.js"></script>
</head>

<body>
<table width="100%" border="1" align="center" cellpadding="0" cellspacing="0" class="tabla_lista_reg">
  <tr class="titulo_lista_reg">
	  <td>Nombre</td>
		<td>Municipios</td>
		<td>Borrar</td>
	</tr>
	
	<?
	//REGIONES
	$arr = $region_dao->GetAllArray('');
	$num_arr = count($arr);
	for($p=0;$p<$num_arr;$p++){
	  $region_vo = $arr[$p];
		$num_mun = count($region_vo->id_muns);
		
	  echo "<tr>";
		echo "<td>".$region_vo->nombre."</td>";
		echo "<td>";
		for($m=0;$m<$num_mun;$m++){
		  echo "- ".$region_vo->nom_muns[$m]."<br>";
		}
		echo "</td>";

		echo "<td><a href='index.php?accion=borrar&id=".$region_vo->id."'>Borrar</a></td>";
		echo "</tr>";
		
	}
	?>
</table>
</body>
</html>
