<?
/**
 * Maneja todas las acciones de administración de Clase de Desplazamiento
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de ClaseDesplazamiento
	* @var object 
	*/
  var $clase_despla;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->clase_despla_dao = new ClaseDesplazamientoDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->clase_despla_dao->Insertar($this->clase_despla);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->clase_despla_dao->Actualizar($this->clase_despla);
    }
    else if ($accion == 'borrar') {
			$this->clase_despla_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de ClaseDesplazamiento (variable de clase) 
  * @access public	
  */	
  function parseForm() {
		if (isset($_POST["id"])){
    	$this->clase_despla->id = $_POST["id"];
		}
  	$this->clase_despla->nombre = $_POST["nombre"];
  }
}
?>