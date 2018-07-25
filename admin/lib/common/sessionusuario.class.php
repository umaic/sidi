<?
//*********************************************************
// Objetivo: Validar Usuario en una aplicación Web usando los
// datos de Login y Password
//*********************************************************
require $_SERVER['DOCUMENT_ROOT'].'/sissh/admin/lib/common/autoload.php';
use Auth0\SDK\Auth0;


Class SessionUsuario {

	var $conn;
	var $auth0;
	var $id_usuario;
	var $id_tipo_usuario;
	var $cnrr;

	function ValidarUsuario ($usuario_login,$usuario_password,$pagina_exito,$pagina_error,$admin=0){
		$this->conn = MysqlDb::getInstance();

		//$usuario_password = crypt($usuario_password,substr($usuario_password,0,3));
		//$sql = "SELECT ID_USUARIO,ID_TIPO_USUARIO, CNRR FROM usuario WHERE LOGIN = '".$usuario_login."' AND PASS = '".$usuario_password."' AND ACTIVO = 1";
		$sql = "SELECT ID_USUARIO,ID_TIPO_USUARIO, ID_ORG_RESPONSABLE, ID_TEMA FROM usuario 
                WHERE LOGIN = '".$usuario_login."' AND PASS = '".$usuario_password."' AND ACTIVO = 1";
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchRow($rs);

		if ($this->conn->RowCount($rs) > 0){
			$this->id_usuario = $row_rs[0];
			$this->id_tipo_usuario = $row_rs[1];
			// $this->cnrr = $row_rs[2];
			$this->id_org = $row_rs[2];
			$this->id_tema = $row_rs[3];

			//Determina si es undaf
			$this->undaf = 0;
			if(in_array($this->id_tipo_usuario,array(27,29,30))){
				$this->undaf = 1;
			}

			//Determina si es mapp-oea
			$this->mapp_oea = 0;
			if(in_array($this->id_tipo_usuario,array(28))){
				$this->mapp_oea = 1;
			}
				
			$this->sidih = ($this->undaf == 0 AND $this->mapp_oea == 0)	? 1 : 0;

			$this->Exito($pagina_exito);
		}
		else{
			$this->Error($pagina_error);
		}
	}

	/*
	 * Function ValidarUsuarioAuth0
	 *
	 * Realiza el proceso completo de inicio sesión con Auth0
	 *
	 * @userInfo (type) about this param
	 * @return (array) arreglo con los valores del usuario existente
	*/
	function ValidarUsuarioAuth0 ($pagina_exito,$pagina_error,$admin=0){

		include_once("admin/lib/common/auth0_config.php");

		//Crear el objeto Auth0
		$this->auth0 = new Auth0(array(
			'domain'        => $auth0_domain,
			'client_id'     => $auth0_client_id,
			'client_secret' => $auth0_client_secret,
			'redirect_uri'  => $auth0_redirect_uri,
			'audience'      => $auth0_audience,
			'scope'         => $auth0_scope,
			'persist_id_token' => true,
			'persist_access_token' => true,
			'persist_refresh_token' => true
		));
		try {

			$userInfo = (object) $this->auth0->getUser();

			if (empty((array) $userInfo)) {
				$this->auth0->login();
			} else
			{
				if (($usuario_sidi = $this->doesUserExists($userInfo)) !== null)
				{
					//Si el email de Auth0 existe local y tiene la relación, Auth0 iniciar sesión con los datos locales
					$this->loginAuth0User($usuario_sidi, $pagina_exito, $pagina_error);

				}
				else if (($usuario_sidi = $this->doesUserExistsUnlinked($userInfo)) !== null)
				{
					//Si el email de Auth0 existe local pero no tiene la relación Auth0, relacionarlo
					$this->linkAuth0User($usuario_sidi, $userInfo, $pagina_exito, $pagina_error);

				}
				else
				{
					//Si el email Auth0 no existe local, crear el usuario local
					$this->createAuth0User($userInfo, $pagina_exito, $pagina_error);
				}
				$this->Error($pagina_error);
			}

		} catch (Auth0ValidationException $e) {
			die($e->getMessage());
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	/*
	 * Function doesUserExistsUnlinked
	 *
	 * Verifica si el email de Auth0 existe local y tiene la relación Auth0
	 *
	 * @userInfo (type) about this param
	 * @return (array) arreglo con los valores del usuario existente
	*/
	function doesUserExists($userInfo)
	{
		$this->conn = MysqlDb::getInstance();

		$row_rs = array();

		//Email registrado en Auth0
		$email = $userInfo->email;

		//ID de Auth0
		$auth0Uid = $userInfo->sub;

		$sql = "SELECT usuario.* FROM usuario
                      INNER JOIN usuario_auth0 ON usuario_auth0.sidi_id_usuario = usuario.id_usuario
                      WHERE usuario.login='$email' AND usuario_auth0.auth0_id_usuario = '$auth0Uid'";
		$result = $this->conn->OpenRecordset($sql);
		while ($row = $this->conn->FetchObject($result)){
			$row_rs[] = (object) $row;
		}
		if (sizeof($row_rs) == 1){
			return $row_rs[0];
		} else {
			return null;
		}
	}

	/*
	 * Function doesUserExistsUnlinked
	 *
	 * Verifica si el email de Auth0 existe local pero no tiene la relación Auth0, relacionarlo
	 *
	 * @userInfo (type) about this param
	 * @return (array) arreglo con los valores del usuario existente
    */
	function doesUserExistsUnlinked($userInfo)
	{
		$this->conn = MysqlDb::getInstance();

		//Email registrado en Auth0
		$email = $userInfo->email;

		$sql = "SELECT usuario.* FROM usuario
                      WHERE usuario.email='$email'";
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = (object) $this->conn->FetchAssoc($rs);
		if ($this->conn->RowCount($rs) > 0){
			return $row_rs;
		} else {
			return null;
		}
	}

	/*
	 * Function loginAuth0User
	 *
	 * Inicia sesión con las credenciales de Auth0
	 *
	 * @userInfo (type) about this param
	 * @return (array) arreglo con los valores del usuario existente
	*/
	function loginAuth0User($usuario_sidi, $pagina_exito, $pagina_error)
	{
		$this->id_usuario = intval($usuario_sidi->ID_USUARIO);
		$this->id_tipo_usuario = intval($usuario_sidi->ID_TIPO_USUARIO);

		$this->id_org = intval($usuario_sidi->ID_ORG_RESPONSABLE);
		$this->id_tema = intval($usuario_sidi->ID_TEMA);

		//Determina si es undaf
		$this->undaf = 0;
		if(in_array($this->id_tipo_usuario,array(27,29,30))){
			$this->undaf = 1;
		}

		//Determina si es mapp-oea
		$this->mapp_oea = 0;
		if(in_array($this->id_tipo_usuario,array(28))){
			$this->mapp_oea = 1;
		}

		$this->sidih = ($this->undaf == 0 AND $this->mapp_oea == 0)	? 1 : 0;
		$this->Exito($pagina_exito);

	}

	/*
	 * Function linkAuth0User
	 *
	 * Crea el vínculo del usuario local con el de auth0
	 *
	 * @userInfo (type) about this param
	 * @return (array) arreglo con los valores del usuario existente
	*/
	function linkAuth0User($usuario_sidi, $userInfo, $pagina_exito, $pagina_error)
	{
		$this->conn = MysqlDb::getInstance();

		if (isset($userInfo->email_verified) && !$userInfo->email_verified) {
			//No se pueden enlazar usuarios no verificados
			$this->Error($pagina_error);
			//ToDo: Agregar un mensaje amigable sobre este error
		}
		$sidiuserid = $usuario_sidi->ID_USUARIO;
		$intdatetime = time();

		if ($this->doesAuth0UserExists($userInfo->sub)) {
			//Ya estaba enlazado
			//ToDo: Actualiza el id_sidi en app_metadata de Auth0

		} else {
			//Crear la relación Auth0
			$sql = "INSERT INTO usuario_auth0(sidi_id_usuario,auth0_id_usuario,fecha_relacion,enlace) VALUES ($sidiuserid,'$userInfo->sub',$intdatetime,1)";
			$this->conn->Execute($sql);

			//ToDo: Actualiza el id_sidi en app_metadata de Auth0

		}
		//Iniciar sesión en la aplicación
		$this->loginAuth0User($usuario_sidi, $pagina_exito, $pagina_error);

	}

	/*
	 * Function createAuth0User
	 *
	 * Crea el usuario local cuando entra con credenciales de Auth0
	 *
	 * @userInfo (type) about this param
	 * @return (array) arreglo con los valores del usuario existente
	*/
	function createAuth0User($userInfo, $pagina_exito, $pagina_error)
	{

		include_once("admin/lib/common/mysqldb.class.php");
		include_once("admin/lib/control/ctlusuario.class.php");
		include_once("admin/lib/dao/usuario.class.php");
		include_once("admin/lib/model/usuario.class.php");

		$this->conn = MysqlDb::getInstance();
		$this->usuario_dao = new UsuarioDAO();
		$this->usuario = new Usuario();

		$this->usuario->id = NULL;

		$this->usuario->nombre = $userInfo->name;
		$this->usuario->login = $userInfo->nickname . rand(); //Con cadena aleatoria para evitar duplicados
		$this->usuario->pass = md5(uniqid(rand(), true));

		$this->usuario->id_tipo = 18; //Perfil por defecto: Consulta Externa
		$this->usuario->email = $userInfo->email;
		$this->usuario->org = '';
		$this->usuario->tel = '';
		$this->usuario->punto_contacto = "Creado por Auth0";
		$this->usuario->cnrr = 0;
		$this->usuario->activo = 1; //Todos los usuarios nuevos registrados quedan activos por defecto
		$this->usuario->id_org = 0;
		$this->usuario->id_tema = 0;

		$this->usuario_dao->Registrar($this->usuario);

		$sidiuserid = $this->usuario->id;

		//ToDo: Actualiza el id_sidi en app_metadata de Auth0 con $sidiuserid

		$intdatetime = time();

		if ($this->doesAuth0UserExists($userInfo->sub)) {
			$sql = "UPDATE usuario_auth0 SET sidi_id_usuario=$sidiuserid,fecha_relacion=$intdatetime WHERE auth0_id_usuario='$userInfo->sub'";
		} else {
			$sql = "INSERT INTO usuario_auth0(sidi_id_usuario,auth0_id_usuario,fecha_relacion,enlace) VALUES ($sidiuserid,'$userInfo->sub',$intdatetime,1)";
		}
		$this->conn->Execute($sql);

		//Iniciar sesión en la aplicación
		$usuario_sidi = $this->doesUserExistsUnlinked($userInfo);
		$this->loginAuth0User($usuario_sidi, $pagina_exito, $pagina_error);
	}

	function doesAuth0UserExists($auth0UserId)
	{
		$this->conn = MysqlDb::getInstance();

		$sql = "SELECT auth0_id_usuario FROM usuario_auth0 WHERE auth0_id_usuario='" . $auth0UserId . "'";
		$rs = $this->conn->OpenRecordset($sql);

		return ($this->conn->RowCount($rs) > 0);
	}

	function ValidarSession(){
		if (!isset($_SESSION["id_usuario_s"])){
			$this->Logout();
		}
	}
	function Exito ($pagina_exito){
		$this->RegistrarVariables();
		header("Location: ".$pagina_exito);exit;
	}

	function Error ($pagina_error){
		header("Location: ".$pagina_error);exit;
	}

	function RegistrarVariables(){
		$_SESSION["id_usuario_s"] = $this->id_usuario;
		$_SESSION["id_tipo_usuario_s"] = $this->id_tipo_usuario;
		//$_SESSION["cnrr"] = $this->cnrr;
		$_SESSION["sidih"] = $this->sidih;
		$_SESSION["undaf"] = $this->undaf;
		$_SESSION["mapp_oea"] = $this->mapp_oea;
		$_SESSION["admin"] = 'admin';
        $_SESSION["id_org"] = $this->id_org;
        $_SESSION["id_tema"] = $this->id_tema;

		if (!empty($this->id_org)) {
            include_once('admin/lib/dao/factory.class.php');

            $org_dao = FactoryDAO::factory('org');
			$nom_org = $org_dao->GetName($this->id_org);
			$id_tipo = $org_dao->GetFieldValue($this->id_org,"id_tipo");

			$_SESSION["nom_org"] = $nom_org;
			$_SESSION["id_tipo_org"] = $id_tipo;
        }
		if (!empty($this->id_tema)) {
            include_once('admin/lib/dao/factory.class.php');

            $tema_dao = FactoryDAO::factory('tema');
			$tvo = $tema_dao->Get($this->id_tema);

			$_SESSION["nom_tema"] = $tvo->nombre;
        }
		//CODIGO_USUARIO_CNRR
		/*
		   if ($this->cnrr == 1){
		   $sql = "SELECT * FROM perfil_usuario_cnrr WHERE ID_TIPO_USUARIO = ".$this->id_tipo_usuario;
		   $rs = $this->conn->Execute($sql);
		   $rs = $this->conn->OpenRecordset($sql);
		   $row_rs = $this->conn->FetchObject($rs);

		   $_SESSION["mod_alimentacion"] = $row_rs->ALIMENTACION_ORG;
		   $_SESSION["mod_consulta"] = $row_rs->CONSULTA_ORG;
		   $_SESSION["mod_admin"] = $row_rs->ADMIN_ORG;

		   }
		 */

		//UNDAF
		include_once("config.php");
		$ids_undaf = $conf['undaf']['id_tipo_usuario'];
		if (in_array($this->id_tipo_usuario,$ids_undaf)){
			$_SESSION["undaf"] = 1;

		}
		else{
			$_SESSION["undaf"] = 0;
		}
	}

	function Logout($pag = 'index.php'){
		@$_SESSION = array();
		@session_destroy();
		header("Location: ../$pag");exit;
	}
}
?>
