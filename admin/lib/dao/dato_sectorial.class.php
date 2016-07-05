<?php
/**
 * DAO de DatoSectorial.
 *
 * Contiene los métodos de la clase DatoSectorial
 *
 * @author Ruben A. Rojas C.
 */
class DatoSectorialDAO
{
    /*
     * Conexión a la base de datos
     * @var object
     */
    public $conn;

    /*
     * Nombre de la Tabla en la Base de Datos
     * @var string
     */
    public $tabla;

    /*
     * Nombre de la columna ID de la Tabla en la Base de Datos
     * @var string
     */
    public $columna_id;

    /*
     * Nombre de la columna Nombre de la Tabla en la Base de Datos
     * @var string
     */
    public $columna_nombre;

    /*
     * Nombre de la columna para ordenar el RecordSet
     * @var string
     */
    public $columna_order;

    /**
     * Constructor
     * Crea la conexi�n a la base de datos.
     */
    public function DatoSectorialDAO()
    {
        $this->conn = MysqlDb::getInstance();
        $this->tabla = 'dato_sector';
        $this->columna_id = 'ID_DATO';
        $this->columna_nombre = 'NOM_DATO';
        $this->columna_order = 'NOM_DATO';
    }

    /**
     * Consulta los datos de una DatoSectorial.
     *
     * @param int $id ID del DatoSectorial
     *
     * @return VO
     */
    public function Get($id)
    {
        $sql = 'SELECT * FROM '.$this->tabla.' WHERE '.$this->columna_id.' = '.$id;
        $rs = $this->conn->OpenRecordset($sql);
        $row_rs = $this->conn->FetchObject($rs);

        //Crea un VO
        $vo = new DatoSectorial();

        //Carga el VO
        $vo = $this->GetFromResult($vo, $row_rs);

        //Retorna el VO
        return $vo;
    }

    /**
     * Retorna el max ID.
     *
     * @return int
     */
    public function GetMaxID()
    {
        $sql = 'SELECT max(ID_PROY) as maxid FROM '.$this->tabla;
        $rs = $this->conn->OpenRecordset($sql);
        if ($row_rs = $this->conn->FetchRow($rs)) {
            return $row_rs[0];
        } else {
            return 0;
        }
    }

    /**
     * Retorna el numero de Registros de Datos.
     *
     * @param string $condicion
     *
     * @return int
     */
    public function numRecords($condicion)
    {
        $sql = 'SELECT count('.$this->columna_id.') as num FROM '.$this->tabla;
        if ($condicion != '') {
            $sql .= ' WHERE '.$condicion;
        }
        $rs = $this->conn->OpenRecordset($sql);
        $row_rs = $this->conn->FetchRow($rs);

        return $row_rs[0];
    }

    /**
     * Consulta el nombre del Dato.
     *
     * @param int $id ID del Dato
     *
     * @return VO
     */
    public function GetName($id)
    {
        $sql = 'SELECT '.$this->columna_nombre.' FROM '.$this->tabla.' WHERE '.$this->columna_id.' = '.$id;
        $rs = $this->conn->OpenRecordset($sql);
        $row_rs = $this->conn->FetchRow($rs);

        //Retorna el VO
        return $row_rs[0];
    }

    /**
     * Consulta la unidad del Dato.
     *
     * @param int $id ID del dato
     *
     * @return int $id_unidad
     */
    public function GetUnidad($id)
    {
        $dato = $this->Get($id);

        if ($dato->formula == '') {
            $sql = 'SELECT id_unidad FROM valor_dato WHERE '.$this->columna_id.' = '.$id.' LIMIT 0,1';
            $rs = $this->conn->OpenRecordset($sql);
            $row_rs = $this->conn->FetchRow($rs);

            $id_unidad = $row_rs[0];
        } else {
            $id_unidad = $dato->id_unidad;
        }

        return $id_unidad;
    }

    /**
     * Retorna el numero de Registros de Valores de Datos.
     *
     * @param string $condicion
     *
     * @return int
     */
    public function numRecordsValores($condicion)
    {
        $sql = 'SELECT count('.$this->columna_id.') as num FROM valor_dato';
        if ($condicion != '') {
            $sql .= ' WHERE '.$condicion;
        }
        $rs = $this->conn->OpenRecordset($sql);
        $row_rs = $this->conn->FetchRow($rs);

        return $row_rs[0];
    }

    /**
     * Consulta Vos.
     *
     * @param string $condicion Condici�n que deben cumplir los Tema y que se agrega en el SQL statement.
     * @param string $limit     Limit en el SQL
     * @param string $order     by Order by en el SQL
     *
     * @return array Arreglo de VOs
     */
    public function GetAllArray($condicion, $limit = '', $order_by = '')
    {
        $sql = 'SELECT * FROM '.$this->tabla;

        if ($condicion != '') {
            $sql .= ' WHERE '.$condicion;
        }

        //ORDER
        $sql .= ($order_by != '') ?  " ORDER BY $order_by" : ' ORDER BY '.$this->columna_order;

        //LIMIT
        if ($limit != '') {
            $sql .= ' LIMIT '.$limit;
        }

        $array = array();

        $rs = $this->conn->OpenRecordset($sql);
        while ($row_rs = $this->conn->FetchObject($rs)) {
            //Crea un VO
            $vo = new DatoSectorial();
            //Carga el VO
            $vo = $this->GetFromResult($vo, $row_rs);
            //Carga el arreglo
            $array[] = $vo;
        }
        //Retorna el Arreglo de VO
        return $array;
    }

    /**
     * Consulta los datos de los Depto que cumplen una condici�n.
     *
     * @param string $condicion Condici�n que deben cumplir los Depto y que se agrega en el SQL statement.
     *
     * @return array Arreglo de ID
     */
    public function GetAllArrayID($condicion)
    {
        $sql = 'SELECT '.$this->columna_id.' FROM '.$this->tabla.'';

        if ($condicion != '') {
            $sql .= ' WHERE '.$condicion;
        }

        $sql .= ' ORDER BY '.$this->columna_order;

        $array = array();

        $rs = $this->conn->OpenRecordset($sql);
        while ($row_rs = $this->conn->FetchRow($rs)) {
            //Carga el arreglo
            $array[] = $row_rs[0];
        }

        //Retorna el Arreglo
        return $array;
    }

    /**
     * Consulta el valor de un DatoSectorial.
     *
     * @param int $id        ID del DatoSectorial
     * @param int $dato_para
     *
     * @return VO
     */
    public function GetValor($condicion, $dato_para)
    {

        //Crea un VO
        $vo = new DatoSectorial();

        /*if ($dato_para == 1){
          $sql = "SELECT VALOR_DATO FROM depto_dato WHERE ".$condicion;
          }
          else if ($dato_para == 2){
          $sql = "SELECT VALOR_DATO FROM mpio_dato WHERE ".$condicion;
          }
          else if ($dato_para == 3){
          $sql = "SELECT VALOR_DATO FROM poblado_dato WHERE ".$condicion;
          }*/

        switch ($dato_para) {
            case '1':
                $cond_d = 'id_depto IS NOT NULL';
            break;
            case '2':
                $cond_d = 'id_mun IS NOT NULL';
            break;
        }

        if (empty($condicion)) {
            $condicion = $cond_d;
        } else {
            $condicion .= ' AND '.$cond_d;
        }

        $sql = 'SELECT VAL_VALDA, INI_VALDA, FIN_VALDA, ID_UNIDAD FROM valor_dato
                WHERE '.$condicion;

        $rs = $this->conn->OpenRecordset($sql);
        $row_rs = $this->conn->Fetchrow($rs);
        $vo->valor = $row_rs[0];
        $vo->fecha_ini = $row_rs[1];
        $vo->fecha_fin = $row_rs[2];
        $vo->id_unidad = $row_rs[3];

        //Retorna el VO
        return $vo;
    }

    /**
     * Consulta el periodo mas reciente del valor de un dato.
     *
     * @param int $id_dato ID del DatoSectorial
     *
     * @return array fecha_val('ini'=>$fecha_ini,'fin'=>$fecha_fin)
     */
    public function GetMaxFecha($id_dato)
    {
        include_once $_SERVER['DOCUMENT_ROOT'].'/sissh/admin/lib/common/cadena.class.php';

        $cadena = new Cadena();

        $vo = $this->Get($id_dato);
        if ($vo->formula != '') {
            $id_datos = $cadena->getContentTag($vo->formula, '[', ']');
            $id_dato = $id_datos[0];
        }

        $sql = "SELECT max(INI_VALDA), max(FIN_VALDA) FROM valor_dato WHERE ID_DATO = $id_dato";
        //echo $sql;
        $rs = $this->conn->OpenRecordset($sql);
        $row_rs = $this->conn->FetchRow($rs);
        $fecha_val['ini'] = $row_rs[0];
        $fecha_val['fin'] = $row_rs[1];

        return $fecha_val;
    }

    /**
     * Consulta el prmier periodo en el que un dato tiene valor.
     *
     * @param int $id_dato ID del DatoSectorial
     *
     * @return array fecha_val('ini'=>$fecha_ini,'fin'=>$fecha_fin)
     */
    public function GetMinFecha($id_dato)
    {
        include_once $_SERVER['DOCUMENT_ROOT'].'/sissh/admin/lib/common/cadena.class.php';

        $cadena = new Cadena();

        $vo = $this->Get($id_dato);
        if ($vo->formula != '') {
            $id_datos = $cadena->getContentTag($vo->formula, '[', ']');
            $id_dato = $id_datos[0];
        }

        $sql = "SELECT min(INI_VALDA), min(FIN_VALDA) FROM valor_dato WHERE ID_DATO = $id_dato";

        //echo $sql;
        $rs = $this->conn->OpenRecordset($sql);
        $row_rs = $this->conn->FetchRow($rs);
        $fecha_val['ini'] = $row_rs[0];
        $fecha_val['fin'] = $row_rs[1];

        return $fecha_val;
    }

    /**
     * Consulta los periodos en los que un dato tiene valor.
     *
     * @param int $id_dato ID del DatoSectorial
     * @param string $full Retornar toda la fecha o solo años
     *
     * @return array periodos('ini'=>array(),'fin'=>array())
     */
    public function GetPeriodos($id_dato,$full = true)
    {
        include_once $_SERVER['DOCUMENT_ROOT'].'/sissh/admin/lib/common/cadena.class.php';

        $cadena = new Cadena();

        $a = array();

        $vo = $this->Get($id_dato);
        if ($vo->formula != '') {
            $id_datos = $cadena->getContentTag($vo->formula, '[', ']');

            foreach ($id_datos as $id_d) {
                return $this->GetPeriodos($id_d, $full);
            }
        }
        else {

            $ini_txt = ($full) ? 'ini_valda' : 'YEAR(ini_valda)';
            $fin_txt = ($full) ? 'fin_valda' : 'YEAR(fin_valda)';

            $sql = "SELECT DISTINCT $ini_txt, $fin_txt FROM valor_dato WHERE id_dato = $id_dato ORDER BY YEAR(ini_valda)";
            $rs = $this->conn->OpenRecordset($sql);
            while ($row_rs = $this->conn->FetchRow($rs)) {
                $a['ini'][] = $row_rs[0];
                $a['fin'][] = $row_rs[1];
            }
        }

        return $a;
    }

    /**
     * Consulta los años de los periodos en los que un dato tiene valor.
     *
     * @param int $id_dato ID del DatoSectorial
     *
     * @return array
     */
    public function GetAnios($id_dato)
    {
        return $this->GetPeriodos($id_dato,false);

    }

    /**
     * Consulta las fechas en las que un dato tiene valores.
     *
     * @param int $id_dato ID del DatoSectorial
     *
     * @return array fechas(array('ini'=>$fecha_ini,'fin'=>$fecha_fin))
     */
    public function GetPeriodosValores($id_dato)
    {
        $array = array();

        $sql = "SELECT DISTINCT INI_VALDA, FIN_VALDA FROM valor_dato where ID_DATO = $id_dato ORDER BY INI_VALDA";
        //echo $sql;
        $rs = $this->conn->OpenRecordset($sql);
        while ($row_rs = $this->conn->FetchRow($rs)) {
            $array[] = array('ini' => $row_rs[0], 'fin' => $row_rs[1]);
        }

        return $array;
    }

    /**
     * Carga un VO de DatoSectorial con los datos de la consulta.
     *
     * @param object $vo        VO de DatoSectorial que se va a recibir los datos
     * @param object $Resultset Resource de la consulta
     *
     * @return object $vo VO de DatoSectorial con los datos
     */
    public function GetFromResult($vo, $Result)
    {
        $vo->id = $Result->{$this->columna_id};
        $vo->nombre = $Result->{$this->columna_nombre};

        $vo->id_contacto = $Result->ID_CONP;
        $vo->id_sector = $Result->ID_COMP;
        $vo->id_cat = $Result->ID_CATE;
        $vo->id_unidad = $Result->ID_UNIDAD;
        $vo->nombre = $Result->NOM_DATO;
        $vo->desagreg_geo = $Result->DESAGREG_GEO;
        $vo->formula = $Result->FORMULA_DATO;
        $vo->tipo_calc_nal = $Result->TIPO_CALC_NAL;
        $vo->tipo_calc_deptal = $Result->TIPO_CALC_DEPTAL;
        $vo->definicion = $Result->DEFINICION_DATO;
        $vo->tipo_calc_reg = $Result->TIPO_CALC_REG;
        //$vo->formula_calc_reg = $Result->FORMULA_CALC_REG;
        //$vo->formula_calc_nal = $Result->FORMULA_CALC_NAL;
        //$vo->formula_calc_deptal = $Result->FORMULA_CALC_DEPTAL;

        //DEPTO
        /*$sql_s = "SELECT ID_DEPTO FROM depto_dato WHERE ID_DATO = ".$id;
          $rs_s = $this->conn->OpenRecordset($sql_s);
          $row_rs_s = $this->conn->FetchRow($rs_s);
          $vo->id_depto = $row_rs_s[0];

        //MUN
        $sql_s = "SELECT ID_MUN FROM mpio_dato WHERE ID_DATO = ".$id;
        $rs_s = $this->conn->OpenRecordset($sql_s);
        $row_rs_s = $this->conn->FetchRow($rs_s);
        $vo->id_mun = $row_rs_s[0];

        //POBLADO
        $sql_s = "SELECT ID_POB FROM poblado_dato WHERE ID_DATO = ".$id;
        $rs_s = $this->conn->OpenRecordset($sql_s);
        $row_rs_s = $this->conn->FetchRow($rs_s);
        $vo->id_poblado = $row_rs_s[0];*/

        return $vo;
    }

    /**
     * Consulta el valor de un Dato Sectorial, los datos formulados
     * son consultados en la misma tabla valor_dato, la cual es populada
     * con el script cron_jobs/totalizar_d_sectorial_formulado.php, el
     * cual popula los datos formulados cuyos datos han sido modificados.
     *
     * @param int $id_dato      ID del DatoSectorial
     * @param int $id_ubicacion ID de la Ubicacion
     * @param int $f_ini        Fecha inicio vigencia
     * @param int $f_fin        Fecha fin vigencia
     * @param int $dato_para
     * @param int $sumada       1 => En la nueva versi�n de la funci�n ya no se usa este par�metro
     *
     * @return array $valor,$id_unidad
     */
    public function GetValorToReport($id_dato, $id_ubicacion, $f_ini, $f_fin, $dato_para, $sumada = 0)
    {

        //INICIALIZA VARIABLES
        $mun_dao = new MunicipioDAO();
        $cadena = new Cadena();

        $dato_vo = $this->Get($id_dato);

        $cond_fecha = " AND INI_VALDA >= '$f_ini' AND FIN_VALDA <= '$f_fin'";

        //ELIMINA CARACTER DE COMILLAS
        $id_ubicacion = str_replace("'", '', $id_ubicacion);
        $valor = 'N.D.';
        $id_unidad = 0;

        //DEPARTAMENTO
        if ($dato_para == 1) {
            if ($dato_vo->desagreg_geo == 'municipal') {
                if ($dato_vo->tipo_calc_deptal == 'suma_mpio' || $dato_vo->tipo_calc_deptal == 'suma_formula') {
                    $sql = 'SELECT TOTAL_DEPTAL, ID_UNIDAD FROM total_deptal_valor_dato WHERE ID_DATO = '.$id_dato.' AND ID_DEPTO = '.$id_ubicacion.$cond_fecha;
                    //echo "$sql<br>";
                    $rs = $this->conn->OpenRecordset($sql);
                    $row_rs = $this->conn->Fetchrow($rs);

                    $valor = $row_rs[0];
                    $id_unidad = $row_rs[1];
                } else {
                    $sql = "SELECT VAL_VALDA,ID_UNIDAD FROM valor_dato WHERE ID_DATO = $id_dato AND ID_DEPTO = '$id_ubicacion'".$cond_fecha;
                    //echo "$sql<br>";
                    $rs = $this->conn->OpenRecordset($sql);
                    if ($this->conn->RowCount($rs) > 0) {
                        $row_rs = $this->conn->Fetchrow($rs);
                        $valor = $row_rs[0];
                        $id_unidad = $row_rs[1];
                    }
                }
            } else {
                if ($dato_vo->desagreg_geo == 'departamental') {
                    $sql = "SELECT VAL_VALDA,ID_UNIDAD FROM valor_dato WHERE ID_DATO = $id_dato AND ID_DEPTO = '$id_ubicacion'".$cond_fecha;
                    //echo "$sql<br>";
                    $rs = $this->conn->OpenRecordset($sql);
                    if ($this->conn->RowCount($rs) > 0) {
                        $row_rs = $this->conn->Fetchrow($rs);
                        $valor = $row_rs[0];
                        $id_unidad = $row_rs[1];
                    }
                }
            }
        }
        //VALOR MPIO
        elseif ($dato_para == 2) {
            $sql = "SELECT VAL_VALDA, ID_UNIDAD FROM valor_dato WHERE ID_DATO = $id_dato AND ID_MUN = '$id_ubicacion'".$cond_fecha;
            $rs = $this->conn->OpenRecordset($sql);
            if ($this->conn->RowCount($rs) > 0) {
                $row_rs = $this->conn->Fetchrow($rs);
                $valor = $row_rs[0];
                $id_unidad = $row_rs[1];
            }
        }
        //VALOR NACIONAL
        elseif ($dato_para == 3) {
            if (($dato_vo->tipo_calc_nal == 'suma_mpio' || $dato_vo->tipo_calc_nal == 'suma_depto')) {
                $sql = 'SELECT TOTAL_NACIONAL, ID_UNIDAD FROM total_nacional_valor_dato WHERE ID_DATO = '.$id_dato.$cond_fecha;
                //echo "$sql<br>";
                $rs = $this->conn->OpenRecordset($sql);
                $row_rs = $this->conn->Fetchrow($rs);

                $valor = $row_rs[0];
                $id_unidad = $row_rs[1];
            } else {
                $sql = "SELECT VAL_VALDA,ID_UNIDAD FROM valor_dato WHERE ID_DATO = $id_dato AND ID_DEPTO = '00'".$cond_fecha;
                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0) {
                    $row_rs = $this->conn->Fetchrow($rs);
                    $valor = $row_rs[0];
                    $id_unidad = $row_rs[1];
                } else {
                    $sql = "SELECT VAL_VALDA,ID_UNIDAD FROM valor_dato WHERE ID_DATO = $id_dato AND ID_MUN = '00000'".$cond_fecha;
                    $rs = $this->conn->OpenRecordset($sql);
                    if ($this->conn->RowCount($rs) > 0) {
                        $row_rs = $this->conn->Fetchrow($rs);
                        $valor = $row_rs[0];
                        $id_unidad = $row_rs[1];
                    }
                }
            }
        }

