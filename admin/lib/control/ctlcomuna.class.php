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
  var $comuna;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->comuna_dao = new ComunaDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->comuna_dao->Insertar($this->comuna);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->comuna_dao->Actualizar($this->comuna);
    }
    else if ($accion == 'borrar') {
			$this->comuna_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de UnidadDatoSector (variable de clase) 
  * @access public	
  */	
  function parseForm() {
	$this->comuna->id = $_POST["id"];
	$this->comuna->id_mun = $_POST["id_mun"];
	$this->comuna->id_pob = $_POST["id_pob"];
  	$this->comuna->nombre = $_POST["nombre"];
  }
}
?>