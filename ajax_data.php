<?
session_start();

// PHP >= 5.3
date_default_timezone_set('America/Bogota');

//LIBRERIAS
require_once "admin/lib/common/mysqldb.class.php";

$object = isset($_GET["object"]) ? $_GET["object"] : $_POST["object"];

switch ($object){

	//Lista ComboBox de Municipios, la misma funcion de admin/ajax_data.php
	case 'comboBoxMunicipio':

		//LIBRERIAS
		require_once "admin/lib/dao/municipio.class.php";

		$dao_ajax = New MunicipioAjax();

		if (isset($_GET['id_deptos']) && isset($_GET['multiple'])){
			$condicion = $_GET['id_deptos'];
			$multiple = $_GET['multiple'];
			$titulo = (isset($_GET['titulo'])) ? $_GET['titulo'] : 1 ;
			$separador_depto = (isset($_GET['separador_depto'])) ? $_GET['separador_depto'] : 1 ;
			$id_name = (isset($_GET['id_name'])) ? $_GET['id_name'] : 'id_muns' ;

			$dao_ajax->comboBoxMunicipio($condicion,$multiple,$titulo,$separador_depto,$id_name);
		}
	break;

		//Lista ComboBox de Municipios
	case 'checkBoxMunicipio':

		//LIBRERIAS
		require_once "admin/lib/dao/municipio.class.php";

		$dao_ajax = New MunicipioAjax();

		$condicion = $_GET['id_deptos'];
		$titulo = (isset($_GET['titulo'])) ? $_GET['titulo'] : 1 ;
		$separador_depto = (isset($_GET['separador_depto'])) ? $_GET['separador_depto'] : 1 ;
		$id_name = (isset($_GET['id_name'])) ? $_GET['id_name'] : 'id_muns' ;
		$id_muns_chk = $_GET['id_muns_chk'];

		$dao_ajax->checkBoxMunicipio($condicion,$titulo,$separador_depto,$id_name,$id_muns_chk);
		break;

	//Consulta la extensi�n en el mapa de acuerdo al depto
	case 'getExtentByDepto';

		//LIBRERIAS
		include("admin/lib/common/postgresdb.class.php");
		include("admin/lib/common/mapserver.class.php");

		$mapserver = New Mapserver();

		$id_depto = $_GET['id_depto'];

		//Extension retornada en arreglo array(xmin,xmax,ymin,ymax)
		$extent = $mapserver->getExtentEnvelopeDepto($id_depto);

		//Para mscross xmin,xmax,ymin,ymax
		echo $extent[0].",".$extent[1].",".$extent[2].",".$extent[3];

	break;

	//Consulta la extensi�n en el mapa de acuerdo al mpio
	case 'getExtentByMpio';

		//LIBRERIAS
		include("admin/lib/common/postgresdb.class.php");
		include("admin/lib/common/mapserver.class.php");

		$mapserver = New Mapserver();

		$id_mpio = $_GET['id_mpio'];
		$extent_orig = $_GET['extent_orig'];

		//Extension retornada en arreglo array(xmin,xmax,ymin,ymax)
		$extent = $mapserver->getExtentEnvelopeMpio($id_mpio,$extent_orig);

		//Para mscross xmin,xmax,ymin,ymax
		echo $extent[0].",".$extent[1].",".$extent[2].",".$extent[3];

	break;

	//Consulta la extensi�n en el mapa de acuerdo a la localizaci�n (regi�n,etc)
	case 'getExtentByLoc';

		//LIBRERIAS
		include("admin/lib/common/postgresdb.class.php");
		include("admin/lib/common/mapserver.class.php");

		$mapserver = New Mapserver();

		$id = $_GET['id'];

		//Extension retornada en arreglo array(xmin,xmax,ymin,ymax)
		$extent = $mapserver->getExtentEnvelopeLoc($id);

		//Para mscross xmin,xmax,ymin,ymax
		echo $extent[0].",".$extent[1].",".$extent[2].",".$extent[3];

	break;


	//Modifica el .map para la consulta de el map area - HTML, del mapa municipal del depto seleccionado para mapserver_consulta
	case 'setMapFile';

		//LIBRERIAS
		include("admin/lib/common/archivo.class.php");
		include("admin/lib/common/postgresdb.class.php");
		include("admin/lib/common/mapserver.class.php");
		include("admin/lib/dao/depto.class.php");
		include("admin/lib/model/depto.class.php");

		//INICIALIZA VARIABLES
		$mapserver = New Mapserver();
		$archivo = New Archivo();
		$depto_dao = New DeptoDAO();
		$document_root = $_SERVER["DOCUMENT_ROOT"]."/";

		$id_depto = $_GET['id_depto'];
		$width = $_GET['width'];
		$height = $_GET['height'];

		//Extension retornada en arreglo array(xmin,xmax,ymin,ymax)
		$extent = $mapserver->getExtentEnvelopeDepto($id_depto);

		$xmin = $extent[0];
		$ymin = $extent[2];
		$xmax = $extent[1];
		$ymax = $extent[3];

		//Para map file: xmin,ymin,xmax,ymax
		$extent = $xmin." ".$ymin." ".$xmax." ".$ymax;

		$depto_vo = $depto_dao->Get($id_depto);
		$depto_nombre = $depto_vo->nombre;

		//Elimina tildes
		$depto_nombre = strtoupper(strtr($depto_nombre,"������","aeioun"));

		//Reemplaza Valle del Cauca por Valle
		if (strtolower($depto_nombre) == 'valle del cauca'){
			$depto_nombre = "VALLE";
		}

		$cont = 'MAP
				NAME "MPIOS"
				SIZE '.$width.' '.$height.'
				IMAGECOLOR 252 243 208
				EXTENT '.$extent.'
				SHAPEPATH "'.$document_root.'sissh/images/shapes/"

				WEB
					IMAGEPATH "'.$document_root.'tmp/"
					IMAGEURL "/tmp/"
				END

				LAYER
					NAME "mpios"
					TYPE line
					DATA "colmun3corregido"
					FILTERITEM "departamen"
					FILTER "'.$depto_nombre.'"
					TEMPLATE "bodytemplate_mpio.html"
					#HEADER "imapheader_mpio.html"
					#FOOTER "imapfooter_mpio.html"

				END # end layer mpios

			END';

		//abre el archivo con el puntero al comienzo
		$fp = $archivo->Abrir('consulta/test_mapserver/test_map_area_mpio.map','w+');
		$archivo->Escribir($fp,$cont);
		$archivo->Cerrar($fp);

	break;

	case 'checkCacheMapArea':
		//LIBRERIAS
		include("admin/lib/common/archivo.class.php");

		//CONF
		include("admin/config.php");

		$archivo = New Archivo();
		$id = $_GET["id_depto"];
		//$caso = $_GET["caso"];
		$caso = 'mpio';

		switch ($caso){
			case 'mpio':
				$nom_file = "map_area_$id";
			break;

			case 'region':
				$nom_file = "map_area_region_$id";
			break;
		}

		$file_name_map_area = $conf['mapserver']['dir_cache_consulta']."/$nom_file.txt";

		if ($archivo->Existe($file_name_map_area)){
			$fp = $archivo->Abrir($file_name_map_area,'r');
			$html = $archivo->LeerEnString($fp,$file_name_map_area);

			echo $html;

			$archivo->Cerrar($fp);
		}
		else{
			echo '';
		}

	break;
	case 'saveCacheMapArea':

		//CONF
		include("admin/config.php");

		$dir_cache = $conf['mapserver']['dir_cache_consulta'];
		$id_depto = $_GET["id_depto"];

		exec ("wget -nv -O ".$dir_cache."/map_area_$id_depto.txt \"http://localhost/cgi-bin/mapserv?map=".$_SERVER["DOCUMENT_ROOT"]."/sissh/consulta/test_mapserver/test_map_area_mpio.map&qlayer=mpios&mode=nquery&searchmap=true\"");

	break;


	case 'generarPerfilOnlineWebSite':

		include_once("consulta/lib/libs_mapa_i.php");
		include_once("admin/lib/common/graphic.class.php");
		include_once("admin/lib/common/imageSmoothArc.php");
		include_once("admin/lib/common/imageSmoothLine.php");

		$sissh = New SisshDAO();
		$depto_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();

		if (isset($_GET["id_depto"]) && isset($_GET["id_mun"]) && isset($_GET["formato"])){
			$id_depto = $_GET["id_depto"];
			$id_mun = $_GET["id_mun"];
			$formato = $_GET["formato"];

			if ($id_mun != 0){
				$ids = $mun_dao->GetAllArrayID('','');
				$id_ubicacion = $id_mun;
			}
			else{
				$ids = $depto_dao->GetAllArrayID('');
				$id_ubicacion = $id_depto;
			}

			if (ereg("[0-9]{2,5}",$id_ubicacion) && in_array($id_ubicacion,$ids)){
				$sissh->Minificha($id_depto,$id_mun,$formato);
			}
		}

	break;

	case 'borrarProyectoHome':

		include_once("consulta/lib/libs_proyecto.php");
		$proy_dao = new ProyectoDAO();

		if (is_numeric($_GET["id"])){
			$id = $_GET["id"];

			//$proy_dao->Borrar($id);
		}

	break;

	case 'listarProyectoHomeTabs':

		include_once("consulta/lib/libs_proyecto.php");
		$proy_dao = new ProyectoDAO();

		if (is_numeric($_GET["id_org"])){
			$id = $_GET["id_org"];

			$proy_dao->listarProyectoHomeTabs($id);
		}

	break;

	case 'enviarSugerencia':

		//COMMON
		//include_once($_SERVER["DOCUMENT_ROOT"]."sissh/admin/lib/common/mysqldb.class.php");
		include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/control/ctlsugerencia.class.php");

		//MODEL
		include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/model/sugerencia.class.php");
		include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/model/usuario.class.php");

		//DAO
		include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/dao/sugerencia.class.php");
		include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/dao/usuario.class.php");

		$sugerencia_dao = New SugerenciaDAO();
		$sugerencia = new Sugerencia();

		$sugerencia->id_usuario = $_SESSION["id_usuario_s"];
		$sugerencia->texto = $_POST["sugerencia"];
		$sugerencia->modulo = $_POST["modulo"];

		$sugerencia_dao->Insertar($sugerencia);

	break;

	case 'enviarSugerenciaUndaf':

		//COMMON
		include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/control/ctlsugerencia.class.php");

		//MODEL
		include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/model/sugerencia.class.php");
		include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/model/usuario.class.php");

		//DAO
		include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/dao/sugerencia.class.php");
		include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/dao/usuario.class.php");

		$sugerencia_dao = New SugerenciaDAO();
		$sugerencia = new Sugerencia();

		$sugerencia->id_usuario = $_SESSION["id_usuario_s"];
		$sugerencia->texto = $_POST["sugerencia"];
		$sugerencia->modulo = '';

		$sugerencia_dao->Insertar($sugerencia,1);

	break;

	case 'grafo':

		include("consulta/lib/libs_org.php");

		$org_dao = new OrganizacionDAO();
		$color_papa = '0099ff';
		$color = '0000ff';
		$textcolor = '000000';
		$id = $_GET["id"];

		$papa = $org_dao->Get($id);
		$ofis = $org_dao->GetAllArray('id_org_papa='.$id,'','');

		$xml = '<?xml version="1.0"?>
			<graph title="Agencias SNU" bgcolor="ffffff" linecolor="cccccc" viewmode="display" width="725" height="400">
			<node id="n0" text="'.$papa->sig.'" color="'.$color_papa.'" textcolor="'.$textcolor.'"/>';

		$o = 1;
		foreach ($ofis as $ofi){
			$xml .= '<node id="n'.$o.'" text="'.$ofi->sig.'" link="t/ver.php?class=OrganizacionDAO&method=Ver&param='.$ofi->id.'" color="'.$color.'" textcolor="'.$textcolor.'"/>';
			$o++;
		}

		for($i=1;$i<$o;$i++){
			$xml .= '<edge sourceNode="n0" targetNode="n'.$i.'" label="" textcolor="555555"/>';
		}

		$xml .= '</graph>';

		echo $xml;

	break;

    case 'erf_orgs':

        include("consulta/lib/libs_org.php");

        $org_dao = new OrganizacionDAO();
        $mun_dao = new MunicipioDAO();

        if (!isset($_GET['t']) || !isset($_GET['c']) || !is_numeric($_GET['c']))   die('Faltan par�metros');

        $t = $_GET['t'];

        switch($_GET['c']){

            // Retorna arreglo con nombre-id
            case '1':
                $orgs = $org_dao->GetAllArray("(nom_org LIKE '%$t%' OR sig_org LIKE '%$t%')
                    AND nit_org IS NOT NULL
                    AND LENGTH(nit_org) > 0
                    AND PU_MAIL_ORG IS NOT NULL
                    AND N_REP_ORG IS NOT NULL
                    AND TEL1_ORG IS NOT NULL
                    AND DIR_ORG IS NOT NULL
                    ",'','');

                $org_r['results'] = array();
                foreach ($orgs as $org){
                    $org_r['results'][] = array('value' => $org->id, 'label' => utf8_encode($org->nom));
                }
            break;

            // Retorna la info. basica dado el id
            case '2':
                $org = $org_dao->get($t);
                $mun_nom = $mun_dao->getName($org->id_mun_sede);
                $org_r['results'][] = array('name' => utf8_encode($org->nom),
                                            'nit' => $org->nit,
                                            'representant' => (!empty($org->n_rep) ? utf8_encode($org->n_rep) : ''),
                                            'email' => $org->pu_email,
                                            'city' => utf8_encode($mun_nom),
                                            'country' => 'CO',
                                            'phone' => (!empty($org->tel1) ? utf8_encode($org->tel1) : ''),
                                            'phone_2' => (!empty($org->tel2) ? utf8_encode($org->tel2) : ''),
                                            'address' => (!empty($org->dir) ? utf8_encode($org->dir) : ''),
                                            'fax' => (!empty($org->fax) ? utf8_encode($org->fax) : ''),

                                            );
            break;
        }

        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');

        echo json_encode($org_r);

    break;

	case 'getProysMapa4w':

		include_once("consulta/lib/libs_p4w.php");
		$dao = new P4wAjax();

        $dao->getProysMapa4w($_GET);

	break;

    case 'setFiltrosProysMapa4w':

		include_once("consulta/lib/libs_p4w.php");
        $dao = new P4wDAO();

        $dao->_manageSessionFilter($_GET);


	break;

    //Lista ComboBox de Municipios Mapa
	case 'checkboxMpiosMapa4w':

		//LIBRERIAS
		require_once "admin/lib/dao/municipio.class.php";

		$dao_ajax = New MunicipioAjax();

		$condicion = $_GET['id_depto'];

		$dao_ajax->checkboxMpiosMapa4w($condicion);
    break;

    // Reportes Conteo 4W
	case 'reportesConteo4w':

		include_once("admin/lib/dao/factory.class.php");
		include_once("consulta/lib/libs_p4w.php");

        $dao = new P4wAjax();

        header("Content-Type: text/csv");
	    header("Content-Disposition: attachment; filename=\"Proyectos4W_OCHA.csv\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        $dao->reporteConteo($_GET);

    break;

    // Reportes Conteo 4W
	case 'reporteProyectos4w':

		include_once("admin/lib/dao/factory.class.php");
		include_once("consulta/lib/libs_p4w.php");

        header("Content-Type: text/csv");
	    header("Content-Disposition: attachment; filename=\"Proyectos4W_OCHA.csv\"");

        echo $_SESSION['csv'];

    break;

    // Reportes por mes presupuesto y beneficiarios 4W
    case 'reporteXMesPresBenef4w':

        include_once("admin/lib/dao/factory.class.php");
        include_once("consulta/lib/libs_p4w.php");

        $dao = new P4wAjax();
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=\"reporte_mes_pres_benef_4W_OCHA.csv\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        $dao->reporteXMesPresBenef();

    break;

    // Ficha PDF 4W
	case 'fichaProyectos4w':

		include_once("admin/lib/dao/factory.class.php");
		include_once("consulta/lib/libs_p4w.php");

        $dao = new P4wAjax();

        $dao->fichaPDF();
        header("Location: ".$_SESSION['4w_ficha_url']);
    break;

    // Actualiza estados
	case 'updateEstadoProyectos':

		include_once("admin/lib/dao/factory.class.php");
		include_once("consulta/lib/libs_p4w.php");

        $dao = new P4wDAO();

        $dao->updateEstadoProyectos();
    break;
        
    // Verifica cobertura de proyectos a nivel municipal
    // cuando han sido marcado solo los departamentos
    case 'checkMunsProyectos':

        include_once("consulta/lib/libs_p4w.php");

        $dao = new P4wDAO();

        $dao->checkMunsProyectos();
    break;

}

?>
