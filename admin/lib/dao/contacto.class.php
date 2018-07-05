<?
/**
 * DAO de Contacto
 *
 * Contiene los métodos de la clase Contacto
 * @author Ruben A. Rojas C.
 */

Class ContactoDAO {

	/**
	 * Conexión a la base de datos
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
	 * URL para redireccionar despu�s de Insertar, Actualizar o Borrar
	 * @var string
	 */
	var $url;

	/**
	 * Constructor
	 * Crea la conexi�n a la base de datos
	 * @access public
	 */
	function __construct(){
		//$this->conn = MysqlDb::getInstance();
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "contacto";
		$this->columna_id = "ID_CON";
		$this->columna_nombre = "NOM_CON";
		$this->columna_order = "NOM_CON";
		$this->num_reg_pag = 20;
		$this->url = "index.php?accion=listar&class=ContactoDAO&method=ListarTabla&param=";

	}

	/**
	 * Consulta los datos de una Contacto
	 * @access public
	 * @param int $id ID del Contacto
	 * @return VO
	 */
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		if ($rs = $this->conn->OpenRecordset($sql)){
            $row_rs = $this->conn->FetchObject($rs);

            //Crea un VO
            $contacto_vo = New Contacto();

            //Carga el VO
            $contacto_vo = $this->GetFromResult($contacto_vo,$row_rs);

            //Retorna el VO
            return $contacto_vo;
        }
        else {
            return false;
        }
	}

	/**
	* Consulta el nombre
	* @access public
	* @param int $id ID
	* @return VO
	*/
	function GetName($id){
		$sql = "SELECT CONCAT(nom_con,' ',ape_con) FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchRow($rs);

		//Retorna el VO
		return $row_rs[0];
	}

	/**
	 * Consulta los datos de los Contacto que cumplen una condici�n
	 * @access public
     * @param string $condicion Condici�n que deben cumplir los Contacto y que se agrega en el SQL statement.
     * @param string $order_by
     * @param string $limit
	 * @return array Arreglo de VOs
	 */
	function GetAllArray($condicion, $order_by='', $limit=''){

        $sql = "SELECT DISTINCT c.* FROM $this->tabla c
                LEFT JOIN ".$this->tabla."_org USING ($this->columna_id)
                LEFT JOIN ".$this->tabla."_esp USING (id_con)
                LEFT JOIN espacio_tipo_usuario USING (id_esp)";

		if ($condicion != ""){
			$sql .= " WHERE ".$condicion;
        }

        if (empty($order_by)){
            $order_by = $this->columna_order;
        }

        $sql .= " ORDER BY ".$order_by;

        if (!empty($limit)) {
            $sql .= " LIMIT $limit";
        }

		//echo $sql;
		$array = Array();

		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchObject($rs)){
			//Crea un VO
			$vo = New Contacto();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}

		//Retorna el Arreglo de VO
		return $array;
	}

	/**
	 * Lista los Contacto que cumplen la condici�n en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listar�n los Contacto, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del Contacto que ser� selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condici�n que deben cumplir los Contacto y que se agrega en el SQL statement.
	 */
	function ListarCombo($formato,$valor_combo,$condicion){
		$arr = $this->GetAllArray($condicion);
		$num_arr = count($arr);
		$v_c_a = is_array($valor_combo);

		for($a=0;$a<$num_arr;$a++){
			$vo = $arr[$a];

			if ($valor_combo == "" && $valor_combo != 0)
				echo "<option value=".$vo->id.">".$vo->nombre."</option>";
			else{
				echo "<option value=".$vo->id;

				if (!$v_c_a){
					if ($valor_combo == $vo->id)
						echo " selected ";
				}
				else{
					if (in_array($vo->id,$valor_combo))
						echo " selected ";
				}

				echo ">".$vo->nombre."</option>";
			}
		}
	}

	/**
	 * Consulta los caracteres iniciales de los nombres de los contacto
	 * @access public
	 * @param string $cond Condicion que deben cumplir los contacto
	 * @return array $chrs Arreglo con los caracteres
	 */
	function getLetrasIniciales($cond=''){

		$espacio_usuario_dao = new EspacioUsuarioDAO();
		$arr = Array();

		if (isset($_SESSION["id_tipo_usuario_s"])){
			/*
            $condicion = "id_tipo_usuario = ".$_SESSION["id_tipo_usuario_s"];
			$espacios_x_tipo = $espacio_usuario_dao->GetAllArray($condicion);

			$cond_c = " AND id_esp IN (".implode(",",$espacios_x_tipo->id_espacio).")";
            */

			$sql = "SELECT DISTINCT UPPER(LEFT(nom_con,1)) FROM $this->tabla c LEFT JOIN ".$this->tabla."_org o_c USING ($this->columna_id)
					LEFT JOIN contacto_esp USING ($this->columna_id)
					";

			if ($cond != ''){
				$sql .= " WHERE $cond";
			}

			//$sql .= $cond_c;

			$sql .= " ORDER BY $this->columna_nombre";

			$rs = $this->conn->OpenRecordset($sql);
			while ($row = $this->conn->FetchRow($rs)){
				$arr[] = $row[0];
			}
		}

		return $arr;
	}

	/**
	 * Consulta los ID de las orgs de las que existen contactos
	 * @access public
	 * @param string $cond Condicion que deben cumplir los contacto
	 * @return array $ids Arreglo con los IDs
	 */
	function getIDOrgContactos($cond=''){

		$arr = Array();
		$sql = "SELECT DISTINCT id_org FROM contacto_org o_c LEFT JOIN $this->tabla c USING ($this->columna_id) ";

		if ($cond != ''){
			$sql .= " WHERE $cond";
		}

		$rs = $this->conn->OpenRecordset($sql);
		while ($row = $this->conn->FetchRow($rs)){
			$arr[] = $row[0];
		}

		return $arr;
	}

	/**
	 * Muestra el indice en el listado de contacto
	 * @access public
	 * @param array $letras_ini Arreglo con los caracteres
	 * @param string $letra_inicial Actual letra inicial
	 */
	function indiceListaContactos($letras_ini,$letra_inicial){

        echo "Indice:&nbsp;";

		//Todos
		$class = ($letra_inicial == '') ? 'a_big' : 'a_normal' ;

		foreach ($letras_ini as $letra){
			$class = (strtolower($letra) == strtolower($letra_inicial)) ? 'a_big' : 'a_normal' ;

			echo "<a href='#' onclick=\"aplicarFiltro('".$letra."')\" class='$class'>".$letra."</a>";
			echo "&nbsp;";
		}
        echo " | <a href='#' onclick=\"aplicarFiltro('')\" class='$class'>Todos</a>&nbsp;&nbsp;";
	}

	/**
	 * Lista los Contacto en una Tabla
	 * @access public
	 */
	function ListarTabla($cond){

		$org_dao = new OrganizacionDAO();
		$espacio_usuario_dao = new EspacioUsuarioDAO();
		$espacio_dao = new EspacioDAO();
		$contacto_col = new ContactoColDAO();
		$contacto_col_op = new ContactoColOpDAO();
        $mun_dao = new MunicipioDAO();
		$cond = "1 = 1";
		$id_org = -1;
        $id_mun = -1;
		$num_cols_tabla = 9;
        $id_esp = -1;
        $jefes = 0;
        $jefes_chk = array('no' => 'checked', 'si' => '');
		$n = '';  //Valor de busqueda por nombre
  		$url = "index.php?m_e=contacto&accion=listar&class=ContactoDAO&method=ListarTabla&param=";
		$filtro = false;

		//CARACTERISITCAS
		$caracts = $contacto_col->GetAllArray('');
		$num_caracts = count($caracts);

		$colspan_total = $num_cols_tabla + $num_caracts;

        // Filtro Org
		if (isset($_GET["id_org"]) && $_GET["id_org"] != 0){
			$cond .= " AND id_org=".$_GET["id_org"];
			$id_org = $_GET["id_org"];
			$filtro = true;
		}

        // Filtro ciudad
        if (isset($_GET["id_mun"]) && $_GET["id_mun"] != 0){

            $id_mun = $_GET["id_mun"];

            if (strlen($id_mun) == 5) {
                $cond .= " AND id_mun=".$_GET["id_mun"];
            }
            else {
                $cond .= " AND id_mun LIKE '".$_GET["id_mun"]."%'";
            }

			$id_mun = $_GET["id_mun"];

			$filtro = true;

		}

        // Filtro nombres
        if (isset($_GET["n"]) && $_GET["n"] != ''){
			$n = $_GET["n"];
			$cond .= " AND CONCAT(nom_con,' ',ape_con) LIKE '%$n%'";

			$filtro = true;

		}

        $cond_c = $cond;
		//CONSULTA LOS ESPACIOS POR TIPO DE USUARIO
        $condicion = "id_tipo_usuario = ".$_SESSION["id_tipo_usuario_s"];
		$espacios_x_tipo = $espacio_usuario_dao->GetAllArray($condicion);


		if (isset($_GET["id_esp"]) && $_GET["id_esp"] != '-1'){
            $id_esp = $_GET["id_esp"];
            $cond_c .= " AND id_esp = ".$id_esp;

			$filtro = true;

        }
        else if (count($espacios_x_tipo->id_espacio) > 0){
            // Filtro jefes
            if (isset($_GET["jefes"]) && $_GET["jefes"] != ''){
                $jefes = $_GET["jefes"];
                if ($jefes == 0) {
                    //unset($espacios_x_tipo->id_espacio[array_keys($espacios_x_tipo->id_espacio, 66)[0]]);
                    //$cond_c .= " AND id_esp != 66";
                    $cond_c .= " AND id_con NOT IN (SELECT id_con FROM contacto JOIN contacto_esp USING(id_con) WHERE id_esp = 66) ";
                }
                else {
                    $jefes_chk['no'] = '';
                    $jefes_chk['si'] = 'checked';
                }
            }
            // Todos los espacios o contactos sin espacios
		    $cond_c .= " AND (id_esp IN (".implode(",",$espacios_x_tipo->id_espacio).") OR id_esp IS NULL )";
		}



        //Consulta las letras y numeros iniciales de los contactos para generar el indice-paginacion
		$letras_ini = $this->getLetrasIniciales($cond_c);
		$si_letra_ini = (count($letras_ini) > 0) ? 1 : 0;
		$letra_inicial = '';
		if ($si_letra_ini == 1){

			if (!$filtro) {
				$letra_inicial = 'A';
			}

            if (isset($_GET["li"])) {
                if ($_GET["li"] != '') {
                    $letra_inicial = $_GET["li"];
                }
                else { 
                    unset($letra_inicial);
                }
            }

			if (!empty($letra_inicial)) {
				$cond_c .= " AND nom_con LIKE '$letra_inicial%'";
			}
		}

        $orders = array(
                        'nom_con ASC' => 'Nombre ASC',
                        'nom_con DESC' => 'Nombre DESC',
                        'ape_con ASC' => 'Apellido ASC',
                        'ape_con DESC' => 'Apellido DESC',
                        'update_con ASC' => 'Fecha de actualizaci&oacute;n ASC',
                        'update_con DESC' => 'Fecha de actualizaci&oacute;n DESC',
                    );

        $order_by = '';
        if (!empty($_GET['order_by'])) {
            $order_by = $_GET['order_by'];
            $sel_order[$order_by] = 'selected';
        }

		//echo $cond_c;

        $arr = $this->GetAllArray($cond_c, $order_by);
		$num_arr = count($arr);

        $ciudades = $this->getCiudadesDropdown();
        $id_orgs = $this->getIDOrgContactos($cond);

        //CARACTERISITCAS
        $caracts = $contacto_col->GetAllArray('');

        include('contacto/index.php');

	}

	/**
	 * Carga un VO de Contacto con los datos de la consulta
	 * @access public
	 * @param object $vo VO de Contacto que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de Contacto con los datos
	 */
	function GetFromResult ($vo,$Result){
		$vo->id = $Result->ID_CON;
		$vo->nombre = $Result->NOM_CON;
		$vo->apellido = $Result->APE_CON;
		$vo->tel = $Result->TEL_CON;
		$vo->cel = $Result->CEL_CON;
		$vo->fax = $Result->FAX_CON;
		$vo->email = $Result->EMAIL_CON;
		$vo->social = $Result->SOCIAL_CON;
		$vo->actua = $Result->UPDATE_CON;
		$vo->id_mun = $Result->ID_MUN;
		$vo->mailchimp = $Result->MAILCHIMP_STATUS;

		//Organizacion
		$sql = "SELECT id_org FROM ".$this->tabla."_org WHERE $this->columna_id = $vo->id";
		$rs = $this->conn->OpenRecordset($sql);
		while ($row = $this->conn->FetchRow($rs)){
			$vo->id_org[] = $row[0];
		}

		//Espacios
		$sql = "SELECT id_esp FROM ".$this->tabla."_esp WHERE $this->columna_id = $vo->id";
		$rs = $this->conn->OpenRecordset($sql);
		while ($row = $this->conn->FetchRow($rs)){
			$vo->id_espacio[] = $row[0];
		}

		//Caracteristicas
		$sql = "SELECT * FROM ".$this->tabla."_opcion_valor WHERE $this->columna_id = $vo->id";
		$rs = $this->conn->OpenRecordset($sql);
		while ($row = $this->conn->FetchObject($rs)){
			$vo->caracteristicas[$row->ID_CONTACTO_COL] = $row->ID_CONTACTO_COL_OPCION;
		}

		return $vo;
	}

	/**
	 * Inserta un Contacto en la B.D.
	 * @access public
	 * @param object $contacto_vo VO de Contacto que se va a insertar
	 */
	function Insertar($contacto_vo, $alert = 1){

		//CONSULTA SI YA EXISTE
		//$cat_a = $this->GetAllArray("(".$this->columna_nombre." = '".$contacto_vo->nombre."' AND ape_con = '$contacto_vo->apellido') OR email_con = '$contacto_vo->email'");
		$cat_a = $this->GetAllArray($this->columna_nombre." = '".$contacto_vo->nombre."' AND ape_con = '$contacto_vo->apellido' AND email_con = '".$contacto_vo->email."'");

		if (count($cat_a) == 0){
			$sql =  "INSERT INTO ".$this->tabla."
			(".$this->columna_nombre.",ape_con,tel_con,cel_con,fax_con,email_con,social_con,creac_con,update_con,id_mun)
			VALUES
			('".$contacto_vo->nombre."','$contacto_vo->apellido','".$contacto_vo->tel."','".$contacto_vo->cel."','".$contacto_vo->fax."','".$contacto_vo->email."','".$contacto_vo->social."', now(), now(),'".$contacto_vo->id_mun."')";
			$this->conn->Execute($sql);

			$contacto_vo->id = $this->conn->GetGeneratedID();

			$this->insertarTablasUnion($contacto_vo);

            if ($alert == 1){
			?>

			<script>
				alert("Registro creado!");
				//location.href = '<?=$this->url;?>';
			</script>
			<?
            }
		}
		else{
			?>
			<script>
				alert("Error - Existe un Contacto con el mismo nombre o con el mismo email");
			</script>
			<?
		}
	}

    /**
	 * Inserta un Contacto desde 4w
	 * @access public
	 * @param object $contacto_vo VO de Contacto que se va a insertar
	 */
	function InsertarCon4w($c_vo){
		//CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray($this->columna_nombre." = '".$c_vo->nombre."' AND ape_con = '$c_vo->apellido' OR email_con = '$c_vo->email'");

		if (count($cat_a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.",ape_con,tel_con,cel_con,fax_con,email_con,si_con,creac_con,update_con) VALUES
                    ('".$c_vo->nombre."','$c_vo->apellido','".$c_vo->tel."','".$c_vo->cel."','".$c_vo->fax."','".$c_vo->email."','4w',now(),now())";
			$this->conn->Execute($sql);
			$c_vo->id = $this->conn->GetGeneratedID();

			$this->insertarTablasUnion($c_vo);

            $ht = '<h1>Contacto registrado con &eacute;xito</h1><br />
                    Debe volver a buscarla en el formulario del proyecto';

		}
		else{
            $ht = '<h1>ERROR.... A pesar de las advertencias, est&aacute; intentando crear un Contacto que ya existe!!!</h1>
                    <br />
                    No olvide que puede buscar por Nombre, apellido o email';
		}

        echo "<div class='alert'>$ht</div>";

	}

	/**
	 * Inserta las tablas de union de un Contacto en la B.D.
	 * @access public
	 * @param object $contacto_vo VO de Contacto que se va a insertar
	 */
	function insertarTablasUnion($contacto_vo){

		//Organizaciones
		foreach($contacto_vo->id_org as $id_org){
			$sql = "INSERT INTO ".$this->tabla."_org ($this->columna_id,id_org) VALUES ($contacto_vo->id,$id_org)";
			$this->conn->Execute($sql);
		}

		//Espacios
		foreach($contacto_vo->id_espacio as $id){
			$sql = "INSERT INTO ".$this->tabla."_esp ($this->columna_id,id_esp) VALUES ($contacto_vo->id,$id)";
			$this->conn->Execute($sql);
		}

		//Caracteristicas
		foreach($contacto_vo->caracteristicas as $id_contacto_col=>$id_contacto_col_op){
			$sql = "INSERT INTO ".$this->tabla."_opcion_valor ($this->columna_id,id_contacto_col,id_contacto_col_opcion) VALUES ($contacto_vo->id,$id_contacto_col,$id_contacto_col_op)";
			//echo $sql;
			$this->conn->Execute($sql);
		}
	}

	/**
	 * Actualiza un Contacto en la B.D.
	 * @access public
	 * @param object $contacto_vo VO de Contacto que se va a actualizar
	 */
	function Actualizar($contacto_vo, $alert = 1){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_nombre." = '".$contacto_vo->nombre."',";
		$sql .= "ape_con = '".$contacto_vo->apellido."',";
		$sql .= "tel_con = '".$contacto_vo->tel."',";
		$sql .= "cel_con = '".$contacto_vo->cel."',";
		$sql .= "fax_con = '".$contacto_vo->fax."',";
		$sql .= "update_con = now(),";
		$sql .= "email_con = '".$contacto_vo->email."',";
		$sql .= "social_con = '".$contacto_vo->social."',";
        $sql .= "id_mun = '".$contacto_vo->id_mun."'";

		$sql .= " WHERE ".$this->columna_id." = ".$contacto_vo->id;

		$this->conn->Execute($sql);
		$this->borrarTablasUnion($contacto_vo->id);
		$this->insertarTablasUnion($contacto_vo);

        if ($alert == 1){
            ?>
            <script>
                alert("Registro actualizado!");
                location.href = '<?=$this->url;?>';
            </script>
            <?
        }
	}

	/**
	 * Borra un Contacto en la B.D.
	 * @access public
	 * @param int $id ID del Contacto que se va a borrar de la B.D
	 */
	function Borrar($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		$this->borrarTablasUnion($id);

		?>
		<script>
			alert("Registro eliminado con �xito!");
			location.href = '<?=$this->url;?>';
		</script>
		<?
	}

	/**
	 * Borra las tablas de union de un Contacto en la B.D.
	 * @access public
	 * @param int $id ID del Contacto que se va a borrar de la B.D
	 */
	function borrarTablasUnion($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla."_org WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		$sql = "DELETE FROM ".$this->tabla."_esp WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		$sql = "DELETE FROM ".$this->tabla."_opcion_valor WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);
	}

	/**
	 * Importa archivo de texto
	 * @access public
	 * @param string $userfile
	 */
	function importarCSV($userfile){

        $contacto_op_col_dao = new ContactoColOpDAO();
        $esp_dao = new EspacioDAO();
        $org_dao = new OrganizacionDAO();
        $mun_dao = new MunicipioDAO();
        $archivo = New Archivo();

        $separador = '|';
        $separador_nuevos = '*';
        $error = false;
        $col_ini_espacios = 12;  // Col M,N,O

        $show_fecha = true;
        $show_fila = true;
        $show_org = true;
        $show_email = true;
        $show_carac = true;
        $show_esp = true;
        $show_update = true;
        $show_mun = true;

        if (empty($_POST['go'])) {
            die('Defina variable go en el formulario, para insertar o no los registros');
        }

        $go = ($_POST['go'] == 'si') ? true : false;

        $orgs_esto = array('UARIV',
                            'GIZ');
        $orgs_por_esto = array('Unidad De Atencion Y Reparacion Integral A Victimas',
                                'Deutsche Gesellschaft f�r Internationale Zusammenarbeit');

        $muns_esto = array(
                            'PUERTO TRUINFO',
                            'SAN ANDR�S DE CUERQUIA',
                            'SAN PEDRO DE LOS MIL.'
                          );
        $muns_por_esto = array(
                                    'Puerto Triunfo',
                                    'San Andr�s',
                                    'San Pedro'
                                    );

        $file_tmp = $userfile['tmp_name'];
		$file_nombre = $userfile['name'];

		$path = "../admin/contacto/csv/".$file_nombre;

		$archivo->SetPath($path);
		$archivo->Guardar($file_tmp);

		$fp = $archivo->Abrir($path,'r');

        // Check separador
        $contenido = $archivo->LeerEnArreglo($fp);
        $col_fin = substr_count($contenido[1],'|');

        if (strpos($contenido[0],'|') === false) {
            ?>
            <script type="text/javascript">
                alert('El archivo se debe exportar desde la hoja de calculo usando como separador el caracter | (barra vertical)');
                location.href = '?m_e=contacto&accion=importar';
            </script>
            <?php
        }

        //ob_start();
        $fin = count($contenido);
        $ins = 0;
        $act = 0;
        $org_new = array();
        $col_new = array();
        $esp_new = array();
        $mun_new = array();
        $sql = '';
        for($i=2;$i<$fin;$i++){
            $fila = utf8_decode($contenido[$i]);
            $l = explode($separador,$fila);
            $i1 = $i + 1;
            if ($show_fila) {
                echo "Fila: $i1 ::: <br />";
            }

            if (!empty($l[0]) && !empty($l[1])){

                $nombre = trim(ucwords(strtolower($l[0])));
                $apellido = trim(ucwords(strtolower($l[1])));
                $email = trim(strtolower($l[6]));


                $org = trim($l[2]);
                $tel = trim($l[3]);
                $cel = trim($l[4]);
                $fax = trim($l[5]);
                $cargo = trim($l[7]);
                $mun = trim($l[8]);
                $fecha = trim($l[9]);
                $encab = trim($l[10]);
                $saludo = trim($l[11]);

                $contacto_vo = new Contacto();

                //echo "<tr>";

                $contacto_vo->nombre = $nombre;
                $contacto_vo->apellido = $apellido;
                $contacto_vo->update_con = date('Y-m-d',strtotime($fecha));

                if ($show_fecha) {
                    echo "Fecha=".$contacto_vo->update_con.'<br />';
                }

                //Telefono, se debe revisar si tiene la palabra Cel, para separar
                if (!empty($tel) && strpos($tel,"Cel:") === false) {
                    $contacto_vo->tel = $tel;
                }
                else if (isset($linea[3][0]) && strpos($linea[3],"Cel:") !== false) {
                    $tmp = explode("Cel: ",$tel);
                    if (strlen($tmp[0]) > 0)	$contacto_vo->tel = $tmp[0];
                    $contacto_vo->cel = $tmp[1];
                }

                $contacto_vo->cel = $cel;
                $contacto_vo->fax = $fax;

                // Check email
                if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    if ($show_email) {
                        echo "<br />- Email invalido: $email";
                    }
                }
                else {
                    $contacto_vo->email = $email;
                }

                // Check Org
                $org_n = $org;
                if (strpos($org, $separador_nuevos) && preg_match('/^Nuev/i',$org)) {
                    $tmp = explode($separador_nuevos,$org);
                    $org_n = $tmp[1];
                }

                // Reemplazos
                $org_n = str_replace($orgs_esto,$orgs_por_esto,$org_n);

                $orgs = $org_dao->GetAllArrayID("nom_org = '$org_n'",'','');
                if (!in_array($org_n, $org_new)) {
                    if (empty($orgs[0])) {
                        $error = true;

                        // Por sigla
                        $orgs = $org_dao->GetAllArrayID("sig_org = '$org_n'",'','');
                        if (empty($orgs[0])) {
                            if ($show_org) {
                                echo "No existe la Org. <b>$org_n</b> <br />";
                            }

                            $org_new[] = $org_n;
                        }
                        else {
                            $contacto_vo->id_org[] = $orgs[0];
                        }

                    }
                    else {
                        $contacto_vo->id_org[] = $orgs[0];
                    }
                }
                if (!empty($mun)) {

                    // Check ciudad
                    $mun_n = $mun;
                    if (strpos($mun, $separador_nuevos) && preg_match('/^Nuev/i',$mun)) {
                        $tmp = explode($separador_nuevos,$mun);
                        $mun_n = $tmp[1];
                    }

                    // Reemplazos
                    $mun_n = str_replace($muns_esto,$muns_por_esto,$mun_n);

                    $muns = $mun_dao->GetAllArrayID("nom_mun = '$mun_n'",'','');
                    if (!in_array($mun_n, $mun_new)) {
                        if (empty($muns[0]) && !in_array($mun_n, $mun_new)) {
                            if ($show_mun) {
                                echo "No existe el municipio <b>$mun_n</b> <br />";
                            }
                            $mun_new[] = $mun_n;
                            $error = true;
                        }
                        else {
                            $contacto_vo->id_mun = $muns[0];
                        }
                    }
                }
                else {
                    $contacto_vo->id_mun = 0;
                }

                // Check cargo-encabezado-saludo
                $cols = array($cargo,$saludo,$encab);
                foreach($cols as $c => $col) {

                    if (!empty($col)) {
                        $id_col = $c + 1;

                        $col_n = $col;
                        if (strpos($col, $separador_nuevos) && preg_match('/^Nuev/i',$col)) {
                            $tmp = explode($separador_nuevos,$col);
                            $col_n = trim($tmp[1]);
                        }

                        $ops = $contacto_op_col_dao->GetAllArrayID("nom_contacto_col_opcion = '$col_n' AND id_contacto_col = $id_col",'','');
                        if (!in_array($col_n, $col_new)) {
                            if (empty($ops[0])) {
                                if ($show_carac) {
                                    echo "No existe la carac--$id_col. <b>$col_n</b> <br />";
                                }
                                $sql .= "INSERT INTO contacto_col_opcion (id_contacto_col,nom_contacto_col_opcion) values ($id_col,'".$col_n."');<br />";
                                $col_new[] = $col_n;
                                $error = true;
                            }
                            else {
                                $contacto_vo->caracteristicas[$id_col] = $ops[0];
                            }
                        }
                    }
                }

                //Espacios, columnas $col_ini en adelante
                for($e=$col_ini_espacios;$e<=$col_fin;$e++){
                    $esp = trim($l[$e]);
                    $esp_n = $esp;

                    if (!empty($esp)) {
                        if (preg_match('/^Nuev/i',$esp)) {
                            $tmp = explode($separador_nuevos,$esp);
                            $esp_n = $tmp[1];
                        }

                        $esps = $esp_dao->GetAllArrayID("nom_esp = '$esp_n'",'','');
                        if (!in_array($esp_n, $esp_new)) {
                            if (empty($esps[0])) {
                                if ($show_esp) {
                                    echo "No existe el espacio. <b>$esp</b> <br />";
                                }
                                $sql .= "INSERT INTO espacio (nom_esp,id_papa) values ('".$esp_n."',0);<br />";
                                $esp_new[] = $esp_n;
                                $error = true;
                            }
                            else {
                                if (!in_array($esps[0], $contacto_vo->id_espacio)) {
                                    $contacto_vo->id_espacio[] = $esps[0];
                                }
                            }
                        }
                    }
                }

                if ($go) {
                    $tmp = $this->GetAllArray($this->columna_nombre." = '".$contacto_vo->nombre."' AND ape_con = '$contacto_vo->apellido'");
                    if (count($tmp) > 0){
                        if ($show_update) {
                            echo "<br />- El contacto # $i: $nombre $apellido existe en el sistema, por tanto se actualizaran los datos ";
                        }

                        $contacto_vo->id = $tmp[0]->id;
                        //var_dump($contacto_vo);
                        $this->Actualizar($contacto_vo,0);
                        $act++;
                    }
                    else{
                        //var_dump($contacto_vo);
                        $this->Insertar($contacto_vo,0);
                        $ins++;
                    }
                }

            }
        }

        //$_SESSION['import_txt'] = ob_get_clean();
        //
        echo $sql;

        echo "Se crearon $ins <br />";
        echo "Se actualizarion $act <br />";

        $alert = ($error) ? "Existen errores en la importaci�n" : "Se procesarion $ins Contactos con exito!";

        ?>
        <script type="text/javascript">
            //alert('<?php echo $alert; ?>');
            //location.href = '?m_e=contacto&accion=importar';
        </script>
        <?php
    }

    /**
	 * Consulta ciudades con contactos
	 * @access public
     *
     * @return array $ciudades
	 */
    function getCiudadesDropdown() {

        $ciudades = array();

        $sql = "SELECT id_depto, nom_depto, GROUP_CONCAT(DISTINCT CONCAT_WS('|', c.id_mun, nom_mun) ORDER BY nom_mun)
            FROM contacto c INNER JOIN municipio USING(id_mun) INNER JOIN departamento USING(id_depto) GROUP BY id_depto ORDER BY nom_depto, nom_mun";

        $rs = $this->conn->OpenRecordset($sql);

        while($row = $this->conn->FetchRow($rs)) {
            $ciudades[$row[0].'-'.$row[1]] = $row[2];
        }

        return $ciudades;
    }

	/**
	 * Consulta espacios a los que pertenece un contacto
	 * @access public
	 *
	 * @param int $id
	 * @return array $espacios
	 */
	function getContactoEspacios($id) {
		$ids = array();
		$noms = array();

		$sql = "SELECT ID_ESP, NOM_ESP FROM espacio e JOIN contacto_esp c USING(ID_ESP) WHERE c.ID_CON = $id";
		$rs = $this->conn->OpenRecordset($sql);

        while($row = $this->conn->FetchRow($rs)) {
            $ids[] = $row[0];
            $noms[] = $row[1];
        }

		return compact('ids','noms');
	}
}

?>
