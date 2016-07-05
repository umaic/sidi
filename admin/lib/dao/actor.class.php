<?
/**
 * DAO de Actor
 *
 * Contiene los métodos de la clase Actor 
 * @author Ruben A. Rojas C.
 */

Class ActorDAO {

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
	function ActorDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "actor";
		$this->columna_id = "ID_ACTOR";
		$this->columna_nombre = "NOM_ACTOR";
		$this->columna_order = "COD_INTERNO";
		$this->activar = 0;
	}

	/**
	* Consulta los datos de una Actor
	* @access public
	* @param int $id ID del Actor
	* @return VO
	*/	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$actor_vo = New Actor();

		//Carga el VO
		$actor_vo = $this->GetFromResult($actor_vo,$row_rs);

		//Retorna el VO
		return $actor_vo;
	}

	
	/**
	* Consulta los datos de una Actor
	* @access public
	* @param int $level Nivel en el arbol geniagolico
	* @return VO
	*/	
	function getNextLevelId($level){
		$sql = "SELECT max(cod_interno) FROM ".$this->tabla." WHERE level = $level";
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchRow($rs);
		
		return $row_rs[0];
	}
	
	/**
	* Consulta el codigo interno de un actor
	* @access public
	* @param int $id_actor
	* @return int
	*/	
	function getCodigoInterno($id_actor){
		$sql = "SELECT cod_interno FROM ".$this->tabla." WHERE ".$this->columna_id." = $id_actor";
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->Fetchrow($rs);
		
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
			$vo = New Actor();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}  
		//Retorna el Arreglo de VO
		return $array;
	}

	
	/**
  * Lista los Actor que cumplen la condición en el formato dado
  * @access public
  * @param string $formato Formato en el que se listarán los Actor, puede ser Tabla o ComboSelect
	* @param int $valor_combo ID del Actor que será selccionado cuando el formato es ComboSelect
  * @param string $condicion Condición que deben cumplir los Actor y que se agrega en el SQL statement.
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
  * Lista los registros en una Tabla
  * @access public
  */			
	function ListarTabla($condicion){
		
		$id_papa = (isset($_GET["id_papa"]) && $_GET["id_papa"] != '') ? $_GET["id_papa"] : 0;
		
		$condicion = "id_papa = $id_papa";
		
		$arr = $this->GetAllArray($condicion);

		$condicion_num = ($id_papa > 0) ? $condicion : '';
		$num_arr = $this->numRecords($condicion_num);

		echo "<table align='center' class='tabla_lista' width='800'>";
		
		echo "<tr><td colspan=3>";
		
		echo "Filtrar por Actor :&nbsp;";
		
		echo "<select class='select' onchange=\"refreshTab('index_parser.php?accion=listar&class=ActorDAO&method=ListarTabla&param=&id_papa='+this.value)\"><option value=''>[ Seleccione ]</option>";

		// Abuelos
		$abuelos = $this->GetAllArray('id_papa = 0 AND nivel=0');
		foreach ($abuelos as $abue){
			echo "<option value=$abue->id";
			if ($id_papa > 0 AND $id_papa == $abue->id)	echo " selected ";
			echo ">$abue->nombre</option>";

			$hijos = $this->GetAllArray("id_papa = $abue->id");
			
			foreach ($hijos as $hijo){
				echo "<option value=$hijo->id";
				if ($id_papa > 0 AND $id_papa == $hijo->id)	echo " selected ";
				echo ">&nbsp;&nbsp;&nbsp;$hijo->nombre</option>";

			}
		}
		
		echo "</select></td></tr>
			<tr>
				<td width='70'><img src='images/home/insertar.png'>&nbsp;<a href='#' onclick=\"addWindowIU('actor','insertar','');return false;\">Crear</a></td>
				<td align='right' colspan='2'>[$num_arr Registros]</td>
			</tr>";
		
		echo "<tr class='titulo_lista'><td width='50' align='center'>COD. INTERNO</td><td>Nombre</td></tr>";
        
        $tab = '';
		foreach ($arr as $p=>$vo){
			
			echo "<tr class='fila_lista'>";
			echo "<td>";
			if (!$this->checkForeignKeys($vo->id))	echo "<a href='#'  onclick=\"if(confirm('Está seguro que desea borrar el actor: ".$vo->nombre."')){borrarRegistro('ActorDAO','".$vo->id."')}else{return false};\"><img src='images/trash.png' border='0' title='Borrar' /></a>&nbsp;";
			echo $vo->cod_interno."</td>";
			echo "<td>$tab<a href='#' onclick=\"addWindowIU('actor','actualizar',".$vo->id.");return false;\">".$vo->nombre."</a></td>";
			
			echo "</tr>";

            if ($id_papa > 0) {
                $this->ListarTablaHijos($arr[$p]->id,"");
            }

            $tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		}

		echo "</table>";
	}

	/**
	 * Lista los Hijos en una Tabla
	 * @access public
	 */			
	function ListarTablaHijos($id_papa,$tab){

		$tab .= "&nbsp;&nbsp;&nbsp;&nbsp;";
		$arr = $this->GetAllArray("id_papa = $id_papa");

		foreach ($arr as $p=>$vo){
			echo "<tr class='fila_lista'>";
			echo "<td>";
			if (!$this->checkForeignKeys($vo->id))	echo "<a href='#'  onclick=\"if(confirm('Está seguro que desea borrar el actor: ".$vo->nombre."')){borrarRegistro('EspacioDAO','".$vo->id."')}else{return false};\"><img src='images/trash.png' border='0' title='Borrar' /></a>&nbsp;";
			echo $vo->cod_interno."</td>";
			echo "<td>$tab<a href='#' onclick=\"addWindowIU('actor','actualizar',".$vo->id.");return false;\">".$vo->nombre."</a></td>";
			
			echo "</tr>";

			$this->ListarTablaHijos($vo->id,$tab);
		}
	}		

	
	/**
	* Reportar
	* @access public
	*/			
	function Reportar(){

		$arr = $this->GetAllArray("");

		echo "<table align='center' width='500' cellspacing='1' cellpadding='3'>
	    <tr><td>&nbsp;</td></tr>
	    <tr class='titulo_lista'><td align='center' colspan=4><b>ACTORES EN EL SISTEMA</b></td></tr>
	    <tr><td>&nbsp;</td></tr>";

		$v = 0;
		foreach($arr as $vo){

			$style = "";
			if (fmod($v+1,2) == 0)  $style = "fila_lista";
			echo "<tr class='".$style."'>";
			echo "<td>".$vo->nombre."</td>";
			echo "</tr>";
			$v++;
		}

		echo "</table>";
	}

	/**
  * Imprime en pantalla los datos del Actor
  * @access public
  * @param object $vo Actor que se va a imprimir
  * @param string $formato Formato en el que se listarán los Actor, puede ser Tabla o ComboSelect
	* @param int $valor_combo ID del Actor que será selccionado cuando el formato es ComboSelect
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
  * Carga un VO de Actor con los datos de la consulta
  * @access public
  * @param object $vo VO de Actor que se va a recibir los datos
  * @param object $Resultset Resource de la consulta
	* @return object $vo VO de Actor con los datos
  */			
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};
		$vo->cod_interno = $Result->COD_INTERNO;
		$vo->id_papa = $Result->ID_PAPA;
		$vo->nivel = $Result->NIVEL;
		$vo->activo = $Result->ACTIVO;

		return $vo;
	}

	/**
  * Inserta un Actor en la B.D.
  * @access public
  * @param object $actor_vo VO de Actor que se va a insertar
  */		
	function Insertar($actor_vo){
		
		//CONSULTA SI YA EXISTE EL COD INTERNO
		$cat_a = $this->GetAllArray("cod_interno = '".$actor_vo->cod_interno."'");

		if (count($cat_a) > 0){
			echo "Error - El ID ya existe";
		}
		else{
			//CONSULTA SI YA EXISTE POR NOMBRE
			$cat_a = $this->GetAllArray($this->columna_nombre." = '".$actor_vo->nombre."' AND id_papa = ".$actor_vo->id_papa);
			if (count($cat_a) == 0){
				
				$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.",id_papa,nivel,cod_interno) VALUES ('".$actor_vo->nombre."',$actor_vo->id_papa,$actor_vo->nivel,$actor_vo->cod_interno)";
				$this->conn->Execute($sql);

				echo "Registro insertado con &eacute;xito!";
			}
			else{
				echo "Error - Existe un registro con el mismo nombre";
			}
		}
	}

	/**
  * Actualiza un Actor en la B.D.
  * @access public
  * @param object $actor_vo VO de Actor que se va a actualizar
  */		
	function Actualizar($actor_vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_nombre." = '".$actor_vo->nombre."',";
		$sql .= "cod_interno = $actor_vo->cod_interno,";
		$sql .= "id_papa = $actor_vo->id_papa,";
		$sql .= "nivel = $actor_vo->nivel";
		$sql .= " WHERE ".$this->columna_id." = ".$actor_vo->id;

		$this->conn->Execute($sql);

	}

	/**
  * Borra un Actor en la B.D.
  * @access public
  * @param int $id ID del Actor que se va a borrar de la B.D
  */	
	function Borrar($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
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
	
	/**
	* Desactiva un actor
	* @access public
	* @param int id
	*/
	function Desactivar($id){
		$sql = "UPDATE ".$this->tabla." SET activo = 0 WHERE ".$this->columna_id."= $id";
		$this->conn->Execute($sql);
	}	


	/**
	 * Check de llaves foraneas, para permitir acciones como borrar
	 * @access public
	 * @param int $id ID del registro a consultar
	 */	
	function checkForeignKeys($id){

		$tabla_rel = 'actor_descevento';

		$sql = "SELECT sum($this->columna_id) FROM $tabla_rel JOIN actor USING($this->columna_id) WHERE ".$this->columna_id." = $id OR $this->columna_id IN (SELECT $this->columna_id FROM actor WHERE id_papa = $id)";
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$r = ($row[0] > 0) ? true : false;

		return $r;

	}
}	
	

