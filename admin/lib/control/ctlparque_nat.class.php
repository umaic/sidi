<?
/**
 * Maneja todas las acciones de administración de ParqueNates
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de ParqueNat
	* @var object 
	*/
  var $parque_nat;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->parque_nat_dao = new ParqueNatDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->parque_nat_dao->Insertar($this->parque_nat);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->parque_nat_dao->Actualizar($this->parque_nat);
    }
    else if ($accion == 'borrar') {
			$this->parque_nat_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de ParqueNat (variable de clase) 
  * @access public	
  */	
  function parseForm() {
		if (isset($_POST["id"])){
    	$this->parque_nat->id = $_POST["id"];
		}
  	$this->parque_nat->nombre = $_POST["nombre"];

		$this->parque_nat->id_deptos = Array();
		if (isset($_POST["id_depto"])){
    	$this->parque_nat->id_deptos = $_POST["id_depto"];
		}
  	
		$this->parque_nat->id_muns = Array();
		if (isset($_POST["id_muns"])){
    	$this->parque_nat->id_muns = $_POST["id_muns"];
		}
		
  }
}
?>