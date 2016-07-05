<?
/**
 * Maneja todas las acciones de administración de Tipo de Eventos
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de UnidadDatoSector
	* @var object 
	*/
  var $cat_d_s;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->cat_d_s_dao = new UnidadDatoSectorDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->cat_d_s_dao->Insertar($this->cat_d_s);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->cat_d_s_dao->Actualizar($this->cat_d_s);
    }
    else if ($accion == 'borrar') {
			$this->cat_d_s_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de UnidadDatoSector (variable de clase) 
  * @access public	
  */	
  function parseForm() {
		if (isset($_POST["id"])){
    	$this->cat_d_s->id = $_POST["id"];
		}
  	$this->cat_d_s->nombre = $_POST["nombre"];
  }
}
?>