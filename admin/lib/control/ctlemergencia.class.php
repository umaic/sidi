<?
/**
 * Maneja todas las acciones de administración de Estados de Proyecto
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de Emergencia
	* @var object 
	*/
  var $emergencia;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->emergencia_dao = new EmergenciaDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->emergencia_dao->Insertar($this->emergencia);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->emergencia_dao->Actualizar($this->emergencia);
    }
    else if ($accion == 'borrar') {
			$this->emergencia_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de Emergencia (variable de clase) 
  * @access public	
  */	
  function parseForm() {
	if (isset($_POST["id"])){
		$this->emergencia->id = $_POST["id"];
	}
  	$this->emergencia->nombre = $_POST["nombre"];
  }
}
?>
