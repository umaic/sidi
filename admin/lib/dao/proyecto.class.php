<?
/**
 * DAO de Proyecto
 *
 * Contiene los métodos de la clase Proyecto 
 * @author Ruben A. Rojas C.
 */

Class ProyectoDAO {

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
     * URL para redireccionar después de Insertar, Actualizar o Borrar
     * @var string
     */
    var $url;

    /**
     * Constructor
     * Crea la conexión a la base de datos
     * @access public
     */	
    function ProyectoDAO (){
        $this->conn = MysqlDb::getInstance();
        $this->tabla = "proyecto";
        $this->columna_id = "ID_PROY";
        $this->columna_nombre = "NOM_PROY";
        $this->columna_order = "NOM_PROY";
        $this->num_reg_pag = 10;

        if ($_SESSION['undaf'] == 1)	$this->url = "../index_undaf.php?m_e=home";
        else							$this->url = "index.php?m_e=proyecto&accion=listar&class=ProyectoDAO&method=ListarTabla&param=";
    }

    /**
     * Consulta los datos de una Proyecto
     * @access public
     * @param int $id ID del Proyecto
     * @return VO
     */	
    function Get($id){
        $sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
        $rs = $this->conn->OpenRecordset($sql);
        $row_rs = $this->conn->FetchObject($rs);

        //Crea un VO
        $vo = New Proyecto();

        //Carga el VO
        $vo = $this->GetFromResult($vo,$row_rs);

        //Retorna el VO
        return $vo;
    }

    /**
     * Retorna el max ID
     * @access public
     * @return int
     */	
    function GetMaxID(){
        $sql = "SELECT max(ID_PROY) as maxid FROM ".$this->tabla;
        $rs = $this->conn->OpenRecordset($sql);
        if($row_rs = $this->conn->FetchRow($rs)){
            return $row_rs[0];
        }
        else{
            return 0;
        }
    }

    /**
     * Consulta el nombre del Proyecto
     * @access public
     * @param int $id ID del Proyecto
     * @return VO
     */
    function GetName($id){
        $sql = "SELECT ".$this->columna_nombre." FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
        $rs = $this->conn->OpenRecordset($sql);
        $row_rs = $this->conn->FetchRow($rs);

        //Retorna el VO
        return $row_rs[0];
    }	

    /**
     * Consulta si tiene cobertura nacional el Proyecto
     * @access public
     * @param int $id ID del Proyecto
     * @return VO
     */
    function getCoberturaNacional($id){
        $sql = "SELECT cobertura_nal_proy FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
        $rs = $this->conn->OpenRecordset($sql);
        $row_rs = $this->conn->FetchRow($rs);

        //Retorna el VO
        return $row_rs[0];
    }	

    /**
     * Consulta los mpios que cubre el proyecto
     * @access public
     * @param int $id ID del Proyecto
     * @return array
     */
    function getMpiosCobertura($id){

        $arr = Array();

        if ($this->getCoberturaNacional($id) == 1)  $sql_s = "SELECT ID_MUN FROM municipio";
        else			 							$sql_s = "SELECT ID_MUN FROM mun_proy WHERE ID_PROY = ".$id;

        $rs_s = $this->conn->OpenRecordset($sql_s);
        while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            $arr[] = $row_rs_s[0];
        }

        return $arr;
    }

    /**
     * Retorna el numero de Registros
     * @access public
     * @return int
     */	
    function numRecords($condicion){
        $sql = "SELECT count(ID_PROY) as num FROM ".$this->tabla;
        if ($condicion != ""){
            $sql .= " WHERE ".$condicion;
        }
        $rs = $this->conn->OpenRecordset($sql);
        $row_rs = $this->conn->FetchRow($rs);

        return $row_rs[0];
    }

    /**
     * Consulta los datos de los Proyecto que cumplen una condición
     * @access public
     * @param string $condicion Condición que deben cumplir los Proyecto y que se agrega en el SQL statement.
     * @return array Arreglo de VOs
     */	
    function GetAllArray($condicion,$limit='',$order_by=''){
        $sql = "SELECT * FROM ".$this->tabla;
        if ($condicion != ""){
            $sql .= " WHERE ".$condicion;
        }

        //ORDER
        if ($order_by != ""){
            $sql .= " ORDER BY ".$order_by;
        }
        else{
            $sql .= " ORDER BY ".$this->columna_order;
        }

        //LIMIT
        if ($limit != ""){
            $sql .= " LIMIT ".$limit;
        }

        $array = Array();

        $rs = $this->conn->OpenRecordset($sql);
        while ($row_rs = $this->conn->FetchObject($rs)){
            //Crea un VO
            $vo = New Proyecto();
            //Carga el VO
            $vo = $this->GetFromResult($vo,$row_rs);
            //Carga el arreglo
            $array[] = $vo;
        }  
        //Retorna el Arreglo de VO
        return $array;
    }

    /**
     * Consulta los ID de las Organizacion que cumplen una condición
     * @access public
     * @param string $condicion Condicin que deben cumplir los Organizacion y que se agrega en el SQL statement.
     * @return array Arreglo de VOs
     */
    function GetAllArrayID($condicion,$limit,$order_by){

        $sql = "SELECT ".$this->columna_id." FROM ".$this->tabla;
        if ($condicion != ""){
            $sql .= " WHERE ".$condicion;
        }

        //ORDER
        if ($order_by != ""){
            $sql .= " ORDER BY ".$order_by;
        }
        else{
            $sql .= " ORDER BY ".$this->columna_order;
        }

        //LIMIT
        if ($limit != ""){
            $sql .= " LIMIT ".$limit;
        }


        $array = Array();

        $rs = $this->conn->OpenRecordset($sql);
        while ($row_rs = $this->conn->FetchRow($rs)){
            //Carga el arreglo
            $array[] = $row_rs[0];
        }
        //Retorna el Arreglo de VO
        return $array;
    }	

    /* Consulta los ID de los Proyectos ejecutados por las organizaciones dadas
     * @access public
     * @param array $id_orgs
     * @param string $condicion
     * @param string $limit
     * @param string $order_by
     * @return array Arreglo de IDs
     */
    function GetIDByEjecutor($id_orgs,$condicion='',$limit='',$order_by=''){

        $id_orgs_s = implode(",",$id_orgs);

        $sql = "SELECT vinculorgpro.".$this->columna_id." FROM vinculorgpro 

            JOIN $this->tabla USING ($this->columna_id)";

        //Filtro de tema en home
        $cond_t = '';
        if (isset($_GET["id_t"])){
            $sql .= " JOIN proyecto_tema USING($this->columna_id)";
            $cond_t = " AND id_tema = ".$_GET["id_t"];
        }

        //Filtro de depto en home
        $cond_d = '';
        if (isset($_GET["id_d"])){
            $sql .= " JOIN depto_proy USING($this->columna_id)";
            $cond_d = " AND id_depto = ".$_GET["id_d"] ;
        }

        $sql .= " WHERE vinculorgpro.id_org IN ($id_orgs_s) AND id_tipo_vinorgpro = 1 ";

        $sql .= $cond_t.$cond_d;

        if ($condicion != ""){
            $sql .= " AND ".$condicion;
        }

        //echo $sql;

        //ORDER
        if ($order_by != ""){
            $sql .= " ORDER BY ".$order_by;
        }
        else{
            $sql .= " ORDER BY ".$this->columna_order;
        }

        //LIMIT
        if ($limit != ""){
            $sql .= " LIMIT ".$limit;
        }

        //echo $sql;

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
     * Lista los Proyecto que cumplen la condición en el formato dado
     * @access public
     * @param string $formato Formato en el que se listarán los Proyecto, puede ser Tabla o ComboSelect
     * @param int $valor_combo ID del Proyecto que será selccionado cuando el formato es ComboSelect
     * @param string $condicion Condición que deben cumplir los Proyecto y que se agrega en el SQL statement.
     */			
    function ListarCombo($formato,$valor_combo='',$condicion=''){
        $arr = $this->GetAllArray($condicion,'','');
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
     * Lista las Proyectoes en una Tabla
     * @access public
     */			
    function ListarTabla(){

        //INICIALIZACION DE VARIABLES
        $depto_dao = New DeptoDAO();
        $mun_dao = New MunicipioDAO();
        $estado_dao = New EstadoProyectoDAO();
        $moneda_dao = New MonedaDAO();
        $org_dao = new OrganizacionDAO();
        $condicion = '';
        $filtro_nom = '';
        $filtro_estado = '';
        $filtro_org_e = '';
        $li = '';
        $num_arr = 0;
        $url = 'index.php?m_e=proyecto&accion=listar&class=ProyectoDAO&method=ListarTabla&param=';

        // FILTROS
        // Estado
        if (isset($_GET["filtro_estado"])){
            $filtro_estado = $_GET["filtro_estado"];

            if ($condicion != '')	$condicion .= ' AND';
            $condicion .= ' id_estp = '.$filtro_estado;

            $url .= "&filtro_estado=$filtro_estado";
        }

        // Org. Ejecutora
        if (isset($_GET["filtro_org_e"])){
            $filtro_org_e = $_GET["filtro_org_e"];

            if ($condicion != '')	$condicion .= ' AND';
            $condicion .= ' id_org = '.$filtro_org_e;

            $url .= "&filtro_org_e=$filtro_org_e";
        }
        $url_todos = $url;

        // Nombre
        if (isset($_GET["filtro_nom"])){
            $filtro_nom = $_GET["filtro_nom"];

            if ($condicion != '')	$condicion .= ' AND';
            $condicion .= " nom_proy LIKE '%$filtro_nom%'";

            $url .= "&filtro_nom=$filtro_nom";
            $url_todos = $url;
        }
        // Indice
        else {
            $li = (isset($_GET["li"])) ? $_GET["li"] : $indice[0];

            if ($condicion != '')	$condicion .= ' AND ';

            $condicion .= "nom_proy LIKE '$li%'";

            $url_todos = $url;
            if ($li != $indice[0]) $url .= "&li=$li";
        }

        // Indice
        $indice = $this->getLetrasIniciales($condicion);

        $sql = "SELECT DISTINCT(id_proy) FROM proyecto";
        if ($filtro_org_e != '')	$sql .= ' JOIN vinculorgpro USING(id_proy)';
        if ($condicion != '')	$sql .= " WHERE $condicion";

        $rs = $this->conn->OpenRecordset($sql);
        while ($row = $this->conn->FetchRow($rs)){
            $arr[] = $this->Get($row[0]);
            $num_arr++;
        }

        echo "<table width='95%' class='tabla_lista'>";

        if ($num_arr > 0){

            // INDICE
            //Todos
            $class = ($li == '') ? 'a_big' : 'a_normal' ;

            echo '<tr><td colspan="3">';
            echo "<a href='$url_todos' class='$class'>Todos</a>&nbsp;&nbsp;";

            foreach ($indice as $letra){
                $class = (strtolower($letra) == strtolower($li)) ? 'a_big' : 'a_normal' ;

                echo "<a href='$url&li=$letra' class='$class'>".strtoupper($letra)."</a>";
                echo "&nbsp;&nbsp;";
            }

            echo "</td>";
            // Link	insertar
            echo "<td colspan='3' align='right'><img src='images/home/insertar.png'>&nbsp;<a href='index.php?m_e=proyecto&accion=insertar'>Crear Proyecto</a>&nbsp;&nbsp;[$num_arr Registros]</td></tr>";

            // Filtros
            echo "<tr>
                <td><input type='button' value='Filtrar' class='boton' onclick='filtrarListaProyAlimentacion($li)'></td>
                <td><input type='text' id='filtro_nom' class='textfield' size='60' value=$filtro_nom></td>
                <td>
                <select id='filtro_estado' class='select'>
                <option value=''>Todos</option>";

            $estado_dao->ListarCombo('combo',$filtro_estado,'');

            echo "</select></td>
                <td colspan='3'><select id='filtro_org_e' class='select' style='width:300px'>
                <option value=''>Todas</option>";

            $orgs_e_total = $this->getOrgEjecutoraTotal();

            foreach ($orgs_e_total as $id_org_e_total){
                $nom_org_e_total = $org_dao->GetName($id_org_e_total);
                echo "<option value=".$id_org_e_total;
                if ($id_org_e_total == $filtro_org_e)	echo ' selected ';
                echo ">$nom_org_e_total</option>";
            }

            echo "</select></td></tr>";				


            echo "<tr class='titulo_lista'>
                <td width='60'>ID</td>
                <td>Nombre</td>
                <td>Estado</td>
                <td>Org. Ejecutora</td>
                <td width='80'>Inicio</td>
                <td>Duraci&oacute;n meses</td>
                </tr>";

            foreach ($arr as $proy){	

                //NOMBRE DEL ESTADO
                $estado = $estado_dao->Get($proy->id_estp);
                $nom_estado = $estado->nombre;

                //NOMBRE DE LA MONEDA
                $moneda = $moneda_dao->Get($proy->id_mon);
                $nom_moneda = $moneda->nombre;

                // ORG EJECUTORA
                $org_e = $org_dao->Get($proy->id_orgs_e[0]);

                echo "<tr class='fila_lista'>";
                echo "<td>";
                echo "<a href='index.php?accion=borrar&class=ProyectoDAO&method=Borrar&param=".$proy->id_proy."' onclick=\"return confirm('Está seguro que desea borrar el Proyecto: ".$proy->nom_proy."');\"><img src='images/trash.png' title='Borrar'></a>&nbsp;";
                echo $proy->id_proy;
                echo "</td>";
                echo "<td>";
                echo "<a href='../download_pdf.php?c=3&id=$proy->id_proy'><img src='images/pdf.gif' title='Ficha PDF'></a>";
                echo "&nbsp;<a href='index.php?accion=actualizar&id=".$proy->id_proy."'>".$proy->nom_proy."</a>";
                echo "</td>";
                echo "<td>$nom_estado</td>";
                echo "<td>".$org_e->nom."</td>";
                echo "<td>".$proy->inicio_proy."</td>";
                echo "<td>".$proy->duracion_proy."</td>";
                echo "</tr>";
            }
        }

        else{
            echo "<tr><td align='center'><br><b>NO SE ENCONTRARON PROYECTOS</b></td></tr>";
            echo "<tr><td align='center'><br><a href='javascript:history.back(1)'>Regresar</a></td></tr>";
        }
        echo "</table>";
    }

    /**
     * Reporte de Proyectoes filtradas por cobertura geográfica en formato HTML
     * @param Array $id_depto ID de los departamentos que filtran
     * @param Array $id_depto ID de los municipios que filtran
     * @param Array $columna Columnas a mostrar en el reporte
     * @access public
     */			
    function ReporteCoberturaGeograficaHTML($id_depto,$id_mun,$columna){

        $depto_dao = New DeptoDAO();

        //RECORRE LOS DEPARTAMENTOS
        foreach ($id_depto as $id_d){
            $depto = $depto_dao->Get($id_d);

            $where = "ID_DEPTO = '".$id_d."'";

            if (count($id_mun) > 0){
                $where .= "AND ID_MUN IN (".$id_d.")";
            }
            $arr = $this->GetAllArray($where,'','');
            $num_arr = count($arr);

            ////TITULO DEL DEPARTAMENTO
            if ($num_arr > 0){
                echo "<table align='center' cellspacing='1' cellpadding='3'>";
                echo "<tr><td><b>DEPARTAMENTO: ".$depto->nombre."</b></td></tr>";
                echo "</table>";

                ////TITLES
                echo "<table align='center' cellspacing='1' cellpadding='3' class='tabla_reporte'>
                    <tr class='titulo_lista'>
                    <td align='center' width='70'><b>Departamento</b></td>
                    <td align='center' width='70'><b>Municipio</b></td>
                    <td align='center' width='100'><b>Lugar</b></td>
                    <td align='center' width='100'><b>Tipo de Proyecto</b></td>
                    <td align='center' width='10'><b>Actores</b></td>
                    <td align='center' width='100'><b>Consecuencias Humanitarias</b></td>
                    <td align='center' width='70'><b>Riesgos Humanitarios</b></td>
                    <td align='center' width='200'><b>Descripción</b></td>
                    <td align='center' width='70'><b>Fecha registro</b></td>
                    </tr>";

                foreach($arr as $arr_vo){
                    echo "<tr class='fila_lista'>";

                    ////DEPARTAMENTOS
                    echo "<td>";
                    $z=0;
                    foreach($arr_vo->id_deptos as $id){
                        $vo = $depto_dao->Get($id);
                        if ($z==0)  echo $vo->nombre;
                        else				echo ", ".$vo->nombre;
                        $z++;
                    }
                    echo "</td>";
                }
                echo "</table><br>";
            }
        }
    }

    /**
     * Muestra la Información completa de una Organización
     * @access public
     * @param id $id Id de la Proyecto
     */			
    function Ver($id){

        //INICIALIZACION DE VARIABLES
        $tema_dao = New TemaDAO();
        $estado_dao = New EstadoProyectoDAO();
        $contacto_dao = New ContactoDAO();
        $org_dao = New OrganizacionDAO();
        $depto_dao = New DeptoDAO();
        $mun_dao = New MunicipioDAO();
        $region_dao = New RegionDAO();
        $sector_dao = New SectorDAO();
        $poblacion_dao = New PoblacionDAO();
        $poblado_dao = New PobladoDAO();
        $resguardo_dao = New ResguardoDAO();
        $parque_nat_dao = New ParqueNatDAO();
        $div_afro_dao = New DivAfroDAO();
        $moneda_dao = New MonedaDAO();
        $enfoque_dao = New EnfoqueDAO();


        //CONSULTA LA INFO DE LA ORG.
        $proyecto = $this->Get($id);

        //CODIGO
        if ($proyecto->codigo == "")	$proyecto->codigo = "-";

        //ESTADO
        if ($proyecto->id_estp != 0){
            $vo = $estado_dao->Get($proyecto->id_estp);
            $estado = $vo->nombre;
        }

        //DESC
        //if ($proyecto->desc == "")	$proyecto->desc = "-";

        //OBJ
        if ($proyecto->obj == "")	$proyecto->obj = "-";

        //F. INI
        if ($proyecto->fecha_ini == "0000-00-00")	$proyecto->fecha_ini = "No especificada";

        //F. FINAL
        if ($proyecto->fecha_fin == "0000-00-00")	$proyecto->fecha_fin = "No especificada";

        //DURACION
        if ($proyecto->duracion == "")	$proyecto->duracion = "-";

        //COSTO
        if ($proyecto->costo != "" && $proyecto->costo > 0){
            $moneda = $moneda_dao->Get($proyecto->id_moneda);
            $proyecto->costo = $moneda->nombre." ".$proyecto->costo;
        }
        else{
            $proyecto->costo = "" ;
        }

        echo "<table cellspacing=1 cellpadding=3 class='tabla_consulta' border=0 align='center'>";
        echo "<tr class='titulo_lista'><td align='center' colspan='6'>INFORMACION DE PROYECTO</td></tr>";
        echo "<tr><td class='tabla_consulta' width='150'><b>Nombre</b></td><td class='tabla_consulta' width='500'>".$proyecto->nombre."</td><td class='tabla_consulta'><b>Código</b></td><td class='tabla_consulta'>".$proyecto->codigo."</td></tr>";
        echo "<tr><td class='tabla_consulta'><b>Estado</b></td><td class='tabla_consulta'>".$estado."</td></tr>";
        //TEMAS
        echo "<tr><td class='tabla_consulta'><b>Tema</b></td>";
        $s = 0;
        foreach($proyecto->id_temas as $id){
            if (fmod($s,8) == 0)	echo "<td class='tabla_consulta'>";
            $vo = $tema_dao->Get($id);
            echo "- ".$vo->nombre."<br>";
            $s++;
        }
        echo "<tr><td class='tabla_consulta'><b>Descripción</b></td><td class='tabla_consulta'>".$proyecto->desc."</td></tr>";
        echo "<tr><td class='tabla_consulta'><b>Objetivo</b></td><td class='tabla_consulta'>".$proyecto->obj."</td></tr>";
        echo "<tr><td class='tabla_consulta' width='200'><b>Fecha</b></td><td class='tabla_consulta'><b>Inicio</b>: ".$proyecto->fecha_ini."&nbsp;&nbsp;-&nbsp;&nbsp;<b>Finalización</b>: ".$proyecto->fecha_fin."</td><td class='tabla_consulta'><b>Duración en meses</b></td><td class='tabla_consulta'>".$proyecto->duracion."</td></tr>";
        echo "<tr><td class='tabla_consulta'><b>Costo</b></td><td class='tabla_consulta'>".$proyecto->costo."</td></tr>";

        //CONTACTOS
        echo "<tr><td class='tabla_consulta'><b>Contáctos</b></td>";
        $s = 0;
        if (count ($proyecto->id_contactos) > 0){
            echo "<td class='tabla_consulta' colspan=3><table cellspacing=1 cellpadding=3>";
            echo "<tr class='titulo_lista'><td width='150'><b>Nombre</b></td><td width='150'><b>Teléfono</b></td><td width='150'><b>Email</b></td></tr>";
        }
        foreach($proyecto->id_contactos as $id){
            echo "<tr class='fila_lista'>";
            $vo = $contacto_dao->Get($id);
            echo "<td>".$vo->nombre."</td>";
            echo "<td>".$vo->tel."</td>";
            echo "<td><a href='mailto:".$vo->email."'>".$vo->email."</a></td>";
            echo "</tr>";
            $s++;
        }
        if (count ($proyecto->id_contactos) > 0){
            echo "</table></td>";
        }

        echo "</tr>";

        //ORGS. DONANTES
        echo "<tr><td class='tabla_consulta'><b>Organizaciones Donantes</b></td>";
        $s = 0;
        if (count ($proyecto->id_orgs_d) > 0){
            echo "<td class='tabla_consulta' colspan=3><table cellspacing=1 cellpadding=3>";
            echo "<tr class='titulo_lista'><td width='200'><b>Organización</b></td><td width='100'><b>Valor aporte</b></td></tr>";
        }
        foreach($proyecto->id_orgs_d as $id){
            echo "<tr class='fila_lista'>";
            $vo = $org_dao->Get($id);
            echo "<td>".$vo->nom."</td>";
            echo "<td>".$proyecto->id_orgs_d_valor_ap[$s]."</td>";
            echo "</tr>";
            $s++;
        }
        if (count ($proyecto->id_orgs_d) > 0){
            echo "</table></td>";
        }

        echo "</tr>";

        //ORGS. EJECUTORAS
        echo "<tr><td class='tabla_consulta'><b>Organizaciones Ejecutoras</b></td>";
        $s = 0;
        foreach($proyecto->id_orgs_e as $id){
            if (fmod($s,8) == 0)	echo "<td class='tabla_consulta'>";
            $vo = $org_dao->Get($id);
            echo "- ".$vo->nom."<br>";
            $s++;
        }

        //SECTOR
        echo "<tr><td class='tabla_consulta'><b>Sector</b></td>";
        $s = 0;
        foreach($proyecto->id_sectores as $id){
            if (fmod($s,8) == 0)	echo "<td class='tabla_consulta'>";
            $vo = $sector_dao->Get($id);
            echo "- ".$vo->nombre_es."<br>";
            $s++;
        }

        //ENFOQUE
        echo "<tr><td class='tabla_consulta'><b>Enfoque</b></td>";
        $s = 0;
        foreach($proyecto->id_enfoques as $id){
            if (fmod($s,8) == 0)	echo "<td class='tabla_consulta'>";
            $vo = $enfoque_dao->Get($id);
            echo "- ".$vo->nombre_es."<br>";
            $s++;
        }

        //POBLACION BENEFICIADAS
        echo "<tr><td class='tabla_consulta'><b>Población Beneficiada</b></td>";
        $s = 0;
        if (count ($proyecto->id_beneficiarios) > 0){
            echo "<td class='tabla_consulta' colspan=3><table cellspacing=1 cellpadding=3>";
            echo "<tr class='titulo_lista'><td width='300'><b>Población</b></td><td><b>Número de personas beneficiadas</b></td></tr>";
        }
        foreach($proyecto->id_beneficiarios as $id){
            echo "<tr class='fila_lista'>";
            $vo = $poblacion_dao->Get($id);
            echo "<td>".$vo->nombre_es."</td>";
            echo "<td>".$proyecto->cant_per[$s]."</td>";
            echo "</tr>";
            $s++;
        }
        if (count ($proyecto->id_beneficiarios) > 0){
            echo "</table></td>";
        }

        echo "</tr>";

        //COBERTURA POR DEPARTAMENTO
        echo "<tr><td class='tabla_consulta'><b>Cobertura Geográfica por Departamento</b></td>";
        $s = 0;
        echo "<td class='tabla_consulta' colspan='3'>";
        foreach($proyecto->id_deptos as $id){
            $vo = $depto_dao->Get($id);
            echo "- ".$vo->nombre."<br>";
            $s++;
        }
        echo "</td></tr>";

        //COBERTURA POR MUNICIPIO
        echo "<tr><td class='tabla_consulta'><b>Cobertura Geográfica por Municipio</b></td>";
        $s = 0;
        echo "<td class='tabla_consulta' colspan='3'><table cellspacing='0' cellpadding='3'><tr>";
        foreach($proyecto->id_muns as $id){
            if (fmod($s,40) == 0)	echo "<td valign='top'>";
            $vo = $mun_dao->Get($id);
            echo "- ".$vo->nombre."<br>";
            $s++;
        }
        echo "</td></tr></table>";
        echo "</td></tr>";

        //COBERTURA POR REGION
        echo "<tr><td class='tabla_consulta'><b>Cobertura Geográfica por Región</b></td>";
        $s = 0;
        echo "<td class='tabla_consulta' colspan='3'>";
        foreach($proyecto->id_regiones as $id){
            $vo = $region_dao->Get($id);
            echo "- ".$vo->nombre."<br>";
            $s++;
        }
        echo "</td></tr>";

        //COBERTURA POR POBLADO
        if (count($proyecto->id_poblados) > 0){
            echo "<tr><td class='tabla_consulta'><b>Cobertura Geográfica por Poblado</b></td>";
            $s = 0;
            echo "<td class='tabla_consulta' colspan='3'>";
            foreach($proyecto->id_poblados as $id){
                $vo = $poblado_dao->Get($id);
                echo "- ".$vo->nombre."<br>";
                $s++;
            }
            echo "</td></tr>";
        }

        //COBERTURA POR PARQUE NAT.
        if (count($proyecto->id_parques) > 0){
            echo "<tr><td class='tabla_consulta'><b>Cobertura Geográfica por Parque Natural</b></td>";
            $s = 0;
            echo "<td class='tabla_consulta' colspan='3'>";
            foreach($proyecto->id_parques as $id){
                $vo = $parque_nat_dao->Get($id);
                echo "- ".$vo->nombre."<br>";
                $s++;
            }
            echo "</td></tr>";
        }

        //COBERTURA POR RESGUARDO
        if (count($proyecto->id_resguardos) > 0){
            echo "<tr><td class='tabla_consulta'><b>Cobertura Geográfica por Resguardo</b></td>";
            $s = 0;
            echo "<td class='tabla_consulta' colspan='3'>";
            foreach($proyecto->id_resguardos as $id){
                $vo = $resguardo_dao->Get($id);
                echo "- ".$vo->nombre."<br>";
                $s++;
            }
            echo "</td></tr>";
        }

        //COBERTURA POR DIV. AFRO
        if (count($proyecto->id_divisiones_afro) > 0){
            echo "<tr><td class='tabla_consulta'><b>Cobertura Geográfica por División Afro</b></td>";
            $s = 0;
            echo "<td class='tabla_consulta' colspan='3'>";
            foreach($proyecto->id_divisiones_afro as $id){
                $vo = $div_afro_dao->Get($id);
                echo "- ".$vo->nombre."<br>";
                $s++;
            }
            echo "</td></tr>";
        }

        echo "</table>";

    }

    /**
     * Imprime en pantalla los datos del Proyecto
     * @access public
     * @param object $vo Proyecto que se va a imprimir
     * @param string $formato Formato en el que se listarán los Proyecto, puede ser Tabla o ComboSelect
     * @param int $valor_combo ID del Proyecto que será selccionado cuando el formato es ComboSelect
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
     * Carga un VO de Proyecto con los datos de la consulta
     * @access public
     * @param object $vo VO de Proyecto que se va a recibir los datos
     * @param object $Resultset Resource de la consulta
     * @return object $vo VO de Proyecto con los datos
     */			
    function GetFromResult ($vo,$Result){

        $vo->id_proy = $Result->ID_PROY;
        $vo->id_mon = $Result->ID_MON;
        $vo->id_estp = $Result->ID_ESTP;
        $vo->nom_proy = $Result->NOM_PROY;
        $vo->cod_proy = $Result->COD_PROY;
        $vo->des_proy = $Result->DES_PROY;
        $vo->obj_proy = $Result->OBJ_PROY;
        $vo->inicio_proy = $Result->INICIO_PROY;
        $vo->fin_proy = $Result->FIN_PROY;
        $vo->actua_proy = $Result->ACTUA_PROY;
        $vo->costo_proy = $Result->COSTO_PROY;
        $vo->duracion_proy = $Result->DURACION_PROY;
        $vo->info_conf_proy = $Result->INFO_CONF_PROY;
        $vo->staff_nal_proy = $Result->STAFF_NAL_PROY;
        $vo->staff_intal_proy = $Result->STAFF_INTAL_PROY;
        $vo->cobertura_nal_proy = $Result->COBERTURA_NAL_PROY;
        $vo->cant_benf_proy = $Result->CANT_BENF_PROY;
        //$vo->desc_cant_benf_proy = $Result->DESC_CANT_BENF_PROY;
        $vo->valor_aporte_donantes = $Result->VALOR_APORTE_DONANTES;
        $vo->valor_aporte_socios = $Result->VALOR_APORTE_SOCIOS;
        $vo->info_extra_donantes = $Result->INFO_EXTRA_DONANTES;
        $vo->info_extra_socios = $Result->INFO_EXTRA_SOCIOS;
        $vo->joint_programme_proy = $Result->JOINT_PROGRAMME_PROY;
        $vo->mou_proy = $Result->MOU_PROY;
        $vo->acuerdo_coop_proy = $Result->ACUERDO_COOP_PROY;
        $vo->interv_ind_proy = $Result->INTERV_IND_PROY;
        $vo->otro_cual_benf_proy = $Result->OTRO_CUAL_BENF_PROY;
        $vo->si_proy = $Result->SI_PROY;

        $id = $vo->id_proy;

        //TEMAS
        $arr = Array();
        $arr_t = Array();
        $sql_s = "SELECT ID_TEMA, DESC_PROY_TEMA FROM proyecto_tema JOIN tema USING(id_tema) WHERE ID_PROY = $id AND id_papa = 0";
        $rs_s = $this->conn->OpenRecordset($sql_s);
        while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            $id_p = $row_rs_s[0];

            $arr[$id_p] = array();

            //Texto extra
            $arr_t[$id_p] = $row_rs_s[1];

            //Hijos
            $sql_h = "SELECT ID_TEMA FROM proyecto_tema JOIN tema USING(id_tema) WHERE ID_PROY = $id AND id_papa = $id_p";
            $rs_h = $this->conn->OpenRecordset($sql_h);
            while ($row_h = $this->conn->FetchRow($rs_h)){
                $id_h = $row_h[0];
                $arr[$id_p]["hijos"][] = $id_h;

                //Nietos
                $sql_n = "SELECT ID_TEMA FROM proyecto_tema INNER JOIN tema USING(id_tema) WHERE ID_PROY = $id AND id_papa = $id_h";
                $rs_n = $this->conn->OpenRecordset($sql_n);
                while ($row_n = $this->conn->FetchRow($rs_n)){
                    $arr[$id_p]["nietos"][] = $row_n[0];
                }

            }

        }
        $vo->id_temas = $arr;
        $vo->texto_extra_tema = $arr_t;

            /*
            //SECTORES
            $arr = Array();
            $sql_s = "SELECT ID_COMP FROM sector_proy WHERE ID_PROY = ".$id;
            $rs_s = $this->conn->OpenRecordset($sql_s);
            while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            array_push($arr,$row_rs_s[0]);
            }
            $vo->id_sectores = $arr;

            //ENFOQUES
            $arr = Array();
            $sql_s = "SELECT ID_ENF FROM enfoque_proy WHERE ID_PROY = ".$id;
            $rs_s = $this->conn->OpenRecordset($sql_s);
            while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            array_push($arr,$row_rs_s[0]);
            }
            $vo->id_enfoques = $arr;

            //CONTACTOS
            $arr = Array();
            $sql_s = "SELECT ID_CONP FROM proyecto_conta WHERE ID_PROY = ".$id;
            $rs_s = $this->conn->OpenRecordset($sql_s);
            while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            array_push($arr,$row_rs_s[0]);
            }
            $vo->id_contactos = $arr;
             */

        //ORGS. EJECUTORAS
        $arr = Array();
        $sql_s = "SELECT ID_ORG FROM vinculorgpro WHERE ID_PROY = ".$id." AND ID_TIPO_VINORGPRO = 1";
        $rs_s = $this->conn->OpenRecordset($sql_s);
        while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            $arr[] = $row_rs_s[0];
        }
        $vo->id_orgs_e = $arr;

        //ORGS. DONANTES
        $arr = Array();
        //$arr_v = Array();
        $sql_s = "SELECT ID_ORG, VALOR_APORTE FROM vinculorgpro WHERE ID_PROY = ".$id." AND ID_TIPO_VINORGPRO = 2";
        $rs_s = $this->conn->OpenRecordset($sql_s);
        while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            $arr[] = $row_rs_s[0];
            //$arr_v = $row_rs_s[1];
        }
        $vo->id_orgs_d = $arr;
        //$vo->id_orgs_d_valor_ap = $arr_v;

        //ORGS. SOCIOS
        $arr = Array();
        $sql_s = "SELECT ID_ORG, VALOR_APORTE FROM vinculorgpro WHERE ID_PROY = ".$id." AND ID_TIPO_VINORGPRO = 3";
        $rs_s = $this->conn->OpenRecordset($sql_s);
        while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            $arr[] = $row_rs_s[0];
        }
        $vo->id_orgs_s = $arr;

        //ORGS. TRABAJO COORDINADO
        $arr = Array();
        $sql_s = "SELECT ID_ORG, VALOR_APORTE FROM vinculorgpro WHERE ID_PROY = ".$id." AND ID_TIPO_VINORGPRO = 4";
        $rs_s = $this->conn->OpenRecordset($sql_s);
        while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            $arr[] = $row_rs_s[0];
            $arr_valor_ap[$row_rs_s[0]] = $row_rs_s[1];
        }
        $vo->id_orgs_coor = $arr;
        $vo->id_orgs_coor_valor_ap = $arr_valor_ap;

        //ORGS-OFICINA DESDE LA QUE SE CUBRE EL PROYECTO
        $arr = Array();
        $sql_s = "SELECT ID_ORG, VALOR_APORTE FROM vinculorgpro WHERE ID_PROY = ".$id." AND ID_TIPO_VINORGPRO = 5";
        $rs_s = $this->conn->OpenRecordset($sql_s);
        while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            $arr[] = $row_rs_s[0];
        }
        $vo->id_orgs_cubre = $arr;

        //POBLACION BENEFICIADA DIRECTOS
        $arr = Array();
        //$arr_c = Array();
        $sql_s = "SELECT ID_POBLA, CANT_PER FROM proyecto_beneficiario WHERE ID_PROY = $id AND TIPO_REL = 1";
        $rs_s = $this->conn->OpenRecordset($sql_s);
        while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            $arr[] = $row_rs_s[0];
            //$arr_c = $row_rs_s[1];
        }
        $vo->id_beneficiarios = $arr;
        //$vo->cant_per = $arr_c;

        //POBLACION BENEFICIADA INDIRECTOS
        $arr = Array();
        $sql_s = "SELECT ID_POBLA, CANT_PER FROM proyecto_beneficiario WHERE ID_PROY = $id AND TIPO_REL = 2";
        $rs_s = $this->conn->OpenRecordset($sql_s);
        while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            $arr[] = $row_rs_s[0];
        }
        $vo->id_beneficiarios_indirectos = $arr;

            /*
            //CONTACTOS
            $arr = Array();
            $sql_s = "SELECT ID_CONP FROM proyecto_conta WHERE ID_PROY = ".$id;
            $rs_s = $this->conn->OpenRecordset($sql_s);
            while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            array_push($arr,$row_rs_s[0]);
            }
            $vo->id_contactos = $arr;
             */

        //DEPARTAMENTOS
        $arr = Array();
        $sql_s = "SELECT ID_DEPTO FROM depto_proy WHERE ID_PROY = ".$id;
        $rs_s = $this->conn->OpenRecordset($sql_s);
        while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            $arr[] = $row_rs_s[0];
        }
        $vo->id_deptos = $arr;

        //MPIOS
        $vo->id_muns = $this->getMpiosCobertura($id);

            /*
            //REGIONES
            $arr = Array();
            $sql_s = "SELECT ID_REG FROM reg_proy WHERE ID_PROY = ".$id;
            $rs_s = $this->conn->OpenRecordset($sql_s);
            while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            array_push($arr,$row_rs_s[0]);
            }
            $vo->id_regiones = $arr;

            //POBLADOS
            $arr = Array();
            $sql_s = "SELECT ID_POB FROM pob_proy WHERE ID_PROY = ".$id;
            $rs_s = $this->conn->OpenRecordset($sql_s);
            while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            array_push($arr,$row_rs_s[0]);
            }
            $vo->id_poblados = $arr;

            //RESGUARDOS
            $arr = Array();
            $sql_s = "SELECT ID_RESGUADRO FROM resg_proy WHERE ID_PROY = ".$id;
            $rs_s = $this->conn->OpenRecordset($sql_s);
            while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            array_push($arr,$row_rs_s[0]);
            }
            $vo->id_resguardos = $arr;

            //PARQUES
            $arr = Array();
            $sql_s = "SELECT ID_PAR_NAT FROM par_nat_proy WHERE ID_PROY = ".$id;
            $rs_s = $this->conn->OpenRecordset($sql_s);
            while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            array_push($arr,$row_rs_s[0]);
            }
            $vo->id_parques = $arr;

            //DIV. AFRO
            $arr = Array();
            $sql_s = "SELECT ID_DIV_AFRO FROM div_afro_proy WHERE ID_PROY = ".$id;
            $rs_s = $this->conn->OpenRecordset($sql_s);
            while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            array_push($arr,$row_rs_s[0]);
            }
            $vo->id_divisiones_afro = $arr;
             */

        return $vo;

    }

    /**
     * Consulta las organizaciones ejecutoras de un proyecto
     * @access public
     * @param int $id_proy ID del proyecto
     * @return array $id_orgs Arreglo con los ID de las orgs ejecutoras
     */			
    function getOrgEjecutora($id_proy){

        //ORGS. EJECUTORAS
        $arr = Array();
        $sql_s = "SELECT ID_ORG FROM vinculorgpro WHERE ID_PROY = ".$id_proy." AND ID_TIPO_VINORGPRO = 1";
        $rs_s = $this->conn->OpenRecordset($sql_s);
        while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            $arr[] = $row_rs_s[0];
        }

        return $arr;
    }

    /**
     * Consulta las organizaciones que son ejecutoras
     * @access public
     * @return array $id_orgs Arreglo con los ID de las orgs ejecutoras
     */			
    function getOrgEjecutoraTotal(){

        //ORGS. EJECUTORAS
        $arr = Array();
        $sql_s = "SELECT DISTINCT(ID_ORG) FROM vinculorgpro JOIN organizacion USING(id_org) WHERE ID_TIPO_VINORGPRO = 1 ORDER BY nom_org";
        $rs_s = $this->conn->OpenRecordset($sql_s);
        while ($row_rs_s = $this->conn->FetchRow($rs_s)){
            $arr[] = $row_rs_s[0];
        }

        return $arr;
    }

    /**
     * Consulta si un proyecto existe
     * @access public
     * @param string $nom Nombre del proyecto
     * @param int $id_org ID de la Organizacion ejecutora
     */		
    function checkExiste($nom,$id_org=0){

        $sql = "SELECT count(id_proy) FROM proyecto LEFT JOIN vinculorgpro USING ($this->columna_id) WHERE nom_proy = '$nom' AND id_org = $id_org AND id_tipo_vinorgpro = 1";
        $rs = $this->conn->OpenRecordset($sql);
        $row = $this->conn->FetchRow($rs);

        return $row[0];
    }


    /**
     * Inserta un Proyecto en la B.D.
     * @access public
     * @param object $depto_vo VO de Proyecto que se va a insertar
     */		
    function Insertar($vo){

        //CONSULTA SI YA EXISTE
        $a = $this->checkExiste($vo->nom_proy,$vo->id_orgs_e[0]);

        if ($a == 0){


            $sql = "INSERT INTO $this->tabla (id_mon,id_estp,nom_proy,cod_proy,des_proy,obj_proy,inicio_proy,fin_proy,actua_proy,costo_proy,duracion_proy,info_conf_proy,staff_nal_proy,staff_intal_proy,cobertura_nal_proy,cant_benf_proy,valor_aporte_donantes,valor_aporte_socios,info_extra_donantes,info_extra_socios,joint_programme_proy,mou_proy,acuerdo_coop_proy,interv_ind_proy,otro_cual_benf_proy,si_proy) 
                VALUES ($vo->id_mon,$vo->id_estp,'$vo->nom_proy','$vo->cod_proy','$vo->des_proy','$vo->obj_proy','$vo->inicio_proy','$vo->fin_proy',now(),$vo->costo_proy,$vo->duracion_proy,$vo->info_conf_proy,$vo->staff_nal_proy,$vo->staff_intal_proy,$vo->cobertura_nal_proy,'$vo->cant_benf_proy','$vo->valor_aporte_donantes','$vo->valor_aporte_socios','$vo->info_extra_donantes','$vo->info_extra_socios',$vo->joint_programme_proy,$vo->mou_proy,$vo->acuerdo_coop_proy,$vo->interv_ind_proy,'$vo->otro_cual_benf_proy','$vo->si_proy')";

            //echo $sql;
            //die;
            $this->conn->Execute($sql);
            $id_proyecto = $this->GetMaxID();

            $this->InsertarTablasUnion($vo,$id_proyecto);
            $this->InsertarTablasUnionCobertura($vo,$id_proyecto);

?>
            <script>
            alert("Proyecto insertado con éxito");
            </script>
<?
        }
        else{
?>
            <script>
            alert("Error - Existe un Proyecto con el mismo nombre");
            location.href = '<?=$this->url;?>';
            </script>
<?
        }
    }

    /**
     * Inserta las tablas de union para el Proyecto en la B.D.
     * @access public
     * @param object $proyecto_vo VO de Proyecto que se va a insertar
     * @param int $id_proyecto ID de la Proyecto que se acaba de insertar
     */		
    function InsertarTablasUnion($proyecto_vo,$id_proyecto){

        //TEMAS
        $arr = $proyecto_vo->id_temas;
        foreach($arr as $id_tema=>$otros){
            $texto = (isset($proyecto_vo->texto_extra_tema[$id_tema])) ? $proyecto_vo->texto_extra_tema[$id_tema] : "";

            $sql = "INSERT INTO proyecto_tema (ID_TEMA,ID_PROY,DESC_PROY_TEMA) VALUES ($id_tema,$id_proyecto,'$texto')";
            //echo $sql;
            $this->conn->Execute($sql);

            //Hijos
            if (isset($proyecto_vo->id_temas[$id_tema]["hijos"])){
                $hijos = $proyecto_vo->id_temas[$id_tema]["hijos"];
                foreach($hijos as $id_hijo){
                    $sql = "INSERT INTO proyecto_tema (ID_TEMA,ID_PROY) VALUES ($id_hijo,$id_proyecto)";
                    $this->conn->Execute($sql);

                    //echo $sql;
                }
            }

            //Nietos
            if (isset($proyecto_vo->id_temas[$id_tema]["nietos"])){
                $nietos = $proyecto_vo->id_temas[$id_tema]["nietos"];
                foreach($nietos as $id_nieto){
                    $sql = "INSERT INTO proyecto_tema (ID_TEMA,ID_PROY) VALUES ($id_nieto,$id_proyecto)";
                    $this->conn->Execute($sql);

                    //echo $sql;
                }
            }

        }

            /*
            //SECTORES
            $arr = $proyecto_vo->id_sectores;
            $num_arr = count($arr);

            for($m=0;$m<$num_arr;$m++){
            $sql = "INSERT INTO sector_proy (ID_COMP,ID_PROY) VALUES (".$arr[$m].",".$id_proyecto.")";
            $this->conn->Execute($sql);
            }

            //ENFOQUE
            $arr = $proyecto_vo->id_enfoques;
            $num_arr = count($arr);

            for($m=0;$m<$num_arr;$m++){
            $sql = "INSERT INTO enfoque_proy (ID_ENF,ID_PROY) VALUES (".$arr[$m].",".$id_proyecto.")";
            $this->conn->Execute($sql);
            //echo $sql;
            }

            //CONTACTOS
            $arr = $proyecto_vo->id_contactos;
            $num_arr = count($arr);

            for($m=0;$m<$num_arr;$m++){
            $sql = "INSERT INTO proyecto_conta (ID_PROY,ID_CONP) VALUES (".$id_proyecto.",".$arr[$m].")";
            $this->conn->Execute($sql);
            }
             */

        //POBLACION BENEFICIADA DIRECTAMENTE
        $arr = $proyecto_vo->id_beneficiarios;

        foreach ($arr as $a){
            $sql = "INSERT INTO proyecto_beneficiario (ID_POBLA,ID_PROY,TIPO_REL) VALUES ($a,$id_proyecto,1)";
            //echo $sql;
            $this->conn->Execute($sql);
        }

        //POBLACION BENEFICIADA INDIRECTAMENTE
        $arr = $proyecto_vo->id_beneficiarios_indirectos;

        foreach ($arr as $a){
            $sql = "INSERT INTO proyecto_beneficiario (ID_POBLA,ID_PROY,TIPO_REL) VALUES ($a,$id_proyecto,2)";
            //echo $sql;
            $this->conn->Execute($sql);
        }

        //ORGANIZACIONES EJECUTORAS
        $arr = $proyecto_vo->id_orgs_e;

        foreach ($arr as $a){
            $sql = "INSERT INTO vinculorgpro (ID_TIPO_VINORGPRO,ID_ORG,ID_PROY,VALOR_APORTE) VALUES (1,".$a.",".$id_proyecto.",0)";
            //echo $sql;
            $this->conn->Execute($sql);
        }

        //ORGANIZACIONES DONANTES
        $arr = $proyecto_vo->id_orgs_d;
        foreach ($arr as $a){
            $sql = "INSERT INTO vinculorgpro (ID_TIPO_VINORGPRO,ID_ORG,ID_PROY,VALOR_APORTE) VALUES (2,".$a.",".$id_proyecto.",0)";
            //echo $sql;
            $this->conn->Execute($sql);
        }

        //ORGANIZACIONES SOCIOS
        $arr = $proyecto_vo->id_orgs_s;

        foreach ($arr as $a){
            $sql = "INSERT INTO vinculorgpro (ID_TIPO_VINORGPRO,ID_ORG,ID_PROY,VALOR_APORTE) VALUES (3,".$a.",".$id_proyecto.",0)";
            //echo $sql;
            $this->conn->Execute($sql);
        }

        //ORGANIZACIONES-OFICINA DESDE LA QUE SE CUBRE EL PROYECTO
        $arr = $proyecto_vo->id_orgs_cubre;

        foreach ($arr as $a){
            $sql = "INSERT INTO vinculorgpro (ID_TIPO_VINORGPRO,ID_ORG,ID_PROY,VALOR_APORTE) VALUES (5,".$a.",".$id_proyecto.",0)";
            //echo $sql;
            $this->conn->Execute($sql);
        }

        //ORGANIZACIONES TRABAJO COORDINADO
        $arr = $proyecto_vo->id_orgs_coor;
        foreach ($arr as $i=>$a){
            $valor_ap = $proyecto_vo->id_orgs_coor_valor_ap[$i];
            $sql = "INSERT INTO vinculorgpro (ID_TIPO_VINORGPRO,ID_ORG,ID_PROY,VALOR_APORTE) VALUES (4,".$a.",".$id_proyecto.",$valor_ap)";
            //echo $sql;
            $this->conn->Execute($sql);
        }
    }

    /**
     * Inserta las tablas de union de cobertura de la Proyecto en la B.D.
     * @access public
     * @param object $proyecto_vo VO de Proyecto que se va a insertar
     * @param int $id_proyecto ID de la Proyecto que se acaba de insertar
     */		
    function InsertarTablasUnionCobertura($proyecto_vo,$id_proyecto){

        //DEPTOS
        $arr = $proyecto_vo->id_deptos;
        foreach($arr as $a){
            $sql = "INSERT INTO depto_proy (ID_DEPTO,ID_PROY) VALUES ('$a',".$id_proyecto.")";
            $this->conn->Execute($sql);
            //echo $sql;
        }

        //MUNICPIOS
        $arr = $proyecto_vo->id_muns;
        foreach($arr as $a){
            $sql = "INSERT INTO mun_proy (ID_MUN,ID_PROY) VALUES ('$a',".$id_proyecto.")";
            $this->conn->Execute($sql);
            //echo $sql;
        }

            /*
            //REGIONES
            $arr = $proyecto_vo->id_regiones;
            $num_arr = count($arr);


            //CONSULTA LOS MUNICIPIOS DONDE TIENE COBERTURA
            $sql = "SELECT ID_MUN FROM mun_proy WHERE ID_PROY = ".$id_proyecto;
            $rs = $this->conn->OpenRecordset($sql);
            $id_muns_cob = Array();
            while ($row_rs = $this->conn->FetchRow($rs)){
            array_push($id_muns_cob,$row_rs[0]);
            }

            for($m=0;$m<$num_arr;$m++){
            $sql = "INSERT INTO reg_proy (ID_REG,ID_PROY) VALUES (".$arr[$m].",".$id_proyecto.")";
            $this->conn->Execute($sql);

            //CONSULTA LOS MUNICIPIOS DE LA REGION
            $sql = "SELECT ID_MUN FROM mun_reg WHERE ID_REG = ".$arr[$m];
            $rs = $this->conn->OpenRecordset($sql);
            while ($row_rs = $this->conn->FetchRow($rs)){
            if (!in_array($row_rs[0],$id_muns_cob)){
            $sql_i = "INSERT INTO mun_proy (ID_MUN,ID_PROY,COBERTURA) VALUES ('".$row_rs[0]."',".$id_proyecto.",0)";
            $this->conn->Execute($sql_i);
            //echo $sql_i;
            }
            }
            }

            //POBLADOS
            $arr = $proyecto_vo->id_poblados;
            $num_arr = count($arr);

            for($m=0;$m<$num_arr;$m++){
            $sql = "INSERT INTO pob_proy (ID_POB,ID_PROY) VALUES ('".$arr[$m]."',".$id_proyecto.")";
            $this->conn->Execute($sql);
            //echo $sql;
            }

            //RESGUARDOS
            $arr = $proyecto_vo->id_resguardos;
            $num_arr = count($arr);

            for($m=0;$m<$num_arr;$m++){
            $sql = "INSERT INTO resg_proy (ID_RESGUADRO,ID_PROY) VALUES (".$arr[$m].",".$id_proyecto.")";
            $this->conn->Execute($sql);
            //echo $sql;
            }

            //PARQUES
            $arr = $proyecto_vo->id_parques;
            $num_arr = count($arr);

            for($m=0;$m<$num_arr;$m++){
            $sql = "INSERT INTO par_nat_proy (ID_PAR_NAT,ID_PROY) VALUES (".$arr[$m].",".$id_proyecto.")";
            $this->conn->Execute($sql);
            //echo $sql;
            }

            //DIV. AFRO
            $arr = $proyecto_vo->id_divisiones_afro;
            $num_arr = count($arr);

            for($m=0;$m<$num_arr;$m++){
            $sql = "INSERT INTO div_afro_proy (ID_DIV_AFRO,ID_PROY) VALUES (".$arr[$m].",".$id_proyecto.")";
            $this->conn->Execute($sql);
            //echo $sql;
            }
             */
    }

    /**
     * Actualiza un Proyecto en la B.D.
     * @access public
     * @param object $depto_vo VO de Proyecto que se va a actualizar
     */		
    function Actualizar($vo){

        $id = $vo->id_proy;

        $sql = "UPDATE $this->tabla SET 
            id_mon = $vo->id_mon, 
            id_estp = $vo->id_estp, 
            nom_proy = '$vo->nom_proy', 
            cod_proy = '$vo->cod_proy', 
            des_proy = '$vo->des_proy', 
            obj_proy = '$vo->obj_proy', 
            inicio_proy = '$vo->inicio_proy', 
            fin_proy = '$vo->fin_proy', 
            actua_proy = now(), 
               costo_proy = $vo->costo_proy, 
               duracion_proy = $vo->duracion_proy, 
               info_conf_proy = $vo->info_conf_proy, 
               staff_nal_proy = $vo->staff_nal_proy, 
               staff_intal_proy = $vo->staff_intal_proy, 
               cobertura_nal_proy = $vo->cobertura_nal_proy, 
               cant_benf_proy = '$vo->cant_benf_proy', 
               valor_aporte_donantes = '$vo->valor_aporte_donantes', 
               valor_aporte_socios = '$vo->valor_aporte_socios', 
               info_extra_donantes = '$vo->info_extra_donantes', 
               info_extra_socios = '$vo->info_extra_socios',
               joint_programme_proy = $vo->joint_programme_proy,
               mou_proy = $vo->mou_proy,
               acuerdo_coop_proy = $vo->acuerdo_coop_proy,
               interv_ind_proy = $vo->interv_ind_proy,
               otro_cual_benf_proy = '$vo->otro_cual_benf_proy' 

               WHERE $this->columna_id = $id";

        $this->conn->Execute($sql);

        $this->BorrarTablasUnion($id);
        $this->BorrarTablasUnionCobertura($id);

        $this->InsertarTablasUnion($vo,$id);
        $this->InsertarTablasUnionCobertura($vo,$id);

?>
        <script>
        alert("Proyecto actualizado con éxito");
        location.href = '<?=$this->url?>';
        </script>
<?
    }

    /**
     * Borra un Proyecto en la B.D.
     * @access public
     * @param int $id ID del Proyecto que se va a borrar de la B.D
     */	
    function Borrar($id){

        //BORRA TABLAS DE UNION
        $this->BorrarTablasUnion($id);
        $this->BorrarTablasUnionCobertura($id);

        //BORRA LA ORG.
        $sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
        $this->conn->Execute($sql);

?>
        <script>
        alert("Proyecto eliminado con éxito!");
        <? if ($_SESSION['sidih'] == 1)	echo "location.href = '$this->url';"; ?>
        </script>
<?
    }

    /**
     * Borra las tablas de union de un Proyecto en la B.D.
     * @access public
     * @param int $id ID del Proyecto que se va a borrar de la B.D
     */	
    function BorrarTablasUnion($id){

        //TEMAS
        $sql = "DELETE FROM proyecto_tema WHERE ".$this->columna_id." = ".$id;
        $this->conn->Execute($sql);
            /*
            //SECTOR
            $sql = "DELETE FROM sector_proy WHERE ".$this->columna_id." = ".$id;
            $this->conn->Execute($sql);

            //ENFOQUE
            $sql = "DELETE FROM enfoque_proy WHERE ".$this->columna_id." = ".$id;
            $this->conn->Execute($sql);

            //CONTACTOS
            $sql = "DELETE FROM proyecto_conta WHERE ".$this->columna_id." = ".$id;
            $this->conn->Execute($sql);
             */
        //POBLACION BENEFICIADA
        $sql = "DELETE FROM proyecto_beneficiario WHERE ".$this->columna_id." = ".$id;
        $this->conn->Execute($sql);

        //ORGANIZACIONES EJECUTORAS - DONANTES
        $sql = "DELETE FROM vinculorgpro WHERE ".$this->columna_id." = ".$id;
        $this->conn->Execute($sql);

    }

    /**
     * Borra las tablas de union de un Proyecto en la B.D.
     * @access public
     * @param int $id ID del Proyecto que se va a borrar de la B.D
     */	
    function BorrarTablasUnionCobertura($id){

        //DEPTOS
        $sql = "DELETE FROM depto_proy WHERE ".$this->columna_id." = ".$id;
        $this->conn->Execute($sql);

        //MUNICPIOS
        $sql = "DELETE FROM mun_proy WHERE ".$this->columna_id." = ".$id;
        $this->conn->Execute($sql);

            /*
            //BORRA LOS MUNICIPOS ASOCIADOS A LA REGION Y QUE NO SON DE COBERTURA INICIAL
            //CONSULTA LAS REGIONES DONDE TIENE COBERTURA
            $sql = "SELECT ID_REG FROM reg_proy WHERE ID_PROY = ".$id;
            $rs = $this->conn->OpenRecordset($sql);
            while ($row_rs = $this->conn->FetchRow($rs)){
            //CONSULTA LOS MUNICIPIOS DE LA REGION
            $sql_m = "SELECT ID_MUN FROM mun_reg WHERE ID_REG = ".$row_rs[0];
            $rs_m = $this->conn->OpenRecordset($sql_m);
            while ($row_rs_m = $this->conn->FetchRow($rs_m)){
            $sql_d = "DELETE FROM mun_proy WHERE ".$this->columna_id." = ".$id." AND ID_MUN = '".$row_rs_m[0]."' AND COBERTURA = 0";
            $this->conn->Execute($sql_d);
            //echo $sql_d;
            }
            }

            //REGIONES
            $sql = "DELETE FROM reg_proy WHERE ".$this->columna_id." = ".$id;
            $this->conn->Execute($sql);

            //POBLADOS
            $sql = "DELETE FROM pob_proy WHERE ".$this->columna_id." = ".$id;
            $this->conn->Execute($sql);
            //echo $sql;
            }
            if ($opcion == 4 || $opcion == 0){

            //RESGUARDOS
            $sql = "DELETE FROM resg_proy WHERE ".$this->columna_id." = ".$id;
            $this->conn->Execute($sql);

            //PARQUES
            $sql = "DELETE FROM par_nat_proy WHERE ".$this->columna_id." = ".$id;
            $this->conn->Execute($sql);

            //DIV. AFRO
            $sql = "DELETE FROM div_afro_proy WHERE ".$this->columna_id." = ".$id;
            $this->conn->Execute($sql);
            }

             */
    }

    /**
     * Lista los Proyectos en una Tabla
     * @access public
     */			
    function Reportar(){

        //INICIALIZACION DE VARIABLES
        $depto_dao = New DeptoDAO();
        $municipio_dao = New MunicipioDAO();
        $sector = New SectorDAO();
        $poblacion = New PoblacionDAO();
        $org = New OrganizacionDAO();
        $estado= New EstadoProyectoDAO();
        $tema= New TemaDAO();

        if (isset($_POST["donante"])){
            $donante = $_POST["donante"];
        }

        //SE CONSTRUYE EL SQL

        $condicion = "";
        $arreglos = "";
        //ESTADO
        if (isset($_POST["id_estado"])){
            $id_estado = $_POST['id_estado'];
            $id_s = implode(",",$id_estado);

            $condicion .= "ID_ESTP IN ('".$id_s."')";
        }

        //FECHA
        if (isset($_POST["f_ini"]) && $_POST["f_ini"] != ""){
            $f_ini = $_POST['f_ini'];
            $f_final = $_POST['f_fin'];

            if ($condicion == "")			$condicion = "INICIO_PROY >= '".$f_ini."' AND FIN_PROY <= '".$f_final."'";
            else							$condicion .= " AND INICIO_PROY >= '".$f_ini."' AND FIN_PROY <= '".$f_final."'";
        }


        if (isset($_POST["id_estado"])){

            $arr_id_est_fecha = Array();

            $sql = "SELECT ID_PROY FROM proyecto WHERE ".$condicion;
            $rs = $this->conn->OpenRecordset($sql);
            $i = 0;
            while ($row_rs = $this->conn->FetchRow($rs)){
                $arr_id_est_fecha[$i] = $row_rs[0];
                $i++;
            }

            $arreglos .= "\$arr_id_est_fecha";
        }

        if (isset($_POST["f_ini"]) && $_POST["f_ini"] != ""){

            $arr_id_est_fecha = Array();

            $sql = "SELECT ID_PROY FROM proyecto WHERE ".$condicion;
            $rs = $this->conn->OpenRecordset($sql);
            $i = 0;
            while ($row_rs = $this->conn->FetchRow($rs)){
                $arr_id_est_fecha[$i] = $row_rs[0];
                $i++;
            }

            $arreglos .= "\$arr_id_est_fecha";

        }

        //SECTOR
        if (isset($_POST["id_sector"])){

            $arr_id_sector = Array();

            $id_sector = $_POST['id_sector'];
            $id_s = implode(",",$id_sector);

            $sql = "SELECT ID_PROY FROM sector_proy WHERE ID_COMP IN (".$id_s.")";
            $rs = $this->conn->OpenRecordset($sql);
            $i = 0;
            while ($row_rs = $this->conn->FetchRow($rs)){
                $arr_id_sector[$i] = $row_rs[0];
                $i++;
            }

            if ($arreglos == "")	$arreglos = "\$arr_id_sector";
            else					$arreglos .= ",\$arr_id_sector";
        }

        //TEMA
        if (isset($_POST["id_tema"])){
            $arr_id_tema = Array();

            $id_tema = $_POST['id_tema'];
            $id_s = implode(",",$id_tema);

            $sql = "SELECT ID_PROY FROM tema_proyecto WHERE ID_TEMA IN (".$id_s.")";
            $rs = $this->conn->OpenRecordset($sql);
            $i = 0;
            while ($row_rs = $this->conn->FetchRow($rs)){
                $arr_id_tema[$i] = $row_rs[0];
                $i++;
            }

            if ($arreglos == "")	$arreglos = "\$arr_id_tema";
            else					$arreglos .= ",\$arr_id_tema";

        }

        //POBLACION
        if (isset($_POST["id_poblacion"])){

            $arr_id_poblacion = Array();

            $id_poblacion = $_POST['id_poblacion'];
            $id_s = implode(",",$id_poblacion);

            $sql = "SELECT ID_PROY FROM beneficiario WHERE ID_POBLA IN (".$id_s.")";
            $rs = $this->conn->OpenRecordset($sql);
            $i = 0;
            while ($row_rs = $this->conn->FetchRow($rs)){
                $arr_id_poblacion[$i] = $row_rs[0];
                $i++;
            }

            if ($arreglos == "")	$arreglos = "\$arr_id_poblacion";
            else					$arreglos .= ",\$arr_id_poblacion";

        }

        //ORG. DONANTE - EJECUTORA
        if (isset($_POST["id_orgs"]) && $_POST["id_orgs"] != ""){

            $arr_id_orgs = Array();

            $id_orgs = $_POST['id_orgs'];
            $donante = $_POST['donante'];

            $sql = "SELECT ID_PROY FROM vinculorgpro WHERE ID_ORG IN (".$id_orgs.") AND ID_TIPO_VINORGPRO = ".$donante;
            $rs = $this->conn->OpenRecordset($sql);
            $i = 0;
            while ($row_rs = $this->conn->FetchRow($rs)){
                $arr_id_orgs[$i] = $row_rs[0];
                $i++;
            }

            if ($arreglos == "")	$arreglos = "\$arr_id_orgs";
            else					$arreglos .= ",\$arr_id_orgs";

        }

        //UBIACION GEOGRAFICA
        if (isset($_POST["id_depto"]) && !isset($_POST["id_muns"])){

            $id_depto = $_POST['id_depto'];

            $m = 0;
            foreach ($id_depto as $id){
                $id_depto_s[$m] = "'".$id."'";
                $m++;
            }
            $id_depto_s = implode(",",$id_depto_s);

            $sql = "SELECT proyecto.ID_PROY FROM depto_proy INNER JOIN proyecto ON depto_proy.ID_PROY = proyecto.ID_PROY WHERE ID_DEPTO IN (".$id_depto_s.")";

            $sql .= " ORDER BY proyecto.ID_PROY ASC";

            $arr_id_u_g = Array();
            $i = 0;
            $rs = $this->conn->OpenRecordset($sql);
            while ($row_rs = $this->conn->FetchRow($rs)){
                $arr_id_u_g[$i] = $row_rs[0];
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

            $sql = "SELECT proyecto.ID_PROY FROM mun_proy INNER JOIN proyecto ON mun_proy.ID_PROY = proyecto.ID_PROY WHERE ID_MUN IN (".$id_mun_s.")";

            $sql .= " ORDER BY proyecto.ID_PROY ASC";

            $arr_id_u_g = Array();
            $i = 0;
            $rs = $this->conn->OpenRecordset($sql);
            while ($row_rs = $this->conn->FetchRow($rs)){
                $arr_id_u_g[$i] = $row_rs[0];
                $i++;
            }
        }

        if (isset($_POST["id_depto"])){

            if ($arreglos == "")	$arreglos = "\$arr_id_u_g";
            else					$arreglos .= ",\$arr_id_u_g";
        }

        //INTERSECCION DE LOS ARREGLOS PARA REALIZAR LA CONSULTA

        if (count(explode(",",$arreglos)) > 1 ){
            eval("\$arr_id = array_intersect($arreglos);");
        }
        else{
            eval("\$arr_id = $arreglos;");
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

        echo "<form action='index.php?m_e=proyecto&accion=consultar&class=ProyectoDAO' method='POST'>";
        echo "<table align='center' class='tabla_reportelist_outer'>";
        echo "<tr><td>&nbsp;</td></tr>";
        if ($num_arr > 0){
            echo "<tr><td colspan='7' align='right'>Exportar a: <input type='image' src='images/consulta/generar_pdf.gif' onclick=\"document.getElementById('pdf').value = 1;\">&nbsp;&nbsp;<input type='image' src='images/consulta/excel.gif' onclick=\"document.getElementById('pdf').value = 2;\"></td>";
        }
        echo "<tr><td align='center' class='titulo_lista' colspan=7>CONSULTA DE PROYECTOS</td></tr>";
        echo "<tr><td colspan=5>Consulta realizada aplicando los siguientes filtros:</td>";
        echo "<tr><td colspan=5>";
        //TITULO DE ESTADO
        if (isset($_POST["id_estado"])){
            echo "<img src='images/flecha.gif'> Estado del Proyecto: ";
            $t = 0;
            foreach($id_estado as $id_t){
                $vo  = $estado->Get($id_t);
                if ($t == 0)	echo "<b>".$vo->nombre."</b>";
                else			echo ", <b>".$vo->nombre."</b>";
                $t++;
            }
            echo "<br>";
        }
        //TITULO DE SECTOR
        if (isset($_POST["id_sector"])){
            echo "<img src='images/flecha.gif'> Sector: ";
            $t = 0;
            foreach($id_sector as $id_t){
                $vo  = $sector->Get($id_t);
                if ($t == 0)	echo "<b>".$vo->nombre_es."</b>";
                else			echo ", <b>".$vo->nombre_es."</b>";
                $t++;
            }
            echo "<br>";
        }
        //TITULO DE TEMA
        if (isset($_POST["id_tema"])){
            echo "<img src='images/flecha.gif'> Tema: ";
            $t = 0;
            foreach($id_tema as $id_t){
                $vo  = $tema_dao->Get($id_t);
                if ($t == 0)	echo "<b>".$vo->nombre."</b>";
                else			echo ", <b>".$vo->nombre."</b>";
                $t++;
            }
            echo "<br>";
        }
        //TITULO DE POBLACION
        if (isset($_POST["id_poblacion"])){
            echo "<img src='images/flecha.gif'> Población: ";
            $t = 0;
            foreach($id_poblacion as $id_t){
                $vo  = $poblacion->Get($id_t);
                if ($t == 0)	echo "<b>".$vo->nombre_es."</b>";
                else			echo ", <b>".$vo->nombre_es."</b>";
                $t++;
            }
            echo "<br>";
        }
        //TITULO DE ORG. DONANTE O EJECUTORA
        if (isset($_POST["id_orgs"]) && $_POST["id_orgs"] != ""){
            $donante = "Ejecutora";
            if ($_POST["donante"] == 2)	$donante = "Donante";
            echo "<img src='images/flecha.gif'> Organización ".$donante.": ";
            $t = 0;
            foreach(explode(",",$id_orgs) as $id_t){
                $vo  = $org->Get($id_t);
                if ($t == 0)	echo "<b>".$vo->nom."</b>";
                else			echo ", <b>".$vo->nom."</b>";
                $t++;
            }
            echo "<br>";
        }
        //FECHA
        if (isset($_POST["f_ini"]) && $_POST["f_ini"] != ""){
            echo "<img src='images/flecha.gif'> Fecha Inicio: <b>".$_POST["f_ini"]."</b> -- Fecha Finalización: <b>".$_POST["f_fin"]."</b>";
            echo "<br>";
        }
        //TITULO DE DEPTO
        if (isset($_POST["id_depto"])){
            echo "<img src='images/flecha.gif'> Departamento: ";
            $t = 0;
            foreach($_POST["id_depto"] as $id_t){
                $vo  = $depto_dao->Get($id_t);
                if ($t == 0)	echo "<b>".$vo->nombre."</b>";
                else			echo ", <b>".$vo->nombre."</b>";
                $t++;
            }
            echo "<br>";
        }
        //TITULO DE MPIO
        if (isset($_POST["id_muns"])){
            echo "<img src='images/flecha.gif'> Municipio: ";
            $t = 0;
            foreach($id_mun as $id_t){
                $vo  = $municipio_dao->Get($id_t);
                if ($t == 0)	echo "<b>".$vo->nombre."</b>";
                else			echo ", <b>".$vo->nombre."</b>";
                $t++;
            }
            echo "<br>";
        }
        echo "</td>";

        if ($num_arr > 0){
            echo "<tr><td colspan=3><table class='tabla_reportelist'>";
            echo"<tr class='titulo_lista'>
                <td width='150'>Nombre</td>
                <td width='60'>Estado</td>
                <td width='280'>Objetivo</td>
                <td width='60'>F. Inicio</td>
                <td width='100'>F. Finalización</td>";
            echo "<td align='center' width='80'>Registros: ".$num_arr."</td>
                </tr>";

            for($p=0;$p<$num_arr;$p++){
                $style = "";
                if (fmod($p+1,2) == 0)  $style = "fila_lista";

                //NOMBRE
                if ($arr[$p]->nombre != ""){

                    //NOMBRE DEL ESTADO
                    $est = $estado->Get($arr[$p]->id_estp);
                    $nom_estado = $est->nombre;

                    echo "<tr class='fila_lista'>";
                    echo "<td>".$arr[$p]->nombre."</td>";
                    echo "<td>".$nom_estado."</td>";
                    echo "<td><div align='justify'>".$arr[$p]->obj."</div></td>";
                    if ($arr[$p]->fecha_ini == '0000-00-00')	$arr[$p]->fecha_ini = "";
                    echo "<td><div align='justify'>".$arr[$p]->fecha_ini."</div></td>";
                    if ($arr[$p]->fecha_fin == '0000-00-00')	$arr[$p]->fecha_fin = "";
                    echo "<td><div align='justify'>".$arr[$p]->fecha_fin."</div></td>";
                    echo "<td align='center'><a href='index.php?accion=consultar&class=ProyectoDAO&method=Ver&param=".$arr[$p]->id."''>Detalles</a></td>";
                    echo "</tr>";
                }
            }

            echo "<tr><td>&nbsp;</td></tr>";
        }
        else{
            echo "<tr><td align='center'><br><b>NO SE ENCONTRARON PROYECTOS</b></td></tr>";
            echo "<tr><td align='center'><br><a href='javascript:history.back();'>Regresar</a></td></tr>";
die;
        }

        echo "<input type='hidden' name='id_proyectos' value='".implode(",",$arr_id)."'>";
        echo "<input type='hidden' id='pdf' name='pdf'>";
        echo "</table>";
        echo "</form>";
    }

    /******************************************************************************
     * Reporte PDF - EXCEL
     * @param Array $id_proyectos Id de los Proyectos a Reportar
     * @param Int $formato PDF o Excel
     * @param Int $basico 1 = Básico - 2 = Detallado
     * @access public
     *******************************************************************************/
    function ReporteProyecto($id_proyectos,$formato,$basico){

        //INICIALIZACION DE VARIABLES
        $depto_dao = New DeptoDAO();
        $mun_dao = New MunicipioDAO();
        $sector = New SectorDAO();
        $enfoque_dao = New EnfoqueDAO();
        $poblacion = New PoblacionDAO();
        $org = New OrganizacionDAO();
        $estado_dao = New EstadoProyectoDAO();
        $tema= New TemaDAO();
        $moneda_dao = New MonedaDAO();
        $contacto_dao = New ContactoDAO();
        $region_dao = New RegionDAO();
        $poblado_dao = New PobladoDAO();
        $resguardo_dao = New ResguardoDAO();
        $parque_nat_dao = New ParqueNatDAO();
        $div_afro_dao = New DivAfroDAO();
        $file = New Archivo();

        $arr_id = explode(",",$id_proyectos);


        if ($formato == 1){

            $pdf = new Cezpdf();
            $pdf->selectFont('admin/lib/common/PDFfonts/Helvetica.afm');

            if ($basico == 1){
                $pdf -> ezSetMargins(80,70,20,20);
            }
            else{
                $pdf -> ezSetMargins(100,70,50,50);
            }


            // Coloca el logo y el pie en todas las páginas
            $all = $pdf->openObject();
            $pdf->saveState();
            $img_att = getimagesize('images/logos/enc_reporte_semanal.jpg');
            $pdf->addPngFromFile('images/logos/enc_reporte_semanal.png',700,550,$img_att[0]/2,$img_att[1]/2);

            $pdf->addText(300,580,14,'<b>Sala de Situación Humanitaria</b>');

            if ($basico == 1){
                $pdf->addText(230,560,12,'Listado de Proyectos con Nombre,Estado,Objetivo,Fecha y Cobertura');
            }
            else{
                $pdf->addText(230,560,12,'Listado de Proyectos por Cobertura Geográfica y Tipo de Organización');

            }

            $fecha = getdate();
            $fecha_hoy = $fecha["mday"]."/".$fecha["mon"]."/".$fecha["year"];

            $pdf->addText(370,540,12,$fecha_hoy);

            if ($basico == 2){
                $pdf->setLineStyle(1);
                $pdf->line(50,535,740,535);
                $pdf->line(50,530,740,530);
            }

            $pdf->restoreState();
            $pdf->closeObject();
            $pdf->addObject($all,'all');

            $pdf->ezSetDy(-30);

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

            //FORMATO BASICO
            if ($basico == 1){

                $title = Array('nombre' => '<b>Nombre</b>',
                    'estado'   => '<b>Estado</b>',
                    'objetivo'   => '<b>Objetivo</b>',
                    'f_ini'   => '<b>F. Inicio</b>',
                    'f_fin'   => '<b>F. Finalización</b>',
                    'cobertura'   => '<b>Cobertura</b>');
                for($p=0;$p<$num_arr;$p++){
                    $data[$p]['nombre'] = $arr[$p]->nombre;
                    $data[$p]['objetivo'] = $arr[$p]->obj;
                    $data[$p]['f_ini'] = "";
                    if ($arr[$p]->fecha_ini != "0000-00-00")
                        $data[$p]['f_ini'] = $arr[$p]->fecha_ini;

                    $data[$p]['f_fin'] = "";
                    if ($arr[$p]->fecha_fin != "0000-00-00")
                        $data[$p]['f_fin'] = $arr[$p]->fecha_fin;


                    //NOMBRE DEL ESTADO
                    $est = $estado_dao->Get($arr[$p]->id_estp);
                    $nom_est = $est->nombre;
                    $data[$p]['estado'] = $nom_est;


                    //COBERTURA
                    $cob = "";
                    foreach($arr[$p]->id_deptos as $id){
                        $vo = $depto_dao->Get($id);
                        if ($cob == "")	$cob = $vo->nombre;
                        else				$cob .= ",".$vo->nombre;
                    }
                    $data[$p]['cobertura'] = $cob;
                }

                $options = Array('showLines' => 2, 'shaded' => 0, 'width' => 750, 'fontSize'=>8, 'cols'=>array('nombre'=>array('width'=>250),'estado'=>array('width'=>60),'objetivo'=>array('width'=>200)));
                $pdf->ezTable($data,$title,'',$options);
            }
            //FORMATO DETALLADO
            else if ($basico == 2){

                for($p=0;$p<$num_arr;$p++){

                    $proyecto = $arr[$p];

                    //ESTADO
                    if ($proyecto->id_estp != 0){
                        $vo = $estado_dao->Get($proyecto->id_estp);
                        $estado = $vo->nombre;
                    }

                    //F. INI
                    if ($proyecto->fecha_ini == "0000-00-00")	$proyecto->fecha_ini = "No especificada";

                    //F. FINAL
                    if ($proyecto->fecha_fin == "0000-00-00")	$proyecto->fecha_fin = "No especificada";

                    //COSTO
                    if ($proyecto->costo != "" && $proyecto->costo > 0){
                        $moneda = $moneda_dao->Get($proyecto->id_moneda);
                        $proyecto->costo = $moneda->nombre." ".$proyecto->costo;
                    }
                    else{
                        $proyecto->costo = "" ;
                    }

                    //NOMBRE
                    $pdf->setColor(0.9,0.9,0.9);
                    $pdf->filledRectangle($pdf->ez['leftMargin'],$pdf->y-$pdf->getFontHeight(10)+$pdf->getFontDecender(10),$pdf->ez['pageWidth']-$pdf->ez['leftMargin']-$pdf->ez['rightMargin'],$pdf->getFontHeight(10));
                    $pdf->setColor(0,0,0);

                    $pdf->ezText("<b>Proyecto</b>: ".$proyecto->nombre,10);
                    $pdf->ezSetDy(-5);

                    //CODIGO
                    if ($proyecto->codigo != ""){
                        $pdf->ezText("<b>Código</b>: ".$proyecto->codigo,10);
                        $pdf->ezSetDy(-5);
                    }

                    //ESTADO
                    $pdf->ezText("<b>Estado</b>: ".$estado,10);
                    $pdf->ezSetDy(-5);

                    //TEMAS
                    if (count($proyecto->id_temas) > 0){
                        $pdf->ezText("<b>Tema</b>:",10);
                        foreach($proyecto->id_temas as $id){
                            $vo = $tema_dao->Get($id);
                            $pdf->ezText($vo->nombre,10);
                        }
                        $pdf->ezSetDy(-5);
                    }

                    //DESC.
                    if ($proyecto->desc != ""){
                        $pdf->ezText("<b>Descripción</b>: ".$proyecto->desc,10);
                        $pdf->ezSetDy(-5);
                    }
                    //OBJ
                    if ($proyecto->obj != ""){
                        $pdf->ezText("<b>Objetivo</b>: ".$proyecto->obj,10);
                        $pdf->ezSetDy(-5);
                    }

                    //FECHA
                    $pdf->ezText("<b>Fecha de Inicio</b>: ".$proyecto->fecha_ini."  -  <b>Fecha de Finalización</b>: ".$proyecto->fecha_fin,10);
                    $pdf->ezSetDy(-5);

                    //COSTO
                    $pdf->ezText("<b>Costo</b>: ".$proyecto->costo,10);
                    $pdf->ezSetDy(-5);

                    //CONTACTOS
                    if (count($proyecto->id_contactos) > 0){
                        $pdf->ezText("<b>Contáctos</b>:",10);
                        foreach($proyecto->id_contactos as $id){
                            $vo = $contacto_dao->Get($id);
                            $li = "- ".$vo->nombre;
                            if ($vo->tel != ""){
                                $li .= "   <b>Tel: </b> ".$vo->tel;
                            }
                            if ($vo->email != ""){
                                $li .= "   <b>Email: </b> ".$vo->email;
                            }
                            $pdf->ezText($li,10);
                        }
                        $pdf->ezSetDy(-5);
                    }

                    //ORGS. DONANTES
                    if (count($proyecto->id_orgs_d) > 0){
                        $pdf->ezText("<b>Organizaciones Donantes</b>:",10);
                        $s = 0;
                        foreach($proyecto->id_orgs_d as $id){
                            $vo = $org->Get($id);
                            $li = "- ".$vo->nom;
                            if ($proyecto->id_orgs_d_valor_ap[$s] != ""){
                                $li .= "   <b>Valor Aporte: </b> ".$proyecto->id_orgs_d_valor_ap[$s];
                            }
                            $pdf->ezText($li,10);
                            $s++;
                        }
                        $pdf->ezSetDy(-5);
                    }

                    //ORGS. EJECUTORAS
                    if (count($proyecto->id_orgs_e) > 0){
                        $pdf->ezText("<b>Organizaciones Ejecutoras</b>:",10);
                        $s = 0;
                        foreach($proyecto->id_orgs_e as $id){
                            $vo = $org->Get($id);
                            $li = "- ".$vo->nom;
                            $pdf->ezText($li,10);
                            $s++;
                        }
                        $pdf->ezSetDy(-5);
                    }

                    //SECTOR
                    if (count($proyecto->id_sectores) > 0){
                        $pdf->ezText("<b>Sector</b>:",10);
                        $s = 0;
                        foreach($proyecto->id_sectores as $id){
                            $vo = $sector->Get($id);
                            $li = "- ".$vo->nombre_es;
                            $pdf->ezText($li,10);
                            $s++;
                        }
                        $pdf->ezSetDy(-5);
                    }

                    //ENFOQUE
                    if (count($proyecto->id_enfoques) > 0){
                        $pdf->ezText("<b>Enfoque</b>: ",10);
                        $s = 0;
                        foreach($proyecto->id_enfoques as $id){
                            $vo = $enfoque_dao->Get($id);
                            $li = "- ".$vo->nombre_es;
                            $pdf->ezText($li,10);
                            $s++;
                        }
                        $pdf->ezSetDy(-5);
                    }

                    //POBLACION BENEFICIADA
                    if (count($proyecto->id_beneficiarios) > 0){
                        $pdf->ezText("<b>Población Beneficiada</b>:",10);
                        $s = 0;
                        foreach($proyecto->id_beneficiarios as $id){
                            $vo = $poblacion->Get($id);
                            $li = "- ".$vo->nombre_es;
                            if ($proyecto->cant_per[$s] != "" && $proyecto->cant_per[$s] > 0){
                                $li .= "   <b>No. de personas : </b> ".$proyecto->cant_per[$s];
                            }
                            $pdf->ezText($li,10);
                            $s++;
                        }
                        $pdf->ezSetDy(-5);
                    }

                    //DEPTO
                    if (count($proyecto->id_deptos) > 0){
                        $pdf->ezText("<b>Cobertura Geográfica por Departamento</b>:",10);
                        $s = 0;
                        foreach($proyecto->id_deptos as $id){
                            $vo = $depto_dao->Get($id);
                            $li = "- ".$vo->nombre;
                            $pdf->ezText($li,10);
                            $s++;
                        }
                        $pdf->ezSetDy(-5);
                    }

                    //MUN
                    if (count($proyecto->id_muns) > 0){
                        $pdf->ezText("<b>Cobertura Geográfica por Municipio</b>:",10);
                        $s = 0;
                        $li = "";
                        foreach($proyecto->id_muns as $id){
                            $vo = $mun_dao->Get($id);
                            $li .= "- ".$vo->nombre."  ";
                            $s++;
                        }
                        $pdf->ezText($li,10);
                        $pdf->ezSetDy(-5);
                    }

                    //REGION
                    if (count($proyecto->id_regiones) > 0){
                        $pdf->ezText("<b>Cobertura Geográfica por Región</b>:",10);
                        $s = 0;
                        foreach($proyecto->id_regiones as $id){
                            $vo = $region_dao->Get($id);
                            $li = "- ".$vo->nombre;
                            $pdf->ezText($li,10);
                            $s++;
                        }
                        $pdf->ezSetDy(-5);
                    }

                    //POBLADO
                    if (count($proyecto->id_poblados) > 0){
                        $pdf->ezText("<b>Cobertura Geográfica por Poblado</b>:",10);
                        $s = 0;
                        foreach($proyecto->id_poblados as $id){
                            $vo = $poblado_dao->Get($id);
                            $li = "- ".$vo->nombre;
                            $pdf->ezText($li,10);
                            $s++;
                        }
                        $pdf->ezSetDy(-5);
                    }

                    //PARQUE NAT.
                    if (count($proyecto->id_parques) > 0){
                        $pdf->ezText("<b>Cobertura Geográfica por Parque Natural</b>:",10);
                        $s = 0;
                        foreach($proyecto->id_parques as $id){
                            $vo = $parque_nat_dao->Get($id);
                            $li = "- ".$vo->nombre;
                            $pdf->ezText($li,10);
                            $s++;
                        }
                        $pdf->ezSetDy(-5);
                    }

                    //RESGUARDO
                    if (count($proyecto->id_resguardos) > 0){
                        $pdf->ezText("<b>Cobertura Geográfica por Resguardo</b>:",10);
                        $s = 0;
                        foreach($proyecto->id_resguardos as $id){
                            $vo = $resguardo_dao->Get($id);
                            $li = "- ".$vo->nombre;
                            $pdf->ezText($li,10);
                            $s++;
                        }
                        $pdf->ezSetDy(-5);
                    }

                    //DIV. AFRO
                    if (count($proyecto->id_divisiones_afro) > 0){
                        $pdf->ezText("<b>Cobertura Geográfica por División Afrouardo</b>:",10);
                        $s = 0;
                        foreach($proyecto->id_divisiones_afro as $id){
                            $vo = $div_afro_dao->Get($id);
                            $li = "- ".$vo->nombre;
                            $pdf->ezText($li,10);
                            $s++;
                        }
                        $pdf->ezSetDy(-5);
                    }
                    $pdf->ezSetDy(-5);
                }
            }

            //MUESTRA EN EL NAVEGADOR EL PDF
            //$pdf->ezStream();

            //CREA UN ARCHIVO PDF PARA BAJAR
            $nom_archivo = 'consulta/csv/proyecto.pdf';
            $file = New Archivo();
            $fp = $file->Abrir($nom_archivo,'wb');
            $pdfcode = $pdf->ezOutput();
            $file->Escribir($fp,$pdfcode);
            $file->Cerrar($fp);

?>
                    <table align='center' cellspacing="1" cellpadding="3" border="0" width="750">
                    <tr><td>&nbsp;</td></tr>
                    <tr><td align='center' class='titulo_lista' colspan=2>REPORTAR PROYECTOS EN FORMATO PDF</td></tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr><td colspan=2>
                    Se ha generado correctamente el archivo PDF de Proyectos.<br><br>
                    Para salvarlo use el botón derecho del mouse y la opción Guardar destino como sobre el siguiente link: <a href='<?=$nom_archivo;?>'>Archivo PDF</a>
                    </td></tr>
                    </table>
<?

        }
        //EXCEL
        else if ($formato == 2){

            $file = New Archivo();

            $fp = $file->Abrir('consulta/csv/proyecto.csv','w');

            if ($basico == 1){
                $tit = "NOMBRE,ESTADO,OBJETIVO,FECHA INICIO,FECHA FINALIZACION,COBERTURA";
            }
            else{
                $tit = "NOMBRE,ESTADO,OBJETIVO,FECHA INICIO,FECHA FINALIZACION,DURACION EN MESES,COSTO,CODIGO,DESCRIPCION,TEMA,CONTACTOS(Nombre-Tel-Email),ORGANIZACION DONANTES(Organización-Valor aporte),ORGANIZACION EJECUTORAS,SECTOR,POBLACION BENEFICIADA(Población-No. Perosonas Beneficiadas),COBERTURA POR DEPARTAMENTO,COBERTURA POR MUNICIPIO,COBERTURA POR REGION,COBERTURA POR POBLADO,COBERTURA POR PARQUE NATURAL,COBERTURA POR RESGUARDO,COBERTURA POR DIVISION AFRO";
            }

            //ENCABEZADO
            $file->Escribir($fp,$tit."\n");

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

            $p = 0;
            $que = Array(",","\r\n");
            $con = Array(" "," ");
            for($p=0;$p<$num_arr;$p++){

                $proyecto = $arr[$p];

                $linea = str_replace($que,$con,$arr[$p]->nombre);

                //NOMBRE DEL ESTADO
                $est = $estado_dao->Get($arr[$p]->id_estp);
                $nom_est = $est->nombre;
                $linea .= ",".$nom_est;

                $linea .= ",".str_replace($que,$con,$arr[$p]->obj);

                if ($arr[$p]->fecha_ini != "0000-00-00")
                    $linea .= ",".$arr[$p]->fecha_ini;
                else
                    $linea .= ",";

                if ($arr[$p]->fecha_fin != "0000-00-00")
                    $linea .= ",".$arr[$p]->fecha_fin;
                else
                    $linea .= ",";

                if ($basico == 1){
                    //COBERTURA POR DEPARTAMENTO
                    $s = 0;
                    foreach($proyecto->id_deptos as $id){
                        $vo = $depto_dao->Get($id);
                        if ($s == 0)	$linea .= ",".$vo->nombre;
                        else			$linea .= "-".$vo->nombre;
                        $s++;
                    }
                    if ($s == 0)	$linea .= ",";
                }
                else{
                    $linea .= ",".$proyecto->duracion;

                    //COSTO
                    if ($proyecto->costo != "" && $proyecto->costo > 0){
                        $moneda = $moneda_dao->Get($proyecto->id_moneda);
                        $linea .= ",".$moneda->nombre." ".$proyecto->costo;
                    }
                    else{
                        $linea .= ",,";
                    }

                    $linea .= ",".$proyecto->codigo;
                    $linea .= ",".$proyecto->desc;


                    //TEMAS
                    $s = 0;
                    foreach($proyecto->id_temas as $id){
                        $vo = $tema_dao->Get($id);
                        if ($s == 0)	$linea .= ",".$vo->nombre_es;
                        else			$linea .= "-".$vo->nombre_es;
                        $s++;
                    }
                    if ($s == 0)	$linea .= ",";

                    //CONTACTOS
                    $s = 0;
                    foreach($proyecto->id_contactos as $id){
                        $vo = $contacto_dao->Get($id);
                        if ($s == 0)	$linea .= ",".$vo->nombre."-".$vo->tel."-".$vo->email;
                        else			$linea .= ";".$vo->nombre."-".$vo->tel."-".$vo->email;
                        $s++;
                    }
                    if ($s == 0)	$linea .= ",";

                    //ORGS. DONANTES
                    $s = 0;
                    foreach($proyecto->id_orgs_d as $id){
                        $vo = $org->Get($id);
                        if ($s == 0)	$linea .= ",".$vo->nom."-".$proyecto->id_orgs_d_valor_ap[$s];
                        else			$linea .= ";".$vo->nom."-".$proyecto->id_orgs_d_valor_ap[$s];
                        $s++;
                    }
                    if ($s == 0)	$linea .= ",";

                    //ORGS. EJECUTORAS
                    $s = 0;
                    foreach($proyecto->id_orgs_e as $id){
                        $vo = $org->Get($id);
                        if ($s == 0)	$linea .= ",".$vo->nom;
                        else			$linea .= "-".$vo->nom;

                        $s++;
                    }
                    if ($s == 0)	$linea .= ",";

                    //SECTOR
                    $s = 0;
                    foreach($proyecto->id_sectores as $id){
                        $vo = $sector->Get($id);
                        if ($s == 0)	$linea .= ",".$vo->nombre_es;
                        else			$linea .= "-".$vo->nombre_es;

                        $s++;
                    }
                    if ($s == 0)	$linea .= ",";

                    //ENFOQUE
                    $s = 0;
                    foreach($proyecto->id_enfoques as $id){
                        $vo = $enfoque_dao->Get($id);
                        if ($s == 0)	$linea .= ",".$vo->nombre_es;
                        else			$linea .= "-".$vo->nombre_es;
                        $s++;
                    }
                    if ($s == 0)	$linea .= ",";

                    //POBLACION BENEFICIADAS
                    $s = 0;
                    foreach($proyecto->id_beneficiarios as $id){
                        $vo = $poblacion->Get($id);
                        if ($s == 0)	$linea .= ",".$vo->nombre_es."-".$proyecto->cant_per[$s];
                        else			$linea .= ";".$vo->nombre_es."-".$proyecto->cant_per[$s];

                        $s++;
                    }
                    if ($s == 0)	$linea .= ",";

                    //COBERTURA POR DEPARTAMENTO
                    $s = 0;
                    foreach($proyecto->id_deptos as $id){
                        $vo = $depto_dao->Get($id);
                        if ($s == 0)	$linea .= ",".$vo->nombre;
                        else			$linea .= "-".$vo->nombre;
                        $s++;
                    }
                    if ($s == 0)	$linea .= ",";
                    //COBERTURA POR MUNICIPIO
                    $s = 0;
                    foreach($proyecto->id_muns as $id){
                        $vo = $mun_dao->Get($id);
                        if ($s == 0)	$linea .= ",".$vo->nombre;
                        else			$linea .= "-".$vo->nombre;
                        $s++;
                    }
                    if ($s == 0)	$linea .= ",";
                    //COBERTURA POR REGION
                    $s = 0;
                    foreach($proyecto->id_regiones as $id){
                        $vo = $region_dao->Get($id);
                        if ($s == 0)	$linea .= ",".$vo->nombre;
                        else			$linea .= "-".$vo->nombre;
                        $s++;
                    }
                    if ($s == 0)	$linea .= ",";
                    //COBERTURA POR POBLADO
                    if (count($proyecto->id_poblados) > 0){
                        $s = 0;
                        foreach($proyecto->id_poblados as $id){
                            $vo = $poblado_dao->Get($id);
                            if ($s == 0)	$linea .= ",".$vo->nombre;
                            else			$linea .= "-".$vo->nombre;
                            $s++;
                        }
                    }
                    if ($s == 0)	$linea .= ",";
                    //COBERTURA POR PARQUE NAT.
                    if (count($proyecto->id_parques) > 0){
                        $s = 0;
                        foreach($proyecto->id_poblados as $id){
                            $vo = $parque_nat_dao->Get($id);
                            if ($s == 0)	$linea .= ",".$vo->nombre;
                            else			$linea .= "-".$vo->nombre;
                            $s++;
                        }
                    }
                    if ($s == 0)	$linea .= ",";
                    //COBERTURA POR RESGUARDO
                    if (count($proyecto->id_resguardos) > 0){
                        $s = 0;
                        foreach($proyecto->id_resguardos as $id){
                            $vo = $resguardo_dao->Get($id);
                            if ($s == 0)	$linea .= ",".$vo->nombre;
                            else			$linea .= "-".$vo->nombre;
                            $s++;
                        }
                    }
                    if ($s == 0)	$linea .= ",";
                    //COBERTURA POR DIV. AFRO
                    if (count($proyecto->id_divisiones_afro) > 0){
                        $s = 0;
                        foreach($proyecto->id_divisiones_afro as $id){
                            $vo = $div_afro_dao->Get($id);
                            if ($s == 0)	$linea .= ",".$vo->nombre;
                            else			$linea .= "-".$vo->nombre;
                            $s++;
                        }
                    }
                    if ($s == 0)	$linea .= ",";
                }
                $file->Escribir($fp,$linea."\n");
            }
            $file->Cerrar($fp);

?>
                    <table align='center' cellspacing="1" cellpadding="3" border="0" width="750">
                    <tr><td>&nbsp;</td></tr>
                    <tr><td align='center' class='titulo_lista' colspan=2>REPORTAR PROYECTOS EN FORMATO CSV (Excel)</td></tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr><td colspan=2>
                    Se ha generado correctamente el archivo CSV de Proyectos.<br><br>
                    Para salvarlo use el botón derecho del mouse y la opción Guardar destino como sobre el siguiente link: <a href='consulta/csv/proyecto.csv'>Archivo CSV</a>
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

        //INICIALIZACION DE VARIABLES
        $depto_dao = New DeptoDAO();
        $mun_dao = New MunicipioDAO();
        $sector = New SectorDAO();
        $poblacion = New PoblacionDAO();
        $org = New OrganizacionDAO();
        $estado= New EstadoProyectoDAO();
        $tema= New TemaDAO();
        $arr_id = Array();

        //SE CONSTRUYE EL SQL

        //UBIACION GEOGRAFICA
        if (isset($_POST["id_depto"]) && !isset($_POST["id_muns"])){

            $id_depto = $_POST['id_depto'];

            $m = 0;
            foreach ($id_depto as $id){
                $id_depto_s[$m] = "'".$id."'";
                $m++;
            }
            $id_depto_s = implode(",",$id_depto_s);

            $sql = "SELECT proyecto.ID_PROY FROM depto_proy INNER JOIN proyecto ON depto_proy.ID_PROY = proyecto.ID_PROY WHERE ID_DEPTO IN (".$id_depto_s.")";

            $sql .= " ORDER BY proyecto.ID_PROY ASC";

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

            $sql = "SELECT proyecto.ID_PROY FROM mun_proy INNER JOIN proyecto ON mun_proy.ID_PROY = proyecto.ID_PROY WHERE ID_MUN IN (".$id_mun_s.")";

            $sql .= " ORDER BY proyecto.ID_PROY ASC";

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
        if ($num_arr > 0 && !isset($_POST["que_org"])){
            echo "<tr><td colspan='5' align='right'>Exportar a: <input type='image' src='images/consulta/generar_pdf.gif' onclick=\"document.getElementById('pdf_proy').value = 1;\">&nbsp;&nbsp;<input type='image' src='images/consulta/excel.gif' onclick=\"document.getElementById('pdf_proy').value = 2;\"></td>";
        }
        echo "<tr><td align='center' class='titulo_lista' colspan=7>PROYECTOS DESARROLLADOS EN : ";
        //TITULO DE DEPTO
        if (isset($_POST["id_depto"]) && !isset($_POST["id_muns"])){
            $t = 0;
            foreach($_POST["id_depto"] as $id_t){
                $vo  = $depto_dao->Get($id_t);
                if ($t == 0)	echo "<b>".$vo->nombre."</b>";
                else			echo ", <b>".$vo->nombre."</b>";
                $t++;
            }
            echo "<br>";
        }
        //TITULO DE MPIO
        if (isset($_POST["id_muns"])){
            $t = 0;
            foreach($id_mun as $id_t){
                $vo  = $mun_dao->Get($id_t);
                if ($t == 0)	echo "<b>".$vo->nombre."</b>";
                else			echo ", <b>".$vo->nombre."</b>";
                $t++;
            }
            echo "<br>";
        }
        echo "</td>";
        echo "</td></tr>";

        if ($num_arr > 0){
            echo"<tr class='titulo_lista'>
                <td width='150'>Nombre</td>
                <td>Estado</td>
                <td width='280'>Objetivo</td>
                <td width='60'>F. Inicio</td>
                <td width='100'>F. Finalización</td>";
            echo "<td align='center' width='80'>Registros: ".$num_arr."</td>
                </tr>";

            for($p=0;$p<$num_arr;$p++){
                $style = "";
                if (fmod($p+1,2) == 0)  $style = "fila_lista";

                //NOMBRE
                if ($arr[$p]->nombre != ""){

                    //NOMBRE DEL ESTADO
                    $est = $estado->Get($arr[$p]->id_estp);
                    $nom_estado = $est->nombre;

                    echo "<tr class='fila_lista'>";
                    echo "<td>".$arr[$p]->nombre."</td>";
                    echo "<td>".$nom_estado."</td>";
                    echo "<td><div align='justify'>".$arr[$p]->obj."</div></td>";
                    if ($arr[$p]->fecha_ini == '0000-00-00')	$arr[$p]->fecha_ini = "";
                    echo "<td><div align='justify'>".$arr[$p]->fecha_ini."</div></td>";
                    if ($arr[$p]->fecha_fin == '0000-00-00')	$arr[$p]->fecha_fin = "";
                    echo "<td><div align='justify'>".$arr[$p]->fecha_fin."</div></td>";
                    echo "<td><a href='index.php?accion=consultar&class=ProyectoDAO&method=Ver&param=".$arr[$p]->id."''>Detalles</a></td>";
                    echo "</tr>";
                }
            }

            echo "<tr><td>&nbsp;</td></tr>";
        }
        else{
            echo "<tr><td align='center'><br><b>NO SE ENCONTRARON PROYECTOS</b></td></tr>";
        }

        echo "<input type='hidden' name='id_proyectos' value='".implode(",",$arr_id)."'>";
        echo "<input type='hidden' id='que_proy' name='que_proy' value='1'>";
        echo "</table>";
    }

    /**
     * Importa un archivo plano de proyectos
     * @param $userfile, archivo upload
     * @param int $import 1= importar y preview, 0=preview
     * @access public
     */			
    function ImportarCSV($userfile,$import){

        //Inicializacion de variables	
        $archivo = New Archivo();
        $proy_dao = New ProyectoDAO();
        $org_dao = New OrganizacionDAO();
        $estado_dao = New EstadoProyectoDAO();
        $depto_dao = New DeptoDAO();
        $mun_dao = New MunicipioDAO();

        $glue_col = "¬";

        $file_tmp = $userfile['tmp_name'];
        $file_nombre = $userfile['name'];

        $path = "proyecto/csv/".$file_nombre;

        $archivo->SetPath($path);
        $archivo->Guardar($file_tmp);

        $fp = $archivo->Abrir($path,'r');
        $cont_archivo = $archivo->LeerEnArreglo($fp);
        $archivo->Cerrar($fp);
        $num_rep = count($cont_archivo);

        if ($num_rep > 0){
            echo "<table class='tabla_consulta' cellspacing=1 cellpadding=5 border=1>";
            echo "<tr><td class='titulo_lista' align='center' colspan='40'>RESUMEN DE IMPORTACION</td></tr>";
            echo "<tr><td>&nbsp;</td></tr>";

            echo "<tr><td></td>
                <td>Nombre o Descripci&oacute;n</td>
                <td>Codigo</td>
                <td>Estado</td>
                <td>Agencia</td>
                <td>Fecha de inicio</td>
                <td>Duración</td>
                <td>Recursos</td>
                <td>Donantes</td>
                <td>Socios</td>
                <td>Aportes Agencias</td>
                <td>Aportes socios</td>
                <td>Pobreza, equidad y Desarrollo</td>
                <td>1.1</td>
                <td>1.2</td>
                <td>1.3</td>
                <td>1.4</td>
                <td>1.5</td>
                <td>1.6</td>
                <td>1.7</td>
                <td>1.8</td>
                <td>Desarrollo Sostenible</td>
                <td>2.1</td>
                <td>2.2</td>
                <td>2.3</td>
                <td>Estado social de Derecho y Gobernabilidad</td>
                <td>3.1</td>
                <td>3.2</td>
                <td>3.3</td>
                <td>3.4</td>
                <td>3.5</td>
                <td>Paz, seguridad y reconciliación</td>
                <td>4.1</td>	
                <td>4.2</td>	
                <td>4.3</td>	
                <td>4.4</td>	
                <td>4.5</td>	
                <td>4.6</td>	
                <td>Afro</td>
                <td>Jóvenes</td>
                <td>Indígenas</td>
                <td>Adultos mayores</td>
                <td>Mestizos</td>
                <td>Desplazados</td>	
                <td>Niños</td>
                <td>Campesinos</td>
                <td>Pob. Receptora</td>
                <td>Sindical</td>
                <td>Pob. Reubicada</td>
                <td>Mujeres</td>
                <td>Pob. Confinada</td>
                <td>No. Beneficiarios</td>
                <td>Cob Nacional</td>
                <td>Departamentos</td>
                <td>Municipios</td>
                <td>Oficina que cubre el proyecto</td>
                <td>Staff Nacional</td>
                <td>Staff Internacional</td>
                <td>Trabajo con agencias del SNU</td></tr>";

            $cod_ant = '';
            $num_proys = 0;
            for($r=3;$r<$num_rep;$r++){
                echo "<tr>";

                $linea = $cont_archivo[$r];

                $linea = explode("$glue_col",$linea);

                if (count($linea > 0) && $linea[0] != ""){

                    echo "<td>".($r)."</td>";

                    $c = 0;
                    $cod_proy = $linea[$c];

                    //INSERTA ********************
                    if ($cod_proy != $cod_ant && $r > 3){
                        $this->Insertar($proy);
                        $num_proys++;
                    }

                    if ($cod_proy != $cod_ant){

                        $proy = New Proyecto();

                        //CODIGO
                        $proy->cod_proy = $cod_proy;
                        echo "<td>$proy->cod_proy</td>";

                        //NOMBRE
                        $c += 1;
                        $proy->nom_proy =  trim(str_replace('"',"",$linea[$c]));
                        echo "<td>$proy->nom_proy</td>";

                        //Check si existe el proyecto
                        if($proy_dao->checkExiste($proy->nom_proy) > 0) $class = "fila_roja";

                        //ESTADO
                        $c += 1;
                        $est = $linea[$c];
                        $tmp = $estado_dao->GetAllArray("nom_estp LIKE '%$est%'");
                        if (count($tmp) > 0){
                            $est_vo = $tmp[0];

                            $proy->id_estp =  $est_vo->id;
                            echo "<td>$est_vo->nombre</td>";
                        }
                        else{
                            echo "<td class='error_import'>No existe el estado en el sistema</td>";
                        }

                        //EJECUTORAS
                        $c += 1;
                        $d = trim($linea[$c]);
                        $ids = $org_dao->GetAllArrayID("nom_org = '$d' OR sig_org = '$d'",'','');
                        $proy->id_orgs_e[] = $ids[0];
                        //$proy->id_orgs_e[] = $_SESSION["id_org"];
                        echo "<td>".$proy->id_orgs_e[0]."</td>";

                        //INICIO
                        $c += 1;
                        $proy->inicio_proy =  str_replace("/","-",$linea[$c]);
                        echo "<td>$proy->inicio_proy</td>";
                        $aaaa = explode("-",$proy->inicio_proy);
                        $aaaa = $aaaa[0];
                        if ($aaaa < 1995 && count(explode("-",$proy->inicio_proy)) == 3)	die;
                        //if (count(explode("-",$proy->inicio_proy)) < 3)	die;

                        //DURACION
                        $c += 1;
                        $duracion = $linea[$c];
                        $proy->duracion_proy = (strlen($duracion) > 0 && ereg("([0-9])",$duracion)) ? str_replace(",",".",$duracion) : 0;
                        echo "<td>$proy->duracion_proy</td>";

                        if ($proy->duracion_proy > 0 && count(explode("-",$proy->inicio_proy)) == 3){

                            //Calcula la fecha fin a partir de f_ini + meses duracion
                            $date = new Date();
                            $proy->fin_proy = $date->sumValorFecha($proy->inicio_proy,$proy->duracion_proy,'mes');
                        }


                        //INFO CONFIRMADA
                        $proy->info_conf_proy = 1;

                        //COSTO
                        $c += 1;
                        $proy->id_mon = 1; //U$
                        //$proy->costo_proy =  ceil(str_replace(array(",","."),"",$linea[$c]));
                        $proy->costo_proy =  str_replace(array(",","."),"",$linea[$c])*1;

                        echo "<td>$proy->costo_proy</td>";

                        echo "<td>";

                        //DONANTES
                        $c += 1;
                        $donantes = $linea[$c];
                        if (strlen($donantes) > 0 && !in_array(strtolower(trim($donantes)),array("ninguno","n.d"))){
                            $tmp = split("[,-/]",$donantes);
                            foreach ($tmp as $d){
                                $d = trim($d);
                                $ids = $org_dao->GetAllArrayID("nom_org LIKE '%$d%' OR sig_org LIKE '%$d%'",'','');

                                //Si existe solo una ocurrencia del nombre de la org, la asocia
                                if (count($ids) == 1){
                                    $id_o = $ids[0];
                                    $proy->id_orgs_d[] = $id_o;
                                    $nom = $org_dao->GetName($id_o);
                                    echo "- ".$nom."<br>";
                                }
                                else{
                                    echo "- No se encontró la org donante ($d)<br>";

                                }

                            }
                        }
                        echo "</td>";

                        echo "<td>";

                        //SOCIOS
                        $c += 1;
                        $socios = $linea[$c];
                        if (strlen($socios) > 0 && !in_array(strtolower(trim($socios)),array("ninguno","n.d"))){
                            $tmp = split("[,-/]",$socios);
                            foreach ($tmp as $d){
                                $d = trim($d);
                                $ids = $org_dao->GetAllArrayID("nom_org LIKE '%$d%' OR sig_org LIKE '%$d%'",'','');

                                //Si existe solo una ocurrencia del nombre de la org, la asocia
                                if (count($ids) == 1){
                                    $id_o = $ids[0];
                                    $proy->id_orgs_s[] = $id_o;
                                    $nom = $org_dao->GetName($id_o);
                                    echo "- ".$nom."<br>";
                                }
                                else{
                                    echo "- No se encontró la org donante ($d)<br>";

                                }

                            }
                        }
                        echo "</td>";

                        //APORTE AGENCIAS
                        $c += 1;
                        $proy->valor_aporte_donantes = str_replace(array("'","."),array("",""),$linea[$c]);
                        echo "<td>$proy->valor_aporte_donantes</td>";

                        //APORTE SOCIOS
                        $c += 1;
                        $proy->valor_aporte_socios = str_replace("'",".",$linea[$c]);
                        echo "<td>$proy->valor_aporte_socios</td>";

                        //TEMA - POBREZA
                        $id_tema = 1;
                        $c += 1;
                        $t = trim($linea[$c]);
                        $txt = '';

                        echo "<td>$t</td>";

                        if($t == 1){
                            $proy->id_temas[$id_tema] = array();
                        }

                        //Hijos
                        $id_hijos = array(0,5,6,7,8,9,10,11,12);
                        for($i=1;$i<count($id_hijos);$i++){
                            $c += 1;
                            $t_h = trim($linea[$c]);

                            echo "<td>$t_h</td>";

                            if ($t_h == 1) $proy->id_temas[$id_tema]['hijos'][] = $id_hijos[$i];

                        }

                        //Texto Extra
                            /*
                               $txt = $linea[$c-1];
                               if (strlen(trim($txt)) > 0){
                               $proy->texto_extra_tema[$id_tema] = $txt;
                               }
                             */

                        //echo "<td>$txt</td>";

                        //TEMA - DESARROLLO
                        $id_tema = 2;
                        $c += 1;
                        $t = trim($linea[$c]);
                        $txt = '';

                        echo "<td>$t</td>";

                        if($t == 1){
                            $proy->id_temas[$id_tema] = array();
                        }

                        //Hijos
                        $id_hijos = array(0,63,64,65);
                        for($i=1;$i<count($id_hijos);$i++){
                            $c += 1;
                            $t_h = trim($linea[$c]);

                            echo "<td>$t_h</td>";

                            if ($t_h == 1) $proy->id_temas[$id_tema]['hijos'][] = $id_hijos[$i];

                        }

                        //TEMA - ESTADO
                        $id_tema = 3;
                        $c += 1;
                        $t = trim($linea[$c]);
                        $txt = '';

                        echo "<td>$t</td>";

                        if($t == 1){
                            $proy->id_temas[$id_tema] = array();
                        }

                        //Hijos
                        $id_hijos = array(0,78,79,80,81);
                        for($i=1;$i<count($id_hijos);$i++){
                            $c += 1;
                            $t_h = trim($linea[$c]);

                            echo "<td>$t_h</td>";

                            if ($t_h == 1) $proy->id_temas[$id_tema]['hijos'][] = $id_hijos[$i];

                        }

                        // 3,5 no exite entonces se suma 1
                        $c += 1;

                        //TEMA - PAZ
                        $id_tema = 4;
                        $c += 1;
                        $t = trim($linea[$c]);
                        $txt = '';

                        echo "<td>$t</td>";

                        if($t == 1){
                            $proy->id_temas[$id_tema] = array();
                        }

                        //Hijos
                        $id_hijos = array(0,97,98,99,100,101,102);
                        for($i=1;$i<count($id_hijos);$i++){
                            $c += 1;
                            $t_h = trim($linea[$c]);

                            echo "<td>$t_h</td>";

                            if ($t_h == 1) $proy->id_temas[$id_tema]['hijos'][] = $id_hijos[$i];

                        }

                        //POBLACION
                        $id_b = array(34,37,33,39,35,43,36,42,21,41,17,38,18);
                        for($i=0;$i<count($id_b);$i++){
                            $c += 1;
                            $v = trim($linea[$c]); 
                            if($v == 1)	$proy->id_beneficiarios[] = $id_b[$i];
                            echo "<td>$v</td>";
                        }

                        //CANT. BENEFICIARIOS
                        $c += 1;
                        $proy->cant_benf_proy = $linea[$c];
                        echo "<td>$proy->cant_benf_proy</td>";

                        //COB. NAL
                        $c += 1;
                        $proy->cobertura_nal_proy = (trim($linea[$c] == 1))	? 1 : 0;
                        echo "<td>$proy->cobertura_nal_proy</td>";

                        echo "<td>";

                        //DEPTOS
                        $c += 1;
                        if ($proy->cobertura_nal_proy == 0){
                            $id_depto = trim($linea[$c]);

                            if (strlen($id_depto == 2)){
                                $depto = $depto_dao->Get($id_depto);
                                $proy->id_deptos[] = $depto->id;
                            }

                            echo $depto->nombre;
                        }

                            /*
                            $v = strtolower(trim($linea[$c]));
                            if (strlen($v) > 0 && !in_array($v,array("ninguno","n.d")) && strpos($v,"onal") === false){
                                $tmp = split("[,/;Y\-]",$linea[$c]);
                                //print_r($tmp);
                                foreach ($tmp as $d){
                                    $d = trim($d);
                                    if (strlen($d) > 2){

                                        if (strpos(strtolower($d),"bogot") === false){
                                            $d = str_replace(".","",$d);
                                        }

                                        $ids = $depto_dao->GetAllArray("nom_depto LIKE '%$d%'",'','');

                                        //Casos puntuales, Cauca y Santander
                                        if (strtolower($d) == "cauca")	$ids = array($ids[0]);
                                        if (strtolower($d) == "santander")	$ids = array($ids[1]);

                                        //Si existe solo una ocurrencia del depto, lo asocia
                                        if (count($ids) == 1){
                                            $depto = $ids[0];
                                            if (!in_array($depto->id,$proy->id_deptos)) $proy->id_deptos[] = $depto->id;
                                            echo "- ".$depto->nombre."<br>";
                                        }
                                        else{
                                            echo "<b>- No se encontró el depto ($d)</b><br>";

                                        }
                                    }
                                }
                            }
                             */
                        echo "</td>";

                        echo "<td>";

                        //MPIOS
                        $c += 1;

                        if ($proy->cobertura_nal_proy == 0){
                            $id_mun = trim($linea[$c]);
                            if (strlen($id_mun) == 5){
                                $mun = $mun_dao->Get($id_mun);
                                $proy->id_muns[] = $mun->id;
                            }

                            echo $mun->nombre;
                        }

                            /*
                            $proy->id_muns = array();
                            $v = strtolower(trim($linea[$c]));
                            if (strlen($v) > 0 && !in_array($v,array("ninguno","n.d")) && strpos($v,"onal") === false){
                                $tmp = split("[,/;Y:.\-]",$linea[$c]);
                                //print_r($tmp);
                                foreach ($tmp as $d){
                                    $d = trim($d);
                                    if (strlen($d) > 2){

                                        if (strpos(strtolower($d),"bogot") === false){
                                            $d = str_replace(".","",$d);
                                        }

                                        $ids = $mun_dao->GetAllArray("nom_mun = '$d'",'','');

                                        //Varios mpios con el mismo nombre
                                        if (count($ids) > 1){
                                            foreach ($ids as $mun){
                                                if (in_array(substr($mun->id,0,2),$proy->id_deptos)){

                                                    if (!in_array($mun->id,$proy->id_muns)) $proy->id_muns[] = $mun->id;
                                                    echo "- ".$mun->nombre."<br>";
                                                }
                                            }
                                        }

                                        //Si existe solo una ocurrencia del depto, lo asocia
                                        else if (count($ids) == 1){
                                            $mun = $ids[0];
                                            if (!in_array($mun->id,$proy->id_muns)) $proy->id_muns[] = $mun->id;
                                            echo "- ".$mun->nombre."<br>";
                                        }
                                        else{
                                            echo "<b>- No se encontró el mpio ($d)</b><br>";

                                        }
                                    }
                                }
                            }
                             */

                        echo "</td>";

                        echo "<td>";

                        //OFICINA DESDE LA QUE SE CUBRE
                        $c += 3;
                        if (strlen($linea[$c]) > 0 && !in_array(strtolower(trim($linea[$c])),array("ninguno","n.d"))){
                            $tmp = split("[,/]",$linea[$c]);
                            foreach ($tmp as $d){
                                $d = trim($d);

                                $ids = $org_dao->GetAllArrayID("nom_org LIKE '%$d%' OR sig_org LIKE '%$d%' AND id_org_papa = ".$proy->id_orgs_e[0],'','');

                                //Si existe solo una ocurrencia del nombre de la org, la asocia
                                if (count($ids) == 1){
                                    $id_o = $ids[0];
                                    $proy->id_orgs_d[] = $id_o;
                                    $nom = $org_dao->GetName($id_o);
                                    echo "- ".$nom."<br>";
                                }
                                else{
                                    if (!in_array($proy->id_orgs_e[0],$proy->id_orgs_cubre))	$proy->id_orgs_cubre[] = $proy->id_orgs_e[0];
                                    echo "- <b>No se encontró la org-oficina cubre ($d)</b><br>";
                                }
                            }
                        }
                        echo "</td>";

                        //Staff Nacional
                        $c += 1;
                        $proy->staff_nal_proy = (ereg("([0-9])",$linea[$c],$reg)) ? $reg[0] : 0 ;
                        echo "<td>$proy->staff_nal_proy</td>";

                        //Staff Internacional
                        $c += 1;
                        $proy->staff_intal_proy = (ereg("([0-9])",$linea[$c],$reg)) ? $reg[0] : 0 ;
                        echo "<td>$proy->staff_intal_proy</td>";


                        echo "<td>";

                        //ORGS. COOPERACION
                        $c += 1;
                        if (strlen($linea[$c]) > 0 && !in_array(strtolower(trim($linea[$c])),array("ninguno","n.d","ninguna"))){
                            $tmp = split("[,-/]",$linea[$c]);
                            foreach ($tmp as $d){
                                $d = trim($d);
                                $ids = $org_dao->GetAllArrayID("nom_org = '$d' OR sig_org = '$d'",'','');

                                //Si existe solo una ocurrencia del nombre de la org, la asocia
                                if (count($ids) == 1){
                                    $id_o = $ids[0];
                                    $proy->id_orgs_coor[] = $id_o;
                                    $nom = $org_dao->GetName($id_o);
                                    echo "- ".$nom."<br>";
                                }
                                else{
                                    echo "- <b>No se encontró la org trabajo coordinado ($d)</b><br>";

                                }

                            }
                        }
                        echo "</td>";

                    }
                    else{

                        //DEPTOS
                        $c = 52;
                        $id_depto = trim($linea[$c]);

                        if (strlen($id_depto) == 2){

                            $depto = $depto_dao->Get($id_depto);

                            if (!in_array($id_depto,$proy->id_deptos)){
                                $proy->id_deptos[] = $depto->id;
                            }

                            echo "<td>$depto->nombre</td>";
                        }

                        //MPIOS
                        $c += 1;
                        $id_mun = trim($linea[$c]);

                        if (strlen($id_mun) == 5){

                            $mun = $mun_dao->Get($id_mun);
                            if (!in_array($id_mun,$proy->id_muns)){
                                $proy->id_muns[] = $mun->id;
                            }

                            echo "<td>$mun->nombre</td>";
                        }

                    }


                    $cod_ant = $cod_proy;
                }
                echo "</tr>";

            }

            //INSERTA EL ULTIMO ********************
            $this->Insertar($proy);
            $num_proys++;

        }

        echo "Se importaron $num_proys";

    }


    /**
     * Listar los proyectos de una organizacion en el home del usuario (tipo undaf)
     * @param int $id_org
     * @access public
     */			
    function listarProyectoHomeTabs($id_org){

        $est_proy_dao = new EstadoProyectoDAO();
        $org_dao = new OrganizacionDAO();
        $depto_dao = new DeptoDAO();
        $tema_dao = new TemaDAO();
        $estados = $est_proy_dao->GetAllArray("");
        $max_chrs = 190;

        foreach($estados as $estado){

            $sql = "SELECT DISTINCT p.id_proy FROM proyecto p LEFT JOIN proyecto_tema p_t USING (id_proy) ";
            $sql .= " LEFT JOIN vinculorgpro v USING (id_proy) LEFT JOIN depto_proy USING (id_proy)";

            $cond = " si_proy='undaf' AND id_estp = $estado->id";

            //Tema
            if (isset($_GET["id_t"]) && $_GET["id_t"] != ''){
                $cond .= " AND p_t.id_tema = ".$_GET["id_t"];
            }

            //Ubicacion geografica
            $id_d = -1;
            if (isset($_GET["id_d"]) && $_GET["id_d"] != ''){
                //$cond .= " AND id_depto = ".$_GET["id_d"]." OR cobertura_nal_proy = 1";
                $id_d = $_GET["id_d"];

                $cond .= " AND id_depto = $id_d";
            }

            //Agraga el filtro por agencia en cobertura
            if ($id_org > 0){
                $cond .= " AND v.id_org = $id_org AND id_tipo_vinorgpro = 1";
            }


            //Consulta las letras y numeros iniciales de los proyectos para generar el indice-paginacion
            $letras_ini = $this->getLetrasIniciales($cond);
            $si_letra_ini = (count($letras_ini) > 0) ? 1 : 0;
            $letra_inicial = '';
            if ($si_letra_ini == 1){

                if (isset($_GET["li"]) && $_GET["li"] != ''){
                    $letra_inicial = $_GET["li"];
                    $cond .= " AND nom_proy LIKE '$letra_inicial%'";
                }

            }

            $sql .= "WHERE $cond ORDER BY nom_proy";

            //echo $sql;

            $proys = array();
            $rs = $this->conn->OpenRecordset($sql);
            while ($row = $this->conn->FetchRow($rs)){
                $proys[] = $row[0];
            }

            $num_proys = count($proys);

            //if ($num_proys > 0){

            echo '<div class="tabbertab"> <h2>'.$estado->nombre.' ('.$num_proys.')</h2><br />';

            /***********************
            /// FILTROS : INICIO
            /**********************/

            //Filtro rapido si son mas de 5 proyectos
            $id_num_search = "num_bus_r_".$estado->id;
            $div_num_search = "<span id='$id_num_search' style='width:150px;'>&nbsp;</span>";

            //echo "<div class='home_busqueda_rapida_proy'>
            echo "<table width='100%' cellspacing='0' cellpadding='0' class='home_busqueda_rapida_proy'>
                <tr><td align='right'><img src='images/undaf/home/reload.png'>&nbsp;<a href='/sissh/index_undaf.php?m_e=home'>Limpiar filtros</a></td></tr>
                <tr>
                <td>
                <img src='images/undaf/home/search.png'>&nbsp;Busqueda r&aacute;pida:&nbsp;<input type='text' class='textfield' style='width:350px' onkeyup=\"filtrarUL(this.value,'ul_proyectos_home_".$estado->id."','num_bus_r_".$estado->id."',' proyectos filtrados')\">
                $div_num_search
                </td>
                </tr>";

            //Perfil Admin-UNDAF
            if ($_SESSION["id_tipo_usuario_s"] == 30){

                echo "<br />
                    <tr><td><img src='images/undaf/home/search.png'>&nbsp;Filtrar por agencia:&nbsp;
                <select id='id_org_filtro' class='select' style='width:460px' onchange=\"filtroHome(this.value,'f_a')\">";
                echo '<option value=""></option>';

                $papas = $org_dao->GetAllArrayID('id_tipo=4 AND id_org_papa=0','','');
                foreach($papas as $id_papa){

                    $num_papa = count($this->GetIDByEjecutor(array($id_papa)));
                    $num_txt = '';
                    $style = '';
                    if ($num_papa > 0){

                        $nom_papa = $org_dao->GetName($id_papa);
                        echo "<option value=$id_papa";
                        if ($id_papa == $id_org)	echo " selected ";
                        echo ">".$num_txt.$nom_papa."</option>";

                        $hijos = $org_dao->GetAllArrayID('id_org_papa='.$id_papa,'','');

                        foreach($hijos as $id){
                            $num_hijo = count($this->GetIDByEjecutor(array($id)));
                            $num_txt = '';
                            $style = '';
                            if ($num_hijo > 0){
                                $nom_hijo = $org_dao->GetName($id);
                                echo "<option value=$id";
                                if ($id == $id_org)	echo " selected ";
                                echo ">&nbsp;&nbsp;l__&nbsp;".$num_txt.$nom_hijo."</option>";
                            }	
                        }
                    }
                }

                echo "</select>";
            }

            $id_t = (isset($_GET["id_t"])) ? $_GET["id_t"] : 0;;
            $temas = $this->getTemasConProyectos('id_clasificacion=1 AND id_papa=0');
            foreach ($temas as $tema){
                $id_temas[] = $tema->id;
            }

            echo "<br />
                <tr><td><img src='images/undaf/home/search.png'>&nbsp;Filtrar por &aacute;rea UNDAF:&nbsp;
            <select id='id_tema_filtro' class='select' style='width:460px' onchange=\"filtroHome(this.value,'f_t')\">";


            echo '<option value=""></option>';
            $tema_dao->ListarCombo('combo',$id_t,"id_tema IN (".implode(",",$id_temas).")");
            echo "</select>
                <br />
                <tr><td><img src='images/undaf/home/search.png'>&nbsp;Filtrar por territorio:&nbsp;
            <select id='id_depto_filtro' class='select' onchange=\"filtroHome(this.value,'f_d')\">";
            echo '<option value=""></option>';
            $depto_dao->ListarCombo('combo',$id_d,'');
            echo "</select>";

            echo "</div>";

            echo "</table>";

            /***********************
            /// FILTROS : FIN
            /**********************/

            echo "<div style='clear:both'></div><div style='float:right;'><br /><img src='images/undaf/consulta/excel.gif' border=0 title='Exportar a Excel'>&nbsp;<a href='#' onclick=\"listado($estado->id,'".strtolower(str_replace(" ","_",$estado->nombre))."','$id_num_search');return false;\">Exportar listado</a></div>";

            //INDICE
            $this->indiceHomeProys($letras_ini,$letra_inicial);

            echo $div_num_search;

            echo "<br />&nbsp;";
            echo "<ul id='ul_proyectos_home_".$estado->id."' style='clear:both'>";

            foreach($proys as $i=>$id_proy){
                $nom = $this->GetName($id_proy);
                $nombre = (strlen($nom) > $max_chrs) ? substr($nom,0,$max_chrs)."..." : $nom;

                $id_org_e = $this->getOrgEjecutora($id_proy);
                $sig_org_e = $org_dao->GetFieldValue($id_org_e[0],'sig_org');

                echo "<li>
                    <table border=0 width='100%'>
                    <tr>
                    <td colspan='2'>
                    <a href='t/index_undaf.php?m_e=proyecto&accion=actualizar&id=$id_proy' title='$nom' style='font-size:14px;font-weight:bold;color:#000000;'>$nombre<a>&nbsp;&nbsp;
                </td>
                    </tr>";
                echo "<tr>
                    <td style='width:150px;color:#008CD6;'>Agencia: <b>$sig_org_e</b></td>
                    <td align='right'>";
                //<div id='op_$id_proy' style='display:none;z-index:100;position:absolute;left:572px;border:1px solid #cccccc;width:130px;background:#f1f1f1;height:58px;padding:5px;padding-top:10px;text-align:left;' onmouseover=\"document.getElementById('op_$id_proy').style.display='';\" onmouseout=\"document.getElementById('op_$id_proy').style.display='none';\">
                echo "	<div id='op_$id_proy'>
                    <img src='images/undaf/home/pdf.gif' border='0' />&nbsp;&nbsp;<a href=\"download_pdf.php?c=3&id=$id_proy\">Consultar Ficha PDF</a>
                    &nbsp;<img src='images/undaf/home/mapa.png' border='0' />&nbsp;<a href='#' onclick=\"mapaProyectoCobertura($id_proy,'mostrar')\">Acceder a Mapa</a>
                    &nbsp;<img src='images/undaf/home/borrar.gif' border='0' />&nbsp;<a href='#' onclick=\"if(confirm('Está seguro que desea eliminar el proyecto?')){location.href='?m_e=home&accion=borrar&id=$id_proy';}return false;\">Eliminar</a>
                    </div>";

                //<a href='#' onmouseover=\"document.getElementById('op_$id_proy').style.display='';\" onmouseout=\"document.getElementById('op_$id_proy').style.display='none';\">Opciones &raquo;</a>
                echo "</td>
                    </tr>";

                echo "	</table>
                    </li>";
            }
            echo "</ul><br />";

            //INDICE
            $this->indiceHomeProys($letras_ini,$letra_inicial);

            echo "</div>";
            //}
        }

        // Lista los proyectos en trabajo coordinado donde NO ES LA LIDER

        //Consulta las letras y numeros iniciales de los proyectos para generar el indice-paginacion

        $proys = $this->getIdProyectosCoorByOrg($id_org);

        $num_proys = count($proys);

        echo '<div class="tabbertab"> <h2>T. Coor. ('.$num_proys.')</h2><br />';

        echo "<div style='font-size: 14px;'>Estos son los proyectos en los que realiza un trabajo coordinado con la agencia l&iacute;der</div>";
        echo "<div style='font-size: 12px;'>Como no es la agencia l&iacute;der solo podr&aacute; consultar informaci&oacute;n</div>";
        
        /***********************
        /// FILTROS : INICIO
        /**********************/

        //Filtro rapido si son mas de 5 proyectos
        $id_num_search = "num_bus_r_".$estado->id;
        $div_num_search = "<span id='$id_num_search' style='width:150px;'>&nbsp;</span>";

        //echo "<div class='home_busqueda_rapida_proy'>
        echo "<table width='100%' cellspacing='0' cellpadding='0' class='home_busqueda_rapida_proy'>
            <tr><td align='right'><img src='images/undaf/home/reload.png'>&nbsp;<a href='/sissh/index_undaf.php?m_e=home'>Limpiar filtros</a></td></tr>
            <tr>
            <td>
            <img src='images/undaf/home/search.png'>&nbsp;Busqueda r&aacute;pida:&nbsp;<input type='text' class='textfield' style='width:350px' onkeyup=\"filtrarUL(this.value,'ul_proyectos_home_coor','num_bus_r_".$estado->id."',' proyectos filtrados')\">
            $div_num_search
            </td>
            </tr>";

        //Perfil Admin-UNDAF
        if ($_SESSION["id_tipo_usuario_s"] == 30){

            echo "<br />
                <tr><td><img src='images/undaf/home/search.png'>&nbsp;Filtrar por agencia:&nbsp;
            <select id='id_org_filtro' class='select' style='width:460px' onchange=\"filtroHome(this.value,'f_a')\">";
            echo '<option value=""></option>';

            $papas = $org_dao->GetAllArrayID('id_tipo=4 AND id_org_papa=0','','');
            foreach($papas as $id_papa){

                $num_papa = count($this->GetIDByEjecutor(array($id_papa)));
                $num_txt = '';
                $style = '';
                if ($num_papa > 0){

                    $nom_papa = $org_dao->GetName($id_papa);
                    echo "<option value=$id_papa";
                    if ($id_papa == $id_org)	echo " selected ";
                    echo ">".$num_txt.$nom_papa."</option>";

                    $hijos = $org_dao->GetAllArrayID('id_org_papa='.$id_papa,'','');

                    foreach($hijos as $id){
                        $num_hijo = count($this->GetIDByEjecutor(array($id)));
                        $num_txt = '';
                        $style = '';
                        if ($num_hijo > 0){
                            $nom_hijo = $org_dao->GetName($id);
                            echo "<option value=$id";
                            if ($id == $id_org)	echo " selected ";
                            echo ">&nbsp;&nbsp;l__&nbsp;".$num_txt.$nom_hijo."</option>";
                        }	
                    }
                }
            }

            echo "</select>";
        }

        echo "<br />
            <tr><td><img src='images/undaf/home/search.png'>&nbsp;Filtrar por &aacute;rea UNDAF:&nbsp;
        <select id='id_tema_filtro' class='select' style='width:460px' onchange=\"filtroHome(this.value,'f_t')\">";


        echo '<option value=""></option>';
        $tema_dao->ListarCombo('combo',$id_t,"id_tema IN (".implode(",",$id_temas).")");
        echo "</select>
            <br />
            <tr><td><img src='images/undaf/home/search.png'>&nbsp;Filtrar por territorio:&nbsp;
        <select id='id_depto_filtro' class='select' onchange=\"filtroHome(this.value,'f_d')\">";
        echo '<option value=""></option>';
        $depto_dao->ListarCombo('combo',$id_d,'');
        echo "</select>";

        echo "</div>";

        echo "</table>";

        /***********************
        /// FILTROS : FIN
        /**********************/

        echo "<div style='clear:both'></div><div style='float:right;'><br /><img src='images/undaf/consulta/excel.gif' border=0 title='Exportar a Excel'>&nbsp;<a href='#' onclick=\"listado($estado->id,'".strtolower(str_replace(" ","_",$estado->nombre))."','$id_num_search');return false;\">Exportar listado</a></div>";
        
        echo $div_num_search;

        echo "<br />&nbsp;";
        echo "<ul id='ul_proyectos_home_coor' style='clear:both'>";

        foreach($proys as $i=>$id_proy){
            $nom = $this->GetName($id_proy);
            $nombre = (strlen($nom) > $max_chrs) ? substr($nom,0,$max_chrs)."..." : $nom;

            $id_org_e = $this->getOrgEjecutora($id_proy);
            $sig_org_e = $org_dao->GetFieldValue($id_org_e[0],'sig_org');

            echo "<li>
                <table border=0 width='100%'>
                <tr>
                <td colspan='2'>
                $nombre&nbsp;&nbsp;
            </td>
                </tr>";
            echo "<tr>
                <td style='width:150px;color:#008CD6;'>Agencia L&iacute;der: <b>$sig_org_e</b></td>
                <td align='right'>";
            //<div id='op_$id_proy' style='display:none;z-index:100;position:absolute;left:572px;border:1px solid #cccccc;width:130px;background:#f1f1f1;height:58px;padding:5px;padding-top:10px;text-align:left;' onmouseover=\"document.getElementById('op_$id_proy').style.display='';\" onmouseout=\"document.getElementById('op_$id_proy').style.display='none';\">
            echo "	<div id='op_$id_proy'>
                <img src='images/undaf/home/pdf.gif' border='0' />&nbsp;&nbsp;<a href=\"download_pdf.php?c=3&id=$id_proy\">Consultar Ficha PDF</a>
                &nbsp;<img src='images/undaf/home/mapa.png' border='0' />&nbsp;<a href='#' onclick=\"mapaProyectoCobertura($id_proy,'mostrar')\">Acceder a Mapa</a>
                </div>";

            echo "</td>
                </tr>";

            echo "	</table>
                </li>";
        }
        echo "</ul><br />";

        echo "</div>";
        //}

    }

    /**
     * Muestra el indice en el listado de home de proyectos
     * @access public
     * @param array $letras_ini Arreglo con los caracteres
     * @param string $letra_inicial Actual letra inicial
     */			
    function indiceHomeProys($letras_ini,$letra_inicial){
        echo "<div class='indice'><img src='images/undaf/home/indice.png'>&nbsp;Indice:&nbsp;";

        //Todos
        $class = ($letra_inicial == '') ? 'a_big' : 'a_normal' ;
        echo "<a href='#'; onclick=\"filtroHome('','f_li');return false;\" class='$class'>Todos</a>&nbsp;&nbsp;";

        foreach ($letras_ini as $letra){
            $class = (strtolower($letra) == strtolower($letra_inicial)) ? 'a_big' : 'a_normal' ;

            echo "<a href='#'; onclick=\"filtroHome('$letra','f_li');return false;\" class='$class'>".strtoupper($letra)."</a>";
            echo "&nbsp;&nbsp;";
        }

        echo "</div>";
    }

    /**
     * Consulta los caracteres iniciales de los nombres de los proyectos iniciales
     * @access public
     * @param string $cond Condicion que deben cumplir los proyectos
     * @return array $chrs Arreglo con los caracteres
         */			
    function getLetrasIniciales($cond=''){

        $arr = Array();
        $sql = "SELECT DISTINCT LEFT(nom_proy,1) FROM proyecto p LEFT JOIN proyecto_tema p_t USING (id_proy) ";
        $sql .= " LEFT JOIN vinculorgpro v USING (id_proy) LEFT JOIN depto_proy USING (id_proy)";

        if ($cond != ''){
            $sql .= " WHERE $cond";
        }

        $sql .= " ORDER BY nom_proy";

        $rs = $this->conn->OpenRecordset($sql);
        while ($row = $this->conn->FetchRow($rs)){
            $arr[] = $row[0];
        }

        return $arr;
    }

    /******************************************************************************
     * Consulta los ID de los proyectos en los que la Org esta en trabajo coordinado
     * con la ejecutora
     * @param string $case Tema, Poblacion
     * @param int $id Id de Tema, Poblacion
     * @param boolean $depto 1=Departamento , 0=Municipio, 2=Nacional
     * @param string $ubicacion ID de la ubiaccion
     * @param string $cond Condicion extra a aplicar al SQL
     * @return array $num
     * @access public
     *******************************************************************************/
    function getIdProyectosCoorByOrg($id_org){

        $id_proys = array();

        $sql = "SELECT $this->columna_id FROM vinculorgpro WHERE id_tipo_vinorgpro = 4 AND id_org = $id_org AND
                $this->columna_id NOT IN (SELECT $this->columna_id FROM vinculorgpro WHERE id_tipo_vinorgpro = 1 AND id_org = $id_org)";
        
        $rs = $this->conn->OpenRecordset($sql);
        while ($row = $this->conn->FetchRow($rs)){
            $id_proys[] = $row[0];
        }

        return $id_proys;
    }

    /******************************************************************************
     * Consulta los ID de los proyectos segun filtros
     * @param string $case Tema, Poblacion
     * @param int $id Id de Tema, Poblacion
     * @param boolean $depto 1=Departamento , 0=Municipio, 2=Nacional
     * @param string $ubicacion ID de la ubiaccion
     * @param string $cond Condicion extra a aplicar al SQL
     * @return array $num
     * @access public
     *******************************************************************************/
    function getIdProyectosReporte($case,$id,$depto,$ubicacion,$cond=''){

        switch ($case) {
        case 'tema':
            $tabla = "proyecto_tema";
            $col_id_filtro = "id_tema";
            break;

        case 'poblacion':
            $tabla = "proyecto_beneficiario";
            $col_id_filtro = "id_pobla";
            break;

        case 'agencia':
            $tabla = "vinculorgpro";
            $col_id_filtro = "id_org";
            break;

        case 'cobertura':
            $tabla = "proyecto";

            $id_filtro = $id;

            $col_id_filtro = "1";
            $id = 1;

            //Caso cobertura nacional
            if ($depto == 2){
                $id = 1;
                $col_id_filtro = 'cobertura_nal_proy';
            }
            break;

        case 'estado':
            $tabla = "proyecto";
            $col_id_filtro = "id_estp";
            break;
        }

        //Undaf
        //if (isset($_SESSION["id_org"])){
        //	$sql = "SELECT v.".$this->columna_id." FROM vinculorgpro v JOIN $tabla USING($this->columna_id) ";
        //}
        //else{
        $sql = "SELECT p.id_proy FROM ";

        if ($tabla != 'proyecto')	$sql .= " proyecto JOIN ";

        $sql .= " $tabla p ";

        if ($tabla != 'proyecto')	$sql .= " USING ($this->columna_id) ";

        //}

        //Agraga el filtro por agencia en cobertura
        if ($case == 'cobertura' && $id_filtro > 0){
            $sql .= " LEFT JOIN vinculorgpro v USING ($this->columna_id) ";
        }
        //Ubicacion geografica
        if ($depto == 1){
            //$sql .= " LEFT JOIN depto_proy USING($this->columna_id) WHERE id_depto = $ubicacion OR cobertura_nal_proy = 1";
            $sql .= " LEFT JOIN depto_proy USING($this->columna_id) WHERE id_depto = $ubicacion";
        }
        else if ($depto == 0){
            //$sql .= " LEFT JOIN mun_proy USING ($this->columna_id) WHERE id_mun = $ubicacion OR cobertura_nal_proy = 1";
            $sql .= " LEFT JOIN mun_proy USING ($this->columna_id) WHERE id_mun = $ubicacion";
        }
        else{
            $sql .= "WHERE 1=1 ";
        }

        $sql .= " AND $col_id_filtro IN ($id)";

        //Agraga el filtro por agencia en cobertura
        if ($case == 'agencia'){
            $sql .= " AND id_tipo_vinorgpro = 1";
        }
        //Agraga el filtro por agencia en cobertura
        if ($case == 'cobertura' && $id_filtro > 0){
            $sql .= " AND id_org = $id_filtro AND id_tipo_vinorgpro = 1";
        }

        if ($cond != '')	$sql .= " AND $cond";

        $rs = $this->conn->OpenRecordset($sql);

        $arr = array();
        while ($row = $this->conn->FetchRow($rs)){
            $arr[] = $row[0];
        }

        return $arr;
    }

    /******************************************************************************
     * Cuenta el número de Proyectos de un Tema, Poblacion, etc
     * @param string $case Tema, Poblacion
     * @param int $id Id de Tema, Poblacion
     * @param boolean $depto 1=Departamento , 0=Municipio, 2=Nacional
     * @param string $ubicacion ID de la ubiaccion
     * @param string $si Sistema de info, sidih, undaf, etc
     * @return array $num
     * @access public
     *******************************************************************************/
    function numProyectos($case,$id,$depto,$ubicacion,$si='undaf'){

        return count($this->getIdProyectosReporte($case,$id,$depto,$ubicacion,"si_proy='$si'"));

    }

    /******************************************************************************
     * Retorna los temas que tienen proyectos
     * @param string $condicion Condicion que deben cumplir los temas
     * @return array $num
     * @access public
     *******************************************************************************/
    function getTemasConProyectos($condicion){

        $tema_dao = new TemaDAO();	

        $sql = "SELECT DISTINCT id_tema from proyecto_tema JOIN tema USING(id_tema) WHERE $condicion";
        $rs = $this->conn->OpenRecordset($sql);
        while ($row = $this->conn->FetchRow($rs)){
            $temas[] = $tema_dao->Get($row[0]);
        }

        return $temas;
    }

    /******************************************************************************
     * Genera la ficha PDF de un proyecto
     * @param int $id Id del Proyecto
     * @access public
     *******************************************************************************/
    function fichaPdf($id){

        //LIBRERIAS
        include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/common/archivo.class.php");
        include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/common/date.class.php");
        include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/config.php");

        //INICIALIZACION DE VARIABLES
        $archivo = New Archivo();
        $date = New Date();
        $tema_dao = New TemaDAO();
        $poblacion_dao = New PoblacionDAO();
        $depto_dao = New DeptoDAO();
        $mun_dao = New MunicipioDAO();
        $moneda_dao = New MonedaDAO();
        $estado_dao = New EstadoProyectoDAO();
        $org_dao = New OrganizacionDAO();

        $f_name = "ficha_$id.pdf";
        //$f_cache = $conf['proyecto']['dir_cache']."/".$f_name;
        $f_cache = $_SERVER["DOCUMENT_ROOT"]."/sissh/consulta/cache_proyecto/$f_name";
        //Genera la ficha
        if ($this->checkCacheFichaPdf($id,$f_cache) == 1){

            $html = '
                <html>
                <head>
                <title>Ficha Proyecto</title>
                <link href="style/consulta_undaf.css" type="text/css" rel="stylesheet">
                <style>
                .proy_tabla_ficha{
                    background: #000000;
        }
        .proy_tabla_ficha_td{
            background: #FFFFFF;
        }
        </style>
            </head>
            <body>';

        $html .= $this->fichaPdfCodigoHTML($id);
        //die($html);

        $html .= "</body></html>";

        //Html 2 Pdf
        include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/common/html2fpdf/html2fpdf.php");

        //Horizontal
        $pdf = new HTML2FPDF('P','mm','Letter');
        //$pdf = new HTML2FPDF('L');

        $pdf->AddPage();

        $pdf->WriteHTML($html);
        //echo $html;
        $pdf_code = $pdf->Output('','S');
        //$this->createFileCache($pdf_code,$f_cache);

        echo $pdf_code;


        }
        else{
            $fp = $archivo->Abrir($f_cache,'r');
            echo $archivo->LeerEnString($fp,$f_cache);
        }
    }


    /******************************************************************************
     * Genera el codigo HTML para la ficha PDF de un proyecto
     * @param int $id Id del Proyecto
     * @access public
     *******************************************************************************/
    function fichaPdfCodigoHTML($id){

        //LIBRERIAS
        include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/common/date.class.php");

        //INICIALIZACION DE VARIABLES
        $date = New Date();
        $tema_dao = New TemaDAO();
        $poblacion_dao = New PoblacionDAO();
        $depto_dao = New DeptoDAO();
        $mun_dao = New MunicipioDAO();
        $moneda_dao = New MonedaDAO();
        $estado_dao = New EstadoProyectoDAO();
        $org_dao = New OrganizacionDAO();


        $proy = $this->Get($id);
        $hoy = getdate();
        $meses = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Novimebre","Diciembre");
        $hoy = $hoy["mday"]." ".$meses[$hoy["mon"]]." ".$hoy["year"];

        ob_start();
        ?>

        <table width="100%">
            <tr><td align="right">Fecha Reporte: <?=$hoy?></td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td><b>INICIATIVAS, PROYECTOS Y PROGRAMAS DEL SISTEMA DE LAS NACIONES UNIDAS EN COLOMBIA</b></td></tr>
        </table>
        <br />
        <table border='1'>
        <?
        echo "<tr><td colspan='3'><b>$proy->nom_proy</b></td>";
        //ESTADO
        $est_vo = $estado_dao->Get($proy->id_estp);
        echo "<td>$est_vo->nombre</td></tr>";

        //EJECUTORAS
        $n = $org_dao->GetName($proy->id_orgs_e[0]);
        echo "<tr><td colspan='2'><b>Agencia</b>: ".$n."</td>";

        //INICIO
        echo "<td><b>Inicio</b>: ".$date->Format($proy->inicio_proy,'aaaa-mm-dd','dd-MC-aaaa')."</td>";

        //DURACION
        echo "<td><b>Duraci&oacute;n</b> (meses): $proy->duracion_proy</td></tr>";


        echo "<tr><td colspan='2'><b>Donantes</b>:<br /> ";

        //DONANTES
        foreach ($proy->id_orgs_d as $id){
            $n = $org_dao->GetName($id);
            echo "- $n<br />";
        }
        echo "</td>";

        //echo "<td>Socios - Gobierno (contra partes) y/o  agencias del SNU en el caso de actividades articuladas (MoU, JO, otras modalidades)  y contrapartes implementadoras:";
        echo "<td colspan='2'><b>Socios</b>: <br />";

        //SOCIOS
        foreach ($proy->id_orgs_s as $id){
            $n = $org_dao->GetName($id);
            echo "- $n<br />";
        }

        $moneda = $moneda_dao->Get($proy->id_mon);
        
        if ($proy->valor_aporte_socios != '' && $proy->valor_aporte_socios != 0){
            echo "<br /><b>Aportes de socios</b>: $moneda->nombre $proy->valor_aporte_socios";
        }
        
        echo "</td></tr>";
        
        // PRESUPUESTO
        
        if ($proy->joint_programme_proy == 1){
            $marco_proy = 'Joint Programme';
        }

        if ($proy->mou_proy == 1){
            $marco_proy = 'MOU';
        }
        
        if ($proy->interv_ind_proy == 1){
            $marco_proy = 'Agencia Independiente';
        }

        echo "<tr><td colspan='4'><b>PRESUPUESTO</b>:<br />1. Marco en el que se desarrolla: $marco_proy";

        //COSTO
        $costo = '';
        if ($proy->costo_proy > 0){
            $costo = $moneda->nombre." ".$proy->costo_proy;
        }
        
        echo "<br />2. Presupuesto total: $costo";
        
        // OFICINAS TRABAJO COORDINADO
        if (count($proy->id_orgs_coor) > 0){
            $nbsp_left = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

            echo "<br />3. Agencias del SNU con las existe un trabajo coordinado para este proyecto: <br /><br />";

            foreach ($proy->id_orgs_coor as $id_org_coor){
                $sig_org_coor = $org_dao->GetFieldValue($id_org_coor,'sig_org');

                echo "$nbsp_left- <b>$sig_org_coor</b>:&nbsp;&nbsp;";

                echo "Aporte: $moneda->nombre ".$proy->id_orgs_coor_valor_ap[$id_org_coor]."<br />";
                
            }
        }

        echo "</td></tr>";

        //APORTE AGENCIAS
        //echo "<tr><td>Aportes de las agencias: $proy->valor_aporte_donantes</td>";

        echo "<tr><td colspan='4'><b>TEMAS Y PROGRAMAS</b></td></tr>";

        //Consulta los temas generales
        $temas_abuelo = $tema_dao->GetAllArray('id_papa = 0','','');

        foreach ($temas_abuelo as $abuelo){

            $id_tema = $abuelo->id;
            $txt = '';
            $t = 'No';

            if (array_key_exists($id_tema,$proy->id_temas)){
                $txt = (isset($proy->texto_extra_tema[$id_tema])) ? $proy->texto_extra_tema[$id_tema] : '';

                $t = 'Si';

                foreach ($proy->id_temas[$id_tema]['hijos'] as $id_t_tmp){
                    $vo_tmp = $tema_dao->Get($id_t_tmp);
                    $t .= "<br />-&nbsp;".$vo_tmp->nombre;
                }
            }

            $vo_tmp = $tema_dao->Get($id_tema);
            echo "<tr><td colspan='4'><b>$vo_tmp->nombre</b>: $t";
            if ($txt != '')	echo "<br />$txt";
            echo "</td></tr>";
        }

        echo "<tr><td colspan='4'><b>BENEFICIARIOS</b> DE LOS PROGRAMAS/PROYECTOS</td></tr>";
        echo "<tr><td colspan='2'>Tipo de beneficiarios directos:<br />";

        //POBLACION
        foreach($proy->id_beneficiarios as $i=>$id){
            $dao = $poblacion_dao->Get($id);
            if($i > 0)	echo ", ";
            echo $dao->nombre_es;
        }
        
        echo "<br /><br />Tipo de beneficiarios indirectos:<br />";

        //POBLACION
        foreach($proy->id_beneficiarios_indirectos as $i=>$id){
            $dao = $poblacion_dao->Get($id);
            if($i > 0)	echo ", ";
            echo $dao->nombre_es;
        }
        echo "</td>";

        //CANT. BENEFICIARIOS
        //echo "<td colspan='2'>Cantidad de beneficiarios directos del proyecto (opcional): genero, etnico, etáreo, situación de desplazamiento, etc<br />$proy->cant_benf_proy</td>";
        echo "<td colspan='2'>Cantidad de beneficiarios del proyecto<br />$proy->cant_benf_proy</td>";
        echo "</tr>";

        echo "<tr><td colspan='3'><b>COBERTURA GEOGR&Aacute;FICA</b></td>";
        $cob = ($proy->cobertura_nal_proy) ? 'Si' : 'No';
        echo "<td>Cobertura nacional: $cob</td></tr>";

        echo "<tr><td colspan='4'>Departamentos: ";

        //DEPTOS
        foreach ($proy->id_deptos as $i=>$id){
            $dao = $depto_dao->Get($id);
            if($i > 0)	echo ", ";
            echo $dao->nombre;
        }
        echo "</td></tr>";

        echo "<tr><td colspan='4'>Municipios: ";

        //MPIOS

        foreach ($proy->id_muns as $i=>$id){
            $dao = $mun_dao->Get($id);
            if($i > 0)	echo ", ";
            echo $dao->nombre;
        }

        echo "</td></tr>";

/*
echo "<tr><td colspan='2'>Oficina desde la que se cubre este proyecto: ";

//OFICINA DESDE LA QUE SE CUBRE
foreach ($proy->id_orgs_cubre as $i=>$id){
    $n = $org_dao->GetName($id);
    if($i > 0)	echo ", ";
    echo $n;
}

echo "</td>";

//Staff Nacional
echo "<td>Staff nacional dedicado al proyecto (número de personas): $proy->staff_nal_proy</td>";

//Staff Internacional
echo "<td>Staff internacional dedicado al proyecto (número de personas): $proy->staff_intal_proy</td>";

echo "</tr>";
         

    echo "<tr><td colspan='4'>Existe en este proyecto un trabajo coordinado con otras agencias del SNU? Cu&aacute;les? <br />";

    //ORGS. COOPERACION
    foreach ($proy->id_orgs_coor as $i=>$id){
        $n = $org_dao->GetName($id);
        if($i > 0)	echo ", ";
        echo $n;
    }

    echo "</td></tr>";
    */
    
    echo "</table>";

    $fecha_actua = $date->Format($proy->actua_proy,'aaaa-mm-dd','dd-MM-aaaa','-');
    echo "<br /><table width='100%'><tr><td align='right'>Fecha &uacute;ltima actualizaci&oacute;n de la ficha: $fecha_actua</td></tr>";
    echo "</table>";


    $html = ob_get_contents();
    ob_end_clean();

    return $html;

    }

    /******************************************************************************
     * Check si se debe generar la ficha PDF
     * @param string $f_cache Nombre del archivo fisico
     * @param int $id ID del proyecto
     * @return int $generar 1 o 0
     * @access public
     *******************************************************************************/
    function checkCacheFichaPdf($id,$f_cache){

        include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/common/archivo.class.php");

        $archivo = New Archivo();
        $gen = 0;

        if ($archivo->Existe($f_cache)){

            //Consulta la ultima fecha de actualizacion del proyecto
            $fecha_file = $archivo->fechaModificacion($f_cache);

            $proy = $this->Get($id);

            //Compara las 2 fechas
            $fecha_db = explode("-",$proy->actua_proy);
            $fecha_db = mktime(0,0,0,$fecha_db[1], $fecha_db[2], $fecha_db[0]);

            //Genera la ficha
            if ($fecha_db > $fecha_file){
                $gen = 1;
            }
        }
        else{
            $gen = 1;
        }

        return $gen;
    }

    /**
     * createFilePDF
     * @access public
     * @param string $file_content 
     * @param string $nom_file
         */
    function createFileCache($file_content,$nom_file){

        $file = New Archivo();

        //CREA UN ARCHIVO LOCAL
        $nom_archivo = $nom_file;

        $fp = $file->Abrir($nom_archivo,'w+');
        $file->Escribir($fp,$file_content);
        $file->Cerrar($fp);
    }


}



/**
 * Ajax de Organizacion
 *
 * Contiene los metodos para Ajax de la clase Organizacion
 * @author Ruben A. Rojas C.
                 */

            Class ProyectoAjax extends ProyectoDAO {

                /**
                 * Reporte Proyectos por tema, poblacion
                 * @access public
                 * @param int $ubicacion ID de la Ubicacion Geográfica
                 * @param int $depto 1 o 0
                 * @param string $filtro Tema, Poblacion
                 * @param int $id_filtro Valor de Tema, Poblacion
                 * @param string $caso Listado, Reporte On Line, Reporte Pdf
                 * @param int $show_html Mostrar el codigo html
                 */
            function reporteProyectoUndaf($ubicacion,$depto,$filtro,$id_filtro,$caso,$show_html=1){
                require_once "lib/libs_proyecto.php";

                //INICIALIZACION DE VARIABLES
                $tema_dao = New TemaDAO();
                $poblacion_dao = New PoblacionDAO();
                $depto_dao = New DeptoDAO();
                $mun_dao = New MunicipioDAO();
                $moneda_dao = New MonedaDAO();
                $estado_dao = New EstadoProyectoDAO();
                $org_dao = New OrganizacionDAO();


                if ($filtro != 'listado_completo'){
                    $nom_ubi = "Nacional";
                    if ($depto == 1){
                        $ubi = $depto_dao->Get($ubicacion);
                        $nom_ubi = $ubi->nombre;
                    }
                    else if ($depto == 0){
                        $ubi = $mun_dao->Get($ubicacion);
                        $nom_ubi = $ubi->nombre;
                    }

                    //CONSULTA LOS ID de PROYECTOS
                    $arr = $this->getIdProyectosReporte($filtro,$id_filtro,$depto,$ubicacion,"si_proy='undaf'");
                }
                else{
                    $arr = $this->GetAllArrayID("si_proy='undaf'",'','');
                }

                $num_arr = count($arr);

                //switch ($caso){
                //	case 'reporteOnLineProyectoUndaf':

                if ($num_arr == 0){
                    echo "<p><b>No se encontraron Proyectos</b></p>";
                }
                else{
                    if ($show_html == 1){
                        echo "<p><img src='images/undaf/consulta/excel.gif' border=0 title='Exportar a Excel'>&nbsp;
                        <a href=\"#\" onclick=\"location.href='export_data.php?case=xls_session&nombre_archivo=proyectos';return false;\"\">Exportar a Hoja de C&aacute;lculo</a>
                            ( $num_arr Proyectos ) </p>";
                    }


                    $html = "<table cellpadding='2' cellspacing='1' class='listado'>";
                    $html .=	"<tr class='titulo'>
                        <td>Nombre o Descripci&oacute;n</td>
                        <td>Estado</td>
                        <td>Agencia</td>
                        <td>Fecha de inicio</td>
                        <td>Duración</td>
                        <td>Recursos</td>
                        <td>Donantes</td>
                        <td>Socios</td>
                        <td>Aportes Agencias</td>
                        <td>Aportes socios</td>";

                    $temas_papa = $tema_dao->GetAllArray('id_papa=0','','id_tema');
                    foreach ($temas_papa as $tema_papa){
                        $arbol = array('id'=>array(),'nombre'=>array());

                        $id_papa = $tema_papa->id;

                        $temas = $tema_dao->getArbolNombreNumerado($id_papa,$id_papa,$arbol,0,0,1);

                        $html .= "<td>$id_papa $tema_papa->nombre</td>";
                        foreach($temas['nombre'] as $t_titulo){
                            $html .= "<td>$t_titulo</td>";
                        }
                    }


                    $html .= "<td>Afro</td>
                        <td>Jóvenes</td>
                        <td>Indígenas</td>
                        <td>Adultos mayores</td>
                        <td>Mestizos</td>
                        <td>Desplazados</td>	
                        <td>Niños</td>
                        <td>Campesinos</td>
                        <td>Pob. Receptora</td>
                        <td>Sindical</td>
                        <td>Pob. Reubicada</td>
                        <td>Mujeres</td>
                        <td>Pob. Confinada</td>
                        <td>No. Beneficiarios</td>
                        <td>Cob Nacional</td>
                        <td>Departamentos</td>
                        <td>Municipios</td>
                        <td>Oficina que cubre el proyecto</td>
                        <td>Staff Nacional</td>
                        <td>Staff Internacional</td>
                        <td>Trabajo con agencias del SNU</td>
                        </tr>";

                    foreach ($arr as $id){

                        $proy = $this->Get($id);

                        $html .= "<tr>";

                        //NOMBRE
                        $html .= "<td>$proy->nom_proy</td>";

                        //ESTADO
                        $est_vo = $estado_dao->Get($proy->id_estp);
                        $html .= "<td>$est_vo->nombre</td>";

                        //EJECUTORAS
                        $n = $org_dao->GetName($proy->id_orgs_e[0]);
                        $html .= "<td>".$n."</td>";

                        //INICIO
                        //$proy->inicio_proy =  str_replace("/","-",$linea[3]);
                        $html .= "<td>$proy->inicio_proy</td>";

                        //DURACION
                        //$proy->duracion_proy = (ereg("([0-9])",$linea[4])) ? str_replace(",",".",$linea[4]) : 0;
                        $html .= "<td>$proy->duracion_proy</td>";

                        //COSTO
                        $costo = '';
                        if ($proy->costo_proy > 0){
                            $moneda = $moneda_dao->Get($proy->id_mon);
                            $costo = $moneda->nombre." ".$proy->costo_proy;
                        }
                        $html .= "<td>$costo</td>";


                        $html .= "<td>";

                        //DONANTES
                        foreach ($proy->id_orgs_d as $id){
                            $n = $org_dao->GetName($proy->id_orgs_e[0]);
                            $html .= $n;
                        }
                        $html .= "</td>";

                        $html .= "<td>";

                        //SOCIOS
                        foreach ($proy->id_orgs_s as $id){
                            $n = $org_dao->GetName($id);
                            $html .= $n;
                        }
                        $html .= "</td>";

                        //APORTE AGENCIAS
                        $html .= "<td>$proy->valor_aporte_donantes</td>";

                        //APORTE SOCIOS
                        $html .= "<td>$proy->valor_aporte_socios</td>";
                        $temas_papa = $tema_dao->GetAllArray('id_papa=0','','id_tema');
                        foreach ($temas_papa as $tema_papa){
                            $arbol = array('id'=>array(),'nombre'=>array());

                            $id_papa = $tema_papa->id;

                            $x =  (array_key_exists($id_papa,$proy->id_temas)) ? 'x' : '';
                            $html .= "<td>$x</td>";

                            $temas = $tema_dao->getArbolNombreNumerado($id_papa,$id_papa,$arbol,0,0,1);
                            foreach($temas['id'] as $id_tema){
                                $x =  (isset($proy->id_temas[$id_papa]['hijos']) && in_array($id_tema,$proy->id_temas[$id_papa]['hijos'])) ? 'x' : '';
                                $html .= "<td>$x</td>";
                            }
                        }

                        //POBLACION
                        $id_b = array(34,37,33,39,35,43,36,42,21,41,17,38,18);
                        foreach($id_b as $id){
                            $v = (in_array($id,$proy->id_beneficiarios)) ? 'x' : '';
                            $html .= "<td>$v</td>";
                        }

                        //CANT. BENEFICIARIOS
                        $html .= "<td>$proy->cant_benf_proy</td>";

                        //COB. NAL
                        $html .= "<td>$proy->cobertura_nal_proy</td>";

                        $html .= "<td>";

                        //DEPTOS
                        foreach ($proy->id_deptos as $i=>$id){
                            $dao = $depto_dao->Get($id);
                            if($i > 0)	$html .= ",";
                            $html .= $dao->nombre;
                        }
                        $html .= "</td>";

                        $html .= "<td>";

                        //MPIOS
                        foreach ($proy->id_muns as $i=>$id){
                            $dao = $mun_dao->Get($id);
                            if($i > 0)	$html .= ",";
                            $html .= $dao->nombre;
                        }

                        $html .= "</td>";

                        $html .= "<td>";

                        //OFICINA DESDE LA QUE SE CUBRE
                        foreach ($proy->id_orgs_cubre as $i=>$id){
                            $n = $org_dao->GetName($id);
                            if($i > 0)	$html .= ",";
                            $html .= $n;
                        }

                        $html .= "</td>";

                        //Staff Nacional
                        $html .= "<td>$proy->staff_nal_proy</td>";

                        //Staff Internacional
                        $html .= "<td>$proy->staff_intal_proy</td>";

                        $html .= "<td>";

                        //ORGS. COOPERACION
                        foreach ($proy->id_orgs_coor as $i=>$id){
                            $n = $org_dao->GetName($id);
                            if($i > 0)	$html .= ",";
                            $html .= $n;
                        }

                        $html .= "</td>";
                        $html .= "</tr>";
                    }

                    $html .= "</table>";

                    if ($show_html == 1)	echo $html;
                    else					echo '&nbsp;'; 

                    $_SESSION["xls"] = $html;
                }

                //		break;
                //}

            }

            /**
             * Reporte conteo de proyectos por cantidad de  presupuesto, poblacion
             * @access public
             * @param int $ubicacion ID de la Ubicacion Geográfica
             * @param int $depto 1 o 0
             * @param string $filtro Presupuesto, Poblacion 
             * @param string $id_filtro 
             * @param string $intervalos String separado por coma, que indican los intervalos que el usuario define
             * @param int $show_html Mostrar el codigo html
                 */
            function reporteProyectoConteoUndaf($ubicacion,$depto,$filtro,$id_filtro,$intervalos,$show_html=1){
                require_once "lib/libs_proyecto.php";
                require_once "lib/common/mapserver.class.php";
                require_once "lib/common/number.class.php";
                require_once "lib/common/postgresdb.class.php";


                //INICIALIZACION DE VARIABLES
                $poblacion_dao = New PoblacionDAO();
                $depto_dao = New DeptoDAO();
                $mun_dao = New MunicipioDAO();
                $moneda_dao = New MonedaDAO();
                $mapserver = New Mapserver();
                $number = New Number();

                $moneda = $moneda_dao->GET($id_filtro);
                $jenks  = ($intervalos != '') ? 0 : 1;
                $intervalos_txt = '';

                if ($jenks == 1){

                    $num_intervalos = 5;  //Default

                    $sql = "SELECT costo_proy FROM proyecto ";

                    //Ubicacion geografica
                    if ($depto == 1){
                        $sql .= "JOIN depto_proy USING(id_proy) WHERE id_depto = $ubicacion";
                    }
                    else if ($depto == 0){
                        $sql .= "JOIN mun_proy USING (id_proy) WHERE id_mun = $ubicacion";
                    }
                    else{
                        $sql .= " WHERE 1=1";
                    }

                    $sql .= " AND id_mon = $id_filtro AND si_proy='undaf'";
                    $sql .= " ORDER BY costo_proy";

                    $rs = $this->conn->OpenRecordset($sql);
                    while ($row = $this->conn->FetchRow($rs)){
                        $co = $row[0];
                        if ($co > 0)	$presupuestos[] = $row[0];
                    }

                    if (count($presupuestos) < $num_intervalos){
                        $kclass = array_keys($presupuestos);
                        $num_intervalos = count($presupuestos);
                    }
                    else{

                        $kclass = $mapserver->jenks($num_intervalos,$presupuestos);
                    }
                    $intervalos[] = 0;
                    for ($k=0;$k<$num_intervalos;$k++){
                        $intervalos[] = $number->round0($presupuestos[$kclass[$k]]);
                    }
                }
                else{
                    $intervalos_txt = $intervalos;
                    $intervalos = explode(",",$intervalos);
                    $intervalos = array_merge(array(0),$intervalos);
                }

                $num_intervalos = count($intervalos);

                if ($show_html == 1){
                    echo "<p><h2 style='display:inline'>Moneda:</h2>&nbsp;<select id='id_moneda' class='select' onchange=\"accionProyecto('bar','reporteOnLineConteoProyectoUndaf','conteo_presupuesto');\">";

                    //Consulta las monedas en las que exista proyectos
                    $sql = "SELECT DISTINCT p.id_mon, m.nom_mon FROM proyecto p JOIN moneda m USING(id_mon) WHERE si_proy='undaf'";
                    $rs = $this->conn->OpenRecordset($sql);
                    while($row = $this->conn->FetchRow($rs)){
                        echo "<option value=$row[0]";
                        if ($row[0] == $id_filtro)	echo " selected ";
                        echo ">$row[1]</option>";
                    }
                    echo "</select>&nbsp; &raquo;&raquo;&nbsp; Nota: se contar&aacute;n los proyecto cuyo presupuesto est&eacute; dado en la moneda seleccionada<br /><br />&nbsp;";

            /*
            echo "<p><img src='images/undaf/consulta/excel.gif' border=0 title='Exportar a Hoja de Cálculo'>&nbsp;
            <a href=\"#\" onclick=\"location.href='export_data.php?case=xls_session&nombre_archivo=proyectos';return false;\"\">Exportar a Hoja de C&aacute;lculo</a>
            </p>";
                 */

            echo "<div style='float:left'>";
                }

                $html = "<table cellpadding='2' cellspacing='1' class='tabla_grafica_conteo'>";
                $html .=	"<tr class='titulo_tabla_conteo'>
                    <td>Rango de presupuesto ($moneda->nombre)</td>
                    <td>N&uacute;mero de proyectos</td>
                    </tr>";

                for ($k=0;$k<($num_intervalos-1);$k++){

                    $inf = $intervalos[$k];

                    if ($inf > 0)	$inf += 1; 

                    $sup = $intervalos[$k+1];

                    $sql = "SELECT count(id_proy) FROM proyecto ";

                    //Ubicacion geografica
                    if ($depto == 1){
                        $sql .= "JOIN depto_proy USING(id_proy) WHERE id_depto = $ubicacion";
                    }
                    else if ($depto == 0){
                        $sql .= "JOIN mun_proy USING (id_proy) WHERE id_mun = $ubicacion";
                    }
                    else{
                        $sql .= " WHERE 1=1";
                    }

                    $sql .= " AND costo_proy BETWEEN $inf AND $sup AND id_mon = $id_filtro AND si_proy='undaf'";

                    $rs = $this->conn->OpenRecordset($sql);
                    $row = $this->conn->FetchRow($rs);
                    $num = $row[0];

                    $inf_txt = number_format($inf,0,".",",");
                    $sup_txt = number_format($sup,0,".",",");
                    $html .= "<tr class='fila_lista'><td>$inf_txt - $sup_txt</td><td>$num</td></tr>";

                }
                $html .= "</table>"; 

                if ($show_html == 1)	echo $html;
                else					echo '&nbsp;'; 

                $_SESSION["xls"] = $html;

                echo "</div>";

                //Div para intervalos del usuario
                echo "<div class='consulta_proy_filtro' style='float:left;margin-left:20px;;width:450px;'><h2 style='padding:0;margin:0'>Defina aqu&iacute; sus propios rangos</h2><br />Puede definir los rangos de presupuesto, para esto
                    escriba los limites separados por coma en el siguiente campo:<br /><input type='text' id='intervalos' size='30' class='textfield' value='$intervalos_txt'><br /><br />
                    Ejemplo: 1000,2000,3000, definiria 3 rangos: 0-1000, 1001-2000, 2001-3000
                    </div>";

            }

            /**
             * Reporte PDF de Proyectos por tema, poblacion, las fichas de cada proyecto
             * @access public
             * @param int $ubicacion ID de la Ubicacion Geográfica
             * @param int $depto 1 o 0
             * @param string $filtro Tema, Poblacion
             * @param int $id_filtro Valor de Tema, Poblacion
             * @param string $caso Listado, Reporte On Line, Reporte Pdf
             * @param int $show_html Mostrar el codigo html
                 */
            function reportePDFProyectoUndaf($ubicacion,$depto,$filtro,$id_filtro,$caso,$show_html=1){
                //require_once $_SERVER["DOCUMENT_ROOT"]."sissh/consulta/lib/libs_proyecto.php";

                //INICIALIZACION DE VARIABLES
                $depto_dao = New DeptoDAO();
                $mun_dao = New MunicipioDAO();

                if ($filtro != 'listado_completo'){
                    $nom_ubi = "Nacional";
                    if ($depto == 1){
                        $ubi = $depto_dao->Get($ubicacion);
                        $nom_ubi = $ubi->nombre;
                    }
                    else if ($depto == 0){
                        $ubi = $mun_dao->Get($ubicacion);
                        $nom_ubi = $ubi->nombre;
                    }

                    //CONSULTA LOS ID de PROYECTOS
                    $arr = $this->getIdProyectosReporte($filtro,$id_filtro,$depto,$ubicacion,"si_proy='undaf'");
                }
                else{
                    $arr = $this->GetAllArrayID("si_proy='undaf'",'','');
                }

                $num_arr = count($arr);

                if ($num_arr == 0){
                    echo "<p><b>No se encontraron Proyectos</b></p>";
                }
                else{

                    //Html 2 Pdf
                    include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/common/html2fpdf/html2fpdf.php");

                    //Horizontal
                    $pdf = new HTML2FPDF('P','mm','Letter');
                    //$pdf = new HTML2FPDF('L');
                    //echo "<p><b>Generando el archivo PDF para $num_arr Proyecto(s)</b></p>";

                    //Genera el archivo pdf fisico
                    foreach($arr as $id){
                        $html = $this->fichaPdfCodigoHTML($id);

                        $pdf->AddPage();
                        $pdf->WriteHTML($html);
                    }

                    $pdf->WriteHTML($html);
                    //echo $html;
                    echo $pdf->Output('','S');


                }
            }

            /**
             * Grafica Proyectos por tema, poblacion
             * @access public
             * @param int $ubicacion ID de la Ubicacion Geográfica
             * @param int $depto 1 o 0
             * @param int $filtro Tema, Poblacion
             * @param int $id_filtro Valor de Tema, Poblacion
             * @param string $chart Tipo de grafica
                 */
            function graficaProyectoUndaf($ubicacion,$depto,$filtro,$id_filtro,$chart){
                require_once "lib/common/graphic.class.php";
                require_once "lib/libs_proyecto.php";

                //INICIALIZACION DE VARIABLES
                $tema_dao = New TemaDAO();
                $poblacion_dao = New PoblacionDAO();
                $depto_dao = New DeptoDAO();
                $mun_dao = New MunicipioDAO();

                $nom_ubi = "Nacional";
                if ($depto == 1){
                    $ubi = $depto_dao->Get($ubicacion);
                    $nom_ubi = $ubi->nombre;
                }
                else if ($depto == 0){
                    $ubi = $mun_dao->Get($ubicacion);
                    $nom_ubi = $ubi->nombre;
                }

                $cond = '';
                switch ($filtro) {
                case 'tema':
                    $cond = "id_clasificacion = 1 AND id_papa=0";
                    $arrs = $tema_dao->GetAllArray($cond);
                    $title_gra = "Número de Proyectos por Tema";
                    break;

                case 'poblacion':
                    $arrs = $poblacion_dao->GetAllArray($cond,'','');
                    $title_gra = "Número de Proyectos por Población";
                    break;

                case 'agencia':

                    $sql = "SELECT count(v.id_org) as num, v.id_org, o.sig_org FROM vinculorgpro v JOIN organizacion o USING (id_org) JOIN proyecto USING(ID_PROY) WHERE o.id_tipo = 4 AND id_tipo_vinorgpro = 1 GROUP BY id_org";
                    $rs = $this->conn->OpenRecordset($sql);
                    while ($row = $this->conn->FetchRow($rs)){
                        $arrs[] = array('num' => $row[0], 'id' => $row[1], 'nombre' => $row[2]);
                    }

                    $title_gra = "Número de Proyectos por Agencia";
                    break;

                case 'cobertura':
                    $title_gra = ($depto == 1) ? "Municipios" : "Departamentos";
                    $title_gra .= " con mayor cobertura";

                    $num_reg = 10;

                    //Muestra los mpios
                    if ($depto == 1){
                        $col_id = 'id_mun';
                        $table = 'mun_proy';
                        $dao_ubi = $mun_dao;
                        $extra_join = 'JOIN municipio USING(id_mun)';
                        $cond = 'WHERE id_depto = '.$ubicacion;
                    }
                    else{
                        $col_id = 'id_depto';
                        $table = 'depto_proy';
                        $dao_ubi = $depto_dao;
                        $extra_join = '';
                        $cond = '';
                    }

                    $sql = "SELECT count(d.$this->columna_id) as num, $col_id FROM proyecto p JOIN $table d USING($this->columna_id) $extra_join $cond";
                    $sql .= "GROUP BY $col_id ORDER BY num DESC LIMIT 0,$num_reg";

                    //echo $sql;
                    $rs = $this->conn->OpenRecordset($sql);
                    while ($row = $this->conn->FetchRow($rs)){

                        $id = $row[1];

                        $vo = $dao_ubi->Get($id);

                        $arrs[] = array('nombre' => $vo->nombre, 'num' => $row[0]);
                    }

                    break;
                }


                echo "<table cellpadding=2 width='100%' border='0'><tr><td valign='top'><table>";
                echo "<tr><td>Localizaci&oacute;n Geogr&aacute;fica: <b>$nom_ubi</b></td></tr>";
                echo "<tr>
                    <td valign='top'>
                    <table border=0 class='tabla_grafica_conteo' cellpadding=4 cellspacing=1 width='200'>
                    <tr class='titulo_tabla_conteo'><td align='center'>".ucfirst($filtro)."</td><td align='center' colspan='2'>Cant.</td></tr>";

                $d = 0;
                $f = 0;
                $total = 0;
                foreach($arrs as $f=>$arr){

                    if ($filtro == 'cobertura'){
                        $num = $arr['num'];
                        $nombre = $arr['nombre'];
                    }
                    else{
                        if ($filtro == 'agencia'){
                            $id = $arr['id'];
                            $num = $arr['num'];
                        }
                        else{
                            $id = $arr->id;
                            $num = $this->numProyectos($filtro,$arr->id,$depto,$ubicacion,'undaf');
                        }

                        if ($filtro == 'poblacion'){
                            $nombre = $arr->nombre_es;
                        }
                        else if ($filtro == 'agencia'){
                            $nombre = $arr['nombre'];
                        }
                        else{
                            $nombre = $arr->nombre;
                        }
                    }

                    $g->x[$f] = $nombre;
                    $g->y[$f] = $num;

                    $total += $num;

                    echo "<tr class='fila_tabla_conteo'><td>$nombre</td>";
                    echo "<td align='right'>".number_format($num)."</td>";

                    echo "</tr>";

                }

                echo "<tr><td><b>Total Proyectos</b></td><td align='right'><b>".number_format($total)."</b></td></tr>";
                echo "<tr><td colspan=2>Nota: Un proyecto puede pertenecer a mas de un(a) $filtro, por tanto el total dado en este conteo no es el n&uacute;mero real de proyectos</td></tr>";

                echo "</table></td></tr></table></td>";

        /********************************************************************************
        //PARA GRAFICA OPEN CHART
                /*******************************************************************************/
            $chk_chart = array('bar' => '', 'bar_3d' => '');
                $chk_chart[$chart] = ' selected ';

                echo "<td align='center' valign='top'><table>";

                echo "<tr><td align='left'>";

                //Si no viene de API lo muestra
                if (!isset($_GET["api"])){

                    echo "Tipo de Gr&aacute;fica:&nbsp;
                    <select onchange=\"accionProyecto(this.value,'graficaProyectoUndaf','$filtro')\" class='select'>
                        <option value='bar' ".$chk_chart['bar'].">Barras</option>
                        <option value='bar_3d' ".$chk_chart['bar_3d'].">Barras 3D</option>
                        </select>&nbsp;&nbsp;::&nbsp;&nbsp;";
                }

                echo "Si desea guardar la imagen haga click sobre el icono <img src='images/save.png'>
                    </td>
                    </tr>
                    <tr><td class='tabla_grafica_conteo' colspan=1 width='600' bgcolor='#F0F0F0' align='center'><br>";

                //Eje x
                $i = 0;
                foreach ($g->x as $x){
                    if ($i == 0)	$ejex = "'".utf8_encode($x)."'";
                    else			$ejex .= ",'".utf8_encode($x)."'";

                    $i++;
                }

                //Eje y
                $ejey = implode(",",$g->y);
                $max_y = max($g->y);

                //Variable de sesion que va a ser el nomnre dela grafica al guardar
                $_SESSION["titulo_grafica"] = $title_gra;

                //Estilos para bar y bar3D
                $chart_style = array('bar' => array('alpha' => 90, 'bar_color' => '#0066ff'),
                    'bar_3d' => array('alpha' => 90,'bar_color' => '#0066ff'));

                $path = 'admin/lib/common/open-flash-chart/';
                $path_in = 'lib/common/open-flash-chart/';

                include("$path_in/php-ofc-library/sidihChart.php");
                $g = New sidihChart();
                $max_y = $g->maxY($max_y);

                $content = "<?
                \$path = '".$path."';
                include_once(\$path.'php-ofc-library/sidihChart.php' );

                \$bar = new $chart(".$chart_style[$chart]['alpha'].", '".$chart_style[$chart]['bar_color']."' );

                \$g = new sidihChart();
                \$g->title( '".utf8_encode($title_gra)."' );
                \$g->set_x_labels( array(".$ejex.") );
                \$g->set_x_label_style( 8, '#000000', 2 );";


                if ($chart == 'bar_3d'){
                    $content .= "\$g->set_x_axis_3d(6);";
                    $content .= "\$g->x_axis_colour('#dddddd','#FFFFFF');";
                }

                $content .= "\$bar->data = array(".$ejey.");
                \$g->data_sets[] = \$bar;
                \$g->set_tool_tip( '#x_label# <br> Proyectos: #val#' );		
                // set the Y max
                \$g->set_y_max( ".$max_y." );
                // label every 20 (0,20,40,60)
                \$g->y_label_steps( 5 );

                // display the data
                echo \$g->render();
?>";

//MODIFICA EL ARCHIVO DE DATOS
$archivo = New Archivo();
$fp = $archivo->Abrir('../chart-data.php','w+');

$archivo->Escribir($fp,$content);
$archivo->Cerrar($fp);

//IE Fix
//Variable para que IE cargue el nuevo archivo de datos y cambie el tipo de grafica con los nuevos valores
$nocache = time();
include_once $path_in.'php-ofc-library/open_flash_chart_object.php';
open_flash_chart_object( 500, 350, 'chart-data.php?nocache='.$nocache,false );

echo "</td></tr>";

//Si no viene de API lo muestra
        /*
        if (!isset($_GET["api"])){

            echo "<tr><td><img src='images/spacer.gif' height='30'></td></tr>
            <tr>
                <td align='center' colspan=1>
                    <input type='button' name='button' value='Generar Listado' onclick=\"document.getElementById('listadoConteoOrgMsg').innerHTML='Generando el listado en la parte inferior....';generarListadoOrgs();\" class='boton'>
                    <br><br><span id='listadoConteoOrgMsg'></span>
                </td>
            </tr>";
        }
                 */
            }
            }	
?>
