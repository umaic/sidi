<?
/**
 * Maneja todas las acciones de administración de Tipo de Desplazamiento
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de TipoDesplazamiento
	* @var object 
	*/
  var $tipo_despla;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->tipo_despla_dao = new TipoDesplazamientoDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->tipo_despla_dao->Insertar($this->tipo_despla);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->tipo_despla_dao->Actualizar($this->tipo_despla);
    }
    else if ($accion == 'borrar') {
			$this->tipo_despla_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de TipoDesplazamiento (variable de clase) 
  * @access public	
  */	
  function parseForm() {
		if (isset($_POST["id"])){
    	$this->tipo_despla->id = $_POST["id"];
		}
  	$this->tipo_despla->nombre = $_POST["nombre"];
  }
}
?>