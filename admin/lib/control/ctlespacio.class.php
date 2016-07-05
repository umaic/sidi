<?
/**
 * Maneja todas las acciones de administración de Espacios
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

	/**
	 * VO de Espacio
	 * @var object 
	 */
	var $espacio;

	/**
	 * Variable para el manejo de la clase EspacioDAO
	 * @var object 
	 */
	var $espacio_dao;

	/**
	 * Constructor
	 * Crea la conexión a la base de datos y ejecuta la accion
	 * @access public
	 * @param string $accion Variable que indica la accion a realizar
	 */	
	function ControladorPagina($accion) {

		$this->espacio_dao = new EspacioDAO();

		if ($accion == 'insertar') {
			$this->parseForm();
			$this->espacio_dao->Insertar($this->espacio);
		}
		else if ($accion == 'actualizar') {
			$this->parseForm();
			$this->espacio_dao->Actualizar($this->espacio);
		}
		else if ($accion == 'borrar') {
			$this->espacio_dao->Borrar($_GET["id"]);
		}
	}

	/**
	 * Realiza el Parse de las variables de la forma y las asigna al VO de Espacio (variable de clase) 
	 * @access public	
	 */	
	function parseForm() {
		if (isset($_POST["id"])){
			$this->espacio->id = $_POST["id"];
		}

		$this->espacio->id_papa = $_POST["id_papa"];
		$this->espacio->nombre = $_POST["nombre"];
	}
}
?>
