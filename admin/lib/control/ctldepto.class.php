<?
/**
 * Maneja todas las acciones de administración de DEPTOS
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de UnidadDatoSector
	* @var object 
	*/
  var $depto;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->depto_dao = new DeptoDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->depto_dao->Insertar($this->depto);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->depto_dao->Actualizar($this->depto);
    }
    else if ($accion == 'borrar') {
			$this->depto_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de UnidadDatoSector (variable de clase) 
  * @access public	
  */	
  function parseForm() {
	$this->depto->id = $_POST["id"];
	$this->depto->id_pais = $_POST["id_pais"];
  	$this->depto->nombre = $_POST["nombre"];
  }
}
?>