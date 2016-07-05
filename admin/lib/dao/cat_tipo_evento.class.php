<?
/**
 * DAO de CatTipoEvento
 *
 * Contiene los métodos de la clase CatTipoEvento 
 * @author Ruben A. Rojas C.
 */

Class CatTipoEventoDAO {

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
	function CatTipoEventoDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "cat_tipo_evento";
		$this->columna_id = "ID_CAT_TIPO_EVE";
		$this->columna_nombre = "NOM_CAT_TIPO_EVE";
		$this->columna_order = "NOM_CAT_TIPO_EVE";
	}

	/**
  * Consulta los datos de una CatTipoEvento
  * @access public
  * @param int $id ID del CatTipoEvento
  * @return VO
  */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$cat_tipo_evento_vo = New CatTipoEvento();

		//Carga el VO
		$cat_tipo_evento_vo = $this->GetFromResult($cat_tipo_evento_vo,$row_rs);

		//Retorna el VO
		return $cat_tipo_evento_vo;
	}

	/**
  * Consulta los datos de los CatTipoEvento que cumplen una condición
  * @access public
  * @param string $condicion Condición que deben cumplir los CatTipoEvento y que se agrega en el SQL statement.
  * @return array Arreglo de VOs
  */	
	function GetAllArray($condicion){
		
		$c = 0;
		$sql = "SELECT * FROM ".$this->tabla."";
		if ($condicion != ""){
			$sql .= " WHERE ".$condicion;
		}
		$sql .= " ORDER BY ".$this->columna_order;

		$array = Array();

		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchObject($rs)){
			//Crea un VO
			$vo = New CatTipoEvento();
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
  * Lista las Categorias que cumplen la condición en el formato dado
  * @access public
  * @param string $formato Formato en el que se listarán los Sector, puede ser Tabla o ComboSelect
	* @param int $valor_combo ID del Sector que será selccionado cuando el formato es ComboSelect
  * @param string $condicion Condición que deben cumplir los Sector y que se agrega en el SQL statement.
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
  * Imprime en pantalla los datos del CatTipoEvento
  * @access public
  * @param object $vo CatTipoEvento que se va a imprimir
  * @param string $formato Formato en el que se listarán los CatTipoEvento, puede ser Tabla o ComboSelect
	* @param int $valor_combo ID del CatTipoEvento que será selccionado cuando el formato es ComboSelect
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
  * Carga un VO de CatTipoEvento con los datos de la consulta
  * @access public
  * @param object $vo VO de CatTipoEvento que se va a recibir los datos
  * @param object $Resultset Resource de la consulta
	* @return object $vo VO de CatTipoEvento con los datos
  */			
	function GetFromResult ($vo,$Result){
		
		

		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};

		return $vo;
	}

	/**
  * Inserta un CatTipoEvento en la B.D.
  * @access public
  * @param object $cat_tipo_evento_vo VO de CatTipoEvento que se va a insertar
  */		
	function Insertar($cat_tipo_evento_vo){
		//CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray($this->columna_nombre." = '".$cat_tipo_evento_vo->nombre."'");
		if (count($cat_a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.") VALUES ('".$cat_tipo_evento_vo->nombre."')";

			$this->conn->Execute($sql);

    	?>
    	<script>
    	alert("Done!");
    	</script>
    	<?
		}
		else{
    	?>
    	<script>
    	alert("Error - Existe un Tipo de Evento con el mismo nombre");
    	</script>
    	<?
		}
	}

	/**
  * Actualiza un CatTipoEvento en la B.D.
  * @access public
  * @param object $cat_tipo_evento_vo VO de CatTipoEvento que se va a actualizar
  */		
	function Actualizar($cat_tipo_evento_vo){
		$sql =  "UPDATE ".$this->tabla." SET";
		$sql .= " id_product = ".$cat_tipo_evento_vo->id_prod.",";
		$sql .= " id_product_size = ".$cat_tipo_evento_vo->id_prod_size.",";
		$sql .= " NOM_MUN = '".$cat_tipo_evento_vo->NOM_MUN."',";
		$sql .= " estado = ".$cat_tipo_evento_vo->estado.",";
		$sql .= " description = '".$cat_tipo_evento_vo->desc."',";
		$sql .= " image = '".$cat_tipo_evento_vo->image."'";
		$sql .= " WHERE ID_MUN = ".$cat_tipo_evento_vo->id;

		$this->conn->Execute($sql);

		?>
  	<script>
  	alert("Done!");
  	location.href='index.php';
  	</script>
  	<?
	}

	/**
  * Borra un CatTipoEvento en la B.D.
  * @access public
  * @param int $id ID del CatTipoEvento que se va a borrar de la B.D
  */	
	function Borrar($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

    ?>
    <script>
    alert("Done!");
    location.href='index.php?accion=listar';
    </script>
    <?
	}
}

?>