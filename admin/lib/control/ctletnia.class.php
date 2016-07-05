<?
/**
 * Maneja todas las acciones de administración de Etnias
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de Etnia
	* @var object 
	*/
  var $etnia;

  /**
	* Variable para el manejo de la clase EtniaDAO
	* @var object 
	*/
  var $etnia_dao;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->etnia_dao = new EtniaDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->etnia_dao->Insertar($this->etnia);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->etnia_dao->Actualizar($this->etnia);
    }
    else if ($accion == 'borrar') {
			$this->etnia_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de Etnia (variable de clase) 
  * @access public	
  */	
  function parseForm() {
		if (isset($_POST["id"])){
    	$this->etnia->id = $_POST["id"];
		}
  	$this->etnia->nombre = $_POST["nombre"];
  }
}
?>