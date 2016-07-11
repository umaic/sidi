<?
/**
 * DAO de 4w
 *
 * Contiene los métodos de la clase Proyecto
 * @author Ruben A. Rojas C.
 */

Class P4wDAO {

    /**
     * Conexiòn a la base de datos
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
     * N�mero de Registros en Pantalla para ListarTAbla
     * @var string
     */
    var $num_reg_pag;

    /**
     * URL para redireccionar despues de Insertar, Actualizar o Borrar
     * @var string
     */
    var $url;

    /**
     * Constructor
     * Crea la conexi�n a la base de datos
     * @access public
     */
    function P4wDAO (){
        $this->conn = MysqlDb::getInstance();
        $this->tabla = "proyecto";
        $this->columna_id = "ID_PROY";
        $this->columna_nombre = "NOM_PROY";
        $this->columna_order = "NOM_PROY";
        $this->num_reg_pag = 10;

        $this->url = "index.php?m_e=p4w&accion=listar&class=P4wDAO&method=Dashboard&param=";

    }

    /**
     * Consulta los datos de una Proyecto
     * @access public
     * @param int $id ID del Proyecto
     * @return VO
     */
    function Get($id){
        $sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
        $rs = $this->conn->OpenRecordset($sql);
        $row_rs = $this->conn->FetchObject($rs);

        //Crea un VO
        $vo = New P4w();

        //Carga el VO
        $vo = $this->GetFromResult($vo,$row_rs);

        //Retorna el VO
        return $vo;
    }

    /**
     * Retorna el max ID
     * @access public
     * @return int
     */
    function GetMaxID(){
        $sql = "SELECT max(ID_PROY) as maxid FROM ".$this->tabla;
        $rs = $this->conn->OpenRecordset($sql);
        if($row_rs = $this->conn->FetchRow($rs)){
            return $row_rs[0];
        }
        else{
            return 0;
        }
    }

    /**
     * Consulta el nombre del Proyecto
     * @access public
     * @param int $id ID del Proyecto
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
	* Consulta el valor de un field
	* @access public
	* @param int $id ID del Proyecto
	* @param string $field Field de la tabla org
	* @return VO
	*/
	function GetFieldValue($id,$field){
		$sql = "SELECT ".$field." FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchRow($rs);

		//Retorna el campo
		return $row_rs[0];
	}

    /**
     * Consulta si tiene cobertura nacional el Proyecto
     * @access public
     * @param int $id ID del Proyecto
     * @return VO
     */
    function getCoberturaNacional($id){
        $sql = "SELECT cobertura_nal_proy FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
        $rs = $this->conn->OpenRecordset($sql);
        $row_rs = $this->conn->FetchRow($rs);

        //Retorna el VO
        return $row_rs[0];
    }

    /**
     * Consulta los deptos que cubre el proyecto
     * @access public
     * @param int $id ID del Proyecto
     * @param string $cond condicion de ubicación
     * @return array
     */
    function getIdDeptosCobertura($id,$cond=''){

        $deptos = array();

        $sql = "SELECT id_depto FROM depto_proy WHERE id_proy = $id";
        $rs = $this->conn->OpenRecordset($sql);
        while ($row = $this->conn->FetchObject($rs)){
            $deptos[] = $row->id_depto;
        }

        return $deptos;
    }
    
    /**
     * Consulta los mpios que cubre el proyecto
     * @access public
     * @param int $id ID del Proyecto
     * @param string $cond condicion de ubicaci�n
     * @return array
     */
    function getMpiosCobertura($id,$cond=''){

        $ids = $lon = $lat = $deptos = $deptos_id = $noms = array();

        if ($this->getCoberturaNacional($id) == 1) {
            $sq = "SELECT ID_MUN AS id, NOM_MUN AS nom, LATITUDE AS lat,
                    LONGITUDE AS lon, NOM_DEPTO AS nd, ID_DEPTO AS id_d FROM municipio WHERE 1=1";
        }
        else {

            $sq = 'SELECT ID_MUN AS id, NOM_MUN AS nom, m.LATITUDE AS lat,
                    m.LONGITUDE AS lon, NOM_DEPTO AS nd, ID_DEPTO AS id_d';

            // MDGD
            $sqc = "SELECT COUNT(id_mun) FROM mun_proy WHERE id_proy = $id";
            $rsc = $this->conn->OpenRecordset($sqc);
            $rowc = $this->conn->FetchRow($rsc);

            if (empty($rowc[0])) {
                $sq .= ' FROM depto_proy
                    JOIN municipio AS m USING(id_depto)';

                $all = 'Todo el Departamento';
            }
            else {
                $sq .= ', mp.LATITUDE AS latp, mp.LONGITUDE AS lonp
                        FROM mun_proy AS mp
                        JOIN municipio AS m USING(id_mun)';

                $all = '';
            }

            $sq .= ' JOIN departamento USING(id_depto) WHERE id_proy = '.$id;
        }

        if (!empty($cond)) {
            $sq .= ' AND '.$cond;
        }

        //echo $sq;

        $rs_s = $this->conn->OpenRecordset($sq);
        while ($row = $this->conn->FetchObject($rs_s)){
            $id = $row->id;
            $ids[] = $id;
            $noms[] = $row->nom;

            if (!in_array($row->id_d, $deptos_id)) {
                $deptos_id[] = $row->id_d;
                $deptos[] = $row->nd;
            }

            if (!empty($row->lat) &&  !empty($row->lon)) {
                $lat[$id] = $row->lat;
                $lon[$id] = $row->lon;
            }
            else if (!empty($row->latp) &&  !empty($row->lonp)) {
                $lat[$id] = $row->latp;
                $lon[$id] = $row->lonp;
            }

        }

        return compact('ids', 'noms', 'lat', 'lon', 'deptos', 'deptos_id', 'all');
    }

    /**
     * Consulta los temas del proyecto
     * @access public
     * @param int $id ID del Proyecto
     * @param string $cond condicion
     * @return array
     */
    function getTemas($id,$cond=''){

        $tms = array();
        $sq = "SELECT id_tema, nom_tema, presupuesto FROM proyecto_tema
               JOIN tema USING(id_tema) WHERE id_proy = ".$id;

        if (!empty($cond)) {
            $sq .= ' AND '.$cond;
        }

        $r = 0;
        $rs = $this->conn->OpenRecordset($sq);
        while ($row = $this->conn->FetchRow($rs)){
            $tms['id'][$r] = $row[0];
            $tms['nom'][$r] = $row[1];
            $tms['pres'][$r] = $row[2];

            $r++;
        }

        return $tms;
    }

    /**
     * Consulta los temas del proyecto
     * @access public
     * @param int $id ID del Proyecto
     * @param string $cond condicion
     * @return array
     */
    function getTemasAgrupado($id,$cond=''){

        $tms = array();
        foreach(array(1 => 'UNDAF', 2 => 'Cluster', 4 => 'Des-Paz') as $c => $t) {

            $tms[$c] = $this->getTemas($id, "id_clasificacion = $c");
        }

        return $tms;
    }

    /**
     * Retorna el numero de Registros
     * @access public
     * @return int
     */
    function numRecords($condicion){
        $sql = "SELECT count(ID_PROY) as num FROM ".$this->tabla;
        if ($condicion != ""){
            $sql .= " WHERE ".$condicion;
        }
        $rs = $this->conn->OpenRecordset($sql);
        $row_rs = $this->conn->FetchRow($rs);

        return $row_rs[0];
    }

    /**
     * Consulta los datos de los Proyecto que cumplen una condici�n
     * @access public
     * @param string $condicion Condici�n que deben cumplir los Proyecto y que se agrega en el SQL statement.
     * @return array Arreglo de VOs
     */
    function GetAllArray($condicion,$limit='',$order_by=''){
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
            $vo = New Proyecto();
            //Carga el VO
            $vo = $this->GetFromResult($vo,$row_rs);
            //Carga el arreglo
            $array[] = $vo;
        }
        //Retorna el Arreglo de VO
        return $array;
    }

    /**
     * Consulta los ID de las Organizacion que cumplen una condici�n
     * @access public
     * @param string $condicion Condicin que deben cumplir los Organizacion y que se agrega en el SQL statement.
     * @return array Arreglo de VOs
     */
    function GetAllArrayID($condicion,$limit,$order_by){

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
            $array[] = $row_rs[0];
        }
        //Retorna el Arreglo de VO
        return $array;
    }

    /* Consulta los ID de los Proyectos ejecutados por las organizaciones dadas
     * @access public
     * @param array $id_orgs
     * @param string $condicion
     * @param string $limit
     * @param string $order_by
     * @return array Arreglo de IDs
     */
    function GetIDByEjecutor($id_orgs,$condicion='',$limit='',$order_by=''){

        $id_orgs_s = implode(",",$id_orgs);

        $sql = "SELECT vinculorgpro.".$this->columna_id." FROM vinculorgpro

            JOIN $this->tabla USING ($this->columna_id)";

        //Filtro de tema en home
        $cond_t = '';
        if (isset($_GET["id_t"])){
            $sql .= " JOIN proyecto_tema USING($this->columna_id)";
            $cond_t = " AND id_tema = ".$_GET["id_t"];
        }

        //Filtro de depto en home
        $cond_d = '';
        if (isset($_GET["id_d"])){
            $sql .= " JOIN depto_proy USING($this->columna_id)";
            $cond_d = " AND id_depto = ".$_GET["id_d"] ;
        }

        $sql .= " WHERE vinculorgpro.id_org IN ($id_orgs_s) AND id_tipo_vinorgpro = 1 ";

        $sql .= $cond_t.$cond_d;

        if ($condicion != ""){
            $sql .= " AND ".$condicion;
        }

        //echo $sql;

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

        //echo $sql;

        $array = Array();

        $rs = $this->conn->OpenRecordset($sql);
        while ($row_rs = $this->conn->FetchRow($rs)){
            //Carga el arreglo
            $array[] = $row_rs[0];
        }
        //Retorna el Arreglo de VO
        return $array;
    }


    /**
     * Revisa si el proyecto esta clasificado en todos las clasificaciones
     * @access publioc
     */
    function checkClaAll($id_proy) {

        $sql = "SELECT COUNT(id_tema) FROM proyecto_tema JOIN tema USING(id_tema) WHERE
                id_clasificacion IN (SELECT id_clasificacion FROM clasificacion)";
        $rs = $this->conn->OpenRecordset($sql);
        $row = $this->conn->FetchRow($rs);

        return (empty($row[0])) ? false : true;

    }

    /**
     * Lista las Proyectoes en una Tabla
     * @access public
     */
    function Dashboard(){

        $id_org = (isset($_GET['id_org'])) ? $_GET['id_org'] : false;
        $t = (empty($_GET['t'])) ? false : $_GET['t'];

        //$undaf = (empty($_GET['undaf'])) ? false : true;
        if (isset($_GET['si_proy'])) {
            $si_proy = $_GET['si_proy'];
            $_SESSION['si_proy'] = $si_proy;
        }
        else {
            $si_proy = $_SESSION['si_proy'];
        }

        $sr = (isset($_GET['sr'])) ? $_GET['sr'] : 0;
        $cundaf = 1; // ID clasificacion UNDAF
        //$cond = "num = (SELECT COUNT(id_clasificacion) FROM clasificacion) ";
        $cond = "validado_cluster_proy = 1";
        $condao = ' 1=1 ';

        switch($si_proy) {
            case '4w':
                $titulo = 'Proyectos '.((empty($t)) ? ' pendientes por clasificar' : ' validados');
                $titulo_left = 'Humanitario';
            break;
            default:
                $titulo = 'Proyectos en el sistema';
                $titulo_left = 'UNDAF';
            break;
        }

        // Se usa la vista p4w_dashboard
        $sql = "SELECT COUNT(DISTINCT(ID_PROY)) FROM p4w_dashboard";

        // Alimentador ORG
        if ($_SESSION['id_tipo_usuario_s'] == 41 || $_SESSION['id_tipo_usuario_s'] == 27) {
            $sql .= ' JOIN vinculorgpro USING(id_proy) ';
            $condao .= 'AND id_org = '.$_SESSION['id_org'];

            //$titulo = 'Proyectos '.(($undaf) ? 'UNDAF' : '4W ').((empty($t)) ? ' pendientes por clasificar' : '');
        }

        // Validador Cluster, 1964 = id de 4w, 1977 = id de 4wp
        else if ($_SESSION['id_tipo_usuario_s'] == 42 && !$undaf && !in_array($_SESSION['id_usuario_s'], array(1964, 1977))) {
            $sql .= ' JOIN proyecto_tema USING(id_proy) ';
            $condao .= "AND temas LIKE '%".$_SESSION['id_tema']."%'";

        }

        // UNDAF totales
        //$p4w['ucr'] = $this->numRecords("si_proy = 'undaf'");

        /*$sqlu = $sql." WHERE $cond AND si_proy = 'undaf'";
        $rs = $this->conn->OpenRecordset($sqlu);
        $row = $this->conn->FetchRow($rs);
        $p4w['upc'] = $p4w['ucr'] - $row[0];
        $p4w['upo'] = ceil(($row[0])/$p4w['ucr']*100);
         */

        // 4w totales
        $cond_4 = "$condao AND si_proy <> 'undaf'";

        $sqlcr = $sql." WHERE $cond_4";

        //echo $sqlcr;
        $rscr = $this->conn->OpenRecordset($sqlcr);
        $rowcr = $this->conn->FetchRow($rscr);
        $p4w['cr'] = $rowcr[0];

        if (empty($p4w['cr'])) {
            $p4w['cr'] = 0;
        }

        $sql_4 = $sql." WHERE $cond AND $cond_4";
        $rs = $this->conn->OpenRecordset($sql_4);
        $row = $this->conn->FetchRow($rs);
        $p4w['pc'] = $p4w['cr'] - $row[0];
        $p4w['po'] = (empty($p4w['cr'])) ? 0 : floor(($row[0])/($p4w['cr'])*100);


        // Opciones de busqueda
        $encargados = $this->getOrgEjecutoraTotal(array('nom_org'), 1, array('no_conditionsi' => true));
        $donantes = $this->getOrgEjecutoraTotal(array('nom_org'), 2, array('no_conditionsi' => true));

        $proys = $this->getProsDashboard($sr, $t, $si_proy);

        include('p4w/dashboard.php');
    }


    /**
     * Consulta reportes para dashboard
     * @param int $sr Ultimo registro del dashboard
     * @param int $t t=1 muestra todos los proyecto
     * @param string $si_proy
     * @return array $proys
     * @access public
     */
    function getProsDashboard($sr, $t, $si_proy){

        $fr = (is_numeric($sr)) ? 50 : false;
        $cond = '1 = 1 ';
        $forg = false;

        $order = (!empty($_GET['order'])) ? str_replace('+',' ', $_GET['order']) : 'nom_proy ASC';

        $sql = 'SELECT DISTINCT(id_proy), nom_proy, nom_org, sig_org, cod_proy,
            inicio_proy, fin_proy, creac_proy, usuario, actua_proy
            FROM p4w_dashboard';

        $cond .= " AND si_proy <> 'undaf'";

        if ($si_proy == '4w' && empty($t)) {
            //$cond .= " AND num < (SELECT COUNT(id_clasificacion) FROM clasificacion)";
            $cond .= ' AND validado_cluster_proy = 0';
        }

        if (!empty($_GET['codigo'])) {
            $cond .= " AND cod_proy = '".$_GET['codigo']."'";
        }

        if (!empty($_GET['encargado'])) {
            $cond .= " AND org = ".$_GET['encargado']." AND id_tipo_vinorgpro = 1";
            $forg = true;
        }

        if (!empty($_GET['donante'])) {
            $cond .= " AND org = ".$_GET['donante']." AND id_tipo_vinorgpro = 2";
            $forg = true;
        }

        // Alimentador ORG
        if ($_SESSION['id_tipo_usuario_s'] == 41 || $_SESSION['id_tipo_usuario_s'] == 27) {
            $sql .= ' JOIN vinculorgpro AS v USING(id_proy) ';
            $cond .= ' AND v.id_tipo_vinorgpro = 1 AND org = '.$_SESSION['id_org'];
            $forg = true;
        }

        // Validador Cluster
        //else if ($_SESSION['id_tipo_usuario_s'] == 42 && !$undaf && !in_array($_SESSION['id_usuario_s'], array(1964, 1977))) {
        else if ($_SESSION['id_tipo_usuario_s'] == 42 && !$undaf && $_SESSION['id_tipo_usuario_s'] != 1) {
            $sql .= ' JOIN proyecto_tema USING(id_proy) ';
            //$cond .= " AND temas LIKE '%".$_SESSION['id_tema']."%'";
            $cond .= " AND id_tema = ".$_SESSION['id_tema'];
        }

        if (!$forg) {
            $cond .= " AND id_tipo_vinorgpro = 1";
        }

        // Limit
        $limit = ($fr !== false) ? " LIMIT ".$sr.", $fr" : '';

        $sql .= " WHERE $cond ORDER BY $order $limit";

        //echo $sql;

        $proys = array();
        $rs = $this->conn->OpenRecordset($sql);
        while ($row = $this->conn->FetchObject($rs)){
            // Check clasificacion en los 3 sistemas
            //if (!$t && !$this->checkClaAll($row[0])) {
                $proys[] = $row;

            //}
        }

        return $proys;
    }

    /**
     * Imprime en pantalla los datos del Proyecto
     * @access public
     * @param object $vo Proyecto que se va a imprimir
     * @param string $formato Formato en el que se listar�n los Proyecto, puede ser Tabla o ComboSelect
     * @param int $valor_combo ID del Proyecto que ser� selccionado cuando el formato es ComboSelect
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
     * Carga un VO de Proyecto con los datos de la consulta
     * @access public
     * @param object $vo VO de Proyecto que se va a recibir los datos
     * @param object $Resultset Resource de la consulta
     * @return object $vo VO de Proyecto con los datos
     */
    function GetFromResult ($vo,$Result){

        $vo->id_proy = $Result->ID_PROY;
        $vo->id_mon = $Result->ID_MON;
        $vo->id_estp = $Result->ID_ESTP;
        $vo->id_emergencia = $Result->ID_EMERGENCIA;
        $vo->id_con = $Result->ID_CON;
        $vo->nom_proy = $Result->NOM_PROY;
        $vo->cod_proy = $Result->COD_PROY;
        $vo->des_proy = $Result->DES_PROY;
        $vo->obj_proy = $Result->OBJ_PROY;
        $vo->inicio_proy = $Result->INICIO_PROY;
        $vo->fin_proy = $Result->FIN_PROY;
        $vo->srp_proy = $Result->SRP_PROY;
        $vo->actua_proy = $Result->ACTUA_PROY;
        $vo->costo_proy = $Result->COSTO_PROY;
        $vo->duracion_proy = $Result->DURACION_PROY;
        $vo->info_conf_proy = $Result->INFO_CONF_PROY;
        $vo->staff_nal_proy = $Result->STAFF_NAL_PROY;
        $vo->staff_intal_proy = $Result->STAFF_INTAL_PROY;
        $vo->cobertura_nal_proy = $Result->COBERTURA_NAL_PROY;
        $vo->cant_benf_proy = $Result->CANT_BENF_PROY;
        $vo->valor_aporte_donantes = $Result->VALOR_APORTE_DONANTES;
        $vo->valor_aporte_socios = $Result->VALOR_APORTE_SOCIOS;
        $vo->info_extra_donantes = $Result->INFO_EXTRA_DONANTES;
        $vo->info_extra_socios = $Result->INFO_EXTRA_SOCIOS;
        $vo->joint_programme_proy = $Result->JOINT_PROGRAMME_PROY;
        $vo->mou_proy = $Result->MOU_PROY;
        $vo->acuerdo_coop_proy = $Result->ACUERDO_COOP_PROY;
        $vo->interv_ind_proy = $Result->INTERV_IND_PROY;
        $vo->otro_cual_benf_proy = $Result->OTRO_CUAL_BENF_PROY;
        $vo->si_proy = $Result->SI_PROY;
        $vo->validado_cluster_proy = $Result->VALIDADO_CLUSTER_PROY;

        $id = $vo->id_proy;

        //TEMAS
        $arr = array();
        $arr_t = array();
        $temas_presupuesto = array();
        //$sql_s = "SELECT ID_TEMA, DESC_PROY_TEMA, PRESUPUESTO FROM proyecto_tema JOIN tema USING(id_tema) WHERE ID_PROY = $id AND id_papa = 0";
        $sql_s = "SELECT ID_TEMA, DESC_PROY_TEMA, PRESUPUESTO FROM proyecto_tema JOIN tema USING(id_tema) WHERE ID_PROY = $id";
        $rs_s = $this->conn->OpenRecordset($sql_s);
        while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            $id_p = $row_rs_s[0];

            $arr[$id_p] = array();

            //Tema principal
            if (!empty($row_rs_s[1])) {
                $vo->id_tema_p = $row_rs_s[0];
            }

            $temas_presupuesto[$id_p] = (empty($row_rs_s[2])) ? '' : $row_rs_s[2];

            //Hijos
            $sql_h = "SELECT ID_TEMA, DESC_PROY_TEMA, PRESUPUESTO FROM proyecto_tema JOIN tema USING(id_tema) WHERE ID_PROY = $id AND id_papa = $id_p";
            $rs_h = $this->conn->OpenRecordset($sql_h);
            while ($row_h = $this->conn->FetchRow($rs_h)){
                $id_h = $row_h[0];
                $arr[$id_p]["hijos"][] = $id_h;

                //Tema principal
                if (!empty($row_h[1])) {
                    $vo->id_tema_p = $id_h;
                }

                $temas_presupuesto[$id_h] = (empty($row_h[2])) ? '' : $row_h[2];

                //Nietos
                $sql_n = "SELECT ID_TEMA, PRESUPUESTO FROM proyecto_tema INNER JOIN tema USING(id_tema) WHERE ID_PROY = $id AND id_papa = $id_h";
                $rs_n = $this->conn->OpenRecordset($sql_n);
                while ($row_n = $this->conn->FetchRow($rs_n)){
                    $arr[$id_p]["nietos"][] = $row_n[0];
                }

            }

        }
        $vo->id_temas = $arr;
        $vo->temas_presupuesto = $temas_presupuesto;

            /*
            //SECTORES
            $arr = Array();
            $sql_s = "SELECT ID_COMP FROM sector_proy WHERE ID_PROY = ".$id;
            $rs_s = $this->conn->OpenRecordset($sql_s);
            while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            array_push($arr,$row_rs_s[0]);
            }
            $vo->id_sectores = $arr;

            //ENFOQUES
            $arr = Array();
            $sql_s = "SELECT ID_ENF FROM enfoque_proy WHERE ID_PROY = ".$id;
            $rs_s = $this->conn->OpenRecordset($sql_s);
            while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            array_push($arr,$row_rs_s[0]);
            }
            $vo->id_enfoques = $arr;

            //CONTACTOS
            $arr = Array();
            $sql_s = "SELECT ID_CONP FROM proyecto_conta WHERE ID_PROY = ".$id;
            $rs_s = $this->conn->OpenRecordset($sql_s);
            while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            array_push($arr,$row_rs_s[0]);
            }
            $vo->id_contactos = $arr;
             */

        //ORGS. EJECUTORAS
        $arr = Array();
        $sql_s = "SELECT ID_ORG FROM vinculorgpro WHERE ID_PROY = ".$id." AND ID_TIPO_VINORGPRO = 1 ORDER BY ID_VINORGPRO";
        $rs_s = $this->conn->OpenRecordset($sql_s);
        while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            $arr[] = $row_rs_s[0];
        }
        $vo->id_orgs_e = $arr;

        //ORGS. DONANTES
        $arr = array();
        $arr_v = array();
        $arr_c = array();

        $sql_s = "SELECT ID_ORG, VALOR_APORTE, CODIGO FROM vinculorgpro WHERE ID_PROY = ".$id." AND ID_TIPO_VINORGPRO = 2 ORDER BY ID_VINORGPRO";
        $rs_s = $this->conn->OpenRecordset($sql_s);
        while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            $id_d = $row_rs_s[0];
            $arr[] = $id_d;
            $arr_v[$id_d] = $row_rs_s[1];
            $arr_c[$id_d] = $row_rs_s[2];
        }
        $vo->id_orgs_d = $arr;
        $vo->id_orgs_d_valor_ap = $arr_v;
        $vo->id_orgs_d_codigo = $arr_c;

        //ORGS. SOCIOS
        $arr = Array();
        $sql_s = "SELECT ID_ORG, VALOR_APORTE FROM vinculorgpro WHERE ID_PROY = ".$id." AND ID_TIPO_VINORGPRO = 3 ORDER BY ID_VINORGPRO";
        $rs_s = $this->conn->OpenRecordset($sql_s);
        while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            $arr[] = $row_rs_s[0];
        }
        $vo->id_orgs_s = $arr;

        $vo->benf_proy = $this->getCantBenef($id);

            /*
            //CONTACTOS
            $arr = Array();
            $sql_s = "SELECT ID_CONP FROM proyecto_conta WHERE ID_PROY = ".$id;
            $rs_s = $this->conn->OpenRecordset($sql_s);
            while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            array_push($arr,$row_rs_s[0]);
            }
            $vo->id_contactos = $arr;
             */

        //DEPARTAMENTOS
        $arr = Array();
        $sql_s = "SELECT ID_DEPTO FROM depto_proy WHERE ID_PROY = ".$id;
        $rs_s = $this->conn->OpenRecordset($sql_s);
        while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            $arr[] = $row_rs_s[0];
        }
        $vo->id_deptos = $arr;

        //MPIOS
        $_m =  $this->getMpiosCobertura($id);
        $vo->id_muns = $_m['ids'];
        $vo->longitude = $_m['lon'];
        $vo->latitude = $_m['lat'];

        // ALBERGUES
        $arr = Array();
        $sql_s = "SELECT ID_ALBERGUE FROM albergue_proy WHERE ID_PROY = ".$id;
        $rs_s = $this->conn->OpenRecordset($sql_s);
        while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            $arr[] = $row_rs_s[0];
        }
        $vo->id_albergues = $arr;

        return $vo;

    }

    /**
     * Consulta la organizacion ejecutora,implementadora o donante de un proyecto
     * @access public
     * @param int $id_proy ID del proyecto
     * @param int $tipo Id del tipo 1=Ejecutora, 2=Donante, 3=Implementador
     * @return array $id_org Arreglo con los ID de las orgs ejecutoras
     */
    function getOrgs($id_proy, $tipo=1){

        $ids = Array();
        $sql = "SELECT v.id_org, nom_org, sig_org, valor_aporte, nomb_tipo_es, codigo
                FROM vinculorgpro AS v
                JOIN organizacion USING(id_org)
                JOIN tipo_org USING(id_tipo)
                WHERE ID_PROY = ".$id_proy." AND ID_TIPO_VINORGPRO = ".$tipo;
        $rs = $this->conn->OpenRecordset($sql);
        while ($row = $this->conn->FetchRow($rs)) {
            $ids['id'][] = $row[0];
            $ids['nom'][] = $row[1];
            $ids['sig'][] = $row[2];
            $ids['a'][] = $row[3];
            $ids['tipo'][] = $row[4];
            $ids['c'][] = $row[5];
        }

        return $ids;
    }

    /**
     * Consulta el aporte de un donante
     * @access public
     * @param int $id_proy ID del proyecto
     * @param int $id_donante Id del Donante
     * @return int $aporte Aporte del donante
     */
    function getAporteDonante($id_proy, $id_donante){

        $sql = "SELECT valor_aporte FROM vinculorgpro AS v
                WHERE ID_PROY = $id_proy AND ID_TIPO_VINORGPRO = 2 AND ID_ORG = $id_donante";

        $rs = $this->conn->OpenRecordset($sql);
        $row = $this->conn->FetchRow($rs);

        return (isset($row[0])) ? $row[0] : 0;
    }

    /**
     * Consulta las organizaciones que son ejecutoras
     * @access public
     * @param $fields Arreglo de campos a retornar ademas de ID
     * @param $id_tipo Tipo de vinculo 1=ejecutor, 2=donante
     * @return array $params Paramestros extras
     */
    function getOrgEjecutoraTotal($fields = null, $id_tipo = 1, $params=array()){

        if (!empty($fields)) {
            $fs = ', '.implode(',', $fields);
            $fso = $fields[0];
        }
        else {
            $fs = '';
            $fso = 'nom_org';
        }

        //ORGS. EJECUTORAS
        $arr = array();
        $sql_s = "SELECT DISTINCT(ID_ORG) $fs
                  FROM vinculorgpro AS v
                  JOIN organizacion USING(id_org)
                  JOIN tipo_org USING(id_tipo)
                  JOIN proyecto_tema USING(id_proy)
                  WHERE ID_TIPO_VINORGPRO = $id_tipo ";

       if (empty($params['no_conditionsi'])) {
           $sql_s .= $this->_setConditionSi(array('no_vs' => true));
       }

       $sql_s .= " ORDER BY ".$fso;

       //echo $sql_s;

        $rs_s = $this->conn->OpenRecordset($sql_s);
        $r = 0;
        while ($row_rs_s = $this->conn->FetchRow($rs_s)){

            if (!empty($fields)) {

                $arr[$r]['id_org'] = $row_rs_s[0];

                $c = 1;
                foreach($fields as $f) {
                    $arr[$r][$f] = $row_rs_s[$c];
                    $c++;
                }
            }
            else {
                $arr[] = $row_rs_s[0];
            }

            $r++;
        }

        return $arr;
    }

    /**
     * Numero de beneficiarios
     * @access public
     * @param int $id_proy ID del proyecto
     * @return array $n Arreglo con numero de beneficiarios por tipo de rel
     */
    function getCantBenef($id_proy){

        $si = $this->GetFieldValue($id_proy, 'si_proy');
        $benf = array();

        /*
        if ($si == 'undaf') {
            $sql = 'SELECT cant_per FROM proyecto_beneficiario WHERE id_proy = '.$id_proy;
            $rs = $this->conn->OpenRecordset($sql);
            $row = $this->conn->FetchRow($rs);
            $benf['d']['total'] = (empty($row[0])) ? 0 : $row[0];
        }
        else{
         */
            $_t = array(1 => 'd', 2 => 'i');
            $sqls = "SELECT * FROM p4w_beneficiario WHERE ID_PROY = $id_proy";
            $rss = $this->conn->OpenRecordset($sqls);
            while ($row = $this->conn->FetchObject($rss)){
                if (empty($row->EDAD_P4W_B) && empty($row->GENERO_P4W_B)) {
                    $benf[$_t[$row->TIPO_REL]]['total'] = $row->CANT_P4W_B;
                }
                else if (empty($row->EDAD_P4W_B) && !empty($row->GENERO_P4W_B)) {
                    $benf[$_t[$row->TIPO_REL]][$row->GENERO_P4W_B]['total'] = $row->CANT_P4W_B;
                }
                else {
                    $benf[$_t[$row->TIPO_REL]][$row->GENERO_P4W_B][$row->EDAD_P4W_B] = $row->CANT_P4W_B;
                }
            }
        //}

        return $benf;
    }

    /**
     * Consulta si un proyecto existe
     * @access public
     * @param array $pms Arreglo de parametros
     * @return int $id Id del proyecto
     */
    function checkExiste($pms){

        $cond = 'cobertura_nal_proy = '.$pms['nal'];
        $condd = '';
        $arr['s'] = false;

        $sql = "SELECT DISTINCT(id_proy) FROM proyecto AS p";

        if (!empty($pms['codigo'])) {
            $condc = " AND cod_proy = '".$pms['codigo']."'";

            // Exacto
            $sqle = $sql." WHERE $cond.$condc";
            $rse = $this->conn->OpenRecordset($sqle);
            $rowe = $this->conn->FetchRow($rse);

            $arr['e'] = $rowe[0];
            $arr['s'] = (empty($arr['e'])) ? false : true;

            return $arr;
        }

        // Se elimina check de fechas+ejecutor+ubicacion por OIM, entregan en el mismo lugar diferentes kits,
        // pendiente forma de importar OIM
        if (!empty($pms['nombre'])) {
            $cond .= " AND nom_proy = '".$pms['nombre']."'";
        }

        // que van en proyectos diferentes
        if (!empty($pms['f_ini'])) {
            $cond .= " AND inicio_proy = '".$pms['f_ini']."'";
        }

        if (!empty($pms['f_fin'])) {
            $cond .= " AND fin_proy = '".$pms['f_fin']."'";
        }

        if (!empty($pms['id_org'])) {
            $cond .= " AND id_org = ".$pms['id_org']." AND id_tipo_vinorgpro = 1";
            $sql .= " LEFT JOIN vinculorgpro USING($this->columna_id)";
        }

        if (!empty($pms['id_deptos'])) {
            $condd = " AND '".implode(',', $pms['id_deptos'])."' =
                            (SELECT GROUP_CONCAT(id_depto) FROM depto_proy AS dp WHERE
                             p.id_proy=dp.id_proy GROUP BY id_proy)";

            $sql .= " LEFT JOIN depto_proy USING($this->columna_id)";
        }

        if (!empty($pms['id_muns'])) {
            $condm = " AND '".implode(',', $pms['id_muns'])."' =
                            (SELECT GROUP_CONCAT(id_mun) FROM mun_proy AS mp WHERE
                             p.id_proy=mp.id_proy GROUP BY id_proy)";

            $sql .= " LEFT JOIN mun_proy USING($this->columna_id)";

            // Exacto
            $sqle = $sql." WHERE $cond.$condd.$condm";
            $rse = $this->conn->OpenRecordset($sqle);
            $rowe = $this->conn->FetchRow($rse);

            $arr['e'] = $rowe[0];
        }

        // Posibles duplicidades
        $sqld = $sql." WHERE $cond.$condd";
        //echo $sqld;
        $rs = $this->conn->OpenRecordset($sqld);
        while ($row = $this->conn->FetchRow($rs)){
            $arr['d'][] = $row[0];
        }

        $arr['s'] = (empty($arr['e']) && empty($arr['d'])) ? false : true;

        return $arr;
    }


    /**
     * Inserta un Proyecto en la B.D.
     * @access public
     * @param object $depto_vo VO de Proyecto que se va a insertar
     * @param int $msg Mostrar mensaje
     */
    function Insertar($vo, $msg=true){

        $ok = false;

        //CONSULTA SI YA EXISTE
        $a = $this->checkExiste(array('codigo' => (!empty($vo->cod_proy) ? $vo->cod_proy : ''),
                                      'f_ini' => (!empty($vo->inicio_proy) ? $vo->inicio_proy : ''),
                                      'f_fin' => (!empty($vo->fin_proy) ? $vo->fin_proy : ''),
                                      'id_org' => (!empty($vo->id_orgs_e[0]) ? $vo->id_orgs_e[0] : ''),
                                      'nombre' => (!empty($vo->nom_proy) ? $vo->nom_proy : ''),
                                      'id_deptos' => (!empty($vo->id_deptos) ? $vo->id_deptos : ''),
                                      'id_muns' => (!empty($vo->id_muns) ? $vo->id_muns : ''),
                                      'nal' => $vo->cobertura_nal_proy
                                ));

        if (empty($_POST['sigd']) && $a['s'] === false){
            $ok = true;
        }
        else if (!empty($_POST['sigd'])){
            $ok = true;
        }

        if ($ok){

            $sql = "INSERT INTO $this->tabla (id_mon,id_estp,id_emergencia,id_con,nom_proy,cod_proy,des_proy,obj_proy,inicio_proy,fin_proy,srp_proy,
                                                actua_proy,costo_proy,duracion_proy,info_conf_proy,staff_nal_proy,
                                                staff_intal_proy,cobertura_nal_proy,cant_benf_proy,valor_aporte_donantes,
                                                valor_aporte_socios,info_extra_donantes,info_extra_socios,joint_programme_proy,
                                                mou_proy,acuerdo_coop_proy,interv_ind_proy,otro_cual_benf_proy,si_proy,creac_proy,validado_cluster_proy, id_usuario)
                    VALUES ($vo->id_mon,$vo->id_estp,$vo->id_emergencia,$vo->id_con,'$vo->nom_proy','$vo->cod_proy','$vo->des_proy','$vo->obj_proy','$vo->inicio_proy','$vo->fin_proy',$vo->srp_proy,
                    now(),$vo->costo_proy,$vo->duracion_proy,$vo->info_conf_proy,$vo->staff_nal_proy,$vo->staff_intal_proy,$vo->cobertura_nal_proy,'
                    $vo->cant_benf_proy','$vo->valor_aporte_donantes','$vo->valor_aporte_socios',
                    '$vo->info_extra_donantes','$vo->info_extra_socios',$vo->joint_programme_proy,
                    $vo->mou_proy,$vo->acuerdo_coop_proy,$vo->interv_ind_proy,'$vo->otro_cual_benf_proy','$vo->si_proy',now(),$vo->validado_cluster_proy, ".$_SESSION['id_usuario_s'].")";

            $this->conn->Execute($sql);
            $id_proyecto = $this->GetMaxID();

            $this->InsertarTablasUnion($vo,$id_proyecto);
            $this->InsertarTablasUnionCobertura($vo,$id_proyecto);

            $ht = '<h1>Proyecto creado con &eacute;xito....</h1><br />
                    <a href="?accion=listar&class=P4wDAO&method=Dashboard" class="boton">Ir a Dashboard</a>&nbsp;
                    <a href="?accion=insertar" class="boton">Crear otro proyecto</a>
                    ';
        }
        else{
            $ht = "<h2>ERROR, Existe un proyecto igual o existen posbiles duplicidades!!!</h2> <br />
                    Recuerde que los campos que se usan para realizar la verificaci&oacute;n son:<br />
                        <b>Repetido</b>: C&oacute;digo u Organizaci&oacute;n encargada + Fecha de inicio + Fecha de finalizaci&oacute;n
                        + Departamentos + Municipios<br />
                        <b>Duplicidad</b>: Organizaci&oacute;n encargada + Fecha de inicio + Fecha de finalizaci&oacute;n +
                        Departamentos
                    <br /><br />";

            if (!empty($a['e'])) {
                $ht .= "<div>
                        <h3>Este es el proyecto que existe en el sistema</h3>
                        ".utf8_encode($this->GetFieldValue($a['e'], 'nom_proy'))."<br />
                        <a href='?accion=actualizar&id=".$a['e']."&ver=1' target='_blanck' class='external'>[ Ver Proyecto ]</a>
                      ";
            }
            else if (!empty($a['d'])) {
                $ht .= "<div>
                        <h3>Estas son las posibles duplicidades que existen en el sistema</h3>";

                foreach($a['d'] as $id) {

                    $ht .= "<div class='grid'>
                        ".utf8_encode($this->GetFieldValue($id, 'nom_proy'))."<br />
                        <a href='?accion=actualizar&id=$id&ver=1' target='_blanck' class='external'>[ Ver Proyecto ]</a>
                        </div>";
                }

                $ht .= '<div><br />Hay posibles duplicidades, desea crear el proyecto?<br />
                <a href="#" onclick="$j(\'#sigd\').val(1);$j(\'form\').submit();return false" class="boton">Si</a>
                <a href="#" onclick="closeWindow(); return false;" class="boton">No</a><br />
                </div>';

                $ht .= '<div>';
            }
        }

        if ($msg) {
            echo "<div class='alert'>$ht</div>";
        }

        return $a;
    }

    /**
     * Inserta las tablas de union para el Proyecto en la B.D.
     * @access public
     * @param object $proyecto_vo VO de Proyecto que se va a insertar
     * @param int $id_proyecto ID de la Proyecto que se acaba de insertar
     */
    function InsertarTablasUnion($proyecto_vo,$id_proyecto){

        //TEMAS
        $arr = $proyecto_vo->id_temas;
        foreach($arr as $id_tema=>$otros){
            $texto = (!empty($proyecto_vo->id_tema_p) && $proyecto_vo->id_tema_p == $id_tema) ? 'temap' : '';

            $pres = (empty($proyecto_vo->temas_presupuesto[$id_tema])) ? 0 : $proyecto_vo->temas_presupuesto[$id_tema];

            $sql = "INSERT INTO proyecto_tema (ID_TEMA,ID_PROY,DESC_PROY_TEMA,PRESUPUESTO) VALUES ($id_tema,$id_proyecto,'$texto',$pres)";
            //echo $sql;
            $this->conn->Execute($sql);

            //Hijos
            if (isset($proyecto_vo->id_temas[$id_tema]["hijos"])){
                $hijos = $proyecto_vo->id_temas[$id_tema]["hijos"];
                foreach($hijos as $id_hijo){
                    $sql = "INSERT INTO proyecto_tema (ID_TEMA,ID_PROY) VALUES ($id_hijo,$id_proyecto)";
                    $this->conn->Execute($sql);

                    ////echo $sql;
                }
            }

            //Nietos
            if (isset($proyecto_vo->id_temas[$id_tema]["nietos"])){
                $nietos = $proyecto_vo->id_temas[$id_tema]["nietos"];
                foreach($nietos as $id_nieto){
                    $sql = "INSERT INTO proyecto_tema (ID_TEMA,ID_PROY) VALUES ($id_nieto,$id_proyecto)";
                    $this->conn->Execute($sql);

                    ////echo $sql;
                }
            }
        }

        $_bx = array(1, 2, 3, 4);
        $_bg = array('h' => $_bx, 'm' => $_bx);
        $_b = array(1 => array('d' => $_bg),
                    2 => array('i' => $_bg)
                    );

        //POBLACION BENEFICIADA
        $benf_proy = $proyecto_vo->benf_proy;
        foreach($_b as $id_tipo => $b) {
            foreach($b as $tipo => $_g) {
                if (!empty($benf_proy[$tipo]['total'])) {
                    $sql = "INSERT INTO p4w_beneficiario (ID_PROY,CANT_P4W_B,TIPO_REL) VALUES
                                         ($id_proyecto,".$benf_proy[$tipo]['total'].",$id_tipo)";
                    //echo $sql;
                    $this->conn->Execute($sql);
                }
                foreach($_g as $g => $_p) {
                    if (!empty($benf_proy[$tipo][$g]['total'])) {
                        $sql = "INSERT INTO p4w_beneficiario (ID_PROY,CANT_P4W_B,GENERO_P4W_B,TIPO_REL) VALUES
                                         ($id_proyecto,".$benf_proy[$tipo][$g]['total'].",'$g',$id_tipo)";
                        //echo $sql;
                        $this->conn->Execute($sql);
                    }

                    foreach($_p as $p) {
                        if (!empty($benf_proy[$tipo][$g][$p])) {
                            $sql = "INSERT INTO p4w_beneficiario (ID_PROY,CANT_P4W_B,GENERO_P4W_B,EDAD_P4W_B,TIPO_REL) VALUES
                                         ($id_proyecto,".$benf_proy[$tipo][$g][$p].",'$g',$p,$id_tipo)";
                        //echo $sql;
                        $this->conn->Execute($sql);
                        }
                    }
                }
            }
        }

        //ORGANIZACIONES EJECUTORAS
        $arr = $proyecto_vo->id_orgs_e;
        foreach ($arr as $a){
            $sql = "INSERT INTO vinculorgpro (ID_TIPO_VINORGPRO,ID_ORG,ID_PROY,VALOR_APORTE) VALUES (1,$a,$id_proyecto,0)";
            //echo $sql;
            $this->conn->Execute($sql);
        }

        //ORGANIZACIONES DONANTES
        $arr = $proyecto_vo->id_orgs_d;
        foreach ($arr as $a){
            if (!empty($a)) {
                $valor = (empty($proyecto_vo->id_orgs_d_valor_ap[$a])) ? 0 : $proyecto_vo->id_orgs_d_valor_ap[$a];
                $codigo = (empty($proyecto_vo->id_orgs_d_codigo[$a])) ? '' : $proyecto_vo->id_orgs_d_codigo[$a];
                $sql = "INSERT INTO vinculorgpro (ID_TIPO_VINORGPRO,ID_ORG,ID_PROY,VALOR_APORTE,CODIGO) VALUES (2,$a,$id_proyecto,$valor,'$codigo')";
                ////echo $sql;
                $this->conn->Execute($sql);
            }
        }

        //ORGANIZACIONES SOCIOS
        $arr = $proyecto_vo->id_orgs_s;
        foreach ($arr as $a){
            if (!empty($a)) {
                $sql = "INSERT INTO vinculorgpro (ID_TIPO_VINORGPRO,ID_ORG,ID_PROY,VALOR_APORTE) VALUES (3,".$a.",".$id_proyecto.",0)";
                ////echo $sql;
                $this->conn->Execute($sql);
            }
        }
    }

    /**
     * Inserta las tablas de union de cobertura de la Proyecto en la B.D.
     * @access public
     * @param object $proyecto_vo VO de Proyecto que se va a insertar
     * @param int $id_proyecto ID de la Proyecto que se acaba de insertar
     */
    function InsertarTablasUnionCobertura($proyecto_vo,$id_proyecto){

        //DEPTOS
        $arr = $proyecto_vo->id_deptos;
        foreach($arr as $a){
            $sql = "INSERT INTO depto_proy (ID_DEPTO,ID_PROY) VALUES ('$a',".$id_proyecto.")";
            $this->conn->Execute($sql);
            //echo $sql;
        }

        //MUNICPIOS
        $arr = $proyecto_vo->id_muns;
        foreach($arr as $a){
            $lon = (empty($proyecto_vo->longitude[$a])) ? '' : $proyecto_vo->longitude[$a];
            $lat = (empty($proyecto_vo->latitude[$a])) ? '' : $proyecto_vo->latitude[$a];
            $sql = "INSERT INTO mun_proy (ID_MUN,ID_PROY,LONGITUDE,LATITUDE) VALUES ('$a',".$id_proyecto.",'$lon','$lat')";
            $this->conn->Execute($sql);
            //echo $sql;
        }

        // Albergues
        $arr = $proyecto_vo->id_albergues;
        foreach($arr as $a){
            $sql = "INSERT INTO albergue_proy (ID_ALBERGUE,ID_PROY) VALUES ('$a',".$id_proyecto.")";
            $this->conn->Execute($sql);
            //echo $sql;
        }
    }

    /**
     * Actualiza un Proyecto en la B.D.
     * @access public
     * @param object $depto_vo VO de Proyecto que se va a actualizar
     */
    function Actualizar($vo){

        $id = $vo->id_proy;

        $sql = "UPDATE $this->tabla SET
                id_mon = $vo->id_mon,
                id_estp = $vo->id_estp,
                id_emergencia = $vo->id_emergencia,
                id_con = $vo->id_con,
                nom_proy = '$vo->nom_proy',
                cod_proy = '$vo->cod_proy',
                des_proy = '$vo->des_proy',
                obj_proy = '$vo->obj_proy',
                inicio_proy = '$vo->inicio_proy',
                fin_proy = '$vo->fin_proy',
                srp_proy = '$vo->srp_proy',
                actua_proy = now(),
                costo_proy = $vo->costo_proy,
                duracion_proy = $vo->duracion_proy,
                info_conf_proy = $vo->info_conf_proy,
                staff_nal_proy = $vo->staff_nal_proy,
                staff_intal_proy = $vo->staff_intal_proy,
                cobertura_nal_proy = $vo->cobertura_nal_proy,
                cant_benf_proy = '$vo->cant_benf_proy',
                valor_aporte_donantes = '$vo->valor_aporte_donantes',
                valor_aporte_socios = '$vo->valor_aporte_socios',
                info_extra_donantes = '$vo->info_extra_donantes',
                info_extra_socios = '$vo->info_extra_socios',
                joint_programme_proy = $vo->joint_programme_proy,
                mou_proy = $vo->mou_proy,
                acuerdo_coop_proy = $vo->acuerdo_coop_proy,
                interv_ind_proy = $vo->interv_ind_proy,
                otro_cual_benf_proy = '$vo->otro_cual_benf_proy',
                validado_cluster_proy = $vo->validado_cluster_proy,
                si_proy = '$vo->si_proy'

               WHERE $this->columna_id = $id";

        $this->conn->Execute($sql);

        $this->BorrarTablasUnion($id);
        $this->BorrarTablasUnionCobertura($id);

        $this->InsertarTablasUnion($vo,$id);
        $this->InsertarTablasUnionCobertura($vo,$id);

        $ht = '<h1>Proyecto actualizado con &eacute;xito....</h1><br />
                <a href="?accion=listar&class=P4wDAO&method=Dashboard" class="boton">Ir a Dashboard</a>&nbsp;
                <a href="?accion=insertar" class="boton">Crear proyecto</a>
                ';

        echo "<div class='alert'>$ht</div>";
    }

    /**
     * Importa un archivo plano de proyectos
     * @param $userfile, archivo upload
     * @param int $importar 1= importar y preview, 0=preview
     * @access public
     */
    function Importar($importar){

        // File Uploader
        require_once 'lib/common/fileuploader.class.php';
        require_once 'lib/common/date.class.php';
        require_once 'lib/common/archivo.class.php';
        require_once 'lib/model/org.class.php';
        require_once 'lib/model/tipo_org.class.php';
        require_once 'lib/model/tema.class.php';
        require_once 'lib/model/municipio.class.php';
        require_once 'lib/model/p4w.class.php';
        require_once 'lib/model/depto.class.php';
        require_once 'lib/model/contacto.class.php';
        require_once 'lib/model/estado_proyecto.class.php';
        require_once 'lib/dao/municipio.class.php';
        require_once 'lib/dao/tipo_org.class.php';
        require_once 'lib/dao/org.class.php';
        require_once 'lib/dao/tema.class.php';
        require_once 'lib/dao/depto.class.php';
        require_once 'lib/dao/contacto.class.php';
        require_once 'lib/dao/estado_proyecto.class.php';

        //Inicializacion de variables
        $archivo = New Archivo();
        $org_dao = New OrganizacionDAO();
        $tipo_dao = New TipoOrganizacionDAO();
        $tema_dao = New TemaDAO();
        $depto_dao = New DeptoDAO();
        $mun_dao = New MunicipioDAO();
        $contacto_dao = New ContactoDAO();
        $estado_dao = New EstadoProyectoDAO();
        $date = new Date();

        // Solo se activa true en el formulario cuando no existen errores
        $insertar_db = ($importar == '1') ? true : false;

        $t_dir = '../../tmp/';
        $nc = 37;
        $sp = '|';
        $msg = '';
        $check = true;
        $cobli = array(1,2,3,4,5,7,8,9,10,11,12,14,20,21,22,23,34);
        $ni = 0;
        $ne = -1; // En -1 para mostrar error si falla alguna validaci�n b�sica de columnas, etc
        $id_org_new = $id_tema_new = $id_org_s_new = $id_mun_new = $id_depto_new = $id_con_new = $sectores = $contactos = array();

        $latin = array('�','�','�','�','�','�');
        $normal = array('a','e','i','o','u','n');

        $latinUpper = array('�','�','�','�','�','�','�');
        $latinLower = array('�','�','�','�','�','�','�');

        $re_yyyy_mm_dd = '/^(19|20)\d\d[-\/]([0-9]|0[1-9]|1[012])[-\/]([1-9]|0[1-9]|[12][0-9]|3[01])$/';
        $re_dd_mm_yyyy = '/^([1-9]|0[1-9]|[12][0-9]|3[01])[-\/]([1-9]|0[1-9]|1[012])[-\/](19|20)\d\d$/';
        $re_mm_dd_yyyy = '/^([1-9]|0[1-9]|1[012])[-\/]([1-9]|0[1-9]|[12][0-9]|3[01])[-\/](19|20)\d\d$/';


        // Checks aplicados
        $chks = array('ob' => false, // Check columnas obligatorias
                        'oe' => true, //Org encargada
                        'oo' => true, //Operador
                        's' => true, // Sector
                        'benef' => true, // Beneficiario
                        'f' => true, // Fechas
                        'p' => true, // Presupuesto
                        'cob' => true, // Cobertura
                        'con' => true, // Contacto
                     );

        // list of valid extensions, ex. array("jpeg", "xml", "bmp")
        $allowedExtensions = array();
        // max file size in bytes
        $sizeLimit = 2 * 1024 * 1024;

        $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
        $result = $uploader->handleUpload($t_dir);

        $path = $t_dir.'/'.$result['fn'];
        $fp = $archivo->Abrir($path,'r');
        $cf = $archivo->LeerEnArreglo($fp);
        $nf = count($cf); // Cuenta el arreglo cuyo ultimo elemento es una linea vacia que devuelve el fgets, por eso el while va hasta < no <=
        $archivo->Cerrar($fp);
        $fnc = count(explode($sp, $cf[1]));
        $np = 0;

        $r=2; // fila con titulos de columnas

        // Check separador
        if (strpos($cf[0], $sp) === false) {
            $msg = 'El archivo no esta separado por "|" (barra vertical)';
        }

        // Check numero de cols
        else if (!isset($cf[$r])) {
            $msg = 'El archivo debe tener m�nimo 3 filas';
        }

        // Check numero de cols
        else if ($fnc != $nc) {
            $msg =  "El archivo tiene $fnc columnas y deben ser $nc";
        }

        // OK para revisar la info
        else {
            $ne = 0;
            $msg = '<div>A continuaci�n las filas con errores</div>
            <table><tr><th>Fila</th><th>Error</th></tr>';
            $er = $insertar = false;
            $coda = $noma = 'xxx';
            $_sqlo = $_sqlc = '';
            $_csvo = 'Sigla,Nombre,Tipo\n';
            $_csvc = 'Sigla Org,Nombre Org,Nombre completo,Email,Tel/Cel';
            $sqio  = "INSERT INTO organizacion (nom_org,sig_org,id_tipo,info_confirmada,si_org) VALUES ('%s','%s',%d,0,'4w');\n";
            $sqic  = "INSERT INTO contacto (nom_con,email_con,tel_con) VALUES ('%s','%s','%s');\n";
            $retro = "ID 4W|Fila archivo|CODIGO INTERNO|Organizaci�n encargada|||Operador|||Sector|Intervenci�n||||||||Contacto en Terreno|||Poblaci�n Beneficiaria|Cobertura||||\n";
            $retro .= "|||Sigla|Nombre|Tipo|Sigla|Nombre|Tipo|Sector|Fecha de inicio|Fecha de finalizaci�n|Tiempo de ejecuci�n|Presupuesto USD$|CERF|ERF|OTRO|Descripci�n de la ayuda|Responsable|Correo Electr�nico|Celular|Personas|Divipola|Departamento|Municipio|Lat|Long\n";

            while ($r < $nf) {
                if (!empty($cf[$r]) || ($r == ($nf-1) && $insertar_db)) {
                //if (!empty($cf[$r])) {

                    //echo 'Fila: '.($r).'<br />';

                    if (!empty($cf[$r])) {

                        $f = explode($sp, utf8_decode($cf[$r]));

                        $cod_proy = $f[0];
                        $nom_proy = $f[1];
                        $des_proy = $f[2];
                        $estado_proy = strtolower(str_replace($latinUpper, $latinLower, trim($f[13])));

                        $er = false;
                        $_msg = '';
                        $cod_proy = (empty($cod_proy)) ? '' : trim($cod_proy);

                        if (!empty($nom_proy)) {
                            $nom_proy = $nom_proy;
                            $des_proy = (!empty($des_proy)) ? $des_proy : '4W :: Importado el '.date('c');
                        }
                        else {
                            if (!empty($dec_proy)) {
                                $nom_proy = $des_proy;
                            }
                        }

                        $cond_cod = (empty($cod_proy) || $coda != $cod_proy) ? true : false;

                        // Nombre si no tiene codigo
                        $cond_nom = ($cond_cod || empty($nom_proy) || $noma != $nom_proy) ? true : false;

                        // No tiene en cuenta los formulados, suspendidos, evaluacion
                        //$cond_estado = (in_array($estado_proy, array('formulaci�n','suspendido','evaluaci�n'))) ? false : true;
                    }

                    //INSERTA ********************
                    if ($r > 2 && $insertar && $cond_cod && $cond_nom && $insertar_db) {

                        $ch = $this->Insertar($p_vo, false);

                        if ($ch['s']) {
                            $idp = 'No importado --- Duplicidad con Proyecto ID = '.$ch['e'];
                            $_msg .= $idp;
                            $er = true;
                        }
                        else {
                            $idp = $this->GetMaxID().((empty($_msgr)) ? '' : '--- Importado con: '.$_msgr);
                            $ni++;
                        }

                        $retro .= $idp.'|'.($r).'|'.$cf[$r-1];

                        // Si es la �ltima fila, termina loop
                        if ($r == $nf) {
                            break;
                        }
                    }

                    //if ($cond_cod && $cond_nom && $cond_estado) {
                    if ($cond_cod && $cond_nom) {
                        $p_vo = New P4w();
                        $insertar = false;
                        $_msgr = '';

                        // Check obligatorios
                        if ($chks['ob']) {
                            $_msg = '';
                            foreach($cobli as $_c => $c) {
                                if (empty($f[$c])) {
                                    if (empty($_msg)) {
                                        $_msg .= 'Columnas obligatorias: ';
                                    }

                                    $_msg .= chr(65 + $c).' - ';
                                    $er = true;
                                }
                            }
                        }

                        // Se asigna codigo si no tiene
                        $col = 0;
                        if (!empty($p_vo->id_orgs_e[0]) && empty($cod_proy)) {
                            $_sq = 'SELECT COUNT(id_proy) FROM vinculorgpro WHERE
                                id_org = '.$p_vo->id_orgs_e[0].' AND id_tipo_vinorgpro = 1';
                            $_rs = $this->conn->OpenRecordset($_sq);
                            $_row = $this->conn->FetchRow($_rs);
                            $num = (empty($_row[$col])) ? 1 : ($_row[$col] + 1);

                            $cod_proy = '4W-'.$s.'-'.$num;
                        }

                        $col = 3;
                        if ($chks['oe'] && !$er) {
                            // Checks Orgs Encargada
                            $os = $f[$col];
                            $on = $f[++$col];
                            $ot = $f[++$col];
                            if (!empty($os) && !empty($on)) {
                                $s = str_replace($latin, $normal, trim(strtolower($os)));
                                $n = str_replace($latin, $normal, trim(strtolower($on)));
                                $t = trim($ot);
                                $kk = "Sigla:$s,Nombre:$n,Tipo:$t";

                                $orgs = $org_dao->GetAllArrayID("nom_org LIKE '%$n%' AND sig_org LIKE '%$s%'",'','');
                                $tipo = $tipo_dao->GetAllArrayID("nomb_tipo_es LIKE '%$t%'");
                                $tid = (empty($tipo[0])) ? 0 : $tipo[0];

                                if (empty($orgs[0]) && !in_array($kk, $id_org_new)) {
                                    $_msg .= "No existe la Org. Encargada: <b>$kk</b> <br />";
                                    $id_org_new[] = $kk;
                                    $er = true;

                                    if (!empty($tid)) {
                                        $_sqlo .= "# Fila: ".($r+1)." Org. Encargada \n".sprintf($sqio,$n,$s,$tid);
                                        $_csvo .= "$n,$s,$tid";
                                    }
                                }
                                else if (isset($orgs[0])) {
                                    $p_vo->id_orgs_e[] = $orgs[0];
                                }
                            }
                            // Solo sigla
                            else if (!empty($os) && empty($on)) {
                                $s = str_replace($latin, $normal, strtolower(trim($os)));
                                $kk = "Sigla:$s";

                                $orgs = $org_dao->GetAllArrayID("sig_org LIKE '%$s%'",'','');

                                if (empty($orgs[0]) && !in_array($kk, $id_org_new)) {
                                    $_msg .= "No existe la Org. Encargada: <b>$kk</b> <br />";
                                    $id_org_new[] = $kk;
                                    $er = true;
                                }
                                else if (isset($orgs[0])) {
                                    $p_vo->id_orgs_e[] = $orgs[0];
                                }
                            }
                            // Solo nombre
                            else if (empty($os) && !empty($on)) {
                                $n = str_replace($latin, $normal, strtolower(trim($on)));
                                $kk = "Nombre:$n";

                                $orgs = $org_dao->GetAllArrayID("nom_org LIKE '%$n%'",'','');

                                if (empty($orgs[0]) && !in_array($kk, $id_org_new)) {
                                    $_msg .= "No existe la Org. Encargada: <b>$kk</b> <br />";
                                    $id_org_new[] = $kk;
                                    $er = true;
                                }
                                else if (isset($orgs[0])) {
                                    $p_vo->id_orgs_e[] = $orgs[0];
                                }
                            }
                            else {
                                $_msgr .= " - No hay Org. Encargada";
                                $_msg .= "No hay Org. Encargada<br />";
                                $er = true;
                            }
                        }


                        // Checks Operador
                        $os = $f[++$col];
                        $on = $f[++$col];
                        $ot = $f[++$col];
                        if (!empty($os) && !empty($on)) {

                            $_v = explode(',', trim($os));
                            $_n = explode(',', trim($on));

                            foreach($_v as $i => $v) {

                                $s = str_replace($latin, $normal, strtolower(trim($v)));
                                $n = str_replace($latin, $normal, strtolower(trim($_n[$i])));
                                $t = str_replace($latin, $normal, strtolower(trim($ot)));
                                $kk = "Sigla:$s,Nombre:$n,Tipo:$t";

                                $orgs = $org_dao->GetAllArrayID("nom_org LIKE '%$n%' AND sig_org LIKE '%$s%'",'','');
                                $tipo = $tipo_dao->GetAllArrayID("nomb_tipo_es LIKE '%$t%'");
                                $tid = (empty($tipo[0])) ? 0 : $tipo[0];

                                if (empty($orgs[0]) && !in_array($kk, $id_org_new)) {
                                    $_msgr .= " - No existe el operador: $kk";
                                    if ($chks['oo']) {

                                        $_msg .= "No existe el operador: <b>$kk</b> <br />";
                                        $id_org_new[] = $kk;
                                        $er = true;

                                        if (!empty($tid)) {
                                            $_sqlo .= "# Fila: ".($r+1).", Operador \n".sprintf($sqio,$n,$s,$tid);
                                            $_csvo .= "$n,$s,$tid";
                                        }
                                    }
                                }
                                else if (isset($orgs[0])) {
                                    $p_vo->id_orgs_s[] = $orgs[0];
                                }
                            }
                        }
                        // Solo sigla
                        else if (!empty($os) && empty($on)) {

                            $_v = explode(',', trim($os));

                            foreach($_v as $i => $v) {

                                $s = str_replace($latin, $normal, strtolower(trim($v)));
                                $kk = "Sigla:$s";

                                $orgs = $org_dao->GetAllArrayID("sig_org LIKE '%$s%'",'','');

                                if (empty($orgs[0]) && !in_array($kk, $id_org_new)) {
                                    $_msgr .= " - No existe el operador: $kk";
                                    if ($chks['oo']) {
                                        $_msg .= "No existe el operador: <b>$kk</b> <br />";
                                        $id_org_new[] = $kk;
                                        $er = true;
                                    }
                                }
                                else if (isset($orgs[0])) {
                                    $p_vo->id_orgs_s[] = $orgs[0];
                                }
                            }
                        }
                        // Solo nombre
                        else if (empty($os) && !empty($on)) {

                            $_v = explode(',', trim($on));

                            foreach($_v as $i => $v) {

                                $s = '';
                                $_n = ucwords(strtolower(str_replace($latinUpper, $latinLower, trim($v))));

                                $n = str_replace($latin, $normal, strtolower($_n));
                                $t = str_replace($latin, $normal, strtolower(trim($ot)));
                                $kk = "Nombre:$v,Tipo:$t";

                                $orgs = $org_dao->GetAllArrayID("nom_org LIKE '%$n%'",'','');
                                $tipo = $tipo_dao->GetAllArrayID("nomb_tipo_es LIKE '%$t%'");
                                $tid = (empty($tipo[0])) ? 0 : $tipo[0];

                                if (empty($orgs[0]) && !in_array($kk, $id_org_new)) {
                                    $_msgr .= " - No existe el operador: $kk";
                                    if ($chks['oo']) {
                                        $_msg .= "No existe el operador: <b>$kk</b> <br />";
                                        $id_org_new[] = $kk;
                                        $er = true;

                                        if (!empty($tid)) {
                                            $_sqlo .= "# Fila: ".($r+1).", Operador \n".sprintf($sqio,$_n,$s,$tid);
                                            $_csvo .= "$n,$s,$tid";
                                        }
                                    }
                                }
                                else if (isset($orgs[0])) {
                                    $p_vo->id_orgs_s[] = $orgs[0];
                                }
                            }
                        }
                        else {

                            $_msgr .= " - No hay operador";

                            if ($chks['oo']) {
                                $_msg .= "No hay operador<br />";
                                $er = true;
                            }
                        }

                        // Sector, resultados
                        $sec = $f[++$col];
                        if ($chks['s'] && !empty($sec)) {
                            $_v = explode(';', $sec);

                            $from = array_merge($latin, array('Albergues y elementos no alimentarios'));
                            $to =   array_merge($normal, array('Alojamiento y ayuda no alimentaria'));

                            foreach($_v as $v) {

                                $v = str_replace($from, $to, trim($v));

                                if (in_array($v, $sectores)) {
                                    $_idt = array_search($v, $sectores);
                                    $p_vo->id_temas[$_idt] = array();
                                    $p_vo->id_tema_p = $_idt;
                                }
                                else {
                                    $sec = $tema_dao->GetAllArrayID("nom_tema LIKE '%$v%' AND id_clasificacion IN (2,4)");
                                    if (empty($sec)) {
                                        $_msg .= "No existe el sector: <b>$v</b> <br />";
                                        $er = true;
                                        if (!in_array($v, $id_tema_new)) {
                                            $id_tema_new[] = $v;
                                        }
                                    }
                                    else if (isset($sec[0]) && !array_key_exists($sec[0], $p_vo->id_temas)) {
                                        $p_vo->id_temas[$sec[0]] = array();
                                        $p_vo->id_tema_p = $sec[0];
                                        $sectores[$sec[0]] = $v;
                                    }
                                }
                            }
                        }
                        else if ($chks['s'] && empty($sec)) {
                            $_msg .= "No existe el sector: <b>$sec</b> <br />";
                        }

                        // Fecha inicio
                        $fini = $f[++$col];
                        if (!empty($fini)) {
                            $_f = trim($fini);
                            $v = explode('/', $_f);
                            if (count($v) != 3) {
                                if ($chks['f']) {
                                    $_msg .= "Fecha de inicio incorrecta, no tiene 3 elementos: <b>$fini</b> <br />";
                                    $er = true;
                                }
                            }
                            else {
                                // Check formato
                                // yyyy-/mm-/dd
                                if (preg_match($re_yyyy_mm_dd, $_f)) {
                                    $p_vo->inicio_proy = $v[0].'-'.$v[1].'-'.$v[2];
                                }
                                // dd-/mm-/yyyy
                                else if (preg_match($re_dd_mm_yyyy, $_f) ) {
                                    $p_vo->inicio_proy = $v[2].'-'.$v[1].'-'.$v[0];
                                }
                                // mm-/dd-/yyyy
                                else if (preg_match($re_mm_dd_yyyy, $_f) ) {
                                    $p_vo->inicio_proy = $v[2].'-'.$v[0].'-'.$v[1];
                                }
                                else {
                                    $_msgr .= " - Fecha de inicio incorrecta: <b>$_f</b>";
                                    if ($chks['f']) {
                                        $_msg .= "Fecha de inicio incorrecta: <b>$_f</b> <br />";
                                        $er = true;
                                    }
                                }
                            }
                        }
                        else {
                            $_msgr .= " - No tiene fecha de inicio";
                            if ($chks['f']) {
                                $_msg .= "No tiene fecha de inicio<br />";
                                $er = true;
                            }
                        }

                        // Fecha final
                        $ffinal = $f[++$col];
                        if (!empty($ffinal)) {
                            $_f = $ffinal;
                            $v = explode('/', $_f);
                            if (count($v) != 3) {
                                $_msg .= "Fecha final incorrecta: <b>$ffinal</b> <br />";
                                $er = true;
                            }
                            else {
                                // Check formato
                                // yyyy-mm-dd
                                if (preg_match($re_yyyy_mm_dd, $_f)) {
                                    $p_vo->fin_proy = $v[0].'-'.$v[1].'-'.$v[2];
                                }
                                // dd-mm-yyyy
                                else if (preg_match($re_dd_mm_yyyy, $_f) ) {
                                    $p_vo->fin_proy = $v[2].'-'.$v[1].'-'.$v[0];
                                }
                                // mm-dd-yyyy
                                else if (preg_match($re_mm_dd_yyyy, $_f) ) {
                                    $p_vo->fin_proy = $v[2].'-'.$v[0].'-'.$v[1];
                                }
                                else {
                                    $_msgr .= " - Fecha de finalizaci�n incorrecta: <b>$_f</b>";
                                    if ($chks['f']) {
                                        $_msg .= "Fecha de finalizaci�n incorrecta: <b>$_f</b> <br />";
                                        $er = true;
                                    }
                                }
                            }
                        }
                        else {
                            $_msgr .= " - No tiene fecha de finalizaci�n";
                            if ($chks['f']) {
                                $_msg .= "No tiene fecha de finalizaci�n<br />";
                                $er = true;
                            }
                        }

                        // Duracion proy
                        $duracion_proy = $f[++$col];
                        if (empty($duracion_proy)) {
                            $p_vo->duracion_proy = $date->RestarFechas($p_vo->inicio_proy,$p_vo->fin_proy);
                        }
                        else {
                            $p_vo->duracion_proy = $duracion_proy;
                        }

                        // Estado
                        $estado = $estado_proy;
                        //if (empty($estado_proy)) {
                            $estado = (strtotime('now') < $p_vo->fin_proy) ? 3 : 4;
                        //}
                        /*
                        else {
                            $_estp = $estado_dao->GetAllArray("nom_estp LIKE '%".$estado_proy."%'");
                            if (empty($_estp)) {
                                $_msg .= "No existe el estado de proyecto: <b>$estado_proy</b> <br />";
                                $er = true;
                            }
                            else if (isset($_estp[0])) {
                                $estado = $_estp[0]->id;
                            }
                        }
                         */

                        // Presupuesto
                        $col = $col + 2;
                        $p_vo->costo_proy = 0;
                        $presu = floor($f[$col]);
                        if (isset($presu) && is_numeric($presu)) {
                            $p_vo->costo_proy = number_format(trim($presu),0,'','');
                        }
                        else {
                            $_msgr .= ' - No tiene Presupuesto';
                            if ($chks['p']) {
                                $_msg .= "No tiene presupuesto - $presu<br />";
                                $er = true;
                            }
                        }

                        // Checks Donante
                        $os = $f[++$col];
                        $on = $os;
                        $om = $f[++$col];
                        /*
                        if ($os) {
                            $p_vo->id_orgs_d[] = 4507; // Id org Central Emergency Respond Found
                            $p_vo->id_orgs_d_valor_ap[4507] = $p_vo->costo_proy;
                        }
                        else if (!empty($erf)) {
                            $p_vo->id_orgs_d[] = 6649; // Id org Emergency Respond Found
                            $p_vo->id_orgs_d_valor_ap[6649] = $p_vo->costo_proy;
                        }
                        else {
                         */
                            if (!empty($os) && !empty($on)) {

                                $_v = explode(',', trim($os));
                                $_n = explode(',', trim($on));

                                foreach($_v as $i => $v) {

                                    $s = str_replace($latin, $normal, strtolower(trim($v)));
                                    $n = str_replace($latin, $normal, strtolower(trim($_n[$i])));
                                    $t = str_replace($latin, $normal, strtolower(trim($ot)));
                                    $kk = "Sigla:$s,Nombre:$n,Tipo:$t";

                                    $orgs = $org_dao->GetAllArrayID("nom_org LIKE '%$n%' AND sig_org LIKE '%$s%'",'','');
                                    $tipo = $tipo_dao->GetAllArrayID("nomb_tipo_es LIKE '%$t%'");
                                    $tid = (empty($tipo[0])) ? 0 : $tipo[0];

                                    if (empty($orgs[0]) && !in_array($kk, $id_org_new)) {
                                        $_msgr .= " - No existe el donante: $kk";
                                        if ($chks['oo']) {

                                            $_msg .= "No existe el donante: <b>$kk</b> <br />";
                                            $id_org_new[] = $kk;
                                            $er = true;

                                            if (!empty($tid)) {
                                                $_sqlo .= "# Fila: ".($r+1).", donante \n".sprintf($sqio,$n,$s,$tid);
                                                $_csvo .= "$n,$s,$tid";
                                            }
                                        }
                                    }
                                    else if (isset($orgs[0])) {
                                        $p_vo->id_orgs_d[] = $orgs[0];
                                        $p_vo->id_orgs_d_valor_ap[] = $om;
                                    }
                                }
                            }
                            // Solo sigla
                            else if (!empty($os) && empty($on)) {

                                $_v = explode(',', trim($os));

                                foreach($_v as $i => $v) {

                                    $s = str_replace($latin, $normal, strtolower(trim($v)));
                                    $kk = "Sigla:$s";

                                    $orgs = $org_dao->GetAllArrayID("sig_org LIKE '%$s%'",'','');

                                    if (empty($orgs[0]) && !in_array($kk, $id_org_new)) {
                                        $_msgr .= " - No existe el donante: $kk";
                                        if ($chks['oo']) {
                                            $_msg .= "No existe el donante: <b>$kk</b> <br />";
                                            $id_org_new[] = $kk;
                                            $er = true;
                                        }
                                    }
                                    else if (isset($orgs[0])) {
                                        $p_vo->id_orgs_d[] = $orgs[0];
                                        $p_vo->id_orgs_d_valor_ap[] = $om;
                                    }
                                }
                            }
                            // Solo nombre
                            else if (empty($os) && !empty($on)) {

                                $_v = explode(',', trim($on));

                                foreach($_v as $i => $v) {

                                    $s = '';
                                    $_n = ucwords(strtolower(str_replace($latinUpper, $latinLower, trim($v))));

                                    $n = str_replace($latin, $normal, strtolower($_n));
                                    $t = str_replace($latin, $normal, strtolower(trim($ot)));
                                    $kk = "Nombre:$v,Tipo:$t";

                                    $orgs = $org_dao->GetAllArrayID("nom_org LIKE '%$n%'",'','');
                                    $tipo = $tipo_dao->GetAllArrayID("nomb_tipo_es LIKE '%$t%'");
                                    $tid = (empty($tipo[0])) ? 0 : $tipo[0];

                                    if (empty($orgs[0]) && !in_array($kk, $id_org_new)) {
                                        $_msgr .= " - No existe el donante: $kk";
                                        if ($chks['oo']) {
                                            $_msg .= "No existe el donante: <b>$kk</b> <br />";
                                            $id_org_new[] = $kk;
                                            $er = true;

                                            if (!empty($tid)) {
                                                $_sqlo .= "# Fila: ".($r+1).", donante \n".sprintf($sqio,$_n,$s,$tid);
                                                $_csvo .= "$n,$s,$tid";
                                            }
                                        }
                                    }
                                    else if (isset($orgs[0])) {
                                        $p_vo->id_orgs_d[] = $orgs[0];
                                        $p_vo->id_orgs_d_valor_ap[] = $om;
                                    }
                                }
                            }
                        //}

                        // Check duplicidades
                        /*
                        $codigo = $f[0];
                        $nombre = '';
                        if ($this->checkExiste(compact('codigo', 'id_org', 'nombre'))) {
                        }
                        */
                    }

                    // SRP
                    $srp = trim($f[++$col]);

                    if ($srp == 1) {
                        $p_vo->srp_proy = 1;
                    }
                    else {
                        $p_vo->srp_proy = 0;
                    }

                    // Contacto
                    $cn = $f[++$col];
                    $ce = $f[++$col];
                    $cc = $f[++$col];
                    $p_vo->id_con = 0;
                    if ($chks['con'] && !empty($ce)) {
                        $v = trim($cn);

                        if (in_array($v, $contactos)) {
                            $p_vo->id_con = array_search($v, $contactos);
                        }
                        else {
                            $v = trim($ce);
                            $contacto = $contacto_dao->GetAllArray("email_con LIKE '%$v%'");
                            if (empty($contacto)) {
                                $_msg .= "No existe el contacto: <b>$v</b> <br />";
                                $er = true;

                                if (!in_array($v, $id_con_new)) {
                                    $id_con_new[] = $v;
                                    $_sqlc .= sprintf($sqic,$cn,$ce,$cc);
                                    //$_csvc .= "$f[1],$f[2],$f[16],$f[17],$f[18]";
                                }
                            }
                            else if (isset($contacto[0])) {
                                $p_vo->id_con = $contacto[0]->id;
                                $contactos[$p_vo->id_con] = $v;
                            }
                        }
                    }


                    // Beneficiarios
                    $benef = $f[++$col];
                    $benf_g = array(
                                    ++$col => array('m','total'),
                                    ++$col => array('m',1),
                                    ++$col => array('m',2),
                                    ++$col => array('m',3),
                                    ++$col => array('m',4),
                                    ++$col => array('h','total'),
                                    ++$col => array('h',1),
                                    ++$col => array('h',2),
                                    ++$col => array('h',3),
                                    ++$col => array('h',4),
                                    );
                    $str_benef = strlen($benef);
                    if (!empty($str_benef)) {

                        preg_match("/\d+/", trim($benef), $v);

                        if ($benef == '0' || !empty($v[0])) {
                            $p_vo->benf_proy['d']['total'] = $v[0];

                            // Benf. por genero y rango de edad
                            foreach($benf_g as $_g => $_s) {
                                preg_match("/\d+/", trim($f[$_g]), $v);
                                if (!empty($v[0])) {
                                    $p_vo->benf_proy['d'][$_s[0]][$_s[1]] = $v[0];
                                }
                            }
                        }
                        else {
                            $_msgr .= "Beneficiario=$benef es texto<br />";
                            if ($chks['benef']) {
                                $_msg .= "Beneficiario=$benef es texto<br />";
                                $er = true;
                            }
                        }

                    }
                    else {
                        $_msgr .= " - No tiene beneficiarios";
                        if ($chks['benef']){
                            $_msg .= "No tiene beneficiarios<br />";
                            $er = true;
                        }
                    }


                    // Divipola
                    $kws = array('departamental');
                    $divipola = $f[++$col];
                    $departamento = $f[++$col];
                    $municipio = $f[++$col];
                    $latitud = $f[++$col];
                    $longitud = $f[++$col];
                    $cob_nal = 0;

                    if (strtolower($divipola) == 'nacional' || strtolower($departamento) == 'nacional' || strtolower($municipio) == 'nacional') {
                        $con_nal = 1;
                    }
                    else {
                        $in_kws = in_array(trim(strtolower($municipio)), $kws);

                        if ($chks['cob'] && !empty($divipola) && !$in_kws) {
                            $_v = explode(',', $divipola);
                            foreach($_v as $dv) {
                                preg_match("/\d+/", trim($dv), $v);

                                // Tiene divipola
                                if (isset($v[0])) {
                                    // 4 digitos
                                    if (strlen($v[0]) == 4) {
                                        $v[0] = '0'.$v[0];
                                    }

                                    $ms = $mun_dao->GetAllArrayID("id_mun = '".$v[0]."'",'');
                                    if (!isset($ms[0]) && !in_array($v, $id_mun_new)) {
                                        $_msg .= "No existe el divipola: <b>$v[0]</b> <br />";
                                        $id_mun_new[] = $v;
                                        $er = true;
                                    }
                                    else {
                                        if (!in_array($v[0], $p_vo->id_muns)) {
                                            $p_vo->id_muns[] = $v[0];
                                        }

                                        if (!in_array(substr($v[0],0,2), $p_vo->id_deptos)) {
                                            $p_vo->id_deptos[] = substr($v[0],0,2);
                                        }

                                        if (!empty($latitud) && !empty($longitud)) {
                                            $p_vo->longitude[$v[0]] = $longitud;
                                            $p_vo->latitude[$v[0]] = $latitud;
                                        }
                                    }
                                }
                            }
                        }
                        // No divipola si nombre mpio
                        else if (!empty($municipio) && !$in_kws) {
                            $_v = explode(',', $municipio);
                            foreach($_v as $v) {
                                $v = str_replace(array('�','�','�','�','�','�'), array('a','e','i','o','u','n'), trim($v));
                                $ms = $mun_dao->GetAllArrayID("nom_mun LIKE '%".$v."%'",'');
                                if (empty($ms) && !in_array($v, $id_mun_new)) {
                                    $_msg .= "No existe el municipio: <b>$v</b> <br />";
                                    $id_mun_new[] = $v;
                                    $er = true;
                                }
                                else if (!empty($ms[0])) {

                                    if (!in_array($ms[0], $p_vo->id_muns)) {
                                        $p_vo->id_muns[] = $ms[0];
                                    }

                                    if (!in_array(substr($ms[0],0,2), $p_vo->id_deptos)) {
                                        $p_vo->id_deptos[] = substr($ms[0],0,2);
                                    }

                                    if (!empty($latitud) && !empty($longitud)) {
                                        $p_vo->longitude[$ms[0]] = $longitud;
                                        $p_vo->latitude[$ms[0]] = $latitud;
                                    }
                                }
                            }
                        }
                        // Solo nombre depto
                        else if ((empty($divipola) && !empty($departamento) && empty($municipio)) || $in_kws) {
                            $_v = explode(',', $departamento);
                            foreach($_v as $v) {
                                $v = str_replace(array('�','�','�','�','�','�'), array('a','e','i','o','u','n'), trim($v));
                                // Solo depto
                                $ds = $depto_dao->GetAllArrayID("nom_depto LIKE '%".$v."%'");
                                if (empty($ds)) {

                                    $_msg .= "No existe el departamento: <b>$v</b> <br />";
                                    $er = true;

                                    if (!in_array($v, $id_depto_new)) {
                                        $id_depto_new[] = $v;
                                    }
                                }
                                else {
                                    if (!in_array($ds[0], $p_vo->id_deptos)) {
                                        $p_vo->id_deptos[] = $ds[0];
                                    }
                                }
                            }
                        }
                        else {
                            $_msg .= "No tiene cobertura<br />";
                            $er = true;
                        }
                    }

                    if ($er) {
                        $ne++;
                        $msg .= "<tr><td>".($r+1)."</td><td>$_msg</td></tr>";
                        $retro .= 'No Importado --- '.str_replace(array('<br />','<b>','</b>'), array('-','',''), $_msg).'|'.($r+1).'|'.$cf[$r];
                    }
                    else {

                        // Completa valores
                        $p_vo->cod_proy = $cod_proy;
                        $p_vo->nom_proy = $nom_proy ;
                        $p_vo->des_proy = $des_proy ;
                        $p_vo->id_mon = 1;
                        $p_vo->id_estp = $estado;
                        $p_vo->info_conf_proy = 0;
                        $p_vo->info_conf_proy = 0;
                        $p_vo->staff_nal_proy = 0;
                        $p_vo->staff_intal_proy = 0;
                        $p_vo->cobertura_nal_proy = $cob_nal;
                        // Ahora el marco viene de un combo, es necesario hacer case
                        $p_vo->joint_programme_proy = 0;
                        $p_vo->mou_proy = 0;
                        $p_vo->acuerdo_coop_proy = 0;
                        $p_vo->interv_ind_proy =0;
                        //Oficina desde la que se cubre
                        $p_vo->id_orgs_cubre = Array();
                        //Trabajo coordinado
                        $p_vo->id_orgs_coor = Array();
                        // Trabajo coordinado Aportes
                        $p_vo->id_orgs_coor_valor_ap = Array();
                        $p_vo->cant_benf_proy = '';
                        $p_vo->validado_cluster_proy = 1;
                        $p_vo->id_emergencia = 0;

                        $p_vo->duracion_proy = $duracion_proy;

                        $si_proy = array();
                        foreach($p_vo->id_temas as $id_tema) {

                            if ($id_tema >= 125 && $id_tema <= 163 && !in_array('4w', $si_proy)) {
                                $si_proy[] = '4w';
                            }

                            if ($id_tema >= 164 and $id_tema <= 173 && !in_array('des', $si_proy)) {
                                $si_proy[] = 'des';
                            }
                        }

                        $p_vo->si_proy = implode('-', $si_proy);

                        /*
                        if (!empty($p_vo->inicio_proy) && !empty($p_vo->fin_proy)) {
                            //Calcula los meses
                            $date = new Date();
                            $p_vo->duracion_proy = $date->RestarFechas($p_vo->inicio_proy,$p_vo->fin_proy, 'meses');
                        }
                         */

                        $insertar = true;
                    }

                    $coda = $cod_proy;
                    $noma = $nom_proy;
                    $np++;
                }

                $r++;
            }
        }

        $rsu = '<span class="total">Registros en el archivo: '.$np.'</span>
                <span class="ok">Proyectos importados: '.$ni.'</span>
                <span class="error">Proyectos con errores : '.$ne.'</span>';


        if ($ne == -1 && $insertar_db) {
            $msg = '';
        }

        $result['rsu'] = utf8_encode($rsu);
        $result['msg'] = utf8_encode($msg);
        $result['check'] = ($ne == 0) ? true : false;
        $result['ne'] = $ne;
        $result['h2'] = (empty($msg)) ? 'RESUMEN' : 'ERROR';

        // Error
        if (!empty($msg)) {
            $archivo->Borrar($path);
        }

        if (!empty($retro)) {
            // Retro-alimentaci�n
            $f = $archivo->Abrir($t_dir.'/importados_'.date('Y-m-d').'.csv', 'w+');
            $archivo->Escribir($f,$retro);
            $archivo->Cerrar($f);
        }

        if (!empty($_sqlo)) {

            $f = $archivo->Abrir($t_dir.'/sql_orgs.sql', 'w+');
            $archivo->Escribir($f,utf8_encode($_sqlo));

        }

        if (!empty($_csvo)) {
            $f = $archivo->Abrir($t_dir.'/orgs.csv', 'w+');
            $archivo->Escribir($f,$_csvo);

        }
        if (!empty($_sqlc)) {
            $f = $archivo->Abrir($t_dir.'/sql_cons.sql', 'w+');
            $archivo->Escribir($f,$_sqlc);
        }

        if (!empty($_csvc)) {
            $f = $archivo->Abrir($t_dir.'/cons.csv', 'w+');
            $archivo->Escribir($f,$_csvc);
            $archivo->Cerrar($f);
        }

        echo json_encode($result);

    }

    /**
     * Procesa archivo CSV para importar
     * @param boolean $importar, Especifica si se debe insertar en la base de
     * datos
     * @access public
     */
    function procesarCSVFile($importar){

    }

    /**
     * Borra un Proyecto en la B.D.
     * @access public
     * @param int $id ID del Proyecto que se va a borrar de la B.D
     */
    function Borrar($id){

        //BORRA TABLAS DE UNION
        $this->BorrarTablasUnion($id);
        $this->BorrarTablasUnionCobertura($id);

        //BORRA LA ORG.
        $sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
        $this->conn->Execute($sql);

        echo json_encode(array('success' => 1));
    }

    /**
     * Borra las tablas de union de un Proyecto en la B.D.
     * @access public
     * @param int $id ID del Proyecto que se va a borrar de la B.D
     */
    function BorrarTablasUnion($id){

        //TEMAS
        $sql = "DELETE FROM proyecto_tema WHERE ".$this->columna_id." = ".$id;
        $this->conn->Execute($sql);
            /*
            //SECTOR
            $sql = "DELETE FROM sector_proy WHERE ".$this->columna_id." = ".$id;
            $this->conn->Execute($sql);

            //ENFOQUE
            $sql = "DELETE FROM enfoque_proy WHERE ".$this->columna_id." = ".$id;
            $this->conn->Execute($sql);

            //CONTACTOS
            $sql = "DELETE FROM proyecto_conta WHERE ".$this->columna_id." = ".$id;
            $this->conn->Execute($sql);
             */

        //CONTACTOS
        $sql = "DELETE FROM proyecto_tema WHERE ".$this->columna_id." = ".$id;
        $this->conn->Execute($sql);

        //POBLACION BENEFICIADA
        $sql = "DELETE FROM p4w_beneficiario WHERE ".$this->columna_id." = ".$id;
        $this->conn->Execute($sql);

        //ORGANIZACIONES EJECUTORAS - DONANTES
        $sql = "DELETE FROM vinculorgpro WHERE ".$this->columna_id." = ".$id;
        $this->conn->Execute($sql);

    }

    /**
     * Borra las tablas de union de un Proyecto en la B.D.
     * @access public
     * @param int $id ID del Proyecto que se va a borrar de la B.D
     */
    function BorrarTablasUnionCobertura($id){

        //DEPTOS
        $sql = "DELETE FROM depto_proy WHERE ".$this->columna_id." = ".$id;
        $this->conn->Execute($sql);

        //MUNICPIOS
        $sql = "DELETE FROM mun_proy WHERE ".$this->columna_id." = ".$id;
        $this->conn->Execute($sql);

        $sql = "DELETE FROM albergue_proy WHERE ".$this->columna_id." = ".$id;
        $this->conn->Execute($sql);

            /*
            //BORRA LOS MUNICIPOS ASOCIADOS A LA REGION Y QUE NO SON DE COBERTURA INICIAL
            //CONSULTA LAS REGIONES DONDE TIENE COBERTURA
            $sql = "SELECT ID_REG FROM reg_proy WHERE ID_PROY = ".$id;
            $rs = $this->conn->OpenRecordset($sql);
            while ($row_rs = $this->conn->FetchRow($rs)){
            //CONSULTA LOS MUNICIPIOS DE LA REGION
            $sql_m = "SELECT ID_MUN FROM mun_reg WHERE ID_REG = ".$row_rs[0];
            $rs_m = $this->conn->OpenRecordset($sql_m);
            while ($row_rs_m = $this->conn->FetchRow($rs_m)){
            $sql_d = "DELETE FROM mun_proy WHERE ".$this->columna_id." = ".$id." AND ID_MUN = '".$row_rs_m[0]."' AND COBERTURA = 0";
            $this->conn->Execute($sql_d);
            //echo $sql_d;
            }
            }

            //REGIONES
            $sql = "DELETE FROM reg_proy WHERE ".$this->columna_id." = ".$id;
            $this->conn->Execute($sql);

            //POBLADOS
            $sql = "DELETE FROM pob_proy WHERE ".$this->columna_id." = ".$id;
            $this->conn->Execute($sql);
            //echo $sql;
            }
            if ($opcion == 4 || $opcion == 0){

            //RESGUARDOS
            $sql = "DELETE FROM resg_proy WHERE ".$this->columna_id." = ".$id;
            $this->conn->Execute($sql);

            //PARQUES
            $sql = "DELETE FROM par_nat_proy WHERE ".$this->columna_id." = ".$id;
            $this->conn->Execute($sql);

            //DIV. AFRO
            $sql = "DELETE FROM div_afro_proy WHERE ".$this->columna_id." = ".$id;
            $this->conn->Execute($sql);
            }

             */
    }

