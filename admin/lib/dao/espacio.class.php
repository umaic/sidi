<?
/**
 * DAO de Espacio
 *
 * Contiene los métodos de la clase Espacio 
 * @author Ruben A. Rojas C.
 */

Class EspacioDAO {

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
	function EspacioDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "espacio";
		$this->columna_id = "ID_ESP";
		$this->columna_nombre = "NOM_ESP";
		$this->columna_order = "NOM_ESP";
	}

	/**
	 * Consulta los datos de una Espacio
	 * @access public
	 * @param int $id ID del Espacio
	 * @return VO
	 */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$espacio_vo = New Espacio();

		//Carga el VO
		$espacio_vo = $this->GetFromResult($espacio_vo,$row_rs);

		//Retorna el VO
		return $espacio_vo;
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
			$vo = New Espacio();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}  
		//Retorna el Arreglo de VO
		return $array;
	}
    
    /**
     * Consulta los ID de las espacios que cumplen una condición
     * @access public
     * @param string $condicion Condicion que deben cumplir los Organizacion y que se agrega en el SQL statement.
     * @return array Arreglo
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

	/**
	 * Lista los Espacios que cumplen la condición en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarán los Tema, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del Tema que será selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condición que deben cumplir los Tema y que se agrega en el SQL statement.
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

				$this->ListarComboHijos($valor_combo,$vo->id,"");
			}			
		}
	}

	/**
	 * Lista los Items que cumplen la condición en un ComboSelect
	 * @access public
	 * @param int $valor_combo ID del CatProducto que será selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condición que deben cumplir los CatProducto y que se agrega en el SQL statement.
	 */			
	function ListarComboHijos($valor_combo,$id_papa,$tab){

		$tab .= "&nbsp;&nbsp;&nbsp;&nbsp;";
		$v_c_a = is_array($valor_combo);

		$arr = $this->GetAllArray("id_papa=$id_papa","","");

		foreach ($arr as $vo){	

			$nombre = $vo->nombre;

			if ($valor_combo == "" && $valor_combo != 0){
				echo "<option value=".$vo->id.">".$tab.$nombre."</option>";
			}
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

				echo ">".$tab.$nombre."</option>";
			}

			$this->ListarComboHijos($valor_combo,$vo->id,$tab);
		}
	}

	/**
	 * Lista los Espacio en una Tabla
	 * @access public
	 */			
	function ListarTabla($condicion){

		$arr = $this->GetAllArray('id_papa=0');
		$num_arr = count($arr);

		echo "<table align='center' class='tabla_lista'>";

		echo "<tr>
			<td width='100'><img src='images/home/insertar.png'>&nbsp;<a href='#' onclick=\"addWindowIU('espacio','insertar','');return false;\">Crear</a></td>
			<td colspan='2' align='right'>[$num_arr Registros]</td>
		</tr>";
		
		echo "<tr class='titulo_lista'>
			<td width='50' align='center'>ID</td>
			<td width='500'>Nombre</td>
			</tr>";


		foreach ($arr as $vo){

			echo "<tr class='fila_lista'>";
			echo "<td>";
			if (!$this->checkForeignKeys($vo->id))	echo "<a href='#'  onclick=\"if(confirm('Está seguro que desea borrar el espacio: ".$vo->nombre."')){borrarRegistro('EspacioDAO','".$vo->id."')}else{return false};\"><img src='images/trash.png' border='0' title='Borrar' /></a>&nbsp;";
			echo "$vo->id</td>";
			echo "<td><a href='#' onclick=\"addWindowIU('espacio','actualizar','".$vo->id."');return false;\">".$vo->nombre."</a></td>";
			echo "</tr>";

			$this->ListarTablaHijos($vo->id,"");
		}

		echo "</table>";
	}

	/**
	 * Lista las items en una Tabla
	 * @access public
	 */			
	function ListarTablaHijos($id_papa,$tab){

		$tab .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		$arr = $this->GetAllArray("id_papa=$id_papa");
		$num_arr = count($arr);

		foreach($arr as $vo){
			echo "<tr class='fila_lista'>";
			echo "<td>";
			if (!$this->checkForeignKeys($vo->id))	echo "<a href='#'  onclick=\"if(confirm('Está seguro que desea borrar el espacio: ".$vo->nombre."')){borrarRegistro('EspacioDAO','".$vo->id."')}else{return false};\"><img src='images/trash.png' border='0' title='Borrar' /></a>&nbsp;";
			echo "$vo->id</td>";
			echo "<td>$tab<a href='#' onclick=\"addWindowIU('espacio','actualizar','".$vo->id."');return false;\">".$vo->nombre."</a></td>";
			echo "</tr>";

			$this->ListarTablaHijos($vo->id,$tab);

		}
	}

	/**
	 * Carga un VO de Espacio con los datos de la consulta
	 * @access public
	 * @param object $vo VO de Espacio que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de Espacio con los datos
	 */			
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->ID_ESP;
		$vo->id_papa = $Result->ID_PAPA;
		$vo->nombre = $Result->NOM_ESP;

		return $vo;
	}

	/**
	 * Inserta un Espacio en la B.D.
	 * @access public
	 * @param object $espacio_vo VO de Espacio que se va a insertar
	 */		
	function Insertar($espacio_vo){
		//CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray($this->columna_nombre." = '".$espacio_vo->nombre."'");
		if (count($cat_a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.",id_papa) VALUES ('".$espacio_vo->nombre."',$espacio_vo->id_papa)";
			$this->conn->Execute($sql);

			echo "Registro insertado con éxito!";
		}
		else{
			echo "Error - Existe un registro con el mismo nombre";
		}

	}

	/**
	 * Actualiza un Espacio en la B.D.
	 * @access public
	 * @param object $espacio_vo VO de Espacio que se va a actualizar
	 */		
	function Actualizar($espacio_vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_nombre." = '".$espacio_vo->nombre."',";
		$sql .= "id_papa = ".$espacio_vo->id_papa;

		$sql .= " WHERE ".$this->columna_id." = ".$espacio_vo->id;

		$this->conn->Execute($sql);

	}

	/**
	 * Borra un Espacio en la B.D.
	 * @access public
	 * @param int $id ID del Espacio que se va a borrar de la B.D
	 */	
	function Borrar($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla."_tipo_usuario WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);
        
        $sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

	}

	/**
	 * Check de llaves foraneas, para permitir acciones como borrar
	 * @access public
	 * @param int $id ID del registro a consultar
	 */	
	function checkForeignKeys($id){

		$tabla_rel = 'contacto_esp';

		$sql = "SELECT sum($this->columna_id) FROM $tabla_rel JOIN espacio USING($this->columna_id) WHERE ".$this->columna_id." = $id OR $this->columna_id IN (SELECT $this->columna_id FROM espacio WHERE id_papa = $id)";
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$r = ($row[0] > 0) ? true : false;

		return $r;

	}
}

?>
