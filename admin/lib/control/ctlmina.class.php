<?
/**
 * Maneja todas las acciones de administración de Minas
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de Mina
	* @var object 
	*/
  var $mina;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->mina_dao = new MinaDAO();

 	if ($accion == 'importar') {
		$this->mina_dao->ImportarCSV($_FILES['archivo_csv'],$_POST["id_op"],$_POST["separador"]);
	}	
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de Mina (variable de clase) 
  * @access public	
  */	
  function parseForm() {
  }
}  

?>