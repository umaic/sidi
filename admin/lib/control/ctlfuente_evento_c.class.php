<?
/**
 * Maneja todas las acciones de administración de Fuente de Eventos
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

	/**
	* VO de FuenteEventoConflicto
	* @var object 
	*/
	var $fuente;

	/**
	* Variable para el manejo de la clase FuenteEventoConflictoDAO
	* @var object 
	*/
	var $cat_dao;

	/**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
	function ControladorPagina($accion) {

		$this->cat_dao = new FuenteEventoConflictoDAO();

		if ($accion == 'insertar') {
			$this->parseForm();
			$this->cat_dao->Insertar($this->fuente);
		}
		else if ($accion == 'actualizar') {
			$this->parseForm();
			$this->cat_dao->Actualizar($this->fuente);
		}
		else if ($accion == 'borrar') {
			$this->cat_dao->Borrar($_GET["id"]);
		}
	}

	/**
  * Realiza el Parse de las variables de la forma y las asigna al VO de FuenteEventoConflicto (variable de clase) 
  * @access public	
  */	
	function parseForm() {
		if (isset($_POST["id"])){
			$this->fuente->id = $_POST["id"];
		}
		$this->fuente->nombre = $_POST["nombre"];

	}
}
?>