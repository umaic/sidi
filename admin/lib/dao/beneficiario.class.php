<?
/**
 * DAO de Beneficiario
 *
 * Contiene los métodos de la clase Beneficiario 
 * @author Ruben A. Rojas C.
 */

Class BeneficiarioDAO {

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
	function BeneficiarioDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "beneficiario";
	}

	/**
  * Consulta los datos de una Beneficiario
  * @access public
  * @param int $id ID del Beneficiario
  * @return VO
  */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$Beneficiario_vo = New Beneficiario();

		//Carga el VO
		$Beneficiario_vo = $this->GetFromResult($Beneficiario_vo,$row_rs);

		//Retorna el VO
		return $Beneficiario_vo;
	}

	/**
  * Consulta los datos de los Beneficiario que cumplen una condición
  * @access public
  * @param string $condicion Condición que deben cumplir los Beneficiario y que se agrega en el SQL statement.
  * @return array Arreglo de VOs
  */	
	function GetAllArray($condicion){
		
		$c = 0;
		$sql = "SELECT * FROM ".$this->tabla."";
		if ($condicion != ""){
			$sql .= " WHERE ".$condicion;
		}

		$array = Array();

		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchObject($rs)){
			//Crea un VO
			$vo = New Beneficiario();
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
  * Carga un VO de Beneficiario con los datos de la consulta
  * @access public
  * @param object $vo VO de Beneficiario que se va a recibir los datos
  * @param object $Resultset Resource de la consulta
	* @return object $vo VO de Beneficiario con los datos
  */			
	function GetFromResult ($vo,$Result){
		$vo->id_pobla = $Result->ID_POBLA;
		$vo->id_proy = $Result->ID_PROY;
		$vo->cant_per = $Result->CANT_PER;

		return $vo;
	}

	/**
  * Inserta un Beneficiario en la B.D.
  * @access public
  * @param object $Beneficiario_vo VO de Beneficiario que se va a insertar
  */		
	function Insertar($Beneficiario_vo){
		//CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray("NOM_MUN = '".$Beneficiario_vo->NOM_MUN."' AND id_product = ".$Beneficiario_vo->id_prod." AND id_product_size = ".$Beneficiario_vo->id_prod_size);
		if (count($cat_a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (NOM_MUN,id_product,id_product_size,description,image,estado)";
			$sql .= " VALUES ('".$Beneficiario_vo->NOM_MUN."',".$Beneficiario_vo->id_prod.",".$Beneficiario_vo->id_prod_size.",'".$Beneficiario_vo->desc."','".$Beneficiario_vo->image."',".$Beneficiario_vo->estado.")";

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
  * Actualiza un Beneficiario en la B.D.
  * @access public
  * @param object $Beneficiario_vo VO de Beneficiario que se va a actualizar
  */		
	function Actualizar($Beneficiario_vo){
		$sql =  "UPDATE ".$this->tabla." SET";
		$sql .= " id_product = ".$Beneficiario_vo->id_prod.",";
		$sql .= " id_product_size = ".$Beneficiario_vo->id_prod_size.",";
		$sql .= " NOM_MUN = '".$Beneficiario_vo->NOM_MUN."',";
		$sql .= " estado = ".$Beneficiario_vo->estado.",";
		$sql .= " description = '".$Beneficiario_vo->desc."',";
		$sql .= " image = '".$Beneficiario_vo->image."'";
		$sql .= " WHERE ID_MUN = ".$Beneficiario_vo->id;

		$this->conn->Execute($sql);

		?>
  	<script>
  	alert("Done!");
  	location.href='index.php';
  	</script>
  	<?
	}

	/**
  * Borra un Beneficiario en la B.D.
  * @access public
  * @param int $id ID del Beneficiario que se va a borrar de la B.D
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