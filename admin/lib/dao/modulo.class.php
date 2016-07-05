<?
/**
 * DAO de Modulo
 *
 * Contiene los métodos de la clase Modulo 
 * @author Ruben A. Rojas C.
 */

Class ModuloDAO {

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
	function ModuloDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "modulo";
		$this->columna_id = "ID_MODULO";
		$this->columna_nombre = "NOM_MODULO";
		$this->columna_order = "NOM_MODULO";
	}

	/**
	 * Consulta los datos de una Modulo
	 * @access public
	 * @param int $id ID del Modulo
	 * @return VO
	 */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$modulo_vo = New Modulo();

		//Carga el VO
		$modulo_vo = $this->GetFromResult($modulo_vo,$row_rs);

		//Retorna el VO
		return $modulo_vo;
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
			$vo = New Modulo();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}  
		//Retorna el Arreglo de VO
		return $array;
	}

	/**
	 * Lista los Modulo que cumplen la condición en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarán los Modulo, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del Modulo que será selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condición que deben cumplir los Modulo y que se agrega en el SQL statement.
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
	 * Lista los Modulo en una Tabla
	 * @access public
	 */			
	function ListarTabla($condicion){

		$arr = $this->GetAllArray('ID_PAPA = 0');
		$num_arr = count($this->GetAllArray(''));

		echo "<table align='center' class='tabla_lista'><tr>
			<td width='100'><img src='images/home/insertar.png'>&nbsp;<a href='#' onclick=\"addWindowIU('modulo','insertar','');\">Crear</a></td>
			<td colspan='2' align='right'>[$num_arr Registros]</td>
			</tr>";

		echo "<tr class='titulo_lista'>
			<td width='50' align='center'>ID</td>
			<td width='300'>Nombre</td>
			</tr>";

		foreach ($arr as $vo){

			echo "<tr>";
			echo "<td colspan='2' align='center'><a href='#' onclick=\"addWindowIU('modulo','actualizar',$vo->id);\">".strtoupper($vo->nombre)."</a></td>";
			echo "</tr>";

			//HIJOS
			$arr_h = $this->GetAllArray('ID_PAPA = '.$vo->id);
			
			foreach ($arr_h as $vo_h){
				echo "<tr class='fila_lista'>";
				echo "<td align='center'><a href='#' onclick=\"if(confirm('Está seguro que desea borrar el modulo: ".$vo_h->nombre."')){borrarRegistro('moduloDAO','".$vo_h->id."')}else{return false};\"><img src='images/trash.png' border='0' title='Borrar' /></a>&nbsp;".$vo_h->id."</td>";
				echo "<td>&nbsp;&nbsp;&nbsp;- <a href='#' onclick=\"addWindowIU('modulo','actualizar',$vo_h->id);\">".$vo_h->nombre."</a></td>";
				echo "</tr>";
			}
		}

		echo "</table>";
	}

	/**
	 * Carga un VO de Modulo con los datos de la consulta
	 * @access public
	 * @param object $vo VO de Modulo que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de Modulo con los datos
	 */			
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};
		$vo->id_papa = $Result->ID_PAPA;

		return $vo;
	}

	/**
	 * Inserta un Modulo en la B.D.
	 * @access public
	 * @param object $modulo_vo VO de Modulo que se va a insertar
	 */		
	function Insertar($modulo_vo){
		//CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray($this->columna_nombre." = '".$modulo_vo->nombre."' AND ID_PAPA = ".$modulo_vo->id_papa);
		if (count($cat_a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.",ID_PAPA) VALUES ('".$modulo_vo->nombre."',".$modulo_vo->id_papa.")";
			$this->conn->Execute($sql);

			echo "Registro insertado con &eacute;xito!";
		}
		else{
			echo "Error - Existe un registro con el mismo nombre";
		}

	}

	/**
	 * Actualiza un Modulo en la B.D.
	 * @access public
	 * @param object $modulo_vo VO de Modulo que se va a actualizar
	 */		
	function Actualizar($modulo_vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_nombre." = '".$modulo_vo->nombre."',";
		$sql .= " ID_PAPA = '".$modulo_vo->id_papa."'";
		$sql .= " WHERE ".$this->columna_id." = ".$modulo_vo->id;

		$this->conn->Execute($sql);

	}

	/**
	 * Borra un Modulo en la B.D.
	 * @access public
	 * @param int $id ID del Modulo que se va a borrar de la B.D
	 */	
	function Borrar($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

	}
}

?>
