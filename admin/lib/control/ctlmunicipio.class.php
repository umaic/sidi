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
  var $municipio;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->municipio_dao = new MunicipioDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->municipio_dao->Insertar($this->municipio);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->municipio_dao->Actualizar($this->municipio);
    }
    else if ($accion == 'borrar') {
			$this->municipio_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de UnidadDatoSector (variable de clase) 
  * @access public	
  */	
  function parseForm() {
	$this->municipio->id = $_POST["id"];
	$this->municipio->id_depto = $_POST["id_depto"];
  	$this->municipio->nombre = $_POST["nombre"];
  	
  	$this->municipio->manzanas = (isset($_POST["manzanas"]) && $_POST["manzanas"] != "") ? $_POST["manzanas"] : "NULL";
  	$this->municipio->acto_admin = $_POST["acto_admin"];
  	$this->municipio->nacimiento = (isset($_POST["nacimiento"]) && $_POST["nacimiento"] != "") ? $_POST["nacimiento"] : "NULL";
  }
}
?>