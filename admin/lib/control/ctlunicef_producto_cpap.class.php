<?
/**
 * Maneja todas las acciones de administración de los productos CPAP
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

	/**
	* VO de ProductoCpap
	* @var object 
	*/
	var $vo;

	/**
	* Variable para el manejo de la clase ProductoCpapDAO
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

		$this->vo = new UnicefProductoCpap();
        $this->dao = new UnicefProductoCpapDAO();

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
  * Realiza el Parse de las variables de la forma y las asigna al VO de ProductoCpap (variable de clase) 
  * @access public	
  */	
	function parseForm() {
        
        $this->vo->id = (isset($_POST["id"])) ? $_POST["id"] : 0;
        $this->vo->id_resultado = (isset($_POST["id_resultado"])) ? $_POST["id_resultado"] : 0;
        $this->vo->id_indicador = (isset($_POST["id_indicador"])) ? $_POST["id_indicador"] : array();
        $this->vo->nombre =  (isset($_POST["nombre"])) ? $_POST["nombre"] : '';
        $this->vo->codigo =  (isset($_POST["codigo"])) ? $_POST["codigo"] : '';
        
        $aaaa_ini = $_POST['aaaa_ini'];
        $aaaa_fin = $_POST['aaaa_fin'];
        foreach ($this->vo->id_indicador as $i=>$ind){
            $this->vo->linea_base[] =  (isset($_POST["linea_base_$i"])) ? $_POST["linea_base_$i"] : '';
            $this->vo->meta[] =  (isset($_POST["meta_$i"])) ? $_POST["meta_$i"] : '';
            for ($a=$aaaa_ini;$a<=$aaaa_fin;$a++){
                if (isset($_POST["indicador_valor_".$a."_".$i]) && strlen($_POST["indicador_valor_".$a."_".$i]) > 0)    $this->vo->indicador_valor[$i][$a] = $_POST["indicador_valor_".$a."_".$i];
                if (isset($_POST["meta_valor_".$a."_".$i]) && strlen($_POST["meta_valor_".$a."_".$i]) > 0)    $this->vo->meta_valor[$i][$a] = $_POST["meta_valor_".$a."_".$i];
            }
        }

        /*
        for ($a=$aaaa_ini;$a<=$aaaa_fin;$a++){
            if (isset($_POST["indicador_valor_$a"]) && strlen($_POST["indicador_valor_$a"]))    $this->vo->indicador_valor[$a] = $_POST["indicador_valor_$a"];
            if (isset($_POST["meta_valor_$a"]) && strlen($_POST["meta_valor_$a"]))              $this->vo->meta_valor[$a] = $_POST["meta_valor_$a"];
        }
        */
	}
}
?>
