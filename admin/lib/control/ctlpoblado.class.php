<?
/**
 * Maneja todas las acciones de administración de MPIOS
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de UnidadDatoSector
	* @var object 
	*/
  var $poblado;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->poblado_dao = new PobladoDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->poblado_dao->Insertar($this->poblado);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->poblado_dao->Actualizar($this->poblado);
    }
    else if ($accion == 'borrar') {
			$this->poblado_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de UnidadDatoSector (variable de clase) 
  * @access public	
  */	
  function parseForm() {
	$this->poblado->id = $_POST["id"];
	$this->poblado->id_mun = $_POST["id_mun"];
  	$this->poblado->nombre = $_POST["nombre"];
  	$this->poblado->clase = $_POST["clase"];
  	$this->poblado->acto_admin = $_POST["acto_admin"];
  	$this->poblado->nacimiento = $_POST["nacimiento"];
  }
}
?>