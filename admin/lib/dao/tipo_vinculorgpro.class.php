<?
/**
 * DAO de TipoVinculorgpro
 *
 * Contiene los métodos de la clase TipoVinculorgpro 
 * @author Ruben A. Rojas C.
 */

Class TipoVinculorgproDAO {

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
	function TipoVinculorgproDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "tipo_vinculorgpro";
		$this->columna_id = "ID_TIPO_VINORGPRO";
		$this->columna_nombre = "NOM_TIPO_VINORGPRO";
		$this->columna_order = "NOM_TIPO_VINORGPRO";
	}

	/**
  * Consulta los datos de una TipoVinculorgpro
  * @access public
  * @param int $id ID del TipoVinculorgpro
  * @return VO
  */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$depto_vo = New TipoVinculorgpro();

		//Carga el VO
		$depto_vo = $this->GetFromResult($depto_vo,$row_rs);

		//Retorna el VO
		return $depto_vo;
	}

	/**
  * Consulta los datos de los TipoVinculorgpro que cumplen una condición
  * @access public
  * @param string $condicion Condición que deben cumplir los TipoVinculorgpro y que se agrega en el SQL statement.
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
			$vo = New TipoVinculorgpro();
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
  * Lista los TipoVinculorgpro que cumplen la condición en el formato dado
  * @access public
  * @param string $formato Formato en el que se listarán los TipoVinculorgpro, puede ser Tabla o ComboSelect
	* @param int $valor_combo ID del TipoVinculorgpro que será selccionado cuando el formato es ComboSelect
  * @param string $condicion Condición que deben cumplir los TipoVinculorgpro y que se agrega en el SQL statement.
  */			
	function ListarCombo($formato,$valor_combo,$condicion){
		$arr = $this->GetAllArray($condicion);
		$num_arr = count($arr);

		for($a=0;$a<$num_arr;$a++){
			$this->Imprimir($arr[$a],$formato,$valor_combo);
		}
	}

	/**
  * Imprime en pantalla los datos del TipoVinculorgpro
  * @access public
  * @param object $vo TipoVinculorgpro que se va a imprimir
  * @param string $formato Formato en el que se listarán los TipoVinculorgpro, puede ser Tabla o ComboSelect
	* @param int $valor_combo ID del TipoVinculorgpro que será selccionado cuando el formato es ComboSelect
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
  * Carga un VO de TipoVinculorgpro con los datos de la consulta
  * @access public
  * @param object $vo VO de TipoVinculorgpro que se va a recibir los datos
  * @param object $Resultset Resource de la consulta
	* @return object $vo VO de TipoVinculorgpro con los datos
  */			
	function GetFromResult ($vo,$Result){
		
		

		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};

		return $vo;
	}

	/**
  * Inserta un TipoVinculorgpro en la B.D.
  * @access public
  * @param object $depto_vo VO de TipoVinculorgpro que se va a insertar
  */		
	function Insertar($depto_vo){
		//CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray("NOM_MUN = '".$depto_vo->NOM_MUN."' AND id_product = ".$depto_vo->id_prod." AND id_product_size = ".$depto_vo->id_prod_size);
		if (count($cat_a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (NOM_MUN,id_product,id_product_size,description,image,estado)";
			$sql .= " VALUES ('".$depto_vo->NOM_MUN."',".$depto_vo->id_prod.",".$depto_vo->id_prod_size.",'".$depto_vo->desc."','".$depto_vo->image."',".$depto_vo->estado.")";

			$this->conn->Execute($sql);

    	?>
    	<script>
    	alert("Done!");
    	location.href='index.php?accion=listar';
    	</script>
    	<?
		}
		else{
    	?>
    	<script>
    	alert("A Product Color with the same NOM_MUN exists.!!!");
    	location.href='index.php?accion=listar';
    	</script>
    	<?
		}
	}

	/**
  * Actualiza un TipoVinculorgpro en la B.D.
  * @access public
  * @param object $depto_vo VO de TipoVinculorgpro que se va a actualizar
  */		
	function Actualizar($depto_vo){
		$sql =  "UPDATE ".$this->tabla." SET";
		$sql .= " id_product = ".$depto_vo->id_prod.",";
		$sql .= " id_product_size = ".$depto_vo->id_prod_size.",";
		$sql .= " NOM_MUN = '".$depto_vo->NOM_MUN."',";
		$sql .= " estado = ".$depto_vo->estado.",";
		$sql .= " description = '".$depto_vo->desc."',";
		$sql .= " image = '".$depto_vo->image."'";
		$sql .= " WHERE ID_MUN = ".$depto_vo->id;

		$this->conn->Execute($sql);

		?>
  	<script>
  	alert("Done!");
  	location.href='index.php';
  	</script>
  	<?
	}

	/**
  * Borra un TipoVinculorgpro en la B.D.
  * @access public
  * @param int $id ID del TipoVinculorgpro que se va a borrar de la B.D
  */	
	function Borrar($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla." WHERE ID_MUN = ".$id;
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