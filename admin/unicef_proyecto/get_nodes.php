<?php 
session_start();

function getUrlParameter($url,$parameter){
	
    $url_t = explode('&',$url);

	for ($i=0;$i<count($url_t);$i++){

		$par = explode('=',$url_t[$i]);

		if ($par[0] == $parameter)	return $par[1];	
	}
}

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

/*
$data['producto_awp']['titulo'] = 'Convenio';
$data['producto_awp']['titulo_hijo'] = '';
$data['producto_awp']['hijo'] = '';
$data['producto_awp']['col_id_hijo'] = '';
$data['producto_awp']['m_e'] = 'unicef_convenio';
$data['producto_awp']['icon'] = 'folder_convenio';
*/

if (isset($_POST['node'])){
	
    $node = $_POST['node'];
    $_SESSION['id_node_papa_click'] = $node;
    $aaaa = $_SESSION['aaaa'];
    $id = getUrlParameter($node,'id');
    $caso = getUrlParameter($node,'caso');
    $col_nombre = ($caso == 'producto_awp') ? 'codigo' : 'nombre';
    $col_codigo = 'codigo';
    
    switch ($caso){

        case 'sub_comp':
	
            include('../lib/libs_unicef_resultado.php');
            include('../lib/model/unicef_producto_cpap.class.php');
            include('../lib/dao/unicef_producto_cpap.class.php');

            $dao = new UnicefResultadoDAO();
            $hijo_dao = new UnicefProductoCpapDAO();
            
            $vos = $dao->GetAllArrayID("id_sub_componente = $id");
        break;

	    case 'resultado':
	
            include('../lib/libs_unicef_producto_cpap.php');
            include('../lib/model/unicef_actividad_awp.class.php');
            include('../lib/dao/unicef_actividad_awp.class.php');

            $dao = new UnicefProductoCpapDAO();
            $hijo_dao = new UnicefActividadAwpDAO();
            
            $vos = $dao->GetAllArrayID("id_resultado = $id");
        break;

	    case 'producto_cpap':
	
            include('../lib/libs_unicef_actividad_awp.php');
            include('../lib/model/unicef_producto_awp.class.php');
            include('../lib/dao/unicef_producto_awp.class.php');
            include('../lib/model/unicef_convenio.class.php');
            include('../lib/dao/unicef_convenio.class.php');

            $dao = new UnicefActividadAwpDAO();
            $hijo_dao = new UnicefProductoAwpDAO();
            $hijo_dao_1 = new UnicefConvenioDAO();
            
            $vos = $dao->GetAllArrayID("id_producto = $id AND aaaa = $aaaa");
        break;

	    case 'actividad_awp':
	
            include('../lib/libs_unicef_producto_awp.php');
            include('../lib/model/unicef_convenio.class.php');
            include('../lib/dao/unicef_convenio.class.php');

            $dao = new UnicefProductoAwpDAO();
            $hijo_dao = new UnicefConvenioDAO();
            
            $vos = $dao->GetAllArrayID("id_actividad = $id AND aaaa = $aaaa");
        break;

	    case 'producto_awp':
	
            include('../lib/libs_unicef_convenio.php');

            $dao = new UnicefConvenioDAO();
            
            $vos = $dao->GetAllArrayID("id_producto = $id AND YEAR(fecha_ini) = $aaaa");
        break;

	}

    // La primera opcion es la de crear
    if ($caso != 'componente'){
        $info['text'] = 'Crear '.$data[$caso]['titulo'];
        $info['leaf'] = true;
        $info['icon'] = 'admin/images/home/insertar_16_18.gif';
        $info['id'] = 'm_e='.$data[$caso]['m_e'].'&accion=insertar&id_papa='.$id;
        $nodos[] = $info;
    }

	foreach($vos as $id_vo){

		$codigo = $dao->getFieldValue($id_vo,$col_codigo);
		$nombre = $dao->getFieldValue($id_vo,$col_nombre);
		$num_chrs = 100;
        $id_n = "id=$id_vo&id_papa=$id&accion=actualizar&m_e=".$data[$caso]['m_e']."&caso=".$data[$caso]['hijo'];
		$text = $codigo.' ';
        $text .= (strlen($nombre) > $num_chrs) ? substr($nombre,0,$num_chrs)."...." : $nombre;
        $text = htmlentities($text);
        
        if ($caso != 'actividad_awp')  $text .= ' [<span style=\'color:#'.$data[$caso]['class_num'].'\'>'.$hijo_dao->numRecords($data[$caso]['col_id_hijo'].'='. $id_vo.' AND aaaa = '.$aaaa,'tree').' '.$data[$caso]['titulo_hijo_num'].'</span> ]';
        
        if ($caso == 'producto_cpap')   $text .= ' [<span style=\'color:#'.$data[$caso]['class_num_1'].'\'>'.$hijo_dao_1->numRecords($data[$caso]['col_id_hijo'].'='. $id_vo.' AND YEAR(fecha_ini) = '.$aaaa).' '.$data[$caso]['titulo_hijo_num_1'].'</span> ]';

		$info['text'] = $text;
		$info['cls'] = 'file';
		$info['id'] = $id_n;
		$info['leaf'] = false;
        $info['icon'] = 'images/unicef/tree/'.$data[$caso]['icon'].'.gif';
        $info['qtip'] = htmlentities($nombre);
		$info['leaf'] = ($caso == 'actividad_awp') ? true : false;

		$nodos[] = $info;
	}

    if ($caso == 'actividad_awp'){
        $info['text'] = 'Crear '.$data[$caso]['titulo_1'];
        $info['leaf'] = true;
        $info['icon'] = 'admin/images/home/insertar_16_18.gif';
        $info['id'] = 'm_e='.$data[$caso]['m_e_1'].'&accion=insertar&id_papa='.$id;
        $nodos[] = $info;


        $dao = new UnicefConvenioDAO();
        $vos = $dao->GetAllArrayID("id_actividad = $id");
        
        foreach($vos as $id_vo){

            // Codigo + nombre
            $nombre = $dao->getFieldValue($id_vo,$col_nombre);
            $codigo = $dao->getFieldValue($id_vo,'codigo');

            $nombre = $codigo.' - '.$nombre;

            $num_chrs = 100;
            $id_n = "id=$id_vo&id_papa=$id&accion=actualizar&m_e=".$data[$caso]['m_e_1']."&caso=".$data[$caso]['hijo_1'];
            $text = (strlen($nombre) > $num_chrs) ? substr($nombre,0,$num_chrs)."...." : $nombre;
            $text = htmlentities($text);
            
            $info['text'] = $text;
            $info['cls'] = 'file';
            $info['id'] = $id_n;
            $info['leaf'] = false;
            $info['icon'] = 'images/unicef/tree/'.$data[$caso]['icon_1'].'.gif';
            $info['qtip'] = htmlentities($nombre);
            $info['leaf'] = ($caso == 'actividad_awp') ? true : false;

            $nodos[] = $info;
        }


    }

	echo json_encode($nodos);

}
?> 
