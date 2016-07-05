<?
/**
 * DAO de ParqueNat
 *
 * Contiene los métodos de la clase ParqueNat 
 * @author Ruben A. Rojas C.
 */

Class ParqueNatDAO {

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
	function ParqueNatDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "parque_natural";
		$this->columna_id = "ID_PAR_NAT";
		$this->columna_nombre = "NOM_PAR_NAT";
		$this->columna_order = "NOM_PAR_NAT";
	}

	/**
  * Consulta los datos de una ParqueNat
  * @access public
  * @param int $id ID del ParqueNat
  * @return VO
  */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$depto_vo = New ParqueNat();

		//Carga el VO
		$depto_vo = $this->GetFromResult($depto_vo,$row_rs);

		//Retorna el VO
		return $depto_vo;
	}

	/**
  * Consulta los datos de los ParqueNat que cumplen una condición
  * @access public
  * @param string $condicion Condición que deben cumplir los ParqueNat y que se agrega en el SQL statement.
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
			$vo = New ParqueNat();
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
  * Lista los ParqueNat que cumplen la condición en el formato dado
  * @access public
  * @param string $formato Formato en el que se listarán los ParqueNat, puede ser Tabla o ComboSelect
	* @param int $valor_combo ID del ParqueNat que será selccionado cuando el formato es ComboSelect
  * @param string $condicion Condición que deben cumplir los ParqueNat y que se agrega en el SQL statement.
  */			
	function ListarCombo($formato,$valor_combo,$condicion){
		$arr = $this->GetAllArray($condicion);
		$num_arr = count($arr);

		for($a=0;$a<$num_arr;$a++){
			$this->Imprimir($arr[$a],$formato,$valor_combo);
		}
	}

	/**
  * Imprime en pantalla los datos del ParqueNat
  * @access public
  * @param object $vo ParqueNat que se va a imprimir
  * @param string $formato Formato en el que se listarán los ParqueNat, puede ser Tabla o ComboSelect
	* @param int $valor_combo ID del ParqueNat que será selccionado cuando el formato es ComboSelect
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
  * Carga un VO de ParqueNat con los datos de la consulta
  * @access public
  * @param object $vo VO de ParqueNat que se va a recibir los datos
  * @param object $Resultset Resource de la consulta
	* @return object $vo VO de ParqueNat con los datos
  */			
	function GetFromResult ($vo,$Result){
		
		

		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};

		//CONSULTA LOS MUNICIPIOS
		$id_muns = Array();
		$nom_muns = Array();
		$id_deptos = Array();

		$sql = "SELECT par_nat_mun.ID_MUN, NOM_MUN, ID_DEPTO FROM par_nat_mun INNER JOIN municipio ON par_nat_mun.ID_MUN = municipio.ID_MUN WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchRow($rs)){
			array_push($id_muns,$row_rs[0]);
			array_push($nom_muns,$row_rs[1]);

			if (!in_array($row_rs[2],$id_deptos)){
				array_push($id_deptos,$row_rs[2]);
			}
		}

		$vo->id_muns = $id_muns;
		$vo->nom_muns = $nom_muns;


		return $vo;
	}

	/**
  * Inserta un ParqueNat en la B.D.
  * @access public
  * @param object $depto_vo VO de ParqueNat que se va a insertar
  */		
	function Insertar($parque_nat_vo){

		//CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray("NOM_REG = '".$parque_nat_vo->nombre."'");
		if (count($cat_a) == 0){
			//INSERTA EL NOMBRE DE LA REGION
			$sql =  "INSERT INTO parque_nat (NOM_REG) VALUES ('".$parque_nat_vo->nombre."')";
			$this->conn->Execute($sql);

			$id_reg = $this->conn->GetGeneratedID();

			//INSERTA LOS MUNICIPIOS QUE FORMAN LA REGION
			$num_mun = count($parque_nat_vo->id_muns);
			for($m=0;$m<$num_mun;$m++){
				$sql = "INSERT INTO mun_reg (ID_MUN,ID_REG) VALUES (".$parque_nat_vo->id_muns[$m].",".$id_reg.")";
				$this->conn->Execute($sql);
			}

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
    	alert("La región ya existe.!!!");
    	location.href='insert.php';
    	</script>
    	<?
		}
	}

	/**
  * Actualiza un ParqueNat en la B.D.
  * @access public
  * @param object $depto_vo VO de ParqueNat que se va a actualizar
  */		
	function Actualizar($depto_vo){
		$sql =  "UPDATE depto SET";
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
  * Borra un ParqueNat en la B.D.
  * @access public
  * @param int $id ID del ParqueNat que se va a borrar de la B.D
  */	
	function Borrar($id){

		//BORRA MUNICPIOS DE LA REGION
		$sql = "DELETE FROM mun_reg WHERE ID_REG = ".$id;
		$this->conn->Execute($sql);

		//BORRA REGION
		$sql = "DELETE FROM ".$this->tabla." WHERE ID_REG = ".$id;
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