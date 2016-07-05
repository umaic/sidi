<?php
/**
 * DAO de ProductoAwp
 *
 * Contiene los métodos de la clase ProductoAwp 
 * @author Ruben A. Rojas C.
 */

Class UnicefProductoAwpDAO {

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
	function UnicefProductoAwpDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "unicef_producto_awp";
		$this->columna_id = "id_producto";
		$this->columna_nombre = "nombre";
		$this->columna_order = "codigo";
	}

	/**
	 * Consulta los datos de un ProductoAwp
	 * @access public
	 * @param int $id ID del ProductoAwp
	 * @return VO
	 */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$vo = New UnicefProductoAwp();

		//Carga el VO
		$vo = $this->GetFromResult($vo,$row_rs);

		//Retorna el VO
		return $vo;
	}

	/**
	* Consulta el valor de un field
	* @access public
	* @param int $id ID 
	* @param string $field Nombre de la columna de la tabla
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
			$vo = New UnicefProductoAwp();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}  
		//Retorna el Arreglo de VO
		return $array;
	}

	/**
	 * Consulta Vos por cobertura
	 * @access public
	 * @param string $mdgd Deptal,Mpal
	 * @param string $condicion Condición que deben cumplir los Tema y que se agrega en el SQL statement.
	 * @param string $limit Limit en el SQL
	 * @param string $order by Order by en el SQL 
	 * @return array Arreglo de VOs
	 */	
	function GetAllArrayCobertura($mdgd,$id_depto_mpio,$condicion,$limit='',$order_by=''){

        if ($mdgd == 'nal'){
            $sql = "SELECT * FROM ".$this->tabla." WHERE cobertura = 'N' ";
        }
        else{
            $caso = ($mdgd == 'deptal') ? 'depto' : 'mun';
            $sql = "SELECT p_awp.* FROM $this->tabla p_awp JOIN ".$this->tabla."_".$caso." USING($this->columna_id)";
            $sql .= " WHERE  id_".$caso." IN (".$id_depto_mpio.")";
        }
        
		if ($condicion != "") $sql .= " AND ".$condicion;

		//ORDER
		$sql .= ($order_by != "") ?  " ORDER BY $order_by" : " ORDER BY ".$this->columna_order;

		//LIMIT
		if ($limit != "") $sql .= " LIMIT ".$limit;

		$array = Array();

		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchObject($rs)){
			//Crea un VO
			$vo = New UnicefProductoAwp();
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
	 * Consulta ID por cobertura
	 * @access public
	 * @param string $mdgd Deptal,Mpal
	 * @param string $condicion Condición que deben cumplir los Tema y que se agrega en el SQL statement.
	 * @param string $limit Limit en el SQL
	 * @param string $order by Order by en el SQL 
	 * @return array Arreglo de VOs
	 */	
	function GetAllArrayIDCobertura($mdgd,$id_depto_mpio,$condicion,$limit='',$order_by=''){

        if ($mdgd == 'nal'){
            $sql = "SELECT $this->columna_id FROM ".$this->tabla." WHERE cobertura = 'N' ";
        }
        else{
            $caso = ($mdgd == 'deptal') ? 'depto' : 'mun';
            $sql = "SELECT p_awp.$this->columna_id FROM $this->tabla p_awp JOIN ".$this->tabla."_".$caso." USING($this->columna_id)";
            $sql .= " WHERE  id_".$caso." IN (".$id_depto_mpio.")";
        }
        
		if ($condicion != "") $sql .= " AND ".$condicion;

		//ORDER
		$sql .= ($order_by != "") ?  " ORDER BY $order_by" : " ORDER BY ".$this->columna_order;

		//LIMIT
		if ($limit != "") $sql .= " LIMIT ".$limit;

		$array = Array();

		$rs = $this->conn->OpenRecordset($sql);
		while ($row = $this->conn->FetchRow($rs)){
			$array[] = $row[0];
		}

		//Retorna el Arreglo de VO
		return $array;
	}

    /**
	 * Retorna la información para opcion QUE
	 * @access public
     * @param string $cond
	 * @return array
	 */
	function getInfoQue($caso,$cond){

		$ps = array();

        $tabla = 'unicef_producto_awp';
        $col_id = ($caso != 'fuente_pba') ? "id_$caso" : 'id_fuente';

        $sql = "SELECT DISTINCT($col_id) FROM ".$tabla."_".$caso." JOIN ".$tabla." AS p_awp USING($this->columna_id)";

        if ($caso != 'socio')
            $sql .= " LEFT JOIN ".$tabla."_socio USING ($this->columna_id)";

        if ($caso != 'fuente_pba')
            $sql .= " LEFT JOIN ".$tabla."_fuente_pba USING ($this->columna_id) LEFT JOIN unicef_fuente_pba USING(id_fuente)";
        else if ($caso == 'fuente_pba')
            $sql .= " LEFT JOIN unicef_fuente_pba USING(id_fuente)";

        $sql .= " 
            LEFT JOIN unicef_actividad_awp AS act USING (id_actividad) LEFT JOIN unicef_producto_cpap AS p_cpap on act.id_producto=p_cpap.id_producto
            LEFT JOIN unicef_resultado_cpd AS result USING (id_resultado) LEFT JOIN unicef_sub_componente AS sub_c USING(id_sub_componente)
        ";

        if ($cond != '')    $sql .= " WHERE $cond AND $col_id IS NOT NULL";
            
        //echo $sql;

        $rs = $this->conn->OpenRecordset($sql);
        while ($row = $this->conn->FetchRow($rs)){
            if(!in_array($row[0],$ps))    $ps[] = $row[0];
        }
        return $ps;
	}
    
    /**
	 * Retorna los productos que cumplen con la condición - Join en todo el arbol
     * @access public
     * @param string $cond
	 * @return array
	 */
	function getTreeIDReporte($cond){
		
        $ps = array();

        $tabla = $this->tabla;

        $sql = "SELECT DISTINCT(p_awp.$this->columna_id), sub_c.nombre, res.nombre, p_cpap.nombre, act.nombre, act.id_estado, comp.codigo FROM $this->tabla as p_awp";

        $sql .= " 
            LEFT JOIN ".$tabla."_socio USING ($this->columna_id) LEFT JOIN ".$tabla."_fuente_pba USING ($this->columna_id) LEFT JOIN unicef_fuente_pba USING (id_fuente)
            LEFT JOIN unicef_actividad_awp AS act USING (id_actividad) LEFT JOIN unicef_producto_cpap AS p_cpap on act.id_producto=p_cpap.id_producto
            LEFT JOIN unicef_resultado_cpd AS res USING (id_resultado) LEFT JOIN unicef_sub_componente AS sub_c USING(id_sub_componente)
            LEFT JOIN unicef_componente AS comp USING (id_componente) 
            WHERE
        ";

        if (strlen($cond) > 0)  $sql .= " $cond AND ";
        $sql .= "p_awp.$this->columna_id IS NOT NULL";

        $sql .= ' ORDER BY comp.id_componente,sub_c.id_componente,res.id_sub_componente,p_cpap.id_resultado,act.id_producto';

        //echo $sql;

        $rs = $this->conn->OpenRecordset($sql);
		while ($row = $this->conn->FetchRow($rs)){
            $ps['id'][] = $row[0];
            $ps['sub_c'][] = $row[1];
            $ps['res'][] = $row[2];
            $ps['p_cpap'][] = $row[3];
            $ps['act'][] = $row[4];
            $ps['act_estado'][] = $row[5];
            $ps['comp'][] = $row[6];
        }

		return $ps;

	}

    /**
	 * Retorna los deptos de cobertura opcion DONDE
	 * @access public
     * @param string $cond
	 * @return int
	 */
	function getNumMaxDeptosDonde($cond){

		$ps = array();
        $caso = 'depto';

        $tabla = 'unicef_producto_awp';
        $col_id = 'id_producto';

        $sql = "SELECT count(id_depto) as num FROM ".$tabla."_".$caso." JOIN ".$tabla." AS p_awp USING($col_id)";

        $sql .= " LEFT JOIN unicef_actividad_awp AS act USING (id_actividad) LEFT JOIN unicef_producto_cpap AS p_cpap on act.id_producto=p_cpap.id_producto
                  LEFT JOIN unicef_resultado_cpd AS result USING (id_resultado) LEFT JOIN unicef_sub_componente AS sub_c USING(id_sub_componente)";

        if ($cond != '')    $sql .= ' WHERE '.$cond;
        $sql .= " GROUP BY p_awp.$this->columna_id ORDER BY num DESC LIMIT 0,1";
            
        //echo $sql;

        $rs = $this->conn->OpenRecordset($sql);
        $row = $this->conn->FetchRow($rs);
        
        return $row[0];
	}

	/**
	 * Lista los ProductoAwp que cumplen la condición en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarán los ProductoAwp, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del ProductoAwp que será selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condición que deben cumplir los ProductoAwp y que se agrega en el SQL statement.
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
	 * Carga un VO de ProductoAwp con los datos de la consulta
	 * @access public
	 * @param object $vo VO de ProductoAwp que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de ProductoAwp con los datos
	 */			
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};
		$vo->id_actividad = $Result->id_actividad;
		$vo->id_funcionario = $this->getValuesRel($vo->id,'funcionario');
		$vo->codigo = $Result->codigo;
		$vo->nombre = $Result->nombre;
		$vo->cobertura = $Result->cobertura;
		$vo->aliados = $Result->aliados;
		$vo->indigena = $Result->indigena;
		$vo->afro = $Result->afro;
		$vo->equidad_genero = $Result->equidad_genero;
		$vo->participacion = $Result->participacion;
		$vo->prevencion = $Result->prevencion;
		$vo->movilizacion = $Result->movilizacion;
		$vo->id_presupuesto_desc = $this->getValuesRel($vo->id,'presupuesto_desc');
		$vo->funded = $Result->funded;
		$vo->unfunded = $Result->unfunded;
		$vo->cronograma_1_tri = $Result->cronograma_1_tri;
		$vo->cronograma_2_tri = $Result->cronograma_2_tri;
		$vo->cronograma_3_tri = $Result->cronograma_3_tri;
		$vo->cronograma_4_tri = $Result->cronograma_4_tri;
		
        $vo->id_donante = $this->getValuesRel($vo->id,'donante');
		$vo->id_socio_implementador = $this->getValuesRel($vo->id,'socio');
		$vo->id_fuente_funded = $this->getFuente($vo->id,'funded');
		$vo->fuente_funded_valor = $this->getFuenteValor($vo->id,'funded');
		$vo->fuente_unfunded_valor = $this->getFuenteValor($vo->id,'unfunded');
		$vo->id_fuente_unfunded = $this->getFuente($vo->id,'unfunded');
        $vo->aaaa = $Result->aaaa;
    
        $tmp = $this->getPresupuesto('cop',$vo->id);
        if (isset($tmp['valor']))   $vo->presupuesto_cop = $tmp['valor'];

        $tmp = $this->getPresupuesto('ex',$vo->id);
        if (isset($tmp['valor']))   $vo->presupuesto_ex = $tmp['valor'];
        if (isset($tmp['id_mon']))   $vo->id_mon_ex = $tmp['id_mon'];
        
        // Localizacion
        if ($vo->cobertura != 'N'){
            $sql = "SELECT id_depto FROM unicef_producto_awp_depto WHERE $this->columna_id = $vo->id";
            $rs = $this->conn->OpenRecordset($sql);
            while ($row = $this->conn->FetchRow($rs)){
                $vo->id_depto[] = $row[0];
            }
            
            if ($vo->cobertura == 'M'){
                $sql = "SELECT id_mun FROM unicef_producto_awp_mun WHERE $this->columna_id = $vo->id";
                $rs = $this->conn->OpenRecordset($sql);
                while ($row = $this->conn->FetchRow($rs)){
                    $vo->id_mun[] = $row[0];
                }
            }
        }

		return $vo;
	}

    /**
     * Retorna el presupuesto en moneda nacional o extranjera
     * @access public
     * @param string $caso Nacional=cop o Extranjera=ex
     * @param int $id ID del producto AWP
     * @return array
     */
	function getPresupuesto($caso,$id){
        
        $array = array();

        switch ($caso){
            case 'cop':
                $sql = "SELECT valor,id_mon FROM ".$this->tabla."_presupuesto WHERE $this->columna_id = $id AND id_mon = 2";
            break;

            case 'ex':
                $sql = "SELECT valor,id_mon FROM ".$this->tabla."_presupuesto WHERE $this->columna_id = $id AND id_mon != 2";
            break;
        }

		$rs = $this->conn->OpenRecordset($sql);
		
        if ($row = $this->conn->FetchRow($rs)){
			$array['id_mon'] = $row[1];
			$array['valor'] = $row[0];
		}

        return $array;
	}

    /**
     * Consulta el valor de datos relacionados
     * @access public
     * @param int $id ID del producto AWP
     * @param string $tabla nombre extra tabla
     * @return array
     */
	function getValuesRel($id,$tabla){
        
        $array = array();
        $sql = "SELECT id_$tabla FROM ".$this->tabla."_$tabla WHERE $this->columna_id = $id";
		$rs = $this->conn->OpenRecordset($sql);

        while ($row = $this->conn->FetchRow($rs)){
            $array[] = $row[0];
        }
        
        return $array;
    }   

    /**
     * Consulta las fuentes
     * @access public
     * @param int $id ID del producto AWP
     * @param string $caso funded,unfunded
     * @return array
     */
	function getFuente($id,$caso){
        
        $array = array();
        $sql = "SELECT id_fuente FROM ".$this->tabla."_fuente_pba WHERE $this->columna_id = $id";
        if ($caso == 'funded')  $sql .= ' AND funded = 1';
        else                    $sql .= ' AND unfunded = 1';

		$rs = $this->conn->OpenRecordset($sql);

        while ($row = $this->conn->FetchRow($rs)){
            $array[] = $row[0];
        }
        
        return $array;
    }   
    
    /**
     * Consulta los aportes fuentes
     * @access public
     * @param int $id ID del producto AWP
     * @param string $caso funded,unfunded
     * @return array
     */
	function getFuenteValor($id,$caso){
        
        $array = array();
        $sql = "SELECT id_fuente,valor FROM ".$this->tabla."_fuente_pba WHERE $this->columna_id = $id";
        if ($caso == 'funded')  $sql .= ' AND funded = 1';
        else                    $sql .= ' AND unfunded = 1';

		$rs = $this->conn->OpenRecordset($sql);

        while ($row = $this->conn->FetchRow($rs)){
            $array[$row[0]] = $row[1];
        }
        
        return $array;
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
	 * Inserta un ProductoAwp en la B.D.
	 * @access public
	 * @param object $subcat_vo VO de ProductoAwp que se va a insertar
	 */		
	function Insertar($vo){
		
        //CONSULTA SI YA EXISTE
		$tmp = $this->GetAllArray($this->columna_nombre." = '".$vo->nombre."' AND id_actividad = $vo->id_actividad AND aaaa = $vo->aaaa AND codigo = '$vo->codigo'");
		if (count($tmp) == 0){
			$sql = "INSERT INTO $this->tabla (id_actividad,nombre,codigo,cobertura,aliados,aaaa,funded,unfunded,indigena,afro,equidad_genero,participacion,prevencion,movilizacion,cronograma_1_tri,cronograma_2_tri,cronograma_3_tri,cronograma_4_tri) VALUES ($vo->id_actividad,'$vo->nombre','$vo->codigo','$vo->cobertura','$vo->aliados',$vo->aaaa,$vo->funded,$vo->unfunded,$vo->indigena,$vo->afro,$vo->equidad_genero,$vo->participacion,$vo->prevencion,$vo->movilizacion,$vo->cronograma_1_tri,$vo->cronograma_2_tri,$vo->cronograma_3_tri,$vo->cronograma_2_tri)";
            //echo $sql;
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
	 * Inserta los valores del indice de un ProductoAwp en la B.D.
	 * @access public
	 * @param object $vo VO de ProductoAwp
	 */		
	function InsertarTablasUnion($vo){
        
        // Funcionarios
        foreach ($vo->id_funcionario as $id_f){
            $sql = "INSERT INTO ".$this->tabla."_funcionario ($this->columna_id,id_funcionario) VALUES ($vo->id,$id_f)"; 
            $this->conn->Execute($sql);
        }

        // Socios
        foreach ($vo->id_socio_implementador as $id_s){
            $sql = "INSERT INTO ".$this->tabla."_socio ($this->columna_id,id_socio) VALUES ($vo->id,$id_s)"; 
            $this->conn->Execute($sql);
        }
        
        // Donantes
        /*
        foreach ($vo->id_donante as $id_s){
            $sql = "INSERT INTO ".$this->tabla."_donante ($this->columna_id,id_donante) VALUES ($vo->id,$id_s)"; 
            $this->conn->Execute($sql);
        }
        */
        
        // Fuentes FUNDED
        foreach ($vo->id_fuente_funded as $id_f){
            $valor = $vo->fuente_funded_valor[$id_f];
            $sql = "INSERT INTO ".$this->tabla."_fuente_pba ($this->columna_id,id_fuente,valor,funded,unfunded) VALUES ($vo->id,$id_f,$valor,1,0)"; 
            $this->conn->Execute($sql);
        }

        // Fuentes UNFUNDED
        foreach ($vo->id_fuente_unfunded as $id_f){
            $valor = $vo->fuente_unfunded_valor[$id_f];
            $sql = "INSERT INTO ".$this->tabla."_fuente_pba ($this->columna_id,id_fuente,valor,funded,unfunded) VALUES ($vo->id,$id_f,$valor,0,1)"; 
            $this->conn->Execute($sql);
        }

        // Localizacion
        if (!in_array($vo->cobertura,array('N','I','NA'))){
            foreach ($vo->id_depto as $divipola){
                $sql = "INSERT INTO ".$this->tabla."_depto ($this->columna_id,id_depto) VALUES ($vo->id,'$divipola')"; 
                $this->conn->Execute($sql);
                //echo "<br />$sql";
            }
            
            if ($vo->cobertura == 'M'){
                foreach ($vo->id_mun as $divipola){
                    $sql = "INSERT INTO ".$this->tabla."_mun ($this->columna_id,id_mun) VALUES ($vo->id,'$divipola')";
                    $this->conn->Execute($sql);
                    //echo "<br />$sql";
                }
            }
        }

        // Presupuesto
        if ($vo->presupuesto_cop != ''){ 
            $sql = "INSERT INTO ".$this->tabla."_presupuesto (id_producto,id_mon,valor) VALUES ($vo->id,2,$vo->presupuesto_cop)";
            $this->conn->Execute($sql);
        }
        
        if ($vo->presupuesto_ex != ''){ 
            $sql = "INSERT INTO ".$this->tabla."_presupuesto (id_producto,id_mon,valor) VALUES ($vo->id,$vo->id_mon_ex,$vo->presupuesto_ex)";
            $this->conn->Execute($sql);
        }
        
        // Presupuesto Desc
        foreach ($vo->id_presupuesto_desc as $id_p){
            $sql = "INSERT INTO ".$this->tabla."_presupuesto_desc ($this->columna_id,id_presupuesto_desc) VALUES ($vo->id,$id_p)"; 
            $this->conn->Execute($sql);
        }

    }

	/**
	 * Actualiza un ProductoAwp en la B.D.
	 * @access public
	 * @param object $vo VO de ProductoAwp que se va a actualizar
	 */		
	function Actualizar($vo){
        $sql = "UPDATE $this->tabla SET 
            id_actividad = $vo->id_actividad,
            codigo = '$vo->codigo',
            nombre = '$vo->nombre',
            cobertura = '$vo->cobertura',
            aliados = '$vo->aliados',
            aaaa = '$vo->aaaa',
            indigena = $vo->indigena,
            afro = $vo->afro,
            equidad_genero = $vo->equidad_genero,
            participacion = $vo->participacion,
            prevencion = $vo->prevencion,
            movilizacion = $vo->movilizacion,
            funded = $vo->funded,
            unfunded = $vo->unfunded,
            cronograma_1_tri = $vo->cronograma_1_tri,
            cronograma_2_tri = $vo->cronograma_2_tri,
            cronograma_3_tri = $vo->cronograma_3_tri,
            cronograma_4_tri = $vo->cronograma_4_tri
		    
            WHERE ".$this->columna_id." = ".$vo->id;
            
            $this->BorrarTablasUnion($vo->id);
            $this->InsertarTablasUnion($vo);

		$this->conn->Execute($sql);

	}

	/**
	 * Borra un ProductoAwp en la B.D.
	 * @access public
	 * @param int $id ID del ProductoAwp que se va a borrar de la B.D
	 */	
	function Borrar($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

        $this->BorrarTablasUnion($id);

	}

	/**
	 * Borra las tabla de union de un ProductoAwp en la B.D.
	 * @access public
	 * @param int $id ID del ProductoAwp que se va a borrar de la B.D
	 */	
	function BorrarTablasUnion($id){
        
        $tablas = array('donante','socio','depto','mun','presupuesto','fuente_pba','funcionario','presupuesto_desc');
        
        foreach ($tablas as $tabla){
            $sql = "DELETE FROM ".$this->tabla."_".$tabla." WHERE ".$this->columna_id." = ".$id;
            $this->conn->Execute($sql);
        }
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

		$tabla_rel = 'unicef_convenio';
		$col_id = $this->columna_id;
		
		$sql = "SELECT count($col_id) FROM $tabla_rel WHERE $col_id = $id";
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$r = ($row[0] > 0) ? true : false;

		return $r;

	}
}

?>
