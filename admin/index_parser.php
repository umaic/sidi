<?

//SEGURIDAD
include_once 'seguridad.php';
include_once 'lib/libs_perfil_usuario.php';
include_once 'lib/dao/log.class.php';

//REGISTRA EL MODULO ESPECIFICO
if (isset($_GET["m_e"])){
	$_SESSION["m_e"] = $_GET["m_e"];
}
else if (!isset($_SESSION["m_e"]) && !isset($_GET["m_e"])){
	$_SESSION["m_e"] = "";
}

//LIBRERIAS
if ($_SESSION["m_e"] != 'home'){
	switch ($_SESSION["m_e"]){
		case "evento":
			include_once("lib/libs_evento.php");
			include_once ('js/calendar/calendar.php');
			break;
		case "perfil_usuario":
			include_once("lib/control/ctlperfil_usuario.class.php");
			break;
		case "proyecto":
			include_once("lib/libs_proyecto.php");
			include_once ('js/calendar/calendar.php');
			break;
		case "dato_sectorial":
			include_once("lib/libs_dato_sectorial.php");
			include_once ('js/calendar/calendar.php');
			break;
		case "dato_s_valor":
			include_once("lib/libs_dato_sectorial.php");
			break;
		case "log_consulta":
			include_once("lib/libs_log.php");
			break;
		case "log_admin":
			include_once("lib/libs_log.php");
			break;
		default:
			if (isset($_SESSION["m_e"]) && $_SESSION["m_e"] != '')
				include_once("lib/libs_".$_SESSION["m_e"].".php");
		break;
	}
}

// Para la nueva interfaz de administración con Ajax, es necesario decodificar UTF-8 todo lo que sean enviado... AJAX codifica todo en utf-8
function decodeUTF8($array) {
 
        foreach ($array as $k => $postTmp) {
                if (is_array($postTmp)) {
                        $array[$k]= decodeUTF8($postTmp);
                }else{
                        $array[$k] = utf8_decode($postTmp);
                }
        }
 
        return $array;
}
 
//$_POST = decodeUTF8($_POST);


//ACCION DE LA FORMA
if (isset($_POST["submit"])){
	$accion = $_POST["accion"];

	//Controlador
	$ct = New ControladorPagina($accion);
	
	if ($accion == 'actualizar' && !isset($_POST['si_proy'])){

		echo 'Registro actualizado con &eacute;xito';
	}

	return;
}
//INICIALIZACION DE VARIABLES
$accion = "";
if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

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

$param = '';
if (isset($_GET["param"])){
	$param = $_GET["param"];
}
else if (isset($_POST["param"])){
	$param = $_POST["param"];
}

?>

<!-- CONTENIDO : INICIO -->
<div id="cont"><?
if ($accion != ""){
	if ($accion == "listar" || $accion == "borrar" || $accion == "reportar" || $accion == "publicar"){
		//EVENTO
		if ($class == "EventoDAO"){

			$obj = New $class();

			/////LISTA EVENTOS
			if ($accion == "listar"){
				if (!isset($_POST['f_ini']) && !isset($_GET['page'])){
					include_once($_SESSION["m_e"]."/index.php");
				}
				else{

					////FECHA INICIAL
					if (isset($_POST["f_ini"])){
						$f_ini = $_POST['f_ini'];
					}
					else if (isset($_GET["f_ini"])){
						$f_ini = $_GET['f_ini'];
					}

					////FECHA FINAL
					if (isset($_POST["f_fin"])){
						$f_fin = $_POST['f_fin'];
					}
					else if (isset($_GET["f_fin"])){
						$f_fin = $_GET['f_fin'];
					}

					$where = "FECHA_REGISTRO between '".$f_ini."' AND '".$f_fin."'";
					$obj->ListarTabla($where);
				}
			}
			else if ($accion == "borrar"){
				$obj->Borrar($param,0);
			}
			else{
				$obj->{$method}($param);
			}
		}
		//EVENTO CONFLICTO
		if ($class == "EventoConflictoDAO"){

			$obj = New $class();

			/////LISTA EVENTOS
			if ($accion == "listar"){
				if (!isset($_GET['f_ini']) && !isset($_GET['page'])){
					include_once($_SESSION["m_e"]."/index.php");
				}
				else{

					$obj->ListarTabla();
				}
			}
			else if ($accion == "borrar"){
				$obj->Borrar($param,0);
			}
			else{
				$obj->{$method}($param);
			}
		}
		//ORG
		else if ($class == "OrganizacionDAO"){
			$obj = New $class();

			/////LISTA ORGS
			if ($accion == "listar"){
				if (!isset($_POST['consultar']) && !isset($_POST['buscar']) && !isset($_GET['page']) && !isset($_GET['criterio']) && !isset($_GET['col_orden'])){
					include_once($_SESSION["m_e"]."/index.php");
				}
				else{
					$obj->ListarTabla();
				}
			}
			else if ($accion == "publicar"){
				$obj->ListarOrgPublicar();
			}
			else if ($accion == "borrar"){
				$obj->Borrar($param,0);
			}
			else{
				$obj->{$method}($param);
			}
		}
		//PROYECTO
		else if ($class == "ProyectoDAO"){
			$obj = New $class();

			/////LISTA PROYS
			if ($accion == "listar"){
				if (!isset($_POST['consultar']) && !isset($_POST['buscar']) && !isset($_GET['page']) && !isset($_GET['criterio']) && !isset($_GET['col_orden'])){
					include_once($_SESSION["m_e"]."/index.php");
				}
				else{
					$obj->ListarTabla();
				}
			}
			else if ($accion == "borrar"){
				$obj->Borrar($param,0);
			}
			else{
				$obj->{$method}($param);
			}
		}
		else{
			$obj = New $class();
			$obj->{$method}($param);

		}
	}
	else if ($accion == "insertar" || $accion == "actualizar"){
		include_once($_SESSION["m_e"]."/insert.php");
	}
	else if ($accion == "consultar"){
		include_once("consulta/index.php");
	}
	else if ($accion == "importar"){
		include_once($_SESSION["m_e"]."/importar.php");
	}
	// Importacion de datos de desplazamiento accion social = sipod
	else if ($accion == "importar_sipod"){
		include_once($_SESSION["m_e"]."/importar_sipod.php");
	}
	else if ($accion == "reportar_admin"){
		include_once($_SESSION["m_e"]."/reportar.php");
	}
	else if ($accion == "insertarDatoValor" || $accion == "actualizarDatoValor"){
		include_once($_SESSION["m_e"]."/insert.php");
	}
	else if ($accion == "fechaCorte"){
		include_once($_SESSION["m_e"]."/fecha_corte.php");
	}
	else if ($accion == "sincro_cnrr"){
		include_once($_SESSION["m_e"]."/sincro_cnrr.php");
	}
	else if ($accion == "insertarOrg4w"){
		include_once($_SESSION["m_e"]."/insert_org4w.php");
	}
	else if ($accion == "insertarCon4w"){
		include_once($_SESSION["m_e"]."/insert_con4w.php");
	}

	// Alerta de borrado exitoso
	if ($accion == 'borrar'){
		echo 'Registro eliminado con &eacute;xito';
		return;
	}
}
?>
