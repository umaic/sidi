<?
/**
* Maneja todas las acciones de administración de Sub Condicions
*
* @author Ruben A. Rojas C.
*/
class ControladorPagina {

	/**
	* VO de SubCondicion
	* @var object 
	*/
	var $subcondicion;

	/**
	* Variable para el manejo de la clase SubCondicionDAO
	* @var object 
	*/
	var $subcondicion_dao;

	/**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
	function ControladorPagina($accion) {

		$this->subcondicion_dao = new SubCondicionDAO();

		if ($accion == 'insertar') {
			$this->parseForm();
			$this->subcondicion_dao->Insertar($this->subcondicion);
		}
		else if ($accion == 'actualizar') {
			$this->parseForm();
			$this->subcondicion_dao->Actualizar($this->subcondicion);
		}
		else if ($accion == 'borrar') {
			$this->subcondicion_dao->Borrar($_GET["id"]);
		}
	}

	/**
  * Realiza el Parse de las variables de la forma y las asigna al VO de SubCondicion (variable de clase) 
  * @access public	
  */	
	function parseForm() {
		if (isset($_POST["id"])){
			$this->subcondicion->id = $_POST["id"];
		}
		$this->subcondicion->id_condicion = $_POST["id_condicion"];
		$this->subcondicion->nombre = $_POST["nombre"];

	}
}
?>