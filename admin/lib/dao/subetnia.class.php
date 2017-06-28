<?
/**
 * DAO de SubEtnia
 *
 * Contiene los métodos de la clase SubEtnia 
 * @author Ruben A. Rojas C.
 */

Class SubEtniaDAO {

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
	 * Constructor
	 * Crea la conexión a la base de datos
	 * @access public
	 */	
	function SubEtniaDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "sub_etnia";
		$this->columna_id = "ID_SUBETNIA";
		$this->columna_nombre = "DESC_SUBETNIA";
		$this->columna_order = "DESC_SUBETNIA";
	}

	/**
	 * Consulta los datos de una SubEtnia
	 * @access public
	 * @param int $id ID del SubEtnia
	 * @return VO
	 */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$subetnia_vo = New SubEtnia();

		//Carga el VO
		$subetnia_vo = $this->GetFromResult($subetnia_vo,$row_rs);

		//Retorna el VO
		return $subetnia_vo;
	}

	/**
	 * Consulta Vos
	 * @access public
	 * @param string $condicion Condición que deben cumplir los Tema y que se agrega en el SQL statement.
	 * @param string $limit Limit en el SQL
	 * @param string $order by Order by en el SQL 
	 * @return array Arreglo de VOs
	 */	
	function GetAllArray($condicion,$limit='',$order_by=''){
		
		$sql = "SELECT * FROM ".$this->tabla;
		
		if ($condicion != "") $sql .= " WHERE ".$condicion;

		//ORDER
		$sql .= ($order_by != "") ?  " ORDER BY $order_by" : " ORDER BY ".$this->columna_order;

		//LIMIT
		if ($limit != "") $sql .= " LIMIT ".$limit;

		$array = Array();

		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchObject($rs)){
			//Crea un VO
			$vo = New SubEtnia();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}  
		//Retorna el Arreglo de VO
		return $array;
	}

	/**
	* Consulta los datos de los Depto que cumplen una condición
	* @access public
	* @param string $condicion Condición que deben cumplir los Depto y que se agrega en el SQL statement.
	* @return array Arreglo de ID
	*/
	function GetAllArrayID($condicion){
		
		$sql = "SELECT ".$this->columna_id." FROM ".$this->tabla."";
	
		if ($condicion != "") $sql .= " WHERE ".$condicion;
		
		$sql .= " ORDER BY ".$this->columna_order;

		$array = Array();

		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchRow($rs)){
			//Carga el arreglo
			$array[] = $row_rs[0];
		}

		//Retorna el Arreglo
		return $array;
	}


	/**
	 * Lista los SubEtnia que cumplen la condición en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarán los SubEtnia, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del SubEtnia que será selccionado cuando el formato es ComboSelect
	 * @param string $etnia Condición que deben cumplir los SubEtnia y que se agrega en el SQL statement.
	 */			
	function ListarCombo($formato,$valor_combo,$etnia){
		$arr = $this->GetAllArray($etnia);
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
	 * Lista los SubEtnia en una Tabla
	 * @access public
	 */			
	function ListarTabla($etnia){

		include_once ("lib/common/layout.class.php");

		$layout = new Layout();

		$layout->adminGrid(array('nombre' => array('titulo' => 'Nombre')),
						   array('id_etnia' => array('dao' => 'EtniaDAO', 'nom' => 'nombre', 'titulo' => 'Etnia', 'filtro' => true)));

	}

	/**
	 * Carga un VO de SubEtnia con los datos de la consulta
	 * @access public
	 * @param object $vo VO de SubEtnia que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de SubEtnia con los datos
	 */			
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};
		$vo->id_etnia = $Result->ID_ETNIA;

		return $vo;
	}

	/**
	 * Inserta un SubEtnia en la B.D.
	 * @access public
	 * @param object $subetnia_vo VO de SubEtnia que se va a insertar
	 */		
	function Insertar($subetnia_vo){
		//CONSULTA SI YA EXISTE
		$vo_t = $this->GetAllArray($this->columna_nombre." = '".$subetnia_vo->nombre."'");
		if (count($vo_t) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.",ID_ETNIA) VALUES ('".$subetnia_vo->nombre."',$subetnia_vo->id_etnia)";
			$this->conn->Execute($sql);

			// Lo crea en MONITOR
            $sql_monitor = "INSERT INTO victim_sub_ethnic_group (victim_ethnic_group_id, sub_ethnic_group) VALUES ($subetnia_vo->id_etnia, '".$subetnia_vo->nombre."')";
            $my_monitor = mysqli_connect('192.168.1.3','sissh','mjuiokm2017','violencia_armada');
            mysqli_real_query($my_monitor,$sql_monitor);

			echo "Registro insertado con &eacute;xito!";
		}
		else{
			echo "Error - Existe un registro con el mismo nombre";
		}

	}

	/**
	 * Actualiza un SubEtnia en la B.D.
	 * @access public
	 * @param object $subetnia_vo VO de SubEtnia que se va a actualizar
	 */		
	function Actualizar($subetnia_vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_nombre." = '".$subetnia_vo->nombre."',";
		$sql .= " ID_ETNIA = ".$subetnia_vo->id_etnia;
		$sql .= " WHERE ".$this->columna_id." = ".$subetnia_vo->id;

		$this->conn->Execute($sql);

	}

	/**
	 * Borra un SubEtnia en la B.D.
	 * @access public
	 * @param int $id ID del SubEtnia que se va a borrar de la B.D
	 */	
	function Borrar($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

	}

	/**
	 * Retorna el numero de Registros
	 * @access public
	 * @return int
	 */
	function numRecords($etnia){
		$sql = "SELECT count(".$this->columna_id.") as num FROM ".$this->tabla;
		if ($etnia != ""){
			$sql .= " WHERE ".$etnia;
		}
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchRow($rs);

		return $row_rs[0];
	}	
	
	/**
	 * Check de llaves foraneas, para permitir acciones como borrar
	 * @access public
	 * @param int $id ID del registro a consultar
	 */	
	function checkForeignKeys($id){

		$tabla_rel = 'victima';
		$col_id = $this->columna_id;
		
		$sql = "SELECT sum($col_id) FROM $tabla_rel WHERE $col_id = $id";
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$r = ($row[0] > 0) ? true : false;

		return $r;

	}
}

