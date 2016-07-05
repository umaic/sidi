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
$ds_dato = New DatoSectorialDAO();
$mun_dao = New MunicipioDAO();

$ids = !empty($_GET['ID_DATO']) ? $_GET['ID_DATO'] : 0;
$lastUpdate = !empty($_GET['ACTUALIZACION']) ? $_GET['ACTUALIZACION'] : date('Y-m-d H:i:00', strtotime('- 1 days'));
$json = array();
$datos_formulados = array();

$sql = 'SELECT a.ID_DATO, a.NOM_DATO, a.DEFINICION_DATO, a.DESAGREG_GEO, a.ACTUALIZACION, a.FORMULA_DATO, b.NOM_CON '.
	'FROM dato_sector as a LEFT JOIN contacto b on a.ID_CONP = b. ID_CON '.
	'WHERE ID_DATO in ('. $ids . ') AND ACTUALIZACION > \''. $lastUpdate . '\'';
$result = $db->OpenRecordset($sql);
while ($row = $db->FetchAssoc($result)){
	$json[$row['ID_DATO']]['metadato'] = array('ID_DATO' => $row['ID_DATO'], 'NOM_DATO' => utf8_encode($row['NOM_DATO']),  'NOM_CON' => utf8_encode($row['NOM_CON']),
		'DEFINICION_DATO' => utf8_encode($row['DEFINICION_DATO']), 'DESAGREG_GEO' => $row['DESAGREG_GEO'], 'ACTUALIZACION' => $row['ACTUALIZACION']);
    
    $json[$row['ID_DATO']]['datos'] = array();

    if (!empty($row['FORMULA_DATO'])) {
        $datos_formulados[$row['ID_DATO']] = $row['DESAGREG_GEO'];;
    }
}

$datoIds = !empty($json) ? implode(',', array_keys($json)) : 0;

$sql = 'SELECT ID_DEPTO as DIVIPOLA, VALOR_DATO, ID_DATO, ID_DEPTO_DATO as ID '.
    'FROM depto_dato '.
    'WHERE ID_DATO in (' . $datoIds . ')'. 
    'UNION '.
    'SELECT ID_MUN as DIVIPOLA, VALOR_DATO, ID_DATO, ID_MUN_DATO as ID '.
    'FROM mpio_dato '.
    'WHERE ID_DATO in (' . $datoIds . ')';

$result = $db->OpenRecordset($sql);
while ($row = $db->FetchAssoc($result)){
    $json[$row['ID_DATO']]['datos'][] = $row;
}
 
$sql = 'SELECT ID_VALDA , ID_MUN, ID_DEPTO, ID_UNIDAD, ID_DATO, INI_VALDA, FIN_VALDA, VAL_VALDA '.
    'FROM valor_dato '.
    'WHERE ID_DATO in (' . $datoIds . ') '.
    'UNION '.
    'SELECT ID_TOTAL AS ID_VALDA, "" AS ID_MUN, ID_DEPTO COLLATE latin1_swedish_ci, ID_UNIDAD, ID_DATO, INI_VALDA, FIN_VALDA, TOTAL_DEPTAL AS VAL_VALDA '.
    'FROM total_deptal_valor_dato '.
    'WHERE ID_DATO in (' . $datoIds . ') '.
    'UNION '.
    'SELECT ID_TOTAL AS ID_VALDA, "" ID_MUN, "" AS ID_DEPTO, ID_UNIDAD, ID_DATO, INI_VALDA, FIN_VALDA, TOTAL_NACIONAL AS VAL_VALDA '.
    'FROM total_nacional_valor_dato '.
    'WHERE ID_DATO in (' . $datoIds . ')'
    ;

$result = $db->OpenRecordset($sql);
while ($row = $db->FetchAssoc($result)){
        $json[$row['ID_DATO']]['valores'][] = $row;
}

header('Content-type: application/json; charset=utf-8');
echo json_encode($json);
?>
