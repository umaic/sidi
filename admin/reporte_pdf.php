<?

if (isset($_GET["class"])){
  $class = $_GET["class"];
}
else if (isset($_POST["class"])){
  $class = $_POST["class"];
}

if (isset($_GET["method"])){
  $method = $_GET["method"];
}
else if (isset($_POST["method"])){
  $method = $_POST["method"];
}

if (isset($_GET["param"])){
  $param = $_GET["param"];
}
else if (isset($_POST["param"])){
  $param = $_POST["param"];
}


//LIBRERIAS
switch ($class){
	case "EventoDAO":
		include_once 'lib/libs_evento.php';
	break;
	case "OrganizacionDAO":
		include_once 'lib/libs_org.php';
	break;
	case "ProyectoDAO":
		include_once 'lib/libs_proyecto.php';
	break;
	case "DatoSectorialDAO":
		include_once 'lib/libs_dato_sectorial.php';
	break;
	case "DesplazamientoDAO":
		include_once 'lib/libs_desplazamiento.php';
	break;
	case "MinaDAO":
		include_once 'lib/libs_mina.php';
	break;
	case "MapaI":
		include_once 'lib/libs_mapa_i.php';
	break;
}

switch ($method){
  case "ReporteDiarioPDF":
    $obj = New $class();
    $obj->{$method}($param);
	break;
  case "ReporteSemanalPDF":
    $obj = New $class();
    $obj->{$method}($_GET["fecha_ini"],$_GET["fecha_fin"]);
  break;
  case "ReporteOrganizacion":
    $obj = New $class();
    $obj->{$method}($_POST['id_orgs'],$_POST['pdf'],$_POST['basico']);
  break;
  case "ReporteProyecto":
    $obj = New $class();
    $obj->{$method}($_POST['id_proyectos'],$_POST['pdf'],$_POST['basico']);
  break;
  case "ReporteEvento":
    $obj = New $class();
    $obj->{$method}($_POST['id_eventos'],$_POST['pdf'],$_POST['basico']);
  break;
  case "ReporteDatoSectorial":
    $obj = New $class();
    $obj->{$method}($_POST['id_datos'],$_POST['pdf'],$_POST['basico'],$_POST['dato_para']);
  break;
  case "ReporteDesplazamiento":
    $obj = New $class();
    $obj->{$method}($_POST['id_desplazamientos'],$_POST['pdf'],$_POST['basico'],$_POST['dato_para']);
  break;
  case "ReporteMina":
    $obj = New $class();
    $obj->{$method}($_POST['id_minas'],$_POST['pdf'],$_POST['basico']);
  break;
  case "ReporteMapaI":
    $org_dao = New OrganizacionDAO();
    $proy_dao = New ProyectoDAO();
    $eve_dao = New EventoDAO();
    $dato_dao = New DatoSectorialDAO();
    $desplazamiento_dao = New DesplazamientoDAO();
    
    //ORGS
	if ($_POST['id_orgs'] != ""){
	    $org_dao->ReporteOrganizacion($_POST['id_orgs'],1,$_POST['basico']);
	}
	//PROYS
    if ($_POST['id_proyectos'] != ""){
	    $proy_dao->ReporteProyecto($_POST['id_proyectos'],1,$_POST['basico']);
	}
	//EVENTOS
    if ($_POST['id_eventos'] != ""){
	    $eve_dao->ReporteEvento($_POST['id_eventos'],1,$_POST['basico']);
	}
	//DATOS
    if ($_POST['id_datos'] != ""){
	    $dato_dao->ReporteDatoSectorial($_POST['id_datos'],1,$_POST['basico'],$_POST['dato_para']);
	}
	//DESPLAZAMIENTO
    if ($_POST['id_desplazamientos'] != ""){
	    $desplazamiento_dao->ReporteDesplazamiento($_POST['id_desplazamientos'],1,$_POST['basico'],$_POST['dato_para']);
	}
	//MINA
    if ($_POST['id_minas'] != ""){
	    $mina_dao->ReporteMina($_POST['id_minas'],1,$_POST['basico']);
	}
	
  break;
} 

?>
