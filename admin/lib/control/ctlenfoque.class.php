<?
/**
 * Maneja todas las acciones de administración de Enfoques
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de Enfoque
	* @var object 
	*/
  var $enfoque;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->enfoque_dao = new EnfoqueDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->enfoque_dao->Insertar($this->enfoque);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->enfoque_dao->Actualizar($this->enfoque);
    }
    else if ($accion == 'borrar') {
			$this->enfoque_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de Enfoque (variable de clase) 
  * @access public	
  */	
  function parseForm() {
	if (isset($_POST["id"])){
		$this->enfoque->id = $_POST["id"];
	}
  	$this->enfoque->nombre_es = $_POST["nombre_es"];
  	$this->enfoque->nombre_in = $_POST["nombre_in"];
  }
}
?>