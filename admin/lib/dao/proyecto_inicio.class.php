<?
/**
 * DAO de Proyecto
 *
 * Contiene los métodos de la clase Proyecto 
 * @author Ruben A. Rojas C.
 */
 
Class ProyectoDAO {

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
	function ProyectoDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "proyecto";
		$this->columna_id = "ID_PROY";
		$this->columna_nombre = "NOM_PROY";
		$this->columna_order = "NOM_PROY";
	}
	
  /**
  * Consulta los datos de una Proyecto
  * @access public
  * @param int $id ID del Proyecto
  * @return VO
  */	
	function Get($id){
  	$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
  	$rs = $this->conn->OpenRecordset($sql);
  	$row_rs = $this->conn->FetchObject($rs);
		
  	//Crea un VO
  	$depto_vo = New Proyecto();
  
  	//Carga el VO
  	$depto_vo = $this->GetFromResult($depto_vo,$row_rs);
  
  	//Retorna el VO
  	return $depto_vo;
	}

  /**
  * Consulta los datos de los Proyecto que cumplen una condición
  * @access public
  * @param string $condicion Condición que deben cumplir los Proyecto y que se agrega en el SQL statement.
  * @return array Arreglo de VOs
  */	
	function GetAllArray($condicion){
  	//Crea un VO
  	$vo = New Proyecto();
		$c = 0;
  	$sql = "SELECT * FROM ".$this->tabla;
		if ($condicion != ""){
			 $sql .= " WHERE ".$condicion;
		}
		$sql .= " ORDER BY ".$this->columna_order;

		$array = Array();
				
  	$rs = $this->conn->OpenRecordset($sql);
  	while ($row_rs = $this->conn->FetchObject($rs)){
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
  * Lista los Proyecto que cumplen la condición en el formato dado
  * @access public
  * @param string $formato Formato en el que se listarán los Proyecto, puede ser Tabla o ComboSelect
	* @param int $valor_combo ID del Proyecto que será selccionado cuando el formato es ComboSelect
  * @param string $condicion Condición que deben cumplir los Proyecto y que se agrega en el SQL statement.
  */			
	function ListarCombo($formato,$valor_combo,$condicion){
		$arr = $this->GetAllArray($condicion);
		$num_arr = count($arr);
		
		for($a=0;$a<$num_arr;$a++){
			$this->Imprimir($arr[$a],$formato,$valor_combo);
		}
	}

  /**
  * Imprime en pantalla los datos del Proyecto
  * @access public
  * @param object $vo Proyecto que se va a imprimir
  * @param string $formato Formato en el que se listarán los Proyecto, puede ser Tabla o ComboSelect
	* @param int $valor_combo ID del Proyecto que será selccionado cuando el formato es ComboSelect
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
  * Carga un VO de Proyecto con los datos de la consulta
  * @access public
  * @param object $vo VO de Proyecto que se va a recibir los datos
  * @param object $Resultset Resource de la consulta
	* @return object $vo VO de Proyecto con los datos
  */			
	function GetFromResult ($vo,$Result){
	  
    $beneficiario_vo = New Beneficiario();
    $beneficiario_dao = New BeneficiarioDAO();		
	  
	  
	  
		
	  $vo->id = $Result->{$this->columna_id};
	  $vo->nombre = $Result->{$this->columna_nombre};

		$vo->id_moneda = $Result->ID_MON;
		$vo->id_estp = $Result->ID_ESTP;
		$vo->codigo = $Result->COD_PROY;
		$vo->desc = $Result->DES_PROY;
		$vo->obj = $Result->OBJ_PROY;
		$vo->fechi_ini = $Result->INICIO_PROY;
		$vo->fechi_fin = $Result->FIN_PROY;
		$vo->fechi_update = $Result->ACTUA_PROY;
		$vo->costo = $Result->COSTO_PROY;
		
		//DEPARTAMENTOS
		$arr = Array();
		$sql_s = "SELECT ID_DEPTO FROM depto_proy WHERE ID_PROY = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
  	while ($row_rs_s = $this->conn->FetchRow($rs_s)){
		  array_push($arr,$row_rs_s[0]);
		}		
		$vo->id_deptos = $arr;
		
		//MUNICIPIOS
		$arr = Array();
		$sql_s = "SELECT ID_MUN FROM mun_proy WHERE ID_PROY = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
  	while ($row_rs_s = $this->conn->FetchRow($rs_s)){
		  array_push($arr,$row_rs_s[0]);
		}		
		$vo->id_muns = $arr;
		
		//REGIONES
		$arr = Array();
		$sql_s = "SELECT ID_REG FROM reg_proy WHERE ID_PROY = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
  	while ($row_rs_s = $this->conn->FetchRow($rs_s)){
		  array_push($arr,$row_rs_s[0]);
		}		
		$vo->id_regiones = $arr;

		//SECTORES
		$sectores = Array();
		$sql_s = "SELECT ID_COMP FROM sector_proy WHERE ID_PROY = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
  	while ($row_rs_s = $this->conn->FetchRow($rs_s)){
		  array_push($sectores,$row_rs_s[0]);
		}		
		$vo->id_sectores = $sectores;
		
		//BENEFICIARIOS
		$benes = $beneficiario_dao->GetAllArray('ID_PROY='.$id);
		$num_benes = count($benes);
		$id_benes = Array();
		for($s=0;$s<$num_benes;$s++){
		  $beneficiario_vo = $benes[$s];
		  array_push($id_benes,$beneficiario_vo->id_pobla);
		}
		$vo->id_beneficiarios = $id_benes;
		
		return $vo;
		
	}

  /**
  * Inserta un Proyecto en la B.D.
  * @access public
  * @param object $depto_vo VO de Proyecto que se va a insertar
  */		
	function Insertar($proy_vo){
	  //CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray("NOM_PROY like '".$proy_vo->nombre."'");
 		if (count($cat_a) == 0){
  		
			//DATOS DEL PROYECTO
			$sql =  "INSERT INTO ".$this->tabla." (ID_MON,ID_ESTP,NOM_PROY,COD_PROY,DES_PROY,OBJ_PROY,INICIO_PROY,FIN_PROY,ACTUA_PROY,COSTO_PROY,DURACION)";
  		$sql .= " VALUES (".$proy_vo->id_moneda.",".$proy_vo->id_estp.",'".$proy_vo->nombre."','".$proy_vo->codigo."','".$proy_vo->desc."','".$proy_vo->obj."','".$proy_vo->fecha_ini."','".$proy_vo->fecha_fin."',now(),".$proy_vo->costo.",".$proy_vo->duracion.")";
			
  		$this->conn->Execute($sql);
			//echo $sql;
			$id_proy = $this->conn->GetGeneratedID();
  		
			//COBERTURA - DEPTOS
			$arr = $proy_vo->id_deptos;
			$num_arr = count($arr);
			
			for($m=0;$m<$num_arr;$m++){
			  $sql = "INSERT INTO depto_proy (ID_DEPTO,ID_PROY) VALUES ('".$arr[$m]."',".$id_proy.")";
				echo $sql;
				$this->conn->Execute($sql);
			}

			//COBERTURA - MUNICPIOS
			$arr = $proy_vo->id_muns;
			$num_arr = count($arr);
			
			for($m=0;$m<$num_arr;$m++){
			  $sql = "INSERT INTO mun_proy (ID_MUN,ID_PROY) VALUES ('".$arr[$m]."',".$id_proy.")";
				$this->conn->Execute($sql);
			}
			
			//SECTORES
			$arr = $proy_vo->id_sectores;
			$num_arr = count($arr);
			
			for($m=0;$m<$num_arr;$m++){
			  $sql = "INSERT INTO sector_proy (ID_COMP,ID_PROY) VALUES (".$arr[$m].",".$id_proy.")";
				$this->conn->Execute($sql);
			}

			//CONTACTOS
			$arr = $proy_vo->id_contactos;
			$num_arr = count($arr);
			
			for($m=0;$m<$num_arr;$m++){
			  $sql = "INSERT INTO proyecto_conta (ID_PROY,ID_CONP) VALUES (".$id_proy.",".$arr[$m].")";
				$this->conn->Execute($sql);
			}

			//REGIONES
			$arr = $proy_vo->id_regiones;
			$num_arr = count($arr);
			
			for($m=0;$m<$num_arr;$m++){
			  $sql = "INSERT INTO reg_proy (ID_REG,ID_PROY) VALUES (".$arr[$m].",".$id_proy.")";
				$this->conn->Execute($sql);
			}
			
			//POBLACION BENEFICIADA
			$arr = $proy_vo->id_poblaciones;
			$num_arr = count($arr);
			
			for($m=0;$m<$num_arr;$m++){
			  $sql = "INSERT INTO beneficiario (ID_POBLA,ID_PROY,CANT_PER) VALUES (".$arr[$m].",".$id_proy.",".$proy_vo->cant_per.")";
				$this->conn->Execute($sql);
			}
			
			//ORGANIZACIONES EJECUTORAS - DONANTES
			$arr = $proy_vo->id_orgs_e;
			//$id_tipo = $proy_vo->id_tipo_vinc_orgs;
			$num_arr = count($arr);
			
			for($m=0;$m<$num_arr;$m++){
			  $sql = "INSERT INTO vinculorgpro (ID_TIPO_VINORGPRO,ID_ORG,ID_PROY,VALOR_APORTE) VALUES (1,".$arr[$m].",".$id_proy.",0)";
				echo $sql;
				$this->conn->Execute($sql);
			}
			
			//ORGANIZACIONES DONANTES
			$arr = $proy_vo->id_orgs_d;
			//$id_tipo = $proy_vo->id_tipo_vinc_orgs;
			$num_arr = count($arr);
			
			for($m=0;$m<$num_arr;$m++){
			  $sql = "INSERT INTO vinculorgpro (ID_TIPO_VINORGPRO,ID_ORG,ID_PROY,VALOR_APORTE) VALUES (2,".$arr[$m].",".$id_proy.",0)";
				echo $sql;
				$this->conn->Execute($sql);
			}
			
			
    	?>
    	<script>
    	//alert("Done!");
    	//location.href='index.php?accion=listar';
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
  * Actualiza un Proyecto en la B.D.
  * @access public
  * @param object $depto_vo VO de Proyecto que se va a actualizar
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
  * Borra un Proyecto en la B.D.
  * @access public
  * @param int $id ID del Proyecto que se va a borrar de la B.D
  */	
	function Borrar($id){
		
  	//COBERTURA - DEPTOS
    $sql = "DELETE FROM depto_proy WHERE ID_PROY = ".$id;
    $this->conn->Execute($sql);
  	
		//COBERTURA - MUNICPIOS
    $sql = "DELETE FROM mun_proy WHERE ID_PROY = ".$id;
    $this->conn->Execute($sql);
			
  	//SECTORES
    $sql = "DELETE FROM sector_proy WHERE ID_PROY = ".$id;
    $this->conn->Execute($sql);
			
		//CONTACTOS
    $sql = "DELETE FROM proyecto_conta WHERE ID_PROY = ".$id;
    $this->conn->Execute($sql);
			
		//ORGANIZACIONES
    $sql = "DELETE FROM vinculorgpro WHERE ID_PROY = ".$id;
    $this->conn->Execute($sql);
			
		//REGIONES
    $sql = "DELETE FROM reg_proy WHERE ID_PROY = ".$id;
    $this->conn->Execute($sql);
			
		//POBLACION BENEFICIADA
    $sql = "DELETE FROM beneficiario WHERE ID_PROY = ".$id;
    $this->conn->Execute($sql);
			
		//ORGANIZACIONES EJECUTORAS - DONANTES
    $sql = "DELETE FROM vinculorgpro WHERE ID_PROY = ".$id;
    $this->conn->Execute($sql);
		
		//PROYECTO
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