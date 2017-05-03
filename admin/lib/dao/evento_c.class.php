<?php
/**
 * DAO de EventoConflicto
 *
 * Contiene los métodos de la clase EventoConflicto 
 * @author Ruben A. Rojas C.
 */

Class EventoConflictoDAO {

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
    * Número de Registros en Pantalla para ListarTAbla
    * @var string
    */
    var $num_reg_pag;

    /**
  * Constructor
    * Crea la conexión a la base de datos
  * @access public
  */    
    function EventoConflictoDAO (){
        $this->conn = MysqlDb::getInstance();
        $this->tabla = "evento_c";
        $this->columna_id = "ID_EVEN";
        $this->columna_nombre = "SINTESIS_EVEN";
        $this->columna_order = "FECHA_REG_EVEN";
        $this->num_reg_pag = 40;
//      $this->url = "index.php?accion=listar&class=EventoConflictoDAO&method=ListarTabla&param=";
        $this->url = "index.php?m_e=evento_c&accion=insertar";
    }

    /**
  * Consulta los datos de una EventoConflicto
  * @access public
  * @param int $id ID del EventoConflicto
  * @return VO
  */    
    function Get($id){
        $sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
        $rs = $this->conn->OpenRecordset($sql);
        $row_rs = $this->conn->FetchObject($rs);

        //Crea un VO
        $depto_vo = New EventoConflicto();

        //Carga el VO
        $depto_vo = $this->GetFromResult($depto_vo,$row_rs);

        //Retorna el VO
        return $depto_vo;
    }

    /**
  * Retorna el max ID
  * @access public
  * @return int
  */    
    function GetMaxID(){
        
        $sql = "SELECT max($this->columna_id) as maxid FROM ".$this->tabla;
        $rs = $this->conn->OpenRecordset($sql);
        if($row_rs = $this->conn->FetchRow($rs)){
            return $row_rs[0];
        }
        else{
            return 0;
        }
    }
    
    /**
    * Retorna el max(numero de descripciones) que tienen los eventos
    * @access public
    * @return int
    */  
    function getMaxDescsEvento($f_ini='',$f_fin=''){
        $sql = 'SELECT count(id_deseven) as num FROM descripcion_evento';
        
        if ($f_ini != '' AND $f_fin != ''){
            $sql .= " JOIN $this->tabla USING ($this->columna_id) WHERE fecha_reg_even BETWEEN '$f_ini' AND '$f_fin'";
        }
        
        $sql .= ' GROUP BY id_even ORDER BY num DESC LIMIT 0,1';
        $rs = $this->conn->OpenRecordset($sql);
        $row_rs = $this->conn->FetchRow($rs);
        return $row_rs[0];
    }
    
    /**
    * Retorna el max(numero de actores) que tienen los eventos
    * @access public
    * @return int
    */  
    function getMaxActoresEvento($f_ini='',$f_fin=''){
        $sql = 'SELECT count(actor_descevento.id_actor) AS num FROM actor_descevento JOIN actor USING(id_actor)';
        
        if ($f_ini != '' AND $f_fin != ''){
            $sql .= " JOIN descripcion_evento USING (id_deseven) JOIN $this->tabla USING($this->columna_id)"; 
        }
        
        $sql .= ' WHERE nivel=1';
    
        if ($f_ini != '' AND $f_fin != ''){
            $sql .= " AND fecha_reg_even BETWEEN '$f_ini' AND '$f_fin'";
        }

        $sql .= ' GROUP BY id_deseven ORDER BY num DESC LIMIT 0,1';
        $rs = $this->conn->OpenRecordset($sql);
        $row_rs = $this->conn->FetchRow($rs);
        return $row_rs[0];
    }

    /**
    * Retorna el max(numero de victimas) que tienen los eventos
    * @access public
    * @return int
    */  
    function getMaxVictimasEvento($f_ini='',$f_fin=''){
        $sql = 'SELECT count(id_victima) as num FROM victima';
        $sql .= " JOIN descripcion_evento USING (id_deseven) JOIN $this->tabla USING($this->columna_id)"; 
        
        if ($f_ini != '' AND $f_fin != ''){
            $sql .= " WHERE fecha_reg_even BETWEEN '$f_ini' AND '$f_fin'";
        }

        $sql .= ' GROUP BY '.$this->columna_id.' ORDER BY num DESC LIMIT 0,1';
        $rs = $this->conn->OpenRecordset($sql);
        $row_rs = $this->conn->FetchRow($rs);
        return $row_rs[0];
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
  * Consulta los datos de los EventoConflicto que cumplen una condición
  * @access public
  * @param string $condicion Condición que deben cumplir los EventoConflicto y que se agrega en el SQL statement.
  * @return array Arreglo de VOs
  */    
    function GetAllArray($condicion){
        
        $sql = "SELECT * FROM ".$this->tabla;
        if ($condicion != ""){
            $sql .= " WHERE ".$condicion;
        }
        $sql .= " ORDER BY ".$this->columna_order;

        $array = Array();

        $rs = $this->conn->OpenRecordset($sql);
        while ($row_rs = $this->conn->FetchObject($rs)){
            //Crea un VO
            $vo = New EventoConflicto();
            //Carga el VO
            $vo = $this->GetFromResult($vo,$row_rs);
            //Carga el arreglo
            $array[] = $vo;
        }
        //Retorna el Arreglo de VO
        return $array;
    }
    
    /**
  * Consulta los ID de los EventoConflicto que cumplen una condición
  * @access public
  * @param string $condicion Condición que deben cumplir los EventoConflicto y que se agrega en el SQL statement.
  * @return array Arreglo de VOs
  */    
    function GetAllArrayID($condicion){
        
        $sql = "SELECT ".$this->columna_id." FROM ".$this->tabla;

        if ($condicion != ""){
            $sql .= " WHERE ".$condicion;
        }
        $sql .= " ORDER BY ".$this->columna_order;

        $array = Array();

        $rs = $this->conn->OpenRecordset($sql);
        while ($row_rs = $this->conn->FetchRow($rs)){
            //Carga el arreglo
            $array[] = $row_rs[0];
        }

        //Retorna el Arreglo de VO
        return $array;
    }

    /**
  * Lista los EventoConflicto que cumplen la condición en el formato dado
  * @access public
  * @param string $formato Formato en el que se listarán los EventoConflicto, puede ser Tabla o ComboSelect
    * @param int $valor_combo ID del EventoConflicto que será selccionado cuando el formato es ComboSelect
  * @param string $condicion Condición que deben cumplir los EventoConflicto y que se agrega en el SQL statement.
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
  * Lista los TipoEventoConflicto en una Tabla
  * @access public
  */            
    function ListarTabla(){
        
        $cat_dao = new CatEventoConflictoDAO();

        ////CLASS
        if (isset($_POST["class"])){
            $class = $_POST['class'];
        }
        else if (isset($_GET["class"])){
            $class = $_GET['class'];
        }

        ////METHOD
        if (isset($_POST["method"])){
            $method = $_POST['method'];
        }
        else if (isset($_GET["method"])){
            $method = $_GET['method'];
        }

        ////PARAM
        if (isset($_POST["param"])){
            $param = $_POST['param'];
        }
        else if (isset($_GET["method"])){
            $param = $_GET['param'];
        }

        ////FECHA INICIAL
        $f_ini = "";
        if (isset($_POST["f_ini"])){
            $f_ini = $_POST['f_ini'];
            $_SESSION['f_ini'] = $f_ini;
        }
        else if (isset($_GET["f_ini"])){
            $f_ini = $_GET['f_ini'];
            $_SESSION['f_ini'] = $f_ini;
        }

        ////FECHA FINAL
        $f_fin = "";
        if (isset($_POST["f_fin"])){
            $f_fin = $_POST['f_fin'];
            $_SESSION['f_fin'] = $f_fin;
        }
        else if (isset($_GET["f_fin"])){
            $f_fin = $_GET['f_fin'];
            $_SESSION['f_fin'] = $f_fin;
        }
        
        //ID
        $id = "";
        if (isset($_POST["id"])){
            $id = $_POST['id'];
            $_SESSION['id'] = $id;
        }
        else if (isset($_GET["id"])){
            $id = $_GET['id'];
            $_SESSION['id'] = $id;
        }
        
        if ($f_ini != "" && $f_fin != ""){
            $where = "FECHA_REG_EVEN BETWEEN '".$f_ini."' AND '".$f_fin."'";
            $texto_filtro = "<b>Listado de Eventos que ocurrieron entre $f_ini y $f_fin</b>";
        }
        else if ($id != ""){
            $where = "id_even = $id";
            $texto_filtro = "";
        }
        
        $arr = $this->GetAllArray($where);
        $num_arr = count($arr);

        
        echo "<table width='900' align='center' cellspacing='1' cellpadding='3'>
                <tr><td>&nbsp;</td></tr>
                <tr><td colspan='5'>$texto_filtro</td></tr>
          <tr class='titulo_lista'>
              <td>ID</td>
              <td>Categor&iacute;a</td>
              <td width='700'>Evento</td>
              <td align='center' width='80'>Fecha Reg.</td>
              <td align='center'># ".$num_arr."</td>
            </tr>";

        //PAGINACION
        $inicio = 0;
        $pag_url = 1;
        if (isset($_GET['page']) && $_GET['page'] > 1){
            $pag_url = $_GET['page'];
            $inicio = ($pag_url-1)*$this->num_reg_pag;
        }
        $fin = $inicio + $this->num_reg_pag;
        if ($fin > $num_arr){
            $fin = $num_arr;
        }

        for($p=$inicio;$p<$fin;$p++){
            $style = "";
            if (fmod($p+1,2) == 0)  $style = "fila_lista";
            echo "<tr class='".$style."'>";

            echo "<td><div align='justify'>".$arr[$p]->id."</div></td>";
            //Categoria
            if ($arr[$p]->id_cat != ""){
                $cat = $cat_dao->Get($arr[$p]->id_cat);
                echo "<td align='center'>".$cat->nombre."</td>";
            }
            else{
                echo "<td>-</td>";
            }

            echo "<td><div align='justify'>".$arr[$p]->sintesis."</div></td>";
            echo "<td align='center'>".$arr[$p]->fecha_evento."</td>";
            echo "<td align='center'>
                <a href='#' onclick=\"window.open('evento_c/ver.php?id=".$arr[$p]->id."','','top=100,left=100,height=600,width=1024,scrollbars=1');return false;\">Ver</a><br>
                <a href='index.php?accion=actualizar&id=".$arr[$p]->id."'>Editar</a><br>
                <a href='index.php?accion=borrar&class=".$class."&method=Borrar&param=".$arr[$p]->id."' onclick=\"return confirm('Está seguro que desea borrar el Evento?');\">Borrar</a>
                </td>";

            echo "</tr>";
        }

        echo "<tr><td>&nbsp;</td></tr>";
        //PAGINACION
        if ($num_arr > $this->num_reg_pag){

            $num_pages = ceil($num_arr/$this->num_reg_pag);
            echo "<tr><td colspan='5' align='center'>";

            echo "Ir a la página:&nbsp;<select onchange=\"location.href='index.php?f_ini=".$f_ini."&f_fin=".$f_fin."&accion=listar&class=".$class."&method=".$method."&param=".$param."&page='+this.value\" class='select'>";
            for ($pa=1;$pa<=$num_pages;$pa++){
                echo " <option value='".$pa."'";
                if ($pa == $pag_url)    echo " selected ";
                echo ">".$pa."</option> ";
            }
            echo "</select>";
            echo "</td></tr>";
        }
        echo "</table>";
    }

    /**
  * Imprime en pantalla los datos del EventoConflicto
  * @access public
  * @param object $vo EventoConflicto que se va a imprimir
  * @param string $formato Formato en el que se listarán los EventoConflicto, puede ser Tabla o ComboSelect
    * @param int $valor_combo ID del EventoConflicto que será selccionado cuando el formato es ComboSelect
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
  * Carga un VO de EventoConflicto con los datos de la consulta
  * @access public
  * @param object $vo VO de EventoConflicto que se va a recibir los datos
  * @param object $Resultset Resource de la consulta
    * @return object $vo VO de EventoConflicto con los datos
  */            
    function GetFromResult ($vo,$Result){

        $vo->id = $Result->ID_EVEN;
        $vo->fecha_evento = $Result->FECHA_REG_EVEN;
        $vo->sintesis = $Result->SINTESIS_EVEN;
        $vo->validado = $Result->VALIDADO;
        
        //Categoria
        $sql = "SELECT id_cateven FROM subcat_even INNER JOIN descripcion_evento ON subcat_even.id_scateven = descripcion_evento.id_scateven WHERE id_even = $vo->id";
        $rs = $this->conn->OpenRecordset($sql);
        $row = $this->conn->FetchRow($rs);
        
        $vo->id_cat = $row[0];

        return $vo;

    }

    /**
    * Consulta los actores de un evento
    * @access public
    * @param int $id_evento ID deL evento
    * @return array Arreglo de Id de los mpios, y arreglo de lugares
    */          
    function getLocalizacionEvento ($id_evento){
        
        $arr = array();
        $arr_lugar = array();
        
        $l = 0;
        $sql = "SELECT ID_MUN,LUGAR FROM evento_localizacion WHERE ID_EVEN = $id_evento";
        $rs = $this->conn->OpenRecordset($sql);
        while ($row_rs = $this->conn->FetchRow($rs)){

            $arr[] = $row_rs[0];
            $arr_lugar[] = $row_rs[1];
            
            $l++;
        }

        return array("mpios" => $arr,"lugar" => $arr_lugar,"num"=>$l);
    }   
    
    /**
    * Consulta los actores de un evento
    * @access public
    * @param int $id_desevento ID de la descripcion del evento
    * @param int $nivel Nivel de profunidad en el árbol genialógico
    * @return array Arreglo de Id de los actores, y arreglo de nombres
    */          
    function getActorEvento ($id_desevento,$nivel=1){
        
        $actor_dao = New ActorDAO();
        
        $arr = array();
        $arr_nom = array();
        
        $sql = "SELECT actor_descevento.ID_ACTOR FROM actor_descevento JOIN actor USING(id_actor) WHERE ID_DESEVEN = $id_desevento AND nivel = $nivel";

        // Si no es nivel 1, debe hacer check de que el papa o abuelo exista
        if ($nivel > 1){
            $ids_papa = $this->getActorEvento($id_desevento,$nivel-1);
            $ids_papa = implode(',',$ids_papa['id']);
            if ($ids_papa == '')    $sql .= " AND 1=-1";
        }
        $rs = $this->conn->OpenRecordset($sql);
        while ($row_rs = $this->conn->FetchRow($rs)){
            
            $arr[] = $row_rs[0];
            
            $actor = $actor_dao->Get($row_rs[0]);
            $arr_nom[] = $actor->nombre;
        }
        return array("id" => $arr,"nombre" => $arr_nom);
    }
    
    /**
    * Consulta las fuentes de un evento
    * @access public
    * @param int $id_evento ID del evento
    * @return array Arreglo de Id de las fuentes y arreglo de los nombre
    */          
    function getFuenteEvento ($id_evento){
        
        $sfuente_dao = New SubFuenteEventoConflictoDAO();
        $fuente_dao = New FuenteEventoConflictoDAO();
        
        $arr_id_fuente = array();
        $arr_id_sfuente = array();
        $arr_nom_fuente = array();
        $arr_nom_sfuente = array();
        $arr_fecha = array();
        $arr_medio = array();
        $arr_desc = array();
        
        $f = 0;
        $sql = "SELECT * FROM fuen_evento WHERE ID_EVEN = $id_evento";
        $rs = $this->conn->OpenRecordset($sql);
        while ($row_rs = $this->conn->FetchObject($rs)){

            $arr_id_sfuente[] = $row_rs->ID_SFUEVEN;
            
            if ($row_rs->ID_SFUEVEN > 0){
                
                $sfuente = $sfuente_dao->Get($row_rs->ID_SFUEVEN);
                $arr_nom_sfuente[] = $sfuente->nombre;
                $arr_id_fuente[] = $sfuente->id_fuente;
            
                $fuente = $fuente_dao->Get($sfuente->id_fuente);
                $arr_nom_fuente[] = $fuente->nombre;
            }
            else{
                $arr_nom_sfuente[] = "";
                $arr_id_fuente[] = 0;
                $arr_nom_fuente[] = "";
            }

            $arr_desc[] = $row_rs->DESEVEN_FUENTE;
            $arr_fecha[] = $row_rs->FECHA_FUENTE;
            $arr_medio[] = $row_rs->REFER_FUENTE;
            
            $f++;
        }
        return array("id_fuente" => $arr_id_fuente,
                     "nom_fuente" => $arr_nom_fuente,
                     "id_sfuente" => $arr_id_sfuente,
                     "nom_sfuente" => $arr_nom_sfuente,
                     "desc" => $arr_desc,
                     "fecha" => $arr_fecha,
                     "medio" => $arr_medio,
                     "num"=>$f);
    }   
    
    /**
    * Consulta las descripciones de un evento
    * @access public
    * @param int $id_evento ID del evento
    * @return array Arreglo con las variables
    */          
    function getDescripcionEvento ($id_evento){
        
        $scat_dao = New SubCatEventoConflictoDAO();
        $cat_dao = New CatEventoConflictoDAO();
        
        $arr_id = array();
        $arr_id_cat = array();
        $arr_nom_cat = array();
        $arr_id_scat = array();
        $arr_nom_scat = array();
        $arr_victimas = array();
        
        $vict_total = 0;
        $n = 0;
        $sql = "SELECT * FROM descripcion_evento WHERE ID_EVEN = $id_evento ORDER BY id_deseven";
        $rs = $this->conn->OpenRecordset($sql);
        while ($row_rs = $this->conn->FetchObject($rs)){
            $arr_id[] = $row_rs->ID_DESEVEN;
            
            $scat = $scat_dao->get($row_rs->ID_SCATEVEN);
            $arr_id_scat[] = $row_rs->ID_SCATEVEN;
            $arr_nom_scat[] = $scat->nombre;
            
            $arr_id_cat[] = $scat->id_cat;
            $vo = $cat_dao->Get($scat->id_cat);
            $arr_nom_cat[] = $vo->nombre;
            
            $sql = "SELECT count(id_victima) FROM victima WHERE id_deseven = ".$row_rs->ID_DESEVEN;
            $rs_v = $this->conn->OpenRecordset($sql);
            $row_rs_v = $this->conn->FetchRow($rs_v);
            $arr_victimas[] = $row_rs_v[0];
            $vict_total += $row_rs_v[0];
            
            $n++;
        }
        return array("id" => $arr_id, 
                     "id_cat" => $arr_id_cat,
                     "nom_cat" => $arr_nom_cat,
                     "id_scat" => $arr_id_scat, 
                     "nom_scat" => $arr_nom_scat, 
                     "num" => $n, 
                     "num_victimas" => $arr_victimas, 
                     "num_victimas_total" => $vict_total);
    }
    
    /**
    * Consulta las victimas por descripción
    * @access public
    * @param int $id_deseven ID de la descripción evento
    * @return array Arreglo con las variables
    */          
    function getVictimaDescripcionEvento ($id_deseven){
        
        $scat_dao = New SubCatEventoConflictoDAO();
        $setnia_dao = New SubEtniaDAO();
        $edad_dao = New EdadDAO();
        $rango_edad_dao = New RangoEdadDAO();
        $estado_dao = New EstadoMinaDAO();
        $condicion_dao = New CondicionMinaDAO();
        $subcondicion_dao = New SubCondicionDAO();
        $sexo_dao = New SexoDAO();
        $etnia_dao = New EtniaDAO();
        $sub_etnia_dao = New SubEtniaDAO();
        $subetnia_dao = New SubetniaDAO();
        $ocupacion_dao = New OcupacionDAO();
        
        $arr_cant = array();
        $arr_id_edad = array();
        $arr_nom_edad = array();
        $arr_id_redad = array();
        $arr_nom_redad = array();
        $arr_id_sexo = array();
        $arr_nom_sexo = array();
        $arr_id_etnia = array();
        $arr_nom_etnia = array();
        $arr_id_setnia = array();
        $arr_nom_setnia = array();
        $arr_id_condicion = array();
        $arr_nom_condicion = array();
        $arr_id_scondicion = array();
        $arr_nom_scondicion = array();
        $arr_id_estado = array();
        $arr_nom_estado = array();
        $arr_id_ocupacion = array();
        $arr_nom_ocupacion = array();

        $v = 0;
        $sql = "SELECT * FROM victima WHERE ID_DESEVEN = $id_deseven";
        $rs = $this->conn->OpenRecordset($sql);
        while ($row_rs = $this->conn->FetchObject($rs)){
            
            $arr_cant[] = $row_rs->CANT_VICTIMA;
            
            $arr_id_edad[] = $row_rs->ID_EDAD;
            if ($row_rs->ID_EDAD > 0){
                $vo = $edad_dao->Get($row_rs->ID_EDAD);
                $arr_nom_edad[] = $vo->nombre;
            }
            else{
                $arr_nom_edad[] = '';
            }
            
            $arr_id_redad[] = $row_rs->ID_RANED;
            if ($row_rs->ID_RANED > 0){
                $vo = $rango_edad_dao->Get($row_rs->ID_RANED);
                $arr_nom_redad[] = $vo->nombre;
            }
            else{
                $arr_nom_redad[] = '';
            }
            
            $arr_id_setnia[] = $row_rs->ID_SUBETNIA;
            if ($row_rs->ID_SUBETNIA > 0){
                $setnia = $setnia_dao->Get($row_rs->ID_SUBETNIA);
                
                $arr_nom_setnia[] = $setnia->nombre;
                
                $arr_id_etnia[] = $setnia->id_etnia;
                $vo = $etnia_dao->Get($setnia->id_etnia);
                $arr_nom_etnia[] = $vo->nombre;
            }
            else{
                $arr_nom_etnia[] = '';
                $arr_nom_setnia[] = '';
            }
            
            $arr_id_sexo[] = $row_rs->ID_SEXO;
            if ($row_rs->ID_SEXO > 0){
                $vo = $sexo_dao->Get($row_rs->ID_SEXO);
                $arr_nom_sexo[] = $vo->nombre;
            }
            else{
                $arr_nom_sexo[] = '';
            }
            
            $arr_id_estado = $row_rs->ID_ESTADO;
            if ($row_rs->ID_ESTADO > 0){
                $vo = $estado_dao->Get($row_rs->ID_ESTADO);
                $arr_nom_estado[] = $vo->nombre;
            }
            else{
                $arr_nom_estado[] = '';
            }
            
            $arr_id_ocupacion[] = $row_rs->ID_OCUPACION;
            if ($row_rs->ID_OCUPACION > 0){
                $vo = $ocupacion_dao->Get($row_rs->ID_OCUPACION);
                $arr_nom_ocupacion[] = $vo->nombre;
            }
            else{
                $arr_nom_ocupacion[] = '';
            }
            
            $arr_id_condicion[] = $row_rs->ID_CONDICION;
            if ($row_rs->ID_CONDICION > 0){
                $vo = $condicion_dao->Get($row_rs->ID_CONDICION);
                $arr_nom_condicion[] = $vo->nombre;
            }
            else{
                $arr_nom_condicion[] = '';
            }
            
            $arr_id_scondicion[] = $row_rs->ID_SUBCONDICION;
            if ($row_rs->ID_SUBCONDICION > 0){
                $vo = $subcondicion_dao->Get($row_rs->ID_SUBCONDICION);
                $arr_nom_scondicion[] = $vo->nombre;
            }
            else{
                $arr_nom_scondicion[] = '';
            }
            
            $v++;
            
        }
        
        return array("cant" => $arr_cant,
                     "edad" => $arr_id_edad,
                     "nom_edad" => $arr_nom_edad,
                     "redad" => $arr_id_redad,
                     "nom_redad" => $arr_nom_redad,
                     "setnia" => $arr_id_setnia,
                     "nom_setnia" => $arr_nom_setnia,
                     "etnia" => $arr_id_etnia,
                     "nom_etnia" => $arr_nom_etnia,
                     "sexo" => $arr_id_sexo,
                     "nom_sexo" => $arr_nom_sexo,
                     "estado" => $arr_id_estado,
                     "nom_estado" => $arr_nom_estado,
                     "ocupacion" => $arr_id_ocupacion,
                     "nom_ocupacion" => $arr_nom_ocupacion,
                     "condicion" => $arr_id_condicion,
                     "nom_condicion" => $arr_nom_condicion,
                     "scondicion" => $arr_id_scondicion,
                     "nom_scondicion" => $arr_nom_scondicion,
                     "num" => $v
                     );
    }   
    
    /**
    * Inserta un EventoConflicto en la B.D.
    * @access public
    * @param object $evento_vo VO de EventoConflicto que se va a insertar
    * @param int $alert Muestra la alerta JS
    * @param array $num_vict_desc Número de víctimas por descripción, el numero tiene que ser el real menos 1, debido al formulario de insertar
    * @param array $num_actores_desc Número de actores por descripción
    * @param array $num_subactores_desc Número de sub actores por descripción
    * @param array $num_subsubactores_desc Número de sub sub actores por descripción
    */      
    function Insertar($evento_vo,$alert=0,$num_vict_desc,$num_actores_0_desc,$num_actores_desc,$num_subactores_desc,$num_subsubactores_desc){
        //DATOS DEL EVENTO
        $sql =  "INSERT INTO ".$this->tabla." (SINTESIS_EVEN,FECHA_REG_EVEN,FECHA_ING_EVEN)";
        $sql .= " VALUES ('".$evento_vo->sintesis."','".$evento_vo->fecha_evento."',now())";
        
        //Esta se usa, para la importacion de eventos de 2007, para que la fecha de registro no quede la fecha de importacion
        //$sql .= " VALUES ('".$evento_vo->sintesis."','".$evento_vo->fecha_evento."','".$evento_vo->fecha_ingreso."')";
        
        //echo "$sql<br>";
        
        $this->conn->Execute($sql);
        
        $id_evento = $this->conn->GetGeneratedID();

        $this->InsertarTablasUnion($evento_vo,$id_evento,$num_vict_desc,$num_actores_0_desc,$num_actores_desc,$num_subactores_desc,$num_subsubactores_desc);
        
        if ($alert == 1){
            ?>
            <script>
                alert("Evento insertado con éxito!");
                location.href="<?=$this->url;?>";
            </script>
            <?
        }
    }

    /**
    * Inserta las tablas de union para el EventoConflicto en la B.D.
    * @access public
    * @param object $depto_vo VO de EventoConflicto que se va a insertar
    * @param array $num_vict_desc Número de víctimas por descripción
    * @param array $num_actores_desc Número de actores por descripción
    * @param array $num_subactores_desc Número de sub actores por descripción
    * @param array $num_subsubactores_desc Número de sub sub actores por descripción
    */      
    function InsertarTablasUnion($evento_vo,$id_evento,$num_vict_desc,$num_actores_0_desc,$num_actores_desc,$num_subactores_desc,$num_subsubactores_desc){

        $actor_dao = New ActorDAO();
        
        //DESCRIPCION EVENTO
        $arr = $evento_vo->id_cat;
        
        $a = 0;
        $num_victimas_acumulado = 0;
        $num_actores_0_acumulado = 0;
        $num_actores_acumulado = 0;
        $num_subactores_acumulado = 0;
        $num_subsubactores_acumulado = 0;
        foreach ($arr as $ar){
            
            $sql = "INSERT INTO descripcion_evento (ID_SCATEVEN,ID_EVEN) VALUES (".$evento_vo->id_subcat[$a].",$id_evento)";
            $this->conn->Execute($sql);

            //echo("Descripcion $sql<br>");
            
            $id_desceven = $this->conn->GetGeneratedID();
            
            //VICTIMAS
            if ($num_vict_desc[$a] == 0) $num_vict_desc[$a] = 1;
            
            $numero_victimas_x_desc = $num_vict_desc[$a];

            $hasta = $num_victimas_acumulado + $numero_victimas_x_desc;
            for($d=$num_victimas_acumulado;$d<$hasta;$d++){
            
                if (isset($evento_vo->num_victimas[$d]) && $evento_vo->num_victimas[$d] != ""){
                    
                    $num_victimas = $evento_vo->num_victimas[$d];
                    
                    $id_rango_edad = (!isset($evento_vo->id_rango_edad[$d])) ? 0 : $evento_vo->id_rango_edad[$d];
                    $id_sub_cond = (!isset($evento_vo->id_subcondicion[$d])) ? 0 : $evento_vo->id_subcondicion[$d];
                    $id_sub_etnia = (!isset($evento_vo->id_subetnia[$d])) ? 0 : $evento_vo->id_subetnia[$d];
                    
                    $sql = "INSERT INTO victima
                            (ID_SEXO,ID_EDAD,ID_RANED,ID_CONDICION,ID_SUBCONDICION,ID_ESTADO,ID_DESEVEN,ID_SUBETNIA,ID_OCUPACION,CANT_VICTIMA) VALUES 
                            (".$evento_vo->id_sexo[$d].",".$evento_vo->id_edad[$d].",".$id_rango_edad.",".$evento_vo->id_condicion[$d].",".$id_sub_cond.",".$evento_vo->id_estado[$d].",$id_desceven,".$id_sub_etnia.",".$evento_vo->id_ocupacion[$d].",$num_victimas)";
                    //echo $sql;
//                  echo("Victimas$sql<br>");
                    $this->conn->Execute($sql);
                    
                }
                
            }
            
            $num_victimas_acumulado += $numero_victimas_x_desc;
            
            //ACTORES 0
            $numero_actores_0_x_desc = $num_actores_0_desc[$a];
            $hasta = $num_actores_0_acumulado + $numero_actores_0_x_desc;

            for($i=$num_actores_0_acumulado;$i<$hasta;$i++){
                $ar = $evento_vo->id_actor_0[$i];
                if ($ar != ''){
                    
                    //$cod_interno = $actor_dao->getCodigoInterno($ar);
                    
                    //$sql = "INSERT INTO actor_descevento (ID_ACTOR,ID_DESEVEN,COD_INTERNO_ACTOR) VALUES (".$ar.",".$id_desceven.",$cod_interno)";
                    $sql = "INSERT INTO actor_descevento (ID_ACTOR,ID_DESEVEN) VALUES (".$ar.",".$id_desceven.")";
                    //echo "Actor $i Descripcion $a:$sql<br>";
                    $this->conn->Execute($sql);
                }
            }
            
            $num_actores_0_acumulado += $numero_actores_0_x_desc;
            
            //ACTORES
            $numero_actores_x_desc = $num_actores_desc[$a];
            $hasta = $num_actores_acumulado + $numero_actores_x_desc;

            for($i=$num_actores_acumulado;$i<$hasta;$i++){
                $ar = $evento_vo->id_actor[$i];
                if ($ar != ''){
                    
                    //$cod_interno = $actor_dao->getCodigoInterno($ar);
                    
                    //$sql = "INSERT INTO actor_descevento (ID_ACTOR,ID_DESEVEN,COD_INTERNO_ACTOR) VALUES (".$ar.",".$id_desceven.",$cod_interno)";
                    $sql = "INSERT INTO actor_descevento (ID_ACTOR,ID_DESEVEN) VALUES (".$ar.",".$id_desceven.")";
                    //echo "Actor $i Descripcion $a:$sql<br>";
                    $this->conn->Execute($sql);
                }
            }
            
            $num_actores_acumulado += $numero_actores_x_desc;
            
            //SUB ACTORES
            $numero_subactores_x_desc = $num_subactores_desc[$a];
            $hasta = $num_subactores_acumulado + $numero_subactores_x_desc;
            for($i=$num_subactores_acumulado;$i<$hasta;$i++){
                $ar = $evento_vo->id_subactor[$i];
                if ($ar != ''){
                    $sql = "INSERT INTO actor_descevento (ID_ACTOR,ID_DESEVEN) VALUES (".$ar.",".$id_desceven.")";
//                  echo "Sub Actor $i Descripcion $a:$sql<br>";
                    $this->conn->Execute($sql);
                }
            }
            
            $num_subactores_acumulado += $numero_subactores_x_desc;
            
            //SUB SUB ACTORES
            $numero_subsubactores_x_desc = $num_subsubactores_desc[$a];
            $hasta = $num_subsubactores_acumulado + $numero_subsubactores_x_desc;
            for($i=$num_subsubactores_acumulado;$i<$hasta;$i++){
                $ar = $evento_vo->id_subsubactor[$i];
                if ($ar != ''){
                    $sql = "INSERT INTO actor_descevento (ID_ACTOR,ID_DESEVEN) VALUES (".$ar.",".$id_desceven.")";
//                  echo "Sub Sub Actor $i Descripcion $a:$sql<br>";
                    $this->conn->Execute($sql);
                }
            }
            
            $num_subsubactores_acumulado += $numero_subsubactores_x_desc;
            
            $a++;
        }
        
        //FUENTE EVENTO
        $arr = $evento_vo->id_subfuente;
        
        $a = 0;
        foreach ($arr as $ar){
            $sql = "INSERT INTO fuen_evento 
                    (ID_SFUEVEN,ID_EVEN,FECHA_FUENTE,DESEVEN_FUENTE,REFER_FUENTE) VALUES 
                    ($ar,$id_evento,'".$evento_vo->fecha_fuente[$a]."','".$evento_vo->desc_fuente[$a]."','".$evento_vo->refer_fuente[$a]."')";
            
//          echo $sql;
            $this->conn->Execute($sql);
            
            $a++;
        }
        
        //LOCALIZACION
        $arr = $evento_vo->id_mun;
        $a = 0;
        foreach ($arr as $ar){
            $sql = "INSERT INTO evento_localizacion 
                    (ID_MUN,ID_EVEN,LUGAR) VALUES 
                    ('$ar',$id_evento,'".$evento_vo->lugar[$a]."')";
            
//          echo "Mun:$sql<br>";
            $this->conn->Execute($sql);
            
            $a++;
            
        }
    }


    /**
    * Actualiza un EventoConflicto en la B.D.
    * @access public
    * @param object $evento_vo VO de EventoConflicto que se va a insertar
    * @param int $alert Muestra la alerta JS
    * @param array $num_vict_desc Número de víctimas por descripción
    * @param array $num_actores_desc Número de actores por descripción
    * @param array $num_subactores_desc Número de sub actores por descripción
    * @param array $num_subsubactores_desc Número de sub sub actores por descripción
    */      
    function Actualizar($evento_vo,$alert=0,$num_vict_desc,$num_actores_desc,$num_subactores_desc,$num_subsubactores_desc){
        //DATOS DEL EVENTO
        $sql =  "UPDATE ".$this->tabla." SET
                                        SINTESIS_EVEN = '".$evento_vo->sintesis."',
                                        FECHA_REG_EVEN = '".$evento_vo->fecha_evento."',
                                        VALIDADO = ".$evento_vo->validado."
                                        WHERE id_even = ".$evento_vo->id;
        //echo "$sql<br>";
        
        $this->conn->Execute($sql);
        
        $id_evento = $evento_vo->id;

        $this->BorrarTablasUnion($id_evento);
        $this->InsertarTablasUnion($evento_vo,$id_evento,$num_vict_desc,$num_actores_desc,$num_subactores_desc,$num_subsubactores_desc);
        
//      die();

        if ($alert == 1){
            ?>
            <script>
                alert("Evento actualizado con éxito!");
                location.href="index.php?accion=listar&class=EventoConflictoDAO&method=ListarTabla&param=";
            </script>
            <?
        }
    }

    /**
  * Borra un EventoConflicto en la B.D.
  * @access public
  * @param int $id ID del EventoConflicto que se va a borrar de la B.D
  */    
    function Borrar($id){

        //BORRA TABLAS DE UNION
        $this->BorrarTablasUnion($id);

        //BORRA EL EVENTO
        $sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
        $this->conn->Execute($sql);

    }

    /**
  * Borra las tablas de union de un EventoConflicto en la B.D.
  * @access public
  * @param int $id ID del EventoConflicto que se va a borrar de la B.D
  */    
    function BorrarTablasUnion($id){

        $sql = "SELECT id_deseven FROM descripcion_evento WHERE ".$this->columna_id." = ".$id;
        $rs = $this->conn->OpenRecordset($sql);
        while ($row = $this->conn->FetchRow($rs)){
            //ACTOR
            $sql = "DELETE FROM actor_descevento WHERE id_deseven = ".$row[0];
            $this->conn->Execute($sql);
    
            //VICTIMA
            $sql = "DELETE FROM victima WHERE id_deseven = ".$row[0];
            $this->conn->Execute($sql);
        }
        
        //DESCRIPCION
        $sql = "DELETE FROM descripcion_evento WHERE ".$this->columna_id." = ".$id;
        $this->conn->Execute($sql);
        

        //LOCALIZACION
        $sql = "DELETE FROM evento_localizacion WHERE ".$this->columna_id." = ".$id;
        $this->conn->Execute($sql);

        //FUENTE
        $sql = "DELETE FROM fuen_evento WHERE ".$this->columna_id." = ".$id;
        $this->conn->Execute($sql);
        
    }

    /**
  * Muestra la Información completa de una Organización
  * @access public
  * @param id $id Id de la Proyecto
  */            
    function Ver($id){

        //INICIALIZACION DE VARIABLES
        $depto_dao = New DeptoDAO();
        $mun_dao = New MunicipioDAO();
        $tipo_dao = New TipoEventoConflictoDAO();
        $actor_dao = New ActorDAO();
        $cat_tipo_dao = New CatTipoEventoConflictoDAO();
        $cons_hum_dao = New ConsHumDAO();
        $cons_hum_vo = New ConsHum();
        $riesgo_hum_dao = New RiesgoHumDAO();
        $riesgo_hum_vo = New RiesgoHum();


        //CONSULTA LA INFO DE LA ORG.
        $arr_vo = $this->Get($id);

        echo "<table cellspacing=1 cellpadding=3 class='tabla_consulta' border=0 align='center'>";
        echo "<tr class='titulo_lista'><td align='center' colspan='6'>INFORMACION DEL EVENTO</td></tr>";

        echo "<tr><td class='tabla_consulta'><b>Departamento</b></td>";
        echo "<td class='tabla_consulta'>";
        $z = 0;
        foreach($arr_vo->id_deptos as $id){
            $vo = $depto_dao->Get($id);

            echo "- ".$vo->nombre."<br>";
            $z++;
        }
        echo "</td></tr>";

        echo "<tr><td class='tabla_consulta'><b>Municipio</b></td>";
        echo "<td class='tabla_consulta'>";
        $z = 0;
        foreach($arr_vo->id_muns as $id){
            $vo = $mun_dao->Get($id);

            echo "- ".$vo->nombre."<br>";
            $z++;
        }
        echo "</td></tr>";

        echo "<tr><td class='tabla_consulta' width='150'><b>Lugar</b></td><td class='tabla_consulta' width='500'>".$arr_vo->lugar."</td></tr>";

        echo "<tr><td class='tabla_consulta'><b>Tipo de EventoConflicto</b></td>";
        echo "<td class='tabla_consulta'>";
        $z = 0;
        foreach($arr_vo->id_tipo as $id){
            $vo = $tipo_dao->Get($id);

            echo "- ".$vo->nombre."<br>";
            $z++;
        }
        echo "</td></tr>";

        echo "<tr><td class='tabla_consulta'><b>Actores</b></td>";
        echo "<td class='tabla_consulta'>";
        $z = 0;
        foreach($arr_vo->id_actores as $id){
            $vo = $actor_dao->Get($id);

            echo "- ".$vo->nombre."<br>";
            $z++;
        }
        echo "</td></tr>";

        echo "<tr><td class='tabla_consulta'><b>Consecuencias Humanitarias</b></td>";
        echo "<td class='tabla_consulta'>";
        $z = 0;
        foreach($arr_vo->id_cons as $id){
            $vo = $cons_hum_dao->Get($id);

            echo "- ".$vo->nombre."<br>";
            $z++;
        }
        echo "</td></tr>";

        echo "<tr><td class='tabla_consulta'><b>Riesgos Humanitarios</b></td>";
        echo "<td class='tabla_consulta'>";
        $z = 0;
        foreach($arr_vo->id_riesgos as $id){
            $vo = $riesgo_hum_dao->Get($id);

            echo "- ".$vo->nombre."<br>";
            $z++;
        }
        echo "</td></tr>";

        echo "<tr><td class='tabla_consulta' width='150'><b>Descripción</b></td><td class='tabla_consulta' width='500'>".$arr_vo->desc."</td></tr>";
        echo "<tr><td class='tabla_consulta' width='150'><b>Fecha de registro</b></td><td class='tabla_consulta' width='500'>".$arr_vo->fecha_registro."</td></tr>";

        echo "</table>";

    }

    /**
    * Lista los EventoConflictos en una Tabla
    * @access public
    */          
    function Reportar(){

        set_time_limit(0);

        //INICIALIZACION DE VARIABLES
        $evento_vo = New EventoConflicto();
        $evento_dao = New EventoConflictoDAO();
        $municipio_vo = New Municipio();
        $municipio_dao = New MunicipioDAO();
        $depto_vo = New Depto();
        $depto_dao = New DeptoDAO();
        $actor_vo = New Actor();
        $actor_dao = New ActorDAO();
        $fuente_vo = New FuenteEventoConflicto();
        $fuente_dao = New FuenteEventoConflictoDAO();
        $subfuente_vo = New SubFuenteEventoConflicto();
        $subfuente_dao = New SubFuenteEventoConflictoDAO();
        $cat_vo = New CatEventoConflicto();
        $cat_dao = New CatEventoConflictoDAO();
        $subcat_vo = New SubCatEventoConflicto();
        $subcat_dao = New SubCatEventoConflictoDAO();
        $edad_dao = New EdadDAO();
        $rango_edad_dao = New RangoEdadDAO();
        $estado_dao = New EstadoMinaDAO();
        $condicion_dao = New CondicionMinaDAO();
        $subcondicion_dao = New SubCondicionDAO();
        $sexo_dao = New SexoDAO();
        $etnia_dao = New EtniaDAO();
        $sub_etnia_dao = New SubEtniaDAO();
        $subetnia_dao = New SubetniaDAO();
        $ocupacion_dao = New OcupacionDAO();
        
        $filtro_cat = 0;
        $filtro_sexo = 0;
        $filtro_condicion = 0;
        $filtro_subcondicion = 0;
        $filtro_edad = 0;
        $filtro_redad = 0;
        $filtro_etnia = 0;
        $filtro_subetnia = 0;
        $filtro_ocupacion = 0;
        $filtro_estado = 0;
        $filtro_fecha = 0;
        $filtro_actor = 0;
        
        $style_leading_zeros = 'style="mso-style-parent:text; mso-number-format:\'@\';white-space:normal;"';

        $reporte = $_POST["reporte"];
        
        $tit_reporte = array(1 => "Conteo de eventos por Categoría/Subcategoría",
                             2 => "Conteo de eventos por confrontaciones entre dos actores",
                             3 => "Conteo de eventos por periodo de tiempo",
                             4 => "Conteo de eventos por categoría",
                             5 => "Conteo de eventos por confrontaciones entre dos actores",
                             6 => "Cantidad de víctimas",
                             7 => "Reporte General de Eventos");
        

        if (isset($_POST["f_ini"]) && $_POST["f_ini"][0] != ''){
            $filtro_fecha = 1;  
        }
        
        $fecha_ini = $_POST["f_ini"];
        $fecha_fin = $_POST["f_fin"];
        
        $nivel_localizacion = $_POST["nivel_localizacion"];
        
        //ACTORES
        $id_actor1 = array();
        $id_actor_filtro = 0;
        if (isset($_POST["id_actor1"])) {
            $id_actor1 = $_POST["id_actor1"];
            $id_actor1_s = implode(',', $id_actor1);
            $id_actor_filtro = $id_actor1[0];
            $filtro_actor = 1;
        }

        $id_actor2 = (isset($_POST["id_actor2"])) ? $_POST["id_actor2"] : array();

        
        //CAT-SUBCAT
        if (isset($_POST["id_cat"])){

            $filtro_cat = 1;
            $id_cat = $_POST["id_cat"];
            $id_cat_s = implode(",",$id_cat);
            
            $cats = $cat_dao->GetAllArray("id_cateven in ($id_cat_s)");
            
            if (isset($_POST["id_subcat"])){
                $id_subcat = $_POST["id_subcat"];
                $id_subcat_s = implode(",",$id_subcat);
                
                foreach($id_cat as $id){
                    $subtipos[$id] = $subcat_dao->GetAllArray("ID_CATEVEN = $id AND ID_SCATEVEN IN ($id_subcat_s)");
                }
            }
            else{
                $condicion_cat = "ID_CATEVEN IN ($id_cat_s)";
                $vo_cats = $cat_dao->GetAllArray($condicion_cat);
                
                foreach($id_cat as $id){
                    $subtipos[$id] = $subcat_dao->GetAllArray("ID_CATEVEN = $id");
                }

                //$subtipos = $subcat_dao->GetAllArray($condicion_cat);
                $id_subcat_s = implode(",",$subcat_dao->GetAllArrayID("ID_CATEVEN IN ($id_cat_s)"));
            }
        }
        else{
            $cats = $cat_dao->GetAllArray('');
            //$subtipos = $subcat_dao->GetAllArray('');
            
            foreach($cats as $vo){
                $subtipos[$vo->id] = $subcat_dao->GetAllArray("ID_CATEVEN = $vo->id");
            }
        }

        if ($reporte == 1){
            $sql_eventos = "SELECT DISTINCT evento_localizacion.id_mun FROM evento_localizacion INNER JOIN evento_c ON evento_localizacion.id_even = evento_c.id_even INNER JOIN municipio ON evento_localizacion.id_mun = municipio.id_mun INNER JOIN departamento ON municipio.id_depto = departamento.id_depto";
            
            if ($filtro_fecha == 1){
                $sql_eventos .= " WHERE fecha_reg_even BETWEEN '$fecha_ini[0]' AND '$fecha_fin[0]'";
            }
            
            $sql_eventos .= " ORDER BY nom_depto,nom_mun";
            $rs = $this->conn->OpenRecordset($sql_eventos);
            $m = 0;
            while ($row_rs = $this->conn->FetchRow($rs)){
                $id_ubi[$m] = $row_rs[0];
                $m++;
            }
        }
        else if ($reporte == 2){
            
            $sql_eventos = "SELECT DISTINCT evento_localizacion.id_mun FROM evento_localizacion INNER JOIN evento_c ON evento_localizacion.id_even = evento_c.id_even INNER JOIN municipio ON evento_localizacion.id_mun = municipio.id_mun INNER JOIN departamento ON municipio.id_depto = departamento.id_depto";
            
            if ($filtro_fecha == 1){
                $sql_eventos .= " WHERE fecha_reg_even BETWEEN '$fecha_ini[0]' AND '$fecha_fin[0]'";
            }
            
            $sql_eventos .= " ORDER BY nom_depto,nom_mun";
                    
            $rs = $this->conn->OpenRecordset($sql_eventos);
            $m = 0;
            while ($row_rs = $this->conn->FetchRow($rs)){
                $id_ubi[$m] = $row_rs[0];
                $m++;
            }
        }
        else if ($reporte == 3){
            
            $sql_eventos = "SELECT DISTINCT evento_localizacion.id_mun FROM evento_localizacion INNER JOIN evento_c ON evento_localizacion.id_even = evento_c.id_even INNER JOIN municipio ON evento_localizacion.id_mun = municipio.id_mun INNER JOIN departamento ON municipio.id_depto = departamento.id_depto";
            
            $sql_eventos .= " ORDER BY nom_depto,nom_mun";
                    
            $rs = $this->conn->OpenRecordset($sql_eventos);
            $m = 0;
            while ($row_rs = $this->conn->FetchRow($rs)){
                $id_ubi[$m] = $row_rs[0];
                $m++;
            }
        }
        //CONTEO DE VICTIMAS
        else if ($reporte == 6 || $reporte == 7){
            
            $cat_victima_localizacion = $_POST["cat_victima_localizacion"];
            
            //Filtro de sexo
            if (isset($_POST["id_sexo"])){
                $id = $_POST["id_sexo"];
                $id_sexos = implode(",",$id);
                $sexos = $sexo_dao->GetAllArray("id_sexo IN ($id_sexos)");
                $filtro_sexo = 1;
                
                //Para usar en el sql del reporte 7 - reporte general
                $cond_sql_sexo = " AND id_sexo IN ($id_sexos)";
            }
            else{
                $sexos = $sexo_dao->GetAllArray("");
            }
            
            //Filtro de condicion
            if (isset($_POST["id_condicion"])){
                $filtro_condicion = 1;
                $id = $_POST["id_condicion"];
                $id_condiciones = implode(",",$id);
                
                $cond = "id_condicion_mina IN ($id_condiciones)";
                $condiciones = $condicion_dao->GetAllArray($cond);
                
                //Para usar en el sql del reporte 7 - reporte general
                $cond_sql_condicion = " AND id_condicion IN ($id_condiciones)";
                
                if (isset($_POST["id_subcondicion"])){
                    $filtro_subcondicion = 1;
                    $id = $_POST["id_subcondicion"];
                    $id_subcondiciones = implode(",",$id);
                    
                    //Para usar en el sql del reporte 7 - reporte general
                    $cond_sql_subcondicion = " AND id_subcondicion IN ($id_subcondiciones)";
                    
                    $subcondiciones = $subcondicion_dao->GetAllArray("id_subcondicion IN ($id_subcondiciones)");
                }
                else{
                    $subcondiciones = $subcondicion_dao->GetAllArray("id_condicion IN ($id_condiciones)");
                }
            }
            else{
                $condiciones = $condicion_dao->GetAllArray('');
                $subcondiciones = array();
                foreach($condiciones as $i => $_c){
                    $_s = $subcondicion_dao->GetAllArray('id_condicion='.$_c->id);
                    if (count($_s) > 0) {
                        $subcondiciones = array_merge($subcondiciones, $_s);
                    }
                    else{
                        unset($condiciones[$i]);
                    }
                }
            }
            
            //Filtro de edad
            if (isset($_POST["id_edad"])){
                $filtro_edad = 1;
                $id = $_POST["id_edad"];
                $id_edades = implode(",",$id);
                
                $cond = "id_edad IN ($id_edades)";
                $edades = $edad_dao->GetAllArray($cond);
                
                //Para usar en el sql del reporte 7 - reporte general
                $cond_sql_edad = " AND id_edad IN ($id_edades)";
                
                if (isset($_POST["id_rango_edad"])){
                    $filtro_redad = 1;
                    $id = $_POST["id_rango_edad"];
                    $id_rango_edades = implode(",",$id);
                    
                    //Para usar en el sql del reporte 7 - reporte general
                    $cond_sql_redad = " AND id_raned IN ($id_rango_edades)";
                    
                    $rangos_edades = $rango_edad_dao->GetAllArray("id_raned IN ($id_rango_edades)");
                }
                else{
                    $rangos_edades = $rango_edad_dao->GetAllArray($cond);
                }
            }
            else{
                $edades = $edad_dao->GetAllArray('');
                $rangos_edades = $rango_edad_dao->GetAllArray('');
            }
            
            //Filtro de etnia
            if (isset($_POST["id_etnia"])){
                $filtro_etnia = 1;
                $id = $_POST["id_etnia"];
                $id_etnias = implode(",",$id);
                
                $cond = "id_etnia IN ($id_etnias)";
                $etnias = $etnia_dao->GetAllArray($cond);
                
                if (isset($_POST["id_subetnia"])){
                    $filtro_subetnia = 1;
                    $id = $_POST["id_subetnia"];
                    $id_subetnias = implode(",",$id);
                    
                    //Para usar en el sql del reporte 7 - reporte general
                    $cond_sql_setnia = " AND id_subetnia IN ($id_subetnias)";
                    
                    $subetnias = $sub_etnia_dao->GetAllArray("id_etnia IN ($id_subetnias)");
                }
                else{
                    $subetnias = $sub_etnia_dao->GetAllArray($cond);
                    $id_subetnias = implode(",",$sub_etnia_dao->GetAllArrayID($cond));
                }
            }
            else{
                $etnias = $etnia_dao->GetAllArray('');
                $subetnias = $sub_etnia_dao->GetAllArray('');
            }
            
            //Filtro de estado
            if (isset($_POST["id_estado"])){
                $id = $_POST["id_estado"];
                $id_estados = implode(",",$id);
                $estados = $estado_dao->GetAllArray("id_estado_mina IN ($id_estados)");
                $filtro_estado = 1;
                
                //Para usar en el sql del reporte 7 - reporte general
                $cond_sql_estado = " AND id_estado IN ($id_estados)";
                
            }
            else{
                $estados = $estado_dao->GetAllArray("");
            }
            
            //Filtro de ocupacion
            if (isset($_POST["id_ocupacion"])){
                $id = $_POST["id_ocupacion"];
                $id_ocupaciones = implode(",",$id);
                $ocupaciones = $ocupacion_dao->GetAllArray("id_ocupacion IN ($id_ocupaciones)");
                $filtro_ocupacion = 1;
                
                //Para usar en el sql del reporte 7 - reporte general
                $cond_sql_ocupacion = " AND id_ocupacion IN ($id_ocupaciones)";
                
            }
            else{
                $ocupaciones = $ocupacion_dao->GetAllArray("");
            }
            
            if ($reporte == 6){
            
                $sql_eventos = "SELECT DISTINCT evento_localizacion.id_mun 
                            FROM evento_localizacion 
                            INNER JOIN evento_c ON evento_localizacion.id_even = evento_c.id_even 
                            INNER JOIN municipio ON evento_localizacion.id_mun = municipio.id_mun 
                            INNER JOIN departamento ON municipio.id_depto = departamento.id_depto";
                
                if ($filtro_fecha == 1){
                    $sql_eventos .= " AND fecha_reg_even BETWEEN '$fecha_ini[0]' AND '$fecha_fin[0]'";
                }
                
                $sql_eventos .= " ORDER BY nom_depto,nom_mun";
                        
                $rs = $this->conn->OpenRecordset($sql_eventos);
                $m = 0;
                while ($row_rs = $this->conn->FetchRow($rs)){
                    $id_ubi[$m] = $row_rs[0];
                    $m++;
                }
            }
            else {
                //$sql_eventos = "SELECT * FROM evento_c INNER JOIN descripcion_evento ON evento_c.id_even = descripcion_evento.id_even INNER JOIN evento_localizacion ON evento_c.id_even = evento_localizacion.id_even INNER JOIN victima ON victima.id_deseven = descripcion_evento.id_deseven WHERE 1=1 ";
                $sql_eventos = "SELECT * FROM evento_c JOIN descripcion_evento USING(id_even) JOIN evento_localizacion USING(id_even) JOIN victima USING(id_deseven) WHERE 1=1 ";
    
                $cond_eventos = "";
                if (isset($_POST["id_cat"])){
                    $cond_eventos .= " AND id_scateven IN ($id_subcat_s)";
                }
                
                if (isset($_POST["id_depto"]) && !isset($_POST["id_muns"])){
                    $id_depto = $_POST["id_depto"];
                    $d = 0;
                    foreach ($id_depto as $id_d){
                        $id_depto[$d] = "'".$id_d."'";
                        $d++;
                    }
                    $id_depto_s = implode(",",$id_depto);
                    
                    $id_muns_s = $municipio_dao->GetIDWhere($id_depto_s);
                    
                    $cond_eventos .= " AND id_mun IN ($id_muns_s)";
                }
                else if (isset($_POST["id_depto"]) && isset($_POST["id_muns"])){
                    $arr_id_u_g = Array();
        
                    $id_muns = $_POST["id_muns"];
                    $m = 0;
                    foreach ($id_muns as $id_m){
                        $id_muns[$m] = "'".$id_m."'";
                        $m++;
                    }
        
                    $id_muns_s = implode(",",$id_muns);
                    
                    $cond_eventos .= " AND id_mun IN ($id_muns_s)";
                }
                
                if ($filtro_fecha == 1){
                    $cond_eventos .= " AND fecha_reg_even BETWEEN '$fecha_ini[0]' AND '$fecha_fin[0]'";
                }
                
                //Filtros Victimas
                if ($filtro_sexo == 1)  $cond_eventos .= $cond_sql_sexo;
                if ($filtro_condicion == 1) $cond_eventos .= $cond_sql_condicion;
                if ($filtro_subcondicion == 1)  $cond_eventos .= $cond_sql_subcondicion;
                if ($filtro_edad == 1)  $cond_eventos .= $cond_sql_edad;
                if ($filtro_redad == 1) $cond_eventos .= $cond_sql_redad;
                if ($filtro_subetnia == 1)  $cond_eventos .= $cond_sql_setnia;
                if ($filtro_estado == 1)    $cond_eventos .= $cond_sql_estado;
                if ($filtro_ocupacion == 1) $cond_eventos .= $cond_sql_ocupacion;
                
                    
                $sql_eventos .= $cond_eventos ." ORDER BY fecha_reg_even DESC";
                
                //echo $sql_eventos;
                $m = 1;  //Temporal para que entre en el if
            }
        }
        else if ($reporte == 4 || $reporte == 5){
            $m = 1;         
        }

        //echo $sql_eventos;

        echo "<table align='center' cellspacing='1' cellpadding='3' border=0 width='100%'>";
        if ($m > 0){
            echo "<tr><td><a href='javascript:history.back(-1)'><img src='images/back.gif' border=0>&nbsp;Regresar</a>";
            
            if ($reporte != 7)  echo "&nbsp;<a href=\"#\" onclick=\"document.getElementById('pdf').value = 2;reportStream('evento_c');return false;\"><img src='images/consulta/excel.gif' border=0 class='TipExport' title='Exportar a Excel::Se genera un archivo descargable en formato xls'>&nbsp;Exportar a Excel</a></td>";
            
            echo "</tr>";
        }
        echo "<tr><td align='center' class='titulo_lista' colspan=30>CONSULTA DE EVENTOS</td></tr>";
        echo "<tr><td><b>REPORTE: ".strtoupper($tit_reporte[$reporte])."</b></td></tr>";
        
        if (isset($_POST["id_cat"]) || $filtro_fecha == 1 || isset($_POST["id_depto"]) || $filtro_condicion == 1
            || $filtro_edad == 1 || $filtro_estado == 1 || $filtro_etnia == 1 || $filtro_ocupacion == 1){
        
            echo "<tr><td colspan=5>Consulta realizada aplicando los siguientes filtros:</td>";
            echo "<tr><td colspan=5>";
    
            //TITULO DE CATEGORIA
            if (isset($_POST["id_cat"])){
                echo "<img src='images/flecha.gif'> Categor&iacute;a: ";
                $t = 0;
                foreach($id_cat as $id){
                    $vo  = $cat_dao->Get($id);
                    if ($t == 0)    echo "<b>".$vo->nombre."</b>";
                    else            echo ", <b>".$vo->nombre."</b>";
                    $t++;
                }
                echo "<br>";
            }
            //FECHA
            if ($filtro_fecha == 1){
                echo "<img src='images/flecha.gif'> Fecha Desde: <b>".$fecha_ini[0]."</b> -- Fecha Hasta: <b>".$fecha_fin[0]."</b>";
                echo "<br>";
            }
            if ($filtro_sexo == 1){
                echo "<img src='images/flecha.gif'> Sexo: ";
                $t = 0;
                foreach ($sexos as $vo){
                    echo ($t == 0) ? "<b>".$vo->nombre."</b>" : ", <b>".$vo->nombre."</b>";
                    echo "<br>";
                    $t++;
                }
            }
            if ($filtro_condicion == 1){
                echo "<img src='images/flecha.gif'> Condici&oacute;n: ";
                $t = 0;
                foreach ($condiciones as $vo){
                    echo ($t == 0) ? "<b>".$vo->nombre."</b>" : ", <b>".$vo->nombre."</b>";
                    echo "<br>";
                    $t++;
                }
            }
            if ($filtro_subcondicion == 1){
                echo "<img src='images/flecha.gif'> Subcondici&oacute;n: ";
                $t = 0;
                foreach ($subcondiciones as $vo){
                    echo ($t == 0) ? "<b>".$vo->nombre."</b>" : ", <b>".$vo->nombre."</b>";
                    echo "<br>";
                    $t++;
                }
            }
            if ($filtro_edad == 1){
                echo "<img src='images/flecha.gif'> Grupo Etareo: ";
                $t = 0;
                foreach ($edades as $vo){
                    echo ($t == 0) ? "<b>".$vo->nombre."</b>" : ", <b>".$vo->nombre."</b>";
                    echo "<br>";
                    $t++;
                }
            }
            if ($filtro_redad == 1){
                echo "<img src='images/flecha.gif'> Rango de Edad: ";
                $t = 0;
                foreach ($rangos_edades as $vo){
                    echo ($t == 0) ? "<b>".$vo->nombre."</b>" : ", <b>".$vo->nombre."</b>";
                    echo "<br>";
                    $t++;
                }
            }
            if ($filtro_estado == 1){
                echo "<img src='images/flecha.gif'> Estado: ";
                $t = 0;
                foreach ($estados as $vo){
                    echo ($t == 0) ? "<b>".$vo->nombre."</b>" : ", <b>".$vo->nombre."</b>";
                    echo "<br>";
                    $t++;
                }
            }
            if ($filtro_etnia == 1){
                echo "<img src='images/flecha.gif'> Grupo Poblacional: ";
                $t = 0;
                foreach ($etnias as $vo){
                    echo ($t == 0) ? "<b>".$vo->nombre."</b>" : ", <b>".$vo->nombre."</b>";
                    echo "<br>";
                    $t++;
                }
            }
            if ($filtro_subetnia == 1){
                echo "<img src='images/flecha.gif'> Etnia: ";
                $t = 0;
                foreach ($subetnias as $vo){
                    echo ($t == 0) ? "<b>".$vo->nombre."</b>" : ", <b>".$vo->nombre."</b>";
                    echo "<br>";
                    $t++;
                }
            }
            if ($filtro_ocupacion == 1){
                echo "<img src='images/flecha.gif'> Ocupaci&oacute;n: ";
                $t = 0;
                foreach ($ocupaciones as $vo){
                    echo ($t == 0) ? "<b>".$vo->nombre."</b>" : ", <b>".$vo->nombre."</b>";
                    echo "<br>";
                    $t++;
                }
            }
            //TITULO DE DEPTO
            if (isset($_POST["id_depto"])){
                echo "<img src='images/flecha.gif'> Departamento: ";
                $t = 0;
                foreach($_POST["id_depto"] as $id_t){
                    $vo  = $depto_dao->Get($id_t);
                    if ($t == 0)    echo "<b>".$vo->nombre."</b>";
                    else            echo ", <b>".$vo->nombre."</b>";
                    $t++;
                }
                echo "<br>";
            }
            //TITULO DE MPIO
            if (isset($_POST["id_muns"])){
                echo "<img src='images/flecha.gif'> Municipio: ";
                $t = 0;
                foreach($_POST["id_muns"] as $id_t){
                    $vo  = $municipio_dao->Get($id_t);
                    if ($t == 0)    echo "<b>".$vo->nombre."</b>";
                    else            echo ", <b>".$vo->nombre."</b>";
                    $t++;
                }
                echo "<br>";
            }
            echo "</td>";
        }

        echo '</tr></table>';
        
        echo '<div style="overflow:auto; margin-top:5px; padding:3px; height:350px;">';
        echo "<table class='tabla_reportelist'>";
        
        $content = '';
        
        if ($m > 0){
            
            if ($reporte == 1 || $reporte == 2 || $reporte == 3 || $reporte == 6){
                
                $cols_loca = ($nivel_localizacion == 'deptal') ? 2 : 4;

                $content .= "<tr class='titulo_lista'><td class='titulo_lista_localizacion_evento' colspan='$cols_loca'>LOCALIZACION GEOGRAFICA</td>";
                
                if ($reporte == 1){
                    foreach ($cats as $vo){
                        //$num_subcats = $subcat_dao->numRecords("id_cateven = $vo->id");
                        $num_subcats = count($subtipos[$vo->id]);
                        $content .= "<td align='center' colspan=$num_subcats>$vo->nombre</td>";
                    }
                }
                
                else if ($reporte == 6){
                    switch ($cat_victima_localizacion){
                        case 'no':
                            foreach ($cats as $vo){
                                $num_subcats = $subcat_dao->numRecords("id_cateven = $vo->id");
                                $content .= "<td align='center' colspan=".($num_subcats*4).">$vo->nombre</td>";
                            }
                        break;
                        case 'sexo':
                            $num = $sexo_dao->numRecords('');
                            $content .= "<td align='center' colspan=$num>SEXO</td>";
                        break;
                        case 'condicion':
                            foreach ($condiciones as $vo){
                                $num = $subcondicion_dao->numRecords("id_condicion = $vo->id");
                                $content .= "<td align='center' colspan=$num>$vo->nombre</td>";
                            }
                        break;
                        case 'edad':
                            foreach ($edades as $vo){
                                $num = $rango_edad_dao->numRecords("id_edad = $vo->id");
                                $content .= "<td align='center' colspan=$num>$vo->nombre</td>";
                            }
                        break;
                        case 'ocupacion':
                            $num = $ocupacion_dao->numRecords('');
                            $content .= "<td align='center' colspan=$num>OCUPACION</td>";
                        break;
                        case 'estado':
                            $num = $estado_dao->numRecords('');
                            $content .= "<td align='center' colspan=$num>ESTADO</td>";
                        break;
                        case 'etnia':
                            foreach ($etnias as $vo){
                                $num = $subetnia_dao->numRecords("id_etnia = $vo->id");
                                $content .= "<td align='center' colspan=$num>$vo->nombre</td>";
                            }
                        break;
                    }
                }
                    
                $content .= "</tr>";
                
                if ($nivel_localizacion == 'deptal'){
                    $content .= "<tr class='fila_lista'><td class='titulo_lista_localizacion_evento'>COD. DEPTO</td><td class='titulo_lista_localizacion_evento'>Departamento</td>";
                }
                else{
                    $content .= "<tr class='fila_lista'><td class='titulo_lista_localizacion_evento'>COD. DEPTO</td><td class='titulo_lista_localizacion_evento'>Departamento</td><td class='titulo_lista_localizacion_evento'>COD. MPIO</td><td class='titulo_lista_localizacion_evento'>Municipio</td>";
                }
            }
                
            //DETALLE POR PERIODO
            if ($reporte == 4 || $reporte == 5){
                $content .= "<tr class='titulo_lista'><td><b>PERIODO</b></td>";
            }
            
            
            //LOC_GEOGRAFICA - CAT-SUBCAT
            if ($reporte == 1){
                foreach ($cats as $vo){
                //foreach ($subtipos as $vo){
                    foreach ($subtipos[$vo->id] as $subc){
                        $content .= "<td align='center'>$subc->nombre</td>";
                    }
                }
            }
            //LOC_GEOGRAFICA - ACTORES
            else if ($reporte == 2 || $reporte == 5){
                $a = 0;
                foreach ($id_actor1 as $id_actor){
                    $vo = $actor_dao->Get($id_actor);
                    $vo1 = $actor_dao->Get($id_actor2[$a]);
                    $content .= "<td align='center'><b>$vo->nombre</b> - <b>$vo1->nombre</b></td>";
                    
                    $a++;
                }
            }
            //LOC_GEOGRAFICA - PERIODOS
            else if ($reporte == 3){
                $a = 0;
                foreach ($fecha_ini as $f_ini){
                    $content .= "<td align='center'><b>$f_ini</b> - <b>$fecha_fin[$a]</b></td>";
                    
                    $a++;
                }
            }
            //PERIODO POR CATEGORIA
            else if ($reporte == 4){
                
                if ($filtro_cat == 0){
                    $vo_cats = $cat_dao->GetAllArray('');
                }
                foreach($vo_cats as $vo){
                    $content .= "<td align='center'>$vo->nombre</td>";
                }
            }
            
            else if ($reporte == 6){
                switch ($cat_victima_localizacion){
                    case 'no':
                        foreach ($cats as $cat){
                            foreach ($subtipos[$cat->id] as $vo){
                                $content .= "<td align='center' colspan='4'>$vo->nombre</td>";
                            }
                        }
                    break;
                    case 'sexo':
                        foreach ($sexos as $vo){
                            $content .= "<td align='center'>$vo->nombre</td>";
                        }
                    break;
                    case 'condicion':
                        foreach ($subcondiciones as $vo){
                            $content .= "<td align='center'>$vo->nombre</td>";
                        }
                    break;
                    case 'edad':
                        foreach ($rangos_edades as $vo){
                            $content .= "<td align='center'>$vo->nombre</td>";
                        }
                    break;
                    case 'etnia':
                        foreach ($subetnias as $vo){
                            $content .= "<td align='center'>$vo->nombre</td>";
                        }
                    break;
                    case 'estado':
                        foreach ($estados as $vo){
                            $content .= "<td align='center'>$vo->nombre</td>";
                        }
                    break;
                    case 'ocupacion':
                        foreach ($ocupaciones as $vo){
                            $content .= "<td align='center'>$vo->nombre</td>";
                        }
                    break;
                    
                }
            }
            else if ($reporte == 7){
                $this->ReportarDepuracion($cond_eventos);

                return;
            }
            else if ($reporte == 8){
                $this->ReportarDepuracionBoletin($cond_eventos);

                return;
            }
            
            $content .= "<tr>";
            
            if ($reporte == 6){
                if ($cat_victima_localizacion == 'no'){
                    $content .= "<tr>";
                    
                    if ($nivel_localizacion == 'deptal'){
                        $content .= "<td></td><td></td>";
                    }
                    else {
                        $content .= "<td></td><td></td><td></td><td></td>";
                    }
                    foreach ($cats as $cat){
                        foreach ($subtipos[$cat->id] as $vo){
                            $content .= "
                                <td align='center'>Civ</td>
                                <td align='center'>Ind</td>
                                <td align='center'>Afr</td>
                                <td align='center'>Total</td>
                                ";
                        }
                    }
                    $content .= "</tr>";
                }
            }
            
            
            if ($reporte == 1 || $reporte == 2 || $reporte == 3 || $reporte == 6){
            
                $hay = 1;
                $id_depto_ant = 0;
                foreach ($id_ubi as $id_ubi){
                    
                    $id_depto = substr($id_ubi,0,2);
                    
                    $esta = (!isset($_POST["id_muns"]) && !isset($_POST["id_depto"])) ? 1 : 0;
                    
                    if (isset($_POST["id_muns"]) && in_array($id_ubi,$_POST["id_muns"])){
                        $esta = 1;
                    }
                    else if (isset($_POST["id_depto"])){
                        
                        if (in_array($id_depto,$_POST["id_depto"])){
                            $esta = 1;
                        }
                    }
                    
                    if ($esta == 1){
                        
                        
                        $depto = $depto_dao->Get($id_depto);
                        $mun = $municipio_dao->Get($id_ubi);
                        
                        $id_depto_linea = $depto->id;
                        
                        if ($nivel_localizacion == 'mpal'){
                            $content .= "<td>$depto->id</td>";
                            $content .= "<td>$depto->nombre</td>";
                            $content .= "<td>$mun->nombre</td>";
                        }
                                                
                        //CAT-SUBCAT
                        if ($reporte == 1){
                            foreach ($cats as $cat){
                                foreach ($subtipos[$cat->id] as $vo){
                                    
                                    if ($id_depto_linea != $id_depto_ant){
                                        $total[$id_depto][$vo->id] = 0;
                                    }
                                    
                                    $num_eventos = $this->numEventosReporte($id_ubi,$vo->id,$id_actor_filtro,$fecha_ini[0],$fecha_fin[0]);
                                    if ($nivel_localizacion == 'mpal'){
                                        $content .= "<td align='center'>$num_eventos</td>";
                                    }
                                    else {
                                        $total[$id_depto][$vo->id] += $num_eventos;
                                    }
                                }
                            }
                        }
                        //CONFRONTACION DE ACTORES
                        else if ($reporte == 2){
                            $a = 0;
                            foreach ($id_actor1 as $id_actor){
                                $vo = $actor_dao->Get($id_actor);
                                $vo1 = $actor_dao->Get($id_actor2[$a]);
                                
                                if ($id_depto_linea != $id_depto_ant){
                                    $total[$id_depto][$vo->id] = 0;
                                }
                                
                                $num_eventos = $this->numEventosReporte($id_ubi,0,"$vo->id,$vo1->id",$fecha_ini[0],$fecha_fin[0]);
                                
                                if ($nivel_localizacion == 'mpal'){
                                    $content .= "<td align='center'>$num_eventos</td>";
                                }
                                else {
                                    $total[$id_depto][$vo->id] += $num_eventos;
                                }
                                
                                $a++;
                            }
                        }
                        //POR PERIODO DE TIEMPO
                        else if ($reporte == 3){
                            $a = 0;
                            foreach ($fecha_ini as $f_ini){
                                
                                if ($id_depto_linea != $id_depto_ant){
                                    $total[$id_depto][$a] = 0;
                                }
                                
                                $num_eventos = $this->numEventosReporte($id_ubi,0,$id_actor_filtro,$f_ini,$fecha_fin[$a]);
                                
                                if ($nivel_localizacion == 'mpal'){
                                    $content .= "<td align='center'>$num_eventos</td>";
                                }
                                else {
                                    $total[$id_depto][$a] += $num_eventos;
                                }
                                
                                $a++;
                            }
                        }
                        
                        //CONTEO DE VICTIMAS
                        else if ($reporte == 6){
                            
                            $a = 0;
                            $filtros['id_mun'] = $id_ubi;
                            $filtros['f_ini'] = $fecha_ini[0];
                            $filtros['f_fin'] = $fecha_fin[0];
                            
                            if ($cat_victima_localizacion != 'no') {
                                if ($filtro_cat == 1)   $filtros['id_scat'] = $id_subcat_s;
                                if ($filtro_actor == 1) $filtros['id_actor'] = $id_actor1_s;
                            }

                            if ($cat_victima_localizacion == 'no'){
                                
                                if ($filtro_sexo == 1)  $filtros['id_sexo'] = $id_sexos;
                                if ($filtro_condicion == 1) $filtros['id_condicion'] = $id_condiciones;
                                if ($filtro_subcondicion == 1)  $filtros['id_subcondicion'] = $id_subcondiciones;
                                if ($filtro_edad == 1)  $filtros['id_edad'] = $id_edades;
                                if ($filtro_redad == 1) $filtros['id_rango_edad'] = $id_rango_edades;
                                if ($filtro_etnia == 1) $filtros['id_etnia'] = $id_etnias;
                                if ($filtro_subetnia == 1)  $filtros['id_subetnia'] = $id_subetnias;
                                if ($filtro_estado == 1)    $filtros['id_estado'] = $id_estados;
                                if ($filtro_ocupacion == 1) $filtros['id_ocupacion'] = $id_ocupaciones;
                                
                                // Civiles => id_condicion = 2
                                // Afros => id_etnia = 2
                                // Indigenas => id_etnia = 1
                                $bg = "style='background-color: #BC2227;color:#ffffff;'";

                                $filtros['id_actor'] = $id_actor_filtro;
                                $total_mpal = 0;    
                                foreach ($cats as $cat){
                                    foreach ($subtipos[$cat->id] as $vo){

                                        $_id = $vo->id;
                                        
                                        if ($id_depto_linea != $id_depto_ant){
                                            $total[$id_depto][$_id] = 0;
                                            $total_d[$id_depto][$_id]['c'] = 0;
                                            $total_d[$id_depto][$_id]['a'] = 0;
                                            $total_d[$id_depto][$_id]['i'] = 0;
                                        }
                                        
                                        $filtros['id_scat'] = $vo->id;
                                        
                                        $num_victimas = $this->numVictimasReporte($id_ubi,$filtros);
                                        $total_mpal += $num_victimas;

                                        // Civiles
                                        $filtros_d = $filtros;
                                        $filtros_d['id_condicion'] = 2;
                                        $civiles = $this->numVictimasReporte($id_ubi,$filtros_d);
                                        
                                        // Indigenas
                                        $filtros_d = $filtros;
                                        $filtros_d['id_etnia'] = 1;
                                        $indigenas = $this->numVictimasReporte($id_ubi,$filtros_d);
                                        
                                        // Afro
                                        $filtros_d = $filtros;
                                        $filtros_d['id_etnia'] = 2;
                                        $afro = $this->numVictimasReporte($id_ubi,$filtros_d);
                                        
                                        if ($nivel_localizacion == 'mpal'){
                                            $content .= "
                                                <td align='center' ".(($civiles > 0) ? $bg : '').">$civiles</td>
                                                <td align='center' ".(($indigenas > 0) ? $bg : '').">$indigenas</td>
                                                <td align='center' ".(($afro > 0) ? $bg : '').">$afro</td>
                                                <td align='center' ".(($num_victimas > 0) ? $bg : '').">$num_victimas</td>
                                                
                                                ";
                                        }
                                        else {
                                            $total[$id_depto][$_id] += $num_victimas;
                                            $total_d[$id_depto][$_id]['c'] += $civiles;
                                            $total_d[$id_depto][$_id]['a'] += $afro;
                                            $total_d[$id_depto][$_id]['i'] += $indigenas;
                                        }
                                        
                                        $a++;
                                    }
                                }

                                if ($nivel_localizacion == 'mpal'){
                                    $content .= "<td align='center' ".(($total_mpal > 0) ? $bg : '').">$total_mpal</td>";
                                }
                            }
                            
                            else if ($cat_victima_localizacion == 'sexo'){
                                foreach ($sexos as $vo){
                                    
                                    if ($id_depto_linea != $id_depto_ant){
                                        $total[$id_depto][$vo->id] = 0;
                                    }
                                    
                                    $filtros['id_sexo'] = $vo->id;
                                    
                                    $num_victimas = $this->numVictimasReporte($id_ubi,$filtros);
                                    if ($nivel_localizacion == 'mpal'){
                                        $content .= "<td align='center'>$num_victimas</td>";
                                    }
                                    else {
                                        $total[$id_depto][$vo->id] += $num_victimas;
                                    }
                                }
                            }
                            
                            else if ($cat_victima_localizacion == 'condicion'){
                                foreach ($subcondiciones as $vo){
                                    
                                    if ($id_depto_linea != $id_depto_ant){
                                        $total[$id_depto][$vo->id] = 0;
                                    }
                                    
                                    $filtros['id_subcondicion'] = $vo->id;
                                    
                                    $num_victimas = $this->numVictimasReporte($id_ubi,$filtros);
                                    if ($nivel_localizacion == 'mpal'){
                                        $content .= "<td align='center'>$num_victimas</td>";
                                    }
                                    else {
                                        $total[$id_depto][$vo->id] += $num_victimas;
                                    }
                                }
                            }
                            
                            else if ($cat_victima_localizacion == 'etnia'){
                                foreach ($subetnias as $vo){
                                    
                                    if ($id_depto_linea != $id_depto_ant){
                                        $total[$id_depto][$vo->id] = 0;
                                    }
                                    
                                    $filtros['id_subetnia'] = $vo->id;
                                    
                                    $num_victimas = $this->numVictimasReporte($id_ubi,$filtros);
                                    if ($nivel_localizacion == 'mpal'){
                                        $content .= "<td align='center'>$num_victimas</td>";
                                    }
                                    else {
                                        $total[$id_depto][$vo->id] += $num_victimas;
                                    }
                                }
                            }
                            
                            else if ($cat_victima_localizacion == 'edad'){
                                foreach ($rangos_edades as $vo){
                                    
                                    if ($id_depto_linea != $id_depto_ant){
                                        $total[$id_depto][$vo->id] = 0;
                                    }
                                    
                                    $filtros['id_rango_edad'] = $vo->id;
                                    
                                    $num_victimas = $this->numVictimasReporte($id_ubi,$filtros);
                                    if ($nivel_localizacion == 'mpal'){
                                        $content .= "<td align='center'>$num_victimas</td>";
                                    }
                                    else {
                                        $total[$id_depto][$vo->id] += $num_victimas;
                                    }
                                }
                            }
                            
                            else if ($cat_victima_localizacion == 'estado'){
                                foreach ($estados as $vo){
                                    
                                    if ($id_depto_linea != $id_depto_ant){
                                        $total[$id_depto][$vo->id] = 0;
                                    }
                                    
                                    $filtros['id_estado'] = $vo->id;
                                    
                                    $num_victimas = $this->numVictimasReporte($id_ubi,$filtros);
                                    if ($nivel_localizacion == 'mpal'){
                                        $content .= "<td align='center'>$num_victimas</td>";
                                    }
                                    else {
                                        $total[$id_depto][$vo->id] += $num_victimas;
                                    }
                                }
                            }
                            
                            else if ($cat_victima_localizacion == 'ocupacion'){
                                foreach ($ocupaciones as $vo){
                                    
                                    if ($id_depto_linea != $id_depto_ant){
                                        $total[$id_depto][$vo->id] = 0;
                                    }
                                    
                                    $filtros['id_ocupacion'] = $vo->id;
                                    
                                    $num_victimas = $this->numVictimasReporte($id_ubi,$filtros);
                                    if ($nivel_localizacion == 'mpal'){
                                        $content .= "<td align='center'>$num_victimas</td>";
                                    }
                                    else {
                                        $total[$id_depto][$vo->id] += $num_victimas;
                                    }
                                }
                            }
                        }
                        
                        $id_depto_ant = $id_depto_linea;
                        
                        $hay++;
                        
                        if ($nivel_localizacion == 'mpal'){
                            $content .= "</tr>";
                        }
                        
                    }
                }
                
                if ($nivel_localizacion == 'deptal'){
                    
                    foreach ($total as $id_depto => $valores){
                        
                        $depto = $depto_dao->Get($id_depto);
                        $content .= "<tr class='fila_lista'>";
                        $content .= "<td>$depto->id</td>";
                        $content .= "<td>$depto->nombre</td>";
                    
                    //TOTAL PARA DEPTO
                        //CAT-SUBCAT
                        if ($reporte == 1){
                            foreach ($cats as $cat){
                                foreach ($subtipos[$cat->id] as $vo){
                                    $content .= "<td align='center'>".$valores[$vo->id]."</td>";
                                }
                            }
                        }
                        else if ($reporte == 2){
                            foreach ($id_actor1 as $id_actor){
                                $vo = $actor_dao->Get($id_actor);
                                $content .= "<td align='center'>".$valores[$vo->id]."</td>";
                            }
                        }
                        else if ($reporte == 3){
                            $a = 0;
                            foreach ($fecha_ini as $f_ini){
                                $content .= "<td align='center'>".$valores[$a]."</td>";
                                
                                $a++;
                            }
                        }
                        
                        //CONTEO DE VICTIMAS
                        else if ($reporte == 6){
                            
                            $a = 0;
                            $filtros['id_mun'] = $id_ubi;
                            $filtros['f_ini'] = $fecha_ini[0];
                            $filtros['f_fin'] = $fecha_fin[0];

                            if ($cat_victima_localizacion == 'no'){
                                
                                $total_depto = 0;
                                
                                //foreach ($subtipos as $vo){
                                foreach ($cats as $cat){
                                    foreach ($subtipos[$cat->id] as $vo){
                                        
                                        $_id = $vo->id;
                                        $civiles = $total_d[$id_depto][$_id]['c'];
                                        $indigenas = $total_d[$id_depto][$_id]['i'];
                                        $afro = $total_d[$id_depto][$_id]['a'];
                                        $tot = $valores[$_id];
                                        $total_depto += $tot;

                                        $content .= "
                                        <td align='center' ".(($civiles > 0) ? $bg : '').">$civiles</td>
                                        <td align='center' ".(($indigenas > 0) ? $bg : '').">$indigenas</td>
                                        <td align='center' ".(($afro > 0) ? $bg : '').">$afro</td>
                                        <td align='center' ".(($tot > 0) ? $bg : '').">".$tot."</td>
                                        ";
                                    }
                                }
                                $content .= "<td align='center' ".(($total_depto > 0) ? $bg : '').">$total_depto</td>";
                            }
                            else if ($cat_victima_localizacion == 'sexo'){
                                foreach ($sexos as $vo){
                                    $content .= "<td align='center'>".$valores[$vo->id]."</td>";
                                }
                            }
                            
                            else if ($cat_victima_localizacion == 'condicion'){
                                foreach ($subcondiciones as $vo){
                                    $content .= "<td align='center'>".$valores[$vo->id]."</td>";
                                }
                            }
                            
                            else if ($cat_victima_localizacion == 'etnia'){
                                foreach ($subetnias as $vo){
                                    $content .= "<td align='center'>".$valores[$vo->id]."</td>";
                                }
                            }
                            
                            else if ($cat_victima_localizacion == 'edad'){
                                foreach ($rangos_edades as $vo){
                                    $content .= "<td align='center'>".$valores[$vo->id]."</td>";
                                }
                            }
                            
                            else if ($cat_victima_localizacion == 'estado'){
                                foreach ($estados as $vo){
                                    $content .= "<td align='center'>".$valores[$vo->id]."</td>";
                                }
                            }
                            
                            else if ($cat_victima_localizacion == 'ocupacion'){
                                foreach ($ocupaciones as $vo){
                                    $content .= "<td align='center'>".$valores[$vo->id]."</td>";
                                }
                            }
                        }
                        
                        $content .= "</tr>";
                    }
                }
                
                
                if ($hay == 1){
                    $content .= "<tr><td align='center' colspan='10'><br><b>NO SE ENCONTRARON EVENTOS</b></td></tr>";
                }
            }
            //DETALLE POR PERIODO
            if ($reporte == 4 || $reporte == 5){
                
                $id_depto = array();
                $id_muns = array();

                if (isset($_POST["id_depto"]) && !isset($_POST["id_muns"])){
                    $id_depto = $_POST["id_depto"];
                    $d = 0;
                    foreach ($id_depto as $id_d){
                        $id_depto[$d] = "'".$id_d."'";
                        $d++;
                    }
                    
                    $id_depto_s = implode(",",$id_depto);
                    
                    $id_muns_s = $municipio_dao->GetIDWhere($id_depto_s);
                    $id_muns = explode(',', $id_muns_s);

                }
                else if (isset($_POST["id_depto"]) && isset($_POST["id_muns"])){
                    $arr_id_u_g = Array();

                    $id_muns = $_POST["id_muns"];
                    $m = 0;
                    foreach ($id_muns as $id_m){
                        $id_muns[$m] = "'".$id_m."'";
                        $m++;
                    }
                }

                $a = 0;
                foreach ($fecha_ini as $f_ini){
                    
                    $content .= "<tr class='fila_lista'><td>$f_ini - $fecha_fin[$a]</td>";
                    
                    //PERIODO POR CATEGORIA
                    if ($reporte == 4){
                        foreach ($vo_cats as $vo){
                            $ids = $subcat_dao->GetAllArrayID("id_cateven = ".$vo->id);
                            $num_eventos = 0;
                            foreach($ids as $id){
                                if (!empty($id_muns)) {
                                    foreach($id_muns as $id_mun) {
                                        $num_eventos += $this->numEventosReporte($id_mun,$id,0,$f_ini,$fecha_fin[$a]);
                                    } 
                                }
                                else {
                                    $num_eventos += $this->numEventosReporte(0,$id,0,$f_ini,$fecha_fin[$a]);
                                }
                            }
                            $content .= "<td align='center'>$num_eventos</td>";
                        }
                    }
                    //PERIODO POR ACTORES
                    else if ($reporte == 5){
                            $a = 0;
                            foreach ($id_actor1 as $id_actor){
                                $vo = $actor_dao->Get($id_actor);
                                $vo1 = $actor_dao->Get($id_actor2[$a]);
                                $num_eventos = $this->numEventosReporte(0,0,"$vo->id,$vo1->id",$f_ini,$fecha_fin[$a]);
                                $content .= "<td>$num_eventos</td>";
                                
                                $a++;
                            }
                        }
                    
                    $a++;   
                }
                
            }
            
            //$content = "</table>";
            
            echo $content;
            echo '</div>';

            $_SESSION["evento_c_xls"] = "<table border=1>".$content."</table>";
            echo "<input type='hidden' id='pdf' name='pdf'>";
//          echo "</form>";
        }
        else{
            echo "<tr><td align='center'><br><b>NO SE ENCONTRARON EVENTOS</b></td></tr>";
            echo "<tr><td align='center'><br><a href='javascript:history.back();'>Regresar</a></td></tr>";
            die;
        }
    }

    /**
    * Lista los Eventos en Excel para depuracion
    * @param string $sql Condicion SQL cuando se ejecuta este reporte desde el método Reportar()
    * @access public
    */          
    function ReportarDepuracion($sql=''){

        set_time_limit(0);
        ini_set ( "memory_limit", "64M");

        //INICIALIZACION DE VARIABLES
        $evento_vo = New EventoConflicto();
        $evento_dao = New EventoConflictoDAO();
        $municipio_vo = New Municipio();
        $municipio_dao = New MunicipioDAO();
        $depto_vo = New Depto();
        $depto_dao = New DeptoDAO();
        $actor_vo = New Actor();
        $actor_dao = New ActorDAO();
        $fuente_vo = New FuenteEventoConflicto();
        $fuente_dao = New FuenteEventoConflictoDAO();
        $subfuente_vo = New SubFuenteEventoConflicto();
        $subfuente_dao = New SubFuenteEventoConflictoDAO();
        $cat_vo = New CatEventoConflicto();
        $cat_dao = New CatEventoConflictoDAO();
        $subcat_vo = New SubCatEventoConflicto();
        $subcat_dao = New SubCatEventoConflictoDAO();
        $edad_dao = New EdadDAO();
        $rango_edad_dao = New RangoEdadDAO();
        $estado_dao = New EstadoMinaDAO();
        $condicion_dao = New CondicionMinaDAO();
        $subcondicion_dao = New SubCondicionDAO();
        $sexo_dao = New SexoDAO();
        $etnia_dao = New EtniaDAO();
        $sub_etnia_dao = New SubEtniaDAO();
        $subetnia_dao = New SubetniaDAO();
        $ocupacion_dao = New OcupacionDAO();
        $archivo = New Archivo();
        
        $f_ini = (isset($_POST["f_ini"]) && $_POST["f_ini"][0] != '') ? $_POST["f_ini"][0] : "";
        $f_fin = (isset($_POST["f_fin"]) && $_POST["f_fin"][0] != '') ? $_POST["f_fin"][0] : "";

        $filtro_fecha = 0;
        if ($f_ini != '' && $f_fin != ''){
            $filtro_fecha = 1;  
        }
        
        //$fecha_ini = $_POST["f_ini"];
        //$fecha_fin = $_POST["f_fin"];
        
        if ($sql == ''){
            $sql_eventos = "SELECT DISTINCT evento_c.ID_EVEN, FECHA_REG_EVEN, FECHA_ING_EVEN,SINTESIS_EVEN FROM evento_c LEFT JOIN descripcion_evento USING(id_even) JOIN evento_localizacion USING(id_even) LEFT JOIN victima USING(id_deseven)";
    
            if ($filtro_fecha == 1){
                $sql_eventos .= " WHERE fecha_reg_even BETWEEN '$f_ini' AND '$f_fin'";
            }
                
            $sql_eventos .= " ORDER BY fecha_reg_even DESC";
        }
        else{
            $sql_eventos = "SELECT DISTINCT evento_c.ID_EVEN, FECHA_REG_EVEN, FECHA_ING_EVEN,SINTESIS_EVEN FROM evento_c LEFT JOIN descripcion_evento USING(id_even) JOIN evento_localizacion USING(id_even) LEFT JOIN victima USING(id_deseven) 
                            WHERE 1=1 $sql";
        }
        
        //die($sql_eventos);
        
        $rs_eventos = $this->conn->OpenRecordset($sql_eventos);
        $m = $this->conn->RowCount($rs_eventos);

        echo "<table align='center' cellspacing='1' cellpadding='3' border=0 width='100%'>";
        //$content = "<table align='center' cellspacing='1' cellpadding='3' border=0 width='100%'>";
        
        if ($m > 0){
            
            $cols_descs = $this->getMaxDescsEvento($f_ini,$f_fin);
            $cols_actores = $this->getMaxActoresEvento($f_ini,$f_fin);
            $cols_victimas = $this->getMaxVictimasEvento($f_ini,$f_fin);
            
            //$content .= "<tr><td colspan=3>";
            
            $content = "<table border=1 height='100%'><tr>";
            if ($sql == '') $content .= "<td>ID del Evento</td>";
            $content .= "<td>FECHA_ENTRADA_REGISTRO</td>
                    <td>FECHA EVENTO</td>";
            
            if ($sql == '') $content .= "<td>RESUMEN_EVENTO</td>";
            
            for ($cl=1;$cl<=$cols_descs;$cl++){
                $content .= "<td>CATEGORIA_$cl</td>
                    <td>SUBCATEGORIA_$cl</td>";
            }

            for ($cl=1;$cl<=$cols_actores;$cl++){
                $content .= "
                    <td>ACTOR/PRESUNTO_PERPETRADOR_NIVEL_0_$cl</td>
                    <td>ACTOR/PRESUNTO_PERPETRADOR_$cl</td>
                    <td>SUB_ACTOR/PRESUNTO_PERPETRADOR_$cl</td>
                    <td>SUB_SUB_ACTOR/PRESUNTO_PERPETRADOR_$cl</td>";
            }
            
            for ($cl=1;$cl<=$cols_victimas;$cl++){
                $content .= "<td>VICTIMAS_$cl</td>
                    <td>GRUPO_ETAREO_$cl</td>
                    <td>CONDICIÓN_$cl</td>
                    <td>SUB_CONDICION_$cl</td>
                    <td>GRUPO_POBLACIONAL_$cl</td>
                    <td>OCUPACION_$cl</td>
                    <td>RANGO_DE_EDAD_$cl</td>
                    <td>ESTADO_$cl</td>
                    <td>SEXO_$cl</td>
                    <td>SUB_ETNIA_$cl</td>";
            }
            
            if ($sql == '') $content .= "<td>TIPO_DE_FUENTE</td>";
            
            $content .= "<td>FUENTE</td>
                    <td>DESCRIPCIÓN</td>
                    <td>FECHA_FUENTE</td>
                    <td>REFERENCIA</td>
                    <td>DEPARTAMENTO</td>
                    <td>COD_DANE_DEPTO</td>
                    <td>MUNICIPIO</td>
                    <td>COD_DANE_MPIO</td>
                    <td>LUGAR</td>";
            
            $content .= "</tr>";

            $r = 0;
            while ($row_rs = $this->conn->FetchObject($rs_eventos)){
                
                $id = $row_rs->ID_EVEN;

                //Descripciones
                $desc_evento = $evento_dao->getDescripcionEvento($id);
                
                //Fuentes
                $fuentes = $evento_dao->getFuenteEvento($id);
                $num_fuentes = $fuentes['num'];
                
                //Localizaciones
                $locas = $evento_dao->getLocalizacionEvento($id);
                $num_locas = $locas['num'];
                
                    
                $content .= "<tr>";
                
                if ($sql == '') $content .= "<td>$row_rs->ID_EVEN</td>";
                
                $content .= "<td>$row_rs->FECHA_ING_EVEN</td>";
                $content .= "<td>$row_rs->FECHA_REG_EVEN</td>";
                if ($sql == '') $content .= "<td>$row_rs->SINTESIS_EVEN</td>";
                
                //for ($i=0;$i<=2*$cols_descs-2;$i++){
                for ($i=0;$i<$cols_descs;$i++){
                    if (isset($desc_evento['id_cat'][$i])){
                        $content .= "<td>".$desc_evento['nom_cat'][$i]."</td>";
                        $content .= "<td>".$desc_evento['nom_scat'][$i]."</td>";
                    }
                    else{
                        for($v=0;$v<2;$v++){
                            $content .= "<td></td>";
                        }
                    }
                    //$i++;
                }

                $celdas = 0;
                $num_cols_actor_total = $cols_actores*4;
                for ($i=0;$i<$num_cols_actor_total;$i++){
                    if (isset($desc_evento['id'][$i])){
                        $id_desevento = $desc_evento['id'][$i];
                        
                        //ACTORES NIVEL 0
                        $array_actores_0 = $this->getActorEvento($id_desevento,0);
                        $actores_0 = $array_actores_0["nombre"];
                        
                        //ACTORES
                        $array_actores = $this->getActorEvento($id_desevento,1);
                        $actores = $array_actores["nombre"];
        
                        //SUB ACTORES
                        $array_actores = $this->getActorEvento($id_desevento,2);
                        $sub_actores = $array_actores["nombre"];
        
                        //SUB SUB ACTORES
                        $array_actores = $this->getActorEvento($id_desevento,3);
                        $sub_sub_actores = $array_actores["nombre"];

                        // Actores nivel 0
                        if (count($actores_0) > 0){
                            $content .= "<td>";
                            $celdas++;
                        }
                        foreach($actores_0 as $ac){
                            $content .= "$ac-";
                        }
                        if (count($actores_0) > 0)  $content .= "</td>";
                        // Fin Actores nivel 0

                        // Actores
                        if (count($actores) > 0){
                            $content .= "<td>";
                            $celdas++;
                        }
                        foreach($actores as $ac){
                            $content .= "$ac-";
                        }
                        if (count($actores) > 0)    $content .= "</td>";
                        // Fin Actores
                        
                        // Sub Actores
                        if (count($sub_actores) > 0){
                            $content .= "<td>";
                            $celdas++;
                        }
                        foreach($sub_actores as $ac){
                            $content .= "$ac-";
                        }
                        if (count($sub_actores) > 0)    $content .= "</td>";
                        // Fin Sub Actores

                        // Sub Sub Actores
                        if (count($sub_sub_actores) > 0){
                            $content .= "<td>";
                            $celdas++;
                        }
                        foreach($sub_sub_actores as $ac){
                            $content .= "$ac-";
                        }
                        if (count($sub_sub_actores) > 0)    $content .= "</td>";
                        // Fin Sub Sub Actores
                        
                    }
                }
                
                //Completa las celdas de actores
                for ($c=$celdas;$c<$num_cols_actor_total;$c++){
                //  $content .= "<td>Llenando A</td>";
                    $content .= "<td></td>";
                }

                $celdas = 0;
                $num_cols_vict_total = 10*$cols_victimas;
                foreach($desc_evento['id'] as $id_deseven){ 
                    $victimas = $evento_dao->getVictimaDescripcionEvento($id_deseven);
                    $num_vict_x_desc = $victimas['num'];
        
                    if ($celdas < $num_cols_vict_total){
                        if ($num_vict_x_desc > 0){
    
                            for ($i=0;$i<$num_vict_x_desc;$i++){
            
                                if ($celdas < $num_cols_vict_total){
                                    $content .= "<td>".$victimas['cant'][$i]."</td>";
                                    
                                    if (isset($victimas['nom_edad'][$i])){
                                        $content .= "<td>".$victimas['nom_edad'][$i]."</td>";
                                    }
                                    else{
                                        $content .= "<td></td>";
                                    }
                                    
                                    if (isset($victimas['nom_condicion'][$i])){
                                        $content .= "<td>".$victimas['nom_condicion'][$i]."</td>";
                                    }
                                    else{
                                        $content .= "<td></td>";
                                    }
                                    
                                    if (isset($victimas['nom_scondicion'][$i])){
                                        $content .= "<td>".$victimas['nom_scondicion'][$i]."</td>";
                                    }
                                    else{
                                        $content .= "<td></td>";
                                    }
                        
                                    if (isset($victimas['nom_etnia'][$i])){
                                        $content .= "<td>".$victimas['nom_etnia'][$i]."</td>";
                                    }
                                    else{
                                        $content .= "<td></td>";
                                    }
        
                                    if (isset($victimas['nom_ocupacion'][$i])){
                                        $content .= "<td>".$victimas['nom_ocupacion'][$i]."</td>";
                                    }
                                    else{
                                        $content .= "<td></td>";
                                    }
        
                                    if (isset($victimas['nom_redad'][$i])){
                                        $content .= "<td>".$victimas['nom_redad'][$i]."</td>";
                                    }
                                    else{
                                        $content .= "<td></td>";
                                    }
                                    
                                    if (isset($victimas['nom_estado'][$i])){
                                        $content .= "<td>".$victimas['nom_estado'][$i]."</td>";
                                    }
                                    else{
                                        $content .= "<td></td>";
                                    }
                                    if (isset($victimas['nom_sexo'][$i])){
                                        $content .= "<td>".$victimas['nom_sexo'][$i]."</td>";
                                    }
                                    else{
                                        $content .= "<td></td>";
                                    }
                                    if (isset($victimas['nom_setnia'][$i])){
                                        $content .= "<td>".$victimas['nom_setnia'][$i]."</td>";
                                    }
                                    else{
                                        $content .= "<td></td>";
                                    }
                                    
                                    $celdas += 10;
                                }
                            }
                        }
                        else{
                            for($v=0;$v<10;$v++){
                                $content .= "<td></td>";
                                $celdas++;
                            }
                        }
                    }
                }
                
                //Completa las celdas de victimas
                for ($c=$celdas;$c<$num_cols_vict_total;$c++){
//                  $content .= "<td>Llenando V</td>";
                    $content .= "<td></td>";
                }
                
                $cols_fuente = ($sql == '') ? 5 : 4;
                if (isset($fuentes['nom_fuente'][0])){
                    if ($sql == ''){
                        $content .= "<td>".$fuentes['nom_fuente'][0]."</td>";
                    }
                    $content .= "<td>".$fuentes['nom_sfuente'][0]."</td>";
                    $content .= "<td>".$fuentes['desc'][0]."</td>";
                    $content .= "<td>".$fuentes['fecha'][0]."</td>";
                    $content .= "<td>".$fuentes['medio'][0]."</td>";
                }
                else{
                    for($v=0;$v<$cols_fuente;$v++){
                        $content .= "<td></td>";
                    }
                }
                
                $id_mun = $locas['mpios'][0];
                if ($id_mun > 0){
                    $mun = $municipio_dao->Get($id_mun);
                    $depto = $depto_dao->Get($mun->id_depto);
                    
                    $content .= "<td>$depto->nombre</td>";
                    $content .= "<td>$depto->id</td>";
                    $content .= "<td>$mun->nombre</td>";
                    $content .= "<td>$mun->id</td>";
                    $content .= "<td>".$locas['lugar'][0]."</td>";
                }
                else{
                    for($v=0;$v<5;$v++){
                        $content .= "<td></td>";
                    }
                }
                
                $content .= "</tr>";
                $r++;
            }
        }
        
        else{
            echo "<tr><td align='center'><br><b>NO SE ENCONTRARON EVENTOS</b></td></tr>";
            echo "<tr><td align='center'><br><a href='javascript:history.back();'>Regresar</a></td></tr>";
            die;
        }


        $f = "/sissh/admin/evento_c/reporte_eventos.";
        $root = $_SERVER['DOCUMENT_ROOT'];
        $_f = $root.$f;

        $file_html = $_f.'html';
        $file_xls = $_f.'xlsx';
        $file_zip = $_f.'zip';
        
        $fp = $archivo->Abrir($file_html,"w+");
        $archivo->Escribir($fp,$content);
        $archivo->Cerrar($fp);
        
        include 'admin/lib/common/phpexcel/PHPExcel/IOFactory.php';

        $inputFileType = 'HTML';
        $outputFileType = 'Excel2007';

        $objPHPExcelReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objPHPExcelReader->load($file_html);

        $objPHPExcelWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,$outputFileType);
        $objPHPExcel = $objPHPExcelWriter->save($file_xls);

        exec("zip -j $file_zip $file_xls");
        
        $size = ceil(filesize($file_xls) / 1000);
        $size_zip = ceil(filesize($file_zip) / 1000);
        
        echo "<tr><td><img src='/sissh/admin/images/excel.gif'>&nbsp;<a href='".$f."xlsx'>Descargar excel</a>&nbsp;[ Tamaño: ".$size." kB ] [ <b>$m Eventos Reportados</b> ]";
        echo "<tr><td><img src='/sissh/admin/images/zip.png'>&nbsp;<a href='".$f."zip'>Descargar Archivo ZIP</a>&nbsp;[ Tamaño: ".$size_zip." kB ]";
        echo "</table>";
    }
        
    /**
    * Lista los Eventos en Excel para depuracion, con la info de victimas luego de cada categoria
    * @param string $sql Condicion SQL cuando se ejecuta este reporte desde el método Reportar()
    * @access public
    */          
    function ReportarDepuracionBoletin($sql=''){

        set_time_limit(0);
        ini_set ( "memory_limit", "64M");

        //INICIALIZACION DE VARIABLES
        $evento_vo = New EventoConflicto();
        $evento_dao = New EventoConflictoDAO();
        $municipio_vo = New Municipio();
        $municipio_dao = New MunicipioDAO();
        $depto_vo = New Depto();
        $depto_dao = New DeptoDAO();
        $actor_vo = New Actor();
        $actor_dao = New ActorDAO();
        $fuente_vo = New FuenteEventoConflicto();
        $fuente_dao = New FuenteEventoConflictoDAO();
        $subfuente_vo = New SubFuenteEventoConflicto();
        $subfuente_dao = New SubFuenteEventoConflictoDAO();
        $cat_vo = New CatEventoConflicto();
        $cat_dao = New CatEventoConflictoDAO();
        $subcat_vo = New SubCatEventoConflicto();
        $subcat_dao = New SubCatEventoConflictoDAO();
        $edad_dao = New EdadDAO();
        $rango_edad_dao = New RangoEdadDAO();
        $estado_dao = New EstadoMinaDAO();
        $condicion_dao = New CondicionMinaDAO();
        $subcondicion_dao = New SubCondicionDAO();
        $sexo_dao = New SexoDAO();
        $etnia_dao = New EtniaDAO();
        $sub_etnia_dao = New SubEtniaDAO();
        $subetnia_dao = New SubetniaDAO();
        $ocupacion_dao = New OcupacionDAO();
        $archivo = New Archivo();
        
        $f_ini = (isset($_POST["f_ini"]) && $_POST["f_ini"][0] != '') ? $_POST["f_ini"][0] : "";
        $f_fin = (isset($_POST["f_fin"]) && $_POST["f_fin"][0] != '') ? $_POST["f_fin"][0] : "";

        $filtro_fecha = 0;
        if ($f_ini != '' && $f_fin != ''){
            $filtro_fecha = 1;  
        }
        
        //$fecha_ini = $_POST["f_ini"];
        //$fecha_fin = $_POST["f_fin"];
        
        if ($sql == ''){
            $sql_eventos = "SELECT DISTINCT evento_c.ID_EVEN, FECHA_REG_EVEN, FECHA_ING_EVEN,SINTESIS_EVEN FROM evento_c LEFT JOIN descripcion_evento USING(id_even) JOIN evento_localizacion USING(id_even) LEFT JOIN victima USING(id_deseven)";
    
            if ($filtro_fecha == 1){
                $sql_eventos .= " WHERE fecha_reg_even BETWEEN '$f_ini' AND '$f_fin'";
            }
                
            $sql_eventos .= " ORDER BY fecha_reg_even DESC";
        }
        else{
            $sql_eventos = "SELECT DISTINCT evento_c.ID_EVEN, FECHA_REG_EVEN, FECHA_ING_EVEN,SINTESIS_EVEN FROM evento_c LEFT JOIN descripcion_evento USING(id_even) JOIN evento_localizacion USING(id_even) LEFT JOIN victima USING(id_deseven) 
                            WHERE 1=1 $sql";
        }
        
        //die($sql_eventos);
        
        $rs_eventos = $this->conn->OpenRecordset($sql_eventos);
        $m = $this->conn->RowCount($rs_eventos);

        echo "<table align='center' cellspacing='1' cellpadding='3' border=0 width='100%'>";
        //$content = "<table align='center' cellspacing='1' cellpadding='3' border=0 width='100%'>";
        
        if ($m > 0){
            
            $cols_descs = $this->getMaxDescsEvento($f_ini,$f_fin);
            $cols_actores = $this->getMaxActoresEvento($f_ini,$f_fin);
            $cols_victimas = $this->getMaxVictimasEvento($f_ini,$f_fin);
            
            //$content .= "<tr><td colspan=3>";
            
            $content = "<table border=1 height='100%'><tr>";
            if ($sql == '') $content .= "<td>ID del Evento</td>";
            $content .= "<td>FECHA_ENTRADA_REGISTRO</td>
                    <td>FECHA EVENTO</td>";
            
            if ($sql == '') $content .= "<td>RESUMEN_EVENTO</td>";
            
            for ($cl=1;$cl<=$cols_descs;$cl++){
                $content .= "<td>CATEGORIA_$cl</td>
                    <td>SUBCATEGORIA_$cl</td>";
            }

            for ($cl=1;$cl<=$cols_actores;$cl++){
                $content .= "
                    <td>ACTOR/PRESUNTO_PERPETRADOR_NIVEL_0_$cl</td>
                    <td>ACTOR/PRESUNTO_PERPETRADOR_$cl</td>
                    <td>SUB_ACTOR/PRESUNTO_PERPETRADOR_$cl</td>
                    <td>SUB_SUB_ACTOR/PRESUNTO_PERPETRADOR_$cl</td>";
            }
            
            for ($cl=1;$cl<=$cols_victimas;$cl++){
                $content .= "<td>VICTIMAS_$cl</td>
                    <td>GRUPO_ETAREO_$cl</td>
                    <td>CONDICIÓN_$cl</td>
                    <td>SUB_CONDICION_$cl</td>
                    <td>GRUPO_POBLACIONAL_$cl</td>
                    <td>OCUPACION_$cl</td>
                    <td>RANGO_DE_EDAD_$cl</td>
                    <td>ESTADO_$cl</td>
                    <td>SEXO_$cl</td>
                    <td>SUB_ETNIA_$cl</td>";
            }
            
            if ($sql == '') $content .= "<td>TIPO_DE_FUENTE</td>";
            
            $content .= "<td>FUENTE</td>
                    <td>DESCRIPCIÓN</td>
                    <td>FECHA_FUENTE</td>
                    <td>REFERENCIA</td>
                    <td>DEPARTAMENTO</td>
                    <td>COD_DANE_DEPTO</td>
                    <td>MUNICIPIO</td>
                    <td>COD_DANE_MPIO</td>
                    <td>LUGAR</td>";
            
            $content .= "</tr>";

            $r = 0;
            while ($row_rs = $this->conn->FetchObject($rs_eventos)){
                
                $id = $row_rs->ID_EVEN;

                //Descripciones
                $desc_evento = $evento_dao->getDescripcionEvento($id);
                
                //Fuentes
                $fuentes = $evento_dao->getFuenteEvento($id);
                $num_fuentes = $fuentes['num'];
                
                //Localizaciones
                $locas = $evento_dao->getLocalizacionEvento($id);
                $num_locas = $locas['num'];
                
                    
                $content .= "<tr>";
                
                if ($sql == '') $content .= "<td>$row_rs->ID_EVEN</td>";
                
                $content .= "<td>$row_rs->FECHA_ING_EVEN</td>";
                $content .= "<td>$row_rs->FECHA_REG_EVEN</td>";
                if ($sql == '') $content .= "<td>$row_rs->SINTESIS_EVEN</td>";
                
                //for ($i=0;$i<=2*$cols_descs-2;$i++){
                for ($i=0;$i<$cols_descs;$i++){
                    if (isset($desc_evento['id_cat'][$i])){
                        $content .= "<td>".$desc_evento['nom_cat'][$i]."</td>";
                        $content .= "<td>".$desc_evento['nom_scat'][$i]."</td>";
                    }
                    else{
                        for($v=0;$v<2;$v++){
                            $content .= "<td></td>";
                        }
                    }
                    //$i++;
                }

                $celdas = 0;
                $num_cols_actor_total = $cols_actores*4;
                for ($i=0;$i<$num_cols_actor_total;$i++){
                    if (isset($desc_evento['id'][$i])){
                        $id_desevento = $desc_evento['id'][$i];
                        
                        //ACTORES NIVEL 0
                        $array_actores_0 = $this->getActorEvento($id_desevento,0);
                        $actores_0 = $array_actores_0["nombre"];
                        
                        //ACTORES
                        $array_actores = $this->getActorEvento($id_desevento,1);
                        $actores = $array_actores["nombre"];
        
                        //SUB ACTORES
                        $array_actores = $this->getActorEvento($id_desevento,2);
                        $sub_actores = $array_actores["nombre"];
        
                        //SUB SUB ACTORES
                        $array_actores = $this->getActorEvento($id_desevento,3);
                        $sub_sub_actores = $array_actores["nombre"];

                        // Actores nivel 0
                        if (count($actores_0) > 0){
                            $content .= "<td>";
                            $celdas++;
                        }
                        foreach($actores_0 as $ac){
                            $content .= "$ac-";
                        }
                        if (count($actores_0) > 0)  $content .= "</td>";
                        // Fin Actores nivel 0

                        // Actores
                        if (count($actores) > 0){
                            $content .= "<td>";
                            $celdas++;
                        }
                        foreach($actores as $ac){
                            $content .= "$ac-";
                        }
                        if (count($actores) > 0)    $content .= "</td>";
                        // Fin Actores
                        
                        // Sub Actores
                        if (count($sub_actores) > 0){
                            $content .= "<td>";
                            $celdas++;
                        }
                        foreach($sub_actores as $ac){
                            $content .= "$ac-";
                        }
                        if (count($sub_actores) > 0)    $content .= "</td>";
                        // Fin Sub Actores

                        // Sub Sub Actores
                        if (count($sub_sub_actores) > 0){
                            $content .= "<td>";
                            $celdas++;
                        }
                        foreach($sub_sub_actores as $ac){
                            $content .= "$ac-";
                        }
                        if (count($sub_sub_actores) > 0)    $content .= "</td>";
                        // Fin Sub Sub Actores
                        
                    }
                }
                
                //Completa las celdas de actores
                for ($c=$celdas;$c<$num_cols_actor_total;$c++){
                //  $content .= "<td>Llenando A</td>";
                    $content .= "<td></td>";
                }

                $celdas = 0;
                $num_cols_vict_total = 10*$cols_victimas;
                foreach($desc_evento['id'] as $id_deseven){ 
                    $victimas = $evento_dao->getVictimaDescripcionEvento($id_deseven);
                    $num_vict_x_desc = $victimas['num'];
        
                    if ($celdas < $num_cols_vict_total){
                        if ($num_vict_x_desc > 0){
    
                            for ($i=0;$i<$num_vict_x_desc;$i++){
            
                                if ($celdas < $num_cols_vict_total){
                                    $content .= "<td>".$victimas['cant'][$i]."</td>";
                                    
                                    if (isset($victimas['nom_edad'][$i])){
                                        $content .= "<td>".$victimas['nom_edad'][$i]."</td>";
                                    }
                                    else{
                                        $content .= "<td></td>";
                                    }
                                    
                                    if (isset($victimas['nom_condicion'][$i])){
                                        $content .= "<td>".$victimas['nom_condicion'][$i]."</td>";
                                    }
                                    else{
                                        $content .= "<td></td>";
                                    }
                                    
                                    if (isset($victimas['nom_scondicion'][$i])){
                                        $content .= "<td>".$victimas['nom_scondicion'][$i]."</td>";
                                    }
                                    else{
                                        $content .= "<td></td>";
                                    }
                        
                                    if (isset($victimas['nom_etnia'][$i])){
                                        $content .= "<td>".$victimas['nom_etnia'][$i]."</td>";
                                    }
                                    else{
                                        $content .= "<td></td>";
                                    }
        
                                    if (isset($victimas['nom_ocupacion'][$i])){
                                        $content .= "<td>".$victimas['nom_ocupacion'][$i]."</td>";
                                    }
                                    else{
                                        $content .= "<td></td>";
                                    }
        
                                    if (isset($victimas['nom_redad'][$i])){
                                        $content .= "<td>".$victimas['nom_redad'][$i]."</td>";
                                    }
                                    else{
                                        $content .= "<td></td>";
                                    }
                                    
                                    if (isset($victimas['nom_estado'][$i])){
                                        $content .= "<td>".$victimas['nom_estado'][$i]."</td>";
                                    }
                                    else{
                                        $content .= "<td></td>";
                                    }
                                    if (isset($victimas['nom_sexo'][$i])){
                                        $content .= "<td>".$victimas['nom_sexo'][$i]."</td>";
                                    }
                                    else{
                                        $content .= "<td></td>";
                                    }
                                    if (isset($victimas['nom_setnia'][$i])){
                                        $content .= "<td>".$victimas['nom_setnia'][$i]."</td>";
                                    }
                                    else{
                                        $content .= "<td></td>";
                                    }
                                    
                                    $celdas += 10;
                                }
                            }
                        }
                        else{
                            for($v=0;$v<10;$v++){
                                $content .= "<td></td>";
                                $celdas++;
                            }
                        }
                    }
                }
                
                //Completa las celdas de victimas
                for ($c=$celdas;$c<$num_cols_vict_total;$c++){
//                  $content .= "<td>Llenando V</td>";
                    $content .= "<td></td>";
                }
                
                $cols_fuente = ($sql == '') ? 5 : 4;
                if (isset($fuentes['nom_fuente'][0])){
                    if ($sql == ''){
                        $content .= "<td>".$fuentes['nom_fuente'][0]."</td>";
                    }
                    $content .= "<td>".$fuentes['nom_sfuente'][0]."</td>";
                    $content .= "<td>".$fuentes['desc'][0]."</td>";
                    $content .= "<td>".$fuentes['fecha'][0]."</td>";
                    $content .= "<td>".$fuentes['medio'][0]."</td>";
                }
                else{
                    for($v=0;$v<$cols_fuente;$v++){
                        $content .= "<td></td>";
                    }
                }
                
                $id_mun = $locas['mpios'][0];
                if ($id_mun > 0){
                    $mun = $municipio_dao->Get($id_mun);
                    $depto = $depto_dao->Get($mun->id_depto);
                    
                    $content .= "<td>$depto->nombre</td>";
                    $content .= "<td>$depto->id</td>";
                    $content .= "<td>$mun->nombre</td>";
                    $content .= "<td>$mun->id</td>";
                    $content .= "<td>".$locas['lugar'][0]."</td>";
                }
                else{
                    for($v=0;$v<5;$v++){
                        $content .= "<td></td>";
                    }
                }
                
                $content .= "</tr>";
                $r++;
            }
        }
        
        else{
            echo "<tr><td align='center'><br><b>NO SE ENCONTRARON EVENTOS</b></td></tr>";
            echo "<tr><td align='center'><br><a href='javascript:history.back();'>Regresar</a></td></tr>";
            die;
        }
        
        $file = $_SERVER['DOCUMENT_ROOT']."/sissh/admin/evento_c/reporte_eventos.xls";
        $file_zip = $_SERVER['DOCUMENT_ROOT']."/sissh/admin/evento_c/reporte_eventos.zip";
        
        $fp = $archivo->Abrir($file,"w+");
        $archivo->Escribir($fp,$content);
        $archivo->Cerrar($fp);

        //$archivo->Borrar($file_zip);
        exec("zip -j ".$_SERVER["DOCUMENT_ROOT"]."/sissh/admin/evento_c/reporte_eventos.zip ".$_SERVER["DOCUMENT_ROOT"]."/sissh/admin/evento_c/reporte_eventos.xls");
        
        $size = ceil(filesize($file) / 1000);
        $size_zip = ceil(filesize($file_zip) / 1000);
        
        echo "<tr><td><img src='/sissh/admin/images/excel.gif'>&nbsp;<a href='/sissh/admin/evento_c/reporte_eventos.xls'>Descargar Archivo XLS</a>&nbsp;[ Tamaño: ".$size." kB ] [ <b>$m Eventos Reportados</b> ]";
        
        //if ($m < 500) echo " [ <a href='#' onclick=\"document.getElementById('eventos_online').style.display=''\">Ver Eventos</a> ]";
        echo "<tr><td><img src='/sissh/admin/images/zip.png'>&nbsp;<a href='/sissh/admin/evento_c/reporte_eventos.zip'>Descargar Archivo ZIP</a>&nbsp;[ Tamaño: ".$size_zip." kB ]";
        
        //if ($m < 500) echo "<tr id='eventos_online' style='display:none'><td>$content</td></tr>";
            
        echo "</table>";
    }

    
    /******************************************************************************
    * Número de Eventos aplicando los filtros
    * @param $id_mun
    * @param $id_subcat
    * @param $id_actor
    * @param $fecha_ini
    * @param $fecha_fin
    * @access public
    *******************************************************************************/
    function numEventosReporte($id_mun,$id_subcat,$id_actor,$fecha_ini,$fecha_fin){
        
        $filtro_fecha = ($fecha_ini != "" && $fecha_fin != "") ? 1 : 0;

        if (!empty($id_subcat) && empty($id_actor)){
            $sql = "SELECT count(evento_localizacion.id_even) 
                    FROM descripcion_evento 
                    INNER JOIN evento_localizacion ON descripcion_evento.id_even = evento_localizacion.id_even 
                    INNER JOIN evento_c ON evento_localizacion.id_even = evento_c.id_even 
                    WHERE id_scateven = $id_subcat";
            
            if (!empty($id_mun))    $sql .= " AND id_mun = ".$id_mun;
                
            if ($filtro_fecha == 1){
                $sql .= " AND fecha_reg_even BETWEEN '$fecha_ini' AND '$fecha_fin'";
            }
            //echo "$sql<br />";
            $rs = $this->conn->OpenRecordset($sql);
            $row_rs = $this->conn->FetchRow($rs);
            
            return $row_rs[0];
            
        }
        else if (!empty($id_subcat) && !empty($id_actor)){
            $sql = "SELECT count(evento_localizacion.id_even) 
                    FROM descripcion_evento 
                    INNER JOIN evento_localizacion USING(id_even) 
                    INNER JOIN evento_c USING(id_even)
                    INNER JOIN actor_descevento USING(id_deseven)
                    WHERE id_scateven = $id_subcat AND id_actor = $id_actor";
            
            if (!empty($id_mun))    $sql .= " AND id_mun = ".$id_mun;
                
            if ($filtro_fecha == 1){
                $sql .= " AND fecha_reg_even BETWEEN '$fecha_ini' AND '$fecha_fin'";
            }
            //echo "$sql<br />";
            $rs = $this->conn->OpenRecordset($sql);
            $row_rs = $this->conn->FetchRow($rs);
            
            return $row_rs[0];
            
        }
        else if (empty($id_subcat) && empty($id_actor)){
            $sql = "SELECT count(evento_c.id_even) 
                    FROM evento_c 
                    INNER JOIN evento_localizacion ON evento_localizacion.id_even = evento_c.id_even WHERE ";

            if ($id_mun > 0){
                $sql .= "id_mun = ".$id_mun;
                
                if ($filtro_fecha == 1){
                    $sql .= " AND ";    
                }
            }
            
            if ($filtro_fecha == 1){
                $sql .= " fecha_reg_even BETWEEN '$fecha_ini' AND '$fecha_fin'";
            }
//          echo $sql;

            $rs = $this->conn->OpenRecordset($sql);
            $row_rs = $this->conn->FetchRow($rs);
            
            return $row_rs[0];
            
        }
        else if (empty($id_subcat) && !empty($id_actor)){
            $sql = "SELECT evento_c.id_even 
                    FROM actor_descevento 
                    INNER JOIN evento_localizacion ON actor_descevento.id_deseven = evento_localizacion.id_even 
                    INNER JOIN evento_c ON evento_localizacion.id_even = evento_c.id_even 
                    WHERE id_actor IN ($id_actor) AND id_mun = $id_mun";

//          echo $sql;
            if ($filtro_fecha == 1){
                $sql .= " AND fecha_reg_even BETWEEN '$fecha_ini' AND '$fecha_fin'";
            }
            //echo $sql;
            $rs = $this->conn->OpenRecordset($sql);
            
            //Si id_actor es del tipo combinacion (id1,id2), cuenta los eventos que tienen todos los actores
            if (count(explode(",",$id_actor)) > 1){
                $a = 0;
                $id_even_a = array();
                $id_even_unique = array();
                while ($row_rs = $this->conn->FetchRow($rs)){
                    $id_even_a[$a] = $row_rs[0];
                    $a++;
                }
                if (count($id_even_a) > 0){
                    //Elimina repetidos
                    $id_even_unique = array_unique($id_even_a);
                    
                    //Resta la longitud de ambos arreglos para saber los actores que cumplen
                    $num = count($id_even_a) - count($id_even_unique);
                }
                else $num = 0;
                
                return $num;
                
            }
            
            else{
                $row_rs = $this->conn->FetchRow($rs);
                
                return $row_rs[0];
            }
            
        }

    }
    
    /***************************************************************************************
    * Número de Victimas aplicando filtros
    * @param string $id_mun
    * @param array $filtros  Arreglo de filtros, las claves son los nombres del filtro
    * @access public
    ****************************************************************************************/
    function numVictimasReporte($id_mun,$filtros){

        $filtro_fecha = (isset($filtros['f_ini']) && isset($filtros['f_fin']) && $filtros['f_ini'] != '' && $filtros['f_fin'] != '') ? 1 : 0;
        
        $sql = "SELECT sum(victima.cant_victima) 
                FROM victima 
                INNER JOIN descripcion_evento USING (id_deseven) 
                INNER JOIN evento_localizacion USING(id_even) 
                INNER JOIN evento_c USING(id_even)";

        $cond = "1=1 ";
            
        if ($filtro_fecha == 1){
            $cond .= " AND fecha_reg_even BETWEEN '".$filtros['f_ini']."' AND '".$filtros['f_fin']."'";
        }
        
        if (!empty($filtros['id_mun'])){
            $cond .= " AND id_mun = '$id_mun'";
        }

        if (!empty($filtros['id_scat'])){
            $cond .= " AND id_scateven IN (".$filtros['id_scat'].")";
        }
        
        if (!empty($filtros['id_actor'])){
            $sql .= " INNER JOIN actor_descevento USING (id_deseven) ";
            $cond .= " AND id_actor IN (".$filtros['id_actor'].")";
        }
        
        if (!empty($filtros['id_sexo'])){
            $cond .= " AND victima.id_sexo IN (".$filtros['id_sexo'].")";
        }
        
        if (!empty($filtros['id_condicion'])){
            $cond .= " AND victima.id_condicion IN (".$filtros['id_condicion'].")";
        }
        
        if (!empty($filtros['id_subcondicion'])){
            $cond .= " AND victima.id_subcondicion IN (".$filtros['id_subcondicion'].")";
        }
        
        if (!empty($filtros['id_subetnia'])){
            $cond .= " AND victima.id_subetnia IN (".$filtros['id_subetnia'].")";
        }
        
        if (!empty($filtros['id_etnia'])){
            $sql .= "INNER JOIN sub_etnia USING(id_subetnia)";
            $cond .= " AND id_etnia IN (".$filtros['id_etnia'].")";
        }
        
        if (!empty($filtros['id_edad'])){
            $cond .= " AND victima.id_edad IN (".$filtros['id_edad'].")";
        }
        
        if (!empty($filtros['id_rango_edad'])){
            $cond .= " AND victima.id_raned IN (".$filtros['id_rango_edad'].")";
        }
        
        if (!empty($filtros['id_estado'])){
            $cond .= " AND victima.id_estado IN (".$filtros['id_estado'].")";
        }
        
        if (!empty($filtros['id_ocupacion'])){
            $cond .= " AND victima.id_ocupacion IN (".$filtros['id_ocupacion'].")";
        }

        $sql .= " WHERE $cond";

        if ($id_mun == 19142 && $filtros['id_scat'] == 1) {
            //echo $sql;
        } 

        //echo $sql;

        $rs = $this->conn->OpenRecordset($sql);
        $row_rs = $this->conn->FetchRow($rs);
        
        if (is_null($row_rs[0])) return 0;
        else    return $row_rs[0];
        
    }
    
    /******************************************************************************
    * Reporte PDF - EXCEL
    * @param Array $id_eventos Id de los EventoConflictos a Reportar
    * @param Int $formato PDF o Excel
    * @param Int $basico 1 = Básico - 2 = Detallado
    * @access public
    *******************************************************************************/
    function ReporteEventoConflicto($id_eventos,$formato,$basico){

        //INICIALIZACION DE VARIABLES
        $depto_dao = New DeptoDAO();
        $mun_dao = New MunicipioDAO();
        $tipo_dao = New TipoEventoConflictoDAO();
        $actor_dao = New ActorDAO();
        $cat_dao = New CatTipoEventoConflictoDAO();
        $cons_hum_dao = New ConsHumDAO();
        $cons_hum_vo = New ConsHum();
        $riesgo_hum_dao = New RiesgoHumDAO();
        $riesgo_hum_vo = New RiesgoHum();
        $region_dao = New RegionDAO();
        $poblado_dao = New PobladoDAO();
        $resguardo_dao = New ResguardoDAO();
        $parque_nat_dao = New ParqueNatDAO();
        $div_afro_dao = New DivAfroDAO();
        $cats = $cat_dao->GetAllArray('');
        $file = New Archivo();

        $arr_id = explode(",",$id_eventos);


        if ($formato == 1){

            $pdf = new Cezpdf();

            $pdf -> ezSetMargins(80,70,20,20);
            $pdf->selectFont('admin/lib/common/PDFfonts/Helvetica.afm');

            // Coloca el logo y el pie en todas las páginas
            $all = $pdf->openObject();
            $pdf->saveState();
            $img_att = getimagesize('images/logos/enc_reporte_semanal.jpg');
            $pdf->addPngFromFile('images/logos/enc_reporte_semanal.png',700,550,$img_att[0]/2,$img_att[1]/2);

            $pdf->addText(300,580,14,'<b>Sala de Situación Humanitaria</b>');

            if ($basico == 1){
                $pdf->addText(350,560,12,'Listado de EventoConflictos');
            }

            if ($basico == 2){
                $pdf->setLineStyle(1);
                $pdf->line(50,535,740,535);
                $pdf->line(50,530,740,530);
            }

            $pdf->addText(330,30,8,'Sala de Situación Humanitaria - Naciones Unidas');

            $pdf->restoreState();
            $pdf->closeObject();
            $pdf->addObject($all,'all');

            $pdf->ezSetDy(0);

            //FORMATO BASICO
            if ($basico == 1){

                $c = 0;
                $arr = Array();
                foreach ($arr_id as $id){
                    //Carga el VO
                    $vo = $this->Get($id);
                    //Carga el arreglo
                    $arr[$c] = $vo;
                    $c++;
                }

                $num_arr = count($arr);

                //CLASIFICA LOS EVENTOS POR CATEGORIAS
                $c = 0;
                foreach ($cats as $cat_vo){
                    $e = 0;
                    foreach($arr as $eve){
                        if ($eve->id_cat == $cat_vo->id){
                            $arr_c[$c][$e] = $eve;
                            $e++;
                        }
                    }
                    $c++;
                }


                ////SE MUESTRAN LOS EVENTOS POR CATEGORIA
                $c = 0;
                foreach ($cats as $cat_vo){

                    //VERIFICA SI EXISTEN EVENTOS EN LA CATEGORIA
                    $tiene = 0;
                    foreach($arr as $eve){
                        if ($eve->id_cat == $cat_vo->id){
                            $tiene = 1;
                        }
                    }

                    ////TITULO DE LA CATEGORIA
                    if ($tiene == 1){

                        $data = Array(Array('title'=>'<b>'.$cat_vo->nombre.'</b>'));
                        $options = Array('showLines' => 0,'width' => 750, 'cols'=>array('title' => array('justification'=>'center')));
                        $pdf->ezTable($data,Array('title'=>''),'',$options);

                        $title = Array('depto' => '<b>Departamento</b>',
                        'mun'   => '<b>Municipio</b>',
                        'lugar'   => '<b>Lugar</b>',
                        't_evento'   => '<b>Tipo de EventoConflicto</b>',
                        'actor'   => '<b>Actores</b>',
                        'cons'   => '<b>Consecuencias Humanitarias</b>',
                        'riesg'   => '<b>Riesgos Humanitarios</b>',
                        'desc'   => '<b>Descripción</b>',
                        'fecha'   => '<b>Fecha registro</b>');

                        $f = 0;
                        foreach($arr_c[$c] as $arr_vo){

                            ////DEPARTAMENTOS
                            $z=0;
                            $tmp = "";
                            foreach($arr_vo->id_deptos as $id){
                                $vo = $depto_dao->Get($id);
                                if ($z==0)  $tmp = $vo->nombre;
                                else                $tmp .= ", ".$vo->nombre;
                                $z++;
                            }
                            $data[$f]['depto'] = $tmp;

                            ////MUNICIPIOS
                            $z=0;
                            $tmp = "";
                            foreach($arr_vo->id_muns as $id){
                                $vo = $mun_dao->Get($id);
                                if ($z==0)  $tmp = $vo->nombre;
                                else                $tmp .= ", ".$vo->nombre;
                                $z++;
                            }
                            $data[$f]['mun'] = $tmp;

                            ////LUGAR
                            $data[$f]['lugar'] = $arr_vo->lugar;

                            ////TIPO DE EVENTOS
                            $z=0;
                            $tmp = "";
                            foreach($arr_vo->id_tipo as $id){
                                $vo = $tipo_dao->Get($id);
                                if ($z==0)  $tmp = $vo->nombre;
                                else                $tmp .= ", ".$vo->nombre;
                                $z++;
                            }
                            $data[$f]['t_evento'] = $tmp;

                            ////ACTORES
                            $z=0;
                            $tmp = "";
                            foreach($arr_vo->id_actores as $id){
                                $vo = $actor_dao->Get($id);
                                if ($z==0)  $tmp = $vo->nombre;
                                else                $tmp .= ", ".$vo->nombre;
                                $z++;
                            }
                            $data[$f]['actor'] = $tmp;

                            ////CONS. HUM
                            $z=0;
                            $tmp = "";
                            foreach($arr_vo->id_cons as $id){
                                $vo = $cons_hum_dao->Get($id);
                                if ($z==0)  $tmp = $vo->nombre;
                                else                $tmp .= ", ".$vo->nombre;
                                $z++;
                            }

                            ////Descripción de las consecuencias
                            if ($arr_vo->desc_cons_hum != "")
                            $tmp .= " - ".$arr_vo->desc_cons_hum;

                            $data[$f]['cons'] = $tmp;

                            ////RIESG. HUM
                            $z=0;
                            $tmp = "";
                            foreach($arr_vo->id_riesgos as $id){
                                $vo = $riesgo_hum_dao->Get($id);
                                if ($z==0)  $tmp = $vo->nombre;
                                else                $tmp .= ", ".$vo->nombre;
                                $z++;
                            }
                            ////Descripción de los riegos
                            if ($arr_vo->desc_riesg_hum != "")
                            $tmp .= " - ".$arr_vo->desc_riesg_hum;

                            $data[$f]['riesg'] = $tmp;

                            ////DESCRIPCION
                            $data[$f]['desc'] = $arr_vo->desc;

                            ////FECHA DE REGISTRO
                            $data[$f]['fecha'] = $arr_vo->fecha_registro;

                            $f++;
                        }
                    }
                    $c++;

                    $options = Array('showLines' => 2, 'shaded' => 0, 'width' => 750, 'fontSize'=>8, 'cols'=>array('desc'=>array('width'=>200,'justification'=>'full'),'lugar'=>array('width'=>80),'fecha'=>array('width'=>60)));
                    $pdf->ezTable($data,$title,'',$options);

                }
            }

            //MUESTRA EN EL NAVEGADOR EL PDF
            //$pdf->ezStream();

            //CREA UN ARCHIVO PDF PARA BAJAR
            $nom_archivo = 'consulta/csv/evento.pdf';
            $file = New Archivo();
            $fp = $file->Abrir($nom_archivo,'wb');
            $pdfcode = $pdf->ezOutput();
            $file->Escribir($fp,$pdfcode);
            $file->Cerrar($fp);

            ?>
            <table align='center' cellspacing="1" cellpadding="3" border="0" width="750">
                <tr><td>&nbsp;</td></tr>
                <tr><td align='center' class='titulo_lista' colspan=2>REPORTAR EVENTOS EN FORMATO PDF</td></tr>
                <tr><td>&nbsp;</td></tr>
                <tr><td colspan=2>
                    Se ha generado correctamente el archivo PDF de EventoConflictos.<br><br>
                    Para salvarlo use el botón derecho del mouse y la opción Guardar destino como sobre el siguiente link: <a href='<?=$nom_archivo;?>'>Archivo PDF</a>
                </td></tr>
            </table>
            <?

        }
        //EXCEL
        else if ($formato == 2){

            $fp = $file->Abrir('consulta/csv/evento.txt','w');

            $c = 0;
            $arr = Array();
            foreach ($arr_id as $id){
                //Carga el VO
                $vo = $this->Get($id);
                //Carga el arreglo
                $arr[$c] = $vo;
                $c++;
            }

            $num_arr = count($arr);

            //CLASIFICA LOS EVENTOS POR CATEGORIAS
            /*$c = 0;
            foreach ($cats as $cat_vo){
            $e = 0;
            foreach($arr as $eve){
            if ($eve->id_cat == $cat_vo->id){
            $arr_c[$c][$e] = $eve;
            $e++;
            }
            }
            $c++;
            }*/


            $linea = "ID_EVENTO|COD_DEPTO|DEPARTAMENTO|COD_MPIO|MUNICIPIO|LUGAR|CATEGORIA|TIPO DE EVENTO|SUBTIPO DE EVENTO|ACTORES|CONSECUENCIAS HUMANITARIAS|RIESGOS HUMANITARIOS|DESCRIPCION|FUENTE|FECHA DE REGISTRO|FECHA DEL EVENTO\n";
            $file->Escribir($fp,$linea);

            $f = 0;
            foreach($arr as $arr_vo){
                $linea = "";

                //REGISTRO POR MUNICIPIO
                if (count($arr_vo->id_muns) > 0){
                    foreach($arr_vo->id_muns as $id_mun){
                        $mun = $mun_dao->Get($id_mun);
                        $depto = $depto_dao->Get($mun->id_depto);

                        ////ID EVENTO
                        $linea .= $arr_vo->id;

                        ////COD. DEPARTAMENTO
                        $linea .= "|".$depto->id;

                        ////DEPARTAMENTO
                        $linea .= "|".$depto->nombre;

                        ////COD. MPIO
                        $linea .= "|".$mun->id;

                        ////MUNICIPIO
                        $linea .= "|".$mun->nombre;

                        ////LUGAR
                        $arr_vo->lugar = str_replace("\r\n","",$arr_vo->lugar);
                        $linea .= "|".$vo->lugar;

                        ////CATEGORIA
                        $vo = $cat_dao->Get($arr_vo->id_cat);
                        $linea .= "|".$vo->nombre;

                        ////TIPO DE EVENTOS
                        $z=0;
                        $tmp_papa = "";
                        $tmp_hijo = "";
                        foreach($arr_vo->id_tipo as $id){
                            $vo = $tipo_dao->Get($id);
                            //ES PAPA
                            if ($vo->id_papa == 0){
                                if ($z==0)  $tmp_papa = $vo->nombre;
                                else        $tmp_papa .= ",".$vo->nombre;
                            }
                            //ES HIJO
                            else {
                                if ($z==0)  $tmp_hijo = $vo->nombre;
                                else        $tmp_hijo .= ",".$vo->nombre;
                            }

                            $z++;
                        }
                        $linea .= "|".$tmp_papa;
                        $linea .= "|".$tmp_hijo;

                        ////ACTORES
                        $z=0;
                        $tmp = "";
                        foreach($arr_vo->id_actores as $id){
                            $vo = $actor_dao->Get($id);
                            if ($z==0)  $tmp = $vo->nombre;
                            else        $tmp .= ",".$vo->nombre;
                            $z++;
                        }
                        $linea .= "|".$tmp;

                        ////CONS. HUM
                        $z=0;
                        $tmp = "";
                        foreach($arr_vo->id_cons as $id){
                            $vo = $cons_hum_dao->Get($id);
                            if ($z==0)  $tmp = $vo->nombre;
                            else                $tmp .= ",".$vo->nombre;
                            $z++;
                        }

                        ////Descripción de las consecuencias
                        if ($arr_vo->desc_cons_hum != ""){
                            //ELIMINA EL CARACTER DE NUEVA LINEA QUE EL USUARIO INGRESA EN EL TEXTAREA
                            $arr_vo->desc_cons_hum = str_replace("\r\n","",$arr_vo->desc_cons_hum);


                            if ($tmp == "") $tmp .= $arr_vo->desc_cons_hum;
                            else            $tmp .= " - ".$arr_vo->desc_cons_hum;
                        }
                        $linea .= "|".$tmp;

                        ////RIESG. HUM
                        $z=0;
                        $tmp = "";
                        foreach($arr_vo->id_riesgos as $id){
                            $vo = $riesgo_hum_dao->Get($id);
                            if ($z==0)  $tmp = $vo->nombre;
                            else                $tmp .= ",".$vo->nombre;
                            $z++;
                        }
                        ////Descripción de los riegos
                        if ($arr_vo->desc_riesg_hum != ""){
                            //ELIMINA EL CARACTER DE NUEVA LINEA QUE EL USUARIO INGRESA EN EL TEXTAREA
                            $arr_vo->desc_riesg_hum = str_replace("\r\n","",$arr_vo->desc_riesg_hum);

                            if ($tmp == "") $tmp .= $arr_vo->desc_riesg_hum;
                            else            $tmp .= " - ".$arr_vo->desc_riesg_hum;
                        }
                        $linea .= "|".$tmp;

                        ////DESCRIPCION
                        //ELIMINA EL CARACTER DE NUEVA LINEA QUE EL USUARIO INGRESA EN EL TEXTAREA
                        $arr_vo->desc = str_replace("\r\n","",$arr_vo->desc);

                        $linea .= "|".$arr_vo->desc;

                        ////FUENTE
                        $linea .= "|".$arr_vo->fuente;

                        ////FECHA DE REGISTRO
                        $linea .= "|".$arr_vo->fecha_registro;

                        ////FECHA DEL EVENTO
                        $linea .= "|".$arr_vo->fecha_evento;

                        $linea .= "\n";
                    }
                }  //FIN: EVENTO TIENE MUNICIPIOS

                //REGISTRO POR DEPARTAMENTO
                else if (count($arr_vo->id_muns) == 0 && count($arr_vo->id_deptos) > 0){
                    foreach($arr_vo->id_deptos as $id_depto){

                        $depto = $depto_dao->Get($id_depto);

                        ////ID EVENTO
                        $linea .= $arr_vo->id;

                        ////COD. DEPARTAMENTO
                        $linea .= "|".$depto->id;

                        ////DEPARTAMENTO
                        $linea .= "|".$depto->nombre;

                        ////COD. MPIO
                        $linea .= "|";

                        ////MUNICIPIO
                        $linea .= "|";

                        ////LUGAR
                        $arr_vo->lugar = str_replace("\r\n","",$arr_vo->lugar);
                        $linea .= "|".$vo->lugar;

                        ////CATEGORIA
                        $vo = $cat_dao->Get($arr_vo->id_cat);
                        $linea .= "|".$vo->nombre;

                        ////TIPO DE EVENTOS
                        $z=0;
                        $tmp_papa = "";
                        $tmp_hijo = "";
                        foreach($arr_vo->id_tipo as $id){
                            $vo = $tipo_dao->Get($id);
                            //ES PAPA
                            if ($vo->id_papa == 0){
                                if ($z==0)  $tmp_papa = $vo->nombre;
                                else        $tmp_papa .= ",".$vo->nombre;
                            }
                            //ES HIJO
                            else {
                                if ($z==0)  $tmp_hijo = $vo->nombre;
                                else        $tmp_hijo .= ",".$vo->nombre;
                            }

                            $z++;
                        }
                        $linea .= "|".$tmp_papa;
                        $linea .= "|".$tmp_hijo;

                        ////ACTORES
                        $z=0;
                        $tmp = "";
                        foreach($arr_vo->id_actores as $id){
                            $vo = $actor_dao->Get($id);
                            if ($z==0)  $tmp = $vo->nombre;
                            else                $tmp .= ",".$vo->nombre;
                            $z++;
                        }
                        $linea .= "|".$tmp;

                        ////CONS. HUM
                        $z=0;
                        $tmp = "";
                        foreach($arr_vo->id_cons as $id){
                            $vo = $cons_hum_dao->Get($id);
                            if ($z==0)  $tmp = $vo->nombre;
                            else                $tmp .= ",".$vo->nombre;
                            $z++;
                        }

                        ////Descripción de las consecuencias
                        if ($arr_vo->desc_cons_hum != ""){
                            //ELIMINA EL CARACTER DE NUEVA LINEA QUE EL USUARIO INGRESA EN EL TEXTAREA
                            $arr_vo->desc_cons_hum = str_replace("\r\n","",$arr_vo->desc_cons_hum);


                            if ($tmp == "") $tmp .= $arr_vo->desc_cons_hum;
                            else            $tmp .= " - ".$arr_vo->desc_cons_hum;
                        }
                        $linea .= "|".$tmp;

                        ////RIESG. HUM
                        $z=0;
                        $tmp = "";
                        foreach($arr_vo->id_riesgos as $id){
                            $vo = $riesgo_hum_dao->Get($id);
                            if ($z==0)  $tmp = $vo->nombre;
                            else                $tmp .= ",".$vo->nombre;
                            $z++;
                        }
                        ////Descripción de los riegos
                        if ($arr_vo->desc_riesg_hum != ""){
                            //ELIMINA EL CARACTER DE NUEVA LINEA QUE EL USUARIO INGRESA EN EL TEXTAREA
                            $arr_vo->desc_riesg_hum = str_replace("\r\n","",$arr_vo->desc_riesg_hum);

                            if ($tmp == "") $tmp .= $arr_vo->desc_riesg_hum;
                            else            $tmp .= " - ".$arr_vo->desc_riesg_hum;
                        }
                        $linea .= "|".$tmp;

                        ////DESCRIPCION
                        //ELIMINA EL CARACTER DE NUEVA LINEA QUE EL USUARIO INGRESA EN EL TEXTAREA
                        $arr_vo->desc = str_replace("\r\n","",$arr_vo->desc);

                        $linea .= "|".$arr_vo->desc;

                        ////FUENTE
                        $linea .= "|".$arr_vo->fuente;

                        ////FECHA DE REGISTRO
                        $linea .= "|".$arr_vo->fecha_registro;

                        ////FECHA DEL EVENTO
                        $linea .= "|".$arr_vo->fecha_evento;

                        $linea .= "\n";
                    }
                }  //FIN: EVENTO TIENE DEPARTAMENTO

                //REGISTRO EVENTO
                else if (count($arr_vo->id_muns) == 0 && count($arr_vo->id_deptos) == 0){

                    ////ID EVENTO
                    $linea = $arr_vo->id;

                    ////COD. DEPARTAMENTO
                    $linea .= "|";

                    ////DEPARTAMENTO
                    $linea .= "|";

                    ////COD. MPIO
                    $linea .= "|";

                    ////MUNICIPIO
                    $linea .= "|";

                    ////LUGAR
                    $linea .= "|".$arr_vo->lugar;

                    ////CATEGORIA
                    $vo = $cat_dao->Get($arr_vo->id_cat);
                    $linea .= "|".$vo->nombre;

                    ////TIPO DE EVENTOS
                    $z=0;
                    $tmp_papa = "";
                    $tmp_hijo = "";
                    foreach($arr_vo->id_tipo as $id){
                        $vo = $tipo_dao->Get($id);
                        //ES PAPA
                        if ($vo->id_papa == 0){
                            if ($z==0)  $tmp_papa = $vo->nombre;
                            else        $tmp_papa .= ",".$vo->nombre;
                        }
                        //ES HIJO
                        else {
                            if ($z==0)  $tmp_hijo = $vo->nombre;
                            else        $tmp_hijo .= ",".$vo->nombre;
                        }

                        $z++;
                    }
                    $linea .= "|".$tmp_papa;
                    $linea .= "|".$tmp_hijo;

                    ////ACTORES
                    $z=0;
                    $tmp = "";
                    foreach($arr_vo->id_actores as $id){
                        $vo = $actor_dao->Get($id);
                        if ($z==0)  $tmp = $vo->nombre;
                        else                $tmp .= ",".$vo->nombre;
                        $z++;
                    }
                    $linea .= "|".$tmp;

                    ////CONS. HUM
                    $z=0;
                    $tmp = "";
                    foreach($arr_vo->id_cons as $id){
                        $vo = $cons_hum_dao->Get($id);
                        if ($z==0)  $tmp = $vo->nombre;
                        else                $tmp .= ",".$vo->nombre;
                        $z++;
                    }

                    ////Descripción de las consecuencias
                    if ($arr_vo->desc_cons_hum != ""){
                        //ELIMINA EL CARACTER DE NUEVA LINEA QUE EL USUARIO INGRESA EN EL TEXTAREA
                        $arr_vo->desc_cons_hum = str_replace("\r\n","",$arr_vo->desc_cons_hum);


                        if ($tmp == "") $tmp .= $arr_vo->desc_cons_hum;
                        else            $tmp .= " - ".$arr_vo->desc_cons_hum;
                    }
                    $linea .= "|".$tmp;

                    ////RIESG. HUM
                    $z=0;
                    $tmp = "";
                    foreach($arr_vo->id_riesgos as $id){
                        $vo = $riesgo_hum_dao->Get($id);
                        if ($z==0)  $tmp = $vo->nombre;
                        else                $tmp .= ",".$vo->nombre;
                        $z++;
                    }
                    ////Descripción de los riegos
                    if ($arr_vo->desc_riesg_hum != ""){
                        //ELIMINA EL CARACTER DE NUEVA LINEA QUE EL USUARIO INGRESA EN EL TEXTAREA
                        $arr_vo->desc_riesg_hum = str_replace("\r\n","",$arr_vo->desc_riesg_hum);

                        if ($tmp == "") $tmp .= $arr_vo->desc_riesg_hum;
                        else            $tmp .= " - ".$arr_vo->desc_riesg_hum;
                    }
                    $linea .= "|".$tmp;

                    ////DESCRIPCION
                    //ELIMINA EL CARACTER DE NUEVA LINEA QUE EL USUARIO INGRESA EN EL TEXTAREA
                    $arr_vo->desc = str_replace("\r\n","",$arr_vo->desc);

                    $linea .= "|".$arr_vo->desc;

                    ////FUENTE
                    $linea .= "|".$arr_vo->fuente;

                    ////FECHA DE REGISTRO
                    $linea .= "|".$arr_vo->fecha_registro;

                    ////FECHA DE EVENTO
                    $linea .= "|".$arr_vo->fecha_evento;

                    $linea .= "\n";

                }  //FIN: EVENTO TIENE DEPARTAMENTO
                $f++;
                $file->Escribir($fp,$linea);
            }
            $file->Cerrar($fp);

            ?>
            <table align='center' cellspacing="1" cellpadding="3" border="0" width="750">
                <tr><td>&nbsp;</td></tr>
                <tr><td align='center' class='titulo_lista' colspan=2>REPORTAR EVENTOS EN FORMATO TXT (Excel)</td></tr>
                <tr><td>&nbsp;</td></tr>
                <tr><td colspan=2>
                    Se ha generado correctamente el archivo TXT de EventoConflictos.<br><br>
                    Para salvarlo use el botón derecho del mouse y la opción Guardar destino como sobre el siguiente link: <a href='consulta/csv/evento.txt'>Archivo TXT</a>
                </td></tr>
            </table>
            <?
        }
    }

    /**
    * Lista los Proyectos en una Tabla
    * @access public
    */          
    function ReportarMapaI(){
        $cat_dao = New CatTipoEventoConflictoDAO();
        $depto_dao = New DeptoDAO();
        $mun_dao = New MunicipioDAO();
        $tipo_evento_dao = New TipoEventoConflictoDAO();
        $actor_dao = New ActorDAO();
        $cons_hum_dao = New ConsHumDAO();
        $riesgo_hum_dao = New RiesgoHumDAO();
        $cats = $cat_dao->GetAllArray('');
        $arr_id = Array();

        //UBIACION GEOGRAFICA
        if (isset($_POST["id_depto"]) && !isset($_POST["id_muns"])){

            $id_depto = $_POST['id_depto'];

            $m = 0;
            foreach ($id_depto as $id){
                $id_depto_s[$m] = "'".$id."'";
                $m++;
            }
            $id_depto_s = implode(",",$id_depto_s);

            $sql = "SELECT evento.ID_EVENTO FROM depto_evento INNER JOIN evento ON depto_evento.ID_EVENTO = evento.ID_EVENTO WHERE ID_DEPTO IN (".$id_depto_s.")";

            $sql .= " ORDER BY evento.ID_EVENTO ASC";

            $arr_id_u_g = Array();
            $i = 0;
            $rs = $this->conn->OpenRecordset($sql);
            while ($row_rs = $this->conn->FetchRow($rs)){
                $arr_id[$i] = $row_rs[0];
                $i++;
            }
        }

        //MUNICIPIO
        else if (isset($_POST["id_muns"])){

            $id_mun = $_POST['id_muns'];

            $m = 0;
            foreach ($id_mun as $id){
                $id_mun_s[$m] = "'".$id."'";
                $m++;
            }
            $id_mun_s = implode(",",$id_mun_s);

            $sql = "SELECT evento.ID_EVENTO FROM mun_evento INNER JOIN evento ON mun_evento.ID_EVENTO = evento.ID_EVENTO WHERE ID_MUN IN (".$id_mun_s.")";

            $sql .= " ORDER BY evento.ID_EVENTO ASC";

            $arr_id_u_g = Array();
            $i = 0;
            $rs = $this->conn->OpenRecordset($sql);
            while ($row_rs = $this->conn->FetchRow($rs)){
                $arr_id[$i] = $row_rs[0];
                $i++;
            }
        }

        $c = 0;
        $arr = Array();
        foreach ($arr_id as $id){
            //Carga el VO
            $vo = $this->Get($id);
            //Carga el arreglo
            $arr[$c] = $vo;
            $c++;
        }

        $num_arr = count($arr);

        echo "<table align='center' cellspacing='1' cellpadding='3' width='750'>";
        echo "<tr><td>&nbsp;</td></tr>";
        if ($num_arr > 0 && !isset($_POST["que_org"]) && !isset($_POST["que_eve"])){
            echo "<tr><td colspan='7' align='right'>Exportar a: <input type='image' src='images/consulta/generar_pdf.gif' onclick=\"document.getElementById('pdf_eve').value = 1;\">&nbsp;&nbsp;<input type='image' src='images/consulta/excel.gif' onclick=\"document.getElementById('pdf_eve').value = 2;\"></td>";
        }

        echo "<tr><td align='center' class='titulo_lista' colspan=7>EVENTOS QUE HAN SUCEDIDO EN : ";
        //TITULO DE DEPTO
        if (isset($_POST["id_depto"]) && !isset($_POST["id_muns"])){
            $t = 0;
            foreach($_POST["id_depto"] as $id_t){
                $vo  = $depto_dao->Get($id_t);
                if ($t == 0)    echo "<b>".$vo->nombre."</b>";
                else            echo ", <b>".$vo->nombre."</b>";
                $t++;
            }
            echo "<br>";
        }
        //TITULO DE MPIO
        if (isset($_POST["id_muns"])){
            $t = 0;
            foreach($id_mun as $id_t){
                $vo  = $mun_dao->Get($id_t);
                if ($t == 0)    echo "<b>".$vo->nombre."</b>";
                else            echo ", <b>".$vo->nombre."</b>";
                $t++;
            }
            echo "<br>";
        }
        echo "</td>";
        echo "</td></tr>";


        if ($num_arr > 0){

            //CLASIFICA LOS EVENTOS POR CATEGORIAS
            $c = 0;
            foreach ($cats as $cat_vo){
                $e = 0;
                foreach($arr as $eve){
                    if ($eve->id_cat == $cat_vo->id){
                        $arr_c[$c][$e] = $eve;
                        $e++;
                    }
                }
                $c++;
            }


            ////SE MUESTRAN LOS EVENTOS POR CATEGORIA
            $c = 0;
            foreach ($cats as $cat_vo){

                //VERIFICA SI EXISTEN EVENTOS EN LA CATEGORIA
                $tiene = 0;
                foreach($arr as $eve){
                    if ($eve->id_cat == $cat_vo->id){
                        $tiene = 1;
                    }
                }

                ////TITULO DE LA CATEGORIA
                if ($tiene == 1){
                    echo "<tr><td colspan='5'><br><b>Categoria del EventoConflicto: ".$cat_vo->nombre."</b></td></tr>
                            <tr class='titulo_lista'>
                            <td align='center' width='70'><b>Departamento</b></td>
                            <td align='center' width='70'><b>Municipio</b></td>
                            <td align='center' width='100'><b>Tipo de EventoConflicto</b></td>
                            <td align='center' width='10'><b>Actores</b></td>
                            <td align='center'><b>Descripción</b></td>
                            <td align='center' width='70'><b>Fecha registro</b></td>
                            <td align='center' width='80'>Registros: ".$num_arr."</td>
                            </tr>";

                    $p = 0;
                    foreach($arr_c[$c] as $arr_vo){
                        echo "<tr class='fila_lista'>";

                        ////DEPARTAMENTOS
                        echo "<td>";
                        $z=0;
                        foreach($arr_vo->id_deptos as $id){

                            $vo = $depto_dao->Get($id);

                            /*$img = '../images/mapas/depto/'.$id.'.gif';
                            $size = getimagesize($img);
                            $width = $size[0]*0.2;
                            $height = $size[1]*0.2;*/

                            //if ($z==0)  echo "<img src='../images/mapas/depto/".$id.".gif' width='".$width."' height='".$height."'>".$vo->nombre;
                            if ($z==0)  echo $vo->nombre;
                            else                echo "<br> ".$vo->nombre;
                            $z++;
                        }
                        echo "</td>";

                        ////MUNICIPIOS
                        echo "<td>";
                        $z=0;
                        foreach($arr_vo->id_muns as $id){
                            $vo = $mun_dao->Get($id);
                            if ($z==0)  echo $vo->nombre;
                            else                echo ", ".$vo->nombre;
                            $z++;
                        }
                        echo "</td>";


                        ////TIPO DE EVENTOS
                        echo "<td>";
                        $z=0;
                        foreach($arr_vo->id_tipo as $id){
                            $vo = $tipo_evento_dao->Get($id);
                            if ($z==0)  echo $vo->nombre;
                            else                echo ", ".$vo->nombre;
                            $z++;
                        }
                        echo "</td>";

                        ////ACTORES
                        echo "<td>";
                        $z=0;
                        foreach($arr_vo->id_actores as $id){
                            $vo = $actor_dao->Get($id);
                            if ($z==0)  echo $vo->nombre;
                            else                echo ", ".$vo->nombre;
                            $z++;
                        }
                        echo "</td>";


                        ////DESCRIPCION
                        echo "<td><div align='justify'>".$arr_vo->desc."</div></td>";

                        ////FECHA DE REGISTRO
                        echo "<td>".$arr_vo->fecha_registro."</td>";
                        echo "<td><a href='#' onclick=\"window.open('index.php?accion=consultar&class=EventoConflictoDAO&method=Ver&param=".$arr_vo->id."','','top=30,left=30,height=750,width=750,scrollbars=1');return false;\">Detalles</a></td>";

                        echo "</tr>";

                        $p++;
                    }
                }
                $c++;
            }
            echo "<input type='hidden' name='id_eventos' value='".implode(",",$arr_id)."'>";
            echo "<input type='hidden' id='que_eve' name='que_eve' value='1'>";
        }
        else{
            echo "<tr><td align='center'><br><b>NO SE ENCONTRARON EVENTOS</b></td></tr>";
        }
        echo "</table>";
    }

    /**
    * Genera el archivo XML para la grafica TimeLine
    * @access public
    * @param  $id_s ID de la subcategoria = tipo de evento
    * @param  $mes Mes
    * @param  $aaaa Año
    */  
    function genXmlTimeLine($id_s,$mes,$aaaa) {
        
        $archivo = new Archivo();
        $mun_dao = new MunicipioDAO();
        $depto_dao = new DeptoDAO();

        $sql = "SELECT e.id_even AS id, CONCAT(LEFT(MONTHNAME(fecha_reg_even),3),' ',DAY(fecha_reg_even),' ',YEAR(fecha_reg_even)) AS start, MONTH(fecha_reg_even) AS mes, YEAR(fecha_reg_even) AS aaaa, sintesis_even AS title FROM 
        evento_c e JOIN descripcion_evento USING(id_even) WHERE id_scateven = $id_s 
        AND YEAR(fecha_reg_even) = $aaaa AND MONTH(fecha_reg_even) = $mes
        ORDER BY FECHA_REG_EVEN
        ";
        
        $rs = $this->conn->OpenRecordset($sql);

        if ($this->conn->RowCount($rs) > 0){
            //echo $sql;
            $xml = '<?xml version="1.0" encoding="utf-8" ?>';
            $xml .= '<data>';
            
            $filename = $id_s.'_'.$mes.'_'.$aaaa.'.xml';
            $fp = $archivo->Abrir($_SERVER["DOCUMENT_ROOT"]."/sissh/consulta/timeline_xml/$filename","w+");
            
            while($row = $this->conn->FetchObject($rs)){
                $id_evento = $row->id;

                $title_t = explode(".",$row->title);
                $tit = $title_t[0].'.'.$title_t[2];
                $xml .= '<event title="'.utf8_encode(htmlspecialchars($tit)).'" start="'.$row->start.'">';

                //Fuente evento
                $fuente_info = $this->getFuenteEvento($id_evento);
                $xml_tmp = '<u><b>Fuentes del evento:</b></u><br />';

                foreach ($fuente_info['nom_sfuente'] as $f=>$nom_fuente){
                    $xml_tmp .= "<br /><i>".$nom_fuente."</i> : ".htmlspecialchars($fuente_info['desc'][$f])."<br />";
                }

                //Actores
                $act_info = $this->getActorEvento($id_evento);
                
                if (count($act_info['nombre']) > 0){
                    $xml_tmp .= '<br /><u><b>Actores</b></u><br />';
                    
                    foreach ($act_info['nombre'] as $n=>$nom_act){
                        if ($n == 0 && $nom_actor != '')    $xml_tmp .= ' Vs ';
                        if ($nom_actor != '')               $xml_tmp .= "$nom_act<br />";
                    }
                }

                //Localizacion
                $loc_info = $this->getLocalizacionEvento($id_evento);
                $xml_tmp .= '<br /><u><b>Localización del evento:</b></u><br />';
                
                foreach ($loc_info['mpios'] as $id_mpio){

                    $mun = $mun_dao->Get($id_mpio);
                    $depto = $depto_dao->Get($mun->id_depto);

                    $xml_tmp .= "$mun->nombre, $depto->nombre<br />";
                }

                $xml .= utf8_encode(htmlspecialchars($xml_tmp));
                $xml .= '</event>';
            }
            
            $xml .= '</data>';
            
            $archivo->Escribir($fp,$xml);
            $archivo->Cerrar($fp);
        }
    }

    /**
    * Genera el archivo txt para la grafica TimePlot
    * @access public
    * @param  $id_s ID de la subcategoria = tipo de evento
    * @param  $aaaa Año
    */  
    function genTxtTimePlot($id_s,$aaaa) {
        
        $archivo = new Archivo();
        $mun_dao = new MunicipioDAO();
        $depto_dao = new DeptoDAO();

        $sql = "SELECT fecha_reg_even AS fecha, COUNT(id_even) AS num FROM evento_c JOIN descripcion_evento USING(id_even) WHERE id_scateven = $id_s 
        AND YEAR(fecha_reg_even) = $aaaa GROUP BY (fecha_reg_even) ORDER BY FECHA_REG_EVEN ";
        
        $rs = $this->conn->OpenRecordset($sql);

        if ($this->conn->RowCount($rs) > 0){
            //echo $sql;
            $txt = '# Fuente datos para timeplot eventos del conflicto';
            
            $filename = $id_s.'_'.$aaaa.'.txt';
            $fp = $archivo->Abrir($_SERVER["DOCUMENT_ROOT"]."/sissh/consulta/timeplot_txt/$filename","w+");
            
            while($row = $this->conn->FetchObject($rs)){
                $txt .= "\n$row->fecha,$row->num";
            }

            $archivo->Escribir($fp,$txt);
            $archivo->Cerrar($fp);
        }   
    }


}

