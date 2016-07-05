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
  var $barrio;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->barrio_dao = new BarrioDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->barrio_dao->Insertar($this->barrio);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->barrio_dao->Actualizar($this->barrio);
    }
    else if ($accion == 'borrar') {
			$this->barrio_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de UnidadDatoSector (variable de clase) 
  * @access public	
  */	
  function parseForm() {
	$this->barrio->id = $_POST["id"];
	$this->barrio->id_mun = $_POST["id_mun"];
	$this->barrio->id_pob = $_POST["id_pob"];
	$this->barrio->id_comuna = $_POST["id_comuna"];
  	$this->barrio->nombre = $_POST["nombre"];
  }
}
?>