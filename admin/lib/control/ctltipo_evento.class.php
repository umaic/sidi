<?
/**
 * Maneja todas las acciones de administración de Tipo de Eventos
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de TipoEvento
	* @var object 
	*/
  var $tipo_evento;

  /**
	* Variable para el manejo de la clase TipoEventoDAO
	* @var object 
	*/
  var $tipo_evento_dao;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->tipo_evento_dao = new TipoEventoDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->tipo_evento_dao->Insertar($this->tipo_evento);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->tipo_evento_dao->Actualizar($this->tipo_evento);
    }
    else if ($accion == 'borrar') {
			$this->tipo_evento_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de TipoEvento (variable de clase) 
  * @access public	
  */	
  function parseForm() {
		if (isset($_POST["id"])){
    	$this->tipo_evento->id = $_POST["id"];
		}
  	$this->tipo_evento->nombre = $_POST["nombre"];
		$this->tipo_evento->id_cat = $_POST["id_cat"];
		$this->tipo_evento->id_papa = $_POST["id_papa"];
		
		$this->tipo_evento->icono = "";
		if (isset($_POST["icono"])){
  		$this->tipo_evento->icono = $_POST["icono"];
		}
  }
}
?>