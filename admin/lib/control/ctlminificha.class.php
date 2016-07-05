<?
/**
 * Maneja todas las acciones de administración de Minifichas
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

	/**
	* VO de Minificha
	* @var object
	*/
	var $Minificha;

	/**
	* Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
	* @access public
	* @param string $accion Variable que indica la accion a realizar
	*/
	function ControladorPagina($accion) {

		$this->Minificha_dao = new MinifichaDAO();

		if ($accion == 'insertar') {

			$mod = array();
			$submods = array();
			if (isset($_POST["mod"]))	$mod = $_POST["mod"];
			if (isset($_POST["submods"]))	$submods = $_POST["submods"];

			$this->Minificha_dao->UpdateInfoMinificha($_POST["id_modulo"],$mod,$submods);
		}
	}

}
?>