        return array('valor' => $valor, 'id_unidad' => $id_unidad);
    }

    /**
     * Consulta el valor de un Dato Sectorial formulado.
     *
     * @param int $id_dato      ID del DatoSectorial
     * @param int $id_ubicacion ID de la Ubicacion
     * @param int $f_ini        Fecha inicio vigencia
     * @param int $f_fin        Fecha fin vigencia
     * @param int $dato_para
     * @param int $sumada       1 => En la nueva versi�n de la funci�n ya no se usa este par�metro
     *
     * @return array $valor,$id_unidad
     */
    public function GetValorFormulado($id_dato, $id_ubicacion, $f_ini, $f_fin, $dato_para, $sumada = 0)
    {

        //INICIALIZA VARIABLES
        $mun_dao = new MunicipioDAO();
        $cadena = new Cadena();

        $dato_vo = $this->Get($id_dato);

        $cond_fecha = " AND INI_VALDA >= '$f_ini' AND FIN_VALDA <= '$f_fin'";

        //ELIMINA CARACTER DE COMILLAS
        $id_ubicacion = str_replace("'", '', $id_ubicacion);
        $valor = 'N.D.';
        $id_unidad = 0;

        //DEPARTAMENTO
        if ($dato_para == 1) {
            if ($dato_vo->desagreg_geo == 'municipal' && $dato_vo->formula == '') {
                if ($dato_vo->tipo_calc_deptal == 'suma_mpio' || $dato_vo->tipo_calc_deptal == 'suma_formula') {
                    $sql = 'SELECT TOTAL_DEPTAL, ID_UNIDAD FROM total_deptal_valor_dato WHERE ID_DATO = '.$id_dato.' AND ID_DEPTO = '.$id_ubicacion.$cond_fecha;
                    //echo "$sql<br>";
                    $rs = $this->conn->OpenRecordset($sql);
                    $row_rs = $this->conn->Fetchrow($rs);

                    $valor = $row_rs[0];
                    $id_unidad = $row_rs[1];
                } elseif ($dato_vo->tipo_calc_deptal == 'formula') {
                    $formula = $dato_vo->formula_calc_deptal;
                    $id_subdatos = $cadena->getContentTag($formula, '[', ']');
                    $id_unidad = $dato_vo->id_unidad;

                    foreach ($id_subdatos as $id_sd) {
                        $sql = 'SELECT TOTAL_DEPTAL FROM total_deptal_valor_dato WHERE ID_DATO = '.$id_sd.' AND ID_DEPTO = '.$id_ubicacion.$cond_fecha;
                        //echo "$sql<br>";
                        $rs = $this->conn->OpenRecordset($sql);
                        $row_rs = $this->conn->Fetchrow($rs);
                        $valor = $row_rs[0];

                        if (strcmp('N.D.', $valor) != 0) {
                            $formula = str_replace("[$id_sd]", $valor, $formula);
                        }

                        //echo "formula::::$formula:::$id_ubicacion::::$dato_vo->tipo_calc_deptal<br>";
                    }

                    if (strpos($formula, '[') === false) {
                        eval('$valor = '.$formula.';');
                    }
                } else {
                    $sql = "SELECT VAL_VALDA,ID_UNIDAD FROM valor_dato WHERE ID_DATO = $id_dato AND ID_DEPTO = '$id_ubicacion'".$cond_fecha;
                    //echo "$sql<br>";
                    $rs = $this->conn->OpenRecordset($sql);
                    if ($this->conn->RowCount($rs) > 0) {
                        $row_rs = $this->conn->Fetchrow($rs);
                        $valor = $row_rs[0];
                        $id_unidad = $row_rs[1];
                    }
                }
            } else {
                if ($dato_vo->desagreg_geo == 'departamental' && $dato_vo->formula == '') {
                    $sql = "SELECT VAL_VALDA,ID_UNIDAD FROM valor_dato WHERE ID_DATO = $id_dato AND ID_DEPTO = '$id_ubicacion'".$cond_fecha;
                    //echo "$sql<br>";
                    $rs = $this->conn->OpenRecordset($sql);
                    if ($this->conn->RowCount($rs) > 0) {
                        $row_rs = $this->conn->Fetchrow($rs);
                        $valor = $row_rs[0];
                        $id_unidad = $row_rs[1];
                    }
                } else {
                    $dato_f_vo = $this->Get($id_dato);
                    $id_unidad = $dato_f_vo->id_unidad;

                    $formula = $dato_vo->formula;
                    $id_datos = $cadena->getContentTag($formula, '[', ']');

                    foreach ($id_datos as $id_d) {

                        //CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
                        $fecha_val = $this->GetMaxFecha($id_d);
                        if ($f_fin != $fecha_val['fin']) {
                            $f_fin = $fecha_val['fin'];
                        }
                        $vr = $this->GetValorToReport($id_d, $id_ubicacion, $f_ini, $f_fin, $dato_para);
                        $val = $vr['valor'];

                        //if (!empty($val) && strcmp("N.D.",$val) != 0){
                        if (strcmp('N.D.', $val) != 0) {
                            $formula = str_replace("[$id_d]", $val, $formula);
                        } else {
                            $formula = 'N.D.';
                        }
                    }
                    if (strpos($formula, '[') === false && $formula != 'N.D.') {
                        eval('$valor = '.$formula.';');
                    } else {
                        $valor = 'N.D.';
                    }
                }
            }
        }
        //VALOR MPIO
        elseif ($dato_para == 2) {

            $sql = "SELECT VAL_VALDA, ID_UNIDAD FROM valor_dato WHERE ID_DATO = $id_dato AND ID_MUN = '$id_ubicacion'".$cond_fecha;
            //echo $sql;
            $rs = $this->conn->OpenRecordset($sql);

            if ($this->conn->RowCount($rs) > 0) {
                $row_rs = $this->conn->Fetchrow($rs);
                $valor = $row_rs[0];
                $id_unidad = $row_rs[1];
            } else {

                if ($dato_vo->formula != '') {

                    $dato_f_vo = $this->Get($id_dato);
                    $id_unidad = $dato_f_vo->id_unidad;

                    $formula = $dato_vo->formula;
                    $id_datos = $cadena->getContentTag($formula, '[', ']');

                    foreach ($id_datos as $id_d) {

                        //CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
                        $fecha_val = $this->GetMaxFecha($id_d);
                        if ($f_fin != $fecha_val['fin']) {
                            $f_fin = $fecha_val['fin'];
                        }
                        //$vr = $this->GetValorToReport($id_d,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],$dato_para);
                        $vr = $this->GetValorToReport($id_d, $id_ubicacion, $f_ini, $f_fin, $dato_para);
                        $val = $vr['valor'];

                        if (strcmp('N.D.', $val) != 0) {
                            $formula = str_replace("[$id_d]", $val, $formula);
                        } else {
                            $formula = 'N.D.';
                        }
                    }

                    //echo "dato:::::$dato_vo->id:::::formula::::$formula:::$id_ubicacion::::$dato_vo->formula<br>";
                    if (strpos($formula, '[') === false && $formula != 'N.D.') {
                        eval('$valor = '.$formula.';');
                    } else {
                        $valor = 'N.D.';
                    }
                }
            }
        }
        //VALOR NACIONAL
        elseif ($dato_para == 3) {

            //if ($dato_vo->tipo_calc_nal == 'suma_mpio' || $dato_vo->tipo_calc_nal == 'suma_depto' || $dato_vo->tipo_calc_nal == 'suma_formula'){
            if (($dato_vo->tipo_calc_nal == 'suma_mpio' || $dato_vo->tipo_calc_nal == 'suma_depto') && $dato_vo->formula == '') {
                $sql = 'SELECT TOTAL_NACIONAL, ID_UNIDAD FROM total_nacional_valor_dato WHERE ID_DATO = '.$id_dato.$cond_fecha;
                //echo "$sql<br>";
                $rs = $this->conn->OpenRecordset($sql);
                $row_rs = $this->conn->Fetchrow($rs);

                $valor = $row_rs[0];
                $id_unidad = $row_rs[1];
            } elseif ($dato_vo->formula != '') {
                $dato_f_vo = $this->Get($id_dato);
                $id_unidad = $dato_f_vo->id_unidad;

                $formula = $dato_vo->formula;
                $id_datos = $cadena->getContentTag($formula, '[', ']');
                //var_dump($id_datos);
                foreach ($id_datos as $id_d) {

                    //$vr = $this->GetValorToReport($id_d,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],$dato_para);
                    $vr = $this->GetValorToReport($id_d, $id_ubicacion, $f_ini, $f_fin, $dato_para);
                    //echo "dato_en_formula = $id_d";
                    //var_dump($vr);
                    $val = $vr['valor'];

                    // Si no tiene valores para el a�o seleccionado, pero si el multiplicador o denominador, toma
                    // el valor mas reciente
                    /*
                    if (strcmp("N.D.",$val) == 0) {

                        //CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
                        $fecha_val = $this->GetMaxFecha($id_d);

                        if ($f_fin != $fecha_val['fin'])	$f_fin = $fecha_val['fin'];

                        $vr = $this->GetValorToReport($id_d,$id_ubicacion,$f_ini,$f_fin,$dato_para);
                    }
                     */

                    if (strcmp('N.D.', $val) != 0) {
                        $formula = str_replace("[$id_d]", $val, $formula);
                        //echo "formula::::$formula:::$id_ubicacion::::$dato_vo->formula::::dato=$id_dato::::f_ini=$f_ini::::f_fin=$f_fin<br>";
                    } else {
                        $formula = 'N.D.';
                    }
                }

                if (strpos($formula, '[') === false && $formula != 'N.D.') {
                    @eval('$valor = '.$formula.';');
                } else {
                    $valor = 'N.D.';
                }
            } else {
                $sql = "SELECT VAL_VALDA,ID_UNIDAD FROM valor_dato WHERE ID_DATO = $id_dato AND ID_DEPTO = '00'".$cond_fecha;
                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0) {
                    $row_rs = $this->conn->Fetchrow($rs);
                    $valor = $row_rs[0];
                    $id_unidad = $row_rs[1];
                } else {
                    $sql = "SELECT VAL_VALDA,ID_UNIDAD FROM valor_dato WHERE ID_DATO = $id_dato AND ID_MUN = '00000'".$cond_fecha;
                    $rs = $this->conn->OpenRecordset($sql);
                    if ($this->conn->RowCount($rs) > 0) {
                        $row_rs = $this->conn->Fetchrow($rs);
                        $valor = $row_rs[0];
                        $id_unidad = $row_rs[1];
                    }
                }
            }
        }

        return array('valor' => $valor, 'id_unidad' => $id_unidad);
    }

    /**
     * Aplica el formato y simbolos para presentacion de valores de datos sectoriales.
     *
     * @param int    $id_unidad
     * @param float  $valor
     * @param int    $simbolos    1=agregar simbolos - 0=sin simbolos
     * @param string $sep_decimal (, o .)
     *
     * @return string $valor_format
     */
    public function formatValor($id_unidad, $valor, $simbolos = 1, $sep_decimal = '.')
    {
        $sep_miles = ($sep_decimal == '.') ? ',' : '.';
        $decimals = 2;

        //CHECK PARA PORCENTAJE
        if ($valor != 'N.D.') {
            if ($id_unidad == 4 || $id_unidad == 9) {
                if ($valor < 1) {
                    $valor *= 100;
                }

                $valor = number_format($valor, $decimals, $sep_decimal, $sep_miles);

                if ($simbolos == 1) {
                    $valor .= ' %';
                }
            } elseif (in_array($id_unidad, array(2, 20))) {
                $valor = number_format($valor, $decimals, $sep_decimal, $sep_miles);
            } else {
                $valor = number_format($valor, 0, '', $sep_miles);
            }

            //Si el dato esta en pesos
            if ($simbolos == 1) {
                if ($id_unidad == 5 || $id_unidad == 6) {
                    $valor = "$ $valor";
                }
            }
        }

        return $valor;
    }

    /* Aplica el formato y simbolos para presentacion de valores de datos sectoriales en mapas
     * @access public
     * @param int $id_unidad
     * @param float $valor
     * @param int $simbolos 1=agregar simbolos - 0=sin simbolos
     * @return string $valor_format
     */
    public function formatValorToMapa($id_unidad, $valor, $simbolos = 1)
    {
        $decimals = 2;

        //CHECK PARA PORCENTAJE
        if ($valor != 'N.D.') {
            if ($id_unidad == 4 || $id_unidad == 9) {
                if ($valor < 1) {
                    $valor *= 100;
                }

                $valor = number_format($valor, $decimals, '.', ',');

                if ($simbolos == 1) {
                    $valor .= ' %';
                }
            } elseif ($id_unidad == 2) {
                $valor = number_format($valor, $decimals, '.', '');
            } else {
                $valor = number_format($valor, 0, '', '');
            }

            //Si el dato esta en pesos
            if ($simbolos == 1) {
                if ($id_unidad == 5 || $id_unidad == 6) {
                    $valor = "$ $valor";
                }
            }
        }

        return $valor;
    }

    /**
     * Consulta los municipios o departamentos que tienen valor de un dato.
     *
     * @param string $id_dato
     * @param string $id_depto
     * @param string $dato_para 1 = Departamentos, 0= Mpios
     *
     * @return array $arr_id
     */
    public function GetDeptosMpiosDato($id_dato, $id_depto, $dato_para)
    {
        $arr_id = array();

        $ubi = array('ID_MUN', 'ID_DEPTO');

        //MUNICIPIOS
        if ($dato_para == 0 && $id_depto != '') {
            $sql = "SELECT valor_dato.$ubi[$dato_para] FROM valor_dato INNER JOIN municipio ON valor_dato.ID_MUN = municipio.ID_MUN WHERE ID_DATO = $id_dato AND municipio.ID_DEPTO = '$id_depto'";
        } else {
            $sql = "SELECT $ubi[$dato_para] FROM valor_dato WHERE ID_DATO = $id_dato";
        }

        $rs = $this->conn->OpenRecordset($sql);
        $i = 0;
        while ($row_rs = $this->conn->FetchRow($rs)) {
            $arr_id[$i] = $row_rs[0];
            ++$i;
        }

        return $arr_id;
    }

    /**
     * Consulta los Datos que cumplen con los criterios de b�squeda.
     *
     * @param string $f_ini
     * @param string $f_fin
     * @param int    $dato_para
     * @param string $id_detos              ID de Deptos separados por coma
     * @param string $id_muns               ID de Mpios separados por coma
     * @param string $id_cat
     * @param string $id_dato
     * @param string $condicion_filtro_dato mayor,menor,mayor_igual,menor_igual,entre
     * @param string $valor_filtro_dato
     *
     * @return array $arr_id
     */
    public function GetIDToConsulta($f_ini, $f_fin, $dato_para, $id_deptos, $id_muns, $id_cat, $id_dato, $condicion_filtro_dato, $valor_filtro_dato)
    {
        $arr_id_cat = array();
        $arr_id_dato = array();
        $arr_id_deptos = array();
        $arr_id_muns = array();
        $mun_dao = new MunicipioDAO();
        $cond_valor = '';

        $cond_fecha = "INI_VALDA >= '$f_ini' AND FIN_VALDA <= '$f_fin'";

        //CATEGORIA

        if ($id_cat != '') {
            if ($dato_para == 1) {
                $sql = 'SELECT ID_VALDA FROM valor_dato INNER JOIN dato_sector ON valor_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_CATE IN ('.$id_cat.") AND ID_DEPTO IS NOT NULL AND $cond_fecha ORDER BY ID_VALDA, ID_CATE";
            } else {
                $sql = 'SELECT ID_VALDA FROM valor_dato INNER JOIN dato_sector ON valor_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_CATE IN ('.$id_cat.") AND ID_MUN IS NOT NULL AND $cond_fecha ORDER BY ID_VALDA, ID_CATE";
            }

            $rs = $this->conn->OpenRecordset($sql);
            $i = 0;
            while ($row_rs = $this->conn->FetchRow($rs)) {
                $arr_id_cat[$i] = $row_rs[0];
                ++$i;
            }

            $arr_id = $arr_id_cat;
        }

        //UBIACION GEOGRAFICA
        ($dato_para == 1) ? $cond_u = 'ID_DEPTO IS NOT NULL AND ' : $cond_u = 'ID_MUN IS NOT NULL AND ';
        if ($id_deptos != '' && $id_muns == '') {
            $id_depto_s = $id_deptos;

            if ($dato_para == 1) {
                $cond_u = 'ID_DEPTO IN ('.$id_depto_s.') AND ';
                $cond_u_mun = 'ID_MUN IN ('.$mun_dao->GetIDWhere($id_depto_s).')AND ';
            } else {
                $cond_u = 'ID_MUN IN ('.$mun_dao->GetIDWhere($id_depto_s).') AND ';
            }
            //$sql = "SELECT ID_VALDA FROM valor_dato INNER JOIN municipio ON valor_dato.ID_MUN = municipio.ID_MUN WHERE municipio.ID_DEPTO IN ($id_depto_s) $cond_fecha ORDER BY municipio.ID_MUN, ID_DATO";
        }

        //MUNICIPIO
        if ($id_muns != '') {
            $id_mun_s = $id_muns;
            $cond_u = 'ID_MUN IN ('.$id_mun_s.') AND ';
        }

        //FILTRO VALOR DATO
        if ($valor_filtro_dato != '') {
            $operadores = array('mayor' => '>', 'menor' => '<', 'menor_igual' => '<=', 'mayor_igual' => '<=', 'entre' => 'BETWEEN ');

            $cond_valor = " AND VAL_VALDA $operadores[$condicion_filtro_dato] $valor_filtro_dato";
        }

        //DATO
        if ($id_dato != '') {
            if ($dato_para == 1) {
                $sql = 'SELECT ID_VALDA FROM valor_dato WHERE ID_DATO IN ('.$id_dato.") AND $cond_u $cond_fecha $cond_valor ORDER BY ID_VALDA, ID_DATO";
                $rs = $this->conn->OpenRecordset($sql);
                $i = 0;
                while ($row_rs = $this->conn->FetchRow($rs)) {
                    $arr_id_dato[$i] = $row_rs[0];
                    ++$i;
                }

                if ($i == 0) {
                    $sql = 'SELECT ID_VALDA FROM valor_dato WHERE ID_DATO IN ('.$id_dato.") AND $cond_u $cond_fecha $cond_valor ORDER BY ID_MUN ASC";
                    $rs = $this->conn->OpenRecordset($sql);
                    $i = 0;
                    while ($row_rs = $this->conn->FetchRow($rs)) {
                        $arr_id_dato[$i] = $row_rs[0];
                        ++$i;
                    }
                    $_SESSION['__mpio__depto'] = 1;
                }
            } else {
                $sql = 'SELECT ID_VALDA FROM valor_dato WHERE ID_DATO IN ('.$id_dato.") AND $cond_u $cond_fecha $cond_valor ORDER BY ID_VALDA, ID_DATO";
                $rs = $this->conn->OpenRecordset($sql);
                $i = 0;
                while ($row_rs = $this->conn->FetchRow($rs)) {
                    $arr_id_dato[$i] = $row_rs[0];
                    ++$i;
                }
            }

            //echo $sql;
            if ($id_cat != '') {
                $arr_id = array_intersect($arr_id_cat, $arr_id_dato);
            } else {
                $arr_id = $arr_id_dato;
            }
        }

        return $arr_id;
    }

    /**
     * Lista los DatoSectorial que cumplen la condici�n en el formato dado.
     *
     * @param string $formato     Formato en el que se listar�n los DatoSectorial, puede ser Tabla o ComboSelect
     * @param int    $valor_combo ID del DatoSectorial que ser� selccionado cuando el formato es ComboSelect
     * @param string $condicion   Condici�n que deben cumplir los DatoSectorial y que se agrega en el SQL statement.
     */
    public function ListarCombo($formato, $valor_combo, $condicion)
    {
        $arr = $this->GetAllArray($condicion, '', '');
        $num_arr = count($arr);
        $v_c_a = is_array($valor_combo);

        for ($a = 0; $a < $num_arr; ++$a) {
            $vo = $arr[$a];

            if ($valor_combo == '' && $valor_combo != 0) {
                echo '<option value='.$vo->id.'>'.$vo->nombre.'</option>';
            } else {
                echo '<option value='.$vo->id;

                if (!$v_c_a) {
                    if ($valor_combo == $vo->id) {
                        echo ' selected ';
                    }
                } else {
                    if (in_array($vo->id, $valor_combo)) {
                        echo ' selected ';
                    }
                }

                echo '>'.$vo->nombre.'</option>';
            }
        }
    }

    /**
     * Lista las DatoSectoriales en una Tabla.
     */
    public function ListarTabla()
    {
        $cat_dao = new CategoriaDatoSectorDAO();
        $unidad_dao = new UnidadDatoSectorDAO();
        $condicion = '';

        $id_cat = 45;
        //if (isset($_GET["id_cat"]) && $_GET["id_cat"] != "" && $_GET["id_cat"] != 0){
        if (!empty($_GET['id_cat'])) {
            $id_cat = $_GET['id_cat'];
        }

        $condicion = 'ID_CATE = '.$id_cat;

        $arr = $this->GetAllArray($condicion, '', '');
        $num_arr = count($arr);

        echo "<table align='center' class='tabla_lista' width='1000'>
			<tr>
			<td colspan='5'>
			Filtrar por Categoria&nbsp;<select nane='id_tipo' class='select' onchange=\"refreshTab('index_parser.php?accion=listar&class=".$_GET['class']."&method=ListarTabla&param=&id_cat='+this.value)\">
			<option value=''>Todos</option>";
        $cat_dao->ListarCombo('combo', $id_cat, '');
        echo "</select></td></tr>

			<tr>
			<td width='70' colspan='2'><img src='images/home/insertar.png'>&nbsp;<a href='#' onclick=\"addWindowIU('dato_sectorial','insertar','');return false;\">Crear</a></td>
			<td align='right' colspan='3'>[$num_arr Registros]</td>
			</tr>
			<tr class='titulo_lista'>
			<td width='50' align='center'>ID</td>
			<td width='280'>Nombre</td>
			<td width='280'>Categoria</td>
			<td width='50'>Desagreg. Geo.</td>
			<td></td>
			</tr>";

        foreach ($arr as $vo) {
            $id = $vo->id;

            //CHECK DE REGISTROS DE VALORES DEL DATO
            if ($this->numRecordsValores('ID_DATO = '.$id) == 0) {
                $alert = 'Esta seguro que desea borrar el Dato: '.$vo->nombre;
            } else {
                $alert = 'Existen valores de este Dato en el sistema, esta seguro que desea borrar el Dato: '.$vo->nombre.' y los valores?';
            }

            echo "<tr class='fila_lista'>";
            echo "<td align='center'><a href='#' onclick=\"if (confirm('".$alert."')){borrarRegistro('".$_GET['class']."','".$id."')} else{return false}\"><img src='images/trash.png' border='0' ></a>&nbsp;$id</td>";
            echo "<td><a href='#' onclick=\"addWindowIU('dato_sectorial','actualizar',$id)\">".$vo->nombre.'</a></td>';

            // CAT
            $cat = $cat_dao->Get($vo->id_cat);
            echo '<td>'.$cat->nombre.'</td>';

            //UNIDAD
            /*$unidad = $unidad_dao->Get($id_unidad);
              echo "<td>".$unidad->nombre."</td>";*/
            echo '<td>'.ucfirst($vo->desagreg_geo).'</td>';

            echo '<td>';

            //CHECK DE REGISTROS DE VALORES DEL DATO
            if ($this->numRecordsValores('ID_DATO = '.$id) > 0) {
                $periodos = $this->GetPeriodosValores($id);

                echo "- <a href='#' onclick=\"return borrarValores(".$id.",'".$vo->nombre."')\">Borrar Valores para</a>";
                echo "<br /><span id='span_periodo_dato_$id'>
					<select class='select' id='periodos_dato_".$id."'><option value=''>Todos los periodos</option>";
                foreach ($periodos as $per) {
                    echo "<option value='".$per['ini'].'|'.$per['fin']."'>".$per['ini'].' a '.$per['fin'].'</option>';
                }
                echo '</select></span>';
                echo '<br> ';
            }

            echo '</td><td>';
            if ($vo->tipo_calc_nal != 'manual' || $vo->tipo_calc_deptal != 'manual') {
                echo "<a href='../cron_jobs/totalizar_d_sectorial.php?id_dato=".$id."' target='_blank'>Totalizar valores deptales y nacionales</a>";
            }
            echo '</td>';
            echo '<td>';
            if ($vo->formula != '') {
                echo "<a href='../cron_jobs/totalizar_d_sectorial_formulado.php?id_dato=".$id."' target='_blank'>Totalizar f&oacute;rmula</a>";
            }
            echo '</td>';
            echo '</tr>';
        }
    }

    /**
     * Reporta los TipoEvento.
     */
    public function ReportarAdmin()
    {
        $cat_dao = new CategoriaDatoSectorDAO();
        $sector_dao = new SectorDAO();
        $contacto_dao = new ContactoDAO();
        $cats = $cat_dao->GetAllArray('');

        $linea = '<table><tr><td>DATOS SECTORIALES EN EL SISTEMA SISSH</td></tr>';

        echo "<table align='center' width='750' class='tabla_lista'>
			<tr><td>&nbsp;</td></tr>
			<tr><td colspan='5' align='right'><img src='../images/consulta/excel.gif'>&nbsp;<a href='../export_data.php?case=xls_session&nombre_archivo=Datos_Sectoriales_SIDIH'>Guardar Archivo</a></td>
			<tr class='titulo_lista'><td align='center' colspan=5><b>DATOS SECTORIALES SIDIH</b></td></tr>";

        foreach ($cats as $cat) {
            $linea .= '<tr><td>'.strtoupper($cat->nombre).'</td></tr>';

            $linea .= '<tr><td>Dato</td><td>Sector</td><td>Fuente</td><td>MDGD</td></tr>';

            $arr = $this->GetAllArray('ID_CATE='.$cat->id, '', '');

            echo "<tr><td>&nbsp;</td></tr>
				<tr><td class='titulo_lista' colspan='4'><b>".strtoupper($cat->nombre)."</b></td></tr>
				<tr class='titulo_lista' width='250'><td><b>Dato</b></td><td><b>Sector</b></td><td><b>Fuente</b></td><td><b>MDGD <a href='#' title='MDGD: Maxima desagregaci�n geografica disponible'>?</a></b></td></tr>";

            foreach ($arr as $vo) {
                echo "<tr class='fila_lista'>";
                echo "<td><a href='#' onclick=\"addWindowIU('dato_sectorial','actualizar',$vo->id)\">".$vo->nombre.'</a></td>';

                //SECTOR
                $sector = $sector_dao->Get($vo->id_sector);
                echo '<td>'.$sector->nombre_es.'</td>';

                //FUENTE
                $contacto = $contacto_dao->Get($vo->id_contacto);
                echo '<td>'.$contacto->nombre.'</td>';

                echo '<td>'.ucfirst($vo->desagreg_geo).'</td>';

                $linea .= "<tr><td>$vo->nombre</td><td>$sector->nombre_es</td><td>$contacto->nombre</td><td>".ucfirst($vo->desagreg_geo).'</td></tr>';

                echo '</tr>';
            }
        }

        echo '</table>';

        $_SESSION['xls'] = $linea;
    }

    /**
     * Reporta los Datos Sectoriales en el sistema.
     *
     * @param $id_cat
     */
    public function ReportarMetadatos($id_cat)
    {
        $cat_dao = new CategoriaDatoSectorDAO();
        $sector_dao = new SectorDAO();
        $contacto_dao = new ContactoDAO();

        ($id_cat == 0) ? $cond = '' : $cond = "ID_CATE = $id_cat";
        $cats = $cat_dao->GetAllArray($cond);

        $xls = '<table><tr><td>DATOS SECTORIALES EN EL SISTEMA SISSH</td></tr>';

        echo "<table align='center' width='850' cellspacing='1' cellpadding='3'>
			<tr><td>&nbsp;</td></tr>
			<tr><td colspan='6' align='right'><img src='images/consulta/excel.gif'>&nbsp;<a href='consulta/excel.php?f=metadatos_sissh'>Guardar Archivo</a></td>
			<tr class='titulo_lista'><td align='center' colspan=6><b>DATOS SECTORIALES EN EL SISTEMA</b></td></tr>";

        echo "<tr><td colspan='4'>Filtrar por Categoria : <select id='id_cat' name='id_cat' class='select' onchange=\"location.href='".$_SERVER['PHP_SELF']."?m_e=dato_sectorial&accion=consultar&class=DatoSectorialDAO&method=ReportarMetadatos&id='+this.value\">
			<option value=0>Todas</option>";

        $cat_dao->ListarCombo('combo', $id_cat, '');

        echo '</select></td></tr>';

        foreach ($cats as $cat) {
            $linea = strtoupper($cat->nombre);

            $arr = $this->GetAllArray('ID_CATE='.$cat->id, '', '');
            $num_arr = count($arr);

            echo '<tr><td>&nbsp;</td></tr>';
            $xls .= '<tr><td>&nbsp;</td></tr>';

            echo "<tr><td class='titulo_lista' colspan='6'><b>".strtoupper($cat->nombre).'</b></td></tr>';
            $xls .= "<tr><td colspan='6'><b>".strtoupper($cat->nombre).'</b></td></tr>';

            echo "<tr class='titulo_lista' width='250'><td><b>Dato</b></td><td><b>Sector</b></td><td><b>Fuente</b></td><td><b>Periodo Disponible</b></td><td><b>MDGD <a href='#' title='MDGD: Maxima desagregaci�n geografica disponible'>?</a></b></td><td>Definici&oacute;n</td></tr>";
            $xls .= '<tr><td>Dato</td><td>Sector</td><td>Fuente</td><td>Periodo Disponible</td><td>MDGD</td><td>Definici�n</td></tr>';

            for ($p = 0; $p < $num_arr; ++$p) {
                $style = '';
                if (fmod($p + 1, 2) == 0) {
                    $style = 'fila_lista';
                }

                echo "<tr class='".$style."'>";
                echo '<td>'.$arr[$p]->nombre.'</td>';

                //SECTOR
                $sector = $sector_dao->Get($arr[$p]->id_sector);
                echo '<td>'.$sector->nombre_es.'</td>';

                //FUENTE
                $contacto = $contacto_dao->Get($arr[$p]->id_contacto);
                echo '<td>'.$contacto->nombre.'</td>';

                //PERIODO DISPONIBLE
                /*$fecha = $this->GetMaxFecha($arr[$p]->id);
                  $a = split("-",$fecha["fin"]);
                  echo "<td>".$a[0]."</td>"; */

                //PERIODO DISPONIBLE
                $fecha_ini = $this->GetMinFecha($arr[$p]->id);
                $fecha_ini = split('-', $fecha_ini['fin']);
                $a_ini = $fecha_ini[0];

                $fecha_fin = $this->GetMaxFecha($arr[$p]->id);
                $fecha_fin = split('-', $fecha_fin['fin']);
                $a_fin = $fecha_fin[0];

                $periodo = ($a_ini < $a_fin) ? "$a_ini-$a_fin" : $a_ini;

                echo '<td>'.$periodo.'</td>';

                echo '<td>'.ucfirst($arr[$p]->desagreg_geo).'</td>';

                echo '<td>'.$arr[$p]->definicion.'</td>';

                //				$xls .= "<tr><td>".$arr[$p]->id."</td><td>".$arr[$p]->nombre."</td><td>$sector->nombre_es</td><td>$contacto->nombre</td><td>".$a[0]."</td><td>".ucfirst($arr[$p]->desagreg_geo)."</td></tr>";
                $xls .= '<tr><td>'.$arr[$p]->nombre."</td><td>$sector->nombre_es</td><td>$contacto->nombre</td><td>".$periodo.'</td><td>'.ucfirst($arr[$p]->desagreg_geo).'</td><td>'.$arr[$p]->definicion.'</td></tr>';

                echo '</tr>';
            }

            //$linea = "\n";
            //$file->Escribir($fp_tipo,$linea."\n");
        }

        echo '</table>';
        $xls .= '</table>';

        $_SESSION['xls'] = $xls;

        //$file->Cerrar($fp_tipo);
    }

    /**
     * Muestra la Informaci�n completa de una Organizaci�n.
     *
     * @param id $id Id de la DatoSectorial
     */
    public function Ver($id)
    {

        //INICIALIZACION DE VARIABLES
        $tema_dao = new TemaDAO();
        $estado_dao = new EstadoDatoSectorialDAO();
        $contacto_dao = new ContactoDAO();
        $org_dao = new OrganizacionDAO();
        $depto_dao = new DeptoDAO();
        $mun_dao = new MunicipioDAO();
        $region_dao = new RegionDAO();
        $sector_dao = new SectorDAO();
        $poblacion_dao = new PoblacionDAO();
        $poblado_dao = new PobladoDAO();
        $resguardo_dao = new ResguardoDAO();
        $parque_nat_dao = new ParqueNatDAO();
        $div_afro_dao = new DivAfroDAO();
        $moneda_dao = new MonedaDAO();

        //CONSULTA LA INFO DE LA ORG.
        $dato_sectorial = $this->Get($id);

        //CODIGO
        if ($dato_sectorial->codigo == '') {
            $dato_sectorial->codigo = '-';
        }

        //ESTADO
        if ($dato_sectorial->id_estp != 0) {
            $vo = $estado_dao->Get($dato_sectorial->id_estp);
            $estado = $vo->nombre;
        }

        //DESC
        //if ($dato_sectorial->desc == "")	$dato_sectorial->desc = "-";

        //OBJ
        if ($dato_sectorial->obj == '') {
            $dato_sectorial->obj = '-';
        }

        //F. INI
        if ($dato_sectorial->fecha_ini == '0000-00-00') {
            $dato_sectorial->fecha_ini = 'No especificada';
        }

        //F. FINAL
        if ($dato_sectorial->fecha_fin == '0000-00-00') {
            $dato_sectorial->fecha_fin = 'No especificada';
        }

        //DURACION
        if ($dato_sectorial->duracion == '') {
            $dato_sectorial->duracion = '-';
        }

        //COSTO
        if ($dato_sectorial->costo != '' && $dato_sectorial->costo > 0) {
            $moneda = $moneda_dao->Get($dato_sectorial->id_moneda);
            $dato_sectorial->costo = $moneda->nombre.' '.$dato_sectorial->costo;
        } else {
            $dato_sectorial->costo = '';
        }

        echo "<table cellspacing=1 cellpadding=3 class='tabla_consulta' border=0 align='center'>";
        echo "<tr class='titulo_lista'><td align='center' colspan='6'>INFORMACION DE PROYECTO</td></tr>";
        echo "<tr><td class='tabla_consulta' width='150'><b>Nombre</b></td><td class='tabla_consulta' width='500'>".$dato_sectorial->nombre."</td><td class='tabla_consulta'><b>C�digo</b></td><td class='tabla_consulta'>".$dato_sectorial->codigo.'</td></tr>';
        echo "<tr><td class='tabla_consulta'><b>Estado</b></td><td class='tabla_consulta'>".$estado.'</td></tr>';
        //TEMAS
        echo "<tr><td class='tabla_consulta'><b>Tema</b></td>";
        $s = 0;
        foreach ($dato_sectorial->id_temas as $id) {
            if (fmod($s, 8) == 0) {
                echo "<td class='tabla_consulta'>";
            }
            $vo = $tema_dao->Get($id);
            echo '- '.$vo->nombre.'<br>';
            ++$s;
        }
        echo "<tr><td class='tabla_consulta'><b>Descripci�n</b></td><td class='tabla_consulta'>".$dato_sectorial->desc.'</td></tr>';
        echo "<tr><td class='tabla_consulta'><b>Objetivo</b></td><td class='tabla_consulta'>".$dato_sectorial->obj.'</td></tr>';
        echo "<tr><td class='tabla_consulta' width='200'><b>Fecha</b></td><td class='tabla_consulta'><b>Inicio</b>: ".$dato_sectorial->fecha_ini.'&nbsp;&nbsp;-&nbsp;&nbsp;<b>Finalizaci�n</b>: '.$dato_sectorial->fecha_fin."</td><td class='tabla_consulta'><b>Duraci�n en meses</b></td><td class='tabla_consulta'>".$dato_sectorial->duracion.'</td></tr>';
        echo "<tr><td class='tabla_consulta'><b>Costo</b></td><td class='tabla_consulta'>".$dato_sectorial->costo.'</td></tr>';

        //CONTACTOS
        echo "<tr><td class='tabla_consulta'><b>Cont�ctos</b></td>";
        $s = 0;
        if (count($dato_sectorial->id_contactos) > 0) {
            echo "<td class='tabla_consulta' colspan=3><table cellspacing=1 cellpadding=3>";
            echo "<tr class='titulo_lista'><td width='150'><b>Nombre</b></td><td width='150'><b>Tel�fono</b></td><td width='150'><b>Email</b></td></tr>";
        }
        foreach ($dato_sectorial->id_contactos as $id) {
            echo "<tr class='fila_lista'>";
            $vo = $contacto_dao->Get($id);
            echo '<td>'.$vo->nombre.'</td>';
            echo '<td>'.$vo->tel.'</td>';
            echo "<td><a href='mailto:".$vo->email."'>".$vo->email.'</a></td>';
            echo '</tr>';
            ++$s;
        }
        if (count($dato_sectorial->id_contactos) > 0) {
            echo '</table></td>';
        }

        echo '</tr>';

        //ORGS. DONANTES
        echo "<tr><td class='tabla_consulta'><b>Organizaciones Donantes</b></td>";
        $s = 0;
        if (count($dato_sectorial->id_orgs_d) > 0) {
            echo "<td class='tabla_consulta' colspan=3><table cellspacing=1 cellpadding=3>";
            echo "<tr class='titulo_lista'><td width='200'><b>Organizaci�n</b></td><td width='100'><b>Valor aporte</b></td></tr>";
        }
        foreach ($dato_sectorial->id_orgs_d as $id) {
            echo "<tr class='fila_lista'>";
            $vo = $org_dao->Get($id);
            echo '<td>'.$vo->nom.'</td>';
            echo '<td>'.$dato_sectorial->id_orgs_d_valor_ap[$s].'</td>';
            echo '</tr>';
            ++$s;
        }
        if (count($dato_sectorial->id_orgs_d) > 0) {
            echo '</table></td>';
        }

        echo '</tr>';

        //ORGS. EJECUTORAS
        echo "<tr><td class='tabla_consulta'><b>Organizaciones Ejecutoras</b></td>";
        $s = 0;
        foreach ($dato_sectorial->id_orgs_e as $id) {
            if (fmod($s, 8) == 0) {
                echo "<td class='tabla_consulta'>";
            }
            $vo = $org_dao->Get($id);
            echo '- '.$vo->nom.'<br>';
            ++$s;
        }

        //SECTOR
        echo "<tr><td class='tabla_consulta'><b>Sector</b></td>";
        $s = 0;
        foreach ($dato_sectorial->id_sectores as $id) {
            if (fmod($s, 8) == 0) {
                echo "<td class='tabla_consulta'>";
            }
            $vo = $sector_dao->Get($id);
            echo '- '.$vo->nombre_es.'<br>';
            ++$s;
        }

        //POBLACION BENEFICIADAS
        echo "<tr><td class='tabla_consulta'><b>Poblaci�n Beneficiada</b></td>";
        $s = 0;
        if (count($dato_sectorial->id_beneficiarios) > 0) {
            echo "<td class='tabla_consulta' colspan=3><table cellspacing=1 cellpadding=3>";
            echo "<tr class='titulo_lista'><td width='300'><b>Poblaci�n</b></td><td><b>N�mero de personas beneficiadas</b></td></tr>";
        }
        foreach ($dato_sectorial->id_beneficiarios as $id) {
            echo "<tr class='fila_lista'>";
            $vo = $poblacion_dao->Get($id);
            echo '<td>'.$vo->nombre_es.'</td>';
            echo '<td>'.$dato_sectorial->cant_per[$s].'</td>';
            echo '</tr>';
            ++$s;
        }
        if (count($dato_sectorial->id_beneficiarios) > 0) {
            echo '</table></td>';
        }

        echo '</tr>';

        //COBERTURA POR DEPARTAMENTO
        echo "<tr><td class='tabla_consulta'><b>Cobertura Geogr�fica por Departamento</b></td>";
        $s = 0;
        echo "<td class='tabla_consulta' colspan='3'>";
        foreach ($dato_sectorial->id_deptos as $id) {
            $vo = $depto_dao->Get($id);
            echo '- '.$vo->nombre.'<br>';
            ++$s;
        }
        echo '</td></tr>';

        //COBERTURA POR MUNICIPIO
        echo "<tr><td class='tabla_consulta'><b>Cobertura Geogr�fica por Municipio</b></td>";
        $s = 0;
        echo "<td class='tabla_consulta' colspan='3'><table cellspacing='0' cellpadding='3'><tr>";
        foreach ($dato_sectorial->id_muns as $id) {
            if (fmod($s, 40) == 0) {
                echo "<td valign='top'>";
            }
            $vo = $mun_dao->Get($id);
            echo '- '.$vo->nombre.'<br>';
            ++$s;
        }
        echo '</td></tr></table>';
        echo '</td></tr>';

        //COBERTURA POR REGION
        echo "<tr><td class='tabla_consulta'><b>Cobertura Geogr�fica por Regi�n</b></td>";
        $s = 0;
        echo "<td class='tabla_consulta' colspan='3'>";
        foreach ($dato_sectorial->id_regiones as $id) {
            $vo = $region_dao->Get($id);
            echo '- '.$vo->nombre.'<br>';
            ++$s;
        }
        echo '</td></tr>';

        //COBERTURA POR POBLADO
        if (count($dato_sectorial->id_poblados) > 0) {
            echo "<tr><td class='tabla_consulta'><b>Cobertura Geogr�fica por Poblado</b></td>";
            $s = 0;
            echo "<td class='tabla_consulta' colspan='3'>";
            foreach ($dato_sectorial->id_poblados as $id) {
                $vo = $poblado_dao->Get($id);
                echo '- '.$vo->nombre.'<br>';
                ++$s;
            }
            echo '</td></tr>';
        }

        //COBERTURA POR PARQUE NAT.
        if (count($dato_sectorial->id_parques) > 0) {
            echo "<tr><td class='tabla_consulta'><b>Cobertura Geogr�fica por Parque Natural</b></td>";
            $s = 0;
            echo "<td class='tabla_consulta' colspan='3'>";
            foreach ($dato_sectorial->id_parques as $id) {
                $vo = $parque_nat_dao->Get($id);
                echo '- '.$vo->nombre.'<br>';
                ++$s;
            }
            echo '</td></tr>';
        }

        //COBERTURA POR RESGUARDO
        if (count($dato_sectorial->id_resguardos) > 0) {
            echo "<tr><td class='tabla_consulta'><b>Cobertura Geogr�fica por Resguardo</b></td>";
            $s = 0;
            echo "<td class='tabla_consulta' colspan='3'>";
            foreach ($dato_sectorial->id_resguardos as $id) {
                $vo = $resguardo_dao->Get($id);
                echo '- '.$vo->nombre.'<br>';
                ++$s;
            }
            echo '</td></tr>';
        }

        //COBERTURA POR DIV. AFRO
        if (count($dato_sectorial->id_divisiones_afro) > 0) {
            echo "<tr><td class='tabla_consulta'><b>Cobertura Geogr�fica por Divisi�n Afro</b></td>";
            $s = 0;
            echo "<td class='tabla_consulta' colspan='3'>";
            foreach ($dato_sectorial->id_divisiones_afro as $id) {
                $vo = $div_afro_dao->Get($id);
                echo '- '.$vo->nombre.'<br>';
                ++$s;
            }
            echo '</td></tr>';
        }

        echo '</table>';
    }

    /**
     * Imprime en pantalla los datos del DatoSectorial.
     *
     * @param object $vo          DatoSectorial que se va a imprimir
     * @param string $formato     Formato en el que se listar�n los DatoSectorial, puede ser Tabla o ComboSelect
     * @param int    $valor_combo ID del DatoSectorial que ser� selccionado cuando el formato es ComboSelect
     */
    public function Imprimir($vo, $formato, $valor_combo)
    {
        if ($formato == 'combo') {
            if ($valor_combo == '' && $valor_combo != 0) {
                echo '<option value='.$vo->id.'>'.$vo->nombre.'</option>';
            } else {
                echo '<option value='.$vo->id;
                if ($valor_combo == $vo->id) {
                    echo ' selected ';
                }
                echo '>'.$vo->nombre.'</option>';
            }
        }
    }

    /**
     * Inserta un DatoSectorial en la B.D.
     *
     * @param object $depto_vo VO de DatoSectorial que se va a insertar
     */
    public function Insertar($dato_sectorial_vo)
    {

        //CONSULTA SI YA EXISTE
        $a = $this->GetAllArray($this->columna_nombre." = '".$dato_sectorial_vo->nombre."'", '', '');
        if (count($a) == 0) {

            /*$sql =  "INSERT INTO ".$this->tabla." (ID_CATE,ID_CONP,ID_COMP,NOM_DATO,INICIO_DATO,FIN_DATO,ID_UNIDAD,DESAGREG_GEO)";
              $sql .= " VALUES (".$dato_sectorial_vo->id_cat.",".$dato_sectorial_vo->id_contacto.",".$dato_sectorial_vo->id_sector.",'".$dato_sectorial_vo->nombre."','".$dato_sectorial_vo->fecha_ini."','".$dato_sectorial_vo->fecha_fin."',".$dato_sectorial_vo->id_unidad.",'".$dato_sectorial_vo->desagreg_geo."')";*/

            if ($dato_sectorial_vo->formula != '') {
                //$sql =  "INSERT INTO ".$this->tabla." (ID_CATE,ID_CONP,ID_COMP,NOM_DATO,DESAGREG_GEO,FORMULA_DATO,ID_UNIDAD,TIPO_CALC_NAL,TIPO_CALC_DEPTAL,DEFINICION_DATO,TIPO_CALC_REG,FORMULA_CALC_REG)";
                //$sql .= " VALUES (".$dato_sectorial_vo->id_cat.",".$dato_sectorial_vo->id_contacto.",".$dato_sectorial_vo->id_sector.",'".$dato_sectorial_vo->nombre."','".$dato_sectorial_vo->desagreg_geo."','".$dato_sectorial_vo->formula."',".$dato_sectorial_vo->id_unidad.",'".$dato_sectorial_vo->tipo_calc_nal."','".$dato_sectorial_vo->tipo_calc_deptal."','".$dato_sectorial_vo->definicion."','".$dato_sectorial_vo->tipo_cal_reg."','".$dato_sectorial_vo->formula_cal_reg."')";

                $sql = 'INSERT INTO '.$this->tabla.' (ID_CATE,ID_CONP,ID_COMP,NOM_DATO,DESAGREG_GEO,FORMULA_DATO,ID_UNIDAD,TIPO_CALC_NAL,TIPO_CALC_DEPTAL,DEFINICION_DATO)';
                $sql .= ' VALUES ('.$dato_sectorial_vo->id_cat.','.$dato_sectorial_vo->id_contacto.','.$dato_sectorial_vo->id_sector.",'".$dato_sectorial_vo->nombre."','".$dato_sectorial_vo->desagreg_geo."','".$dato_sectorial_vo->formula."',".$dato_sectorial_vo->id_unidad.",'".$dato_sectorial_vo->tipo_calc_nal."','".$dato_sectorial_vo->tipo_calc_deptal."','".$dato_sectorial_vo->definicion."')";
            } else {
                //$sql =  "INSERT INTO ".$this->tabla." (ID_CATE,ID_CONP,ID_COMP,NOM_DATO,DESAGREG_GEO,TIPO_CALC_NAL,TIPO_CALC_DEPTAL,DEFINICION_DATO,TIPO_CALC_REG,FORMULA_CALC_REG)";
                //$sql .= " VALUES (".$dato_sectorial_vo->id_cat.",".$dato_sectorial_vo->id_contacto.",".$dato_sectorial_vo->id_sector.",'".$dato_sectorial_vo->nombre."','".$dato_sectorial_vo->desagreg_geo."','".$dato_sectorial_vo->tipo_calc_nal."','".$dato_sectorial_vo->tipo_calc_deptal."','".$dato_sectorial_vo->definicion."','".$dato_sectorial_vo->tipo_cal_reg."','".$dato_sectorial_vo->formula_cal_reg."')";

                $sql = 'INSERT INTO '.$this->tabla.' (ID_CATE,ID_CONP,ID_COMP,NOM_DATO,DESAGREG_GEO,TIPO_CALC_NAL,TIPO_CALC_DEPTAL,DEFINICION_DATO)';
                $sql .= ' VALUES ('.$dato_sectorial_vo->id_cat.','.$dato_sectorial_vo->id_contacto.','.$dato_sectorial_vo->id_sector.",'".$dato_sectorial_vo->nombre."','".$dato_sectorial_vo->desagreg_geo."','".$dato_sectorial_vo->tipo_calc_nal."','".$dato_sectorial_vo->tipo_calc_deptal."','".$dato_sectorial_vo->definicion."')";
            }

            $this->conn->Execute($sql);

            echo 'Registro insertado con &eacute;xito!';
        } else {
            echo 'Error - Existe un registro con el mismo nombre';
        }
    }

    /**
     * Inserta el valor de un DatoSectorial en la B.D.
     *
     * @param object $depto_vo  VO de DatoSectorial que se va a insertar
     * @param int    $dato_para A que corresponde el dato
     */
    public function InsertarDatoValor($dato_sectorial_vo, $dato_para)
    {

        //DEPTO
        if ($dato_para == 1) {
            $dato = $this->GetValor("ID_DEPTO = '$dato_sectorial_vo->id_depto' AND ID_DATO = $dato_sectorial_vo->id AND INI_VALDA = '$dato_sectorial_vo->fecha_ini' AND FIN_VALDA = '$dato_sectorial_vo->fecha_fin' ", 1);

            /*$sql =  "INSERT INTO depto_dato (ID_DEPTO,ID_DATO,VALOR_DATO) VALUES ('".$dato_sectorial_vo->id_depto."',".$dato_sectorial_vo->id.",".$dato_sectorial_vo->valor.")";
              if (isset($dato->valor)){
              $sql = "UPDATE depto_dato VALOR_DATO = $dato_sectorial_vo->valor WHERE ID_DEPTO = '$dato_sectorial_vo->id_depto' AND ID_DATO = $dato_sectorial_vo->id";
              }*/

            $sql = "INSERT INTO valor_dato (ID_DEPTO,ID_DATO,VAL_VALDA,ID_UNIDAD,INI_VALDA,FIN_VALDA) VALUES ('$dato_sectorial_vo->id_depto',$dato_sectorial_vo->id,$dato_sectorial_vo->valor,$dato_sectorial_vo->id_unidad,'$dato_sectorial_vo->fecha_ini','$dato_sectorial_vo->fecha_fin')";
            if ($dato->valor != null) {
                $sql = "UPDATE valor_dato SET VAL_VALDA = $dato_sectorial_vo->valor, ID_UNIDAD = $dato_sectorial_vo->id_unidad, INI_VALDA = '$dato_sectorial_vo->fecha_ini', FIN_VALDA = '$dato_sectorial_vo->fecha_fin' WHERE ID_DEPTO = '$dato_sectorial_vo->id_depto' AND ID_DATO = $dato_sectorial_vo->id AND INI_VALDA = '$dato_sectorial_vo->fecha_ini' AND FIN_VALDA = '$dato_sectorial_vo->fecha_fin'";
            }

            $this->conn->Execute($sql);
        }
        //MUN
        if ($dato_para == 2) {
            $dato = $this->GetValor("ID_MUN = '$dato_sectorial_vo->id_mun' AND ID_DATO = $dato_sectorial_vo->id  AND INI_VALDA = '$dato_sectorial_vo->fecha_ini' AND FIN_VALDA = '$dato_sectorial_vo->fecha_fin'", 2);

            /*$sql =  "INSERT INTO mpio_dato (ID_MUN,ID_DATO,VALOR_DATO) VALUES ('".$dato_sectorial_vo->id_mun."',".$dato_sectorial_vo->id.",".$dato_sectorial_vo->valor.")";
              if (isset($dato->valor)){
              $sql = "UPDATE mpio_dato SET VALOR_DATO = $dato_sectorial_vo->valor WHERE ID_MUN = '$dato_sectorial_vo->id_mun' AND ID_DATO = $dato_sectorial_vo->id";
              }*/

            $sql = "INSERT INTO valor_dato (ID_MUN,ID_DATO,VAL_VALDA,ID_UNIDAD,INI_VALDA,FIN_VALDA) VALUES ('$dato_sectorial_vo->id_mun',$dato_sectorial_vo->id,$dato_sectorial_vo->valor,$dato_sectorial_vo->id_unidad,'$dato_sectorial_vo->fecha_ini','$dato_sectorial_vo->fecha_fin')";
            if ($dato->valor != null) {
                $sql = "UPDATE valor_dato SET VAL_VALDA = $dato_sectorial_vo->valor, ID_UNIDAD = $dato_sectorial_vo->id_unidad, INI_VALDA = '$dato_sectorial_vo->fecha_ini', FIN_VALDA = '$dato_sectorial_vo->fecha_fin' WHERE ID_MUN = '$dato_sectorial_vo->id_mun' AND ID_DATO = $dato_sectorial_vo->id AND INI_VALDA = '$dato_sectorial_vo->fecha_ini' AND FIN_VALDA = '$dato_sectorial_vo->fecha_fin'";
            }
            $this->conn->Execute($sql);
        }
        //POB
        if ($dato_para == 3) {
            $dato = $this->GetValor("ID_POB = '$dato_sectorial_vo->id_pob' AND ID_DATO = $dato_sectorial_vo->id AND INI_VALDA = '$dato_sectorial_vo->fecha_ini' AND FIN_VALDA = '$dato_sectorial_vo->fecha_fin'", 3);

            $sql = "INSERT INTO poblado_dato (ID_POB,ID_DATO,VALOR_DATO,ID_UNIDAD,INI_VALDA,FIN_VALDA) VALUES ('$dato_sectorial_vo->id_pob',$dato_sectorial_vo->id,$dato_sectorial_vo->valor,$dato_sectorial_vo->id_unidad,'$dato_sectorial_vo->fecha_ini','$dato_sectorial_vo->fecha_fin')";
            if ($dato->valor != null) {
                $sql = "UPDATE valor_dato SET VAL_VALDA = $dato_sectorial_vo->valor, ID_UNIDAD = $dato_sectorial_vo->id_unidad, INI_VALDA = '$dato_sectorial_vo->fecha_ini', FIN_VALDA = '$dato_sectorial_vo->fecha_fin' WHERE ID_POB = '$dato_sectorial_vo->id_pob' AND ID_DATO = $dato_sectorial_vo->id AND INI_VALDA = '$dato_sectorial_vo->fecha_ini' AND FIN_VALDA = '$dato_sectorial_vo->fecha_fin'";
            }
            //echo $sql;
            $this->conn->Execute($sql);
        }

        //echo $sql;
    }

    /**
     * Actualiza un DatoSectorial en la B.D.
     *
     * @param object $depto_vo VO de DatoSectorial que se va a actualizar
     */
    public function Actualizar($dato_sectorial_vo)
    {
        $sql = 'UPDATE '.$this->tabla.' SET ';
        $sql .=  'ID_CATE = '.$dato_sectorial_vo->id_cat.',';
        $sql .=  'ID_CONP = '.$dato_sectorial_vo->id_contacto.',';
        $sql .=  'ID_COMP = '.$dato_sectorial_vo->id_sector.',';
        $sql .=  "NOM_DATO = '".$dato_sectorial_vo->nombre."',";
        //$sql .=  "INICIO_DATO = '".$dato_sectorial_vo->fecha_ini."',";
        //$sql .=  "FIN_DATO = '".$dato_sectorial_vo->fecha_fin."',";
        //$sql .=  "ID_UNIDAD = $dato_sectorial_vo->id_unidad,";
        $sql .=  "DESAGREG_GEO = '".$dato_sectorial_vo->desagreg_geo."',";
        $sql .=  "FORMULA_DATO = '".$dato_sectorial_vo->formula."',";
        $sql .=  "TIPO_CALC_NAL = '".$dato_sectorial_vo->tipo_calc_nal."',";
        $sql .=  "TIPO_CALC_DEPTAL = '".$dato_sectorial_vo->tipo_calc_deptal."',";
        $sql .=  "DEFINICION_DATO = '".$dato_sectorial_vo->definicion."'";
        //$sql .=  "TIPO_CALC_REG= '".$dato_sectorial_vo->tipo_calc_reg."',";
        //$sql .=  "FORMULA_CALC_REG= '".$dato_sectorial_vo->FORMULA_calc_reg."'";

        if ($dato_sectorial_vo->formula != '') {
            $sql .=  ',ID_UNIDAD = '.$dato_sectorial_vo->id_unidad;
        }

        $sql .= ' WHERE '.$this->columna_id.' = '.$dato_sectorial_vo->id;

        $this->conn->Execute($sql);
    }

    /**
     * Actualiza el valor de un DatoSectorial en la B.D.
     *
     * @param object $depto_vo  VO de DatoSectorial que se va a insertar
     * @param int    $dato_para A que corresponde el dato
     */
    public function ActualizarDatoValor($dato_sectorial_vo, $dato_para)
    {
        $sql = 'UPDATE valor_dato SET VAL_VALDA = '.$dato_sectorial_vo->valor.", ID_UNIDAD = $dato_sectorial_vo->id_unidad, INI_VALDA = '$dato_sectorial_vo->fecha_ini', FIN_VALDA = '$dato_sectorial_vo->fecha_fin' WHERE ID_DATO = $dato_sectorial_vo->id AND ";

        //DEPTO
        if ($dato_para == 1) {
            //$sql =  "UPDATE depto_dato SET VALOR_DATO = ".$dato_sectorial_vo->valor." WHERE ID_DEPTO = ".$dato_sectorial_vo->id_depto." AND ID_DATO = ".$dato_sectorial_vo->id;
            $sql .=  'ID_DEPTO = '.$dato_sectorial_vo->id_depto;
            //echo $sql;
            $this->conn->Execute($sql);
        }
        //MUN
        if ($dato_para == 2) {
            //$sql =  "UPDATE mpio_dato SET VALOR_DATO = ".$dato_sectorial_vo->valor." WHERE ID_MUN = ".$dato_sectorial_vo->id_mun." AND ID_DATO = ".$dato_sectorial_vo->id;
            $sql .= 'ID_MUN = '.$dato_sectorial_vo->id_mun;
            //echo $sql;
            $this->conn->Execute($sql);
        }
        //POB
        if ($dato_para == 3) {
            //$sql =  "UPDATE poblado_dato SET VALOR_DATO = ".$dato_sectorial_vo->valor." WHERE ID_POB = ".$dato_sectorial_vo->id_pob." AND ID_DATO = ".$dato_sectorial_vo->id;
            $sql .=  'ID_POB = '.$dato_sectorial_vo->id_pob;
            //echo $sql;
            $this->conn->Execute($sql);
        }
    }

    /**
     * Borra un DatoSectorial en la B.D.
     *
     * @param int $id ID del DatoSectorial que se va a borrar de la B.D
     */
    public function Borrar($id)
    {

        //CONSULTA SI EL DATO ESTA EN LA MINIFICHA
        $sql = 'SELECT ID_DATO FROM minificha_datos_resumen WHERE '.$this->columna_id.' = '.$id;
        $rs = $this->conn->OpenRecordset($sql);
        if ($this->conn->RowCount($rs) > 0) {
            echo 'No se puede eliminar el Dato, porque est� seleccionado en el resumen de la Minificha!';
            die;
        }

        $sql = 'SELECT ID_DATO FROM minificha_enfermedades WHERE '.$this->columna_id.' = '.$id;
        $rs = $this->conn->OpenRecordset($sql);
        if ($this->conn->RowCount($rs) > 0) {
            alert('No se puede eliminar el Dato, porque est� seleccionado en la gr�fica de Enfermedades de la Minificha!');
            die;
        }

        //BORRA
        $sql = 'DELETE FROM '.$this->tabla.' WHERE '.$this->columna_id.' = '.$id;
        $this->conn->Execute($sql);

        //BORRA DATOS
        $sql = 'DELETE FROM valor_dato WHERE '.$this->columna_id.' = '.$id;
        $this->conn->Execute($sql);
    }

    /**
     * Borra los valores de un Dato en el SI.
     *
     * @param int $id ID del DatoSectorial que se va a borrar de la B.D
     */
    public function BorrarValores($id, $f_ini = '', $f_fin = '')
    {

        //BORRA DATOS
        $sql = 'DELETE FROM valor_dato WHERE '.$this->columna_id.' = '.$id;
        if ($f_ini != '') {
            $sql .= " AND INI_VALDA = '$f_ini' AND FIN_VALDA = '$f_fin'";
        }

        $this->conn->Execute($sql);

        $this->setFlatTotalizar($id, 1);

        ?>
        <script>
			alert("Valores eliminados con &eacute;xito!");
            location.href = '<?=$this->url;
        ?>';
		</script>
        <?php

    }

    /**
     * Lista los DatoSectoriales en una Tabla como resultado de una Consulta.
     */
    public function Reportar()
    {

        //INICIALIZACION DE VARIABLES
        $depto_dao = new DeptoDAO();
        $municipio_dao = new MunicipioDAO();
        $unidad_dao = new UnidadDatoSectorDAO();
        $cat_dao = new CategoriaDatoSectorDAO();
        $arr_id = array();
        $sector_dao = new SectorDAO();
        $contacto_dao = new ContactoDAO();
        $cadena = new Cadena();
        $_SESSION['__mpio__depto'] = 0;
        $hay_formulados = 0;
        $todos_formulados = 0;
        $id_datos_formulados = array();
        $array_ubicacion = array();
        $meses = array('', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');

        //Rango Fechas
        $f_ini = '';
        $f_fin = '';
        $cond_fecha = '';

        $dato_para = $_POST['dato_para'];

        //CATEGORIA
        //$id_cat = $_POST['id_cat'];
        $id_cat_s = '';
        if (isset($_POST['id_cat'])) {
            $id_cat_s = implode(',', $id_cat);
        }

        //DATO
        $id_dato_s = '';
        if (isset($_POST['id_dato'])) {
            //$id_dato = $_POST['id_dato'];
            //$id_dato_s = implode(",",$id_dato);
            $id_dato_s = $_POST['id_dato'];
            $id_dato = split(',', $id_dato_s);
            $num_datos = count($id_dato);

            //Para saber que datos vienen, porque al eliminar, puede que quede el dato_1 de primero y no el dato_0
            $num_dato_lista = split(',', $_POST['num_dato_lista']);
        }

        //print_r($num_dato_lista);
        //echo $id_dato_s;

        $tit_dato_para = 'Departamental';
        if ($dato_para == 2) {
            $tit_dato_para = 'Municipal';
        }

        //UBIACION GEOGRAFICA
        $id_depto_s = '';
        $id_mun_s = '';
        if (isset($_POST['id_depto']) && !isset($_POST['id_muns'])) {
            $id_depto = $_POST['id_depto'];

            $m = 0;
            foreach ($id_depto as $id) {
                $id_depto_s[$m] = "'".$id."'";
                ++$m;
            }

            $id_depto_s = implode(',', $id_depto_s);
        }

        //MUNICIPIO
        elseif (isset($_POST['id_muns'])) {
            $id_mun = $_POST['id_muns'];

            $m = 0;
            foreach ($id_mun as $id) {
                $id_mun_s[$m] = "'".$id."'";
                ++$m;
            }
            $id_mun_s = implode(',', $id_mun_s);
        }

        //FILTRO VALOR DATO
        if (isset($_POST['filtro_dato'])) {
            $valor_filtro_dato = $_POST['filtro_dato'];
            $condicion_filtro_dato = $_POST['condicion_filtro_dato'];
            if ($condicion_filtro_dato == 'entre') {
                $valor_filtro_dato .= ' AND '.$_POST['filtro_dato_entre'];
            }
        }

        //periodo en la forma viene: f_ini|f_fin en arreglo de checkbox para cada dato
        $arr_id = array();

        $d = 0;
        foreach ($id_dato as $id_d) {
            $num_d_l = $num_dato_lista[$d];

            $periodos = $_POST['ini_fin_dato_'.$num_d_l];

            foreach ($periodos as $p => $periodo) {
                $p_tmp = split('[|]', $periodo);
                $f_ini = $p_tmp[0];
                $f_fin = $p_tmp[1];

                //$arr_id = array_merge($arr_id,$this->GetIDToConsulta($f_ini,$f_fin,$dato_para,$id_depto_s,$id_mun_s,$id_cat_s,$id_dato_s,$condicion_filtro_dato,$valor_filtro_dato));
                $arr_id = array_merge($arr_id, $this->GetIDToConsulta($f_ini, $f_fin, $dato_para, $id_depto_s, $id_mun_s, $id_cat_s, $id_d, $condicion_filtro_dato, $valor_filtro_dato));

                $f_tmp = split('-', $f_ini);
                $periodo_ini = ($f_tmp[2] * 1).' '.$meses[$f_tmp[1] * 1].' '.$f_tmp[0];

                $f_tmp = split('-', $f_fin);
                $periodo_fin = ($f_tmp[2] * 1).' '.$meses[$f_tmp[1] * 1].' '.$f_tmp[0];

                //String para filtro
                $periodo_dato_ini[$id_d][] = $f_ini;
                $periodo_dato_fin[$id_d][] = $f_fin;
                $periodo_dato_txt[$id_d][] = "$periodo_ini a $periodo_fin";

                //if ($p == 0)	$tit_filtro_periodo = "$periodo_ini a $periodo_fin";
                //else			$tit_filtro_periodo .= ", $periodo_ini a $periodo_fin";
            }

            ++$d;
        }

        $num_arr = count($arr_id);

        echo "<table align='center' class='tabla_reportelist_outer' border=0>";
        echo '<tr><td>&nbsp;</td></tr>';

        //if ($num_arr > 0){
        ?>
			<tr>
			<td>
			<table border=0 width="100%" cellpadding=0 cellspacing=0>
			<tr>
			<td width="100"><a href='javascript:history.back(-1)'><img
			src='images/back.gif' border=0>&nbsp;Regresar</a></td>
			<td align='right'>Exportar a:&nbsp; <a href="#"
			onclick="document.getElementById('pdf').value = 1;reportStream('dato_sectorial');return false;"><img
			src='images/consulta/generar_pdf.gif' border=0
			title='Exportar a PDF'></a> &nbsp;&nbsp;<a href="#"
			onclick="document.getElementById('pdf').value = 2;reportStream('dato_sectorial');return false;"><img
			src='images/consulta/excel.gif' border=0
			title='Exportar a Hoja de C�lculo'></a></td>
			</tr>
			</table>
			</td>
			</tr>
			<?php
            //}
            echo "<tr><td align='center' class='titulo_lista' colspan=10>CONSULTA DE DATOS SECTORIALES</td></tr>";
        echo '<tr><td colspan=9>Consulta realizada a nivel <b>'.$tit_dato_para.'</b> aplicando los siguientes filtros:</td>';
        echo '<tr><td colspan=7>';

        //PERIODO
        //echo "<img src='images/flecha.gif'> Periodo:&nbsp;<b>$tit_filtro_periodo</b><br>";

        //TITULO DE CATEGORIA
        if (isset($_POST['id_cat'])) {
            echo '&#187;&nbsp; CATEGORIA DEL DATO: ';
            $t = 0;
            foreach ($id_cat as $id_t) {
                $vo = $cat_dao->Get($id_t);
                if ($t == 0) {
                    echo '<b>'.$vo->nombre.'</b>';
                } else {
                    echo ', <b>'.$vo->nombre.'</b>';
                }
                ++$t;
            }
            echo '<br>';
        }
        //TITULO DE DATO
        if (isset($_POST['id_dato'])) {
            echo '&#187;&nbsp; DATOS: ';
            $t = 0;
            foreach ($id_dato as $id_t) {
                $vo = $this->Get($id_t);

                echo "<br>&nbsp;&nbsp;&nbsp;-[<a href='#tr_$id_t'>Ir</a>]&nbsp;<b>".$vo->nombre.'</b>&nbsp;, Periodos:  '.implode(' , ', $periodo_dato_txt[$id_t]);

                //Check de datos formulados
                if ($vo->formula != '') {
                    $id_datos_formulados[] = $id_t;
                    ++$hay_formulados;
                }
                ++$t;
            }
            if ($hay_formulados == $num_datos) {
                $todos_formulados = 1;
            }
            echo '<br /><br />';
        }

        //TITULO DE DEPTO
        if (isset($_POST['id_depto'])) {
            echo '&#187;&nbsp; DEPARTAMENTO: ';
            $t = 0;
            foreach ($_POST['id_depto'] as $id_t) {
                $vo = $depto_dao->Get($id_t);
                if ($t == 0) {
                    echo '<b>'.$vo->nombre.'</b>';
                } else {
                    echo ', <b>'.$vo->nombre.'</b>';
                }

                $array_ubicacion[] = $id_t;
                ++$t;
            }
            echo '<br><br />';
        }
        //TITULO DE MPIO
        if (isset($_POST['id_muns'])) {
            echo '&#187;&nbsp; MUNICIPIO: ';
            $t = 0;
            $array_ubicacion = array();
            foreach ($id_mun as $id_t) {
                $vo = $municipio_dao->Get($id_t);
                if ($t == 0) {
                    echo '<b>'.$vo->nombre.'</b>';
                } else {
                    echo ', <b>'.$vo->nombre.'</b>';
                }

                $array_ubicacion[] = $id_t;
                ++$t;
            }
            echo '<br><br />';
        }
        echo '</td>';

        $contenido = '';

        //LISTADO DE DATOS
        if ($num_arr > 0 && $todos_formulados == 0) {
            echo  "<tr><td><table class='tabla_reportelist'>";

            $contenido .= "<tr class='titulo_lista'>";

            //NIVEL DEPARTAMENTAL
            if ($dato_para == 1 && $_SESSION['__mpio__depto'] == 0) {
                $contenido .= '<td>Cod. Depto.</td>';
                $contenido .= '<td>Departamento</td>';

                //Para pdf
                $title['nom_ubi'] = '<b>Departamento</b>';
            } elseif ($dato_para == 1 && $_SESSION['__mpio__depto'] == 1) {
                $contenido .= '<td>Codigo</td>';
                $contenido .= '<td>Municipio/Depto</td>';

                //Para pdf
                $title['nom_ubi'] = '<b>Municipio/Depto</b>';
            }

            //NIVEL MUNICIPAL
            if ($dato_para == 2) {
                $contenido .= '<td>Cod. Mpio.</td>';
                $contenido .= '<td>Municipio</td>';

                //Para pdf
                $title['id_ubi'] = '<b>Codigo</b>';
                $title['nom_ubi'] = '<b>Municipio</b>';
            }

            $title['id_ubi'] = '<b>Codigo</b>';
            $title['dato'] = '<b>Dato</b>';
            $title['valor'] = '<b>Valor</b>';
            $title['unidad'] = '<b>Unidad</b>';
            $title['f_ini'] = '<b>Inicio Vigencia</b>';
            $title['f_fin'] = '<b>Fin Vigencia</b>';
            $title['fuente'] = '<b>Fuente</b>';
            $title['cat'] = '<b>Categoria</b>';
            $title['sector'] = '<b>Sector</b>';

            $contenido .= "<td width='100'>Dato</td>
				<td width='100'>Valor</td>
				<td width='150'>Unidad</td>
				<td width='150'>Ini. Vigencia</td>
				<td width='150'>Fin Vigencia</td>
				<td width='150'>Fuente</td>
				<td width='150'>Categoria</td>
				<td width='150'>Sector</td>
				</tr>";

            $p = 0;
            $__id__depto = 0;
            $__id__depto__ant = 0;
            $__valor_depto = 0;
            $__id_dato__ant = 0;
            foreach ($arr_id as $id_dato_valor) {
                $sql = 'SELECT * FROM valor_dato WHERE ID_VALDA = '.$id_dato_valor;
                $rs = $this->conn->OpenRecordset($sql);
                $row_rs = $this->conn->FetchObject($rs);
                $id_dato = $row_rs->ID_DATO;
                $id_unidad = $row_rs->ID_UNIDAD;
                $fecha_ini = $row_rs->INI_VALDA;
                $fecha_fin = $row_rs->FIN_VALDA;

                $valor = $row_rs->VAL_VALDA;

                $valor = $this->formatValor($id_unidad, $valor, 0);

                $dato = $this->Get($id_dato);

                if ($dato_para == 1 && $_SESSION['__mpio__depto'] == 0) {
                    $id_ubicacion = $row_rs->ID_DEPTO;
                    $ubicacion = $depto_dao->Get($id_ubicacion);
                } elseif ($dato_para == 1 && $_SESSION['__mpio__depto'] == 1) {
                    $id_ubicacion = $row_rs->ID_MUN;
                    $ubicacion = $municipio_dao->Get($id_ubicacion);
                    $__id__depto = $ubicacion->id_depto;
                } else {
                    $id_ubicacion = $row_rs->ID_MUN;
                    $ubicacion = $municipio_dao->Get($id_ubicacion);
                }

                //COLOCA EL TOTAL DEL DEPTO CUANDO SE CONSULTA A NIVEL DPTAL PERO HAY DATOS EN MPIOS
                if ($__id__depto != $__id__depto__ant && $p > 0) {
                    $contenido .= "<tr class='titulo_ocurrenciasOrg'>";

                    $ubicacion = $depto_dao->Get($__id__depto__ant);
                    //$valor = number_format($__valor_depto,2);
                    $valor = $this->formatValor($id_unidad, $__valor_depto, 0);

                    $contenido .= '<td>'.$ubicacion->id.'</td>';
                    $contenido .= '<td>'.$ubicacion->nombre.'</td>';

                    $data[$p]['id_ubi'] = $ubicacion->id;
                    $data[$p]['nom_ubi'] = $ubicacion->nombre;

                    //DATO
                    $contenido .= '<td>'.$dato->nombre.'</td>';
                    $data[$p]['dato'] = $dato->nombre;

                    ////VALOR
                    $contenido .= '<td>'.$valor.'</td>';
                    $data[$p]['valor'] = $valor;

                    //UNIDAD
                    $unidad = $unidad_dao->Get($id_unidad);
                    $contenido .= '<td>'.$unidad->nombre.'</td>';
                    $data[$p]['unidad'] = $unidad->nombre;

                    //FECHA INICIO
                    if ($fecha_ini != '0000-00-00') {
                        $contenido .= '<td>'.$fecha_ini.'</td>';
                        $data[$p]['f_ini'] = $fecha_ini;
                    } else {
                        $data[$p]['f_ini'] = '';
                        $contenido .= '<td>&nbsp;</td>';
                    }

                    //FECHA FIN
                    if ($fecha_fin != '0000-00-00') {
                        $contenido .= '<td>'.$fecha_fin.'</td>';
                        $data[$p]['f_fin'] = $fecha_fin;
                    } else {
                        $data[$p]['f_fin'] = '';
                        $contenido .= '<td>&nbsp;</td>';
                    }

                    //FUENTE
                    $fuente = $contacto_dao->Get($dato->id_contacto);
                    $contenido .= '<td>'.$fuente->nombre.'</td>';
                    $data[$p]['fuente'] = $fuente->nombre;

                    //CATEGORIA
                    $categoria = $cat_dao->Get($dato->id_cat);
                    $contenido .= '<td>'.$categoria->nombre.'</td>';
                    $data[$p]['cat'] = $categoria->nombre;

                    //SECTOR
                    $sector = $sector_dao->Get($dato->id_sector);
                    $contenido .= '<td>'.$sector->nombre_es.'</td>';
                    $data[$p]['sector'] = $sector->nombre_es;

                    $__valor_depto = 0;
                } else {
                    $contenido .= '<tr>';

                    $contenido .= '<td>';

                    // Anclas
                    if ($id_dato != $__id_dato__ant) {
                        $contenido .= "<a name='tr_$id_dato'></a>";
                    }

                    $contenido .= $ubicacion->id.'</td>';

                    $contenido .= '<td>'.$ubicacion->nombre.'</td>';

                    $data[$p]['id_ubi'] = $ubicacion->id;
                    $data[$p]['nom_ubi'] = $ubicacion->nombre;

                    //DATO
                    $contenido .= '<td>'.$dato->nombre.'</td>';
                    $data[$p]['dato'] = $dato->nombre;

                    ////VALOR
                    $contenido .= '<td>'.$valor.'</td>';
                    $data[$p]['valor'] = $valor;

                    //UNIDAD
                    $unidad = $unidad_dao->Get($id_unidad);
                    $contenido .= '<td>'.$unidad->nombre.'</td>';
                    $data[$p]['unidad'] = $unidad->nombre;

                    //FECHA INICIO
                    if ($fecha_ini != '0000-00-00') {
                        $contenido .= '<td>'.$fecha_ini.'</td>';
                        $data[$p]['f_ini'] = $fecha_ini;
                    } else {
                        $data[$p]['f_ini'] = '';
                        $contenido .= '<td>&nbsp;</td>';
                    }

                    //FECHA FIN
                    if ($fecha_fin != '0000-00-00') {
                        $contenido .= '<td>'.$fecha_fin.'</td>';
                        $data[$p]['f_fin'] = $fecha_fin;
                    } else {
                        $data[$p]['f_fin'] = '';
                        $contenido .= '<td>&nbsp;</td>';
                    }

                    //FUENTE
                    $fuente = $contacto_dao->Get($dato->id_contacto);
                    $contenido .= '<td>'.$fuente->nombre.'</td>';
                    $data[$p]['fuente'] = $fuente->nombre;

                    //CATEGORIA
                    $categoria = $cat_dao->Get($dato->id_cat);
                    $contenido .= '<td>'.$categoria->nombre.'</td>';
                    $data[$p]['cat'] = $categoria->nombre;

                    //SECTOR
                    $sector = $sector_dao->Get($dato->id_sector);
                    $contenido .= '<td>'.$sector->nombre_es.'</td>';
                    $data[$p]['sector'] = $sector->nombre_es;

                    $contenido .= '</tr>';

                    $__valor_depto += $row_rs->VAL_VALDA;
                }

                $__id__depto__ant = $__id__depto;
                $__id_dato__ant = $id_dato;

                ++$p;
            }

            //ULTIMO TOTAL DE DEPTO
            if ($_SESSION['__mpio__depto'] == 1) {
                $contenido .= "<tr class='titulo_ocurrenciasOrg'>";

                $ubicacion = $depto_dao->Get($__id__depto__ant);

                //$valor = number_format($__valor_depto,2);
                $valor = $this->formatValor($id_unidad, $__valor_depto, 0);

                $contenido .= '<td>'.$ubicacion->id.'</td>';
                $contenido .= '<td>'.$ubicacion->nombre.'</td>';

                $data[$p]['id_ubi'] = $ubicacion->id;
                $data[$p]['nom_ubi'] = $ubicacion->nombre;

                //DATO
                $contenido .= '<td>'.$dato->nombre.'</td>';
                $data[$p]['dato'] = $dato->nombre;

                ////VALOR
                $contenido .= '<td>'.$valor.'</td>';
                $data[$p]['valor'] = $valor;

                //UNIDAD
                $unidad = $unidad_dao->Get($id_unidad);
                $contenido .= '<td>'.$unidad->nombre.'</td>';
                $data[$p]['unidad'] = $unidad->nombre;

                //FECHA INICIO
                if ($fecha_ini != '0000-00-00') {
                    $contenido .= '<td>'.$fecha_ini.'</td>';
                    $data[$p]['f_ini'] = $fecha_ini;
                } else {
                    $data[$p]['f_ini'] = '';
                    $contenido .= '<td>&nbsp;</td>';
                }

                //FECHA FIN
                if ($fecha_fin != '0000-00-00') {
                    $contenido .= '<td>'.$fecha_fin.'</td>';
                    $data[$p]['f_fin'] = $fecha_fin;
                } else {
                    $data[$p]['f_fin'] = '';
                    $contenido .= '<td>&nbsp;</td>';
                }

                //FUENTE
                $fuente = $contacto_dao->Get($dato->id_contacto);
                $contenido .= '<td>'.$fuente->nombre.'</td>';
                $data[$p]['fuente'] = $fuente->nombre;

                //CATEGORIA
                $categoria = $cat_dao->Get($dato->id_cat);
                $contenido .= '<td>'.$categoria->nombre.'</td>';
                $data[$p]['cat'] = $categoria->nombre;

                //SECTOR
                $sector = $sector_dao->Get($dato->id_sector);
                $contenido .= '<td>'.$sector->nombre_es.'</td>';
                $data[$p]['sector'] = $sector->nombre_es;

                $contenido .= '</tr>';

                $__valor_depto = 0;
            }
        }
        //LISTA DE DATOS FORMULADOS
        if ($hay_formulados > 0) {
            foreach ($id_datos_formulados as $id_dato) {
                $dato = $this->Get($id_dato);

                if (count($arr_id) == 0) {
                    echo "<tr><td colspan=3><table class='tabla_reportelist'>";

                    $contenido .= "<tr class='titulo_lista'>";
                    //NIVEL DEPARTAMENTAL
                    if ($dato_para == 1) {
                        $contenido .= '<td>Cod. Depto.</td>';
                        $contenido .= '<td>Departamento</td>';

                        $title['nom_ubi'] = '<b>Departamento</b>';
                    }

                    //NIVEL MUNICIPAL
                    if ($dato_para == 2) {
                        $contenido .= '<td>Cod. Mpio.</td>';
                        $contenido .= '<td>Municipio</td>';

                        $title['nom_ubi'] = '<b>Municipio</b>';
                    }

                    $contenido .= "<td width='100'>Dato</td>
						<td width='100'>Valor</td>
						<td width='150'>Ini. Vigencia</td>
						<td width='150'>Fin Vigencia</td>
						<td width='150'>Unidad</td>
						<td width='150'>Fuente</td>
						<td width='150'>Categoria</td>
						<td width='150'>Sector</td>
						</tr>";

                    $title['id_ubi'] = '<b>Codigo</b>';
                    $title['dato'] = '<b>Dato</b>';
                    $title['valor'] = '<b>Valor</b>';
                    $title['f_ini'] = '<b>Inicio Vigencia</b>';
                    $title['f_fin'] = '<b>Fin Vigencia</b>';
                    $title['unidad'] = '<b>Unidad</b>';
                    $title['fuente'] = '<b>Fuente</b>';
                    $title['cat'] = '<b>Categoria</b>';
                    $title['sector'] = '<b>Sector</b>';
                }

                if (count($array_ubicacion) > 0) {

                    //NIVEL MUNICIPAL
                    if ($dato_para == 2) {
                        $ubicacion = $municipio_dao->GetAllArray('');

                        $array_ubicacion_new = array();
                        if (isset($_POST['id_depto']) && !isset($_POST['id_muns'])) {
                            foreach ($array_ubicacion as $id_ubicacion) {
                                $array_ubicacion_new = array_merge($array_ubicacion_new, $municipio_dao->GetAllArrayID("ID_DEPTO IN ($id_depto_s)", ''));
                            }

                            $array_ubicacion = $array_ubicacion_new;
                        }
                    }

                    foreach ($array_ubicacion as $id_ubicacion) {

                        //Para cada periodo seleccionado
                        $p = 0;
                        foreach ($periodo_dato_ini[$id_dato] as $f_ini) {
                            $f_fin = $periodo_dato_fin[$id_dato][$p];

                            $ubicacion = ($dato_para == 1) ?  $depto_dao->Get($id_ubicacion) : $municipio_dao->Get($id_ubicacion);

                            $valor = $this->GetValorToReport($id_dato, $id_ubicacion, $f_ini, $f_fin, $dato_para, 0);
                            $id_unidad = $valor['id_unidad'];

                            $valor_a = ($valor['valor'] != 'N.D.') ? $this->formatValor($id_unidad, $valor['valor'], 0) : 'N.D.';

                            $style = '';
                            if (fmod($p + 1, 2) == 0) {
                                $style = 'fila_lista';
                            }

                            $contenido .= "<tr class='".$style."'>";

                            $contenido .= '<td>'.$ubicacion->id.'</td>';
                            $contenido .= '<td>'.$ubicacion->nombre.'</td>';

                            $data[$p]['id_ubi'] = $ubicacion->id;
                            $data[$p]['nom_ubi'] = $ubicacion->nombre;

                            //DATO
                            $contenido .= '<td>'.$dato->nombre.'</td>';
                            $data[$p]['dato'] = $dato->nombre;

                            ////VALOR
                            $contenido .= '<td>'.$valor_a.'</td>';
                            $data[$p]['valor'] = $valor_a;

                            //FECHA INICIO
                            if ($f_ini != '0000-00-00') {
                                $contenido .= '<td>'.$f_ini.'</td>';
                                $data[$p]['f_ini'] = $f_ini;
                            } else {
                                $data[$p]['f_ini'] = '';
                                $contenido .= '<td>&nbsp;</td>';
                            }

                            //FECHA FIN
                            if ($f_fin != '0000-00-00') {
                                $contenido .= '<td>'.$f_fin.'</td>';
                                $data[$p]['f_fin'] = $f_fin;
                            } else {
                                $data[$p]['f_fin'] = '';
                                $contenido .= '<td>&nbsp;</td>';
                            }

                            //UNIDAD
                            $unidad = $unidad_dao->Get($id_unidad);
                            $contenido .= '<td>'.$unidad->nombre.'</td>';
                            $data[$p]['unidad'] = $unidad->nombre;

                            //FUENTE
                            $fuente = $contacto_dao->Get($dato->id_contacto);
                            $contenido .= '<td>'.$fuente->nombre.'</td>';
                            $data[$p]['fuente'] = $fuente->nombre;

                            //CATEGORIA
                            $categoria = $cat_dao->Get($dato->id_cat);
                            $contenido .= '<td>'.$categoria->nombre.'</td>';
                            $data[$p]['cat'] = $categoria->nombre;

                            //SECTOR
                            $sector = $sector_dao->Get($dato->id_sector);
                            $contenido .= '<td>'.$sector->nombre_es.'</td>';
                            $data[$p]['sector'] = $sector->nombre_es;

                            $contenido .= '</tr>';

                            ++$p;
                        }
                    }
                } else {
                    if ($dato_para == 1) {
                        $ubis = $depto_dao->GetAllArray('');
                    }

                    //NIVEL MUNICIPAL
                    elseif ($dato_para == 2) {
                        $ubis = $municipio_dao->GetAllArray('');
                    }

                    $p = 0;
                    foreach ($ubis as $ubicacion) {

                        //Para cada periodo seleccionado
                        $p = 0;
                        foreach ($periodo_dato_ini[$dato->id] as $f_ini) {
                            $f_fin = $periodo_dato_fin[$dato->id][$p];

                            $valor = $this->GetValorToReport($id_dato, $ubicacion->id, $f_ini, $f_fin, $dato_para, 0);
                            $id_unidad = $valor['id_unidad'];

                            $valor_a = ($valor['valor'] != 'N.D.') ? $this->formatValor($id_unidad, $valor['valor'], 0) : 'N.D.';

                            $style = '';
                            if (fmod($p + 1, 2) == 0) {
                                $style = 'fila_lista';
                            }

                            $contenido .= "<tr class='".$style."'>";

                            $contenido .= '<td>'.$ubicacion->id.'</td>';
                            $contenido .= '<td>'.$ubicacion->nombre.'</td>';

                            $data[$p]['id_ubi'] = $ubicacion->id;
                            $data[$p]['nom_ubi'] = $ubicacion->nombre;

                            //DATO
                            $contenido .= '<td>'.$dato->nombre.'</td>';
                            $data[$p]['dato'] = $dato->nombre;

                            ////VALOR
                            $contenido .= '<td>'.$valor_a.'</td>';
                            $data[$p]['valor'] = $valor_a;

                            //FECHA INICIO
                            if ($f_ini != '0000-00-00') {
                                $contenido .= '<td>'.$f_ini.'</td>';
                                $data[$p]['f_ini'] = $f_ini;
                            } else {
                                $data[$p]['f_ini'] = '';
                                $contenido .= '<td>&nbsp;</td>';
                            }

                            //FECHA FIN
                            if ($f_fin != '0000-00-00') {
                                $contenido .= '<td>'.$f_fin.'</td>';
                                $data[$p]['f_fin'] = $f_fin;
                            } else {
                                $data[$p]['f_fin'] = '';
                                $contenido .= '<td>&nbsp;</td>';
                            }

                            //UNIDAD
                            $unidad = $unidad_dao->Get($id_unidad);
                            $contenido .= '<td>'.$unidad->nombre.'</td>';
                            $data[$p]['unidad'] = $unidad->nombre;

                            //FUENTE
                            $fuente = $contacto_dao->Get($dato->id_contacto);
                            $contenido .= '<td>'.$fuente->nombre.'</td>';
                            $data[$p]['fuente'] = $fuente->nombre;

                            //CATEGORIA
                            $categoria = $cat_dao->Get($dato->id_cat);
                            $contenido .= '<td>'.$categoria->nombre.'</td>';
                            $data[$p]['cat'] = $categoria->nombre;

                            //SECTOR
                            $sector = $sector_dao->Get($dato->id_sector);
                            $contenido .= '<td>'.$sector->nombre_es.'</td>';
                            $data[$p]['unidad'] = $sector->nombre_es;

                            $contenido .= '</tr>';

                            ++$p;
                        }
                    }
                }
            }
        } elseif ($num_arr == 0) {
            echo "<tr><td align='center'><br><b>NO SE ENCONTRARON DATOS SECTORIALES</b></td></tr>";
            echo "<tr><td align='center'><br><a href='javascript:history.back(-1);'>Regresar</a></td></tr>";
            die;
        }

        echo $contenido;

        //VARIABLE DE SESION QUE SE USA PARA EXPORTAR A EXCEL Y PDF EN EL ARCHIVO export_data.php
        $_SESSION['xls_dato_sectorial'] = $contenido;

        $_SESSION['pdf_title_dato_sectorial'] = $title;
        $_SESSION['pdf_data_dato_sectorial'] = $data;

        echo "<input type='hidden' id='dato_para' name='dato_para' value=".$dato_para.'>';
        echo "<input type='hidden' id='pdf' name='pdf'>";
        echo '</table>';
    }

    /**
     * Lista los Datos de un Municipo o de un Poblado en una tabla.
     *
     * @param $id_depto int ID del departamento
     * @param $id_dato int ID del Dato Secotrial
     * @opcion $opcion int 1=Lista Dato de Municipios - 2=Lista Datos de Poblados
     */
    public function VerDato($id_depto, $id_dato, $opcion)
    {
        //INICIALIZACION DE VARIABLES
        $depto_dao = new DeptoDAO();
        $mun_dao = new MunicipioDAO();
        $poblado_dao = new PobladoDAO();
        $unidad_dao = new UnidadDatoSectorDAO();
        $cat_dao = new CategoriaDatoSectorDAO();
        $dato_dao = new self();

        $depto = $depto_dao->Get($id_depto);
        $dato = $dato_dao->Get($id_dato);
        $unidad = $unidad_dao->Get($dato->id_unidad);

        $valor_dato = $this->GetValor('ID_DEPTO = '.$id_depto, 1);
        $valor_dato_depto = $valor_dato->valor.' '.$unidad->nombre;

        //MUNICIPIO
        if ($opcion == 1) {
            $sql = "SELECT ID_MUN FROM municipio WHERE ID_DEPTO = '".$id_depto."'";

            $arr_id = array();
            $i = 0;
            $rs = $this->conn->OpenRecordset($sql);
            while ($row_rs = $this->conn->FetchRow($rs)) {
                $arr_id[$i] = $row_rs[0];
                ++$i;
            }

            $tabla = 'mpio_dato';
            $titulo = 'Municipio';
            $col_id = 'ID_MUN';
        }

        //POBLADOS
        if ($opcion == 2) {
            $sql = "SELECT ID_POB FROM poblado INNER JOIN municipio ON poblado.ID_MUN = municipio.ID_MUN WHERE ID_DEPTO = '".$id_depto."'";

            $arr_id = array();
            $i = 0;
            $rs = $this->conn->OpenRecordset($sql);
            while ($row_rs = $this->conn->FetchRow($rs)) {
                $arr_id[$i] = $row_rs[0];
                ++$i;
            }

            $tabla = 'poblado_dato';
            $titulo = 'Poblado';
            $col_id = 'ID_POB';
        }

        $num_arr = count($arr_id);

        echo "<form action='index.php?m_e=dato_sectorial&accion=consultar&class=DatoSectorialDAO' method='POST'>";
        echo "<table align='center' cellspacing='1' cellpadding='3' width='750'>";
        echo '<tr><td>&nbsp;</td></tr>';
        echo "<tr><td align='center' class='titulo_lista' colspan=7>CONSULTA DE DATOS SECTORIALES</td></tr>";
        echo '<tr><td colspan=3><b>Dato</b>: '.$dato->nombre.' </td></tr>
			<tr><td colspan=3><b>Departamento</b>: '.$depto->nombre.' </td></tr>
			<tr><td colspan=3><b>Valor del Dato a nivel Departamental</b>: '.$valor_dato_depto.'</b></td>';

        if ($num_arr > 0) {
            echo"<tr class='titulo_lista'>
				<td width='150'>".$titulo.'</td>
				<td>Valor</td>
				</tr>';

            for ($p = 0; $p < $num_arr; ++$p) {
                if ($opcion == 1) {
                    $vo = $mun_dao->Get($arr_id[$p]);
                }

                if ($opcion == 2) {
                    $vo = $poblado_dao->Get($arr_id[$p]);
                }

                //CONSULTA LOS DATOS
                $sql_d = 'SELECT * FROM '.$tabla.' WHERE ID_DATO = '.$id_dato.' AND '.$col_id." = '".$arr_id[$p]."'";
                $rs_d = $this->conn->OpenRecordset($sql_d);

                if ($this->conn->RowCount($rs_d) > 0) {
                    $row_rs_s = $this->conn->FetchObject($rs_d);

                    echo "<tr class='fila_lista'>";
                    echo '<td>'.$vo->nombre.'</td>';
                    echo '<td>'.$row_rs_s->VALOR_DATO.' '.$unidad->nombre.'</td>';
                    echo '</tr>';
                }
            }

            echo '<tr><td>&nbsp;</td></tr>';
        }

        echo "<input type='hidden' name='id_datos' value='".implode(',', $arr_id)."'>";
        echo "<input type='hidden' id='pdf' name='pdf'>";
        echo '</table>';
        echo '</form>';
    }

    /******************************************************************************
     * Reporte PDF - EXCEL
     * @param Array $ids Id de los DatoSectorials a Reportar
     * @param Int $formato PDF o Excel
     * @param Int $basico 1 = Bsico - 2 = Detallado
     * @param Int $dato_para 1 = Depto - 2 = Municipio
     * @param Int $stream 0 = Link a archivo f�sico 1 = Opcion Download
     * @access public
     *******************************************************************************/
    public function ReporteDatoSectorial($formato, $basico, $dato_para, $stream = 0)
    {

        //INICIALIZACION DE VARIABLES
        $file = new Archivo();

        if ($formato == 1) {
            $pdf = new Cezpdf();
            $pdf->selectFont('admin/lib/common/PDFfonts/Helvetica.afm');

            if ($basico == 1) {
                $pdf->ezSetMargins(100, 70, 20, 20);
            } else {
                $pdf->ezSetMargins(100, 70, 50, 50);
            }

            // Coloca el logo y el pie en todas las p�ginas
            $all = $pdf->openObject();
            $pdf->saveState();
            $img_att = getimagesize('images/logos/enc_reporte_semanal.jpg');
            $pdf->addPngFromFile('images/logos/enc_reporte_semanal.png', 700, 550, $img_att[0] / 2, $img_att[1] / 2);

            $pdf->addText(170, 550, 14, '<b>OCHA Colombia - Sistema Integrado de Informaci�n Humanitaria</b>');

            if ($basico == 1) {
                $pdf->addText(330, 530, 12, 'Datos Sectoriales');
            }

            /*
               $fecha = getdate();
               $fecha_hoy = $fecha["mday"]."/".$fecha["mon"]."/".$fecha["year"];

               $pdf->addText(370,510,12,$fecha_hoy);
             */

            if ($basico == 2) {
                $pdf->setLineStyle(1);
                $pdf->line(50, 535, 740, 535);
                $pdf->line(50, 530, 740, 530);
            }

            $pdf->restoreState();
            $pdf->closeObject();
            $pdf->addObject($all, 'all');

            $pdf->ezSetDy(-30);

            $title = $_SESSION['pdf_title_dato_sectorial'];
            $data = $_SESSION['pdf_data_dato_sectorial'];

            //FORMATO BASICO
            if ($basico == 1) {
                $options = array('showLines' => 2, 'shaded' => 0, 'width' => 750, 'fontSize' => 8, 'cols' => array('dato' => array('width' => 120), 'valor' => array('width' => 80)));
                $pdf->ezTable($data, $title, '', $options);
            }

            //MUESTRA EN EL NAVEGADOR EL PDF
            //$pdf->ezStream();

            //MUESTRA EN EL NAVEGADOR EL PDF
            if ($stream == 1) {
                //$pdf->ezStream();
                echo $pdf->ezOutput();
            } else {

                //CREA UN ARCHIVO PDF PARA BAJAR
                $nom_archivo = 'consulta/csv/datos_sectoriales.pdf';
                $fp = $file->Abrir($nom_archivo, 'wb');
                $pdfcode = $pdf->ezOutput();
                $file->Escribir($fp, $pdfcode);
                $file->Cerrar($fp);

                ?>
					<table align='center' cellspacing="1" cellpadding="3" border="0"
					width="750">
					<tr>
					<td>&nbsp;</td>
					</tr>
					<tr>
					<td align='center' class='titulo_lista' colspan=2>REPORTAR DATOS
					SECTORIALES EN FORMATO PDF</td>
					</tr>
					<tr>
					<td>&nbsp;</td>
					</tr>
					<tr>
					<td colspan=2>Se ha generado correctamente el archivo PDF de Datos
					Sectoriales.<br>
					<br>
					Para salvarlo use el bot�n derecho del mouse y la opci�n Guardar
					destino como sobre el siguiente link: <a href='<?=$nom_archivo;
                ?>'>Archivo
					PDF</a></td>
					</tr>
					</table>
					<?php

            }
        }
        //EXCEL
        elseif ($formato == 2) {
            $xls = $_SESSION['xls_dato_sectorial'];

            if ($stream == 0) {
                $fp = $file->Abrir('consulta/csv/dato_sectorial.csv', 'w');
                $file->Escribir($fp, $xls);
                $file->Cerrar($fp);

                ?>
					<table align='center' cellspacing="1" cellpadding="3" border="0"
					width="750">
					<tr>
					<td>&nbsp;</td>
					</tr>
					<tr>
					<td align='center' class='titulo_lista' colspan=2>REPORTAR DATOS
					SECTORIALES EN FORMATO CSV (Excel)</td>
					</tr>
					<tr>
					<td>&nbsp;</td>
					</tr>
					<tr>
					<td colspan=2>Se ha generado correctamente el archivo CSV de Datos
					Sectoriales.<br>
					<br>
					Para salvarlo use el bot�n derecho del mouse y la opci�n Guardar
					destino como sobre el siguiente link: <a
					href='consulta/csv/dato_sectorial.csv'>Archivo CSV</a></td>
					</tr>
					</table>
					<?php

            } else {
                echo "<table border=1>$xls</table>";
            }
        }
    }

    /**
     * Lista los Datos en una Tabla y Grafica los datos.
     */
    public function ReportarTablaGrafica()
    {

        //INICIALIZACION DE VARIABLES
        $depto_dao = new DeptoDAO();
        $municipio_dao = new MunicipioDAO();
        $fuente_dao = new FuenteDAO();
        $cat_dao = new CategoriaDatoSectorDAO();

        if (in_array(4, $_POST['que'])) {
            $reporte = $_POST['id_reporte_dato'];
        }

        if (in_array(5, $_POST['que'])) {
            $reporte = $_POST['id_reporte_dato_d_natural'];
        }

        if (in_array(8, $_POST['que'])) {
            $reporte = $_POST['id_reporte_dato_s_publicos'];
        }
        if (in_array(9, $_POST['que'])) {
            $reporte = $_POST['id_reporte_dato_general'];
        }
        if (in_array(10, $_POST['que'])) {
            $reporte = $_POST['id_reporte_dato_resumen'];
        }

        if ($reporte == 1 || $reporte == 2) {
            $id_dato_cabecera = $_POST['id_dato_cabecera'];
            $id_dato_resto = $_POST['id_dato_resto'];
            $valor_pob_cabecera = 0;
            $valor_pob_resto = 0;
            $title = $_POST['titulo_grafica_dato'];
        }
        if ($reporte == 3 || $reporte == 4) {
            $id_dato_amenaza_avalancha = $_POST['id_dato_amenaza_avalancha'];
            $id_dato_amenaza_deslizamiento = $_POST['id_dato_amenaza_deslizamiento'];
            $id_dato_amenaza_inundacion = $_POST['id_dato_amenaza_inundacion'];
            $id_dato_amenaza_ninguna = $_POST['id_dato_amenaza_ninguna'];
            $title = $_POST['titulo_grafica_dato_d_natural'];

            $valor_amenza_avalancha = 0;
            $valor_amenza_deslizamiento = 0;
            $valor_amenza_inundacion = 0;
            $valor_amenza_ninguna = 0;
        }

        if ($reporte == 5 || $reporte == 6 || $reporte == 7) {
            $id_dato_sin_acueducto = $_POST['id_dato_sin_acueducto'];
            $id_dato_sin_alcantarillado = $_POST['id_dato_sin_alcantarillado'];
            $id_dato_sin_electricidad = $_POST['id_dato_sin_electricidad'];
            $id_dato_sin_gas = $_POST['id_dato_sin_gas'];
            $id_dato_sin_basura = $_POST['id_dato_sin_basura'];
            $id_dato_sin_tel = $_POST['id_dato_sin_tel'];

            $title = $_POST['titulo_grafica_dato_d_natural'];

            $valor_sin_acueducto = 0;
            $valor_sin_alcantarillado = 0;
            $valor_sin_electricidad = 0;
            $valor_sin_gas = 0;
            $valor_sin_basura = 0;
            $valor_sin_tel = 0;
        }

        if ($reporte == 8) {
            $title = $_POST['titulo_grafica_dato_general'];
            $_SESSION['title'] = $title;
            $_SESSION['id_datos_click'] = array();
            $aaaa = $_POST['aaaa'];

            $id_cat_salud_publica = 4;

            //SE CONSULTAN LOS DATOS SECTORIALES DE SALUD PUBLICA DEL A�O SELECCIONADO
            $datos = $this->GetAllArray("ID_CATE = $id_cat_salud_publica AND YEAR(INICIO_DATO) >= $aaaa AND YEAR(FIN_DATO) <= $aaaa ", '', '');

            $valor = array();
            $valor_nacional = array();
            $ubicacion = '';
        }
        //RESUMEN GENERAL
        if ($reporte == 9) {
            $title = $_POST['titulo_resumen'];
            $_SESSION['id_datos_resumen'] = array();
            $_SESSION['id_cats_resumen'] = array();

            //SE CONSULTAN LOS DATOS SECTORIALES DEL A�O SELECCIONADO
            /*$c = 0;
              $cats = Array();
              $cats_tmp = $cat_dao->GetAllArray('');
              foreach($cats_tmp as $cat){
              $datos_tmp = $this->GetAllArray("ID_CATE = $cat->id AND $aaaa BETWEEN YEAR(INICIO_DATO) AND YEAR(FIN_DATO)",'','');

              if (count($datos_tmp) > 0){
              $datos[$cat->id] = $datos_tmp;
              $cats[$c] = $cat;
              $c++;
              }
              }*/
            $valor = array();
            $valor_nacional = array();
            $ubicacion = '';
        }

        $file = new Archivo();
        $nom_archivo_tipo = 'consulta/csv/reporte_sissh.txt';

        $fp = $file->Abrir($nom_archivo_tipo, 'w');

        //SE CONSTRUYE EL SQL
        $condicion = '';
        $arreglos = '';

        //UBIACION GEOGRAFICA
        if (isset($_POST['id_depto']) && !isset($_POST['id_muns'])) {
            $id_depto = $_POST['id_depto'];

            $m = 0;
            foreach ($id_depto as $id) {
                $id_depto_s[$m] = "'".$id."'";
                ++$m;
            }
            $id_depto_s = implode(',', $id_depto_s);

            if ($reporte == 1 || $reporte == 2) {

                //VALOR CABECERA
                //DEPTO
                if ($id_depto_s != "'0'") {
                    $sql = "SELECT VALOR_DATO from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_DEPTO IN ($id_depto_s)  AND depto_dato.ID_DATO = $id_dato_cabecera";
                } else {
                    $sql = "SELECT sum(VALOR_DATO) from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE depto_dato.ID_DATO = $id_dato_cabecera";
                }

                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0) {
                    $row_rs = $this->conn->FetchRow($rs);
                    $valor_pob_cabecera = $row_rs[0];
                }

                //VALOR RESTO
                if ($id_depto_s != "'0'") {
                    $sql = "SELECT VALOR_DATO from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_DEPTO IN ($id_depto_s)  AND depto_dato.ID_DATO = $id_dato_resto";
                } else {
                    $sql = "SELECT sum(VALOR_DATO) from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE depto_dato.ID_DATO = $id_dato_resto";
                }

                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0) {
                    $row_rs = $this->conn->FetchRow($rs);
                    $valor_pob_resto = $row_rs[0];

                    //REPORTE 2: POB TOTAL - POB INDIGENA
                    if ($reporte == 2) {
                        $valor_pob_resto = $valor_pob_resto - $valor_pob_cabecera;
                    }
                }

                //SI NO EXISTE DATO DEL DEPARTAMENTO LISTA TODOS LOS MUNICIPIOS
                if ($valor_pob_resto == 0) {
                    $muns = $municipio_dao->GetAllArrayID('ID_DEPTO IN ('.$id_depto_s.')', '');
                    $m_m = 0;
                    foreach ($muns as $id) {
                        $muns[$m_m] = "'".$id."'";
                        ++$m_m;
                    }

                    $id_muns = implode(',', $muns);

                    //VALOR CABECERA
                    if ($id_depto_s != "'0'") {
                        $sql = "SELECT sum(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_muns)  AND mpio_dato.ID_DATO = $id_dato_cabecera";
                    } else {
                        $sql = "SELECT sum(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE mpio_dato.ID_DATO = $id_dato_cabecera";
                    }

                    $rs = $this->conn->OpenRecordset($sql);
                    if ($this->conn->RowCount($rs) > 0) {
                        $row_rs = $this->conn->FetchRow($rs);
                        $valor_pob_cabecera = $row_rs[0];
                    }

                    //VALOR RESTO
                    if ($id_depto_s != "'0'") {
                        $sql = "SELECT sum(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_muns)  AND mpio_dato.ID_DATO = $id_dato_resto";
                    } else {
                        $sql = "SELECT sum(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE mpio_dato.ID_DATO = $id_dato_resto";
                    }
                    $rs = $this->conn->OpenRecordset($sql);
                    if ($this->conn->RowCount($rs) > 0) {
                        $row_rs = $this->conn->FetchRow($rs);
                        $valor_pob_resto = $row_rs[0];

                        //REPORTE 2: POB TOTAL - POB INDIGENA
                        if ($reporte == 2) {
                            $valor_pob_resto = $valor_pob_resto - $valor_pob_cabecera;
                        }
                    }
                }
            }
            //RESPORTES DE DESASTRES NATURALES
            elseif ($reporte == 3 || $reporte == 4) {

                //AMENAZA AVALANCHA
                //DEPTO
                if ($id_depto_s != "'0'") {
                    $sql = "SELECT VALOR_DATO from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_DEPTO IN ($id_depto_s)  AND depto_dato.ID_DATO = $id_dato_amenaza_avalancha";
                } else {
                    $sql = "SELECT sum(VALOR_DATO) from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE depto_dato.ID_DATO = $id_dato_amenaza_avalancha";
                }

                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0) {
                    $row_rs = $this->conn->FetchRow($rs);
                    $valor_amenza_avalancha = $row_rs[0];
                }

                //AMENAZA DESLIZAMIENTO
                //DEPTO
                if ($id_depto_s != "'0'") {
                    $sql = "SELECT VALOR_DATO from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_DEPTO IN ($id_depto_s)  AND depto_dato.ID_DATO = $id_dato_amenaza_deslizamiento";
                } else {
                    $sql = "SELECT sum(VALOR_DATO) from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE depto_dato.ID_DATO = $id_dato_amenaza_deslizamiento";
                }

                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0) {
                    $row_rs = $this->conn->FetchRow($rs);
                    $valor_amenza_deslizamiento = $row_rs[0];
                }

                //AMENAZA INUNDACION
                //DEPTO
                if ($id_depto_s != "'0'") {
                    $sql = "SELECT VALOR_DATO from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_DEPTO IN ($id_depto_s)  AND depto_dato.ID_DATO = $id_dato_amenaza_inundacion";
                } else {
                    $sql = "SELECT sum(VALOR_DATO) from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE depto_dato.ID_DATO = $id_dato_amenaza_inundacion";
                }

                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0) {
                    $row_rs = $this->conn->FetchRow($rs);
                    $valor_amenza_inundacion = $row_rs[0];
                }

                //AMENAZA NINGUNA
                //DEPTO
                if ($id_depto_s != "'0'") {
                    $sql = "SELECT VALOR_DATO from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_DEPTO IN ($id_depto_s)  AND depto_dato.ID_DATO = $id_dato_amenaza_ninguna";
                } else {
                    $sql = "SELECT sum(VALOR_DATO) from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE depto_dato.ID_DATO = $id_dato_amenaza_ninguna";
                }

                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0) {
                    $row_rs = $this->conn->FetchRow($rs);
                    $valor_amenza_ninguna = $row_rs[0];
                }

                //SI NO EXISTE DATO DEL DEPARTAMENTO LISTA TODOS LOS MUNICIPIOS
                if ($valor_amenza_avalancha == 0 && $valor_amenza_deslizamiento == 0 && $valor_amenza_inundacion == 0 && $valor_amenza_ninguna == 0) {
                    $muns = $municipio_dao->GetAllArrayID('ID_DEPTO IN ('.$id_depto_s.')', '');
                    $m_m = 0;
                    foreach ($muns as $id) {
                        $muns[$m_m] = "'".$id."'";
                        ++$m_m;
                    }

                    $id_muns = implode(',', $muns);

                    //AMENAZA AVALANCHA
                    if ($id_depto_s != "'0'") {
                        $sql = "SELECT AVG(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_muns)  AND mpio_dato.ID_DATO = $id_dato_amenaza_avalancha";
                    } else {
                        $sql = "SELECT AVG(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE mpio_dato.ID_DATO = $id_dato_amenaza_avalancha";
                    }

                    $rs = $this->conn->OpenRecordset($sql);
                    if ($this->conn->RowCount($rs) > 0) {
                        $row_rs = $this->conn->FetchRow($rs);
                        $valor_amenza_avalancha = $row_rs[0];
                    }

                    //AMENAZA DESLIZAMIENTO
                    if ($id_depto_s != "'0'") {
                        $sql = "SELECT AVG(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_muns)  AND mpio_dato.ID_DATO = $id_dato_amenaza_deslizamiento";
                    } else {
                        $sql = "SELECT AVG(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE mpio_dato.ID_DATO = $id_dato_amenaza_deslizamiento";
                    }

                    $rs = $this->conn->OpenRecordset($sql);
                    if ($this->conn->RowCount($rs) > 0) {
                        $row_rs = $this->conn->FetchRow($rs);
                        $valor_amenza_deslizamiento = $row_rs[0];
                    }

                    //AMENAZA INUNDACION
                    if ($id_depto_s != "'0'") {
                        $sql = "SELECT AVG(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_muns)  AND mpio_dato.ID_DATO = $id_dato_amenaza_inundacion";
                    } else {
                        $sql = "SELECT AVG(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE mpio_dato.ID_DATO = $id_dato_amenaza_inundacion";
                    }

                    $rs = $this->conn->OpenRecordset($sql);
                    if ($this->conn->RowCount($rs) > 0) {
                        $row_rs = $this->conn->FetchRow($rs);
                        $valor_amenza_inundacion = $row_rs[0];
                    }

                    //AMENAZA NINGUNA
                    if ($id_depto_s != "'0'") {
                        $sql = "SELECT AVG(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_muns)  AND mpio_dato.ID_DATO = $id_dato_amenaza_ninguna";
                    } else {
                        $sql = "SELECT AVG(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE mpio_dato.ID_DATO = $id_dato_amenaza_ninguna";
                    }

                    $rs = $this->conn->OpenRecordset($sql);
                    if ($this->conn->RowCount($rs) > 0) {
                        $row_rs = $this->conn->FetchRow($rs);
                        $valor_amenza_ninguna = $row_rs[0];
                    }
                }
            }

            //RESPORTES DE DESASTRES NATURALES
            elseif ($reporte == 5 || $reporte == 6 || $reporte == 7) {

                //ACUEDUCTO
                //DEPTO
                if ($id_depto_s != "'0'") {
                    $sql = "SELECT VALOR_DATO from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_DEPTO IN ($id_depto_s)  AND depto_dato.ID_DATO = $id_dato_sin_acueducto";
                } else {
                    $sql = "SELECT sum(VALOR_DATO) from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE depto_dato.ID_DATO = $id_dato_sin_acueducto";
                }

                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0) {
                    $row_rs = $this->conn->FetchRow($rs);
                    $valor_sin_acueducto = $row_rs[0];
                }

                //ALCANTARILLADO
                //DEPTO
                if ($id_depto_s != "'0'") {
                    $sql = "SELECT VALOR_DATO from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_DEPTO IN ($id_depto_s)  AND depto_dato.ID_DATO = $id_dato_sin_alcantarillado";
                } else {
                    $sql = "SELECT sum(VALOR_DATO) from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE depto_dato.ID_DATO = $id_dato_sin_alcantarillado";
                }

                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0) {
                    $row_rs = $this->conn->FetchRow($rs);
                    $valor_sin_alcantarillado = $row_rs[0];
                }

                //ELECTRICIDAD
                //DEPTO
                if ($id_depto_s != "'0'") {
                    $sql = "SELECT VALOR_DATO from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_DEPTO IN ($id_depto_s)  AND depto_dato.ID_DATO = $id_dato_sin_electricidad";
                } else {
                    $sql = "SELECT sum(VALOR_DATO) from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE depto_dato.ID_DATO = $id_dato_sin_electricidad";
                }

                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0) {
                    $row_rs = $this->conn->FetchRow($rs);
                    $valor_sin_electricidad = $row_rs[0];
                }

                //GAS
                //DEPTO
                if ($id_depto_s != "'0'") {
                    $sql = "SELECT VALOR_DATO from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_DEPTO IN ($id_depto_s)  AND depto_dato.ID_DATO = $id_dato_sin_gas";
                } else {
                    $sql = "SELECT sum(VALOR_DATO) from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE depto_dato.ID_DATO = $id_dato_sin_gas";
                }

                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0) {
                    $row_rs = $this->conn->FetchRow($rs);
                    $valor_sin_gas = $row_rs[0];
                }

                //BASURA
                //DEPTO
                if ($id_depto_s != "'0'") {
                    $sql = "SELECT VALOR_DATO from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_DEPTO IN ($id_depto_s)  AND depto_dato.ID_DATO = $id_dato_sin_basura";
                } else {
                    $sql = "SELECT sum(VALOR_DATO) from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE depto_dato.ID_DATO = $id_dato_sin_basura";
                }

                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0) {
                    $row_rs = $this->conn->FetchRow($rs);
                    $valor_sin_basura = $row_rs[0];
                }

                //TEL
                //DEPTO
                if ($id_depto_s != "'0'") {
                    $sql = "SELECT VALOR_DATO from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_DEPTO IN ($id_depto_s)  AND depto_dato.ID_DATO = $id_dato_sin_tel";
                } else {
                    $sql = "SELECT sum(VALOR_DATO) from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE depto_dato.ID_DATO = $id_dato_sin_tel";
                }

                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0) {
                    $row_rs = $this->conn->FetchRow($rs);
                    $valor_sin_tel = $row_rs[0];
                }

                //SI NO EXISTE DATO DEL DEPARTAMENTO LISTA TODOS LOS MUNICIPIOS
                if ($valor_sin_acueducto == 0 && $valor_sin_alcantarillado == 0 && $valor_sin_electricidad == 0 && $valor_sin_gas == 0 && $valor_sin_basura == 0 && $valor_sin_tel == 0) {
                    $muns = $municipio_dao->GetAllArrayID('ID_DEPTO IN ('.$id_depto_s.')', '');
                    $m_m = 0;
                    foreach ($muns as $id) {
                        $muns[$m_m] = "'".$id."'";
                        ++$m_m;
                    }

                    $id_muns = implode(',', $muns);

                    //ACUEDUCTO
                    if ($id_depto_s != "'0'") {
                        $sql = "SELECT AVG(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_muns)  AND mpio_dato.ID_DATO = $id_dato_sin_acueducto";
                    } else {
                        $sql = "SELECT AVG(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE mpio_dato.ID_DATO = $id_dato_sin_acueducto";
                    }

                    $rs = $this->conn->OpenRecordset($sql);
                    if ($this->conn->RowCount($rs) > 0) {
                        $row_rs = $this->conn->FetchRow($rs);
                        $valor_sin_acueducto = $row_rs[0];
                    }

                    //ALCANTARILLADO
                    if ($id_depto_s != "'0'") {
                        $sql = "SELECT AVG(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_muns)  AND mpio_dato.ID_DATO = $id_dato_sin_alcantarillado";
                    } else {
                        $sql = "SELECT AVG(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE mpio_dato.ID_DATO = $id_dato_sin_alcantarillado";
                    }

                    $rs = $this->conn->OpenRecordset($sql);
                    if ($this->conn->RowCount($rs) > 0) {
                        $row_rs = $this->conn->FetchRow($rs);
                        $valor_sin_alcantarillado = $row_rs[0];
                    }

                    //ELECTRICIDAD
                    if ($id_depto_s != "'0'") {
                        $sql = "SELECT AVG(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_muns)  AND mpio_dato.ID_DATO = $id_dato_sin_electricidad";
                    } else {
                        $sql = "SELECT AVG(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE mpio_dato.ID_DATO = $id_dato_sin_electricidad";
                    }

                    $rs = $this->conn->OpenRecordset($sql);
                    if ($this->conn->RowCount($rs) > 0) {
                        $row_rs = $this->conn->FetchRow($rs);
                        $valor_sin_electricidad = $row_rs[0];
                    }

                    //GAS
                    if ($id_depto_s != "'0'") {
                        $sql = "SELECT AVG(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_muns)  AND mpio_dato.ID_DATO = $id_dato_sin_gas";
                    } else {
                        $sql = "SELECT AVG(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE mpio_dato.ID_DATO = $id_dato_sin_gas";
                    }

                    $rs = $this->conn->OpenRecordset($sql);
                    if ($this->conn->RowCount($rs) > 0) {
                        $row_rs = $this->conn->FetchRow($rs);
                        $valor_sin_gas = $row_rs[0];
                    }

                    //BASURA
                    if ($id_depto_s != "'0'") {
                        $sql = "SELECT AVG(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_muns)  AND mpio_dato.ID_DATO = $id_dato_sin_basura";
                    } else {
                        $sql = "SELECT AVG(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE mpio_dato.ID_DATO = $id_dato_sin_basura";
                    }

                    $rs = $this->conn->OpenRecordset($sql);
                    if ($this->conn->RowCount($rs) > 0) {
                        $row_rs = $this->conn->FetchRow($rs);
                        $valor_sin_basura = $row_rs[0];
                    }

                    //TEL
                    if ($id_depto_s != "'0'") {
                        $sql = "SELECT AVG(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_muns)  AND mpio_dato.ID_DATO = $id_dato_sin_tel";
                    } else {
                        $sql = "SELECT AVG(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE mpio_dato.ID_DATO = $id_dato_sin_tel";
                    }

                    $rs = $this->conn->OpenRecordset($sql);
                    if ($this->conn->RowCount($rs) > 0) {
                        $row_rs = $this->conn->FetchRow($rs);
                        $valor_sin_tel = $row_rs[0];
                    }
                }
            }

            if ($reporte == 8) {
                foreach ($datos as $dato) {
                    //DEPTO
                    if ($id_depto_s != "'0'") {
                        $sql = "SELECT VALOR_DATO from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_DEPTO IN ($id_depto_s)  AND depto_dato.ID_DATO = $dato->id";
                        $rs = $this->conn->OpenRecordset($sql);
                        if ($this->conn->RowCount($rs) > 0) {
                            $row_rs = $this->conn->FetchRow($rs);
                            $valor[$dato->id] = number_format($row_rs[0], 0);
                        }

                        $vo = $depto_dao->Get($id_depto_s);
                        $ubicacion = $vo->nombre;
                    }

                    $sql = "SELECT sum(VALOR_DATO) from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE depto_dato.ID_DATO = $dato->id";
                    $rs = $this->conn->OpenRecordset($sql);
                    $row_rs = $this->conn->FetchRow($rs);
                    if ($row_rs[0] != null) {
                        $valor_nacional[$dato->id] = number_format($row_rs[0], 0);
                    }

                    //SI NO EXISTE DATO DEL DEPARTAMENTO LISTA TODOS LOS MUNICIPIOS
                    if (!isset($valor_nacional[$dato->id])) {
                        $muns = $municipio_dao->GetAllArrayID('ID_DEPTO IN ('.$id_depto_s.')', '');
                        $m_m = 0;
                        foreach ($muns as $id) {
                            $muns[$m_m] = "'".$id."'";
                            ++$m_m;
                        }

                        $id_muns = implode(',', $muns);

                        if ($id_depto_s != "'0'") {
                            $sql = "SELECT VALOR_DATO from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_muns)  AND mpio_dato.ID_DATO = $dato->id";
                            $rs = $this->conn->OpenRecordset($sql);
                            if ($this->conn->RowCount($rs) > 0) {
                                $row_rs = $this->conn->FetchRow($rs);
                                $valor[$dato->id] = number_format($row_rs[0], 0);
                            }
                        }

                        $sql = "SELECT sum(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE mpio_dato.ID_DATO = $dato->id";
                        $rs = $this->conn->OpenRecordset($sql);
                        $row_rs = $this->conn->FetchRow($rs);
                        if ($row_rs[0] != null) {
                            $valor_nacional[$dato->id] = number_format($row_rs[0], 0, '', '');
                        }
                    }
                }
            }
            //RESUMEN
            if ($reporte == 9) {
                if ($id_depto_s != "'0'") {
                    $dato_para = 1;
                } else {
                    $dato_para = 3;
                }
                $id_ubicacion = $id_depto_s;
                /*foreach ($cats as $cat){
                  foreach ($datos[$cat->id] as $dato){
                //DEPTO
                if ($id_depto_s != "'0'"){
                $sql = "SELECT VALOR_DATO from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_DEPTO IN ($id_depto_s)  AND depto_dato.ID_DATO = $dato->id";
                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0){
                $row_rs = $this->conn->FetchRow($rs);
                $valor[$cat->id][$dato->id] = number_format($row_rs[0],0);
                }

                $vo  = $depto_dao->Get($id_depto_s);
                $ubicacion = $vo->nombre;

                }

                //LOS DATOS QUE SON PORCENTAJE USAN AVG
                if ($dato->id_unidad == 4 || $dato->id_unidad == 9){
                $sql = "SELECT AVG(VALOR_DATO) from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE depto_dato.ID_DATO = $dato->id";
                }
                else{
                $sql = "SELECT sum(VALOR_DATO) from depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE depto_dato.ID_DATO = $dato->id";
                }
                $rs = $this->conn->OpenRecordset($sql);
                $row_rs = $this->conn->FetchRow($rs);
                if ($row_rs[0] != null){
                $valor_nacional[$cat->id][$dato->id] = number_format($row_rs[0],0);
                }

                //SI NO EXISTE DATO DEL DEPARTAMENTO LISTA TODOS LOS MUNICIPIOS
                if (!isset($valor_nacional[$dato->id])){
                $muns = $municipio_dao->GetAllArrayID("ID_DEPTO IN (".$id_depto_s.")",'');
                $m_m = 0;
                foreach ($muns as $id){
                $muns[$m_m] = "'".$id."'";
                $m_m++;
                }

                $id_muns = implode(",",$muns);

                if ($id_depto_s != "'0'"){

                if ($dato->id_unidad == 4 || $dato->id_unidad == 9){
                $sql = "SELECT AVG(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_muns)  AND mpio_dato.ID_DATO = $dato->id";
                }
                else{
                $sql = "SELECT sum(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_muns)  AND mpio_dato.ID_DATO = $dato->id";
                }
                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0){
                $row_rs = $this->conn->FetchRow($rs);
                $valor[$cat->id][$dato->id] = number_format($row_rs[0],0);
                }
                }

                //LOS DATOS QUE SON PORCENTAJE USAN AVG
                if ($dato->id_unidad == 4 || $dato->id_unidad == 9){
                $sql = "SELECT AVG(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE mpio_dato.ID_DATO = $dato->id";
                }
                else{
                $sql = "SELECT sum(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE mpio_dato.ID_DATO = $dato->id";
                }
                $rs = $this->conn->OpenRecordset($sql);
                $row_rs = $this->conn->FetchRow($rs);
                if ($row_rs[0] != null){
                $valor_nacional[$cat->id][$dato->id] = number_format($row_rs[0],0,'','');
                }
                }
                }
                }*/
            }
        }

        //MUNICIPIO
        elseif (isset($_POST['id_muns'])) {
            $id_mun = $_POST['id_muns'];

            $m = 0;
            foreach ($id_mun as $id) {
                $id_mun_s[$m] = "'".$id."'";
                ++$m;
            }
            $id_mun_s = implode(',', $id_mun_s);

            if ($reporte == 1 || $reporte == 2) {

                //VALOR CABECERA
                $sql = "SELECT VALOR_DATO from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_mun_s) AND mpio_dato.ID_DATO = $id_dato_cabecera";

                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0) {
                    $row_rs = $this->conn->FetchRow($rs);
                    $valor_pob_cabecera = $row_rs[0];
                }

                //VALOR RESTO
                $sql = "SELECT VALOR_DATO from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_mun_s) AND mpio_dato.ID_DATO = $id_dato_resto";
                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0) {
                    $row_rs = $this->conn->FetchRow($rs);
                    $valor_pob_resto = $row_rs[0];

                    //REPORTE 2: POB TOTAL - POB INDIGENA
                    if ($reporte == 2) {
                        $valor_pob_resto = $valor_pob_resto - $valor_pob_cabecera;
                    }
                }
            } elseif ($reporte == 3 || $reporte == 4) {

                //AMENAZA AVALANCHA
                $sql = "SELECT VALOR_DATO from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_mun_s) AND mpio_dato.ID_DATO = $id_dato_amenaza_avalancha";
                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0) {
                    $row_rs = $this->conn->FetchRow($rs);
                    $valor_amenza_avalancha = $row_rs[0];
                }

                //AMENAZA DESLIZAMIENTO
                $sql = "SELECT VALOR_DATO from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_mun_s) AND mpio_dato.ID_DATO = $id_dato_amenaza_deslizamiento";

                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0) {
                    $row_rs = $this->conn->FetchRow($rs);
                    $valor_amenza_deslizamiento = $row_rs[0];
                }

                //AMENAZA INUNDACION
                $sql = "SELECT VALOR_DATO from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_mun_s) AND mpio_dato.ID_DATO = $id_dato_amenaza_inundacion";

                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0) {
                    $row_rs = $this->conn->FetchRow($rs);
                    $valor_amenza_inundacion = $row_rs[0];
                }

                //AMENAZA NINGUNA
                $sql = "SELECT VALOR_DATO from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_mun_s) AND mpio_dato.ID_DATO = $id_dato_amenaza_ninguna";

                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0) {
                    $row_rs = $this->conn->FetchRow($rs);
                    $valor_amenza_ninguna = $row_rs[0];
                }
            } elseif ($reporte == 5 || $reporte == 6  || $reporte == 7) {

                //ACUEDUCTO
                $sql = "SELECT VALOR_DATO from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_mun_s) AND mpio_dato.ID_DATO = $id_dato_sin_acueducto";
                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0) {
                    $row_rs = $this->conn->FetchRow($rs);
                    $valor_sin_acueducto = $row_rs[0];
                }

                //ALCANTARILLADO
                $sql = "SELECT VALOR_DATO from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_mun_s) AND mpio_dato.ID_DATO = $id_dato_sin_alcantarillado";
                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0) {
                    $row_rs = $this->conn->FetchRow($rs);
                    $valor_sin_alcantarillado = $row_rs[0];
                }
                //ELECTRICIDAD
                $sql = "SELECT VALOR_DATO from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_mun_s) AND mpio_dato.ID_DATO = $id_dato_sin_electricidad";
                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0) {
                    $row_rs = $this->conn->FetchRow($rs);
                    $valor_sin_electricidad = $row_rs[0];
                }
                //GAS
                $sql = "SELECT VALOR_DATO from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_mun_s) AND mpio_dato.ID_DATO = $id_dato_sin_gas";
                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0) {
                    $row_rs = $this->conn->FetchRow($rs);
                    $valor_sin_gas = $row_rs[0];
                }
                //BASURA
                $sql = "SELECT VALOR_DATO from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_mun_s) AND mpio_dato.ID_DATO = $id_dato_sin_basura";
                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0) {
                    $row_rs = $this->conn->FetchRow($rs);
                    $valor_sin_basura = $row_rs[0];
                }
                //TEL
                $sql = "SELECT VALOR_DATO from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_mun_s) AND mpio_dato.ID_DATO = $id_dato_sin_tel";
                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0) {
                    $row_rs = $this->conn->FetchRow($rs);
                    $valor_sin_tel = $row_rs[0];
                }
            }

            if ($reporte == 8) {
                foreach ($datos as $dato) {
                    $sql = "SELECT sum(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE mpio_dato.ID_DATO = $dato->id";
                    $rs = $this->conn->OpenRecordset($sql);
                    if ($this->conn->RowCount($rs) > 0) {
                        $row_rs = $this->conn->FetchRow($rs);
                        $valor_nacional[$dato->id] = number_format($row_rs[0], 0);
                    }

                    $sql = "SELECT VALOR_DATO from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_mun_s) AND mpio_dato.ID_DATO = $dato->id";
                    $rs = $this->conn->OpenRecordset($sql);
                    if ($this->conn->RowCount($rs) > 0) {
                        $row_rs = $this->conn->FetchRow($rs);
                        $valor[$dato->id] = number_format($row_rs[0], 0);
                    }
                }
            }
            //RESUMEN
            if ($reporte == 9) {
                $dato_para = 2;
                $id_ubicacion = $id_mun_s;
                /*foreach ($cats as $cat){
                  $id_cat = $cat->id;
                  foreach ($datos as $dato){

                //LOS DATOS QUE SON PORCENTAJE USAN AVG
                if ($dato->id_unidad == 4 || $dato->id_unidad == 9){
                $sql = "SELECT AVG(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE mpio_dato.ID_DATO = $dato->id";
                }
                else{
                $sql = "SELECT sum(VALOR_DATO) from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE mpio_dato.ID_DATO = $dato->id";
                }
                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0){
                $row_rs = $this->conn->FetchRow($rs);
                $valor_nacional[$id_cat][$dato->id] = number_format($row_rs[0],2);
                }

                $sql = "SELECT VALOR_DATO from mpio_dato INNER JOIN dato_sector ON mpio_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ($id_mun_s) AND mpio_dato.ID_DATO = $dato->id";
                $rs = $this->conn->OpenRecordset($sql);
                if ($this->conn->RowCount($rs) > 0){
                $row_rs = $this->conn->FetchRow($rs);
                $valor[$id_cat][$dato->id] = number_format($row_rs[0],2);
                }
                }
                }*/
            }
        }

        if ($reporte == 1 || $reporte == 2) {
            $valor_pob_cabecera = number_format($valor_pob_cabecera, 0, '', '');
            $valor_pob_resto = number_format($valor_pob_resto, 0, '', '');
        }

        if ($reporte == 3 || $reporte == 4) {
            $valor_amenza_avalancha = number_format($valor_amenza_avalancha, 2);
            $valor_amenza_deslizamiento = number_format($valor_amenza_deslizamiento, 2);
            $valor_amenza_inundacion = number_format($valor_amenza_inundacion, 2);
            $valor_amenza_ninguna = number_format($valor_amenza_ninguna, 2);
        }
        if ($reporte == 5 || $reporte == 6  || $reporte == 7) {
            $valor_sin_acueducto = number_format($valor_sin_acueducto, 2);
            $valor_sin_alcantarillado = number_format($valor_sin_alcantarillado, 2);
            $valor_sin_electricidad = number_format($valor_sin_electricidad, 2);
            $valor_sin_gas = number_format($valor_sin_gas, 2);
            $valor_sin_basura = number_format($valor_sin_basura, 2);
            $valor_sin_tel = number_format($valor_sin_tel, 2);
        }
        if ($reporte == 8) {
            $_SESSION['valor_nacional'] = $valor_nacional;
            $_SESSION['valor'] = $valor;
            $_SESSION['ubicacion'] = $ubicacion;
            echo "<input type='hidden' id='id_dato_click' name='id_dato_click'>";
            echo "<input type='hidden' id='click_state' name='click_state'>";
        }
        //TABLA RESUMEN
        if ($reporte == 9) {
            $_SESSION['id_ubicacion'] = $id_ubicacion;
            $_SESSION['dato_para'] = $dato_para;
        }

        echo "<table align='center' cellspacing='1' cellpadding='3' width='750'>";
        echo "<tr><td><img src='images/back.gif' border=0>&nbsp;<a href='javascript:history.back(-1)'>Regresar</a></td>
			<td colspan='5' align='right'><img src='images/consulta/excel.gif'>&nbsp;<a href='consulta/csv/reporte_sissh.txt'>Guardar Archivo</a></td>
			</tr>";

        echo "<tr class='titulo_lista'><td colspan='10'>$title</td></tr>";

        //ESCRIBE TITULO
        $file->Escribir($fp, $title."\n\n");

        if ($reporte == 1 || $reporte == 2) {
            if ($valor_pob_cabecera > 0) {
                if ($reporte == 1) {
                    echo '<tr><td><b>Poblaci&oacute;n Cabecera</b></td><td><b>Poblaci&oacute;n Resto</b></td>';
                    $file->Escribir($fp, "Poblaci�n Cabecera|Poblaci�n Resto\n");
                }
                if ($reporte == 2) {
                    echo '<tr><td><b>Poblaci�n Ind&iacute;gena</b></td><td><b>Otro</b></td>';
                    $file->Escribir($fp, "Poblaci�n Ind�gena|Otro\n");
                }

                echo "<tr class='fila_lista'><td>$valor_pob_cabecera</td><td>$valor_pob_resto</td>";

                $file->Escribir($fp, "$valor_pob_cabecera|$valor_pob_resto\n");
            } else {
                echo "<tr><td align='center'><br><b>NO SE ENCONTRARON DATOS</b></td></tr>";
            }
        }

        if ($reporte == 3 || $reporte == 4) {
            if ($valor_amenza_avalancha == 0 && $valor_amenza_deslizamiento == 0 && $valor_amenza_inundacion == 0 && $valor_amenza_ninguna == 0) {
                echo "<tr><td align='center'><br><b>NO SE ENCONTRARON DATOS</b></td></tr>";
            } else {
                if ($reporte == 4) {
                    echo '<tr>
						<td><b>Amenaza avalancha - Rural</b></td>
						<td><b>Amenaza deslizamiento - Rural</b></td>
						<td><b>Amenaza inundaci&oacute;n - Rural</b></td>
						<td><b>Ninguna amenaza - Rural</b></td>
						</tr>';

                    $file->Escribir($fp, "Amenaza avalancha - Rural|Amenaza deslizamiento - Rural|Amenaza inundaci�n - Rural|Ninguna amenaza - Rural\n");
                } elseif ($reporte == 3) {
                    echo '<tr>
						<td><b>Amenaza avalancha - Urbana (Cabecera y Centro Poblado)</b></td>
						<td><b>Amenaza deslizamiento - Urbana (Cabecera y Centro Poblado)</b></td>
						<td><b>Amenaza inundaci&oacute;n - Urbana (Cabecera y Centro Poblado)</b></td>
						<td><b>Ninguna amenaza - Urbana (Cabecera y Centro Poblado)</b></td>
						</tr>';

                    $file->Escribir($fp, "Amenaza avalancha - Urbana (Cabecera y Centro Poblado)|Amenaza deslizamiento - Urbana (Cabecera y Centro Poblado)|Amenaza inundaci�n - Urbana (Cabecera y Centro Poblado)|Ninguna amenaza - Urbana (Cabecera y Centro Poblado)\n");
                }

                echo "<tr class='fila_lista'>
					<td>$valor_amenza_avalancha %</td>
					<td>$valor_amenza_deslizamiento %</td>
					<td>$valor_amenza_inundacion %</td>
					<td>$valor_amenza_ninguna %</td>
					</tr>";

                $file->Escribir($fp, "$valor_amenza_avalancha|$valor_amenza_deslizamiento|$valor_amenza_inundacion|$valor_amenza_ninguna\n");
            }
        }

        if ($reporte == 5 || $reporte == 6  || $reporte == 7) {
            if ($valor_sin_acueducto == 0 && $valor_sin_alcantarillado == 0 && $valor_sin_electricidad == 0 && $valor_sin_gas == 0 && $valor_sin_basura == 0 && $valor_sin_tel == 0) {
                echo "<tr><td align='center'><br><b>NO SE ENCONTRARON DATOS</b></td></tr>";
            } else {
                if ($reporte == 5) {
                    echo '<tr>
						<td><b>Sin servicio de acueducto - Cabecera y centro poblado</b></td>
						<td><b>Sin servicio de alcantarillado - Cabecera y centro poblado</b></td>
						<td><b>Sin servicio de electricidad - Cabecera y centro poblado</b></td>
						<td><b>Sin servicio de gas - Cabecera y centro poblado</b></td>
						<td><b>Sin servicio de recolecci&oacute;n de basuras - Cabecera y centro poblado</b></td>
						<td><b>Sin servicio de tel&eacute;fono - Cabecera y centro poblado</b></td>
						</tr>';

                    $file->Escribir($fp, "Sin servicio de acueducto - Cabecera y centro poblado|Sin servicio de alcantarillado - Cabecera y centro poblado|Sin servicio de electricidad - Cabecera y centro poblado|Sin servicio de gas - Cabecera y centro poblado|Sin servicio de recolecci&oacute;n de basuras - Cabecera y centro poblado|Sin servicio de tel&eacute;fono - Cabecera y centro poblado\n");
                }

                if ($reporte == 6) {
                    echo '<tr>
						<td><b>Sin servicio de acueducto - Rural</b></td>
						<td><b>Sin servicio de alcantarillado - Rural</b></td>
						<td><b>Sin servicio de electricidad - Rural</b></td>
						<td><b>Sin servicio de gas - Rural</b></td>
						<td><b>Sin servicio de recolecci&oacute;n de basuras - Rural</b></td>
						<td><b>Sin servicio de tel&eacute;fono - Rural</b></td>
						</tr>';

                    $file->Escribir($fp, "Sin servicio de acueducto - Rural|Sin servicio de alcantarillado - Rural|Sin servicio de electricidad - Rural|Sin servicio de gas - Rural|Sin servicio de recolecci&oacute;n de basuras - Rural|Sin servicio de tel&eacute;fono - Rural\n");
                }

                if ($reporte == 7) {
                    echo '<tr>
						<td><b>Sin servicio de acueducto - Total</b></td>
						<td><b>Sin servicio de alcantarillado - Total</b></td>
						<td><b>Sin servicio de electricidad - Total</b></td>
						<td><b>Sin servicio de gas - Total</b></td>
						<td><b>Sin servicio de recolecci&oacute;n de basuras - Total</b></td>
						<td><b>Sin servicio de tel&eacute;fono - Total</b></td>
						</tr>';

                    $file->Escribir($fp, "Sin servicio de acueducto - Total|Sin servicio de alcantarillado - Total|Sin servicio de electricidad - Total|Sin servicio de gas - Total|Sin servicio de recolecci&oacute;n de basuras - Total|Sin servicio de tel&eacute;fono - Total\n");
                }

                echo "<tr class='fila_lista'>
					<td>$valor_sin_acueducto %</td>
					<td>$valor_sin_alcantarillado %</td>
					<td>$valor_sin_electricidad %</td>
					<td>$valor_sin_gas %</td>
					<td>$valor_sin_basura %</td>
					<td>$valor_sin_tel %</td>
					</tr>";

                $file->Escribir($fp, "$valor_sin_acueducto|$valor_sin_alcantarillado|$valor_sin_electricidad|$valor_sin_gas|$valor_sin_basura|$valor_sin_tel\n");
            }
        }

        if ($reporte == 8) {
            if (count($valor_nacional) == 0 && count($valor) == 0) {
                echo "<tr><td align='center'><br><b>NO SE ENCONTRARON DATOS</b></td></tr>";
            } else {
                echo '<tr>
					<td><b>Graficar</b></td>
					<td><b>Enfermedad</b></td>
					<td><b>Nacional</b></td>';
                if ($ubicacion != '') {
                    echo "<td><b>$ubicacion</b></td>";
                }

                echo '</tr>';

                $linea = 'Enfermedad|Nacional';
                if ($ubicacion != '') {
                    $linea .= "$ubicacion";
                }

                $file->Escribir($fp, "$linea\n");

                foreach ($datos as $dato) {
                    echo "<tr class='fila_lista'>
						<td><input type='checkbox' value=".$dato->id." onclick=\"document.getElementById('id_dato_click').value=this.value;document.getElementById('click_state').value=this.checked;getData('graficaEnfermedades');\"></td>
						<td>$dato->nombre</td>";

                    $linea = $dato->nombre;

                    if (isset($valor_nacional[$dato->id])) {
                        echo '<td>'.$valor_nacional[$dato->id].'</td>';

                        $linea .= '|'.$valor_nacional[$dato->id];
                    }
                    if ($ubicacion != '' && isset($valor[$dato->id])) {
                        echo '<td>'.$valor[$dato->id].'</td>';
                    }
                    echo '</tr>';

                    $file->Escribir($fp, $linea."\n");

                    if ($ubicacion != '' && isset($valor[$dato->id])) {
                        $file->Escribir($fp, '|'.$valor[$dato->id]."\n");
                    }
                }
            }
        }
        //RESUMEN
        if ($reporte == 9) {

            //LIBRERIAS
            include_once 'admin/js/calendar/calendar.php';

            $calendar = new DHTML_Calendar('admin/js/calendar/', 'es', 'calendar-win2k-1', false);
            $calendar->load_files();
            echo '<tr><td><table cellpadding=5>';
            ?>
				<tr>
				<td colspan="2"><b>Periodo</b>:&nbsp; <b>Desde</b>&nbsp; <?php $calendar->make_input_field(
                        // calendar options go here; see the documentation and/or calendar-setup.js
                        array('firstDay' => 1, // show Monday first
                            'ifFormat' => '%Y-%m-%d',
                            'timeFormat' => '12', ),
                        // field attributes go here
                        array('class' => 'textfield',
                            'size' => '10',
                            'name' => 'f_ini', ));

            ?>&nbsp;&nbsp; <b>Hasta</b>&nbsp; <?php $calendar->make_input_field(
                    // calendar options go here; see the documentation and/or calendar-setup.js
                    array('firstDay' => 1, // show Monday first
                        'ifFormat' => '%Y-%m-%d',
                        'timeFormat' => '12', ),
                    // field attributes go here
                    array('class' => 'textfield',
                        'size' => '10',
                        'name' => 'f_fin', ));
            ?></td>
				</tr>
				<?php
                echo "<tr><td><b>1. Categoria</b></td><td><select id='id_cat' name='id_cat' onchange=\"document.getElementById('label_combo_dato_sectorial').style.display='';getDataV1('comboDatoSectorial','admin/ajax_data.php?object=comboDatoSectorial&id_cat='+this.value,'combo_dato_sectorial');\" class='select'>";
            echo '<option>Seleccione una Categoria</option>';
            echo $cat_dao->ListarCombo('', '', '');
            echo "</select></td></tr><tr><td id='label_combo_dato_sectorial' style='display:none'><b>2. Dato</b></td><td><span id='combo_dato_sectorial'></span></td></tr>";
            echo '<tr><td>&nbsp;</td></tr>';
            echo "<tr><td colspan='2' id='tabla_resumen'></td></tr>";

            echo '</table></td></tr>';

            echo '</tr>';
        }

        echo '<tr><td>&nbsp;</td></tr>';
        echo '</table>';

        $file->Cerrar($fp);

        //GRAFICA
        $PG = new PowerGraphic();

        $PG->title = $title;

        if ($reporte == 1) {
            $PG->type = 5;

            $PG->x[0] = 'Cabecera';
            $PG->y[0] = $valor_pob_cabecera;

            $PG->x[1] = 'Resto';
            $PG->y[1] = $valor_pob_resto;
        } elseif ($reporte == 2) {
            $PG->type = 5;

            $PG->x[0] = 'Ind�gena';
            $PG->y[0] = $valor_pob_cabecera;

            $PG->x[1] = 'Otro';
            $PG->y[1] = $valor_pob_resto;
        } elseif ($reporte == 3 || $reporte == 4) {
            $PG->type = 5;

            $PG->x[0] = 'Avalancha';
            $PG->y[0] = $valor_amenza_avalancha;

            $PG->x[1] = 'Deslizamiento';
            $PG->y[1] = $valor_amenza_deslizamiento;

            $PG->x[2] = 'Inundaci�n';
            $PG->y[2] = $valor_amenza_inundacion;

            $PG->x[3] = 'Ninguno';
            $PG->y[3] = $valor_amenza_ninguna;

            $PG->x[4] = 'Otro';
            $PG->y[4] = 100 - $valor_amenza_avalancha - $valor_amenza_deslizamiento - $valor_amenza_inundacion - $valor_amenza_ninguna;
        } elseif ($reporte == 5 || $reporte == 6 || $reporte == 7) {
            $PG->type = 2;

            $PG->x[0] = '             Sin Acueducto';
            $PG->y[0] = $valor_sin_acueducto;

            $PG->x[1] = '             Sin Alcantarillado';
            $PG->y[1] = $valor_sin_alcantarillado;

            $PG->x[2] = '     Sin Energ�a El�ctrica';
            $PG->y[2] = $valor_sin_electricidad;

            $PG->x[3] = '     Sin Gas';
            $PG->y[3] = $valor_sin_gas;

            $PG->x[4] = '     Sin Recolecci�n de Basura';
            $PG->y[4] = $valor_sin_basura;

            $PG->x[5] = '     Sin Tel�fono';
            $PG->y[5] = $valor_sin_tel;
        }
        $PG->skin = 1;
        $PG->credits = 0;

        ?>
			<table id="table_grafica" cellspacing='0' cellpadding='5' width="300">
			<tr>
			<td id='td_grafica'><?php
            if (!in_array($reporte, array(8, 9))) {
                echo "<img src='admin/lib/common/graphic.class.php?".$PG->create_query_string()."' border=1 /></td>";
            }
        ?>

			</tr>
			</table>
			<?php

    }

    /**
     * Lista los Datos en una Tabla.
     */
    public function ReportarMapaI()
    {
        $cat_dao = new CategoriaDatoSectorDAO();
        $depto_dao = new DeptoDAO();
        $municipio_dao = new MunicipioDAO();
        $sector_dao = new SectorDAO();
        $dato_dao = new self();
        $unidad_dao = new UnidadDatoSectorDAO();
        $contacto_dao = new ContactoDAO();

        $sectores = $sector_dao->GetAllArray('');
        $cats = $cat_dao->GetAllArray('');

        $dato_para = 1;
        if (isset($_POST['id_muns'])) {
            $dato_para = 2;
        }

        $tit_dato_para = 'Departamental';
        if ($dato_para == 2) {
            $tit_dato_para = 'Municipal';
        }

        $arr_id = array();

        //UBIACION GEOGRAFICA
        if (isset($_POST['id_depto']) && !isset($_POST['id_muns'])) {
            $id_depto = $_POST['id_depto'];

            $m = 0;
            foreach ($id_depto as $id) {
                $id_depto_s[$m] = "'".$id."'";
                ++$m;
            }
            $id_depto_s = implode(',', $id_depto_s);

            //$sql = "SELECT ID_DEPTO_DATO FROM depto_dato INNER JOIN dato_sector ON depto_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_DEPTO IN (".$id_depto_s.") ORDER BY ID_DEPTO_DATO, ID_CATE";
            $sql = 'SELECT ID_VALDA FROM valor_dato INNER JOIN dato_sector ON valor_dato.ID_DATO = dato_sector.ID_DATO WHERE valor_dato.ID_DEPTO IN ('.$id_depto_s.') ORDER BY ID_VALDA, ID_CATE';

            $arr_id_u_g = array();
            $i = 0;
            $rs = $this->conn->OpenRecordset($sql);
            while ($row_rs = $this->conn->FetchRow($rs)) {
                $arr_id_u_g[$i] = $row_rs[0];
                ++$i;
            }

            //SI NO EXISTE DATO DEL DEPARTAMENTO LISTA TODOS LOS MUNICIPIOS
            if ($i == 0) {
                $dato_para = 2;
                $sql = 'SELECT ID_VALDA FROM valor_dato INNER JOIN dato_sector ON valor_dato.ID_DATO = dato_sector.ID_DATO INNER JOIN municipio ON valor_dato.ID_MUN = municipio.ID_MUN WHERE valor_dato.ID_DEPTO IN ('.$id_depto_s.') ORDER BY ID_VALDA, ID_CATE';

                $arr_id_u_g = array();
                $i = 0;
                $rs = $this->conn->OpenRecordset($sql);
                while ($row_rs = $this->conn->FetchRow($rs)) {
                    $arr_id_u_g[$i] = $row_rs[0];
                    ++$i;
                }
            }
        }

        //MUNICIPIO
        elseif (isset($_POST['id_muns'])) {
            $id_mun = $_POST['id_muns'];

            $m = 0;
            foreach ($id_mun as $id) {
                $id_mun_s[$m] = "'".$id."'";
                ++$m;
            }
            $id_mun_s = implode(',', $id_mun_s);

            $sql = 'SELECT ID_VALDA FROM valor_dato INNER JOIN dato_sector ON valor_dato.ID_DATO = dato_sector.ID_DATO WHERE ID_MUN IN ('.$id_mun_s.') ORDER BY ID_VALDA, ID_CATE';
            $arr_id_u_g = array();
            $i = 0;
            $rs = $this->conn->OpenRecordset($sql);
            while ($row_rs = $this->conn->FetchRow($rs)) {
                $arr_id_u_g[$i] = $row_rs[0];
                ++$i;
            }
        }

        $arr = $arr_id_u_g;

        $num_arr = count($arr);

        echo "<table align='center' cellspacing='1' cellpadding='3' width='750'>";
        echo '<tr><td>&nbsp;</td></tr>';
        if ($num_arr > 0 && !isset($_POST['que_org']) && !isset($_POST['que_eve'])) {
            echo "<tr>
				<td colspan=2><a href='javascript:history.back(-1)'><img src='images/back.gif' border=0>&nbsp;Regresar</a></td>
				<td colspan='10' align='right'>Exportar a: <input type='image' src='images/consulta/generar_pdf.gif' onclick=\"document.getElementById('pdf_dato').value = 1;\">&nbsp;&nbsp;<input type='image' src='images/consulta/excel.gif' onclick=\"document.getElementById('pdf_dato').value = 2;\"></td>";
        }

        echo "<tr><td align='center' class='titulo_lista' colspan=10>DATOS SECTORIALES DE : ";
        //TITULO DE DEPTO
        if (isset($_POST['id_depto']) && !isset($_POST['id_muns'])) {
            $t = 0;
            foreach ($_POST['id_depto'] as $id_t) {
                $vo = $depto_dao->Get($id_t);
                if ($t == 0) {
                    echo '<b>'.$vo->nombre.'</b>';
                } else {
                    echo ', <b>'.$vo->nombre.'</b>';
                }
                ++$t;
            }
            echo '<br>';
        }
        //TITULO DE MPIO
        if (isset($_POST['id_muns'])) {
            $t = 0;
            foreach ($id_mun as $id_t) {
                $vo = $municipio_dao->Get($id_t);
                if ($t == 0) {
                    echo '<b>'.$vo->nombre.'</b>';
                } else {
                    echo ', <b>'.$vo->nombre.'</b>';
                }
                ++$t;
            }
            echo '<br>';
        }
        echo '</td>';
        echo '</td></tr>';

        if ($num_arr > 0) {
            echo "<tr class='titulo_lista'>";

            //NIVEL DEPARTAMENTAL
            if ($dato_para == 1) {
                echo '<td>Cod. Depto.</td>';
                echo '<td>Departamento</td>';
            }

            //NIVEL MUNICIPAL
            if ($dato_para == 2) {
                echo '<td>Cod. Mpio.</td>';
                echo '<td>Municipio</td>';
            }

            echo "<td width='100'>Dato</td>
				<td width='100'>Valor</td>
				<td width='300'>Inicio Vigencia</td>
				<td width='400'>Fin Vigencia</td>
				<td width='150'>Unidad</td>
				<td width='150'>Fuente</td>
				<td width='150'>Categoria</td>
				<td width='150'>Sector</td>
				</tr>";

            $id_cat_f_ant = 0;
            for ($p = 0; $p < $num_arr; ++$p) {

                /*if ($p > 0)	$id_cat_f_ant = $arr[$p-1]->id_cat;
                  $id_cat_f = $arr[$p]->id_cat;

                  $style = "";
                  if (fmod($p+1,2) == 0)  $style = "fila_lista";

                  $cat = $cat_dao->Get($id_cat_f);


                //TITULO DE LA CATEGORIA
                if ($id_cat_f_ant != $id_cat_f){
                echo "<tr><td>&nbsp;</td></tr>";
                echo "<tr><td algin='center' colspan='3'><b>CATEGORIA: ".$cat->nombre."</b></td></tr>";
                echo"<tr class='titulo_lista'>";

                if ($dato_para == 1){
                echo "<td width='120'>Departamento</td>";
                }
                else{
                echo "<td width='120'>Municipio</td>";
                }

                echo "<td>Dato</td><td width='200'>Valor</td>";

                echo "</tr>";
                }*/

                $style = '';
                if (fmod($p + 1, 2) == 0) {
                    $style = 'fila_lista';
                }

                echo "<tr class='".$style."'>";

                $id_dato_valor = $arr[$p];

                $sql = 'SELECT * FROM valor_dato WHERE ID_VALDA = '.$id_dato_valor;
                $rs = $this->conn->OpenRecordset($sql);
                $row_rs = $this->conn->FetchObject($rs);
                $id_dato = $row_rs->ID_DATO;
                $id_unidad = $row_rs->ID_UNIDAD;
                $fecha_ini = $row_rs->INI_VALDA;
                $fecha_fin = $row_rs->FIN_VALDA;

                $valor = number_format($row_rs->VAL_VALDA, 2);

                $dato = $this->Get($id_dato);

                if ($dato_para == 1) {
                    $id_ubicacion = $row_rs->ID_DEPTO;
                    $ubicacion = $depto_dao->Get($id_ubicacion);
                } else {
                    $id_ubicacion = $row_rs->ID_MUN;
                    $ubicacion = $municipio_dao->Get($id_ubicacion);
                }

                echo '<td>'.$ubicacion->id.'</td>';
                echo '<td>'.$ubicacion->nombre.'</td>';

                //DATO
                echo '<td>'.$dato->nombre.'</td>';

                ////VALOR
                echo '<td>'.$valor.'</td>';

                //FECHA INICIO
                if ($fecha_ini != '0000-00-00') {
                    echo '<td>'.$fecha_ini.'</td>';
                } else {
                    echo '<td>&nbsp;</td>';
                }

                //FECHA FIN
                if ($fecha_fin != '0000-00-00') {
                    echo '<td>'.$fecha_fin.'</td>';
                } else {
                    echo '<td>&nbsp;</td>';
                }

                //UNIDAD
                $unidad = $unidad_dao->Get($id_unidad);
                echo '<td>'.$unidad->nombre.'</td>';

                //FUENTE
                $fuente = $contacto_dao->Get($dato->id_contacto);
                echo '<td>'.$fuente->nombre.'</td>';

                //CATEGORIA
                $categoria = $cat_dao->Get($dato->id_cat);
                echo '<td>'.$categoria->nombre.'</td>';

                //SECTOR
                $sector = $sector_dao->Get($dato->id_sector);
                echo '<td>'.$sector->nombre_es.'</td>';

                echo '</tr>';
            }

            echo '<tr><td>&nbsp;</td></tr>';
        } else {
            echo "<tr><td align='center'><br><b>NO SE ENCONTRARON DATOS</b></td></tr>";
        }

        echo "<input type='hidden' name='id_datos' value='".implode(',', $arr)."'>";
        echo "<input type='hidden' id='que_dato' name='que_dato' value='1'>";
        echo "<input type='hidden' id='dato_para' name='dato_para' value=".$dato_para.'>';
        echo '</table>';
    }

    /**
     * Importa los registros de eventos con minas.
     *
     * @param file   $userfile  Archivo CSV a importar
     * @param int    $id_dato   Id del Dato
     * @param int    $dato_para Si es Dtal, Mpial o para Poblado
     * @param int    $id_unidad ID de la Unidad
     * @param string $f_ini     Inicio Periodo
     * @param string $f_fin     Fin Periodo
     * @param string $f_corte   Fecha de Corte
     */
    public function ImportarCSV($userfile, $id_dato, $dato_para, $id_unidad, $f_ini, $f_fin)
    {
        $archivo = new Archivo();
        $mun_dao = new MunicipioDAO();
        $depto_dao = new DeptoDAO();
        $sissh = new SisshDAO();

        //Borra el cache de perfiles y mapas, para reflejar los nuevos valores
        $sissh->borrarCache();
        $sissh->borrarCacheMapa('dato_sector', $id_dato);

        $dato_sectorial->id = $id_dato;
        $dato_sectorial->id_unidad = $id_unidad;
        $dato_sectorial->fecha_ini = $f_ini;
        $dato_sectorial->fecha_fin = $f_fin;

        //Check si existe valores para el periodo
        $cond = "INI_VALDA = '$dato_sectorial->fecha_ini' AND FIN_VALDA = '$dato_sectorial->fecha_fin' AND ID_DATO = $dato_sectorial->id";
        $vo = $this->GetValor($cond, $dato_para);

        if ($vo->valor != '') {
            echo '<script>';
            echo "alert('Existen valores del dato para el periodo $f_ini . $f_fin, se deben elminar atrav�s del m�dulo de administraci�n!');";
            echo "location.href='index.php?m_e=dato_s_valor&accion=importar';";
            echo '</script>';
            die();
        } else {
            $file_tmp = $userfile['tmp_name'];
            $file_nombre = $userfile['name'];

            $path = 'dato_s_valor/csv/'.$file_nombre;

            $archivo->SetPath($path);
            $archivo->Guardar($file_tmp);

            $fp = $archivo->Abrir($path, 'r');
            $cont_archivo = $archivo->LeerEnArreglo($fp);
            $archivo->Cerrar($fp);
            $num_rep = count($cont_archivo);

            $linea_tmp = $cont_archivo[1];
            $linea_tmp = split('[,]', $linea_tmp);

            $num_cols_file = count($linea_tmp);
            $num_cols_form = 2;

            $ex = 0;
            //COMIENZA DESDE LA SEGUNDA LINEA
            for ($r = 1; $r < $num_rep; ++$r) {
                $linea = $cont_archivo[$r];

                $linea = split('[,]', $linea);

                if (count($linea > 0) && $linea[0] != '') {

                    //DEPARTAMENTAL
                    if ($dato_para == 1) {
                        $dato_sectorial->id_depto = $linea[0];
                    }
                    //MUNICIPAL
                    elseif ($dato_para == 2) {
                        $dato_sectorial->id_mun = $linea[0];
                    }
                    //POBLADO
                    elseif ($dato_para == 3) {
                        $dato_sectorial->id_pob = $linea[0];
                    }

                    //VALOR
                    $dato_sectorial->valor = $linea[1];
                    $dato_sectorial->id_unidad = $id_unidad;
                    $dato_sectorial->fecha_ini = $f_ini;
                    $dato_sectorial->fecha_fin = $f_fin;
                    //$dato_sectorial->fecha_corte = $f_corte;

                    $this->InsertarDatoValor($dato_sectorial, $dato_para);

                    ++$ex;
                }
            }

            // Actualiza flag para tomar el dato sectorial en totalizacion
            $this->setFlatTotalizar($id_dato, 1);

            echo '<script>';
            echo "alert('Se cargaron : ".$ex." Registros...Por favor, no olvide totalizar con el bot�n magico!!');";
            echo "location.href='index.php?m_e=dato_s_valor&accion=importar';";
            echo '</script>';
        }
    }

    /**
     * Calcula el total nacional para cada Dato Sectorial, en cada periodo
     * y almacena los datos en la tabla total_nacional_valor_dato.
     */
    public function Totalizar()
    {
        set_time_limit(0);

        //INICIALIZA VARIABLES
        $mun_dao = new MunicipioDAO();
        $cadena = new Cadena();
        $d = 1;

        //$sql = "TRUNCATE table total_nacional_valor_dato";
        $sql = 'DELETE FROM total_nacional_valor_dato WHERE id_dato IN (SELECT id_dato FROM dato_sector WHERE totalizar_nal = 1)';
        $this->conn->Execute($sql);

        //TOTAL NACIONAL
        $datos = $this->GetAllArray("TIPO_CALC_NAL IN ('suma_mpio','suma_depto','suma_formula') AND totalizar_nal = 1", '', 'ID_DATO');
        //print_r($datos);
        //die;
        //$datos =array($this->Get(240));

        $num_datos = count($datos);

        foreach ($datos as $dato_vo) {

            $this->TotalizarUnDato(true, $dato_vo);

            echo "<script>document.getElementById('total_nal').innerHTML = '".$d.' de '.$num_datos." Datos';</script>";
            ++$d;
        }
    }

    /**
     * Calcula el total departamental para cada Dato Sectorial, en cada periodo
     * y almacena los datos en la tabla total_deptal_valor_dato.
     */
    public function TotalizarDepto()
    {
        set_time_limit(0);

        //INICIALIZA VARIABLES
        $mun_dao = new MunicipioDAO();
        $depto_dao = new DeptoDAO();
        $cadena = new Cadena();
        $d = 1;

        //$sql = "TRUNCATE table total_deptal_valor_dato";
        $sql = 'DELETE FROM total_deptal_valor_dato WHERE id_dato IN (SELECT id_dato FROM dato_sector WHERE totalizar_deptal = 1)';
        $this->conn->Execute($sql);

        //TOTAL DEPARTAMENTAL
        $datos = $this->GetAllArray("TIPO_CALC_DEPTAL IN ('suma_mpio','suma_formula') AND totalizar_deptal = 1", '', 'ID_DATO');

        $num_datos = count($datos);

        //$datos =array($this->Get(134));

        foreach ($datos as $dato_vo) {
            $id_dato = $dato_vo->id;

            $this->totalizarUnDato(false, $id_dato);

            echo "<script>document.getElementById('total_deptal').innerHTML = '".$d.' de '.$num_datos." Datos';</script>";

            ++$d;
        }
    }

    /**
     * Totaliza un dato formulado
     */
    public function totalizarUnDato($nal, $id_dato)
    {
        $depto_dao = new DeptoDAO();
        $dato_vo = $this->Get($id_dato);
        $periodos = $this->GetPeriodosValores($id_dato);

        if ($nal) {
            $this->totalizarGo($nal, $dato_vo);
        } else {
            $deptos = $depto_dao->GetAllArrayID('');

            foreach ($deptos as $id_depto) {
                $this->totalizarGo($nal, $dato_vo, $id_depto);
            }
        }
    }

    /**
     * Realiza el calculo para totalizar
     */
    public function totalizarGo($nal, $dato_vo, $id_depto = '')
    {
        set_time_limit(0);

        $id_dato = $dato_vo->id;

        $tipo_calc = ($nal) ? $dato_vo->tipo_calc_nal : $dato_vo->tipo_calc_deptal;

        $periodos = $this->GetPeriodosValores($id_dato);

        switch ($tipo_calc){
            //SUMA FORMULA
            case 'suma_formula':
                $valor = 0;
                $id_unidad = 0;
                $id_muns = $this->GetDeptosMpiosDato($id_dato,$id_depto,0);
                $formula = ($nal) ? $dato_vo->formula_calc_nal : $dato_vo->formula_calc_deptal;
                $id_datos = $cadena->getContentTag($formula,'[',']');

                foreach ($id_datos as $id_d) {
                    $id_muns_dato[$id_d] = $this->GetDeptosMpiosDato($id_d,"",0);
                }

                foreach ($periodos as $periodo){

                    $f_ini = $periodo['ini'];
                    $f_fin = $periodo['fin'];

                    foreach ($id_muns as $id_mun){
                        $formula = $dato_vo->formula_calc_nal;
                        foreach ($id_datos as $id_d) {

                            $dato_formula = $this->Get($id_d);

                            //LOS DATOS DE LA FORMULA NO SON FORMULADOS
                            if (in_array($id_mun,$id_muns_dato[$id_d]) && $dato_formula->formula == ""){

                                $vr = $this->GetValorToReport($id_d,$id_mun,$f_ini,$f_fin,2);
                                $val = $vr['valor'];
                                //if ($val != "N.D." || $val == 0){
                                if (strcmp("N.D.",$val) <> 0){
                                    $formula = str_replace("[$id_d]",$val,$formula);
                                }
                            } else if ($dato_formula->formula != "")
                            {

                                //CHECK SI LOS DATOS DE LA FORMULA DEL DATO DE LA FORMULA TIENEN VALOR EN EL MPIO
                                $id_subdatos = $cadena->getContentTag($dato_formula->formula,'[',']');

                                foreach ($id_subdatos as $id_sd) {
                                    $id_muns_subdato[$id_sd] = $this->GetDeptosMpiosDato($id_sd,"",0);
                                }

                                if (in_array($id_mun,$id_muns_subdato[$id_sd])){
                                    $vr = $this->GetValorToReport($id_d,$id_mun,$f_ini,$f_fin,2);
                                    $val = $vr['valor'];
                                    //echo strcmp("N.D.",$val)."compa";
                                    if (strcmp("N.D.",$val) <> 0){
                                        $formula = str_replace("[$id_d]",$val,$formula);
                                    }

                                }
                            }
                            //echo "formula:::$formula::::$id_mun<br>";
                        }

                        //echo "valor::$val----$id_d----$formula--->$dato_vo->formula_calc_nal----$id_mun<br>";

                        if (strpos($formula,'[') === false){
                            eval("\$valor += ".$formula.";");
                            echo "$valor<br>";
                        }
                    }

                    $id_unidad = $vr['id_unidad'];

                    if ($nal) {
                        $sql = "INSERT INTO total_nacional_valor_dato (ID_DATO,INI_VALDA,FIN_VALDA,TOTAL_NACIONAL,ID_UNIDAD) VALUES ($dato_vo->id,'$f_ini','$f_fin',$valor,$id_unidad)";
                    } else {
                        $sql = "INSERT INTO total_deptal_valor_dato (ID_DATO,INI_VALDA,FIN_VALDA,TOTAL_DEPTAL,ID_UNIDAD,ID_DEPTO) VALUES ($dato_vo->id,'$f_ini','$f_fin',$valor,$id_unidad,'$id_depto')";

                    }
                    //echo $sql;
                    $this->conn->Execute($sql);

                }

            break;

                //SUMA VALOR MUNICIPAL
            case 'suma_mpio':

                if (count($periodos) == 0) {
                    echo "Dato sin periodo: $dato_vo->nombre<br>";
                } else {
                    foreach ($periodos as $periodo){

                        $f_ini = $periodo['ini'];
                        $f_fin = $periodo['fin'];

                        $cond_fecha = " AND INI_VALDA >= '$f_ini' AND FIN_VALDA <= '$f_fin'";

                        if ($nal)
                        {
                            $sql = "SELECT VAL_VALDA, ID_UNIDAD from valor_dato WHERE ID_DATO = $id_dato AND ID_MUN IS NOT NULL".$cond_fecha;
                        } else {
                            $sql = "SELECT VAL_VALDA, ID_UNIDAD from valor_dato INNER JOIN municipio ON valor_dato.ID_MUN = municipio.ID_MUN WHERE ID_DATO = $id_dato AND municipio.ID_DEPTO = '$id_depto' AND valor_dato.ID_MUN IS NOT NULL".$cond_fecha;
                        }

                        $valor = 0;
                        $id_unidad = 0;
                        $rs = $this->conn->OpenRecordset($sql);
                        while ($row_rs = $this->conn->FetchRow($rs)){
                            $id_unidad = $row_rs[1];
                            if ($row_rs[0] != null){
                                $valor += $row_rs[0];
                            }
                        }

                        if ($nal)
                        {
                            $sql = "INSERT INTO total_nacional_valor_dato (ID_DATO,INI_VALDA,FIN_VALDA,TOTAL_NACIONAL,ID_UNIDAD) VALUES ($dato_vo->id,'$f_ini','$f_fin',$valor,$id_unidad)";
                        } else {
                            $sql = "INSERT INTO total_deptal_valor_dato (ID_DATO,INI_VALDA,FIN_VALDA,TOTAL_DEPTAL,ID_UNIDAD,ID_DEPTO) VALUES ($dato_vo->id,'$f_ini','$f_fin',$valor,$id_unidad,'$id_depto')";
                            $this->setFlatTotalizar($dato_vo->id, 0, 'deptal');
                        }

                        //echo $sql;
                        $this->conn->Execute($sql);

                    }
                }
            break;

            //SUMA VALOR MUNICIPAL
            case 'suma_depto':

                if (count($periodos) == 0){
                    echo "Dato sin periodo: $dato_vo->nombre<br>";
                }
                else{

                    foreach ($periodos as $periodo){

                        $f_ini = $periodo['ini'];
                        $f_fin = $periodo['fin'];

                        $cond_fecha = " AND INI_VALDA >= '$f_ini' AND FIN_VALDA <= '$f_fin'";

                        $sql = "SELECT VAL_VALDA, ID_UNIDAD from valor_dato WHERE ID_DATO = $id_dato AND ID_DEPTO IS NOT NULL AND ID_MUN IS NULL ".$cond_fecha;
                        $valor = 0;
                        $id_unidad = 0;
                        $rs = $this->conn->OpenRecordset($sql);
                        while ($row_rs = $this->conn->FetchRow($rs)){
                            $id_unidad = $row_rs[1];
                            if ($row_rs[0] != null){
                                $valor += $row_rs[0];
                            }
                        }

                        $sql = "INSERT INTO total_nacional_valor_dato (ID_DATO,INI_VALDA,FIN_VALDA,TOTAL_NACIONAL,ID_UNIDAD) VALUES ($dato_vo->id,'$f_ini','$f_fin',$valor,$id_unidad)";
                        //echo $sql;
                        $this->conn->Execute($sql);

                        $this->setFlatTotalizar($dato_vo->id, 0, 'nal');

                    }
                }
            break;
        }
    }


    public function setFlatTotalizar($id, $f, $dg = '')
    {
        switch ($dg) {
                case 'nal':
                    $sql = "UPDATE dato_sector SET totalizar_nal = $f WHERE id_dato = $id";
                    $this->conn->Execute($sql);
                break;
                case 'deptal':
                    $sql = "UPDATE dato_sector SET totalizar_deptal = $f WHERE id_dato = $id";
                    $this->conn->Execute($sql);
                break;
                case '':
                    $sql = "UPDATE dato_sector SET totalizar_nal = $f WHERE id_dato = $id";
                    $this->conn->Execute($sql);

                    $sql = "UPDATE dato_sector SET totalizar_deptal = $f WHERE id_dato = $id";
                    $this->conn->Execute($sql);
                break;
            }
    }
}

