<?php
/**
 * DAO de ActividadAwp
 *
 * Contiene los métodos de la clase ActividadAwp 
 * @author Ruben A. Rojas C.
 */

Class UnicefActividadAwpDAO {

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
	function UnicefActividadAwpDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "unicef_actividad_awp";
		$this->columna_id = "id_actividad";
		$this->columna_nombre = "nombre";
		$this->columna_order = "codigo";
	}

	/**
	 * Consulta los datos de un ActividadAwp
	 * @access public
	 * @param int $id ID del ActividadAwp
	 * @return VO
	 */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$vo = New UnicefActividadAwp();

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
	* Consulta el mayor año con informacion
	* @access public
	* @return int
	*/
	function GetMaxAAAA(){
		$sql = "SELECT MAX(aaaa) FROM ".$this->tabla;
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
			$vo = New UnicefActividadAwp();
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
	 * Lista los ActividadAwp que cumplen la condición en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarán los ActividadAwp, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del ActividadAwp que será selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condición que deben cumplir los ActividadAwp y que se agrega en el SQL statement.
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
	 * Carga un VO de ActividadAwp con los datos de la consulta
	 * @access public
	 * @param object $vo VO de ActividadAwp que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de ActividadAwp con los datos
	 */			
	function GetFromResult ($vo,$Result){
        
        $vo->id = $Result->{$this->columna_id};
        $vo->nombre = $Result->{$this->columna_nombre};
        $vo->id_tema_undaf_1 = $Result->id_tema_undaf_1;
        $vo->id_tema_undaf_2 = $Result->id_tema_undaf_2;
        $vo->id_tema_undaf_3 = $Result->id_tema_undaf_3;
        $vo->id_estado = $Result->id_estado;
        $vo->id_producto = $Result->id_producto;
        $vo->codigo = $Result->codigo;
        $vo->aaaa = $Result->aaaa;
        $vo->fecha_update = $Result->fecha_update;

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
	 * Retorna el numero de registros por cobertura
	 * @access public
     * @param $mdgd deptal - mpal
     * @param $ids ID de Deptos o Mpios separados por ,
	 * @return int
	 */
	function getIdByCobertura($mdgd,$ids,$cond){

		$ps = array();
        $caso = ($mdgd == 'deptal') ? 'depto' : 'mun';

        $tabla = 'unicef_producto_awp';
        $col_id = 'id_producto';
        
        if ($mdgd == 'nal'){
            $sql = "SELECT DISTINCT(act.$this->columna_id) FROM $tabla";
        }
        else{
            $sql = "SELECT DISTINCT(act.$this->columna_id) FROM $tabla JOIN ".$tabla."_".$caso." USING($col_id)";
        }

        $sql .= " 
            RIGHT JOIN ".$tabla."_socio USING (id_producto) RIGHT JOIN ".$tabla."_fuente_pba USING (id_producto) RIGHT JOIN unicef_fuente_pba USING (id_fuente)
            RIGHT JOIN unicef_actividad_awp AS act USING (id_actividad) RIGHT JOIN unicef_producto_cpap AS p_cpap on act.id_producto=p_cpap.id_producto
            RIGHT JOIN unicef_resultado_cpd AS result USING (id_resultado) RIGHT JOIN unicef_sub_componente AS sub_c USING(id_sub_componente)
            ";

        if ($mdgd == 'nal'){
            $sql .= " WHERE cobertura = 'N' ";
        }
        else{
            $sql .= " WHERE  id_".$caso." IN (".$ids.")";
        }

        if ($cond != '')    $sql .= ' AND '.$cond;

        $sql .= ' ORDER BY sub_c.id_componente,result.id_sub_componente,p_cpap.id_resultado,act.codigo';

        //echo $sql;

        $rs = $this->conn->OpenRecordset($sql);
		while ($row = $this->conn->FetchRow($rs)){
            $ps[] = $row[0] ;
        }

		return $ps;
	}

    /**
	 * Retorna el numero de registros dada la condicion
	 * @access public
     * @param string $caso
     * @param string $cond 
	 * @return array
	 */
	function getTreeIDReporte($caso,$cond){

		$ps = array();

        $tabla = 'unicef_producto_awp';
        $col_id = array('sub_c' => 'sub_c.id_sub_componente',
                        'res' => 'res.id_resultado',
                        'p_cpap' => 'p_cpap.id_producto',
                        'act' => 'act.id_actividad',
                        'p_awp' => 'unicef_producto_awp.id_producto'
                );

        $sql = "SELECT DISTINCT($col_id[$caso]) FROM $tabla ";

        $sql .= " 
                  RIGHT JOIN ".$tabla."_socio USING (id_producto) RIGHT JOIN ".$tabla."_fuente_pba USING (id_producto) RIGHT JOIN unicef_fuente_pba USING (id_fuente)
                  RIGHT JOIN unicef_actividad_awp AS act USING (id_actividad) RIGHT JOIN unicef_producto_cpap AS p_cpap on act.id_producto=p_cpap.id_producto
                  RIGHT JOIN unicef_resultado_cpd AS res USING (id_resultado) RIGHT JOIN unicef_sub_componente AS sub_c USING(id_sub_componente)
        ";

        $sql .= " WHERE $cond AND $col_id[$caso] IS NOT NULL";

        $sql .= ' ORDER BY sub_c.id_componente,res.id_sub_componente,p_cpap.id_resultado,act.id_producto';

        $rs = $this->conn->OpenRecordset($sql);
		while ($row = $this->conn->FetchRow($rs)){
            $ps[] = $row[0];
        }

		return $ps;
	}

	/**
	 * Inserta un ActividadAwp en la B.D.
	 * @access public
	 * @param object $subcat_vo VO de ActividadAwp que se va a insertar
	 */		
	function Insertar($vo){
		
        //CONSULTA SI YA EXISTE
		$tmp = $this->GetAllArray($this->columna_nombre." = '".$vo->nombre."' AND id_producto = $vo->id_producto AND aaaa = $vo->aaaa");
		if (count($tmp) == 0){
            
            $sql = "INSERT INTO $this->tabla (id_producto,id_tema_undaf_1,id_tema_undaf_2,id_tema_undaf_3,id_estado,nombre,codigo,aaaa,fecha_update) VALUES ($vo->id_producto,$vo->id_tema_undaf_1,$vo->id_tema_undaf_2,$vo->id_tema_undaf_3,$vo->id_estado,'$vo->nombre','$vo->codigo',$vo->aaaa,now())"; 
            //echo $sql;
            $this->conn->Execute($sql);
			echo "Registro insertado con &eacute;xito!";
		}
		else{
			echo "Error - Ya existe la actividad";
		}

	}

	/**
	 * Actualiza un ActividadAwp en la B.D.
	 * @access public
	 * @param object $vo VO de ActividadAwp que se va a actualizar
	 */		
	function Actualizar($vo){
          $sql = "UPDATE $this->tabla SET 
              id_producto = $vo->id_producto,
              id_tema_undaf_1 = $vo->id_tema_undaf_1,
              id_tema_undaf_2 = $vo->id_tema_undaf_2,
              id_tema_undaf_3 = $vo->id_tema_undaf_3,
              id_estado = $vo->id_estado,
              nombre = '$vo->nombre',
              codigo = '$vo->codigo',
              aaaa = '$vo->aaaa',
              fecha_update = now()
		    
            WHERE ".$this->columna_id." = ".$vo->id;
            
		$this->conn->Execute($sql);

	}

	/**
	 * Borra un ActividadAwp en la B.D.
	 * @access public
	 * @param int $id ID del ActividadAwp que se va a borrar de la B.D
	 */	
	function Borrar($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

	}
    
    /******************************************************************************
     * Genera la ficha PDF de una actividad
     * @param int $id Id de una actividad
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
        //$tema_dao = FactoryDAO::factory('tema');
        $producto_awp_dao = FactoryDAO::factory('unicef_producto_awp');
        $convenio_dao = FactoryDAO::factory('unicef_convenio');
        $depto_dao = FactoryDAO::factory('depto');
        $mun_dao = FactoryDAO::factory('municipio');
        $moneda_dao = FactoryDAO::factory('moneda');

        $vo = $this->Get($id);
        $estado = $estado_dao->Get($vo->id_estado);
        $tree_names = $this->getTreeNames($vo);
        //$tema_1 = $tema_dao->Get($vo->id_tema_undaf_1);
        //$tema_2 = $tema_dao->Get($vo->id_tema_undaf_2);
        //$tema_3 = $tema_dao->Get($vo->id_tema_undaf_3);
        $p_awps = $producto_awp_dao->GetAllArray("id_actividad=$id");
        $convs = $convenio_dao->GetAllArray("id_actividad=$id");


        $hoy = getdate();
        $meses = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Novimebre","Diciembre");
        $hoy = $hoy["mday"]." ".$meses[$hoy["mon"]]." ".$hoy["year"];
        $cobertura = array('N'=>'Nacional','D'=>'Departamental','M'=>'Municipal');

        ob_start();
        ?>
        <style type="text/css">
        <!--
        table { width:	100%; }
        td { text-align:left; font-size: 11px; padding: 3px;}
        table.borde{ border: 1px solid black; border-collapse: collapse; }
        table.borde td{ border: 1px solid black; }
        td.col1 { text-align: right; width: 75%; }
        .header{ font-size: 16px; width: 25%; }
        .titulo{ font-size: 22px; font-weight: bold; }
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
                <td class="header">Ficha Resumen Actividad AWP<br /><span style="font-size:11px">Fecha Reporte: <?=$hoy?></span></td>
                <td class="col1"><img src="images/unicef/unicef_logo_b.png"></td>
            </tr>
        </table>
        <br />
        <table>
            <tr><td class="titulo"><? echo $vo->nombre.' ('.$vo->codigo.')'?></td></tr>
        </table>
        <br />
        <table>
        <?php
            echo "<tr><td style='width:10%'><b>A&ntilde;o</b></td><td style='width:90%'>$vo->aaaa</td></tr>
                  <tr><td style='width:10%'><b>Componente</b></td><td style='width:90%'>".$tree_names['componente']."</td></tr>
                  <tr><td style='width:10%'><b>Sub Componente</b></td><td style='width:90%'>".$tree_names['sub']."</td></tr>
                  <tr><td style='width:10%'><b>Resultado CPD</b></td><td style='width:90%'>".$tree_names['resultado']."</td></tr>
                  <tr><td style='width:10%'><b>Producto CPAP</b></td><td style='width:90%'>".$tree_names['p_cpap']."</td></tr>
                  <tr><td style='width:10%'><b>Estado</b></td><td style='width:90%'>$estado->nombre</td></tr>";
                  /*
                  <tr><td>UNDAF Area Prioritaria</td><td>$tema_1->nombre</td></tr>
                  <tr><td>UNDAF Outcome</td><td>$tema_2->nombre</td></tr>
                  <tr><td>UNDAF Output</td><td>$tema_3->nombre</td></tr>
                  */
        
        echo '</table>';

        // Productos AWP
        echo '<br /><p><b>PRODUCTOS AWP</b></p><table class="borde">';
        echo "<tr><td><b>Producto</b></td><td><b>Socio Implementador</b></td><td><b>Cobertura</b></td><td><b>Monto</b></td></tr>";

        foreach($p_awps as $p_awp){

            $socios = $this->getNames('unicef_socio',$p_awp->id_socio_implementador);
            $id_muns = implode(',',$p_awp->id_mun);

            $presu = '';
            if (strlen($p_awp->presupuesto_ex) > 0){
                $moneda = $moneda_dao->Get($p_awp->id_mon_ex);
                $presu = $moneda->nombre.' '.$p_awp->presupuesto_ex;
            }

            echo "<tr><td style='width:30%'>$p_awp->nombre</td><td style='width:30%'>$socios</td><td style='width:30%'>";
            echo '<b>'.$cobertura[$p_awp->cobertura].'</b><br />';

            foreach ($p_awp->id_depto as $id_depto){

                $nom_depto = $depto_dao->GetName($id_depto);
                echo "<b>$nom_depto</b>";
                
                if ($id_muns != ''){ 
                    $id_muns_d = $mun_dao->GetAllArrayID("id_depto = $id_depto AND id_mun IN ($id_muns)",'');
                
                    echo ':&nbsp;'.$this->getNames('municipio',$id_muns_d).'<br />';
                }
                else    echo ', ';

            }
            echo "</td><td style='width:7%'>$presu</td></tr>";
        }

        echo '</table>';
        
        // Convenios
        echo '<br /><p><b>CONVENIOS</b></p>';

        if (count($convs) > 0){
            echo "<table class='borde'><tr><td><b>Convenio</b></td><td><b>Objetivo</b></td><td><b>Socio Implementador</b></td><td><b>Cobertura</b></td><td><b>Monto</b></td></tr>";

            foreach($convs as $conv){

                $socios = $this->getNames('unicef_socio',$conv->id_socio_implementador);
                $id_muns = implode(',',$conv->id_mun);

                $presu = '';
                if (strlen($conv->presupuesto_ex) > 0){
                    $moneda = $moneda_dao->Get($conv->id_mon_ex);
                    $presu = $moneda->nombre.' '.$conv->presupuesto_ex;
                }

                echo "<tr>
                    <td style='width:8%'>$conv->codigo</td>
                    <td style='width:27%'>$conv->nombre</td>
                    <td style='width:25%'>$socios</td>
                    <td style='width:30%'>";

                echo '<b>'.$cobertura[$conv->cobertura].'</b><br />';

                foreach ($conv->id_depto as $id_depto){

                    $nom_depto = $depto_dao->GetName($id_depto);
                    echo "<b>$nom_depto</b>";
                    
                    if ($id_muns != ''){ 
                        $id_muns_d = $mun_dao->GetAllArrayID("id_depto = $id_depto AND id_mun IN ($id_muns)",'');
                    
                        echo ':&nbsp;'.$this->getNames('municipio',$id_muns_d).'<br />';
                    }
                    else    echo ', ';

                }
                echo "</td><td style='width:7%'>$presu</td></tr>";
            }
        }
        else    echo '<table><tr><td><br />No hay información asociada de convenios</td></tr>';

        echo '</table>';

        $html = ob_get_contents();
        ob_end_clean();

        return $html;

    }
	
    /**
	 * Retorna el nombre de la entidad separados por ,
	 * @access public
	 * @param string $caso Entidad
	 * @param array $ids ID de las entidades
	 * @return string
	 */
	static function getNames($caso,$ids){
        
        $dao = FactoryDAO::factory($caso);
        $txt = '';
        
        foreach($ids as $i=>$id){

            $vo = $dao->Get($id);
            
            if ($i == 0) $txt = $vo->nombre;
            else         $txt .= ",$vo->nombre";
        }

        return $txt;
    }
    
    /**
	 * Retorna el nombre de todos los papas de actividad AWP
	 * @access public
	 * @param object $vo VO de actividad
	 * @return string
	 */
	function getTreeNames($vo){
        
        include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/dao/factory.class.php");
        
        $componente_dao =  FactoryDAO::factory('unicef_componente');
        $sub_dao = FactoryDAO::factory('unicef_sub_componente');
        $resultado_dao = FactoryDAO::factory('unicef_resultado');
        $producto_cpap_dao = FactoryDAO::factory('unicef_producto_cpap');
        $nom_p_cpap = $producto_cpap_dao->GetFieldValue($vo->id_producto,'nombre');
        $id_resultado = $producto_cpap_dao->GetFieldValue($vo->id_producto,'id_resultado');

        $nom_resultado = $resultado_dao->GetFieldValue($id_resultado,'nombre');
        $id_sub_componente = $resultado_dao->GetFieldValue($id_resultado,'id_sub_componente');
        
        $nom_sub = $sub_dao->GetFieldValue($id_sub_componente,'nombre');
        $id_componente = $sub_dao->GetFieldValue($id_sub_componente,'id_componente');
        
        $nom_componente = $componente_dao->GetFieldValue($id_componente,'nombre');

        return array('p_cpap'=>$nom_p_cpap,'resultado'=>$nom_resultado,'sub'=>$nom_sub,'componente'=>$nom_componente);

    }

    /**
	 * Reporte generado desde opcion Info de Mapa
	 * @access public
	 * @param string $nom_depto_mpio Nombre de la ubicacion
	 * @param int $num Numero de actividades en la ubicacion
	 * @param array $ids IDs de las actividades
	 * @param string $mdgd Deptal,Mpal
     * @param $ids ID de Deptos o Mpios separados por ,
	 * @param string $condicion Condición extra
	 */
	function reporteInfoMapa($nom_depto_mpio,$num,$ids,$ids_nal,$mdgd,$id_depto_mpio,$condicion){
        
        //LIBRERIAS
        include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/dao/factory.class.php");
        
        $estado_dao = FactoryDAO::factory('unicef_estado');
        $tema_dao = FactoryDAO::factory('tema');
        $producto_awp_dao = FactoryDAO::factory('unicef_producto_awp');
        $depto_dao = FactoryDAO::factory('depto');
        $mun_dao = FactoryDAO::factory('municipio');
        $moneda_dao = FactoryDAO::factory('moneda');
        $funcionario_dao = FactoryDAO::factory('unicef_funcionario');
        $presupuesto_desc_dao = FactoryDAO::factory('unicef_presupuesto_desc');
        $fuente_dao = FactoryDAO::factory('unicef_fuente_pba');
        $donante_dao = FactoryDAO::factory('unicef_donante');
        $cobertura_t = array('N'=>'Nacional','D'=>'Departamental','M'=>'Municipal','NA'=>'No aplica','I'=>'Interno');

        $html = "<table border='1'>
            <tr>
                <td>$nom_depto_mpio</td>
                <td>$num&nbsp;Proyecto(s)</b></td>
            </tr>
            <tr>
                <td>A&ntilde;o</td>
                <td>Componente</td>
                <td>Sub Componente</td>
                <td>Resultado CPD</td>
                <td>Productos previstos del componente programático (tal como aparecen en el CPAP) Incluir indicadores y metas, tal como aparecen en el CPAP y además especificar metas anuales</td>
                <td>C&oacute;digo</td>
                <td>ACTIVIDADES PLANEADAS  Enumerar todas las actividades planeadas para el 2010 para alcanzar los productos previstos</td>
                <td>Estado</td>";
                /*
                <td>UNDAF Area Prioritaria</td>
                <td>UNDAF Outcome</td>
                <td>UNDAF Output</td>
                */
            $html .= "
                <td>RESULTADOS ESPERADOS</td>
                <td>Cobertura</td>
                <td>Deptos - Mpios</td>
                <td>Socio Implementador</td>
                <td colspan='4'>CRONOGRAMA (por trimestres)</td>
                <td>Funcionario Responsable</td>
                <td colspan='4'>Presupuesto</td>
                <td colspan='6'>El resultado esperado le apunta DIRECTA y ESPECÍFICAMENTE a alguno de los siguientes temas transversales?</td>
                </tr>
                <tr>
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                <td>1T Cronograma</td>
                <td>2T Cronograma</td>
                <td>3T Cronograma</td>
                <td>4T Cronograma</td>
                <td></td>
                <td>Fuente de los Fondos FUNDED</td>
                <td>Fuente de los Fondos UNFUNDED</td>
                <td>Descripci&oacute;n</td>
                <td>Monto</td>
                <td>Indígenas</td>
                <td>Afros</td>	
                <td>Prevenciòn de la violencia</td>	
                <td>Movilizaciòn y alianzas</td>	
                <td>Equidad de género</td>	
                <td>Participación</td>
            </tr>";
        
        $ids_total = array('deptal_mpal' => $ids, 'nal' => $ids_nal);
        foreach ($ids_total as $cobertura => $ids_t){
            foreach ($ids_t as $id){
                
                $vo = $this->Get($id);
                $estado = $estado_dao->Get($vo->id_estado);
                
                $tree_names = $this->getTreeNames($vo);
                //$nom_tema_1 = ($vo->id_tema_undaf_1 > 0) ? $tema_dao->Get($vo->id_tema_undaf_1)->nombre : '';
                //$nom_tema_2 = ($vo->id_tema_undaf_2 > 0) ? $tema_dao->Get($vo->id_tema_undaf_2)->nombre : '';
                //$nom_tema_3 = ($vo->id_tema_undaf_3 > 0) ? $tema_dao->Get($vo->id_tema_undaf_3)->nombre : '';

                if ($cobertura == 'nal'){
                    $p_awps = $producto_awp_dao->GetAllArrayCobertura('nal',0,"id_actividad=$id AND $condicion");
                }
                else{
                    $p_awps = $producto_awp_dao->GetAllArrayCobertura($mdgd,$id_depto_mpio,"id_actividad=$id AND $condicion");
                }
                
                //$num_p_awps = count($p_awps);
                
                /*
                $html .= "<tr>
                      <td rowspan='$num_p_awps'>$vo->aaaa</td>
                      <td rowspan='$num_p_awps'>".$tree_names['componente']."</td>
                      <td rowspan='$num_p_awps'>".$tree_names['sub']."</td>
                      <td rowspan='$num_p_awps'>".$tree_names['resultado']."</td>
                      <td rowspan='$num_p_awps'>".$tree_names['p_cpap']."</td>
                        <td rowspan='$num_p_awps'>$vo->codigo</td>
                        <td rowspan='$num_p_awps'>$vo->nombre</td>
                      <td rowspan='$num_p_awps'>$estado->nombre</td>";
                      
                      <td rowspan='$num_p_awps'>$nom_tema_1</td>
                      <td rowspan='$num_p_awps'>$nom_tema_2</td>
                      <td rowspan='$num_p_awps'>$nom_tema_3</td>";
                      */
                      

                // Productos AWP
                foreach($p_awps as $p_awp){

                    $id_muns = implode(',',$p_awp->id_mun);
                    
                    $html .= "<tr>
                      <td>$vo->aaaa</td>
                      <td>".$tree_names['componente']."</td>
                      <td>".$tree_names['sub']."</td>
                      <td>".$tree_names['resultado']."</td>
                      <td>".$tree_names['p_cpap']."</td>
                      <td>$vo->codigo</td>
                      <td>$vo->nombre</td>
                      <td>$estado->nombre</td>
                      <td>$p_awp->nombre</td>";
                    
                    // Cobertura
                    $html .= '<td>'.$cobertura_t[$p_awp->cobertura].'</td><td>';

                    foreach ($p_awp->id_depto as $d=>$id_depto){

                        $nom_depto = $depto_dao->GetName($id_depto);
                        if ($d > 0) $html .= ", ";
                        $html .= "<b>$nom_depto</b>";
                        
                        if ($id_muns != ''){ 
                            $id_muns_d = $mun_dao->GetAllArrayID("id_depto = $id_depto AND id_mun IN ($id_muns)",'');
                        
                            $html .= ":&nbsp;".$this->getNames('municipio',$id_muns_d);
                        }

                    }

                    echo '</td>';
                    
                    // Socios
                    $socios = $this->getNames('unicef_socio',$p_awp->id_socio_implementador);
                    $html .= "<td>$socios</td>";

                    // Cronograma
                    for($c=1;$c<5;$c++){
                        $tri = 'cronograma_'.$c.'_tri';
                        $x = ($p_awp->$tri == 1) ? 'X' : '' ;

                        $html .= "<td>$x</td>";
                    }

                    // Funcionario
                    $fun = '';
                    foreach ($p_awp->id_funcionario as $f => $id_f){
                        $nom = $funcionario_dao->Get($id_f)->nombre;
                        if ($f == 0)    $fun = $nom;
                        else            $fun .= ' - '.$nom;
                    }

                    $html .= "<td>$fun</td>";
                    
                    // Presupuesto
                    // FUNDED
                    $td_funded = '';
                    foreach ($p_awp->id_fuente_funded as $f=>$id_f){
                        $fuente = $fuente_dao->Get($id_f);
                        $donante = $donante_dao->Get($fuente->id_donante);
                        $valor = $p_awp->fuente_funded_valor[$id_f];

                        $td_funded = "$fuente->nombre : $valor ($donante->nombre)";

                        if ($f > 0) $td_funded .= ', '.$td_funded;
                    }

                    $html .= "<td>$td_funded</td>";

                    // UNFUNDED
                    $td_unfunded = '';
                    foreach ($p_awp->id_fuente_unfunded as $f=>$id_f){
                        $fuente = $fuente_dao->Get($id_f);
                        $donante = $donante_dao->Get($fuente->id_donante);
                        $valor = $p_awp->fuente_unfunded_valor[$id_f];

                        $td_unfunded = "$fuente->nombre : $valor ($donante->nombre)";

                        if ($f > 0) $td_unfunded .= ', '.$td_unfunded;
                    }

                    $html .= "<td>$td_unfunded</td>";

                    // Desc
                    $desc = '';
                    foreach ($p_awp->id_presupuesto_desc as $d => $id_d){
                        $nom = $presupuesto_desc_dao->Get($id_d)->nombre;
                        if ($d == 0)    $desc = $nom;
                        else            $desc .= ' - '.$nom;
                    }

                    $html .= "<td>$desc</td>";
                    
                    $presu = '';
                    if (strlen($p_awp->presupuesto_ex) > 0){
                        //$moneda = $moneda_dao->Get($p_awp->id_mon_ex);
                        //$presu = $moneda->nombre.' '.$p_awp->presupuesto_ex;
                        $presu = $p_awp->presupuesto_ex;
                    }
                    $html .= "<td>$presu</td>";

                    // Temas transversales
                    $temas_t = array('afro','indigena','prevencion','movilizacion','participacion','equidad_genero');
                    foreach($temas_t as $tema){
                        $x = ($p_awp->$tema == 1) ? 'X' : '' ;

                        $html .= "<td>$x</td>";
                    }

                    echo '</tr>';
                }
            }
        }
        
        $html .= '</table>';
        
        $_SESSION['xls'] = $html;
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

		$tabla_rel = 'unicef_producto_awp';
		$col_id = $this->columna_id;
		
		$sql = "SELECT count($col_id) FROM $tabla_rel WHERE $col_id = $id";
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$r = ($row[0] > 0) ? true : false;

		return $r;

	}
}

?>
