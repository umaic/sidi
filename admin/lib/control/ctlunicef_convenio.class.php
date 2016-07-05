<?
/**
 * Maneja todas las acciones de administración de los convenios
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

		$this->dao = new UnicefConvenioDAO();
		$this->vo = new UnicefConvenio();

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
        
        $date = new Date();

		$this->vo->id = (isset($_POST["id"])) ? $_POST["id"] : 0;
		$this->vo->id_convenio = (isset($_POST["id_convenio"]) && strlen($_POST["id_convenio"]) > 0) ? $_POST["id_convenio"] : 0;
		$this->vo->id_funcionario = (isset($_POST["id_funcionario"]) && strlen($_POST["id_funcionario"]) > 0) ? $_POST["id_funcionario"] : 0;
		$this->vo->id_estado = (isset($_POST["id_estado"]) && strlen($_POST["id_estado"]) > 0) ? $_POST["id_estado"] : 0;
		$this->vo->id_actividad = (isset($_POST["id_actividad"])) ? $_POST["id_actividad"] : 0;
		$this->vo->id_socio_implementador = (isset($_POST["id_socio_implementador"])) ? $_POST["id_socio_implementador"] : array();
		$this->vo->codigo =  (isset($_POST["codigo"])) ? $_POST["codigo"] : '';
		$this->vo->nombre =  (isset($_POST["nombre"])) ? $_POST["nombre"] : '';
		$this->vo->aliados =  (isset($_POST["aliados"])) ? $_POST["aliados"] : '';
		$this->vo->fecha_ini =  (isset($_POST["fecha_ini"])) ? $_POST["fecha_ini"] : '';
		$this->vo->fecha_fin =  (isset($_POST["fecha_fin"])) ? $_POST["fecha_fin"] : '';
		$this->vo->presupuesto_cop =  (isset($_POST["presupuesto_cop"])) ? $_POST["presupuesto_cop"] : '';
		$this->vo->presupuesto_ex =  (isset($_POST["presupuesto_ex"])) ? $_POST["presupuesto_ex"] : '';
		$this->vo->id_mon_ex =  (isset($_POST["id_mon_ex"])) ? $_POST["id_mon_ex"] : '';
		$this->vo->aporte_unicef_cop =  (isset($_POST["aporte_unicef_cop"])) ? $_POST["aporte_unicef_cop"] : '';
		$this->vo->aporte_unicef_ex =  (isset($_POST["aporte_unicef_ex"])) ? $_POST["aporte_unicef_ex"] : '';
		$this->vo->id_mon_ex_aporte_unicef =  (isset($_POST["id_mon_ex_aporte_unicef"])) ? $_POST["id_mon_ex_aporte_unicef"] : '';
		$this->vo->otros_fondos_cop =  (isset($_POST["otros_fondos_cop"])) ? $_POST["otros_fondos_cop"] : '';
		$this->vo->otros_fondos_ex =  (isset($_POST["otros_fondos_ex"])) ? $_POST["otros_fondos_ex"] : '';
		$this->vo->id_mon_ex_otros_fondos =  (isset($_POST["id_mon_ex_otros_fondos"])) ? $_POST["id_mon_ex_otros_fondos"] : 0;
		//$this->vo->id_donante_otros_fondos =  (isset($_POST["id_donante_otros_fondos"])) ? $_POST["id_donante_otros_fondos"] : array();
		$this->vo->id_fuente_otros_fondos =  (isset($_POST["id_fuente_otros_fondos"])) ? $_POST["id_fuente_otros_fondos"] : array();
		$this->vo->avances_cop =  (isset($_POST["avances_cop"])) ? $_POST["avances_cop"] : array();
		$this->vo->avances_ex =  (isset($_POST["avances_ex"])) ? $_POST["avances_ex"] : array();
		$this->vo->avances_fecha =  (isset($_POST["avances_fecha"])) ? $_POST["avances_fecha"] : array();
		$this->vo->id_mon_ex_avances =  (isset($_POST["id_mon_ex_avances"])) ? $_POST["id_mon_ex_avances"] : array();

        $this->vo->duracion_meses = $date->RestarFechas($this->vo->fecha_ini,$this->vo->fecha_fin,'meses');
        
		$this->vo->numero_avances = count($this->vo->avances_fecha);
        foreach ($this->vo->avances_fecha as $i=>$fecha){
            //$this->vo->id_donante_avances[$i] =  (isset($_POST["id_donante_avances_$i"])) ? $_POST["id_donante_avances_$i"] : array();
            $this->vo->id_fuente_avances[$i] =  (isset($_POST["id_fuente_avances_$i"])) ? $_POST["id_fuente_avances_$i"] : array();
        }
        
        $this->vo->cobertura =  $_POST["cobertura"];

        if ($this->vo->cobertura != 'N'){
            $this->vo->id_depto = $_POST['id_depto'];
            if ($this->vo->cobertura == 'M')    $this->vo->id_mun = $_POST['id_mun'];
        }
    }
}
?>
