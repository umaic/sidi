<?
/**
 * Maneja todas las acciones de administración de Tipo de Eventos
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

	/**
	 * VO de ContactoColOp
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

		$this->vo_dao = new ContactoColOpDAO();

		if ($accion == 'insertar') {
			$this->parseForm();
			$this->vo_dao->Insertar($this->vo);
		}
		else if ($accion == 'actualizar') {
			$this->parseForm();
			$this->vo_dao->Actualizar($this->vo);
		}
		else if ($accion == 'borrar') {
			$this->vo_dao->Borrar($_GET["id"]);
		}
	}

	/**
	 * Realiza el Parse de las variables de la forma y las asigna al VO de ContactoColOp (variable de clase) 
	 * @access public	
	 */	
	function parseForm() {
		if (isset($_POST["id"])){
			$this->vo->id = $_POST["id"];
		}
		$this->vo->nombre = $_POST["nombre"];
		$this->vo->id_contacto_col = $_POST["id_contacto_col"];
	}
}
?>
