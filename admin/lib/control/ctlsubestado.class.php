<?
/**
 * Maneja todas las acciones de administración de Sub Estados
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

	/**
	* VO de SubEstado
	* @var object 
	*/
	var $subestado;

	/**
	* Variable para el manejo de la clase SubEstadoDAO
	* @var object 
	*/
	var $subestado_dao;

	/**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
	function ControladorPagina($accion) {

		$this->subestado_dao = new SubEstadoDAO();

		if ($accion == 'insertar') {
			$this->parseForm();
			$this->subestado_dao->Insertar($this->subestado);
		}
		else if ($accion == 'actualizar') {
			$this->parseForm();
			$this->subestado_dao->Actualizar($this->subestado);
		}
		else if ($accion == 'borrar') {
			$this->subestado_dao->Borrar($_GET["id"]);
		}
	}

	/**
  * Realiza el Parse de las variables de la forma y las asigna al VO de SubEstado (variable de clase) 
  * @access public	
  */	
	function parseForm() {
		if (isset($_POST["id"])){
			$this->subestado->id = $_POST["id"];
		}
		$this->subestado->id_estado = $_POST["id_estado"];
		$this->subestado->nombre = $_POST["nombre"];

	}
}
?>