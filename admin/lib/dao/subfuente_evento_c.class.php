<?
/**
 * DAO de SubFuenteEventoConflicto
 *
 * Contiene los métodos de la clase SubFuenteEventoConflicto 
 * @author Ruben A. Rojas C.
 */

Class SubFuenteEventoConflictoDAO {

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
	function SubFuenteEventoConflictoDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "subfuente_even";
		$this->columna_id = "ID_SFUEVEN";
		$this->columna_nombre = "NOM_SFUEVEN";
		$this->columna_order = "NOM_SFUEVEN";
	}

	/**
	 * Consulta los datos de una SubFuenteEventoConflicto
	 * @access public
	 * @param int $id ID del SubFuenteEventoConflicto
	 * @return VO
	 */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$subfuente_vo = New SubFuenteEventoConflicto();

		//Carga el VO
		$subfuente_vo = $this->GetFromResult($subfuente_vo,$row_rs);

		//Retorna el VO
		return $subfuente_vo;
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
			$vo = New SubFuenteEventoConflicto();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}  
		//Retorna el Arreglo de VO
		return $array;
	}

	/**
	 * Lista los SubFuenteEventoConflicto que cumplen la condición en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarán los SubFuenteEventoConflicto, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del SubFuenteEventoConflicto que será selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condición que deben cumplir los SubFuenteEventoConflicto y que se agrega en el SQL statement.
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
	 * Lista los SubFuenteEventoConflicto en una Tabla
	 * @access public
	 */			
	function ListarTabla($condicion){

		include_once ("lib/common/layout.class.php");

		$layout = new Layout();

		$layout->adminGrid(array('nombre' => array('titulo' => 'Nombre')),
						   array('id_fuente' => array('tabla_columna' => 'id_fueven', 'dao' => 'FuenteEventoConflictoDAO', 'nom' => 'nombre', 'titulo' => 'Fuente', 'filtro' => true)));


	}

	/**
	 * Carga un VO de SubFuenteEventoConflicto con los datos de la consulta
	 * @access public
	 * @param object $vo VO de SubFuenteEventoConflicto que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de SubFuenteEventoConflicto con los datos
	 */			
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};
		$vo->id_fuente = $Result->ID_FUEVEN;

		return $vo;
	}

	/**
	 * Inserta un SubFuenteEventoConflicto en la B.D.
	 * @access public
	 * @param object $subfuente_vo VO de SubFuenteEventoConflicto que se va a insertar
	 */		
	function Insertar($subfuente_vo){
		//CONSULTA SI YA EXISTE
		$subfuente_a = $this->GetAllArray($this->columna_nombre." = '".$subfuente_vo->nombre."' AND id_fueven = ".$subfuente_vo->id_fuente);
		if (count($subfuente_a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.",ID_FUEVEN) VALUES ('".$subfuente_vo->nombre."',$subfuente_vo->id_fuente)";
			$this->conn->Execute($sql);

            // Lo crea en MONITOR
            $sql_monitor = "INSERT INTO source (source_type_id, source) VALUES ($subfuente_vo->id_fuente, '".$subfuente_vo->nombre."')";
            $my_monitor = mysqli_connect('192.168.1.3','sissh','mjuiokm2017','violencia_armada');
            mysqli_real_query($my_monitor,$sql_monitor);

			echo "Registro insertado con &eacute;xito!";
		}
		else{
			echo "Error - Existe un registro con el mismo nombre";
		}

	}

	/**
	 * Actualiza un SubFuenteEventoConflicto en la B.D.
	 * @access public
	 * @param object $subfuente_vo VO de SubFuenteEventoConflicto que se va a actualizar
	 */		
	function Actualizar($subfuente_vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_nombre." = '".$subfuente_vo->nombre."',";
		$sql .= " ID_FUEVEN = ".$subfuente_vo->id_fuente;
		$sql .= " WHERE ".$this->columna_id." = ".$subfuente_vo->id;

		$this->conn->Execute($sql);

	}

	/**
	 * Borra un SubFuenteEventoConflicto en la B.D.
	 * @access public
	 * @param int $id ID del SubFuenteEventoConflicto que se va a borrar de la B.D
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
	 * Check de llaves foraneas, para permitir acciones como borrar
	 * @access public
	 * @param int $id ID del registro a consultar
	 */	
	function checkForeignKeys($id){

		$tabla_rel = 'fuen_evento';
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
 * Contiene los metodos para Ajax de la clase SubCatEventoConflicto
 * @author Ruben A. Rojas C.
 */

Class SubFuenteEventoConflictoAjax extends SubFuenteEventoConflictoDAO {

	/**
	 * Lista ComboBox de subcategorias
	 * @access public
	 * @param string $id_cat ID de la Categoria
	 * @param int $multiple 0 = comboBox no es multiple, > 0 comboBox es multiple de size = $multiple
	 * @param int $titulo 1 = Mostrar titulo
	 */
	function comboBoxSubfuente($id_fuente,$multiple=0,$titulo=0){

		//LIBRERIAS
		include_once("lib/model/subfuente_evento_c.class.php");
		include_once("lib/model/fuente_evento_c.class.php");
		include_once("lib/dao/fuente_evento_c.class.php");

		$num = $this->numRecords("ID_FUEVEN = $id_fuente");

		if ($num > 0){

			$vos = $this->GetAllArray("ID_FUEVEN = $id_fuente");

			if ($titulo == 1)	echo "<b>Fuente</b><br>";

			if ($multiple == 0){
				echo "<select id='id_subfuente' name='id_subfuente[]' class='select' style='width:200px'>";
				echo "<option value=''>[ Seleccione ]</option>";
			}
			else{
				//if ($num < $multiple)	$multiple = $num;
				echo "<select id='id_subfuente' name='id_subfuente[]' class='select' multiple size=$multiple>";
			}

			foreach ($vos as $vo){
				echo "<option value='".$vo->id."'>".$vo->nombre."</option>";
			}

			echo "</select>";
		}
		else{
			echo "<b>* No hay Info *</b><input type='hidden' id='id_subfuente' name='id_subfuente[]' value=0>";
		}
	}		
}
?>
