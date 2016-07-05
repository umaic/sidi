<?
/**
 * Maneja todas las acciones de administración de Tipo de Organizacions
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de TipoOrganizacion
	* @var object 
	*/
  var $tipo_org;

  /**
	* Variable para el manejo de la clase TipoOrganizacionDAO
	* @var object 
	*/
  var $tipo_org_dao;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->tipo_org_dao = new TipoOrganizacionDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->tipo_org_dao->Insertar($this->tipo_org);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->tipo_org_dao->Actualizar($this->tipo_org);
    }
    else if ($accion == 'borrar') {
			$this->tipo_org_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de TipoOrganizacion (variable de clase) 
  * @access public	
  */	
  function parseForm() {
	if (isset($_POST["id"])){
		$this->tipo_org->id = $_POST["id"];
	}
  	$this->tipo_org->nombre_es = $_POST["nombre_es"];
  	$this->tipo_org->nombre_in = $_POST["nombre_in"];
  }
}
?>