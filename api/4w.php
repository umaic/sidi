<?
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

if (!isset($_GET['_c']) || $_GET['_c'] != 's1dicol-api' || !in_array($mod, array('presencia','totales'))) {
    die('¬¬');
}

function utf8ing(&$item, $key) {
    $item = utf8_encode($item);
}

function nf($num) {
    return number_format($num,0,'.',',');
}

switch ($mod){

    case 'presencia':

        if (empty($_GET['yyyy'])) {
            die('Faltan yyyy');
        }

        $yyyy = $_GET['yyyy'];


        $cond_deptos = '';
        if (!empty($_GET['deptos'])) {
            $cond_deptos = 'ID_DEPTO IN ('.$_GET['deptos'].')';
        }

        $mun_dao = FactoryDAO::factory('municipio');
        $depto_dao = FactoryDAO::factory('depto');
        $sector_dao = FactoryDAO::factory('tema'); // tema es para 4W
        $org_dao = FactoryDAO::factory('org');
        $tipo_dao = FactoryDAO::factory('tipo_org');

        $headers = array('Divipola','Departamento','Divipola','Municipio','No. implementadores','Implementadores');

        $separador = '|';

        // Aumenta el tamaño del texto returnado
        $sql = "SET SESSION group_concat_max_len = 10240";
        $conn->Execute($sql);

        $sectores_a = $sector_dao->GetAllArray('ID_CLASIFICACION = 2');
        $tipos_a = $tipo_dao->GetAllArray('');
        $deptos = $depto_dao->GetAllArray($cond_deptos);
        $orgs_a = $org_dao->GetAllArray('','','');

        /*
        foreach($tipos_a as $tipo) {
            $tipo_nom = utf8_encode($tipo->nombre_es);
            $headers[] = $tipo_nom;
        }
         */

        foreach($sectores_a as $sector) {
            $sector_nom = $sector->nombre;
            $sectores[$sector->id] = $sector_nom;
            $headers[] = "No. implementadores en $sector_nom";
            $headers[] = "Implementadores en $sector_nom";
        }

        $csv = implode($separador, $headers)."\n";

        $matrix = array();

        $sql = "SELECT COUNT(distinct v.id_org), GROUP_CONCAT(DISTINCT o.nom_org SEPARATOR '~')
                FROM proyecto
                INNER JOIN vinculorgpro AS v USING (id_proy)
            INNER JOIN organizacion AS o USING(id_org)
            INNER JOIN %s USING(%s)
            INNER JOIN %s_proy USING(id_proy)
            WHERE id_%s = '%s'
            AND %s = %d
            AND si_proy = '4W'
            AND id_tipo_vinorgpro = 3
            AND year(fin_proy) >= ".min(explode(',',$yyyy))."
            GROUP BY %s";

        foreach($deptos as $depto) {

            // Implementadores
            $sql_imp = sprintf($sql,'tipo_org','id_tipo','depto','depto',$depto->id,1,1,'id_depto');
            //echo "----$sql_imp <br /> <br /> ";
            $rs = $conn->OpenRecordset($sql_imp);
            $row = $conn->FetchRow($rs);

            $linea = array($depto->id,$depto->nombre,'','',$row[0],$row[1]);

            foreach($sectores_a as $sector) {
                $sql_sec = sprintf($sql,'proyecto_tema','id_proy','depto','depto',$depto->id,'id_tema',$sector->id,'id_depto');
                //echo "----$sql_sec <br /> <br /> ";
                $rs = $conn->OpenRecordset($sql_sec);
                $row = $conn->FetchRow($rs);

                $linea[] = $row[0];
                $linea[] = $row[1];
            }

            $csv .= implode($separador, $linea)."\n";

            // Municipios
            $muns = $mun_dao->GetAllArray('ID_DEPTO = '.$depto->id);

            foreach($muns as $mun) {
                // Implementadores
                $sql_imp = sprintf($sql,'tipo_org','id_tipo','mun','mun',$mun->id,1,1,'id_mun');
                //echo "----$sql_imp <br /> <br /> ";
                $rs = $conn->OpenRecordset($sql_imp);
                $row = $conn->FetchRow($rs);

                $linea = array($depto->id,$depto->nombre,$mun->id,$mun->nombre,$row[0],$row[1]);

                foreach($sectores_a as $sector) {
                    $sql_sec = sprintf($sql,'proyecto_tema','id_proy','mun','mun',$mun->id,'id_tema',$sector->id,'id_mun');
                    //echo "----$sql_sec <br /> <br /> ";
                    $rs = $conn->OpenRecordset($sql_sec);
                    $row = $conn->FetchRow($rs);

                    $linea[] = $row[0];
                    $linea[] = $row[1];
                }

                $csv .= implode($separador, $linea)."\n";

            }
        }

        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=\"SIDIH-4W.csv\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo $csv;

    break;

    case 'totales':

        $periodo = date('Y');

        if (!isset($_GET['yyyy'])) {
            die;
        }
        else {
            $periodo = $_GET['yyyy'];
        }

        $_SESSION['si_proy'] = '4w';

        $cual_y = (date('Y') == $periodo) ? 1 : 0;

        $proy_dao = FactoryDAO::factory('p4w');
        $proy_dao->_manageSessionFilter(array('periodo_que' => 'v'));
        $proy_dao->_manageSessionFilter(array('c' => 'periodo', 'id' => $periodo));
        $rs = $proy_dao->resumenMapa('filtros');

        $proyectos_otros = $rs['otros']['eje']['p'][$cual_y];
        $beneficiarios_otros = $rs['otros']['eje']['b'][$cual_y];
        $organizaciones_otros = $rs['otros']['eje']['o'][$cual_y];
        $presupuesto_otros = $rs['otros']['eje']['pres'][$cual_y];
        $presupuesto_otros_tema = $rs['otros']['eje']['pres_tema'][$cual_y];
        $presupuesto_otros_donante = $rs['otros']['eje']['pres_donante'][$cual_y];

        $proyectos_ehp = $rs['ehp']['eje']['p'][$cual_y];
        $beneficiarios_ehp = $rs['ehp']['eje']['b'][$cual_y];
        $organizaciones_ehp = $rs['ehp']['eje']['o'][$cual_y];
        $presupuesto_ehp = $rs['ehp']['eje']['pres'][$cual_y];
        $presupuesto_ehp_tema = $rs['ehp']['eje']['pres_tema'][$cual_y];
        $presupuesto_ehp_donante = $rs['ehp']['eje']['pres_donante'][$cual_y];

        $proyectos = $proyectos_ehp + $proyectos_otros;
        $beneficiarios = $beneficiarios_ehp + $beneficiarios_otros;
        $organizaciones = $organizaciones_ehp + $organizaciones_otros;
        $presupuesto = $presupuesto_ehp + $presupuesto_otros;

        foreach($presupuesto_ehp_tema as $tema_id => $pt) {
            $pt_o = (empty($presupuesto_otros_tema[$tema_id])) ? 0 : $presupuesto_otros_tema[$tema_id];
            $presupuesto_tema[$tema_id] = $pt + $pt_o;
        }

        foreach($presupuesto_ehp_donante as $donante_id => $pd) {
            $pd_o = (empty($presupuesto_otros_donante[$donante_id])) ? 0 : $presupuesto_otros_donante[$donante_id];
            $presupuesto_donante[$donante_id] = $pd + $pd_o;
        }

        header('Content-type: text/json');
        header('Content-type: application/json');

        echo json_encode(compact('proyectos','beneficiarios','organizaciones','presupuesto','presupuesto_tema','presupuesto_donante',
                                    'proyectos_otros','beneficiarios_otros','organizaciones_otros','presupuesto_otros','presupuesto_otros_tema','presupuesto_otros_donante',
                                    'proyectos_ehp','beneficiarios_ehp','organizaciones_ehp','presupuesto_ehp','presupuesto_ehp_tema','presupuesto_ehp_donante'
                                    ));

    break;

}

?>
