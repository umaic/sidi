<?
/**
 * DAO de Municipio
 *
 * Contiene los métodos de la clase Municipio
 * @author Ruben A. Rojas C.
 */

Class MunicipioDAO {

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
    function MunicipioDAO (){
        //$this->conn = MysqlDb::getInstance();
        $this->conn = MysqlDb::getInstance();
        $this->tabla = "municipio";
        $this->columna_id = "ID_MUN";
        $this->columna_nombre = "NOM_MUN";
        $this->columna_order = "NOM_MUN";
        $this->num_reg_pag = 50;
        $this->url = "index.php?accion=listar&class=MunicipioDAO&method=ListarTabla&param=";
    }

    /**
     * Consulta los datos de una Municipio
     * @access public
     * @param int $id ID del Municipio
     * @return VO
     */
    function Get($id){
        $sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = '".$id."'";
        $rs = $this->conn->OpenRecordset($sql);
        $row_rs = $this->conn->FetchObject($rs);

        //Crea un VO
        $vo = New Municipio();

        if ($this->conn->RowCount($rs) > 0){
            //Carga el VO
            $vo = $this->GetFromResult($vo,$row_rs);
        }

        //Retorna el VO
        return $vo;
    }

    /**
     * Consulta el nombre de un municipio
     * @access public
     * @param int $id ID del Municipio
     * @return string
     */
    function GetName($id){
        $sql = "SELECT ".$this->columna_nombre." FROM ".$this->tabla." WHERE ".$this->columna_id." = '$id'";
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
        $sql .= ($order_by != "") ?  " ORDER BY $order_by" : " ORDER BY ".$this->columna_order;file_put_contents('/tmp/cont.txt', '>>>' . $sql . "\n\r<<<", FILE_APPEND);

        //LIMIT
        if ($limit != "") $sql .= " LIMIT ".$limit;


        $array = Array();
        $rs = $this->conn->OpenRecordset($sql);
        while ($row_rs = $this->conn->FetchObject($rs)){
            //Crea un VO
            $vo = New Municipio();
            //Carga el VO
            $vo = $this->GetFromResult($vo,$row_rs);
            //Carga el arreglo
            $array[] = $vo;
        }  
        //Retorna el Arreglo de VO
        return $array;
    }


    /**
     * Consulta los ID de los Municipio que cumplen una condición
     * @access public
     * @param string $condicion Condición que deben cumplir los Municipio y que se agrega en el SQL statement.
     * @param string $order_by Columna de Ordenamiento.
     * @return array Arreglo con los ID´s
     */
    function GetAllArrayID($condicion,$order_by){

        $sql = "SELECT ".$this->columna_id." FROM ".$this->tabla."";

        if ($condicion != "") $sql .= " WHERE ".$condicion;

        $sql .= ($order_by != "") ?  " ORDER BY $order_by" : " ORDER BY ".$this->columna_order;

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
     * Consulta los ID del Depto y retorna un string separado por coma para uso en un WHERE
     * @access public
     * @param string $id_depto ID del Departamento
     * @return string Arreglo con los ID´s
     */
    function GetIDWhere($id_depto){

        $id_muns = "''";

        $muns = $this->GetAllArrayID("ID_DEPTO IN ($id_depto)",'');
        $m_m = 0;
        foreach ($muns as $id){
            $m_m == 0 ? $id_muns = "'".$id."'" : $id_muns .= ",'".$id."'";
            $m_m++;
        }

        return $id_muns;

    }

    /**
     * Lista los Municipio que cumplen la condición en el formato dado
     * @access public
     * @param string $formato Formato en el que se listarán los Municipio, puede ser Tabla o ComboSelect
     * @param int $valor_combo ID del Municipio que será selccionado cuando el formato es ComboSelect
     * @param string $condicion Condición que deben cumplir los Municipio y que se agrega en el SQL statement.
     */
    function ListarCombo($formato,$valor_combo,$condicion){
        $arr = $this->GetAllArray($condicion);
        $num_arr = count($arr);

        for($a=0;$a<$num_arr;$a++){
            $this->Imprimir($arr[$a],$formato,$valor_combo);
        }
    }

    /**
     * Lista los Municipio en una Tabla
     * @access public
     */
    function ListarTabla($condicion){

        include_once ("lib/common/layout.class.php");

        $layout = new Layout();

        $layout->adminGrid(array('nombre' => array('titulo' => 'Nombre'), 'manzanas' => array('titulo' => 'Manzanas'), 'nacimiento' => array('titulo' => 'A&ntilde;o de creaci&oacute;n')),
                array('id_depto' => array('dao' => 'DeptoDao', 'nom' => 'nombre', 'titulo' => 'Departamento', 'filtro' => true)),
                array('checkForeignKeys' => false)
                );

    }

    /**
     * Imprime en pantalla los datos del Municipio
     * @access public
     * @param object $vo Municipio que se va a imprimir
     * @param string $formato Formato en el que se listarán los Municipio, puede ser Tabla o ComboSelect
     * @param int $valor_combo ID del Municipio que será selccionado cuando el formato es ComboSelect
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
     * Carga un VO de Municipio con los datos de la consulta
     * @access public
     * @param object $vo VO de Municipio que se va a recibir los datos
     * @param object $Resultset Resource de la consulta
     * @return object $vo VO de Municipio con los datos
     */
    function GetFromResult ($vo,$Result){

        $vo->id = $Result->{$this->columna_id};
        $vo->nombre = $Result->{$this->columna_nombre};
        $vo->id_depto = $Result->ID_DEPTO;
        $vo->manzanas = $Result->MAN_MUN;
        $vo->acto_admin = $Result->ACTO_MUN;
        $vo->nacimiento = $Result->NACI_MUN;
        $vo->longitude = $Result->LONGITUDE;
        $vo->latitude = $Result->LATITUDE;

        return $vo;
    }

    /**
     * Inserta un Mpio. en la B.D.
     * @access public
     * @param object $vo VO de Mpio que se va a insertar
     */
    function Insertar($vo){
        //CONSULTA SI YA EXISTE
        $cat_a = $this->GetAllArray($this->columna_nombre." = '".$vo->nombre."'");
        if (count($cat_a) == 0){
            $sql =  "INSERT INTO ".$this->tabla." (".$this->columna_id.",".$this->columna_nombre.",ID_DEPTO,MAN_MUN,NACI_MUN,ACTO_MUN) VALUES ('".$vo->id."','".$vo->nombre."','".$vo->id_depto."',".$vo->manzanas.",".$vo->nacimiento.",'".$vo->acto_admin."')";

            $this->conn->Execute($sql);

            echo "Registro insertado con &eacute;xito!";
        }
        else{
            echo "Error - Existe un registro con el mismo nombre";
        }
    }

    /**
     * Actualiza un Depto en la B.D.
     * @access public
     * @param object $vo VO de Depto que se va a actualizar
     */
    function Actualizar($vo){
        $sql =  "UPDATE ".$this->tabla." SET ";
        $sql .= $this->columna_id." = '".$vo->id."',";
        $sql .= $this->columna_nombre." = '".$vo->nombre."',";
        $sql .= "ID_DEPTO = '".$vo->id_depto."',";
        $sql .= "MAN_MUN = ".$vo->manzanas.",";
        $sql .= "NACI_MUN = ".$vo->nacimiento.",";
        $sql .= "ACTO_MUN = '".$vo->acto_admin."'";
        $sql .= " WHERE ".$this->columna_id." = '".$vo->id."'";
        $this->conn->Execute($sql);

    }

    /**
     * Borra un Depto en la B.D.
     * @access public
     * @param int $id ID del Depto que se va a borrar de la B.D
     */
    function Borrar($id){

        //BORRA
        $sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = '".$id."'";
        $this->conn->Execute($sql);

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
}


/**
 * Ajax de Municipios
 *
 * Contiene los metodos para Ajax de la clase Municipio
 * @author Ruben A. Rojas C.
 */

Class MunicipioAjax extends MunicipioDAO {

    /**
     * Lista ComboBox de municipios
     * @access public
     * @param string $id_deptos ID de los Departamentos
     * @param int $multiple 0 = comboBox no es multiple, > 0 comboBox es multiple de size = $multiple
     * @param int $titulo 1 = Mostrar titulo
     * @param int $separador_depto 1 = Coloca el titulo del departamento en las opciones
     */
    function comboBoxMunicipio($id_deptos,$multiple,$titulo,$separador_depto,$id_name){

        //LIBRERIAS
        include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/model/municipio.class.php");
        include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/model/depto.class.php");
        include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/dao/depto.class.php");

        //INICIALIZACION DE VARIABLES
        $depto_dao = New DeptoDAO();

        $id_deptos = explode(",",$id_deptos);
        $d = 0;
        foreach ($id_deptos as $id_depto){
            $id_deptos_i[$d] = "'$id_depto'";
            $d++;
        }
        $num = $this->numRecords("ID_DEPTO IN (".implode(",",$id_deptos_i).")");

        if ($num > 0){

            if ($titulo == 1)	echo "<b>Municipios</b><br>";

            if ($multiple == 0){
                echo "<select id='$id_name' name='$id_name' class='select'>";
                echo "<option value=''>[ Seleccione ]</option>";
            }
            else{
                //if ($num < $multiple)	$multiple = $num;
                echo "<select id='$id_name' name='".$id_name."[]' class='select' multiple size=$multiple>";
            }

            foreach ($id_deptos as $id_depto){
                $depto = $depto_dao->Get($id_depto);
                //echo $id_depto;
                $muns = $this->GetAllArray("ID_DEPTO ='$id_depto'");

                if ($separador_depto == 1)	echo "<option value='' disabled>-------- ".$depto->nombre." --------</option>";
                foreach ($muns as $mun){
                    echo "<option value='".$mun->id."'>".$mun->nombre."</option>";
                }
            }

            echo "</select>";
        }
        else{
            echo "<b>* No hay Info *</b>";
        }
    }

    /**
     * Lista grupo de checkbox de municipios
     * @access public
     * @param string $id_deptos ID de los Departamentos
     * @param int $multiple 0 = comboBox no es multiple, > 0 comboBox es multiple de size = $multiple
     * @param int $titulo 1 = Mostrar titulo
     * @param int $separador_depto 1 = Coloca el titulo del departamento en las opciones
     * @param string $id_name Name para el input
     * @param string $id_muns_chk IDs de los muns checked si ya existe el combo, caso update en forms
     */
    function checkBoxMunicipio($id_deptos,$titulo,$separador_depto,$id_name,$id_muns_chk){

        //LIBRERIAS
        include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/model/municipio.class.php");
        include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/model/depto.class.php");
        include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/dao/depto.class.php");

        //INICIALIZACION DE VARIABLES
        $depto_dao = New DeptoDAO();

        $id_deptos = explode(",",$id_deptos);
        $id_muns_chk = explode(",",$id_muns_chk);

        $d = 0;
        foreach ($id_deptos as $id_depto){
            $id_deptos_i[$d] = "'$id_depto'";
            $d++;
        }
        $num = $this->numRecords("ID_DEPTO IN (".implode(",",$id_deptos_i).")");

        if ($num > 0){

            if ($titulo == 1)	echo "<b>Municipios</b><br>";

            foreach ($id_deptos as $id_depto){
                $depto = $depto_dao->Get($id_depto);
                //echo $id_depto;
                $muns = $this->GetAllArray("ID_DEPTO ='$id_depto'");

                if ($separador_depto == 1)	echo "-------- <b>".strtoupper($depto->nombre)."</b> --------<br />";
                foreach ($muns as $mun){
                    $chk = (in_array($mun->id,$id_muns_chk)) ? ' checked ' : '';
                    echo "<input type='checkbox' name='".$id_name."[]' value='".$mun->id."' $chk>&nbsp;".$mun->nombre."<br />";
                }

                echo '<br />';
            }

        }
        else{
            echo "<b>* No hay Info *</b>";
        }
    }

    /**
     * Lista ComboBox de municipios para Insertar Evento Conflicto
     * @access public
     * @param string $id_deptos ID de los Departamentos
     * @param int $multiple 0 = comboBox no es multiple, > 0 comboBox es multiple de size = $multiple
     * @param int $titulo 1 = Mostrar titulo
     * @param int $separador_depto 1 = Coloca el titulo del departamento en las opciones
     */
    function comboBoxMunicipioEvento($id_deptos,$multiple,$titulo,$separador_depto){

        //LIBRERIAS
        include_once("lib/model/municipio.class.php");
        include_once("lib/model/depto.class.php");
        include_once("lib/dao/depto.class.php");

        //INICIALIZACION DE VARIABLES
        $depto_dao = New DeptoDAO();

        $id_deptos = explode(",",$id_deptos);
        $d = 0;
        foreach ($id_deptos as $id_depto){
            $id_deptos_i[$d] = "'$id_depto'";
            $d++;
        }

        $num = $this->numRecords("ID_DEPTO IN (".implode(",",$id_deptos_i).")");

        if ($num > 0){

            if ($titulo == 1)	echo "<b>Municipios</b><br>";

            if ($multiple == 0){
                echo "<select id='id_muns' name='id_muns[]' class='select'>";
                echo "<option value=''>[ Seleccione ]</option>";
            }
            else{
                //if ($num < $multiple)	$multiple = $num;
                echo "<select id='id_muns' name='id_muns[]' class='select' multiple size=$multiple>";
            }

            foreach ($id_deptos as $id_depto){
                $depto = $depto_dao->Get($id_depto);
                echo $id_depto;
                $muns = $this->GetAllArray("ID_DEPTO ='$id_depto'");

                if ($separador_depto == 1)	echo "<option value='' disabled>-------- ".$depto->nombre." --------</option>";
                foreach ($muns as $mun){
                    echo "<option value='".$mun->id."'>".$mun->nombre."</option>";
                }
            }

            echo "</select>";
        }
        else{
            echo "<b>* No hay Info *</b>";
        }
    }	
    
    /**
     * Lista Municipios alimentación 4w
     * @access public
     * @param string $id_depto ID del Departamento
     * @param int $titulo 1 = Mostrar titulo
     */
    function checkboxMpios4w($id_depto,$titulo){

        //LIBRERIAS
        include_once("lib/model/municipio.class.php");
        include_once("lib/model/depto.class.php");
        include_once("lib/dao/depto.class.php");

        //INICIALIZACION DE VARIABLES
        $depto_dao = New DeptoDAO();

        $num = $this->numRecords("ID_DEPTO = $id_depto");

        if ($num > 0){
            
            $depto = $depto_dao->Get($id_depto);
            $muns = $this->GetAllArray("ID_DEPTO ='$id_depto'");

            foreach ($muns as $m => $mun){
                
                echo '<div class="checkbox col left" id="mpio_'.$mun->id.'">
                        <input type="checkbox" value="'.$mun->id.'" id="mun_'.$mun->id.'" name="id_muns[]" 
                         onclick="$j(\'#mpio_'.$mun->id.'\').toggleClass(\'selected\')" />
                        <label for="mun_'.$mun->id.'" class="ch">'.$mun->nombre.'</div>';
            }
        }
        else{
            echo "<b>* No hay Info *</b>";
        }
    }	
    
    /**
     * Consulta Municipios a partir de divipolas separados por coma - alimentación 4w
     * @access public
     * @param string $ids Divipolas
     */
    function getMpiosFromDivipola($ids) {

        $sql = "SELECT id_mun, nom_mun, nom_depto FROM municipio JOIN departamento USING(id_depto) WHERE id_mun IN ($ids)";
        $rs = $this->conn->OpenRecordset($sql);

        $j = array();
        $success = 0;
        while ($row = $this->conn->FetchObject($rs)) {
            $success = 1;
            $label = $row->nom_depto.' >> '.$row->nom_mun; 
            $j[] = array('divipola' => $row->id_mun, 'label' => utf8_encode($label));
        }

        echo json_encode(compact('success', 'j'));

    }


    /**
     * Lista Municipios mapa 4w
     * @access public
     * @param string $id_depto ID del Departamento
     */
    function checkboxMpiosMapa4w($id_depto){

        //LIBRERIAS
        include_once("admin/lib/model/municipio.class.php");
        include_once("admin/lib/model/depto.class.php");
        include_once("admin/lib/dao/depto.class.php");

        //INICIALIZACION DE VARIABLES
        $depto_dao = New DeptoDAO();
            
        $depto = $depto_dao->Get($id_depto);
        $muns = $this->GetAllArray("ID_DEPTO ='$id_depto'");

        foreach ($muns as $m){
            echo '<option value="'.$m->id.'|'.$m->longitude.','.$m->latitude.'">'.$m->nombre.'</option>';
        }
    }	
}
?>