/**
 * Ajax de Datos Sectoriales.
 *
 * Contiene los metodos para Ajax de la clase DatoSectorial
 *
 * @author Ruben A. Rojas C.
 */
class DatoSectorialAjax extends DatoSectorialDAO
{
    /**
     * Lista ComboBox de Datos Sectoriales.
     *
     * @param string $condicion
     */
    public function comboBoxDatoSectorial($condicion, $multiple)
    {

        //LIBRERIAS
        include_once 'lib/model/dato_sectorial.class.php';

        $num = $this->numRecords($condicion);

        echo '<table>';

        if ($num > 0) {
            echo "<tr><td width='90'><b>Dato</b></td>";
            if ($multiple == 0) {
                echo "<td><select id='id_dato' name='id_id_dato' class='select'>";
                echo "<option value=''>[ Seleccione ]</option>";
            } else {
                if ($num < $multiple) {
                    $multiple = $num;
                }
                echo "<select id='id_dato' name='id_dato[]' class='select' multiple size=$multiple>";
            }

            $this->ListarCombo('combo', '', $condicion);
            echo '</select></td></tr>';
        } else {
            echo '<tr><td><b>*** No hay Datos ***</b></td></tr>';
        }

        echo '</table>';
    }

    /**
     * Lista los años disponibles para un dato o grupo de datos, en una lista de checkbox.
     *
     * @param int $id_dato
     */
    public function getAniosDatoSectorial($id_dato, $reporte)
    {

        //LIBRERIAS
        include_once 'lib/model/dato_sectorial.class.php';

        $id_datos_tmp = explode(',', $id_dato);

        //SE SELECCIONA UN SOLO DATO
        if (in_array($reporte, array(0, 1, 3, 4))) {
            $a_s = $this->GetAnios($id_dato);

            $un_periodo = (count($a_s['ini']) == 0) ? 1 : 0;

            ?>
				<table cellpadding="2" border="0" class='table_filtro_gra_resumen'
				width="390">
				<tr>
				<td class="titulo_filtro">&nbsp;<img
				src="images/gra_resumen/fl_filtro.gif">&nbsp;Periodos Disponibles</td>
				</tr>
				<tr>
				<td><?php
                if (count($a_s) > 0) {
                    foreach ($a_s['ini'] as $f => $a) {
                        $n = $f + 1;
                        echo "<input type='checkbox' value=$a id='aaaa' name='aaaa' checked>&nbsp;$a&nbsp;";
                        if (fmod($n, 6) == 0) {
                            echo '<br>';
                        }
                    }
                } else {
                    echo '* No hay periodos disponibles para este dato';
                }
        } elseif ($reporte == 2) {
            ?>
				<table cellpadding="2" border="0" class='table_filtro_gra_resumen'
				width="390">
				<tr>
				<td class="titulo_filtro">&nbsp;<img
				src="images/gra_resumen/fl_filtro.gif">&nbsp;Periodos Disponibles</td>
				</tr>
				<tr>
				<td>(Para el grupo de datos seleccionados)</td>
				</tr>
				<tr>
				<td><?php

                foreach ($id_datos_tmp as $i => $id_dato) {
                    $tmp = $this->GetAnios($id_dato);

                    if ($i == 0) {
                        $a_s = $tmp['ini'];
                    } else {
                        $a_s = array_intersect($a_s, $tmp['ini']);
                    }
                }

            if (count($a_s) > 0) {
                foreach ($a_s as $f => $a) {
                    $n = $f + 1;
                    echo "<input type='radio' value=$a id='aaaa' name='aaaa' checked>&nbsp;$a&nbsp;";
                    if (fmod($n, 6) == 0) {
                        echo '<br>';
                    }
                }
            } else {
                echo '* No hay periodos disponibles para este dato';
            }
        }
    }

