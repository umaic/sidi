<?
/**
 * DAO de SubEstado
 *
 * Contiene los métodos de la clase SubEstado 
 * @author Ruben A. Rojas C.
 */

Class SubEstadoDAO {

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
	function SubEstadoDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "sub_estado";
		$this->columna_id = "ID_SUBESTADO";
		$this->columna_nombre = "NOM_SUBESTADO";
		$this->columna_order = "NOM_SUBESTADO";
		$this->num_reg_pag = 20;
		$this->url = "index.php?accion=listar&class=SubEstadoDAO&method=ListarTabla&param=";
	}

	/**
  * Consulta los datos de una SubEstado
  * @access public
  * @param int $id ID del SubEstado
  * @return VO
  */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$subestado_vo = New SubEstado();

		//Carga el VO
		$subestado_vo = $this->GetFromResult($subestado_vo,$row_rs);

		//Retorna el VO
		return $subestado_vo;
	}

	/**
  * Consulta los datos de los SubEstado que cumplen una condición
  * @access public
  * @param string $estado Condición que deben cumplir los SubEstado y que se agrega en el SQL statement.
  * @return array Arreglo de VOs
  */	
	function GetAllArray($estado){
		$c = 0;
		$sql = "SELECT * FROM ".$this->tabla."";
		if ($estado != ""){
			$sql .= " WHERE ".$estado;
		}
		$sql .= " ORDER BY ".$this->columna_order;

		$array = Array();

		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchObject($rs)){
			//Crea un VO
			$vo = New SubEstado();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[$c] = $vo;
			$c++;
		}
		//Retorna el Arreglo de VO
		return $array;
	}
	
	/**
  * Consulta los ID de las Subcategorias que cumplen una condición
  * @access public
  * @param string $estado Condición
  * @return array Arreglo de IDs
  */	
	function GetAllArrayID($estado){
		$c = 0;
		$sql = "SELECT $this->columna_id FROM ".$this->tabla."";
		if ($estado != ""){
			$sql .= " WHERE ".$estado;
		}
		$sql .= " ORDER BY ".$this->columna_order;

		$array = Array();

		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchRow($rs)){
			//Carga el arreglo
			$array[$c] = $row_rs[0];
			$c++;
		}
		//Retorna el Arreglo de VO
		return $array;
	}	

	/**
  * Lista los SubEstado que cumplen la condición en el formato dado
  * @access public
  * @param string $formato Formato en el que se listarán los SubEstado, puede ser Tabla o ComboSelect
	* @param int $valor_combo ID del SubEstado que será selccionado cuando el formato es ComboSelect
  * @param string $estado Condición que deben cumplir los SubEstado y que se agrega en el SQL statement.
  */			
	function ListarCombo($formato,$valor_combo,$estado){
		$arr = $this->GetAllArray($estado);
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
  * Lista los SubEstado en una Tabla
  * @access public
  */			
	function ListarTabla($estado){
		
		//INICIALIZA VARIABLES
		$estado_dao = New EstadoMinaDAO();
		
		$id_estado = 0;
		if (isset($_GET["id_estado"]) && $_GET["id_estado"] != ''){
			$id_estado = $_GET["id_estado"];
		}		
		
		$condicion = ($id_estado > 0) ? "ID_ESTADO = $id_estado": "";
		$arr = $this->GetAllArray($condicion);
		$num_arr = count($arr);

		echo "<table align='center' cellspacing='1' cellpadding='3'>
	    <tr><td>&nbsp;</td></tr>
	    <tr class='titulo_lista'><td align='center' colspan=5><b>SUB ESTADO</b></td></tr>
		<tr>
	    <td colspan='3'>
	    Filtrar por Estado&nbsp;<select nane='id_tipo' class='select' onchange=\"location.href='index.php?accion=listar&class=".$_GET["class"]."&method=ListarTabla&param=ID_CAT_TIPO_EVE='+this.value+'&id_estado='+this.value\">
			<option value=''>Todas</option>";
		$estado_dao->ListarCombo('combo',$id_estado,'');
		echo "</select></td></tr>	    
		<tr class='titulo_lista'>
		<td width='50' align='center'>ID</td>
		<td width='250'>Nombre</td>
		<td width='200'>Estado</td>
	    <td align='center'>Registros: ".$num_arr."</td>
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

			//ESTADO
			$vo = $estado_dao->Get($arr[$p]->id_estado);
			
			echo "<tr class='".$style."'>";
			echo "<td align='center'>".$arr[$p]->id."</td>";
			echo "<td><a href='".$_SERVER['PHP_SELF']."?accion=actualizar&id=".$arr[$p]->id."'>".$arr[$p]->nombre."</a></td>";
			echo "<td align='left'>".$vo->nombre."</td>";
			echo "<td align='center'><a href='index.php?accion=borrar&class=".$_GET["class"]."&method=Borrar&param=".$arr[$p]->id."' onclick=\"return confirm('Está seguro que desea borrar: ".$arr[$p]->nombre."');\">Borrar</a></td>";
			echo "</tr>";
		}

		echo "<tr><td>&nbsp;</td></tr>";
		//PAGINACION
		if ($num_arr > $this->num_reg_pag){

			$num_pages = ceil($num_arr/$this->num_reg_pag);
			echo "<tr><td colspan='4' align='center'>";

			echo "Ir a la página:&nbsp;<select onchange=\"location.href='index.php?accion=listar&class=".$_GET["class"]."&method=".$_GET["method"]."&param=".$_GET["param"]."&page='+this.value\" class='select'>";
			for ($pa=1;$pa<=$num_pages;$pa++){
				echo " <option value='".$pa."'";
				if ($pa == $pag_url)	echo " selected ";
				echo ">".$pa."</option> ";
			}
			echo "</select>";
			echo "</td></tr>";
		}
		echo "</table>";
	}

	/**
  * Carga un VO de SubEstado con los datos de la consulta
  * @access public
  * @param object $vo VO de SubEstado que se va a recibir los datos
  * @param object $Resultset Resource de la consulta
	* @return object $vo VO de SubEstado con los datos
  */			
	function GetFromResult ($vo,$Result){
		
		

		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};
		$vo->id_estado = $Result->ID_ESTADO;

		return $vo;
	}

	/**
  * Inserta un SubEstado en la B.D.
  * @access public
  * @param object $subestado_vo VO de SubEstado que se va a insertar
  */		
	function Insertar($subestado_vo){
		//CONSULTA SI YA EXISTE
		$vo_t = $this->GetAllArray($this->columna_nombre." = '".$subestado_vo->nombre."'");
		if (count($vo_t) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.",ID_ESTADO) VALUES ('".$subestado_vo->nombre."',$subestado_vo->id_estado)";
			$this->conn->Execute($sql);
			
    	?>
    	<script>
    	alert("Registro insertado con &eacute;xito!");
    	location.href = '<?=$this->url;?>';
    	</script>
    	<?
		}
		else{
    	?>
    	<script>
    	alert("Error - Existe un Sub Estado con el mismo nombre");
    	</script>
    	<?
		}
	}

	/**
  * Actualiza un SubEstado en la B.D.
  * @access public
  * @param object $subestado_vo VO de SubEstado que se va a actualizar
  */		
	function Actualizar($subestado_vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_nombre." = '".$subestado_vo->nombre."',";
		$sql .= " ID_ESTADO = ".$subestado_vo->id_estado;
		$sql .= " WHERE ".$this->columna_id." = ".$subestado_vo->id;

		$this->conn->Execute($sql);

		?>
  	<script>
  	alert("Registro actualizado con &eacute;xito!");
  	location.href = '<?=$this->url;?>';
  	</script>
  	<?
	}

	/**
  * Borra un SubEstado en la B.D.
  * @access public
  * @param int $id ID del SubEstado que se va a borrar de la B.D
  */	
	function Borrar($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

    ?>
    <script>
    alert("Registro eliminado con &eacute;xito!");
    location.href = '<?=$this->url;?>';
    </script>
    <?
	}
	
	/**
	* Retorna el numero de Registros
	* @access public
	* @return int
	*/
	function numRecords($estado){
		$sql = "SELECT count(".$this->columna_id.") as num FROM ".$this->tabla;
		if ($estado != ""){
			$sql .= " WHERE ".$estado;
		}
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchRow($rs);

		return $row_rs[0];
	}	
}

