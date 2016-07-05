<?php
/**
 * DAO de Convenio
 *
 * Contiene los métodos de la clase Convenio 
 * @author Ruben A. Rojas C.
 */

Class UnicefConvenioDAO {

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
	function UnicefConvenioDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "unicef_convenio";
		$this->columna_id = "id_convenio";
		$this->columna_nombre = "nombre";
		$this->columna_order = "nombre";
	}

	/**
	 * Consulta los datos de un Convenio
	 * @access public
	 * @param int $id ID del Convenio
	 * @return VO
	 */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$vo = New UnicefConvenio();

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
	* Consulta el mayor año con informacion de inicio o finalizacion
	* @access public
    * @param string $caso ini,fin
	* @return int
	*/
	function GetMaxAAAA($caso){
		$sql = "SELECT MAX(YEAR(fecha_$caso) FROM ".$this->tabla;
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
			$vo = New UnicefConvenio();
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
	 * Retorna los convenios que cumplen con la condición - Join en todo el arbol
     * @access public
     * @param string $cond
     * @param string $mdgd Nal, Deptal, Mpal
     * @param int $id_depto_mpio ID del departamento o del mpio
	 * @return array
	 */
	function getTreeIDReporte($cond,$mdgd='nal',$id_depto_mpio=0){
		
        $ps = array();

        $tabla = $this->tabla;

        $sql = "SELECT DISTINCT(conv.$this->columna_id), sub_c.nombre, res.nombre, p_cpap.nombre, act.nombre, comp.nombre FROM $this->tabla as conv";

        if ($mdgd == 'deptal' && $id_depto_mpio > 0){
            $sql .= " LEFT JOIN ".$tabla."_depto USING ($this->columna_id)";
        }
        
        if ($mdgd == 'mpal' && $id_depto_mpio > 0){
            $sql .= " LEFT JOIN ".$tabla."_mun USING ($this->columna_id)";
        }

        $sql .= " 
            LEFT JOIN ".$tabla."_socio USING ($this->columna_id) LEFT JOIN ".$tabla."_avance_fuente_pba USING ($this->columna_id) LEFT JOIN unicef_fuente_pba USING (id_fuente)
            LEFT JOIN unicef_actividad_awp AS act USING (id_actividad) LEFT JOIN unicef_producto_cpap AS p_cpap on act.id_producto=p_cpap.id_producto
            LEFT JOIN unicef_resultado_cpd AS res USING (id_resultado) LEFT JOIN unicef_sub_componente AS sub_c USING(id_sub_componente)
            LEFT JOIN unicef_componente AS comp USING (id_componente) 
            WHERE
        ";

        if (strlen($cond) > 0)  $sql .= " $cond AND ";

        $sql .= "conv.$this->columna_id IS NOT NULL";

        $sql .= ' ORDER BY comp.id_componente,sub_c.id_componente,res.id_sub_componente,p_cpap.id_resultado,act.id_producto';

        //echo $sql;

        $rs = $this->conn->OpenRecordset($sql);
		while ($row = $this->conn->FetchRow($rs)){
            $ps['id'][] = $row[0];
            $ps['sub_c'][] = $row[1];
            $ps['res'][] = $row[2];
            $ps['p_cpap'][] = $row[3];
            $ps['act'][] = $row[4];
            $ps['comp'][] = $row[5];
        }

		return $ps;

	}
    
    /**
	 * Retorna la informacion para opcion QUE
	 * @access public
     * @param string $cond
	 * @return array
	 */
	function getInfoQue($caso,$cond){

		$ps = array();

        $tabla = 'unicef_convenio';
        if ($caso == 'fuente_pba')  $col_id = 'id_fuente';
        else if ($caso == 'avance') $col_id = 'fecha';
        else                        $col_id = "id_$caso";

        $sql = "SELECT DISTINCT($col_id) FROM ".$tabla."_".$caso." JOIN ".$tabla." USING($this->columna_id)";
        
        if ($caso != 'socio')
            $sql .= " LEFT JOIN ".$tabla."_socio USING ($this->columna_id)";

        if ($caso != 'fuente_pba')
            $sql .= " LEFT JOIN ".$tabla."_avance_fuente_pba USING ($this->columna_id) LEFT JOIN unicef_fuente_pba USING(id_fuente)";
        else if ($caso == 'fuente_pba')
            $sql .= " LEFT JOIN unicef_fuente_pba USING(id_fuente)";

        $sql .= " LEFT JOIN unicef_actividad_awp AS act USING (id_actividad) LEFT JOIN unicef_producto_cpap AS p_cpap on act.id_producto=p_cpap.id_producto
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
	 * Retorna el numero de registros por cobertura
	 * @access public
     * @param $mdgd deptal - mpal
     * @param $ids ID de Deptos o Mpios separados por ,
	 * @return int
	 */
	function getIdByCobertura($mdgd,$ids,$cond){

		$ps = array();
        $caso = ($mdgd == 'deptal') ? 'depto' : 'mun';

        $tabla = $this->tabla;
        $col_id = $this->columna_id;
        
        if ($mdgd == 'nal'){
            $sql = "SELECT DISTINCT(conv.$this->columna_id) FROM $tabla conv";
        }
        else{
            $sql = "SELECT DISTINCT(conv.$this->columna_id) FROM $tabla conv JOIN ".$tabla."_".$caso." USING($col_id)";
        }

        $sql .= " 
            LEFT JOIN ".$tabla."_socio USING ($this->columna_id) LEFT JOIN ".$tabla."_avance_fuente_pba USING ($this->columna_id) LEFT JOIN unicef_fuente_pba USING (id_fuente)
            LEFT JOIN unicef_actividad_awp AS act USING (id_actividad) LEFT JOIN unicef_producto_cpap AS p_cpap on act.id_producto=p_cpap.id_producto
            LEFT JOIN unicef_resultado_cpd AS result USING (id_resultado) LEFT JOIN unicef_sub_componente AS sub_c USING(id_sub_componente)
            ";

        if ($mdgd == 'nal'){
            $sql .= " WHERE cobertura = 'N' ";
        }
        else{
            $sql .= " WHERE  id_".$caso." IN (".$ids.")";
        }

        if ($cond != '')    $sql .= ' AND '.$cond;

        //$sql .= ' ORDER BY sub_c.id_componente,result.id_sub_componente,p_cpap.id_resultado,act.codigo';

        //echo $sql;

        $rs = $this->conn->OpenRecordset($sql);
		while ($row = $this->conn->FetchRow($rs)){
            $ps[] = $row[0] ;
        }

		return $ps;
	}
	
    /**
	 * Lista los Convenio que cumplen la condición en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarán los Convenio, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del Convenio que será selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condición que deben cumplir los Convenio y que se agrega en el SQL statement.
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
	 * Carga un VO de Convenio con los datos de la consulta
	 * @access public
	 * @param object $vo VO de Convenio que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de Convenio con los datos
	 */			
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->{$this->columna_id};
		$vo->id_funcionario = $Result->id_funcionario;
		$vo->id_estado = $Result->id_estado;
		$vo->id_actividad = $Result->id_actividad;
		$vo->id_socio_implementador = $this->getSocio($vo->id);
		$vo->codigo = $Result->codigo;
		$vo->nombre = $Result->nombre;
		$vo->aliados = $Result->aliados;
		$vo->fecha_ini = $Result->fecha_ini;
		$vo->fecha_fin = $Result->fecha_fin;
		$vo->duracion_meses = $Result->duracion_meses;
		$vo->numero_avances = $Result->numero_avances;
		$vo->cobertura = $Result->cobertura;
        $vo->fecha_update = $Result->fecha_update;

        $tmp = $this->getPresupuesto('valor_total','cop',$vo->id);
        if (isset($tmp['valor']))    $vo->presupuesto_cop = $tmp['valor'];

        $tmp = $this->getPresupuesto('valor_total','ex',$vo->id);
        if (isset($tmp['valor']))    $vo->presupuesto_ex = $tmp['valor'];
        if (isset($tmp['id_mon']))   $vo->id_mon_ex = $tmp['id_mon'];
        
        $tmp = $this->getPresupuesto('aporte_unicef','cop',$vo->id);
        if (isset($tmp['valor']))    $vo->aporte_unicef_cop = $tmp['valor'];

        $tmp = $this->getPresupuesto('aporte_unicef','ex',$vo->id);
        if (isset($tmp['valor']))    $vo->aporte_unicef_ex = $tmp['valor'];
        if (isset($tmp['id_mon']))   $vo->id_mon_ex_aporte_unicef = $tmp['id_mon'];
        
        $tmp = $this->getPresupuesto('otros_fondos','cop',$vo->id);
        if (isset($tmp['valor']))       $vo->otros_fondos_cop = $tmp['valor'];
        $vo->id_donante_otros_fondos = $this->getDonante('otros_fondos',$vo->id);
        $vo->id_fuente_otros_fondos = $this->getFuente('otros_fondos',$vo->id);

        $tmp = $this->getPresupuesto('otros_fondos','ex',$vo->id);
        if (isset($tmp['valor']))    $vo->otros_fondos_ex = $tmp['valor'];
        if (isset($tmp['id_mon']))   $vo->id_mon_ex_otros_fondos = $tmp['id_mon'];
		
        $tmp = $this->getAvances('cop',$vo->id);
        if (isset($tmp['valor']))       $vo->avances_cop = $tmp['valor'];
        if (isset($tmp['fecha']))       $vo->avances_fecha = $tmp['fecha'];

        $tmp = $this->getAvances('ex',$vo->id);
        if (isset($tmp['valor']))    $vo->avances_ex = $tmp['valor'];
        if (isset($tmp['id_mon']))   $vo->id_mon_ex_avances = $tmp['id_mon'];

        //if (count($vo->avances_fecha) == 0) $vo->avances_fecha = $tmp['fecha'];
        if (isset($tmp['fecha'])) $vo->avances_fecha = $tmp['fecha'];

        // Localizacion
        if ($vo->cobertura != 'N'){
            $sql = "SELECT id_depto FROM unicef_convenio_depto WHERE $this->columna_id = $vo->id";
            $rs = $this->conn->OpenRecordset($sql);
            while ($row = $this->conn->FetchRow($rs)){
                $vo->id_depto[] = $row[0];
            }
            
            if ($vo->cobertura == 'M'){
                $sql = "SELECT id_mun FROM unicef_convenio_mun WHERE $this->columna_id = $vo->id";
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
     * @param string $caso Valor total o aporte UNICEF
     * @param string $moneda Nacional=cop o Extranjera=ex
     * @param int $id ID del convenio
     * @return array
     */
	function getPresupuesto($caso,$moneda,$id){
        
        $array = array();

        $sql = "SELECT valor,id_mon FROM ".$this->tabla."_$caso WHERE $this->columna_id = $id ";
        
        switch ($moneda){
            case 'cop':
                $sql .= "AND id_mon = 2";
            break;

            case 'ex':
                $sql .= "AND id_mon != 2";
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
     * Consulta los socios
     * @access public
     * @param int $id ID del actividad AWP
     * @return array
     */
	function getSocio($id){
        
        $array = array();
        $sql = "SELECT id_socio FROM ".$this->tabla."_socio WHERE $this->columna_id = $id";
		$rs = $this->conn->OpenRecordset($sql);

        while ($row = $this->conn->FetchRow($rs)){
            $array[] = $row[0];
        }
        
        return $array;
    }   
    
    /**
     * Consulta las fuentes
     * @access public
     * @param string $caso Otros fondos, Avances
     * @param int $id ID del convenio o del avance
     * @return array
     */
	function getFuente($caso,$id){
        
        $array = array();

        $sql = "SELECT id_fuente FROM ".$this->tabla."_".$caso."_fuente_pba WHERE $this->columna_id = $id";
		$rs = $this->conn->OpenRecordset($sql);

        while ($row = $this->conn->FetchRow($rs)){
            $array[] = $row[0];
        }
        
        return $array;
    } 

    /**
     * Consulta los donantes
     * @access public
     * @param string $caso Otros fondos, Avances
     * @param int $id ID del convenio
     * @return array
     */
	function getDonante($caso,$id){
        
        $array = array();

        $sql = "SELECT id_donante FROM ".$this->tabla."_".$caso."_donante WHERE $this->columna_id = $id";
		$rs = $this->conn->OpenRecordset($sql);

        while ($row = $this->conn->FetchRow($rs)){
            $array[] = $row[0];
        }
        
        return $array;
    } 

    /**
     * Consulta las fuentes por avance
     * @access public
     * @param string $avance # del avance
     * @param int $id ID del convenio
     * @return array
     */
	function getFuenteAvance($avance,$id){
        
        $array = array();

        $sql = "SELECT avance,id_fuente FROM ".$this->tabla."_avance_fuente_pba WHERE $this->columna_id = $id AND avance = $avance ";
		$rs = $this->conn->OpenRecordset($sql);

        while ($row = $this->conn->FetchRow($rs)){
            $array[] = $row[1];
        }
        
        return $array;
    } 

    /**
     * Consulta los donantes por avance
     * @access public
     * @param string $avance # del avance
     * @param int $id ID del convenio
     * @return array
     */
	function getDonanteAvance($avance,$id){
        
        $array = array();

        $sql = "SELECT avance,id_donante FROM ".$this->tabla."_avance_donante WHERE $this->columna_id = $id AND avance = $avance ";
		$rs = $this->conn->OpenRecordset($sql);

        while ($row = $this->conn->FetchRow($rs)){
            $array[] = $row[1];
        }
        
        return $array;
    } 

    /**
     * Retorna los avances de un convenio en moneda nacional y extranjera
     * @access public
     * @param string $moneda Nacional=cop o Extranjera=ex
     * @param int $id ID del convenio
     * @return array
     */
	function getAvances($moneda,$id){
        
        $array = array();

        $sql = "SELECT * FROM ".$this->tabla."_avance WHERE $this->columna_id = $id ";
        
        switch ($moneda){
            case 'cop':
                $sql .= "AND id_mon = 2";
            break;

            case 'ex':
                $sql .= "AND id_mon != 2";
            break;
        }

        $sql .= ' ORDER BY id_avance';

		$rs = $this->conn->OpenRecordset($sql);
		
        while ($row = $this->conn->FetchObject($rs)){
			$array['id_mon'][] = $row->id_mon;
			$array['valor'][] = $row->valor;
			$array['fecha'][] = $row->fecha;
		}
        
        return $array;

	}

    /**
     * Retorna la información del avance de un convenio dado el ID del avance
     * @access public
     * @param int $id ID del avance
     * @return array
     */
	function getAvanceById($id){
        
        $array = array();

        $sql = "SELECT * FROM ".$this->tabla."_avance WHERE id_avance = $id ";
        
		$rs = $this->conn->OpenRecordset($sql);
		
        while ($row = $this->conn->FetchObject($rs)){
			$array['valor'] = $row->valor;
			$array['fecha'] = $row->fecha;
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
	 * Inserta un Convenio en la B.D.
	 * @access public
	 * @param object $subcat_vo VO de Convenio que se va a insertar
	 */		
	function Insertar($vo){
		
        //CONSULTA SI YA EXISTE
		$tmp = $this->GetAllArray($this->columna_nombre." = '".$vo->nombre."' AND id_actividad = $vo->id_actividad");
		if (count($tmp) == 0){
			$sql = "INSERT INTO $this->tabla (id_funcionario,id_estado,id_actividad,codigo,nombre,aliados,fecha_ini,fecha_fin,duracion_meses,numero_avances,cobertura,fecha_update) VALUES ($vo->id_funcionario,$vo->id_estado,$vo->id_actividad,'$vo->codigo','$vo->nombre','$vo->aliados','$vo->fecha_ini','$vo->fecha_fin',$vo->duracion_meses,$vo->numero_avances,'$vo->cobertura',now())"; 
			$this->conn->Execute($sql);
            
            $vo->id = $this->GetMaxID();
            $this->InsertarTablasUnion($vo);

			echo "Registro insertado con &eacute;xito!";
		}
		else{
			echo "Error - Ya existe un convenio con el mismo codigo";
		}

	}

	/**
	 * Inserta los valores del indice de un Convenio en la B.D.
	 * @access public
	 * @param object $vo VO de Convenio
	 */		
	function InsertarTablasUnion($vo){

        // Presupuesto
        if ($vo->presupuesto_cop != ''){ 
            $sql = "INSERT INTO ".$this->tabla."_valor_total (id_convenio,id_mon,valor) VALUES ($vo->id,2,$vo->presupuesto_cop)";
            $this->conn->Execute($sql);
        }
        
        if ($vo->presupuesto_ex != ''){ 
            $sql = "INSERT INTO ".$this->tabla."_valor_total (id_convenio,id_mon,valor) VALUES ($vo->id,$vo->id_mon_ex,$vo->presupuesto_ex)";
            $this->conn->Execute($sql);
        }
        
        // Aporte Unicef
        if ($vo->aporte_unicef_cop != ''){ 
            $sql = "INSERT INTO ".$this->tabla."_aporte_unicef (id_convenio,id_mon,valor) VALUES ($vo->id,2,$vo->aporte_unicef_cop)";
            $this->conn->Execute($sql);
        }
        
        if ($vo->aporte_unicef_ex != ''){ 
            $sql = "INSERT INTO ".$this->tabla."_aporte_unicef (id_convenio,id_mon,valor) VALUES ($vo->id,$vo->id_mon_ex_aporte_unicef,$vo->aporte_unicef_ex)";
            $this->conn->Execute($sql);
        }
        
        // Socios
        foreach ($vo->id_socio_implementador as $id_s){
            $sql = "INSERT INTO ".$this->tabla."_socio ($this->columna_id,id_socio) VALUES ($vo->id,$id_s)"; 
            $this->conn->Execute($sql);
        }
        
		// Otros fondos
        if ($vo->otros_fondos_cop != ''){ 
            $sql = "INSERT INTO ".$this->tabla."_otros_fondos (id_convenio,id_mon,valor) VALUES ($vo->id,2,$vo->otros_fondos_cop)";
            $this->conn->Execute($sql);
        }
        
        if ($vo->otros_fondos_ex != ''){ 
            $sql = "INSERT INTO ".$this->tabla."_otros_fondos (id_convenio,id_mon,valor) VALUES ($vo->id,$vo->id_mon_ex_otros_fondos,$vo->otros_fondos_ex)";
            $this->conn->Execute($sql);
        }

        /*
        // Donantes
        foreach ($vo->id_donante_otros_fondos as $id_d){
            $sql = "INSERT INTO ".$this->tabla."_otros_fondos_donante ($this->columna_id,id_donante) VALUES ($vo->id,$id_d)"; 
            $this->conn->Execute($sql);
        }
        */
        
        // Fuentes
        foreach ($vo->id_fuente_otros_fondos as $id_f){
            $sql = "INSERT INTO ".$this->tabla."_otros_fondos_fuente_pba ($this->columna_id,id_fuente) VALUES ($vo->id,$id_f)"; 
            $this->conn->Execute($sql);
        }

		// Avances
        if (count($vo->avances_cop) > 0){
            $avances = $vo->avances_cop;
            $avances_ex = $vo->avances_ex;
            
            for($a=0;$a<count($avances);$a++)   $id_mon_cop_avances[$a] = 2; // COP
            $id_mon_ex_avances = $vo->id_mon_ex_avances;
        }
        else{
            $avances = $vo->avances_ex;
            $avances_ex = $vo->avances_cop;
            
            for($a=0;$a<count($avances_ex);$a++)   $id_mon_ex_avances[$a] = 2; // COP
            $id_mon_cop_avances = $vo->id_mon_ex_avances;
        }

		foreach ($avances as $i=>$avance){

			$id_donante = (isset($vo->id_donante_avances[$i])) ? $vo->id_donante_avances[$i] : 0;
			$id_fuente = (isset($vo->id_fuente_avances[$i])) ? $vo->id_fuente_avances[$i] : 0;
			$fecha = (isset($vo->avances_fecha[$i])) ? $vo->avances_fecha[$i] : '';

			if ($avance != ''){
				$sql = "INSERT INTO ".$this->tabla."_avance (id_convenio,id_mon,valor,fecha) VALUES ($vo->id,$id_mon_cop_avances[$i],$avance,'$fecha')";
				$this->conn->Execute($sql);
			}
			
			if (isset($avances_ex[$i]) && $avances_ex[$i] != ''){ 
				$sql = "INSERT INTO ".$this->tabla."_avance (id_convenio,id_mon,valor,fecha) VALUES ($vo->id,".$id_mon_ex_avances[$i].",".$avances_ex[$i].",'$fecha')";
				$this->conn->Execute($sql);
			}
            
            /*
            // Donantes
            foreach ($vo->id_donante_avances[$i] as $id_d){
                $sql = "INSERT INTO ".$this->tabla."_avance_donante (id_convenio,avance,id_donante) VALUES ($vo->id,$i,$id_d)"; 
                $this->conn->Execute($sql);
            }
            */
            
            // Fuentes
            foreach ($vo->id_fuente_avances[$i] as $id_f){
                $sql = "INSERT INTO ".$this->tabla."_avance_fuente_pba (id_convenio,avance,id_fuente) VALUES ($vo->id,$i,$id_f)"; 
                $this->conn->Execute($sql);
            }
		}

        // Localizacion
        if ($vo->cobertura != 'N'){
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
    }

	/**
	 * Actualiza un Convenio en la B.D.
	 * @access public
	 * @param object $vo VO de Convenio que se va a actualizar
	 */		
	function Actualizar($vo){
        
		$sql = "UPDATE $this->tabla SET 
			id_funcionario = $vo->id_funcionario,
			id_estado = $vo->id_estado,
			id_actividad = $vo->id_actividad,
			codigo = '$vo->codigo',
			nombre = '$vo->nombre',
			aliados = '$vo->aliados',
			fecha_ini = '$vo->fecha_ini',
			fecha_fin = '$vo->fecha_fin',
			duracion_meses = $vo->duracion_meses,
			numero_avances = $vo->numero_avances,
			cobertura = '$vo->cobertura',
            fecha_update = now()
		
            WHERE ".$this->columna_id." = ".$vo->id;
            
            $this->BorrarTablasUnion($vo->id);
            $this->InsertarTablasUnion($vo);

		$this->conn->Execute($sql);

	}

	/**
	 * Borra un Convenio en la B.D.
	 * @access public
	 * @param int $id ID del Convenio que se va a borrar de la B.D
	 */	
	function Borrar($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

        $this->BorrarTablasUnion($id);

	}

	/**
	 * Borra las tabla de union de un Convenio en la B.D.
	 * @access public
	 * @param int $id ID del Convenio que se va a borrar de la B.D
	 */	
	function BorrarTablasUnion($id){

        //$tablas = array('valor_total','aporte_unicef','socio','avance','avance_fuente_pba','avance_donante','otros_fondos','otros_fondos_fuente_pba','otros_fondos_donante','depto','mun');
        $tablas = array('valor_total','aporte_unicef','socio','avance','avance_fuente_pba','otros_fondos','otros_fondos_fuente_pba','depto','mun');
        
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
		
		$sql = "SELECT sum($col_id) FROM $tabla_rel WHERE $col_id = $id";
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$r = ($row[0] > 0) ? true : false;

		return $r;

	}
    
    /******************************************************************************
     * Genera la ficha PDF de un convenio
     * @param int $id Id del Convenio
     * @access public
     *******************************************************************************/
    function fichaPdf($id){

        //LIBRERIAS
        include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/common/html2pdf_tcpdf/html2pdf.class.php");

        $html = $this->fichaPdfCodigoHTML($id);
        $html2pdf = new HTML2PDF('L','Letter','en', false, 'ISO-8859-1',array(10,10,10,10));
        //$html2pdf->setModeDebug();
        $html2pdf->setDefaultFont('Arial');
        $html2pdf->writeHTML($html);
        $html2pdf->Output("ficha_$id.pdf",'I');
    }

    /******************************************************************************
     * Genera el codigo HTML para la ficha PDF
     * @param int $id ID
     * @access public
     *******************************************************************************/
    function fichaPdfCodigoHTML($id){

        //LIBRERIAS
        include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/common/date.class.php");
        include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/dao/factory.class.php");

        //INICIALIZACION DE VARIABLES
        $date = New Date();
        $estado_dao = FactoryDAO::factory('unicef_estado');
        $act_awp_dao = FactoryDAO::factory('unicef_actividad_awp');
        $conv_dao = FactoryDAO::factory('unicef_convenio');
        $depto_dao = FactoryDAO::factory('depto');
        $mun_dao = FactoryDAO::factory('municipio');
        $moneda_dao = FactoryDAO::factory('moneda');
        $fuente_dao = FactoryDAO::factory('unicef_fuente_pba');
        $donante_dao = FactoryDAO::factory('unicef_donante');

        $vo = $this->Get($id);
        $estado = $estado_dao->Get($vo->id_estado);
        $all_info = $conv_dao->GetTreeIDReporte('');
        $conv = $conv_dao->Get($id);
        $acts = $act_awp_dao->GetAllArray("id_actividad = $conv->id_actividad");

        $hoy = getdate();
        $meses = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Novimebre","Diciembre");
        $hoy = $hoy["mday"]." ".$meses[$hoy["mon"]]." ".$hoy["year"];
        $cobertura_t = array('N'=>'Nacional','D'=>'Departamental','M'=>'Municipal');
                    
        // Cobertura
        $cobertura = '';
        $id_muns = implode(',',$conv->id_mun);

        foreach ($conv->id_depto as $d=>$id_depto){

            $nom_depto = $depto_dao->GetName($id_depto);

            if ($d > 0) $cobertura .= ", ";
            
            $cobertura .= "<b>$nom_depto</b>";

            if ($id_muns != ''){ 
                $id_muns_d = $mun_dao->GetAllArrayID("id_depto = $id_depto AND id_mun IN ($id_muns)",'');

                $cobertura .= ":&nbsp;".$act_awp_dao->getNames('municipio',$id_muns_d);
            }
        }
        
        // Avances
        $avances_nombre = '';
        foreach ($conv->avances_ex as $a=>$avance_valor){
            
            if ($a > 0) $avances_nombre .= '<br />';

            $fecha = $date->Format($conv->avances_fecha[$a],'aaaa-mm-dd','dd-MM-aaaa');
            $avances_nombre .= '<b>Avance '.($a+1).'</b>: $ '.$avance_valor.' ('.$fecha.')';

            $id_fuente_avance = $conv_dao->getFuenteAvance($a,$conv->id);
            foreach ($id_fuente_avance as $id_fuente){
                $fuente = $fuente_dao->Get($id_fuente);
                $avances_nombre .= " - <b>Fuente</b>: $fuente->nombre";

                $donante = $donante_dao->Get($fuente->id_donante);

                $avances_nombre .= "- <b>Donante</b>: $donante->nombre";

            }
        }

        // Presupuesto
        $presu_nombre = $conv->presupuesto_ex;
        
        // Socios
        $socios_nombre = $act_awp_dao->getNames('unicef_socio',$conv->id_socio_implementador);
        //echo "<td>$socios</td>";

        ob_start();
        ?>
        <style type="text/css">
        <!--
        table { width:	100%; }
        td { text-align:left; font-size: 11px; padding: 3px;}
        table.borde{ border: 1px solid black; border-collapse: collapse; }
        table.borde td{ border: 1px solid black; }
        td.col1 { text-align: right; width: 65%; }
        .header{ font-size: 16px; width: 35%; }
        .titulo{ font-size: 18px; font-weight: bold; }
        -->
        </style>
        <?php
        $tmp = explode(' ',$vo->fecha_update);
        $fecha_actua = $date->Format($tmp[0],'aaaa-mm-dd','dd-MM-aaaa','-');
        echo "<page_footer><table><tr>
            <td style='width:90%'>Sistema de Información UNICEF Colombia -  
            Fecha &uacute;ltima actualizaci&oacute;n de la ficha: $fecha_actua</td>
            <td align='right' style='width:10%'>Página [[page_cu]]/[[page_nb]]</td>
        </tr>";
        echo "</table></page_footer>";
        ?>
        <table>
            <tr>
                <td class="header">Ficha Resumen Convenio: <b><?php echo $conv->codigo ?></b><br /><span style="font-size:11px">Fecha Reporte: <?=$hoy?></span></td>
                <td class="col1"><img src="images/unicef/unicef_logo_b.png"></td>
            </tr>
        </table>
        <br />
        <table>
            <tr><td class="titulo"><? echo $conv->nombre ?></td></tr>
        </table>
        <br />
        <table>
        <?php
            echo "<tr><td style='width:100%' colspan='2'><b>Fecha Inicio:</b>&nbsp;$conv->fecha_ini &nbsp;&nbsp;<b>Fecha Finalización:</b>&nbsp;$conv->fecha_fin &nbsp;&nbsp;<b>Duración en meses:</b>&nbsp;$conv->duracion_meses</td></tr>
                  <tr><td style='width:10%'><b>Componente</b></td><td style='width:90%'>".$all_info['comp'][0]."</td></tr>
                  <tr><td style='width:10%'><b>Sub Componente</b></td><td style='width:90%'>".$all_info['sub_c'][0]."</td></tr>
                  <tr><td style='width:10%'><b>Resultado CPD</b></td><td style='width:90%'>".$all_info['res'][0]."</td></tr>
                  <tr><td style='width:10%'><b>Producto CPAP</b></td><td style='width:90%'>".$all_info['p_cpap'][0]."</td></tr>
                  <tr><td style='width:10%'><b>Estado</b></td><td style='width:90%'>$estado->nombre</td></tr>
                  <tr><td style='width:10%'><b>Cobertura</b><br />".$cobertura_t[$conv->cobertura]."</td><td style='width:90%'>$cobertura</td></tr>
                  <tr><td style='width:10%'><b>Avances</b></td><td style='width:90%'>$avances_nombre</td></tr>
                  <tr><td style='width:10%'><b>Presupuesto Total</b></td><td style='width:90%'>$presu_nombre</td></tr>
                  <tr><td style='width:10%'><b>Socios</b></td><td style='width:90%'>$socios_nombre</td></tr>";
        
        echo '</table>';

        // Actividades
        echo '<br /><p><b>ESTE CONVENIO ESTA ASOCIADO A LAS SIGUIENTES ACTIVIDADES AWP</b></p><table>';

        foreach($acts as $a => $act){
            echo "<tr><td style='width:100%'>".($a + 1).". $act->nombre ($act->codigo)</td></tr>";
        }

        echo '</table>';

        $html = ob_get_contents();
        ob_end_clean();

        return $html;

    }
}

?>
