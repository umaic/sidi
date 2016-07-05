<?
/**
 * Maneja todas las acciones de administración de Organizacions
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

	/**
	* VO de Organizacion
	* @var object 
	*/
	var $organizacion;

	/**
	* Constructor
	* Crea la conexión a la base de datos y ejecuta la accion
	* @access public
	* @param string $accion Variable que indica la accion a realizar
	*/	
	function ControladorPagina($accion) {

		$this->organizacion_dao = new OrganizacionDAO();

		if ($accion == 'insertar') {
			$this->parseForm();
			$this->organizacion_dao->Insertar($this->organizacion);
		}
		else if ($accion == 'actualizar'){
			if (!isset($_POST["actualizar_cobertura"])){
				$this->parseForm();
				$this->organizacion_dao->Actualizar($this->organizacion);
			}
			else{
				$opcion = $_POST["opcion_f"];
				$this->parseFormCobertura($opcion);
				$this->organizacion_dao->ActualizarCobertura($this->organizacion,$opcion);
			}
		}
		else if ($accion == 'listar') {
			$f_ini = $_POST['f_ini'];
			$f_fin = $_POST['f_fin'];
			$where = "FECHA_REG_EVENTO between '".$f_ini."' AND '".$f_fin."'";
			$this->organizacion_dao->ListarTabla($where);
		}
		else if ($accion == 'importar') {
			$this->organizacion_dao->ImportarCSV($_FILES['archivo_csv']);
		}
		else if ($accion == 'registrar') {
			$this->parseFormRegistro();
			$this->organizacion_dao->Registrar($this->organizacion,$this->org_registro);
		}
		else if ($accion == 'insertar_mo') {
			$this->parseFormOrgMO();
			$this->organizacion_dao->InsertarOrgMO($this->organizacion,$this->org_mo);
		}
		else if ($accion == 'actualizar_mo') {
			$this->parseFormOrgMO();
			$this->organizacion_dao->ActualizarOrgMO($this->organizacion,$this->org_mo);
		}
		else if ($accion == 'insertarOrg4w') {
			$this->parseForm4w();
			$this->organizacion_dao->InsertarOrg4w($this->organizacion);
		}
	}

	/**
	* Realiza el Parse de las variables de la forma y las asigna al VO de Organizacion (variable de clase) 
	* @access public	
	*/	
	function parseForm() {
		if (isset($_POST["id"])){
			$this->organizacion->id = $_POST["id"];
		}
		$this->organizacion->id_tipo = $_POST["id_tipo"];
		$this->organizacion->id_mun_sede = $_POST["id_mun_sede"];
		$this->organizacion->nom = $_POST["nombre"];
		$this->organizacion->view = $_POST["view"];
		$this->organizacion->dona = $_POST["dona"];
		$this->organizacion->info_confirmada = $_POST["info_conf"];
		$this->organizacion->consulta_social = $_POST["consulta_social"];
		$this->organizacion->cnrr = $_POST["cnrr"];

        $this->organizacion->pais_ciudad = '';
        if (isset($_POST["pais_ciudad"]) && $_POST["pais_ciudad"] != ""){
            $this->organizacion->pais_ciudad = $_POST["pais_ciudad"];
        }
		$this->organizacion->id_papa = 0;
		if (isset($_POST["id_papa"]) && $_POST["id_papa"] != ""){
			$this->organizacion->id_papa = $_POST["id_papa"];
		}

		$this->organizacion->sig = "";
		if (isset($_POST["sigla"]) && $_POST["sigla"] != ""){
			$this->organizacion->sig = $_POST["sigla"];
		}

		$this->organizacion->des = "";
		if (isset($_POST["des"]) && $_POST["des"] != ""){
			$this->organizacion->des = $_POST["des"];
		}

		$this->organizacion->naci = 0;
		if (isset($_POST["naci"]) && $_POST["naci"] != ""){
			$this->organizacion->naci = $_POST["naci"];
		}

		$this->organizacion->bd = 0;
		if (isset($_POST["bd"]) && $_POST["bd"] != ""){
			$this->organizacion->bd = $_POST["bd"];
		}


		$this->organizacion->nit = "";
		if (isset($_POST["nit"]) && $_POST["nit"] != ""){
			$this->organizacion->nit = $_POST["nit"];
		}

		$this->organizacion->esp_coor = "";
		if (isset($_POST["esp_coor"]) && $_POST["esp_coor"] != ""){
			$this->organizacion->esp_coor = $_POST["esp_coor"];
		}

		$this->organizacion->dir = "";
		if (isset($_POST["dir"]) && $_POST["dir"] != ""){
			$this->organizacion->dir = $_POST["dir"];
		}

		$this->organizacion->tel1 = "";
		if (isset($_POST["tel1"]) && $_POST["tel1"] != ""){
			$this->organizacion->tel1 = $_POST["tel1"];
		}

		$this->organizacion->tel2 = "";
		if (isset($_POST["tel2"]) && $_POST["tel2"] != ""){
			$this->organizacion->tel2 = $_POST["tel2"];
		}

		$this->organizacion->fax = "";
		if (isset($_POST["fax"]) && $_POST["fax"] != ""){
			$this->organizacion->fax = $_POST["fax"];
		}

		$this->organizacion->pu_email = "";
		if (isset($_POST["pu_email"]) && $_POST["pu_email"] != ""){
			$this->organizacion->pu_email = $_POST["pu_email"];
		}

		$this->organizacion->un_email = "";
		if (isset($_POST["un_email"]) && $_POST["un_email"] != ""){
			$this->organizacion->un_email = $_POST["un_email"];
		}

		$this->organizacion->web = "";
		if (isset($_POST["web"]) && $_POST["web"] != ""){
			$this->organizacion->web = $_POST["web"];
		}

		$this->organizacion->logo = "";
		if (isset($_POST["logo"]) && $_POST["logo"] != ""){
			$this->organizacion->logo = $_POST["logo"];
		}

		$this->organizacion->n_rep = "";
		if (isset($_POST["n_rep"]) && $_POST["n_rep"] != ""){
			$this->organizacion->n_rep = $_POST["n_rep"];
		}

		$this->organizacion->t_rep = "";
		if (isset($_POST["t_rep"]) && $_POST["t_rep"] != ""){
			$this->organizacion->t_rep = $_POST["t_rep"];
		}

		$this->organizacion->tel_rep = "";
		if (isset($_POST["tel_rep"]) && $_POST["tel_rep"] != ""){
			$this->organizacion->tel_rep = $_POST["tel_rep"];
		}

		$this->organizacion->email_rep = "";
		if (isset($_POST["email_rep"]) && $_POST["email_rep"] != ""){
			$this->organizacion->email_rep = $_POST["email_rep"];
		}

		$this->organizacion->id_poblaciones = Array();
		if (isset($_POST["id_poblaciones"])){
			$this->organizacion->id_poblaciones = $_POST["id_poblaciones"];
		}
		$this->organizacion->id_sectores = Array();
		if (isset($_POST["id_sectores"])){
			$this->organizacion->id_sectores = $_POST["id_sectores"];
		}

		$this->organizacion->id_enfoques = Array();
		if (isset($_POST["id_enfoques"])){
			$this->organizacion->id_enfoques = $_POST["id_enfoques"];
		}

		$this->organizacion->id_donantes = Array();
		if (isset($_POST["id_donantes"]) && $_POST["id_donantes"] != ''){
			$this->organizacion->id_donantes = explode("|",$_POST["id_donantes"]);
		}
	}

	/**
  * Realiza el Parse de las variables de la forma de cobertura y las asigna al VO de Organizacion (variable de clase) 
  * @access public	
  */	
	function parseFormCobertura($opcion) {
		if (isset($_POST["id"])){
			$this->organizacion->id = $_POST["id"];
		}

		if ($opcion == 1 || $opcion == 3){
			$this->organizacion->id_deptos = Array();
			if (isset($_POST["id_depto"])){
				$this->organizacion->id_deptos = $_POST["id_depto"];
			}

			$this->organizacion->id_muns = Array();
			if (isset($_POST["id_muns"])){
				$this->organizacion->id_muns = $_POST["id_muns"];
			}
			

		}
		else if ($opcion == 2 || $opcion == 5){
			if (isset($_POST["id"])){
				$this->organizacion->id = $_POST["id"];
			}

			$this->organizacion->id_regiones = Array();
			if (isset($_POST["id_regiones"])){
				$this->organizacion->id_regiones = $_POST["id_regiones"];
			}

			$this->organizacion->id_poblados = Array();
			if (isset($_POST["id_poblados"])){
				$this->organizacion->id_poblados = $_POST["id_poblados"];
			}
		}
		else if ($opcion == 4){
			if (isset($_POST["id"])){
				$this->organizacion->id = $_POST["id"];
			}

			$this->organizacion->id_resguardos = Array();
			if (isset($_POST["id_resguardos"])){
				$this->organizacion->id_resguardos = $_POST["id_resguardos"];
			}

			$this->organizacion->id_parques = Array();
			if (isset($_POST["id_parques"])){
				$this->organizacion->id_parques = $_POST["id_parques"];
			}

			$this->organizacion->id_divisiones_afro = Array();
			if (isset($_POST["id_divisiones_afro"])){
				$this->organizacion->id_divisiones_afro = $_POST["id_divisiones_afro"];
			}
		}
	}
		
	/**
	* Realiza el Parse de las variables de la forma de registro y las asigna al VO de Organizacion (variable de clase)
	* @access public	
	*/	
	function parseFormRegistro() {
		
		$this->org_registro = New OrganizacionRegistro();
		
		$this->organizacion->id_tipo = $_POST["id_tipo"];
		$this->organizacion->id_mun_sede = $_POST["id_mun_sede"];
		$this->organizacion->nom = $_POST["nombre"];
		$this->organizacion->cnrr = $_POST["cnrr"];

		$this->organizacion->sig = "";
		if (isset($_POST["sigla"]) && $_POST["sigla"] != ""){
			$this->organizacion->sig = $_POST["sigla"];
		}

		$this->organizacion->des = "";
		if (isset($_POST["descripcion"]) && $_POST["descripcion"] != ""){
			$this->organizacion->des = $_POST["descripcion"];
		}

		$this->organizacion->naci = 0;
		if (isset($_POST["naci"]) && $_POST["naci"] != ""){
			$this->organizacion->naci = $_POST["naci"];
		}

		$this->organizacion->nit = "";
		if (isset($_POST["nit"]) && $_POST["nit"] != ""){
			$this->organizacion->nit = $_POST["nit"];
		}

		$this->organizacion->esp_coor = "";
		if (isset($_POST["esp_coor"]) && $_POST["esp_coor"] != ""){
			$this->organizacion->esp_coor = $_POST["esp_coor"];
		}

		$this->organizacion->dir = "";
		if (isset($_POST["dir"]) && $_POST["dir"] != ""){
			$this->organizacion->dir = $_POST["dir"];
		}

		$this->organizacion->tel1 = "";
		if (isset($_POST["tel1"]) && $_POST["tel1"] != ""){
			$this->organizacion->tel1 = $_POST["tel1"];
		}

		$this->organizacion->tel2 = "";
		if (isset($_POST["tel2"]) && $_POST["tel2"] != ""){
			$this->organizacion->tel2 = $_POST["tel2"];
		}

		$this->organizacion->fax = "";
		if (isset($_POST["fax"]) && $_POST["fax"] != ""){
			$this->organizacion->fax = $_POST["fax"];
		}

		$this->organizacion->pu_email = "";
		if (isset($_POST["pu_email"]) && $_POST["pu_email"] != ""){
			$this->organizacion->pu_email = $_POST["pu_email"];
		}

		$this->organizacion->web = "";
		if (isset($_POST["web"]) && $_POST["web"] != ""){
			$this->organizacion->web = $_POST["web"];
		}

		$this->organizacion->n_rep = "";
		if (isset($_POST["n_rep"]) && $_POST["n_rep"] != ""){
			$this->organizacion->n_rep = $_POST["n_rep"];
		}

		$this->organizacion->t_rep = "";
		if (isset($_POST["t_rep"]) && $_POST["t_rep"] != ""){
			$this->organizacion->t_rep = $_POST["t_rep"];
		}

		$this->organizacion->id_deptos = Array();
		if (isset($_POST["id_depto_cobertura"])){
			$this->organizacion->id_deptos = $_POST["id_depto_cobertura"];
		}
		
		$this->organizacion->id_muns = Array();
		if (isset($_POST["id_muns"])){
			$this->organizacion->id_muns = $_POST["id_muns"];
		}

		$this->organizacion->id_poblaciones = Array();
		if (isset($_POST["id_poblaciones"])){
			$this->organizacion->id_poblaciones = $_POST["id_poblaciones"];
		}
		$this->organizacion->id_sectores = Array();
		if (isset($_POST["id_sectores"])){
			$this->organizacion->id_sectores = $_POST["id_sectores"];
		}

		$this->organizacion->id_enfoques = Array();
		if (isset($_POST["id_enfoques"])){
			$this->organizacion->id_enfoques = $_POST["id_enfoques"];
		}

		$this->org_registro->donantes = Array();
		if (isset($_POST["donantes"])){
			$this->org_registro->donantes = $_POST["donantes"];
		}

		$this->org_registro->pob_vul_nombre = Array();
		if (isset($_POST["pob_vul_nombre"])){
			$this->org_registro->pob_vul_nombre = $_POST["pob_vul_nombre"];
		}

		$this->org_registro->pob_vul_email = Array();
		if (isset($_POST["pob_vul_email"])){
			$this->org_registro->pob_vul_email = $_POST["pob_vul_email"];
		}

		$this->org_registro->pob_vul_tel = Array();
		if (isset($_POST["pob_vul_tel"])){
			$this->org_registro->pob_vul_tel = $_POST["pob_vul_tel"];
		}
		
		$this->org_registro->ingresa_nombre = Array();
		if (isset($_POST["ingresa_nombre"])){
			$this->org_registro->ingresa_nombre = $_POST["ingresa_nombre"];
		}

		$this->org_registro->ingresa_email = Array();
		if (isset($_POST["ingresa_email"])){
			$this->org_registro->ingresa_email = $_POST["ingresa_email"];
		}
		
		$this->org_registro->ingresa_tel = Array();
		if (isset($_POST["ingresa_tel"])){
			$this->org_registro->ingresa_tel = $_POST["ingresa_tel"];
		}
		
	}

	/**
	* Realiza el Parse de las variables de mapp-oea
	* @access public	
	*/	
	function parseFormOrgMO() {
		
		$this->org_mo = New OrganizacionMO();
		
		if (isset($_POST["id"])){
			$this->organizacion->id = $_POST["id"];
		}

		$this->organizacion->id_tipo = $_POST["id_tipo"];
		$this->organizacion->id_mun_sede = $_POST["id_mun_sede"];
		$this->organizacion->nom = $_POST["nombre"];
		$this->organizacion->cnrr = 0;
		$this->organizacion->view = 1;
		$this->organizacion->dona = 0;
		$this->organizacion->info_confirmada = 1;
		$this->organizacion->consulta_social = 0;

		$this->organizacion->id_papa = 0;
		if (isset($_POST["id_papa"]) && $_POST["id_papa"] != ""){
			$this->organizacion->id_papa = $_POST["id_papa"];
		}

		$this->organizacion->sig = "";
		if (isset($_POST["sigla"]) && $_POST["sigla"] != ""){
			$this->organizacion->sig = $_POST["sigla"];
		}

		$this->organizacion->des = "";
		if (isset($_POST["descripcion"]) && $_POST["descripcion"] != ""){
			$this->organizacion->des = $_POST["descripcion"];
		}

		$this->organizacion->naci = 0;
		if (isset($_POST["naci"]) && $_POST["naci"] != ""){
			$this->organizacion->naci = $_POST["naci"];
		}

		$this->organizacion->nit = "";
		if (isset($_POST["nit"]) && $_POST["nit"] != ""){
			$this->organizacion->nit = $_POST["nit"];
		}

		$this->organizacion->esp_coor = "";
		if (isset($_POST["esp_coor"]) && $_POST["esp_coor"] != ""){
			$this->organizacion->esp_coor = $_POST["esp_coor"];
		}

		$this->organizacion->dir = "";
		if (isset($_POST["dir"]) && $_POST["dir"] != ""){
			$this->organizacion->dir = $_POST["dir"];
		}

		$this->organizacion->tel1 = "";
		if (isset($_POST["tel1"]) && $_POST["tel1"] != ""){
			$this->organizacion->tel1 = $_POST["tel1"];
		}

		$this->organizacion->tel2 = "";
		if (isset($_POST["tel2"]) && $_POST["tel2"] != ""){
			$this->organizacion->tel2 = $_POST["tel2"];
		}

		$this->organizacion->fax = "";
		if (isset($_POST["fax"]) && $_POST["fax"] != ""){
			$this->organizacion->fax = $_POST["fax"];
		}

		$this->organizacion->pu_email = "";
		if (isset($_POST["pu_email"]) && $_POST["pu_email"] != ""){
			$this->organizacion->pu_email = $_POST["pu_email"];
		}

		$this->organizacion->web = "";
		if (isset($_POST["web"]) && $_POST["web"] != ""){
			$this->organizacion->web = $_POST["web"];
		}

		$this->organizacion->n_rep = "";
		if (isset($_POST["n_rep"]) && $_POST["n_rep"] != ""){
			$this->organizacion->n_rep = $_POST["n_rep"];
		}

		$this->organizacion->t_rep = "";
		if (isset($_POST["t_rep"]) && $_POST["t_rep"] != ""){
			$this->organizacion->t_rep = $_POST["t_rep"];
		}

		$this->organizacion->id_deptos = Array();
		if (isset($_POST["id_depto"])){
			$this->organizacion->id_deptos = $_POST["id_depto"];
		}
		
		$this->organizacion->id_muns = Array();
		if (isset($_POST["id_muns"])){
			$this->organizacion->id_muns = $_POST["id_muns"];
		}

		$this->organizacion->id_poblados = Array();
		if (isset($_POST["id_poblados"])){
			$this->organizacion->id_poblados = $_POST["id_poblados"];
		}
		
		$this->organizacion->id_poblaciones = Array();
		if (isset($_POST["id_poblaciones"])){
			$this->organizacion->id_poblaciones = $_POST["id_poblaciones"];
		}
		$this->organizacion->id_sectores = Array();
		if (isset($_POST["id_sectores"])){
			$this->organizacion->id_sectores = $_POST["id_sectores"];
		}

		$this->organizacion->id_enfoques = Array();
		if (isset($_POST["id_enfoques"])){
			$this->organizacion->id_enfoques = $_POST["id_enfoques"];
		}

		$this->org_mo->donantes = Array();
		if (isset($_POST["donantes"])){
			$this->org_mo->donantes = $_POST["donantes"];
		}

		$this->organizacion->bd = 0;
		if (isset($_POST["bd"]) && $_POST["bd"] != ""){
			$this->organizacion->bd = $_POST["bd"];
		}
		
		$this->organizacion->un_email = "";
		if (isset($_POST["un_email"]) && $_POST["un_email"] != ""){
			$this->organizacion->un_email = $_POST["un_email"];
		}

		$this->organizacion->logo = "";
		if (isset($_POST["logo"]) && $_POST["logo"] != ""){
			$this->organizacion->logo = $_POST["logo"];
		}
		
		$this->organizacion->tel_rep = "";
		if (isset($_POST["tel_rep"]) && $_POST["tel_rep"] != ""){
			$this->organizacion->tel_rep = $_POST["tel_rep"];
		}

		$this->organizacion->email_rep = "";
		if (isset($_POST["email_rep"]) && $_POST["email_rep"] != ""){
			$this->organizacion->email_rep = $_POST["email_rep"];
		}
		
		$this->org_mo->org_conoce_nombre = Array();
		if (isset($_POST["org_conoce_nombre"])){
			$this->org_mo->org_conoce_nombre = $_POST["org_conoce_nombre"];
		}

		$this->org_mo->org_conoce_email = Array();
		if (isset($_POST["org_conoce_email"])){
			$this->org_mo->org_conoce_email = $_POST["org_conoce_email"];
		}

		$this->org_mo->org_conoce_tel = Array();
		if (isset($_POST["org_conoce_tel"])){
			$this->org_mo->org_conoce_tel = $_POST["org_conoce_tel"];
		}
		
		$this->org_mo->org_trabaja_nombre = Array();
		if (isset($_POST["org_trabaja_nombre"])){
			$this->org_mo->org_trabaja_nombre = $_POST["org_trabaja_nombre"];
		}

		$this->org_mo->org_trabaja_email = Array();
		if (isset($_POST["org_trabaja_email"])){
			$this->org_mo->org_trabaja_email = $_POST["org_trabaja_email"];
		}

		$this->org_mo->org_trabaja_tel = Array();
		if (isset($_POST["org_trabaja_tel"])){
			$this->org_mo->org_trabaja_tel = $_POST["org_trabaja_tel"];
		}
	}
	
    /**
	* Realiza el Parse de las variables de la forma de crear org dedse 4w
	* @access public	
	*/	
	function parseForm4w() {
		$this->organizacion->id_tipo = $_POST["id_tipo"];
		$this->organizacion->id_mun_sede = $_POST["id_mun_sede"];
		$this->organizacion->nom = $_POST["nombre"];
		$this->organizacion->id_papa = 0;
        $this->organizacion->sig = $_POST["sigla"];
		$this->organizacion->dir = "";
		if (isset($_POST["dir"]) && $_POST["dir"] != ""){
			$this->organizacion->dir = $_POST["dir"];
		}

		$this->organizacion->tel1 = "";
		if (isset($_POST["tel1"]) && $_POST["tel1"] != ""){
			$this->organizacion->tel1 = $_POST["tel1"];
		}

		$this->organizacion->pu_email = "";
		if (isset($_POST["pu_email"]) && $_POST["pu_email"] != ""){
			$this->organizacion->pu_email = $_POST["pu_email"];
		}

    }
}
?>
