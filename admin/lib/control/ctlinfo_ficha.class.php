<?
/**
 * Maneja todas las acciones de administración de Fichas de Información de Graficas y Resumenes
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

	/**
	 * VO de InfoFicha
	 * @var object 
	 */
	var $info_ficha;

	/**
	 * Variable para el manejo de la clase InfoFichaDAO
	 * @var object 
	 */
	var $info_ficha_dao;

	/**
	 * Constructor
	 * Crea la conexión a la base de datos y ejecuta la accion
	 * @access public
	 * @param string $accion Variable que indica la accion a realizar
	 */	
	function ControladorPagina($accion) {

		$this->info_ficha_dao = new InfoFichaDAO();

		if ($accion == 'insertar') {
			$this->parseForm();
			$this->info_ficha_dao->Insertar($this->info_ficha);
		}
		else if ($accion == 'actualizar') {
			$this->parseForm();
			$this->info_ficha_dao->Actualizar($this->info_ficha);
		}
		else if ($accion == 'borrar') {
			$this->info_ficha_dao->Borrar($_GET["id"]);
		}
	}

	/**
	 * Realiza el Parse de las variables de la forma y las asigna al VO de InfoFicha (variable de clase) 
	 * @access public	
	 */	
	function parseForm() {
		if (isset($_POST["id"])){
			$this->info_ficha->id = $_POST["id"];
		}
		$this->info_ficha->modulo = $_POST["modulo"];
		$this->info_ficha->texto = $_POST["texto"];
	}
}
?>
