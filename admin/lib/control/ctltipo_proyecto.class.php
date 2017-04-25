<?
/**
 * Maneja todas las acciones de administración de Estados de Proyecto
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

    /**
     * VO de TipoProyecto
     * @var object
     */
    var $tipo_proyecto;

    /**
     * Constructor
     * Crea la conexión a la base de datos y ejecuta la accion
     * @access public
     * @param string $accion Variable que indica la accion a realizar
     */
    function ControladorPagina($accion) {

        $this->tipo_proyecto_dao = new TipoProyectoDAO();

        if ($accion == 'insertar') {
            $this->parseForm();
            $this->tipo_proyecto_dao->Insertar($this->tipo_proyecto);
        }
        else if ($accion == 'actualizar') {
            $this->parseForm();
            $this->tipo_proyecto_dao->Actualizar($this->tipo_proyecto);
        }
        else if ($accion == 'borrar') {
            $this->tipo_proyecto_dao->Borrar($_GET["id"]);
        }
    }

    /**
     * Realiza el Parse de las variables de la forma y las asigna al VO de TipoProyecto (variable de clase)
     * @access public
     */
    function parseForm() {
        if (isset($_POST["id"])){
            $this->tipo_proyecto->id = $_POST["id"];
        }
        $this->tipo_proyecto->nombre = $_POST["nombre"];
    }
}
?>