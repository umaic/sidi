<?
session_start();

$case = $_GET["c"];
//$id_ubicacion = $_GET["id_ubicacion"];

if (is_numeric($case)){
	switch ($case){

		//Minificha PDF generada desde la minificha HTML
		case 1:
			//LIBRERIAS
			include('admin/lib/common/archivo.class.php');

			if (isset($_SESSION["pdfcode"])){
				header("Content-Type: application/pdf");
				header("Content-Disposition: attachment; filename=\"perfil_".$_GET["ubi"].".pdf\"");

				echo $_SESSION["pdfcode"];
			}

			break;


		//Minificha PDF generada directamente desde el mapa
		case 2:
			//LIBRERIAS

			include_once("consulta/lib/libs_mapa_i.php");
			include_once("admin/lib/common/graphic.class.php");
			include_once("admin/lib/common/imageSmoothArc.php");
			include_once("admin/lib/common/imageSmoothLine.php");
			include_once("admin/lib/common/archivo.class.php");
			include_once("admin/lib/dao/log.class.php");

			//log fisico para ataques
			$log = new LogUsuarioDAO();
			$log->insertarLogFisico('download_pdf','mapserver_perfil_pdf');
			// fin log fisico		

			$sissh = New SisshDAO();
			$depto_dao = New DeptoDAO();
			$mun_dao = New MunicipioDAO();
			$archivo = New Archivo();

			if (isset($_GET["id_depto"]) && isset($_GET["id_mun"])){
				$id_depto = $_GET["id_depto"];
				$id_mpio = $_GET["id_mun"];

				if (strlen($id_mpio) == 5){
					$dato_para = 2;
					$ids = $mun_dao->GetAllArrayID('','');
					$id_ubicacion = $id_mpio;
				}
				else if (strlen($id_depto) == 2){
					$dato_para = 1;
					$ids = $depto_dao->GetAllArrayID('');
					$id_ubicacion = $id_depto;
				}

				if (ereg("[0-9]{2,5}",$id_ubicacion) && in_array($id_ubicacion,$ids)){

					$ubicacion = ($dato_para == 1) ? $depto_dao->Get($id_ubicacion) : $mun_dao->Get($id_ubicacion);

					//MANEJO DE CACHE
					//Para almacenar cache html y pdf
					$path_file = $sissh->dir_cache_perfil."/perfil_$id_ubicacion";

					$cache_file = "$path_file.pdf";

					$gen_perfil = $sissh->siGenerarPerfil($cache_file);
					//$gen_perfil = 1;
					//

					//Carga el contenido del cache
					if ($gen_perfil == 1){
						$sissh->Minificha($id_depto,$id_mpio,'pdf');
					}
					else{
						header("Content-Type: application/pdf");
						header("Content-Disposition: attachment; filename=\"perfil_".$ubicacion->nombre.".pdf\"");


						$fp = $archivo->Abrir($cache_file,'r');
						echo $archivo->LeerEnString($fp,$cache_file);
					}

				}
			}
			else{

				//MANEJO DE CACHE
				//Para almacenar cache pdf
				$path_file = $sissh->dir_cache_perfil."/perfil_00";
				$cache_file = "$path_file.pdf";

				$gen_perfil = $sissh->siGenerarPerfil($cache_file);
				//$gen_perfil = 1;

				//Carga el contenido del cache
				if ($gen_perfil == 1){
					$sissh->Minificha(0,0,'pdf');
				}
				else{
					header("Content-Type: application/pdf");
					header("Content-Disposition: attachment; filename=\"Perfil_Nacional.pdf\"");
				}

				$fp = $archivo->Abrir($cache_file,'r');
				echo $archivo->LeerEnString($fp,$cache_file);
			}

			break;

		//Ficha PDF de proyecto Undaf
		case 3:
			//LIBRERIAS
			include("consulta/lib/libs_proyecto.php");

			$proy_dao = New ProyectoDAO();
			$id = $_GET["id"];

			if (is_numeric($id)){
				header("Content-Type: application/pdf");
				header("Content-Disposition: attachment; filename=\"ficha_proyecto_$id.pdf\"");

				$proy_dao->fichaPdf($id);
			}

			break;



		//Ficha PDF de proyecto Undaf
		case 4:
			//LIBRERIAS
			include_once("consulta/lib/libs_proyecto.php");
			//require_once "lib/dao/proyecto.class.php";

			$proy_ajax = New ProyectoAjax();

			$depto = $_GET['depto'];
			$ubicacion = $_GET['ubicacion'];
			$filtro = $_GET['filtro'];
			$id_filtro = $_GET['id_filtro'];

			//LOG
			require_once "admin/lib/dao/log.class.php";
			$log = New LogUsuarioDAO();
			$log->RegistrarFrontend($filtro,'reporte_pdf');

			header("Content-Type: application/pdf");
			header("Content-Disposition: attachment; filename=\"proyectos.pdf\"");
			$proy_ajax->reportePDFProyectoUndaf($ubicacion,$depto,$filtro,$id_filtro,'reportePdfProyectoUndaf');
			break;

		//Ficha PDF de Org Mapp-oea
		case 5:
			//LIBRERIAS
			include("consulta/lib/libs_org.php");

			$org_dao = New OrganizacionDAO();
			$id = $_GET["id"];

			if (is_numeric($id)){
				header("Content-Type: application/pdf");
				header("Content-Disposition: attachment; filename=\"ficha_proyecto_$id.pdf\"");

				$org_dao->fichaPdfMO($id);
			}

			break;

		//Ficha PDF de Actividad AWP UNICEF desde mapa o tabla
		case 6:
			//LIBRERIAS
			include("admin/lib/dao/factory.class.php");
			include("admin/lib/libs_unicef_actividad_awp.php");

			// Se usa ahora factory pattern
			$dao = FactoryDAO::factory('unicef_actividad_awp');

			$id = $_GET["id"];

			if (is_numeric($id)) $dao->fichaPdf($id);

			break;

		//Ficha PDF de Convenio UNICEF desde mapa o tabla
		case 7:
			//LIBRERIAS
			include("admin/lib/dao/factory.class.php");
			include("admin/lib/libs_unicef_convenio.php");

			// Se usa ahora factory pattern
			$dao = FactoryDAO::factory('unicef_convenio');

			$id = $_GET["id"];

			if (is_numeric($id)) $dao->fichaPdf($id);

			break;
	}
}
?>
