<?
/**
 * Maneja todas las acciones de administración de Sub Componentes
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

	/**
	* VO de SubComponente
	* @var object 
	*/
	var $vo;

	/**
	* Variable para el manejo de la clase SubComponenteDAO
	* @var object 
	*/
	var $dao;

	/**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
	function ControladorPagina($accion) {

		$this->dao = new UnicefSubComponenteDAO();

		if ($accion == 'insertar') {
			$this->parseForm();
			$this->dao->Insertar($this->subcat);
		}
		else if ($accion == 'actualizar') {
			$this->parseForm();
			$this->dao->Actualizar($this->subcat);
		}
		else if ($accion == 'borrar') {
			$this->dao->Borrar($_GET["id"]);
		}
	}

	/**
  * Realiza el Parse de las variables de la forma y las asigna al VO de SubComponente (variable de clase) 
  * @access public	
  */	
	function parseForm() {
		if (isset($_POST["id"])){
			$this->vo->id = $_POST["id"];
		}
		$this->vo->id_componente = $_POST["id_componente"];
		$this->vo->nombre = $_POST["nombre"];

	}
}
?>