class EventoConflictoAjax extends EventoConflictoDAO {
    
    /**
    * Gráfica de Eventos C
    * @access public
    * @param $reporte int Reporte
    * @param  $num_records int Numero de Mpios o Deptos a listar en los reportes 1,2
    * @param  $depto int
    * @param  $ubicacion
    * @param  $f_ini string Fecha Inicial
    * @param  $f_fin string Fecha Final
    * @param  $chart string Tipo de gráfica
    * @param  $filtros array Arreglo con los filtros que se pueden aplicar
    * @return int
    */  
    function GraficaResumenEventoC($reporte,$num_records,$depto,$ubicacion,$f_ini,$f_fin,$chart,$filtros) {

        //LIBRERIAS
        require_once "lib/libs_evento_c.php";
        
        //INICIALIZACION DE VARIABLES
        $depto_dao = New DeptoDAO();
        $mun_dao = New MunicipioDAO();
        $cat_dao = New CatEventoConflictoDAO();
        $subcat_dao = New SubCatEventoConflictoDAO();
        $csv_path = "/tmp/graficas_reportes.csv";
        $csv = '';
        $archivo = New Archivo();
                
        $chk_num_records = array(10 => '',11 => '', 12 => '',13 => '',14 => '',15 => '');
        $chk_num_records[$num_records] = ' selected ';
        $title_reporte = array("","Eventos por Municipio",
                               "Eventos por Departamento",
                                "Eventos por Mes",
                               "Eventos por Tipo de acción",
                               "Eventos por presuntos actores",
                               "Víctimas por mes",
                               "Eventos por confrontación de actores",
                               "Eventos por grupo poblacional",
                               "Víctimas por presuntos actores",
                               "Víctimas por confrontación de actores",
                               "Víctimas por grupo poblacional",
                               "Víctimas por presuntos actores",
                           );
        
        $title = $title_reporte[$reporte];
        $valores_x = array();
        $valores_y = array();

        $filtro_fecha = " AND FECHA_REG_EVEN BETWEEN '$f_ini' AND '$f_fin'";
        
        //Nombre ubicación
        $nom_ubi = "Nacional";
        if ($depto == 1){
            $ubi = $depto_dao->Get($ubicacion);
            $nom_ubi = $ubi->nombre;
        }
        else if ($depto == 0){
            $ubi = $mun_dao->Get($ubicacion);
            $nom_ubi = $ubi->nombre;
        }
        
        //CAT-SUBCAT
        $filtro_cat = 0;
        if ($filtros['id_cat'] != ''){

            $filtro_cat = 1;
            $id_cat = $filtros['id_cat'];
            
            if ($filtros['id_scat'] != ''){
                $id_subcat = $filtros['id_scat'];
            }
            else{
                $condicion_cat = "ID_CATEVEN IN ($id_cat)";
                $vo_cats = $cat_dao->GetAllArray($condicion_cat);
                
                $id_subcat = implode(",",$subcat_dao->GetAllArrayID("ID_CATEVEN IN ($id_cat)"));
            }
        }
        
        echo "<table cellpadding=5 width='100%' border=0>";
        echo "<tr><td>Localizaci&oacute;n Geogr&aacute;fica: <b>$nom_ubi</b></td></tr>";
        echo "<tr><td><img src='images/consulta/excel.gif' />&nbsp;<a href='export_data.php?csv2xls=1&csv_path=$csv_path&nombre_archivo=reporte_sidih'>Descargar tabla completa</a></td></tr>";
        
        //Número de eventos por Municipio
        if ($reporte ==  1) {
                echo "<tr>
                        <td valign='top'>
                            <table id='tabla_datos' border=0 class='tabla_grafica_conteo' cellpadding=4 cellspacing=1 width='300' height='400' data-titulo='".$title_reporte[$reporte]."'>
                                <tr class='titulo_tabla_conteo'><td align='center'>Municipio</td><td align='center'>N&uacute;mero de eventos</td></tr>";
                
            $sql = "SELECT COUNT(DISTINCT evento_c.id_even) as num, evento_localizacion.id_mun FROM evento_c 
                    INNER JOIN evento_localizacion ON evento_c.id_even = evento_localizacion.id_even
                    INNER JOIN municipio ON evento_localizacion.id_mun = municipio.id_mun
                    INNER JOIN descripcion_evento ON evento_c.id_even = descripcion_evento.id_even
                    WHERE 1 = 1";

            //CAT-SUBCAT
            if ($filtro_cat == 1){
                $sql .= " AND id_scateven IN ($id_subcat)";
            }
            
            //FILTRO UBICACION
            if ($ubicacion != 0){
                //Depto
                if ($depto == 1){
                    $sql .= "  AND id_depto = $ubicacion";
                }
            }
            
            //FECHA
            $sql .= $filtro_fecha;
            
            //$sql .= " GROUP BY evento_localizacion.id_mun ORDER BY num DESC LIMIT 0,$num_records";
            $sql .= " GROUP BY evento_localizacion.id_mun ORDER BY num DESC";
            
            //echo $sql;
            $rs = $this->conn->OpenRecordset($sql);
            $r = 0; 
            while ($row = $this->conn->FetchObject($rs)){
                
                $id_mun = $row->id_mun;
                $mun = $mun_dao->Get($id_mun);

                $nom = $mun->nombre;
                $num = $row->num;
                
                $csv .= '"'.$id_mun.'",'.utf8_encode($nom).",$num\n";
                
                if ($r <= $num_records) {
                    $valores_x[] = $nom;
                    $valores_y[] = $num;
                    echo "<tr class='fila_tabla_conteo'>
                        <td>$nom</td>
                        <td align='right'>$num</td>
                    </tr>";
                }

                $r++;
            }
            
            echo "</table>";
            
            //Si no viene de API lo muestra
            if (!isset($_GET["api"])){
                echo "<br>Listar 
                              <select onchange=\"graficarEventoC('bar',this.value)\" class='select'>
                                <option value=10 ".$chk_num_records[10].">10</option>
                                <option value=11 ".$chk_num_records[11].">11</option>
                                <option value=12 ".$chk_num_records[12].">12</option>
                                <option value=13 ".$chk_num_records[13].">13</option>
                                <option value=14 ".$chk_num_records[14].">14</option>
                                <option value=15 ".$chk_num_records[15].">15</option>
                              </select> Municipios";
                
                echo "</td>";
            }
            
            echo "<td id='highchart' width='700' height='400'>"; 
            echo "</td>";
            echo "</tr>";

        }
        
        //Número de eventos por Departamento
        else if ($reporte ==  2) {
                echo "<tr>
                        <td valign='top'>
                            <table id='tabla_datos' border=0 class='tabla_grafica_conteo' cellpadding=4 cellspacing=1 width='300' height='400' data-titulo='".$title_reporte[$reporte]."'>
                                <tr class='titulo_tabla_conteo'><td align='center'>Departamento</td><td align='center'>N&uacute;mero de eventos</td></tr>";
                
            $sql = "SELECT COUNT(DISTINCT evento_c.id_even) AS num, municipio.id_depto FROM evento_c
                    INNER JOIN evento_localizacion ON evento_c.id_even = evento_localizacion.id_even 
                    INNER JOIN municipio ON evento_localizacion.id_mun = municipio.id_mun
                    INNER JOIN descripcion_evento ON evento_c.id_even = descripcion_evento.id_even
                    WHERE 1 = 1 ";

            //CAT-SUBCAT
            if ($filtro_cat == 1){
                $sql .= " AND id_scateven IN ($id_subcat)";
            }
            
            
            //FILTRO UBICACION
            if ($ubicacion != 0){
                //Depto
                if ($depto == 1){
                    $sql .= "  AND id_depto = $ubicacion";
                }
            }
            
            //FECHA
            $sql .= $filtro_fecha;
            
            //$sql .= " GROUP BY municipio.id_depto ORDER BY num DESC LIMIT 0,$num_records";
            $sql .= " GROUP BY municipio.id_depto HAVING num > 0 ORDER BY num DESC";

            //echo $sql;
            
            $rs = $this->conn->OpenRecordset($sql);
            $r = 0; 
            while ($row = $this->conn->FetchObject($rs)){
                
                $id_depto = $row->id_depto;
                $depto = $depto_dao->Get($id_depto);
                
                $nom = $depto->nombre;
                $num = $row->num;
                
                $csv .= '"'.$id_depto.'",'.utf8_encode($nom).",$num\n";
                
                if ($r <= $num_records) {
                    $valores_x[] = $nom;
                    $valores_y[] = $num;
                    echo "<tr class='fila_tabla_conteo'><td>$nom</td>";
                    echo "<td align='right'>$num</td>";
                    echo "</tr>";
                }

                $r++;
            }
            
            echo "</table>";
            echo "<br>Listar 
                          <select onchange=\"graficarEventoC('bar',this.value)\" class='select'>
                            <option value=10 ".$chk_num_records[10].">10</option>
                            <option value=11 ".$chk_num_records[11].">11</option>
                            <option value=12 ".$chk_num_records[12].">12</option>
                            <option value=13 ".$chk_num_records[13].">13</option>
                            <option value=14 ".$chk_num_records[14].">14</option>
                            <option value=15 ".$chk_num_records[15].">15</option>
                          </select> Departamentos";
            
            echo "</td>";
            echo "<td id='highchart' width='700' height='400'>";

            echo "</td>";
            echo "</tr>";

        }

        //Número de eventos por Mes
        else if ($reporte ==  3) {
            
            $mes_a = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                echo "<tr>
                        <td valign='top'>
                            <table id='tabla_datos' border=0 class='tabla_grafica_conteo' cellpadding=4 cellspacing=1 width='300' height='400' data-titulo='".$title_reporte[$reporte]."'>
                                <tr class='titulo_tabla_conteo'><td align='center'>Mes</td><td align='center' colspan='2'>N&uacute;mero de eventos</td></tr>";
                
            $sql = "SELECT COUNT(DISTINCT evento_c.id_even) as num, MONTH(fecha_reg_even) as mes, YEAR(fecha_reg_even) as aaaa FROM evento_c 
                    INNER JOIN evento_localizacion ON evento_c.id_even = evento_localizacion.id_even 
                    INNER JOIN municipio ON evento_localizacion.id_mun = municipio.id_mun
                    INNER JOIN descripcion_evento ON evento_c.id_even = descripcion_evento.id_even
                    WHERE fecha_reg_even <> '0000-00-00' ";

            //CAT-SUBCAT
            if ($filtro_cat == 1){
                $sql .= " AND id_scateven IN ($id_subcat)";
            }
            
            //FILTRO UBICACION
            if ($ubicacion != 0){
                //Depto
                if ($depto == 1){
                    $sql .= "  AND id_depto = $ubicacion";
                }
                else if($depto == 0){
                    $sql .= "  AND evento_localizacion.id_mun = $ubicacion";
                }
            }
            
            //FECHA
            $sql .= $filtro_fecha;
            
            $sql .= " GROUP BY mes,aaaa ORDER BY fecha_reg_even";
            $rs = $this->conn->OpenRecordset($sql);
            
            //echo $sql;
            while ($row = $this->conn->FetchObject($rs)){

                $nom = $row->aaaa.'-'.$mes_a[$row->mes];
                $num = $row->num;
                
                $csv .= utf8_encode($nom).",$num\n";
                
                //$valores_x[] = $nom;
                //$valores_y[] = $num;
                echo "<tr class='fila_tabla_conteo'><td>$nom</td>";
                echo "<td align='right'>$num</td>";
                echo "</tr>";

            }
            
            echo "</table>";
            echo "</td>";
            
            echo "<td id='highchart' width='700' height='400'>";
            
            echo "</td>";
            echo "</tr>";

        }

        //Número de eventos por Subcategoría (Tipo de acción)
        else if ($reporte ==  4) {
            
            echo "<tr>
                    <td valign='top'>
                        <table id='tabla_datos' border=0 class='tabla_grafica_conteo' cellpadding=4 cellspacing=1 width='300' height='400' data-titulo='".$title_reporte[$reporte]."'>
                            <tr class='titulo_tabla_conteo'><td align='center'>Mes</td><td align='center' colspan='2'>N&uacute;mero de eventos</td></tr>";
                
            $sql = "SELECT count(evento_c.id_even) as num, id_scateven FROM evento_c 
                    INNER JOIN descripcion_evento ON evento_c.id_even = descripcion_evento.id_even
                    WHERE 1=1 ";

            //CAT-SUBCAT
            if ($filtro_cat == 1){
                $sql .= " AND id_scateven IN ($id_subcat)";
            }
            
            //FILTRO UBICACION
            if ($ubicacion != 0){
                //Depto
                if ($depto == 1){
                    $sql .= "  AND id_depto = $ubicacion";
                }
                else if($depto == 0){
                    $sql .= "  AND evento_localizacion.id_mun = $ubicacion";
                }
                
            }

            //FECHA
            $sql .= $filtro_fecha;
            
            $sql .= " GROUP BY id_scateven ORDER BY num DESC";
            $rs = $this->conn->OpenRecordset($sql);
            
            //echo $sql;
            while ($row = $this->conn->FetchObject($rs)){

                $vo = $subcat_dao->Get($row->id_scateven);
                
                $nom = $vo->nombre;
                $num = $row->num;

                $csv .= utf8_encode($nom).",$num\n";
                
                $valores_x[] = $nom;
                $valores_y[] = $num;
                
                echo "<tr class='fila_tabla_conteo'><td>$nom</td>";
                echo "<td align='right'>$num</td>";
                echo "</tr>";
                
            }
            
            echo "</table>";
            echo "</td>";
            
            echo "<td id='highchart' width='700' height='400'>";
            
            echo "</td>";
            echo "</tr>";

        }
            
        // Número de eventos, victimas por Actor
        else if ($reporte ==  5 || $reporte == 9) {
            
            $actor_dao = New ActorDAO();

            $ev = ($reporte == 5) ? 'eventos' : 'víctimas';

            // Nombres papas
            $sql = 'SELECT id_actor, nom_actor FROM actor WHERE id_papa = 0';
            $rs = $this->conn->OpenRecordset($sql);
            $papas = array();
            while ($row = $this->conn->FetchObject($rs)) {
                $papas[$row->id_actor] = $row->nom_actor;
            }
            
            echo "<tr>
                    <td valign='top'>
                        <table id='tabla_datos' border=0 class='tabla_grafica_conteo' cellpadding=4 cellspacing=1 width='300' height='400' data-titulo='".$title_reporte[$reporte]."'>
                            <thead>
                            <tr class='titulo_tabla_conteo'><th align='center'>Actor</th><th align='center' colspan='2'>N&uacute;mero de $ev</th></tr>
                            </thead>";
                
            $sql = "SELECT de.id_even, COUNT(DISTINCT(ad.id_actor)) as num, GROUP_CONCAT(DISTINCT(ad.id_actor)) AS id_actor, 
                    nom_actor, id_papa, nivel, SUM(CANT_VICTIMA) AS num_vic
                    FROM evento_c 
                    INNER JOIN descripcion_evento de USING(id_even)
                    INNER JOIN actor_descevento ad USING(id_deseven)
                    INNER JOIN actor USING(id_actor) 
                    LEFT JOIN victima AS v USING(ID_DESEVEN)
                    WHERE nivel = 1 ";
                    
            //CAT-SUBCAT
            if ($filtro_cat == 1){
                $sql .= " AND id_scateven IN ($id_subcat)";
            }
            
            //FILTRO UBICACION
            if ($ubicacion != 0){
                //Depto
                if ($depto == 1){
                    $sql .= "  AND id_depto = $ubicacion";
                }
                else if($depto == 0){
                    $sql .= "  AND evento_localizacion.id_mun = $ubicacion";
                }
            }

            //FECHA
            $sql .= $filtro_fecha;
            
            $sql .= " GROUP BY id_deseven ORDER BY num DESC";
            $rs = $this->conn->OpenRecordset($sql);
            
            //echo $sql;

            // Se cuentan eventos para actores que no sea enfrentamiento
            $enfrentamientos = 0;
            $arbol = array();
            $ids = array();
            $k = 0;
            while ($row = $this->conn->FetchObject($rs)) {

                $id_actor = $row->id_actor;
                $nom = $row->nom_actor;
                $num = $row->num;
                $id_papa = $row->id_papa;
                $id_evento = $row->id_even;
                $cant = ($reporte == 5) ? 1 : $row->num_vic;

                if ($num == 1) {
                    //if (!isset($ids[$id_actor])) $ids[$id_actor] = array();

                    //if (!in_array($id_evento, $ids[$id_actor])) {
                        $ids[$id_actor][] = $id_evento;
                        $arbol[$id_papa][$nom] = (isset($arbol[$id_papa][$nom])) ? $arbol[$id_papa][$nom] += $cant : $cant;
                    //}
                } else {
                    $enfrentamientos = $enfrentamientos + $cant;
                }

                $k++;
            }

            if (empty($arbol)) {
                echo "<tr class='fila_tabla_conteo'><td colspan='2'>No hay info</td></tr>";
            }
            else {
                echo "<tr class='fila_tabla_conteo'><th>Enfrentamientos</th><td>$enfrentamientos</td></tr>";
                    $csv .= "Enfrentamientos,$enfrentamientos\n";
                foreach ($arbol as $id_papa => $valores) {
                    echo "<tr class='fila_tabla_conteo'><td><b>".$papas[$id_papa]."</b></td><td></td></tr>";
                    $csv .= utf8_encode($papas[$id_papa])."\n";
                    foreach ($valores as $nom => $num) {
                        echo "<tr class='fila_tabla_conteo'><th style='padding-left:20px'>".$nom."</th>";
                        echo "<td align='right'>$num</td>";
                        echo "</tr>";

                        $csv .= utf8_encode('|___'.$nom).",".$num."\n";
                            
                        $valores_x[] = $nom;
                        $valores_y[] = $num;
                    }
                }
            }
            echo "</table>";
            echo "</td>";
            echo "<td id='highchart' width='700' height='400'>";
            echo "</td>";
            echo "</tr>";

        }
        
        //Número de victimas por Mes
        else if ($reporte ==  6) {
            
            $mes_a = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                echo "<tr>
                        <td valign='top'>
                            <table id='tabla_datos' border=0 class='tabla_grafica_conteo' cellpadding=4 cellspacing=1 width='300' height='400' data-titulo='".$title_reporte[$reporte]."'>
                                <tr class='titulo_tabla_conteo'><td align='center'>Mes</td><td align='center' colspan='2'>N&uacute;mero de víctimas</td></tr>";
                
            $sql = "SELECT sum(cant_victima) as num, MONTH(fecha_reg_even) as mes, YEAR(fecha_reg_even) as aaaa FROM evento_c 
                    INNER JOIN evento_localizacion ON evento_c.id_even = evento_localizacion.id_even 
                    INNER JOIN municipio ON evento_localizacion.id_mun = municipio.id_mun
                    INNER JOIN descripcion_evento ON evento_c.id_even = descripcion_evento.id_even
                    INNER JOIN victima ON descripcion_evento.id_deseven = victima.id_deseven

                    WHERE fecha_reg_even <> '0000-00-00' ";

            //CAT-SUBCAT
            if ($filtro_cat == 1){
                $sql .= " AND id_scateven IN ($id_subcat)";
            }
            
            //FILTRO UBICACION
            if ($ubicacion != 0){
                //Depto
                if ($depto == 1){
                    $sql .= "  AND id_depto = $ubicacion";
                }
                else if($depto == 0){
                    $sql .= "  AND evento_localizacion.id_mun = $ubicacion";
                }
            }
            
            //FECHA
            $sql .= $filtro_fecha;
            
            $sql .= " GROUP BY mes,aaaa ORDER BY fecha_reg_even";
            $rs = $this->conn->OpenRecordset($sql);
            
            //echo $sql;
            
            while ($row = $this->conn->FetchObject($rs)){

                $nom = $row->aaaa.'-'.$mes_a[$row->mes];
                $num = $row->num;
                
                $csv .= utf8_encode($nom).",$num\n";
                
                echo "<tr class='fila_tabla_conteo'><td>$nom</td>";
                echo "<td align='right'>$num</td>";
                echo "</tr>";
            }
            
            echo "</table>";
            echo "</td>";
            
           echo "<td id='highchart' width='700' height='400'>"; 
            echo "</td>";
            echo "</tr>";

        }
            
        // Número de eventos por confrontacion de actores
        else if (in_array($reporte, array(7,10,8,11))) {
            
            $actor_dao = New ActorDAO();

            $ev = ($reporte == 7 || $reporte == 8) ? 'eventos' : 'víctimas';
            
            echo "<tr>
                    <td valign='top'>
                        <table id='tabla_datos' border=0 class='tabla_grafica_conteo' cellpadding=4 cellspacing=1 width='300' height='400' data-titulo='".$title_reporte[$reporte]."'>
                        <tr class='titulo_tabla_conteo'>
                            <td align='center'>Actor</td>
                            <td align='center' colspan='2'>N&uacute;mero de $ev</td>
                        </tr>";
            
            if ($reporte == 7 || $reporte == 10) {
                $sql = "SELECT COUNT(DISTINCT(ad.id_actor)) AS num_actor, GROUP_CONCAT(DISTINCT ad.id_actor ORDER BY id_actor) as ids, GROUP_CONCAT(DISTINCT a.nom_actor SEPARATOR \" | \") as nom,
                               SUM(CANT_VICTIMA) AS num_vic, ec.id_even, de.id_deseven
                        FROM actor_descevento AS ad
                        JOIN actor AS a USING(ID_ACTOR)
                        JOIN descripcion_evento AS de USING(ID_DESEVEN)
                        JOIN evento_c As ec USING(ID_EVEN)
                        JOIN evento_localizacion USING(ID_EVEN)
                        JOIN municipio USING(ID_MUN)
                        LEFT JOIN victima AS v USING(ID_DESEVEN)
                        WHERE nivel = 1 ";
            }
            
            if ($reporte == 8 || $reporte == 11) {
                $sql = "SELECT COUNT(DISTINCT de.ID_EVEN) AS num, DESC_ETNIA AS nom, SUM(CANT_VICTIMA) AS num_vic
                        FROM victima AS v 
                        JOIN sub_etnia AS se USING(ID_SUBETNIA)
                        JOIN etnia USING(ID_ETNIA)
                        JOIN descripcion_evento AS de USING(ID_DESEVEN)
                        JOIN evento_c USING(ID_EVEN)
                        JOIN evento_localizacion USING(ID_EVEN)
                        JOIN municipio USING(ID_MUN)
                        WHERE 1=1 ";
            }

            //CAT-SUBCAT
            if ($filtro_cat == 1){
                $sql .= " AND id_scateven IN ($id_subcat)";
            }
            
            //FILTRO UBICACION
            if ($ubicacion != 0){
                //Depto
                if ($depto == 1){
                    $sql .= "  AND id_depto = $ubicacion";
                 }
                else if($depto == 0){
                    $sql .= "  AND evento_localizacion.id_mun = $ubicacion";
                }
                
            }

            //FECHA
            $sql .= $filtro_fecha;
            
            
            if ($reporte == 7 || $reporte == 10) {
                $sql .= " GROUP BY id_deseven";
            }
            else {
                $sql .= " GROUP BY se.id_etnia";
            }

            //echo $sql;

            $rs = $this->conn->OpenRecordset($sql);
            
            $arbol = array();
            $ids_eventos = array();
            while ($row = $this->conn->FetchObject($rs)) {

                $nom = $row->nom;

                if ($reporte == 7 || $reporte == 10) {
                    if ($row->num_actor > 1) {
                        $ids = $row->ids;
                        
                        if (!isset($arbol[$ids]['num'])) {
                            $arbol[$ids]['num'] = 0;
                        }

                        if ($reporte == 7) {
                            $num = 0;
                            if (!in_array($row->id_even, $ids_eventos)) {
                                $ids_eventos[] = $row->id_even;
                                $num = 1;
                            }
                        }
                        else {
                            $row->num_vic;
                        }
                        
                            $arbol[$ids]['num'] += $num;
                            $arbol[$ids]['nom'] = $nom;
                    }
                }
                else {
                    $num = ($reporte == 8) ? $row->num : $row->num_vic;
                    $arbol[] = compact('num','nom');
                }

                
            }

            usort($arbol, array($this, 'compare_num'));

            if (empty($arbol)) {
                echo "<tr class='fila_tabla_conteo'><td colspan='2'>No hay info</td></tr>";
            }
            else {
                $a = 0;
                foreach ($arbol as $els) {

                    $nom = $els['nom'];
                    $num = $els['num'];

                    echo "<tr class='fila_tabla_conteo'><td>".$nom."</td>";
                    echo "<td align='right'>".$num."</td>";
                    echo "</tr>";

                    if ($a < 15) {
                        $valores_x[] = $nom;
                        $valores_y[] = $num;
                    }
                    
                    $csv .= utf8_encode($nom).",$num\n";

                    $a++;
                }
            }
            
            echo "</table>";
            echo "</td>";
           echo "<td id='highchart' width='700' height='400'>"; 
            echo "</td>";
            echo "</tr>";

        }

        $fp = $archivo->Abrir($_SERVER['DOCUMENT_ROOT'].$csv_path,'w+');
                    
        $archivo->Escribir($fp,$csv);
        $archivo->Cerrar($fp);
        
        echo "</table>";
        
    }

    private function compare_num($a, $b){
        return $b['num'] - $a['num'];
    }
    
}

?>