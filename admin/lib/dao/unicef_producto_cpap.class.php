<?php
/**
 * DAO de ProductoCpap
 *
 * Contiene los métodos de la clase ProductoCpap 
 * @author Ruben A. Rojas C.
 */

Class UnicefProductoCpapDAO {

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
	function UnicefProductoCpapDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "unicef_producto_cpap";
		$this->columna_id = "id_producto";
		$this->columna_nombre = "nombre";
		$this->columna_order = "codigo";
	}

	/**
	 * Consulta los datos de un ProductoCpap
	 * @access public
	 * @param int $id ID del ProductoCpap
	 * @return VO
	 */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$vo = New UnicefProductoCpap();

		//Carga el VO
		$vo = $this->GetFromResult($vo,$row_rs);

		//Retorna el VO
		return $vo;
	}

	/**
	* Consulta el valor de un field de la Org
	* @access public
	* @param int $id ID del Organizacion
	* @param string $field Field de la tabla org
	* @return VO
	*/
	function GetFieldValue($id,$field){
		$sql = "SELECT ".$field." FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchRow($rs);

		//Retorna el VO
		return $row_rs[0];
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
			$vo = New UnicefProductoCpap();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}  
		//Retorna el Arreglo de VO
		return $array;
	}

	/**
	 * Consulta los datos de los Depto que cumplen una condición
	 * @access public
	 * @param string $condicion Condición que deben cumplir los Depto y que se agrega en el SQL statement.
	 * @return array Arreglo de ID
	 */
	function GetAllArrayID($condicion){

		$sql = "SELECT ".$this->columna_id." FROM ".$this->tabla."";

		if ($condicion != "") $sql .= " WHERE ".$condicion;

		$sql .= " ORDER BY ".$this->columna_order;

		$array = Array();

		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchRow($rs)){
			//Carga el arreglo
			$array[] = $row_rs[0];
		}

		//Retorna el Arreglo
		return $array;
	}

	/**
	 * Consulta los datos de los Productos que cumplen una condición
	 * @access public
	 * @param string $condicion Condición que deben cumplir los Resultados y que se agrega en el SQL statement.
	 * @return array Arreglo de ID
	 */
	function GetAllArrayIDTree($condicion){

		$sql = "SELECT DISTINCT(".$this->columna_id.") FROM ".$this->tabla." JOIN unicef_actividad_awp USING(id_producto)";

		if ($condicion != "") $sql .= " WHERE ".$condicion;

		$sql .= " ORDER BY ".$this->tabla.".".$this->columna_order;

		$array = Array();

		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchRow($rs)){
			//Carga el arreglo
			$array[] = $row_rs[0];
		}

		//Retorna el Arreglo
		return $array;
	}

	/**
	 * Lista los ProductoCpap que cumplen la condición en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarán los ProductoCpap, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del ProductoCpap que será selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condición que deben cumplir los ProductoCpap y que se agrega en el SQL statement.
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
	 * Carga un VO de ProductoCpap con los datos de la consulta
	 * @access public
	 * @param object $vo VO de ProductoCpap que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de ProductoCpap con los datos
	 */			
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};
		$vo->id_resultado = $Result->id_resultado;
		$vo->codigo = $Result->codigo;

        // Valores del indicador para el periodo
        $periodo_dao = new UnicefPeriodoDAO();
        $periodo = $periodo_dao->GetAllArray('activo=1');
        $aaaa_ini = $periodo[0]->aaaa_ini;
        $aaaa_fin = $periodo[0]->aaaa_fin;

        // Indicadores & Lineas de base & meta
        $sql = "SELECT id_indicador,linea_base,meta FROM ".$this->tabla."_indicador WHERE $this->columna_id = $vo->id ORDER BY id_producto_indicador";
        $rs = $this->conn->OpenRecordset($sql);
        while ($row = $this->conn->FetchRow($rs)){
            $id_indicador = $row[0];
		    $vo->id_indicador[] = $id_indicador;
		    $vo->linea_base[] = $row[1];
		    $vo->meta[] = $row[2];

            for ($a=$aaaa_ini;$a<=$aaaa_fin;$a++){
                $sql_v = "SELECT valor FROM unicef_indicador_valor WHERE $this->columna_id = $vo->id AND aaaa = $a AND id_indicador = ".$id_indicador;
                $rs_v = $this->conn->OpenRecordset($sql_v);
                $row_v = $this->conn->FetchRow($rs_v);
                $vo->indicador_valor[$id_indicador][$a] = $row_v[0];

                $sql_v = "SELECT valor FROM ".$this->tabla."_meta WHERE $this->columna_id = $vo->id AND aaaa = $a AND id_indicador = $id_indicador";
                $rs_v = $this->conn->OpenRecordset($sql_v);
                $row_v = $this->conn->FetchRow($rs_v);
                $vo->meta_valor[$id_indicador][$a] = $row_v[0];

            }
        }
        /*
        for ($a=$aaaa_ini;$a<=$aaaa_fin;$a++){
            $sql = "SELECT valor FROM unicef_indicador_valor WHERE id_producto = $vo->id AND aaaa = $a";
            $rs = $this->conn->OpenRecordset($sql);
            $row = $this->conn->FetchRow($rs);
            $vo->indicador_valor[$a] = $row[0]; 
        }

        for ($a=$aaaa_ini;$a<=$aaaa_fin;$a++){
            $sql = "SELECT valor FROM unicef_producto_cpap_meta WHERE id_producto = $vo->id AND aaaa = $a";
            $rs = $this->conn->OpenRecordset($sql);
            $row = $this->conn->FetchRow($rs);
            $vo->meta_valor[$a] = $row[0]; 
        }
        */

		return $vo;
	}

    /**
     * Retorna el max ID
     * @access public
     * @return int
     */
	function GetMaxID(){
        
        $sql = "SELECT max($this->columna_id) as maxid FROM ".$this->tabla;
		$rs = $this->conn->OpenRecordset($sql);
		
        if ($row_rs = $this->conn->FetchRow($rs)){
			return $row_rs[0];
		}
		else{
			return 0;
		}
	}

	/**
	 * Inserta un ProductoCpap en la B.D.
	 * @access public
	 * @param object $subcat_vo VO de ProductoCpap que se va a insertar
	 */		
	function Insertar($vo){
		
        //CONSULTA SI YA EXISTE
		$tmp = $this->GetAllArray($this->columna_nombre." = '".$vo->nombre."' AND id_resultado = $vo->id_resultado");
		if (count($tmp) == 0){
		    $sql = "INSERT INTO $this->tabla (id_resultado,nombre,codigo) VALUES ($vo->id_resultado,'$vo->nombre','$vo->codigo')";	
            $this->conn->Execute($sql);
            
            $vo->id = $this->GetMaxID();
            $this->InsertarTablasUnion($vo);

			echo "Registro insertado con &eacute;xito!";
		}
		else{
			echo "Error - Ya existe el producto";
		}

	}

	/**
	 * Inserta los valores del indice de un ProductoCpap en la B.D.
	 * @access public
	 * @param object $vo VO de ProductoCpap
	 */		
	function InsertarTablasUnion($vo){

        foreach ($vo->id_indicador as $i=>$id_ind){
            $l_b = (isset($vo->linea_base[$i])) ? $vo->linea_base[$i]: '' ;
            $meta = (isset($vo->meta[$i])) ? $vo->meta[$i]: '' ;
            $sql = "INSERT INTO ".$this->tabla."_indicador ($this->columna_id,id_indicador,linea_base,meta) VALUES ($vo->id,$id_ind,'$l_b','$meta')";
            $this->conn->Execute($sql);

            // Valor
            if (isset($vo->indicador_valor[$i])){
                foreach ($vo->indicador_valor[$i] as $aaaa=>$valor){
                    $sql = "INSERT INTO unicef_indicador_valor ($this->columna_id,id_indicador,aaaa,valor) VALUES ($vo->id,$id_ind,$aaaa,'$valor')";
                    $this->conn->Execute($sql);
                }
            }

            // Meta
            if (isset($vo->meta_valor[$i])){
                foreach ($vo->meta_valor[$i] as $aaaa=>$valor){
                    $sql = "INSERT INTO ".$this->tabla."_meta ($this->columna_id,id_indicador,aaaa,valor) VALUES ($vo->id,$id_ind,$aaaa,'$valor')";
                    $this->conn->Execute($sql);
                }
            }
        }
        
        /*
        foreach ($vo->indicador_valor as $aaaa=>$valor){
            $sql = "INSERT INTO unicef_indicador_valor (id_producto,id_indicador,aaaa,valor) VALUES ($vo->id,$vo->id_indicador,$aaaa,'$valor')";
            $this->conn->Execute($sql);
        }
        
        foreach ($vo->meta_valor as $aaaa=>$valor){
            $sql = "INSERT INTO unicef_producto_cpap_meta (id_producto,aaaa,valor) VALUES ($vo->id,$aaaa,'$valor')";
            $this->conn->Execute($sql);
        }
        */
    }

	/**
	 * Actualiza un ProductoCpap en la B.D.
	 * @access public
	 * @param object $vo VO de ProductoCpap que se va a actualizar
	 */		
	function Actualizar($vo){
        $sql = "UPDATE $this->tabla SET 
            id_resultado = $vo->id_resultado,
            nombre = '$vo->nombre',
            codigo = '$vo->codigo'
		    
            WHERE ".$this->columna_id." = ".$vo->id;
            
            $this->BorrarTablasUnion($vo->id);
            $this->InsertarTablasUnion($vo);

		$this->conn->Execute($sql);

	}

	/**
	 * Borra un ProductoCpap en la B.D.
	 * @access public
	 * @param int $id ID del ProductoCpap que se va a borrar de la B.D
	 */	
	function Borrar($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

        $this->BorrarTablasUnion($id);

	}

	/**
	 * Borra las tabla de union de un ProductoCpap en la B.D.
	 * @access public
	 * @param int $id ID del ProductoCpap que se va a borrar de la B.D
	 */	
	function BorrarTablasUnion($id){

		//BORRA
		$sql = "DELETE FROM unicef_indicador_valor WHERE $this->columna_id = $id";
		$this->conn->Execute($sql);
		
        $sql = "DELETE FROM ".$this->tabla."_indicador WHERE $this->columna_id = $id";
		$this->conn->Execute($sql);
		
        $sql = "DELETE FROM ".$this->tabla."_meta WHERE $this->columna_id = $id";
		$this->conn->Execute($sql);

	}

	/**
	 * Retorna el numero de Registros
	 * @access public
	 * @return int
	 */
	function numRecords($condicion,$caso='normal'){

		$sql = "SELECT count(".$this->columna_id.") as num FROM ".$this->tabla;
        
        if ($caso == 'tree')    $sql = "SELECT COUNT(DISTINCT(".$this->columna_id.")) FROM ".$this->tabla." JOIN unicef_actividad_awp USING(id_producto)";
		
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

		$tabla_rel = 'unicef_actividad_awp';
		$col_id = $this->columna_id;
		
		$sql = "SELECT count($col_id) FROM $tabla_rel WHERE $col_id = $id";
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$r = ($row[0] > 0) ? true : false;

		return $r;

	}
}

?>
