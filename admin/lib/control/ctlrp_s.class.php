<?
/**
 * Maneja todas las acciones de administración de Tipo de Eventos
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

	/**
	* VO de ReporteSemanal
	* @var object 
	*/
	var $actor;

	/**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
	function ControladorPagina($accion) {

		$this->dao = new ReporteSemanalDAO();
		$this->vo = new ReporteSemanal();
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
  * Realiza el Parse de las variables de la forma y las asigna al VO de ReporteSemanal (variable de clase) 
  * @access public	
  */	
	function parseForm() {

        $this->vo->id = (isset($_POST["id"]) && strlen($_POST["id"]) > 0) ? $_POST["id"] : 0;
        $this->vo->destacado_esp =  (isset($_POST["destacado_esp"])) ? addslashes($_POST["destacado_esp"]) : '';
        $this->vo->contenido_esp =  (isset($_POST["contenido_esp"])) ? addslashes($_POST["contenido_esp"]) : '';
        $this->vo->destacado_ing =  (isset($_POST["destacado_ing"])) ? addslashes($_POST["destacado_ing"]) : '';
        $this->vo->contenido_ing =  (isset($_POST["contenido_ing"])) ? addslashes($_POST["contenido_ing"]) : '';
        $this->vo->f_ini =  (isset($_POST["f_ini"])) ? $_POST["f_ini"] : '';
        $this->vo->f_fin =  (isset($_POST["f_fin"])) ? $_POST["f_fin"] : '';
        $this->vo->trend_f_ini =  (isset($_POST["trend_f_ini"])) ? $_POST["trend_f_ini"] : '';
        
        $f_ini_t = strtotime($this->vo->f_ini);
        $f_fin_t = strtotime($this->vo->f_fin);
        $mes_ini = date('m',$f_ini_t)*1;
        $mes_fin = date('m',$f_fin_t)*1;
        $dia_ini = date('d',$f_ini_t);
        $dia_fin = date('d',$f_fin_t);
        $aaaa_ini = date('Y',$f_ini_t);
        $aaaa_fin = date('Y',$f_fin_t);
        
        $mes_ing = array('','January','February','March','April','May','June','Juli','August','September','October','November','December');
        $mes_esp = array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

        $mes_ini_txt = ($mes_ini != $mes_fin) ? $mes_esp[$mes_ini] : '';
        $aaaa_ini_txt = ($aaaa_ini != $aaaa_fin) ? $aaaa_ini : '';
        $this->vo->titulo_esp = "N&Uacute;MERO: ".date('W',$f_ini_t)." | $dia_ini $mes_ini_txt $aaaa_ini_txt - $dia_fin $mes_esp[$mes_fin] $aaaa_fin";
        
        $mes_ini_txt = ($mes_ini != $mes_fin) ? $mes_ing[$mes_ini] : '';
        $this->vo->titulo_ing = "ISSUE: ".date('W',$f_ini_t)." | $dia_ini $mes_ini_txt $aaaa_ini_txt - $dia_fin $mes_ing[$mes_fin] $aaaa_fin";
		
	}
}
?>
