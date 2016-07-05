<?
/**
 * Maneja todas las acciones de administración de los donantes
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

    /**
     * VO de Donante
     * @var object 
     */
    var $vo;

    /**
     * Variable para el manejo de la clase DonanteDAO
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

        $this->dao = new UnicefDonanteDAO();
        $this->vo = new UnicefDonante();

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
     * Realiza el Parse de las variables de la forma y las asigna al VO de Donante (variable de clase) 
     * @access public	
     */	
    function parseForm() {
        
        $this->vo->id = (isset($_POST["id"]) && strlen($_POST["id"]) > 0) ? $_POST["id"] : 0;
        $this->vo->nombre =  (isset($_POST["nombre"])) ? $_POST["nombre"] : '';
        $this->vo->codigo =  (isset($_POST["codigo"])) ? $_POST["codigo"] : '';

    }
}
?>
