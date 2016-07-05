<?
/**
 * Maneja todas las acciones de administración de ContactoCols
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de ContactoCol
	* @var object 
	*/
  var $contacto_col;

  /**
	* Variable para el manejo de la clase ContactoColDAO
	* @var object 
	*/
  var $contacto_col_dao;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->contacto_col_dao = new ContactoColDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->contacto_col_dao->Insertar($this->contacto_col);
    }
    else if ($accion == 'actualizar') {
      $this->parseForm();
      $this->contacto_col_dao->Actualizar($this->contacto_col);
    }
    else if ($accion == 'borrar') {
			$this->contacto_col_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de ContactoCol (variable de clase) 
  * @access public	
  */	
  function parseForm() {
		if (isset($_POST["id"])){
    	$this->contacto_col->id = $_POST["id"];
		}
  	$this->contacto_col->nombre = $_POST["nombre"];
  }
}
?>
