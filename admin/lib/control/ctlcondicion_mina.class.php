<?
/**
 * Maneja todas las acciones de administración de CondicionMinas
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de CondicionMina
	* @var object 
	*/
  var $condicion_mina;

  /**
	* Variable para el manejo de la clase CondicionMinaDAO
	* @var object 
	*/
  var $condicion_mina_dao;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->condicion_mina_dao = new CondicionMinaDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->condicion_mina_dao->Insertar($this->condicion_mina);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->condicion_mina_dao->Actualizar($this->condicion_mina);
    }
    else if ($accion == 'borrar') {
			$this->condicion_mina_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de CondicionMina (variable de clase) 
  * @access public	
  */	
  function parseForm() {
		if (isset($_POST["id"])){
    	$this->condicion_mina->id = $_POST["id"];
		}
  	$this->condicion_mina->nombre = $_POST["nombre"];
  }
}
?>