<?
/**
 * Maneja todas las acciones de administración de Poblaciones
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de Poblacion
	* @var object 
	*/
  var $poblacion;

  /**
	* Variable para el manejo de la clase PoblacionDAO
	* @var object 
	*/
  var $poblacion_dao;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->poblacion_dao = new PoblacionDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->poblacion_dao->Insertar($this->poblacion);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->poblacion_dao->Actualizar($this->poblacion);
    }
    else if ($accion == 'borrar') {
			$this->poblacion_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de Poblacion (variable de clase) 
  * @access public	
  */	
  function parseForm() {
	if (isset($_POST["id"])){
		$this->poblacion->id = $_POST["id"];
	}
  	$this->poblacion->nombre_es = $_POST["nombre_es"];
  	$this->poblacion->nombre_in = $_POST["nombre_in"];
  }
}
?>