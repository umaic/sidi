<?
/**
 * Maneja todas las acciones de administración de las fuentes
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

    /**
     * VO de FuentePba
     * @var object 
     */
    var $vo;

    /**
     * Variable para el manejo de la clase FuentePbaDAO
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

        $this->dao = new UnicefFuentePbaDAO();
        $this->vo = new UnicefFuentePba();

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
     * Realiza el Parse de las variables de la forma y las asigna al VO de FuentePba (variable de clase) 
     * @access public	
     */	
    function parseForm() {
        
        $this->vo->id = (isset($_POST["id"]) && strlen($_POST["id"]) > 0) ? $_POST["id"] : 0;
        $this->vo->nombre =  (isset($_POST["nombre"])) ? $_POST["nombre"] : '';
        $this->vo->id_donante =  (isset($_POST["id_donante"])) ? $_POST["id_donante"] : 0;

    }
}
?>