    /* Lista los a�os disponibles para un dato o grupo de datos, en una lista de checkbox para Mapas
     * @access public
     * @param int $id_dato
     */
    public function getAniosDatoSectorialToMapa($id_dato)
    {

        //LIBRERIAS
        include_once 'lib/model/dato_sectorial.class.php';
        include_once 'lib/model/contacto.class.php';
        include_once 'lib/dao/contacto.class.php';

        //INICIALIZACION DE VARIABLES
        $fuente_dao = new ContactoDAO();

        $periodos = $this->GetPeriodos($id_dato);

        if (count($periodos['ini']) > 0) {
            foreach ($periodos['ini'] as $f => $f_ini) {
                $n = $f + 1;
                $f_tmp = split('-', $f_ini);
                $a = $f_tmp[0];

                echo "<input type='checkbox' value=$a id='aaaa_dato' name='aaaa_dato'>&nbsp;$a&nbsp;&nbsp;";
                if (fmod($n, 6) == 0) {
                    echo '<br>';
                }
            }
        } else {
            echo '<tr><td>* No hay periodos disponibles para este dato</td></tr>';
        }
    }

    /* Muestra la definici�n de un Dato
     * @access public
     * @param int $id_dato
     */
    public function getDefinicionDatoSectorial($id_dato)
    {

        //LIBRERIAS
        include_once $_SERVER['DOCUMENT_ROOT'].'/sissh/admin/lib/model/dato_sectorial.class.php';

        $dato_vo = $this->Get($id_dato);

        return $dato_vo->definicion;
    }