/**
     * Borra masivamente proyectos
     * @access public
     */
    function borrarMasivo() {

        // Opciones de busqueda
        $sql_s = "SELECT DISTINCT(ID_ORG), NOM_ORG
                  FROM proyecto
                  JOIN vinculorgpro USING(id_proy)
                  JOIN organizacion USING(id_org)
                  JOIN tipo_org USING(id_tipo)
                  JOIN proyecto_tema USING(id_proy)
                  WHERE ID_TIPO_VINORGPRO = 1 AND SI_PROY = '4w'
                  ORDER BY NOM_ORG";

        $rs_s = $this->conn->OpenRecordset($sql_s);
        $r = 0;
        while ($row_rs_s = $this->conn->FetchRow($rs_s)){

            $encargados[$r]['id_org'] = $row_rs_s[0];
            $encargados[$r]['nom_org'] = $row_rs_s[1];

            $r++;
        }

        include('p4w/borrar_masivo.php');

    }

    /**
     * Borra masivamente consulta a�os de proyectos de un ejecutor
     * @access public
     * @param int $org_id ID de la organizacion ejecutora
     * @param string $ys A�os para borrar
     *
     * @return string $json
     */
    function borrarMasivo4w($org_id, $ys='') {

        if (empty($ys)) {
            $ys = $this->getYearsByOrg($org_id, 1);

            echo json_encode($ys);
        }
        else {

            $cond = "id_org=$org_id AND id_tipo_vinorgpro = 1 AND (YEAR(inicio_proy) IN ($ys) OR YEAR(fin_proy) IN ($ys)) AND si_proy='4w'";

            $sql = "DELETE v.* FROM proyecto_tema AS v LEFT JOIN proyecto USING(id_proy) JOIN vinculorgpro USING(id_proy)    WHERE $cond";
            $this->conn->Execute($sql);

            $sql = "DELETE v.* FROM depto_proy AS v LEFT JOIN proyecto USING(id_proy) JOIN vinculorgpro USING(id_proy)       WHERE $cond";
            $this->conn->Execute($sql);

            $sql = "DELETE v.* FROM mun_proy AS v LEFT JOIN proyecto USING(id_proy) JOIN vinculorgpro USING(id_proy)         WHERE $cond";
            $this->conn->Execute($sql);

            $sql = "DELETE v.* FROM p4w_beneficiario AS v LEFT JOIN proyecto USING(id_proy) JOIN vinculorgpro USING(id_proy) WHERE $cond";
            $this->conn->Execute($sql);

            $sql = "DELETE p.*, v.* FROM proyecto AS p LEFT JOIN vinculorgpro AS v USING(id_proy)                            WHERE $cond";
            $this->conn->Execute($sql);

            echo '1';
        }

    }

    /**
     * Consulta los a�os de proyectos dado una org y el tipo de relacion
     * @access public
     * @param int $org_id ID de la organizacion
     * @param int $tipo Id del tipo 1=Ejecutora, 2=Donante, 3=Implementador
     *
     * @return array $ys
     */
    function getYearsByOrg($org_id, $tipo) {

        $cond = "id_org = $org_id AND id_tipo_vinorgpro = $tipo AND si_proy = '4w'";

        $sql = "SELECT DISTINCT YEAR(inicio_proy) AS y
                  FROM proyecto
                  JOIN vinculorgpro USING(id_proy) WHERE $cond
                ORDER BY y";

        $rs = $this->conn->OpenRecordset($sql);

        $ys = array();
        while ($row = $this->conn->FetchRow($rs)) {
            $ys[] = $row[0];
        }

        return $ys;

    }

    /******************************************************************************
     * Reportes de conteo
     * @param array $params d=departamental, m=municipal
     * @access public
     *******************************************************************************/
    function reporteConteo($params) {

        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '600');

        require_once 'admin/lib/common/archivo.class.php';

        $archivo = New Archivo();
        $row = $params['row']; //d=departamental, m=municipal
        $col = $params['col']; //$col s=sector, o=org, d=donante
        $que = $params['que']; //$que p=proyectos, b=beneficiarios, pre=presupuesto
        $h = $cond = $condu = '';
        //$condaa = "si_proy='".$params['si']."'";
        $condaa = $this->_setConditionSi();

        //$clasifis = (in_array($params['si'], array('4w_ehp', '4w_otros'))) ? 2 : 1;
        $filtro_periodo = false;
        $titulo_reporte = 'Reporte por ';

        $desarrollo = ($params['si'] == 'des') ? true : false;

        if ($desarrollo) {
            $cluster_label = 'resultado';
            $ejecutor_label = 'agencias';
            $clasifis = 4;
        }
        else {
            $cluster_label = 'sector';
            $ejecutor_label = 'ejecutores';
            $clasifis = 2;
        }


        $geo = true;
        $cc = '';

        $_dao = array('d' => 'depto', 'm' => 'municipio', 's' => 'tema');
        //$cn = 'nom';
        switch($row) {
            case 'd':
                $dao = FactoryDAO::factory('depto');
                $dm = 'Departamento';
                $dmi = 1;
                $titulo_reporte .= ' departamentos';

            break;
            case 'm':
                $dao = FactoryDAO::factory('municipio');
                $depto_dao = FactoryDAO::factory('depto');
                $dm = 'Municipio';
                $dmi = 0;
                $titulo_reporte .= ' municipios';
            break;
            case 's':
                $dmi = 2;
                $dm = ucfirst($cluster_label);
                $geo = false;
                $titulo_reporte .= ' '.$cluster_label;
            break;
            case 'r_undaf':
                $dmi = 2;
                $dm = 'Resultado UNDAF';
                $geo = false;
                $titulo_reporte .= ' '.$cluster_label;
            break;
            case 'a_undaf':
                $dmi = 2;
                $dm = 'Area UNDAF';
                $geo = false;
                $titulo_reporte .= ' Area UNDAF';
            break;
            case 'o':
                $dmi = 3;
                $dm = 'Ejecutor,Tipo';
                $geo = false;
                $titulo_reporte .= ' ejecutor';
            break;
            case 'do':
                $dmi = 4;
                $dm = 'Donante,Tipo';
                $geo = false;
                $titulo_reporte .= ' donante';
            break;
        }

        if (!$geo && !empty($params['idu'])) {
            $condaa .= " AND id_depto = '".$params['idu']."'";
        }

        if (array_search('periodo', $_SESSION['4w_f']['c']) !== false) {
            $filtro_periodo = $_SESSION['4w_f']['id'][array_search('periodo',$_SESSION['4w_f']['c'])];
        }
        if (array_search('id_depto_filtro', $_SESSION['4w_f']['c']) !== false) {
            $id_depto_filtro = $_SESSION['4w_f']['id'][array_search('id_depto_filtro',$_SESSION['4w_f']['c'])];
        }
        if (array_search('id_mun_filtro', $_SESSION['4w_f']['c']) !== false) {
            $id_mun_filtro = $_SESSION['4w_f']['id'][array_search('id_mun_filtro',$_SESSION['4w_f']['c'])];
        }
        $daos = array();
        if ($geo) {
            $condu = '';
            if (!empty($id_depto_filtro)) {
                $condu = ($dmi == 0) ? "id_mun like '$id_depto_filtro%'" : "id_depto = '$id_depto_filtro'";
            }

            // Adiciona la primera fila como Nacional
            $daos[0] = 'Nacional';
            $deptos_nombre[0] = 'Nacional';

            foreach($dao->GetAllArray($condu,'') as $vo) {

                // Elimina nacional
                if ($vo->id != '00' && $vo->id != '00000') {
                    $daos[$vo->id] = $vo->nombre;

                    // Municipios
                    if ($dmi == 0) {
                        $deptos_nombre[$vo->id] = $depto_dao->getName($vo->id_depto);
                    }
                }
            }
        }
        else if ($row == 's' || $row == 'a_undaf') {
            foreach(FactoryDAO::factory('clasificacion')->GetAllArray('id_clasificacion IN ('.$clasifis.')') as $clas) {
                foreach (FactoryDAO::factory('tema')->GetAllArray('id_papa = 0 AND id_clasificacion='.$clas->id, '', 'nom_tema') as $vo) {
                    $daos[$vo->id] = $vo->nombre;
                }
            }
        }
        else if ($row == 'r_undaf') {
            foreach(FactoryDAO::factory('clasificacion')->GetAllArray('id_clasificacion IN ('.$clasifis.')') as $clas) {
                foreach (FactoryDAO::factory('tema')->GetAllArray('id_papa > 0 AND id_clasificacion='.$clas->id, '', 'nom_tema') as $vo) {
                    $daos[$vo->id] = $vo->nombre;
                }
            }
        }
        else {
            foreach ($this->getOrgEjecutoraTotal(array('nom_org', 'nomb_tipo_es')) as $_ar){
                $daos[$_ar['id_org']] = $_ar['nom_org'].'|'.$_ar['nomb_tipo_es'];
            }
        }

        switch($col) {
            case 's':
                $pdao = FactoryDAO::factory('tema');
                $c = 'cluster';
                //$cn = 'nombre';
                //$cond = 'id_papa = 0';
                $cond = 'id_papa NOT IN (133,168,169)';

                $titulo_reporte .= ' y cluster';
                /*
                $nt = '';
                $h .= ',,';

                if (in_array($row, array('o', 'do'))) {
                    $h .= ',';
                }
                */

                $cls = array();
                foreach(FactoryDAO::factory('clasificacion')->GetAllArray('id_clasificacion IN ('.$clasifis.')') as $clas) {
                    foreach ($pdao->GetAllArray($cond.' AND id_clasificacion='.$clas->id) as $t) {

                        $cls[] = array('id' => $t->id, 'n' => $t->nombre);
                        /*
                        if ($nt != $clas->nombre) {
                            $h .= $clas->nombre;
                        }
                        $h .= ',';
                        */

                        $nt = $clas->nombre;
                    }
                }

            break;
            case 'r_undaf':
                $pdao = FactoryDAO::factory('tema');
                $c = 'cluster';

                $titulo_reporte .= ' y resultado UNDAF';

                $cls = array();
                foreach(FactoryDAO::factory('clasificacion')->GetAllArray('id_clasificacion IN ('.$clasifis.')') as $clas) {
                    foreach ($pdao->GetAllArray('id_papa > 0 AND id_clasificacion='.$clas->id) as $t) {

                        $cls[] = array('id' => $t->id, 'n' => $t->nombre);

                        $nt = $clas->nombre;
                    }
                }
            break;
            case 'a_undaf':
                $pdao = FactoryDAO::factory('tema');
                $c = 'cluster';
                $cc = 'a_undaf';

                $titulo_reporte .= ' y area UNDAF';

                $cls = array();
                $resultados = array();
                foreach(FactoryDAO::factory('clasificacion')->GetAllArray('id_clasificacion IN ('.$clasifis.')') as $clas) {
                    foreach ($pdao->GetAllArray('id_papa = 0 AND id_clasificacion='.$clas->id) as $t) {

                        $cls[] = array('id' => $t->id, 'n' => $t->nombre);

                        foreach ($pdao->GetAllArray('id_papa = '.$t->id) as $th) {
                            $resultados[$t->id][] = $th->id;
                        }

                        $nt = $clas->nombre;
                    }
                }
            break;
            case 'o':
                $c = 'ejecutora';
                foreach ($this->getOrgEjecutoraTotal(array('nom_org')) as $_ar){
                    $cls[] = array('id' => $_ar['id_org'], 'n' => $_ar['nom_org']);
                }

                $titulo_reporte .= ' y ejecutor';
            break;

            case 'd':
                $c = 'donante';
                foreach ($this->getOrgEjecutoraTotal(array('nom_org'), 2) as $_ar){
                    $cls[] = array('id' => $_ar['id_org'], 'n' => $_ar['nom_org']);
                }

                $titulo_reporte .= ' y donante';
            break;
        }

        $h .= "\r\n";

        if ($geo) {
            // Municipio agrega columna de departamento
            if ($dmi == 0) {
                $h .= 'Departamento,';
            }
            $h .= 'Divipola';
        }

        $h .= ",$dm";

        foreach($cls as $cl) {
            $h .= ',"'.$cl['n'].'"';
        }
        $h .= "\r\n";

        $id_depto_reporte = 0;
        foreach($daos as $id => $nom) {

            if ($geo) {

                // Municipio agrega columna de departamento
                if ($dmi == 0) {
                    $h .= $deptos_nombre[$id].',';
                    $id_depto_reporte = substr($id, 0, 2);;

                }
                else {
                    $id_depto_reporte = $id;
                }

                $h .= $id;

            }

            foreach(explode('|', $nom) as $_n) {
                $h .= ',"'.$_n.'"';
            }

            foreach($cls as $cl) {
                $cl_id = $cl['id'];

                if ($cc == 'a_undaf') {
                    $idsp = array();
                    foreach ($resultados[$cl_id] as $clh_id) {
                        $ids_temp = $this->getIdProyectosReporte($c, $clh_id, $dmi, $id, $condaa);

                        foreach ($ids_temp as $id_temp) {
                            if (!in_array($id_temp, $idsp)) {
                                $idsp[] = $id_temp;
                            }
                        }
                    }
                } else {
                    $idsp = $this->getIdProyectosReporte($c, $cl_id, $dmi, $id, $condaa);
                }

                $n = '';
                switch($que) {
                    case 'p':
                        $n = count($idsp);

                        $titulo_reporte_que = 'No. de proyectos';
                    break;
                    case 'b':
                        $n = 0;
                        foreach($idsp as $idp) {
                            $_b = $this->getCantBenef($idp);
                            if (!empty($_b['d']['total'])) {
                                $benef = $this->getPresupuestoBeneficiariosRealMeses($idp,$_b['d']['total'],
                                            $this->GetFieldValue($idp, 'inicio_proy'),
                                            $this->GetFieldValue($idp, 'fin_proy'),
                                            $this->GetFieldValue($idp, 'duracion_proy'),
                                            $id_depto_reporte
                                            );

                                // Si no es humanitario los beneficiarios se dividen por resultado
                                if ($desarrollo && ($c == 's' || $c == 'a_undaf')) {
                                    $clasif = 4;
                                    $benef_tema = $this->getBenefTema($idp,$benef,$clasif);

                                    // No para opcion TODOS
                                    if ($cc != 'a_undaf') {
                                        $benef = $benef_tema[$cl_id];
                                    }
                                    else {
                                        $benef = 0;
                                        foreach ($resultados[$cl_id] as $clh_id) {
                                            if (isset($benef_tema[$clh_id])) {
                                                $benef += $benef_tema[$clh_id];
                                            }
                                        }
                                    }
                                }

                                $n += ceil($benef);
                            }
                        }

                        $titulo_reporte_que = 'No. de beneficiarios';

                    break;
                    case 'pre':
                        $n = 0;
                        foreach($idsp as $idp) {
                            $pres = $this->GetFieldValue($idp, 'costo_proy');
                            if (!empty($pres)) {
                                // Columna Donante
                                if ($c == 'donante') {
                                    $pres = $this->getAporteDonante($idp, $cl_id);
                                }

                                //echo "pres = $pres <br />";
                                // Fila Sector
                                if ($cc == 'a_undaf') {
                                    $cluster_id = ($dmi == 2) ? $id : $cl_id;

                                    $pres_tema = $this->getPresTema($idp,$pres);

                                    $pres_area = 0;
                                    foreach ($resultados[$cluster_id] as $id_r) {
                                        $pres_area += (empty($pres_tema[$id_r])) ? 0 : $pres_tema[$id_r];
                                    }

                                    $pres = $pres_area;
                                }
                                else if ($dmi == 2 || $c == 'cluster') {
                                    $cluster_id = ($dmi == 2) ? $id : $cl_id;

                                    $pres_tema = $this->getPresTema($idp,$pres);
                                    $pres = (empty($pres_tema[$cluster_id])) ? 0 : $pres_tema[$cluster_id];
                                }

                                //echo "pres = $pres <br />";
                                $n += ceil($this->getPresupuestoBeneficiariosRealMeses($idp,$pres,
                                            $this->GetFieldValue($idp, 'inicio_proy'),
                                            $this->GetFieldValue($idp, 'fin_proy'),
                                            $this->GetFieldValue($idp, 'duracion_proy'),
                                            $id_depto_reporte
                                            ));
                            }
                        }

                        $titulo_reporte_que = 'Presupuesto';

                    break;
                }

                $h .= ','.$n;
            }
            $h .= "\r\n";
        }

        // Filtros para el titulo del reporte
        $titulo_reporte .= $this->tituloReporte4W();

        $h = $titulo_reporte.'. '.$titulo_reporte_que."\r\n".$h;
        //echo $h;

        $t_dir = '../tmp/';
        $f = $archivo->Abrir($t_dir.'/4w_reporte_conteo.csv', 'w+');
        $archivo->Escribir($f,utf8_encode($h));
        $archivo->Cerrar($f);

    }

    /******************************************************************************
     * Reportes x mes de presupuesto y beneficiarios
     * @access public
     *******************************************************************************/
    function reporteXMesPresBenef() {

        $pres_total = 0;
        $benef_total = 0;

        $condaa = $this->_setConditionSi();

        $yyyy = $_SESSION['4w_f']['id'][array_search('periodo',$_SESSION['4w_f']['c'])];

        $yyyy = explode(',',$yyyy);
        $num_yyyy = count($yyyy);

        foreach ($yyyy as $y) {
            for ($mes=1;$mes<13;$mes++) {
                $pres_meses[$y][$mes] = 0;
                $benef_meses[$y][$mes] = 0;
            }
        }

        $sql_cond = $this->getSqlIDProys();

        $sql = $sql_cond['sql'].' WHERE '.$sql_cond['cond'].' GROUP BY p.id_proy';
        //echo $sql;
        $rs = $this->conn->OpenRecordset($sql);
        while ($row = $this->conn->FetchObject($rs)) {
            $idp = $row->id;
            $pres = $row->costo_proy;
            $inicio_proy = $row->inicio_proy;
            $fin_proy = $row->fin_proy;
            $duracion_proy = $row->duracion_proy;

            list($yyyy_ini,$mes_ini,$dia_ini) = explode('-', $inicio_proy);
            list($yyyy_fin,$mes_fin,$dia_fin) = explode('-', $fin_proy);

            // Se calculan el # de meses que tiene el proyecto en los a�os de filtro
            $meses_proy_yyyy = 0;
            foreach ($yyyy as $y) {
                for ($mes=1;$mes<13;$mes++) {
                    if (($y >= $yyyy_ini && $mes >= $mes_ini) ||
                        ($y <= $yyyy_fin && $mes <= $mes_fin)) {

                        $meses_proy_yyyy += 1;
                    }
                }
            }


            // Presupuesto
            if (!empty($pres)) {

                $pres_real = $this->getPresupuestoBeneficiariosRealMeses($idp,$pres,
                            $inicio_proy,
                            $fin_proy,
                            $duracion_proy
                        );

                // Filtro cluster
                if (isset($_SESSION['4w_f']['c']) && array_search('cluster', $_SESSION['4w_f']['c']) !== false) {
                    $cluster_ids = $_SESSION['4w_f']['id'][array_search('cluster',$_SESSION['4w_f']['c'])];

                    $cluster_ids = explode(',',$cluster_ids);

                    $pres_tema = $this->getPresTema($idp,$pres_real);

                    $pres_cl = 0;
                    foreach($cluster_ids as $cluster_id) {
                        $pres_cl += (empty($pres_tema[$cluster_id])) ? 0 : $pres_tema[$cluster_id];
                    }

                    $pres_real = $pres_cl;
                }

                //echo "Proyecto=".$row->id.", presu=".$row->costo_proy.", calculado=".$pres_real."<br />";

                if (!empty($pres_real)) {

                    $pres_total += $pres_real;

                    // El presupuesto real de los a�os de filtro se divide en los meses
                    // que el proyecto esta en los a�os de filtro
                    $pres_real_mes = $pres_real / $meses_proy_yyyy;

                    // Para cada mes de los a�os de filtro se suma el presupuesto real
                    // de ese mes
                    foreach ($yyyy as $y) {
                        for ($mes=1;$mes<13;$mes++) {
                            if (($y >= $yyyy_ini && $mes >= $mes_ini) ||
                                ($y <= $yyyy_fin && $mes <= $mes_fin)) {

                                $pres_meses[$y][$mes] += $pres_real_mes;
                            }
                        }
                    }
                }
            }

            // Beneficiarios
            $_b = $this->getCantBenef($idp);
            if (!empty($_b['d']['total'])) {
                $benef_real = $this->getPresupuestoBeneficiariosRealMeses($idp,$_b['d']['total'],
                                    $inicio_proy,
                                    $fin_proy,
                                    $duracion_proy
                        );

                $benef_total += $benef_real;

                $benef_real_mes = $benef_real / $meses_proy_yyyy;

                foreach ($yyyy as $y) {
                    for ($mes=1;$mes<13;$mes++) {
                        if (($y >= $yyyy_ini && $mes >= $mes_ini) ||
                            ($y <= $yyyy_fin && $mes <= $mes_fin)) {
                            $benef_meses[$y][$mes] += $benef_real_mes;
                        }
                    }
                }
            }
        }

        /*print_r($pres_meses);

        echo " <br /> <br /> Sumando pres...".array_sum($pres_meses[2015]);
        echo " <br /> <br /> Sumando benef...".array_sum($benef_meses[2015]);

        echo "<br />P=$pres_total, B=$benef_total";
         */

        // Filtros para el titulo del reporte
        $csv = "Reporte de presupuesto y beneficiarios por meses." .$this->tituloReporte4W()."\n";
        $csv .= "A�o,Mes,Presupuesto,Beneficiarios\n";

        foreach ($yyyy as $y) {
            for ($mes=1;$mes<13;$mes++) {
                $csv .= "$y,$mes,".ceil($pres_meses[$y][$mes]).",".ceil($benef_meses[$y][$mes])."\n";
            }
        }

        echo $csv;

    }

    /******************************************************************************
     * Construye el titulo del reporte a partir de los filtros en session
     * @access private
     *******************************************************************************/
    private function tituloReporte4W() {
        $titulo_reporte = '';

        // Filtro periodo
        if (isset($_SESSION['4w_f']['c']) && array_search('periodo', $_SESSION['4w_f']['c']) !== false) {
            $yyyy = $_SESSION['4w_f']['id'][array_search('periodo',$_SESSION['4w_f']['c'])];

            $titulo_reporte .= '. Para '.str_replace(',','-',$yyyy);
        }

        // Filtro cluster
        if (isset($_SESSION['4w_f']['c']) && array_search('cluster', $_SESSION['4w_f']['c']) !== false) {
            $ids_cluster = $_SESSION['4w_f']['id'][array_search('cluster',$_SESSION['4w_f']['c'])];

            $_ids = explode(',',$ids_cluster);

            $tema_dao = FactoryDAO::factory('tema');

            $tema_nombre = array();
            foreach($_ids as $_id) {
                $tema_nombre[] = $tema_dao->getName($_id);
            }


            $titulo_reporte .= '. Cluster: '. implode('-',$tema_nombre);
        }

        // Filtro departamento
        if (isset($_SESSION['4w_f']['c']) && array_search('id_depto_filtro', $_SESSION['4w_f']['c']) !== false) {
            $ids_depto_filtro = $_SESSION['4w_f']['id'][array_search('id_depto_filtro',$_SESSION['4w_f']['c'])];

            $_ids = explode(',',$ids_depto_filtro);

            $depto_dao = FactoryDAO::factory('depto');

            $depto_nombre = array();
            foreach($_ids as $_id) {
                $depto_nombre[] = $depto_dao->getName($_id);
            }

            $titulo_reporte .= '. En '. implode('-',$depto_nombre);
        }

        return $titulo_reporte;
    }

    /******************************************************************************
     * Consulta los ID de los proyectos segun filtros
     * @param string $case Tema, Poblacion
     * @param int $id Id de Tema, Poblacion
     * @param boolean $depto 1=Departamento , 0=Municipio, 2=Nacional
     * @param string $ubicacion ID de la ubiaccion
     * @param string $cond Condicion extra a aplicar al SQL
     * @return array $project_ids
     * @access public
     *******************************************************************************/
    function getIdProyectosReporte($case,$id,$depto,$ubicacion,$cond=''){

        $tabla = 'proyecto';
        $join_vi = false;

        switch ($case) {
            case 'cluster':
                $tabla = "proyecto_tema";
                $col_id_filtro = "id_tema";
            break;
            case 'undaf':
                $tabla = "proyecto_tema";
                $col_id_filtro = "id_tema";
            break;

            case 'poblacion':
                $tabla = "proyecto_beneficiario";
                $col_id_filtro = "id_pobla";
            break;

            case 'ejecutora':
                $tabla = "vinculorgpro";
                $col_id_filtro = "id_org";
                $join_vi = true;
            break;
            case 'donante':
                $tabla = "vinculorgpro";
                $col_id_filtro = "id_org";
                $join_vi = true;
            break;

            case 'cobertura':
                $tabla = "proyecto";

                $id_filtro = $id;

                $col_id_filtro = "1";
                $id = 1;

                //Caso cobertura nacional
                if ($depto == 2){
                    $id = 1;
                    $col_id_filtro = 'cobertura_nal_proy';
                }
            break;

            case 'estado':
                $tabla = "proyecto";
                $col_id_filtro = "id_estp";
            break;
            case 'pres':
                $col_id_filtro = 'costo_proy';
                $rg = explode('-', $id);
                $cond .= ' costo_proy BETWEEN '.trim($rg[0]).' AND '.trim($rg[1]);
            break;
        }

        $sql = "SELECT DISTINCT(p.id_proy) FROM ";


        if ($tabla != 'proyecto')	$sql .= " proyecto JOIN ";

        $sql .= " $tabla p ";

        if ($tabla != 'proyecto')	$sql .= " USING ($this->columna_id) ";

        //}

        if ($case != 'ejecutora' && $case != 'donante') {
            $sql .= " LEFT JOIN vinculorgpro v USING ($this->columna_id) ";
        }

        if ($case != 'cluster') {
            $sql .= " LEFT JOIN proyecto_tema USING ($this->columna_id)";
        }

        //Agrega el filtro por agencia en cobertura
        /*
        if ($case == 'cobertura' && $id_filtro > 0){
            $sql .= " LEFT JOIN vinculorgpro v USING ($this->columna_id) ";
            $join_vi = true;
        }
         */

        //Ubicacion geografica
        if ($depto == 1 && $ubicacion != 0){
            //$sql .= " LEFT JOIN depto_proy USING($this->columna_id) WHERE id_depto = $ubicacion OR cobertura_nal_proy = 1";
            $sql .= " LEFT JOIN depto_proy USING($this->columna_id)";
            $_c = "id_depto = $ubicacion";
        }
        else if ($depto == 0  && $ubicacion != 0){
            //$sql .= " LEFT JOIN mun_proy USING ($this->columna_id) WHERE id_mun = $ubicacion OR cobertura_nal_proy = 1";
            $sql .= " LEFT JOIN mun_proy USING ($this->columna_id)";
            $_c = "id_mun = $ubicacion";
        }
        // Toco usar 2 para cruzar con sector
        else if ($depto == 2){
            //$sql .= " LEFT JOIN proyecto_tema USING ($this->columna_id)";
            $_c = "id_tema = $ubicacion";
            $join_vi = true;
        }
        // Toco usar 3 para cruzar con ejecutor
        //else if ($depto == 3 && !$join_vi){
        else if ($depto == 3 && !$join_vi){
            //$sql .= " LEFT JOIN vinculorgpro v UeSING ($this->columna_id)";
            $_c = "id_org = $ubicacion AND id_tipo_vinorgpro = 1";
            $join_vi = true;
        }
        // Toco usar 4 para cruzar con donante
        else if ($depto == 4 && !$join_vi){
            //$sql .= " LEFT JOIN vinculorgpro v USING ($this->columna_id)";
            $_c = "id_org = $ubicacion AND id_tipo_vinorgpro = 2";
            $join_vi = true;
        }

        else{
            $_c = "1=1 ";
        }

        // extra ubicacion
        if (isset($_SESSION['4w_f']['c']) && $depto > 1 && array_search('id_depto_filtro', $_SESSION['4w_f']['c']) !== false) {
            $sql .= " LEFT JOIN depto_proy USING($this->columna_id)";
        }

        /*
        if (strpos($cond, 'id_depto')) {
            $sql .= " LEFT JOIN depto_proy USING($this->columna_id)";
        }
       e
        if (strpos($cond, 'id_mun')) {
            $sql .= " LEFT JOIN mun_proy USING ($this->columna_id)";
        }


        if (!$join_vi) {
            $sql .= " LEFT JOIN vinculorgpro v USING ($this->columna_id)";
        }
        */

        // Condicion
        $sql .= ' WHERE '.$_c;

        if (!empty($id) && $case != 'pres' && $case != 'periodo') {
            $sql .= " AND $col_id_filtro IN ($id)";
        }

        //Agraga el filtro por agencia en cobertura
        if ($case == 'donante'){
            $sql .= " AND id_tipo_vinorgpro = 2";
        }
        else {
            $sql .= " AND id_tipo_vinorgpro = 1";
        }
        //Agraga el filtro por agencia en cobertura
        if ($case == 'cobertura' && $id_filtro > 0){
            $sql .= " AND id_org = $id_filtro AND id_tipo_vinorgpro = 1";
        }

        $sql .= ' AND '.$this->_setConditionSi(array('no_o' => true));

        //if (!empty($_SESSION['4w_f']['periodo_que']) &&
        //    strpos('year', strtolower($cond) === false)) {
        //

        // Caso Nacional o caso Departamental/Municipal
        $cobertura_nal_proy = ($ubicacion == 0) ? 1 : 0;
        $sql .= ' AND cobertura_nal_proy = '.$cobertura_nal_proy;

        if (isset($_SESSION['4w_f']['c']) && array_search('periodo', $_SESSION['4w_f']['c']) !== false) {

            $pq = $_SESSION['4w_f']['periodo_que'];
            $yyyy = $_SESSION['4w_f']['id'][array_search('periodo',$_SESSION['4w_f']['c'])];

            $y = explode(',',$yyyy);
            $ny = count($y);

            switch($pq) {
                case 'i':
                    if ($ny == 1) {
                        $cy = " YEAR(inicio_proy) = $yyyy";
                    }
                    else {
                        $cy = array();
                        foreach($y as $_y) {
                            $cy[] = "YEAR(inicio_proy) = $yyyy";
                        }
                        $cy = implode(' OR ', $cy);
                    }
                break;
                case 'f':
                    if ($ny == 1) {
                        $cy = " YEAR(fin_proy) = $yyyy";
                    }
                    else {
                        $cyta = array();
                        foreach($y as $_y) {
                            $cyta[] = "YEAR(fin_proy) = $yyyy";
                        }
                        $cy = implode(' OR ', $cyta);
                    }
                break;
                case 'v':
                    if ($ny == 1) {
                        $cy = " YEAR(fin_proy) >= ".$yyyy;
                    }
                    else {
                        $cy = " YEAR(fin_proy) >= ".min($y);
                    }
                break;
            }

            if (empty($cond)) {
                $cond = $cy;
            }
            else {
                $cond .= ' AND'.$cy;
            }
        }

        // Reporte ejecutor x donante
        if ($case == 'donante' && $depto == 3) {
            $sql .= " AND p.id_proy IN (
                SELECT v.id_proy FROM vinculorgpro v
                JOIN proyecto USING(id_proy)
                WHERE id_org = $ubicacion AND id_tipo_vinorgpro = 1
                AND ".$this->_setConditionSi(array('no_o' => true))."
                AND $cond
            ) ";
        }

        if (isset($_SESSION['4w_f']['c'])) {
            // Aplica filtro de ubicacion en los reportes que no son por
            // departamento o municipio
            if ($depto > 1) {
                if (array_search('id_depto_filtro', $_SESSION['4w_f']['c']) !== false) {
                    $id_depto_filtro = $_SESSION['4w_f']['id'][array_search('id_depto_filtro',$_SESSION['4w_f']['c'])];

                    $cond .= " AND id_depto IN (".$id_depto_filtro.")";
                }

            }

            // Aplica filtro de ejecutor en los reportes que no son por
            // ejectuor
            if ($depto != 3) {
                if (array_search('ejecutora', $_SESSION['4w_f']['c']) !== false) {
                    $id_org = $_SESSION['4w_f']['id'][array_search('ejecutora',$_SESSION['4w_f']['c'])];

                    $cond .= " AND id_org IN (".$id_org.")";
                }

            }

            // Aplica filtro de cluster en los reportes que no son por
            // cluster
            if ($case != 'cluster' && $depto != 2) {
                if (array_search('cluster', $_SESSION['4w_f']['c']) !== false) {
                    $id_tema = $_SESSION['4w_f']['id'][array_search('cluster',$_SESSION['4w_f']['c'])];

                    $cond .= " AND id_tema IN (".$id_tema.")";
                }

            }

            // Aplica filtro de SRP
            $srp = array_search('srp', $_SESSION['4w_f']['c']);
            if ($srp !== false) {
                $cond .= " AND srp_proy =1 ";
            }
        }

        if ($cond != '')	$sql .= " AND $cond";

        if ($case == 'ejecutora' && in_array($id, array(303,329,402))) {
        //    echo "$sql <br />";
        }

        //echo "\n$sql";
        //die;

        $rs = $this->conn->OpenRecordset($sql);

        $arr = array();
        while ($row = $this->conn->FetchRow($rs)){
            $arr[] = $row[0];
        }

        return $arr;
    }

    /******************************************************************************
     * Cuenta el n�mero de Proyectos de un Tema, Poblacion, etc
     * @param string $case Tema, Poblacion
     * @param int $id Id de Tema, Poblacion
     * @param boolean $depto 1=Departamento , 0=Municipio, 2=Nacional
     * @param string $ubicacion ID de la ubiaccion
     * @param string $si_proy Sistema de info, sidih, undaf, etc
     * @access public
     *******************************************************************************/
    function numProyectos($case,$id,$depto,$ubicacion,$si_proy){

        return count($this->getIdProyectosReporte($case,$id,$depto,$ubicacion,''));

    }

    /**
     * Retorna proyectos para openlayers
     * @access public
     * @param array $params
     */
    function getProysMapa($params) {
        // LIBRERIAS
        require_once 'admin/lib/common/date.class.php';
        require_once 'admin/lib/common/archivo.class.php';
        require_once 'admin/lib/dao/sissh.class.php';
        require_once 'admin/lib/dao/org.class.php';
        require_once 'admin/lib/dao/tema.class.php';

        $archivo = New Archivo();
        $sissh = New SisshDAO();
        $org_dao = New OrganizacionDAO();
        $tema_dao = New TemaDAO();

        $_SESSION['grupo'] = empty($params['grupo']) ? '' : $params['grupo'];

        $fecha = New Date();
        $f = $params['f']; // Formato salida
        $ps = (empty($params['limit'])) ? 15 : false;
        $si_proy = $params['si'];
        $np = 0;
        $nb = 0;
        $no = 0;
        $ni = 0;
        $npres = 0;
        $id_orgs_e = array();
        $html = '';
        $fnal = true;
        $id_depto = null;
        $id_mun = null;
        $condc = '';
        $ed = 'Ejecutor: ';
        $tv = 1;
        $sectores_top = array();
        $donantes_top = array();
        $deptos_top = array();
        $ejecutoras_top = array();
        $cache = false;

        $fdepto = $fmun = false;
        $filtro_periodo = $filtro_cluster = $filtro_ejecutora = false;
        $filtro_donante = $filtro_implementador = false;
        $aporte_donante_filtro = 0;

        // numero de tops a mostrar
        $top_num_show = 4;

        $map_json_a = array();
        $map_json_a['type'] = 'FeatureCollection';
        $map_json_a['features'] = array();

        //$cond = $this->_setConditionSi(array('si' => $si_proy));
        $desarrollo = ($params['si'] == 'des') ? true : false;

        if ($desarrollo) {
            $clasif = 4;
            $sector_label = 'Resultado';
            $sector_label_top = 'resultados';
            $ejecutor_label = 'agencias';
        }
        else {
            $clasif = 2;
            $sector_label = 'Cluster/Sector';
            $sector_label_top = 'sectores';
            $ejecutor_label = 'ejecutores';
        }

        $sql_cond = $this->getSqlIDProys($params);

        $sql = $sql_cond['sql'];
        $cond = $sql_cond['cond'];

        if ($f == 'mapa') {
            $cond .= ' AND cobertura_nal_proy = 0 ';
        }

        $file = $f;

        // Recorre todos los filtros en session
        if (!empty($_SESSION['4w_f']['c'])) {
            $cs = $_SESSION['4w_f']['c']; // Filtro
            $ids = $_SESSION['4w_f']['id']; // ID de Filtro

            $file .= '-'.implode('~',$cs).'-'.implode('~',$ids).'-'.implode('~',$params);
        }

        // Coloca el valor de limit en el nombre

        if(empty($params['limit'])) {
            $file .= '-15';
        }
        else {
            $file .= '-no-limit';
        }

        $tdir = $sissh->dir_cache_static.'4w/';

        $path_file = $tdir.$file;
        $path_file_csv = $tdir.$file.'.csv';
        $path_file_proyectos = $tdir.'proyectos_4w.csv';

        // Consulta static cache
        if ($cache && $sissh->existsFileCache($path_file)) {
            echo $sissh->getFileCache($path_file);

            // Renombra archivo csv para descarga
            if ($f == 'lista') {
                $archivo->copiar($path_file_csv, $path_file_proyectos);
            }

            return;
        }

        if (!empty($_SESSION['4w_f']['c'])) {

            foreach($cs as $_i => $_c) {

                $id = $ids[$_i];

                switch($_c) {
                    case 'cluster':
                        $filtros['id_tema'] = $id;
                        $filtro_cluster = true;
                    break;
                    case 'undaf':
                        $filtros['id_tema'] = $id;
                    break;
                    case 'ejecutora':
                        $filtros['id_org'] = $id;
                        $filtros['t_org'] = $tv;
                        $filtro_ejecutora = true;
                    break;
                    case 'implementadora':
                        $tv = 3;
                        $filtros['id_org'] = $id;
                        $filtros['t_org'] = $tv;
                        $filtro_implementador = true;
                    break;
                    case 'donante':
                        $tv = 2;
                        $filtros['id_org'] = $id;
                        $filtros['t_org'] = $tv;
                        $filtro_donante = true;
                        $id_donante_filtro = $id;
                    break;
                    case 'estado':
                    break;
                    case 'periodo':
                        $pq = $_SESSION['4w_f']['periodo_que'];

                        // Periodo puede ser yyyy1,yyyy2
                        $y = explode(',',$id);
                        $ny = count($y);

                        $filtros['periodo'] = $id;
                        $filtros['periodo_que'] = $pq;

                        $filtro_periodo = $id;

                    break;
                    case 'pres':
                        $rg = explode('-', $id);
                    break;
                }
            }
        }

        if (!empty($_SESSION['4w_f']['c'])) {
            if(array_search('id_mun_filtro', $_SESSION['4w_f']['c']) !== false) {

                $id_mun = $_SESSION['4w_f']['id'][array_search('id_mun_filtro',$_SESSION['4w_f']['c'])];

                // Varios departamentos
                if (strpos($id_mun, ',') !== false) {
                    $idds = explode(',', $id_mun);
                    foreach($idds as $idd) {
                        $cc[] = " id_mun LIKE '$idd%'";
                    }

                    $condc = '('.implode(' OR ', $cc).')';
                }
                else {
                    $condc = " id_mun LIKE '$id_mun%'";
                }
                $fmun = true;
                $fnal = false;

                $filtros['id_mun'] = $id_mun;
            }

            if (array_search('id_depto_filtro', $_SESSION['4w_f']['c']) !== false) {

                $id_depto = $_SESSION['4w_f']['id'][array_search('id_depto_filtro',$_SESSION['4w_f']['c'])];

                if (!$fmun) {
                    // Varios departamentos
                    if (strpos($id_depto, ',') !== false) {
                        $idds = explode(',', $id_depto);
                        foreach($idds as $idd) {
                            $cc[] = " id_mun LIKE '$idd%'";
                        }

                        $condc = '('.implode(' OR ', $cc).')';
                    }
                    else {
                        $condc = " id_mun LIKE '$id_depto%'";
                    }
                }

                $fdepto = true;
                $fnal = false;

                $filtros['id_depto'] = $id_depto;
            }
        }


        /*
        if (!empty($params['id_mun_filtro'])) {
            $id_mpio = $params['id_mun_filtro'];
            $fmun = true;
            $fnal = false;
        }
         */

        if (!empty($params['c']) && $params['c'] == 'proy' && !empty($params['id'])) {
            $cond .= " AND p.id_proy IN (".$params['id'].")";
        }

        if (!empty($params['srp']) && $params['srp'] == 1 ) {
            $cond .= " AND p.srp_proy = 1";
        }

        $sql .= ' WHERE '.$cond.' GROUP BY p.id_proy';

        $condpdf = $cond;

        //echo $sql;

        $ejecutora_filtro = $donante_filtro = array();
        $implementadora_filtro = $cluster_filtro = array();
        $estado_filtro = $departamento_filtro = $municipio_filtro = array();
        $periodo_filtro = array();
        $aporte_donantes = 0;
        $pres2gob = 0;

        if ($f == 'lista') {

            $order = (empty($params['order'])) ? 'DESC' : $params['order'];
            $ini = (empty($params['ini'])) ? 0 : $params['ini'];

            // Totales para resumen
            if ($ini == 0) {
                $rs = $this->conn->OpenRecordset($sql);
                while ($row = $this->conn->FetchObject($rs)) {

                    $id_proy = $row->id;
                    $id_org_e = $row->id_org;
                    $id_temas = explode(',', $row->id_tema);

                    // filtra temas por categoria
                    foreach ($id_temas as $i => $id_ttemp) {
                        if ($clasif != $tema_dao->GetFieldValue($id_ttemp,'id_clasificacion')) {
                            unset($id_temas[$i]);
                        }
                    }

                    $id_estp = $row->id_estp;
                    $inicio_proy = $row->inicio_proy;
                    $fin_proy = $row->fin_proy;
                    $duracion_proy = $row->duracion_proy;
                    $costo_proy = $row->costo_proy;
                    $cobertura_nal_proy = $row->cobertura_nal_proy;

                    list($yyyy_ini,$mes_ini,$dia_ini) = explode('-', $inicio_proy);
                    list($yyyy_fin,$mes_fin,$dia_fin) = explode('-', $fin_proy);

                    // Consulta ejecutores si el filtro es donante o implementadora
                    if ($filtro_donante || $filtro_implementador) {
                        $_sqle = "SELECT id_org FROM vinculorgpro WHERE id_proy = $id_proy AND id_tipo_vinorgpro = 1";
                        $_rse = $this->conn->OpenRecordset($_sqle);
                        $_rowe = $this->conn->FetchObject($_rse);

                        $id_org_e = $_rowe->id_org;

                    }

                    // Lista filtro donante
                    $pres2suma = 0;
                    $benef2suma = 0;
                    $donantes = $this->getOrgs($id_proy,2);
                    if (!empty($donantes)) {
                        foreach($donantes['id'] as $d => $id_donante) {

                            $donacion = $donantes['a'][$d];

                            $aporte = $this->getPresupuestoBeneficiariosRealMeses($id_proy,$donacion,$inicio_proy,$fin_proy,$duracion_proy);

                            if ($filtro_cluster) {
                                $pres_tema = $this->getPresTema($id_proy,$aporte);

                                $_t = $filtros['id_tema'];

                                if (strpos($_t,',') === false) {
                                    $aporte = $pres_tema[$_t];
                                }
                                else {
                                    $aporte = 0;
                                    foreach(explode(',',$_t) as $_tt) {
                                        if (isset($pres_tema[$_tt])) {
                                            $aporte += $pres_tema[$_tt];
                                        }
                                    }
                                }

                                //$aporte = $pres_tema[$_t];
                            }

                            if (!array_key_exists($id_donante, $donante_filtro)) {
                                $donante_filtro[$id_donante] = 1;
                            }
                            else {
                                $donante_filtro[$id_donante] += 1;
                            }

                            if (!$filtro_donante) {
                                if (!isset($donantes_top[$id_donante])) {
                                    $donantes_top[$id_donante] = $aporte;
                                }
                                else {
                                    $donantes_top[$id_donante] += $aporte;
                                }
                            }

                            $aporte_donantes += $aporte;

                            // Gobierno
                            $id_tipo_don = $org_dao->GetFieldValue($id_donante, 'id_tipo');
                            if (in_array($id_tipo_don, array(10,16,17))) {
                                $pres2gob += $aporte;
                                //echo "<br /> proy = $id_proy ----- donacion = $donacion ------- aporte = $aporte ----- tipo : $id_tipo_don ---- pres_gob = $pres2gob<br />";
                            }

                            if ($filtro_donante && $id_donante_filtro == $id_donante) {
                                $aporte_donante_filtro = $aporte;
                                $pres2suma = $aporte;
                            }
                        }

                    }

                    // Lista filtro implementadora
                    $implementadoras = $this->getOrgs($id_proy,3);
                    if (!empty($implementadoras)) {
                        foreach($implementadoras['id'] as $id_implementadora) {

                            if (!array_key_exists($id_implementadora, $implementadora_filtro)) {
                                $implementadora_filtro[$id_implementadora] = 1;
                                $ni++;
                            }
                            else {
                                $implementadora_filtro[$id_implementadora] += 1;
                            }
                        }
                    }

                    // Lista filtro ubicacion, actualiza la lista de filtro solo
                    // cuando no tenga filtro de depto, esto es para poder
                    // seleccionar varios
                    if (!$fdepto && !$fmun) {
                        $deptos = $this->getIdDeptosCobertura($id_proy);
                        foreach($deptos as $id_depto) {

                            if (!array_key_exists($id_depto, $departamento_filtro)) {
                                $departamento_filtro[$id_depto] = 1;
                            }
                            else {
                                $departamento_filtro[$id_depto] += 1;
                            }
                        }
                    }

                    // Consulta municipios si hay filtro de depto y no de mun, para poder
                    // seleccionar varios
                    if ($fdepto && !$fmun) {
                        $muns = $this->getMpiosCobertura($id_proy, "id_depto IN (".$filtros['id_depto'].")")['ids'];
                        foreach($muns as $id_mun) {

                            if (!array_key_exists($id_mun, $municipio_filtro)) {
                                $municipio_filtro[$id_mun] = 1;
                            }
                            else {
                                $municipio_filtro[$id_mun] += 1;
                            }
                        }
                    }

                    $benf_proy = $this->getCantBenef($row->id);
                    if (!empty($benf_proy['d']['total'])) {
                        if (!empty($filtro_periodo)) {
                            $benef2suma = $this->getPresupuestoBeneficiariosRealMeses($id_proy,$benf_proy['d']['total'],$inicio_proy,$fin_proy,$duracion_proy);

                            // Si no es humanitario los beneficiarios se dividen por resultado
                            if ($desarrollo && isset($filtros['id_tema'])) {
                                $_g = $filtros['id_tema'];

                                $clasif = 4;
                                $benef_tema = $this->getBenefTema($id_proy,$benef2suma,$clasif);

                                // No para opcion TODOS
                                if (strpos($_g,',') === false) {
                                    $benef2suma = $benef_tema[$_g];
                                }
                                else {
                                    $benef2suma = 0;
                                    foreach(explode(',',$_g) as $_tt) {
                                        if (isset($benef_tema[$_tt])) {
                                            $benef2suma += $benef_tema[$_tt];
                                        }
                                    }
                                }
                            }
                        }
                        else {
                            $benef2suma = $benf_proy['d']['total'];
                        }

                        $nb += $benef2suma;
                    }

                    if (!empty($row->costo_proy)) {
                        if (!empty($filtro_periodo)) {
                            //echo "presupuesto <br />";
                            $pres = $this->getPresupuestoBeneficiariosRealMeses($id_proy,$costo_proy,$inicio_proy,$fin_proy,$duracion_proy);
                            //echo "Proyecto=".$row->id.", presu=".$row->costo_proy.", calculado=".$pres."<br />";
                        }
                        else {
                            $pres = $row->costo_proy;
                        }

                        $pres2tema = $pres;
                        if ($filtro_donante) {
                            $pres2tema = $aporte_donante_filtro;
                        }

                        $pres_tema = $this->getPresTema($id_proy,$pres2tema,$clasif);

                        if (!$filtro_cluster) {
                            foreach($pres_tema as $id_tema => $prest) {
                                if (!array_key_exists($id_tema, $sectores_top)) {
                                    $sectores_top[$id_tema] = $prest;
                                }
                                else {
                                    $sectores_top[$id_tema] += $prest;
                                }
                            }
                        }
                        else if ($filtro_cluster && !$filtro_donante) {

                            $_t = $filtros['id_tema'];

                            if (strpos($_t,',') === false) {
                                if (isset($pres_tema[$_t])) {
                                    $pres2suma = $pres_tema[$_t];
                                }
                            }
                            else {
                                $pres2suma = 0;
                                foreach(explode(',',$_t) as $_tt) {
                                    if (isset($pres_tema[$_tt])) {
                                        $pres2suma += $pres_tema[$_tt];
                                    }
                                }
                            }
                        }

                        // Para depto hay que hacer el calculo como si fuera filtro
                        if ($pres2suma == 0) {
                            $pres2depto = $pres;
                        }
                        else {
                            $pres2depto = $pres2suma;
                        }

                        if ($cobertura_nal_proy == 0) {
                            $duracion_proy = 0; // Para que solo calcule deptos, porque pres2suma ya esta temporizado

                            $cobertura = $this->getMpiosCobertura($id_proy);
                            foreach($cobertura['deptos_id'] as $id_d) {
                                $pres_depto = $this->getPresupuestoBeneficiariosRealMeses($id_proy,$pres2depto,$inicio_proy,$fin_proy,$duracion_proy,$id_d);

                                if (!isset($deptos_top[$id_d])) {
                                    $deptos_top[$id_d] = 0;
                                }

                                $deptos_top[$id_d] += $pres_depto;
                            }
                        }
                        else {
                            if (!isset($deptos_top[0])) {
                                $deptos_top[0] = $pres2depto;
                            } else {
                                $deptos_top[0] += $pres2depto;
                            }
                        }

                        if ($pres2suma == 0) {
                            $pres2suma = $pres;
                        }

                        $npres += $pres2suma;
                    }

                    $np++;

                    // # de Orgs ejecutoras + lista para filtro
                    if (!in_array($id_org_e, $id_orgs_e)) {
                        $no++;
                        $id_orgs_e[] = $id_org_e;

                        $ejecutora_filtro[$id_org_e] = 1;

                    }
                    else {
                        $ejecutora_filtro[$id_org_e] += 1;
                    }

                    // Top ejecutoras
                    if (!$filtro_ejecutora) {
                         if (!isset($ejecutoras_top[$id_org_e])) {
                             $ejecutoras_top[$id_org_e] = $pres2suma;
                         }
                         else {
                             $ejecutoras_top[$id_org_e] += $pres2suma;
                         }
                     }

                    // Lista filtro cluster
                    foreach($id_temas as $id_tema) {
                        if (!array_key_exists($id_tema, $cluster_filtro)) {
                            $cluster_filtro[$id_tema] = 1;
                        }
                        else {
                            $cluster_filtro[$id_tema] += 1;
                        }
                    }

                    // Lista filtro estado
                    if (!array_key_exists($id_estp, $estado_filtro)) {
                        $estado_filtro[$id_estp] = 1;
                    }
                    else {
                        $estado_filtro[$id_estp] += 1;
                    }

                    // Lista filtro periodo con ini
                    if (!array_key_exists($yyyy_ini, $periodo_filtro)) {
                        $periodo_filtro[$yyyy_ini] = 1;
                    }
                    else {
                        $periodo_filtro[$yyyy_ini] += 1;
                    }

                    // Lista filtro periodo con fin
                    if (!array_key_exists($yyyy_fin, $periodo_filtro)) {
                        $periodo_filtro[$yyyy_fin] = 1;
                    }
                    else {
                        $periodo_filtro[$yyyy_fin] += 1;
                    }
                }

                // Coloca la fila de dato sin donante
                $pres_sin_donante = 0;
                if ($aporte_donantes < $npres) {
                    $donantes_top[0] = $npres - $aporte_donantes;
                    $pres_sin_donante = $donantes_top[0];
                }

                $sectores_top = $this->orderTopData($sectores_top,'tema',$npres);
                $donantes_top = $this->orderTopData($donantes_top,'org',$npres);
                $deptos_top = $this->orderTopData($deptos_top,'depto',$npres);
                $ejecutoras_top = $this->orderTopData($ejecutoras_top,'org',$npres);

                // Separa el total de presupuesto en humanitario | gobierno
                $npres_total = $npres;
                if ($pres2gob > 0) {
                    $npres -= $pres2gob;
                }

                // Resta sin dato donante
                if ($pres_sin_donante > 0) {
                    $npres -= $pres_sin_donante;
                }

                // Totales, son leidos en la funcion js/p4w/consulta.js:changeTotales()
                $html = '
                <input type="hidden" id="4w_np" value="'.number_format($np).'" />
                <input type="hidden" id="4w_no" value="'.number_format($no).'" />
                <input type="hidden" id="4w_ni" value="'.number_format($ni).'" />
                <input type="hidden" id="4w_nb" value="'.number_format($nb).'" />
                <input type="hidden" id="4w_npres" value="'.number_format($npres).'" />
                <input type="hidden" id="4w_npres_gob" value="'.number_format($pres2gob).'" />
                <input type="hidden" id="4w_npres_sin_donante" value="'.number_format($pres_sin_donante).'" />
                <input type="hidden" id="4w_npres_total" value="'.number_format($npres_total).'" />';

                // HTML para Tops
                $html_top = '';
                if (!$filtro_cluster) {
                    $search = array('Seguridad Alimentaria y Nutrición (SAN)','Educación en Emergencia (EeE)','Agua, saneamiento e higiene (WASH)');
                    $replace = array('SAN','EeE','WASH');


                    $num = count($sectores_top);
                    $do = ( $num > 0) ? true : false;

                    if ($do) {
                        $html_top = '<span><div class="total">Principales '.$sector_label_top.'</div><table>';
                    }

                    foreach($sectores_top as $_s => $st) {

                        $show = ($_s > $top_num_show) ? 'top_hide hide' : '';

                        $html_top .= '<tr class="tr_top_cluster '.$show.'">
                            <td class="n">'.($_s + 1).'</td>
                            <td class="l cl">
                                '.str_replace($search,$replace,$st[0]).'
                            </td>
                            <td class="per"><b>'.$st[2].' % </b>
                                <br />
                                <span class="nota">USD '.$st[1].'</span>
                            </td>
                        </tr>';
                    }

                    if ($num > $top_num_show) {
                        $html_top .= '<tr><td colspan="2"><a href="#" onclick="$j(\'tr.tr_top_cluster.top_hide\').toggleClass(\'hide\');return false" class="ver">Mostrar todos</a></td></tr>';
                    }

                    $html_top .= '</table></span>';

                }

                $html .= '<textarea id="resumen_top_cluster" class="hide">'.$html_top.'</textarea>';

                $html_top = '';
                if (!$filtro_donante) {

                    $num = count($donantes_top);
                    $do = ($num > 0) ? true : false;

                    if ($do) {
                        $html_top = '<span><div class="total">Principales fuentes de financiaci&oacute;n</div><table>';
                    }

                    foreach($donantes_top as $_s => $st) {

                        /*
                        $nom_sig = explode('|',$st[0]);
                        $nom = $nom_sig[0];
                        $sig = @$nom_sig[1];
                        */
                        $nom = $st[0];

                        $n = (empty($sig)) ? $nom : $sig;

                        $show = ($_s > $top_num_show) ? 'top_hide hide' : '';

                        $html_top .= '<tr class="tr_top_donante '.$show.'">
                            <td class="n">'.($_s + 1).'</td>
                            <td class="l cl">
                                <a href="#" title="'.$nom.'" onclick="return false;">'.$n.'</a>
                            </td>
                            <td class="per">
                                <span class="nota">USD</span>&nbsp;&nbsp;<b>'.$st[1].'</b>
                            </td>
                        </tr>';
                    }

                    if ($num > $top_num_show) {
                        $html_top .= '<tr><td colspan="2"><a href="#" onclick="$j(\'tr.tr_top_donante.top_hide\').toggleClass(\'hide\');return false;" class="ver">Mostrar todos</a></td></tr>';
                    }

                    $html_top .= '</table></span>';

                }

                $html .= '<textarea id="resumen_top_donantes" class="hide">'.$html_top.'</textarea>';

                $html_top = '';
                if (!$fdepto) {

                    $num = count($deptos_top);

                    $do = ($num > 0) ? true : false;

                    if ($do) {
                        $html_top = '<span id="resumen_top_deptos"><div class="total">Departamentos que reciben cooperaci&oacute;n</div><table>';
                    }

                    foreach($deptos_top as $_s => $st) {

                        $show = ($_s > $top_num_show) ? 'top_hide hide' : '';

                        $html_top .= '<tr class="tr_top_depto '.$show.'">
                            <td class="n">'.($_s + 1).'</td>
                            <td class="l cl">
                                '.$st[0].'
                            </td>
                            <td class="per"><b>'.$st[2].' %</b>
                                <br />
                                <span class="nota">USD '.$st[1].'</span>
                            </td>
                        </tr>';
                    }

                    if ($num > $top_num_show) {
                        $html_top .= '<tr><td colspan="2"><a href="#" onclick="$j(\'tr.tr_top_depto.top_hide\').toggleClass(\'hide\');return false;" class="ver">Mostrar todos</a></td></tr>';
                    }

                    $html_top .= '</table></span>';

                }

                $html .= '<textarea id="resumen_top_deptos" class="hide">'.$html_top.'</textarea>';

                $html_top = '';
                if (!$filtro_ejecutora) {

                    $num = count($ejecutoras_top);

                    $do = ($num > 0) ? true : false;

                    if ($do) {
                        $html_top = '<span id="resumen_top_ejecutoras"><div class="total">Principales '.$ejecutor_label.'</div><table>';
                    }

                    foreach($ejecutoras_top as $_s => $st) {

                        $show = ($_s > $top_num_show) ? 'top_hide hide' : '';

                        $html_top .= '<tr class="tr_top_depto '.$show.'">
                            <td class="n">'.($_s + 1).'</td>
                            <td class="l cl">
                                '.$st[0].'
                            </td>
                            <td class="per"><b>'.$st[2].' %</b>
                                <br />
                                <span class="nota">USD '.$st[1].'</span>
                            </td>
                        </tr>';
                    }

                    if ($num > $top_num_show) {
                        $html_top .= '<tr><td colspan="2"><a href="#" onclick="$j(\'tr.tr_top_depto.top_hide\').toggleClass(\'hide\');return false;" class="ver">Mostrar todos</a></td></tr>';
                    }

                    $html_top .= '</table></span>';

                }

                $html .= '<textarea id="resumen_top_ejecutoras" class="hide">'.$html_top.'</textarea>';

                // Filtros consulta actualizados
                $filtros['id_depto'] = $id_depto;
                //if (!empty($_SESSION['4w_f']['c']) || !empty($condc)) {
                    $ts = array(
                        'ejecutora' => 'Ejecutora',
                        'donante' => 'Donante',
                        'implementadora' => 'Implementadora',
                        'cluster' => 'Cluster',
                        //'ubicacion' => 'Ubicaci&oacute;n',
                        'departamento' => 'Departamento',
                        'municipio' => 'Municipio',
                        'estado' => 'Estado',
                        'periodo' => 'Periodo'
                        );

                    foreach($ts as $t => $ti) {
                        $fil = ${$t."_filtro"};
                        if ($t != 'periodo') {
                            arsort($fil);
                        }
                        else {
                            ksort($fil);
                        }
                        $html .= '<div id="fcu_'.$t.'" class="hide">'.$this->getFiltrosConsulta($t, $fil).'</div>';
                    }
               // }

            }

            $sql .= ' ORDER BY creac_proy '.$order;

            if ($ps !== false) {
                $sql .= " LIMIT $ini,$ps";
            }
        }

        //echo $sql;

        $csv = ",Proyecto,,,,,,,,Ejecutor,,,Implementadores,,,Donantes,,,,,Resultados,Sectores,Beneficiarios Directos,,,,,,,,,Beneficiarios Indirectos,,,,,,,,,,\r\n";
        $csv .= "ID 4w,Código,Nombre,Descripción,Inicio,Fin,Meses,Presupuesto,Estado,Sigla,Nombre,Tipo,Sigla,Nombre,Tipo,Sigla,Nombre,Tipo,Aporte,Código que usa el donante,Resultados,Sectores,";
        $csv .= "Total,";
        $csv .= "Total Hombres,Hombres 0-5 Años,Hombres 6-18 Años,Hombre 18+ Años,";
        $csv .= "Total Mujeres,Mujeres 0-5 Años,Mujeres 6-18 Años,Hombre 18+ Años,";
        $csv .= "Total,";
        $csv .= "Total Hombres,Hombres 0-5 Años,Hombres 6-18 Años,Hombre 18+ Años,";
        $csv .= "Total Mujeres,Mujeres 0-5 Años,Mujeres 6-18 Años,Hombre 18+ Años,";
        $csv .= 'Departamento,Municipios';
        $csv .= "\r\n";

        $rs = $this->conn->OpenRecordset($sql);
        while ($row = $this->conn->FetchObject($rs)) {

            $id = $row->id;
            $ficha = '';
            if ($f == 'mapa') {
                // Cob
                $_c = $this->GetMpiosCobertura($id, $condc);
                foreach($_c['ids'] as $l) {
                    if (!empty($_c['lon'][$l]) && !empty($_c['lat'][$l])) {
                        $map_json_a['features'][] = array('type' => 'Feature',
                            'properties' => array('desc' => $id),
                            'geometry' => array('type' => 'Point',
                            'coordinates' => array($_c['lon'][$l],$_c['lat'][$l])));
                    }
                }
            }
            else {

                // Detalle
                $ejec = $this->getOrgs($id, 1);
                $ejec_nom = $ejec['nom'][0];
                $ejec_sig = $ejec['sig'][0];
                $ejec_tipo = $ejec['tipo'][0];

                $html .= '<div class="fila row_proy">
                            <div class="t" id="'.$id.'"><a href="#" title="'.$id.'" onclick="return false">'.$row->nom_proy.'</a></div>
                            <div><i>C&oacute;digo:</i> '.$row->cod_proy.'</div>
                            <div><i>Presupuesto USD:</i> '.number_format($row->costo_proy).'</div>
                            <div><i>'.$ed.'</i><span class="s" id="'.$row->id_org.'">'.(empty($ejec_sig) ? $ejec_nom : $ejec_sig).'</span></div>';


                $csv .= $row->id.',"'.$row->cod_proy.'","'.str_replace(array("\r\n", "\r", "\n",'"'), '', $row->nom_proy).'","'.
                        str_replace(array("\r\n", "\r", "\n",'"'), '', $row->des_proy).'","'.
                        $row->inicio_proy.'","'.$row->fin_proy.'",'.$row->duracion_proy.','.$row->costo_proy.',"'.
                        $row->nom_estp.'","'.$ejec_sig.'","'.$ejec_nom.'","'.$ejec_tipo.'"';

                // Implementadores, Donantes
                $impls = $this->getOrgs($id, 3);
                $dons = $this->getOrgs($id, 2);
                //if ($c == '' || $c == 'todos') {
                if (empty($params['c']) || $params['c'] == 'todos') {
                    // Imple
                    if (count($impls) > 0) {
                        $html .= '<div><i>Implementadores:</i>';
                        foreach($impls['sig'] as $i => $isig) {
                            $html .= ' - <span class="s" id="'.$impls['id'][$i].'">'.(empty($isig)
                                         ? $impls['nom'][$i] : $isig).'</span>';
                        }
                        $html .= '</div>';
                    }

                    // Donan
                    if (count($dons) > 0) {
                        $html .= '<div><i>Donantes:</i>';
                        foreach($dons['sig'] as $i => $dsig) {
                            $html .= ' - <span class="s" id="'.$dons['id'][$i].'">'.(empty($dsig)
                                         ? $dons['nom'][$i] : $dsig).'</span>. '.
                                         (!empty($dons['a'][$i]) ? 'Aporte: USD '.number_format($dons['a'][$i]) : '' );
                        }
                        $html .= '</div>';
                    }

                }

                $csv .= ',"'.((!empty($impls['sig'])) ? implode('-', $impls['sig']) : '').'"';
                $csv .= ',"'.((!empty($impls['nom'])) ? implode('-', $impls['nom']) : '').'"';
                $csv .= ',"'.((!empty($impls['tipo'])) ? implode('-', $impls['tipo']) : '').'"';
                $csv .= ',"'.((!empty($dons['sig'])) ? implode('-', $dons['sig']) : '').'"';
                $csv .= ',"'.((!empty($dons['nom'])) ? implode('-', $dons['nom']) : '').'"';
                $csv .= ',"'.((!empty($dons['tipo'])) ? implode('-', $dons['tipo']) : '').'"';
                $csv .= ',"'.((!empty($dons['a'])) ? implode('-', $dons['a']) : '').'"';
                $csv .= ',"'.((!empty($dons['c'])) ? implode('-', $dons['c']) : '').'"';



                $html .= '<div><i>Cobertura: </i>';
                if ($row->cobertura_nal_proy == 1) {
                    $html .= 'Con Cobertura Nacional';
                }
                else {
                    $muns = $this->getMpiosCobertura($id);
                    if (!empty($muns['ids'])) {
                        $n = count($muns['ids']);
                        if ($n > 2) {
                            $html .= 'En '.$n.' Municipios';
                        }
                        else {
                            $html .= 'En: '.implode(' y ', $muns['noms']);
                        }
                    }
                }
                $html .= '</div>';

                $html .= '<div>';
                $tms = $this->getTemasAgrupado($id);
                foreach(array(1 => 'UNDAF', 2 => 'Cluster') as $c => $t) {
                    if (!empty($tms[$c])) {
                        $n = count($tms[$c]['id']);

                        $html .= '<br /><b>'.$t.'</b>: ';

                        if ($n > 0) {
                            $ficha .= '<h3>'.$t.'</h3>';
                        }

                        foreach($tms[$c]['nom'] as $i => $nom) {
                            $ficha .= $nom.'<br />';
                        }

                        if ($n > 2) {
                            $html .= $n.' temas';
                        }
                        else {
                            foreach($tms[$c]['nom'] as $i => $nom) {
                                $html .= '<span class="tema '.$c.'" id="'.$tms[$c]['id'][$i].'">'.$nom.'. </span><br />';
                            }
                        }
                    }
                }

                $html .= '</div>';

                // Undaf
                //$csv .= ',"'.(empty($tms[1]['id']) ? '' : implode('-', $tms[1]['nom'])).'"';

                // Des-Paz
                $csv .= ',"'.(empty($tms[4]['id']) ? '' : implode('-', $tms[4]['nom'])).'"';

                // Cluster
                $csv .= ',"'.(empty($tms[2]['id']) ? '' : implode('-', $tms[2]['nom'])).'"';

                // Ficha proyecto
                $html .= '<div id="ficha_'.$id.'" class="fichap">
                            <div class="fila ficha">
                                <div class="tp">'.$row->nom_proy.'</div>
                                <div><h3>Ejecutor</h3>'.$row->nom_org.'</div>';

                if (!empty($row->des_proy)) {
                    $html .= '<div><h3>Descripci&oacute;n</h3>'.$row->des_proy.'</div>';
                }

                if (!empty($impls)) {
                    $html .= '<div><h3>Implementador(es)</h3>'.(implode('<br />', $impls['nom'])).'</div>';
                }

                if (!empty($dons)) {
                    $html .= '<div><h3>Donante(s)</h3>'.(implode('<br />', $dons['nom'])).'</div>';
                }

                if (!empty($row->costo_proy)) {
                    $html .= '<div><h3>Presupuesto</h3>U$ '.$row->costo_proy.'</div>';
                }

                // Temas
                $html .= '<div>'.$ficha.'</div>';

                // Timeline
                if (!empty($row->inicio_proy) && !empty($row->fin_proy) &&
                    $row->inicio_proy != '0000-00-00' && $row->fin_proy != '0000-00-00') {

                    //$meses = $fecha->RestarFechas($row->inicio_proy, $row->fin_proy, 'meses');
                    $meses = $row->duracion_proy;
                    $hoy = date('Y-m-d');
                    $fm = $fecha->RestarFechas($hoy, $row->fin_proy, 'meses');

                    if ($fm < 0 || strtotime($hoy) > strtotime($row->fin_proy)) {
                        $w = 100;
                        $tltl = 'Finalizado';
                        $tltf = '';
                    }
                    else {
                        $tltl = $meses - $fm;
                        $tltf = $fm;
                        $w = ($meses > 0) ? 100*($tltl / $meses) : 100;

                        // Evita espacio pequeño en faltante
                        if ($w < 10) {
                            $tltl = '';
                        }
                        if ($w > 90) {
                            $tltf = '';
                        }
                    }

                    $html .= '<div>
                                <h3>Duraci&oacute;n</h3>
                                <div class="text_center">'.$row->duracion_proy.' Meses</div>
                                <div class="timeline">
                                    <div class="timelinei left" style="width:'.$w.'%">'.$tltl.'</div>
                                    <div class="text_center right" style="width:'.(100-$w).'%">'.$tltf.'</div>
                                    <div class="timelinef clear">
                                        <div class="left">'.$row->inicio_proy.'</div><div class="right">'.$row->fin_proy.'</div>
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>';
                }

                //$csv .= ','.$row->erf.','.$row->cerf;
                //$csv .= ',,';

                // Beneficiarios
                $benf_proy = $this->getCantBenef($id);

                if (!empty($benf_proy['d']['total'])) {

                    $html .= '<div><h3>Beneficiarios</h3>';

                    $_bx = array(1, 2, 3);
                    $_bg = array('h' => $_bx, 'm' => $_bx);
                    $_b = array(1 => array('d' => $_bg),
                                2 => array('i' => $_bg)
                                );

                    $_t = array(1 => 'd', 2 => 'i');

                    $titulos = array('d' => 'Directos',
                                     'i' => 'Indirectos',
                                     'h' => 'Hombres',
                                     'm' => 'Mujeres',
                                     '1' => '0-5 Años',
                                     '2' => '6-18 Años',
                                     '3' => '18+ Años'
                                     );
                    foreach($_b as $id_tipo => $b) {

                        $html .= '<div class="left half">';
                        foreach($b as $tipo => $_g) {
                            // Total D-I
                            $_t = (!empty($benf_proy[$tipo]['total'])) ?
                                            $benf_proy[$tipo]['total'] : '';
                            $html .= $_t;
                            $csv .= ','.$_t;

                            foreach($_g as $g => $_p) {
                                // Total H-M
                                $_t = (!empty($benf_proy[$tipo][$g]['total'])) ?
                                                $benf_proy[$tipo][$g]['total'] : '';

                                if (!empty($_t)) {
                                    $html .= '<br />'.$_t;
                                }
                                $csv .= ','.$_t;

                                foreach($_p as $p) {
                                    // Total Edad
                                    $_t = (!empty($benf_proy[$tipo][$g][$p])) ?
                                                    $benf_proy[$tipo][$g][$p] : '';
                                    if (!empty($_t)) {
                                        $html .= '<br />'.$_t;
                                    }
                                    $csv .= ','.$_t;
                                }
                            }
                        }
                        $html .= '</div>';
                    }
                    $html .= '<div class="clear"></div>';
                    $html .= '</div>';
                }
                else {
                    $csv .= ',,,,,,,,,,,,,,,,,,';
                }

                // Cobertura

                $_cb = $this->getMpiosCobertura($id);

                $csv .= ',';
                if (is_array($_cb['deptos'])) {
                    $csv .= implode('-', $_cb['deptos']);
                }
                else {
                    $csv .= ',';
                }

                $csv .= ',';
                if (is_array($_cb['noms'])) {
                    $csv .= implode('-', $_cb['noms']);
                }
                else {
                    $csv .= ',';
                }
                $html .= '</div></div>';

                $html .= '</div>';
                $csv .= "\r\n";

                $np++;
            }
        }


        if ($f == 'mapa') {
            $json = json_encode($map_json_a);

            // Guarda cache
            $sissh->createFileCache($json, $path_file);

            echo $json;
        }
        else {

            //$_SESSION['csv'] = utf8_encode($csv);
            /*
            $t_dir = $sissh->dir_cache_static;
            $file = $archivo->Abrir($t_dir.'/4w/proyectos_4w.csv', 'w+');
            $archivo->Escribir($file,utf8_encode($csv));
            $archivo->Cerrar($file);
             */

            $csv = utf8_encode($csv);
            $sissh->createFileCache($csv, $path_file.'.csv');
            $sissh->createFileCache($csv, $path_file_proyectos);  // proyectos_4w.csv

            // Para ficha PDF
            $sqlpdf = "SELECT DISTINCT(p.id_proy) AS id_proy, nom_proy, sig_org, nom_org, id_tema,
                nom_tema, nom_estp, tema.id_papa AS id_papa
                FROM proyecto AS p
                JOIN vinculorgpro AS v USING(id_proy)
                JOIN organizacion USING(id_org)
                JOIN depto_proy USING(id_proy)
                JOIN proyecto_tema USING(id_proy)
                JOIN tema USING(id_tema)
                JOIN estado_proy USING(id_estp)
                WHERE ".$condpdf." ORDER BY id_tema, nom_org, nom_proy";

            //echo $sqlpdf;

            $_SESSION['4w_ficha_params'] = compact('sqlpdf', 'fnal', 'fdepto', 'fmun', 'id_depto');

            // Guarda cache
            $sissh->createFileCache($html, $path_file);

            echo $html;
        }
    }

    /**
     * Retorna el sql para consultar proyectos de acuedo a los filtros
     * en session
     *
     */
    function getSqlIDProys($params=array()) {

        $tv = 1;

        //$si_proy = (empty($params['si'])) ? '4w_todos' : $params['si'];
        $si_proy = $params['si'];

        $sql = "SELECT DISTINCT(p.id_proy) AS id, nom_proy, cod_proy, des_proy, inicio_proy, fin_proy,
                costo_proy, duracion_proy, cobertura_nal_proy, cant_benf_proy,
                GROUP_CONCAT(DISTINCT id_tema) AS id_tema, nom_org, sig_org, v.id_org, id_estp, nom_estp
                FROM proyecto AS p
                INNER JOIN vinculorgpro AS v USING(id_proy)
                INNER JOIN organizacion USING(id_org)
                INNER JOIN estado_proy USING(id_estp)
                INNER JOIN proyecto_tema USING(id_proy) ";

        $cond = $this->_setConditionSi(array('si' => $si_proy));

        // Recorre todos los filtros en session
        if (!empty($_SESSION['4w_f']['c'])) {
            $cs = $_SESSION['4w_f']['c']; // Filtro
            $ids = $_SESSION['4w_f']['id']; // ID de Filtro

            $cond .= ' AND v.id_tipo_vinorgpro = 1 ';

            foreach($cs as $_i => $_c) {

                $id = $ids[$_i];

                switch($_c) {
                    case 'cluster':
                        $sql .= ' JOIN tema USING(id_tema)';
                        //$cond .= ' AND id_tema IN ('.$id.') AND id_clasificacion = 2';
                        $cond .= ' AND id_tema IN ('.$id.')';
                    break;
                    case 'undaf':
                        $sql .= ' JOIN tema USING(id_tema)';
                        $cond .= ' AND id_tema IN ('.$id.') AND id_clasificacion = 1';
                    break;
                    case 'ejecutora':
                        $cond .= ' AND v.id_org = '.$id.' AND v.id_tipo_vinorgpro = 1';
                    break;
                    case 'donante':
                        $tabla = 'w';
                        $sql .= " INNER JOIN vinculorgpro AS $tabla ON p.id_proy = $tabla.id_proy";

                        $cond .= " AND $tabla.id_org = $id AND $tabla.id_tipo_vinorgpro = 2";
                        $ed = 'Donante: ';
                    break;
                    case 'implementadora':

                        $tabla = 'y';

                        $sql .= " INNER JOIN vinculorgpro AS $tabla ON p.id_proy = $tabla.id_proy";
                        $cond .= " AND $tabla.id_org = $id AND $tabla.id_tipo_vinorgpro = 3";

                        $ed = 'Implementador: ';
                    break;
                    case 'estado':
                        $cond .= " AND id_estp IN ($id)";
                    break;
                    case 'periodo':
                        $pq = $_SESSION['4w_f']['periodo_que'];

                        // Periodo puede ser yyyy1,yyyy2
                        $y = explode(',',$id);
                        $ny = count($y);

                        switch($pq) {
                            case 'i':
                                if ($ny == 1) {
                                    $cond .= " AND YEAR(inicio_proy) = $id";
                                }
                                else {
                                    $cy = array();
                                    foreach($y as $_y) {
                                        $cy[] = "YEAR(inicio_proy) = $id";
                                    }
                                    $cond .= " AND ".implode(' OR ', $cy);
                                }
                            break;
                            case 'f':
                                if ($ny == 1) {
                                    $cond .= " AND YEAR(fin_proy) = $id";
                                }
                                else {
                                    $cy = array();
                                    foreach($y as $_y) {
                                        $cy[] = "YEAR(fin_proy) = $id";
                                    }
                                    $cond .= " AND ".implode(' OR ', $cy);
                                }
                            break;
                            case 'v':
                                if ($ny == 1) {
                                    $cond .= " AND YEAR(fin_proy) >= ".$id;
                                }
                                else {
                                    $cond .= " AND YEAR(fin_proy) >= ".min($y);
                                }
                            break;
                        }

                    break;
                    case 'pres':
                        $rg = explode('-', $id);
                        $cond .= ' AND costo_proy BETWEEN '.trim($rg[0]).' AND '.trim($rg[1]);
                    break;
                    case 'emergencia':
                        $cond .= " AND id_emergencia = $id";
                    break;
                    default:
                        // Para todo el mapa solo se muestran los proys en muns
                        //$sql .= 'JOIN mun_proy USING(id_proy)';
                    break;
                }
            }
        }

        if (!empty($_SESSION['4w_f']['c'])) {
            if (array_search('id_mun_filtro', $_SESSION['4w_f']['c']) !== false) {

                $id_mun = $_SESSION['4w_f']['id'][array_search('id_mun_filtro',$_SESSION['4w_f']['c'])];
                $sql .= ' JOIN mun_proy AS dp ON dp.id_proy = p.id_proy';
                $cond .= " AND id_mun IN ($id_mun)";
            }
            else if (array_search('id_depto_filtro', $_SESSION['4w_f']['c']) !== false) {

                $id_depto = $_SESSION['4w_f']['id'][array_search('id_depto_filtro',$_SESSION['4w_f']['c'])];
                $sql .= ' JOIN depto_proy AS dp ON dp.id_proy = p.id_proy';
                $cond .= " AND id_depto IN ($id_depto)";
            }
        }

        //$cond .= ' AND id_tipo_vinorgpro = '.$tv;

        //echo "cond=$cond";

        return compact('sql', 'cond');

    }

    /**
     * Retorna temas, orgs con # proyectos de mayor a menor
     * @access public
     * @param string $c Cual filtro
     * @param array $f Arreglo de condicionales
     * @return array $t
     */
    function getFiltrosConsulta($c, $ts=array()) {

        $extra_1 = '';

        if ($c != 'periodo') {

            $tabla = array('cluster' => 'tema',
                           'undaf' => 'tema',
                           'ejecutora' => 'organizacion',
                           'implementadora' => 'organizacion',
                           'donante' => 'organizacion',
                           'departamento' => 'departamento',
                           'municipio' => 'municipio',
                           'estado' => 'estado_proy');

            $cid_proy = 'id_proy';

            switch($c) {
                case 'cluster':
                    $cid = 'id_tema';
                    $cnom = 'nom_tema';

                    switch($_SESSION['si_proy']) {
                        case 'des':
                            $cond = 'id_clasificacion = 4';
                        break;
                        default:
                            $cond = 'id_clasificacion = 2 AND id_tema != 159';
                        break;
                    }
                break;
                case 'undaf':
                    $cid = 'id_tema';
                    $cnom = 'nom_tema';
                    $cond = 'id_clasificacion = 1';
                break;

                case 'ejecutora':
                    $cid = 'id_org';
                    $cnom = 'nom_org';
                break;

                case 'implementadora':
                    $cid = 'id_org';
                    $cnom = 'nom_org';
                    $sqlc = "LEFT JOIN vinculorgpro USING($cid)";
                    $cond = ' id_tipo_vinorgpro = 3';
                    $vino = true;
                break;

                case 'donante':
                    $cid = 'id_org';
                    $cnom = 'nom_org';
                    $sqlc = "LEFT JOIN vinculorgpro USING($cid)";
                    $cond = 'id_tipo_vinorgpro = 2';
                    $vino = true;
                break;

                case 'departamento':
                    $cid = 'id_depto';
                    $cnom = 'nom_depto';
                    $sqlc = "LEFT JOIN depto_proy USING($cid)";
                    $cond = '1=1';
                    $extra_1 = ', centroide_dd AS extra_1';
                break;
                
                case 'municipio':
                    $cid = 'id_mun';
                    $cnom = 'nom_mun';
                    $sqlc = "LEFT JOIN mun_proy USING($cid)";
                    $cond = '1=1';
                    $extra_1 = ', CONCAT_WS(",",longitude, latitude) AS extra_1';
                break;

                case 'estado':
                    $cid = 'id_estp';
                    $cnom = 'nom_estp';
                    $cond = "p.$cid = t.$cid";
                    $cid_proy = $cid;
                break;
            }

            $col_order = 'n';
        }

        $html = '';
        $_attrs = '';
        $w = 18;

        if (!empty($ts)) {
            $fr = $w / reset($ts);
            foreach($ts as $_id => $_t) {
                //$_id = $_t['id'];

                $_nom = $_id;

                if ($c != 'periodo') {
                    $sqln = "SELECT $cnom AS nom $extra_1 FROM ".$tabla[$c]." AS t WHERE $cid = '$_id'";
                    $rsn = $this->conn->OpenRecordset($sqln);
                    $rown = $this->conn->FetchObject($rsn);
                    $_nom = $rown->nom;
                    $_extra_1 = (!empty($rown->extra_1)) ? $rown->extra_1 : '';

                }

                if ($c == 'departamento' || $c == 'municipio') {

                    if ($_id == '11') {
                        $lon = -74.0833333;
                        $lat = 4.6;
                    }
                    else if ($_id == '00') {
                        $lon = 0;
                        $lat = 0;
                    }
                    else {
                        if (!empty($extra_1)) {
                            $_l = explode(',', $_extra_1);
                            if (count($_l) > 1) {
                                $lon = $_l[0];
                                $lat = $_l[1];
                            }
                        }
                    }

                    $_attrs = 'lon="'.$lon.'" lat="'.$lat.'"';

                }

                $class = (!empty($_SESSION['4w_f']) && in_array($_id, $_SESSION['4w_f']['id'])) ? 'selected' : '';

                // Depto
                if (!empty($_SESSION['4w_f']['id_depto_filtro']) &&
                    $_SESSION['4w_f']['id_depto_filtro'] == $_id
                ) {
                    $class = 'selected';
                }
                
                // Mpio
                if (!empty($_SESSION['4w_f']['id_mun_filtro']) &&
                    $_SESSION['4w_f']['id_mun_filtro'] == $_id
                ) {
                    $class = 'selected';
                }

                $nombre = '<span class="nom">'.$_nom.'</span>';

                // Tipo org en ejecutor
                if ($c == 'ejecutora' || $c == 'donante' || $c == 'implementadora') {

                    // Sigla
                    $sql = 'SELECT sig_org FROM organizacion WHERE id_org =  '.$_id;
                    $rs = $this->conn->OpenRecordset($sql);
                    $row = $this->conn->FetchRow($rs);

                    $nombre .= '<span class="sigla_tipo">';

                    if ($row[0] != $_nom && !empty($row[0])) {
                        $nombre = '<i>'.$row[0].'</i> - '.$nombre;
                    }

                    $nombre .= '</span>';

                    // Tipo de org
                    $sql = 'SELECT nomb_tipo_es FROM tipo_org JOIN organizacion USING(id_tipo) WHERE id_org =  '.$_id;
                    $rs = $this->conn->OpenRecordset($sql);
                    $row = $this->conn->FetchRow($rs);

                    $nombre .= ' | <i>'.$row[0].'</i>';

                }

                $html .=  '<div class="fila f'.$c.' '.$class.'" id="'.$_id.'" '.$_attrs.'><div class="nom">'.$nombre.'</div>';

                if ($_t > 0 && $c != 'periodo') {
                    $html .=  '<div class="num">'.$_t.'</div>';
                }

                $html .=  '<div class="clear"></div></div>';
            }
        }
        else {
            $html .= '<div class="fila">No existe informaci&oacute;n</div>';
        }

        return $html;

    }

    /**
     * Genera condicion general para 4w
     * @access private
     * @param array $params Arreglo de parametros
     * @param boolean $w Write in session
     */
    private function _setConditionSi($p = array(), $w = true) {

        $condo = '';

        if (empty($_SESSION['si_proy']) && empty($p['si'])) {
            $si = '4w';
        }
        else if (!empty($p['si'])) {
            $si = $p['si'];
        }
        else {
            $si = $_SESSION['si_proy'];
        }

        if ($w) {
            $_SESSION['si_proy'] = $si;
        }

        if (empty($_SESSION['grupo']) && empty($p['grupo'])) {
            $grupo = 'todos';
        }
        else if (!empty($p['grupo'])) {
            $grupo = $p['grupo'];
        }
        else {
            $grupo = $_SESSION['grupo'];
        }

        switch($si) {
            case '4w':

                // No con tema=ningun cluster
                $condo = ' AND id_tema != 159';

                $sql_ehp = 'SELECT id_org FROM org_espacio WHERE id_esp = 2';

                // Espacios de coordinaci�n
                switch($grupo) {
                    case 'ehp':
                        $condo .= " AND v.id_org IN ($sql_ehp)";

                    break;

                    case 'otros':
                        $condo .= " AND v.id_org NOT IN ($sql_ehp)";
                    break;
                }
            break;
            // Se usa en el resumen
            case 'des':
                if (!empty($grupo) && $grupo != 'todos') {
                    $condo .= " AND id_tema IN ($grupo)";
                }
            break;
        }
/*
        if (strpos($si,'_') !== false) {
            $t = explode('_', $si);
            $si = $t[0];
            $grupo = $t[1];

            switch($si) {
                case '4w':

                    // No con tema=ningun cluster
                    $condo = ' AND id_tema != 159';

                    $sql_ehp = 'SELECT id_org FROM org_espacio WHERE id_esp = 2';

                    // Espacios de coordinaci�n
                    switch($grupo) {
                        case 'ehp':
                            $condo .= " AND v.id_org IN ($sql_ehp)";

                        break;

                        case 'otros':
                            $condo .= " AND v.id_org NOT IN ($sql_ehp)";
                        break;
                    }
                break;
                case 'undaf-des-paz':
                    if ($w != 'todos') {
                        $condo .= " AND id_tema = $w";
                    }
                break;
            }
        }
        */

        if (isset($p['no_vs']) && !isset($p['no_o'])) {
            $cond = $condo;
        }
        else if (isset($p['no_o']) && !isset($p['no_vs'])) {
            $cond = "validado_cluster_proy = 1 AND si_proy LIKE '%$si%'";
        }
        else {
            $cond = "validado_cluster_proy = 1 AND si_proy LIKE '%$si%'".$condo;
        }

        //echo $cond;

        return $cond;
    }

    /**
     * Maneja los filtros en variable de sesión
     * @access private
     * @param array $params Arreglo de parametros
     */
    public function _manageSessionFilter($params) {

        if (empty($_SESSION['4w_f'])) {
            $_SESSION['4w_f'] = array('c' => array(), 'id' => array());
        }

        if (!empty($params['c'])) {

            // Borra filtro
            if (strpos($params['c'], '-') !== false) {
                $_i = array_search(substr($params['c'], 1), $_SESSION['4w_f']['c']);

                if (strpos($_SESSION['4w_f']['id'][$_i], ',') !== false
                    && !empty($params['id'])) {
                    $_ids = explode(',', $_SESSION['4w_f']['id'][$_i]);

                    $_b = array_search($params['id'], $_ids);

                    unset($_ids[$_b]);

                    $_SESSION['4w_f']['id'][$_i] = implode(',', $_ids);
                }
                else {
                    unset($_SESSION['4w_f']['c'][$_i]);
                    unset($_SESSION['4w_f']['id'][$_i]);
                }

                var_dump($_SESSION['4w_f']['c']);
            }
            else {
                $_c = $params['c'];
                $_id = urldecode($params['id']);
                if (!in_array($_c, $_SESSION['4w_f']['c'])) {
                    $_SESSION['4w_f']['c'][] = $_c;
                    $_SESSION['4w_f']['id'][] = $_id;
                }
                else {
                    $_i = array_search($_c, $_SESSION['4w_f']['c']);
                    $_SESSION['4w_f']['c'][$_i] = $_c;

                    if (!in_array($_id, $_SESSION['4w_f']['id'])) {
                        $_SESSION['4w_f']['id'][$_i] .= ','.$_id;
                    }
                    else {
                        $_SESSION['4w_f']['id'][$_i] = $_id;
                    }
                }
                var_dump($_SESSION['4w_f']['c']);
                var_dump($_SESSION['4w_f']['id']);
            }
        }

        if (!empty($params['id_depto_filtro'])) {
            if (strpos($params['id_depto_filtro'], '-') !== false) {
                unset($_SESSION['4w_f']['id_depto_filtro']);
            }
            else {
                $_SESSION['4w_f']['id_depto_filtro'] .= ','.$params['id_depto_filtro'];
            }
        }

        if (!empty($params['periodo_que'])) {
            $_k = 'periodo_que';
            $_c = $params[$_k];

            $_SESSION['4w_f'][$_k] = $_c;
        }

    }

    /**
     * Retorna datos resumen para mapa
     * @access public
     * @return array $r
     */
    function resumenMapa($case='total', $id='') {

        $tema_dao = FactoryDAO::factory('tema');

        // Grupos
        $si_proy = $_SESSION['si_proy'];
        $grs = array();
        $desarrollo = ($si_proy == 'des') ? true : false ;

        if ($desarrollo) {
            $tsp = $tema_dao->GetAllArray("id_clasificacion = 4 AND id_papa=0");
            foreach($tsp as $tp) {
                $todos_id = array();
                $ts = $tema_dao->GetAllArray("id_clasificacion = 4 AND id_papa = ".$tp->id);

                foreach($ts as $t) {
                    $grs[] = $t->id;
                    $todos_id[] = $t->id;
                }

                $grs[] = implode(',',$todos_id);
            }
        }
        else {
            $grs = array('ehp', 'otros');
        }

        $yyyy = date('Y');
        $yyyy_ant = $yyyy - 1;
        $aaaa = array($yyyy_ant, $yyyy);
        $num_top_sectores = 5;

        // Orgs
        $sqlo = "SELECT COUNT(DISTINCT(id_org)) FROM vinculorgpro v
            JOIN proyecto USING(id_proy)
            JOIN proyecto_tema USING(id_proy)
                 WHERE id_tipo_vinorgpro = 1 ";

        // Beneficiarios UNDAF y humanitario
        $sqlb = " SELECT DISTINCT(p.id_proy), cant_p4w_b, p.inicio_proy, p.fin_proy, p.duracion_proy
            FROM p4w_beneficiario
            JOIN proyecto AS p USING(id_proy)
            JOIN vinculorgpro v USING(id_proy)
            JOIN proyecto_tema USING(id_proy)
            WHERE tipo_rel=1 AND genero_p4w_b IS NULL AND edad_p4w_b IS NULL AND id_tipo_vinorgpro = 1
            ";

        $sqlp = 'SELECT COUNT(DISTINCT(id_proy)) FROM '.$this->tabla.'
            JOIN vinculorgpro v USING('.$this->columna_id.')
            JOIN proyecto_tema USING(id_proy)
            WHERE  id_tipo_vinorgpro = 1 ';

        $sqlpre = 'SELECT DISTINCT(p.id_proy), p.costo_proy, p.inicio_proy, p.fin_proy, p.duracion_proy
            FROM '.$this->tabla.' AS p
            JOIN vinculorgpro v USING('.$this->columna_id.')
            JOIN proyecto_tema USING(id_proy)
            WHERE id_tipo_vinorgpro = 1';

        //echo "$sqlo <br />";
        //echo "$sqlb <br />";
        //echo "$sqlp <br />";

        foreach($grs as $_g) {

            // Condiciones de si
            $condg = $this->_setConditionSi(array('grupo' => $_g), false);

            // totales para 2 años ejecucion
            foreach($aaaa as $y => $yyyy) {

                //$condgg = $condg." AND YEAR(fin_proy) >= $yyyy AND YEAR(inicio_proy) <= $yyyy ";
                $condgg = $condg." AND YEAR(fin_proy) >= $yyyy";

                // Presupuesto
                //echo "$sqlpre AND $condgg";
                $rs = $this->conn->OpenRecordset("$sqlpre AND $condgg"); // x para own alias
                $aporte_donantes = 0;
                $r[$_g]['eje']['pres'][$y] = 0;
                while ($row = $this->conn->FetchRow($rs)) {

                    $proy_id = $row[0];
                    $pres = $this->getPresupuestoBeneficiariosRealMeses($proy_id,$row[1],$row[2],$row[3],$row[4],0,$yyyy);

                    // Si es desarrollo, el presupuesto por tema se divide
                    if ($desarrollo) {

                        $clasif = 4;
                        $pres_tema = $this->getPresTema($proy_id, $pres,$clasif);

                        // No para opcion TODOS
                        if (strpos($_g,',') === false) {
                            $pres = $pres_tema[$_g];
                        }
                        else {
                            $pres = 0;
                            foreach(explode(',',$_g) as $_tt) {
                                if (isset($pres_tema[$_tt])) {
                                    $pres += $pres_tema[$_tt];
                                }
                            }
                        }
                    }

                    $r[$_g]['eje']['pres'][$y] += $pres;

                }

                //echo '<br /><br />Beneficiarios<br />'.$_g.'---'.$sqlb.' AND '.$condgg.'<br />';
                //echo "$sqlb AND $condgg";
                $rs = $this->conn->OpenRecordset($sqlb.' AND '.$condgg);
                //$row = $this->conn->FetchRow($rs);
                $r[$_g]['eje']['b'][$y] = 0;
                while ($row = $this->conn->FetchRow($rs)) {
                    $proy_id = $row[0];
                    $benef = $this->getPresupuestoBeneficiariosRealMeses($proy_id,$row[1],$row[2],$row[3],$row[4],0,$yyyy);

                    // Si es desarrollo, los beneficiarios por tema se divide
                    if ($desarrollo) {

                        $benef_tema = $this->getBenefTema($proy_id, $benef,4);

                        // No para opcion TODOS
                        if (strpos($_g,',') === false) {
                            $benef = $benef_tema[$_g];
                            if ($benef < 0) {
                                echo "proyecto = $proy_id *** benef = $benef <br>";
                            }
                        }
                        else {
                            $benef = 0;
                            foreach(explode(',',$_g) as $_tt) {
                                if (isset($benef_tema[$_tt])) {
                                    $benef += $benef_tema[$_tt];
                                }
                            }
                        }
                    }

                    $r[$_g]['eje']['b'][$y] += $benef;
                }

                // Proyectos
                //echo '<br /><br />Proyectos<br />'.$sqlp.' AND '.$condgg;
                $rs = $this->conn->OpenRecordset($sqlp.' AND '.$condgg);
                $row = $this->conn->FetchRow($rs);
                $r[$_g]['eje']['p'][$y] = $row[0];

                // Orgs
                //echo $sqlo.' AND '.$condg.'<br />';
                $rs = $this->conn->OpenRecordset($sqlo.' AND '.$condgg);
                $row = $this->conn->FetchRow($rs);
                $r[$_g]['eje']['o'][$y] = $row[0];

                $pres_total = $r[$_g]['eje']['pres'][$y];
            }
        }

        // Total desarorllo y paz
        if ($desarrollo) {
            // Condiciones de si
            $condg = $this->_setConditionSi(array(), false);

            // totales para 2 años ejecucion
            foreach($aaaa as $y => $yyyy) {

                $condgg = $condg." AND YEAR(fin_proy) >= $yyyy";

                $rs = $this->conn->OpenRecordset($sqlo.' AND '.$condgg);
                $row = $this->conn->FetchRow($rs);
                $r[$si_proy]['eje']['o'][$y] = $row[0];

                //echo $sqlb.' AND '.$condgg.'<br />';
                $rs = $this->conn->OpenRecordset($sqlb.' AND '.$condgg);
                $r[$si_proy]['eje']['b'][$y] = 0;
                while ($row = $this->conn->FetchRow($rs)) {
                    $id_proy = $row[0];
                    $benef = $this->getPresupuestoBeneficiariosRealMeses($id_proy,$row[1],$row[2],$row[3],$row[4],0,$yyyy);

                    $r[$si_proy]['eje']['b'][$y] += $benef;
                }

                //echo $sqlp.' AND '.$condg.'<br />';
                $rs = $this->conn->OpenRecordset($sqlp.' AND '.$condgg);
                $row = $this->conn->FetchRow($rs);
                $r[$si_proy]['eje']['p'][$y] = $row[0];

                // Presupuesto
                $rs = $this->conn->OpenRecordset("$sqlpre AND $condgg");
                $r[$si_proy]['eje']['pres'][$y] = 0;
                while ($row = $this->conn->FetchRow($rs)) {

                    $proy_id = $row[0];
                    $pres = $this->getPresupuestoBeneficiariosRealMeses($proy_id,$row[1],$row[2],$row[3],$row[4],0,$yyyy);

                    $r[$si_proy]['eje']['pres'][$y] += $pres;
                }
            }
        }

        return $r;
    }

    /**
     * Retorna datos resumen para mapa
     * @access public
     * @return array $r
     */
    function resumenMapaOLD($case='total', $id='') {

        // Grupos
        $grs = array('4w_ehp', 'undaf' ,'4w_otros','4w_todos');
        $yyyy = date('Y');
        $yyyy_ant = $yyyy - 1;
        $aaaa = array($yyyy_ant, $yyyy);
        $num_top_sectores = 5;
        $tema_dao = FactoryDAO::factory('tema');

        // Orgs
        $sqlo = "SELECT COUNT(DISTINCT(id_org)) FROM vinculorgpro v
            JOIN proyecto USING(id_proy)
            JOIN proyecto_tema USING(id_proy)
                 WHERE id_tipo_vinorgpro = 1 ";

        // Beneficiarios UNDAF y 4w
        $sqlb = " SELECT DISTINCT(p.id_proy), cant_p4w_b, p.inicio_proy, p.fin_proy, p.duracion_proy
            FROM p4w_beneficiario
            JOIN proyecto AS p USING(id_proy)
            JOIN vinculorgpro v USING(id_proy)
            JOIN proyecto_tema USING(id_proy)
            WHERE tipo_rel=1 AND genero_p4w_b IS NULL AND edad_p4w_b IS NULL
            ";

        $sqlp = 'SELECT COUNT(DISTINCT(id_proy)), costo_proy FROM '.$this->tabla.'
            JOIN vinculorgpro v USING('.$this->columna_id.')
            JOIN proyecto_tema USING(id_proy)
            WHERE 1=1';

        $sqlpre = 'SELECT DISTINCT(p.id_proy), p.costo_proy, p.inicio_proy, p.fin_proy, p.duracion_proy
            FROM '.$this->tabla.' AS p
            JOIN vinculorgpro v USING('.$this->columna_id.')
            JOIN proyecto_tema USING(id_proy)
            WHERE 1=1';

        //echo "$sqlo <br />";
        //echo "$sqlb <br />";
        //echo "$sqlp <br />";

        if ($case == 'total'){

            foreach($grs as $_g) {

                // Condiciones de si
                $condg = $this->_setConditionSi(array('si' => $_g), false);

                // totales para 2 a�os ejecuci�n
                foreach($aaaa as $y => $yyyy) {

                    $condgg = $condg." AND YEAR(fin_proy) >= $yyyy";
                    $sectores = array();
                    $sectores_top = array();

                    // Presupuesto
                    $rs = $this->conn->OpenRecordset("$sqlpre AND $condgg"); // x para own alias
                    $r[$_g]['eje']['pres'][$y] = 0;
                    while ($row = $this->conn->FetchRow($rs)) {
                        //echo 'a';

                        $proy_id = $row[0];
                        $pres = $this->getPresupuestoBeneficiariosRealMeses($proy_id,$row[1],$row[2],$row[3],$row[4],0,$yyyy);
                        $r[$_g]['eje']['pres'][$y] += $pres;

                    }

                    //echo $sqlb.' AND '.$condg.'<br />';
                    //echo "$sqlb AND $condgg";
                    $rs = $this->conn->OpenRecordset($sqlb.' AND '.$condgg);
                    //$row = $this->conn->FetchRow($rs);
                    $r[$_g]['eje']['b'][$y] = 0;
                    while ($row = $this->conn->FetchRow($rs)) {
                        $r[$_g]['eje']['b'][$y] += $this->getPresupuestoBeneficiariosRealMeses($row[0],$row[1],$row[2],$row[3],$row[4],0,$yyyy);
                    }

                    // Proyectos
                    //echo $sqlp.' AND '.$condgg;
                    $rs = $this->conn->OpenRecordset($sqlp.' AND '.$condgg);
                    $row = $this->conn->FetchRow($rs);
                    $r[$_g]['eje']['p'][$y] = $row[0];


                    // Orgs
                    //echo $sqlo.' AND '.$condg.'<br />';
                    $rs = $this->conn->OpenRecordset($sqlo.' AND '.$condgg);
                    $row = $this->conn->FetchRow($rs);
                    $r[$_g]['eje']['o'][$y] = $row[0];

                    $pres_total = $r[$_g]['eje']['pres'][$y];
                    $sectores_top = $this->orderTopData($sectores,'tema',$pres_total,5);

                    $r[$_g]['sectores_top'][$y] = $sectores_top;
                }

                $condg .= " AND YEAR(fin_proy) < '$yyyy-".date('n')."-".date('j')."'";

                // Totales ejecutados
                // Orgs
                //echo $sqlo.' AND '.$condg.'<br />';
                $rs = $this->conn->OpenRecordset($sqlo.' AND '.$condg);
                $row = $this->conn->FetchRow($rs);
                $r[$_g]['total']['o'] = $row[0];


                //echo $sqlb.' AND '.$condg.'<br />';
                $rs = $this->conn->OpenRecordset($sqlb.' AND '.$condg);
                $row = $this->conn->FetchRow($rs);
                $r[$_g]['total']['b'] = (empty($row[0]) ? 0 : $row[0]);


                //$r['p'] = $this->numRecords('validado_cluster_proy=1');
                //echo $sqlp.' AND '.$condg.'<br />';
                $rs = $this->conn->OpenRecordset($sqlp.' AND '.$condg);
                $row = $this->conn->FetchRow($rs);
                $r[$_g]['total']['p'] = $row[0];

                // Presupuesto
                $rs = $this->conn->OpenRecordset("SELECT SUM(costo_proy) FROM ($sqlpre AND $condg) x"); // x para own alias
                $row = $this->conn->FetchRow($rs);
                $r[$_g]['total']['pres'] = $row[0];

                // Min Year
                $sql = 'SELECT MIN(YEAR(inicio_proy))
                    FROM '.$this->tabla.'
                    JOIN vinculorgpro USING('.$this->columna_id.')
                    JOIN proyecto_tema USING(id_proy)
                    WHERE id_tipo_vinorgpro = 1
                    AND inicio_proy <> "" and inicio_proy <> 0
                    AND '.$condg;
                $rs = $this->conn->OpenRecordset($sql);
                $row = $this->conn->FetchRow($rs);

                $r[$_g]['min_year'] = $row[0];
            }
        }
        // Caso de filtros por a�o, departamento, cluster, etc
        else if ($case == 'filtros') {
            $filtro_periodo = (!empty($_SESSION['4w_f']['c']) && in_array('periodo', $_SESSION['4w_f']['c'])) ? true : false;

            foreach($grs as $_g) {

                $np = 0;
                $nb = 0;
                $no = 0;
                $npres = 0;
                $id_orgs_e = array();

                $sql_cond = $this->getSqlIDProys(array('si' => $_g));

                $sql = $sql_cond['sql'];
                $cond = $sql_cond['cond'];

                $sql .= ' WHERE '.$cond;

                $rs = $this->conn->OpenRecordset($sql);
                while ($row = $this->conn->FetchObject($rs)) {

                    // # de Orgs ejecutoras
                    if (!in_array($row->id_org, $id_orgs_e)) {
                        $no++;
                        $id_orgs_e[] = $row->id_org;
                    }

                    $benf_proy = $this->getCantBenef($row->id);
                    if (!empty($benf_proy['d']['total'])) {
                        if (!empty($filtro_periodo)) {
                            $nb += $this->getPresupuestoBeneficiariosRealMeses($row->id,$benf_proy['d']['total'],$row->inicio_proy,$row->fin_proy,$row->duracion_proy);
                        }
                        else {
                            $nb += $benf_proy['d']['total'];
                        }
                    }

                    if (!empty($row->costo_proy)) {
                        if (!empty($filtro_periodo)) {
                            $npres += $this->getPresupuestoBeneficiariosRealMeses($row->id,$row->costo_proy,$row->inicio_proy,$row->fin_proy,$row->duracion_proy);
                        }
                        else {
                            $npres += $row->costo_proy;
                        }
                    }

                    $np++;
                }

                $r[$_g]['o'] = $no;
                $r[$_g]['b'] = $nb;
                $r[$_g]['p'] = $np;
                $r[$_g]['pres'] = $npres;
            }
        }
        else {

            $r['o'] = $r['b'] = $r['pres'] = 0;

            $_idsv = $this->getIdProyectosReporte($case, $id, 2, 0);
            $_ids = implode(',', $_idsv);

                // Orgs
                $sqlo .= " AND id_proy IN ($_ids)";
                $rs = $this->conn->OpenRecordset($sqlo);
                $row = $this->conn->FetchRow($rs);
                $r['o'] = $row[0];

                // Beneficiarios UNDAF y 4w
                $sqlb .= " AND id_proy IN ($_ids)) a";
                $rs = $this->conn->OpenRecordset($sqlb);
                $row = $this->conn->FetchRow($rs);
                $r['b'] = $row[0];

                // Presupuesto
                $sqlp .= " AND id_proy IN ($_ids)";
                $rs = $this->conn->OpenRecordset($sqlp);
                $row = $this->conn->FetchRow($rs);
                $r['pres'] = $row[1];

                $r['p'] = count($_idsv);
        }

        return $r;
    }

    /**
     * Obtiene el presupuesto por tema de un proyecto
     * @param int $proy_id
     * @param int $clasif Clasificacion de los temas
     * @param int $pres Presupuesto
     *
     * @return array $pres_tema
     */
    function getPresTema($proy_id, $pres = false, $clasif = '') {

        $tema_dao = New TemaDAO();
        $pres_tema = array();
        $temas_sin_pres = array();

        $cond = (empty($clasif)) ? '' : "id_clasificacion = $clasif";

        $temas = $this->getTemas($proy_id, $cond);

        $proyecto = $this->Get($proy_id);

        if ($pres === false) {
            $pres = $this->GetFieldValue($proy_id, 'costo_proy')*1;
        }

        $pres_restante = $pres;

        //$pres_eq = $pres/count($temas);

        foreach($temas['id'] as $c => $tema_id) {

            $hijos = $tema_dao->GetAllArray("id_papa = $tema_id");
            $num_hijos = count($hijos);

            if ($num_hijos == 0) {
                $tema_pres = $temas['pres'][$c];
                if (!empty($tema_pres)) {

                    $tema_pres = $this->getPresupuestoBeneficiariosRealMeses($proy_id,$tema_pres,$proyecto->inicio_proy,$proyecto->fin_proy,$proyecto->duracion_proy);
                    $pres_restante -= $tema_pres;

                    $pres_tema[$tema_id] = $tema_pres;
                }
                else {
                    $temas_sin_pres[] = $tema_id;
                }
            }
        }

        $tspn = count($temas_sin_pres);
        foreach($temas_sin_pres as $tsp) {
            $pres_tema[$tsp] = $pres_restante / $tspn;
        }

        return $pres_tema;
    }

    /**
     * Obtiene los beneficiarios por resultado de un proyecto
     * @param int $proy_id
     * @param int $clasif Clasificacion de los temas
     * @param int $benef Cantidad de beneficiarios
     *
     * @return array $benef_tema
     */
    function getBenefTema($proy_id, $benef = false, $clasif = '') {

        $tema_dao = New TemaDAO();

        $benef_tema = array();

        $cond = (empty($clasif)) ? '' : "id_clasificacion = $clasif";

        $temas = $this->getTemas($proy_id, $cond);

        if ($benef === false) {
            $benef_tmp = $this->getCantBenef($proy_id);

            $benef = $benef_tmp['d']['total'];
        }

        $cant = $benef / count($temas['id']);

        // Mantenemos arreglo por tema, por si mas adelante se detallan
        // los beneficiarios por tema en el formulario de carga
        foreach($temas['id'] as $c => $tema_id) {

            $benef_tema[$tema_id] = $cant;
        }

        return $benef_tema;
    }

    /* Obtiene el presupuesto real de un proyecto para el filtro de periodo en session
     *
     * @param int $id Id proyecto
     * @param float $pres Presupuesto
     * @param string $inicio yyyy-mm-dd
     * @param string $fin yyyy-mm-dd
     * @param int $meses
     * @param int $id_depto Filtrar por este departamento
     * @param int $yyyy Filtrar por este año
     */
    function getPresupuestoBeneficiariosRealMeses($id,$cant,$inicio,$fin,$meses,$id_depto_reporte=0,$yyyy=false) {

        $filtro_periodo = false;
        $filtro_depto = false;
        if (isset($_SESSION['4w_f'])) {
            $filtro_periodo = array_search('periodo', $_SESSION['4w_f']['c']);
            $filtro_depto = array_search('id_depto_filtro', $_SESSION['4w_f']['c']);
            $filtro_mun = array_search('id_mun_filtro', $_SESSION['4w_f']['c']);
        }

        //echo "\nId=$id, Cantidad original = $cant, Inicio=$inicio, Fin=$fin, Meses=$meses <br />";

        if (empty($meses) && $yyyy === false && empty($id_depto_reporte)) {
            return $cant;
        }
        else if ($filtro_periodo === false && $filtro_depto === false 
                && $yyyy === false && empty($id_depto_reporte)) {
            return $cant;
        }
        else if (!empty($meses)) {

            if ($filtro_periodo !== false || $yyyy !== false) {

                if ($yyyy === false) {
                    $filtro = $_SESSION['4w_f']['id'][array_search('periodo',$_SESSION['4w_f']['c'])];
                    $y = explode(',',$filtro);
                }
                else {
                    $y = array($yyyy);
                }

                $filtro_ini = min($y);
                $filtro_fin = max($y);

                $pm = $cant / $meses;

                //echo "id=$id pm = $pm <br />";

                $tmp = explode('-', $fin);
                $yfin = $tmp[0];
                $mfin = $tmp[1];

                $tmp = explode('-', $inicio);
                $yini = $tmp[0]*1;
                $mini = $tmp[1]*1;

                //$meses_filtro = ($yfin - $yini + 1) * 12;
                $meses_filtro = ($filtro_fin - $filtro_ini + 1) * 12;

                //echo "meses_filtro = $meses_filtro <br />";

                // El filtro de a�o esta entre ini y fin
                if ($yini < $filtro_ini && $yfin > $filtro_fin) {
                    //$real = $pm * $meses_filtro;
                    //echo "entro con $cant y dio: $real<br />";
                    $real = 12 * $pm;
                }
                // El a�o de filtro es mayor que el periodo del proy
                else if ($yini < $filtro_ini && $yfin < $filtro_fin && $filtro_ini == $filtro_fin) {
                    $real = 0;
                }
                // El a�o de filtro es mayor que el periodo del proy
                else if ($yini < $filtro_ini && $yfin < $filtro_fin && $filtro_ini < $filtro_fin) {
                    $real = $mfin * $pm;
                }
                // El filtro de mas de 1 a�o toma unos meses del proyecto
                else if ($yini < $filtro_ini && $yfin > $filtro_fin && $filtro_ini < $filtro_fin) {
                    $real = 0;
                }
                // El filtro de mas de 1 a�o toma unos meses del proyecto
                else if ($yini < $filtro_ini && $filtro_fin == $yfin && $filtro_ini == $filtro_fin) {
                    $real = $mfin * $pm;
                }
                // El filtro de mas de 1 a�o toma unos meses del proyecto
                else if ($yini < $filtro_ini && $yfin == $filtro_fin && $filtro_ini < $filtro_fin) {
                    $real = ($meses_filtro - (12 - $mfin)) * $pm;
                }
                // Retorna la misma cantidad
                //else if ($yini < $filtro_ini && $yfin > $filtro_fin) {
                //    $real = 12 * $pm;
                // El año de filtro es menor que el periodo del proy
                else if ($yini > $filtro_ini && $yfin > $filtro_fin && $filtro_ini == $filtro_fin) {
                    $real = 0;
                }
                else if ($yini > $filtro_ini && $yfin > $filtro_fin && $filtro_ini < $filtro_fin) {
                    $real = (12 - $mini + 1) * $pm;
                }
                else if ($yini > $filtro_ini && $yfin == $filtro_fin && $filtro_ini < $filtro_fin) {
                    $real = $cant;
                }
                else if ($yini > $filtro_ini && $yfin < $filtro_fin && $filtro_ini < $filtro_fin) {
                    $real = $cant;
                }
                else if ($yini == $filtro_ini && $filtro_fin == $yfin && $filtro_ini < $filtro_fin) {
                    $real = ((12 - $mini + 1) * $pm) + ($mfin * $pm);
                }
                else if ($yini == $filtro_ini && $yfin > $filtro_fin && $filtro_ini < $filtro_fin) {
                    $real = ((12 - $mini + 1) * $pm) + (12 * $pm);
                }
                else if ($yini == $filtro_ini && $yfin > $filtro_fin && $filtro_ini == $filtro_fin) {
                    $real = (12 - $mini + 1) * $pm;
                }
                // Retorna la misma cantidad
                else if ($yini == $filtro_ini && $yfin < $filtro_fin) {
                    $real = $cant;
                }
                // Filtro es de 1 a�o y es el mismo del proyecto
                else if ($yini == $filtro_ini && $yfin == $filtro_fin) {
                    $real = $cant;
                }

                //}

                if (!isset($real)) {
                    //echo "Proyecto = $id, benef = $cant<br />";
                    //echo "$cant,$inicio,$fin,$meses,$yyyy,$real<br />";
                } else {
                    //echo "Proyecto = $id, real = $real<br />";
                    $cant = $real;
                }
            }

            if ($yyyy === false && ($filtro_mun !== false || !empty($id_mun_reporte))) {
                if (empty($id_mun_reporte)) {
                    $muns = $_SESSION['4w_f']['id'][array_search('id_mun_filtro',$_SESSION['4w_f']['c'])];
                }
                else {
                    $muns = $id_mun_reporte;
                }

                //echo "<br />".$deptos."<br />";
                $muns = $this->getMpiosCobertura($id);
                $total_muns = count($muns['ids']);
                //echo "\nproyecto=$id - total_muns=$total_muns - cant = $cant <br />";
                if (!empty($total_muns)) {
                    $pm = $cant / $total_muns;

                    $cant = $pm;
                }
                else {
                    $cant = 0;
                }
            }
            else if ($yyyy === false && ($filtro_depto !== false || !empty($id_depto_reporte))) {
                if (empty($id_depto_reporte)) {
                    $deptos = $_SESSION['4w_f']['id'][array_search('id_depto_filtro',$_SESSION['4w_f']['c'])];
                }
                else {
                    $deptos = $id_depto_reporte;
                }

                //echo "<br />".$deptos."<br />";
                $muns = $this->getMpiosCobertura($id);
                $total_muns = count($muns['ids']);
                //echo "\nproyecto=$id - total_muns=$total_muns - cant = $cant <br />";
                if (!empty($total_muns)) {
                    $pm = $cant / $total_muns;

                    $muns_depto_filtro = $this->getMpiosCobertura($id,'id_depto IN ('.$deptos.')');
                    $num_muns_deptos_filtro = count($muns_depto_filtro['ids']);
                    //echo "\nmunicipios en el departamento=$deptos es $num_muns_deptos_filtro";
                    $cant = $pm * $num_muns_deptos_filtro;
                }
                else {
                    $cant = 0;
                }
            }

            //echo "Proyecto = $id, benef = $cant<br />";
            //echo "$cant,$inicio,$fin,$meses,$yyyy,$real<br />";

            //return number_format($cant,2,',','');
            return $cant;
        }
        else if (empty($meses) && !empty($id_depto_reporte)) {
            if (empty($id_depto_reporte)) {
                $deptos = $_SESSION['4w_f']['id'][array_search('id_depto_filtro',$_SESSION['4w_f']['c'])];
            }
            else {
                $deptos = $id_depto_reporte;
            }

            //echo "<br />".$deptos."<br />";
            $muns = $this->getMpiosCobertura($id);
            $total_muns = count($muns['ids']);
            if (!empty($total_muns)) {
                $pm = $cant / $total_muns;

                $muns_depto_filtro = $this->getMpiosCobertura($id,'id_depto IN ('.$deptos.')');
                $num_muns_deptos_filtro = count($muns_depto_filtro['ids']);
                $cant = $pm * $num_muns_deptos_filtro;
            }
            else {
                $cant = 0;
            }

            return $cant;

        }
        else {
            return $cant;
        }

    }

    /* Ordena y limita data para Top
     *
     * @param array $data Arreglo a ordenar
     * @param string $caso Que se va a ordenar
     * @param int $total  Total para sacar porcentaje
     *
     * @return array $top
     */
    public function orderTopData($data,$caso,$total,$num_top=0) {

        $dao = FactoryDAO::factory($caso);

        // Ordena
        arsort($data);

        if ($num_top > 0) {
            $data = array_slice($data,0,$num_top,true);
        }

        $top = array();

        foreach($data as $ids => $valor) {

            if ($valor > 0) {

                if ($caso == 'org') {
                    if ($ids == 0) {
                        $nom = 'Sin identificar';
                    }
                    else {
                        $sig = $dao->GetFieldValue($ids,'sig_org');
                        $nom = $dao->GetName($ids);

                        if (!empty($sig) && $nom != $sig) {
                            $nom .= ' | '.$sig;
                        }
                    }
                } elseif ($caso == 'depto') {
                    $nom = ($ids == 0) ? 'Nacional' : $dao->GetName($ids);
                }
                else {
                    $nom = $dao->GetName($ids);
                }

                $porc = number_format((100*$valor/$total),2);
                $valor = number_format($valor,0);

                $top[] = array($nom,$valor,$porc);
            }
        }

        return $top;
    }

    /**
     * Actualiza el estado de los proyectos mediante cronjob
     *
     */
    function updateEstadoProyectos() {

        // Ejecucion
        $sql = "UPDATE proyecto SET id_estp = 3 WHERE fin_proy > now()";
        $this->conn->execute($sql);

        // Finalizados
        $sql = "UPDATE proyecto SET id_estp = 4 WHERE fin_proy <= now()";
        $this->conn->execute($sql);

    }
}

