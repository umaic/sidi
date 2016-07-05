<?
/**
 * Maneja todas las acciones de administración de Tipo de Eventos
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de PerfilUsuario
	* @var object 
	*/
  var $perfil_usuario;

  /**
	* Variable para el manejo de la clase PerfilUsuarioDAO
	* @var object 
	*/
  var $perfil_usuario_dao;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->perfil_usuario_dao = new PerfilUsuarioDAO();

    if ($accion == 'actualizar') {
      $this->parseForm();
      $this->perfil_usuario_dao->Actualizar($this->perfil_usuario);
    }
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de PerfilUsuario (variable de clase) 
  * @access public	
  */	
  function parseForm() {
	$this->perfil_usuario->id_tipo_usuario = $_POST["id_tipo_usuario"];
	
	$this->perfil_usuario->id_modulo = Array();
	if (isset($_POST["id_modulo_perfil"])){
	  $this->perfil_usuario->id_modulo = $_POST["id_modulo_perfil"];
	}
  }
}
?>
