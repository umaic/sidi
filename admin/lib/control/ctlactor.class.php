<?
/**
 * Maneja todas las acciones de administración de Tipo de Eventos
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

	/**
	* VO de Actor
	* @var object 
	*/
	var $actor;

	/**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
	function ControladorPagina($accion) {

		$this->actor_dao = new ActorDAO();

		if ($accion == 'insertar') {
			$this->parseForm();
			$this->actor_dao->Insertar($this->actor);
		}
		else if ($accion == 'actualizar') {
			$this->parseForm();
			$this->actor_dao->Actualizar($this->actor);
		}
		else if ($accion == 'borrar') {
			$this->actor_dao->Borrar($_GET["id"]);
		}
	}

	/**
  * Realiza el Parse de las variables de la forma y las asigna al VO de Actor (variable de clase) 
  * @access public	
  */	
	function parseForm() {
		if (isset($_POST["id"])){
			$this->actor->id = $_POST["id"];
		}
		$this->actor->cod_interno = $_POST["cod_interno"];
		$this->actor->nombre = $_POST["nombre"];
		
		if ($_POST["papa"] != ''){
			$this->actor->nivel = 3;
			$this->actor->id_papa = $_POST["papa"];
		}
		
		else if ($_POST["abuelo"] != '' && $_POST["papa"] == ''){
			$this->actor->nivel = 2;
			$this->actor->id_papa = $_POST["abuelo"];
		}
		else if ($_POST["abuelo"] == '' && $_POST["papa"] == ''){
			$this->actor->nivel = 1;
			$this->actor->id_papa = 0;
		}
		
		
	}
}
?>