<?
/**
 * DAO de Contacto
 *
 * Contiene los métodos de la clase Contacto específico para Dato Sectorial
 * @author Ruben A. Rojas C.
 */

Class ContactoDatoSectorDAO {

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
	 * Número de Registros en Pantalla para ListarTAbla
	 * @var string
	 */
	var $num_reg_pag;

	/**
	 * URL para redireccionar después de Insertar, Actualizar o Borrar
	 * @var string
	 */
	var $url;

	/**
	 * Constructor
	 * Crea la conexión a la base de datos
	 * @access public
	 */	
	function __construct(){
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
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$contacto_vo = New Contacto();

		//Carga el VO
		$contacto_vo = $this->GetFromResult($contacto_vo,$row_rs);

		//Retorna el VO
		return $contacto_vo;
	}

	/**
	 * Consulta los datos de los Contacto que cumplen una condición
	 * @access public
	 * @param string $condicion Condición que deben cumplir los Contacto y que se agrega en el SQL statement.
	 * @return array Arreglo de VOs
	 */	
	function GetAllArray($condicion){
		
		$sql = "SELECT DISTINCT c.* FROM $this->tabla c LEFT JOIN ".$this->tabla."_org USING ($this->columna_id) LEFT JOIN ".$this->tabla."_esp USING (id_con) LEFT JOIN espacio_tipo_usuario USING (id_esp)";
		
		if ($condicion != ""){
			$sql .= " WHERE ".$condicion;
		}
		$sql .= " ORDER BY ".$this->columna_order;

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
	 * Lista los Contacto que cumplen la condición en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarán los Contacto, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del Contacto que será selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condición que deben cumplir los Contacto y que se agrega en el SQL statement.
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
			$condicion = "id_tipo_usuario = ".$_SESSION["id_tipo_usuario_s"];
			$espacios_x_tipo = $espacio_usuario_dao->GetAllArray($condicion);

			$cond_c = " AND id_esp IN (".implode(",",$espacios_x_tipo->id_espacio).")";
			
			$sql = "SELECT DISTINCT UPPER(LEFT(nom_con,1)) FROM $this->tabla c LEFT JOIN ".$this->tabla."_org o_c USING ($this->columna_id) 
					LEFT JOIN contacto_esp USING ($this->columna_id)
					";
			
			if ($cond != ''){
				$sql .= " WHERE $cond";
			}

			$sql .= $cond_c;

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

		//Filtros contactos
		$url = "index.php?m_e=contacto&accion=listar&class=ContactoDAO&method=ListarTabla&param=";

		if (isset($_GET["id_org"])){
			$url .= "&id_org=".$_GET["id_org"];
		}

		if (isset($_GET["nom"])){
			$url .= "&nom=".$_GET["nom"];
		}

		echo "<img src='../images/indice.png'>&nbsp;Indice:&nbsp;";

		//Todos
		$class = ($letra_inicial == '') ? 'a_big' : 'a_normal' ;
		echo "<a href='$url&li=' class='$class'>Todos</a>&nbsp;&nbsp;";

		foreach ($letras_ini as $letra){
			$class = (strtolower($letra) == strtolower($letra_inicial)) ? 'a_big' : 'a_normal' ;

			echo "<a href='$url&li=$letra' class='$class'>".$letra."</a>";
			echo "&nbsp;&nbsp;";
		}
	}

	/**
	 * Lista los Contacto en una Tabla
	 * @access public
	 */			
	function ListarTabla($cond){

		$id_org = 0;
		$num_cols = 9;
		
		$cond_c = " id_esp = 37";
		
		$arr = $this->GetAllArray($cond_c);
		$num_arr = count($arr);

		echo "<table align='center' class='tabla_lista' width='90%'>";
		
		echo "<tr>
			<td width='70'><img src='images/home/insertar.png'>&nbsp;<a href='#' onclick=\"addWindowIU('contacto_d_s','insertar','');return false;\">Crear</a></td>
			<td colspan='$num_cols' align='right'>[$num_arr Registros]</td>
		</tr>
		<tr class='titulo_lista'>
			<td>ID</td>
			<td>Nombre</td>
			<td>Tel&eacute;fono</td>
			<td>Celular</td>
			<td>Fax</td>
			<td>Email</td>
		</tr>";

		foreach($arr as $p=>$contacto){

			echo "<tr class='fila_lista'><td>";
			if (!$this->checkForeignKeys($contacto->id))
				echo "<a href='#'  onclick=\"if(confirm('Está seguro que desea borrar la Fuente: ".$contacto->nombre."')){borrarRegistro('ContactoDatoSectorDAO','".$contacto->id."')}else{return false};\"><img src='images/trash.png' border='0' title='Borrar' /></a>&nbsp;";
			
			echo $contacto->id;
			echo "<td><a href='#' onclick=\"addWindowIU('contacto_d_s','actualizar','".$contacto->id."');return false;\">".$contacto->nombre."</a>";
			//echo "<td>".$contacto->apellido."</td>";
			echo "<td>".$contacto->tel."</td>";
			echo "<td>".$contacto->cel."</td>";
			echo "<td>".$contacto->fax."</td>";
			echo "<td>".$contacto->email."</td>";
			echo "</tr>";

		}

		echo "</table>";

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
	function Insertar($contacto_vo){
		
		//CONSULTA SI YA EXISTE
		//$cat_a = $this->GetAllArray("(".$this->columna_nombre." = '".$contacto_vo->nombre."' AND ape_con = '$contacto_vo->apellido') OR email_con = '$contacto_vo->email'");
		$cat_a = $this->GetAllArray($this->columna_nombre." = '".$contacto_vo->nombre."' AND ape_con = '$contacto_vo->apellido'");
		
		if (count($cat_a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.",ape_con,tel_con,cel_con,fax_con,email_con) VALUES ('".$contacto_vo->nombre."','$contacto_vo->apellido','".$contacto_vo->tel."','".$contacto_vo->cel."','".$contacto_vo->fax."','".$contacto_vo->email."')";
			$this->conn->Execute($sql);
			
			$contacto_vo->id = $this->conn->GetGeneratedID();

			$this->insertarTablasUnion($contacto_vo);

			?>
			
			<script>
				alert("Registro insertado con éxito!");
				//location.href = '<?=$this->url;?>';
			</script>
			<?
		}
		else{
			//echo "Repetido el man: $contacto_vo->nombre $contacto_vo->apellido<br>";
			?>
			<script>
				alert("Error - Existe un Contacto con el mismo nombre o con el mismo email");
			</script>
			<?
		}
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
		
	}

	/**
	 * Actualiza un Contacto en la B.D.
	 * @access public
	 * @param object $contacto_vo VO de Contacto que se va a actualizar
	 */		
	function Actualizar($contacto_vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_nombre." = '".$contacto_vo->nombre."',";
		$sql .= "ape_con = '".$contacto_vo->apellido."',";
		$sql .= "tel_con = '".$contacto_vo->tel."',";
		$sql .= "cel_con = '".$contacto_vo->cel."',";
		$sql .= "fax_con = '".$contacto_vo->fax."',";
		$sql .= "email_con = '".$contacto_vo->email."'";

		$sql .= " WHERE ".$this->columna_id." = ".$contacto_vo->id;

		$this->conn->Execute($sql);
		$this->borrarTablasUnion($contacto_vo->id);
		$this->insertarTablasUnion($contacto_vo);

		?>
		<script>
			alert("Registro actualizado con éxito!");
			location.href = '<?=$this->url;?>';
		</script>
		<?
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
			alert("Registro eliminado con éxito!");
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
		
	}

	/**
	 * Check de llaves foraneas, para permitir acciones como borrar
	 * @access public
	 * @param int $id ID del registro a consultar
	 */	
	function checkForeignKeys($id){

		$tabla_rel = 'dato_sector';
		$col_id = 'id_conp';

		$sql = "SELECT sum($col_id) FROM $tabla_rel WHERE $col_id = $id";
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$r = ($row[0] > 0) ? true : false;

		return $r;

	}
}

?>