Class P4wAjax extends P4wDAO {

    /**
     * Retorna lista de proyectos para dashboard Scroll Loader
     * @access public
     */
    function getPrsDbScroll($sr, $t){

        $proys = $this->getProsDashboard($sr, $t);

        foreach($proys as $p => $pr) {
            $_p = $p + $sr;
        }

    }

    /**
     * Retorna proyectos para openlayers
     * @access public
     * @param array $params
     */
    function getProysMapa4w($params){

        $this->getProysMapa($params);

    }

    /**
     * Genera ficha PDF
     * @access public
     */
    function fichaPDF(){

        extract($_SESSION['4w_ficha_params']);

        $sectores = array();
        $pryspdf = array();
        $tema_dao = new TemaDAO;

        $_rs = $this->conn->OpenRecordset($sqlpdf);
        $sant = '';
        $cb = '';
        while ($row = $this->conn->FetchObject($_rs)) {

            $_sh = "SELECT COUNT(id_tema) FROM tema WHERE id_papa = ".$row->id_tema;
            $_osh = $this->conn->OpenRecordset($_sh);
            $_rh = $this->conn->FetchRow($_osh);
            $_hijos = (empty($_rh[0])) ? false : true;

            if ($row->id_papa > 0) {
                $_tp = $tema_dao->Get($row->id_papa);
                $row->nom_tema = $_tp->nombre.' - '.$row->nom_tema;
            }

            if ($sant != $row->id_tema && !$_hijos) {
                $sectores[$row->id_tema] = $row->nom_tema;
            }

            $_cb = $this->getMpiosCobertura($row->id_proy);

            if ($fnal) {
                if (is_array($_cb['deptos'])) {
                    $cb = implode(',', $_cb['deptos']);
                }
            }
            else {
                if (empty($_cb['all'])) {
                    if (is_array($_cb['noms'])) {
                        $cb = implode(',', $_cb['noms']);
                    }
                }
                else {
                    $cb = $_cb['all'];
                }
            }

            $sant = $row->id_tema;

            $pryspdf[$row->id_tema][] = array('n' => $row->nom_proy,
                                              'e' => $row->nom_org.' - '.$row->sig_org,
                                              'est' => $row->nom_estp,
                                              'cb' => $cb);
        }

        include('consulta/p4w_ficha.php');
    }
}

?>