/**
* Ajax de Subcategorias
*
* Contiene los metodos para Ajax de la clase SubEstado
* @author Ruben A. Rojas C.
*/

Class SubEstadoAjax extends SubEstadoDAO {

	/**
	* Lista ComboBox de subestados
	* @access public
	* @param string $id_estado ID de la Estado
	* @param int $multiple 0 = comboBox no es multiple, > 0 comboBox es multiple de size = $multiple
	* @param int $titulo 1 = Mostrar titulo
	*/
	function comboBoxSubestado($id_estado,$multiple=0,$titulo=0,$separador=0){

		//LIBRERIAS
		include_once("lib/model/subestado.class.php");
		include_once("lib/model/estado_mina.class.php");
		include_once("lib/dao/estado_mina.class.php");
		
		//INICIALIZACION VARIABLES
		$estado_dao = New EstadoMinaDAO();

		$num = $this->numRecords("id_estado IN ($id_estado)");

		if ($num > 0){
			
			$estados = $estado_dao->GetAllArray("ID_ESTADO_MINA IN ($id_estado)");
			
			
			if ($titulo == 1)	echo "<b>Subestados</b><br>";
			
			if ($multiple == 0){
				echo "<select id='id_subestado' name='id_subestado[]' class='select'>";
				echo "<option value=''>[ Seleccione ]</option>";
			}
			else{
				//if ($num < $multiple)	$multiple = $num;
				echo "<select id='id_subestado' name='id_subestado[]' class='select' multiple size=$multiple>";
			}

			
			foreach ($estados as $estado) {
				$vos = $this->GetAllArray("ID_ESTADO = $estado->id");
				
				if ($separador == 1)	echo "<option value='' disabled>--- ".$estado->nombre." ---</option>";
				foreach ($vos as $vo){
					echo "<option value='".$vo->id."'>".$vo->nombre."</option>";
				}
			}

			echo "</select>";
		}
		else{
			echo "<b>* No hay Info *</b>";
		}
	}		
}

?>