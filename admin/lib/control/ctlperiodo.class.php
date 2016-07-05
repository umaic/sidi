<?
/**
 * Maneja todas las acciones de administración de Tipo de Eventos
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de Periodo
	* @var object 
	*/
  var $periodo;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->periodo = new Periodo();
    $this->periodo_dao = new PeriodoDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->periodo_dao->Insertar($this->periodo);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->periodo_dao->Actualizar($this->periodo);
    }
    else if ($accion == 'borrar') {
			$this->periodo_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de Periodo (variable de clase) 
  * @access public	
  */	
  function parseForm() {
		if (isset($_POST["id"])){
    	$this->periodo->id = $_POST["id"];
		}
  	$this->periodo->nombre = $_POST["nombre"];
  }
}
?>
