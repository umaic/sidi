<?
/**
 * Maneja todas las acciones de administración de la Clasificación de los temas de los proyectos 
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

	/**
	 * VO de Clasificacion
	 * @var object 
	 */
	var $clasificacion;

	/**
	 * Constructor
	 * Crea la conexión a la base de datos y ejecuta la accion
	 * @access public
	 * @param string $accion Variable que indica la accion a realizar
	 */	
	function ControladorPagina($accion) {

		$this->clasificacion_dao = new ClasificacionDAO();

		if ($accion == 'insertar') {
			$this->parseForm();
			$this->clasificacion_dao->Insertar($this->clasificacion);
		}
		else if ($accion == 'actualizar') {
			$this->parseForm();
			$this->clasificacion_dao->Actualizar($this->clasificacion);
		}
		else if ($accion == 'borrar') {
			$this->clasificacion_dao->Borrar($_GET["id"]);
		}
	}

	/**
	 * Realiza el Parse de las variables de la forma y las asigna al VO de Clasificacion (variable de clase) 
	 * @access public	
	 */	
	function parseForm() {
		if (isset($_POST["id"])){
			$this->clasificacion->id = $_POST["id"];
		}
		$this->clasificacion->nombre = $_POST["nombre"];
	}
}
?>
