<?php

//INICIA LA SESION
@session_start();

//LIBRERIAS
if (!isset($dir)) {
    $dir = $_SERVER["DOCUMENT_ROOT"].dirname($_SERVER['SCRIPT_NAME']);
}
require_once $dir."/lib/dao/ajax.class.php";

$ajax = New AjaxDAO();

$object = $_GET["object"];

switch ($object){
	case 'graficaConsulta' :
		$case = $_GET["case"];
		$id_dato = $_GET["id_dato"];
		$click_state = $_GET["click_state"];
		$ajax->graficar($case,$id_dato,$click_state);
		break;

	case 'comboDatoSectorial' :
		$id_cat = $_GET["id_cat"];
		$ajax->comboDatoSectorialResumen($id_cat);
		break;

	case 'datoTablaResumen' :
		$id_cat = $_GET["id_cat"];
		$id_dato = $_GET["id_dato"];
		$f_ini = $_GET["f_ini"];
		$f_fin = $_GET["f_fin"];
		$accion = $_GET["accion"];
		$ajax->agregarDatoResumen($id_cat,$id_dato,$f_ini,$f_fin,$accion);
		break;

	//OCURRENCIAS EN ALIMENTACION ORG Y EN BUSQUEDAS
	case 'ocurrenciasOrg' ;
		$s = $_GET["s"];
		$case = $_GET["case"];
		$donde = $_GET["donde"];
		$busqueda = 0;
		if (isset($_GET["busqueda"]))	$busqueda = 1;
		$ajax->ocurrenciasOrg($s,$case,$busqueda,$donde);
		break;

	//OCURRENCIAS EN MAPAS DE ORGS
	case 'ocurrenciasOrgMapa' ;
		$s = $_GET["s"];
		$case = $_GET["case"];
		$donde = $_GET["donde"];
		$busqueda = 1;
		$ajax->ocurrenciasOrgMapa($s,$case,$busqueda,$donde);
		break;

    //OCURRENCIAS DE ORGS EN 4W ALIMENTACION
	case 'ocurrenciasOrg4wA' ;
		$s = $_GET["s"];
		$inom = $_GET["inom"];
		$iid = $_GET["iid"];
		$inner = $_GET["inner"];
		$ajax->ocurrenciasOrg4wA($s, $inom, $iid, $inner);
	break;

    //OCURRENCIAS DE CONTACTOS EN 4W ALIMENTACION
	case 'ocurrenciasCon4wA' ;
		$s = $_GET["s"];
		$inom = $_GET["inom"];
		$iid = $_GET["iid"];
		$inner = $_GET["inner"];
		$ajax->ocurrenciasCon4wA($s, $inom, $iid, $inner);
	break;

    //OCURRENCIAS DE ORG CON JQUERY+json
	case 'ocurrenciasOrgJson' ;
        $s = $_GET["s"];

        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/json');

        echo $ajax->ocurrenciasOrgJson($s);

        break;

    /**
	* Crea una opcion en el formulario de insertar contacto
	*/
	case 'crearContactoColOpcion' ;
        $id_col = $_GET["id_col"];
        $val = $_GET["val"];

        echo $ajax->crearContactoColOpcion($id_col,$val);

        break;

    // P4W DASHBOARD SCROLL LOADER
	case 'dashboardScrollLodaer4w' ;

        //LIBRERIAS
		require_once $dir."/lib/dao/p4w.class.php";

		$ajax = New P4wAjax();
        $sr = $_GET["sr"];
		$proys = $ajax->getProsDashboard($sr, $_GET["t"], $_GET["undaf"]);

        include('p4w/lista_proys.php');

	break;

	//GRAFICA DE ORGS POR CONTEO DE TIPO, ENFOQUE, SECTOR, POBLACION - GRAFICAS Y RESUMENES
	case 'graficaConteoOrg';

		//LIBRERIAS
		require_once $dir."/lib/dao/org.class.php";

		$org_ajax = New OrganizacionAjax();

		$depto = $_GET['depto'];
		$ubicacion = $_GET['ubicacion'];
		$graficar_por = $_GET['graficar_por'];
		$chart = $_GET['chart'];
		$tipo_papa = $_GET['tipo_papa'];

		/*$sede = $_GET["sede"];
		  $cobertura = $_GET["cobertura"];*/
		$cnrr = $_GET["cnrr"];
		$consulta_social = $_GET["consulta_social"];

		//LOG
		require_once $dir."/lib/dao/log.class.php";
		$log = New LogUsuarioDAO();
		$log->RegistrarFrontend($graficar_por,'Organizaciones');

		$org_ajax->graficaConteoOrg($ubicacion,$depto,$graficar_por,$cnrr,$consulta_social,$chart,$tipo_papa);

		break;

	//LISTADO DE ORGS POR CONTEO DE TIPO, ENFOQUE, SECTOR, POBLACION GENERADO DESDE LA GRAFICA
	case 'listadoConteoOrg';

		//LIBRERIAS
		require_once $dir."/lib/dao/org.class.php";

		$org_ajax = New OrganizacionAjax();

		$org_ajax->listadoConteoOrg($_SESSION["id_orgs"]);

		break;

	//GRAFICA DE ORGS POR TIPO, SECTOR, ENFOQUE O POBLACION PARA DEPTOS O MPIOS DE UN DEPTO
	case 'graficaConteoOrgDeptoMpio';

		//LIBRERIAS
		require_once $dir."/lib/dao/org.class.php";

		$org_ajax = New OrganizacionAjax();

		$depto = $_GET['depto'];
		$ubicacion = $_GET['ubicacion'];
		$graficar_por = $_GET['graficar_por'];
		$filtro_graficar_por = $_GET['filtro_graficar_por'];
		$id_ubicacion = 0;
		if (isset($_GET["id_ubicacion"]))	$id_ubicacion = $_GET["id_ubicacion"];

		$checked = 0;
		if (isset($_GET["checked"]))	$checked = $_GET["checked"];

		$sede = $_GET["sede"];
		$cobertura = $_GET["cobertura"];
		$cnrr = $_GET["cnrr"];
		$consulta_social = $_GET["consulta_social"];

		$org_ajax->graficaConteoOrgDeptoMpioSedeCobertura($ubicacion,$depto,$graficar_por,$filtro_graficar_por,$id_ubicacion,$checked,$sede,$cobertura,$cnrr,$consulta_social);

		break;

	//GRAFICA DE DESPLAZAMIENTO PARA GRAFICAS Y RESUMENES
	case 'GraficaResumenDesplazamiento';

		//LIBRERIAS
		require_once $dir."/lib/dao/desplazamiento.class.php";

		$des_ajax = New DesplazamientoAjax();

		$depto = $_GET['depto'];
		$ubicacion = $_GET['ubicacion'];
		$reporte = $_GET['reporte'];
		$fuentes = explode(",",$_GET["fuentes"]);
		$ini = $_GET["f_ini"];
		$fin = $_GET["f_fin"];
		$exp_rec = $_GET["exp_rec"];
		$chart = $_GET['chart'];
		$ejex = $_GET["ejex"];
		$dato_para_reporte_4_despla = $_GET["dato_para_reporte_4_despla"];

		//LOG
		if (!isset($_GET['api'])){
			require_once $dir."/lib/dao/log.class.php";
			$log = New LogUsuarioDAO();
			$log->RegistrarFrontend($reporte,'Desplazamiento');
		}

		$des_ajax->GraficaResumenDesplazamiento($reporte,$exp_rec,$fuentes,$depto,$ubicacion,$ini,$fin,$chart,$ejex,$dato_para_reporte_4_despla);

		break;

	//REPORTE DE DESPLAZAMIENTO PARA GRAFICAS Y RESUMENES
	case 'reporteGraResumenDesplazamiento';

		//LIBRERIAS
		require_once $dir."/lib/dao/desplazamiento.class.php";

		$des_ajax = New DesplazamientoAjax();

		$depto = $_GET['depto'];
		$ubicacion = $_GET['ubicacion'];
		$reporte = $_GET['reporte'];
		$fuentes = explode(",",$_GET["fuentes"]);
		$ini = $_GET["f_ini"];
		$fin = $_GET["f_fin"];
		$exp_rec = $_GET["exp_rec"];
		$ejex = $_GET["ejex"];
		$tipo_nal = $_GET["tipo_nal"];

		$des_ajax->reporteGraResumenDesplazamiento($reporte,$exp_rec,$fuentes,$depto,$ubicacion,$ini,$fin,$ejex,$tipo_nal);

		break;

	//GRAFICA DE MINA PARA GRAFICAS Y RESUMENES
	case 'GraficaResumenMina';

		//LIBRERIAS
		require_once $dir."/lib/dao/mina.class.php";

		$mina_ajax = New MinaAjax();

		$reporte = $_GET['reporte'];
		$depto = $_GET['depto'];
		$ubicacion = $_GET['ubicacion'];
		$filtro = explode("|",$_GET["filtros"]);
		$ini = $_GET["f_ini"];
		$fin = $_GET["f_fin"];
		$grafica = $_GET["grafica"];
		$ejex = $_GET["ejex"];
		$dato_para_reporte_5 = $_GET["dato_para_reporte_5_mina"];
		$acc_vic = $_GET["acc_vic"];

		//LOG
		if (!isset($_GET['api'])){
			require_once $dir."/lib/dao/log.class.php";
			$log = New LogUsuarioDAO();
			$log->RegistrarFrontend($reporte,'Mina');
		}

		$mina_ajax->GraficaResumenMina($reporte,$filtro,$depto,$ubicacion,$ini,$fin,$grafica,$ejex,$dato_para_reporte_5,$acc_vic);

		break;

	//REPORTE DE MINA PARA GRAFICAS Y RESUMENES
	case 'reporteGraResumenMina';

		//LIBRERIAS
		require_once $dir."/lib/dao/mina.class.php";

		$mina_ajax = New MinaAjax();

		$reporte = $_GET['reporte'];
		$depto = $_GET['depto'];
		$ubicacion = $_GET['ubicacion'];
		$filtro = explode("|",$_GET["filtros"]);
		$ini = $_GET["f_ini"];
		$fin = $_GET["f_fin"];
		$tipo_nal = $_GET["tipo_nal"];
		$ejex = $_GET["ejex"];
		$acc_vic = $_GET["acc_vic"];

		$mina_ajax->reporteGraResumenMina($reporte,$filtro,$depto,$ubicacion,$ini,$fin,$tipo_nal,$ejex,$acc_vic);

		break;

	//GRAFICA DE DATOS SECTORIALES PARA GRAFICAS Y RESUMENES
	case 'GraficaResumenDatos';

		//LIBRERIAS
		require_once $dir."/lib/dao/dato_sectorial.class.php";

		$dato_dao_ajax = New DatoSectorialAjax();

		$reporte = $_GET['reporte'];
		$id_dato = $_GET['id_dato'];
		$depto = $_GET['depto'];
		$ubicacion = $_GET['ubicacion'];
		$id_periodos = explode(",",$_GET["id_periodos"]);
		$chart = $_GET['chart'];
		$dato_para_reporte_4_dato = $_GET['dato_para_reporte_4_dato'];

		//LOG
		if (!isset($_GET['api'])){
			require_once $dir."/lib/dao/log.class.php";
			$log = New LogUsuarioDAO();
			$log->RegistrarFrontend($reporte,'Datos Sectoriales');
		}

		$dato_dao_ajax->GraficaResumenDatos($reporte,$id_dato,$depto,$ubicacion,$id_periodos,$chart,$dato_para_reporte_4_dato);

		break;

	//REPORTE DE DATOS SECTORIALES PARA GRAFICAS Y RESUMENES
	case 'reporteGraResumenDatos';

		//LIBRERIAS
		require_once $dir."/lib/dao/dato_sectorial.class.php";

		$dato_dao_ajax = New DatoSectorialAjax();

		$reporte = $_GET['reporte'];
		$id_dato = $_GET['id_dato'];
		$depto = $_GET['depto'];
		$ubicacion = $_GET['ubicacion'];
		$id_periodos = explode(",",$_GET["id_periodos"]);
		$tipo_nal = $_GET["tipo_nal"];
		$sep_decimal = $_GET["sep_decimal"];

		//LOG
		require_once $dir."/lib/dao/log.class.php";
		$log = New LogUsuarioDAO();
		$log->RegistrarFrontend($reporte,'Datos Sectoriales');

		$dato_dao_ajax->reporteGraResumenDatos($reporte,$id_dato,$depto,$ubicacion,$id_periodos,$tipo_nal,$sep_decimal);

		break;

	//GRAFICA DE EVENTOS C PARA GRAFICAS Y RESUMENES
	case 'GraficaResumenEventoC';

		//LIBRERIAS
		require_once $dir."/lib/dao/evento_c.class.php";

		$dao_ajax = New EventoConflictoAjax();

		$reporte = $_GET['reporte'];
		$num_records = $_GET['num_records'];
		$depto = $_GET['depto'];
		$ubicacion = $_GET['ubicacion'];
		$f_ini = $_GET["f_ini"];
		$f_fin = $_GET["f_fin"];
		$filtros['id_cat'] = $_GET["id_cat"];
		$filtros['id_scat'] = $_GET["id_scat"];
		$chart = $_GET['chart'];

		//LOG
		require_once $dir."/lib/dao/log.class.php";
		$log = New LogUsuarioDAO();
		$log->RegistrarFrontend($reporte,'Evento Conflicto');

		$dao_ajax->GraficaResumenEventoC($reporte,$num_records,$depto,$ubicacion,$f_ini,$f_fin,$chart,$filtros);

		break;

	case 'getAniosDatoSectorial';
	//LIBRERIAS
		require_once $dir."/lib/dao/dato_sectorial.class.php";

		$dato_dao_ajax = New DatoSectorialAjax();

		$id_dato = $_GET['id_dato'];

		$reporte = $_GET["reporte"];
		$dato_dao_ajax->getAniosDatoSectorial($id_dato,$reporte);
		break;

		case 'getAniosDatoSectorialToMapa';

		//LIBRERIAS
		require_once $dir."/lib/dao/dato_sectorial.class.php";

		$dato_dao_ajax = New DatoSectorialAjax();

		$id_dato = $_GET['id_dato'];
		$dato_dao_ajax->getAniosDatoSectorialToMapa($id_dato);
		break;

		case 'getDefinicionDatoSectorial';

		//LIBRERIAS
		require_once $dir."/lib/dao/dato_sectorial.class.php";

		$dato_dao_ajax = New DatoSectorialAjax();

		$id_dato = $_GET['id_dato'];
		echo $dato_dao_ajax->getDefinicionDatoSectorial($id_dato);
		break;

	case 'getAniosDatoSectorial_reporte2';
		//LIBRERIAS
		require_once $dir."/lib/dao/dato_sectorial.class.php";

		$dato_dao_ajax = New DatoSectorialAjax();

		$id_datos = $_GET["id_datos"];
		$reporte = $_GET["reporte"];
		echo $dato_dao_ajax->getAniosDatoSectorial($id_datos,$reporte);
		break;

	case 'getPeriodosDatoSectorial';
		//LIBRERIAS
		require_once $dir."/lib/dao/dato_sectorial.class.php";

		$dato_dao_ajax = New DatoSectorialAjax();

		$id_dato = $_GET['id_dato'];
		$formato = $_GET['formato'];
		$checked = $_GET['checked'];
		$num_items_f_c = $_GET['num_items_f_c'];
		$box_name = $_GET['box_name'];

		$dato_dao_ajax->getPeriodosDatoSectorial($id_dato,$formato,$num_items_f_c,$checked,$box_name);
		break;


	//LISTA LOS TIPO, ENFOQUES, POBLACIONES O SECTORES TEPS
	case 'teps';
		$graficar_por = $_GET['graficar_por'];
		$ajax->teps($graficar_por);
		break;

		//Lista ComboBox de Categoria de Datos Sectoriales
	case 'comboBoxCategoriaDatoSectorial':

		//LIBRERIAS
		require_once $dir."/lib/dao/cat_d_s.class.php";

		$cat_ajax = New CategoriaDatoSectorAjax();

		$condicion = $_GET['condicion'];
		$multiple = $_GET['multiple'];

		$cat_ajax->comboBoxCategoriaDatoSectorial($condicion,$multiple);
		break;

	// Lista ComboBox de Categoria de Datos Sectoriales en formulario insert-update
	case 'comboBoxCategoriaDatoSectorialInsertUpdate':

		//LIBRERIAS
		require_once $dir."/lib/dao/cat_d_s.class.php";

		$cat_ajax = New CategoriaDatoSectorAjax();

		$condicion = $_GET['condicion'];
		$multiple = $_GET['multiple'];

		$cat_ajax->comboBoxCategoriaDatoSectorialInsertUpdate($condicion,$multiple);
		break;

		//Lista ComboBox de Datos Sectoriales
	case 'comboBoxDatoSectorial':

		//LIBRERIAS
		require_once $dir."/lib/dao/dato_sectorial.class.php";

		$dao_ajax = New DatoSectorialAjax();

		$condicion = $_GET['condicion'];
		$multiple = $_GET['multiple'];

		$dao_ajax->comboBoxDatoSectorial($condicion,$multiple);
		break;

		//Lista ComboBox de Municipios
	case 'comboBoxMunicipio':

		//LIBRERIAS
		require_once $dir."/lib/dao/municipio.class.php";

		$dao_ajax = New MunicipioAjax();

		$condicion = $_GET['id_deptos'];
		$multiple = $_GET['multiple'];
		$titulo = (isset($_GET['titulo'])) ? $_GET['titulo'] : 1 ;
		$separador_depto = (isset($_GET['separador_depto'])) ? $_GET['separador_depto'] : 1 ;
		$id_name = (isset($_GET['id_name'])) ? $_GET['id_name'] : 'id_muns' ;

		$dao_ajax->comboBoxMunicipio($condicion,$multiple,$titulo,$separador_depto,$id_name);
		break;

		//Lista ComboBox de Municipios
	case 'checkBoxMunicipio':

		//LIBRERIAS
		require_once $dir."/lib/dao/municipio.class.php";

		$dao_ajax = New MunicipioAjax();

		$condicion = $_GET['id_deptos'];
		$titulo = (isset($_GET['titulo'])) ? $_GET['titulo'] : 1 ;
		$separador_depto = (isset($_GET['separador_depto'])) ? $_GET['separador_depto'] : 1 ;
		$id_name = (isset($_GET['id_name'])) ? $_GET['id_name'] : 'id_muns' ;

		$dao_ajax->checkBoxMunicipio($condicion,$titulo,$separador_depto,$id_name);
		break;

		//Lista ComboBox de Municipios
	case 'comboBoxMunicipioEvento':

		//LIBRERIAS
		require_once $dir."/lib/dao/municipio.class.php";

		$dao_ajax = New MunicipioAjax();

		$condicion = $_GET['id_deptos'];
		$multiple = $_GET['multiple'];
		$titulo = (isset($_GET['titulo'])) ? $_GET['titulo'] : 1 ;
		$separador_depto = (isset($_GET['separador_depto'])) ? $_GET['separador_depto'] : 1 ;

		$dao_ajax->comboBoxMunicipioEvento($condicion,$multiple,$titulo,$separador_depto);
		break;

        //Lista ComboBox de Municipios
	case 'checkboxMpios4w':

		//LIBRERIAS
		require_once $dir."/lib/dao/municipio.class.php";

		$dao_ajax = New MunicipioAjax();

		$condicion = $_GET['id_deptos'];
		$titulo = (isset($_GET['titulo'])) ? $_GET['titulo'] : 1 ;

		$dao_ajax->checkboxMpios4w($condicion, $titulo);
		break;

		//Lista ComboBox de Subcategorias de eventos
	case 'comboBoxSubcategoria':

		//LIBRERIAS
		require_once $dir."/lib/dao/subcat_evento_c.class.php";

		$dao_ajax = New SubCatEventoConflictoAjax();

		$id_cat = $_GET["id_cat"];
		$multiple = (isset($_GET['multiple'])) ? $_GET['multiple'] : 0;
		$titulo = (isset($_GET['titulo'])) ? $_GET['titulo'] : 0;
		$separador = (isset($_GET['separador'])) ? $_GET['separador'] : 0 ;

		$dao_ajax->comboBoxSubcategoria($id_cat,$multiple,$titulo,$separador);
		break;

		//Lista ComboBox de Subestados de eventos
	case 'comboBoxSubestado':

		//LIBRERIAS
		require_once $dir."/lib/dao/subestado.class.php";

		$dao_ajax = New SubestadoAjax();

		$id_estado = $_GET["id_estado"];
		$multiple = (isset($_GET['multiple'])) ? $_GET['multiple'] : 0;
		$titulo = (isset($_GET['titulo'])) ? $_GET['titulo'] : 0;
		$separador = (isset($_GET['separador'])) ? $_GET['separador'] : 0 ;

		$dao_ajax->comboBoxSubestado($id_estado,$multiple,$titulo,$separador);
		break;

		//Lista ComboBox de Subcondiciones de eventos
	case 'comboBoxSubcondicion':

		//LIBRERIAS
		require_once $dir."/lib/dao/subcondicion.class.php";

		$dao_ajax = New SubCondicionAjax();

		$id_condicion = $_GET["id_condicion"];
		$multiple = (isset($_GET['multiple'])) ? $_GET['multiple'] : 0;
		$titulo = (isset($_GET['titulo'])) ? $_GET['titulo'] : 0;
		$separador = (isset($_GET['separador'])) ? $_GET['separador'] : 0 ;
		$id_field = (isset($_GET['id_field'])) ? $_GET['id_field'] : 'id_subcondicion' ;

		$dao_ajax->comboBoxSubcondicion($id_condicion,$multiple,$titulo,$separador,$id_field);
		break;

		//Lista ComboBox de Rango de Edad en Eventos
	case 'comboBoxRangoEdad':

		//LIBRERIAS
		require_once $dir."/lib/dao/rango_edad.class.php";

		$dao_ajax = New RangoEdadAjax();

		$id_edad = $_GET["id_edad"];
		$multiple = (isset($_GET['multiple'])) ? $_GET['multiple'] : 0;
		$titulo = (isset($_GET['titulo'])) ? $_GET['titulo'] : 0;
		$separador = (isset($_GET['separador'])) ? $_GET['separador'] : 0 ;
		$id_field = (isset($_GET['id_field'])) ? $_GET['id_field'] : 'id_rango_edad' ;

		$dao_ajax->comboBoxRangoEdad($id_edad,$multiple,$titulo,$separador,$id_field);
		break;

		//Lista ComboBox de Subetnias de eventos
	case 'comboBoxSubetnia':

		//LIBRERIAS
		require_once $dir."/lib/dao/subetnia.class.php";

		$dao_ajax = New SubetniaAjax();

		$id_etnia = $_GET["id_etnia"];
		$multiple = (isset($_GET['multiple'])) ? $_GET['multiple'] : 0;
		$titulo = (isset($_GET['titulo'])) ? $_GET['titulo'] : 0;
		$separador = (isset($_GET['separador'])) ? $_GET['separador'] : 0 ;
		$id_field = (isset($_GET['id_field'])) ? $_GET['id_field'] : 'id_subetnia' ;

		$dao_ajax->comboBoxSubetnia($id_etnia,$multiple,$titulo,$separador,$id_field);
		break;

		//Lista ComboBox de Subfuentes de eventos
	case 'comboBoxSubfuente':

		//LIBRERIAS
		require_once $dir."/lib/dao/subfuente_evento_c.class.php";

		$dao_ajax = New SubFuenteEventoConflictoAjax();

		$id_fuente = $_GET["id_fuente"];

		$dao_ajax->comboBoxSubfuente($id_fuente);
		break;

		//Lista ComboBox de Subactores de eventos
	case 'comboBoxActor':

		//LIBRERIAS
		require_once $dir."/lib/dao/actor.class.php";

		$dao_ajax = New ActorAjax();

		$id_papa = $_GET["id_papa"];
		$name_field = $_GET["name_field"];
		$onchange = $_GET["onchange"];
		$multiple = $_GET["multiple"];
		$separador = (isset($_GET["separador"])) ? $_GET["separador"] : 0;

		$value = (isset($_GET["value"])) ? $_GET["value"] : 0;
		$numero_fila = (isset($_GET["numero_fila"])) ? $_GET["numero_fila"] : 0;

		$dao_ajax->comboBoxActor($id_papa,$name_field,$onchange,$value,$numero_fila,$multiple,0,$separador);
		break;

		//Lista ComboBox de Subactores de eventos
	case 'comboBoxActorInsertar':

		//LIBRERIAS
		require_once $dir."/lib/dao/actor.class.php";

		$dao_ajax = New ActorAjax();

		$id_papa = $_GET["id_papa"];
		$name_field = $_GET["name_field"];
		$onchange = $_GET["onchange"];
		$multiple = $_GET["multiple"];
		$separador = $_GET["separador"];

		$value = (isset($_GET["value"])) ? $_GET["value"] : 0;

		$dao_ajax->comboBoxActorInsertar($id_papa,$name_field,$onchange,$value,$multiple,0,$separador);
		break;

		//Lista ComboBox de Temas de proyectos
	case 'comboBoxTemaProyecto':

		//LIBRERIAS
		require_once $dir."/lib/dao/tema.class.php";

		$dao_ajax = New TemaAjax();

		$condicion = $_GET['id_papa'];
		$multiple = $_GET['multiple'];
		$titulo = (isset($_GET['titulo'])) ? $_GET['titulo'] : '';
		$separador = (isset($_GET['separador'])) ? $_GET['separador'] : 1;
		$id_name = (isset($_GET['id_name'])) ? $_GET['id_name'] : 'id_tema';

		$dao_ajax->comboBoxTemaProyecto($condicion,$multiple,$titulo,$separador,$id_name);

		break;

		//Lista ComboBox de Temas de proyectos
	case 'checkBoxTemaProyecto':

		//LIBRERIAS
		require_once $dir."/lib/dao/tema.class.php";

		$dao_ajax = New TemaAjax();

		$condicion = $_GET['id_papa'];
		$titulo = (isset($_GET['titulo'])) ? $_GET['titulo'] : '';
		$separador = (isset($_GET['separador'])) ? $_GET['separador'] : 1;
		$id_name = (isset($_GET['id_name'])) ? $_GET['id_name'] : 'id_tema';
		$link = (isset($_GET['link'])) ? $_GET['link'] : 1;

		$dao_ajax->checkBoxTemaProyecto($condicion,$titulo,$separador,$id_name,$link);

		break;

		//PRECARGA DE IMPORTACION DESPLAZAMIENTO
		case "preCargaDesplazamiento":

			//LIBRERIAS
			require_once $dir."/lib/dao/desplazamiento.class.php";

		$dao_ajax = New DesplazamientoAjax();

		$periodos = $_GET['periodos'];
		$dao_ajax->PreCargaCSV($periodos);

		break;

		//COLOCA EL NOMBRE DEL DEPTO
		case "nombreDepto":
			//LIBRERIAS
			require_once $dir."/lib/model/depto.class.php";
		require_once $dir."/lib/dao/depto.class.php";

		$depto_dao = New DeptoDAO();

		$id_depto = $_GET["id_depto"];

		$depto = $depto_dao->Get($id_depto);

		echo $depto->nombre;

		break;

		//COLOCA EL NOMBRE DEL MPIO
		case "nombreMpio":
			//LIBRERIAS
			require_once $dir."/lib/model/municipio.class.php";
		require_once $dir."/lib/dao/municipio.class.php";

		$mpio_dao = New MunicipioDAO();

		$id_mpio = $_GET["id_mpio"];

		$mpio = $mpio_dao->Get($id_mpio);

		echo $mpio->nombre;

		break;

		//Ocurrencias de Actor en Alimentacion de eventos de conflicto Abuelo-Papa-Hijo
	case 'ocurrenciasActor' :

		//LIBRERIAS
		require_once $dir."/lib/dao/actor.class.php";

		$dao_ajax = New ActorAjax();

		$s = $_GET["s"];
		$donde = $_GET["donde"];
		$case = $_GET["case"];
		$numero_fila = (isset($_GET["numero_fila"])) ? $_GET["numero_fila"] : 0;

		$dao_ajax->ocurrenciasActor($s,$donde,$case,$numero_fila);
		break;

	case 'setOrdenPeriodo':

		//LIBRERIAS
		require_once $dir."/lib/dao/periodo.class.php";
		require_once $dir."/lib/model/periodo.class.php";

		$dao = new PeriodoDAO();
		$key = $_POST["item_list"];

		$dao->setOrder($key);

		break;

	case 'setOrdenPerfil':

		//LIBRERIAS
		require_once $dir."/lib/dao/minificha.class.php";

		$dao = new MinifichaDAO();
		$key = $_POST["item_list"];

		$dao->setOrder($key,'cate');

		break;

	case 'setOrdenPerfilDato':

		//LIBRERIAS
		require_once $dir."/lib/dao/minificha.class.php";

		$dao = new MinifichaDAO();
		//Solo viene un elemento, pero no sabemos el indice
		$key = end($_POST);

		$dao->setOrder($key,'dato');

		break;

	case 'borrarValorDato':
		//LIBRERIAS
		require_once $dir."/lib/dao/dato_sectorial.class.php";

		$dato_dao = New DatoSectorialDAO();

		$id = $_GET["id_dato"];
		$f_fin = $_GET["f_fin"];
		$f_ini = $_GET["f_ini"];

		$dato_dao->BorrarValores($id,$f_ini,$f_fin);

		$periodos = $dato_dao->GetPeriodosValores($id);

		//Pinta el nuevo combo de periodos
		echo "<select class='select' id='periodos_dato_".$id."'><option value=''>Todos los periodos</option>";
		foreach ($periodos as $per){
			echo "<option value='".$per['ini']."|".$per['fin']."'>".$per['ini']." a ".$per['fin']."</option>";
		}

		echo "</select>";

		break;

	//Grafica de proyectos UNDAF
	case 'graficaProyectoUndaf';

		//LIBRERIAS
		require_once $dir."/lib/dao/proyecto.class.php";

		$proy_ajax = New ProyectoAjax();

		$depto = $_GET['depto'];
		$ubicacion = $_GET['ubicacion'];
		$filtro = $_GET['filtro'];
		$id_filtro = $_GET['id_filtro'];
		$chart = $_GET['chart'];

		//LOG
		require_once $dir."/lib/dao/log.class.php";
		$log = New LogUsuarioDAO();
		$log->RegistrarFrontend($filtro,'grafica');

		$proy_ajax->graficaProyectoUndaf($ubicacion,$depto,$filtro,$id_filtro,$chart);

		break;

	//Reporte de proyectos UNDAF
	case 'reporteOnLineProyectoUndaf';

		//LIBRERIAS
		require_once $dir."/lib/dao/proyecto.class.php";

		$proy_ajax = New ProyectoAjax();

		$depto = $_GET['depto'];
		$ubicacion = $_GET['ubicacion'];
		$filtro = $_GET['filtro'];
		$id_filtro = $_GET['id_filtro'];
		$show_html = (isset($_GET["show_html"])) ? $_GET["show_html"] : 1;

		//LOG
		require_once $dir."/lib/dao/log.class.php";
		$log = New LogUsuarioDAO();
		$log->RegistrarFrontend($filtro,'reporte_online');

		$proy_ajax->reporteProyectoUndaf($ubicacion,$depto,$filtro,$id_filtro,'reporteOnLineProyectoUndaf',$show_html);

		break;

		case 'reportePdfProyectoUndaf';

		//LIBRERIAS
		require_once $dir."/lib/dao/proyecto.class.php";

		$proy_ajax = New ProyectoAjax();

		$depto = $_GET['depto'];
		$ubicacion = $_GET['ubicacion'];
		$filtro = $_GET['filtro'];
		$id_filtro = $_GET['id_filtro'];

		//LOG
		require_once $dir."/lib/dao/log.class.php";
		$log = New LogUsuarioDAO();
		$log->RegistrarFrontend($filtro,'reporte_pdf');

		$proy_ajax->reportePDFProyectoUndaf($ubicacion,$depto,$filtro,$id_filtro,'reportePdfProyectoUndaf');

		break;

		case 'listadoCompletoProyectoUndaf';

		//LIBRERIAS
		require_once $dir."/lib/dao/proyecto.class.php";

		$proy_ajax = New ProyectoAjax();

		//LOG
		require_once $dir."/lib/dao/log.class.php";
		$log = New LogUsuarioDAO();
		$log->RegistrarFrontend('','listado');

		$proy_ajax->reporteProyectoUndaf(0,0,'listado_completo',0,'reporteOnLineProyectoUndaf');

		break;

		//Reporte conteo de proyectos UNDAF
		case 'reporteOnLineConteoProyectoUndaf';

		//LIBRERIAS
		require_once $dir."/lib/dao/proyecto.class.php";

		$proy_ajax = New ProyectoAjax();

		$depto = $_GET['depto'];
		$ubicacion = $_GET['ubicacion'];
		$filtro = $_GET['filtro'];
		$id_filtro = $_GET['id_filtro'];
		$intervalos = (isset($_GET['intervalos'])) ? $_GET["intervalos"] : '';
		$id_moneda = (isset($_GET["id_moneda"])) ? $_GET["id_moneda"] : 1;
		$show_html = (isset($_GET["show_html"])) ? $_GET["show_html"] : 1;

		//LOG
		require_once $dir."/lib/dao/log.class.php";
		$log = New LogUsuarioDAO();
		$log->RegistrarFrontend($filtro,'reporte_conteo_online');

		$proy_ajax->reporteProyectoConteoUndaf($ubicacion,$depto,$filtro,$id_filtro,$intervalos,$show_html);

		break;

	//Reporte de proyectos UNDAF
	case 'reporteOnLineOrgMO';

		//LIBRERIAS
		require_once $dir."/lib/dao/org.class.php";

		$org_ajax = New OrganizacionAjax();

		$depto = $_GET['depto'];
		$ubicacion = $_GET['ubicacion'];
		$filtro = $_GET['filtro'];
		$id_filtro = $_GET['id_filtro'];
		$show_html = (isset($_GET["show_html"])) ? $_GET["show_html"] : 1;

		//LOG
		require_once $dir."/lib/dao/log.class.php";
		$log = New LogUsuarioDAO();
		$log->RegistrarFrontend($filtro,'reporte_online');

		$org_ajax->reporteOrgMO($ubicacion,$depto,$filtro,$id_filtro,'reporteOnLineOrgMO',$show_html);

		break;

	case 'reportePdfOrgMO';

		//LIBRERIAS
		require_once $dir."/lib/dao/org.class.php";

		$org_ajax = New ProyectoAjax();

		$depto = $_GET['depto'];
		$ubicacion = $_GET['ubicacion'];
		$filtro = $_GET['filtro'];
		$id_filtro = $_GET['id_filtro'];

		//LOG
		require_once $dir."/lib/dao/log.class.php";
		$log = New LogUsuarioDAO();
		$log->RegistrarFrontend($filtro,'reporte_pdf');

		$org_ajax->reportePDFOrgMO($ubicacion,$depto,$filtro,$id_filtro,'reportePdfOrgMO');

		break;

	//Grafica de proyectos UNDAF
	case 'graficaOrgMO';

		//LIBRERIAS
		require_once $dir."/lib/dao/org.class.php";

		$org_ajax = New OrganizacionAjax();

		$depto = $_GET['depto'];
		$ubicacion = $_GET['ubicacion'];
		$filtro = $_GET['filtro'];
		$id_filtro = $_GET['id_filtro'];
		$chart = $_GET['chart'];

		//LOG
		require_once $dir."/lib/dao/log.class.php";
		$log = New LogUsuarioDAO();
		$log->RegistrarFrontend($filtro,'grafica');

		$org_ajax->graficaConteoOrg($ubicacion,$depto,$filtro,1,2,$chart,0);
		//function graficaConteoOrg($ubicacion,$depto,$graficar_por,$mapp_oea,$consulta_social,$chart,$tipo_papa){

		break;

	case 'sessionEmailStringContacto':
		$_SESSION['string_email_contacto'] = $_GET["str"];
	break;

    case 'importarP4w':

        //LIBRERIAS
		require_once $dir."/lib/dao/p4w.class.php";
		require_once $dir."/lib/common/archivo.class.php";

        $p4 = New P4wDAO();

        $in = $_GET['insertar_db'];

        // Borra static cache
        if ($in == 1) {
            require_once $dir."/lib/dao/sissh.class.php";
            $si = New SisshDAO();
            $si->borrarCacheStatic('4w');
        }

        $p4->importar($in);

    break;


    case 'borrarMasivo4w':
		//LIBRERIAS
		require_once $dir."/lib/dao/p4w.class.php";
        require_once $dir."/lib/dao/sissh.class.php";
		require_once $dir."/lib/common/archivo.class.php";

        // Borra static cache
        if (!empty($ys)) {
            $si = New SisshDAO();
            $si->borrarCacheStatic('4w');
        }

        $p4 = New P4wDAO();

        $org_id = $_GET['org_id'];
        $ys = (isset($_GET['ys'])) ? $_GET['ys'] : '';

        header('Content-type: application/json');

        $p4->borrarMasivo4w($org_id, $ys);

    break;

    case 'borrar':
        include_once($dir."/lib/libs_".$_SESSION["m_e"].".php");

        $class = $_GET['class'];

        $obj = New $class();

        $obj->{$_GET['method']}($_GET['param']);

        // Borra static cache
        if ($class == 'P4wDAO') {
            require_once $dir."/lib/dao/sissh.class.php";
            $si = New SisshDAO();
            $si->borrarCacheStatic('4w');
        }

    break;

    // 4w, procesa puntos del mapa LON, LAT
	case 'getMpioFromPoint';

		//LIBRERIAS
		include($dir."/lib/common/postgresdb.class.php");
		include($dir."/lib/common/mapserver.class.php");

		$mapserver = New Mapserver();

		$c = $_GET['c'];

        $wp = explode('<br />', preg_replace("/(\r\n)+|(\n|\r)+/", "<br />", trim($_POST['points'])));

        $mapserver->getMpioFromPoint($c, $wp);

	break;

    // 4w, procesa divipola
	case 'getMpioFromDivipola';

		//LIBRERIAS
		include($dir."/lib/model/municipio.class.php");
		include($dir."/lib/dao/municipio.class.php");

		$ajax = New MunicipioAjax();

        $ajax->getMpiosFromDivipola($_POST['divipolas']);

	break;
}

?>
