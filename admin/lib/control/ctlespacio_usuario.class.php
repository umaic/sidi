<?
/**
 * Maneja todas las acciones de administración de Tipo de Eventos
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

	/**
	 * VO de EspacioUsuario
	 * @var object 
	 */
	var $espacio_usuario;

	/**
	 * Variable para el manejo de la clase EspacioUsuarioDAO
	 * @var object 
	 */
	var $espacio_usuario_dao;

	/**
	 * Constructor
	 * Crea la conexión a la base de datos y ejecuta la accion
	 * @access public
	 * @param string $accion Variable que indica la accion a realizar
	 */	
	function ControladorPagina($accion) {

		$this->espacio_usuario_dao = new EspacioUsuarioDAO();

		if ($accion == 'actualizar') {
			$this->parseForm();
			$this->espacio_usuario_dao->Actualizar($this->espacio_usuario);
		}
	}

	/**
	 * Realiza el Parse de las variables de la forma y las asigna al VO de EspacioUsuario (variable de clase) 
	 * @access public	
	 */	
	function parseForm() {
		$this->espacio_usuario->id_tipo_usuario = $_POST["id_tipo_usuario"];

		$this->espacio_usuario->id_espacio = (isset($_POST["id_espacio"])) ? $this->espacio_usuario->id_espacio = $_POST["id_espacio"] : array();
		
	}
}
?>
