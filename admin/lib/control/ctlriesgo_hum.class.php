<?
/**
 * Maneja todas las acciones de administración de Riesgos Humanitarios
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de RiesgoHum
	* @var object 
	*/
  var $riesgo_hum;

  /**
	* Variable para el manejo de la clase RiesgoHumDAO
	* @var object 
	*/
  var $riesgo_hum_dao;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->riesgo_hum_dao = new RiesgoHumDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->riesgo_hum_dao->Insertar($this->riesgo_hum);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->riesgo_hum_dao->Actualizar($this->riesgo_hum);
    }
    else if ($accion == 'borrar') {
			$this->riesgo_hum_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de RiesgoHum (variable de clase) 
  * @access public	
  */	
  function parseForm() {
		if (isset($_POST["id"])){
    	$this->riesgo_hum->id = $_POST["id"];
		}
  	$this->riesgo_hum->nombre = $_POST["nombre"];
		
		$this->tipo_evento->icono = "";
		if (isset($_POST["icono"])){
  		$this->riesgo_hum->icono = $_POST["icono"];
		}		
		
  }
}
?>