<?php 

include('admin/lib/dao/factory.class.php');
            include('admin/lib/libs_unicef_resultado.php');
            include('admin/lib/model/unicef_producto_cpap.class.php');
            include('admin/lib/dao/unicef_producto_cpap.class.php');
            include('admin/lib/model/unicef_actividad_awp.class.php');
            include('admin/lib/dao/unicef_actividad_awp.class.php');
            include('admin/lib/model/unicef_producto_awp.class.php');
            include('admin/lib/dao/unicef_producto_awp.class.php');
            include('admin/lib/model/unicef_convenio.class.php');
            include('admin/lib/dao/unicef_convenio.class.php');

if (!isset($_GET['id_c']))   die('Falta el ID del componente');
else    $id_componente = $_GET['id_c'];

$a_ini = 2009;
$aaaa = $a_ini;
if (isset($_GET['aaaa'])){
    $aaaa = $_GET['aaaa'];
}

$_SESSION['aaaa'] = $aaaa;

$sub_componente_dao = FactoryDAO::factory('unicef_sub_componente');
$componente_dao = FactoryDAO::factory('unicef_componente');
$resultado_dao = FactoryDAO::factory('unicef_resultado');
$actividad_dao = FactoryDAO::factory('unicef_actividad_awp');
$conn = MysqlDb::getInstance();
$comp = $componente_dao->Get($id_componente);

function tree($vos, $caso, $dao, $hijo_dao, $id, $aaaa){
    $data['sub_comp']['titulo'] = 'Resultado';
    $data['sub_comp']['titulo_hijo'] = 'Producto CPAP';
    $data['sub_comp']['titulo_hijo_num'] = 'P. CPAP';
    $data['sub_comp']['class_num'] = '392110';
    $data['sub_comp']['hijo'] = 'resultado';
    $data['sub_comp']['col_id_hijo'] = 'id_resultado';
    $data['sub_comp']['m_e'] = 'unicef_resultado';
    $data['sub_comp']['icon'] = 'folder_resultado';

    $data['resultado']['titulo'] = 'Producto CPAP';
    $data['resultado']['titulo_hijo'] = 'Actividad AWP';
    $data['resultado']['titulo_hijo_num'] = 'A. AWP';
    $data['resultado']['class_num'] = '0066ff';
    $data['resultado']['hijo'] = 'producto_cpap';
    $data['resultado']['col_id_hijo'] = 'id_producto';
    $data['resultado']['m_e'] = 'unicef_producto_cpap';
    $data['resultado']['icon'] = 'folder_producto_cpap';

    $data['producto_cpap']['titulo'] = 'Actividad AWP';
    $data['producto_cpap']['titulo_hijo'] = 'Producto AWP';
    $data['producto_cpap']['titulo_hijo_num'] = 'P. AWP';
    $data['producto_cpap']['class_num'] = 'c7cdb7';
    $data['producto_cpap']['titulo_hijo_num_1'] = 'Conv.';
    $data['producto_cpap']['class_num_1'] = 'd2a1de';
    $data['producto_cpap']['hijo'] = 'actividad_awp';
    $data['producto_cpap']['col_id_hijo'] = 'id_actividad';
    $data['producto_cpap']['m_e'] = 'unicef_actividad_awp';
    $data['producto_cpap']['icon'] = 'folder_actividad_awp';

    $data['actividad_awp']['titulo'] = 'Producto AWP';
    $data['actividad_awp']['titulo_hijo'] = '';
    $data['actividad_awp']['hijo'] = 'producto_awp';
    $data['actividad_awp']['col_id_hijo'] = '';
    $data['actividad_awp']['m_e'] = 'unicef_producto_awp';
    $data['actividad_awp']['icon'] = 'folder_producto_awp';

    $data['actividad_awp']['titulo_1'] = 'convenio';
    $data['actividad_awp']['hijo_1'] = 'convenio';
    $data['actividad_awp']['m_e_1'] = 'unicef_convenio';
    $data['actividad_awp']['icon_1'] = 'folder_convenio';

$col_codigo = 'codigo';
    $col_nombre = ($caso == 'producto_awp') ? 'codigo' : 'nombre';
    switch ($caso){

        case 'sub_comp':
	
            $dao = new UnicefResultadoDAO();
            $hijo_dao = new UnicefProductoCpapDAO();
            
            $vos = $dao->GetAllArrayID("id_sub_componente = $id");
            $caso_h = 'resultado';
            $l = 1;
        break;

	    case 'resultado':

            $dao = new UnicefProductoCpapDAO();
            $hijo_dao = new UnicefActividadAwpDAO();
            
            $vos = $dao->GetAllArrayID("id_resultado = $id");
            $caso_h = 'producto_cpap';
            $l = 2;
        break;

	    case 'producto_cpap':
	
            $dao = new UnicefActividadAwpDAO();
            $hijo_dao = new UnicefProductoAwpDAO();
            $hijo_dao_1 = new UnicefConvenioDAO();
            
            $vos = $dao->GetAllArrayID("id_producto = $id AND aaaa = $aaaa");
            $caso_h = 'actividad_awp';
            $l = 3;
        break;

	    case 'actividad_awp':
	
            $dao = new UnicefProductoAwpDAO();
            
            $vos = $dao->GetAllArrayID("id_actividad = $id AND aaaa = $aaaa");
            $caso_h = 'producto_awp';
            $l = 4;
        break;


	}
    
    foreach($vos as $id_vo){

        $col_nombre = 'nombre';
        $codigo = $dao->getFieldValue($id_vo,$col_codigo);
        $nombre = $dao->getFieldValue($id_vo,$col_nombre);
        $text = $codigo.' '.$nombre;
        $text = htmlentities($text);

        if ($caso != 'actividad_awp')  $text .= ' [<span style=\'color:#'.$data[$caso]['class_num'].'\'>'.$hijo_dao->numRecords($data[$caso]['col_id_hijo'].'='. $id_vo.' AND aaaa = '.$aaaa,'tree').' '.$data[$caso]['titulo_hijo_num'].'</span> ]';

        if ($caso == 'producto_cpap')   $text .= ' [<span style=\'color:#'.$data[$caso]['class_num_1'].'\'>'.$hijo_dao_1->numRecords($data[$caso]['col_id_hijo'].'='. $id_vo.' AND YEAR(fecha_ini) = '.$aaaa).' '.$data[$caso]['titulo_hijo_num_1'].'</span> ]';

        echo "<tr>";
        for ($i=0;$i<$l;$i++){
            $border = ($i == ($l-1) && $l == 5) ? '' : 'border-right:1px solid #000;';
            echo "<td style='width:20px;'><table cellpadding='0' cellspacing='0'><tr><td style='width:10px;$border;height:30px;'>&nbsp;</td></tr></table></td>";
        }
        echo "<td colspan='".(6-$l)."' style='font-size:11px;white-space:normal;'>$text</td></tr>";

        
        if ($caso != 'actividad_awp')
                    tree($vos, $caso_h, $dao, $hijo_dao, $id_vo, $aaaa);

    }
        
        if ($caso == 'actividad_awp'){
            $dao = new UnicefConvenioDAO();
            $vos = $dao->GetAllArrayID("id_actividad = $id");
            
            foreach($vos as $id_vo){
                $nombre = $dao->getFieldValue($id_vo,$col_nombre);
                $codigo = $dao->getFieldValue($id_vo,'codigo');

                $nombre = $codigo.' - '.$nombre;
                $text = htmlentities($nombre);
                
                echo "<tr>";
                for ($i=0;$i<$l;$i++){
                    $border = ($i == ($l-1) && $l == 5) ? '' : 'border-right:1px solid #000;';
                    echo "<td style='width:20px;'><table cellpadding='0' cellspacing='0'><tr><td style='width:10px;$border;height:30px;'>&nbsp;</td></tr></table></td>";
                }
                echo "<td colspan='".(6-$l)."' style='font-size:11px;white-space:normal;'>$text</td></tr>";

            }

        }
}

