<?
/**
 * DAO de Usuario
 *
 * Contiene los m�todos de la clase Usuario
 * @author Ruben A. Rojas C.
 */

Class UsuarioDAO {

	/**
	 * Conexi�n a la base de datos
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
	 * Crea la conexi�n a la base de datos
	 * @access public
	 */
	function UsuarioDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "usuario";
		$this->columna_id = "ID_USUARIO";
		$this->columna_nombre = "NOM_USUARIO";
		$this->columna_order = "NOM_USUARIO";
	}

	/**
	 * Consulta los datos de una Usuario
	 * @access public
	 * @param int $id ID del Usuario
	 * @return VO
	 */
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$usuario_vo = New Usuario();

		//Carga el VO
		$usuario_vo = $this->GetFromResult($usuario_vo,$row_rs);

		//Retorna el VO
		return $usuario_vo;
	}


	/**
	 * Consulta los datos de los Tema que cumplen una condici�n
	 * @access public
	 * @param string $condicion Condici�n que deben cumplir los Tema y que se agrega en el SQL statement.
	 * @param string $limit Limit en el SQL
	 * @param string $order by Order by en el SQL
	 * @return array Arreglo de VOs
	 */
	function GetAllArray($condicion,$limit='',$order_by=''){

		$c = 0;
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
			$vo = New Usuario();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}
		//Retorna el Arreglo de VO
		return $array;
	}

	/**
	 * Lista los Usuario que cumplen la condici�n en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listar�n los Usuario, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del Usuario que ser� selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condici�n que deben cumplir los Usuario y que se agrega en el SQL statement.
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
	 * Lista los Usuario en una Tabla
	 * @access public
	 */
	function ListarTabla(){

		//INICIALIZA VARIABLES
		$tipo_dao = New TipoUsuarioDAO();
		$activo_img = array("inactivo.gif","activo.gif");
		$chk_activo = array('activo' => '', 'inactivo' => '');
		$condicion = '';

		$url = "index_parser.php?accion=listar&class=".$_GET["class"]."&method=ListarTabla&param=";
		$url_tipo = '';
		$url_activo = '';
		$url_li = '';

		$id_tipo = 0;
		if (isset($_GET["id_tipo"]) && $_GET["id_tipo"] != ''){
			$id_tipo = $_GET["id_tipo"];
			$url_tipo = "&id_tipo=$id_tipo";
			$condicion = "id_tipo_usuario = $id_tipo";

		}
		$activo = 0;
		if (isset($_GET["activo"]) && $_GET["activo"] != ''){
			$activo = $_GET["activo"];
			($activo == 1)	?	$chk_activo['activo'] = ' selected ' : $chk_activo['inactivo'] = ' selected ';
			$url_activo = "&activo=$activo";

			if ($condicion == '')	$condicion = "activo = $activo";
			else					$condicion .= " AND activo = $activo";

		}

		//Consulta las letras y numeros iniciales de los contactos para generar el indice-paginacion
		$letras_ini = $this->getLetrasIniciales($condicion);
		$si_letra_ini = (count($letras_ini) > 0) ? 1 : 0;
		$letra_inicial = $letras_ini[0];
		if ($si_letra_ini == 1){

			if (isset($_GET["li"]) && $_GET["li"] != ''){
				$letra_inicial = $_GET["li"];
				$url_li = "&li=$letra_inicial";
			}
			if ($condicion == '')	$condicion = " $this->columna_nombre LIKE '$letra_inicial%'";
			else					$condicion .= " AND $this->columna_nombre LIKE '$letra_inicial%'";
		}

		$arr = $this->GetAllArray($condicion);
		$num_arr = count($arr);

		echo "<table align='center' class='tabla_lista'>
			<tr><td colspan='5'>";

		//INDICE
		$this->indiceListaContactos($letras_ini,$letra_inicial,$url.$url_tipo.$url_activo);

		echo "</td></tr>";

		echo "<tr><td>&nbsp;</td></tr>";

		echo "<tr>
			<td colspan='5'>
			Filtrar por Tipo de Usuario&nbsp;<select nane='id_tipo' class='select' onchange=\"refreshTab('".$url.$url_li.$url_activo."&id_tipo='+this.value)\">
			<option value=''>Todos</option>";
		$tipo_dao->ListarCombo('combo',$id_tipo,'');
		echo "</select></td></tr>
			<tr>
			<td colspan='3'>
			Filtrar por Estado&nbsp;<select nane='activo' class='select' onchange=\"refreshTab('".$url.$url_li.$url_tipo."&activo='+this.value)\">
			<option value=''>Todos</option>
			<option value=1 ".$chk_activo['activo'].">Activos</option>
			<option value=0 ".$chk_activo['inactivo'].">Inactivos</option>";
		echo "</select></td></tr>";

		echo "<tr>
			<td width='100'><img src='images/home/insertar.png'>&nbsp;<a href='#' onclick=\"addWindowIU('usuario','insertar','');return false;\">Crear</a></td>
	    	<td colspan='5' align='right'>[$num_arr Registros]</td>
		</tr>
			<tr class='titulo_lista'>
			<td width='50' align='center'>ID</td>
			<td width='200'>Nombre</td>
			<td width='100'>Tipo</td>
			<td width='200'>Email</td>
			<td width='200'>Org</td>
			<td width='14'>Activo</td>
			</tr>";


		foreach ($arr as $p=>$vo){

            //NOMBRE DEL TIPO DE USUARIO
            $nom_tipo = '';
            if ($vo->id_tipo > 0){
			    $tipo_vo = $tipo_dao->Get($vo->id_tipo);
			    $nom_tipo = $tipo_vo->nombre;
            }

			echo "<tr class='fila_lista'>";
			echo "<td><a href='#'  onclick=\"if(confirm('Est� seguro que desea borrar el Usuario: ".$vo->nombre."')){borrarRegistro('UsuarioDAO','".$vo->id."')}else{return false};\"><img src='images/trash.png' border='0' title='Borrar' /></a>&nbsp;".$vo->id."</td>";
			echo "<td><a href='#' onclick=\"addWindowIU('usuario','actualizar',$vo->id)\">".$vo->nombre."</a> </td>";
			echo "<td>".$nom_tipo."</td>";
			echo "<td>".$vo->email."</td>";
			echo "<td>".$vo->org."</td>";
			echo "<td align='center'><img src='images/usuario/".$activo_img[$vo->activo]."'></td>";
			echo "</tr>";
		}

		echo "<tr><td>&nbsp;</td></tr>";
		echo "</table>";
	}

	/**
	 * Lista los Usuario en una Tabla UNICEF
	 * @access public
	 */
	function UnicefListarTabla(){

		//INICIALIZA VARIABLES
		$tipo_dao = New TipoUsuarioDAO();
		$activo_img = array("inactivo.gif","activo.gif");
		$chk_activo = array('activo' => '', 'inactivo' => '');
		$condicion = 'id_tipo_usuario IN (31,32,40)';
        $letra_inicial = '';

		$url = "index_unicef.php?m_g=admin&m_e=usuario&accion=listar&class=".$_GET["class"]."&method=UnicefListarTabla&param=";
		$url_tipo = '';
		$url_activo = '';
		$url_li = '';

		$id_tipo = 0;
		if (isset($_GET["id_tipo"]) && $_GET["id_tipo"] != ''){
			$id_tipo = $_GET["id_tipo"];
			$url_tipo = "&id_tipo=$id_tipo";
			$condicion = "id_tipo_usuario = $id_tipo";

		}
		$activo = 0;
		if (isset($_GET["activo"]) && $_GET["activo"] != ''){
			$activo = $_GET["activo"];
			($activo == 1)	?	$chk_activo['activo'] = ' selected ' : $chk_activo['inactivo'] = ' selected ';
			$url_activo = "&activo=$activo";

			if ($condicion == '')	$condicion = "activo = $activo";
			else					$condicion .= " AND activo = $activo";

		}

		//Consulta las letras y numeros iniciales de los contactos para generar el indice-paginacion

		$letras_ini = $this->getLetrasIniciales($condicion);
		$si_letra_ini = (count($letras_ini) > 0) ? 1 : 0;
/*
		if ($si_letra_ini == 1){
            $letra_inicial = $letras_ini[0];

			if (isset($_GET["li"]) && $_GET["li"] != ''){
				$letra_inicial = $_GET["li"];
				$url_li = "&li=$letra_inicial";
			}
			if ($condicion == '')	$condicion = " $this->columna_nombre LIKE '$letra_inicial%'";
			else					$condicion .= " AND $this->columna_nombre LIKE '$letra_inicial%'";
		}
*/
		$arr = $this->GetAllArray($condicion);
		$num_arr = count($arr);

		echo "<br /><table align='center' class='tabla_lista' width='1000'>
			<tr><td colspan='5'>";

		//INDICE
		//if ($si_letra_ini == 1) $this->indiceListaContactos($letras_ini,$letra_inicial,$url.$url_tipo.$url_activo);

		echo "</td></tr>";

		echo "<tr><td>&nbsp;</td></tr>";

		echo "<tr>
			<td colspan='2'>
			Filtrar por Tipo de Usuario&nbsp;<select nane='id_tipo' class='select' onchange=\"location.href='".$url.$url_li.$url_activo."&id_tipo='+this.value\">
			<option value=''>Todos</option>";
			$tipo_dao->ListarCombo('combo',$id_tipo,"id_tipo_usuario IN (31,32,40)");
		echo "</select></td>

			<td colspan='3'>
			Filtrar por Estado&nbsp;<select nane='activo' class='select' onchange=\"location.href='".$url.$url_li.$url_tipo."&activo='+this.value\">
			<option value=''>Todos</option>
			<option value=1 ".$chk_activo['activo'].">Activos</option>
			<option value=0 ".$chk_activo['inactivo'].">Inactivos</option>";
		echo "</select></td></tr>";

        if ($si_letra_ini == 0){
            echo '<tr><td>No existen Usuarios</td></tr>';
        }
        else{
            echo "<tr><td>&nbsp;</td></tr><tr>
                <td width='100'><img src='/sissh/admin/images/home/insertar.png'>&nbsp;<a href='#' onclick=\"addWindowIU('unicef_usuario','insertar','');return false;\">Crear</a></td>
                <td colspan='5' align='right'>[$num_arr Registros]</td>
            </tr>
                <tr class='titulo_lista'>
                <td width='50' align='center'>ID</td>
                <td width='200'>Nombre</td>
                <td width='150'>Tipo</td>
                <td width='200'>Email</td>
                <td width='200'>Org</td>
                <td width='14'>Activo</td>
                </tr>";


            foreach ($arr as $p=>$vo){

                //NOMBRE DEL TIPO DE USUARIO
                $nom_tipo = '';
                if ($vo->id_tipo > 0){
                    $tipo_vo = $tipo_dao->Get($vo->id_tipo);
                    $nom_tipo = $tipo_vo->nombre;
                }

                $email = (strlen($vo->email) > 0) ? $vo->email : '&nbsp;';
                $org = (strlen($vo->org) > 0) ? $vo->org : '&nbsp;';

                echo "<tr class='fila_lista'>";
                echo "<td><a href='#'  onclick=\"if(confirm('Est� seguro que desea borrar el Usuario: ".$vo->nombre."')){borrarRegistro('UsuarioDAO','".$vo->id."')}else{return false};\"><img src='/sissh/admin/images/trash.png' border='0' title='Borrar' /></a>&nbsp;".$vo->id."</td>";
                echo "<td><a href='#' onclick=\"addWindowIU('unicef_usuario','actualizar',$vo->id)\">".$vo->nombre."</a> </td>";
                echo "<td>".$nom_tipo."</td>";
                echo "<td>".$email."</td>";
                echo "<td>".$org."</td>";
                echo "<td align='center'><img src='/sissh/admin/images/usuario/".$activo_img[$vo->activo]."'></td>";
                echo "</tr>";
            }

            echo "<tr><td>&nbsp;</td></tr>";
        }
		echo "</table>";
	}

	/**
	 * Consulta los caracteres iniciales de los nombres de los contacto
	 * @access public
	 * @param string $cond Condicion que deben cumplir los contacto
	 * @return array $chrs Arreglo con los caracteres
	 */
	function getLetrasIniciales($cond=''){

		$arr = Array();
		$sql = "SELECT DISTINCT UPPER(LEFT($this->columna_nombre,1)) FROM $this->tabla";

		if ($cond != ''){
			$sql .= " WHERE $cond";
		}

		$sql .= " ORDER BY $this->columna_nombre";

		$rs = $this->conn->OpenRecordset($sql);
		while ($row = $this->conn->FetchRow($rs)){
			$arr[] = $row[0];
		}

		return $arr;
	}

	/**
	 * Muestra el indice en el listado de contacto
	 * @access public
	 * @param array $letras_ini Arreglo con los caracteres
	 * @param string $letra_inicial Actual letra inicial
	 * @param string $url Base del url
	 */
	function indiceListaContactos($letras_ini,$letra_inicial,$url){

		//Filtros
		if (isset($_GET["id_tipo"])){
			$url .= "&id_tipo=".$_GET["id_tipo"];
		}

		if (isset($_GET["activo"])){
			$url .= "&activo=".$_GET["activo"];
		}

		echo "<img src='/sissh/images/indice.png'>&nbsp;Indice:&nbsp;";

		//Todos
		$class = ($letra_inicial == '') ? 'a_big' : 'a_normal' ;
		//echo "<a href='$url&li=' class='$class'>Todos</a>&nbsp;&nbsp;";

		foreach ($letras_ini as $letra){
			$class = (strtolower($letra) == strtolower($letra_inicial)) ? 'a_big' : 'a_normal' ;

			echo "<a href='#' onclick=\"refreshTab('$url&li=$letra')\" class='$class'>".$letra."</a>";
			echo "&nbsp;&nbsp;";
		}
	}

	/**
	 * Reportar
	 * @access public
	 */
	function Reportar(){

		$tipo_dao = New TipoUsuarioDAO();
		$tipos = $tipo_dao->GetAllArray('');

		echo "<table align='center' width='700' class='tabla_lista'>
			<tr><td colspan='5' align='right'><img src='../images/consulta/excel.gif'>&nbsp;<a href='../export_data.php?case=xls_session&nombre_archivo=usuarios_SIDIH'>Guardar Archivo</a></td>";

		$xls = "<tr><td>USUARIO SIDIH</td></tr>";

		foreach ($tipos as $tipo){

			$arr = $this->GetAllArray("ID_TIPO_USUARIO=".$tipo->id);

			echo "<tr><td>&nbsp;</td></tr>
				<tr><td class='titulo_lista'><b>".$tipo->nombre."</b></td></tr>";

			$xls .= "<tr><td>&nbsp;</td></tr><tr><td><b>".strtoupper($tipo->nombre)."</b></td></tr>";

			foreach ($arr as $vo){

				echo "<tr class='fila_lista'>";
				echo "<td>".$vo->nombre."</td>";
				echo "</tr>";

				$xls .= "<tr><td>$vo->nombre</td></tr>";
			}
		}
		echo "</table>";

		$_SESSION["xls"] = "<table>$xls</table>";
	}

	/**
	 * Carga un VO de Usuario con los datos de la consulta
	 * @access public
	 * @param object $vo VO de Usuario que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de Usuario con los datos
	 */
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->ID_USUARIO;
		$vo->nombre = $Result->NOM_USUARIO;
		$vo->id_tipo = $Result->ID_TIPO_USUARIO;
		$vo->login = $Result->LOGIN;
		$vo->pass = $Result->PASS;
		$vo->email = $Result->EMAIL;
		$vo->org = $Result->ORG;
		$vo->tel = $Result->TEL;
		$vo->punto_contacto = $Result->PUNTO_CONTACTO;
		$vo->activo = $Result->ACTIVO;
		$vo->cnrr = $Result->CNRR;
		$vo->id_org = $Result->ID_ORG_RESPONSABLE;
		$vo->id_tema = $Result->ID_TEMA;

		return $vo;
	}

	/**
	 * Inserta un Usuario en la B.D.
	 * @access public
	 * @param object $usuario_vo VO de Usuario que se va a insertar
	 */
	function Insertar($usuario_vo){
		//CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray("LOGIN = '".$usuario_vo->login."'");
		if (count($cat_a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.",ID_TIPO_USUARIO,LOGIN,PASS,EMAIL,CNRR,ORG,TEL,ACTIVO,PUNTO_CONTACTO,ID_ORG_RESPONSABLE,ID_TEMA) VALUES ('".$usuario_vo->nombre."',".$usuario_vo->id_tipo.",'".$usuario_vo->login."','".$usuario_vo->pass."','".$usuario_vo->email."',".$usuario_vo->cnrr.",'".$usuario_vo->org."','".$usuario_vo->tel."',1,'".$usuario_vo->punto_contacto."',$usuario_vo->id_org,$usuario_vo->id_tema)";
			$this->conn->Execute($sql);

			echo "Registro insertado con &eacute;xito!";
		}
		else{
			echo "Error - Existe un registro con el mismo nombre";
		}
	}

	/**
	 * Registra un Usuario en la B.D.
	 * @access public
	 * @param object $usuario_vo VO de Usuario que se va a insertar
	 */
	function Registrar($usuario_vo){
		//CONSULTA SI YA EXISTE
		//		$cat_a = $this->GetAllArray("LOGIN = '".$usuario_vo->login."' OR EMAIL = '$usuario_vo->email ' OR ORG = '$usuario_vo->org'");
		$cat_a = $this->GetAllArray("LOGIN = '".$usuario_vo->login."' OR EMAIL = '$usuario_vo->email '");
		if (count($cat_a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.",ID_TIPO_USUARIO,LOGIN,PASS,EMAIL,CNRR,ORG,TEL,ACTIVO,PUNTO_CONTACTO) VALUES ('".$usuario_vo->nombre."',".$usuario_vo->id_tipo.",'".$usuario_vo->login."','".$usuario_vo->pass."','".$usuario_vo->email."',".$usuario_vo->cnrr.",'".$usuario_vo->org."','".$usuario_vo->tel."',0,'".$usuario_vo->punto_contacto."')";
			//echo $sql;
			//die;
			$this->conn->Execute($sql);

			//ENVIA EMAIL
			$from = "no-reply@umaic.org";

			require($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/common/class.phpmailer.php");

			$mail = new PHPMailer();

			$mail->IsSMTP(); // set mailer to use SMTP

			$mail->From = $from;
			$mail->FromName = "SIDI";
            $mail->AddAddress("zhang17@un.org", "Xitong Zhang");
			$mail->AddCC("villaveces@un.org", "Jeffrey Villaveces");
			$mail->AddBCC("rubenrojasc@gmail.com", "Ruben Rojas");
			$mail->AddBCC("ict@umaic.org", "ICT UMAIC");

			$mail->WordWrap = 50;                                 // set word wrap to 50 characters
			$mail->IsHTML(true);                                  // set email format to HTML

			$mail->Subject = "Nuevo usuario registrado en SIDI";
            $mail->Body    = "Se ha registrado el usuario: <br />
                              Nombre: <b>$usuario_vo->nombre</b> <br />
                              Login:  <b>$usuario_vo->login</b> <br />
                              Email:  <b>$usuario_vo->email</b> <br />
                              Organización:  <b>$usuario_vo->org</b> <br />
                              Tel&eacute;fono: <b>$usuario_vo->tel</b> <br />
                              Contacto en OCHA: <b>$usuario_vo->punto_contacto</b> <br /><br />
                              El usuario queda pendiente de activación";

			$mail->Send();
		}
		else{
			?>
				<script>
				alert("Error - Ya existe un usuario registrado con el mismo Nombre de Usaurio(login) o con el mismo Email\n\nSi ya se registr� por favor espere el correo de activaci�n de su cuenta, gracias.!!");
			    //location.href='registro.php';
			</script>
				<?
		}
	}

    /**
	 * Registra un Usuario en la B.D de UNICEF.
	 * @access public
	 * @param object $usuario_vo VO de Usuario que se va a insertar
	 */
	function UnicefRegistrar($usuario_vo){
		//CONSULTA SI YA EXISTE
		$a = $this->GetAllArray("LOGIN = '".$usuario_vo->login."' OR EMAIL = '$usuario_vo->email '");
		if (count($a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.",ID_TIPO_USUARIO,LOGIN,PASS,EMAIL,CNRR,ORG,TEL,ACTIVO,PUNTO_CONTACTO) VALUES ('".$usuario_vo->nombre."',".$usuario_vo->id_tipo.",'".$usuario_vo->login."','".$usuario_vo->pass."','".$usuario_vo->email."',".$usuario_vo->cnrr.",'".$usuario_vo->org."','".$usuario_vo->tel."',0,'".$usuario_vo->punto_contacto."')";
			//echo $sql;
			//die;
			$this->conn->Execute($sql);

			//ENVIA EMAIL
			$from = "rojas@un-ocha.org";

			require($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/common/class.phpmailer.php");

			$mail = new PHPMailer();

			$mail->IsSMTP(); // set mailer to use SMTP

			$mail->From = $from;
			$mail->FromName = "Sistema de Informaci�n UNICEF";
			$mail->AddAddress("sriaga@unicef.org", "Sergio Riaga");
			$mail->AddBCC("rubenrojasc@gmail.com", "Ruben Rojas");

			$mail->WordWrap = 50;                                 // set word wrap to 50 characters
			$mail->IsHTML(true);                                  // set email format to HTML

			$mail->Subject = "Nuevo usuario registrado en SI UNICEF";
			$mail->Body    = "Se ha registrado el usuario <b>$usuario_vo->nombre</b>, con login = <b>$usuario_vo->login</b>, el usuario queda pendiente de activaci�n";

			$mail->Send();
		}
		else{
			?>
				<script>
				alert("Error - Ya existe un usuario registrado con el mismo Nombre de Usaurio(login) o con el mismo Email\n\nSi ya se registr� por favor espere el correo de activaci�n de su cuenta, gracias.!!");
			    //location.href='registro.php';
			</script>
				<?
		}
	}

	/**
	 * Actualiza un Usuario en la B.D.
	 * @access public
	 * @param object $usuario_vo VO de Usuario que se va a actualizar
	 */
	function Actualizar($usuario_vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_nombre." = '".$usuario_vo->nombre."',";
		$sql .= " LOGIN = '".$usuario_vo->login."',";
		$sql .= " PASS = '".$usuario_vo->pass."',";
		$sql .= " EMAIL = '".$usuario_vo->email."',";
		$sql .= " TEL = '".$usuario_vo->tel."',";
		$sql .= " ORG = '".$usuario_vo->org."',";
		$sql .= " PUNTO_CONTACTO = '".$usuario_vo->punto_contacto."',";
		$sql .= " ACTIVO = ".$usuario_vo->activo.",";
		$sql .= " ID_TIPO_USUARIO = ".$usuario_vo->id_tipo.",";
		$sql .= " CNRR = $usuario_vo->cnrr,";
		$sql .= " ID_TEMA = $usuario_vo->id_tema,";
		$sql .= " ID_ORG_RESPONSABLE = ".$usuario_vo->id_org;
		$sql .= " WHERE ".$this->columna_id." = ".$usuario_vo->id;
		$this->conn->Execute($sql);

	}

	/**
	 * Borra un Usuario en la B.D.
	 * @access public
	 * @param int $id ID del Usuario que se va a borrar de la B.D
	 */
	function Borrar($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

	}

	function Restaurar($email){

		//CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray("LOGIN = '".$usuario_vo->login."' OR EMAIL = '$usuario_vo->email '");
		if (count($cat_a) == 0){
			?>
			<script>
				alert("Error - No existe un usuario registrado con el Email");
			</script>
			<?
		}
		else{
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.",ID_TIPO_USUARIO,LOGIN,PASS,EMAIL,CNRR,ORG,TEL,ACTIVO,PUNTO_CONTACTO) VALUES ('".$usuario_vo->nombre."',".$usuario_vo->id_tipo.",'".$usuario_vo->login."','".$usuario_vo->pass."','".$usuario_vo->email."',".$usuario_vo->cnrr.",'".$usuario_vo->org."','".$usuario_vo->tel."',0,'".$usuario_vo->punto_contacto."')";
			//echo $sql;
			//die;
			$this->conn->Execute($sql);

			//ENVIA EMAIL
			$from = "no-reply@umaic.org";

			require($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/common/class.phpmailer.php");

			$mail = new PHPMailer();

			$mail->IsSMTP(); // set mailer to use SMTP

			$mail->From = $from;
			$mail->FromName = "SIDI";
			$mail->AddAddress("zhang17@un.org", "Xitong Zhang");
			$mail->AddCC("villaveces@un.org", "Jeffrey Villaveces");
			$mail->AddBCC("rubenrojasc@gmail.com", "Ruben Rojas");
			$mail->AddBCC("ict@umaic.org", "ICT UMAIC");

			$mail->WordWrap = 50;                                 // set word wrap to 50 characters
			$mail->IsHTML(true);                                  // set email format to HTML

			$mail->Subject = "Nuevo usuario registrado en SIDI";
			$mail->Body    = "Se ha registrado el usuario: <br />
                              Nombre: <b>$usuario_vo->nombre</b> <br />
                              Login:  <b>$usuario_vo->login</b> <br />
                              Email:  <b>$usuario_vo->email</b> <br />
                              Organización:  <b>$usuario_vo->org</b> <br />
                              Tel&eacute;fono: <b>$usuario_vo->tel</b> <br />
                              Contacto en OCHA: <b>$usuario_vo->punto_contacto</b> <br /><br />
                              El usuario queda pendiente de activación";

			$mail->Send();
		}

	}
}

?>
