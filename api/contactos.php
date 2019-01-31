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

if (!isset($_GET['_c']) || $_GET['_c'] != 's1dicol-api' || !in_array($mod, array('debug'))) {
	die('¬¬');
}

function utf8ing(&$item, $key) {
	$item = utf8_encode($item);
}

function nf($num) {
	return number_format($num,0,'.',',');
}

switch ($mod){

	case 'debug':


		$sql = "
SELECT 
  c.`ID_CON` AS Id,
  cco2.nom_contacto_col_opcion AS Primer_Encabezado,
  c.`NOM_CON` AS Nombres,
  c.`APE_CON` AS Apellidos,
  c.`EMAIL_CON` AS Correo,
  c.`CEL_CON` AS Celular,
  c.`TEL_CON` AS Telefono_Fijo,
  c.`FAX_CON` AS Fax,
  c.`SOCIAL_CON` AS Skype,
  cco1.nom_contacto_col_opcion AS Cargo,
  cco3.nom_contacto_col_opcion AS Saludo,
  c.`SI_CON` AS Sistema_de_Informacion,
  c.`CREAC_CON` AS Fecha_Creacion,
  c.`UPDATE_CON` AS Fecha_Actualizacion,
  c.`ID_MUN` AS Id_Municipio,
  m.nom_mun AS Municipio,
  d.nom_depto AS Departamento,
  c.`MAILCHIMP_STATUS` AS Estado_Mailchimp

,Count(p.id_proy) AS Numero_Proyectos, Group_concat(p.id_proy) AS Id_Proyectos
,Count(ce.id_esp) AS Numero_Espacios, Group_concat(ce.id_esp) AS Id_Espacios
FROM 
contacto c
LEFT JOIN proyecto p ON p.id_con=c.id_con
LEFT JOIN contacto_esp ce ON ce.id_con=c.id_con
LEFT JOIN municipio m ON m.id_mun=c.id_mun
LEFT JOIN departamento d ON d.id_depto=Left(c.`ID_MUN`,2)

LEFT JOIN contacto_opcion_valor cov1 ON (cov1.id_con=c.id_con AND cov1.id_contacto_col=1)
LEFT JOIN contacto_col_opcion cco1 ON cco1.id_contacto_col_opcion=cov1.id_contacto_col_opcion

LEFT JOIN contacto_opcion_valor cov2 ON (cov2.id_con=c.id_con AND cov2.id_contacto_col=2)
LEFT JOIN contacto_col_opcion cco2 ON cco2.id_contacto_col_opcion=cov2.id_contacto_col_opcion

LEFT JOIN contacto_opcion_valor cov3 ON (cov3.id_con=c.id_con AND cov3.id_contacto_col=3)
LEFT JOIN contacto_col_opcion cco3 ON cco3.id_contacto_col_opcion=cov3.id_contacto_col_opcion

GROUP BY c.id_con
ORDER BY c.id_con DESC
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