echo '<table style="width:900px;margin:0 0 0 20px" cellpadding="0" cellspacing="0">';
$subs = $sub_componente_dao->GetAllArray("id_componente=$comp->id",'','');
$num_subs = $sub_componente_dao->numRecords("id_componente=$comp->id");
foreach ($subs as $s=>$sub){
    $sql = "select count(DISTINCT r.id_resultado) as num_r, count(DISTINCT p_cpap.id_producto) as num_p_cpap, count(DISTINCT a_awp.id_actividad) as num_a_awp, count(DISTINCT p_awp.id_producto) as num_p_awp, count(DISTINCT conv.id_convenio) as num_conv  from unicef_sub_componente sub left join unicef_resultado_cpd r using(id_sub_componente) left join unicef_producto_cpap p_cpap using(id_resultado) left join unicef_actividad_awp a_awp using(id_producto) left join unicef_producto_awp p_awp using(id_actividad) left join unicef_convenio conv on a_awp.id_actividad = conv.id_actividad WHERE id_sub_componente = $sub->id AND a_awp.aaaa = $aaaa";
    $rs = $conn->OpenRecordset($sql);
    $row = $conn->FetchObject($rs);
    $resumen_conteo = "[<span style=\'color:#64cf4b\'>$row->num_r Result.</span>] [<span style=\'color:#392110\'>$row->num_p_cpap P. CPAP</span>] [<span style=\'color:#0066ff\'>$row->num_a_awp Activ.</span>] [<span style=\'color:#c7cd7b\'>$row->num_p_awp P. AWP</span>] [<span style=\'color:#d2a1de\'>$row->num_conv Conv.</span>]";

    echo '<tr><td colspan="6" style="font-size: 18px;">'.$sub->nombre.$resumen_conteo.'</td></<tr>';
    $dao = new UnicefResultadoDAO();
    $hijo_dao = new UnicefProductoCpapDAO();
    $vos = $dao->GetAllArrayID("id_sub_componente = $sub->id");
    $caso = 'sub_comp';

    tree($vos, $caso, $dao, $hijo_dao, $sub->id, $aaaa);

}
                ?>
