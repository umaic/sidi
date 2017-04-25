<?
/**
 * Maneja todas las acciones de administración de Mecanismo de Entrega
 *
 * @author
 */

class ControladorPagina {

    /**
     * VO de ModalidadAsistencia
     * @var object
     */
    var $modalidad_asistencia;


    /**
     * Constructor
     * Crea la conexión a la base de datos y ejecuta la accion
     * @access public
     * @param string $accion Variable que indica la accion a realizar
     */
    function ControladorPagina($accion) {

        $this->modalidad_asistencia_dao = new ModalidadAsistenciaDAO();

        if ($accion == 'insertar') {
            $this->parseForm();
            $this->modalidad_asistencia_dao->Insertar($this->modalidad_asistencia);
        }
        else if ($accion == 'actualizar') {
            $this->parseForm();
            $this->modalidad_asistencia_dao->Actualizar($this->modalidad_asistencia);
        }
        else if ($accion == 'borrar') {
            $this->modalidad_asistencia_dao->Borrar($_GET["id"]);
        }
    }

    /**
     * Realiza el Parse de las variables de la forma y las asigna al VO de EstadoProyecto (variable de clase)
     * @access public
     */
    function parseForm() {
        if (isset($_POST["id"])){
            $this->modalidad_asistencia->id = $_POST["id"];
        }
        $this->modalidad_asistencia->nombre = $_POST["nombre"];
    }
}
?>