    /**
     * Lista los a�os disponibles para un dato o grupo de datos, en una lista de checkbox.
     *
     * @param int    $id_dato
     * @param string $formato       H = horizontal, V = vertical
     * @param int    $num_items_f_c numero de periodos antes de pasar a la siguiente fila para H o la siguiente columna para V
     * @param string $checked,      checked = checked los checkbox, '' no checked
     * @param string $box_name,     nombre del input checkbox
     */
    public function getPeriodosDatoSectorial($id_dato, $formato, $num_items_f_c, $checked, $box_name)
    {

        //LIBRERIAS
        include_once 'lib/model/dato_sectorial.class.php';

        $meses = array('', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');

        $periodos = $this->GetPeriodos($id_dato);
        //echo $id_dato;
        echo '<table cellpadding=2 cellspacing=0>';

        if (count($periodos['ini']) > 0) {
            foreach ($periodos['ini'] as $f => $f_ini) {
                if (fmod($f, $num_items_f_c) == 0) {
                    if ($formato == 'V') {
                        echo '<td>';
                    } else {
                        echo '<tr><td>';
                    }
                }

                $f_tmp = split('-', $f_ini);
                $periodo_ini = ($f_tmp[2] * 1).' '.$meses[$f_tmp[1] * 1].' '.$f_tmp[0];

                $f_fin = $periodos['fin'][$f];
                $f_tmp = split('-', $f_fin);
                $periodo_fin = ($f_tmp[2] * 1).' '.$meses[$f_tmp[1] * 1].' '.$f_tmp[0];

                echo "<input type='checkbox' value='$f_ini|$f_fin' name='".$box_name."[]' $checked>&nbsp;$periodo_ini a $periodo_fin&nbsp;&nbsp;";

                if ($formato == 'V') {
                    echo '<br>';
                }
            }

            if ($formato == 'V') {
                echo '<td>';
            }

            echo '</tr>';
        } else {
            echo '<tr><td>* No hay periodos disponibles para este dato</td></tr>';
        }

        echo '</table>';
    }

    /**
     * Lista los Datos en una Tabla y Grafica los datos - GRAFICAS Y RESUMENES.
     *
     * @param int    $reporte                  Reporte
     * @param int    $id_dato                  ID del Dato Sectorial o de los datos separados por coma
     * @param int    $depto                    Desagregacion geografica 2 = Mpal 1 = Deptal 3 = Nacional
     * @param int    $ubicacion                Id de la Ubicacion
     * @param array  $id_periodos              Id de los periodos a reportar. Reporte 1
     * @param string $chart                    Tipo de grafica
     * @param string $dato_para_reporte_4_dato Municipios o Departamentos
     */
    public function GraficaResumenDatos($reporte, $id_dato, $depto, $ubicacion, $id_periodos, $chart, $dato_para_reporte_4_dato)
    {
        require_once $_SERVER['DOCUMENT_ROOT'].'/sissh/admin/lib/common/graphic.class.php';
        require_once $_SERVER['DOCUMENT_ROOT'].'/sissh/admin/lib/libs_dato_sectorial.php';

        //INICIALIZACION DE VARIABLES
        $depto_dao = new DeptoDAO();
        $municipio_dao = new MunicipioDAO();
        $dato_dao = new DatoSectorialDAO();
        $unidad_dao = new UnidadDatoSectorDAO();
        $fuente_dao = new ContactoDAO();
        $PG->x = array();

        //$PG = new PowerGraphic;

        $nom_ubi = 'Nacional';
        if ($depto == 1) {
            $ubi = $depto_dao->Get($ubicacion);
            $nom_ubi = $ubi->nombre;
        } elseif ($depto == 2) {
            $ubi = $municipio_dao->Get($ubicacion);
            $nom_ubi = $ubi->nombre;
        }

        echo '<br>';
        echo "<table align='center' cellspacing='1' cellpadding='3' width='100%' border='0'>";

        $html = "<tr><td align='left'><img src='images/consulta/excel.gif'>&nbsp;<a href='consulta/excel.php?f=dato_sectorial_sidih'>Exportar tabla a Hoja de c&aacute;lculo</a></td></tr>";
        $html .= "<tr><td>Localizaci&oacute;n Geogr&aacute;fica: <b>$nom_ubi</b></td></tr>";
        $html .= "<tr>
			<td valign='top'>
			<table border=0 class='tabla_grafica_conteo' cellpadding=4 cellspacing=1 width='250'>";

        $xls = '<table>';

        if (in_array($reporte, array(1, 3))) {
            $dato_vo = $dato_dao->Get($id_dato);

            $html .= "<tr class='titulo_tabla_conteo'><td><b>A&ntilde;o</b></td><td><b>Valor</b></td>";

            $xls .= "<tr><td>$dato_vo->nombre</td></tr>";
            $xls .= "<tr class='titulo_tabla_conteo'><td><b>A&ntilde;o</b></td><td><b>Valor</b></td>";

            $d = 0;
            foreach ($id_periodos as $a) {
                $html .= "<tr class='fila_tabla_conteo'><td>".$a.'</td>';
                $xls .= "<tr class='fila_tabla_conteo'><td>".$a.'</td>';

                $val = $dato_dao->GetValorToReport($id_dato, $ubicacion, "$a-1-1", "$a-12-31", $depto);
                $valor = $val['valor'];
                $id_unidad = $val['id_unidad'];

                if ($valor != 'N.D.') {
                    $PG->x[$d] = $a;
                    $PG->y[$d] = $valor;

                    //APLICA FORMATO
                    $valor = $dato_dao->formatValor($id_unidad, $valor, 0);
                    $unidad = $unidad_dao->Get($id_unidad);

                    if ($id_unidad == 4 || $id_unidad == 9) {
                        $html .= "<td>$valor %</td>";
                        $xls .= "<td>$valor %</td>";
                    } else {
                        $html .= "<td>$valor $unidad->nombre</td>";
                        $xls .= "<td>$valor $unidad->nombre</td>";
                    }

                    $nombre_unidad = $unidad->nombre;
                    ++$d;
                } else {
                    $html .= "<td>$valor</td>";
                    $xls .= "<td>$valor</td>";
                    $nombre_unidad = '';
                }

                $html .= '</tr>';
                $xls .= '</tr>';
            }

            // Valores API
            if (!isset($_GET['api']) || (isset($_GET['api']) && isset($_GET['tabla_api']) && $_GET['tabla_api'] == 1)) {
                echo $html;
            }

            if ($reporte == 1) {
                $title = $dato_vo->nombre;

                $PG->title = $title;
                $PG->axis_x = 'A�o';
                $PG->axis_y = $nombre_unidad;
                $PG->skin = 1;
                $PG->type = 1;
                $PG->credits = 0;
                $PG->decimals = 0;

                $dato = $this->Get($id_dato);
                $fuente_vo = $fuente_dao->Get($dato->id_contacto);
                $PG->texto_1 = " | Fuente: $fuente_vo->nombre ";

                echo '</table></td>';
                echo "<td align='center' valign='top'><table>";

                if (count($PG->x) > 1) {
                    if (!isset($_GET['api']) || (isset($_GET['api']) && isset($_GET['grafica_api']) && $_GET['grafica_api'] == 1)) {
                        /********************************************************************************
                        //PARA GRAFICA OPEN CHART
                        /*******************************************************************************/
                        $chk_chart = array('bar' => '', 'bar_3d' => '', 'line' => '');
                        $chk_chart[$chart] = ' selected ';
                        $font_size_key = 10;
                        $font_size_x_label = 8;
                        $font_size_y_label = 8;

                        echo "<tr>
							<td align='left'>";

                        //Si no viene de API lo muestra
                        if (!isset($_GET['api'])) {
                            echo "Tipo de Gr&aacute;fica:&nbsp;
							<select onchange=\"graficarDatos(this.value,0)\" class='select'>
								<option value='bar' ".$chk_chart['bar'].">Barras</option>
								<option value='bar_3d' ".$chk_chart['bar_3d'].">Barras 3D</option>
								<option value='line' ".$chk_chart['line'].'>Lineas</option>

								</select>&nbsp;&nbsp;::&nbsp;&nbsp;';
                        }

                        echo "Si desea guardar la imagen haga click sobre el icono <img src='images/save.png'>
							</td>
							</tr>
							<tr><td class='tabla_grafica_conteo' colspan=1 width='600' bgcolor='#F0F0F0' align='center'><br>";

                        //Eje x
                        $i = 0;
                        foreach ($PG->x as $x) {
                            if ($i == 0) {
                                $ejex = "'".utf8_encode($x)."'";
                            } else {
                                $ejex .= ",'".utf8_encode($x)."'";
                            }

                            ++$i;
                        }

                        //Estilos para bar y bar3D
                        $chart_style = array('bar' => array('alpha' => 90, 'color' => array('#0066ff', '#639F45', '')),
                                'bar_3d' => array('alpha' => 90, 'color' => array('#0066ff', '#639F45', '')),
                                'line' => array('alpha' => 90, 'color' => array('#0066ff', '#639F45', '')), );

                        //Variable de sesion que va a ser el nomnre dela grafica al guardar
                        $_SESSION['titulo_grafica'] = $title;

                        $path = $_SERVER['DOCUMENT_ROOT'].'/sissh/admin/admin/lib/common/open-flash-chart/';
                        $path_in = $_SERVER['DOCUMENT_ROOT'].'/sissh/admin/lib/common/open-flash-chart/';

                        include "$path_in/php-ofc-library/sidihChart.php";
                        $g = new sidihChart();

                        $content = "<?
							include_once('admin/lib/common/open-flash-chart/php-ofc-library/sidihChart.php' );

						\$g = new sidihChart();

						\$g->title('".utf8_encode($title)."','font-size:12px;margin-top:15px;padding:10px');

						// label each point with its value
						\$g->set_x_labels( array(".$ejex.") );
						\$g->set_x_label_style( $font_size_x_label, '#000000');\n";

                        if ($chart == 'bar_3d') {
                            $content .= '$g->set_x_axis_3d(6);';
                            $content .= "\$g->x_axis_colour('#dddddd','#FFFFFF');\n";
                        }

                        //Eje y
                        $ejey = implode(',', $PG->y);
                        $max_y = max($PG->y);

                        if ($chart == 'bar' || $chart == 'bar_3d') {
                            $content .= "\$bar = new $chart(".$chart_style[$chart]['alpha'].", '".$chart_style[$chart]['color'][0]."' );\n";
                            $content .= '$bar->data = array('.$ejey.");\n";

                            if ($chart == 'bar_3d') {
                                $content .= "\$g->set_x_axis_3d(6);\n";
                                $content .= "\$g->x_axis_colour('#dddddd','#FFFFFF');\n";
                            }

                            $content .= '$g->data_sets[] = $bar;';
                        } elseif ($chart == 'line') {
                            $content .= "\$g->set_data(array($ejey));\n";
                            $content .= '$g->'.$chart."_dot(1,3,'".$chart_style[$chart]['color'][0]."','',10);\n";
                        }

                        $max_y = $g->maxY($max_y);

                        $content .= "
							\$g->set_tool_tip( '#x_label# <br> #val# ".utf8_encode($unidad->nombre)."' );
						\$g->set_y_max( ".$max_y." );
						\$g->y_label_steps(5);
						//Espacio para el footer de la imagen con el logo - Toco con x_legend vacio, jejeje
						\$g->set_x_legend('".utf8_encode('A�os')."\n\n\nFuente: ".$fuente_vo->nombre."',12);

						\$g->set_y_legend('".utf8_encode($unidad->nombre)."',12);

						\$g->set_num_decimals(0);

						// display the data
						echo \$g->render();
						?>";

                        //MODIFICA EL ARCHIVO DE DATOS
                        $archivo = new Archivo();
                        $fp = $archivo->Abrir($_SERVER['DOCUMENT_ROOT'].'/sissh/chart-data.php', 'w+');

                        $archivo->Escribir($fp, $content);
                        $archivo->Cerrar($fp);

                        //IE Fix
                        //Variable para que IE cargue el nuevo archivo de datos y cambie el tipo de grafica con los nuevos valores
                        $nocache = time();
                        include_once $path_in.'php-ofc-library/open_flash_chart_object.php';
                        open_flash_chart_object(500, 350, 'chart-data.php?nocache='.$nocache, false);
                        ?> <!--<td valign="top">
							<table id="table_grafica" cellspacing='0' cellpadding='5'>
							<tr>
							<td><img src='admin/lib/common/graphic.class.php?<?php //echo $PG->create_query_string()?>' border=1 /></td>
							</tr>
							</table>
							</td>--></td>
							</tr>
							<?php

                    }
                }
            }
        }
        //10 MPIOS O DEPTOS
        elseif (in_array($reporte, array(4))) {
            $num_repo_4 = 10;
            $id_unidad = $this->GetUnidad($id_dato);
            $dato_vo = $dato_dao->Get($id_dato);

            if ($dato_para_reporte_4_dato == 'mpio') {
                $tit_col = 'Municipio';

                $cond = ($depto == 1) ? "id_depto=$ubicacion" : '';
                $ubis = $municipio_dao->GetAllArray($cond, '');
                $depto_in = 2;
            } else {
                $tit_col = 'Departamento';
                $ubis = $depto_dao->GetAllArray('');
                $depto_in = 1;
            }

            $html .= "<tr class='titulo_tabla_conteo'><td><b>Cod</b><td><b>$tit_col</b></td><td><b>Valor</b></td></tr>";

            $xls .= "<tr><td>$dato_vo->nombre</td></tr>";
            $xls .= "<tr class='titulo_tabla_conteo'><td><b>Cod</b><td><b>$tit_col</b></td><td><b>Valor</b></td></tr>";

            foreach ($ubis as $ubi) {
                foreach ($id_periodos as $i => $a) {
                    if ($i == 0) {
                        $valor[$ubi->id] = 0;
                    }

                    $valo = $dato_dao->GetValorToReport($id_dato, $ubi->id, "$a-1-1", "$a-12-31", $depto_in);
                    $val = $valo['valor'];

                    if ($val != 'N.D.') {
                        $valor[$ubi->id] += $val;
                        $unidad_valor[$ubi->id] = $id_unidad;
                    }
                }
            }

            //Ordena el arreglo
            arsort($valor);
            $valor = array_slice($valor, 0, $num_repo_4, 1);  //Mantiene las claves desde php 5.0.2 con el parametro 4=1

            foreach ($valor as $id_ubi => $val) {
                if ($dato_para_reporte_4_dato == 'mpio') {
                    $ubi = $municipio_dao->Get($id_ubi);
                } else {
                    $ubi = $depto_dao->Get($id_ubi);
                }

                $PG->x[] = $ubi->nombre;
                $PG->y[] = $val;

                $unidad = $unidad_dao->Get($id_unidad);

                //APLICA FORMATO
                $val = $dato_dao->formatValor($id_unidad, $val, 0);

                $html .= "<tr class='fila_tabla_conteo'><td>".$ubi->id.'</td><td>'.$ubi->nombre.'</td>';
                $xls .= "<tr class='fila_tabla_conteo'><td>".$ubi->id.'</td><td>'.$ubi->nombre.'</td>';

                if ($id_unidad == 4 || $id_unidad == 9) {
                    $html .= "<td>$val %</td>";
                    $xls .= "<td>$val %</td>";
                } else {
                    $html .= "<td>$val $unidad->nombre</td>";
                    $xls .= "<td>$val $unidad->nombre</td>";
                }

                $html .= '</tr>';
                $xls .= '</tr>';
            }

            // Valores API
            if (!isset($_GET['api']) || (isset($_GET['api']) && isset($_GET['tabla_api']) && $_GET['tabla_api'] == 1)) {
                echo $html;
            }

            $title = $dato_vo->nombre;

            $PG->title = $title;
            $PG->axis_x = $tit_col;
            $PG->axis_y = $unidad->nombre;
            $PG->skin = 1;
            $PG->type = 1;
            $PG->credits = 0;
            $PG->decimals = 0;

            $dato = $this->Get($id_dato);
            $fuente_vo = $fuente_dao->Get($dato->id_contacto);
            $PG->texto_1 = " | Fuente: $fuente_vo->nombre ";

            echo '</table></td>';
            echo "<td align='center' valign='top'><table>";

            if (count($PG->x) > 1) {
                if (!isset($_GET['api']) || (isset($_GET['api']) && isset($_GET['grafica_api']) && $_GET['grafica_api'] == 1)) {

                    /********************************************************************************
                    //PARA GRAFICA OPEN CHART
                    /*******************************************************************************/
                    $chk_chart = array('bar' => '', 'bar_3d' => '', 'line' => '');
                    $chk_chart[$chart] = ' selected ';
                    $font_size_key = 10;
                    $font_size_x_label = 8;
                    $font_size_y_label = 8;

                    echo "<tr>
						<td align='left'>";

                    //Si no viene de API lo muestra
                    if (!isset($_GET['api'])) {
                        echo "Tipo de Gr&aacute;fica:&nbsp;
						<select onchange=\"graficarDatos(this.value,0)\" class='select'>
							<option value='bar' ".$chk_chart['bar'].">Barras</option>
							<option value='bar_3d' ".$chk_chart['bar_3d'].">Barras 3D</option>
							<option value='line' ".$chk_chart['line'].'>Lineas</option>

							</select>&nbsp;&nbsp;::&nbsp;&nbsp;';
                    }

                    echo "Si desea guardar la imagen haga click sobre el icono <img src='images/save.png'>
						</td>
						</tr>
						<tr><td class='tabla_grafica_conteo' colspan=1 width='600' bgcolor='#F0F0F0' align='center'><br>";

                    //Eje x
                    $i = 0;
                    foreach ($PG->x as $x) {
                        if ($i == 0) {
                            $ejex = "'".utf8_encode($x)."'";
                        } else {
                            $ejex .= ",'".utf8_encode($x)."'";
                        }

                        ++$i;
                    }

                    //Estilos para bar y bar3D
                    $chart_style = array('bar' => array('alpha' => 90, 'color' => array('#0066ff', '#639F45', '')),
                            'bar_3d' => array('alpha' => 90, 'color' => array('#0066ff', '#639F45', '')),
                            'line' => array('alpha' => 90, 'color' => array('#0066ff', '#639F45', '')), );

                    //Variable de sesion que va a ser el nomnre dela grafica al guardar
                    $_SESSION['titulo_grafica'] = $title;

                    $path = $_SERVER['DOCUMENT_ROOT'].'/sissh/admin/admin/lib/common/open-flash-chart/';
                    $path_in = $_SERVER['DOCUMENT_ROOT'].'/sissh/admin/lib/common/open-flash-chart/';

                    include "$path_in/php-ofc-library/sidihChart.php";
                    $g = new sidihChart();

                    $content = "<?
						include_once('admin/lib/common/open-flash-chart/php-ofc-library/sidihChart.php' );

					\$g = new sidihChart();

					\$g->title('".utf8_encode($title)."','font-size:12px;margin-top:15px;padding:10px');

					// label each point with its value
					\$g->set_x_labels( array(".$ejex.") );
					\$g->set_x_label_style( $font_size_x_label, '#000000',2);\n";

                    if ($chart == 'bar_3d') {
                        $content .= '$g->set_x_axis_3d(6);';
                        $content .= "\$g->x_axis_colour('#dddddd','#FFFFFF');\n";
                    }

                    //Eje y
                    $ejey = implode(',', $PG->y);
                    $max_y = max($PG->y);

                    if ($chart == 'bar' || $chart == 'bar_3d') {
                        $content .= "\$bar = new $chart(".$chart_style[$chart]['alpha'].", '".$chart_style[$chart]['color'][0]."' );\n";
                        $content .= '$bar->data = array('.$ejey.");\n";

                        if ($chart == 'bar_3d') {
                            $content .= "\$g->set_x_axis_3d(6);\n";
                            $content .= "\$g->x_axis_colour('#dddddd','#FFFFFF');\n";
                        }

                        $content .= '$g->data_sets[] = $bar;';
                    } elseif ($chart == 'line') {
                        $content .= "\$g->set_data(array($ejey));\n";
                        $content .= '$g->'.$chart."_dot(1,3,'".$chart_style[$chart]['color'][0]."','',10);\n";
                    }

                    $max_y = $g->maxY($max_y);

                    $content .= "
						\$g->set_tool_tip( '#x_label# <br> #val# ".utf8_encode($unidad->nombre)."' );
					\$g->set_y_max( ".$max_y." );
					\$g->y_label_steps(5);
					//Espacio para el footer de la imagen con el logo - Toco con x_legend vacio, jejeje
					\$g->set_x_legend('".utf8_encode('A�os')."\n\n\nFuente: ".$fuente_vo->nombre."',12);

					\$g->set_y_legend('".utf8_encode($unidad->nombre)."',12);

					\$g->set_num_decimals(0);

					// display the data
					echo \$g->render();
					?>";

                    //MODIFICA EL ARCHIVO DE DATOS
                    $archivo = new Archivo();
                    $fp = $archivo->Abrir($_SERVER['DOCUMENT_ROOT'].'/sissh/chart-data.php', 'w+');

                    $archivo->Escribir($fp, $content);
                    $archivo->Cerrar($fp);

                    //IE Fix
                    //Variable para que IE cargue el nuevo archivo de datos y cambie el tipo de grafica con los nuevos valores
                    $nocache = time();
                    include_once $path_in.'php-ofc-library/open_flash_chart_object.php';
                    open_flash_chart_object(500, 350, 'chart-data.php?nocache='.$nocache, false);
                    ?>
						<!--<td valign="top">
						<table id="table_grafica" cellspacing='0' cellpadding='5'>
						<tr>
						<td><img src='admin/lib/common/graphic.class.php?<?php //echo $PG->create_query_string()?>' border=1 /></td>
						</tr>
						</table>
						</td>-->
						</td>
						</tr>
						<?php

                }
            }
        } elseif ($reporte == 2) {
            $id_dato_tmp = split(',', $id_dato);

            if (count($id_periodos) == 1) {
                $a_ini = $a_fin = $id_periodos[0];
            } else {
                $a_ini = $id_periodos[0];
                $a_fin = $id_periodos[1];
            }

            $html .= "<tr class='titulo_tabla_conteo'><td><b>Dato</b></td><td><b>Valor</b></td>";
            $xls .= "<tr class='titulo_tabla_conteo'><td><b>Dato</b></td><td><b>Valor</b></td>";

            $d = 0;
            foreach ($id_dato_tmp as $id_dato) {
                $dato_vo = $dato_dao->Get($id_dato);
                $fuente_vo = $fuente_dao->Get($dato_vo->id_contacto);

                $html .= "<tr class='fila_tabla_conteo'><td>".$dato_vo->nombre.'</td>';
                $xls .= "<tr class='fila_tabla_conteo'><td>".$dato_vo->nombre.'</td>';

                $val = $dato_dao->GetValorToReport($id_dato, $ubicacion, "$a_ini-1-1", "$a_fin-12-31", $depto);
                $valor = $val['valor'];
                $id_unidad = $val['id_unidad'];

                if ($valor != 'N.D.') {
                    $PG->x[$d] = $dato_vo->nombre;
                    $PG->y[$d] = $valor;
                    ++$d;
                }

                //APLICA FORMATO
                $valor = $dato_dao->formatValor($id_unidad, $valor);

                if (isset($id_unidad)) {
                    $unidad = $unidad_dao->Get($id_unidad);
                    $html .= "<td>$valor $unidad->nombre</td>";
                    $xls .= "<td>$valor $unidad->nombre</td>";
                } else {
                    $html .= "<td>$valor</td>";
                    $xls .= "<td>$valor</td>";
                }

                $html .= '</tr>';
                $xls .= '</tr>';

                ++$d;
            }

            // Valores API
            if (!isset($_GET['api']) || (isset($_GET['api']) && isset($_GET['tabla_api']) && $_GET['tabla_api'] == 1)) {
                echo $html;
            }

            echo '</table></td>';

            $PG->title = $dato_vo->nombre;
            $PG->axis_x = 'Dato Sectorial';
            //$PG->axis_y    = $unidad->nombre;
            $PG->skin = 1;
            $PG->type = 5;
            $PG->credits = 0;
            $PG->decimals = 0;
            //print_r($num_minas);

            echo "<td align='center' valign='top'><table>";

            if (count($PG->x) > 1) {
                if (!isset($_GET['api']) || (isset($_GET['api']) && isset($_GET['grafica_api']) && $_GET['grafica_api'] == 1)) {

                    /********************************************************************************
                    //PARA GRAFICA OPEN CHART
                    /*******************************************************************************/
                    $chk_chart = array('bar' => '', 'bar_3d' => '', 'line' => '');
                    $chk_chart[$chart] = ' selected ';
                    $font_size_key = 10;
                    $font_size_x_label = 8;
                    $font_size_y_label = 8;
                    $title = '';

                    echo "<tr>
						<td align='left'>Si desea guardar la imagen haga click sobre el icono <img src='images/save.png'></td>
						</tr>
						<tr><td class='tabla_grafica_conteo' colspan=1 width='600' bgcolor='#F0F0F0' align='center'><br>";

                    //Eje x
                    $i = 0;
                    foreach ($PG->x as $x) {
                        if ($i == 0) {
                            $ejex = "'".utf8_encode($x)."'";
                        } else {
                            $ejex .= ",'".utf8_encode($x)."'";
                        }

                        ++$i;
                    }

                    //Variable de sesion que va a ser el nombre de la grafica al dar click en guardar
                    $_SESSION['titulo_grafica'] = $title;

                    $path = $_SERVER['DOCUMENT_ROOT'].'/sissh/admin/admin/lib/common/open-flash-chart/';
                    $path_in = $_SERVER['DOCUMENT_ROOT'].'/sissh/admin/lib/common/open-flash-chart/';

                    include "$path_in/php-ofc-library/sidihChart.php";
                    $g = new sidihChart();

                    $content = "<?
						include_once('admin/lib/common/open-flash-chart/php-ofc-library/sidihChart.php' );

					\$g = new sidihChart();

					\$g->title('".utf8_encode('Fuente: '.$fuente_vo->nombre)."');

					// label each point with its value
					\$g->set_x_labels( array(".$ejex.") );
					\$g->set_x_label_style( $font_size_x_label, '#000000');\n";

                    if ($chart == 'bar_3d') {
                        $content .= '$g->set_x_axis_3d(6);';
                        $content .= "\$g->x_axis_colour('#dddddd','#FFFFFF');\n";
                    }

                    //Eje y
                    $ejey = implode(',', $PG->y);
                    $max_y = max($PG->y);

                    $content .= "\$g->pie(100,'#CCCCCC','{font-size: 10px; color: #000000;');\n
						\$g->pie_values( array($ejey), array($ejex) );
					\$g->pie_slice_colours( array('#0066ff','#99CC00','#ffcc00') );";

                    $content .= "
						\$g->set_tool_tip( '#x_label# <br> #val# ".utf8_encode($unidad->nombre)."' );

					//Espacio para el footer de la imagen con el logo - Toco con x_legend vacio, jejeje
					\$g->set_x_legend('\n\n\nFuente: ".$fuente_vo->nombre."',12);

					\$g->set_num_decimals(0);

					// display the data
					echo \$g->render();
					?>";

                    //MODIFICA EL ARCHIVO DE DATOS
                    $archivo = new Archivo();
                    $fp = $archivo->Abrir($_SERVER['DOCUMENT_ROOT'].'/sissh/chart-data.php', 'w+');

                    $archivo->Escribir($fp, $content);
                    $archivo->Cerrar($fp);

                    //IE Fix
                    //Variable para que IE cargue el nuevo archivo de datos y cambie el tipo de grafica con los nuevos valores
                    $nocache = time();
                    include_once $path_in.'php-ofc-library/open_flash_chart_object.php';
                    open_flash_chart_object(500, 350, 'chart-data.php?nocache='.$nocache, false);
                    ?>
						<!--<td valign="top">
						<table id="table_grafica" cellspacing='0' cellpadding='5'>
						<tr>
						<td><img src='admin/lib/common/graphic.class.php?<?php //echo $PG->create_query_string()?>' border=1 /></td>
						</tr>
						</table>
						</td>-->
						</td>
						</tr>
						<?php

                }
            }
        }

        $gen_reporte = 0;
        if (in_array($reporte, array(1, 3))) {
            //Opcion nacional
                if ($depto == 3) {
                    if ($dato_vo->desagreg_geo == 'departamental' || $dato_vo->desagreg_geo == 'municipal') {
                        $gen_reporte = 1;
                    }
                } elseif ($depto == 1) {
                    if ($dato_vo->desagreg_geo == 'municipal') {
                        $gen_reporte = 1;
                    }
                }
        }

        $_SESSION['xls'] = $xls;

            //Si no viene de API lo muestra
            if (!isset($_GET['api']) && $gen_reporte == 1) {
                ?>
				<tr>
					<td align='left'>
						<table border='0'>
							<tr>
								<td valign="bottom">
									<input type='button' name='button' value='Generar Reporte'
									onclick="graficarDatos('',1);" class='boton'>
								</td>
								<td valign="top"><br />
									<?php
                                    //Opcion nacional
                                    if ($depto == 3) {
                                        if ($dato_vo->desagreg_geo == 'departamental' || $dato_vo->desagreg_geo == 'municipal') {
                                            echo "<input type='radio' name='tipo_nal' value='deptos' checked>&nbsp;Listar todos los Departamentos&nbsp;&nbsp;&nbsp;";
                                        }

                                        if ($dato_vo->desagreg_geo == 'municipal') {
                                            echo "<input type='radio' name='tipo_nal' value='mpios' checked>&nbsp;Listar todos los Municipios&nbsp;<br>";
                                        }
                                    }
                ?> <br />&#187;&nbsp;<b>Separador decimal que usa su hoja de
									c&aacute;lculo</b>&nbsp; <select id='sep_decimal' class='select'>
									<option value=','>Coma (,)</option>
									<option value=';'>Punto (.)</option>
									</select>
								</td>
							</tr>
						</table>
					</td>
				</tr>
					<?php
            }
        ?>
					</table>
					</td>
					</tr>
					<tr>
					<td id='reporteGraResumenDesplazamiento' colspan='3'></td>
					</tr>
					<?php

    }

        /**
         * Genera el reporte de Datos Sectoriales apartir de una grafica - GRAFICAS Y RESUMENES.
         *
         * @param int    $reporte     Reporte
         * @param int    $id_dato     ID del Dato Sectorial o de los datos separados por coma
         * @param int    $depto       Desagregacion geografica 2 = Mpal 1 = Deptal 3 = Nacional
         * @param int    $ubicacion   Id de la Ubicacion
         * @param array  $id_periodos Id de los periodos a reportar. Reporte 1
         * @param string $tipo_nal    Tipo de reporte nacional, mios o deptos
         * @param string $sep_decimal Separador decimal para exporta a hoja de c�luclo (, o .)
         */
        public function reporteGraResumenDatos($reporte, $id_dato, $depto, $ubicacion, $id_periodos, $tipo_nal, $sep_decimal)
        {
            set_time_limit(0);

            require_once $_SERVER['DOCUMENT_ROOT'].'/sissh/admin/lib/libs_dato_sectorial.php';

            //INICIALIZACION DE VARIABLES
            $depto_dao = new DeptoDAO();
            $municipio_dao = new MunicipioDAO();
            $dato_dao = new DatoSectorialDAO();
            $unidad_dao = new UnidadDatoSectorDAO();
            $fuente_dao = new ContactoDAO();
            $show_total = 1;

            $nom_ubi = 'Nacional';
            if ($depto == 1) {
                $ubi = $depto_dao->Get($ubicacion);
                $nom_ubi = $ubi->nombre;
            } elseif ($depto == 2) {
                $ubi = $municipio_dao->Get($ubicacion);
                $nom_ubi = $ubi->nombre;
            }

            $html = "<br><div style='overflow:auto;width:940px;height:500px;border:1px solid #E1E1E1;'>";
            $html .= "<table align='center' class='tabla_reportelist_outer' border=0>";
            $html .= '<tr><td>&nbsp;</td></tr>';
            $html .= "<tr><td align='left'>Exportar a Hoja de C&aacute;lculo : ";
            $html .= "<a href=\"#\" onclick=\"location.href='consulta/excel.php?f=reporte_dato_sector_sidih';return false;\"\"><img src='images/consulta/excel.gif' border=0 title='Exportar a Excel'></a></td></tr>";
            $html .= "<tr><td colspan=3><table class='tabla_reportelist'>";

            $xls = '
				<STYLE TYPE="text/css"><!--
				.excel_celda_texto {mso-style-parent:text; mso-number-format:"\@";white-space:normal;}
			--></STYLE>';

            $xls .= '<table border=1>';

            $num_periodos = count($id_periodos);

            //Titulo
            if ($reporte == 1 || $reporte == 3) {
                $dato_vo = $dato_dao->Get($id_dato);
                //Consultar la unidad para el t�tulo
                $a = $id_periodos[0];

                $id_unidad = $dato_dao->GetUnidad($id_dato);
                $unidad_vo = $unidad_dao->Get($id_unidad);

                $titulo_html = "$dato_vo->nombre ($unidad_vo->nombre)";

                $html .= "<tr class='titulo_lista'><td></td><td></td><td align='center' colspan='$num_periodos'>$titulo_html</td></tr>";
                $xls .= "<tr class='titulo_lista'><td></td><td></td><td align='center' colspan='$num_periodos'>$titulo_html</td></tr>";
            } else {
            }

            $html .= "<tr class='titulo_lista'><td>CODIGO</td><td>UBICACION</td>";
            $xls .= '<tr><td>CODIGO</td><td>UBICACION</td>';

            if ($reporte == 1 || $reporte == 3) {
                foreach ($id_periodos as $a) {
                    $html .= "<td align='center'>$a</td>";
                    $xls .= "<td align='center'>$a</td>";
                }

                $html .= '</tr>';
                $xls .= '</tr>';

                //NACIONAL
                if ($depto == 3) {
                    if ($tipo_nal == 'deptos') {
                        $id_deptos = $depto_dao->GetAllArrayID("id_depto <> '00'");

                        foreach ($id_deptos as $p => $id_depto) {
                            if ($id_depto != '00') {
                                $depto_vo = $depto_dao->Get($id_depto);
                            } else {
                                $depto_vo->nombre = 'Nacional';
                            }

                            $style = '';
                            if (fmod($p + 1, 2) == 0) {
                                $style = 'fila_lista';
                            }

                            $html .= "<tr class='$style'><td>".$id_depto.'</td>';
                            $html .= '<td>'.$depto_vo->nombre.'</td>';

                            $xls .= "<tr><td class='excel_celda_texto'>".$id_depto.'</td>';
                            $xls .= '<td>'.$depto_vo->nombre.'</td>';

                            foreach ($id_periodos as $a) {
                                $val = $dato_dao->GetValorToReport($id_dato, $id_depto, "$a-1-1", "$a-12-31", 1);
                                $valor = $val['valor'];
                                $id_unidad = $val['id_unidad'];

                                if ($p == 0) {
                                    $valor_total[$a] = 0;
                                }

                                if ($valor != 'N.D.') {
                                    $valor_total[$a] += $valor;
                                }

                                //APLICA FORMATO
                                $valor_format = $dato_dao->formatValor($id_unidad, $valor, 0);
                                $valor_xls = ($valor == 'N.D.') ? '' : $dato_dao->formatValor($id_unidad, $valor, 0, $sep_decimal);

                                $html .= "<td align='right'>".$valor_format.'</td>';
                                $xls .= "<td align='right'>".$valor_xls.'</td>';
                            }

                            $html .= '</tr>';
                            $xls .= '</tr>';
                        }

                        //Total
                        if ($show_total == 1) {
                            $html .= "<tr class='titulo_lista'><td></td><td>Total</td>";

                            foreach ($id_periodos as $a) {
                                //APLICA FORMATO
                                $valor_format = $dato_dao->formatValor($id_unidad, $valor_total[$a], 0);

                                $html .= "<td align='right'>".$valor_format.'</td>';
                            }

                            $html .= '</tr>';
                        }
                    } else {

                        //Agrega el 00000. que si esta en la tabla registro
                        $muns = $municipio_dao->GetAllArrayID('', 'id_mun');
                        //array_push($muns,'00000');

                        foreach ($muns as $p => $id_mun) {
                            $mun_vo = $municipio_dao->Get($id_mun);

                            if ($id_mun != '00000') {
                                $mun_vo = $municipio_dao->Get($id_mun);
                            } else {
                                $mun_vo->nombre = 'Nacional';
                            }

                            $style = '';
                            if (fmod($p + 1, 2) == 0) {
                                $style = 'fila_lista';
                            }

                            $html .= "<tr class='$style'><td>".$id_mun.'</td>';
                            $html .= '<td>'.$mun_vo->nombre.'</td>';

                            $xls .= "<tr><td class='excel_celda_texto'>".$id_mun.'</td>';
                            $xls .= '<td>'.$mun_vo->nombre.'</td>';

                            foreach ($id_periodos as $a) {
                                $val = $dato_dao->GetValorToReport($id_dato, $id_mun, "$a-1-1", "$a-12-31", 2);
                                $valor = $val['valor'];
                                $id_unidad = $val['id_unidad'];

                                if ($p == 0) {
                                    $valor_total[$a] = 0;
                                }
                                if ($valor != 'N.D.') {
                                    $valor_total[$a] += $valor;
                                }

                                //APLICA FORMATO
                                $valor_format = $dato_dao->formatValor($id_unidad, $valor, 0);
                                $valor_xls = ($valor == 'N.D.') ? '' : $dato_dao->formatValor($id_unidad, $valor, 0, $sep_decimal);

                                $html .= "<td align='right'>".$valor_format.'</td>';
                                $xls .= "<td align='right'>".$valor_xls.'</td>';
                            }
                            $html .= '</tr>';
                            $xls .= '</tr>';
                        }

                        //Total
                        if ($show_total == 1) {
                            $html .= "<tr class='titulo_lista'><td></td><td>Total</td>";

                            foreach ($id_periodos as $a) {
                                //APLICA FORMATO
                                $valor_format = $dato_dao->formatValor($id_unidad, $valor_total[$a], 0);

                                $html .= "<td align='right'>".$valor_format.'</td>';
                            }

                            $html .= '</tr>';
                        }
                    }
                }
                //DEPTO
                if ($depto == 1) {
                    $id_muns = $municipio_dao->GetAllArrayID('ID_DEPTO='.$ubicacion, '');
                    foreach ($id_muns as $p => $id_mun) {
                        $mun_vo = $municipio_dao->Get($id_mun);

                        $style = '';
                        if (fmod($p + 1, 2) == 0) {
                            $style = 'fila_lista';
                        }

                        $html .= "<tr class='$style'><td>".$mun_vo->id.'</td>';
                        $html .= '<td>'.$mun_vo->nombre.'</td>';

                        $xls .= "<tr><td class='excel_celda_texto'>".$mun_vo->id.'</td>';
                        $xls .= '<td>'.$mun_vo->nombre.'</td>';

                        foreach ($id_periodos as $a) {
                            $val = $dato_dao->GetValorToReport($id_dato, $id_mun, "$a-1-1", "$a-12-31", 2);
                            $valor = $val['valor'];
                            $id_unidad = $val['id_unidad'];

                            if ($p == 0) {
                                $valor_total[$a] = 0;
                            }
                            if ($valor != 'N.D.') {
                                $valor_total[$a] += $valor;
                            }

                            //APLICA FORMATO
                            $valor_format = $dato_dao->formatValor($id_unidad, $valor, 0);
                            $valor_xls = ($valor == 'N.D.') ? '' : $dato_dao->formatValor($id_unidad, $valor, 0, $sep_decimal);

                            $html .= "<td align='right'>".$valor_format.'</td>';
                            $xls .= "<td align='right'>".$valor_xls.'</td>';
                        }

                        $html .= '</tr>';
                        $xls .= '</tr>';
                    }

                    //Total
                    if ($show_total == 1) {
                        $html .= "<tr class='titulo_lista'><td></td><td>Total</td>";

                        foreach ($id_periodos as $a) {
                            //APLICA FORMATO
                            $valor_format = $dato_dao->formatValor($id_unidad, $valor_total[$a], 0);

                            $html .= "<td align='right'>".$valor_format.'</td>';
                        }

                        $html .= '</tr>';
                    }
                }
                //MPIO
                if ($depto == 0) {
                    $id_mun = $ubicacion;
                    $mun_vo = $municipio_dao->Get($id_mun);

                    $html .= '<tr><td>'.$mun_vo->id.'</td>';
                    $html .= '<td>'.$mun_vo->nombre.'</td>';

                    $xls .= "<tr><td class='excel_celda_texto'>".$mun_vo->id.'</td>';
                    $xls .= '<td>'.$mun_vo->nombre.'</td>';

                    foreach ($id_periodos as $a) {
                        $val = $dato_dao->GetValorToReport($id_dato, $id_mun, "$a-1-1", "$a-12-31", 2);
                        $valor = $val['valor'];
                        $id_unidad = $val['id_unidad'];

                        if ($p == 0) {
                            $valor_total[$a] = 0;
                        }
                        $valor_total[$a] += $valor;

                        //APLICA FORMATO
                        $valor_format = $dato_dao->formatValor($id_unidad, $valor, 0);
                        $valor_xls = ($valor == 'N.D.') ? 0 : $dato_dao->formatValor($id_unidad, $valor, 0, $sep_decimal);

                        $html .= "<td align='right'>".$valor_format.'</td>';
                        $xls .= "<td align='right'>".$valor_xls.'</td>';
                    }

                    $html .= '</tr>';
                    $xls .= '</tr>';
                }

                $html .= '</table>';
                $xls .= '</table>';
            } elseif ($reporte == 2) {
                foreach ($id_periodos as $a) {
                    $html .= "<td colspan='2' align='center'>$a</td>";
                    $xls .= "<td colspan='2' align='center'>$a</td>";
                }

                $html .= '</tr>';
                $xls .= '</tr>';

                $html .= "<tr class='titulo_lista'><td>CODIGO</td><td>UBICACION</td>";
                $xls .= "<tr class='titulo_lista'><td>CODIGO</td><td>UBICACION</td>";
            }

            echo $html;
            $_SESSION['xls'] = $xls;
        }
}

?>
