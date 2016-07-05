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

		$this->evento_dao = new EventoConflictoDAO();

		if ($accion == 'insertar') {
			$this->parseForm();
			$this->evento_dao->Insertar($this->evento,1,explode(",",$_POST["num_vict_desc"]),explode(",",$_POST["num_actores_desc"]),explode(",",$_POST["num_subactores_desc"]),explode(",",$_POST["num_subsubactores_desc"]));
		}
		else if ($accion == 'actualizar') {
			$this->parseForm();
			$this->evento_dao->Actualizar($this->evento,1,explode(",",$_POST["num_vict_desc"]),explode(",",$_POST["num_actores_desc"]),explode(",",$_POST["num_subactores_desc"]),explode(",",$_POST["num_subsubactores_desc"]));		
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

		$this->evento = new EventoConflicto;

		if (isset($_POST["id"]) && $_POST['id'] != ''){
			$this->evento->id = $_POST["id"];
		}

		$this->evento->id_cat = $_POST["id_cat"];
		$this->evento->id_subcat = $_POST["id_subcat"];

		$this->evento->id_fuente = $_POST["id_fuente"];
		$this->evento->id_subfuente = $_POST["id_subfuente"];

		$this->evento->fecha_evento = $_POST["fecha_evento"];
		$this->evento->fecha_fuente = $_POST["fecha_fuente"];
		$this->evento->desc_fuente = $_POST["desc_fuente"];
		$this->evento->refer_fuente = $_POST["refer_fuente"];
		$this->evento->sintesis = $_POST["sintesis"];
		$this->evento->id_mun = $_POST["id_muns"];


		/*$num_reg = $_POST["num_reg"];
		  for($i=0;$i<=$num_reg;$i++){
		  $this->evento->id_actor[$i] = $_POST["id_actor$i"];
		  }*/

		$this->evento->id_actor = $_POST["id_abuelo"];
		$this->evento->id_subactor = (isset($_POST["id_papa"])) ? $_POST["id_papa"] : array();
		$this->evento->id_subsubactor = (isset($_POST["id_hijo"])) ? $_POST["id_hijo"] : array();

		$this->evento->lugar = "";
		if (isset($_POST["lugar"])){
			$this->evento->lugar = $_POST["lugar"];
		}

		$this->evento->id_edad = 0;
		if (isset($_POST["id_edad"])){
			$this->evento->id_edad = $_POST["id_edad"];
		}			

		$this->evento->id_rango_edad = 0;
		if (isset($_POST["id_rango_edad"])){
			$this->evento->id_rango_edad = $_POST["id_rango_edad"];
		}			

		$this->evento->id_sexo = 0;
		if (isset($_POST["id_sexo"])){
			$this->evento->id_sexo = $_POST["id_sexo"];
		}			

		$this->evento->id_condicion = 0;
		if (isset($_POST["id_condicion"])){
			$this->evento->id_condicion = $_POST["id_condicion"];
		}			

		$this->evento->id_subcondicion = 0;
		if (isset($_POST["id_subcondicion"])){
			$this->evento->id_subcondicion = $_POST["id_subcondicion"];
		}	

		$this->evento->id_estado = 0;
		if (isset($_POST["id_estado"])){
			$this->evento->id_estado = $_POST["id_estado"];
		}

		$this->evento->id_etnia = 0;
		if (isset($_POST["id_etnia"])){
			$this->evento->id_etnia = $_POST["id_etnia"];
		}

		$this->evento->id_subetnia = 0;
		if (isset($_POST["id_subetnia"])){
			$this->evento->id_subetnia = $_POST["id_subetnia"];
		}

		$this->evento->id_ocupacion = 0;
		if (isset($_POST["id_ocupacion"])){
			$this->evento->id_ocupacion = $_POST["id_ocupacion"];
		}

		$this->evento->num_victimas = 0;
		if (isset($_POST["num_victimas"])){
			$this->evento->num_victimas = $_POST["num_victimas"];
		}			
	}
}
?>
