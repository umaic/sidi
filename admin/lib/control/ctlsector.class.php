<?
/**
 * Maneja todas las acciones de administración de Sectors
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

	/**
	 * VO de Sector
	 * @var object 
	 */
	var $sector;

	/**
	 * Variable para el manejo de la clase SectorDAO
	 * @var object 
	 */
	var $sector_dao;

	/**
	 * Constructor
	 * Crea la conexión a la base de datos y ejecuta la accion
	 * @access public
	 * @param string $accion Variable que indica la accion a realizar
	 */	
	function ControladorPagina($accion) {

		$this->sector_dao = new SectorDAO();

		if ($accion == 'insertar') {
			$this->parseForm();
			$this->sector_dao->Insertar($this->sector);
		}
		else if ($accion == 'actualizar') {
			$this->parseForm();
			$this->sector_dao->Actualizar($this->sector);
		}
		else if ($accion == 'borrar') {
			$this->sector_dao->Borrar($_GET["id"]);
		}
	}

	/**
	 * Realiza el Parse de las variables de la forma y las asigna al VO de Sector (variable de clase) 
	 * @access public	
	 */	
	function parseForm() {
		if (isset($_POST["id"])){
			$this->sector->id = $_POST["id"];
		}
		$this->sector->nombre_es = $_POST["nombre_es"];
		$this->sector->nombre_in = $_POST["nombre_in"];
		$this->sector->def = (isset($_POST["def"])) ? $_POST["def"] : '';
	}
}
?>
