<?
/**
 * Maneja todas las acciones de administración de Consecuencias Humanitarias
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de ConsHum
	* @var object 
	*/
  var $cons_hum;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->cons_hum_dao = new ConsHumDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->cons_hum_dao->Insertar($this->cons_hum);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->cons_hum_dao->Actualizar($this->cons_hum);
    }
    else if ($accion == 'borrar') {
			$this->cons_hum_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de ConsHum (variable de clase) 
  * @access public	
  */	
  function parseForm() {
		if (isset($_POST["id"])){
    	$this->cons_hum->id = $_POST["id"];
		}
  	$this->cons_hum->nombre = $_POST["nombre"];
		
		$this->tipo_evento->icono = "";
		if (isset($_POST["icono"])){
  		$this->cons_hum->icono = $_POST["icono"];
		}		
		
  }
}
?>