<?
/**
 * Maneja todas las acciones de administración de Tipo de Eventos
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de Modulo
	* @var object 
	*/
  var $modulo;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->modulo_dao = new ModuloDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->modulo_dao->Insertar($this->modulo);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->modulo_dao->Actualizar($this->modulo);
    }
    else if ($accion == 'borrar') {
			$this->modulo_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de Modulo (variable de clase) 
  * @access public	
  */	
  function parseForm() {
		if (isset($_POST["id"])){
    	$this->modulo->id = $_POST["id"];
		}
  	$this->modulo->nombre = $_POST["nombre"];
		$this->modulo->id_papa = $_POST["id_papa"];
		
  }
}
?>