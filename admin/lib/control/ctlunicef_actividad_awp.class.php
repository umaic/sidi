<?
/**
 * Maneja todas las acciones de administración de los resultados CPD
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

	/**
	* VO de ActividadAwp
	* @var object 
	*/
	var $vo;

	/**
	* Variable para el manejo de la clase ActividadAwpDAO
	* @var object 
	*/
	var $dao;

	/**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
	function ControladorPagina($accion) {

		$this->dao = new UnicefActividadAwpDAO();
		$this->vo = new UnicefActividadAwp();

		if ($accion == 'insertar') {
			$this->parseForm();
			$this->dao->Insertar($this->vo);
		}
		else if ($accion == 'actualizar') {
			$this->parseForm();
			$this->dao->Actualizar($this->vo);
		}
		else if ($accion == 'borrar') {
			$this->dao->Borrar($_GET["id"]);
		}
	}

	/**
  * Realiza el Parse de las variables de la forma y las asigna al VO de ActividadAwp (variable de clase) 
  * @access public	
  */	
	function parseForm() {
        
        $this->vo->id = (isset($_POST["id"])) ? $_POST["id"] : 0;
        $this->vo->id_producto = (isset($_POST["id_producto"])) ? $_POST["id_producto"] : 0;
        $this->vo->id_tema_undaf_1 = (isset($_POST["id_tema_undaf_1"])) ? $_POST["id_tema_undaf_1"] : 0;
        $this->vo->id_tema_undaf_2 = (isset($_POST["id_tema_undaf_2"])) ? $_POST["id_tema_undaf_2"] : 0;
        $this->vo->id_tema_undaf_3 = (isset($_POST["id_tema_undaf_3"])) ? $_POST["id_tema_undaf_3"] : 0;
        $this->vo->id_estado = (isset($_POST["id_estado"])) ? $_POST["id_estado"] : 0;
        $this->vo->nombre =  (isset($_POST["nombre"])) ? $_POST["nombre"] : '';
        $this->vo->codigo =  (isset($_POST["codigo"])) ? $_POST["codigo"] : '';
		$this->vo->aaaa = (isset($_POST["aaaa"])) ? $_POST["aaaa"] : 0;
    }
}
?>
