<?
/**
 * Maneja todas las acciones de administración de Tipo de Eventos
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

	/**
	* VO de SubFuenteEventoConflicto
	* @var object 
	*/
	var $subcat;

	/**
	* Variable para el manejo de la clase SubFuenteEventoConflictoDAO
	* @var object 
	*/
	var $subcat_dao;

	/**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
	function ControladorPagina($accion) {

		$this->subcat_dao = new SubFuenteEventoConflictoDAO();

		if ($accion == 'insertar') {
			$this->parseForm();
			$this->subcat_dao->Insertar($this->subcat);
		}
		else if ($accion == 'actualizar') {
			$this->parseForm();
			$this->subcat_dao->Actualizar($this->subcat);
		}
		else if ($accion == 'borrar') {
			$this->subcat_dao->Borrar($_GET["id"]);
		}
	}

	/**
  * Realiza el Parse de las variables de la forma y las asigna al VO de SubFuenteEventoConflicto (variable de clase) 
  * @access public	
  */	
	function parseForm() {
		if (isset($_POST["id"])){
			$this->subcat->id = $_POST["id"];
		}
		$this->subcat->id_fuente = $_POST["id_fuente"];
		$this->subcat->nombre = $_POST["nombre"];

	}
}
?>