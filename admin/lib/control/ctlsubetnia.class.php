<?
/**
 * Maneja todas las acciones de administración de Sub Etnias
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

	/**
	* VO de SubEtnia
	* @var object 
	*/
	var $subetnia;

	/**
	* Variable para el manejo de la clase SubEtniaDAO
	* @var object 
	*/
	var $subetnia_dao;

	/**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
	function ControladorPagina($accion) {

		$this->subetnia_dao = new SubEtniaDAO();

		if ($accion == 'insertar') {
			$this->parseForm();
			$this->subetnia_dao->Insertar($this->subetnia);
		}
		else if ($accion == 'actualizar') {
			$this->parseForm();
			$this->subetnia_dao->Actualizar($this->subetnia);
		}
		else if ($accion == 'borrar') {
			$this->subetnia_dao->Borrar($_GET["id"]);
		}
	}

	/**
  * Realiza el Parse de las variables de la forma y las asigna al VO de SubEtnia (variable de clase) 
  * @access public	
  */	
	function parseForm() {
		if (isset($_POST["id"])){
			$this->subetnia->id = $_POST["id"];
		}
		$this->subetnia->id_etnia = $_POST["id_etnia"];
		$this->subetnia->nombre = $_POST["nombre"];

	}
}
?>