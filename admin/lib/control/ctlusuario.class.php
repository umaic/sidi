<?
class ControladorPagina {

	var $usuario_dao;
	var $conn;

	function ControladorPagina($accion) {

		$this->conn = MysqlDb::getInstance();

		$this->usuario_dao = new UsuarioDAO();
		$this->usuario = new Usuario();

		if ($accion == 'listar'){
			$this->usuario_dao->Listar('tabla','','');
		}
		else if ($accion == 'logout'){
			$vu = New SessionUsuario();
			$vu->Logout();
		}
		else if ($accion == 'insertar') {
			$this->parseForm();
			$this->usuario_dao->Insertar($this->usuario);
		}
		else if ($accion == 'registrar') {
			$this->parseForm();
			$this->usuario_dao->Registrar($this->usuario);
		}
		else if ($accion == 'unicef_registrar') {
			$this->parseForm();
			$this->usuario_dao->UnicefRegistrar($this->usuario);
		}
		else if ($accion == 'actualizar') {
			$this->parseForm();
			$this->usuario_dao->Actualizar($this->usuario);
		}
		else if ($accion == 'actualizar_password') {
			$this->usuario->id = $_POST["id"];
			$this->usuario->password = $_POST["password_usuario"];
			$this->usuario->password_anterior = $_POST["password_anterior_usuario"];

			$this->usuario_dao->ActualizarPassword($this->usuario);
		}
		else if ($accion == 'borrar') {
			$this->usuario_dao->Borrar($_GET["id"]);
		}

	}

	function parseForm() {
		if (isset($_POST["id"]))
		$this->usuario->id = $_POST["id"];

		$this->usuario->nombre = $_POST["nombre"];
		$this->usuario->login = $_POST["login"];
		if (isset($_POST["pass"]))	$this->usuario->pass = $_POST["pass"];

		$this->usuario->id_tipo = $_POST["id_tipo"];
		$this->usuario->email = $_POST["email"];
		if (isset($_POST["org"]))	$this->usuario->org = $_POST["org"];
		if (isset($_POST["tel"]))	$this->usuario->tel = $_POST["tel"];
		if (isset($_POST["punto_contacto"]))	$this->usuario->punto_contacto = $_POST["punto_contacto"];
		$this->usuario->cnrr = $_POST["cnrr"];
		$this->usuario->activo = $_POST["activo"];
		$this->usuario->id_org = (isset($_POST["id_org"])) ? $_POST["id_org"] : 0;
		$this->usuario->id_tema = (isset($_POST["id_tema"])) ? $_POST["id_tema"] : 0;
	}
}
?>
