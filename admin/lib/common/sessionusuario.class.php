<?
//*********************************************************
// Objetivo: Validar Usuario en una aplicación Web usando los
// datos de Login y Password
//*********************************************************


Class SessionUsuario {

	var $conn;
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

	function ValidarSession(){
		if (!isset($_SESSION["id_usuario_s"])){
			$this->Logout();
		}
	}
	function Exito ($pagina_exito){
		$this->RegistrarVariables();
		header("Location: ".$pagina_exito);
	}

	function Error ($pagina_error){
		header("Location: ".$pagina_error);
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
		header("Location: ../$pag");
	}
}
?>
