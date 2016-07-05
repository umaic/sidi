<?

/**
 * DAO de Organizacion
 *
 * Contiene los métodos de la clase Organizacion 
 * @author Ruben A. Rojas C.
 */
 
Class OrganizacionDAO {

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
	function OrganizacionDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "organizacion";
		$this->columna_id = "ID_ORG";
		$this->columna_nombre = "NOM_ORG";
		$this->columna_order = "NOM_ORG";
	}
	
  /**
  * Consulta los datos de una Organizacion
  * @access public
  * @param int $id ID del Organizacion
  * @return VO
  */	
	function Get($id){
  	$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
  	$rs = $this->conn->OpenRecordset($sql);
  	$row_rs = $this->conn->FetchObject($rs);
		
  	//Crea un VO
  	$depto_vo = New Organizacion();
  
  	//Carga el VO
  	$depto_vo = $this->GetFromResult($depto_vo,$row_rs);
  
  	//Retorna el VO
  	return $depto_vo;
	}

  /**
  * Consulta los datos de los Organizacion que cumplen una condición
  * @access public
  * @param string $condicion Condición que deben cumplir los Organizacion y que se agrega en el SQL statement.
  * @return array Arreglo de VOs
  */	
	function GetAllArray($condicion){
  	//Crea un VO
  	$vo = New Organizacion();
		$c = 0;
  	$sql = "SELECT * FROM ".$this->tabla."";
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
  * Lista los Organizacion que cumplen la condición en el formato dado
  * @access public
  * @param string $formato Formato en el que se listarán los Organizacion, puede ser Tabla o ComboSelect
	* @param int $valor_combo ID del Organizacion que será selccionado cuando el formato es ComboSelect
  * @param string $condicion Condición que deben cumplir los Organizacion y que se agrega en el SQL statement.
  */			
	function ListarCombo($formato,$valor_combo,$condicion){
		$arr = $this->GetAllArray($condicion);
		$num_arr = count($arr);
		
		for($a=0;$a<$num_arr;$a++){
			$this->Imprimir($arr[$a],$formato,$valor_combo);
		}
	}


  /**
  * Imprime en pantalla los datos del Organizacion
  * @access public
  * @param object $vo Organizacion que se va a imprimir
  * @param string $formato Formato en el que se listarán los Organizacion, puede ser Tabla o ComboSelect
	* @param int $valor_combo ID del Organizacion que será selccionado cuando el formato es ComboSelect
  */			
	function Imprimir($vo,$formato,$valor_combo){
		$titulo = "";
		if ($vo->nombre != "" && $vo->sigla == ""){
		  $titulo = $vo->nombre;
		}
		else if ($vo->nombre == "" && $vo->sigla != ""){
		  $titulo = $vo->sigla;
		}
		else if ($vo->nombre != "" && $vo->sigla != ""){
		  $titulo = $vo->sigla." -- ".$vo->nombre;
		}
		
		if ($titulo != ""){
  		if ($formato == 'combo'){
  			if ($valor_combo == "" && $valor_combo != 0)
          echo "<option value=".$vo->id.">".$titulo."</option>";
  			else{
          echo "<option value=".$vo->id;
  				if ($valor_combo == $vo->id)
  					 echo " selected ";
  				echo ">".$titulo."</option>";
  			}
  		}
		}
	}

  /**
  * Carga un VO de Organizacion con los datos de la consulta
  * @access public
  * @param object $vo VO de Organizacion que se va a recibir los datos
  * @param object $Resultset Resource de la consulta
	* @return object $vo VO de Organizacion con los datos
  */			
	function GetFromResult ($vo,$Result){
	  eval("\$id = \$Result->$this->columna_id;");
	  eval("\$nombre = \$Result->$this->columna_nombre;");
		
		$vo->id_pobla = $Result->ID_POBLA;
		$vo->id_tipo = $Result->ID_TIPO;
		$vo->id_papa = $Result->ID_ORG_PAPA;
		$vo->id_mun_sede = $Result->ID_MUN_SEDE;
		$vo->sig = $Result->SIG_ORG;
		$vo->naci = $Result->NACI_ORG;
		$vo->view = $Result->VIEW_ORG;
		$vo->bd = $Result->BD_ORG;
		$vo->dir = $Result->DIR_ORG;
		$vo->tel1 = $Result->TEL1_ORG;
		$vo->tel2 = $Result->TEL2_ORG;
		$vo->fax = $Result->FAX_ORG;
		$vo->un_email = $Result->UN_MAIL_ORG;
		$vo->pu_email = $Result->PU_MAIL_ORG;
		$vo->web = $Result->WEB_ORG;
		$vo->logo = $Result->LOGO_ORG;
		$vo->dona = $Result->DONA_ORG;
		$vo->n_rep = $Result->N_REP_ORG;
		$vo->t_rep = $Result->T_REP_ORG;
		$vo->tel_rep = $Result->TEL_REP_ORG;
		$vo->email_rep = $Result->EMAIL_REP_ORG;
		$vo->info_confirmada = $Result->INFO_CONFIRMADA;
		
		//DEPARTAMENTOS
		$arr = Array();
		$sql_s = "SELECT ID_DEPTO FROM depto_evento WHERE ID_ORG = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
  	while ($row_rs_s = $this->conn->FetchRow($rs_s)){
		  array_push($arr,$row_rs_s[0]);
		}		
		$vo->id_deptos = $arr;
		
		//REGIONES
		$arr = Array();
		$sql_s = "SELECT ID_REG FROM reg_evento WHERE ID_ORG = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
  	while ($row_rs_s = $this->conn->FetchRow($rs_s)){
		  array_push($arr,$row_rs_s[0]);
		}		
		$vo->id_regiones = $arr;

		//POBLADOS
		$arr = Array();
		$sql_s = "SELECT ID_POB FROM poblado_evento WHERE ID_ORG = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
  	while ($row_rs_s = $this->conn->FetchRow($rs_s)){
		  array_push($arr,$row_rs_s[0]);
		}		
		$vo->id_poblados = $arr;
		
		//RESGUARDOS
		$arr = Array();
		$sql_s = "SELECT ID_RESGUADRO FROM resg_evento WHERE ID_ORG = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
  	while ($row_rs_s = $this->conn->FetchRow($rs_s)){
		  array_push($arr,$row_rs_s[0]);
		}		
		$vo->id_resguardos = $arr;

		//PARQUES
		$arr = Array();
		$sql_s = "SELECT ID_PAR_NAT FROM par_nat_evento WHERE ID_ORG = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
  	while ($row_rs_s = $this->conn->FetchRow($rs_s)){
		  array_push($arr,$row_rs_s[0]);
		}		
		$vo->id_parques = $arr;
			
		//DIV. AFRO
		$arr = Array();
		$sql_s = "SELECT ID_DIV_AFRO FROM div_afro_evento WHERE ID_ORG = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
  	while ($row_rs_s = $this->conn->FetchRow($rs_s)){
		  array_push($arr,$row_rs_s[0]);
		}		
		$vo->id_divisiones_afro = $arr;
		
		//CONTACTOS
		$arr = Array();
		$sql_s = "SELECT ID_CONP FROM org_conp WHERE ID_ORG = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
  	while ($row_rs_s = $this->conn->FetchRow($rs_s)){
		  array_push($arr,$row_rs_s[0]);
		}		
		$vo->id_contactos = $arr;						
		
		//ENFOQUES
		$arr = Array();
		$sql_s = "SELECT ID_ENF FROM enf_org WHERE ID_ORG = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
  	while ($row_rs_s = $this->conn->FetchRow($rs_s)){
		  array_push($arr,$row_rs_s[0]);
		}		
		$vo->id_enfoques = $arr;						

		//SECTORES
		$arr = Array();
		$sql_s = "SELECT ID_ENF FROM sector_org WHERE ID_ORG = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
  	while ($row_rs_s = $this->conn->FetchRow($rs_s)){
		  array_push($arr,$row_rs_s[0]);
		}		
		$vo->id_sectores = $arr;						

		return $vo;
	}

  /**
  * Inserta un Organizacion en la B.D.
  * @access public
  * @param object $depto_vo VO de Organizacion que se va a insertar
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
  * Actualiza un Organizacion en la B.D.
  * @access public
  * @param object $depto_vo VO de Organizacion que se va a actualizar
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
  * Borra un Organizacion en la B.D.
  * @access public
  * @param int $id ID del Organizacion que se va a borrar de la B.D
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
