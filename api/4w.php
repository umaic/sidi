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

if (!isset($_GET['_c']) || $_GET['_c'] != 's1dicol-api' || !in_array($mod, array('presencia','totales','proyectos','apc','hdx','rss','p4w'))) {
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

	case 'proyectos':

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


	case 'apc':

		if (empty($_GET['fecha'])) {
			die('Falta fecha');
		}

		$fecha = substr($_GET['fecha'],0,4) . '-' . substr($_GET['fecha'],5,2). '-' . substr($_GET['fecha'],8,2);


		$sql = "SELECT 
p.id_proy AS Id,
p.cod_proy AS Codigo,
tp.nom_tipp AS Tipo,
p.nom_proy AS Nombre,
p.des_proy AS Descripcion,
ep.nom_estp AS Estado,
p.costo_proy AS Presupuesto_Total,
p.inicio_proy AS Fecha_Inicio,
p.fin_proy AS Fecha_Finalizacion,
p.duracion_proy AS Tiempo_de_Ejecucion,
ma.nom_moda AS Modalidad_de_Asistencia,
me.nom_mece AS Mecanismo_de_Entrega,
pb1.cant_p4w_b AS Total_beneficiarios_directos,
pb2.cant_p4w_b AS Total_beneficiarios_directos_mujeres,
pb3.cant_p4w_b AS Total_beneficiarios_directos_hombres,
pb4.cant_p4w_b AS Total_beneficiarios_indirectos,
pb5.cant_p4w_b AS Total_beneficiarios_indirectos_mujeres,
pb6.cant_p4w_b AS Total_beneficiarios_indirectos_hombres,
p.num_vic AS Victimas_del_conflicto,
p.num_afe AS Afectados_por_Desastres,
p.num_des AS Desmovilizados_Reinsertados,
p.num_afr AS Afrocolombianos,
p.num_ind AS Indigenas,
p.soportes AS URL_Soportes
FROM proyecto p
LEFT JOIN tipo_proy tp ON p.id_tipp=tp.id_tipp
LEFT JOIN estado_proy ep ON p.id_estp=ep.id_estp
LEFT JOIN modalidad_asistencia ma ON ma.id_moda=p.id_moda
LEFT JOIN mecanismo_entrega me ON me.id_mece=p.id_mece
LEFT JOIN p4w_beneficiario pb1 ON 
(pb1.id_proy=p.id_proy AND pb1.tipo_rel=1 AND pb1.genero_p4w_b IS NULL AND pb1.edad_p4w_b IS NULL)
LEFT JOIN p4w_beneficiario pb2 ON 
(pb2.id_proy=p.id_proy AND pb2.tipo_rel=1 AND pb2.genero_p4w_b='m' AND pb2.edad_p4w_b IS NULL)
LEFT JOIN p4w_beneficiario pb3 ON 
(pb3.id_proy=p.id_proy AND pb3.tipo_rel=1 AND pb3.genero_p4w_b='h' AND pb3.edad_p4w_b IS NULL)
LEFT JOIN p4w_beneficiario pb4 ON 
(pb4.id_proy=p.id_proy AND pb4.tipo_rel=2 AND pb4.genero_p4w_b IS NULL AND pb4.edad_p4w_b IS NULL)
LEFT JOIN p4w_beneficiario pb5 ON 
(pb5.id_proy=p.id_proy AND pb5.tipo_rel=2 AND pb5.genero_p4w_b='m' AND pb5.edad_p4w_b IS NULL)
LEFT JOIN p4w_beneficiario pb6 ON 
(pb6.id_proy=p.id_proy AND pb6.tipo_rel=2 AND pb6.genero_p4w_b='h' AND pb6.edad_p4w_b IS NULL)
WHERE ";

if (intval($_GET['v']) == 1) {
	$sql .= "CHAR_LENGTH(p.soportes) > 0 AND ";
}

		$sql .="
p.actua_proy >= '" .$fecha . "'";

		$result = $conn->OpenRecordset($sql);
		while ($row = $conn->FetchAssoc($result)){


			//CAD
			$sql10 = "SELECT t.id_tema AS Id, REGEXP_SUBSTR(t.nom_tema, '[0-9]{5}') AS Codigo, SUBSTRING(t.nom_tema, 7) AS Nombre
			FROM proyecto p
			LEFT JOIN proyecto_tema pt ON pt.id_proy=p.id_proy
			LEFT JOIN tema t ON t.id_tema=pt.id_tema
			WHERE t.id_clasificacion=6
			AND p.id_proy=".$row['Id'];
			$result10 = $conn->OpenRecordset($sql10);

			//No desplegar proyectos si CAD
			if (intval($_GET['v']) == 1)
			{
				if ($conn->RowCount($result10) == 0)
				{
					continue;
				}
			}


			//ODS
			$sql11 = "SELECT t.id_tema AS Id, trim(REGEXP_SUBSTR(t.nom_tema, '[0-9]+\.?[0-9a-z]?\.?[0-9]?')) AS Codigo, SUBSTRING(t.nom_tema, 5) AS Nombre
			FROM proyecto p
			LEFT JOIN proyecto_tema pt ON pt.id_proy=p.id_proy
			LEFT JOIN tema t ON t.id_tema=pt.id_tema
			WHERE t.id_clasificacion=7
			AND p.id_proy=".$row['Id'];
			$result11 = $conn->OpenRecordset($sql11);

			//No desplegar proyectos si ODS
			if (intval($_GET['v']) == 1)
			{
				if ($conn->RowCount($result11) == 0)
				{
					continue;
				}
			}


			$json[$row['Id']] = $row;

			//Ejecutor
			$sql2 = "SELECT o.id_org AS Id, o.nom_org AS Nombre, nit_org AS NIT
					FROM proyecto p
					LEFT JOIN vinculorgpro vop ON vop.id_proy=p.id_proy
					LEFT JOIN organizacion o ON o.id_org=vop.id_org
					WHERE vop.id_tipo_vinorgpro=1
					AND p.id_proy=".$row['Id'];
			$result2 = $conn->OpenRecordset($sql2);
			$row2 = $conn->FetchAssoc($result2);
			$json[$row['Id']]['Organizacion_Ejecutora'][] = $row2;

			//Implementador
			$sql3 = "SELECT o.id_org AS Id, o.nom_org AS Nombre, nit_org AS NIT
					FROM proyecto p
					LEFT JOIN vinculorgpro vop ON vop.id_proy=p.id_proy
					LEFT JOIN organizacion o ON o.id_org=vop.id_org
					WHERE vop.id_tipo_vinorgpro=3
					AND p.id_proy=".$row['Id'];
			$result3 = $conn->OpenRecordset($sql3);
			while ($row3 = $conn->FetchAssoc($result3))
			{
				$json[$row['Id']]['Organizacion_Implementadora'][] = $row3;
			}

			//Donante
			$sql4 = "SELECT o.id_org AS Id, o.nom_org AS Nombre, nit_org AS NIT, valor_aporte AS Aporte
					FROM proyecto p
					LEFT JOIN vinculorgpro vop ON vop.id_proy=p.id_proy
					LEFT JOIN organizacion o ON o.id_org=vop.id_org
					WHERE vop.id_tipo_vinorgpro=2
					AND p.id_proy=".$row['Id'];
			$result4 = $conn->OpenRecordset($sql4);
			while ($row4 = $conn->FetchAssoc($result4))
			{
				$json[$row['Id']]['Organizacion_Donante'][] = $row4;
			}

			//Contacto
			$sql5 = "SELECT c.id_con AS Id, CONCAT(c.nom_con, ' ', c.ape_con) AS Nombre, c.cel_con AS Telefono
					FROM proyecto p
					LEFT JOIN contacto c ON c.id_con=p.id_con
					WHERE p.id_proy=".$row['Id'];
			$result5 = $conn->OpenRecordset($sql5);
			while ($row5 = $conn->FetchAssoc($result5))
			{
				$json[$row['Id']]['Contacto_en_Terreno'][] = $row5;
			}

			//Municipios
			$sql6 = "SELECT m.id_mun AS Codigo,m.nom_mun AS Nombre, m.id_depto AS Codigo_Departamento, d.nom_depto AS Departamento
			FROM proyecto p
			LEFT JOIN mun_proy mp ON mp.id_proy=p.id_proy
			LEFT JOIN municipio m ON m.id_mun=mp.id_mun
			LEFT JOIN departamento d ON d.id_depto=m.id_depto
			WHERE p.id_proy=".$row['Id'];
			$result6 = $conn->OpenRecordset($sql6);
			while ($row6 = $conn->FetchAssoc($result6))
			{
				$json[$row['Id']]['Municipios'][] = $row6;
			}


			//Sector Humanitario
			$sql7 = "SELECT  t.id_tema AS Id,t.nom_tema AS Nombre
			FROM proyecto p
			LEFT JOIN proyecto_tema pt ON pt.id_proy=p.id_proy
			LEFT JOIN tema t ON t.id_tema=pt.id_tema
			WHERE t.id_clasificacion=2
			AND p.id_proy=".$row['Id'];
			$result7 = $conn->OpenRecordset($sql7);
			while ($row7 = $conn->FetchAssoc($result7))
			{
				$json[$row['Id']]['Sector_Humanitario'][] = $row7;
			}

			//Resultado UNDAF
			$sql8 = "SELECT  t.id_tema AS Id,t.nom_tema AS Nombre
			FROM proyecto p
			LEFT JOIN proyecto_tema pt ON pt.id_proy=p.id_proy
			LEFT JOIN tema t ON t.id_tema=pt.id_tema
			WHERE t.id_clasificacion=4
			AND p.id_proy=".$row['Id'];
			$result8 = $conn->OpenRecordset($sql8);
			while ($row8 = $conn->FetchAssoc($result8))
			{
				$json[$row['Id']]['Resultado_UNDAF'][] = $row8;
			}

			//Acuerdos de Paz
			$sql9 = "SELECT  t.id_tema AS Id, REGEXP_SUBSTR(t.nom_tema, '[0-9]+\.[0-9]+\.[0-9]+') AS Codigo, SUBSTRING(t.nom_tema, 7) AS Nombre
			FROM proyecto p
			LEFT JOIN proyecto_tema pt ON pt.id_proy=p.id_proy
			LEFT JOIN tema t ON t.id_tema=pt.id_tema
			WHERE t.id_clasificacion=5
			AND p.id_proy=".$row['Id'];
			$result9 = $conn->OpenRecordset($sql9);
			while ($row9 = $conn->FetchAssoc($result9))
			{
				$json[$row['Id']]['Acuerdos_de_Paz'][] = $row9;
			}

			while ($row10 = $conn->FetchAssoc($result10))
			{
				$json[$row['Id']]['CAD'][] = $row10;
			}

			while ($row11 = $conn->FetchAssoc($result11))
			{
				$json[$row['Id']]['ODS'][] = $row11;
			}

		}

		//$json[0] = ["_comment" => trim($sql)];


		header('Content-type: application/json; charset=UTF-8');
		header("Pragma: no-cache");
		header("Expires: 0");

		echo json_encode (array_values($json), JSON_UNESCAPED_UNICODE);

		break;

    case 'rss':

        $sql = "
SELECT id_proy AS id, nom_proy AS title, actua_proy AS pubDate
FROM proyecto
WHERE YEAR(actua_proy)=2018
ORDER BY actua_proy DESC";

        $result = $conn->OpenRecordset($sql);

        header('Content-type: application/rss+xml; charset=UTF-8');
        header("Pragma: no-cache");
        header("Expires: 0");

        $rss = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
        $rss .= "<rss version=\"2.0\">\r\n";
        $rss .= "<channel>\r\n";
        $rss .= "  <title>UMAIC - SIDI 4W</title>\r\n";
        $rss .= "  <link>https://sidi.umaic.org</link>\r\n";
        $rss .= "  <language>es-co</language>\r\n";
        $rss .= "  <managingEditor>4w@umaic.org</managingEditor>\r\n";
        $rss .= "  <webMaster>ict@umaic.org</webMaster>\r\n";
        $rss .= "  <description>Proyectos 4w actualizados</description>\r\n";

        while ($row = $conn->FetchAssoc($result)) {

            $rss .= "  <item>\r\n";
            $rss .= "    <title>" . htmlspecialchars(trim($row['title']), ENT_XML1, 'UTF-8') . "</title>\r\n";
            $rss .= "    <description></description>\r\n";
            $rss .= "    <link>http://sidi.umaic.org/sissh/admin/index.php?m_e=p4w&amp;accion=actualizar&amp;class=P4wDAO&amp;id=" . $row['id'] . "</link>\r\n";
            $rss .= "    <pubDate>" . date("D, d M Y H:i:s O", strtotime($row['pubDate'])) . "</pubDate>\r\n";
            $rss .= "  </item>\r\n";
        }

        $rss .= "</channel>\r\n";
        $rss .= "</rss>\r\n";

        echo $rss;


        break;

	case 'p4w':

		if (empty($_GET['yyyy'])) {
			die('Faltan yyyy');
		}

		$yyyy = intval($_GET['yyyy']);

		if ($yyyy > 2055 OR $yyyy < 2004) {
			die('Faltan yyyy');
		}

		$sql = "
 	SELECT
	  id,
      id_proy
      ,si_proy,costo_proy
      ,num_munic
	  ,(costo_proy/num_munic) AS pres_x_mun
      ,inicio_proy, fin_proy
      ,meses_reportados
      ,meses_calculados
      ,srp_proy
      ,ejecutor
      ,sig_ejecutor
      ,sector_humanitario
      ,ods
	FROM
  	(

    SELECT
    concat(p.id_proy,m.id_mun) AS Id,
    p.id_proy,
    p.si_proy
    ,(
    	SELECT Count(id_mun) FROM mun_proy mp2 WHERE mp2.id_proy=p.id_proy
    ) AS num_munic
    ,m.id_mun,m.nom_mun,d.id_depto,d.nom_depto
    ,p.costo_proy
    ,p.inicio_proy, p.fin_proy
    ,p.duracion_proy AS meses_reportados
	,TIMESTAMPDIFF(MONTH, p.inicio_proy, p.fin_proy) AS meses_calculados
    ,p.srp_proy
    ,(
    	SELECT o.nom_org
					FROM proyecto p2
					LEFT JOIN vinculorgpro vop ON vop.id_proy=p2.id_proy
					LEFT JOIN organizacion o ON o.id_org=vop.id_org
					WHERE vop.id_tipo_vinorgpro=1
					AND p2.id_proy=p.id_proy
    ) AS ejecutor
    ,(
    	SELECT o.sig_org
					FROM proyecto p2
					LEFT JOIN vinculorgpro vop ON vop.id_proy=p2.id_proy
					LEFT JOIN organizacion o ON o.id_org=vop.id_org
					WHERE vop.id_tipo_vinorgpro=1
					AND p2.id_proy=p.id_proy
    ) AS sig_ejecutor       
    ,(
    	SELECT GROUP_CONCAT(CONCAT(t.nom_tema,' (', pt.desc_proy_tema , ')') ORDER BY t.nom_tema ASC)
			FROM proyecto p3
			LEFT JOIN proyecto_tema pt ON pt.id_proy=p3.id_proy
			LEFT JOIN tema t ON t.id_tema=pt.id_tema
			WHERE t.id_clasificacion=2
			AND p3.id_proy=p.id_proy
    ) AS sector_humanitario    
    , (
        SELECT GROUP_CONCAT(trim(REGEXP_SUBSTR(t4.nom_tema, '[0-9]+\.?[0-9a-z]?\.?[0-9]?')) ORDER BY t4.nom_tema ASC)
			FROM proyecto p6
			LEFT JOIN proyecto_tema pt3 ON pt3.id_proy=p6.id_proy
			LEFT JOIN tema t4 ON t4.id_tema=pt3.id_tema
			WHERE t4.id_clasificacion=7
			AND p6.id_proy=p.id_proy
    ) AS ods
    FROM proyecto p
    LEFT JOIN mun_proy mp ON mp.id_proy=p.id_proy
    LEFT JOIN municipio m ON m.id_mun=mp.id_mun
    LEFT JOIN departamento d ON d.id_depto=m.id_depto    
    WHERE 
        -- p.id_proy IN (35289) AND (
        m.id_mun IS NOT NULL AND
    	YEAR(p.inicio_proy) = ".$yyyy." 
    	OR YEAR(p.fin_proy) = ".$yyyy." 
        OR (YEAR(p.inicio_proy) < ".$yyyy." AND YEAR(p.fin_proy) > ".$yyyy.")

        -- )
     
    
) AS t2
  WHERE num_munic>0
  GROUP BY id_mun,id_proy
  ORDER BY id_mun,id_proy
      

		";

		$result = $conn->OpenRecordset($sql);
		while ($row = $conn->FetchAssoc($result)){
			$json[$row['Id']] = $row;

		}


		header('Content-type: application/json; charset=UTF-8');
		header("Pragma: no-cache");
		header("Expires: 0");

		echo json_encode (array_values($json), JSON_UNESCAPED_UNICODE);

		break;

}

?>
