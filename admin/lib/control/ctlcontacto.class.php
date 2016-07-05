<?
/**
 * Maneja todas las acciones de administraci�n de Contactos
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

	/**
	 * VO de Contacto
	 * @var object
	 */
	var $vo;

	/**
	 * Constructor
	 * Crea la conexión a la base de datos y ejecuta la accion
	 * @access public
	 * @param string $accion Variable que indica la accion a realizar
	 */
	function ControladorPagina($accion) {

		$this->contacto_dao = new ContactoDAO();

		if ($accion == 'insertar') {
			$this->parseForm();
			$this->contacto_dao->Insertar($this->vo);
		}
		else if ($accion == 'actualizar') {
			$this->parseForm();
			$this->contacto_dao->Actualizar($this->vo);
		}
		else if ($accion == 'borrar') {
			$this->contacto_dao->Borrar($_GET["id"]);
		}
		else if ($accion == 'importar') {
			$this->contacto_dao->importarCSV($_FILES["userfile"]);
		}
		else if ($accion == 'insertarCon4w') {
			$this->parseForm();
			$this->contacto_dao->InsertarCon4w($this->vo);
		}
	}

	/**
	 * Realiza el Parse de las variables de la forma y las asigna al VO de Contacto (variable de clase)
	 * @access public
	 */
	function parseForm() {
		if (isset($_POST["id"])){
			$this->vo->id = $_POST["id"];
		}
		$this->vo->nombre = $_POST["nombre"];
		$this->vo->apellido = (isset($_POST["apellido"])) ? $_POST["apellido"] : '';
		$this->vo->tel = $_POST["tel"];
		$this->vo->fax = (isset($_POST["fax"])) ? $_POST["fax"] : '';
		$this->vo->email = (isset($_POST["email"])) ? $_POST["email"] : '';
		$this->vo->social = (isset($_POST["social"])) ? $_POST["social"] : '';
		$this->vo->cel = (isset($_POST["cel"])) ? $_POST["cel"] : '';
		$this->vo->id_org = (isset($_POST["id_org"])) ? $_POST["id_org"] : array();
		$this->vo->id_espacio = (isset($_POST["id_espacio"])) ? $_POST["id_espacio"] : array();
		$this->vo->id_mun = (isset($_POST["id_mun"])) ? $_POST["id_mun"] : 0;

		//Caracteristicas
        $this->vo->caracteristicas = array();
        if (isset($_POST["id_contacto_col"])) {
            foreach ($_POST["id_contacto_col"] as $id_contacto_col){
                $nom_input = $id_contacto_col."_opcion";
                if (isset($_POST[$nom_input]) && $_POST[$nom_input] != ''){
                    $this->vo->caracteristicas[$id_contacto_col] = $_POST[$nom_input];
                }
            }
        }

	}
}
?>