/**
* Ajax de Actor
*
* Contiene los metodos para Ajax de la clase Actor
* @author Ruben A. Rojas C.
*/

Class ActorAjax extends ActorDAO {

	/**
	* Lista ComboBox de actores
	* @access public
	* @param int $id_papa ID del papa
	* @param string $name_field Nombre para el combo
	* @param int $onchange Si es 1, se agrega el evento onchange al combo
	* @param int $value Opcion selecciona
	* @param int $numero_fila Numero de la fila, en insertar evento cuando se le da + Registro
	* @param int $multiple 0 = comboBox no es multiple, > 0 comboBox es multiple de size = $multiple
	* @param int $titulo 1 = Mostrar titulo antes del combo
 	* @param int $separador 1 = Coloca el titulo de los actores en las opciones
	*/
	function comboBoxActor($id_papa,$name_field,$onchange,$value,$numero_fila,$multiple=0,$titulo=0,$separador=0){

		//LIBRERIAS
		include_once("lib/model/actor.class.php");
		include_once("lib/dao/actor.class.php");

		$num = $this->numRecords("ID_PAPA IN ($id_papa)");

		if ($num > 0){
			
			if ($titulo == 1)	echo "<b>Actor</b><br>";
			
			if ($multiple == 0){
				echo "<select id='$name_field.$numero_fila' name='".$name_field."[]' class='select'";
				if ($onchange == 1){
					echo "onchange=\"enviarActor();\"";
				}
				echo ">";
				echo "<option value=''>[ Seleccione ]</option>";
			}
			else{
				//if ($num < $multiple)	$multiple = $num;
				echo "<select id='".$name_field."".$numero_fila."' name='".$name_field."[]' class='select' multiple size=$multiple>";
			}

			$vos = $this->GetAllArray("ID_ACTOR IN ($id_papa)");
			
			foreach ($vos as $vo){
				
				$vos_h = $this->GetAllArray("ID_PAPA = $vo->id");
				if ($separador == 1)	echo "<option value='' disabled>-------- ".$vo->nombre." --------</option>";
				foreach ($vos_h as $vo_h){
					echo "<option value='".$vo_h->id."'";
					if ($vo_h->id == $value)	echo " selected ";
					echo ">".$vo_h->nombre."</option>";
				}
			}

			echo "</select>";

			//OPCION DE BUSQUEDA
			if ($name_field == 'id_abuelo'){
				echo '&nbsp;<img src="images/evento_c/listar.png">&nbsp;<a href="#" onclick="enviarActor();return false;">Listar Sub Actor</a>';
			}
            else if ($name_field == 'id_papa'){
				echo '&nbsp;<img src="images/evento_c/listar.png">&nbsp;<a href="#" onclick="enviarSubActor();return false;">Listar Sub-Sub Actor</a>';
			}
			else{
				echo "&nbsp;&nbsp;<a href=\"#\" onclick=\"showDivOcurrencia('hijo',event);return false;\"><img src=\"images/icono_search.png\" border=\"0\">&nbsp;Buscar</a>";
			}
			
		}
		else{
			echo "<b>* No hay Info *</b>";
		}
	}

	/**
	* Lista ComboBox de actores para formulario insert
	* @access public
	* @param int $id_papa ID del papa
	* @param string $name_field Nombre para el combo
	* @param int $onchange Si es 1, se agrega el evento onchange al combo
	* @param int $value Opcion selecciona
	* @param int $multiple 0 = comboBox no es multiple, > 0 comboBox es multiple de size = $multiple
	* @param int $titulo 1 = Mostrar titulo antes del combo
 	* @param int $separador 1 = Coloca el titulo de los actores en las opciones
	*/
	function comboBoxActorInsertar($id_papa,$name_field,$onchange,$value,$numero_fila,$multiple=0,$titulo=0,$separador=0){

		//LIBRERIAS
		include_once("lib/model/actor.class.php");
		include_once("lib/dao/actor.class.php");

		$num = $this->numRecords("ID_PAPA IN ($id_papa)");

		if ($num > 0){
			
			if ($titulo == 1)	echo "<b>Actor</b><br>";
			
			if ($multiple == 0){
				echo "<select id='$name_field' name='$name_field' class='select'";
				if ($onchange == 1){
					echo "onchange=\"enviarActor();\"";
				}
				echo ">";
				echo "<option value=''>[ Seleccione ]</option>";
			}
			else{
				//if ($num < $multiple)	$multiple = $num;
				echo "<select id='$name_field' name='".$name_field."[]' class='select' multiple size=$multiple>";
			}

			$vos = $this->GetAllArray("ID_ACTOR IN ($id_papa)");
			
			foreach ($vos as $vo){
				
				$vos_h = $this->GetAllArray("ID_PAPA = $vo->id");
				if ($separador == 1)	echo "<option value='' disabled>-------- ".$vo->nombre." --------</option>";
				foreach ($vos_h as $vo_h){
					echo "<option value='".$vo_h->id."'";
					if ($vo_h->id == $value)	echo " selected ";
					echo ">".$vo_h->nombre."</option>";
				}
			}

			echo "</select>";
			
		}
		else{
			echo "<b>* No hay Info *</b>";
		}
	}

	/**
	* Muestra las ocurrencias por nombre de un actor
	* @access public
	* @param int $s
	* @param int $donde
	* @param string $case
	* @param int $numero_fila Numero de la fila, en insertar evento cuando se le da + Registro
	*/
	function ocurrenciasActor($s,$donde='comience',$case,$numero_fila){
		
		$donde == 'comience' ? $condicion = "NOM_ACTOR like '$s%'" : $condicion = "NOM_ACTOR like '%$s%'";
		
		if ($case == 'hijo'){
			$condicion .= " AND nivel = 3";
		}
		else{
			$condicion .= " AND nivel = 2";
		}
		
		//LIBRERIAS
		include_once("lib/model/actor.class.php");
		include_once("lib/dao/actor.class.php");

		$num = $this->numRecords($condicion);
		
		if ($case == 'hijo')	$title = "Sub-Sub";
		else					$title = "Sub";

		if ($num > 0){
			
			echo "<table><tr><td><b>Hay $num $title Actor (es) ...</b></td></tr>";
//			echo "<tr><td class='titulo_ocurrenciasActor'>Nombre</td><td>&nbsp;</td></tr>";
			
			$vos = $this->GetAllArray($condicion);
			
			foreach ($vos as $vo){
				$papa = $this->get($vo->id_papa);
				

				if ($case == 'hijo'){
					$abuelo = $this->get($papa->id_papa);
					//echo "<tr class='fila_ocurrenciasActor'><td>$vo->nombre</td><td><a href='#' onclick=\"document.getElementById('id_abuelo$numero_fila').value = $abuelo->id;getDataV1('comboBoxActor','ajax_data.php?object=comboBoxActor&onchange=0&name_field=id_papa&multiple=7&id_papa=".$abuelo->id."&value=$vo->id_papa&numero_fila=$numero_fila','comboBoxSubactor$numero_fila');getDataV1('comboBoxActor','ajax_data.php?object=comboBoxActor&onchange=0&name_field=id_hijo&multiple=7&id_papa=".$vo->id_papa."&value=$vo->id&numero_fila=$numero_fila','comboBoxSubSubactor$numero_fila');document.getElementById('buscar_hijo').style.display='none';return false;\">Seleccionar</a></td>";
					echo "<tr class='fila_ocurrenciasActor'><td>$vo->nombre</td><td><a href='#' onclick=\"checkAnOptionCombo('id_abuelo$numero_fila',$abuelo->id);getDataV1('comboBoxActor','ajax_data.php?object=comboBoxActor&onchange=0&name_field=id_papa&multiple=7&id_papa=".$abuelo->id."&value=$vo->id_papa&numero_fila=$numero_fila','comboBoxSubactor$numero_fila');getDataV1('comboBoxActor','ajax_data.php?object=comboBoxActor&onchange=0&name_field=id_hijo&multiple=7&id_papa=".$vo->id_papa."&value=$vo->id&numero_fila=$numero_fila','comboBoxSubSubactor$numero_fila');document.getElementById('buscar_hijo').style.display='none';return false;\">Seleccionar</a></td>";
				}
				else{
					echo "<tr class='fila_ocurrenciasActor'><td>$vo->nombre</td><td><a href='#' onclick=\"document.getElementById('id_abuelo$numero_fila').value = $papa->id;getDataV1('comboBoxActor','ajax_data.php?object=comboBoxActor&onchange=0&name_field=id_papa&multiple=7&id_papa=".$papa->id."&value=$vo->id&numero_fila=$numero_fila','comboBoxSubactor$numero_fila');getDataV1('comboBoxActor','ajax_data.php?object=comboBoxActor&onchange=0&name_field=id_hijo&multiple=7&id_papa=".$vo->id."&numero_fila=$numero_fila','comboBoxSubSubactor$numero_fila');document.getElementById('buscar_papa').style.display='none';return false;\">Seleccionar</a></td>";
				}
			}

		}
		else{
			echo "<b>* No hay Info *</b>";
		}
		
		echo "</td></tr></table>";
		
	}
}

?>
