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
$conn = MysqlDb::getInstance();
$limit = 300;
$html = '';
$titles = array();
$filters = array();
$rows = array();

$render_json = true;

if (!in_array($mod, array('contactos','organizaciones','4w','cifras','portal_csv'))) {
    die('El m&oacute;dulo no existe');
} 

function utf8ing(&$item, $key) {
    $item = utf8_encode($item);
}

function nf($num) {
    return number_format($num,0,'.',',');
}

$render = 'grid';

switch ($mod){

    case 'portal_csv':

       // Orgs
        $org_dao = FactoryDAO::factory('org');
        
        $tipo_org_dao = FactoryDAO::factory('tipo_org');

        $_ts = $tipo_org_dao->GetAllArray('');
        foreach($_ts as $t) {
            $tipo_orgs[$t->id] = $t->nombre_es;
        }

        // Contactos
        $contacto_dao = FactoryDAO::factory('contacto');
        $contacto_col_op_dao = FactoryDAO::factory('contacto_col_op');
        $depto_dao = FactoryDAO::factory('depto');
        $mun_dao = FactoryDAO::factory('municipio');
        $espacio_dao = FactoryDAO::factory('espacio');
        
        $_ess = $espacio_dao->GetAllArray('');
        foreach($_ess as $e) {
            $espacios[$e->id] = $e->nombre;
        }
        
        
        // Solo para exportar los que se han modificado
        //$fecha_primera_sync = '2013-10-31';
        $fecha_primera_sync = '0000-00-00';
        $csv = "Name,Cluster,Salutation,First name,Last name,Email,Telephones,Organization,Job Title,Location,Coordination Hub,Fundings,Themes,Emergencies";

        //$cond_con = 'fecha_update > '.$fecha_primera_sync.' AND id_esp IN (2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,22,23,24,25,26,56,57,70,74,27,28,29,30,31,32,54,34,35,36,37,39,40,41,42,43,45,46,47,48,49,50,51,52,53,58,59,60,61,62,63,64,65,66,67,68,69,75,71,72,76,77,79,80,81,85,88,92)';
        $cond_con = 'id_esp IN (2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,22,23,24,25,26,56,57,70,74,27,28,29,30,31,32,54,34,35,36,37,39,40,41,42,43,45,46,47,48,49,50,51,52,53,58,59,60,61,62,63,64,65,66,67,68,69,75,71,72,76,77,79,80,81,85,88,92)';
        $contactos = $contacto_dao->GetAllArray($cond_con,'','');
        $id_orgs = array();

        foreach($contactos as $c) {

            $lugar = (!empty($c->id_mun)) ? $mun_dao->GetName($c->id_mun) : '';
            $nombre = $c->nombre.' '.$c->apellido;
            $tels = array($c->tel,$c->cel);
            $tels = implode('-', $tels);
            $cargo = '';
            $saludo = '';
            $organizacion = '';
            
            if (!empty($c->id_org)) {
                $id = $c->id_org[0];
                $organizacion = $org_dao->GetName($id);

                if (!in_array($id, $id_orgs)) {
                    $id_orgs[] = $id;
                }
            }
            
            $col_id = 1;
            if (!empty($c->caracteristicas[$col_id])) {
                $_cargo = $contacto_col_op_dao->Get($c->caracteristicas[$col_id]);
                $cargo = $_cargo->nombre;
            }
            
            $col_id = 2;
            if (!empty($c->caracteristicas[$col_id])) {
                $_saludo = $contacto_col_op_dao->Get($c->caracteristicas[$col_id]);
                $saludo = $_saludo->nombre;
            }

            $esps = array();
            foreach($c->id_espacio as $ie) {
                $esps[] = $espacios[$ie];
            }

            $esps = implode('-', $esps);

            $_v = array($nombre,$esps,$saludo,$c->nombre,$c->apellido,$c->email,$tels,$organizacion,$cargo,$lugar,'','','','');

            $csv .= "\n".implode(',', $_v);
        }

        $fp = fopen('../consulta/csv/contactos.csv','w+');
        fwrite($fp, $csv);
        fclose($fp);
        
        // Organizaciones basadas en los contactos
        $csv = 'Name,Type,Acronym,Website';
        $orgs = $org_dao->GetAllArray('id_org IN ('.implode(',', $id_orgs).')','','');
        foreach($orgs as $o) {
            $csv .= "\n".$o->nom.",".$tipo_orgs[$o->id_tipo].",".$o->sig.",".$o->web;
        }

        $fp = fopen('../consulta/csv/orgs.csv','w+');
        fwrite($fp, $csv);
        fclose($fp);

        // Lugares
        $csv = 'PCode,Name,Parent,Population,WKT';
        $muns = $mun_dao->GetAllArray('');
        foreach($muns as $m) {
            
            $depto = $depto_dao->GetName($m->id_depto);

            $csv .= "\n".$m->id.','.$m->nombre.','.$depto.",,";
        }
        
        $fp = fopen('../consulta/csv/lugares.csv','w+');
        fwrite($fp, $csv);
        fclose($fp);

        echo '<a href="../consulta/csv/contactos.csv">Contactos CSV</a><br />';
        echo '<a href="../consulta/csv/orgs.csv">Organizaciones CSV</a><br />';
        echo '<a href="../consulta/csv/lugares.csv">Lugares CSV</a><br />';

        $render_json = false;

    break;

    case 'cifras':
        
        $render = 'html';
        $title_block = 'Sidih en números';

        $org_dao = FactoryDAO::factory('org');
        $num_orgs = $org_dao->numRecords('');

        $pro_dao = FactoryDAO::factory('p4w');
        $num_proy = $pro_dao->numRecords('');

        $d_s_dao = FactoryDAO::factory('dato_sectorial');
        $num_d_s_valores = $d_s_dao->numRecordsValores('');
        $num_d_s = $d_s_dao->numRecords('');

        /*
        $desplazamiento_dao = New DesplazamientoDAO();
        $num_des = $desplazamiento_dao->numRecords('');
        $meses = array ("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");


        $fecha = explode("-",$desplazamiento_dao->GetFechaCorte(2));
        $fecha_corte_a_s = "$fecha[2] ".$meses[$fecha[1]*1]." $fecha[0]"; 

        $fecha = explode("-",$desplazamiento_dao->GetFechaCorte(1));
        $fecha_corte_codhes = "$fecha[2] ".$meses[$fecha[1]*1]." $fecha[0]";
        */

        $evento_dao = FactoryDAO::factory('evento_c');
        $num_evento_c = $evento_dao->numRecords('');

        $html = "<table>
                <tr><td><b>Organizaciones</b></td><td>$num_orgs</td></tr>
                <tr><td><b>Proyectos</b></td><td>$num_proy</td></tr>
                <tr><td><b>Registros de $num_d_s Datos Sectoriales</b></td><td>$num_d_s_valores</td></tr>
                <tr><td><b>Eventos del Conflicto</b></td><td>$num_evento_c</td></tr>
                </table>";

    break;

    // Vigentes año actual
    case '4w':
        
        include_once($_SERVER['DOCUMENT_ROOT']."/sissh/consulta/lib/libs_p4w.php");

        $periodo = date('Y');
        
        $_SESSION['si_proy'] = '4w';

        $proy_dao = New P4wDAO();
        $proy_dao->_manageSessionFilter(array('periodo_que' => 'v'));
        $proy_dao->_manageSessionFilter(array('c' => 'periodo', 'id' => $periodo));
        $rs = $proy_dao->resumenMapa('filtros');
        $title_block = 'Respuesta humanitaria';
        $render = 'html';

        $proyectos_otros = $rs['otros']['eje']['p'][1];
        $beneficiarios_otros = $rs['otros']['eje']['b'][1];
        $organizaciones_otros = $rs['otros']['eje']['o'][1];
        $presupuesto_otros = $rs['otros']['eje']['pres'][1];
        
        $proyectos_ehp = $rs['ehp']['eje']['p'][1];
        $beneficiarios_ehp = $rs['ehp']['eje']['b'][1];
        $organizaciones_ehp = $rs['ehp']['eje']['o'][1];
        $presupuesto_ehp = $rs['ehp']['eje']['pres'][1];

        $proyectos = nf($proyectos_ehp + $proyectos_otros);
        $beneficiarios = nf($beneficiarios_ehp + $beneficiarios_otros);
        $organizaciones = nf($organizaciones_ehp + $organizaciones_otros);
        $presupuesto = nf($presupuesto_ehp + $presupuesto_otros);

        $proyectos_otros = nf($proyectos_otros);
        $beneficiarios_otros = nf($beneficiarios_otros);
        $organizaciones_otros = nf($organizaciones_otros);
        $presupuesto_otros = nf($presupuesto_otros);
        
        $proyectos_ehp = nf($proyectos_ehp);
        $beneficiarios_ehp = nf($beneficiarios_ehp);
        $organizaciones_ehp = nf($organizaciones_ehp);
        $presupuesto_ehp = nf($presupuesto_ehp);
        
        $block =  "<div style='margin-bottom: 10px'><div class='left'><img src='http://sidih.salahumanitaria.co/sissh/images/ocha_icons/%s.png' style='float:left'></div>
                <div><span style='font-size:18px;'><b>%s</b></span><span style='font-size:11px;'>&nbsp;&nbsp;(EHP: %s)</span></div>
                <div><span font-size:11x;>%s</span></div></div>";

        $html = "
            <div style='margin-bottom: 10px'>
                
            ".sprintf($block, 'reporting_red', $proyectos, $proyectos_ehp, 'N&uacute;mero total de proyectos')."
            ".sprintf($block, 'affected_population_red', $beneficiarios, $beneficiarios_ehp, 'N&uacute;mero de beneficiarios directos')."
            ".sprintf($block, 'fund_red', $presupuesto, $presupuesto_ehp, 'Presupuesto U$')."
            ".sprintf($block, 'house_red', $organizaciones, $organizaciones_ehp, 'Total de organizaciones ejecutoras')."
            </div>
            <div style='font-size: 11px;'>
                <div style='float:left'>Información de proyectos vigentes <br />en $periodo. Fuente de informaci&oacute;n</div>
                <div style='float:right'>
                    <a href='http://sidih.salahumanitaria.co' target='_blank'>
                        <img src='http://sidih.salahumanitaria.co/sissh/images/logo_small_brand.gif' style='border:0'>
                    </a>
                </div>
                <div style='clear:both'></div>
            </div>
            ";

        //$rows = compact('proyectos', 'beneficiarios', 'presupuesto', 'organizaciones');

    break;

    case 'contactos':
        $title_block = 'Contactos';

        $contacto_dao = FactoryDAO::factory('contacto');
        $contacto_col_op_dao = FactoryDAO::factory('contacto_col_op');
        $mun_dao = FactoryDAO::factory('municipio');
        $depto_dao = FactoryDAO::factory('depto');
        $espacio_dao = FactoryDAO::factory('espacio');
        $org_dao = FactoryDAO::factory('org');
        $join_org = false;
        $filtro_depto = 0;
        $filtro_org = 0;
        $filtro_esp = 0;
        $filtro_nombre = '';
        $filtro_apellido = '';

        // Espacios filtrados para portal
        if (!empty($_GET['id_esp'])) {
            $filtro_esp = $_GET['id_esp'];

            if (preg_match('/\d+/', $filtro_esp)) {
                $cond = " id_esp = $filtro_esp";
            }
        }
        else {
            $cond = 'ID_ESP IN (2,3,5,6,7,8,9,10,12,13,14,15,17,18,19,22,56,57,74,39,45,52,53,59,92,133)';
        }
        
        // Filtros
        // Departamento
        if (!empty($_GET['departamento']) && $_GET['departamento'] != '00') {
            
            $filtro_depto = $_GET['departamento'];

            if (preg_match('/\d+/', $filtro_depto)) {
                $cond .= " AND id_mun LIKE '$filtro_depto%'";
            }
        }
        
        if (!empty($_GET['id_org'])) {
            $filtro_org = $_GET['id_org'];

            if (preg_match('/\d+/', $filtro_org)) {
                $cond .= " AND id_org = $filtro_org";
            }
        }
        
        
        if (!empty($_GET['nombre'])) {
            $filtro_nombre = $_GET['nombre'];


            if (preg_match('/[a-zA-Z0-9]+/', $filtro_nombre)) {
                $cond .= " AND nom_con LIKE '%$filtro_nombre%'";
            }
        }
        
        if (!empty($_GET['apellido'])) {

            $filtro_apellido = $_GET['apellido'];

            if (preg_match('/[a-zA-Z0-9]+/', $filtro_apellido)) {
                $cond .= " AND ape_con LIKE '%$filtro_apellido%'";
            }
        }

        // Los parametros url viene utf8 desde la funcion de drupal que usa el modulo j2h
        $cond = utf8_decode($cond);

        $_cts = $contacto_dao->GetAllArray($cond, 'nom_con, ape_con', '');

        $fss = array('orgs', 'deptos', 'esps');

        $filters['nombre'] = array('id' => 'nombre',
                                 'label' => 'Nombre',
                                 'type' => 'text',
                                 'value' => $filtro_nombre
                             );
        
        $filters['apellido'] = array('id' => 'apellido',
                                 'label' => 'Apellido',
                                 'type' => 'text',
                                 'value' => $filtro_apellido
                             );

        $filters['orgs'] = array('id' => 'id_org',
                                 'label' => 'Organización',
                                 'type' => 'dropdown',
                                 'value' => $filtro_org,
                                 'options' => array(array('value' => 0,  'text' => 'Todas'))
                               );

        $filters['deptos'] = array('id' => 'departamento',
                                   'label' => 'Departamento',
                                   'type' => 'dropdown', 
                                   'value' => $filtro_depto,
                                   'options' => array(array('value' => 0,  'text' => 'Todos'))
                               );
        
        $filters['esps'] = array('id' => 'id_esp',
                                   'label' => 'Espacio',
                                   'type' => 'dropdown', 
                                   'value' => $filtro_esp,
                                   'options' => array(array('value' => 0,  'text' => 'Todos'))
                               );
        
        $titles = array(
                      array('index' => 'nombre', 'text' => 'Nombre'), 
                      array('index' => 'apellido', 'text' => 'Apellido'), 
                      array('index' => 'email', 'text' => 'Email'), 
                      array('index' => 'departamento', 'text' => 'Departamento'), 
                      array('index' => 'ciudad', 'text' => 'Ciudad'), 
                      array('index' => 'organizacion', 'text' => 'Organización'), 
                      array('index' => 'cargo', 'text' => 'Cargo'),
                      array('index' => 'direccion', 'text' => 'Dirección'), 
                      array('index' => 'web', 'text' => 'Web'), 
                      array('index' => 'espacios', 'text' => 'Espacios')
                  );
        // Filtros
        $deptos = array();
        $sql = "SELECT DISTINCT id_mun, id_depto, nom_depto 
                FROM contacto 
                INNER JOIN contacto_esp USING(id_con)
                INNER JOIN municipio USING(id_mun)
                INNER JOIN departamento USING(id_depto)
                WHERE $cond";

        $rs = $conn->OpenRecordset($sql);
        while ($row = $conn->FetchRow($rs)) {
            $deptos[$row[1]] = $row[2];
        }
        
        $orgs = array(); 
        $sql = "SELECT DISTINCT id_org, nom_org 
                FROM contacto_org 
                INNER JOIN contacto USING(id_con)
                INNER JOIN contacto_esp USING(id_con)
                INNER JOIN organizacion USING(id_org)
                WHERE $cond";
        
        $rs = $conn->OpenRecordset($sql);
        while ($row = $conn->FetchRow($rs)) {
            $orgs[$row[0]] = $row[1];
        }
        
        $esps = array(); 
        $sql = "SELECT DISTINCT id_esp, nom_esp 
                FROM contacto_esp 
                INNER JOIN espacio USING(id_esp)
                INNER JOIN contacto USING(id_con)
                WHERE $cond";

        //echo $sql;

        $rs = $conn->OpenRecordset($sql);
        while ($row = $conn->FetchRow($rs)) {
            $esps[$row[0]] = $row[1];
        }

        // Rows
        foreach($_cts as $_c) {

            $saludo = '';
            $cargo = '';
            $encabezado = '';
            $departamento = '';
            $ciudad = '';
            $espacios = '';
            $organizacion = '';
            $email = '';

            $nombre = trim($_c->nombre);
            $apellido = trim($_c->apellido);
            $email = trim($_c->email);
            $tel = $_c->tel;

            $col_id = 1;
            if (!empty($_c->caracteristicas[$col_id])) {
                $_cargo = $contacto_col_op_dao->Get($_c->caracteristicas[$col_id]);
                $cargo = $_cargo->nombre;
            }
            
            $col_id = 2;
            if (!empty($_c->caracteristicas[$col_id])) {
                $_saludo = $contacto_col_op_dao->Get($_c->caracteristicas[$col_id]);
                $saludo = $_saludo->nombre;
            }
            
            $col_id = 3;
            if (!empty($_c->caracteristicas[$col_id])) {
                $_encabezado = $contacto_col_op_dao->Get($_c->caracteristicas[$col_id]);
                $encabezado = $_encabezado->nombre;
            }

            if (!empty($_c->id_mun)) {
                $_mun = $mun_dao->Get($_c->id_mun);
                $ciudad = $_mun->nombre;
                
                $id_depto = $_mun->id_depto;
                $_depto = $depto_dao->Get($id_depto);

                $departamento = $_depto->nombre;
                
            }
            
            if (!empty($_c->id_org)) {
                
                $id = $_c->id_org[0];
                
                $organizacion = $org_dao->GetName($id);
                $direccion = $org_dao->GetFieldValue($id,'dir_org');
                $web = $org_dao->GetFieldValue($id,'web_org');
                $web = '<a href="'.$web.'" target="_blank">'.$web.'</a>';

            }

            $espacios = array();
            foreach($_c->id_espacio as $id_e) {
                $_e = $espacio_dao->Get($id_e);

                $espacios[] = $_e->nombre;
            }

            $espacios = implode(',', $espacios);



            $ar = compact('nombre','apellido','email','tel','cargo','saludo',
                'encabezado','departamento','ciudad','organizacion','direccion','web',
                'espacios');
            
            array_walk($ar, 'utf8ing');

            $rows[] = $ar;
        }
        
        // Ordena filters
        foreach($fss as $fs) {
            asort($$fs);
            foreach($$fs as $id => $n) {
            
                $filters[$fs]['options'][] = array('value' => $id,
                    'text' => utf8_encode($n));
            }
        }
    
        break;

    case 'organizaciones':

        $title_block = 'Organizaciones';
        $mun_dao = FactoryDAO::factory('municipio');
        $depto_dao = FactoryDAO::factory('depto');
        $sector_dao = FactoryDAO::factory('sector');
        $org_dao = FactoryDAO::factory('org');
        $filtro_depto = 0;
        $filtro_sector = 0;
        $filtro_nombre = '';
        $cond = '1=1';

        // Filtros
        // Departamento
        if (!empty($_GET['departamento'])) {
            
            $filtro_depto = $_GET['departamento'];

            if (preg_match('/\d+/', $filtro_depto)) {
                $cond .= " AND id_mun_sede LIKE '$filtro_depto%'";
            }
        }
        
        if (!empty($_GET['sector'])) {
            
            $filtro_sector = $_GET['sector'];

            if (preg_match('/\d+/', $filtro_depto)) {
                $cond .= " AND id_comp LIKE '$filtro_sector%'";
            }
        }
        
        if (!empty($_GET['nombre'])) {
            $filtro_nombre = $_GET['nombre'];

            if (preg_match('/[a-zA-Z0-9]+/', $filtro_nombre)) {
                $cond .= " AND nom_org LIKE '%$filtro_nombre%'";
            }
        }
        
        $fss = array('deptos','sectores_filtro');

        $filters['nombre'] = array('id' => 'nombre',
                                 'label' => 'Nombre',
                                 'type' => 'text',
                                 'value' => $filtro_nombre
                             );
        $filters['deptos'] = array('id' => 'departamento',
                                   'label' => 'Departamento',
                                   'type' => 'dropdown', 
                                   'value' => $filtro_depto,
                                   'options' => array(array('value' => 0,  'text' => 'Todos'))
                               );
        
        $filters['sectores_filtro'] = array('id' => 'sector',
                                   'label' => 'Sector',
                                   'type' => 'dropdown', 
                                   'value' => $filtro_sector,
                                   'options' => array(array('value' => 0,  'text' => 'Todos'))
                               );
        
        $titles = array(
                      array('index' => 'nombre', 'text' => 'Organización'), 
                      array('index' => 'dir', 'text' => 'Dirección'), 
                      array('index' => 'tel', 'text' => 'Tel'), 
                      array('index' => 'departamento', 'text' => 'Departamento'), 
                      array('index' => 'ciudad', 'text' => 'Ciudad'), 
                      array('index' => 'sectores', 'text' => 'Sectores')
                  );

        // Filtros
        $deptos = array();
        $sql = "SELECT DISTINCT id_depto, nom_depto 
                FROM departamento depto
                INNER JOIN municipio AS mun USING(id_depto)
                INNER JOIN organizacion AS org ON org.id_mun_sede=mun.id_mun
                WHERE $cond";

        $rs = $conn->OpenRecordset($sql);
        while ($row = $conn->FetchRow($rs)) {
            $deptos[$row[0]] = $row[1];
        }

        $sectores_filtro = array();
        $sql = "SELECT DISTINCT id_comp, nom_comp_es
                FROM sector_org
                INNER JOIN organizacion USING(id_org)
                INNER JOIN sector USING(id_comp)
                WHERE $cond";

        $rs = $conn->OpenRecordset($sql);
        while ($row = $conn->FetchRow($rs)) {
            $sectores_filtro[$row[0]] = $row[1];
        }

        // Lista de organizaciones
        $sql = "SELECT id_org, nom_org, dir_org, tel1_org, id_mun_sede,
                GROUP_CONCAT(DISTINCT nom_comp_es) AS sectores
                FROM organizacion
                LEFT JOIN sector_org USING(id_org)
                RIGHT JOIN sector USING(id_comp)
                WHERE $cond
                GROUP BY id_org
                ORDER BY nom_org
                LIMIT $limit
                ";

        $rs = $conn->OpenRecordset($sql);
        while ($_o = $conn->FetchObject($rs)) {
            
            $nombre = $_o->nom_org;
            $tel = $_o->tel1_org;
            $dir = $_o->dir_org;
            $sectores = $_o->sectores;

            if (!empty($_o->id_mun_sede)) {

                $_mun = $mun_dao->Get($_o->id_mun_sede);

                $ciudad = $_mun->nombre;
                
                $id_depto = $_mun->id_depto;

                $_depto = $depto_dao->Get($id_depto);

                $departamento = $_depto->nombre;
                
            }
            
            $ar = compact('nombre','tel','dir',
                          'departamento','ciudad','sectores');
            
            array_walk($ar, 'utf8ing');

            $rows[] = $ar;
        }
        
        // Ordena filters
        foreach($fss as $fs) {
            asort($$fs);
            foreach($$fs as $id => $n) {
            
                $filters[$fs]['options'][] = array('value' => $id,
                    'text' => utf8_encode($n));
            }
        }

    break;
        

}

if ($render_json) {
    $json = compact('render','title_block','filters','titles','rows','html');

    header('Content-type: application/json');
    echo json_encode($json);
} 



?>
