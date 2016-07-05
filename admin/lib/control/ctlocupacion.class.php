<?
/**
 * Maneja todas las acciones de administración de Ocupacions
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de Ocupacion
	* @var object 
	*/
  var $ocupacion;

  /**
	* Variable para el manejo de la clase OcupacionDAO
	* @var object 
	*/
  var $ocupacion_dao;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->ocupacion_dao = new OcupacionDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->ocupacion_dao->Insertar($this->ocupacion);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->ocupacion_dao->Actualizar($this->ocupacion);
    }
    else if ($accion == 'borrar') {
			$this->ocupacion_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de Ocupacion (variable de clase) 
  * @access public	
  */	
  function parseForm() {
		if (isset($_POST["id"])){
    	$this->ocupacion->id = $_POST["id"];
		}
  	$this->ocupacion->nombre = $_POST["nombre"];
  }
}
?>