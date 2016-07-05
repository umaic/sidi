<?
/**
 * Maneja todas las acciones de administración de Tipo de Organizacions
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

    /**
     * VO de Tema
     * @var object 
     */
    var $tema;

    /**
     * Constructor
     * Crea la conexión a la base de datos y ejecuta la accion
     * @access public
     * @param string $accion Variable que indica la accion a realizar
     */	
    function ControladorPagina($accion) {

	$this->tema_dao = new TemaDAO();

	if ($accion == 'insertar') {
	    $this->parseForm();
	    $this->tema_dao->Insertar($this->tema);
	}
	else if ($accion == 'actualizar') {
	    $this->parseForm();
	    $this->tema_dao->Actualizar($this->tema);
	}
	else if ($accion == 'borrar') {
	    $this->tema_dao->Borrar($_GET["id"]);
	}
    }

    /**
     * Realiza el Parse de las variables de la forma y las asigna al VO de Tema (variable de clase) 
     * @access public	
     */	
    function parseForm() {
        if (isset($_POST["id"])){
            $this->tema->id = $_POST["id"];
        }
        $this->tema->nombre = $_POST["nombre"];
        $this->tema->id_clasificacion = $_POST["id_clasificacion"];	
        $this->tema->id_papa = $_POST["id_papa"];	
        $this->tema->def = $_POST["def"];
    }
}
?>
