<?
/**
 * DAO de Sugerencia
 *
 * Contiene los métodos de la clase Sugerencia 
 * @author Ruben A. Rojas C.
 */

Class SugerenciaDAO {

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
	function SugerenciaDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "sugerencia";
		$this->columna_id = "ID_SUGERENCIA";
		$this->columna_nombre = "";
		$this->columna_order = "FECHA";
	}

	/**
	 * Consulta los datos de una Sugerencia
	 * @access public
	 * @param int $id ID del Sugerencia
	 * @return VO
	 */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$sugerencia_vo = New Sugerencia();

		//Carga el VO
		$sugerencia_vo = $this->GetFromResult($sugerencia_vo,$row_rs);

		//Retorna el VO
		return $sugerencia_vo;
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
			$vo = New Sugerencia();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}  
		//Retorna el Arreglo de VO
		return $array;
	}

	/**
	 * Lista los Sugerencia que cumplen la condición en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarán los Sugerencia, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del Sugerencia que será selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condición que deben cumplir los Sugerencia y que se agrega en el SQL statement.
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
	 * Lista los Sugerencia en una Tabla
	 * @access public
	 */			
	function ListarTabla($condicion){

		include_once ("lib/common/layout.class.php");

		$layout = new Layout();

		$layout->adminGrid(array('texto' => array('titulo' => 'Texto', 'width' => 300), 'fecha' => array('titulo' => 'Fecha')),
						   array('id_usuario' => array('tabla_columna' => 'id_usuario','dao' => 'UsuarioDAO', 'nom' => 'nombre', 'titulo' => 'Usuario', 'filtro' => false)),
						   array('checkForeignKeys' => false, 'link_edit' => false, 'link_new' => false)
						   );

	}

	/**
	 * Carga un VO de Sugerencia con los datos de la consulta
	 * @access public
	 * @param object $vo VO de Sugerencia que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de Sugerencia con los datos
	 */			
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->{$this->columna_id};
		$vo->id_usuario = $Result->ID_USUARIO;
		$vo->modulo = $Result->MODULO;
		$vo->texto = $Result->TEXTO;
		$vo->fecha = $Result->FECHA;

		return $vo;
	}


	/**
	 * Inserta un Sugerencia en la B.D.
	 * @access public
	 * @param object $vo VO de Sugerencia que se va a insertar
	 * @param int $vo VO de Sugerencia que se va a insertar
	 */		
	function Insertar($vo,$undaf=0){

		//INICIALIZACION DE VARIABLES
		$usuario_dao = New UsuarioDAO();

		$sql = "INSERT INTO $this->tabla (id_usuario,modulo,texto,fecha,undaf) VALUES ($vo->id_usuario,'$vo->modulo','".utf8_decode($vo->texto)."',now(),$undaf)";
		$this->conn->Execute($sql);

		//DATOS USUARIO QUE ENVIA LA SUGERENCIA
		$usuario = $usuario_dao->Get($vo->id_usuario);

		//ENVIA EMAIL
		$from = "rojas@un-ocha.org";

		require($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/common/class.phpmailer.php");

		$mail = new PHPMailer();

		$mail->IsSMTP(); // set mailer to use SMTP

		$mail->From = $from;
		$mail->FromName = "SIDIH OCHA";
		$mail->AddAddress("rojasr@un.org", "Vladimir Barrero");
		$mail->AddBCC("rubenrojasc@gmail.com", "Ruben Rojas");

		$mail->WordWrap = 50;                                 // set word wrap to 50 characters
		$mail->IsHTML(true);                                  // set email format to HTML

		if ($undaf == 0){
			$mail->Subject = "Nueva sugerencia registrada SIDIH OCHA";
			$mail->Body    = "El usuario $usuario->nombre (ID = $vo->id_usuario) ha registrado la siguiente sugerencia para el m&oacute;dulo $vo->modulo: <br /><br />$vo->texto";
		}
		else{
			$mail->Subject = "Nueva sugerencia registrada SIDIH UNDAF";
			$mail->Body    = "El usuario $usuario->nombre (ID = $vo->id_usuario) ha registrado la siguiente sugerencia: <br /><br />$vo->texto";
		}

		$mail->Send();

		echo "Su sugerencia ha sido enviada con &eacute;xito.&nbsp;<a href='#' onclick=\"showSugerencias('ocultar',event);return false;\">Cerrar</a>";

	}

	/**
	 * Borra un registro en la B.D.
	 * @access public
	 * @param int $id ID del Sexo que se va a borrar de la B.D
	 */	
	function Borrar($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

	}
	
}

?>
