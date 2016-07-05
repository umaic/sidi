<?
/**
 * Maneja todas las acciones de administración de Tipo de Eventos
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de Fuente
	* @var object 
	*/
  var $fuente;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->fuente_dao = new FuenteDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->fuente_dao->Insertar($this->fuente);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->fuente_dao->Actualizar($this->fuente);
    }
    else if ($accion == 'borrar') {
			$this->fuente_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de Fuente (variable de clase) 
  * @access public	
  */	
  function parseForm() {
		if (isset($_POST["id"])){
    	$this->fuente->id = $_POST["id"];
		}
  	$this->fuente->nombre = $_POST["nombre"];
  }
}
?>