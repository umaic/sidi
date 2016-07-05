<?
/**
 * Maneja todas las acciones de administración de los resultados CPD
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

	/**
	* VO de Resultado
	* @var object 
	*/
	var $vo;

	/**
	* Variable para el manejo de la clase ResultadoDAO
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

		$this->vo = new UnicefResultado();
		$this->dao = new UnicefResultadoDAO();

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
  * Realiza el Parse de las variables de la forma y las asigna al VO de Resultado (variable de clase) 
  * @access public	
  */	
	function parseForm() {
        
        $this->vo->id = (isset($_POST["id"])) ? $_POST["id"] : 0;
        $this->vo->id_periodo = (isset($_POST["id_periodo"])) ? $_POST["id_periodo"] : 0;
        $this->vo->id_sub_componente = (isset($_POST["id_sub_componente"])) ? $_POST["id_sub_componente"] : 0;
        $this->vo->nombre =  (isset($_POST["nombre"])) ? $_POST["nombre"] : '';
        $this->vo->codigo =  (isset($_POST["codigo"])) ? $_POST["codigo"] : '';
        
        // Indicadores
        $aaaa_ini = $_POST['aaaa_ini'];
        $aaaa_fin = $_POST['aaaa_fin'];
        if (isset($_POST["id_indicador"]))  $this->vo->id_indicador = $_POST["id_indicador"];

        foreach ($this->vo->id_indicador as $i=>$ind){
            $this->vo->linea_base[] =  (isset($_POST["linea_base_$i"])) ? $_POST["linea_base_$i"] : '';
            for ($a=$aaaa_ini;$a<=$aaaa_fin;$a++){
                if (isset($_POST["indicador_valor_".$a."_".$i]) && strlen($_POST["indicador_valor_".$a."_".$i]) > 0)    $this->vo->indicador_valor[$i][$a] = $_POST["indicador_valor_".$a."_".$i];
            }
        }
        
	}
}
?>
