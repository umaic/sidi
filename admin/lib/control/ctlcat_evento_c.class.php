<?
/**
 * Maneja todas las acciones de administración de Tipo de Eventos
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

	/**
	* VO de CatEventoConflicto
	* @var object 
	*/
	var $cat;

	/**
	* Variable para el manejo de la clase CatEventoConflictoDAO
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

		$this->cat_dao = new CatEventoConflictoDAO();

		if ($accion == 'insertar') {
			$this->parseForm();
			$this->cat_dao->Insertar($this->cat);
		}
		else if ($accion == 'actualizar') {
			$this->parseForm();
			$this->cat_dao->Actualizar($this->cat);
		}
		else if ($accion == 'borrar') {
			$this->cat_dao->Borrar($_GET["id"]);
		}
	}

	/**
  * Realiza el Parse de las variables de la forma y las asigna al VO de CatEventoConflicto (variable de clase) 
  * @access public	
  */	
	function parseForm() {
		if (isset($_POST["id"])){
			$this->cat->id = $_POST["id"];
		}
		$this->cat->nombre = $_POST["nombre"];

	}
}
?>