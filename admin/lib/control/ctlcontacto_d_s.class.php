<?
/**
 * Maneja todas las acciones de administración de Contactos
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

	/**
	 * VO de Contacto
	 * @var object 
	 */
	var $vo;

	/**
	 * Constructor
	 * Crea la conexión a la base de datos y ejecuta la accion
	 * @access public
	 * @param string $accion Variable que indica la accion a realizar
	 */	
	function ControladorPagina($accion) {

		$this->vo = new Contacto();
		$this->contacto_dao = new ContactoDatoSectorDAO();

		if ($accion == 'insertar') {
			$this->parseForm();
			$this->contacto_dao->Insertar($this->vo);
		}
		else if ($accion == 'actualizar') {
			$this->parseForm();
			$this->contacto_dao->Actualizar($this->vo);
		}
		else if ($accion == 'borrar') {
			$this->contacto_dao->Borrar($_GET["id"]);
		}
	}

	/**
	 * Realiza el Parse de las variables de la forma y las asigna al VO de Contacto (variable de clase) 
	 * @access public	
	 */	
	function parseForm() {
		if (isset($_POST["id"])){
			$this->vo->id = $_POST["id"];
		}
		$this->vo->nombre = $_POST["nombre"];
		$this->vo->apellido = (isset($_POST["apellido"])) ? $_POST["apellido"] : '';
		$this->vo->tel = $_POST["tel"];
		$this->vo->fax = (isset($_POST["fax"])) ? $_POST["fax"] : '';
		$this->vo->email = (isset($_POST["email"])) ? $_POST["email"] : '';
		$this->vo->cel = (isset($_POST["cel"])) ? $_POST["cel"] : '';
		$this->vo->id_org = (isset($_POST["id_org"])) ? $_POST["id_org"] : array();
		$this->vo->id_espacio = (isset($_POST["id_espacio"])) ? $_POST["id_espacio"] : array();

	}
}
?>
