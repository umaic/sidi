<?
/**
 * Maneja todas las acciones de administración de Tipo de Eventos
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

	/**
	* VO de TipoUsuario
	* @var object 
	*/
	var $tipo_usuario;

	/**
	* Variable para el manejo de la clase TipoUsuarioDAO
	* @var object 
	*/
	var $tipo_usuario_dao;

	/**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
	function ControladorPagina($accion) {

		$this->tipo_usuario_dao = new TipoUsuarioDAO();

		if ($accion == 'insertar') {
			$this->parseForm();
			$this->tipo_usuario_dao->Insertar($this->tipo_usuario);
		}
		else if ($accion == 'actualizar') {
			$this->parseForm();
			$this->tipo_usuario_dao->Actualizar($this->tipo_usuario);
		}
		else if ($accion == 'borrar') {
			$this->tipo_usuario_dao->Borrar($_GET["id"]);
		}
	}

	/**
  * Realiza el Parse de las variables de la forma y las asigna al VO de TipoUsuario (variable de clase) 
  * @access public	
  */	
	function parseForm() {
		if (isset($_POST["id"])){
			$this->tipo_usuario->id = $_POST["id"];
		}
		$this->tipo_usuario->nombre = $_POST["nombre"];
		$this->tipo_usuario->cnrr = (isset($_POST["cnrr"])) ? $_POST["cnrr"] : 0;
	}
}
?>
