<?php
/**
 * DAO de UNICEF
 *
 * Contiene los métodos de la clase UNICEF
 * @author Ruben A. Rojas C.
 */

Class UnicefDAO {

	/**
	 * Conexión a la base de datos
	 * @var object 
	 */
	var $conn;

	/**
	 * Constructor
	 * Crea la conexión a la base de datos
	 * @access public
	 */	
	function UnicefDAO (){
		$this->conn = MysqlDb::getInstance();
        $this->cobertura_t = array('N'=>'Nacional','D'=>'Departamental','M'=>'Municipal','NA'=>'No aplica','I'=>'Interno');
        $this->filtro_titulo = array('comps' => 'componente','odm' => 'odm','mtsp' => 'mtsp');
        $this->filtro_exp = array('comps' => array('Supervivencia y desarrollo infantil',
                                                'Educaci&oacute;n con Calidad, Desarrollo del Adolescente y Prevenci&oacute;n del VIH/SIDA',
                                                'Protecci&oacute;n y Acci&oacute;n Humanitaria',
                                                'Pol&iacute;ticas P&uacute;blicas Basadas en Evidencia'),
                                'odm' => array('Erradicar la pobreza extrema y el hambre',
                                                'Logar la Educaci&oacute;n Primaria Universal',
                                                'Promover la Igualdad Entre los Sexos y la Autonomia de la Mujer',
                                                'Reducir la Mortalidad Infantil',
                                                'Mejorar la Salud Materna',
                                                'Combatir el VIH/SIDA, Paludismo y Otras Enfermedades End&eacute;micas',
                                                'Garantizar la Sostenibilidad Ambiental',
                                                'Fomentar una Asociaci&oacute;n Mundial para el Desarrollo, con Metas para la Asistencia, el Comercio, el Buen Gobierno y el Alivio a la Deuda',
                                                ),
                                'mtsp' => array('Focus Area 1','Focus Area 2','Focus Area 3','Focus Area 4'),
                                'mtsp_key' => array('Key Result 1','Key Result 2','Key Result 3','Key Result 4')
        
                            );
        $this->sub_dao = FactoryDAO::factory('unicef_sub_componente');
        $this->res_dao = FactoryDAO::factory('unicef_resultado');
        $this->p_cpap_dao = FactoryDAO::factory('unicef_producto_cpap');
        $this->actividad_dao = FactoryDAO::factory('unicef_actividad_awp');
        $this->p_awp_dao = FactoryDAO::factory('unicef_producto_awp');
        $this->estado_dao = FactoryDAO::factory('unicef_estado');
        $this->depto_dao = FactoryDAO::factory('depto');
        $this->mun_dao = FactoryDAO::factory('municipio');
        $this->funcionario_dao = FactoryDAO::factory('unicef_funcionario');
        $this->presupuesto_desc_dao = FactoryDAO::factory('unicef_presupuesto_desc');
        $this->fuente_dao = FactoryDAO::factory('unicef_fuente_pba');
        $this->donante_dao = FactoryDAO::factory('unicef_donante');
        $this->socio_dao = FactoryDAO::factory('unicef_socio');
        $this->grid_columns = array(
                                    'proyectado' => array('comp','sub_c','res','p_cpap','act','estado','p_awp','cob','depto_mpio','socio','crono_1t','crono_2t','crono_3t','crono_4t','func','funded','unfunded','desc','presu','indigena','afro','prevencion','movilizacion','equidad_genero','participacion'),
                                    'ejecutado' => array('codigo','comp','sub_c','res','p_cpap','act','obj','estado','f_ini','f_fin','meses','avances','presu','cob','depto_mpio','socio')
                                    );
	}

	/**
	 * Retorna las columnas de acuerdo al reporte en formato array javascript para Grid Panel de Extjs
	 * @access public
	 * @param string $proy_eje Reporte Proyectado o Ejecutado
     * @return string $cols 
	 */	
	function getColumnsGrid($proy_eje){

        if ($proy_eje == 'proyectado'){
            $extra_info = array('comp' => array('header'=>'Comp','more'=>'width:70'),
                             'sub_c' => array('header'=>'Sub Comp.','more'=>'width:100'),
                             'res' => array('header'=>'Resultado','more'=>'width:300'),
                             'p_cpap' => array('header'=>'Productos','more'=>'width:300'),
                             'act' => array('header'=>'Actividades','more'=>'width:300'),
                             'estado' => array('header'=>'Estado','more'=>'width:60'),
                             'p_awp' => array('header'=>'Resultados','more'=>'width:400'),
                             'cob' => array('header'=>'Cobertura'),
                             'depto_mpio' => array('header'=>'Deptos - Mpios','more'=>'width:200'),
                             'socio' => array('header'=>'Socios','more'=>'width:200'),
                             'crono_1t' => array('header'=>'1 Trim.','more'=>'width:50'),
                             'crono_2t' => array('header'=>'2 Trim.','more'=>'width:50'),
                             'crono_3t' => array('header'=>'3 Trim.','more'=>'width:50'),
                             'crono_4t' => array('header'=>'4 Trim.','more'=>'width:50'),
                             'func' => array('header'=>'Funcionario Responsable',''=>''),
                             'funded' => array('header'=>'Fuentes FUNDED','more'=>'width:300'),
                             'unfunded' => array('header'=>'Fuentes UNFUNDED','more'=>'width:300'),
                             'desc' => array('header'=>'Descripción','more'=>'width:60'),
                             'presu' => array('header'=>'Presupuesto Total','more'=>'width:100,renderer: Ext.util.Format.usMoney'),
                             'indigena' => array('header'=>'Indígenas','more'=>'width:70'),
                             'afro' => array('header'=>'Afros','more'=>'width:70'),
                             'prevencion' => array('header'=>'Prevención de la Violencia','more'=>'width:70'),
                             'movilizacion' => array('header'=>'Movilización y alianzas','more'=>'width:70'),
                             'equidad_genero' => array('header'=>'Equidad de género','more'=>'width:70'),
                             'participacion' => array('header'=>'Participación','more'=>'width:70')
                          );

            $cols_hidden = array('sub_c');
            $cols_multiline = array('comp','sub_c','res','p_cpap','act','p_awp','depto_mpio','socio','funded','unfunded');
            $cols_to_extjs = '';
            foreach ($this->grid_columns['proyectado'] as $i=>$id_column){
                
                if ($i > 0) $cols_to_extjs .= ',';
                
                $cols_to_extjs .= "{id:'$id_column',header:'".$extra_info[$id_column]['header']."',sortable:true,dataIndex:'$id_column'";
                
                if (isset($extra_info[$id_column]['more']))    $cols_to_extjs .= ','.$extra_info[$id_column]['more'];
                if (in_array($id_column,$cols_hidden))  $cols_to_extjs .= ',hidden:true';
                if (in_array($id_column,$cols_multiline)) $cols_to_extjs .= ",renderer: function(value, metaData, record, rowIndex, colIndex, store) { metaData.css = 'multilineColumn'; return value; }";
 
                
                $cols_to_extjs .= '}';
            }

        }
        else{
            $extra_info = array('codigo' => array('header'=>'Convenio','more'=>'width:130'),
                                'comp' => array('header'=>'[AWP] Componente','more'=>'width:100'),
                                'sub_c' => array('header'=>'[AWP] Sub Componente','more'=>'width:100'),
                                'res' => array('header'=>'[AWP] Resultado','more'=>'width:200'),
                                'p_cpap' => array('header'=>'[AWP] Producto','more'=>'width:200'),
                                'act' => array('header'=>'[AWP] Actividad','more'=>'width:300'),
                                'obj' => array('header'=>'Objetivo del Convenio','more'=>'width:300'),
                                'estado' => array('header'=>'Estado','more'=>'width:100'),
                                'f_ini' => array('header'=>'F. Inicio','more'=>'width:100'),
                                'f_fin' => array('header'=>'F. Finalización','more'=>'width:100'),
                                'meses' => array('header'=>'Meses','more'=>'width:50'),
                                'avances' => array('header'=>'Avances','more'=>'width:200'),
                                'presu' => array('header'=>'Valor Total (US$)','more'=>'width:100,renderer:Ext.util.Format.usMoney'),
                                'cob' => array('header'=>'Cobertura','more'=>'width:100'),
                                'depto_mpio' => array('header'=>'Deptos - Mpios','more'=>'width:300'),
                                'socio' => array('header'=>'Socios','more'=>'width:300')
                                );
            
            $cols_multiline = array('comp','sub_c','res','p_cpap','act','obj','depto_mpio','socio');
            
            $cols_to_extjs = '';
            foreach ($this->grid_columns['ejecutado'] as $i=>$id_column){
                
                if ($i > 0) $cols_to_extjs .= ',';
                
                $cols_to_extjs .= "{id:'$id_column',header:'".$extra_info[$id_column]['header']."',sortable:true,dataIndex:'$id_column'";
                
                if (isset($extra_info[$id_column]['more']))    $cols_to_extjs .= ','.$extra_info[$id_column]['more'];
                if (in_array($id_column,$cols_multiline)) $cols_to_extjs .= ",renderer: function(value, metaData, record, rowIndex, colIndex, store) { metaData.css = 'multilineColumn'; return value; }";
 
                $cols_to_extjs .= '}';
            }
        }


        
        return $cols_to_extjs;
    }

	/**
	 * Reporta la informacion en formato AWP
	 * @access public
	 * @param string $caso que,socio,donante
     * @param array $filtros Arreglo con los diferentes filtros
	 */	
	function reporteProyectado($caso,$filtros){
        
        // INICIALIZACION
        $cond = '';
        
        if ($filtros['aaaa'] != '') $cond = 'p_awp.aaaa IN ('.$filtros['aaaa'].')';

        if ($filtros['filtro'] == 'comps'){
            $cond .= ' AND id_componente IN ('.$filtros['id_filtro'].')';
        }
        
        // Socios
        else if (strpos($filtros['filtro'],'socio') !== false){
            $tmp = explode('-',$filtros['id_filtro']);

            $cond .= ' AND id_componente IN ('.$tmp[0].') AND id_socio = '.$tmp[1];
        }
        
        // Donantes
        else if (strpos($filtros['filtro'],'donante') !== false){
            $tmp = explode('-',$filtros['id_filtro']);

            $cond .= ' AND id_componente IN ('.$tmp[0].') AND id_donante = '.$tmp[1];
        }
        
        $th_col_xls = '<tr>
                        <td>Componente</td>
                        <td>Sub Componente</td>
                        <td>Resultado</td>
                        <td>Productos previstos del componente programático</td>
                        <td>ACTIVIDADES PLANEADAS</td>  
                        <td>Estado</td>
                        <td>RESULTADOS ESPERADOS PARA EL '.$filtros['aaaa'].'</td>
                        <td>COBERTURA GEOGRAFICA</td>';

                        // Deptos - Mpios
                        $id_deptos = $this->p_awp_dao->getInfoQue('depto',$cond);
                        foreach ($id_deptos as $id_depto){
                            $depto = $this->depto_dao->Get($id_depto);
                            $id_deptos_nom[$id_depto] = $depto->nombre;
                            $th_col_xls .= "<td>$depto->nombre</td><td>Mpios</td>";
                        }

                        // Socios
                        $id_socios = $this->p_awp_dao->getInfoQue('socio',$cond);
                        foreach ($id_socios as $s=>$id_socio){
                            $socio = $this->socio_dao->Get($id_socio);
                            $id_socios_nom[$id_socio] = $socio->nombre;
                            $th_col_xls .= "<td>Socio ".($s+1).": $socio->nombre</td>";
                        }
        $th_col_xls .= '
                          <td>Crono: 1 Tri</td>
                          <td>Crono: 2 Tri</td>
                          <td>Crono: 3 Tri</td>
                          <td>Crono: 4 Tri</td>
                        <td>Funcionario Responsable</td>
                        ';
                        
                        // Fuente FUNDED
                        $id_fuentes_funded_nom = array();
                        $id_fuentes_funded = $this->p_awp_dao->getInfoQue('fuente_pba',$cond.' AND unicef_producto_awp_fuente_pba.funded = 1');
                        foreach ($id_fuentes_funded as $f=>$id_fuente){
                            $fuente = $this->fuente_dao->Get($id_fuente);
                            $id_fuentes_funded_nom[$id_fuente] = $fuente->nombre;
                            $donante = $this->donante_dao->Get($fuente->id_donante);
                            $th_col_xls .= "<td>Fuente FUNDED ".($f+1).": $fuente->nombre ($donante->nombre)</td><td>Aporte</td>";
                        }
                        
                        // Fuente UNFUNDED
                        $id_fuentes_unfunded_nom = array();
                        $id_fuentes_unfunded = $this->p_awp_dao->getInfoQue('fuente_pba',$cond.' AND unicef_producto_awp_fuente_pba.unfunded = 1');
                        foreach ($id_fuentes_unfunded as $f=>$id_fuente){
                            $fuente = $this->fuente_dao->Get($id_fuente);
                            $id_fuentes_unfunded_nom[$id_fuente] = $fuente->nombre;
                            $donante = $this->donante_dao->Get($fuente->id_donante);
                            $th_col_xls .= "<td>Fuente UNFUNDED ".($f+1).": $fuente->nombre ($donante->nombre)</td><td>Aporte</td>";
                        }
                
                $th_col_xls .= '
                          <td>Descripci&oacute;n</td>
                          <td>Presupuesto Total</td>
                          <td>Indígenas</td>
                          <td>Afros</td>	
                          <td>Prevenciòn de la violencia</td>	
                          <td>Movilizaciòn y alianzas</td>	
                          <td>Equidad de género</td>	
                          <td>Participación</td>
        </tr>';
        
        $xls = '<table border=1><tr><td>Annual Workplan'.$filtros['aaaa'].'</td></tr>'.$th_col_xls;
        $ppp = 0;
        $info = array();

        //$p_awps = $this->p_awp_dao->GetAllArray($cond);
        $all_info = $this->p_awp_dao->GetTreeIDReporte($cond);
        $id_p_awps = (isset($all_info['id'])) ? $all_info['id'] : array();
        foreach ($id_p_awps as $i=>$id_p_awp){

            $p_awp = $this->p_awp_dao->Get($id_p_awp);
            $comp_nombre = '<a href="download_pdf.php?c=6&id='.$id_p_awp.'" target="_blank"><img src="images/unicef/pdf.gif"></a>&nbsp;'.$all_info['comp'][$i];
            $sub_c_nombre = $all_info['sub_c'][$i];
            $res_nombre = $all_info['res'][$i];
            $p_cpap_nombre = $all_info['p_cpap'][$i];
            $act_nombre = $all_info['act'][$i];
            $estado = $this->estado_dao->Get($all_info['act_estado'][$i]);
            $estado_nombre = $estado->nombre;

            $id_muns = implode(',',$p_awp->id_mun);
            $cob_nombre = $this->cobertura_t[$p_awp->cobertura];
            $p_awp_nombre = $p_awp->codigo.' '.$p_awp->nombre;

            // hoja calculo
            $xls .= "<tr>
                        <td>".$all_info['comp'][$i]."</td>
                        <td>$sub_c_nombre</td>
                        <td>$res_nombre</td>
                        <td>$p_cpap_nombre</td>
                        <td>$act_nombre</td>
                        <td>$estado_nombre</td>
                        <td>$p_awp_nombre</td>
                        <td>$cob_nombre</td> 
                        ";
            
            // Cobertura
            //echo '<td>'.$cobertura_nombre.'</td><td>';
            $depto_mpio_nombre = '';
            foreach ($p_awp->id_depto as $d=>$id_depto){
                
                if ($d > 0)    $depto_mpio_nombre .= ' - ';
                
                $nom_depto = $this->depto_dao->GetName($id_depto);
                $depto_mpio_nombre .= "<b>$nom_depto</b>";
                
                if ($id_muns != ''){ 
                    $id_muns_d = $this->mun_dao->GetAllArrayID("id_depto = $id_depto AND id_mun IN ($id_muns)",'');
                
                    $depto_mpio_nombre .= " ".$this->actividad_dao->getNames('municipio',$id_muns_d);
                }
            }

            //echo '</td>';

            // xls
            foreach ($id_deptos_nom as $id_depto=>$depto_nombre){
                if (in_array($id_depto,$p_awp->id_depto))   $xls .= "<td>$depto_nombre</td>";
                else                                        $xls .= '<td></td>';

                if ($id_muns != ''){ 
                    $id_muns_d = $this->mun_dao->GetAllArrayID("id_depto = $id_depto AND id_mun IN ($id_muns)",'');
                
                    $xls .= '<td>'.$this->actividad_dao->getNames('municipio',$id_muns_d).'</td>';
                }
                else    $xls .= '<td></td>';
                
            }
            
            // Socios
            $socio_nombre = $this->actividad_dao->getNames('unicef_socio',$p_awp->id_socio_implementador);
            //echo "<td>$socios</td>";
            
            // xls
            foreach ($id_socios_nom as $id_socio=>$socio_nombre){
                if (in_array($id_socio,$p_awp->id_socio_implementador))   $xls .= "<td>$socio_nombre</td>";
                else                                                        $xls .= '<td></td>';
            }

            // Cronograma
            $crono_xls = '';
            for($c=1;$c<5;$c++){
                $tri = 'cronograma_'.$c.'_tri';
                $x = ($p_awp->$tri == 1) ? 'X' : '' ;
                eval ("\$crono_".$c."t_nombre = \"$x\"; ");

                $crono_xls .= "<td>$x</td>";
            }

            //echo $crono_txt;
            $xls .= $crono_xls;

            // Funcionario
            $func_nombre = '';
            foreach ($p_awp->id_funcionario as $f => $id_f){
                
                $nom = $this->funcionario_dao->Get($id_f)->nombre;
                
                if ($f > 0)     $func_nombre .= ' - ';
                
                $func_nombre .= $nom;
            }

            //echo "<td>$fun</td>";
            $xls .= "<td>$func_nombre</td>";
            
            // Presupuesto
            // FUNDED
            $funded_nombre = '';
            foreach ($p_awp->id_fuente_funded as $f=>$id_f){
                $fuente = $this->fuente_dao->Get($id_f);
                $donante = $this->donante_dao->Get($fuente->id_donante);
                $valor = $p_awp->fuente_funded_valor[$id_f];

                if ($f > 0) $funded_nombre .= ', ';
                $funded_nombre .= "$fuente->nombre : $valor ($donante->nombre)";

            }

            //echo "<td>$td_funded</td>";

            // xls
            foreach ($id_fuentes_funded_nom as $id_fuente=>$fuente_nombre){
                if (in_array($id_fuente,$p_awp->id_fuente_funded)){
                    $xls .= "<td>$fuente_nombre</td>";
                    $valor = $p_awp->fuente_funded_valor[$id_fuente];
                    $xls .= "<td>$valor</td>";

                }
                else{
                    $xls .= '<td></td><td></td>';
                }

            }

            // UNFUNDED
            $unfunded_nombre = '';
            foreach ($p_awp->id_fuente_unfunded as $f=>$id_f){
                $fuente = $this->fuente_dao->Get($id_f);
                $donante = $this->donante_dao->Get($fuente->id_donante);
                $valor = $p_awp->fuente_unfunded_valor[$id_f];

                if ($f > 0) $unfunded_nombre .= ', ';
                $unfunded_nombre .= "$fuente->nombre : $valor ($donante->nombre)";

            }

            //echo "<td>$td_unfunded</td>";
            
            // xls
            foreach ($id_fuentes_unfunded_nom as $id_fuente=>$fuente_nombre){
                if (in_array($id_fuente,$p_awp->id_fuente_unfunded)){
                    $xls .= "<td>$fuente_nombre</td>";
                    $valor = $p_awp->fuente_unfunded_valor[$id_fuente];
                    $xls .= "<td>$valor</td>";

                }
                else{
                    $xls .= '<td></td><td></td>';
                }

            }

            // Desc
            $desc_nombre = '';
            foreach ($p_awp->id_presupuesto_desc as $d => $id_d){
                $nom = $this->presupuesto_desc_dao->Get($id_d)->nombre;
                if ($d > 0)    $desc_nombre .= ' - ';
                $desc_nombre .= $nom;
            }

            //echo "<td>$desc</td>";
            $xls .= "<td>$desc_nombre</td>";
            
            $presu_nombre = '';
            if (strlen($p_awp->presupuesto_ex) > 0){
                //$moneda = $moneda_dao->Get($p_awp->id_mon_ex);
                //$presu = $moneda->nombre.' '.$p_awp->presupuesto_ex;
                $presu_nombre = $p_awp->presupuesto_ex;
            }

            //echo "<td>$presu</td>";
            $xls .= "<td>$presu_nombre</td>";

            // Temas transversales
            $temas_t = array('afro','indigena','prevencion','movilizacion','participacion','equidad_genero');
            $temas_txt = '';
            foreach($temas_t as $tema){
                $x = ($p_awp->$tema == 1) ? 'X' : '&nbsp;' ;

                //$temas_txt .= "<td>$x</td>";
                eval("\$".$tema."_nombre = \"$x\";");
            }

            //echo $temas_txt;
            $xls .= $temas_txt;
            
            //echo '</tr>';
            
            $info_cols = array();
            foreach($this->grid_columns['proyectado'] as $columna){
                //if ($columna == 'act')  echo $act_nombre;
                // # fila
                eval("\$info_cols['$columna'] = utf8_encode(\"$".$columna."_nombre\");");
            }
            
            $info['unicef'][$ppp] = $info_cols;

            $ppp++;

        }


/*
        foreach (explode(',',$filtros['id_filtro']) as $i => $id_f){
            
            $cond_sub_c = $cond ." AND id_componente = $id_f";
            $id_sub_c = $this->actividad_dao->getTreeIDReporte('sub_c',$cond_sub_c);
            $class = ($i == 0) ? 'selected_tab_tr' : 'unselected_tab_tr';
            
            //$txt = 'Annual Workplan '.$filtros['aaaa'].' '.$this->filtro_exp[$filtros['filtro']][$i];
            //echo '<tr id="tab_'.$id_f.'_tr" class="'.$class.'"><td><table width="100%"><tr> <td class="titulo" align="center">'.$txt.'</td></tr>';

            // Sub Componentes
            foreach ($id_sub_c as $id_s){
                
                $sub_c_nombre = $this->sub_dao->GetFieldValue($id_s,'nombre');
                //echo '<tr class="sub_componente"><td>'.$sub_nombre.'</td></tr>';
                
                $cond_res = $cond ." AND sub_c.id_sub_componente = $id_s";
                $id_res = $this->actividad_dao->getTreeIDReporte('res',$cond_res);
                
                // Resultados
                foreach ($id_res as $id_r){
                    $res_nombre = $this->res_dao->GetFieldValue($id_s,'codigo').' '.$this->res_dao->GetFieldValue($id_s,'nombre');
                    //echo '<tr class="resultado"><td>'.$res_nombre.'</td></tr>';
                    //echo "<tr><td><table cellspacing='1' cellpadding='3' class='table_p_cpap'>$th_col_awp";

                    // Productos CPAP
                    $cond_p_cpap = $cond ." AND p_cpap.id_resultado = $id_r";
                    $id_p_cpap = $this->actividad_dao->getTreeIDReporte('p_cpap',$cond_p_cpap);

                    foreach ($id_p_cpap as $id_p_c){

                        // Act. AWP
                        $cond_act = $cond ." AND act.id_producto = $id_p_c";
                        $id_act = $this->actividad_dao->getTreeIDReporte('act',$cond_act);

                        $ids_p_awp_c_pap = $this->actividad_dao->getTreeIDReporte('p_awp',$cond." AND act.id_producto = $id_p_c");

                        $num_rows_p_cpap = count($ids_p_awp_c_pap);

                        $p_cpap_nombre = $this->p_cpap_dao->GetFieldValue($id_p_c,'codigo').' '.$this->p_cpap_dao->GetFieldValue($id_p_c,'nombre');
                        //echo '<tr class="p_cpap"><td rowspan="'.$num_rows_p_cpap.'" valign="top">'.$p_cpap_nombre.'</td>';

                        foreach ($id_act as $aaa => $id_a){

                            $comp_nombre = '<a href="download_pdf.php?c=6&id='.$id_p_c.'"><img src="images/unicef/pdf.gif"></a>&nbsp; CP '.($i+1);
                            $estado = $this->estado_dao->Get($this->actividad_dao->GetFieldValue($id_a,'id_estado'));
                            $estado_nombre = $estado->nombre;
                            
                            // P. AWP
                            $cond_p_awp = $cond." AND id_actividad = $id_a";
                            $p_awps = $this->p_awp_dao->GetAllArray($cond_p_awp);
                            
                            //if ($aaa > 0) echo '<tr class="p_cpap">';
                            
                            $act_nombre = $this->actividad_dao->GetFieldValue($id_a,'codigo').' '.$this->actividad_dao->GetFieldValue($id_a,'nombre');
                            //echo '<td rowspan="'.count($ids_p_awp).'" valign="top">'.$a_awp_nombre.'</td>';
                            //echo '<td rowspan="'.count($ids_p_awp).'" valign="top">'.$estado->nombre.'</td>';
                            
                            foreach ($p_awps as $p_awp){
                                $id_muns = implode(',',$p_awp->id_mun);
                                $cob_nombre = $this->cobertura_t[$p_awp->cobertura];
                                //if ($ppp > 0) echo '<tr class="p_cpap">';

                                $p_awp_nombre = $p_awp->codigo.' '.$p_awp->nombre;

                                //echo "<td>$p_awp_nombre</td>";

                                // hoja calculo
                                $xls .= "<tr>
                                            <td>$comp_nombre</td>
                                            <td>$sub_c_nombre</td>
                                            <td>$res_nombre</td>
                                            <td>$p_cpap_nombre</td>
                                            <td>$act_nombre</td>
                                            <td>$estado_nombre</td>
                                            <td>$p_awp_nombre</td>
                                            <td>$cob_nombre</td> 
                                            ";
                                
                                // Cobertura
                                //echo '<td>'.$cobertura_nombre.'</td><td>';
                                $depto_mpio_nombre = '';
                                foreach ($p_awp->id_depto as $d=>$id_depto){
                                    
                                    if ($d > 0)    $depto_mpio_nombre .= ' - ';
                                    
                                    $nom_depto = $this->depto_dao->GetName($id_depto);
                                    $depto_mpio_nombre .= "<b>$nom_depto</b>";
                                    
                                    if ($id_muns != ''){ 
                                        $id_muns_d = $this->mun_dao->GetAllArrayID("id_depto = $id_depto AND id_mun IN ($id_muns)",'');
                                    
                                        $depto_mpio_nombre .= " ".$this->actividad_dao->getNames('municipio',$id_muns_d);
                                    }
                                }

                                //echo '</td>';

                                // xls
                                foreach ($id_deptos_nom as $id_depto=>$depto_nombre){
                                    if (in_array($id_depto,$p_awp->id_depto))   $xls .= "<td>$depto_nombre</td>";
                                    else                                        $xls .= '<td></td>';

                                    if ($id_muns != ''){ 
                                        $id_muns_d = $this->mun_dao->GetAllArrayID("id_depto = $id_depto AND id_mun IN ($id_muns)",'');
                                    
                                        $xls .= '<td>'.$this->actividad_dao->getNames('municipio',$id_muns_d).'</td>';
                                    }
                                    else    $xls .= '<td></td>';
                                    
                                }
                                
                                // Socios
                                $socio_nombre = $this->actividad_dao->getNames('unicef_socio',$p_awp->id_socio_implementador);
                                //echo "<td>$socios</td>";
                                
                                // xls
                                foreach ($id_socios_nom as $id_socio=>$socio_nombre){
                                    if (in_array($id_socio,$p_awp->id_socio_implementador))   $xls .= "<td>$socio_nombre</td>";
                                    else                                                        $xls .= '<td></td>';
                                }

                                // Cronograma
                                $crono_xls = '';
                                for($c=1;$c<5;$c++){
                                    $tri = 'cronograma_'.$c.'_tri';
                                    $x = ($p_awp->$tri == 1) ? 'X' : '' ;
                                    eval ("\$crono_".$c."t_nombre = \"$x\"; ");

                                    $crono_xls .= "<td>$x</td>";
                                }

                                //echo $crono_txt;
                                $xls .= $crono_xls;

                                // Funcionario
                                $func_nombre = '';
                                foreach ($p_awp->id_funcionario as $f => $id_f){
                                    
                                    $nom = $this->funcionario_dao->Get($id_f)->nombre;
                                    
                                    if ($f > 0)     $func_nombre .= ' - ';
                                    
                                    $func_nombre .= $nom;
                                }

                                //echo "<td>$fun</td>";
                                $xls .= "<td>$func_nombre</td>";
                                
                                // Presupuesto
                                // FUNDED
                                $funded_nombre = '';
                                foreach ($p_awp->id_fuente_funded as $f=>$id_f){
                                    $fuente = $this->fuente_dao->Get($id_f);
                                    $donante = $this->donante_dao->Get($fuente->id_donante);
                                    $valor = $p_awp->fuente_funded_valor[$id_f];

                                    if ($f > 0) $funded_nombre .= ', ';
                                    $funded_nombre .= "$fuente->nombre : $valor ($donante->nombre)";

                                }

                                //echo "<td>$td_funded</td>";

                                // xls
                                foreach ($id_fuentes_funded_nom as $id_fuente=>$fuente_nombre){
                                    if (in_array($id_fuente,$p_awp->id_fuente_funded)){
                                        $xls .= "<td>$fuente_nombre</td>";
                                        $valor = $p_awp->fuente_funded_valor[$id_fuente];
                                        $xls .= "<td>$valor</td>";

                                    }
                                    else{
                                        $xls .= '<td></td><td></td>';
                                    }

                                }

                                // UNFUNDED
                                $unfunded_nombre = '';
                                foreach ($p_awp->id_fuente_unfunded as $f=>$id_f){
                                    $fuente = $this->fuente_dao->Get($id_f);
                                    $donante = $this->donante_dao->Get($fuente->id_donante);
                                    $valor = $p_awp->fuente_unfunded_valor[$id_f];

                                    if ($f > 0) $unfunded_nombre .= ', ';
                                    $unfunded_nombre .= "$fuente->nombre : $valor ($donante->nombre)";

                                }

                                //echo "<td>$td_unfunded</td>";
                                
                                // xls
                                foreach ($id_fuentes_unfunded_nom as $id_fuente=>$fuente_nombre){
                                    if (in_array($id_fuente,$p_awp->id_fuente_unfunded)){
                                        $xls .= "<td>$fuente_nombre</td>";
                                        $valor = $p_awp->fuente_unfunded_valor[$id_fuente];
                                        $xls .= "<td>$valor</td>";

                                    }
                                    else{
                                        $xls .= '<td></td><td></td>';
                                    }

                                }

                                // Desc
                                $desc_nombre = '';
                                foreach ($p_awp->id_presupuesto_desc as $d => $id_d){
                                    $nom = $this->presupuesto_desc_dao->Get($id_d)->nombre;
                                    if ($d > 0)    $desc_nombre .= ' - ';
                                    $desc_nombre .= $nom;
                                }

                                //echo "<td>$desc</td>";
                                $xls .= "<td>$desc_nombre</td>";
                                
                                $presu_nombre = '';
                                if (strlen($p_awp->presupuesto_ex) > 0){
                                    //$moneda = $moneda_dao->Get($p_awp->id_mon_ex);
                                    //$presu = $moneda->nombre.' '.$p_awp->presupuesto_ex;
                                    $presu_nombre = $p_awp->presupuesto_ex;
                                }

                                //echo "<td>$presu</td>";
                                $xls .= "<td>$presu_nombre</td>";

                                // Temas transversales
                                $temas_t = array('afro','indigena','prevencion','movilizacion','participacion','equidad_genero');
                                $temas_txt = '';
                                foreach($temas_t as $tema){
                                    $x = ($p_awp->$tema == 1) ? 'X' : '&nbsp;' ;

                                    //$temas_txt .= "<td>$x</td>";
                                    eval("\$".$tema."_nombre = \"$x\";");
                                }

                                //echo $temas_txt;
                                $xls .= $temas_txt;
                                
                                //echo '</tr>';
                                
                                $info_cols = array();
                                foreach($this->grid_columns['proyectado'] as $columna){
                                    //if ($columna == 'act')  echo $act_nombre;
                                    // # fila
                                    eval("\$info_cols['$columna'] = utf8_encode(\"$".$columna."_nombre\");");
                                }
                                
                                $info['unicef'][$ppp] = $info_cols;

                                $ppp++;

                            }

                        }

                    }

                }
            }

        }
        */

        $json = json_encode($info);
        echo $json;
        $_SESSION['xls'] = $xls.'</table>';
	}
	
    /**
	 * Reporta la informacion en cuadro
	 * @access public
	 * @param string $caso que,socio,donante
     * @param array $filtros Arreglo con los diferentes filtros
	 */	
	function reporteEjecutado($caso,$filtros){
        
        // LIBRERIAS
        include($_SERVER['DOCUMENT_ROOT'].'/sissh/admin/lib/common/date.class.php');
        // INICIALIZACION
        $conv_dao = FactoryDAO::factory('unicef_convenio');
        $date = new Date();
        $cond = '';
        $mdgd = '';
        $id_depto_mpio = 0;
        
        if ($filtros['fecha_inicio_ini'] != '') $cond = "fecha_ini >= '".$filtros['fecha_inicio_ini']."'";
        if ($filtros['fecha_inicio_fin'] != '') $cond .= " AND fecha_ini <= '".$filtros['fecha_inicio_fin']."'";
        
        if ($filtros['fecha_finalizacion_ini'] != ''){
            if ($cond != '')    $cond .= ' AND ';
            $cond .= "fecha_fin >= '".$filtros['fecha_finalizacion_ini']."'";
        }

        if ($filtros['fecha_finalizacion_fin'] != ''){
            if ($cond != '')    $cond .= ' AND ';
            $cond .= "fecha_fin <= '".$filtros['fecha_finalizacion_fin']."'";
        }
        
        if ($filtros['filtro'] == 'comps'){
            if ($cond != '')    $cond .= ' AND ';
            $cond .= 'id_componente IN ('.$filtros['id_filtro'].')';
        }
        
        // Socios
        if (strpos($filtros['filtro'],'socio') !== false){
            $tmp = explode('-',$filtros['id_filtro']);

            if ($cond != '')    $cond .= ' AND ';
            $cond .= ' id_componente IN ('.$tmp[0].') AND id_socio = '.$tmp[1];
        }
        
        // Donantes
        if (strpos($filtros['filtro'],'donante') !== false){
            $tmp = explode('-',$filtros['id_filtro']);

            if ($cond != '')    $cond .= ' AND ';
            $cond .= ' id_componente IN ('.$tmp[0].') AND id_donante = '.$tmp[1];
        }
        
        $cond_conv = $cond;
        if (isset($filtros['id_depto_mpio'])){
            $mdgd = $filtros['mdgd'];
            $id_depto_mpio = $filtros['id_depto_mpio'];
            switch($mdgd){
                case 'deptal':
                    $cond_conv = " id_depto = $id_depto_mpio";
                break;
                case 'mpal':
                    $cond_conv = "id_mun = $id_depto_mpio";
                break;

                if ($cond != '')    $cond_conv = " AND $cond_conv";
            }
        }
        ?>

        <?php
        $th_col_xls = '<table border=1><tr>
                        <td>Convenio</td>
                        <td>[AWP] Componente</td>  
                        <td>[AWP] Proyecto</td>
                        <td>[AWP] Actividad</td>
                        <td>Objectivo del Convenio</td>
                        <td>Estado</td>
                        <td>F. Inicio</td>
                        <td>F. Finalización</td>
                        <td>Meses</td>
                        <td>Avances</td>
                        <td>Valor Total Convenio</td>
                        <td>Cobertura</td>
                        ';
                        
                        /*
                        // Aportes
                        $fecha_avs = $conv_dao->getInfoQue('avance',$cond);
                        foreach ($fecha_avs as $s=>$fecha){
                            //$avance = $conv_dao->getAvanceById($id_av);
                            //$id_avs_fecha[$id_av] = $avance['fecha'];
                            $th_col_xls .= "<td>Avance ".($s+1).": ".$fecha."</td>";
                        }
                        */

                        // Deptos - Mpios
                        if ($mdgd == 'deptal'){
                            $depto = $this->depto_dao->Get($id_depto_mpio);
                            $th_col_xls .= "<td>Depto</td>";
                        }
                        else if ($mdgd == 'mpal'){
                            $mun = $this->mun_dao->Get($id_depto_mpio);
                            $depto = $this->depto_dao->Get($mun->id_depto);
                            $th_col_xls .= "<td>Depto</td><td>Mpios/td>";
                        }
                        else{
                            $id_deptos = $conv_dao->getInfoQue('depto',$cond);
                            foreach ($id_deptos as $id_depto){
                                $depto = $this->depto_dao->Get($id_depto);
                                $id_deptos_nom[$id_depto] = $depto->nombre;
                                $th_col_xls .= "<td>$depto->nombre</td><td>Mpios</td>";
                            }
                        }

                        // Socios
                        $id_socios = $conv_dao->getInfoQue('socio',$cond);
                        foreach ($id_socios as $s=>$id_socio){
                            $socio = $this->socio_dao->Get($id_socio);
                            $id_socios_nom[$id_socio] = $socio->nombre;
                            $th_col_xls .= "<td>Socio ".($s+1).": $socio->nombre</td>";
                        }
                
                $th_col_xls .= '</tr>';
       
        $xls = $th_col_xls;
        foreach (explode(',',$filtros['id_filtro']) as $i_f => $id_f){
           
           $cond_c = $cond_conv;
           if ($cond_c != '') $cond_c .= " AND ";
           $cond_c .= "id_componente = $id_f";

           $all_info = $conv_dao->GetTreeIDReporte($cond_c,$mdgd,$id_depto_mpio);
           $id_convs = (isset($all_info['id'])) ? $all_info['id'] : array();
           foreach ($id_convs as $i=>$id_conv){
                
                $conv = $conv_dao->Get($id_conv);
                $estado = $this->estado_dao->Get($conv->id_estado);
                $meses = floor($date->RestarFechas($conv->fecha_ini,$conv->fecha_fin,'meses'));
                
                $id_muns = implode(',',$conv->id_mun);
                $cob_nombre = $this->cobertura_t[$conv->cobertura];
                $codigo_nombre = '<a href="download_pdf.php?c=7&id='.$id_conv.'"><img src="images/unicef/pdf.gif"></a>&nbsp;'.$conv->codigo;
                $comp_nombre = 'CP '.$id_f;
                $sub_c_nombre = $all_info['sub_c'][$i];
                $res_nombre = $all_info['res'][$i];
                $p_cpap_nombre = $all_info['p_cpap'][$i];
                $act_nombre = $all_info['act'][$i];
                $obj_nombre = $conv->nombre;
                $f_ini_nombre = $date->Format($conv->fecha_ini,'aaaa-mm-dd','dd-MC-aaaa');
                $f_fin_nombre = $date->Format($conv->fecha_fin,'aaaa-mm-dd','dd-MC-aaaa');
                $meses_nombre = $meses;
                $estado_nombre = $estado->nombre;

                // hoja calculo
                $txt_basico = "<tr>
                            <td>$codigo_nombre</td>
                            <td>$comp_nombre</td>
                            <td>$p_cpap_nombre</td>
                            <td>$act_nombre</td>
                            <td>$obj_nombre</td>
                            <td>$estado_nombre</td>
                            <td>$f_ini_nombre</td>
                            <td>$f_fin_nombre</td>
                            <td>$meses_nombre</td>
                            ";
                
                $xls .= $txt_basico;

                // Avances
                $avances_nombre = '';
                foreach ($conv->avances_ex as $a=>$avance_valor){
                    
                    if ($a > 0) $avances_nombre .= '<br />';

                    $fecha = $date->Format($conv->avances_fecha[$a],'aaaa-mm-dd','dd-MM-aaaa');
                    $avances_nombre .= '<b>Avance '.($a+1).'</b>: $ '.$avance_valor.' ('.$fecha.')';

                    $id_fuente_avance = $conv_dao->getFuenteAvance($a,$conv->id);
                    foreach ($id_fuente_avance as $id_fuente){
                        $fuente = $this->fuente_dao->Get($id_fuente);
                        $avances_nombre .= "<br /> Fuente: $fuente->nombre";

                        $donante = $this->donante_dao->Get($fuente->id_donante);

                        $avances_nombre .= "<br /> Donante: $donante->nombre";

                    }
                }

                $xls .= "<td>$avances_nombre</td>";

                // Presupuesto
                $xls .= "<td>$ $conv->presupuesto_ex</td>";
                $presu_nombre = $conv->presupuesto_ex;

                // Cobertura
                $depto_mpio_nombre = '';
                $xls .= '<td>'.$cob_nombre.'</td>';
                
                if ($mdgd == 'deptal'){
                    $xls .= "<td>$depto->nombre</td>";
                }
                else if ($mdgd == 'mpal'){
                    $xls .= "<td>$depto->nombre</td><td>$mun->nombre</td>";
                }
                else{
                    foreach ($conv->id_depto as $d=>$id_depto){

                        if ($d > 0)    $depto_mpio_nombre .= ' - ';

                        $nom_depto = $this->depto_dao->GetName($id_depto);
                        $depto_mpio_nombre .= "<b>$nom_depto</b>";

                        if ($id_muns != ''){ 
                            $id_muns_d = $this->mun_dao->GetAllArrayID("id_depto = $id_depto AND id_mun IN ($id_muns)",'');

                            $depto_mpio_nombre .= " ".$this->actividad_dao->getNames('municipio',$id_muns_d);
                        }
                    }

                    // xls
                    foreach ($id_deptos_nom as $id_depto=>$depto_nombre){
                        if (in_array($id_depto,$conv->id_depto))   $xls .= "<td>$depto_nombre</td>";
                        else                                        $xls .= '<td></td>';

                        if ($id_muns != ''){ 
                            $id_muns_d = $this->mun_dao->GetAllArrayID("id_depto = $id_depto AND id_mun IN ($id_muns)",'');
                        
                            $xls .= '<td>'.$this->actividad_dao->getNames('municipio',$id_muns_d).'</td>';
                        }
                        else    $xls .= '<td></td>';
                        
                    }
                }
                
                // Socios
                $socio_nombre = $this->actividad_dao->getNames('unicef_socio',$conv->id_socio_implementador);
                //echo "<td>$socios</td>";
                
                // xls
                foreach ($id_socios_nom as $id_socio=>$s_nombre){
                    if (in_array($id_socio,$conv->id_socio_implementador))   $xls .= "<td>$s_nombre</td>";
                    else                                                        $xls .= '<td></td>';
                }

                //echo '</tr>';
            
                $info_cols = array();
                foreach($this->grid_columns['ejecutado'] as $columna){
                    eval("\$info_cols['$columna'] = utf8_encode(\"$".$columna."_nombre\");");
                }

                $info['unicef'][$i] = $info_cols;
            
            }
        }
        
        // Reporte que no viene desde el mapa
        if (!isset($filtros['mapa'])){
            $json = json_encode($info);
            echo $json;
        }

        $_SESSION['xls'] = $xls.'</table>';
	}
}

?>
