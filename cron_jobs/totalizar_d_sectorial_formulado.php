<?
include_once("../admin/lib/common/cadena.class.php");
include_once("../admin/lib/common/mysqldb.class.php");
include_once("../admin/lib/dao/dato_sectorial.class.php");
include_once("../admin/lib/dao/municipio.class.php");
include_once("../admin/lib/dao/depto.class.php");
include_once("../admin/lib/model/municipio.class.php");
include_once("../admin/lib/model/depto.class.php");
include_once("../admin/lib/model/dato_sectorial.class.php");

$conn = MysqlDb::getInstance();
$ds_dao = New DatoSectorialDAO();
$mun_dao = New MunicipioDAO();
$depto_dao = New DeptoDAO();
$cadena = New Cadena();
// 587 ok
// 3 ok
// 585 ok
// 240 ok
// 278 ok
// 345 ok
// 377 ok
// 378 ok
// 583 ok
// 659 ok
//
//$datos_formulados = $ds_dao->GetAllArray(                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         'FORMULA_DATO != "" and id_dato = 659');
$datos_formulados_creados = array();
$ayer = date('Y-m-d', strtotime('- 1 days'));

if (isset($_GET['id_dato'])) {
    $datos_formulados = array($ds_dao->Get($_GET['id_dato']));
    $cron = false;
} else {
    $datos_formulados = $ds_dao->GetAllArray('FORMULA_DATO != ""');
    $cron = true;
}

// Revisa si fueron actualizados los datos de la formula o no existe valor calculado
foreach($datos_formulados as $dato_vo) {

    $id_dato = $dato_vo->id;
    $formula = $dato_vo->formula;
    $id_datos = $cadena->getContentTag($formula,'[',']');

    $go = true;

    if ($cron) {
        $go = false;

        // Consulta si tiene valor calculado
        $sql = "SELECT COUNT(id_valda) FROM valor_dato WHERE id_dato = $id_dato";
        $result = $conn->OpenRecordset($sql);
        $row = $conn->FetchRow($result);
        if (empty($row[0])) {
            $go = true;
        } else {
            foreach ($id_datos as $id_d) {
                $d_vo = $ds_dao->Get($id_d);
                $sql = "SELECT COUNT(id_dato) FROM dato_sector WHERE id_dato = $id_d AND ACTUALIZACION >= '$ayer'";

                $result = $conn->OpenRecordset($sql);
                $row = $conn->FetchRow($result);

                if (!empty($row[0])) {
                    $go = true;
                }
            }
        }
    }

    if ($go) {
        $datos[] = $id_dato;

        // Activa flag en dato_sector para el cron totalizar_d_sectorial
        $sql = "UPDATE dato_sector SET totalizar_nal = 1, totalizar_deptal = 1 WHERE id_dato = $id_dato";
        $conn->Execute($sql);

        echo "ID: $id_dato\n";
    }
}

if (isset($datos[0])) {

    $sql = "DELETE FROM valor_dato WHERE id_dato IN (".implode(',', $datos).")";
    //echo $sql;
    $conn->Execute($sql);

    foreach($datos as $id_dato) {
        echo "Dato = $id_dato <br>";
        $dato_vo = $ds_dao->Get($id_dato);
        $mdgd = $dato_vo->desagreg_geo;

        $periodos = $ds_dao->GetPeriodos($id_dato);

        if (empty($periodos))
            die('No hay info de Id= '.$id_dato);

        if ($mdgd == 'municipal' && !isset($municipios)) {
            $ubis = $mun_dao->GetAllArrayID('','');
            $dato_para = 2;
        }

        if ($mdgd == 'departamental' && !isset($deptos)) {
            $ubis = $depto_dao->GetAllArrayID('','');
            $dato_para = 1;
        }

        $i = 0;
        $time_start = microtime(true);
        foreach($ubis as $u => $ubi) {

            if ($dato_para == 1) {
                $depto = $ubi;
                $mun = '';
            }
            else {
                $depto = '';
                $mun = $ubi;
            }

            foreach($periodos['ini'] as $p => $ini) {

                $fin = $periodos['fin'][$p];
                $vu = $ds_dao->GetValorFormulado($id_dato, $ubi, $ini , $fin, $dato_para);
                $valor = $vu['valor'];

                if (!empty($valor) && $valor != 'N.D.') {
                    $id_unidad = $vu['id_unidad'];

                    $sql = "INSERT INTO valor_dato (ID_MUN, ID_DEPTO, ID_UNIDAD, ID_DATO, INI_VALDA, FIN_VALDA, VAL_VALDA) VALUES
                        ('$mun','$depto',$id_unidad,$id_dato,'$ini','$fin',$valor)";

                    $conn->Execute($sql);

                    $i++;
                }
            }

        }
    }

    $time_end = microtime(true);
    $time = $time_end - $time_start;
    echo "Process Time: ". ($time / 3600) ." horas";
}
?>
