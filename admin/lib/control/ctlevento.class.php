<?
/**
 * Maneja todas las acciones de administración de Eventos
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

  /**
	* VO de Evento
	* @var object 
	*/
  var $evento;

  /**
  * Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
  * @access public
	* @param string $accion Variable que indica la accion a realizar
  */	
  function ControladorPagina($accion) {

    $this->evento_dao = new EventoDAO();

    if ($accion == 'insertar') {
      $this->parseForm();
      $this->evento_dao->Insertar($this->evento);
    }
    else if ($accion == 'actualizar') {
      if (!isset($_POST["actualizar_cobertura"])){
		  $this->parseForm();
	      $this->evento_dao->Actualizar($this->evento);
	  }
	  else{
	      $opcion = $_POST["opcion_f"];
		  $this->parseFormCobertura($opcion);
	      $this->evento_dao->ActualizarCobertura($this->evento,$opcion);
	  }
    }
    else if ($accion == 'borrar') {
			$this->evento_dao->Borrar($_GET["id"]);
		}
  }

  /**
  * Realiza el Parse de las variables de la forma y las asigna al VO de Evento (variable de clase) 
  * @access public	
  */	
  function parseForm() {
		if (isset($_POST["id"])){
    	$this->evento->id = $_POST["id"];
		}
  		$this->evento->id_tipo = @array_merge($_POST["id_tipo"],$_POST["id_sub_tipo"]);
		$this->evento->id_cat = @$_POST["id_cat"];
		
		$this->evento->fecha_evento = "";
		if (isset($_POST["fecha_evento"])){
    	$this->evento->fecha_evento = $_POST["fecha_evento"];
		}

		$this->evento->fecha_registro = "";
		if (isset($_POST["fecha_registro"])){
    	$this->evento->fecha_registro = $_POST["fecha_registro"];
		}
		
		$this->evento->lugar = "";
		if (isset($_POST["lugar"])){
    	$this->evento->lugar = $_POST["lugar"];
		}
		
		$this->evento->fuente = "";
		if (isset($_POST["fuente"])){
    	$this->evento->fuente = $_POST["fuente"];
		}
		
		$this->evento->conf = 0;
		if (isset($_POST["conf"])){
    	$this->evento->conf = $_POST["conf"];
		}
		
		$this->evento->desc = "";
		if (isset($_POST["desc"])){
    	$this->evento->desc = $_POST["desc"];
		}

		$this->evento->desc_cons_hum = "";
		if (isset($_POST["desc_cons_hum"])){
    	$this->evento->desc_cons_hum = $_POST["desc_cons_hum"];
		}
		
		$this->evento->desc_riesg_hum = "";
		if (isset($_POST["desc_riesg_hum"])){
    	$this->evento->desc_riesg_hum = $_POST["desc_riesg_hum"];
		}

  		$this->evento->id_deptos = $_POST["id_depto"];
  	
		$this->evento->id_muns = Array();
		if (isset($_POST["id_muns"])){
    	$this->evento->id_muns = $_POST["id_muns"];
		}
		
		$this->evento->id_actores = Array();
		if (isset($_POST["id_actores"])){
    	$this->evento->id_actores = $_POST["id_actores"];
		}
		
		$this->evento->id_cons = Array();
		if (isset($_POST["id_cons"])){
    	$this->evento->id_cons = $_POST["id_cons"];
		}

		$this->evento->id_riesgos = Array();
		if (isset($_POST["id_riesgos"])){
    	$this->evento->id_riesgos = $_POST["id_riesgos"];
		}					
}

  /**
  * Realiza el Parse de las variables de la forma de cobertura y las asigna al VO de Evento (variable de clase) 
  * @access public	
  */	
  function parseFormCobertura($opcion) {
	if (isset($_POST["id"])){
		$this->evento->id = $_POST["id"];
	}
	if ($opcion == 2 || $opcion == 5){
		if (isset($_POST["id"])){
		$this->evento->id = $_POST["id"];
		}
		
		$this->evento->id_regiones = Array();
		if (isset($_POST["id_regiones"])){
			$this->evento->id_regiones = $_POST["id_regiones"];
		}
		
		$this->evento->id_poblados = Array();
		if (isset($_POST["id_poblados"])){
			$this->evento->id_poblados = $_POST["id_poblados"];
		}
	}
	else if ($opcion == 4){
		if (isset($_POST["id"])){
			$this->evento->id = $_POST["id"];
		}
	
		$this->evento->id_resguardos = Array();
		if (isset($_POST["id_resguardos"])){
			$this->evento->id_resguardos = $_POST["id_resguardos"];
		}
	
		$this->evento->id_parques = Array();
		if (isset($_POST["id_parques"])){
			$this->evento->id_parques = $_POST["id_parques"];
		}
		
		$this->evento->id_divisiones_afro = Array();
		if (isset($_POST["id_divisiones_afro"])){
			$this->evento->id_divisiones_afro = $_POST["id_divisiones_afro"];
		}
	}	
  }
}
?>