<?

/**
 * DAO de Organizacion
 *
 * Contiene los mtodos de la clase Organizacion
 * @author Ruben A. Rojas C.
 */

Class OrganizacionDAO {

	/**
	* Conexin a la base de datos
	* @var object
	*/
	var $conn;

	/**
	* Nombre de la Tabla en la Base de Datos
	* @var string
	*/
	var $tabla;

	/**
	* Nombre de la columna ID de la Tabla en la Base de Datos
	* @var string
	*/
	var $columna_id;

	/**
	* Nombre de la columna Nombre de la Tabla en la Base de Datos
	* @var string
	*/
	var $columna_nombre;

	/**
	* Nombre de la columna para ordenar el RecordSet
	* @var string
	*/
	var $columna_order;

	/**
	* Nmero de Registros en Pantalla para ListarTAbla
	* @var string
	*/
	var $num_reg_pag;

	/**
	* URL para redireccionar despus de Insertar, Actualizar o Borrar
	* @var string
	*/
	var $url;

	/**
	* Define si se van a insertar organizaciones localmente, ej. en misión
	* @var int
	*/
	var $captura_local;

	/**
	 * Nombre de la base de datos de SIDIH
	 * @var string
	 */
	var $db_sidih;

	/**
	 * Nombre de la base de datos de CNRR
	 * @var string
	 */
	var $db_cnrr;

	/**
  * Constructor
	* Crea la conexin a la base de datos
  * @access public
  */
	function OrganizacionDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "organizacion";
		$this->columna_id = "ID_ORG";
		$this->columna_nombre = "NOM_ORG";
		$this->columna_order = "NOM_ORG";
		$this->num_reg_pag = 50;
		$this->captura_local = 0;
		$this->db_sidih = "sissh";
		$this->db_cnrr = "cnrr_sissh";

		if (isset($_SESSION["undaf"]) && $_SESSION["undaf"] == 1){
			$this->url = "../index_undaf.php?m_e=home";
		}
		else{
			$this->url = "index.php?accion=listar&class=OrganizacionDAO&method=ListarTabla&param=";
		}
	}

	/**
  * Consulta los datos de una Organizacion
  * @access public
  * @param int $id ID del Organizacion
  * @return VO
  */
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);

		//Crea un VO
		$vo = New Organizacion();

		if ($this->conn->RowCount($rs) > 0){
			$row_rs = $this->conn->FetchObject($rs);

			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);

		}

		//Retorna el VO
		return $vo;
	}

	/**
	* Consulta el nombre de la Org
	* @access public
	* @param int $id ID del Organizacion
	* @return VO
	*/
	function GetName($id){
		$sql = "SELECT ".$this->columna_nombre." FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchRow($rs);

		//Retorna el VO
		return $row_rs[0];
	}

	/**
	* Consulta el valor de un field de la Org
	* @access public
	* @param int $id ID del Organizacion
	* @param string $field Field de la tabla org
	* @return VO
	*/
	function GetFieldValue($id,$field){
		$sql = "SELECT ".$field." FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchRow($rs);

		//Retorna el VO
		return $row_rs[0];
	}

	/**
	* Consulta los datos de una Organizacion Registrada
	* @access public
	* @param int $id ID del Organizacion
	* @return VO
	*/
	function getOrgRegistro($id){
		$sql = "SELECT * FROM ".$this->tabla."_registro WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$Result = $this->conn->FetchObject($rs);

		//Crea un VO
		$vo = New OrganizacionRegistro();

		$vo->id = $Result->ID_ORG;
		$vo->nom = $Result->NOM_ORG;
		$vo->id_tipo = $Result->ID_TIPO;
		$vo->id_mun_sede = $Result->ID_MUN_SEDE;
		$vo->sig = $Result->SIG_ORG;
		$vo->des = $Result->DES_ORG;
		$vo->naci = $Result->NACI_ORG;
		$vo->dir = $Result->DIR_ORG;
		$vo->tel1 = $Result->TEL1_ORG;
		$vo->tel2 = $Result->TEL2_ORG;
		$vo->fax = $Result->FAX_ORG;
		$vo->pu_email = $Result->PU_MAIL_ORG;
		$vo->web = $Result->WEB_ORG;
		$vo->n_rep = $Result->N_REP_ORG;
		$vo->t_rep = $Result->T_REP_ORG;
		$vo->nit = $Result->NIT_ORG;
		$vo->esp_coor = $Result->ESP_COOR_ORG;
		$vo->ingresa_nombre = $Result->NOM_INGRESA;
		$vo->ingresa_tel = $Result->TEL_INGRESA;
		$vo->ingresa_email = $Result->EMAIL_INGRESA;
		$vo->cnrr = $Result->CNRR;

		//POBLACIONES
		$a = 0;
		$arr = Array();
		$sql_s = "SELECT ID_POB FROM poblacion_org_registro WHERE ID_ORG = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			$arr[$a] = $row_rs_s[0];
			$a++;
		}
		$vo->id_poblaciones = $arr;

		//SECTORES
		$a = 0;
		$arr = Array();
		$sql_s = "SELECT ID_COMP FROM sector_org_registro WHERE ID_ORG = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			$arr[$a] = $row_rs_s[0];
			$a++;
		}
		$vo->id_sectores = $arr;

		//ENFOQUES
		$a = 0;
		$arr = Array();
		$sql_s = "SELECT ID_ENF FROM enf_org_registro WHERE ID_ORG = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			$arr[$a] = $row_rs_s[0];
			$a++;
		}
		$vo->id_enfoques = $arr;

		//DONANTES
		$a = 0;
		$arr = Array();
		$sql_s = "SELECT DONANTE FROM org_registro_donante WHERE ID_ORG = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			$arr[$a] = $row_rs_s[0];
			$a++;
		}
		$vo->donantes = $arr;

		//DEPTOS COBERTURA
		$a = 0;
		$arr = Array();
		$sql_s = "SELECT ID_DEPTO FROM depto_org_registro WHERE ID_ORG = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			if ($row_rs_s[0] != ""){
				$arr[$a] = $row_rs_s[0];
				$a++;
			}
		}
		$vo->id_deptos = $arr;

		//MPIOS
		$a = 0;
		$arr = Array();
		$sql_s = "SELECT ID_MUN FROM mpio_org_registro WHERE ID_ORG = ".$id." AND COBERTURA = 1";
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			if ($row_rs_s[0] != ""){
				$arr[$a] = $row_rs_s[0];
				$a++;
			}
		}
		$vo->id_muns = $arr;

		//ORGS. POB VULNERABLE
		$a = 0;
		$arr = Array();
		$sql_s = "SELECT * FROM org_registro_pob_vul WHERE ID_ORG = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchObject($rs_s)){
			//$arr[$a] = $row_rs_s[0];
			$vo->org_pob_vul_nombre[$a] = $row_rs_s->NOMBRE;
			$vo->org_pob_vul_tel[$a] = $row_rs_s->TEL;
			$vo->org_pob_vul_email[$a] = $row_rs_s->EMAIL;
			$a++;
		}


		//Retorna el VO
		return $vo;
	}

	/**
	* Actualiza el estado de la Org a publicada
	* @access public
	* @param $id_org_r Id de la Org
	*/

	function setPublicadaOrgRegistro($id_org_r){
		$sql = "UPDATE ".$this->tabla."_registro SET PUBLICADA = 1 WHERE ID_ORG = $id_org_r";
		$this->conn->Execute($sql);
	}

	/**
	* Consulta si la Org fue cargada desde registro
	* @access public
	* @param $id_org_r Id de la Org
	*/
	function getPublicadaOrg($id_org_r){
		$sql = "SELECT publicada FROM ".$this->tabla."_registro WHERE ID_ORG = $id_org_r";
		$rs = $this->conn->OpenRecordset($sql);

		if ($this->conn->RowCount($rs) > 0){
			return 1;
		}
		else return 0;
	}

	/**
	* Consulta los mpios sede o cobertura de la org
	* @access public
	* @param int $id_org Id de la Org
	* @return array $array_id  Arreglo de IDs de mpios
	*/
	function getMpiosSedeCobertura($id_org){

		$id_mpios = array();

		$sql = "SELECT id_mun FROM mpio_org WHERE ID_ORG = $id_org";
		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchRow($rs)){
			$id_mpios[] = $row_rs[0];
		}

		$sql = "SELECT id_mun_sede FROM ".$this->tabla." WHERE ID_ORG = $id_org";
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchRow($rs);
		$id_mun_sede = $row_rs[0];

		if (!in_array($id_mun_sede,$id_mpios))	$id_mpios[] = $id_mun_sede;
		return $id_mpios;
	}

	/**
  * Retorna el max ID
  * @access public
  * @return int
  */
	function GetMaxID($db_name=''){
		if ($db_name == ''){
			$sql = "SELECT max(ID_ORG) as maxid FROM ".$this->tabla;
		}
		else{
			$sql = "SELECT max(ID_ORG) as maxid FROM ".$db_name.".".$this->tabla;
		}
		$rs = $this->conn->OpenRecordset($sql);
		if($row_rs = $this->conn->FetchRow($rs)){
			return $row_rs[0];
		}
		else{
			return 0;
		}
	}

	/**
  * Retorna el numero de Registros
  * @access public
  * @return int
  */
	function numRecords($condicion){
		$sql = "SELECT count(".$this->columna_id.") as num FROM ".$this->tabla;
		if ($condicion != ""){
			$sql .= " WHERE ".$condicion;
		}
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchRow($rs);

		return $row_rs[0];
	}

	/**
  * Consulta los datos de los Organizacion que cumplen una condición
  * @access public
  * @param string $condicion Condicin que deben cumplir los Organizacion y que se agrega en el SQL statement.
  * @return array Arreglo de VOs
  */
	function GetAllArray($condicion,$limit,$order_by){
		$c = 0;
		$sql = "SELECT * FROM ".$this->tabla;
		if ($condicion != ""){
			$sql .= " WHERE ".$condicion;
		}

		//ORDER
		if ($order_by != ""){
			$sql .= " ORDER BY ".$order_by;
		}
		else{
			$sql .= " ORDER BY ".$this->columna_order;
		}

		//LIMIT
		if ($limit != ""){
			$sql .= " LIMIT ".$limit;
		}


		$array = Array();

		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchObject($rs)){
			//Crea un VO
			$vo = New Organizacion();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[$c] = $vo;
			$c++;
		}
		//Retorna el Arreglo de VO
		return $array;
	}

	/**
  * Consulta los ID de las Organizacion que cumplen una condición
  * @access public
  * @param string $condicion Condicin que deben cumplir los Organizacion y que se agrega en el SQL statement.
  * @return array Arreglo de VOs
  */
	function GetAllArrayID($condicion,$limit,$order_by){

		$c = 0;
		$sql = "SELECT ".$this->columna_id." FROM ".$this->tabla;
		if ($condicion != ""){
			$sql .= " WHERE ".$condicion;
		}

		//ORDER
		if ($order_by != ""){
			$sql .= " ORDER BY ".$order_by;
		}
		else{
			$sql .= " ORDER BY ".$this->columna_order;
		}

		//LIMIT
		if ($limit != ""){
			$sql .= " LIMIT ".$limit;
		}


		$array = Array();

		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchRow($rs)){
			//Carga el arreglo
			$array[$c] = $row_rs[0];
			$c++;
		}
		//Retorna el Arreglo de VO
		return $array;
	}

    /**
     * Consulta campos de las Organizacion que cumplen una condición
     * @access public
     * @param string $condicion Condicin que deben cumplir los Organizacion y que se agrega en el SQL statement.
     * @return array Arreglo de VOs
     */
    function GetAllArrayFields($condicion,$limit,$order_by,$fields){

        $c = 0;
        $sql = "SELECT $fields FROM ".$this->tabla;
        if ($condicion != ""){
            $sql .= " WHERE ".$condicion;
        }

        //ORDER
        if ($order_by != ""){
            $sql .= " ORDER BY ".$order_by;
        }
        else{
            $sql .= " ORDER BY ".$this->columna_order;
        }

        //LIMIT
        if ($limit != ""){
            $sql .= " LIMIT ".$limit;
        }


        $array = Array();

        $rs = $this->conn->OpenRecordset($sql);
        while ($row_rs = $this->conn->FetchRow($rs)){
            //Carga el arreglo
            foreach(explode(',', $fields) as $i => $n) {

                $array[$c][] = $row_rs[$i];
            }
            $c++;
        }
        //Retorna el Arreglo de VO
        return $array;
    }

	/**
    * Lista los Organizacion que cumplen la condicin en el formato dado
    * @access public
    * @param string $formato Formato en el que se listarn los Organizacion, puede ser Tabla o ComboSelect
    * @param int $valor_combo ID del Organizacion que ser selccionado cuando el formato es ComboSelect
    * @param string $condicion Condicin que deben cumplir los Organizacion y que se agrega en el SQL statement.
    */
	function ListarCombo($formato,$valor_combo,$condicion){
		//$arr = $this->GetAllArray($condicion,'','');

		$v_c_a = is_array($valor_combo);

		$col_nombre = ($formato == 'combo_sigla') ? 'sig_org' : $this->columna_nombre;

		$sql = "SELECT $this->columna_id, $col_nombre FROM $this->tabla";

		if ($condicion != "") $sql .= " WHERE $condicion";

		$sql .= " ORDER BY $this->columna_order";

		$rs = $this->conn->OpenRecordset($sql);

		while ($row = $this->conn->FetchRow($rs)){

			$id = $row[0];
			$nom = $row[1];

			if ($valor_combo == "" && $valor_combo != 0)
				echo "<option value=".$id.">".$nom."</option>";
			else{
				echo "<option value=".$id;

				if (!$v_c_a){
					if ($valor_combo == $id)
						echo " selected ";
				}
				else{
					if (in_array($id,$valor_combo))
						echo " selected ";
				}

				echo ">".$nom."</option>";
			}
		}
	}

	/**
	* Lista las Organizaciones en una Tabla
	* @access public
	*/
	function ListarTabla(){

		//INICIALIZACION DE VARIABLES
		$depto_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();
		$tipo_dao = New TipoOrganizacionDAO();
		$sede = 0;
		$cobertura = 0;
		$sel_col_orden = Array("ID_ORG" => "","NOM_ORG" => "", "SIG_ORG" => "");
		$sel_dir_orden = Array("ASC" => "","DESC" => "");
		$col_orden_url = "";
		$dir_orden_url = "";
		$id_depto_url = "";
		$id_mun_url = "";
		$criterio = "";
		$criterio_col = "";
		$buscar = 0;
		$sede_cobertura_t = "Sede";
		$id_depto = Array();
		$id_mun = Array();
		$id_depto_s = Array();
		$id_mun_s = Array();



		////CLASS
		if (isset($_POST["class"])){
			$class = $_POST['class'];
		}
		else if (isset($_GET["class"])){
			$class = $_GET['class'];
		}

		////METHOD
		if (isset($_POST["method"])){
			$method = $_POST['method'];
		}
		else if (isset($_GET["method"])){
			$method = $_GET['method'];
		}

		////PARAM
		if (isset($_POST["param"])){
			$param = $_POST['param'];
		}
		else if (isset($_GET["method"])){
			$param = $_GET['param'];
		}

		//BUSCAR
		if (isset($_POST["buscar"])){
			$buscar = 1;
		}
		else if (isset($_GET["buscar"]) && $_GET["buscar"] == 1){
			$buscar = 1;
		}

		if (isset($_POST["criterio_col"])){
			$criterio_col = $_POST['criterio_col'];
		}
		else if (isset($_GET["criterio_col"])){
			$criterio_col = $_GET['criterio_col'];
		}

		if (isset($_POST["criterio"])){
			$criterio = $_POST['criterio'];
		}
		else if (isset($_GET["criterio"])){
			$criterio = $_GET['criterio'];
		}


		if (isset($_POST["id_search"])){
			$id_search = $_POST['id_search'];
		}
		else if (isset($_GET["id_search"])){
			$id_search = $_GET['id_search'];
		}

		//SI SE HACE REFINAMIENTO POR PALABRA DE UBICACION GEOGRAFICA SE TOMAL LO VALORES DE SEDE Y COBERTURA POR URL
		if (isset($_GET["criterio"]) || isset($_GET["col_orden"]) || isset($_GET["page"])){
			$sede = $_GET["sede"];
			$cobertura = $_GET["cobertura"];

			//VARIABLE PARA EL TITULO
			if($sede == 1 && $cobertura == 0){
				$sede_cobertura_t = "Sede";
			}
			//COBERTURA
			else if($sede == 0 && $cobertura == 1) {
				$sede_cobertura_t = "Cobertura";
			}
			//AMBOS
			else if ($sede == 1 && $cobertura == 1) {
				$sede_cobertura_t = "Sede o Cobertura";
			}

			//VALOR DE SELECTED PARA LOS COMBOS DE ORDENAMIENTO
			if (isset($_GET["col_orden"])){
				$sel_col_orden[$_GET["col_orden"]] = " selected ";
				$sel_dir_orden[$_GET["dir_orden"]] = " selected ";

				$col_orden_url = $_GET["col_orden"];
				$dir_orden_url = $_GET["dir_orden"];

			}
		}

		//if (!isset($_POST["buscar"])){
		if ($buscar == 0){
			////DEPTOS
			if (isset($_POST["id_depto"])){
				$id_depto = $_POST['id_depto'];
			}
			else if (isset($_GET["id_depto"])){
				$id_depto = $_GET['id_depto'];
				$id_depto = explode(",",$id_depto);
			}

			$m = 0;
			foreach ($id_depto as $id){
				$id_depto_s[$m] = $id;
				$m++;
			}
			$id_depto_s = implode(",",$id_depto_s);
			$id_depto_url = implode(",",$id_depto);

			////MUNS
			if (isset($_POST["id_muns"])){
				$id_mun = $_POST['id_muns'];
			}
			else if (isset($_GET["id_muns"]) && $_GET["id_muns"] != ""){
				$id_mun = $_GET['id_muns'];
				$id_mun = explode(",",$id_mun);
			}

			if (count($id_mun) > 0){
				$m = 0;
				foreach ($id_mun as $id){
					//$id_mun_s[$m] = "'".$id."'";
					$id_mun_s[$m] = $id;
					$m++;
				}
				$id_mun_s = implode(",",$id_mun_s);
				$id_mun_url = implode(",",$id_mun);
			}

			//TITULO DE SEDE/COBERTURA
			//SEDE
			if(isset($_POST["sede"]) && !isset($_POST["cobertura"])){
				$sede_cobertura_t = "Sede";
				$sede = 1;
			}
			//COBERTURA
			else if(isset($_POST["cobertura"]) && !isset($_POST["sede"])) {
				$sede_cobertura_t = "Cobertura";
				$cobertura = 1;
			}
			//AMBOS
			else if (isset($_POST["cobertura"]) && isset($_POST["sede"])) {
				$sede_cobertura_t = "Sede o Cobertura";
				$sede = 1;
				$cobertura = 1;
			}

			$arr_id = Array();
			$arr_id_sede = Array();
			$i = 0;
			//EN TODO EL DEPARTAMENTO
			if (count($id_mun) == 0 && count($id_depto) > 0){
				//SEDE
				if ($sede == 1 && $cobertura == 0){
					$sql = "SELECT ID_ORG FROM organizacion INNER JOIN municipio ON organizacion.ID_MUN_SEDE = municipio.ID_MUN WHERE ID_DEPTO IN (".$id_depto_s.")";
					//CODIGO_USUARIO_CNRR
					if ($_SESSION["id_tipo_usuario_s"] == 21){
						$sql .= "	AND CNRR = ".$_SESSION["cnrr"];
					}

					if (isset($_GET["col_orden"]) && $_GET["col_orden"] != ""){
						$sql .= " ORDER BY ".$_GET["col_orden"]." ".$_GET["dir_orden"];
					}
					else{
						$sql .= " ORDER BY ID_ORG ASC";
					}

					$rs = $this->conn->OpenRecordset($sql);
					while ($row_rs = $this->conn->FetchRow($rs)){
						$arr_id[$i] = $row_rs[0];
						$i++;
					}

				}
				//COBERTURA
				else if ($sede == 0 && $cobertura == 1){
					$sql = "SELECT organizacion.ID_ORG FROM depto_org INNER JOIN organizacion ON depto_org.ID_ORG = organizacion.ID_ORG WHERE ID_DEPTO IN (".$id_depto_s.")";
					//CODIGO_USUARIO_CNRR
					if ($_SESSION["id_tipo_usuario_s"] == 21){
						$sql .= "	AND CNRR = ".$_SESSION["cnrr"];
					}

					if (isset($_GET["col_orden"]) && $_GET["col_orden"] != ""){
						$sql .= " ORDER BY organizacion.".$_GET["col_orden"]." ".$_GET["dir_orden"];
					}
					else{
						$sql .= " ORDER BY organizacion.ID_ORG ASC";
					}

					$rs = $this->conn->OpenRecordset($sql);
					while ($row_rs = $this->conn->FetchRow($rs)){
						$arr_id[$i] = $row_rs[0];
						$i++;
					}
				}
				//AMBOS
				else if ($sede == 1 && $cobertura == 1){
					$sql = "SELECT ID_ORG FROM organizacion INNER JOIN municipio ON organizacion.ID_MUN_SEDE = municipio.ID_MUN WHERE ID_DEPTO IN (".$id_depto_s.")";
					//CODIGO_USUARIO_CNRR
					if ($_SESSION["id_tipo_usuario_s"] == 21){
						$sql .= "	AND CNRR = ".$_SESSION["cnrr"];
					}

					if (isset($_GET["col_orden"]) && $_GET["col_orden"] != ""){
						$sql .= " ORDER BY ".$_GET["col_orden"]." ".$_GET["dir_orden"];
					}
					else{
						$sql .= " ORDER BY ID_ORG ASC";
					}

					$rs = $this->conn->OpenRecordset($sql);
					while ($row_rs = $this->conn->FetchRow($rs)){
						$arr_id_sede[$i] = $row_rs[0];
						$arr_id[$i] = $row_rs[0];
						$i++;
					}

					$sql = "SELECT organizacion.ID_ORG FROM depto_org INNER JOIN organizacion ON depto_org.ID_ORG = organizacion.ID_ORG WHERE ID_DEPTO IN (".$id_depto_s.")";
					//CODIGO_USUARIO_CNRR
					if ($_SESSION["id_tipo_usuario_s"] == 21){
						$sql .= "	AND CNRR = ".$_SESSION["cnrr"];
					}

					if (isset($_GET["col_orden"]) && $_GET["col_orden"] != ""){
						$sql .= " ORDER BY organizacion.".$_GET["col_orden"]." ".$_GET["dir_orden"];
					}
					else{
						$sql .= " ORDER BY organizacion.ID_ORG ASC";
					}

					$rs = $this->conn->OpenRecordset($sql);
					while ($row_rs = $this->conn->FetchRow($rs)){
						if (!in_array($row_rs[0],$arr_id)){
							$arr_id[$i] = $row_rs[0];
							$i++;
						}
						$arr_id_cobertura[$i] = $row_rs[0];
					}
				}
			}
			//MUNICIPIO
			else{
				//SEDE
				if ($sede == 1 && $cobertura == 0){
					$sql = "SELECT ID_ORG FROM organizacion WHERE ID_MUN_SEDE IN (".$id_mun_s.")";
					//CODIGO_USUARIO_CNRR
					if ($_SESSION["id_tipo_usuario_s"] == 21){
						$sql .= "	AND CNRR = ".$_SESSION["cnrr"];
					}

					if (isset($_GET["col_orden"]) && $_GET["col_orden"] != ""){
						$sql .= " ORDER BY ".$_GET["col_orden"]." ".$_GET["dir_orden"];
					}
					else{
						$sql .= " ORDER BY ID_ORG ASC";
					}

					$rs = $this->conn->OpenRecordset($sql);
					while ($row_rs = $this->conn->FetchRow($rs)){
						$arr_id[$i] = $row_rs[0];
						$i++;
					}
				}
				//COBERTURA
				else if ($sede == 0 && $cobertura == 1){
					$sql = "SELECT organizacion.ID_ORG FROM mpio_org INNER JOIN organizacion ON mpio_org.ID_ORG = organizacion.ID_ORG WHERE ID_MUN IN (".$id_mun_s.")";
					//CODIGO_USUARIO_CNRR
					if ($_SESSION["id_tipo_usuario_s"] == 21){
						$sql .= "	AND CNRR = ".$_SESSION["cnrr"];
					}

					if (isset($_GET["col_orden"]) && $_GET["col_orden"] != ""){
						$sql .= " ORDER BY organizacion.".$_GET["col_orden"]." ".$_GET["dir_orden"];
					}
					else{
						$sql .= " ORDER BY organizacion.ID_ORG ASC";
					}

					$rs = $this->conn->OpenRecordset($sql);
					while ($row_rs = $this->conn->FetchRow($rs)){
						$arr_id[$i] = $row_rs[0];
						$i++;
					}
				}
				//AMBOS
				else if ($sede == 1 && $cobertura == 1){
					$sql = "SELECT ID_ORG FROM organizacion WHERE ID_MUN_SEDE IN (".$id_mun_s.")";
					//CODIGO_USUARIO_CNRR
					if ($_SESSION["id_tipo_usuario_s"] == 21){
						$sql .= "	AND CNRR = ".$_SESSION["cnrr"];
					}

					if (isset($_GET["col_orden"]) && $_GET["col_orden"] != ""){
						$sql .= " ORDER BY ".$_GET["col_orden"]." ".$_GET["dir_orden"];
					}
					else{
						$sql .= " ORDER BY ID_ORG ASC";
					}

					$rs = $this->conn->OpenRecordset($sql);
					while ($row_rs = $this->conn->FetchRow($rs)){
						$arr_id[$i] = $row_rs[0];
						$arr_id_sede[$i] = $row_rs[0];
						$i++;
					}

					$sql = "SELECT organizacion.ID_ORG FROM mpio_org INNER JOIN organizacion ON mpio_org.ID_ORG = organizacion.ID_ORG WHERE ID_MUN IN (".$id_mun_s.")";
					//CODIGO_USUARIO_CNRR
					if ($_SESSION["id_tipo_usuario_s"] == 21){
						$sql .= "	AND CNRR = ".$_SESSION["cnrr"];
					}

					if (isset($_GET["col_orden"]) && $_GET["col_orden"] != ""){
						$sql .= " ORDER BY organizacion.".$_GET["col_orden"]." ".$_GET["dir_orden"];
					}
					else{
						$sql .= " ORDER BY organizacion.ID_ORG ASC";
					}

					$rs = $this->conn->OpenRecordset($sql);
					while ($row_rs = $this->conn->FetchRow($rs)){
						if (!in_array($row_rs[0],$arr_id)){
							$arr_id[$i] = $row_rs[0];
							$i++;
						}
						$arr_id_cobertura[$i] = $row_rs[0];
					}
				}
			}

			//REFINA LA BUSQUEDA DE UBICACION GEOGRAFICA POR PALABRA
			//if (isset($_GET["criterio"])){
			if ($criterio != ""){
				$sql = "SELECT ID_ORG FROM organizacion WHERE ".$criterio_col." LIKE '%".$criterio."%'";

				//CODIGO_USUARIO_CNRR
				if ($_SESSION["id_tipo_usuario_s"] == 21){
					$sql .= "	AND CNRR = ".$_SESSION["cnrr"];
				}

				if (isset($_GET["col_orden"]) && $_GET["col_orden"] != ""){
					$sql .= " ORDER BY ".$_GET["col_orden"]." ".$_GET["dir_orden"];
				}
				else{
					$sql .= " ORDER BY ID_ORG ASC";
				}

				$rs = $this->conn->OpenRecordset($sql);
				$i = 0;
				while ($row_rs = $this->conn->FetchRow($rs)){
					$arr_id_cr[$i] = $row_rs[0];
					$i++;
				}

				//UBICACION + CRITERIO
				$arr_id = array_intersect($arr_id,$arr_id_cr);
			}

			$c = 0;
			$arr = Array();
			foreach ($arr_id as $id){
				//Carga el VO
				$vo = $this->Get($id);
				//Carga el arreglo
				$arr[$c] = $vo;
				$c++;
			}
		}
		//BUSQUEDA DE ORGANIZACIONES POR PALABRA
		else {

			if (isset($criterio_col) && isset($criterio[1])){
				$condicion = $criterio_col." LIKE '%".$criterio."%'";
			}
			else if (isset($id_search)){
				$condicion = "id_org = $id_search";
			}

			//CODIGO_USUARIO_CNRR
			if ($_SESSION["id_tipo_usuario_s"] == 21){
				$condicion .= "	AND CNRR = ".$_SESSION["cnrr"];
			}

			$order_by = '';
			if (isset($_GET["col_orden"]) && $_GET["col_orden"] != ""){
				$order_by .= $_GET["col_orden"]." ".$_GET["dir_orden"];
			}

			$arr = $this->GetAllArray($condicion,'',$order_by);
		}


		$num_arr = count($arr);

		echo "<div align='center'><table width='95%' align='center' cellspacing='1' cellpadding='3'>";
		//if (!isset($_POST["buscar"])){
		if ($buscar == 0){
			//UBICACION GEOGRAFICA
			echo "<tr>";
			echo "<td valign='top'>
					<table width='95%' align='center' cellspacing='1' cellpadding='3' class='titulo_lista'>
	    				<tr><td align='center' colspan='2'>Consulta por ubicaci&oacute;n geogrfica de Organizaciones con ".$sede_cobertura_t." en:</td></tr>";

			echo "<tr><td bgcolor='#FFFFFF' valign='top' align='left'>Departamento (s): ";
			$d = 0;
			foreach ($id_depto as $id_d){
				$d_vo = $depto_dao->Get($id_d);
				if ($d == 0)	echo $d_vo->nombre;
				else		echo " - ".$d_vo->nombre;
			}
			echo "</tr>";
			echo "<tr><td bgcolor='#FFFFFF' valign='top' align='left'>Municipio (s): ";

			if (count($id_mun) == 0 && count($id_depto) > 0){
				echo "En todo el Departamento";
			}
			else{
				$d = 0;
				foreach ($id_mun as $id_m){
					$m_vo = $mun_dao->Get($id_m);
					echo " - ".$m_vo->nombre."<br>";
				}
			}
			echo "</table>
		    	</td>";
			echo "</tr>";
		}
		else{
			echo "<tr>";
			echo "<td valign='top'>
				<table width='100%' align='center' cellspacing='1' cellpadding='3' class='titulo_lista'>
    				<tr class='titulo_lista'><td align='center' colspan='2'>Resultado de la bsqueda de Organizaciones por palabra</td></tr>
					<tr bgcolor='#FFFFFF'><td>Palabra: <b>".$criterio."</b> - Buscando en: <b>";
			if ($criterio_col == "SIG_ORG")	echo "Sigla";
			else										echo "Nombre";
			echo "</b></td></tr>
				</table>";
			echo "</td>";
			echo "</tr>";

		}
		echo "</table></div>";

		echo "<table width='95%' align='center' cellspacing='1' cellpadding='3' border=0>";
		if ($num_arr > 1){

			//REFINAMIENTO POR PALABRA
			if ($num_arr > 1 && $buscar == 0){
				echo "<tr><td colspan='4'><br>
					<table width='400' cellspacing='1' cellpadding='3' class='titulo_lista'>
						<tr class='titulo_lista'><td align='center' colspan='2'>Refinar los resultados de la consulta</td></tr>
						<tr bgcolor='#FFFFFF'>
							<td align='right'>Palabra</td>
							<td><input type='text' id='criterio' name='criterio' class='textfield' ";
				if (isset($_GET["criterio"])){
					echo "value = '".$_GET["criterio"]."'";
				}
				echo "/></td>
						</tr>
						<tr bgcolor='#FFFFFF'>
							<td align='right'>Buscar en</td>
							<td>
								<select id='criterio_col' name='criterio_col' class='select'>
								  <option value='NOM_ORG'>Nombre</option>
								  <option value='SIG_ORG'";
				if (isset($_GET["criterio"]) && $_GET["criterio_col"] == 'SIG_ORG'){
					echo " selected ";
				}
				echo ">Sigla</option>
								</select>&nbsp;<input type='button' value='Buscar' class='boton' onclick=\"if(document.getElementById('criterio').value == '')  {alert('Escriba el criterio de bsqueda!');return false;}  else{location.href='index.php?id_depto=".$id_depto_url."&id_muns=".$id_mun_url."&accion=listar&class=".$class."&method=".$method."&param=".$param."&sede=".$sede."&cobertura=".$cobertura."&criterio='+document.getElementById('criterio').value+'&criterio_col='+document.getElementById('criterio_col').value}  \"/>
							</td>
						</tr>
					</table>

		    	</td></tr>";
			}

			echo "<tr><td>&nbsp;</td></tr>
			<tr>
				<td colspan='5'><b>Ordenar listado por</b>:
					<select id='col_orden'name='col_orden' class='select'>
						<option value='ID_ORG' ".$sel_col_orden["ID_ORG"].">Id</option>
						<option value='NOM_ORG' ".$sel_col_orden["NOM_ORG"].">Nombre</option>
						<option value='SIG_ORG' ".$sel_col_orden["SIG_ORG"].">Sigla</option>
					</select>&nbsp;
					<select id='dir_orden' name='dir_orden' class='select'>
						<option value='ASC' ".$sel_dir_orden["ASC"].">Ascendente</option>
						<option value='DESC' ".$sel_dir_orden["DESC"].">Descendente</option>
					</select>&nbsp;
					<input type='button' value='Ordenar' class='boton' onclick=\"location.href='index.php?id_depto=".$id_depto_url."&id_muns=".$id_mun_url."&accion=listar&class=".$class."&method=".$method."&param=".$param."&sede=".$sede."&cobertura=".$cobertura."&buscar=".$buscar."&criterio=".$criterio."&criterio_col=".$criterio_col."&col_orden='+document.getElementById('col_orden').value+'&dir_orden='+document.getElementById('dir_orden').value;\" \>
				</td>
			</tr>";
		}

		if ($num_arr > 0){
			echo"<tr class='titulo_lista'>
				<td align='center' width='50'>ID</td>
				<td>Nombre</td>
				<td>Sigla</td>
				<td>Tipo</td>
				<td>Actualizaci&oacute;n</td>
				<td>Registrada<br>Web</td>";
			if($cobertura == 1 && $sede == 1) {
				echo "<td>Sede/Cobertura</td>";
			}
			echo "<td align='center' width='150'>Registros: ".$num_arr."</td>
	    		</tr>";

			//PAGINACION
			$inicio = 0;
			$pag_url = 1;
			if (isset($_GET['page']) && $_GET['page'] > 1){
				$pag_url = $_GET['page'];
				$inicio = ($pag_url-1)*$this->num_reg_pag;
			}
			$fin = $inicio + $this->num_reg_pag;
			if ($fin > $num_arr){
				$fin = $num_arr;
			}

			for($p=$inicio;$p<$fin;$p++){
				$style = "";
				if (fmod($p+1,2) == 0)  $style = "fila_lista";

				//NOMBRE
				if ($arr[$p]->nom != ""){

					//NOMBRE DEL TIPO DE ORGANIZACION
					$tipo = $tipo_dao->Get($arr[$p]->id_tipo);
					$nom_tipo = $tipo->nombre_es;

					echo "<tr class='".$style."'>";
					echo "<td align='center'>".$arr[$p]->id."</td>";
					echo "<td>".$arr[$p]->nom."</td>";
					echo "<td>".$arr[$p]->sig."</td>";
					echo "<td>".$nom_tipo."</td>";
					echo "<td>".$arr[$p]->fecha_update."</td>";

					//SI ES REGISTRADA
					if ($this->getPublicadaOrg($arr[$p]->id) == 1){
						echo "<td align='center'><img src='images/org/publicada.gif'></td>";
					}
					else{
						echo "<td align='center'><img src='images/org/no_publicada.gif'></td>";
					}

					if ($cobertura == 1 && $sede == 1) {
						//SEDE
						if (in_array($arr[$p]->id,$arr_id_sede)){
							echo "<td>Sede</td>";
						}
						else if (in_array($arr[$p]->id,$arr_id_cobertura)){
							echo "<td>Cobertura</td>";
						}
						else{
							echo "<td>Sede/Cobertura</td>";
						}
					}
					echo "<td align='center'><a href='#' onclick=\"window.open('ver.php?class=OrganizacionDAO&method=Ver&param=".$arr[$p]->id."','','top=30,left=30,height=900,width=900,scrollbars=1');return false;\">Ver</a>
					| <a href='".$_SERVER['PHP_SELF']."?accion=actualizar&id=".$arr[$p]->id."'>Modificar</a>";

					$check_borrar = $this->checkBorrar($arr[$p]->id);
					if (count($check_borrar) == 0){
						echo "| <a href='index.php?accion=borrar&class=".$class."&method=Borrar&param=".$arr[$p]->id."' onclick=\"return confirm('Está seguro que desea borrar la Organizaci&oacute;n: ".$arr[$p]->nom."');\">Borrar</a>";
					}
					else{
						echo "| <a href='#' onclick=\"document.getElementById('info_borrar_".$arr[$p]->id."').style.display='';return false;\">[Borrar] </a>";

						echo "<div id='info_borrar_".$arr[$p]->id."' style='display:none;background:#CCCCCC;padding:3px;text-align:left'>";
						if (isset($check_borrar['org'])){
							$id = $check_borrar['org'][0];
							echo "- Donante en la organizaci&oacute;n: <a href='index.php?accion=actualizar&id=$id' target='_blank'>$id</a><br />";
						}

						if (isset($check_borrar['proyecto'])){

							$arr_tipo = array(1 => 'Ejecutora',
									2 => 'Donante',
									3 => 'Socio',
									4 => 'Trabajo coordinado',
									5 => 'Oficina ejecutora');

							foreach ($arr_tipo as $key=>$tit){
								if (isset($check_borrar['proyecto'][$key])){
									$id = $check_borrar['proyecto'][$key];
									echo " -$tit proyecto: <a href='index_undaf.php?m_e=proyecto&accion=actualizar&id=$id' target='_blank'>$id</a><br />";
								}
							}
						}
						echo "<p align='center'><a href='#' onclick=\"document.getElementById('info_borrar_".$arr[$p]->id."').style.display='none'; return false;\">[ cerrar ]</a></p> </div>";
					}

					echo "</td>";
					echo "</tr>";
				}
			}

			echo "<tr><td>&nbsp;</td></tr>";

			//PAGINACION
			if ($num_arr > $this->num_reg_pag){

				$num_pages = ceil($num_arr/$this->num_reg_pag);
				echo "<tr><td colspan='5' align='center'>";

				echo "Ir a la pgina:&nbsp;<select onchange=\"location.href='index.php?id_depto=".$id_depto_url."&id_muns=".$id_mun_url."&accion=listar&class=".$class."&method=".$method."&param=".$param."&sede=".$sede."&cobertura=".$cobertura."&col_orden=".$col_orden_url."&dir_orden=".$dir_orden_url."&buscar=".$buscar."&criterio=".$criterio."&criterio_col=".$criterio_col."&page='+this.value\" class='select'>";
				for ($pa=1;$pa<=$num_pages;$pa++){
					echo " <option value='".$pa."'";
					if ($pa == $pag_url)	echo " selected ";
					echo ">".$pa."</option> ";
				}
				echo "</select>";
				echo "</td></tr>";
			}
		}
		else{
			echo "<tr><td align='center'><br><b>NO SE ENCONTRARON ORGANIZACIONES</b></td></tr>";
			echo "<tr><td align='center'><br><a href='".$_SERVER['PHP_SELF']."?m_e=org&accion=listar&class=OrganizacionDAO&method=ListarTabla&param='>Regresar</a></td></tr>";
		}
		echo "</table>";
	}

	/**
	* Lista las Organizaciones registradas y pendientes de publicación
	* @access public
	*/
	function ListarOrgPublicar(){

		//INICIALIZACION DE VARIABLES
		$mun_dao = New MunicipioDAO();

		echo "<table cellpadding=3 cellspacing=1 width='95%' align='center'>";

		$sql = "SELECT * FROM organizacion_registro";

		$chk_filtro = array(1=>"",0=>" selected ");
		if (isset($_GET["filtro"]) && $_GET["filtro"] != ""){
			$sql .= " WHERE publicada = ".$_GET["filtro"];

			$chk_filtro = array(1=>"",0=>"  ");
			$chk_filtro[$_GET["filtro"]] = " selected ";
		}
		else if (isset($_GET["filtro"]) && $_GET["filtro"] == ""){
			$chk_filtro = array(1=>"",0=>"  ");
		}
		else{
			$sql .= " WHERE publicada = 0";
		}

		if (isset($_GET["borrar_id"])){
			$this->borrarOrgRegistro($_GET["borrar_id"]);
		}

		$rs = $this->conn->OpenRecordset($sql);

		$num_orgs = $this->conn->RowCount($rs);

		if ($num_orgs > 0){
			echo "<tr><td colspan=7 align='center' class='titulo_lista'><b>ORGANIZACIONES REGISTRADAS Y PENDIENTES DE PUBLICACION</b></td></tr>";
			echo "<tr><td colspan=5>Filtrar por:&nbsp;<select id='filtro' onchange=\"location.href='index.php?m_e=org&accion=publicar&class=OrganizacionDAO&method=ListarOrgPublicar&filtro='+this.value\" class='select'>
					<option value=''>Todas las organizaciones registradas</option>
					<option value=1 ".$chk_filtro[1].">Todas las organizaciones registradas y publicadas</option>
					<option value=0 ".$chk_filtro[0].">Todas las organizaciones registradas y NO publicadas</option>
				</select></td></tr>";
			echo "<tr class='titulo_lista'>
				  	<td>&nbsp;</td>
				  	<td>&nbsp;</td>
					<td align='center' colspan='3'>Persona quien ingres&oacute; la Info</td>
				  </tr>";

			echo "<tr class='titulo_lista'>
				  	<td width='180'>Nombre</td>
				  	<td width='80'>Sigla</td>
				  	<td width='80'>Sede</td>
				  	<td>Nombre</td>
				  	<td>Email</td>
				  	<td>Tel</td>
				  	<td></td>
				  	<td>&nbsp;</td>
				  </tr>
				  ";


		}
		else{
			echo "<tr><td>NO HAY ORGANIZACIONES REGISTRADAS</td></tr>";
		}

		while ($row_rs = $this->conn->FetchObject($rs)){

			$sede = $mun_dao->Get($row_rs->ID_MUN_SEDE);

			echo "<tr class='fila_lista'>
					<td>$row_rs->NOM_ORG</td>
					<td>$row_rs->SIG_ORG</td>
					<td>$sede->nombre</td>
					<td>$row_rs->NOM_INGRESA</td>
					<td>$row_rs->EMAIL_INGRESA</td>
					<td>$row_rs->TEL_INGRESA</td>
					<td>";

			if($row_rs->PUBLICADA == 0){
				echo "<a href='index.php?m_e=org&accion=insertar&id_org_r=".$row_rs->ID_ORG."'>Publicar</a><br>";
				echo "<a href='index.php?m_e=org&accion=publicar&class=OrganizacionDAO&method=ListarOrgPublicar&param=&borrar_id=$row_rs->ID_ORG' onclick='return confirm(\"Esta seguro?\")'>Borrar</a></td>";
			}
			else{
				echo "Publicada";
			}

			echo "<td><a href='#' onclick=\"window.open('../registro_org.php?id_org_r=".$row_rs->ID_ORG."','','top=0,left=0,width=950,height=800,scrollbars=1');return false;\">+ info</a></td>";

			//Link ver Ocurrencias
			echo "<td><a href='#' onclick=\"if(document.getElementById('org_".$row_rs->ID_ORG."_ocurrencias').style.display == 'none'){document.getElementById('org_".$row_rs->ID_ORG."_ocurrencias').style.display = ''}else{document.getElementById('org_".$row_rs->ID_ORG."_ocurrencias').style.display = 'none'};\">Ver ocurrencias</a></td></tr>";

			//Ocurrencias
			echo "<tr><td id='org_".$row_rs->ID_ORG."_ocurrencias' style='display:none;background-color:#F9FFD1' width=100% colspan=9 align='center'><table border=0 class='tabla_consulta' cellpadding=5>";

			echo "<tr class='titulo_lista'><td align='center'>OCURRENCIAS POR NOMBRE</td><td align='center'>OCURRENCIAS POR SIGLA</td></tr>";
			echo "<tr>";

			//Por nombre
			echo "<td valign='top'>";

			$num_o_n = 0;
			$sql_o_n = "SELECT * FROM organizacion WHERE nom_org LIKE '%$row_rs->NOM_ORG%'";
			$rs_o_n = $this->conn->OpenRecordset($sql_o_n);
			while ($row_rs_o_n = $this->conn->FetchObject($rs_o_n)){
				echo "<a href='#' onclick=\"window.open('ver.php?class=OrganizacionDAO&method=Ver&param=".$row_rs_o_n->ID_ORG."','','top=0,left=0,width=950,height=800,scrollbars=1');return false;\">$row_rs_o_n->NOM_ORG</a><br><br>";
				$num_o_n++;
			}

			if ($num_o_n == 0)	echo "NO HAY OCURRENCIAS";

			echo "</td>";

			//Por sigla
			echo "<td valign='top'>";

			$num_o_s = 0;
			$sql_o_n = "SELECT * FROM organizacion WHERE sig_org LIKE '%$row_rs->SIG_ORG%'";
			$rs_o_n = $this->conn->OpenRecordset($sql_o_n);
			while ($row_rs_o_n = $this->conn->FetchObject($rs_o_n)){
				echo "<a href='#' onclick=\"window.open('ver.php?class=OrganizacionDAO&method=Ver&param=".$row_rs_o_n->ID_ORG."','','top=0,left=0,width=950,height=800,scrollbars=1');return false;\">$row_rs_o_n->SIG_ORG</a><br><br>";
				$num_o_s++;
			}

			if ($num_o_s == 0)	echo "NO HAY OCURRENCIAS";

			echo "</td></tr></table>";

		}

		echo "</table>";
	}


	/**
	* Lista las Organizaciones para sincronizar-publicar con CNRR
	* @access public
	* @param $caso int 1 => Que estan en Sidih y no en CNRR | 2=> al reves
	* @return $orgs_sincro array Id de las organizaciones para sincro
	*/
	function getOrgsSincronizarCNRR($caso){

		//INICIALIZACION DE VARIABLES
		$id_orgs_sincro = array();

		$sql = "SELECT id_org,nom_org,sig_org,id_mun_sede FROM organizacion WHERE CNRR = 1 ORDER BY nom_org";
		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchObject($rs)){

			$id = $row_rs->id_org;
			$nom = $row_rs->nom_org;
			$sig = $row_rs->sig_org;

			$sql_cnrr = "SELECT nom_org FROM ".$this->db_cnrr.".organizacion WHERE nom_org = '".str_replace("'","''",$nom)."' OR sig_org = '".str_replace("'","''",$sig)."'";
			$rs_cnrr = $this->conn->OpenRecordset($sql_cnrr);
			if ($this->conn->RowCount($rs_cnrr) == 0){
				$id_orgs_sincro[] = $id;
			}
		}

		return $id_orgs_sincro;
	}

	/**
	* Lista las Organizaciones para sincronizar-publicar con CNRR
	* @access public
	* @param $caso int 1 => Que estan en Sidih y no en CNRR | 2=> al reves
	*/
	function ListarOrgSincronizarCNRR($caso){

		//INICIALIZACION DE VARIABLES
		$mun_dao = New MunicipioDAO();

		echo "<table cellpadding=3 cellspacing=1 width='770' align='center'>";
		echo "<tr><td>&nbsp;</td></tr>";

		$id_orgs = $this->getOrgsSincronizarCNRR($caso);
		$num_orgs =count($id_orgs);

		if ($num_orgs > 0){
			echo "<tr><td colspan=4>A continuaci&oacute;n seleccione las Organizaciones que desea sincronizar. Revise detenidamente la lista.  Las Organiaciones cuyos nombres (entre SIDIH y CNRR) sean diferentes por caracteres como tildes, espacios o errores de digitaci&oacute;n serán listadas acá, por favor, use su criterio para saber si las debe sincronizar o solo editar la que est&eacute; mal. </td></tr>";
			echo "<tr><td>&nbsp;</td></tr>";
			echo "<tr><td>[ <b>$num_orgs Organizacion(es)</b> ]</td></tr>";
			echo "<tr class='titulo_lista'>
				  	<td>Nombre</td>
				  	<td>Sigla</td>
				  	<td>Sede</td>
				  	<td>Sincronizar</td>
				  	<td width=45></td>
				  </tr>";

			foreach ($id_orgs as $id){

				$org = $this->Get($id);

				echo "<tr class='fila_lista'>
				  	<td>$org->nom</td>
				  	<td>$org->sig</td>";

				$mun = $mun_dao->Get($org->id_mun_sede);
				echo "<td>$mun->nombre</td>";

			  	echo "<td align='center'><input type='checkbox' name='id_orgs_sincro[]' value=$id></td>
			  	<td>[ <a href='#' onclick=\"window.open('ver.php?class=OrganizacionDAO&method=Ver&param=".$org->id."','','top=0,left=0,width=950,height=800,scrollbars=1');return false;\">Ver</a> ]</td>";

			  	echo "</tr>";

			}
			?>

				<tr><td>&nbsp;</td></tr>
				<tr>
				  <td align='center' colspan="5">
					  <input type="hidden" name="accion" value="<?=$accion?>" />
					  <input type="submit" name="submit" value="Sincronizar" class="boton" onclick="return validar();" />
				  </td>
				</tr>
			<?
		}
		else{
			echo "<tr><td align='center'><b>NO HAY ORGANIZACIONES PARA SINCRONIZAR</b></td></tr>";
		}

		echo "</table>";
	}

	/**
	* Sincroniza las Organizaciones entre Sidih y CNRR
	* @access public
	* @param $caso int 1 => Que estan en Sidih y no en CNRR | 2=> al reves
	*/
	function SincronizarCNRR($caso){

		//Sidih->CNRR
		if ($caso == 1){

			$id_orgs = implode(",",$_POST["id_orgs_sincro"]);

			$orgs = $this->GetAllArray("id_org IN ($id_orgs)","","");

			$num = 0;
			foreach ($orgs as $org){
				$this->Insertar($org,0,$this->db_cnrr);
				$id_organizacion = $this->GetMaxID($this->db_cnrr);
				$this->InsertarTablasUnionCobertura($org,$id_organizacion,1,$this->db_cnrr);
				$this->InsertarTablasUnionCobertura($org,$id_organizacion,2,$this->db_cnrr);
				$this->InsertarTablasUnionCobertura($org,$id_organizacion,5,$this->db_cnrr);
				$num++;
			}

			//Sincroniza Tipos de Org
			mysql_select_db($this->db_sidih);
			$sql = "SELECT * FROM tipo_org";
			$rs = $this->conn->OpenRecordset($sql);
			while ($row_rs = $this->conn->FetchObject($rs)){
				$sql_cnrr = "SELECT id_tipo FROM ".$this->db_cnrr.".tipo_org WHERE id_tipo = $row_rs->ID_TIPO";
				$rs_cnrr = $this->conn->OpenRecordset($sql_cnrr);

				if ($this->conn->RowCount($rs_cnrr) == 0){
					$sql_ins = "INSERT INTO tipo_org (id_tipo,nomb_tipo_es,nomb_tipo_in,cnrr,ocha)
																 VALUES ($row_rs->ID_TIPO,'$row_rs->NOMB_TIPO_ES','$row_rs->NOMB_TIPO_IN',1,0)";
					$this->conn->Execute($sql_ins,$this->db_cnrr);
				}
			}

			//Sincroniza Enfoques
			mysql_select_db($this->db_sidih);
			$sql = "SELECT * FROM enfoque";
			$rs = $this->conn->OpenRecordset($sql);
			while ($row_rs = $this->conn->FetchObject($rs)){
				$sql_cnrr = "SELECT id_enf FROM ".$this->db_cnrr.".enfoque WHERE id_enf = $row_rs->ID_ENF";
				$rs_cnrr = $this->conn->OpenRecordset($sql_cnrr);

				if ($this->conn->RowCount($rs_cnrr) == 0){
					$sql_ins = "INSERT INTO enfoque (id_enf,nom_enf_es,nom_enf_in,cnrr,ocha)
																 VALUES ($row_rs->ID_ENF,'$row_rs->NOM_ENF_ES','$row_rs->NOM_ENF_IN',1,0)";
					$this->conn->Execute($sql_ins,$this->db_cnrr);
				}
			}

			//Sincroniza Sectores
			mysql_select_db($this->db_sidih);
			$sql = "SELECT * FROM sector";
			$rs = $this->conn->OpenRecordset($sql);
			while ($row_rs = $this->conn->FetchObject($rs)){
				$sql_cnrr = "SELECT id_comp FROM ".$this->db_cnrr.".sector WHERE id_comp = $row_rs->ID_COMP";
				$rs_cnrr = $this->conn->OpenRecordset($sql_cnrr);

				if ($this->conn->RowCount($rs_cnrr) == 0){
					$sql_ins = "INSERT INTO sector (id_comp,nom_comp_es,nom_comp_in,cnrr,ocha)
																 VALUES ($row_rs->ID_COMP,'$row_rs->NOM_COMP_ES','$row_rs->NOM_COMP_IN',1,0)";
					$this->conn->Execute($sql_ins,$this->db_cnrr);
				}
			}

			//Sincroniza Poblaciones
			mysql_select_db($this->db_sidih);
			$sql = "SELECT * FROM poblacion";
			$rs = $this->conn->OpenRecordset($sql);
			while ($row_rs = $this->conn->FetchObject($rs)){
				$sql_cnrr = "SELECT id_pobla FROM ".$this->db_cnrr.".poblacion WHERE id_pobla = $row_rs->ID_POBLA";
				$rs_cnrr = $this->conn->OpenRecordset($sql_cnrr);

				if ($this->conn->RowCount($rs_cnrr) == 0){
					$sql_ins = "INSERT INTO poblacion (id_pobla,nom_pobla_es,nom_pobla_in,cnrr,ocha)
																 VALUES ($row_rs->ID_POBLA,'$row_rs->NOM_POBLA_ES','$row_rs->NOM_POBLA_IN',1,0)";
					$this->conn->Execute($sql_ins,$this->db_cnrr);
				}
			}

			echo "<table cellpadding=3 cellspacing=1 width='770' align='center'>";
			echo "<tr><td>&nbsp;</td></tr>";
			echo "<tr><td><b>Se sincronizaron $num organizaciones con &eacute;xito</b></td></tr>";
			echo "</table>";
		}
	}

	/**
	* Aplica los filtros de consulta de Organziaciones
	* @access public
	* @return Array Arreglo de Id de Organizaciones que cumplen con los criterios
	*/
	function filtrarOrganizacionesConsulta(){


		//INICIALIZACION DE VARIABLES
		$depto_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();
		$tipo_dao = New TipoOrganizacionDAO();
		$sector_dao = New SectorDAO();
		$enfoque_dao = New EnfoqueDAO();
		$poblacion_dao = New PoblacionDAO();

		$sede = 0;
		$cobertura = 0;
		if(isset($_POST["sede"]) || isset($_GET["sede"])){
			$sede = 1;
		}
		if(isset($_POST["cobertura"]) || isset($_GET["cobertura"])){
			$cobertura = 1;
		}

		//SE CONSTRUYE EL SQL

		$condicion = "";
		$arreglos = "";


		//TIPO
		$var = 'id_tipo_org';
		if (isset($_POST[$var]) || isset($_GET[$var])){
			$id_tipo = (isset($_POST[$var])) ? $_POST[$var] : $_GET[$var];

			$id_s = implode(",",$id_tipo);

			$condicion .= "ID_TIPO IN (".$id_s.")";

			$arr_id_tipo = Array();

			$sql = "SELECT ID_ORG FROM organizacion WHERE ".$condicion;
			$rs = $this->conn->OpenRecordset($sql);
			while ($row_rs = $this->conn->FetchRow($rs)){
				$arr_id_tipo[] = $row_rs[0];
			}

			$arreglos .= "\$arr_id_tipo";
		}

		//POBLACION
		$var = 'id_poblacion';
		if (isset($_POST[$var]) || isset($_GET[$var])){
			$id_poblacion = (isset($_POST[$var])) ? $_POST[$var] : $_GET[$var];

			$arr_id_poblacion = Array();

			$id_s = implode(",",$id_poblacion);

			$sql = "SELECT ID_ORG FROM poblacion_org WHERE ID_POB IN (".$id_s.")";
			$rs = $this->conn->OpenRecordset($sql);
			while ($row_rs = $this->conn->FetchRow($rs)){
				$arr_id_poblacion[] = $row_rs[0];
			}

			if ($arreglos == "")	$arreglos = "\$arr_id_poblacion";
			else					$arreglos .= ",\$arr_id_poblacion";
		}

		//SECTOR
		$var = 'id_sector';
		if (isset($_POST[$var]) || isset($_GET[$var])){
			$id_sector = (isset($_POST[$var])) ? $_POST[$var] : $_GET[$var];

			$arr_id_sector = Array();

			$id_s = implode(",",$id_sector);

			$sql = "SELECT ID_ORG FROM sector_org WHERE ID_COMP IN (".$id_s.")";
			$rs = $this->conn->OpenRecordset($sql);
			while ($row_rs = $this->conn->FetchRow($rs)){
				$arr_id_sector[] = $row_rs[0];
			}

			if ($arreglos == "")	$arreglos = "\$arr_id_sector";
			else					$arreglos .= ",\$arr_id_sector";
		}

		//ENFOQUE
		$var = 'id_enfoque';
		if (isset($_POST[$var]) || isset($_GET[$var])){
			$id_enfoque = (isset($_POST[$var])) ? $_POST[$var] : $_GET[$var];

			$arr_id_enfoque = Array();

			$id_s = implode(",",$id_enfoque);


			$sql = "SELECT ID_ORG FROM enf_org WHERE ID_ENF IN (".$id_s.")";
			$rs = $this->conn->OpenRecordset($sql);
			while ($row_rs = $this->conn->FetchRow($rs)){
				$arr_id_enfoque[] = $row_rs[0];
			}

			if ($arreglos == "")	$arreglos = "\$arr_id_enfoque";
			else					$arreglos .= ",\$arr_id_enfoque";

		}

		//UBIACION GEOGRAFICA
		//MUNICIPIO
		$var = 'id_muns';
		//if (isset($_POST["id_depto"]) && isset($_POST["id_muns"])){
		if (isset($_POST[$var]) || isset($_GET[$var])){
			$id_muns = (isset($_POST[$var])) ? $_POST[$var] : $_GET[$var];
			$arr_id_u_g = Array();

			foreach ($id_muns as $id_m){
				$id_muns[] = "'".$id_m."'";
			}

			$id_muns_s = implode(",",$id_muns);


			//SEDE
			if ($sede == 1 && $cobertura == 0){
				$sql = "SELECT ID_ORG FROM organizacion WHERE ID_MUN_SEDE IN (".$id_muns_s.")";
				$sql .= " ORDER BY ID_ORG ASC";

				$rs = $this->conn->OpenRecordset($sql);
				while ($row_rs = $this->conn->FetchRow($rs)){
					$arr_id_u_g[] = $row_rs[0];
				}
			}
			//COBERTURA
			else if ($sede == 0 && $cobertura == 1){
				$sql = "SELECT organizacion.ID_ORG FROM mpio_org INNER JOIN organizacion ON mpio_org.ID_ORG = organizacion.ID_ORG WHERE ID_MUN IN (".$id_muns_s.")";
				$sql .= " ORDER BY organizacion.ID_ORG ASC";

				$rs = $this->conn->OpenRecordset($sql);
				while ($row_rs = $this->conn->FetchRow($rs)){
					$arr_id_u_g[] = $row_rs[0];
				}
			}
			//AMBOS
			else if ($sede == 1 && $cobertura == 1){
				$sql = "SELECT ID_ORG FROM organizacion WHERE ID_MUN_SEDE IN (".$id_muns_s.")";
				$sql .= " ORDER BY ID_ORG ASC";

				$rs = $this->conn->OpenRecordset($sql);
				while ($row_rs = $this->conn->FetchRow($rs)){
					$arr_id_u_g[] = $row_rs[0];
					$arr_id_u_g_sede[] = $row_rs[0];
				}

				$sql = "SELECT organizacion.ID_ORG FROM mpio_org INNER JOIN organizacion ON mpio_org.ID_ORG = organizacion.ID_ORG WHERE ID_MUN IN (".$id_muns_s.")";
				$sql .= " ORDER BY organizacion.ID_ORG ASC";

				$rs = $this->conn->OpenRecordset($sql);
				while ($row_rs = $this->conn->FetchRow($rs)){
					if (!in_array($row_rs[0],$arr_id_u_g)){
						$arr_id_u_g[] = $row_rs[0];
					}
					$arr_id_cobertura[] = $row_rs[0];
				}
			}
		}

		//if (isset($_POST["id_depto"]) && !isset($_POST["id_muns"])){
		else if ((isset($_POST["id_depto"]) || isset($_GET["id_depto"]))){

			$var  = "id_depto";
			$id_depto = (isset($_POST[$var])) ? $_POST[$var] : $_GET[$var];

			$d = 0;
			foreach ($id_depto as $id_d){
				$id_depto[$d] = "'".$id_d."'";
				$d++;
			}
			$id_depto_s = implode(",",$id_depto);

			$arr_id_u_g = Array();

			//SEDE
			if ($sede == 1 && $cobertura == 0){
				$sql = "SELECT ID_ORG FROM organizacion INNER JOIN municipio ON organizacion.ID_MUN_SEDE = municipio.ID_MUN WHERE ID_DEPTO IN (".$id_depto_s.")";

				$sql .= " ORDER BY ID_ORG ASC";

				$rs = $this->conn->OpenRecordset($sql);
				$i = 0;
				while ($row_rs = $this->conn->FetchRow($rs)){
					$arr_id_u_g[$i] = $row_rs[0];
					$i++;
				}

			}
			//COBERTURA
			else if ($sede == 0 && $cobertura == 1){
				$sql = "SELECT organizacion.ID_ORG FROM depto_org INNER JOIN organizacion ON depto_org.ID_ORG = organizacion.ID_ORG WHERE ID_DEPTO IN (".$id_depto_s.")";

				if (isset($_GET["col_orden"])){
					$sql .= " ORDER BY organizacion.".$_GET["col_orden"]." ".$_GET["dir_orden"];
				}
				else{
					$sql .= " ORDER BY organizacion.ID_ORG ASC";
				}

				$rs = $this->conn->OpenRecordset($sql);
				while ($row_rs = $this->conn->FetchRow($rs)){
					$arr_id_u_g[] = $row_rs[0];
				}
			}
			//AMBOS
			else if ($sede == 1 && $cobertura == 1){
				$sql = "SELECT ID_ORG FROM organizacion INNER JOIN municipio ON organizacion.ID_MUN_SEDE = municipio.ID_MUN WHERE ID_DEPTO IN (".$id_depto_s.")";

				if (isset($_GET["col_orden"])){
					$sql .= " ORDER BY ".$_GET["col_orden"]." ".$_GET["dir_orden"];
				}
				else{
					$sql .= " ORDER BY ID_ORG ASC";
				}

				$rs = $this->conn->OpenRecordset($sql);
				while ($row_rs = $this->conn->FetchRow($rs)){
					$arr_id_u_g_sede[] = $row_rs[0];
					$arr_id_u_g[] = $row_rs[0];
				}

				$sql = "SELECT organizacion.ID_ORG FROM depto_org INNER JOIN organizacion ON depto_org.ID_ORG = organizacion.ID_ORG WHERE ID_DEPTO IN (".$id_depto_s.")";

				if (isset($_GET["col_orden"])){
					$sql .= " ORDER BY organizacion.".$_GET["col_orden"]." ".$_GET["dir_orden"];
				}
				else{
					$sql .= " ORDER BY organizacion.ID_ORG ASC";
				}

				$rs = $this->conn->OpenRecordset($sql);
				while ($row_rs = $this->conn->FetchRow($rs)){
					if (!in_array($row_rs[0],$arr_id_u_g)){
						$arr_id_u_g[] = $row_rs[0];
					}
					$arr_id_u_g_cobertura[] = $row_rs[0];
				}
			}
		}


		if (isset($_POST["id_depto"]) || isset($_GET["id_depto"]) || isset($_POST["id_muns"]) || isset($_GET["id_muns"])){

			if ($arreglos == "")	$arreglos = "\$arr_id_u_g";
			else					$arreglos .= ",\$arr_id_u_g";
		}

		//INTERSECCION DE LOS ARREGLOS PARA REALIZAR LA CONSULTA
		if (count(explode(",",$arreglos)) > 1 ){
			eval("\$arr_id = array_intersect($arreglos);");
		}
		else{
			if ($arreglos != '')	eval("\$arr_id = $arreglos;");
			else				$arr_id = $this->GetAllArrayID('','','');
		}

		$arr_id = array_unique($arr_id);

		return $arr_id;

	}

	/**
	* Lista las Organizaciones en una Tabla
	* @access public
	*/
	function Reportar(){

		//INICIALIZACION DE VARIABLES
		$depto_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();
		$tipo_dao = New TipoOrganizacionDAO();
		$sector_dao = New SectorDAO();
		$enfoque_dao = New EnfoqueDAO();
		$poblacion_dao = New PoblacionDAO();
		$arr_id_cnrr = array();

		//TODAS
		$todas = 0;
		if (isset($_POST["todas"]) && $_POST["todas"] == 1 && !isset($_POST["id_depto"])){
			$todas = 1;

			$arr_id = $this->GetAllArrayID('','','');
		}
		else{
			$arr_id = $this->filtrarOrganizacionesConsulta();
		}


		/*
		if ($_SESSION["cnrr"] == 1){
			$arr_id_cnrr = $this->GetAllArrayID('CNRR = 1','','');

			$arr_id = array_intersect($arr_id,$arr_id_cnrr);
		}
		*/

		$num_arr = count($arr_id);

		echo "<form action='".$_SERVER['PHP_SELF']."?m_e=org&accion=consultar&class=OrganizacionDAO' method='POST'>";
		echo "<table align='center' class='tabla_reportelist_outer' width='790'>";
		echo "<tr><td>&nbsp;</td></tr>";
		if ($num_arr > 0){
			echo "<tr>
					<td width='300'><a href='javascript:history.back(-1)'><img src='images/back.gif' border=0 >&nbsp;Regresar</a></td>
					<td align='right'>Generar Reporte: <input type='radio' id='basico' name='basico' value='1' checked>&nbsp;B&aacute;sico</a>&nbsp;<input type='radio' id='detallado' name='basico' value=2>&nbsp;Detallado&nbsp;&nbsp;<a href=\"#\" onclick=\"document.getElementById('pdf').value = 1;reportStream('org');return false;\"><img src='images/consulta/generar_pdf.gif' border=0 onmouseover=\"Tip('Exportar a PDF<br><b>Basico</b>: Nombre,Sigla,Tipo,Cobertura<br><b>Detallado</b>: Toda la inforamci&oacute;n')\" onmouseout=\"UnTip()\"></a>&nbsp;&nbsp;<a href=\"#\" onclick=\"document.getElementById('pdf').value = 2;reportStream('org');return false;\"\"><img src='images/consulta/excel.gif' border=0 onmouseover=\"Tip('Exportar a Hoja de C&aacute;lculo<br><b>Basico</b>: Nombre,Sigla,Tipo,Cobertura<br><b>Detallado</b>: Toda la inforamci&oacute;n')\" onmouseout=\"UnTip()\"></a></td>
				</tr>";
		}
		echo "<tr><td align='center' class='titulo_lista' colspan=7>CONSULTA DE ORGANIZACIONES</td></tr>";
		if ($todas == 0){
			echo "<tr><td colspan=3>Consulta realizada aplicando los siguientes filtros:</td>";
			echo "<tr><td colspan=3>";

			//TITULO DE TIPO
			$var = 'id_tipo_org';
			if (isset($_POST[$var]) || isset($_GET[$var])){
				$id_tipo = (isset($_POST[$var])) ? $_POST[$var] : $_GET[$var];

				echo "<img src='images/flecha.gif'> Tipo de Organizaci&oacute;n: ";
				$t = 0;
				foreach($id_tipo as $id_t){
					$vo  = $tipo_dao->Get($id_t);
					if ($t == 0)	echo "<b>".$vo->nombre_es."</b>";
					else			echo ", <b>".$vo->nombre_es."</b>";
					$t++;
				}
				echo "<br>";
			}
			//TITULO DE SECTOR
			$var = 'id_sector';
			if (isset($_POST[$var]) || isset($_GET[$var])){
				$id_sector = (isset($_POST[$var])) ? $_POST[$var] : $_GET[$var];

				echo "<img src='images/flecha.gif'> Sector: ";
				$t = 0;
				foreach($id_sector as $id_t){
					$vo  = $sector_dao->Get($id_t);
					if ($t == 0)	echo "<b>".$vo->nombre_es."</b>";
					else			echo ", <b>".$vo->nombre_es."</b>";
					$t++;
				}
				echo "<br>";
			}
			//TITULO DE ENFOQUE
			$var = 'id_enfoque';
			if (isset($_POST[$var]) || isset($_GET[$var])){
				$id_enfoque = (isset($_POST[$var])) ? $_POST[$var] : $_GET[$var];

				echo "<img src='images/flecha.gif'> Enfoque: ";
				$t = 0;
				foreach($id_enfoque as $id_t){
					$vo  = $enfoque_dao->Get($id_t);
					if ($t == 0)	echo "<b>".$vo->nombre_es."</b>";
					else			echo ", <b>".$vo->nombre_es."</b>";
					$t++;
				}
				echo "<br>";
			}
			//TITULO DE POBLACION
			$var = 'id_poblacion';
			if (isset($_POST[$var]) || isset($_GET[$var])){
				$id_poblacion = (isset($_POST[$var])) ? $_POST[$var] : $_GET[$var];

				echo "<img src='images/flecha.gif'> Poblaci&oacute;n: ";
				$t = 0;
				foreach($id_poblacion as $id_t){
					$vo  = $poblacion_dao->Get($id_t);
					if ($t == 0)	echo "<b>".$vo->nombre_es."</b>";
					else			echo ", <b>".$vo->nombre_es."</b>";
					$t++;
				}
				echo "<br>";
			}
			//TITULO DE DEPTO
			$var = 'id_depto';
			if (isset($_POST[$var]) || isset($_GET[$var])){
				$id_depto = (isset($_POST[$var])) ? $_POST[$var] : $_GET[$var];

				echo "<img src='images/flecha.gif'> Departamento: ";
				$t = 0;
				foreach($id_depto as $id_t){
					$vo  = $depto_dao->Get($id_t);
					if ($t == 0)	echo "<b>".$vo->nombre."</b>";
					else			echo ", <b>".$vo->nombre."</b>";
					$t++;
				}
				echo "<br>";
			}
			//TITULO DE MPIO
			$var = 'id_muns';
			if (isset($_POST[$var]) || isset($_GET[$var])){
				$id_muns = (isset($_POST[$var])) ? $_POST[$var] : $_GET[$var];

				echo "<img src='images/flecha.gif'> Municipio: ";
				$t = 0;
				foreach($id_muns as $id_t){
					$vo  = $mun_dao->Get($id_t);
					if ($t == 0)	echo "<b>".$vo->nombre."</b>";
					else			echo ", <b>".$vo->nombre."</b>";
					$t++;
				}
				echo "<br>";
			}
			echo "</td></tr>";
		}

		if ($num_arr > 0){
			echo "<tr><td colspan=3><table class='tabla_reportelist' width='790'>";
			echo"<tr class='titulo_lista'>
				<td>Nombre</td>
				<td>Sigla</td>
				<td>Tipo</td>";
			echo "<td align='center' width='150'>Registros: ".$num_arr."</td>
	    		</tr>";

			$p = 0;
			foreach ($arr_id as $id){
				$vo = $this->Get($id);
				$style = "";
				if (fmod($p+1,2) == 0)  $style = "fila_lista";

				//NOMBRE
				if ($vo->nom != ""){

					//NOMBRE DEL TIPO DE ORGANIZACION
					$tipo = $tipo_dao->Get($vo->id_tipo);
					$nom_tipo = $tipo->nombre_es;

					echo "<tr class='".$style."'>";
					echo "<td>".$vo->nom."</td>";
					echo "<td>".$vo->sig."</td>";
					echo "<td>".$nom_tipo."</td>";
					echo "<td align='center'><a href='#' onclick=\"window.open('admin/ver.php?class=OrganizacionDAO&method=Ver&param=".$vo->id."','','top=30,left=30,height=900,width=900,scrollbars=1');return false;\">Detalles</a></td>";
					echo "</tr>";

					$p++;
				}
			}

			echo "<tr><td>&nbsp;</td></tr>";
		}
		else{
			echo "<tr><td align='center'><br><b>NO SE ENCONTRARON ORGANIZACIONES</b></td></tr>";
			echo "<tr><td align='center'><br><a href='".$_SERVER['PHP_SELF']."?m_e=org&accion=consultar&class=OrganizacionDAO'>Regresar</a></td></tr>";
			die;
		}

		//VARIABLE DE SESION QUE SE USA PARA EXPORTAR A EXCEL Y PDF EN EL ARCHIVO EXPORT_DATA.PHP
		$_SESSION["id_orgs"] = $arr_id;

		echo "<input type='hidden' id='id_orgs' name='id_orgs' value='".implode(",",$arr_id)."'>";
		echo "<input type='hidden' id='pdf' name='pdf'>";
		echo "<input type='hidden' id='todas' name='todas' value=$todas>";
		echo "</table>";
		echo "</form>";
	}

	/**
  * Muestára la Informacin completa de una Organización
  * @access public
  * @param id $id Id de la Organizacion
  */
	function Ver($id){

		//INICIALIZACION DE VARIABLES
		$tipo_dao = New TipoOrganizacionDAO();
		$depto_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();
		$region_dao = New RegionDAO();
		$sector_dao = New SectorDAO();
		$enfoque_dao = New EnfoqueDAO();
		$poblacion_dao = New PoblacionDAO();
		$poblado_dao = New PobladoDAO();
		$resguardo_dao = New ResguardoDAO();
		$parque_nat_dao = New ParqueNatDAO();
		$div_afro_dao = New DivAfroDAO();


		//CONSULTA LA INFO DE LA ORG.
		$org = $this->Get($id);

		//SIGLA
		if ($org->sig == "")	$org->sig = "-";

		//DESCRIPCION
		if ($org->des == "")	$org->des = "-";

		//DIR
		if ($org->dir == "")	$org->dir = "-";

		//NACI
		if ($org->naci == 0)	$org->naci = "-";

		//ORG. A LA QUE PERTENECE
		$id_papa = $org->id_papa;
		if ($id_papa != 0 ){
			$org_papa = $this->Get($id_papa);
		}
		else{
			$org_papa->nom = "-";
		}

		//TIPO
		$tipo = $tipo_dao->Get($org->id_tipo);

		//MUN. SEDE
		$mun_sede = "";
		if ($org->id_mun_sede != "")
		$mun_sede = $mun_dao->Get($org->id_mun_sede);

		//N. REP
		if ($org->n_rep == "")	$org->n_rep = "-";

		//T. REP
		if ($org->t_rep == "")	$org->t_rep = "-";

		//Email. REP
		if ($org->email_rep == "")	$org->email_rep = "-";

		//TEL. REP
		if ($org->tel_rep == "")	$org->tel_rep = "-";

		//EMAIL PUBLICO
		if ($org->pu_email == "")	$org->pu_email = "-";

		//WWW
		if ($org->web == ""){
			$org->web = "-";
		}
		else{
			//Elimina el http:// si lo tiene
			$org->web = str_replace("http://","",$org->web);
		}

		//TEL. 1
		if ($org->tel1 == "")	$org->tel1 = "-";

		//TEL. 2
		if ($org->tel2 == "")	$org->tel2 = "-";

		//FAX
		if ($org->fax == "")	$org->fax = "-";

		//NIT
		if ($org->nit == "")	$org->nit = "-";

		//ES DONANTE
		if ($org->dona == 1)	$org->dona = "Si";
		else					$org->dona = "No";

		//INFO. CONFIRMADA
		if ($org->info_confirmada == 1)	$org->info_confirmada = "Si";
		else							$org->info_confirmada = "No";

		//B.D
		if ($org->bd == 1)	$org->bd = "Si";
		else				$org->bd = "No";

		//CONSULTA SOCIAL
		if ($org->consulta_social == 1)	$org->consulta_social = "Si";
		else							$org->consulta_social = "No";

		//CNRR
		if ($org->cnrr == 1)	$org->cnrr = "Si";
		else					$org->cnrr = "No";


		echo "<div align='center'><br><img src='../images/consulta/generar_pdf.gif'>&nbsp;<a href='../export_data.php?pdf=1&case=pdf&nombre_archivo=$org->nom'>Exportar a PDF</a></div>";

		ob_start();

		//ESPACIO DE CORRDINACION
		if ($org->esp_coor == "")	$org->esp_coor = " No ";
		echo "<table align='center' cellspacing=1 cellpadding=3 class='tabla_consulta'>";
		echo "<tr class='titulo_lista'><td align='center' colspan='6'>INFORMACION DE ORGANIZACION</td></tr>";
		echo "<tr><td class='tabla_consulta' width='200'><b>Nombre</b></td><td class='tabla_consulta' colspan='5'>".$org->nom."</td></tr>";
		echo "<tr><td class='tabla_consulta'><b>Sigla</b></td><td class='tabla_consulta'>".$org->sig."</td><td class='tabla_consulta' rowspan='5' colspan='2' align='center'>";
		if ($org->logo != "")	echo "<img src='/sissh/".$org->logo."'>";
		echo "</td></tr>";

		echo "<tr><td class='tabla_consulta'><b>Tipo</b></td><td class='tabla_consulta'>".$tipo->nombre_es."</td></tr>";
		echo "<tr><td class='tabla_consulta'><b>Sede</b></td><td class='tabla_consulta'>".$mun_sede->nombre."</td></tr>";
		if ($_SESSION["id_tipo_usuario_s"] != 20)echo "<tr><td class='tabla_consulta'><b>Direcci&oacute;n</b></td><td class='tabla_consulta'>".$org->dir."</td></tr>";
		echo "<tr><td class='tabla_consulta'><b>A&ntilde;o de fundaci&oacute;n en Colombia</b></td><td class='tabla_consulta'>".$org->naci."</td></tr>";
		echo "<tr><td class='tabla_consulta'><b>Descripci&oacute;n</b></td><td class='tabla_consulta' colspan='3'>".$org->des."</td></tr>";
		if ($_SESSION["id_tipo_usuario_s"] != 20)	echo "<tr><td class='tabla_consulta'><b>Nombre Representante</b></td><td class='tabla_consulta'>".$org->n_rep."</td><td class='tabla_consulta'><b>Ttulo Representante</b></td><td class='tabla_consulta'>".$org->t_rep."</td></tr>";
		if ($_SESSION["id_tipo_usuario_s"] != 20)	echo "<tr><td class='tabla_consulta'><b>Tel&eacute;fono Representante</b></td><td class='tabla_consulta'>".$org->tel_rep."</td><td class='tabla_consulta'><b>Email Representante</b></td><td class='tabla_consulta'>".$org->email_rep."</td></tr>";
		if ($_SESSION["id_tipo_usuario_s"] != 20)	echo "<tr><td class='tabla_consulta'><b>Email</b></td><td class='tabla_consulta'><a href='mailto:".$org->pu_email."'>".$org->pu_email."</a></td></tr>";
		if ($_SESSION["id_tipo_usuario_s"] != 20)	echo "<tr><td class='tabla_consulta'><b>P&aacute;gina Web</b></td><td class='tabla_consulta'><a href='http://".$org->web."' target='_blank'>".$org->web."</a></td></tr>";
		if ($_SESSION["id_tipo_usuario_s"] != 20)	echo "<tr><td class='tabla_consulta'><b>Tel&eacute;fono 1</b></td><td class='tabla_consulta'>".$org->tel1."</td><td class='tabla_consulta'><b>Tel&eacute;fono 2</b></td><td class='tabla_consulta'>".$org->tel2."</td></tr>";
		if ($_SESSION["id_tipo_usuario_s"] != 20)	echo "<tr><td class='tabla_consulta'><b>Fax</b></td><td class='tabla_consulta'>".$org->fax."</td><td class='tabla_consulta'><b>NIT</b></td><td class='tabla_consulta'>".$org->nit."</td></tr>";
		echo "<tr><td class='tabla_consulta'><b>Organizaci&oacute;n a la que pertenece</b></td><td class='tabla_consulta'>".$org_papa->nom."</td><td class='tabla_consulta'><b>Es Donante</b></td><td class='tabla_consulta'>".$org->dona."</td></tr>";
		echo "<tr><td class='tabla_consulta'><b>La Informacin de est&aacute; Organizaci&oacute;n <br> est&aacute; confirmada</b></td><td class='tabla_consulta'>".$org->info_confirmada."</td><td class='tabla_consulta'><b>Crea Base de Datos de<br>Organizaciones</b></td><td class='tabla_consulta'>".$org->bd."</td></tr>";
		echo "<tr><td class='tabla_consulta'><b>Consulta Social</b></td><td class='tabla_consulta'>".$org->consulta_social."</td><td class='tabla_consulta'><b>CNRR</b></td><td class='tabla_consulta'>".$org->cnrr."</td></tr>";
		//SECTOR
		echo "<tr><td class='tabla_consulta'><b>Sector</b></td>";
		echo "<td class='tabla_consulta' colspan='3'>";
		foreach($org->id_sectores as $s=>$id){
			if (fmod($s,8) == 0)	echo "<br>";
			$vo = $sector_dao->Get($id);
			echo "- ".$vo->nombre_es." ";
		}
		//ENFOQUE
		echo "<tr><td class='tabla_consulta'><b>Enfoque</b></td>";
		echo "<td class='tabla_consulta' colspan='3'>";
		$s = 0;
		foreach($org->id_enfoques as $s=>$id){
			if (fmod($s,8) == 0)	echo "<br>";
			$vo = $enfoque_dao->Get($id);
			echo "- ".$vo->nombre_es." ";
			$s++;
		}
		//POBLACION
		echo "<tr><td class='tabla_consulta'><b>Poblaci&oacute;n Sujeto</b></td>";
		echo "<td class='tabla_consulta' colspan='3'>";
		foreach($org->id_poblaciones as $s=>$id){
			if (fmod($s,8) == 0)	echo "<br>";
			$vo = $poblacion_dao->Get($id);
			echo "- ".$vo->nombre_es." ";
		}
		echo "</td></tr>";

		//COBERTURA POR DEPARTAMENTO
		echo "<tr><td class='tabla_consulta'><b>Cobertura Geogr&aacute;fica por Departamento</b></td>";
		echo "<td class='tabla_consulta' colspan='3'>";
		//check para todos, se resta  1, porque existe el depto 00
		if (count($org->id_deptos) == ($depto_dao->numRecords('') - 1))	echo "TODOS";
		else{
			foreach($org->id_deptos as $s=>$id){
				if (fmod($s,8) == 0)	echo "<br>";
				$vo = $depto_dao->Get($id);
				echo "- ".$vo->nombre." ";
			}
		}
		echo "</td></tr>";

		//COBERTURA POR MUNICIPIO
		echo "<tr><td class='tabla_consulta'><b>Cobertura Geogr&aacute;fica por Municipio</b></td>";

		echo "<td class='tabla_consulta' colspan='3'>";
		//echo "<td>".count($org->id_muns)."---".$mun_dao->numRecords('')."</td>";
		//check para todos, se resta  1, porque existe el mpio 00000
		if (count($org->id_muns) == ($mun_dao->numRecords('') - 1))	echo "TODOS";
		else{
			foreach($org->id_muns as $s=>$id){
				if (fmod($s,10) == 0)	echo "<br>";
				$vo = $mun_dao->Get($id);
				echo "- ".$vo->nombre." ";
			}
		}
		echo "</td></tr>";

		//COBERTURA POR REGION
		echo "<tr><td class='tabla_consulta'><b>Cobertura Geogr&aacute;fica por Regi&oacute;n</b></td>";
		echo "<td class='tabla_consulta' colspan='3'>";
		foreach($org->id_regiones as $s=>$id){
			if (fmod($s,10) == 0)	echo "<br>";
			$vo = $region_dao->Get($id);
			echo "- ".$vo->nombre." ";
		}
		echo "</td></tr>";

		//COBERTURA POR POBLADO
		if (count($org->id_poblados) > 0){
			echo "<tr><td class='tabla_consulta'><b>Cobertura Geogr&aacute;fica por Poblado</b></td>";
			echo "<td class='tabla_consulta' colspan='3'>";
			foreach($org->id_poblados as $s=>$id){
				if (fmod($s,10) == 0)	echo "<br>";
				$vo = $poblado_dao->Get($id);
				echo "- ".$vo->nombre." ";
			}
			echo "</td></tr>";
		}

		//COBERTURA POR PARQUE NAT.
		if (count($org->id_parques) > 0){
			echo "<tr><td class='tabla_consulta'><b>Cobertura Geogr&aacute;fica por Parque Natural</b></td>";
			echo "<td class='tabla_consulta' colspan='3'>";
			foreach($org->id_parques as $s=>$id){
				if (fmod($s,10) == 0)	echo "<br>";
				$vo = $parque_nat_dao->Get($id);
				echo "- ".$vo->nombre." ";
			}
			echo "</td></tr>";
		}

		//COBERTURA POR RESGUARDO
		if (count($org->id_resguardos) > 0){
			echo "<tr><td class='tabla_consulta'><b>Cobertura Geogr&aacute;fica por Resguardo</b></td>";
			echo "<td class='tabla_consulta' colspan='3'>";
			foreach($org->id_resguardos as $s=>$id){
				if (fmod($s,10) == 0)	echo "<br>";
				$vo = $resguardo_dao->Get($id);
				echo "- ".$vo->nombre." ";
			}
			echo "</td></tr>";
		}

		//COBERTURA POR DIV. AFRO
		if (count($org->id_divisiones_afro) > 0){
			echo "<tr><td class='tabla_consulta'><b>Cobertura Geogr&aacute;fica por Divisin Afro</b></td>";
			echo "<td class='tabla_consulta' colspan='3'>";
			foreach($org->id_divisiones_afro as $s=>$id){
				if (fmod($s,10) == 0)	echo "<br>";
				$vo = $div_afro_dao->Get($id);
				echo "- ".$vo->nombre." ";
			}
			echo "</td></tr>";
		}

		//ESPACIO DE COORDINACION
		echo "<tr><td class='tabla_consulta'><b>Participa en alg&uacute;n espacio de coordinaci&oacute;n?</b></td><td class='tabla_consulta'>".$org->esp_coor."</td>";

		//DONANTES
		echo "<tr><td class='tabla_consulta'><b>Donantes<br>( Orgs. de las que recibe recursos )</b></td>";
		echo "<td class='tabla_consulta' colspan='3'>";
		foreach($org->id_donantes as $s=>$id){
			if (fmod($s,10) == 0)	echo "<br>";
			$vo = $this->Get($id);
			echo "- ".$vo->nom." ";
		}
		echo "</td></tr>";
		echo "</table>";

		$_SESSION["pdf_code"] = ob_get_contents();

		ob_end_flush();

	}

	/**
  * Imprime en pantalla los datos del Organizacion
  * @access public
  * @param object $vo Organizacion que se va a imprimir
  * @param string $formato Formato en el que se listarn los Organizacion, puede ser Tabla o ComboSelect
	* @param int $valor_combo ID del Organizacion que ser selccionado cuando el formato es ComboSelect
  */
	function Imprimir($vo,$formato,$valor_combo){

		if ($formato == 'combo'){
			if ($valor_combo == "" && $valor_combo != 0)
			echo "<option value=".$vo->id.">".$vo->nombre."</option>";
			else{
				echo "<option value=".$vo->id;
				if ($valor_combo == $vo->id)
				echo " selected ";
				echo ">".$vo->nombre."</option>";
			}
		}
	}

	/**
	* Carga un VO de Organizacion con los datos de la consulta
	* @access public
	* @param object $vo VO de Organizacion que se va a recibir los datos
	* @param object $Resultset Resource de la consulta
	* @return object $vo VO de Organizacion con los datos
	*/
	function GetFromResult ($vo,$Result){

		//
		//

		$id = $Result->ID_ORG;
		$nombre = $Result->NOM_ORG;

		$vo->id = $Result->{$this->columna_id};
		$vo->nom = $nombre;

		$vo->id_tipo = $Result->ID_TIPO;
		$vo->id_mun_sede = $Result->ID_MUN_SEDE;
        $vo->pais_ciudad = $Result->PAIS_CIUDAD;
		$vo->id_papa = $Result->ID_ORG_PAPA;
		$vo->sig = $Result->SIG_ORG;
		$vo->des = $Result->DES_ORG;
		$vo->naci = $Result->NACI_ORG;
		$vo->view = $Result->VIEW_ORG;
		$vo->bd = $Result->BD_ORG;
		$vo->dir = $Result->DIR_ORG;
		$vo->tel1 = $Result->TEL1_ORG;
		$vo->tel2 = $Result->TEL2_ORG;
		$vo->fax = $Result->FAX_ORG;
		$vo->un_email = $Result->UN_MAIL_ORG;
		$vo->pu_email = $Result->PU_MAIL_ORG;
		$vo->web = $Result->WEB_ORG;
		$vo->logo = str_replace("\\","/",$Result->LOGO_ORG);
		$vo->dona = $Result->DONA_ORG;
		$vo->n_rep = $Result->N_REP_ORG;
		$vo->t_rep = $Result->T_REP_ORG;
		$vo->tel_rep = $Result->TEL_REP_ORG;
		$vo->email_rep = $Result->EMAIL_REP_ORG;
		$vo->info_confirmada = $Result->INFO_CONFIRMADA;
		$vo->nit = $Result->NIT_ORG;
		$vo->esp_coor = $Result->ESP_COOR_ORG;
		$vo->consulta_social = $Result->CONSULTA_SOCIAL;
		$vo->cnrr = $Result->CNRR;
		$vo->fecha_update = $Result->FECHA_UPDATE;

		//POBLACIONES
		$arr = Array();
		$sql_s = "SELECT ID_POB FROM poblacion_org WHERE ID_ORG = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			$arr[] = $row_rs_s[0];
		}
		$vo->id_poblaciones = $arr;

		//SECTORES
		$arr = Array();
		$sql_s = "SELECT ID_COMP FROM sector_org WHERE ID_ORG = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			$arr[] = $row_rs_s[0];
		}
		$vo->id_sectores = $arr;

		//ENFOQUES
		$arr = Array();
		$sql_s = "SELECT ID_ENF FROM enf_org WHERE ID_ORG = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			$arr[] = $row_rs_s[0];
		}
		$vo->id_enfoques = $arr;

		//DONANTES
		$arr = Array();
		$sql_s = "SELECT ID_DONA FROM org_donan WHERE ID_ORG = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			$arr[] = $row_rs_s[0];
		}
		$vo->id_donantes = $arr;

		//DEPARTAMENTOS
		$arr = Array();
		$sql_s = "SELECT ID_DEPTO FROM depto_org WHERE ID_ORG = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			$arr[] = $row_rs_s[0];
		}
		$vo->id_deptos = $arr;

		//MPIOS
		$arr = Array();
		$sql_s = "SELECT ID_MUN FROM mpio_org WHERE ID_ORG = ".$id." AND COBERTURA = 1";
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			if ($row_rs_s[0] != ""){
				$arr[] = $row_rs_s[0];
			}
		}
		$vo->id_muns = $arr;

		//REGIONES
		$arr = Array();
		$sql_s = "SELECT ID_REG FROM reg_org WHERE ID_ORG = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			$arr[] = $row_rs_s[0];
		}
		$vo->id_regiones = $arr;

		//POBLADOS
		$arr = Array();
		$sql_s = "SELECT ID_POB FROM poblado_org WHERE ID_ORG = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			$arr[] = $row_rs_s[0];
		}
		$vo->id_poblados = $arr;

		//RESGUARDOS
		$arr = Array();
		$sql_s = "SELECT ID_RESGUADRO FROM resg_org WHERE ID_ORG = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			$arr[] = $row_rs_s[0];
		}
		$vo->id_resguardos = $arr;

		//PARQUES
		$arr = Array();
		$sql_s = "SELECT ID_PAR_NAT FROM par_nat_org WHERE ID_ORG = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			$arr[] = $row_rs_s[0];
		}
		$vo->id_parques = $arr;

		//DIV. AFRO
		$arr = Array();
		$sql_s = "SELECT ID_DIV_AFRO FROM div_afro_org WHERE ID_ORG = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			$arr[] = $row_rs_s[0];
		}
		$vo->id_divisiones_afro = $arr;

		if (isset($_SESSION["mapp_oea"]) && $_SESSION["mapp_oea"] == 1){

			//ORGS CONOCE
			$arr = Array();
			$sql_s = "SELECT * FROM org_trabaja_conoce WHERE ID_ORG = $id AND tipo_rel = 1 ORDER BY ID_TC";
			$rs_s = $this->conn->OpenRecordset($sql_s);
			while ($row_rs_s = $this->conn->FetchObject($rs_s)){
				$vo->org_conoce_nombre[] = $row_rs_s->NOMBRE;
				$vo->org_conoce_email[] = $row_rs_s->EMAIL;
				$vo->org_conoce_tel[] = $row_rs_s->TEL;
			}

			//ORGS TRABAJA
			$arr = Array();
			$sql_s = "SELECT * FROM org_trabaja_conoce WHERE ID_ORG = $id AND tipo_rel = 2 ORDER BY ID_TC";
			$rs_s = $this->conn->OpenRecordset($sql_s);
			while ($row_rs_s = $this->conn->FetchObject($rs_s)){
				$vo->org_trabaja_nombre[] = $row_rs_s->NOMBRE;
				$vo->org_trabaja_email[] = $row_rs_s->EMAIL;
				$vo->org_trabaja_tel[] = $row_rs_s->TEL;
			}
		}

		return $vo;

	}

	/*
	 * Consulta si la organización esta asociada a otro modulo, para permitir eliminarla
	 * @access public
	 * @param int $id_org ID de la organizacion
	 * @return array $info Arreglo con la informacion asociada, vacio si no tiene
	 */
	function checkBorrar($id_org){

		$info = array();

		//DONANTES EN ORGS
		$sql_s = "SELECT $this->columna_id FROM org_donan WHERE ID_DONA = ".$id_org;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			$info['org'] = $row_rs_s[0];
		}

		//INFO EN PROYECTOS
		$sql = "SELECT ID_PROY as id_p,ID_TIPO_VINORGPRO as id_tipo FROM vinculorgpro WHERE $this->columna_id = $id_org";
		$rs = $this->conn->OpenRecordset($sql);
		while ($row = $this->conn->FetchObject($rs)){
			$info['proyecto'][$row->id_tipo] = $row->id_p;
		}

		return $info;

	}

	/**
	 * Inserta un Organizacion en la B.D.
	 * @access public
	 * @param object $organizacion_vo VO de Organizacion que se va a insertar
	 * @param int $alert Mostrar alerta Javascript 1 o 0
	 * @param string $db_name Base de datos donde se va a insertar la info
	 */
	function Insertar($organizacion_vo,$alert=1,$db_name=''){

		if ($db_name != '')	mysql_select_db($db_name);

		//CONSULTA SI YA EXISTE
		$a = $this->GetAllArray($this->columna_nombre." = '".$organizacion_vo->nom."' AND SIG_ORG = '".$organizacion_vo->sig."'",'','');
		if (count($a) == 0){

            $sql =  "INSERT INTO ".$this->tabla."
                (NOM_ORG,SIG_ORG,DES_ORG,VIEW_ORG,NACI_ORG,NIT_ORG,ID_ORG_PAPA,ID_TIPO,BD_ORG,
                ID_MUN_SEDE,PAIS_CIUDAD,DONA_ORG,T_REP_ORG,DIR_ORG,PU_MAIL_ORG,UN_MAIL_ORG,WEB_ORG,TEL1_ORG,TEL2_ORG,
                FAX_ORG,LOGO_ORG,TEL_REP_ORG,EMAIL_REP_ORG,INFO_CONFIRMADA,N_REP_ORG,ESP_COOR_ORG,CONSULTA_SOCIAL,
                CNRR,CAPTURA_LOCAL)
                VALUES ('".$organizacion_vo->nom."','".$organizacion_vo->sig."','".$organizacion_vo->des."',".
                $organizacion_vo->view.",".$organizacion_vo->naci.",'".$organizacion_vo->nit."',".$organizacion_vo->id_papa.",".
                $organizacion_vo->id_tipo.",".$organizacion_vo->bd.",'".$organizacion_vo->id_mun_sede."','".$organizacion_vo->pais_ciudad."',".
                $organizacion_vo->dona.",'".  $organizacion_vo->t_rep."','".$organizacion_vo->dir."','".$organizacion_vo->pu_email."','".$organizacion_vo->un_email."','".
                $organizacion_vo->web."','".$organizacion_vo->tel1."','".$organizacion_vo->tel2."','".$organizacion_vo->fax."','".
                $organizacion_vo->logo."','".$organizacion_vo->tel_rep."','".$organizacion_vo->email_rep."',".$organizacion_vo->info_confirmada.",'".
                $organizacion_vo->n_rep."','".$organizacion_vo->esp_coor."',".$organizacion_vo->consulta_social.",".$organizacion_vo->cnrr.",".
                $this->captura_local.")";

			$this->conn->Execute($sql,$db_name);
			$id_organizacion = $this->conn->GetGeneratedID();

			$this->InsertarTablasUnion($organizacion_vo,$id_organizacion,$db_name);

			if ($alert == 1){
		    	?>
		    	<script>
		    	alert("Organizaci\xf3n insertada con \xe9xito, el siguiente paso es definir la cobertura Geogr\xe1fica");
				</script>
		    	<?
			}
		}
		else{
	    	?>
	    	<script>
	    	alert("Error - Existe una Organizaci\xf3n con el mismo nombre o sigla");
	    	location.href = '<?=$this->url;?>';
	    	</script>
	    	<?
		}
	}


	/**
	 * Registro externo de una Organizacion en la B.D.
	 * @access public
	 * @param object $organizacion_vo VO de Organizacion que se va a registrar
	 * @param object $organizacion_vo_registro VO de Registro de Organizacion que se va a registrar
	 */
	function Registrar($organizacion_vo,$organizacion_vo_registro){

		$sql =  "INSERT INTO organizacion_registro (NOM_ORG,SIG_ORG,DES_ORG,NACI_ORG,NIT_ORG,ID_TIPO,ID_MUN_SEDE,T_REP_ORG,DIR_ORG,PU_MAIL_ORG,WEB_ORG,TEL1_ORG,TEL2_ORG,FAX_ORG,N_REP_ORG,ESP_COOR_ORG,NOM_INGRESA,TEL_INGRESA,EMAIL_INGRESA,CNRR)";
		$sql .= " VALUES ('".$organizacion_vo->nom."','".$organizacion_vo->sig."','".$organizacion_vo->des."',".$organizacion_vo->naci.",'".$organizacion_vo->nit."',".$organizacion_vo->id_tipo.",'".$organizacion_vo->id_mun_sede."','".$organizacion_vo->t_rep."','".$organizacion_vo->dir."','".$organizacion_vo->pu_email."','".$organizacion_vo->web."','".$organizacion_vo->tel1."','".$organizacion_vo->tel2."','".$organizacion_vo->fax."','".$organizacion_vo->n_rep."','".$organizacion_vo->esp_coor."','".$organizacion_vo_registro->ingresa_nombre."','".$organizacion_vo_registro->ingresa_tel."','".$organizacion_vo_registro->ingresa_email."',".$organizacion_vo->cnrr.")";

		//		echo $sql;

		//		die($sql);

		$this->conn->Execute($sql);
		$id_organizacion = $this->conn->GetGeneratedID();

		//POBLACION
		$arr = $organizacion_vo->id_poblaciones;
		$num_arr = count($arr);

		for($m=0;$m<$num_arr;$m++){
			$sql = "INSERT INTO poblacion_org_registro (ID_POB,ID_ORG) VALUES (".$arr[$m].",".$id_organizacion.")";
			$this->conn->Execute($sql);
			//echo $sql;
		}

		//SECTOR
		$arr = $organizacion_vo->id_sectores;
		$num_arr = count($arr);

		for($m=0;$m<$num_arr;$m++){
			$sql = "INSERT INTO sector_org_registro (ID_COMP,ID_ORG) VALUES (".$arr[$m].",".$id_organizacion.")";
			$this->conn->Execute($sql);
			//echo $sql;
		}

		//ENFOQUE
		$arr = $organizacion_vo->id_enfoques;
		$num_arr = count($arr);

		for($m=0;$m<$num_arr;$m++){
			$sql = "INSERT INTO enf_org_registro (ID_ENF,ID_ORG) VALUES (".$arr[$m].",".$id_organizacion.")";
			$this->conn->Execute($sql);
			//echo $sql;
		}

		//DEPTOS COBERTURA
		$arr = $organizacion_vo->id_deptos;
		$num_arr = count($arr);

		for($m=0;$m<$num_arr;$m++){
			if ($arr[$m] != ""){
				$sql = "INSERT INTO depto_org_registro (ID_DEPTO,ID_ORG) VALUES ('".$arr[$m]."',".$id_organizacion.")";
				$this->conn->Execute($sql);
			}
			//echo $sql;
		}

		//MUNICIPIOS
		$arr = $organizacion_vo->id_muns;
		$num_arr = count($arr);

		for($m=0;$m<$num_arr;$m++){
			if ($arr[$m] != ""){
				$sql = "INSERT INTO mpio_org_registro (ID_MUN,ID_ORG) VALUES ('".$arr[$m]."',".$id_organizacion.")";
				$this->conn->Execute($sql);
			}
			//echo $sql;
		}

		//DONANTES COMO TEXTO
		$arr = $organizacion_vo_registro->donantes;
		foreach ($arr as $var){
			if ($var != ""){
				$sql = "INSERT INTO org_registro_donante (DONANTE,ID_ORG) VALUES ('".$var."',".$id_organizacion.")";
				$this->conn->Execute($sql);
			}
			//echo $sql;
		}

		//ORG. POBLACION VULNERABLE NOMBRE-TEL-EMAIL
		$arr = $organizacion_vo_registro->pob_vul_nombre;
		$c = 0;
		foreach ($arr as $var){

			$email = $organizacion_vo_registro->pob_vul_email[$c];
			$tel = $organizacion_vo_registro->pob_vul_tel[$c];

			if ($var != ""){
				$sql = "INSERT INTO org_registro_pob_vul (NOMBRE,EMAIL,TEL,ID_ORG) VALUES ('$var','$email','$tel',$id_organizacion)";
				$this->conn->Execute($sql);
			}
			//echo $sql;
		}

		//ENVIA EMAIL
		$from = "rojas@un-ocha.org";

		require($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/common/class.phpmailer.php");

		$mail = new PHPMailer();

		$mail->IsSMTP(); // set mailer to use SMTP

		$mail->From = $from;
		$mail->FromName = "SIDIH OCHA";
		$mail->AddAddress("zhang17@un.org", "Xitong Zhang");
        $mail->AddCC("villaveces@un.org", "Jeffrey Villaveces");
		$mail->AddBCC("rubenrojasc@gmail.com", "Ruben Rojas");

		$mail->IsHTML(true);                                  // set email format to HTML

		$mail->Subject = "Nueva Org. registrada en SI OCHA";
		$mail->Body    = "Se ha registrado la organizaci&oacute;n: <b>$organizacion_vo->nom</b>, la organizaci&oacute;n queda pendiente de publicaci&oacute;n";
		//$mail->AltBody = "This is the body in plain text for non-HTML mail clients";

		$mail->Send();

	}

	/**
	 * Lock una org para edicion, caso muchos alimentadores mapp/oea
	 * @access public
	 * @param int $id ID de Organizacion
	 * @param int $lock Valor del lock 1,0
	 */
	function lockOrgMO($id,$lock=1){
		$sql = "UPDATE organizacion SET locked = $lock WHERE id_org = $id";
		$this->conn->Execute($sql);
	}


	/**
	 * Inserta una org mapp-oea
	 * @access public
	 * @param object $organizacion_vo VO de Organizacion que se va a registrar
	 */
	function InsertarOrgMO($organizacion_vo,$organizacion_mo){

		$l = strtoupper(substr($organizacion_vo->nom,0,1));
		$a = $this->GetAllArray($this->columna_nombre." = '".$organizacion_vo->nom."' AND SIG_ORG = '".$organizacion_vo->sig."'",'','');

		if (count($a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (NOM_ORG,SIG_ORG,DES_ORG,VIEW_ORG,NACI_ORG,NIT_ORG,ID_ORG_PAPA,ID_TIPO,BD_ORG,ID_MUN_SEDE,DONA_ORG,T_REP_ORG,DIR_ORG,PU_MAIL_ORG,UN_MAIL_ORG,WEB_ORG,TEL1_ORG,TEL2_ORG,FAX_ORG,LOGO_ORG,TEL_REP_ORG,EMAIL_REP_ORG,INFO_CONFIRMADA,N_REP_ORG,ESP_COOR_ORG,CONSULTA_SOCIAL,CNRR,CAPTURA_LOCAL,MAPP_OEA)";
			$sql .= " VALUES ('".$organizacion_vo->nom."','".$organizacion_vo->sig."','".$organizacion_vo->des."',".$organizacion_vo->view.",".$organizacion_vo->naci.",'".$organizacion_vo->nit."',".$organizacion_vo->id_papa.",".$organizacion_vo->id_tipo.",".$organizacion_vo->bd.",'".$organizacion_vo->id_mun_sede."',".$organizacion_vo->dona.",'".$organizacion_vo->t_rep."','".$organizacion_vo->dir."','".$organizacion_vo->pu_email."','".$organizacion_vo->un_email."','".$organizacion_vo->web."','".$organizacion_vo->tel1."','".$organizacion_vo->tel2."','".$organizacion_vo->fax."','".$organizacion_vo->logo."','".$organizacion_vo->tel_rep."','".$organizacion_vo->email_rep."',".$organizacion_vo->info_confirmada.",'".$organizacion_vo->n_rep."','".$organizacion_vo->esp_coor."',".$organizacion_vo->consulta_social.",".$organizacion_vo->cnrr.",".$this->captura_local.",1)";

			$this->conn->Execute($sql);
			$id_organizacion = $this->conn->GetGeneratedID();

			$this->InsertarTablasUnionOrgMO($organizacion_vo,$organizacion_mo,$id_organizacion);

			//LOG
			$log = New LogUsuarioDAO();
			$log->RegistrarAdmin($id_organizacion);

			?>
			<script>
			location.href='../index_mo.php?m_e=home&accion=insertar&l=<?=$l?>';
			</script>
			<?
		}
		else{
			//muestra la lista de ocurrencias
			?>
			<script>
			location.href='../index_mo.php?m_e=home&accion=insertar_error&l=<?=$l?>';
			</script>
			<?
		}
		//		echo $sql;

	}

	/**
	 * Inserta una org desde 4w
	 * @access public
	 * @param object $organizacion_vo VO de Organizacion que se va a registrar
	 */
	function InsertarOrg4w($o_vo){

		$a = $this->GetAllArray($this->columna_nombre." = '".$o_vo->nom."' AND SIG_ORG = '".$o_vo->sig."'",'','');

		if (count($a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (NOM_ORG,SIG_ORG,ID_ORG_PAPA,ID_TIPO,ID_MUN_SEDE,DIR_ORG,PU_MAIL_ORG,TEL1_ORG,INFO_CONFIRMADA,SI_ORG)";
			$sql .= " VALUES ('".$o_vo->nom."','".$o_vo->sig."',0,".$o_vo->id_tipo.",'".$o_vo->id_mun_sede."','".$o_vo->dir."','".$o_vo->pu_email."','".$o_vo->tel1."',0,'4w')";

			$this->conn->Execute($sql);
			$id_organizacion = $this->conn->GetGeneratedID();

			//LOG
			$log = New LogUsuarioDAO();
			$log->RegistrarAdmin($id_organizacion);

            $ht = '<h1>Organizaci&oacute;n registrada con &eacute;xito</h1><br />
                    Debe volver a buscarla en el formulario del proyecto';

		}
		else{
            $ht = '<h1>ERROR.... A pesar de las advertencias, est&aacute; intentando crear un Organizaci&oacute;n
                    que ya existe!!!</h1>
                    <br />
                    No olvide que puede buscar por Nombre o Sigla';
		}

        echo "<div class='alert'>$ht</div>";

	}

    /**
	 * Inserta las tablas de union para el Organizacion en la B.D.
	 * @access public
	 * @param object $organizacion_vo VO de Organizacion que se va a insertar
	 * @param int $id_organizacion ID de la Organizacion que se acaba de insertar
	 */
	function InsertarTablasUnion($organizacion_vo,$id_organizacion,$db_name=''){

		if ($id_organizacion > 0){
			//POBLACION
			$arr = $organizacion_vo->id_poblaciones;
			$num_arr = count($arr);

			for($m=0;$m<$num_arr;$m++){
				$sql = "INSERT INTO poblacion_org (ID_POB,ID_ORG) VALUES (".$arr[$m].",".$id_organizacion.")";
				$this->conn->Execute($sql,$db_name);
				//echo $sql;
			}

			//SECTOR
			$arr = $organizacion_vo->id_sectores;
			$num_arr = count($arr);

			for($m=0;$m<$num_arr;$m++){
				$sql = "INSERT INTO sector_org (ID_COMP,ID_ORG) VALUES (".$arr[$m].",".$id_organizacion.")";
				$this->conn->Execute($sql,$db_name);
			}

			//ENFOQUE
			$arr = $organizacion_vo->id_enfoques;
			$num_arr = count($arr);

			for($m=0;$m<$num_arr;$m++){
				$sql = "INSERT INTO enf_org (ID_ENF,ID_ORG) VALUES (".$arr[$m].",".$id_organizacion.")";
				$this->conn->Execute($sql,$db_name);
				//echo $sql;
			}

			//DONANTES
			$arr = $organizacion_vo->id_donantes;
			$num_arr = count($arr);

			for($m=0;$m<$num_arr;$m++){
				$sql = "INSERT INTO org_donan (ID_DONA,ID_ORG) VALUES (".$arr[$m].",".$id_organizacion.")";
				$this->conn->Execute($sql,$db_name);
				//echo $sql;
			}
		}
	}

	/**
  * Inserta las tablas de union de cobertura de la Organizacion en la B.D.
  * @access public
  * @param object $organizacion_vo VO de Organizacion que se va a insertar
  * @param int $id_organizacion ID de la Organizacion que se acaba de insertar
  */
	function InsertarTablasUnionCobertura($organizacion_vo,$id_organizacion,$opcion,$db_name=''){

		if ($opcion == 1 || $opcion == 3){
			//DEPTOS
			$arr = $organizacion_vo->id_deptos;
			$num_arr = count($arr);

			for($m=0;$m<$num_arr;$m++){
				$sql = "INSERT INTO depto_org (ID_DEPTO,ID_ORG) VALUES ('".$arr[$m]."',".$id_organizacion.")";
				$this->conn->Execute($sql,$db_name);
				//echo $sql;
			}

			//MUNICPIOS
			$arr = $organizacion_vo->id_muns;
			$num_arr = count($arr);

			for($m=0;$m<$num_arr;$m++){
				if ($arr[$m] != ""){
					$sql = "INSERT INTO mpio_org (ID_MUN,ID_ORG) VALUES ('".$arr[$m]."',".$id_organizacion.")";
					$this->conn->Execute($sql,$db_name);
				}
				//echo $sql;
			}
		}
		else if ($opcion == 2 || $opcion == 5){

			//REGIONES
			$arr = $organizacion_vo->id_regiones;
			$num_arr = count($arr);


			//CONSULTA LOS MUNICIPIOS DONDE TIENE COBERTURA
			$sql = "SELECT ID_MUN FROM mpio_org WHERE ID_ORG = ".$id_organizacion;
			$rs = $this->conn->OpenRecordset($sql);
			$id_muns_cob = Array();
			while ($row_rs = $this->conn->FetchRow($rs)){
				array_push($id_muns_cob,$row_rs[0]);
			}

			for($m=0;$m<$num_arr;$m++){
				$sql = "INSERT INTO reg_org (ID_REG,ID_ORG) VALUES (".$arr[$m].",".$id_organizacion.")";
				$this->conn->Execute($sql,$db_name);

				//CONSULTA LOS MUNICIPIOS DE LA REGION
				$sql = "SELECT ID_MUN FROM mun_reg WHERE ID_REG = ".$arr[$m];
				$rs = $this->conn->OpenRecordset($sql);
				while ($row_rs = $this->conn->FetchRow($rs)){
					if (!in_array($row_rs[0],$id_muns_cob)){
						$sql_i = "INSERT INTO mpio_org (ID_MUN,ID_ORG,COBERTURA) VALUES ('".$row_rs[0]."',".$id_organizacion.",0)";
						$this->conn->Execute($sql_i,$db_name);
						//echo $sql_i;
					}
				}
			}

			//POBLADOS
			$arr = $organizacion_vo->id_poblados;
			$num_arr = count($arr);

			for($m=0;$m<$num_arr;$m++){
				$sql = "INSERT INTO poblado_org (ID_POB,ID_ORG) VALUES ('".$arr[$m]."',".$id_organizacion.")";
				$this->conn->Execute($sql,$db_name);
				//echo $sql;
			}
		}
		else if ($opcion == 4){

			//RESGUARDOS
			$arr = $organizacion_vo->id_resguardos;
			$num_arr = count($arr);

			for($m=0;$m<$num_arr;$m++){
				$sql = "INSERT INTO resg_org (ID_RESGUADRO,ID_ORG) VALUES (".$arr[$m].",".$id_organizacion.")";
				$this->conn->Execute($sql,$db_name);
				//echo $sql;
			}

			//PARQUES
			$arr = $organizacion_vo->id_parques;
			$num_arr = count($arr);

			for($m=0;$m<$num_arr;$m++){
				$sql = "INSERT INTO par_nat_org (ID_PAR_NAT,ID_ORG) VALUES (".$arr[$m].",".$id_organizacion.")";
				$this->conn->Execute($sql,$db_name);
				//echo $sql;
			}

			//DIV. AFRO
			$arr = $organizacion_vo->id_divisiones_afro;
			$num_arr = count($arr);

			for($m=0;$m<$num_arr;$m++){
				$sql = "INSERT INTO div_afro_org (ID_DIV_AFRO,ID_ORG) VALUES (".$arr[$m].",".$id_organizacion.")";
				$this->conn->Execute($sql,$db_name);
				//echo $sql;
			}
		}

	}

	/**
	 * Inserta las tablas de union para LA Organizacion mapp-oea en la B.D.
	 * @access public
	 * @param object $organizacion_vo VO de Organizacion que se va a insertar
	 * @param object $organizacion_mo VO de OrganizacionMO que se va a insertar
	 * @param int $id_organizacion ID de la Organizacion que se acaba de insertar
	 */
	function InsertarTablasUnionOrgMO($organizacion_vo,$organizacion_mo,$id_organizacion){

		if ($id_organizacion > 0){

			//DEPTOS
			$arr = $organizacion_vo->id_deptos;
			foreach($arr as $a){
				$sql = "INSERT INTO depto_org (ID_DEPTO,ID_ORG) VALUES ('$a',$id_organizacion)";
				$this->conn->Execute($sql);
				//echo $sql;
			}

			//MUNICPIOS
			$arr = $organizacion_vo->id_muns;
			foreach($arr as $a){
				if ($a != ""){
					$sql = "INSERT INTO mpio_org (ID_MUN,ID_ORG) VALUES ('$a',$id_organizacion)";
					$this->conn->Execute($sql);
				}
				//echo $sql;
			}

			//POBLADOS
			$arr = $organizacion_vo->id_poblados;
			foreach($arr as $a){
				$sql = "INSERT INTO poblado_org (ID_POB,ID_ORG) VALUES ('$a',$id_organizacion)";
				$this->conn->Execute($sql);
				//echo $sql;
			}

			//POBLACION
			$arr = $organizacion_vo->id_poblaciones;
			foreach($arr as $a){
				$sql = "INSERT INTO poblacion_org (ID_POB,ID_ORG) VALUES ('$a',$id_organizacion)";
				$this->conn->Execute($sql);
				//echo $sql;
			}

			//SECTOR
			$arr = $organizacion_vo->id_sectores;
			$num_arr = count($arr);

			foreach($arr as $a){
				$sql = "INSERT INTO sector_org (ID_COMP,ID_ORG) VALUES (".$a.",".$id_organizacion.")";
				$this->conn->Execute($sql);
				//echo $sql;
			}

			//ORG que conoce
			//Tipo=1 en la tabla org_trabaja_conoce
			$tipo = 1;
			$arr = $organizacion_mo->org_conoce_nombre;
			$c = 0;
			foreach ($arr as $var){
				$email = isset($organizacion_mo->org_conoce_email[$c]) ? $organizacion_mo->org_conoce_email[$c] : '';
				$tel = isset($organizacion_mo->org_conoce_tel[$c]) ? $organizacion_mo->org_conoce_tel[$c] : '';

				if ($var != ""){
					$sql = "INSERT INTO org_trabaja_conoce (NOMBRE,EMAIL,TEL,ID_ORG,TIPO_REL) VALUES ('$var','$email','$tel',$id_organizacion,$tipo)";
					$this->conn->Execute($sql);
				}
				//echo $sql;
				$c++;
			}

			//ORG con las que trabaja
			//Tipo=2 en la tabla org_trabaja_conoce
			$tipo = 2;
			$arr = $organizacion_mo->org_trabaja_nombre;
			$c = 0;
			foreach ($arr as $var){
				$email = isset($organizacion_mo->org_trabaja_email[$c]) ? $organizacion_mo->org_trabaja_email[$c] : '';
				$tel = isset($organizacion_mo->org_trabaja_tel[$c]) ? $organizacion_mo->org_trabaja_tel[$c] : '';

				if ($var != ""){
					$sql = "INSERT INTO org_trabaja_conoce (NOMBRE,EMAIL,TEL,ID_ORG,TIPO_REL) VALUES ('$var','$email','$tel',$id_organizacion,$tipo)";
					$this->conn->Execute($sql);
				}
				//echo $sql;
				$c++;
			}
		}
	}

	/**
	 * Actualiza un Organizacion en la B.D.
	 * @access public
	 * @param object $organizacion_vo VO de Organizacion que se va a actualizar
	 */
	function Actualizar($organizacion_vo,$alert=1){
		$sql =  "UPDATE ".$this->tabla." SET
		 NOM_ORG = '".$organizacion_vo->nom."',
		 SIG_ORG = '".$organizacion_vo->sig."',
		 DES_ORG = '".$organizacion_vo->des."',
		 VIEW_ORG = ".$organizacion_vo->view.",
		 NACI_ORG = ".$organizacion_vo->naci.",
		 NIT_ORG = '".$organizacion_vo->nit."',
		 ID_ORG_PAPA = ".$organizacion_vo->id_papa.",
		 ID_TIPO = ".$organizacion_vo->id_tipo.",
		 BD_ORG = ".$organizacion_vo->bd.",
		 ID_MUN_SEDE = '".$organizacion_vo->id_mun_sede."',
         PAIS_CIUDAD = '".$organizacion_vo->pais_ciudad."',
		 DONA_ORG = ".$organizacion_vo->dona.",
		 T_REP_ORG = '".$organizacion_vo->t_rep."',
		 DIR_ORG = '".$organizacion_vo->dir."',
		 PU_MAIL_ORG = '".$organizacion_vo->pu_email."',
		 UN_MAIL_ORG = '".$organizacion_vo->un_email."',
		 WEB_ORG = '".$organizacion_vo->web."',
		 TEL1_ORG = '".$organizacion_vo->tel1."',
		 TEL2_ORG = '".$organizacion_vo->tel2."',
		 FAX_ORG = '".$organizacion_vo->fax."',
		 LOGO_ORG = '".$organizacion_vo->logo."',
		 TEL_REP_ORG = '".$organizacion_vo->tel_rep."',
		 EMAIL_REP_ORG = '".$organizacion_vo->email_rep."',
		 N_REP_ORG = '".$organizacion_vo->n_rep."',
		 ESP_COOR_ORG = '".$organizacion_vo->esp_coor."',
		 INFO_CONFIRMADA = ".$organizacion_vo->info_confirmada.",
		 CONSULTA_SOCIAL = ".$organizacion_vo->consulta_social.",
		 CNRR = ".$organizacion_vo->cnrr.",
		 FECHA_UPDATE = now()

		 WHERE ".$this->columna_id." = ".$organizacion_vo->id;

		$this->conn->Execute($sql);

		$this->BorrarTablasUnion($organizacion_vo->id);

		$this->InsertarTablasUnion($organizacion_vo,$organizacion_vo->id);

		if ($alert == 1){
			?>
		  	<script>
		  	alert("Organizaci\xf3n actualizada con \xe9xito, el siguiente paso es actualizar la cobertura Geogr\xe1fica");
		  	</script>
		  	<?
		}
	}

	/**
	* Actualiza la cobertura geogrfica de una Organizacion en la B.D.
	* @access public
	* @param object $organizacion_vo VO de Organizacion que se va a actualizar
	*/
	function ActualizarCobertura($organizacion_vo,$paso){

		$this->BorrarTablasUnionCobertura($organizacion_vo->id,$paso);
		$this->InsertarTablasUnionCobertura($organizacion_vo,$organizacion_vo->id,$paso);

		if ($paso == 1){
			?>
			<script>
			alert("Cobertura Geogr\xe1fica (Departamento - Municipio) registrada con \xe9xito!");
			</script>
			<?
		}
		else if ($paso == 2){
			?>
			<script>
			alert("Cobertura Geogr\xe1fica (Poblado - Regi\xf3n) registrada con \xe9xito!");
			</script>
			<?
		}
		if ($paso == 3){
			?>
			<script>
			alert("Cobertura Geogr\xe1fica (Departamento - Municipio) registrada con \xe9xito!");
			location.href = '<?=$this->url;?>';
			</script>
			<?
		}
		else if ($paso == 4){
			?>
			<script>
			alert("Cobertura Geogr\xe1fica (Parque Natural, Resguardo o Divison Afro) registrada con \xe9xito!");
			location.href = '<?=$this->url;?>';
			</script>
			<?
		}
		else if ($paso == 5){
			?>
			<script>
			alert("Cobertura Geogr\xe1fica (Poblado - Regi\xf3n) registrada con \xe9xito!");
			location.href = '<?=$this->url;?>';
			</script>
			<?
		}
	}

	/**
	 * Actualiza un Organizacion mapp-oea en la B.D.
	 * @access public
	 * @param object $organizacion_vo VO de Organizacion que se va a actualizar
	 * @param object $organizacion_mo VO de Organizacion MO que se va a actualizar
	 */
	function ActualizarOrgMO($organizacion_vo,$organizacion_mo){

		$sql =  "UPDATE ".$this->tabla." SET";
		$sql .= " NOM_ORG = '".$organizacion_vo->nom."',";
		$sql .= " SIG_ORG = '".$organizacion_vo->sig."',";
		$sql .= " DES_ORG = '".$organizacion_vo->des."',";
		$sql .= " VIEW_ORG = ".$organizacion_vo->view.",";
		$sql .= " NACI_ORG = ".$organizacion_vo->naci.",";
		$sql .= " NIT_ORG = '".$organizacion_vo->nit."',";
		$sql .= " ID_ORG_PAPA = ".$organizacion_vo->id_papa.",";
		$sql .= " ID_TIPO = ".$organizacion_vo->id_tipo.",";
		$sql .= " BD_ORG = ".$organizacion_vo->bd.",";
		$sql .= " ID_MUN_SEDE = '".$organizacion_vo->id_mun_sede."',";
		$sql .= " DONA_ORG = ".$organizacion_vo->dona.",";
		$sql .= " T_REP_ORG = '".$organizacion_vo->t_rep."',";
		$sql .= " DIR_ORG = '".$organizacion_vo->dir."',";
		$sql .= " PU_MAIL_ORG = '".$organizacion_vo->pu_email."',";
		$sql .= " UN_MAIL_ORG = '".$organizacion_vo->un_email."',";
		$sql .= " WEB_ORG = '".$organizacion_vo->web."',";
		$sql .= " TEL1_ORG = '".$organizacion_vo->tel1."',";
		$sql .= " TEL2_ORG = '".$organizacion_vo->tel2."',";
		$sql .= " FAX_ORG = '".$organizacion_vo->fax."',";
		$sql .= " LOGO_ORG = '".$organizacion_vo->logo."',";
		$sql .= " TEL_REP_ORG = '".$organizacion_vo->tel_rep."',";
		$sql .= " EMAIL_REP_ORG = '".$organizacion_vo->email_rep."',";
		$sql .= " N_REP_ORG = '".$organizacion_vo->n_rep."',";
		$sql .= " ESP_COOR_ORG = '".$organizacion_vo->esp_coor."',";
		$sql .= " MAPP_OEA = 1,";
		$sql .= " CONSULTA_SOCIAL = ".$organizacion_vo->consulta_social.",";
		$sql .= " FECHA_UPDATE = now()";

		$sql .= " WHERE ".$this->columna_id." = ".$organizacion_vo->id;

		$this->conn->Execute($sql);

		$this->borrarOrgMoTablasUnion($organizacion_vo->id);

		$this->InsertarTablasUnionOrgMO($organizacion_vo,$organizacion_mo,$organizacion_vo->id);

		//UNLOCK
		$this->lockOrgMO($organizacion_vo->id,0);

		//LOG
		$log = New LogUsuarioDAO();
		$log->RegistrarAdmin($organizacion_vo->id);

		$l = strtoupper(substr($organizacion_vo->nom,0,1));
		?>
		<script>
		location.href='../index_mo.php?m_e=home&accion=actualizar&l=<?=$l?>';
		</script>
		<?

	}

	/**
  * Borra un Organizacion en la B.D.
  * @access public
  * @param int $id ID del Organizacion que se va a borrar de la B.D
  */
	function Borrar($id,$opcion){

		//BORRA TABLAS DE UNION
		$this->BorrarTablasUnion($id);
		$this->BorrarTablasUnionCobertura($id,$opcion);

		//BORRA LA ORG.
		$sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		?>
		<script>
		alert("Registro eliminado con &eacute;xito!");
		location.href = '<?=$this->url;?>';
		</script>
		<?
	}

	/**
  * Borra un Organizacion Registrada en la B.D.
  * @access public
  * @param int $id ID del Organizacion que se va a borrar de la B.D
  */
	function borrarOrgRegistro($id){

		//BORRA LA ORG.
		$sql = "DELETE FROM ".$this->tabla."_registro WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		$sql = "DELETE FROM poblacion_org_registro WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		$sql = "DELETE FROM sector_org_registro WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		$sql = "DELETE FROM enf_org_registro WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		$sql = "DELETE FROM depto_org_registro WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		$sql = "DELETE FROM mpio_org_registro WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		$sql = "DELETE FROM org_registro_donante WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		$sql = "DELETE FROM org_registro_pob_vul WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

	}

	/**
  * Borra las tablas de union de un Organizacion en la B.D.
  * @access public
  * @param int $id ID del Organizacion que se va a borrar de la B.D
  */
	function BorrarTablasUnion($id){

		//POBLACION
		$sql = "DELETE FROM poblacion_org WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		//SECTOR
		$sql = "DELETE FROM sector_org WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		//ENFOQUE
		$sql = "DELETE FROM enf_org WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		//DONANTES
		$sql = "DELETE FROM org_donan WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		$sql = "DELETE FROM org_donan WHERE ID_DONA = ".$id;
		$this->conn->Execute($sql);

	}

	/**
	 * Borra una Organizacion mapp-oea
	 * @access public
	 * @param int $id ID del Organizacion que se va a borrar de la B.D
	 */
	function borrarOrgMO($id){

		//LOG
		$log = New LogUsuarioDAO();
		$log->RegistrarAdmin($id,'org');

		//BORRA LA ORG.
		$sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		$this->borrarOrgMOTablasUnion($id);

	}


	/**
	 * Borra las tablas de union de una Organizacion mapp-oea
	 * @access public
	 * @param int $id ID del Organizacion que se va a borrar de la B.D
	 */
	function borrarOrgMOTablasUnion($id){
		$sql = "DELETE FROM poblacion_org WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		$sql = "DELETE FROM sector_org WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		$sql = "DELETE FROM depto_org WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		$sql = "DELETE FROM mpio_org WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		$sql = "DELETE FROM poblado_org WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		$sql = "DELETE FROM org_trabaja_conoce WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

	}

	/**
  * Borra las tablas de union de un Organizacion en la B.D.
  * @access public
  * @param int $id ID del Organizacion que se va a borrar de la B.D
  */
	function BorrarTablasUnionCobertura($id,$opcion){

		if ($opcion == 1 || $opcion == 3 || $opcion == 0){
			//DEPTOS
			$sql = "DELETE FROM depto_org WHERE ".$this->columna_id." = ".$id;
			$this->conn->Execute($sql);

			//MUNICPIOS
			$sql = "DELETE FROM mpio_org WHERE ".$this->columna_id." = ".$id." AND COBERTURA = 1";
			$this->conn->Execute($sql);
		}
		if ($opcion == 2 || $opcion == 0 || $opcion == 5){

			//BORRA LOS MUNICIPOS ASOCIADOS A LA REGION Y QUE NO SON DE COBERTURA INICIAL
			//CONSULTA LAS REGIONES DONDE TIENE COBERTURA
			$sql = "SELECT ID_REG FROM reg_org WHERE ID_ORG = ".$id;
			$rs = $this->conn->OpenRecordset($sql);
			while ($row_rs = $this->conn->FetchRow($rs)){
				//CONSULTA LOS MUNICIPIOS DE LA REGION
				$sql_m = "SELECT ID_MUN FROM mun_reg WHERE ID_REG = ".$row_rs[0];
				$rs_m = $this->conn->OpenRecordset($sql_m);
				while ($row_rs_m = $this->conn->FetchRow($rs_m)){
					$sql_d = "DELETE FROM mpio_org WHERE ".$this->columna_id." = ".$id." AND ID_MUN = '".$row_rs_m[0]."' AND COBERTURA = 0";
					$this->conn->Execute($sql_d);
					//echo $sql_d;
				}
			}

			//REGIONES
			$sql = "DELETE FROM reg_org WHERE ".$this->columna_id." = ".$id;
			$this->conn->Execute($sql);

			//POBLADOS
			$sql = "DELETE FROM poblado_org WHERE ".$this->columna_id." = ".$id;
			$this->conn->Execute($sql);
			//echo $sql;
		}
		if ($opcion == 4 || $opcion == 0){

			//RESGUARDOS
			$sql = "DELETE FROM resg_org WHERE ".$this->columna_id." = ".$id;
			$this->conn->Execute($sql);

			//PARQUES
			$sql = "DELETE FROM par_nat_org WHERE ".$this->columna_id." = ".$id;
			$this->conn->Execute($sql);

			//DIV. AFRO
			$sql = "DELETE FROM div_afro_org WHERE ".$this->columna_id." = ".$id;
			$this->conn->Execute($sql);
		}
	}

	/******************************************************************************
	* Reporte PDF - EXCEL
	* @param Array $id_orgs Id de las Organziaciones a Reportar
	* @param Int $formato PDF o Excel
	* @param Int $basico 1 = Bsico - 2 = Detallado
	* @param Int $stream 0 = Link a archivo físico 1 = Opcion Download
	* @access public
	*******************************************************************************/
	function ReporteOrganizacion($id_org,$formato,$basico,$stream=0){

		set_time_limit(0);

		//INICIALIZACION DE VARIABLES
		$depto_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();
		$tipo_dao = New TipoOrganizacionDAO();
		$sector_dao = New SectorDAO();
		$enfoque_dao = New EnfoqueDAO();
		$poblacion_dao = New PoblacionDAO();
		$poblado_dao = New PobladoDAO();
		$resguardo_dao = New ResguardoDAO();
		$parque_nat_dao = New ParqueNatDAO();
		$div_afro_dao = New DivAfroDAO();
		$region_dao = New RegionDAO();
		$file = New Archivo();

		$arr_id = explode(",",$id_org);

		if ($formato == 1){
			//LIBRERIAS
			$pdf = new Cezpdf();

			if ($basico == 1){
				$pdf -> ezSetMargins(80,70,20,20);
			}
			else{
				$pdf -> ezSetMargins(100,70,50,50);
			}
			$pdf->selectFont('admin/lib/common/PDFfonts/Helvetica.afm');

			// Coloca el logo y el pie en todas las pginas
			$all = $pdf->openObject();
			$pdf->saveState();

			$img_att = getimagesize('images/logos/enc_reporte_semanal.jpg');
			$pdf->addPngFromFile('images/logos/enc_reporte_semanal.png',700,550,$img_att[0]/2,$img_att[1]/2);

			$pdf->addText(300,580,14,'<b>Sala de Situación Humanitaria</b>');

			if ($basico == 1){
				$pdf->addText(230,560,12,'Listado de Organizaciones con Nombre,Sigla,Tipo y Cobertura');
			}
			else{
				$pdf->addText(230,560,12,'Listado de Organizaciones por Cobertura Geográfica y Tipo de Organización');

			}

			$fecha = getdate();
			$fecha_hoy = $fecha["mday"]."/".$fecha["mon"]."/".$fecha["year"];

			$pdf->addText(370,540,12,$fecha_hoy);

			if ($basico == 2){
				$pdf->setLineStyle(1);
				$pdf->line(50,535,740,535);
				$pdf->line(50,530,740,530);
			}

			$pdf->addText(330,30,8,'Sala de Situación Humanitaria - Naciones Unidas');

			$pdf->restoreState();
			$pdf->closeObject();
			$pdf->addObject($all,'all');

			$pdf->ezSetDy(-30);

			$num_arr = count($arr_id);

			//FORMATO BASICO
			if ($basico == 1){

				$title = Array('nombre' => '<b>Nombre</b>',
				'sigla'   => '<b>Sigla</b>',
				'tipo'   => '<b>Tipo</b>',
				'cobertura'   => '<b>Cobertura</b>');

				$p = 0;
				foreach ($arr_id as $id){
					$vo = $this->Get($id);
					$data[$p]['nombre'] = $vo->nom;
					$data[$p]['sigla'] = $vo->sig;

					//NOMBRE DEL TIPO DE ORGANIZACION
					$nom_tipo = '';
					$data[$p]['tipo'] = '';
					if ($vo->id_tipo != ''){
						$tipo = $tipo_dao->Get($vo->id_tipo);
						$nom_tipo = $tipo->nombre_es;
						$data[$p]['tipo'] = $nom_tipo;
					}

					//COBERTURA
					$cob = "";
					foreach($vo->id_deptos as $id){
						$vo = $depto_dao->Get($id);
						if ($cob == "")	$cob = $vo->nombre;
						else				$cob .= ",".$vo->nombre;
					}
					$data[$p]['cobertura'] = $cob;

					$p++;
				}

				$options = Array('showLines' => 2, 'shaded' => 0, 'width' => 750, 'fontSize'=>8, 'cols'=>array('nombre'=>array('width'=>300),'sigla'=>array('width'=>80),'tipo'=>array('width'=>150)));
				$pdf->ezTable($data,$title,'',$options);
			}
			//FORMATO DETALLADO
			else if ($basico == 2){

				foreach ($arr_id as $id){
					$org = $this->Get($id);

					//ORG. A LA QUE PERTENECE
					$id_papa = $org->id_papa;
					if ($id_papa != 0 ){
						$org_papa = $this->Get($id_papa);
					}
					else{
						$org_papa->nom = "-";
					}

					//TIPO
					$tipo = $tipo_dao->Get($org->id_tipo);

					//MUN. SEDE
					$mun_sede = "";
					if ($org->id_mun_sede != "")
					$mun_sede = $mun_dao->Get($org->id_mun_sede);

					//ES DONANTE
					$org->dona = "No";
					if ($org->dona == 1)	$org->dona = "Si";

					//INFO. CONFIRMADA
					$org->info_confirmada = "No";
					if ($org->info_confirmada == 1)	$org->info_confirmada = "Si";

					//B.D
					$org->bd = "No";
					if ($org->bd == 1)	$org->bd = "Si";

					//ESPACIO DE CORRDINACION
					if ($org->esp_coor == "")	$org->esp_coor = " No ";


					//NOMBRE
					$pdf->setColor(0.9,0.9,0.9);
					$pdf->filledRectangle($pdf->ez['leftMargin'],$pdf->y-$pdf->getFontHeight(12)+$pdf->getFontDecender(12),$pdf->ez['pageWidth']-$pdf->ez['leftMargin']-$pdf->ez['rightMargin'],$pdf->getFontHeight(12));
					$pdf->setColor(0,0,0);

					$pdf->ezText("<b>".$org->nom."</b>",10);
					$pdf->ezSetDy(-5);

					$li = "";
					//SIGLA
					if ($org->sig != ""){
						$li .= "<b>Sigla</b>: ".$org->sig."    ";
					}

					//TIPO
					$li .= "<b>Tipo de Organización</b>: ".$tipo->nombre_es."    ";

					//SEDE
					$li .= "<b>Sede</b>: ".$mun_sede->nombre."    ";

					$pdf->ezText($li,10);
					$pdf->ezSetDy(-5);


					//NIT
					if ($org->nit != ""){
						$pdf->ezText("<b>NIT</b>: ".$org->nit,10);
						$pdf->ezSetDy(-5);
					}

					//AÑO FUND.
					if ($org->naci != "" && $org->naci != 0){
						$pdf->ezText("<b>Año de fundación en Colombia</b>: ".$org->naci,10);
						$pdf->ezSetDy(-5);
					}

					if ($_SESSION["id_tipo_usuario_s"] != 20){
						//DIRECCION
						if ($org->dir != ""){
							$pdf->ezText("<b>Dirección</b>: ".$org->dir,10);
							$pdf->ezSetDy(-5);
						}

						//TELES
						$li = "";
						if ($org->tel1 != ""){
							$li .= "<b>Tel&eacute;fono:</b> ".$org->tel1."    ";
						}
						if ($org->tel2 != ""){
							$li .= "<b>Tel&eacute;fono 2:</b> ".$org->tel2."    ";
						}
						if ($org->fax != ""){
							$li .= "<b>Fax:</b> ".$org->fax;
						}

						if ($li != ""){
							$pdf->ezText($li,10);
							$pdf->ezSetDy(-5);
						}

						//EMAIL
						$li = "";
						if ($org->pu_email != ""){
							$li .= "<b>Email:</b> ".$org->pu_email."    ";
						}
						if ($org->web != ""){
							$li .= "<b>Página Web:</b> ".$org->web;
						}
						$pdf->ezText($li,10);
						$pdf->ezSetDy(-5);
					}

					//PAPA
					if ($org->id_papa != 0){
						$pdf->ezText("<b>Organización a la que pertenece</b>: ".$org_papa->nom,10);
						$pdf->ezSetDy(-5);
					}

					//ES DONANTE?
					$pdf->ezText("<b>Es Organización Donante</b>: ".$org->dona,10);
					$pdf->ezSetDy(-5);

					//DONANTES
					if (count($org->id_donantes) > 0){
						$pdf->ezText("<b>Organizaciones de las que recibe recursos</b>: ",10);
						$s = 0;
						foreach($org->id_donantes as $id){
							$vo = $this->Get($id);
							$li = "- ".$vo->nom;
							$pdf->ezText($li,10);
							$s++;
						}
						$pdf->ezSetDy(-5);
					}

					//BD.
					$li = "<b>Crea Base de Datos de Organizaciones</b>: ".$org->bd."         ";

					//ESP. COOR
					$li .= "<b>Participa en algún espacio de coordinación</b>: ".$org->esp_coor;

					$pdf->ezText($li,10);
					$pdf->ezSetDy(-5);


					if ($_SESSION["id_tipo_usuario_s"] != 20){
						//REPRESENTANTE
						if ($org->n_rep != ""){
							$li = "<b>Nombre del Representante:</b>".$org->n_rep;
							if ($org->t_rep != ""){
								$li .= "<b>Título:</b> ".$org->t_rep."    ";
							}
							if ($org->tel_rep != ""){
								$li .= "<b>Teléfono:</b> ".$org->tel_rep."    ";
							}
							if ($org->email_rep != ""){
								$li .= "<b>Email:</b> ".$org->email_rep;
							}

							$pdf->ezText($li,10);
							$pdf->ezSetDy(-5);
						}
					}

					//SECTOR
					if (count($org->id_sectores) > 0){
						$pdf->ezText("<b>Sector</b>:",10);
						$s = 0;
						foreach($org->id_sectores as $id){
							$vo = $sector_dao->Get($id);
							$li = "- ".$vo->nombre_es;
							$pdf->ezText($li,10);
							$s++;
						}
						$pdf->ezSetDy(-5);
					}

					//ENFOQUE
					if (count($org->id_enfoques) > 0){
						$pdf->ezText("<b>Enfoque</b>: ",10);
						$s = 0;
						foreach($org->id_enfoques as $id){
							$vo = $enfoque_dao->Get($id);
							$li = "- ".$vo->nombre_es;
							$pdf->ezText($li,10);
							$s++;
						}
						$pdf->ezSetDy(-5);
					}


					//POBLACION
					if (count($org->id_poblaciones) > 0){
						$pdf->ezText("<b>Población Sujeto</b>: ",10);
						$s = 0;
						foreach($org->id_poblaciones as $id){
							$vo = $poblacion_dao->Get($id);
							$li = "- ".$vo->nombre_es;
							$pdf->ezText($li,10);
							$s++;
						}
						$vo = $poblacion_dao->Get($id);
						$pdf->ezSetDy(-5);
					}

					//DEPTO
					if (count($org->id_deptos) > 0){
						$pdf->ezText("<b>Cobertura Geográfica por Departamento</b>:",10);
						$s = 0;
						foreach($org->id_deptos as $id){
							$vo = $depto_dao->Get($id);
							$li = "- ".$vo->nombre;
							$pdf->ezText($li,10);
							$s++;
						}
						$pdf->ezSetDy(-5);
					}
					//MUN
					if (count($org->id_muns) > 0){
						$pdf->ezText("<b>Cobertura Geográfica Municipio</b>:",10);
						$s = 0;
						$li = "";
						foreach($org->id_muns as $id){
							if ($id != ""){
								$vo = $mun_dao->Get($id);
								$li .= "- ".$vo->nombre."  ";
							}
							$s++;
						}
						$pdf->ezText($li,10);
						$pdf->ezSetDy(-5);
					}
					//REGION
					if (count($org->id_regiones) > 0){
						$pdf->ezText("<b>Cobertura Geográfica por Región</b>:",10);
						$s = 0;
						foreach($org->id_regiones as $id){
							$vo = $region_dao->Get($id);
							$li = "- ".$vo->nombre;
							$pdf->ezText($li,10);
							$s++;
						}
						$pdf->ezSetDy(-5);
					}

					//POBLADO
					if (count($org->id_poblados) > 0){
						$pdf->ezText("<b>Cobertura Geográfica por Poblado</b>:",10);
						$s = 0;
						foreach($org->id_poblados as $id){
							$vo = $poblado_dao->Get($id);
							$li = "- ".$vo->nombre;
							$pdf->ezText($li,10);
							$s++;
						}
						$pdf->ezSetDy(-5);
					}

					//PARQUE NAT.
					if (count($org->id_parques) > 0){
						$pdf->ezText("<b>Cobertura Geográfica por Parque Natural</b>:",10);
						$s = 0;
						foreach($org->id_parques as $id){
							$vo = $parque_nat_dao->Get($id);
							$li = "- ".$vo->nombre;
							$pdf->ezText($li,10);
							$s++;
						}
						$pdf->ezSetDy(-5);
					}

					//RESGUARDO
					if (count($org->id_resguardos) > 0){
						$pdf->ezText("<b>Cobertura Geográfica por Resguardo</b>:",10);
						$s = 0;
						foreach($org->id_resguardos as $id){
							$vo = $resguardo_dao->Get($id);
							$li = "- ".$vo->nombre;
							$pdf->ezText($li,10);
							$s++;
						}
						$pdf->ezSetDy(-5);
					}

					//DIV. AFRO
					if (count($org->id_divisiones_afro) > 0){
						$pdf->ezText("<b>Cobertura Geográfica por Divisin Afrouardo</b>:",10);
						$s = 0;
						foreach($org->id_divisiones_afro as $id){
							$vo = $div_afro_dao->Get($id);
							$li = "- ".$vo->nombre;
							$pdf->ezText($li,10);
							$s++;
						}
						$pdf->ezSetDy(-5);
					}
					$pdf->ezSetDy(-5);
				}

			}

			//MUESTRA EN EL NAVEGADOR EL PDF
			if ($stream == 1){
				//$pdf->ezStream();
				echo $pdf->ezOutput();
			}
			else{
				//CREA UN ARCHIVO PDF PARA BAJAR
				$nom_archivo = 'consulta/csv/org.pdf';
				$fp = $file->Abrir($nom_archivo,'wb');
				$pdfcode = $pdf->ezOutput();
				$file->Escribir($fp,$pdfcode);
				$file->Cerrar($fp);

				?>
				<table align='center' cellspacing="1" cellpadding="3" border="0" width="750">
					<tr><td>&nbsp;</td></tr>
					<tr><td align='center' class='titulo_lista' colspan=2>REPORTAR ORGANIZACIONES EN FORMATO PDF</td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td colspan=2>
						Se ha generado correctamente el archivo PDF de Organizaciones.<br><br>
						Para salvarlo use el botón derecho del mouse y la opción -Guardar destino como- sobre el siguiente link: <a href='<?=$nom_archivo;?>'>Archivo PDF</a>
					</td></tr>
				</table>
				<?
			}
		}
		//EXCEL
		else if ($formato == 2){

			$mapp_oea = 0;
			if ($basico == 1){
				$tit = "ID|NOMBRE|SIGLA|TIPO|COBERTURA";
				$xls = "<tr class='titulo'><td>ID</td><td>NOMBRE</td><td>SIGLA</td><td>TIPO</td><td>COBERTURA</td></tr>";
			}
			else{
				if (isset($_SESSION["mapp_oea"]) && $_SESSION["mapp_oea"] == 1){
					$mapp_oea = 1;
					$tit = "NOMBRE|SIGLA|TIPO|SEDE|DIRECCION|AÑO DE FUNDACION|NOMBRE REPRESENTANTE|TITULO REPRESENTANTE|TELEFONO REPRESENTANTE|EMAIL REPRESENTANTE|EMAIL DE LA ORGANIZACION|PAGINA WEB|TELEFONO 1|TELEFONO 2|FAX|NIT|ORGANIZACION A LA QUE PERTENECE|ES DONANTE|INFO. CONFIRMADA|CREA BASE DE DATOS|SECTOR|ENFOQUE|POBLACION|COBERTURA POR DEPARTAMENTO|COBERTURA POR MUNICIPIO|COBERTURA POR REGION|COBERTURA POR POBLADO|COBERTURA POR PARQUE NATURAL|COBERTURA POR RESGUARDO|COBERTURA POR DIVISION AFRO|ESPACIO DE COORDINACION|DONANTES (Orgs. de las que recibe recursos)|CONSULTA SOCIAL";
					$xls = "<tr class='titulo'><td>NOMBRE</td><td>SIGLA</td><td>TIPO</td><td>SEDE</td><td>DIRECCION</td><td>A&Ntilde;O DE FUNDACION</td><td>NOMBRE REPRESENTANTE</td><td>TITULO REPRESENTANTE</td><td>EMAIL DE LA ORGANIZACION</td><td>PAGINA WEB</td><td>TELEFONO 1</td><td>TELEFONO 2</td><td>FAX</td><td>ORGANIZACION A LA QUE PERTENECE</td><td>SECTOR</td><td>POBLACION</td><td>COBERTURA POR DEPARTAMENTO</td><td>COBERTURA POR MUNICIPIO</td><td>COBERTURA POR POBLADO</td><td>ESPACIO DE COORDINACION</td></tr>";
				}
				else{
					$tit = "ID|NOMBRE|SIGLA|TIPO|SEDE|DIRECCION|AÑO DE FUNDACION|NOMBRE REPRESENTANTE|TITULO REPRESENTANTE|TELEFONO REPRESENTANTE|EMAIL REPRESENTANTE|EMAIL DE LA ORGANIZACION|PAGINA WEB|TELEFONO 1|TELEFONO 2|FAX|NIT|ORGANIZACION A LA QUE PERTENECE|ES DONANTE|INFO. CONFIRMADA|CREA BASE DE DATOS|SECTOR|ENFOQUE|POBLACION|COBERTURA POR DEPARTAMENTO|COBERTURA POR MUNICIPIO|COBERTURA POR REGION|COBERTURA POR POBLADO|COBERTURA POR PARQUE NATURAL|COBERTURA POR RESGUARDO|COBERTURA POR DIVISION AFRO|ESPACIO DE COORDINACION|DONANTES (Orgs. de las que recibe recursos)|CONSULTA SOCIAL|CNRR";
					$xls = "<tr><td>ID</td><td>NOMBRE</td><td>SIGLA</td><td>TIPO</td><td>SEDE</td><td>DIRECCION</td><td>AÑO DE FUNDACION</td><td>NOMBRE REPRESENTANTE</td><td>TITULO REPRESENTANTE</td><td>TELEFONO REPRESENTANTE</td><td>EMAIL REPRESENTANTE</td><td>EMAIL DE LA ORGANIZACION</td><td>PAGINA WEB</td><td>TELEFONO 1</td><td>TELEFONO 2</td><td>FAX</td><td>NIT</td><td>ORGANIZACION A LA QUE PERTENECE</td><td>ES DONANTE</td><td>INFO. CONFIRMADA</td><td>CREA BASE DE DATOS</td><td>SECTOR</td><td>ENFOQUE</td><td>POBLACION</td><td>COBERTURA POR DEPARTAMENTO</td><td>COBERTURA POR MUNICIPIO</td><td>COBERTURA POR REGION</td><td>COBERTURA POR POBLADO</td><td>COBERTURA POR PARQUE NATURAL</td><td>COBERTURA POR RESGUARDO</td><td>COBERTURA POR DIVISION AFRO</td><td>ESPACIO DE COORDINACION</td><td>DONANTES (Orgs. de las que recibe recursos)</td><td>CONSULTA SOCIAL</td><td>CNRR</td></tr>";
				}
			}

			//ENCABEZADO
			if ($stream == 0){
				$nom_archivo = 'consulta/csv/org.txt';
				$fp = $file->Abrir($nom_archivo,'w');
				$file->Escribir($fp,$tit."\n");
			}

			$num_arr = count($arr_id);
			$p = 0;
			foreach ($arr_id as $id){
				$org = $this->Get($id);

				if($mapp_oea == 0)	$linea = $org->id;

				if ($mapp_oea == 0)	$linea .= "|".$org->nom;
				else				$linea = $org->nom;

				$linea .= "|".$org->sig;

				//NOMBRE DEL TIPO DE ORGANIZACION
				if ($org->id_tipo != ''){
					$tipo = $tipo_dao->Get($org->id_tipo);
					$nom_tipo = $tipo->nombre_es;
					$linea .= "|".$nom_tipo;
				}
				else{
					$nom_tipo = '';
					$linea .= "|";
				}

				if ($basico == 1){
					//COBERTURA
					$cob = "";
					foreach($org->id_deptos as $id){
						$vo = $depto_dao->Get($id);
						if ($cob == "")	$cob = $vo->nombre;
						else				$cob .= "-".$vo->nombre;
					}
					$linea .= "|".$cob;
				}
				else{

					//NACI
					if ($org->naci == 0)	$org->naci = "";

					//ORG. A LA QUE PERTENECE
					$id_papa = $org->id_papa;
					if ($id_papa != 0){
						$org_papa = $this->Get($id_papa);
					}
					else{
						$org_papa->nom = "";
					}

					//MUN. SEDE
					$nom_mun_sede = "";
					if ($org->id_mun_sede != ""){
						$mun_sede = $mun_dao->Get($org->id_mun_sede);
						$nom_mun_sede = $mun_sede->nombre;
					}

					//ES DONANTE
					$org->dona = "No";
					if ($org->dona == 1)	$org->dona = "Si";

					//INFO. CONFIRMADA
					$org->info_confirmada = "No";
					if ($org->info_confirmada == 1)	$org->info_confirmada = "Si";

					//B.D
					$org->bd = "No";
					if ($org->bd == 1)	$org->bd = "Si";

					//ESPACIO DE CORRDINACION
					if ($org->esp_coor == "")	$org->esp_coor = " No ";


					$linea .= "|".$nom_mun_sede;
					$linea .= "|".$org->dir;
					$linea .= "|".$org->naci;
					$linea .= "|".$org->n_rep;
					$linea .= "|".$org->t_rep;
					if ($mapp_oea == 0)	$linea .= "|".$org->tel_rep;
					if ($mapp_oea == 0)	$linea .= "|".$org->email_rep;
					$linea .= "|".$org->pu_email;
					$linea .= "|".str_replace("\n","",$org->web);
					$linea .= "|".$org->tel1;
					$linea .= "|".$org->tel2;
					$linea .= "|".$org->fax;
					if ($mapp_oea == 0)	$linea .= "|".$org->nit;
					$linea .= "|".$org_papa->nom;
					if ($mapp_oea == 0)	$linea .= "|".$org->dona;
					if ($mapp_oea == 0)	$linea .= "|".$org->info_confirmada;
					if ($mapp_oea == 0)	$linea .= "|".$org->bd;

					//SECTOR
					$s = 0;
					foreach($org->id_sectores as $id){
						$vo = $sector_dao->Get($id);
						if ($s == 0)	$linea .= "|".$vo->nombre_es;
						else			$linea .= "-".$vo->nombre_es;
						$s++;
					}
					if ($s == 0)	$linea .= "|";

					//ENFOQUE
					if ($mapp_oea == 0){
						$s = 0;
						foreach($org->id_enfoques as $id){
							$vo = $enfoque_dao->Get($id);
							if ($s == 0)	$linea .= "|".$vo->nombre_es;
							else			$linea .= "-".$vo->nombre_es;
							$s++;
						}
						if ($s == 0)	$linea .= "|";
					}

					//POBLACION
					$s = 0;
					foreach($org->id_poblaciones as $id){
						$vo = $poblacion_dao->Get($id);
						if ($s == 0)	$linea .= "|".$vo->nombre_es;
						else			$linea .= "-".$vo->nombre_es;
						$s++;
					}
					if ($s == 0)	$linea .= "|";

					//COBERTURA POR DEPARTAMENTO
					$s = 0;
					foreach($org->id_deptos as $id){
						$vo = $depto_dao->Get($id);
						if ($s == 0)	$linea .= "|".$vo->nombre;
						else			$linea .= "-".$vo->nombre;
						$s++;
					}
					if ($s == 0)	$linea .= "|";

					//COBERTURA POR MUNICIPIO
					$s = 0;
					foreach($org->id_muns as $id){

						$vo = $mun_dao->Get($id);

						if ($s == 0)	$linea .= "|".$vo->nombre;
						else			$linea .= "-".$vo->nombre;
						$s++;
					}

					if ($s == 0)	$linea .= "|";

					//COBERTURA POR REGION
					if ($mapp_oea == 0){
						$s = 0;
						foreach($org->id_regiones as $id){
							$vo = $region_dao->Get($id);
							if ($s == 0)	$linea .= "|".$vo->nombre;
							else			$linea .= "-".$vo->nombre;
							$s++;
						}
						if ($s == 0)	$linea .= "|";
					}

					//COBERTURA POR POBLADO
					if (count($org->id_poblados) > 0){
						$s = 0;
						foreach($org->id_poblados as $id){
							$vo = $poblado_dao->Get($id);
							if ($s == 0)	$linea .= "|".$vo->nombre;
							else			$linea .= "-".$vo->nombre;
							$s++;
						}
					}
					else $linea .= "|";

					if ($mapp_oea == 0){
						//COBERTURA POR PARQUE NAT.
						if (count($org->id_parques) > 0){
							$s = 0;
							foreach($org->id_poblados as $id){
								$vo = $parque_nat_dao->Get($id);
								if ($s == 0)	$linea .= "|".$vo->nombre;
								else			$linea .= "-".$vo->nombre;
								$s++;
							}
						}

						if ($s == 0)	$linea .= "|";
						//COBERTURA POR RESGUARDO
						if (count($org->id_resguardos) > 0){
							$s = 0;
							foreach($org->id_resguardos as $id){
								$vo = $resguardo_dao->Get($id);
								if ($s == 0)	$linea .= "|".$vo->nombre;
								else			$linea .= "-".$vo->nombre;
								$s++;
							}
						}
						if ($s == 0)	$linea .= "|";
						//COBERTURA POR DIV. AFRO
						if (count($org->id_divisiones_afro) > 0){
							$s = 0;
							foreach($org->id_divisiones_afro as $id){
								$vo = $div_afro_dao->Get($id);
								if ($s == 0)	$linea .= "|".$vo->nombre;
								else			$linea .= "-".$vo->nombre;
								$s++;
							}
						}
						if ($s == 0)	$linea .= "|";
					}

					//ESPACIO DE COORDINACION
					$esp_coor = str_replace("\r\n","",$org->esp_coor);
					$esp_coor = str_replace("\n","",$esp_coor);
					$linea .= "|".$esp_coor;

					if ($mapp_oea == 0){
						//DONANTES
						$s = 0;
						foreach($org->id_donantes as $id){
							$vo = $this->Get($id);
							if ($s == 0)	$linea .= "|".$vo->nom;
							else			$linea .= "-".$vo->nom;
							$s++;
						}
						if ($s == 0)	$linea .= "|";

						$linea .= "|".$org->consulta_social;
						$linea .= "|".$org->cnrr;
					}


				}
				if ($stream==0)	$file->Escribir($fp,$linea."\n");
				else 			$xls .= "<tr><td>".str_replace("|","</td><td>",$linea)."</td></tr>";

			}
			if ($stream==0){
				$file->Cerrar($fp);

				?>
				<table align='center' cellspacing="1" cellpadding="3" border="0" width="750">
					<tr><td>&nbsp;</td></tr>
					<tr><td align='center' class='titulo_lista' colspan=2>REPORTAR ORGANIZACIONES EN FORMATO CSV (Excel)</td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td colspan=2>
						Se ha generado correctamente el archivo CSV de Organizaciones.<br><br>
						Para salvarlo use el botón derecho del mouse y la opción -Guardar destino como- sobre el siguiente link: <a href='<?=$nom_archivo;?>'>Archivo CSV</a>
					</td></tr>
				</table>
				<?
			}
			else{
				echo "<table cellpadding='2' cellspacing='1' class='listado'>$xls</table>";
				$_SESSION["xls"] = "<table border=1>$xls</table>";
			}
		}
	}

	/**
	* Lista las Organizaciones en una Tabla
	* @access public
	*/
	function ReportarMapaI(){

		//INICIALIZACION DE VARIABLES
		$depto_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();
		$tipo_dao = New TipoOrganizacionDAO();
		$sector_dao = New SectorDAO();
		$enfoque_dao = New EnfoqueDAO();
		$poblacion_dao = New PoblacionDAO();

		$sede = 0;
		$cobertura = 0;
		if(isset($_POST["sede"])){
			$sede = 1;
		}
		if(isset($_POST["cobertura"])){
			$cobertura = 1;
		}

		if($sede == 1 && $cobertura == 0){
			$sede_cobertura_t = "SEDE";
		}
		//COBERTURA
		else if($sede == 0 && $cobertura == 1) {
			$sede_cobertura_t = "COBERTURA";
		}
		//AMBOS
		else if ($sede == 1 && $cobertura == 1) {
			$sede_cobertura_t = "SEDE o COBERTURA";
		}


		//UBIACION GEOGRAFICA
		if (isset($_POST["id_depto"]) && !isset($_POST["id_muns"])){

			$id_depto = $_POST["id_depto"];
			$d = 0;
			foreach ($id_depto as $id_d){
				$id_depto[$d] = "'".$id_d."'";
				$d++;
			}
			$id_depto_s = implode(",",$id_depto);

			$arr_id_u_g = Array();

			//SEDE
			if ($sede == 1 && $cobertura == 0){
				$sql = "SELECT ID_ORG FROM organizacion INNER JOIN municipio ON organizacion.ID_MUN_SEDE = municipio.ID_MUN WHERE ID_DEPTO IN (".$id_depto_s.")";

				$sql .= " ORDER BY ID_ORG ASC";

				$rs = $this->conn->OpenRecordset($sql);
				$i = 0;
				while ($row_rs = $this->conn->FetchRow($rs)){
					$arr_id_u_g[$i] = $row_rs[0];
					$i++;
				}

			}
			//COBERTURA
			else if ($sede == 0 && $cobertura == 1){
				$sql = "SELECT organizacion.ID_ORG FROM depto_org INNER JOIN organizacion ON depto_org.ID_ORG = organizacion.ID_ORG WHERE ID_DEPTO IN (".$id_depto_s.")";

				if (isset($_GET["col_orden"])){
					$sql .= " ORDER BY organizacion.".$_GET["col_orden"]." ".$_GET["dir_orden"];
				}
				else{
					$sql .= " ORDER BY organizacion.ID_ORG ASC";
				}

				$rs = $this->conn->OpenRecordset($sql);
				$i = 0;
				while ($row_rs = $this->conn->FetchRow($rs)){
					$arr_id_u_g[$i] = $row_rs[0];
					$i++;
				}
			}
			//AMBOS
			else if ($sede == 1 && $cobertura == 1){
				$sql = "SELECT ID_ORG FROM organizacion INNER JOIN municipio ON organizacion.ID_MUN_SEDE = municipio.ID_MUN WHERE ID_DEPTO IN (".$id_depto_s.")";

				if (isset($_GET["col_orden"])){
					$sql .= " ORDER BY ".$_GET["col_orden"]." ".$_GET["dir_orden"];
				}
				else{
					$sql .= " ORDER BY ID_ORG ASC";
				}

				$rs = $this->conn->OpenRecordset($sql);
				$i = 0;
				while ($row_rs = $this->conn->FetchRow($rs)){
					$arr_id_u_g_sede[$i] = $row_rs[0];
					$arr_id_u_g[$i] = $row_rs[0];
					$i++;
				}

				$sql = "SELECT organizacion.ID_ORG FROM depto_org INNER JOIN organizacion ON depto_org.ID_ORG = organizacion.ID_ORG WHERE ID_DEPTO IN (".$id_depto_s.")";

				if (isset($_GET["col_orden"])){
					$sql .= " ORDER BY organizacion.".$_GET["col_orden"]." ".$_GET["dir_orden"];
				}
				else{
					$sql .= " ORDER BY organizacion.ID_ORG ASC";
				}

				$rs = $this->conn->OpenRecordset($sql);
				$i = 0;
				while ($row_rs = $this->conn->FetchRow($rs)){
					if (!in_array($row_rs[0],$arr_id_u_g)){
						$arr_id_u_g[$i] = $row_rs[0];
						$i++;
					}
					$arr_id_u_g_cobertura[$i] = $row_rs[0];
				}
			}
		}
		//MUNICIPIO
		else if (isset($_POST["id_depto"]) && isset($_POST["id_muns"])){
			$arr_id_u_g = Array();

			$id_muns = $_POST["id_muns"];
			$m = 0;
			foreach ($id_muns as $id_m){
				$id_muns[$m] = "'".$id_m."'";
				$m++;
			}

			$id_muns_s = implode(",",$id_muns);


			//SEDE
			if ($sede == 1 && $cobertura == 0){
				$sql = "SELECT ID_ORG FROM organizacion WHERE ID_MUN_SEDE IN (".$id_muns_s.")";
				$sql .= " ORDER BY ID_ORG ASC";

				$rs = $this->conn->OpenRecordset($sql);
				$i = 0;
				while ($row_rs = $this->conn->FetchRow($rs)){
					$arr_id_u_g[$i] = $row_rs[0];
					$i++;
				}
			}
			//COBERTURA
			else if ($sede == 0 && $cobertura == 1){
				$sql = "SELECT organizacion.ID_ORG FROM mpio_org INNER JOIN organizacion ON mpio_org.ID_ORG = organizacion.ID_ORG WHERE ID_MUN IN (".$id_muns_s.")";
				$sql .= " ORDER BY organizacion.ID_ORG ASC";

				$rs = $this->conn->OpenRecordset($sql);
				$i = 0;
				while ($row_rs = $this->conn->FetchRow($rs)){
					$arr_id_u_g[$i] = $row_rs[0];
					$i++;
				}
			}
			//AMBOS
			else if ($sede == 1 && $cobertura == 1){
				$sql = "SELECT ID_ORG FROM organizacion WHERE ID_MUN_SEDE IN (".$id_muns_s.")";
				$sql .= " ORDER BY ID_ORG ASC";

				$rs = $this->conn->OpenRecordset($sql);
				$i = 0;
				while ($row_rs = $this->conn->FetchRow($rs)){
					$arr_id_u_g[$i] = $row_rs[0];
					$arr_id_u_g_sede[$i] = $row_rs[0];
					$i++;
				}

				$sql = "SELECT organizacion.ID_ORG FROM mpio_org INNER JOIN organizacion ON mpio_org.ID_ORG = organizacion.ID_ORG WHERE ID_MUN IN (".$id_muns_s.")";
				$sql .= " ORDER BY organizacion.ID_ORG ASC";

				$rs = $this->conn->OpenRecordset($sql);
				while ($row_rs = $this->conn->FetchRow($rs)){
					if (!in_array($row_rs[0],$arr_id_u_g)){
						$arr_id_u_g[$i] = $row_rs[0];
						$i++;
					}
					$arr_id_cobertura[$i] = $row_rs[0];
				}
			}
		}

		$arr_id = $arr_id_u_g;

		$c = 0;
		$arr = Array();
		foreach ($arr_id as $id){
			//Carga el VO
			$vo = $this->Get($id);
			//Carga el arreglo
			$arr[$c] = $vo;
			$c++;
		}

		$num_arr = count($arr);

		echo "<table align='center' class='tabla_reportelist_outer' border=0>";
		echo "<tr><td>&nbsp;</td></tr>";
		if ($num_arr > 0){
			//<td colspan='5' align='right'>Exportar a: <input type='image' src='images/consulta/generar_pdf.gif' onclick=\"document.getElementById('pdf_org').value = 1;\">&nbsp;&nbsp;<input type='image' src='images/consulta/excel.gif' onclick=\"document.getElementById('pdf_org').value = 2;\"></td>
			echo "<tr>
					<td width='300'><a href='javascript:history.back(-1)'><img src='images/back.gif' border=0 class='TipExport' title='Exportar a Excel::<b>Basico</b>: Nombre,Sigla,Tipo,Cobertura<br><b>Detallado</b>: Toda la inforamci&oacute;n'>&nbsp;Regresar</a></td>
					<td align='right'>Generar Reporte: <input type='radio' id='basico' name='basico' value='1' checked>&nbsp;B&aacute;sico</a>&nbsp;<input type='radio' id='detallado' name='basico' value=2>&nbsp;Detallado&nbsp;&nbsp;<a href=\"#\" onclick=\"document.getElementById('pdf_org').value = 1;reportStream('org');return false;\"><img src='images/consulta/generar_pdf.gif' border=0 class='TipExport' title='Exportar a PDF::<b>Basico</b>: Nombre,Sigla,Tipo,Cobertura<br><b>Detallado</b>: Toda la inforamci&oacute;n'></a>&nbsp;&nbsp;<a href=\"#\" onclick=\"document.getElementById('pdf_org').value = 2;reportStream('org');return false;\"\"><img src='images/consulta/excel.gif' border=0 class='TipExport' title='Exportar a Excel::<b>Basico</b>: Nombre,Sigla,Tipo,Cobertura<br><b>Detallado</b>: Toda la inforamci&oacute;n'></a></td>
				</tr>";
		}
		echo "<tr><td align='center' class='titulo_lista' colspan=7>CONSULTA DE ORGANIZACIONES CON ".$sede_cobertura_t." EN : ";

		//TITULO DE DEPTO
		if (isset($_POST["id_depto"]) && !isset($_POST["id_muns"])){
			$t = 0;
			foreach($_POST["id_depto"] as $id_t){
				$vo  = $depto_dao->Get($id_t);
				if ($t == 0)	echo "<b>".$vo->nombre."</b>";
				else			echo ", <b>".$vo->nombre."</b>";
				$t++;
			}
			echo "<br>";
		}
		//TITULO DE MPIO
		if (isset($_POST["id_muns"])){
			$t = 0;
			foreach($_POST["id_muns"] as $id_t){
				$vo  = $mun_dao->Get($id_t);
				if ($t == 0)	echo "<b>".$vo->nombre."</b>";
				else			echo ", <b>".$vo->nombre."</b>";
				$t++;
			}
		}
		echo "</td></tr>";

		if ($num_arr > 0){
			echo "<tr><td colspan=3><table class='tabla_reportelist'>";
			echo"<tr class='titulo_lista'>
				<td>Nombre</td>
				<td>Sigla</td>
				<td>Tipo</td>";
			echo "<td align='center' width='150'>Registros: ".$num_arr."</td>
	    		</tr>";

			for($p=0;$p<$num_arr;$p++){
				$style = "";
				if (fmod($p+1,2) == 0)  $style = "fila_lista";

				//NOMBRE
				if ($arr[$p]->nom != ""){

					//NOMBRE DEL TIPO DE ORGANIZACION
					$tipo = $tipo_dao->Get($arr[$p]->id_tipo);
					$nom_tipo = $tipo->nombre_es;

					echo "<tr class='".$style."'>";
					echo "<td>".$arr[$p]->nom."</td>";
					echo "<td>".$arr[$p]->sig."</td>";
					echo "<td>".$nom_tipo."</td>";
					echo "<td align='center'><a href='#' onclick=\"window.open('admin/ver.php?class=OrganizacionDAO&method=Ver&param=".$arr[$p]->id."','','top=30,left=30,height=900,width=900,scrollbars=1');return false;\">Detalles</a></td>";
					echo "</tr>";
				}
			}

			echo "<tr><td>&nbsp;</td></tr>";
		}
		else{
			echo "<tr><td align='center'><br><b>NO SE ENCONTRARON ORGANIZACIONES</b></td></tr>";
		}

		//VARIABLE DE SESION QUE SE USA PARA EXPORTAR A EXCEL Y PDF EN EL ARCHIVO EXPORT_DATA.PHP
		$_SESSION["id_orgs"] = $arr_id;

		echo "<input type='hidden' id='id_orgs' name='id_orgs' value='".implode(",",$arr_id)."'>";
		echo "<input type='hidden' id='que_org' name='que_org' value='1'>";
		echo "</table>";
	}

	function ImportarCSV($userfile){

		$archivo = New Archivo();
		$depto_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();
		$org = New Organizacion();
		$tipo_dao = New TipoOrganizacionDAO();

		$id_depto = $_POST["id_depto"];
		$depto = $depto_dao->Get($id_depto);
		$org->id_depto = $id_depto;

		//Muns. del Depto
		$muns_depto = $mun_dao->GetAllArrayID('ID_DEPTO='.$id_depto,'NOM_MUN');

		$file_tmp = $userfile['tmp_name'];
		$file_nombre = $userfile['name'];

		$path = "org/csv/".$file_nombre;

		$archivo->SetPath($path);
		$archivo->Guardar($file_tmp);

		$fp = $archivo->Abrir($path,'r');
		$cont_archivo = $archivo->LeerEnArreglo($fp);
		$archivo->Cerrar($fp);
		$num_rep = count($cont_archivo);

		$linea_tmp = $cont_archivo[0];
		$linea_tmp = explode(",",$linea_tmp);

		$id_sec_word = explode("|",$_POST["id_sector_h"]);
		$id_enf_word = explode("|",$_POST["id_enfoque_h"]);
		$id_pob_word = explode("|",$_POST["id_poblacion_h"]);

		$num_cols_file = count($linea_tmp);
		$num_colum_inicio_csv = 13;
		$num_colum_final_csv = 13;
		$num_cols_form = $num_colum_inicio_csv + $num_colum_final_csv + count($muns_depto) + count($id_sec_word) + count($id_enf_word) + count($id_pob_word);

		if ($num_cols_file != $num_cols_form){
			    ?>
			    <script>
			    alert("El nmero de columnas del archivo CSV no corresponde a los datos suminstrados en la sincronizacin de Enfoques, Sectores o Población, por favor verfiquelos");
			    location.href = 'index.php?accion=importar';
			    </script>
			    <?
		}


		if ($num_rep > 0){
			echo "<table class='tabla_consulta' cellspacing=1 cellpadding=5>";
			echo "<tr><td class='titulo_lista' align='center'>RESUMEN DE IMPORTACION</td></tr>";
			echo "<tr><td>&nbsp;</td></tr>";
		}

		for($r=0;$r<$num_rep;$r++){
			$linea = $cont_archivo[$r];

			//WORD EXPORTA LOS TEXTOS DEL FORMULARIO CON "", SE DEBEN QUITAR
			$linea = str_replace("\"","",$linea);

			$linea = explode(",",$linea);

			if (count($linea > 0) && $linea[0] != ""){
				//NOMBRE
				$org->nom =  $linea[0];

				//SIGLA
				$org->sig =  $linea[1];

				//VIEW
				$org->view =  2;

				//PAPA
				$org->id_papa =  0;

				$org->info_confirmada = 1;

				//ID TIPO
				$tipo = $linea[2];
				$tipo_tmp = $tipo_dao->GetAllArray("NOMB_TIPO_ES='".$tipo."'");
				$org->id_tipo = 1;
				if (count($tipo_tmp) > 0){
					$org->id_tipo = $tipo_tmp[0]->id;
				}

				//MUN SEDE
				$mun_sede = $linea[3];
				$tmp = $mun_dao->GetAllArray("NOM_MUN LIKE '".$mun_sede."'");
				print_r($tmp);
				echo $tmp[0]->id;
				$org->id_mun_sede = '01001';
				if (count($tmp) > 0){
					$org->id_mun_sede = $tmp[0]->id;
				}

				//DIR
				$org->dir =  $linea[4];

				//NACI
				$org->naci = 0;
				if ($linea[5] != ""){
					$org->naci = $linea[5];
				}

				//REP
				$org->n_rep = $linea[6];
				$org->t_rep = $linea[7];

				//EMAIL
				$org->pu_email = $linea[8];

				//WEB
				$org->web = $linea[9];

				//TEL
				$org->tel1 = $linea[10];
				$org->tel2 = $linea[11];
				$org->fax = $linea[12];

				//SECTORES
				$c = 13;
				$as = 0;
				for($s=0;$s<count($id_sec_word);$s++){
					if ($linea[$c + $s] == 1){
						$org->id_sectores[$as] = $id_sec_word[$s];
						$as++;
					}
				}
				$c += count($id_sec_word);

				//ENFOQUE
				$as = 0;
				for($s=0;$s<count($id_enf_word);$s++){
					if ($linea[$c + $s] == 1){
						$org->id_enfoques[$as] = $id_enf_word[$s];
						$as++;
					}
				}
				$c += count($id_enf_word);

				//POBLACION
				$as = 0;
				for($s=0;$s<count($id_pob_word);$s++){
					if ($linea[$c + $s] == 1){
						$org->id_poblaciones[$as] = $id_pob_word[$s];
						$as++;
					}
				}
				$c += count($id_pob_word);

				//COBERTURA MUNICIPIOS
				$as = 0;
				for($s=0;$s<count($muns_depto);$s++){
					if ($linea[$c + $s] == 1){
						$org->id_muns[$as] = $muns_depto[$s];
						$as++;
					}
				}
				$c += count($muns_depto);

				if ($linea[$c] == 1){
					$org->esp_coor = $linea[$c+2];
				}

				//CONSULTA SI EXISTE LA ORG
				$nombre = 0;
				$sigla = 0;

				$sql_o = "SELECT * FROM organizacion WHERE upper(NOM_ORG) = '".strtoupper($org->nom)."'";
				$rs_o = $this->conn->OpenRecordset($sql_o);

				if ($this->conn->RowCount($rs_o) > 0){
					$nombre = 1;
				}

				$sql_s = "SELECT * FROM organizacion WHERE upper(SIG_ORG) = '".strtoupper($org->sig)."'";
				$rs_s = $this->conn->OpenRecordset($sql_s);

				if ($this->conn->RowCount($rs_s) > 0){
					$sigla = 1;
				}

				//NO EXISTE LA ORGANIZACION
				if ($nombre == 0 && $sigla == 0){

					$this->Insertar($org,0);
					$id_org = $this->GetMaxID();
					$this->InsertarTablasUnionCobertura($org,$id_org,1);

					echo "<tr><td>Se creo la Organización: ".$org->nom."</td></tr>";

				}
				//ACTUALIZA LOS DATOS DE LA ORG
				else{

					if ($nombre == 1){
						$row_rs_o = $conn->FetchObject($rs_o);
						$org->id = $row_rs_o->ID_ORG;
						$org->id_mun_sede = $row_rs_o->ID_MUN_SEDE;

					}
					else if($sigla == 1){
						$row_rs_s = $conn->FetchObject($rs_s);
						$org->id = $row_rs_s->ID_ORG;
						$org->id_mun_sede = $row_rs_s->ID_MUN_SEDE;
					}

					//CONSULTA LOS MUNICIPIOS Y DEPARTAMENTOS ACTUALES EN LOS QUE TIENE CONBERTURA
					$org_actual = $org_dao->Get($org->id);
					$org->id_muns = array_merge($org->id_muns,$org_actual->id_muns);
					$org->id_depto = array_merge($org->id_deptos,$org_actual->id_deptos);

					$org->id_mun_sede = $row_rs_s->ID_MUN_SEDE;

					$this->Actualizar($org,0);
					$this->ActualizarCobertura($org,0);

					echo "<tr><td>Se actualiz la Organización: ".$org->nom."</td></tr>";
				}
			}
		}
		die;
	}


	/******************************************************************************
	* Reportes de Administracin
	* @param int $reporte Cual Reporte
	* @param array $filtros Arreglo con los filtros que apliquen para cada reporte
	* @access public
	*******************************************************************************/
	function ReporteAdmin($reporte,$filtros){

		set_time_limit(0);

		//Inicializa Variables
		$mun_dao = New MunicipioDAO();
		$depto_dao = New DeptoDAO();
		$tipo_org_dao = New TipoOrganizacionDAO();
		$sector_dao = New SectorDAO();
		$enfoque_dao = New EnfoqueDAO();
		$poblacion_dao = New PoblacionDAO();

		switch ($reporte){
			//Conteo de Orgs. por Municipio o Depto
			case  1:

				//Filtros
				//Reporte 1: Conteo
				$depto = $filtros['depto'];	//int $depto Por Depto
				$mpio = $filtros['mpio'];  	//int $mpio Por Mpio
				$cual = $filtros['cual'];	//int $cual Contar por: Tipo o Sector o Enfoque o Población

				$num_orgs_detalle = 11;

				//ORGS QUE CUMPLEN CRITERIOS
				$id_orgs_filtros = $this->filtrarOrganizacionesConsulta();
				$num_orgs_filtros = count($id_orgs_filtros);

				//FILTRO POR CNRR
				isset($_POST["cnrr"])	?	$cond_cnrr = ' AND CNRR = '.$_POST["cnrr"]	: $cond_cnrr = '';

				//FILTRO POR CONSULTA SOCIAL
				isset($_POST["consulta_social"])	?	$cond_consulta_social = ' AND CONSULTA_SOCIAL = '.$_POST["consulta_social"]	:	$cond_consulta_social = '';

				//FORMATO REPORTE
				$format = $_POST["format"];

				$cond = '';
				if (isset($_POST["id_tipo_org"])){
					$cond = 'ID_TIPO IN ('.implode(",",$_POST["id_tipo_org"]).')';
				}
				$tipos = $tipo_org_dao->GetAllArray($cond);

				$cond = '';
				if (isset($_POST["id_sector"])){
					$cond = 'ID_COMP IN ('.implode(",",$_POST["id_sector"]).')';
				}
				$sectores = $sector_dao->GetAllArray($cond);

				$cond = '';
				if (isset($_POST["id_enfoque"])){
					$cond = 'ID_ENF IN ('.implode(",",$_POST["id_enfoque"]).')';
				}
				$enfoques = $enfoque_dao->GetAllArray($cond);

				$cond = '';
				if (isset($_POST["id_poblacion"])){
					$cond = 'ID_POBLA IN ('.implode(",",$_POST["id_poblacion"]).')';
				}
				$poblaciones = $poblacion_dao->GetAllArray($cond,'','');

				/*if ($format == 'csv'){
				$file = New Archivo();
				$nom_archivo_tipo = 'org/reportes/conteo_mpio_tipo.txt';
				$nom_archivo_sector = 'org/reportes/conteo_mpio_sector.txt';
				$nom_archivo_enfoque = 'org/reportes/conteo_mpio_enfoque.txt';
				$nom_archivo_poblacion = 'org/reportes/conteo_mpio_poblacion.txt';
				}*/

				echo "<span id='procesando'>Procesando...</span>";

				echo "<table cellpadding='2' style='display:none' id='opciones'>";

				switch ($cual){
					case "tipo" :
						echo "<tr><td class='titulo_lista'>REPORTE POR TIPOS DE ORGANIZACION</td></tr>";

						break;
					case "sector" :
						echo "<tr><td class='titulo_lista'>REPORTE POR SECTORES</td></tr>";

						break;
					case "enfoque" :
						echo "<tr><td class='titulo_lista'>REPORTE POR ENFOQUES</td></tr>";

						break;
					case "poblacion" :
						echo "<tr><td class='titulo_lista'>REPORTE POR POBLACIONES</td></tr>";
						break;
				}

				if ($format == 'csv'){
					echo "<tr><td><img src='../images/consulta/excel.gif'>&nbsp;<a href='../export_data.php?case=reporte_admin_org'>Descargar archivo</a></td></tr>";
				}

				echo "<tr><td>&nbsp;</td></tr>";
				echo "</table>";

				if ($mpio == 1){

					if (isset($_POST["id_muns"])){
						$id_muns = $mun_dao->GetAllArrayID("ID_MUN IN ('".$_POST["id_muns"][0]."')",'ID_MUN');
					}
					else if (!isset($_POST["id_muns"]) && isset($_POST["id_depto"])){
						$id_muns = $mun_dao->GetAllArrayID("ID_DEPTO IN ('".$_POST["id_depto"][0]."')",'ID_MUN');
					}
					else{
						$id_muns = $mun_dao->GetAllArrayID('','ID_MUN');
					}
					switch ($cual){
						//TIPOS
						case "tipo":

							if ($format == 'html'){
								echo "<table cellpadding='2' border=1 id='tabla_tipo'>";
								echo "<tr><td>ID MUN</td>";

								foreach ($tipos as $tipo){
									echo "<td colspan='2'>".$tipo->nombre_es."</td>";
								}
								echo "</tr>";

								echo "<tr><td></td>";
								for($i=0;$i<count($tipos);$i++){
									echo "<td>Sede</td>";
									echo "<td>Cobertura</td>";
								}
								echo "</tr>";
							}

							else {

								$xls = "<table cellpadding='2' border=1 id='tabla_tipo'>";
								$xls .= "<tr><td>ID MUN</td>";

								foreach ($tipos as $tipo){
									$xls .= "<td colspan='2'>".$tipo->nombre_es."</td>";
								}
								$xls .= "</tr>";

								$xls .= "<tr><td></td>";
								for($i=0;$i<count($tipos);$i++){
									$xls .= "<td>Sede</td>";
									$xls .= "<td>Cobertura</td>";
								}
								$xls .= "</tr>";
							}

							$n = 1;
							foreach ($id_muns as $id_mun){

								if ($format == 'html'){
									echo "<tr><td>".$id_mun."</td>";
								}
								else{
									$xls .= "<tr><td>".$id_mun."</td>";
								}

								foreach ($tipos as $tipo){

									//**********
									//SEDE
									//**********
									if ($num_orgs_filtros > 0){
										$sql = "SELECT ID_ORG FROM organizacion WHERE ID_MUN_SEDE = '".$id_mun."' AND ID_TIPO = ".$tipo->id." AND CAPTURA_LOCAL =".$this->captura_local.$cond_cnrr.$cond_consulta_social;
										$rs = $this->conn->OpenRecordset($sql);
										$o = 0;
										$id_orgs = array();
										while ($row_rs = $this->conn->FetchRow($rs)){
											$id_orgs[$o] = $row_rs[0];
											$o++;
										}

										$id_org_final = array_intersect($id_orgs,$id_orgs_filtros);
										$num_orgs_sede = count($id_org_final);
									}
									else{
										$sql = "SELECT count(ID_ORG) FROM organizacion WHERE ID_MUN_SEDE = '".$id_mun."' AND ID_TIPO = ".$tipo->id." AND CAPTURA_LOCAL =".$this->captura_local.$cond_cnrr.$cond_consulta_social;
										$rs = $this->conn->OpenRecordset($sql);
										$num_orgs_sede = $this->conn->RowCount($rs);
									}

									if ($format == 'html'){
										$this->printNumOrgsHtml($num_orgs_sede,'sede',$tipo,$id_org_final,$id_mun);
									}

									//*****************
									//COBERTURA
									//*****************
									if ($num_orgs_filtros > 0){
										$sql = "SELECT mpio_org.ID_ORG FROM mpio_org INNER JOIN organizacion ON mpio_org.ID_ORG = organizacion.ID_ORG WHERE mpio_org.ID_MUN = '".$id_mun."' AND ID_TIPO = ".$tipo->id." AND CAPTURA_LOCAL =".$this->captura_local.$cond_cnrr.$cond_consulta_social;
										$rs = $this->conn->OpenRecordset($sql);
										$o = 0;
										$id_orgs = array();
										while ($row_rs = $this->conn->FetchRow($rs)){
											$id_orgs[$o] = $row_rs[0];
											$o++;
										}
										$id_org_final = array_intersect($id_orgs,$id_orgs_filtros);
										$num_orgs_cob = count($id_org_final);
									}
									else{
										$sql = "SELECT mpio_org.ID_ORG FROM mpio_org INNER JOIN organizacion ON mpio_org.ID_ORG = organizacion.ID_ORG WHERE mpio_org.ID_MUN = '".$id_mun."' AND ID_TIPO = ".$tipo->id." AND CAPTURA_LOCAL =".$this->captura_local.$cond_cnrr.$cond_consulta_social;
										$rs = $this->conn->OpenRecordset($sql);
										$num_orgs_cob = $this->conn->RowCount($rs);
									}

									if ($format == 'html'){
										$this->printNumOrgsHtml($num_orgs_cob,'cobertura',$tipo,$id_org_final,$id_mun);
									}
									else{
										$xls .= "<td>$num_orgs_sede</td><td>$num_orgs_cob</td>";
									}
								}

								echo "</tr>";

							}
							echo "</table>";
							break;

						case "sector":

							//SECTORES
							if ($format == 'html'){
								echo "<table cellpadding='2' border=1 id='tabla_sector'>";
								echo "<tr><td>ID MUN</td>";

								foreach ($sectores as $sector){
									echo "<td colspan='2'>".$sector->nombre_es."</td>";
								}
								echo "</tr>";

								echo "<tr><td></td>";
								for($i=0;$i<count($sectores);$i++){
									echo "<td>Sede</td>";
									echo "<td>Cobertura</td>";
								}
								echo "</tr>";
							}

							else {

								$xls = "<table cellpadding='2' border=1 id='tabla_sector'>";
								$xls .= "<tr><td>ID MUN</td>";

								foreach ($sectores as $sector){
									$xls .= "<td colspan='2'>".$sector->nombre_es."</td>";
								}
								$xls .= "</tr>";

								$xls .= "<tr><td></td>";
								for($i=0;$i<count($sectores);$i++){
									$xls .= "<td>Sede</td>";
									$xls .= "<td>Cobertura</td>";
								}
								$xls .= "</tr>";
							}

							foreach ($id_muns as $id_mun){

								if ($format == 'html'){
									echo "<tr><td>".$id_mun."</td>";
								}
								else{
									$xls .= "<tr><td>".$id_mun."</td>";
								}

								foreach ($sectores as $sector){

									//**********
									//SEDE
									//**********
									if ($num_orgs_filtros > 0){

										$sql = "SELECT organizacion.ID_ORG FROM organizacion INNER JOIN sector_org ON organizacion.ID_ORG = sector_org.ID_ORG  WHERE ID_MUN_SEDE = '$id_mun' AND ID_COMP = $sector->id AND CAPTURA_LOCAL = $this->captura_local $cond_cnrr $cond_consulta_social";
										$rs = $this->conn->OpenRecordset($sql);
										$o = 0;
										$id_orgs = array();
										while ($row_rs = $this->conn->FetchRow($rs)){
											$id_orgs[$o] = $row_rs[0];
											$o++;
										}

										$id_org_final = array_intersect($id_orgs,$id_orgs_filtros);

										//$id_org_final = array_intersect($id_orgs,$id_orgs_filtros);
										$num_orgs_sede = count($id_org_final);
									}
									else{
										$sql = "SELECT count(organizacion.ID_ORG) FROM organizacion INNER JOIN sector_org ON organizacion.ID_ORG = sector_org.ID_ORG  WHERE ID_MUN_SEDE = '$id_mun' AND ID_COMP = $sector->id AND CAPTURA_LOCAL = $this->captura_local $cond_cnrr $cond_consulta_social";
										$rs = $this->conn->OpenRecordset($sql);
										$num_orgs_sede = $this->conn->RowCount($rs);
									}

									if ($format == 'html'){
										$this->printNumOrgsHtml($num_orgs_sede,'sede',$sector,$id_org_final,$id_mun);
									}

									//*****************
									//COBERTURA
									//*****************
									if ($num_orgs_filtros > 0){
										$sql = "SELECT mpio_org.ID_ORG FROM mpio_org INNER JOIN organizacion ON mpio_org.ID_ORG = organizacion.ID_ORG INNER JOIN sector_org ON organizacion.ID_ORG = sector_org.ID_ORG  WHERE mpio_org.ID_MUN = '$id_mun' AND ID_COMP = $sector->id AND CAPTURA_LOCAL = $this->captura_local $cond_cnrr $cond_consulta_social";
										$rs = $this->conn->OpenRecordset($sql);
										$o = 0;
										$id_orgs = array();
										while ($row_rs = $this->conn->FetchRow($rs)){
											$id_orgs[$o] = $row_rs[0];
											$o++;
										}

										$num_orgs_filtros > 0 ? $id_org_final = array_intersect($id_orgs,$id_orgs_filtros) : $id_org_final = $id_orgs;

										//$id_org_final = array_intersect($id_orgs,$id_orgs_filtros);
										$num_orgs_cob = count($id_org_final);
									}
									else{
										$sql = "SELECT count(mpio_org.ID_ORG) FROM mpio_org INNER JOIN organizacion ON mpio_org.ID_ORG = organizacion.ID_ORG INNER JOIN sector_org ON organizacion.ID_ORG = sector_org.ID_ORG  WHERE mpio_org.ID_MUN = '$id_mun' AND ID_COMP = $sector->id AND CAPTURA_LOCAL = $this->captura_local $cond_cnrr $cond_consulta_social";
										$rs = $this->conn->OpenRecordset($sql);
										$num_orgs_cob = $this->conn->RowCount($rs);
									}

									if ($format == 'html'){
										$this->printNumOrgsHtml($num_orgs_cob,'cobertura',$sector,$id_org_final,$id_mun);
									}
									else{
										$xls .= "<td>$num_orgs_sede</td><td>$num_orgs_cob</td>";
									}

								}
								echo "</tr>";

							}
							echo "</table>";
							break;


						case "enfoque" :
							//ENFOQUE
							if ($format == 'html'){
								echo "<table cellpadding='2' border=1 id='tabla_enfoque'>";
								echo "<tr><td>ID MUN</td>";

								foreach ($enfoques as $enfoque){
									echo "<td colspan='2'>".$enfoque->nombre_es."</td>";
								}
								echo "</tr>";

								echo "<tr><td></td>";
								for($i=0;$i<count($enfoques);$i++){
									echo "<td>Sede</td>";
									echo "<td>Cobertura</td>";
								}
								echo "</tr>";
							}

							else {

								$xls = "<table cellpadding='2' border=1 id='tabla_enfoque'>";
								$xls .= "<tr><td>ID MUN</td>";

								foreach ($enfoques as $enfoque){
									$xls .= "<td colspan='2'>".$enfoque->nombre_es."</td>";
								}
								$xls .= "</tr>";

								$xls .= "<tr><td></td>";
								for($i=0;$i<count($enfoques);$i++){
									$xls .= "<td>Sede</td>";
									$xls .= "<td>Cobertura</td>";
								}
								$xls .= "</tr>";
							}


							foreach ($id_muns as $id_mun){

								if ($format == 'html'){
									echo "<tr><td>".$id_mun."</td>";
								}
								else{
									$xls .= "<tr><td>".$id_mun."</td>";
								}

								foreach ($enfoques as $enfoque){

									//**********
									//SEDE
									//**********
									if ($num_orgs_filtros > 0){
										$sql = "SELECT organizacion.ID_ORG FROM organizacion INNER JOIN enf_org ON organizacion.ID_ORG = enf_org.ID_ORG  WHERE ID_MUN_SEDE = '".$id_mun."' AND ID_ENF = ".$enfoque->id." AND CAPTURA_LOCAL =".$this->captura_local;;
										$rs = $this->conn->OpenRecordset($sql);
										$o = 0;
										$id_orgs = array();
										while ($row_rs = $this->conn->FetchRow($rs)){
											$id_orgs[$o] = $row_rs[0];
											$o++;
										}

										$id_org_final = array_intersect($id_orgs,$id_orgs_filtros);
										$num_orgs_sede = count($id_org_final);
									}
									else{
										$sql = "SELECT count(organizacion.ID_ORG) FROM organizacion INNER JOIN enf_org ON organizacion.ID_ORG = enf_org.ID_ORG  WHERE ID_MUN_SEDE = '".$id_mun."' AND ID_ENF = ".$enfoque->id." AND CAPTURA_LOCAL =".$this->captura_local;;
										$rs = $this->conn->OpenRecordset($sql);
										$num_orgs_sede = $this->conn->RowCount($rs);
									}

									if ($format == 'html'){
										$this->printNumOrgsHtml($num_orgs_sede,'sede',$enfoque,$id_org_final,$id_mun);
									}

									//*****************
									//COBERTURA
									//*****************
									if ($num_orgs_filtros > 0){
										$sql = "SELECT mpio_org.ID_ORG FROM mpio_org INNER JOIN organizacion ON mpio_org.ID_ORG = organizacion.ID_ORG INNER JOIN enf_org ON organizacion.ID_ORG = enf_org.ID_ORG  WHERE mpio_org.ID_MUN = '".$id_mun."' AND ID_ENF = ".$enfoque->id." AND CAPTURA_LOCAL =".$this->captura_local.$cond_cnrr.$cond_consulta_social;
										$rs = $this->conn->OpenRecordset($sql);
										$o = 0;
										$id_orgs = array();
										while ($row_rs = $this->conn->FetchRow($rs)){
											$id_orgs[$o] = $row_rs[0];
											$o++;
										}
										$id_org_final = array_intersect($id_orgs,$id_orgs_filtros);
										$num_orgs_cob = count($id_org_final);
									}
									else{
										$sql = "SELECT count(mpio_org.ID_ORG) FROM mpio_org INNER JOIN organizacion ON mpio_org.ID_ORG = organizacion.ID_ORG INNER JOIN enf_org ON organizacion.ID_ORG = enf_org.ID_ORG  WHERE mpio_org.ID_MUN = '".$id_mun."' AND ID_ENF = ".$enfoque->id." AND CAPTURA_LOCAL =".$this->captura_local.$cond_cnrr.$cond_consulta_social;
										$rs = $this->conn->OpenRecordset($sql);
										$num_orgs_cob = $this->conn->RowCount($rs);
									}

									if ($format == 'html'){
										$this->printNumOrgsHtml($num_orgs_cob,'cobertura',$enfoque,$id_org_final,$id_mun);
									}
									else{
										$xls .= "<td>$num_orgs_sede</td><td>$num_orgs_cob</td>";
									}

								}

								echo "</tr>";

							}
							echo "</table>";
							break;

							//echo "<span id='enfoque_ok'><br>Enfoque.... Ok!</span>";

						case "poblacion" :
							//POBLACION
							if ($format == 'html'){
								echo "<table cellpadding='2' border=1 id='tabla_poblacion'>";
								echo "<tr><td>ID MUN</td>";

								foreach ($poblaciones as $poblacion){
									echo "<td colspan='2'>".$poblacion->nombre_es."</td>";
								}
								echo "</tr>";

								echo "<tr><td></td>";
								for($i=0;$i<count($poblaciones);$i++){
									echo "<td>Sede</td>";
									echo "<td>Cobertura</td>";
								}
								echo "</tr>";
							}

							else {

								$xls = "<table cellpadding='2' border=1 id='tabla_poblacion'>";
								$xls .= "<tr><td>ID MUN</td>";

								foreach ($poblaciones as $poblacion){
									$xls .= "<td colspan='2'>".$poblacion->nombre_es."</td>";
								}
								$xls .= "</tr>";

								$xls .= "<tr><td></td>";
								for($i=0;$i<count($poblaciones);$i++){
									$xls .= "<td>Sede</td>";
									$xls .= "<td>Cobertura</td>";
								}
								$xls .= "</tr>";
							}

							foreach ($id_muns as $id_mun){

								if ($format == 'html'){
									echo "<tr><td>".$id_mun."</td>";
								}
								else{
									$xls .= "<tr><td>".$id_mun."</td>";
								}

								foreach ($poblaciones as $poblacion){

									//**********
									//SEDE
									//**********
									if ($num_orgs_filtros > 0){
										$sql = "SELECT organizacion.ID_ORG FROM organizacion INNER JOIN poblacion_org ON organizacion.ID_ORG = poblacion_org.ID_ORG  WHERE ID_MUN_SEDE = '".$id_mun."' AND ID_POB = ".$poblacion->id." AND CAPTURA_LOCAL =".$this->captura_local.$cond_cnrr.$cond_consulta_social;
										$rs = $this->conn->OpenRecordset($sql);
										$o = 0;
										$id_orgs = array();
										while ($row_rs = $this->conn->FetchRow($rs)){
											$id_orgs[$o] = $row_rs[0];
											$o++;
										}

										$id_org_final = array_intersect($id_orgs,$id_orgs_filtros);
										$num_orgs_sede = count($id_org_final);
									}
									else{
										$sql = "SELECT count(organizacion.ID_ORG) FROM organizacion INNER JOIN poblacion_org ON organizacion.ID_ORG = poblacion_org.ID_ORG  WHERE ID_MUN_SEDE = '".$id_mun."' AND ID_POB = ".$poblacion->id." AND CAPTURA_LOCAL =".$this->captura_local.$cond_cnrr.$cond_consulta_social;
										$rs = $this->conn->OpenRecordset($sql);
										$num_orgs_sede = $this->conn->RowCount($rs);
									}

									if ($format == 'html'){
										$this->printNumOrgsHtml($num_orgs_sede,'sede',$poblacion,$id_org_final,$id_mun);
									}

									//*****************
									//COBERTURA
									//*****************
									if ($num_orgs_filtros > 0){
										$sql = "SELECT mpio_org.ID_ORG FROM mpio_org INNER JOIN organizacion ON mpio_org.ID_ORG = organizacion.ID_ORG INNER JOIN poblacion_org ON organizacion.ID_ORG = poblacion_org.ID_ORG  WHERE mpio_org.ID_MUN = '".$id_mun."' AND ID_POB = ".$poblacion->id." AND CAPTURA_LOCAL =".$this->captura_local.$cond_cnrr.$cond_consulta_social;
										$rs = $this->conn->OpenRecordset($sql);
										$o = 0;
										$id_orgs = array();
										while ($row_rs = $this->conn->FetchRow($rs)){
											$id_orgs[$o] = $row_rs[0];
											$o++;
										}
										$id_org_final = array_intersect($id_orgs,$id_orgs_filtros);
										$num_orgs_cob = count($id_org_final);
									}
									else{
										$sql = "SELECT count(mpio_org.ID_ORG) FROM mpio_org INNER JOIN organizacion ON mpio_org.ID_ORG = organizacion.ID_ORG INNER JOIN poblacion_org ON organizacion.ID_ORG = poblacion_org.ID_ORG  WHERE mpio_org.ID_MUN = '".$id_mun."' AND ID_POB = ".$poblacion->id." AND CAPTURA_LOCAL =".$this->captura_local.$cond_cnrr.$cond_consulta_social;
										$rs = $this->conn->OpenRecordset($sql);
										$num_orgs_cob = $this->conn->RowCount($rs);
									}

									if ($format == 'html'){
										$this->printNumOrgsHtml($num_orgs_cob,'cobertura',$poblacion,$id_org_final,$id_mun);
									}
									else{
//										$linea .= "|".$num_orgs_sede."|".$num_orgs_cob;
										$xls .= "<td>$num_orgs_sede</td><td>$num_orgs_cob</td>";
									}
								}
								echo "</tr>";

							}
							echo "</table>";
							break;
					}
				}
				//**********
				//DEPTOS
				//**********
				else if ($depto == 1){

					$cond = '';
					if (isset($_POST["id_depto"])){
						$cond = 'ID_DEPTO IN ('.implode(",",$_POST["id_depto"]).')';
					}

					$id_deptos = $depto_dao->GetAllArrayID($cond);

					switch ($cual){
						case "tipo" :
							//TIPOS
							if ($format == 'html'){
								echo "<table cellpadding='2' border=1 id='tabla_tipo'>";
								echo "<tr><td>ID DEPTO</td>";

								foreach ($tipos as $tipo){
									echo "<td colspan='2'>".$tipo->nombre_es."</td>";
								}
								echo "</tr>";

								echo "<tr><td></td>";
								for($i=0;$i<count($tipos);$i++){
									echo "<td>Sede</td>";
									echo "<td>Cobertura</td>";
								}
								echo "</tr>";
							}

							else {

								$xls = "<table cellpadding='2' border=1 id='tabla_tipo'>";
								$xls .= "<tr><td>ID DEPTO</td>";

								foreach ($tipos as $tipo){
									$xls .= "<td colspan='2'>".$tipo->nombre_es."</td>";
								}
								$xls .= "</tr>";

								$xls .= "<tr><td></td>";
								for($i=0;$i<count($tipos);$i++){
									$xls .= "<td>Sede</td>";
									$xls .= "<td>Cobertura</td>";
								}
								$xls .= "</tr>";
							}

							foreach ($id_deptos as $id_depto){

								if ($format == 'html'){
									echo "<tr><td>".$id_depto."</td>";
								}
								else{
									$xls .= "<tr><td>".$id_depto."</td>";
								}

								foreach ($tipos as $tipo){

									//**********
									//SEDE
									//**********
									if ($num_orgs_filtros > 0){
										$sql = "SELECT ID_ORG FROM organizacion INNER JOIN municipio ON organizacion.ID_MUN_SEDE = municipio.ID_MUN WHERE ID_DEPTO = '".$id_depto."' AND ID_TIPO = ".$tipo->id." AND CAPTURA_LOCAL =".$this->captura_local.$cond_cnrr.$cond_consulta_social;
										$rs = $this->conn->OpenRecordset($sql);
										$o = 0;
										$id_orgs = array();
										while ($row_rs = $this->conn->FetchRow($rs)){
											$id_orgs[$o] = $row_rs[0];
											$o++;
										}

										$id_org_final = array_intersect($id_orgs,$id_orgs_filtros);
										$num_orgs_sede = count($id_org_final);
									}
									else{
										$sql = "SELECT count(ID_ORG FROM) organizacion INNER JOIN municipio ON organizacion.ID_MUN_SEDE = municipio.ID_MUN WHERE ID_DEPTO = '".$id_depto."' AND ID_TIPO = ".$tipo->id." AND CAPTURA_LOCAL =".$this->captura_local.$cond_cnrr.$cond_consulta_social;
										$rs = $this->conn->OpenRecordset($sql);
										$num_orgs_sede = $this->conn->RowCount($rs);
									}

									if ($format == 'html'){
										$this->printNumOrgsHtml($num_orgs_sede,'sede',$tipo,$id_org_final,$id_depto);
									}

									//*****************
									//COBERTURA
									//*****************
									if ($num_orgs_filtros > 0){
										$sql = "SELECT depto_org.ID_ORG FROM depto_org INNER JOIN organizacion ON depto_org.ID_ORG = organizacion.ID_ORG WHERE depto_org.ID_DEPTO = '".$id_depto."' AND ID_TIPO = ".$tipo->id." AND CAPTURA_LOCAL =".$this->captura_local.$cond_cnrr.$cond_consulta_social;
										$rs = $this->conn->OpenRecordset($sql);
										$o = 0;
										$id_orgs = array();
										while ($row_rs = $this->conn->FetchRow($rs)){
											$id_orgs[$o] = $row_rs[0];
											$o++;
										}
										$id_org_final = array_intersect($id_orgs,$id_orgs_filtros);
										$num_orgs_cob = count($id_org_final);
									}
									else{
										$sql = "SELECT depto_org.ID_ORG FROM depto_org INNER JOIN organizacion ON depto_org.ID_ORG = organizacion.ID_ORG WHERE depto_org.ID_DEPTO = '".$id_depto."' AND ID_TIPO = ".$tipo->id." AND CAPTURA_LOCAL =".$this->captura_local.$cond_cnrr.$cond_consulta_social;
										$rs = $this->conn->OpenRecordset($sql);
										$num_orgs_cob = $this->conn->RowCount($rs);
									}

									if ($format == 'html'){
										$this->printNumOrgsHtml($num_orgs_cob,'cobertura',$tipo,$id_org_final,$id_depto);
									}

									if ($format == 'csv'){
										$xls .= "<td>$num_orgs_sede</td><td>$num_orgs_cob</td>";
									}

								}

								echo "</tr>";

							}
							echo "</table>";
							break;

							//echo "<span id='tipo_ok'><br>Tipo.... Ok!</span>";
						case "sector" :
							//SECTORES
							if ($format == 'html'){
								echo "<table cellpadding='2' border=1 id='tabla_sector'>";
								echo "<tr><td>ID DEPTO</td>";

								foreach ($sectores as $sector){
									echo "<td colspan='2'>".$sector->nombre_es."</td>";
								}
								echo "</tr>";

								echo "<tr><td></td>";
								for($i=0;$i<count($sectores);$i++){
									echo "<td>Sede</td>";
									echo "<td>Cobertura</td>";
								}
								echo "</tr>";
							}

							else {

								$xls = "<table cellpadding='2' border=1 id='tabla_sector'>";
								$xls .= "<tr><td>ID DEPTO</td>";

								foreach ($sectores as $sector){
									$xls .= "<td colspan='2'>".$sector->nombre_es."</td>";
								}
								$xls .= "</tr>";

								$xls .= "<tr><td></td>";
								for($i=0;$i<count($sectores);$i++){
									$xls .= "<td>Sede</td>";
									$xls .= "<td>Cobertura</td>";
								}
								$xls .= "</tr>";
							}

							foreach ($id_deptos as $id_depto){

								if ($format == 'html'){
									echo "<tr><td>".$id_depto."</td>";
								}
								else{
									$xls .= "<tr><td>".$id_depto."</td>";
								}

								foreach ($sectores as $sector){

									//**********
									//SEDE
									//**********
									if ($num_orgs_filtros > 0){
										$sql = "SELECT organizacion.ID_ORG FROM organizacion INNER JOIN sector_org ON organizacion.ID_ORG = sector_org.ID_ORG INNER JOIN municipio ON organizacion.ID_MUN_SEDE = municipio.ID_MUN WHERE ID_MUN_SEDE = '".$id_depto."' AND ID_COMP = ".$sector->id." AND CAPTURA_LOCAL =".$this->captura_local.$cond_cnrr.$cond_consulta_social;
										$rs = $this->conn->OpenRecordset($sql);
										$o = 0;
										$id_orgs = array();
										while ($row_rs = $this->conn->FetchRow($rs)){
											$id_orgs[$o] = $row_rs[0];
											$o++;
										}

										$id_org_final = array_intersect($id_orgs,$id_orgs_filtros);
										$num_orgs_sede = count($id_org_final);
									}
									else{
										$sql = "SELECT count(organizacion.ID_ORG) FROM organizacion INNER JOIN sector_org ON organizacion.ID_ORG = sector_org.ID_ORG INNER JOIN municipio ON organizacion.ID_MUN_SEDE = municipio.ID_MUN WHERE ID_MUN_SEDE = '".$id_depto."' AND ID_COMP = ".$sector->id." AND CAPTURA_LOCAL =".$this->captura_local.$cond_cnrr.$cond_consulta_social;
										$rs = $this->conn->OpenRecordset($sql);
										$num_orgs_sede = $this->conn->RowCount($rs);
									}

									if ($format == 'html'){
										$this->printNumOrgsHtml($num_orgs_sede,'sede',$sector,$id_org_final,$id_depto);
									}

									//*****************
									//COBERTURA
									//*****************
									if ($num_orgs_filtros > 0){
										$sql = "SELECT depto_org.ID_ORG FROM depto_org INNER JOIN organizacion ON depto_org.ID_ORG = organizacion.ID_ORG INNER JOIN sector_org ON organizacion.ID_ORG = sector_org.ID_ORG  WHERE depto_org.ID_DEPTO = '".$id_depto."' AND ID_COMP = ".$sector->id." AND CAPTURA_LOCAL =".$this->captura_local.$cond_cnrr.$cond_consulta_social;
										$rs = $this->conn->OpenRecordset($sql);
										$o = 0;
										$id_orgs = array();
										while ($row_rs = $this->conn->FetchRow($rs)){
											$id_orgs[$o] = $row_rs[0];
											$o++;
										}
										$id_org_final = array_intersect($id_orgs,$id_orgs_filtros);
										$num_orgs_cob = count($id_org_final);
									}
									else{
										$sql = "SELECT count(depto_org.ID_ORG) FROM depto_org INNER JOIN organizacion ON depto_org.ID_ORG = organizacion.ID_ORG INNER JOIN sector_org ON organizacion.ID_ORG = sector_org.ID_ORG  WHERE depto_org.ID_DEPTO = '".$id_depto."' AND ID_COMP = ".$sector->id." AND CAPTURA_LOCAL =".$this->captura_local.$cond_cnrr.$cond_consulta_social;
										$rs = $this->conn->OpenRecordset($sql);
										$num_orgs_cob = $this->conn->RowCount($rs);
									}

									if ($format == 'html'){
										$this->printNumOrgsHtml($num_orgs_cob,'cobertura',$sector,$id_org_final,$id_depto);
									}

									if ($format == 'csv'){
										$xls .= "<td>$num_orgs_sede</td><td>$num_orgs_cob</td>";
									}

								}

								echo "</tr>";

							}
							echo "</table>";
							break;

							//echo "<span id='sector_ok'><br>Sector.... Ok!</span>";

						case "enfoque":
							//ENFOQUE
							if ($format == 'html'){
								echo "<table cellpadding='2' border=1 id='tabla_enfoque'>";
								echo "<tr><td>ID DEPTO</td>";

								foreach ($enfoques as $enfoque){
									echo "<td colspan='2'>".$enfoque->nombre_es."</td>";
								}
								echo "</tr>";

								echo "<tr><td></td>";
								for($i=0;$i<count($enfoques);$i++){
									echo "<td>Sede</td>";
									echo "<td>Cobertura</td>";
								}
								echo "</tr>";
							}

							else {

								$xls = "<table cellpadding='2' border=1 id='tabla_enfoque'>";
								$xls .= "<tr><td>ID DEPTO</td>";

								foreach ($enfoques as $enfoque){
									$xls .= "<td colspan='2'>".$enfoque->nombre_es."</td>";
								}
								$xls .= "</tr>";

								$xls .= "<tr><td></td>";
								for($i=0;$i<count($enfoques);$i++){
									$xls .= "<td>Sede</td>";
									$xls .= "<td>Cobertura</td>";
								}
								$xls .= "</tr>";
							}

							foreach ($id_deptos as $id_depto){

								if ($format == 'html'){
									echo "<tr><td>".$id_depto."</td>";
								}
								else{
									$xls .= "<tr><td>".$id_depto."</td>";
								}

								foreach ($enfoques as $enfoque){

									//**********
									//SEDE
									//**********
									if ($num_orgs_filtros > 0){
										$sql = "SELECT organizacion.ID_ORG FROM organizacion INNER JOIN enf_org ON organizacion.ID_ORG = enf_org.ID_ORG INNER JOIN municipio ON organizacion.ID_MUN_SEDE = municipio.ID_MUN WHERE ID_MUN_SEDE = '".$id_depto."' AND ID_ENF = ".$enfoque->id." AND CAPTURA_LOCAL =".$this->captura_local.$cond_cnrr.$cond_consulta_social;
										$rs = $this->conn->OpenRecordset($sql);
										$o = 0;
										$id_orgs = array();
										while ($row_rs = $this->conn->FetchRow($rs)){
											$id_orgs[$o] = $row_rs[0];
											$o++;
										}

										$id_org_final = array_intersect($id_orgs,$id_orgs_filtros);
										$num_orgs_sede = count($id_org_final);
									}
									else{
										$sql = "SELECT count(organizacion.ID_ORG) FROM organizacion INNER JOIN enf_org ON organizacion.ID_ORG = enf_org.ID_ORG INNER JOIN municipio ON organizacion.ID_MUN_SEDE = municipio.ID_MUN WHERE ID_MUN_SEDE = '".$id_depto."' AND ID_ENF = ".$enfoque->id." AND CAPTURA_LOCAL =".$this->captura_local.$cond_cnrr.$cond_consulta_social;
										$rs = $this->conn->OpenRecordset($sql);
										$num_orgs_sede = $this->conn->RowCount($rs);
									}

									if ($format == 'html'){
										$this->printNumOrgsHtml($num_orgs_sede,'sede',$enfoque,$id_org_final,$id_depto);
									}

									//*****************
									//COBERTURA
									//*****************
									if ($num_orgs_filtros > 0){
										$sql = "SELECT depto_org.ID_ORG FROM depto_org INNER JOIN organizacion ON depto_org.ID_ORG = organizacion.ID_ORG INNER JOIN enf_org ON organizacion.ID_ORG = enf_org.ID_ORG  WHERE depto_org.ID_DEPTO = '".$id_depto."' AND ID_ENF = ".$enfoque->id." AND CAPTURA_LOCAL =".$this->captura_local.$cond_cnrr.$cond_consulta_social;
										$rs = $this->conn->OpenRecordset($sql);
										$o = 0;
										$id_orgs = array();
										while ($row_rs = $this->conn->FetchRow($rs)){
											$id_orgs[$o] = $row_rs[0];
											$o++;
										}
										$id_org_final = array_intersect($id_orgs,$id_orgs_filtros);
										$num_orgs_cob = count($id_org_final);
									}
									else{
										$sql = "SELECT count(depto_org.ID_ORG) FROM depto_org INNER JOIN organizacion ON depto_org.ID_ORG = organizacion.ID_ORG INNER JOIN enf_org ON organizacion.ID_ORG = enf_org.ID_ORG  WHERE depto_org.ID_DEPTO = '".$id_depto."' AND ID_ENF = ".$enfoque->id." AND CAPTURA_LOCAL =".$this->captura_local.$cond_cnrr.$cond_consulta_social;
										$rs = $this->conn->OpenRecordset($sql);
										$num_orgs_cob = $this->conn->RowCount($rs);
									}

									if ($format == 'html'){
										$this->printNumOrgsHtml($num_orgs_cob,'cobertura',$enfoque,$id_org_final,$id_depto);
									}
									else{
										$xls .= "<td>$num_orgs_sede</td><td>$num_orgs_cob</td>";
									}
								}

								echo "</tr>";

							}
							echo "</table>";
							break;

							//echo "<span id='enfoque_ok'><br>Enfoque.... Ok!</span>";

						case "poblacion" :
							//POBLACION
							if ($format == 'html'){
								echo "<table cellpadding='2' border=1 id='tabla_poblacion'>";
								echo "<tr><td>ID DEPTO</td>";

								foreach ($poblaciones as $poblacion){
									echo "<td colspan='2'>".$poblacion->nombre_es."</td>";
								}
								echo "</tr>";

								echo "<tr><td></td>";
								for($i=0;$i<count($poblaciones);$i++){
									echo "<td>Sede</td>";
									echo "<td>Cobertura</td>";
								}
								echo "</tr>";
							}

							else {

								$xls = "<table cellpadding='2' border=1 id='tabla_poblacion'>";
								$xls .= "<tr><td>ID DEPTO</td>";

								foreach ($poblaciones as $poblacion){
									$xls .= "<td colspan='2'>".$poblacion->nombre_es."</td>";
								}
								$xls .= "</tr>";

								$xls .= "<tr><td></td>";
								for($i=0;$i<count($poblaciones);$i++){
									$xls .= "<td>Sede</td>";
									$xls .= "<td>Cobertura</td>";
								}
								$xls .= "</tr>";
							}

							foreach ($id_deptos as $id_depto){

								if ($format == 'html'){
									echo "<tr><td>".$id_depto."</td>";
								}
								else{
									$xls .= "<tr><td>".$id_depto."</td>";
								}

								foreach ($poblaciones as $poblacion){

									//**********
									//SEDE
									//**********
									if ($num_orgs_filtros > 0){
										$sql = "SELECT organizacion.ID_ORG FROM organizacion INNER JOIN poblacion_org ON organizacion.ID_ORG = poblacion_org.ID_ORG INNER JOIN municipio ON organizacion.ID_MUN_SEDE = municipio.ID_MUN WHERE ID_MUN_SEDE = '".$id_depto."' AND ID_POB = ".$poblacion->id." AND CAPTURA_LOCAL =".$this->captura_local.$cond_cnrr.$cond_consulta_social;
										$rs = $this->conn->OpenRecordset($sql);
										$o = 0;
										$id_orgs = array();
										while ($row_rs = $this->conn->FetchRow($rs)){
											$id_orgs[$o] = $row_rs[0];
											$o++;
										}

										$id_org_final = array_intersect($id_orgs,$id_orgs_filtros);
										$num_orgs_sede = count($id_org_final);
									}
									else{
										$sql = "SELECT count(organizacion.ID_ORG) FROM organizacion INNER JOIN poblacion_org ON organizacion.ID_ORG = poblacion_org.ID_ORG INNER JOIN municipio ON organizacion.ID_MUN_SEDE = municipio.ID_MUN WHERE ID_MUN_SEDE = '".$id_depto."' AND ID_POB = ".$poblacion->id." AND CAPTURA_LOCAL =".$this->captura_local.$cond_cnrr.$cond_consulta_social;
										$rs = $this->conn->OpenRecordset($sql);
										$num_orgs_sede = $this->conn->RowCount($rs);
									}

									if ($format == 'html'){
										$this->printNumOrgsHtml($num_orgs_sede,'sede',$poblacion,$id_org_final,$id_depto);
									}

									//*****************
									//COBERTURA
									//*****************
									if ($num_orgs_filtros > 0){
										$sql = "SELECT depto_org.ID_ORG FROM depto_org INNER JOIN organizacion ON depto_org.ID_ORG = organizacion.ID_ORG INNER JOIN poblacion_org ON organizacion.ID_ORG = poblacion_org.ID_ORG  WHERE depto_org.ID_DEPTO = '".$id_depto."' AND ID_POB = ".$poblacion->id." AND CAPTURA_LOCAL =".$this->captura_local.$cond_cnrr.$cond_consulta_social;
										$rs = $this->conn->OpenRecordset($sql);
										$o = 0;
										$id_orgs = array();
										while ($row_rs = $this->conn->FetchRow($rs)){
											$id_orgs[$o] = $row_rs[0];
											$o++;
										}
										$id_org_final = array_intersect($id_orgs,$id_orgs_filtros);
										$num_orgs_cob = count($id_org_final);
									}
									else{
										$sql = "SELECT count(depto_org.ID_ORG) FROM depto_org INNER JOIN organizacion ON depto_org.ID_ORG = organizacion.ID_ORG INNER JOIN poblacion_org ON organizacion.ID_ORG = poblacion_org.ID_ORG  WHERE depto_org.ID_DEPTO = '".$id_depto."' AND ID_POB = ".$poblacion->id." AND CAPTURA_LOCAL =".$this->captura_local.$cond_cnrr.$cond_consulta_social;
										$rs = $this->conn->OpenRecordset($sql);
										$num_orgs_cob = $this->conn->RowCount($rs);
									}

									if ($format == 'html'){
										$this->printNumOrgsHtml($num_orgs_cob,'cobertura',$poblacion,$id_org_final,$id_depto);
									}
									else{
										$xls .= "<td>$num_orgs_sede</td><td>$num_orgs_cob</td>";
									}
								}

								echo "</tr>";

							}
							echo "</table>";
							break;
					}
				}

				if ($format == 'csv'){
					$_SESSION["reporte_admin_org"] = $xls;
					//echo $xls;
				}

				echo "<script>document.getElementById('procesando').style.display = 'none';document.getElementById('opciones').style.display = '';</script>";

			break;
			//REPORTE DIRECTORIO
			case 2:
				//LIBRERIAS
				$pdf = new Cezpdf();

				$tipo_dao = New TipoOrganizacionDAO();

				//TODAS
				$todas = 0;
				if (isset($_POST["todas"]) && $_POST["todas"] == 1 && !isset($_POST["id_depto"])){
					$todas = 1;

					$arr_id = $this->GetAllArrayID('','','');
				}
				else{
					$arr_id = $this->filtrarOrganizacionesConsulta();
				}

				$num_arr = count($arr_id);

				if ($num_arr > 0){

					//ORDENA ALBAETICAMENTE LOS IDs, CONSULTANDO LOS NOMBRES DE LAS ORGS
					foreach ($arr_id as $id){
						$org = $this->Get($id);
						$arr_nom[$id] = $org->nom;
					}

					asort($arr_nom);
					$a = 0;
					foreach ($arr_nom as $id => $nom){
						$arr_id[$a] = $id;
						$a++;
					}

					$num_max_caracteres = 65;
					$pdf -> ezSetMargins(150,70,20,20);
					$pdf->selectFont('lib/common/PDFfonts/Helvetica.afm');

					// Coloca el logo y el pie en todas las pginas
					$all = $pdf->openObject();
					$pdf->saveState();

					$img_att = getimagesize('../images/logos/enc_reporte_semanal.jpg');
					$pdf->addPngFromFile('../images/logos/enc_reporte_semanal.png',30,550,$img_att[0]/2,$img_att[1]/2);

					//HEADER
					$pdf->addText(250,570,14,'<b>NACIONES UNIADAS - COLOMBIA</b>');
					$pdf->addText(100,550,14,'<b>OFICINA PARA LA COORDINACION DE ASUNTOS HIMANITARIOS- OCHA</b>');
					$pdf->addText(100,530,12,'PRESENCIA DE ORGANIZACIONES POR SECTOR, ENFOQUE Y COBERTURA GEOGRAFICA');

					$pdf->setLineStyle(1);
					$pdf->line(50,520,710,520);

					$pdf->restoreState();
					$pdf->closeObject();
					$pdf->addObject($all,'all');
					$pdf->ezSetY(490);

					$p = 0;
					$p_total = 0;
					foreach ($arr_id as $id){

						$org = $this->Get($id);

						//NUEVA PAGINA CADA 4 ORGS.
						if (fmod($p,4) == 0){
							if ($p_total > 0){
								//$pdf->ezColumnsStop();
								//$pdf->ezNewPage();
								//$pdf->ezSetY(490);
							}

							//$pdf->ezColumnsStart(array('num'=>2,'gap'=>2));

							$first_l = substr($org->nom,0,1);
							//LETRA DEL DIRECTORIO EN EL HEADER
							$x_c = 710;
							$y_c = 580;
							$lado = 60;
							$pdf->line($x_c,$y_c,$x_c+$lado,$y_c);  //Superior
							$pdf->line($x_c+$lado,$y_c-$lado,$x_c+$lado,$y_c);  //Derecha
							$pdf->line($x_c,$y_c-$lado,$x_c+$lado,$y_c-$lado);  //Inferior
							$pdf->line($x_c,$y_c-$lado,$x_c,$y_c);  //Iz
							$pdf->addText($x_c+10,$y_c-50,54,$first_l);

							$y = 490;
							$x = 50;

							$p = 0;
						}

						if ($p == 1){
							$x = 420;
							$y = 490;
						}
						else if ($p == 2){
							$x = 50;
							$y = 315;
						}
						else if ($p == 3){
							$x = 420;
							$y = 315;
						}

						//NOMBRE
						if ($org->nom != ""){

							$pdf->ezText("<b>".$org->nom."</b>",14);
							$pdf->ezSetDy(-10);

//							$pdf->addText($x,$y,12,"<b>".$org->nom."</b>");
//							$y-=10;

							//NOMBRE DEL TIPO DE ORGANIZACION
							$tipo = $tipo_dao->Get($org->id_tipo);
							$nom_tipo = $tipo->nombre_es;

							$pdf->ezText($nom_tipo,12);
							$pdf->ezSetDy(-10);

//							$pdf->addText($x,$y,12,$nom_tipo);
//							$y-=10;

							//SECTORES
							$pdf->ezText("<b>Sector(es) en que trabaja:</b>",12);
							$pdf->ezSetDy(-10);

//							$pdf->addText($x,$y,12,"<b>Sector(es) en que trabaja:</b>");
//							$y-=10;

							$s = 0;
							foreach($org->id_sectores as $id){
								if (fmod($s,8) == 0)	echo "<td class='tabla_consulta'>";
								$vo = $sector_dao->Get($id);

								($s == 0) ? $txt = $vo->nombre_es : $txt .= "-".$vo->nombre_es;

								$s++;
							}

							$pdf->ezText($txt,12);
							$pdf->ezSetDy(-10);

//							$pdf->addText($x,$y,10,$sector_txt);
//							$y-=10;

							//ENFOQUE
							$pdf->ezText("<b>Enfoque(s) de dela Organización :</b>",12);
							$pdf->ezSetDy(-10);

							$s = 0;
							foreach($org->id_enfoques as $id){
								if (fmod($s,8) == 0)	echo "<td class='tabla_consulta'>";
								$vo = $enfoque_dao->Get($id);

								($s == 0) ? $txt = $vo->nombre_es : $txt .= "-".$vo->nombre_es;

								$s++;
							}
							$pdf->ezText($txt,12);
							$pdf->ezSetDy(-10);

							//POBLACION
							$pdf->ezText("<b>Población Beneficiaria:</b>",12);
							$pdf->ezSetDy(-10);

							$s = 0;
							foreach($org->id_poblaciones as $id){
								if (fmod($s,8) == 0)	echo "<td class='tabla_consulta'>";
								$vo = $poblacion_dao->Get($id);

								($s == 0) ? $txt = $vo->nombre_es : $txt .= "-".$vo->nombre_es;

								$s++;
							}

							$pdf->ezText($txt,12);
							$pdf->ezSetDy(-10);

							//COBERTURA POR DEPARTAMENTO
							/*$pdf->ezText("<b>Cobertura Geográfica:</b>",12);
							$pdf->ezSetDy(-5);

							$s = 0;
							foreach($org->id_deptos as $id){
								if (fmod($s,8) == 0)	echo "<td class='tabla_consulta'>";
								$vo = $depto_dao->Get($id);

								($s == 0) ? $pdf->ezText($vo->nombre,12) : $pdf->ezText("-".$vo->nombre,12);
								$pdf->ezSetDy(-10);

								$s++;
							}				*/

							$p++;
							$p_total++;
						}
					}

				}
				else{
					echo "<tr><td align='center'><br><b>NO SE ENCONTRARON ORGANIZACIONES</b></td></tr>";
					die;
				}


				//ALMACENA EL CODIGO PDF EN SESION
				$_SESSION["pdf_code"] = $pdf->ezOutput();
				echo "<script>location.href='../export_data.php?case=org_reporte_admin_2&pdf=1';</script>";
			break;
		}
	}

	/******************************************************************************
	* Imprime en HTML el numero de organizaciones
	* @param int num Número de Orgs
	* @param string $sede_cob De Sede o de Cobertura
	* @param VO $vo Tipo, Sector, Enfoque, Población
	* @param Array $id_org_final Id de las Orgs
	* @param int $id_mun Id del Mpio
	* @access public
	*******************************************************************************/
	function printNumOrgsHtml($num,$sede_cob,$vo,$id_org_final,$id_mun){

		$num_orgs_detalle = 11;

		$num > 0 ? $style = 'bcg_E1FF6C_0000FF' : $style = '';

		echo "<td class='".$style."'>";
		if ($num < $num_orgs_detalle){
			echo "<a href='#' onclick=\"ShowHide('".$sede_cob."_".$vo->id."_".$id_mun."');return false;\">".$num."</a><div id='".$sede_cob."_".$vo->id."_".$id_mun."' style='display:none'><br><b>Orgs</b>:";

			$i = 0;
			foreach ($id_org_final as $id){
				if (fmod($i,5) == 0)	echo "<br>";
				echo "<a href='#' onclick=\"window.open('ver.php?class=OrganizacionDAO&method=Ver&param=".$id."','','top=30,left=30,height=900,width=900,scrollbars=1');return false;\">".$id."</a>&nbsp";
				$i++;
			}
			echo "</div></td>";
		}
		else{
			echo $num;
		}
	}

	/******************************************************************************
	* Cuenta el número de Orgs de un Tipo o Enfoque o Poblacion o Sector
	* @param string $case Tipo o Enfoque o Poblacion o Sector
	* @param int $id Id de Tipo o Enfoque o Poblacion o Sector
	* @param boolean $depto 1=Departamento , 0=Municipio, 2=Nacional
	* @param string $ubicacion ID de la ubiaccion
	* @param int Condicion de Mapp Oea
	* @param int Condicion de Consulta Social
	* @return array $num_orgs    array('sede'=>num,'cobertura'=>num,'total'=>num,'id_total'=>array)
	* @access public
	*******************************************************************************/
	function numOrgsConteo($case,$id,$depto,$ubicacion,$cond_mapp_oea,$cond_consulta_social){

		//INICIALIZACION DE VARIABLES
		$mun_dao = New MunicipioDAO();
		$num_sede = 0;
		$num_cobertura = 0;
		$num_total = 0;
		$id_sede = array();
		$id_total = array();
		$cond_papa = " AND ID_ORG_PAPA = 0";
		$order_by = " ORDER BY NOM_ORG";
		if ($depto == 1)	$id_muns = $mun_dao->GetIDWhere($ubicacion);

		switch ($case){

			case 'tipo':

				//SEDE
				$id_sede = array();
				$sql = "SELECT ID_ORG FROM organizacion WHERE ID_MUN_SEDE = '$ubicacion' AND ID_TIPO = $id";
				if ($depto == 1)	$sql = "SELECT ID_ORG FROM organizacion WHERE ID_MUN_SEDE IN ($id_muns) AND ID_TIPO = $id";
				else if ($depto == 2)	$sql = "SELECT ID_ORG FROM organizacion WHERE ID_TIPO = $id";

				$sql .= $cond_mapp_oea.$cond_consulta_social.$cond_papa.$order_by;

				$rs = $this->conn->OpenRecordset($sql);
				while ($row_rs = $this->conn->FetchRow($rs)){
					$num_sede++;
					$id_sede[] = $row_rs[0];
				}

				$num_total = count($id_sede);
				$id_total = $id_sede;

				//COBERTURA
				$sql = "SELECT mpio_org.ID_ORG FROM mpio_org JOIN organizacion USING(ID_ORG) WHERE mpio_org.ID_MUN = '$ubicacion' AND ID_TIPO = $id";
				if ($depto == 1)	$sql = "SELECT depto_org.ID_ORG FROM depto_org JOIN organizacion USING(ID_ORG) WHERE depto_org.ID_DEPTO = '$ubicacion' AND ID_TIPO = $id";
				else if ($depto == 2)	$sql = "SELECT ID_ORG FROM organizacion WHERE ID_TIPO = $id";

				$sql .= $cond_mapp_oea.$cond_consulta_social.$cond_papa.$order_by;

				$rs = $this->conn->OpenRecordset($sql);
				while ($row_rs = $this->conn->FetchRow($rs)){
					$num_cobertura++;
					if (!in_array($row_rs[0],$id_sede)){
						$id_total[] = $row_rs[0];
						$num_total++;
					}
				}

				$num_orgs['sede'] = $num_sede;
				$num_orgs['cobertura'] = $num_cobertura;
				$num_orgs['total'] = $num_total;
				$num_orgs['id_total'] = $id_total;

				break;

			case 'sector':

				//SEDE
				$sql = "SELECT organizacion.ID_ORG FROM organizacion JOIN sector_org USING(ID_ORG) WHERE ID_MUN_SEDE = '$ubicacion' AND ID_COMP = $id";
				if ($depto == 1)	$sql = "SELECT organizacion.ID_ORG FROM organizacion JOIN sector_org USING(ID_ORG) WHERE ID_MUN_SEDE IN ($id_muns) AND ID_COMP = $id";
				else if ($depto == 2)	$sql = "SELECT ID_ORG FROM sector_org LEFT JOIN $this->tabla USING($this->columna_id) WHERE ID_COMP = $id";

				$sql .= $cond_mapp_oea.$cond_consulta_social.$cond_papa.$order_by;

				$rs = $this->conn->OpenRecordset($sql);
				while ($row_rs = $this->conn->FetchRow($rs)){
					$num_sede++;
					$id_sede[] = $row_rs[0];
				}

				$num_total = count($id_sede);
				$id_total = $id_sede;

				//COBERTURA
				$sql = "SELECT mpio_org.ID_ORG FROM mpio_org JOIN organizacion USING(ID_ORG) JOIN sector_org USING(ID_ORG) WHERE mpio_org.ID_MUN = '$ubicacion' AND ID_COMP = $id";
				//Depto
				if ($depto == 1)	$sql = "SELECT depto_org.ID_ORG FROM depto_org JOIN organizacion USING(ID_ORG) JOIN sector_org USING(ID_ORG) WHERE depto_org.ID_DEPTO = '$ubicacion' AND ID_COMP = $id";
				//Nacional
				else if ($depto == 2)	$sql = "SELECT ID_ORG FROM sector_org LEFT JOIN $this->tabla USING($this->columna_id) WHERE ID_COMP = $id";

				$sql .= $cond_mapp_oea.$cond_consulta_social.$cond_papa.$order_by;

				$rs = $this->conn->OpenRecordset($sql);
				while ($row_rs = $this->conn->FetchRow($rs)){
					$num_cobertura++;
					if (!in_array($row_rs[0],$id_sede)){
						$id_total[] = $row_rs[0];
						$num_total++;
					}
				}

				$num_orgs['sede'] = $num_sede;
				$num_orgs['cobertura'] = $num_cobertura;
				$num_orgs['total'] = $num_total;
				$num_orgs['id_total'] = $id_total;
				break;

			case 'enfoque':

				//SEDE
				$sql = "SELECT organizacion.ID_ORG FROM organizacion JOIN enf_org USING(ID_ORG) WHERE ID_MUN_SEDE = '$ubicacion' AND ID_ENF = $id";
				if ($depto == 1)	$sql = "SELECT organizacion.ID_ORG FROM organizacion JOIN enf_org USING(organizacion) WHERE ID_MUN_SEDE IN ($id_muns) AND ID_ENF = $id";
				else if ($depto == 2)	$sql = "SELECT ID_ORG FROM enf_org WHERE ID_ENF = $id";

				$sql .= $cond_mapp_oea.$cond_consulta_social.$cond_papa.$order_by;

				$rs = $this->conn->OpenRecordset($sql);
				while ($row_rs = $this->conn->FetchRow($rs)){
					$num_sede++;
					$id_sede[] = $row_rs[0];
				}

				$num_total = count($id_sede);
				$id_total = $id_sede;

				//COBERTURA
				$sql = "SELECT mpio_org.ID_ORG FROM mpio_org JOIN organizacion USING(ID_ORG) JOIN enf_org USING(ID_ORG) WHERE mpio_org.ID_MUN = '$ubicacion' AND ID_ENF = $id";
				if ($depto == 1)	$sql = "SELECT depto_org.ID_ORG FROM depto_org JOIN organizacion USING(ID_ORG) JOIN enf_org USING(ID_ORG) WHERE depto_org.ID_DEPTO = '$ubicacion' AND ID_ENF = $id";
				else if ($depto == 2)	$sql = "SELECT ID_ORG FROM enf_org WHERE ID_ENF = $id";

				$sql .= $cond_mapp_oea.$cond_consulta_social.$cond_papa.$order_by;

				$rs = $this->conn->OpenRecordset($sql);
				while ($row_rs = $this->conn->FetchRow($rs)){
					$num_cobertura++;
					if (!in_array($row_rs[0],$id_sede)){
						$id_total[] = $row_rs[0];
						$num_total++;
					}
				}

				$num_orgs['sede'] = $num_sede;
				$num_orgs['cobertura'] = $num_cobertura;
				$num_orgs['total'] = $num_total;
				$num_orgs['id_total'] = $id_total;
				break;

			case 'poblacion':

				//SEDE
				$sql = "SELECT organizacion.ID_ORG FROM organizacion JOIN poblacion_org USING(ID_ORG)  WHERE ID_MUN_SEDE = '$ubicacion' AND ID_POB = $id";
				if ($depto == 1)	$sql = "SELECT organizacion.ID_ORG FROM organizacion JOIN poblacion_org USING(ID_ORG) WHERE ID_MUN_SEDE IN ($id_muns) AND ID_POB = $id";
				else if ($depto == 2)	$sql = "SELECT ID_ORG FROM poblacion_org LEFT JOIN $this->tabla USING($this->columna_id) WHERE ID_POB = $id";

				$sql .= $cond_mapp_oea.$cond_consulta_social.$cond_papa.$order_by;

				$rs = $this->conn->OpenRecordset($sql);
				while ($row_rs = $this->conn->FetchRow($rs)){
					$num_sede++;
					$id_sede[] = $row_rs[0];
				}

				$num_total = count($id_sede);
				$id_total = $id_sede;

				//COBERTURA
				$sql = "SELECT mpio_org.ID_ORG FROM mpio_org JOIN organizacion USING(ID_ORG) JOIN poblacion_org USING(ID_ORG) WHERE mpio_org.ID_MUN = '$ubicacion' AND ID_POB = $id";
				if ($depto == 1)	$sql = "SELECT depto_org.ID_ORG FROM depto_org JOIN organizacion USING(ID_ORG) JOIN poblacion_org USING(ID_ORG) WHERE depto_org.ID_DEPTO = '$ubicacion' AND ID_POB = $id";
				else if ($depto == 2)	$sql = "SELECT ID_ORG FROM poblacion_org LEFT JOIN $this->tabla USING($this->columna_id) WHERE ID_POB = $id";

				$sql .= $cond_mapp_oea.$cond_consulta_social.$cond_papa.$order_by;

				$rs = $this->conn->OpenRecordset($sql);
				while ($row_rs = $this->conn->FetchRow($rs)){
					$num_cobertura++;
					if (!in_array($row_rs[0],$id_sede)){
						$id_total[] = $row_rs[0];
						$num_total++;
					}
				}

				$num_orgs['sede'] = $num_sede;
				$num_orgs['cobertura'] = $num_cobertura;
				$num_orgs['total'] = $num_total;
				$num_orgs['id_total'] = $id_total;

				break;

			case 'municipio':

				//SEDE
				$sql = "SELECT ID_ORG,ID_ORG_PAPA FROM organizacion WHERE ID_MUN_SEDE = '$ubicacion' ".$cond_mapp_oea.$cond_consulta_social.$order_by;
				$rs = $this->conn->OpenRecordset($sql);
				while ($row_rs = $this->conn->FetchRow($rs)){
					if (!in_array($row_rs[1],$id_sede)){
						$num_sede++;
						$id_sede[] = $row_rs[0];
					}
				}

				$num_total = count($id_sede);
				$id_total = $id_sede;

				//COBERTURA
				$sql = "SELECT ID_ORG,ID_ORG_PAPA FROM mpio_org JOIN organizacion USING($this->columna_id) WHERE ID_MUN = '$ubicacion' ".$cond_mapp_oea.$cond_consulta_social;
				$rs = $this->conn->OpenRecordset($sql);
				while ($row_rs = $this->conn->FetchRow($rs)){
					$num_cobertura++;
					if (!in_array($row_rs[0],$id_sede) && !in_array($row_rs[1],$id_total)){
						$id_total[] = $row_rs[0];
						$num_total++;
					}
				}

				$num_orgs['sede'] = $num_sede;
				$num_orgs['cobertura'] = $num_cobertura;
				$num_orgs['total'] = $num_total;
				$num_orgs['id_total'] = $id_total;

				break;

		}

		return $num_orgs;
	}


	/**
	 * Listar las organizaciones en el home del usuario (tipo mapp-oea)
	 * @access public
	 */
	function listarOrgHomeTabs($letra_ini){

		$cond = ($letra_ini != '') ? "nom_org LIKE '$letra_ini%' AND mapp_oea = 1" : "mapp_oea=1";
		$orgs = $this->GetAllArrayID($cond,'','');
		$num_orgs = count($orgs);
		$url = 'index_mo.php?m_e=home&accion=alimentar';

		if ($num_orgs > 0){

			//INDICE
			echo "<div id='indice'>Indice:&nbsp;";
			for($c=65;$c<=90;$c++){
				$l = chr($c);
				$class = ($l == $letra_ini) ? 'a_big' : 'a_normal' ;

				$cond_l = "nom_org LIKE '$l%' and mapp_oea = 1";
				$n = count($this->GetAllArrayID($cond_l,'',''));

				if ($n > 0){
					echo "<a href='$url&l=$l' class='$class'>$l</a>";
					echo "&nbsp;&nbsp;";
				}
			}

			$class = ($letra_ini == '') ? 'a_big' : 'a_normal' ;
			echo " :: <a href='$url&l=' class='$class'>Todas</a>";

			echo "</div>";

			$id_num_search = "num_bus_r";

			//Filtro rapido si son mas de 5 registros
			$div_num_search = "<div id='$id_num_search' style='float:left;width:180px;text-align:left;'>&nbsp;&nbsp;$num_orgs Org(s)</div>";
			echo "<div style='clear:both;float:left;margin-bottom:10px;padding-left:30px;'>Busqueda r&aacute;pida:&nbsp;<input type='text' class='textfield' onkeyup=\"filtrarUL(this.value,'ul_orgs_home','$id_num_search',' Org(s)')\" size='40'></div>$div_num_search";

			//Exportar
			//Usa la funcion exportarListado en /home_mo.php
			if ($num_orgs > 0){
			echo "<div style='float:right;'>
			<img src='images/mapp_oea/consulta/excel.gif' border=0 title='Exportar a hoja de C&aacute;lculo'>&nbsp;
			<a href='#' onclick=\"exportarListado();return false;\">Exportar listado</a>
			</div>";
			}

			echo "<ul id='ul_orgs_home' style='clear:both'>";

			//Variable para exportar
			$_SESSION["id_orgs"] = $orgs;

			foreach($orgs as $i=>$id_org){
				$nombre = $this->GetName($id_org);
				$lock = $this->GetFieldValue($id_org,'locked');

				//Locked
				if ($lock == 0){
					$href_edit = "t/index_mo.php?m_e=org&accion=actualizar_mo&id=$id_org";
					$onclick_edit = '';

					$href_borrar = "index_mo.php?m_e=home&accion=borrar&param=$id_org&class=OrganizacionDAO";
					if (isset($_GET["l"]))	$href_borrar .= "&l=".$_GET["l"];

					$img_lock = 'images/mapp_oea/spacer.gif';
				}
				else{
					$href_edit = "t/index_mo.php?m_e=org&accion=actualizar_mo&id=$id_org";
					$onclick_edit = 'onclick="return false;"';
					$href_borrar = '#';
					$img_lock = 'images/mapp_oea/home/lock.png';
				}

				echo "<li>
					<table border=0 width='100%'>
					<tr>
					<td align='left' width=24><img src='$img_lock' width=24></td>
					<td align='left'>
					<a href='$href_edit' style='font-size:12px;' $onclick_edit>$nombre<a>&nbsp;&nbsp;
				</td>
					<td align='right'>";

				if ($lock == 0){
					echo "&nbsp;<img src='images/mapp_oea/home/pdf.gif' border='0' />&nbsp;&nbsp;<a href=\"download_pdf.php?c=5&id=$id_org\">Ficha PDF</a>&nbsp;&nbsp;";
					echo "&nbsp;<img src='images/mapp_oea/home/borrar.gif' border='0' />&nbsp;&nbsp;<a href='#' onclick=\"if(confirm('Est\xe1 seguro que desea eliminar la organizaci\xf3n?')){location.href='$href_borrar';}return false;\">Eliminar</a>";
				}
				else{
					echo "&nbsp;<img src='images/mapp_oea/home/stop.png' border='0' />&nbsp;&nbsp;<a href='#' onclick=\"if(confirm('Est\xe1 seguro que desea editar la organizaci\xf3n apesar que se encuentra bloqueada?')){location.href='$href_edit';}return false;\">Editar de todas formas</a>";
				}

				echo "
					</td>
					</tr>
					</table>
					</li>";
			}
			echo "</ul>";
		}
	}


	/******************************************************************************
	* Consulta los ID de las organizaciones segun filtros para MAPP OEA
	* @param string $case Sector, Poblacion
	* @param int $id Id de Sector, Poblacion
	* @param boolean $depto 1=Departamento , 0=Municipio, 2=Nacional
	* @param string $ubicacion ID de la ubiaccion
	* @return array $num
	* @access public
	*******************************************************************************/
	function getIdOrgsReporte($case,$id,$depto,$ubicacion){

		switch ($case) {
			case 'sector':
				$tabla = "sector_org";
				$col_id_filtro = "id_comp";
				break;

			case 'poblacion':
				$tabla = "poblacion_org";
				$col_id_filtro = "id_pob";
				break;

		}

		if ($case != 'cobertura'){
			$sql = "SELECT f.$this->columna_id FROM $tabla f JOIN $this->tabla USING($this->columna_id) ";
		}
		else{
			$sql = "SELECT $this->columna_id FROM $this->tabla ";
		}

		//Ubicacion geografica
		if ($depto == 1){
			$sql .= " LEFT JOIN depto_org USING($this->columna_id) WHERE id_depto = $ubicacion AND mapp_oea = 1";
		}
		else if ($depto == 0){
			//$sql .= " LEFT JOIN mun_org USING ($this->columna_id) WHERE id_mun = $ubicacion OR cobertura_nal_org = 1";
			$sql .= " LEFT JOIN mpio_org USING ($this->columna_id) WHERE id_mun = $ubicacion AND mapp_oea = 1";
		}
		else{
			$sql .= "WHERE mapp_oea = 1 ";
		}

		if ($case != 'cobertura'){
			$sql .= " AND $col_id_filtro IN ($id)";
		}

		//echo $sql;
		$rs = $this->conn->OpenRecordset($sql);

		$arr = array();
		while ($row = $this->conn->FetchRow($rs)){
			$arr[] = $row[0];
		}

		return $arr;
	}

	/**
	 * Ficha PDF organización mapp-oea
	 * @access public
	 * @param id $id Id de la Organizacion
	 */
	function fichaPdfMO($id){

		//INICIALIZACION DE VARIABLES
		$tipo_dao = New TipoOrganizacionDAO();
		$depto_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();
		$sector_dao = New SectorDAO();
		$poblacion_dao = New PoblacionDAO();
		$poblado_dao = New PobladoDAO();

		//CONSULTA LA INFO DE LA ORG.
		$org = $this->Get($id);

		//SIGLA
		if ($org->sig == "")	$org->sig = "-";

		//DIR
		if ($org->dir == "")	$org->dir = "-";

		//NACI
		if ($org->naci == 0)	$org->naci = "-";

		//ORG. A LA QUE PERTENECE
		$id_papa = $org->id_papa;
		if ($id_papa != 0 ){
			$org_papa = $this->Get($id_papa);
		}
		else{
			$org_papa->nom = "-";
		}

		//TIPO
		$tipo = $tipo_dao->Get($org->id_tipo);

		//MUN. SEDE
		$mun_sede = "";
		if ($org->id_mun_sede != "")
		$mun_sede = $mun_dao->Get($org->id_mun_sede);

		//N. REP
		if ($org->n_rep == "")	$org->n_rep = "-";

		//T. REP
		if ($org->t_rep == "")	$org->t_rep = "-";

		//EMAIL PUBLICO
		if ($org->pu_email == "")	$org->pu_email = "-";

		//WWW
		if ($org->web == ""){
			$org->web = "-";
		}
		else{
			//Elimina el http:// si lo tiene
			$org->web = str_replace("http://","",$org->web);
		}

		//TEL. 1
		if ($org->tel1 == "")	$org->tel1 = "-";

		//TEL. 2
		if ($org->tel2 == "")	$org->tel2 = "-";

		//FAX
		if ($org->fax == "")	$org->fax = "-";

		$hoy = getdate();
		$meses = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Novimebre","Diciembre");
		$hoy = $hoy["mday"]." ".$meses[$hoy["mon"]]." ".$hoy["year"];

		ob_start();

		echo "<table align='right'><tr><td>Proyecto mapeo de organizaciones sociales, Universidad del Magdalena - MAPP/OEA</td></tr>
		<tr><td align='center'>Fecha generaci&oacute;n ficha: $hoy</td></tr>
		</table><br />";
		echo "<table border='1'>";
		echo "<tr><td align='center' colspan='6'>$org->nom</td></tr>";
		echo "<tr><td><b>Sigla</b></td><td>".$org->sig."</td>";
		echo "</td></tr>";

		echo "<tr><td><b>Tipo</b></td><td>".$tipo->nombre_es."</td>";
		echo "<td><b>Sede</b></td><td colspan='3'>".$mun_sede->nombre."</td></tr>";

		echo "<tr><td><b>Direcci&oacute;n</b></td><td colspan='5'>".$org->dir."</td></tr>";
		echo "<tr><td><b>Nombre Representante</b></td><td class='tabla_consulta'>".$org->n_rep."</td><td class='tabla_consulta'><b>Ttulo Representante</b></td><td class='tabla_consulta'>".$org->t_rep."</td>";
		echo "<td><b>A&ntilde;o de fundaci&oacute;n en Colombia</b></td><td class='tabla_consulta'>".$org->naci."</td></tr>";
		echo "<tr><td><b>Email</b></td><td colspan='5'>$org->pu_email</td></tr>";
		echo "<tr><td><b>P&aacute;gina Web</b></td><td colspan='5'>$org->web</td></tr>";
		echo "<tr><td><b>Tel&eacute;fono 1</b></td><td>".$org->tel1."</td><td><b>Tel&eacute;fono 2</b></td><td>".$org->tel2."</td>";
		echo "<td><b>Fax</b></td><td>".$org->fax."</td></tr>";
		//SECTOR
		echo "<tr><td><b>Sector</b></td>";
		echo "<td colspan='5'>";
		foreach($org->id_sectores as $s=>$id){
			if (fmod($s,8) == 0)	echo "<br>";
			$vo = $sector_dao->Get($id);
			echo "- ".$vo->nombre_es." ";
		}
		//POBLACION
		echo "<tr><td><b>Poblaci&oacute;n Beneficiaria</b></td>";
		echo "<td colspan='5'>";
		foreach($org->id_poblaciones as $s=>$id){
			if (fmod($s,8) == 0)	echo "<br>";
			$vo = $poblacion_dao->Get($id);
			echo "- ".$vo->nombre_es." ";
		}
		echo "</td></tr>";

		//COBERTURA POR DEPARTAMENTO
		echo "<tr><td><b>Cobertura Geogr&aacute;fica por Departamento</b></td>";
		echo "<td colspan='5'>";
		//check para todos, se resta  1, porque existe el depto 00
		if (count($org->id_deptos) == ($depto_dao->numRecords('') - 1))	echo "TODOS";
		else{
			foreach($org->id_deptos as $s=>$id){
				if (fmod($s,8) == 0)	echo "<br>";
				$vo = $depto_dao->Get($id);
				echo "- ".$vo->nombre." ";
			}
		}
		echo "</td></tr>";

		//COBERTURA POR MUNICIPIO
		echo "<tr><td><b>Cobertura Geogr&aacute;fica por Municipio</b></td>";

		echo "<td colspan='5'>";
		//echo "<td>".count($org->id_muns)."---".$mun_dao->numRecords('')."</td>";
		//check para todos, se resta  1, porque existe el mpio 00000
		if (count($org->id_muns) == ($mun_dao->numRecords('') - 1))	echo "TODOS";
		else{
			foreach($org->id_muns as $s=>$id){
				if (fmod($s,10) == 0)	echo "<br>";
				$vo = $mun_dao->Get($id);
				echo "- ".$vo->nombre." ";
			}
		}
		echo "</td></tr>";

		//COBERTURA POR POBLADO
		if (count($org->id_poblados) > 0){
			echo "<tr><td><b>Cobertura Geogr&aacute;fica por Poblado</b></td>";
			echo "<td colspan='5'>";
			foreach($org->id_poblados as $s=>$id){
				if (fmod($s,10) == 0)	echo "<br>";
				$vo = $poblado_dao->Get($id);
				echo "- ".$vo->nombre." ";
			}
			echo "</td></tr>";
		}

		echo "<tr><td><b>Organizaci&oacute;n a la que pertenece</b></td><td colspan='5'>".$org_papa->nom."</td></tr>";

		//ESPACIO DE COORDINACION
		echo "<tr><td><b>Participa en alg&uacute;n espacio de coordinaci&oacute;n?</b></td><td colspan='5'>".$org->esp_coor."</td>";

		echo "</table>";

		$html = ob_get_contents();

		ob_end_flush();

		//Html 2 Pdf
		include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/common/html2fpdf/html2fpdf.php");

		//Horizontal
		$pdf = new HTML2FPDF('P','mm','Letter');
		//$pdf = new HTML2FPDF('L');

		$pdf->AddPage();

		$pdf->WriteHTML($html);
		//echo $html;
		$pdf_code = $pdf->Output('','S');
		//$this->createFileCache($pdf_code,$f_cache);

		echo $pdf_code;
	}
}


/**
 * Ajax de Organizacion
 *
 * Contiene los metodos para Ajax de la clase Organizacion
 * @author Ruben A. Rojas C.
 */

Class OrganizacionAjax extends OrganizacionDAO {

	/**
	* Grafica Organizaciones por Tipo, Enfoque, Sector o Población
	* @access public
	* @param int $ubicacion ID de la Ubicacion Geográfica
	* @param int $depto 1 o 0
	* @param int $graficar por Tipo, Enfoque, Sector, Poblacion
	* @param int $mapp_oea Filtrar orgs mapp-oea
	* @param string $chart Tipo de grafica
	* @param int $tipo_papa Agrupamiento de los tipos 0=Todos, 1=Internacion, 2=Nacional-Estado, 3=Nacional-Sociedad Civil
	*/
	function graficaConteoOrg($ubicacion,$depto,$graficar_por,$mapp_oea,$consulta_social,$chart,$tipo_papa){
		require_once "lib/common/graphic.class.php";
		require_once "lib/libs_org.php";

		//INICIALIZACION DE VARIABLES
		$PG = new PowerGraphic;
		$tipo_org_dao = New TipoOrganizacionDAO();
		$sector_dao = New SectorDAO();
		$enfoque_dao = New EnfoqueDAO();
		$poblacion_dao = New PoblacionDAO();
		$depto_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();

		$nom_ubi = "Nacional";
		if ($depto == 1){
			$ubi = $depto_dao->Get($ubicacion);
			$nom_ubi = $ubi->nombre;
		}
		else if ($depto == 0){
			$ubi = $mun_dao->Get($ubicacion);
			$nom_ubi = $ubi->nombre;
		}

		$cond = '';
		switch ($graficar_por) {
			case 'tipo':
				//Agrupamiento
				if ($tipo_papa > 0){
					//Internacional
					$id_int = "4,5,11,2,3";

					//Nacional-Estado
					$id_nac_est = "17,10,16";

					switch ($tipo_papa){
						//Internacional
						case 1:
							$cond = "id_tipo IN ($id_int)";
						break;
						//Nacional-Estado
						case 2:
							$cond = "id_tipo IN ($id_nac_est)";
						break;
						//Socieda Civil
						case 3:
							$cond = "id_tipo NOT IN (".$id_int.",".$id_nac_est.")";
						break;
					}
				}
				$arrs = $tipo_org_dao->GetAllArray($cond);
				break;
			case 'sector':
				$arrs = $sector_dao->GetAllArray($cond);
				break;
			case 'poblacion':
				$arrs = $poblacion_dao->GetAllArray($cond,'','');
				break;
			case 'enfoque':
				$arrs = $enfoque_dao->GetAllArray($cond);
				break;

		}

		//FILTRO POR MAPP-OEA
		$cond_mapp_oea = ($mapp_oea != 2) ? ' AND mapp_oea = '.$mapp_oea : '';

		//FILTRO POR CONSULTA SOCIAL
		$cond_consulta_social = ($consulta_social != 2) ? ' AND CONSULTA_SOCIAL = '.$consulta_social : '';

		$PG->title = "Cantidad de Organizaciones por ".ucfirst($graficar_por);
		//$PG->axis_y    = 'Organizaciones';
		//$PG->axis_x    = ucfirst($graficar_por);
		$PG->skin      = 1;
		$PG->type      = 8;
		$PG->credits   = 0;

		echo "<table cellpadding=2 width='100%' border=0>";
		echo "<tr><td>Localizaci&oacute;n Geogr&aacute;fica: <b>$nom_ubi</b></td></tr>";
		echo "<tr>
				<td valign='top'>
					<table border=0 class='tabla_grafica_conteo' cellpadding=4 cellspacing=1 width='250'>
						<tr class='titulo_tabla_conteo'><td align='center'>".ucfirst($graficar_por)."</td><td align='center' colspan='2'>Cant.</td></tr>";

		$d = 0;
		$f = 0;
		$total = 0;
		$id_orgs = array();
		foreach($arrs as $arr){

			$num_orgs = $this->numOrgsConteo($graficar_por,$arr->id,$depto,$ubicacion,$cond_mapp_oea,$cond_consulta_social);

			$PG->x[$f] = $arr->nombre_es;

			$cant_orgs = ($mapp_oea != 2) ? $num_orgs['cobertura'] : $num_orgs['total'];
			$PG->y[$f] = $cant_orgs;

			$tmp = array_diff($num_orgs['id_total'],$id_orgs);

			$id_orgs = array_merge($id_orgs,$tmp);

			$f++;

			$total += count($tmp);

			echo "<tr class='fila_tabla_conteo'><td>$arr->nombre_es</td>";
			echo "<td align='right'>".number_format($cant_orgs)."</td>";

			echo "</tr>";


		}

		$_SESSION["id_orgs"] = $id_orgs;

		echo "<tr><td><b>Total Organizaciones</b></td><td align='right'><b>".number_format($total)."</b></td>";
		echo "</table></td>";

		/********************************************************************************
		//PARA GRAFICA OPEN CHART
		/*******************************************************************************/
		$chk_chart = array('bar' => '', 'bar_3d' => '');
		$chk_chart[$chart] = ' selected ';

		echo "<td align='center' valign='top'><table>";

		echo "<tr><td align='left'>";

		//Si no viene de API lo muestra
		if (!isset($_GET["api"])){

			echo "Tipo de Gr&aacute;fica:&nbsp;
					<select onchange=\"graficar(this.value)\" class='select'>
						<option value='bar' ".$chk_chart['bar'].">Barras</option>
						<option value='bar_3d' ".$chk_chart['bar_3d'].">Barras 3D</option>
					</select>&nbsp;&nbsp;::&nbsp;&nbsp;";
		}

		echo "Si desea guardar la imagen haga click sobre el icono <img src='images/save.png'>
				</td>
			</tr>
		<tr><td class='tabla_grafica_conteo' colspan=1 width='600' bgcolor='#F0F0F0' align='center'><br>";

		//Eje x
		$i = 0;
		foreach ($PG->x as $x){
			if ($i == 0)	$ejex = "'".utf8_encode($x)."'";
			else			$ejex .= ",'".utf8_encode($x)."'";

			$i++;
		}

		//Eje y
		$ejey = implode(",",$PG->y);
		$max_y = max($PG->y);

		$titulo = "Cantidad de Organizaciones por ".ucfirst($graficar_por);
		//Variable de sesion que va a ser el nomnre dela grafica al guardar
		$_SESSION["titulo_grafica"] = $titulo;

		//Estilos para bar y bar3D
		$chart_style = array('bar' => array('alpha' => 90, 'bar_color' => '#0066ff'),
							 'bar_3d' => array('alpha' => 90,'bar_color' => '#0066ff'));

		$path = 'admin/lib/common/open-flash-chart/';
		$path_in = 'lib/common/open-flash-chart/';

		include("$path_in/php-ofc-library/sidihChart.php");
		$g = New sidihChart();
		$max_y = $g->maxY($max_y);

		$content = "<?
		\$path = '".$path."';
		include_once(\$path.'php-ofc-library/sidihChart.php' );

		\$bar = new $chart(".$chart_style[$chart]['alpha'].", '".$chart_style[$chart]['bar_color']."' );

		\$g = new sidihChart();
		\$g->title( '".utf8_encode($titulo)."' );
		\$g->set_x_labels( array(".$ejex.") );
		\$g->set_x_label_style( 8, '#000000', 2 );";


		if ($chart == 'bar_3d'){
			$content .= "\$g->set_x_axis_3d(6);";
			$content .= "\$g->x_axis_colour('#dddddd','#FFFFFF');";
		}

		$content .= "\$bar->data = array(".$ejey.");
		\$g->data_sets[] = \$bar;
		\$g->set_tool_tip( '#x_label# <br> Orgs: #val#' );
		// set the Y max
		\$g->set_y_max( ".$max_y." );
		// label every 20 (0,20,40,60)
		\$g->y_label_steps( 6 );

		// display the data
		echo \$g->render();
		?>";

		//MODIFICA EL ARCHIVO DE DATOS
		$archivo = New Archivo();
		$fp = $archivo->Abrir('../chart-data.php','w+');

		$archivo->Escribir($fp,$content);
		$archivo->Cerrar($fp);

		//IE Fix
		//Variable para que IE cargue el nuevo archivo de datos y cambie el tipo de grafica con los nuevos valores
		$nocache = time();
		include_once $path_in.'php-ofc-library/open_flash_chart_object.php';
		open_flash_chart_object( 500, 350, 'chart-data.php?nocache='.$nocache,false );

		echo "</td></tr>";

		//Si no viene de API lo muestra
		if (!isset($_GET["api"])){

			echo "<tr><td><img src='images/spacer.gif' height='30'></td></tr>
			<tr>
				<td align='center' colspan=1>
					<input type='hidden' id='pdf' name='pdf'>
					<input type='hidden' id='id_orgs' name='id_orgs' value='".implode(",",$id_orgs)."'>
					<input type='button' name='button' value='Generar Listado' onclick=\"document.getElementById('listadoConteoOrgMsg').innerHTML='Generando el listado en la parte inferior....';generarListadoOrgs();\" class='boton'>
					<br><br><span id='listadoConteoOrgMsg'></span>
				</td>
			</tr>";
		}

		echo "</table></td></tr>
		<tr><td id='listadoConteoOrg' colspan='2'></td></tr></table>";

		//Grafica html
		/*echo "<td align='right' valign='top'>
				<table>
					<tr><td><img src='admin/lib/common/graphic.class.php?". $PG->create_query_string() . "' border=1 /></td>
					<tr><td><img src='images/spacer.gif' height='30'></td></tr>
					<tr>
						<td align='center'>
							<input type='hidden' id='pdf' name='pdf'>
							<input type='hidden' id='id_orgs' name='id_orgs' value='".implode(",",$id_orgs)."'>
							<input type='button' name='button' value='Generar Listado' onclick=\"document.getElementById('listadoConteoOrgMsg').innerHTML='Generando el listado en la parte inferior....';generarListadoOrgs();\" class='boton'>
							<br><br><span id='listadoConteoOrgMsg'></span>
						</td>
					</tr>
				</table>
			  </td>
			 </tr>
			 <tr><td id='listadoConteoOrg' colspan='2'></td></tr>
			 ";*/

	}

	/**
	* Listado de Organizaciones por Tipo, Enfoque, Sector o Población generado desde gráfica
	* @access public
	* @param string id_orgs
	*/
	function listadoConteoOrg($id_orgs){
		require_once "lib/common/graphic.class.php";
		require_once "lib/libs_org.php";

		//INICIALIZACION DE VARIABLES
		$tipo_dao = New TipoOrganizacionDAO();

		$num_arr = count($id_orgs);

		echo "<table align='center' class='tabla_reportelist_outer' border=0>";
		echo "<tr><td>&nbsp;</td></tr>";
		echo "<tr>
				<td width='300'>&nbsp;</td>
				<td align='right'>Generar Reporte: <input type='radio' id='basico' name='basico' value='1' checked>&nbsp;B&aacute;sico</a>&nbsp;<input type='radio' id='detallado' name='basico' value=2>&nbsp;Detallado&nbsp;&nbsp;<a href=\"#\" onclick=\"document.getElementById('pdf').value = 1;reportStream('org');return false;\"><img src='images/consulta/generar_pdf.gif' border=0  onmouseover=\"Tip('Exportar a PDF<br><b>Basico</b>: Nombre,Sigla,Tipo,Cobertura<br><b>Detallado</b>: Toda la inforamci&oacute;n')\" onmouseout=\"UnTip()\"></a>&nbsp;&nbsp;<a href=\"#\" onclick=\"document.getElementById('pdf').value = 2;reportStream('org');return false;\"\"><img src='images/consulta/excel.gif' border=0 onmouseover=\"Tip('Exportar a Excel<br><b>Basico</b>: Nombre,Sigla,Tipo,Cobertura<br><b>Detallado</b>: Toda la inforamci&oacute;n')\" onmouseout=\"UnTip()\"></a></td>
			</tr>";

		echo "<tr><td colspan=3><table class='tabla_reportelist'>";
		echo"<tr class='titulo_lista'>
			<td>Nombre</td>
			<td>Sigla</td>
			<td>Tipo</td>";
		echo "<td align='center' width='150'>Registros: ".$num_arr."</td>
    		</tr>";

		//print_r($id_orgs);

		foreach($id_orgs as $i => $id_d){
			if ($id_d == "")	echo "iiii==>$i";
		}

		for($p=0;$p<$num_arr;$p++){

			$vo = $this->get($id_orgs[$p]);

			$style = "";
			if (fmod($p+1,2) == 0)  $style = "fila_lista";

			//NOMBRE
			if ($vo->nom != ""){

				//NOMBRE DEL TIPO DE ORGANIZACION
				$tipo = $tipo_dao->Get($vo->id_tipo);
				$nom_tipo = $tipo->nombre_es;

				echo "<tr class='".$style."'>";
				echo "<td>".$vo->nom."</td>";
				echo "<td>".$vo->sig."</td>";
				echo "<td>".$nom_tipo."</td>";
				echo "<td align='center'><a href='#' onclick=\"window.open('admin/ver.php?class=OrganizacionDAO&method=Ver&param=".$vo->id."','','top=30,left=30,height=900,width=900,scrollbars=1');return false;\">Detalles</a></td>";
				echo "</tr>";
			}
		}
		?>
		<script>
		document.getElementById('listadoConteoOrgMsg').innerHTML = '';
		</script>
		<?
	}

	/**
	* Grafica Organizaciones por Tipo, Enfoque, Sector o Población especificando si es Sede o Cobertura
	* @access public
	* @param int $ubicacion ID de la Ubicacion Geográfica
	* @param int $depto 1 o 0
	* @param int $graficar por Tipo, Enfoque, Sector, Poblacion
	* @param int $id_ubicacion Id de la ubicacion a quitar o colocar en la gráfica
	* @param boolean $checked Estado del checkbox
	* @param boolean $sede Graficar el numero de orgs con sede en
	* @param boolean $cobertura Graficar el numero de orgs concobertura en
	*/
	function graficaConteoOrgDeptoMpioSedeCobertura($ubicacion,$depto,$graficar_por,$filtro_graficar_por,$id_ubicacion,$checked,$sede,$cobertura,$cnrr,$consulta_social){
		require_once "lib/common/graphic.class.php";
		require_once "lib/libs_org.php";

		//INICIALIZACION DE VARIABLES
		$PG = new PowerGraphic;
		$tipo_org_dao = New TipoOrganizacionDAO();
		$sector_dao = New SectorDAO();
		$enfoque_dao = New EnfoqueDAO();
		$poblacion_dao = New PoblacionDAO();
		$depto_dao = New DeptoDAO();
		$mpio_dao = New MunicipioDAO();

		$chk_sede = 'checked';
		$chk_cobertura = 'checked';

		if ($depto == 0){
			$arrs = $depto_dao->GetAllArray('')	;
			$eje_x = 'Departamentos';
			$depto = 1;  //Invierto el valor para usaro el método numOrgsConteo
		}
		else {
			$arrs = $mpio_dao->GetAllArray('ID_DEPTO = '.$ubicacion);
			$eje_x = 'Municipios';
			$depto = 0;
		}


		//FILTRO POR CNRR
		$cnrr != 2	?	$cond_cnrr = ' AND CNRR = '.$cnrr	: $cond_cnrr = '';

		//FILTRO POR CONSULTA SOCIAL
		$consulta_social != 2	?	$cond_consulta_social = ' AND CONSULTA_SOCIAL = '.$consulta_social	:	$cond_consulta_social = '';

		$datos_off = $_SESSION["id_datos_off"];

		if ($checked == 0 && $id_ubicacion > 0){
			array_push($datos_off,$id_ubicacion);
		}
		else{
			$datos_off = array_values(array_diff($datos_off,array($id_ubicacion)));
		}

		$_SESSION["id_datos_off"] = $datos_off;
		$datos_off = $_SESSION["id_datos_off"];

		switch ($graficar_por){
			case 'tipo':
				$vo = $tipo_org_dao->Get($filtro_graficar_por);
				$title = " Tipo: ".$vo->nombre_es;
				break;
			case 'enfoque':
				$vo = $enfoque_dao->Get($filtro_graficar_por);
				$title = " Enfoque: ".$vo->nombre_es;
				break;
			case 'sector':
				$vo = $sector_dao->Get($filtro_graficar_por);
				$title = " Sector: ".$vo->nombre_es;
				break;
			case 'poblacion':
				$vo = $poblacion_dao->Get($filtro_graficar_por);
				$title = " Población: ".$vo->nombre_es;
				break;

		}

		$PG->title = "Número de Organizaciones$title";
		$PG->axis_y    = 'Organizaciones';
		$PG->axis_x    = $eje_x;
		$PG->skin      = 1;
		$PG->type      = 1;
		$PG->credits   = 0;

		if ($sede == 1 && $cobertura == 1){
			$PG->graphic_1 = 'Sede';
			$PG->graphic_2 = 'Cobertura';
		}
		else if ($sede == 1 && $cobertura == 0){
			$PG->graphic_1 = 'Sede';
			$chk_sede = 'checked';
			$chk_cobertura = '';
		}
		else if ($sede == 0 && $cobertura == 1){
			$PG->graphic_1 = 'Cobertura';
			$chk_sede = '';
			$chk_cobertura = 'checked';
		}

		echo "<table>";
		echo "<tr>
				<td>
					<table border=0 class='tabla_grafica_conteo' cellspacing=1>
						<tr class='titulo_lista'><td width='40'>&nbsp;</td><td align='center'>$eje_x</td><td align='center' colspan='2'>Orgs.</td></tr>
						<tr><td></td><td>&nbsp;</td><td class='titulo_lista'><input id='sede' type='checkbox' onclick=\"return graficarDeptoMpio(0,'')\" $chk_sede>Sede</td><td class='titulo_lista'><input id='cobertura' type='checkbox' onclick=\"return graficarDeptoMpio(0,'')\" $chk_cobertura>Cobertura</td></tr>";
		$d = 0;
		$f = 0;
		$total_sede = 0;
		$total_cobertura = 0;
		foreach($arrs as $arr){

			$num_orgs = $this->numOrgsConteo($graficar_por,$filtro_graficar_por,$depto,$arr->id,$cond_cnrr,$cond_consulta_social);
			$dd = $d + 1;

			$chk = '';
			if (!in_array($arr->id,$datos_off)){

				if ($sede == 1 && $cobertura == 1){
					if ($num_orgs['sede'] > 0 || $num_orgs['cobertura'] > 0){
						$PG->x[$f] = $d + 1;
						$PG->y[$f] = $num_orgs['sede'];
						$PG->z[$f] = $num_orgs['cobertura'];
						$chk = 'checked';
						$f++;
					}
				}
				else if ($sede == 1 && $cobertura == 0){
					if ($num_orgs['sede'] > 0){
						$PG->x[$f] = $d + 1;
						$PG->y[$f] = $num_orgs['sede'];
						$chk = 'checked';
						$f++;
					}
				}
				else if ($sede == 0 && $cobertura == 1){
					if ($num_orgs['cobertura'] > 0){
						$PG->x[$f] = $d + 1;
						$PG->y[$f] = $num_orgs['cobertura'];
						$chk = 'checked';
						$f++;
					}
				}

				$total_sede += $num_orgs['sede'];
				$total_cobertura += $num_orgs['cobertura'];
			}

			echo "<tr><td class='fila_lista_claro'><input type='checkbox' onclick=\"graficarDeptoMpio($arr->id,this.checked)\" $chk></td><td class='fila_lista_claro'>$dd.&nbsp;$arr->nombre</td>";
			if ($num_orgs['sede'] > 0 && $sede ==1){
				echo "<td class='fila_lista_77C9FF'>".$num_orgs['sede']."</td>";
			}
			else{
				echo "<td class='fila_lista_claro'>".$num_orgs['sede']."</td>";
			}
			if ($num_orgs['cobertura'] > 0 && $cobertura == 1){
				echo "<td class='fila_lista_77C9FF'>".$num_orgs['cobertura']."</td>";
			}
			else{
				echo "<td class='fila_lista_claro'>".$num_orgs['cobertura']."</td>";
			}

			echo "</tr>";

			$d++;

		}
		echo "<tr class='bcg_0000000_FFFFFF'><td></td><td>Total</td><td>$total_sede</td><td>$total_cobertura</td>";
		echo "</table></td>";
		//echo "<td valign='top'><img src='admin/lib/common/graphic.class.php?". $PG->create_query_string() . "' border=1 /></td></tr>";
		echo "<td valign='top'><iframe src='admin/lib/common/graphic.class.php?". $PG->create_query_string() . "' frameborder=0 width='600' height=300 /></td></tr>";

	}

	/**
	* Reporte Organizaciones mapp-oea por sector, poblacion
	* @access public
	* @param int $ubicacion ID de la Ubicacion Geográfica
	* @param int $depto 1 o 0
	* @param string $filtro Sector, Poblacion
	* @param int $id_filtro Valor de Sector, Poblacion
	* @param string $caso Listado, Reporte On Line, Reporte Pdf
	* @param int $show_html Mostrar el codigo html
	*/
	function reporteOrgMO($ubicacion,$depto,$filtro,$id_filtro,$caso,$show_html=1){
		require_once "lib/libs_org.php";

		//INICIALIZACION DE VARIABLES
		$sector_dao = New SectorDAO();
		$poblacion_dao = New PoblacionDAO();
		$depto_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();
		$org_dao = New OrganizacionDAO();


		if ($filtro != 'listado_completo'){
			$nom_ubi = "Nacional";
			if ($depto == 1){
				$ubi = $depto_dao->Get($ubicacion);
				$nom_ubi = $ubi->nombre;
			}
			else if ($depto == 0){
				$ubi = $mun_dao->Get($ubicacion);
				$nom_ubi = $ubi->nombre;
			}

			//CONSULTA LOS ID de PROYECTOS
			$arr = $this->getIdOrgsReporte($filtro,$id_filtro,$depto,$ubicacion);
		}
		else{
			$arr = $this->GetAllArrayID('','','');
		}

		$num_arr = count($arr);

		if ($num_arr > 0){

			echo "<p><img src='images/mapp_oea/consulta/excel.gif' border=0 title='Exportar a Excel'>&nbsp;
			<a href=\"#\" onclick=\"location.href='export_data.php?case=xls_session&nombre_archivo=orgs_sociales';return false;\"\">Exportar a Hoja de C&aacute;lculo</a>
			( $num_arr Organizaciones ) </p>";

			$this->ReporteOrganizacion(implode(",",$arr),2,2,1);
		}
		else{
			echo "<p>No existen Organizaciones registradas</p>";
		}

	}
}

?>
