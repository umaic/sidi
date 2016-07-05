<?
/**
 * Maneja todas las acciones de administración de EstadoMinas
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de EstadoMina
	* @var object 
	*/
  var $estado_mina;

  /**
	* Variable para el manejo de la clase EstadoMinaDAO
	* @var object 
	*/
  var $estado_mina_dao;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->estado_mina_dao = new EstadoMinaDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->estado_mina_dao->Insertar($this->estado_mina);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->estado_mina_dao->Actualizar($this->estado_mina);
    }
    else if ($accion == 'borrar') {
			$this->estado_mina_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de EstadoMina (variable de clase) 
  * @access public	
  */	
  function parseForm() {
		if (isset($_POST["id"])){
    	$this->estado_mina->id = $_POST["id"];
		}
  	$this->estado_mina->nombre = $_POST["nombre"];
  }
}
?>