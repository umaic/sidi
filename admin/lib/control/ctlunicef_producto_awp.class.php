<?
/**
 * Maneja todas las acciones de administración de los productos AWP
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

	/**
	* VO de ProductoAwp
	* @var object 
	*/
	var $vo;

	/**
	* Variable para el manejo de la clase ProductoAwpDAO
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

		$this->dao = new UnicefProductoAwpDAO();

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
  * Realiza el Parse de las variables de la forma y las asigna al VO de ProductoAwp (variable de clase) 
  * @access public	
  */	
	function parseForm() {
        
		$this->vo->id = (isset($_POST["id"])) ? $_POST["id"] : 0;
		$this->vo->id_actividad = (isset($_POST["id_actividad"]) && strlen($_POST["id_actividad"]) > 0) ? $_POST["id_actividad"] : 0;
		//$this->vo->id_donante = (isset($_POST["id_donante"])) ? $_POST["id_donante"] : array();
		$this->vo->id_socio_implementador = (isset($_POST["id_socio_implementador"])) ? $_POST["id_socio_implementador"] : array();
		$this->vo->id_funcionario = (isset($_POST["id_funcionario"])) ? $_POST["id_funcionario"] : array();
		$this->vo->nombre =  (isset($_POST["nombre"])) ? $_POST["nombre"] : '';
		$this->vo->codigo =  (isset($_POST["codigo"])) ? $_POST["codigo"] : '';
		$this->vo->aliados =  (isset($_POST["aliados"])) ? $_POST["aliados"] : '';       
		$this->vo->presupuesto_cop =  (isset($_POST["presupuesto_cop"])) ? $_POST["presupuesto_cop"] : '';       
		$this->vo->presupuesto_ex =  (isset($_POST["presupuesto_ex"])) ? $_POST["presupuesto_ex"] : '';       
		$this->vo->id_presupuesto_desc =  (isset($_POST["id_presupuesto_desc"])) ? $_POST["id_presupuesto_desc"] : array();       
		$this->vo->id_mon_ex =  (isset($_POST["id_mon_ex"])) ? $_POST["id_mon_ex"] : '';       
		$this->vo->aaaa = (isset($_POST["aaaa"])) ? $_POST["aaaa"] : 0;
		$this->vo->funded = (isset($_POST["funded"])) ? 1 : 0;
		$this->vo->unfunded = (isset($_POST["unfunded"])) ? 1 : 0;
		$this->vo->cronograma_1_tri = (isset($_POST["cronograma_1_tri"])) ? 1 : 0;
		$this->vo->cronograma_2_tri = (isset($_POST["cronograma_2_tri"])) ? 1 : 0;
		$this->vo->cronograma_3_tri = (isset($_POST["cronograma_3_tri"])) ? 1 : 0;
		$this->vo->cronograma_4_tri = (isset($_POST["cronograma_4_tri"])) ? 1 : 0;
        $this->vo->indigena = (isset($_POST["indigena"])) ? 1 : 0;
        $this->vo->afro = (isset($_POST["afro"])) ? 1 : 0;
        $this->vo->equidad_genero = (isset($_POST["equidad_genero"])) ? 1 : 0;
        $this->vo->participacion = (isset($_POST["participacion"])) ? 1 : 0;
        $this->vo->prevencion = (isset($_POST["prevencion"])) ? 1 : 0;
        $this->vo->movilizacion = (isset($_POST["movilizacion"])) ? 1 : 0;
		
		// FUENTES
        $this->vo->id_fuente_funded = (isset($_POST["id_fuente_funded"])) ? $_POST["id_fuente_funded"] : array();
		foreach ($this->vo->id_fuente_funded as $id_f){
            $this->vo->fuente_funded_valor[$id_f] =   (isset($_POST["fuente_funded_valor_$id_f"]) && strlen($_POST["fuente_funded_valor_$id_f"]) > 0)  ?   $_POST["fuente_funded_valor_$id_f"] : 0;
        }
        
        
        $this->vo->id_fuente_unfunded = (isset($_POST["id_fuente_unfunded"])) ? $_POST["id_fuente_unfunded"] : array();
		foreach ($this->vo->id_fuente_unfunded as $id_f){
            $this->vo->fuente_unfunded_valor[$id_f] =   (isset($_POST["fuente_unfunded_valor_$id_f"]) && strlen($_POST["fuente_unfunded_valor_$id_f"]) > 0)  ?   $_POST["fuente_unfunded_valor_$id_f"] : 0;
        }


        $this->vo->cobertura =  $_POST["cobertura"];

        if (!in_array($this->vo->cobertura,array('N','I','NA'))){
            $this->vo->id_depto = (isset($_POST['id_depto'])) ? $_POST['id_depto'] : array();
            if ($this->vo->cobertura == 'M')    $this->vo->id_mun = isset($_POST['id_mun']) ? $_POST['id_mun']: array();
        }
    }
}
?>
