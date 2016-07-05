<?
/**
 * Maneja todas las acciones de administración de Estados de Proyecto
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de EstadoProyecto
	* @var object 
	*/
  var $estado_proyecto;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->estado_proyecto_dao = new EstadoProyectoDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->estado_proyecto_dao->Insertar($this->estado_proyecto);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->estado_proyecto_dao->Actualizar($this->estado_proyecto);
    }
    else if ($accion == 'borrar') {
			$this->estado_proyecto_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de EstadoProyecto (variable de clase) 
  * @access public	
  */	
  function parseForm() {
	if (isset($_POST["id"])){
		$this->estado_proyecto->id = $_POST["id"];
	}
  	$this->estado_proyecto->nombre = $_POST["nombre"];
  }
}
?>