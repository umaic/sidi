<?php
session_start();

//LIBRERIAS
require_once $_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/dao/factory.class.php";
require_once $_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/common/mysqldb.class.php";

if (empty($_GET["mod"])) {
	die('Faltan parametros!');
}

ini_set('max_execution_time', 0);
ini_set('memory_limit','512M');

$mod = $_GET["mod"];
$render = isset($_GET["render"]) ? $_GET["render"] : 'csv';
$conn = MysqlDb::getInstance();
$render_json = true;

if (!isset($_GET['_c']) || $_GET['_c'] != 's1dicol-api' || !in_array($mod, array('desplazamiento','totales'))) {
	die('¬¬');
}

function utf8ing(&$item, $key) {
	$item = utf8_encode($item);
}

function nf($num) {
	return number_format($num,0,'.',',');
}

switch ($mod){

	case 'desplazamiento':


		$sql = "
SELECT 
e.id_even AS Id, 
d.nom_depto AS Departamento_Expulsor,
m.nom_mun AS Municipio_Expulsor,
CASE
	WHEN (LENGTH(el.lugar) - LENGTH(REPLACE(el.lugar, ',', '')) = 1) THEN NULL
    WHEN (LENGTH(el.lugar) - LENGTH(REPLACE(el.lugar, ',', '')) = 2) THEN Trim(SUBSTRING_INDEX(el.lugar, ',', -1))
    WHEN (LENGTH(el.lugar) - LENGTH(REPLACE(el.lugar, ',', '')) = 3) THEN Trim(SUBSTRING_INDEX(el.lugar, ',', -2))
    ELSE el.lugar
END AS Vereda_Corregimiento_Expulsor,
Year(e.fecha_reg_even) AS Ano,
ELT(DATE_FORMAT(e.fecha_reg_even,'%m'),'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre') AS Mes,
Lpad(Day(e.fecha_reg_even),2,0) AS Dia,
group_concat(de.id_scateven) AS Categorias,
CASE
	WHEN de.id_scateven IN (42) THEN 'No'
    WHEN de.id_scateven IN (40,38,36,35,39,37) THEN 'Si'
    ELSE NULL
END AS Masivo
FROM evento_c e
LEFT JOIN evento_localizacion el ON el.id_even=e.id_even
LEFT JOIN municipio m ON m.id_mun=el.id_mun
LEFT JOIN departamento d ON d.id_depto=substring(el.id_mun,1,2)
LEFT JOIN descripcion_evento de ON de.id_even=e.id_even
WHERE 
YEAR(e.fecha_reg_even)=2018
AND de.id_scateven IN (42,40,38,36,35,39,37,41)
GROUP BY e.id_even
ORDER BY e.id_even 
";

			$result = $conn->OpenRecordset($sql);
			while ($row = $conn->FetchAssoc($result))
			{
				$json[$row['Id']] = $row;
			}




		header('Content-type: application/json; charset=UTF-8');
		header("Pragma: no-cache");
		header("Expires: 0");

		echo json_encode (array_values($json), JSON_UNESCAPED_UNICODE);

		break;

}

?>
