<?
/**
 * Maneja todas las acciones de administración de Rango Edad
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

	/**
	* VO de RangoEdad
	* @var object 
	*/
	var $rango_edad;

	/**
	* Variable para el manejo de la clase RangoEdadDAO
	* @var object 
	*/
	var $rango_edad_dao;

	/**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
	function ControladorPagina($accion) {

		$this->rango_edad_dao = new RangoEdadDAO();

		if ($accion == 'insertar') {
			$this->parseForm();
			$this->rango_edad_dao->Insertar($this->rango_edad);
		}
		else if ($accion == 'actualizar') {
			$this->parseForm();
			$this->rango_edad_dao->Actualizar($this->rango_edad);
		}
		else if ($accion == 'borrar') {
			$this->rango_edad_dao->Borrar($_GET["id"]);
		}
	}

	/**
  * Realiza el Parse de las variables de la forma y las asigna al VO de RangoEdad (variable de clase) 
  * @access public	
  */	
	function parseForm() {
		if (isset($_POST["id"])){
			$this->rango_edad->id = $_POST["id"];
		}
		$this->rango_edad->id_edad = $_POST["id_edad"];
		$this->rango_edad->nombre = $_POST["nombre"];

	}
}
?>