<?
/**
 * Maneja todas las acciones de administración de Sexos
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de Sexo
	* @var object 
	*/
  var $sexo;

  /**
	* Variable para el manejo de la clase SexoDAO
	* @var object 
	*/
  var $sexo_dao;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->sexo_dao = new SexoDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->sexo_dao->Insertar($this->sexo);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->sexo_dao->Actualizar($this->sexo);
    }
    else if ($accion == 'borrar') {
			$this->sexo_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de Sexo (variable de clase) 
  * @access public	
  */	
  function parseForm() {
		if (isset($_POST["id"])){
    	$this->sexo->id = $_POST["id"];
		}
  	$this->sexo->nombre = $_POST["nombre"];
  }
}
?>