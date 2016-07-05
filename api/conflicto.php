<?php 

/**
 * @autor Amaury Prieto
 */

/**
 * Authentication
 */

if (empty($_GET['user']) || empty($_GET['password']) || $_GET['user'] != 'siconpaz' || $_GET['password'] != 's1conpaz')
	die();

require_once('api_lib.php');

$db = MysqlDb::getInstance();
$ids = !empty($_GET['ID_DATO']) ? $_GET['ID_DATO'] : 0;
$json = array();

$sql = 'SELECT c.ID_SCATEVEN, YEAR( `FECHA_REG_EVEN` ) as YEAR, b.ID_MUN, count( DISTINCT ( a.`ID_EVEN`) ) as TOTAL '.
	'FROM `evento_c` a '.
		'LEFT JOIN evento_localizacion b ON a.`ID_EVEN` = b.`ID_EVEN` '.
		'INNER JOIN descripcion_evento c ON a.`ID_EVEN` = c.`ID_EVEN` '.
		'WHERE c.ID_SCATEVEN in (' . $ids . ') '.
		'GROUP BY c.ID_SCATEVEN, YEAR( `FECHA_REG_EVEN` ) , b.ID_MUN ';

$result = $db->OpenRecordset($sql);
while ($row = $db->FetchAssoc($result)){
	$json[$row['ID_SCATEVEN']]['datos'][] = $row;
}
 
header('Content-type: application/json; charset=utf-8');
echo json_encode($json);
?>
