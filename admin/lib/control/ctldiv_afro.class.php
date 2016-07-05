<?
/**
 * Maneja todas las acciones de administración de DivAfroes
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de DivAfro
	* @var object 
	*/
  var $div_afro;


  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->div_afro_dao = new DivAfroDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->div_afro_dao->Insertar($this->div_afro);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->div_afro_dao->Actualizar($this->div_afro);
    }
    else if ($accion == 'borrar') {
			$this->div_afro_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de DivAfro (variable de clase) 
  * @access public	
  */	
  function parseForm() {
		if (isset($_POST["id"])){
    	$this->div_afro->id = $_POST["id"];
		}
  	$this->div_afro->nombre = $_POST["nombre"];

		$this->div_afro->id_deptos = Array();
		if (isset($_POST["id_depto"])){
    	$this->div_afro->id_deptos = $_POST["id_depto"];
		}
  	
		$this->div_afro->id_muns = Array();
		if (isset($_POST["id_muns"])){
    	$this->div_afro->id_muns = $_POST["id_muns"];
		}
		
  }
}
?>