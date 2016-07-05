<?
/**
 * Maneja todas las acciones de administración de Edads
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de Edad
	* @var object 
	*/
  var $edad;

  /**
	* Variable para el manejo de la clase EdadDAO
	* @var object 
	*/
  var $edad_dao;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->edad_dao = new EdadDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->edad_dao->Insertar($this->edad);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->edad_dao->Actualizar($this->edad);
    }
    else if ($accion == 'borrar') {
			$this->edad_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de Edad (variable de clase) 
  * @access public	
  */	
  function parseForm() {
		if (isset($_POST["id"])){
    	$this->edad->id = $_POST["id"];
		}
  	$this->edad->nombre = $_POST["nombre"];
  }
}
?>