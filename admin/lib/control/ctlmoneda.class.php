<?
/**
 * Maneja todas las acciones de administración de Monedas
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de Moneda
	* @var object 
	*/
  var $moneda;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->moneda_dao = new MonedaDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->moneda_dao->Insertar($this->moneda);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->moneda_dao->Actualizar($this->moneda);
    }
    else if ($accion == 'borrar') {
			$this->moneda_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de Moneda (variable de clase) 
  * @access public	
  */	
  function parseForm() {
	if (isset($_POST["id"])){
		$this->moneda->id = $_POST["id"];
	}
  	$this->moneda->nombre = $_POST["nombre"];
  }
}
?>