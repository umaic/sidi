<?
/**
 * Maneja todas las acciones de administración de Regiones
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de Region
	* @var object 
	*/
  var $region;

  /**
	* Variable para el manejo de la clase RegionDAO
	* @var object 
	*/
  var $region_dao;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->region_dao = new RegionDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->region_dao->Insertar($this->region);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->region_dao->Actualizar($this->region);
    }
    else if ($accion == 'borrar') {
			$this->region_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de Region (variable de clase) 
  * @access public	
  */	
  function parseForm() {
		if (isset($_POST["id"])){
    	$this->region->id = $_POST["id"];
		}
  		$this->region->nombre = $_POST["nombre"];

		$this->region->id_deptos = Array();
		if (isset($_POST["id_depto"])){
    	$this->region->id_deptos = $_POST["id_depto"];
		}
  	
		$this->region->id_muns = Array();
		if (isset($_POST["id_muns"])){
    	$this->region->id_muns = $_POST["id_muns"];
		}
		
  }
}
?>