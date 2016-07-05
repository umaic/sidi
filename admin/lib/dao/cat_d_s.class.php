<?
/**
 * DAO de CategoriaDatoSector
 *
 * Contiene los métodos de la clase CategoriaDatoSector
 * @author Ruben A. Rojas C.
 */

Class CategoriaDatoSectorDAO {

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
	function CategoriaDatoSectorDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "categoria";
		$this->columna_id = "ID_CATE";
		$this->columna_nombre = "NOM_CATE";
		$this->columna_order = "NOM_CATE";
	}

	/**
	 * Consulta los datos de una CategoriaDatoSector
	 * @access public
	 * @param int $id ID del CategoriaDatoSector
	 * @return VO
	 */
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$cat_d_s_vo = New CategoriaDatoSector();

		//Carga el VO
		$cat_d_s_vo = $this->GetFromResult($cat_d_s_vo,$row_rs);

		//Retorna el VO
		return $cat_d_s_vo;
	}

	/**
	 * Consulta los datos de los Tema que cumplen una condición
	 * @access public
	 * @param string $condicion Condición que deben cumplir los Tema y que se agrega en el SQL statement.
	 * @param string $limit Limit en el SQL
	 * @param string $order by Order by en el SQL 
	 * @return array Arreglo de VOs
	 */	
	function GetAllArray($condicion,$limit='',$order_by=''){
		
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
			$vo = New CategoriaDatoSector();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}  
		//Retorna el Arreglo de VO
		return $array;
	}

	/**
	 * Lista los CategoriaDatoSector que cumplen la condición en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarán los CategoriaDatoSector, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del CategoriaDatoSector que será selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condición que deben cumplir los CategoriaDatoSector y que se agrega en el SQL statement.
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
	 * Lista los TipoEvento en una Tabla
	 * @access public
	 */
	function ListarTabla($condicion){

		include_once ("lib/common/layout.class.php");

		$layout = new Layout();

		$layout->adminGrid(array('nombre' => array('titulo' => 'Nombre')),
						   array('id_sector' => array('tabla_columna' => 'id_comp', 'dao' => 'SectorDAO', 'nom' => 'nombre_es', 'titulo' => 'Sector', 'filtro' => true)));
	}

	/**
	 * Imprime en pantalla los datos del CategoriaDatoSector
	 * @access public
	 * @param object $vo CategoriaDatoSector que se va a imprimir
	 * @param string $formato Formato en el que se listarán los CategoriaDatoSector, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del CategoriaDatoSector que será selccionado cuando el formato es ComboSelect
	 */
	function Imprimir($vo,$formato,$valor_combo){

		$v_c_a = is_array($valor_combo);

		if ($formato == 'combo'){
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
	 * Carga un VO de CategoriaDatoSector con los datos de la consulta
	 * @access public
	 * @param object $vo VO de CategoriaDatoSector que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de CategoriaDatoSector con los datos
	 */
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};
		$vo->id_sector = $Result->ID_COMP;

		return $vo;
	}

	/**
	 * Inserta un CategoriaDatoSector en la B.D.
	 * @access public
	 * @param object $cat_d_s_vo VO de CategoriaDatoSector que se va a insertar
	 */
	function Insertar($cat_d_s_vo){
		//CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray($this->columna_nombre." = '".$cat_d_s_vo->nombre."'");
		if (count($cat_a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.",ID_COMP) VALUES ('".$cat_d_s_vo->nombre."',".$cat_d_s_vo->id_sector.")";
			$this->conn->Execute($sql);

			echo "Registro insertado con &eacute;xito!";
		}
		else{
			echo "Error - Existe un registro con el mismo nombre";
		}
	}

	/**
	 * Actualiza un CategoriaDatoSector en la B.D.
	 * @access public
	 * @param object $cat_d_s_vo VO de CategoriaDatoSector que se va a actualizar
	 */
	function Actualizar($cat_d_s_vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_nombre." = '".$cat_d_s_vo->nombre."',";
		$sql .= "ID_COMP = '".$cat_d_s_vo->id_sector."'";
		$sql .= " WHERE ".$this->columna_id." = ".$cat_d_s_vo->id;

		$this->conn->Execute($sql);

	}

	/**
	 * Borra un CategoriaDatoSector en la B.D.
	 * @access public
	 * @param int $id ID del CategoriaDatoSector que se va a borrar de la B.D
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

		$tabla_rel = 'dato_sector';

		$sql = "SELECT sum($this->columna_id) FROM $tabla_rel WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$r = ($row[0] > 0) ? true : false;

		return $r;

	}
}


/**
 * Ajax de Categorias de Datos Sectoriales
 *
 * Contiene los metodos para Ajax de la clase CategoriaDatoSector
 * @author Ruben A. Rojas C.
 */

Class CategoriaDatoSectorAjax extends CategoriaDatoSectorDAO {

	/**
	 * Lista ComboBox de categorias de Datos Sectoriales
	 * @access public
	 * @param string $condicion
	 * @param int $multiple 0 = comboBox no es multiple, > 0 comboBox es multiple de size = $multiple
	 */
	function comboBoxCategoriaDatoSectorial($condicion,$multiple){

		//LIBRERIAS
		include_once("lib/model/cat_d_s.class.php");

		$num = $this->numRecords($condicion);

		echo "<table>";

		if ($num > 0){
			echo "<td width='90'><b>Categoria</b></td><td>";
			if ($multiple == 0){
				echo "<select id='id_cat' name='id_cat' class='select'>";
				echo "<option value=''>[ Seleccione ]</option>";
			}
			else{
				if ($num < $multiple)	$multiple = $num;
				echo "<select id='id_cat' name='id_cat[]' class='select' multiple size=$multiple>";
			}

			$this->ListarCombo('combo','',$condicion);
			echo "</select></td>";
		}
		else{
			echo "<tr><td><b>*** No hay Categorias ***</b></td></tr> ";
		}

		echo "</table>";
	}

	/**
	 * Lista ComboBox de categorias de Datos Sectoriales en formulario Insert/Update
	 * @access public
	 * @param string $condicion
	 * @param int $multiple 0 = comboBox no es multiple, > 0 comboBox es multiple de size = $multiple
	 */
	function comboBoxCategoriaDatoSectorialInsertUpdate($condicion,$multiple){

		//LIBRERIAS
		include_once("lib/model/cat_d_s.class.php");

		$num = $this->numRecords($condicion);

		if ($num > 0){
			if ($multiple == 0){
				echo "<select id='id_cat' name='id_cat' class='select'>";
				echo "<option value=''>[ Seleccione ]</option>";
			}
			else{
				if ($num < $multiple)	$multiple = $num;
				echo "<select id='id_cat' name='id_cat[]' class='select' multiple size=$multiple>";
			}

			$this->ListarCombo('combo','',$condicion);
			echo "</select>";
		}
		else{
			echo "<b>*** No hay Categorias ***</b>";
		}

	}

}

?>
