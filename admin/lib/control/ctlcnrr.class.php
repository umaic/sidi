<?
/**
 * Maneja todas las acciones de administración de Cnrrs
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

	/**
	* VO de Cnrr
	* @var object 
	*/
	var $cnrr;

	/**
	* Variable para el manejo de la clase CnrrDAO
	* @var object 
	*/
	var $cnrr_dao;

	/**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
	function ControladorPagina($accion) {

		$this->cnrr_dao = new CnrrDAO();

		if ($accion == 'actualizar') {
			$this->parseForm();
			$this->cnrr_dao->Actualizar($this->cnrr);
		}

		if ($accion == 'actualizarPerfil') {
			
			
			isset($_POST["admin_org"])	? $admin_org = 1 : $admin_org = 0;
			isset($_POST["alimentacion_org"])	? $alimentacion_org = 1 : $alimentacion_org = 0;
			isset($_POST["consulta_org"])	? $consulta_org = 1 : $consulta_org = 0;
			isset($_POST["ver_contacto_org"])	? $ver_contacto_org = 1 : $ver_contacto_org = 0;
			
			$this->cnrr_dao->ActualizarPerfil($_POST["id_tipo_usuario"],$admin_org,$alimentacion_org,$consulta_org,$ver_contacto_org);
		}

	}

	/**
  * Realiza el Parse de las variables de la forma y las asigna al VO de Cnrr (variable de clase) 
  * @access public	
  */	
	function parseForm() {
		
		isset($_POST["id_enfoques"])	?	$this->cnrr->id_enfoques = $_POST["id_enfoques"] : $this->cnrr->id_enfoques = array();
		isset($_POST["id_poblaciones"])	?	$this->cnrr->id_poblaciones = $_POST["id_poblaciones"] : $this->cnrr->id_poblaciones = array();
		isset($_POST["id_sectores"])	?	$this->cnrr->id_sectores = $_POST["id_sectores"] : $this->cnrr->id_sectores = array();
		isset($_POST["id_tipos"])		?	$this->cnrr->id_tipos = $_POST["id_tipos"] : $this->cnrr->id_tipos = array();
		
		isset($_POST["id_enfoques_ocha"])		?	$this->cnrr->id_enfoques_ocha = $_POST["id_enfoques_ocha"] : $this->cnrr->id_enfoques_ocha = array();
		isset($_POST["id_poblaciones_ocha"])	?	$this->cnrr->id_poblaciones_ocha = $_POST["id_poblaciones_ocha"] : $this->cnrr->id_poblaciones_ocha = array();
		isset($_POST["id_sectores_ocha"])		?	$this->cnrr->id_sectores_ocha = $_POST["id_sectores_ocha"] : $this->cnrr->id_sectores_ocha = array();
		isset($_POST["id_tipos_ocha"])			?	$this->cnrr->id_tipos_ocha = $_POST["id_tipos_ocha"] : $this->cnrr->id_tipos_ocha = array();
		
	}
}
?>