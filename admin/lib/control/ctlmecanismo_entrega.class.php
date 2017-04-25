<?
/**
 * Maneja todas las acciones de administración de Mecanismo de Entrega
 *
 * @author
 */

class ControladorPagina {

    /**
     * VO de MecanismoEntrega
     * @var object
     */
    var $mecanismo_entrega;


    /**
     * Constructor
     * Crea la conexión a la base de datos y ejecuta la accion
     * @access public
     * @param string $accion Variable que indica la accion a realizar
     */
    function ControladorPagina($accion) {

        $this->mecanismo_entrega_dao = new MecanismoEntregaDAO();

        if ($accion == 'insertar') {
            $this->parseForm();
            $this->mecanismo_entrega_dao->Insertar($this->mecanismo_entrega);
        }
        else if ($accion == 'actualizar') {
            $this->parseForm();
            $this->mecanismo_entrega_dao->Actualizar($this->mecanismo_entrega);
        }
        else if ($accion == 'borrar') {
            $this->mecanismo_entrega_dao->Borrar($_GET["id"]);
        }
    }

    /**
     * Realiza el Parse de las variables de la forma y las asigna al VO de EstadoProyecto (variable de clase)
     * @access public
     */
    function parseForm() {
        if (isset($_POST["id"])){
            $this->mecanismo_entrega->id = $_POST["id"];
        }
        $this->mecanismo_entrega->nombre = $_POST["nombre"];
    }
}
?>