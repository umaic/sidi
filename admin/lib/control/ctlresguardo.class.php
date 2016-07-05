<?
/**
 * Maneja todas las acciones de administración de Resguardoes
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de Resguardo
	* @var object 
	*/
  var $resguardo;

  /**
	* Variable para el manejo de la clase ResguardoDAO
	* @var object 
	*/
  var $resguardo_dao;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->resguardo_dao = new ResguardoDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->resguardo_dao->Insertar($this->resguardo);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->resguardo_dao->Actualizar($this->resguardo);
    }
    else if ($accion == 'borrar') {
			$this->resguardo_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de Resguardo (variable de clase) 
  * @access public	
  */	
  function parseForm() {
		if (isset($_POST["id"])){
    	$this->resguardo->id = $_POST["id"];
		}
  	$this->resguardo->nombre = $_POST["nombre"];

		$this->resguardo->id_deptos = Array();
		if (isset($_POST["id_depto"])){
    	$this->resguardo->id_deptos = $_POST["id_depto"];
		}
  	
		$this->resguardo->id_muns = Array();
		if (isset($_POST["id_muns"])){
    	$this->resguardo->id_muns = $_POST["id_muns"];
		}
		
  }
}
?>