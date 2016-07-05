<?php
/**
 * DAO de Resultado
 *
 * Contiene los métodos de la clase Resultado 
 * @author Ruben A. Rojas C.
 */

Class UnicefResultadoDAO {

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
	function UnicefResultadoDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "unicef_resultado_cpd";
		$this->columna_id = "id_resultado";
		$this->columna_nombre = "nombre";
		$this->columna_order = "codigo";
	}

	/**
	 * Consulta los datos de un Resultado
	 * @access public
	 * @param int $id ID del Resultado
	 * @return VO
	 */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$vo = New UnicefResultado();

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
			$vo = New UnicefResultado();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}  
		//Retorna el Arreglo de VO
		return $array;
	}

	/**
	 * Consulta los datos de los Resultados que cumplen una condición
	 * @access public
	 * @param string $condicion Condición que deben cumplir los Resultados y que se agrega en el SQL statement.
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
	 * Consulta los datos de los Resultados que cumplen una condición
	 * @access public
	 * @param string $condicion Condición que deben cumplir los Resultados y que se agrega en el SQL statement.
	 * @return array Arreglo de ID
	 */
	function GetAllArrayIDTree($condicion){

		$sql = "SELECT DISTINCT(".$this->columna_id.") FROM ".$this->tabla." JOIN unicef_producto_cpap USING(id_resultado) JOIN unicef_actividad_awp USING(id_producto)";

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
	 * Lista los Resultado que cumplen la condición en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarán los Resultado, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del Resultado que será selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condición que deben cumplir los Resultado y que se agrega en el SQL statement.
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
	 * Carga un VO de Resultado con los datos de la consulta
	 * @access public
	 * @param object $vo VO de Resultado que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de Resultado con los datos
	 */			
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};
		$vo->id_sub_componente = $Result->id_sub_componente;
		$vo->id_periodo = $Result->id_periodo;
		$vo->codigo = $Result->codigo;
        
        // Valores del indicador para el periodo
        $periodo_dao = new UnicefPeriodoDAO();
        $periodo = $periodo_dao->GetAllArray('activo=1');
        $aaaa_ini = $periodo[0]->aaaa_ini;
        $aaaa_fin = $periodo[0]->aaaa_fin;
        
        // Indicadores & Lineas de base
        $sql = "SELECT id_indicador,linea_base FROM ".$this->tabla."_indicador WHERE $this->columna_id = $vo->id ORDER BY id_resultado_indicador";
        $rs = $this->conn->OpenRecordset($sql);
        while ($row = $this->conn->FetchRow($rs)){
		    $vo->id_indicador[] = $row[0];
		    $vo->linea_base[] = $row[1];

            for ($a=$aaaa_ini;$a<=$aaaa_fin;$a++){
                $sql_v = "SELECT valor FROM unicef_indicador_valor WHERE id_resultado = $vo->id AND aaaa = $a AND id_indicador = ".$row[0];
                $rs_v = $this->conn->OpenRecordset($sql_v);
                $row_v = $this->conn->FetchRow($rs_v);
                $vo->indicador_valor[$row[0]][$a] = $row_v[0]; 
            }
        }

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
	 * Inserta un Resultado en la B.D.
	 * @access public
	 * @param object $subcat_vo VO de Resultado que se va a insertar
	 */		
	function Insertar($vo){
		
        //CONSULTA SI YA EXISTE
		$tmp = $this->GetAllArray($this->columna_nombre." = '".$vo->nombre."' AND id_sub_componente = $vo->id_sub_componente");
		if (count($tmp) == 0){
		    $sql = "INSERT INTO $this->tabla (id_periodo,id_sub_componente,nombre,codigo) VALUES ($vo->id_periodo,$vo->id_sub_componente,'$vo->nombre','$vo->codigo')";	
            $this->conn->Execute($sql);
            
            $vo->id = $this->GetMaxID();
            $this->InsertarTablasUnion($vo);

			echo "Registro insertado con &eacute;xito!";
		}
		else{
			echo "Error - Ya existe el resultado";
		}

	}

	/**
	 * Inserta los valores del indice de un Resultado en la B.D.
	 * @access public
	 * @param object $vo VO de Resultado
	 */		
	function InsertarTablasUnion($vo){

        foreach ($vo->id_indicador as $i=>$id_ind){
            $l_b = (isset($vo->linea_base[$i])) ? $vo->linea_base[$i]: '' ;
            $sql = "INSERT INTO ".$this->tabla."_indicador ($this->columna_id,id_indicador,linea_base) VALUES ($vo->id,$id_ind,'$l_b')";
            $this->conn->Execute($sql);

            if (isset($vo->indicador_valor[$i])){
                foreach ($vo->indicador_valor[$i] as $aaaa=>$valor){
                    $sql = "INSERT INTO unicef_indicador_valor ($this->columna_id,id_indicador,aaaa,valor) VALUES ($vo->id,$id_ind,$aaaa,'$valor')";
                    $this->conn->Execute($sql);
                }
            }
        }

    }

	/**
	 * Actualiza un Resultado en la B.D.
	 * @access public
	 * @param object $vo VO de Resultado que se va a actualizar
	 */		
	function Actualizar($vo){
        $sql = "UPDATE $this->tabla SET 
            
            id_periodo = $vo->id_periodo,
            id_sub_componente = $vo->id_sub_componente,
            nombre = '$vo->nombre',
            codigo = '$vo->codigo'
		    
            WHERE ".$this->columna_id." = ".$vo->id;
            
            $this->BorrarTablasUnion($vo->id);
            $this->InsertarTablasUnion($vo);

		$this->conn->Execute($sql);

	}

	/**
	 * Borra un Resultado en la B.D.
	 * @access public
	 * @param int $id ID del Resultado que se va a borrar de la B.D
	 */	
	function Borrar($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

        $this->BorrarTablasUnion($id);

	}

	/**
	 * Borra las tabla de union de un Resultado en la B.D.
	 * @access public
	 * @param int $id ID del Resultado que se va a borrar de la B.D
	 */	
	function BorrarTablasUnion($id){

		//BORRA
		$sql = "DELETE FROM unicef_resultado_cpd_indicador WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);
		
        $sql = "DELETE FROM unicef_indicador_valor WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

	}

	/**
	 * Retorna el numero de Registros
	 * @access public
	 * @return int
	 */
	function numRecords($condicion,$caso='normal'){

		$sql = "SELECT count(".$this->columna_id.") as num FROM ".$this->tabla;
        
        if ($caso == 'tree') $sql = "SELECT ".$this->columna_id." FROM ".$this->tabla." JOIN unicef_producto_cpap USING(id_resultado) JOIN unicef_actividad_awp USING(id_producto)";
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

		$tabla_rel = 'unicef_producto_cpap';
		$col_id = $this->columna_id;
		
		$sql = "SELECT count($col_id) FROM $tabla_rel WHERE $col_id = $id";
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$r = ($row[0] > 0) ? true : false;

		return $r;

	}
}

?>
