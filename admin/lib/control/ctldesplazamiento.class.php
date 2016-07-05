<?
/**
 * Maneja todas las acciones de administración de Desplazamientos
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

	/**
	* VO de Desplazamiento
	* @var object
	*/
	var $desplazamiento;

	/**
	* Variable para el manejo de la clase DesplazamientoDAO
	* @var object
	*/
	var $desplazamiento_dao;

	/**
	* Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
	* @access public
	* @param string $accion Variable que indica la accion a realizar
	*/
	function ControladorPagina($accion) {

		$this->desplazamiento_dao = new DesplazamientoDAO();

		if ($accion == 'insertar') {
			$this->parseForm();
			$this->desplazamiento_dao->Insertar($this->desplazamiento,$_POST["dato_para"]);
		}
		else if ($accion == 'actualizar'){
			$this->parseForm();
			$this->desplazamiento_dao->Actualizar($this->desplazamiento,$_POST["dato_para"]);
		}
		else if ($accion == 'borrar') {
			$this->desplazamiento_dao->Borrar($_GET["id"]);
		}
		else if ($accion == 'importar') {
			$this->desplazamiento_dao->ImportarCSV($_FILES['archivo_csv'],$_POST["id_clase"],$_POST["id_tipo"],$_POST["id_fuente"],$_POST["acc"],$_POST["id_periodo_h"],$_POST["f_corte"]);
		}
		else if ($accion == 'fechaCorte') {
			$this->desplazamiento_dao->updateFechaCorte($_POST["f_corte"],$_POST["id_fuente"]);
		}
	}

	/**
	* Realiza el Parse de las variables de la forma y las asigna al VO de Desplazamiento (variable de clase)
	* @access public
	*/
	function parseForm() {
		if (isset($_POST["id"])){
			$this->desplazamiento->id = $_POST["id"];
		}

		if ($_POST["dato_para"] == 1){
			$this->desplazamiento->id_depto_exp = $_POST["id_depto_exp"];
			$this->desplazamiento->id_depto_rec = $_POST["id_depto_rec"];
		}

		if ($_POST["dato_para"] == 2){
			$this->desplazamiento->id_mun_exp = $_POST["id_mun_exp"];
			$this->desplazamiento->id_mun_rec = $_POST["id_mun_rec"];
		}


		$this->desplazamiento->id_tipo = $_POST["id_tipo"];
		$this->desplazamiento->id_clase = $_POST["id_clase"];
		$this->desplazamiento->id_fuente = $_POST["id_fuente"];
		$this->desplazamiento->id_poblacion = $_POST["id_poblacion"];
		$this->desplazamiento->id_contacto = $_POST["id_contacto"];
		$this->desplazamiento->id_periodo = $_POST["id_periodo"];
		$this->desplazamiento->cantidad = $_POST["cantidad"];



	}
}
?>