/**
 * Ajax de Subcategorias
 *
 * Contiene los metodos para Ajax de la clase SubEtnia
 * @author Ruben A. Rojas C.
 */

Class SubEtniaAjax extends SubEtniaDAO {

	/**
	 * Lista ComboBox de subcategorias
	 * @access public
	 * @param string $id_etnia ID de la Estado
	 * @param int $multiple 0 = comboBox no es multiple, > 0 comboBox es multiple de size = $multiple
	 * @param int $titulo 1 = Mostrar titulo
	 * @param string $id_field Id para el combo
	 */
	function comboBoxSubetnia($id_etnia,$multiple=0,$titulo=0,$separador=0,$id_field='id_subetnia'){

		//LIBRERIAS
		include_once("lib/model/subetnia.class.php");
		include_once("lib/model/etnia.class.php");
		include_once("lib/dao/etnia.class.php");

		//INICIALIZACION VARIABLES
		$etnia_dao = New EtniaDAO();

		$num = $this->numRecords("ID_ETNIA IN ($id_etnia)");

		if ($num > 0){

			$etnias = $etnia_dao->GetAllArray("ID_ETNIA IN ($id_etnia)");


			if ($titulo == 1)	echo "<b>Subetnias</b><br>";

			if ($multiple == 0){
				echo "<select id='$id_field' name='id_subetnia[]' class='select'>";
				echo "<option value=0>[ Seleccione ]</option>";
			}
			else{
				//if ($num < $multiple)	$multiple = $num;
				echo "<select id='$id_field' name='id_subetnia[]' class='select' multiple size=$multiple>";
			}


			foreach ($etnias as $etnia) {
				$vos = $this->GetAllArray("ID_ETNIA = $etnia->id");

				if ($separador == 1)	echo "<option value='' disabled>--- ".$etnia->nombre." ---</option>";
				foreach ($vos as $vo){
					echo "<option value='".$vo->id."'>".$vo->nombre."</option>";
				}
			}

			echo "</select>";
		}
		else{
			echo "<b>* No hay Info *</b><input type='hidden' name='id_subetnia[]' value=0>";
		}
	}		
}

?>
