<?
/**
 * DAO de Log
 *
 * Contiene los métodos de la clase Log 
 * @author Ruben A. Rojas C.
 */

Class LogUsuarioDAO {

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
	 * Id de Usuarios
	 * @var string
	 */
	var $id_usuario;

	/**
	 * Módulo específico
	 * @var string
	 */
	var $m_e;

	/**
	 * Constructor
	 * Crea la conexión a la base de datos
	 * @access public
	 */	
	function LogUsuarioDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "log_usuario";
		$this->columna_id = "id_log_usuario";
		$this->columna_nombre = "";
		$this->columna_order = "fecha";

		if (isset($_SESSION['id_usuario_s']))	$this->id_usuario = $_SESSION['id_usuario_s'];
		if (isset($_SESSION['m_e']))	$this->m_e = $_SESSION['m_e'];

	}

	/**
	 * Consulta el número de visitas al módulo
	 * @access public
	 * @param string $modulo Modulo
	 * @param string $ini Fecha Inicial
	 * @param string $fin Fecha Final
	 * @return int
	 */	
	function getNum($modulo,$ini='',$fin=''){

		$fecha = '';
		if ($ini != '' && $fin != ''){
			$fecha = " AND fecha BETWEEN '$ini' AND '$fin'";
		}

		if (!isset($_GET["id_tipo"]) || $_GET["id_tipo"] == ''){
			if ($modulo != 'reporte'){
				$sql = "SELECT count(id_log_usuario) FROM ".$this->tabla." WHERE m_e = '$modulo' $fecha";
			}
			else{
				$sql = "SELECT count(id_log_usuario) FROM ".$this->tabla." WHERE m_e NOT IN ('minificha','tabla_grafico') $fecha";
			}
		}
		else{
			$inner = " INNER JOIN usuario ON ".$this->tabla.".id_usuario = usuario.id_usuario";
			$id_tipo = $_GET["id_tipo"];

			$where_tipo = " AND ID_TIPO_USUARIO = ".$id_tipo;

			if ($modulo != 'reporte'){
				$sql = "SELECT count(id_log_usuario) FROM ".$this->tabla." $inner WHERE m_e = '$modulo' $fecha $where_tipo";
			}
			else{
				$sql = "SELECT count(id_log_usuario) FROM ".$this->tabla." $inner WHERE m_e NOT IN ('minificha','tabla_grafico') $fecha $where_tipo";
			}
		}

		//		echo $sql;

		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchRow($rs);

		return $row_rs[0];
	}

	/**
	 * Consulta el número de acciones en el modulo de administracion
	 * @access public
	 * @param string $modulo Modulo
	 * @param string $accion Accion ejecutada, ej, insertar, borrar, importar
	 * @param string $ini Fecha Inicial
	 * @param string $fin Fecha Final
	 * @return int
	 */	
	function getNumAdmin($modulo,$accion,$ini='',$fin=''){

		$fecha = '';
		if ($ini != '' && $fin != ''){
			$fecha = " AND fecha BETWEEN '$ini' AND '$fin'";
		}

		$sql = "SELECT count(id_log_usuario) FROM log_usuario_admin WHERE m_e = '$modulo' AND accion = '$accion' $fecha ORDER BY fecha DESC";

		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchRow($rs);

		return $row_rs[0];
	}

	/**
	 * Retorna el Top N de los deptos o mpios mas consultados
	 * @access public
	 * @param string $modulo Modulo
	 * @param int $reg Numero de regitros
	 * @param string $ini Fecha Inicial
	 * @param string $fin Fecha Final
	 * @return int
	 */	

	function getNumPerfil($cual,$reg,$ini='',$fin=''){

		$fecha = '';
		if ($ini != '' && $fin != ''){
			$fecha = " AND fecha BETWEEN '$ini' AND '$fin'";
		}

		$array = array();

		if (!isset($_GET["id_tipo"]) || $_GET["id_tipo"] == ''){
			if ($cual == 'depto'){
				$sql = "SELECT count(id_log_usuario) as num, id_depto FROM ".$this->tabla." WHERE m_e = 'minificha' $fecha GROUP BY id_depto ORDER BY num DESC LIMIT 0,$reg";
			}
			else{
				$sql = "SELECT count(id_log_usuario)as num, id_mun FROM ".$this->tabla." WHERE m_e = 'minificha' AND id_mun <> '' $fecha GROUP BY id_mun ORDER BY num DESC LIMIT 0,$reg";
			}
		}
		else{
			$inner = " INNER JOIN usuario ON ".$this->tabla.".id_usuario = usuario.id_usuario";
			$id_tipo = $_GET["id_tipo"];

			$where_tipo = " AND ID_TIPO_USUARIO = ".$id_tipo;

			if ($cual == 'depto'){
				$sql = "SELECT count(id_log_usuario) as num, id_depto FROM ".$this->tabla." $inner WHERE m_e = 'minificha' $fecha $where_tipo GROUP BY id_depto ORDER BY num DESC LIMIT 0,$reg";
			}
			else{
				$sql = "SELECT count(id_log_usuario)as num, id_mun FROM ".$this->tabla." $inner WHERE m_e = 'minificha' AND id_mun <> '' $fecha $where_tipo GROUP BY id_mun ORDER BY num DESC LIMIT 0,$reg";
			}

		}

		$rs = $this->conn->OpenRecordset($sql);

		while ($row_rs = $this->conn->FetchRow($rs)){
			$array[$row_rs[1]] = $row_rs[0] ;
		}

		return $array;
	}

	/**
	 * Número de visitas a cada módulo de Gráficas y Resumenes
	 * @access public
	 * @param string $modulo Modulo
	 * @param string $ini Fecha Inicial
	 * @param string $fin Fecha Final
	 * @return int
	 */	
	function getNumGraResumen($modulo,$ini='',$fin=''){

		$fecha = '';
		if ($ini != '' && $fin != ''){
			$fecha = " AND fecha BETWEEN '$ini' AND '$fin'";
		}		

		if (!isset($_GET["id_tipo"]) || $_GET["id_tipo"] == ''){
			$sql = "SELECT count(id_log_usuario) as num FROM ".$this->tabla." WHERE modulo = '$modulo' $fecha";
		}
		else{
			$inner = " INNER JOIN usuario ON ".$this->tabla.".id_usuario = usuario.id_usuario";
			$id_tipo = $_GET["id_tipo"];

			$where_tipo = " AND ID_TIPO_USUARIO = ".$id_tipo;

			$sql = "SELECT count(id_log_usuario) as num FROM ".$this->tabla." $inner WHERE modulo = '$modulo' $fecha $where_tipo";
		}

		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchRow($rs);

		return $row_rs[0];
	}	

	/**
	 * Consulta el número de visitas del tipo de usuario y usuario
	 * @access public
	 * @param string $case Tipo de Usuario-Usuario
	 * @param int $id ID del tipo o del usuario
	 * @param string $ini Fecha Inicial
	 * @param string $fin Fecha Final
	 * @return int
	 */	
	function getNumUsuario($case,$id,$ini='',$fin=''){

		$fecha = '';
		if ($ini != '' && $fin != ''){
			$fecha = " AND fecha BETWEEN '$ini' AND '$fin'";
		}

		switch ($case){
			case 'tipo_usuario':
				$sql = "SELECT count(id_log_usuario) FROM ".$this->tabla." INNER JOIN usuario ON ".$this->tabla.".id_usuario = usuario.id_usuario WHERE id_tipo_usuario = $id $fecha";
				break;
			case 'usuario':
				$sql = "SELECT count(id_log_usuario) FROM ".$this->tabla." WHERE id_usuario = $id $fecha";
				break;

		}

		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchRow($rs);

		return $row_rs[0];
	}	

	/**
	 * Visualiza el LOG del módulo de administración
	 * @access public
	 */			
	function ListarTablaAdmin(){

		//INICIALIZA VARIABLES
		$usuario_dao = new UsuarioDAO();

		$modulo = '';
		$accion = '';
		$ini = '';
		$fin = '';
		if (isset($_GET["modulo"])){
			$modulo = $_GET["modulo"];
			$accion = $_GET["acc"];
			$ini = $_GET["ini"];
			$fin = $_GET["fin"];		
		}
		?>
		<table width="900" align='center' class='tabla_lista'>
			<tr><td>&nbsp;</td></tr>
			<tr class='titulo_lista'><td align='center'><b>REGISTRO ADMINISTRACION Y ALIMENTACION</b></td></tr>
			<tr>
			<td>
			<table cellpadding="2">
			<tr>
			<td><b>M&oacute;dulo</b>: </td>
			<td>
			<select id='modulo' class='select'>
			<option value='org' <? if($modulo == 'org')	echo 'selected' ?>>Organizaciones</option>
			<option value='dato_sectorial' <? if($modulo == 'dato_sectorial')	echo 'selected' ?>>Datos Sectorial Admin</option>
			<option value='dato_s_valor' <? if($modulo == 'dato_s_valor')	echo 'selected' ?>>Datos Sectorial Valor</option>
			</select>
			</td>
			<td><b>Acci&oacute;n</b>: </td>
			<td>
			<select id="accion" class='select'>
			<option value='insertar' <? if($accion == 'insertar')	echo 'selected' ?>>Crear</option>
			<option value='actualizar' <? if($accion == 'actualizar')	echo 'selected' ?>>Modificar</option>
			<option value='borrar' <? if($accion == 'borrar')	echo 'selected' ?>>Eliminar</option>
			<option value='importar' <? if($accion == 'importar')	echo 'selected' ?>>Importar</option>

			</select>
			</td>
			<td><b>Inicio</b>:</td>
			<td>
                <input type="text" id="f_ini" name="f_ini" class="textfield" size="10">
                <a href="#" onclick="displayCalendar(document.getElementById('f_ini'),'yyyy-mm-dd',this);return false;"><img src="../images/calendar.png" border="0"></a>
			</td>
			<td><b>Fin</b>:</td>
			<td>
                <input type="text" id="f_fin" name="f_fin" class="textfield" size="10">
                <a href="#" onclick="displayCalendar(document.getElementById('f_fin'),'yyyy-mm-dd',this);return false;"><img src="../images/calendar.png" border="0"></a>
			</td>
			<td><input type="button" value="Buscar" onclick="refreshTab('index_parser.php?m_e=log_admin&accion=listar&class=LogUsuarioDAO&method=ListarTablaAdmin&param=&modulo='+document.getElementById('modulo').value+'&acc='+document.getElementById('accion').value+'&ini='+document.getElementById('f_ini').value+'&fin='+document.getElementById('f_fin').value)" class='boton'></td>
			</tr>
			</table>
			</td>
			</tr>
			<?
			if (isset($_GET["modulo"])){

				$fecha = '';
				if ($ini != '' && $fin != ''){
					$fecha = " AND fecha BETWEEN '$ini' AND '$fin'";
				}

				$sql = "SELECT * FROM log_usuario_admin WHERE m_e = '$modulo' AND accion = '$accion' $fecha ORDER BY fecha DESC";
				//echo $sql;
				$rs = $this->conn->OpenRecordset($sql);

				echo "<tr><td colspan='5'><table cellpadding=5 cellpadding=1 width='770'>";
				echo "<tr class='titulo_lista'><td>Elemento</td><td>Usuario</td><td>Fecha</td></tr>";
				while($row_rs = $this->conn->FetchObject($rs)){
					if ($accion == 'borrar'){
						$elemento = $row_rs->nombre;
					}
					else{
						$id = $row_rs->id;

						switch ($modulo){
							case 'dato_sectorial':
								$dao = New DatoSectorialDAO();

								$elemento = $dao->GetName($id);
								break;
							case 'org':
								$dao = New OrganizacionDAO();

								$elemento = $dao->GetName($id);
								break;
						}
					}

					$usuario = $usuario_dao->Get($row_rs->ID_USUARIO);

					echo "<tr class='fila_lista'><td>$elemento</td><td>$usuario->nombre</td><td>$row_rs->fecha</td></tr>";
				}

				echo "</table></td></tr>";
			}

		?>
			</table>
			<?
	}

	/**
	 * Visualiza el LOG del módulo de consultas
	 * @access public
	 */			
	function ListarTablaConsulta(){

		//LIBRERIA SMOOT
		include("lib/common/imageSmoothArc.php");
        
		//INICIALIZACION DE VARIABLES
		$PG = new PowerGraphic();
		$depto_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();
		$tipo_dao = New TipoUsuarioDAO();
		$usuario_dao = New UsuarioDAO();

		$ini = date('Y-m-d',strtotime('-3 months'));
		$fin = date('Y-m-d');
		if (isset($_GET["ini"])){
			$ini = $_GET["ini"];
			$fin = $_GET["fin"];		
		}

		$id_tipo = 0;
		if (isset($_GET["id_tipo"])){
			$id_tipo = $_GET["id_tipo"];
		}

		$num_perfil = $this->getNum('minificha',$ini,$fin);
		$num_g_r = $this->getNum('tabla_grafico',$ini,$fin);
		$num_reporte = $this->getNum('reporte',$ini,$fin);
		$num_reg_perfil = 10;  //Numero de registros del top
		$PG->title     = "";
		//$PG->axis_x    = 'Módulo';
		$PG->axis_y    = 'Consultas';
		$PG->skin      = 1;
		$PG->type      = 5;
		$PG->credits   = 0;

		$PG->x[0] = 'Perfiles';
		$PG->x[1] = 'Gráficas y Resumenes';
		$PG->x[2] = 'Reportes';

		$PG->y[0] = $num_perfil;
		$PG->y[1] = $num_g_r;
		$PG->y[2] = $num_reporte;

		?>
		<table align='center' cellspacing='1' cellpadding='5' width=770>
			<tr><td>&nbsp;<a name="top"></td></tr>
			<tr class='titulo_lista'><td align='center' colspan=4><b>REGISTRO DE CONSULTAS</b></td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
			<td colspan="4"><img src="../images/fecha.jpg">&nbsp;<b>Filtrar por fecha:&nbsp;&nbsp;&nbsp;Inicio</b>:&nbsp;&nbsp;
                <input type="text" id="f_ini" name="f_ini" class="textfield" size="10" value="<?php echo $ini ?>">
                <a href="#" onclick="displayCalendar(document.getElementById('f_ini'),'yyyy-mm-dd',this);return false;"><img src="../images/calendar.png" border="0"></a>
                &nbsp;&nbsp;<b>Fin</b>:&nbsp;&nbsp;
                <input type="text" id="f_fin" name="f_fin" class="textfield" size="10" value="<?php echo $fin ?>">
                <a href="#" onclick="displayCalendar(document.getElementById('f_fin'),'yyyy-mm-dd',this);return false;"><img src="../images/calendar.png" border="0"></a>
			</td>		    
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
			<td colspan="4">
			<img src="../images/reg_usuario.gif">&nbsp;&nbsp;<b>Filtrar por Tipo de usuario:&nbsp;&nbsp;&nbsp;
		<select id="id_tipo" name="id_tipo" class="select">
			<option value=''>[ Todos ]</option>
			<?
			$tipo_dao->ListarCombo("combo",$id_tipo,"");
		?>
			</select>
			<!--&nbsp;&nbsp;<input type="button" value="Buscar" onclick="refreshTab('index_parser.php?m_e=log_consulta&accion=listar&class=LogUsuarioDAO&method=ListarTablaConsulta&param=&id_tipo='+document.getElementById('id_tipo').value+'&ini='+document.getElementById('f_ini').value+'&fin='+document.getElementById('f_fin').value)" class='boton'></td>-->
			&nbsp;&nbsp;<input type="button" value="Buscar" onclick="location.href='index.php?m_e=log_consulta&accion=listar&class=LogUsuarioDAO&method=ListarTablaConsulta&param=&id_tipo='+document.getElementById('id_tipo').value+'&ini='+document.getElementById('f_ini').value+'&fin='+document.getElementById('f_fin').value" class='boton'></td>
			</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr class='titulo_lista'><td align='center' colspan=4><a name="est_gen"><b>Estadisticas Generales</b></a></td></tr>
			<tr>
			<td valign="top">
			<table cellspacing=1 cellpadding="3">
			<tr class='titulo_lista'><td align='center'>M&oacute;dulo</td><td width='100'># Consultas</td></tr>
			<tr class="fila_lista"><td>Perfiles</td><td align="center"><?=$num_perfil;?></td>
			<tr class="fila_lista"><td>Gr&aacute;ficas y Resumenes</td><td align="center"><?=$num_g_r;?></td>
			<tr class="fila_lista"><td>Reportes</td><td align="center"><?=$num_reporte;?></td>
			</table>
			</td>
			<td>
			<?
			if (max($PG->y) > 0){ 
				imagepng($PG->create_graphic_minificha(),'../../tmp/log_0.png');
				echo "<img src='../../tmp/log_0.png'>";
			}
		?>
			</td>
			</tr>
			<tr><td><a href="#top">^ Subir</a></td></tr>
			<tr class='titulo_lista'><td align='center' colspan=4><a name="perfiles"><b> :: DETALLE PERFILES DEPARTAMENTALES Y MUNICIPALES :: </b></a></td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
			<td valign="top">
			<table cellspacing=1 cellpadding="3">
			<tr class='titulo_lista'><td align='center' colspan="2" align="center"><?=$num_reg_perfil?> Departamentos mas consultados</td></tr>
			<tr class='fila_lista'><td align='center'><b>Departamento</b></td><td width='100'><b># Consultas</b></td></tr>
			<?
			$ids = $this->getNumPerfil('depto',$num_reg_perfil,$ini,$fin);

		foreach($ids as $id => $num){
            if ($id != ''){
			    $vo = $depto_dao->Get($id);
                $nombre = $vo->nombre;
            }
            else    $nombre = 'Nacional';

			echo "<tr class='fila_lista'><td>$nombre</td><td align='center'>$num</td>";
		}
		?>

			</table>
			</td>
			<td valign="top">
			<table cellspacing=1 cellpadding="3">
			<tr class='titulo_lista'><td align='center' colspan="2" align="center"><?=$num_reg_perfil?> Municipios mas consultados</td></tr>
			<tr class='fila_lista'><td align='center'><b>Municipio</b></td><td width='100'><b># Consultas</b></td></tr>
			<?
			$ids = $this->getNumPerfil('mun',$num_reg_perfil,$ini,$fin);

		foreach($ids as $id => $num){
			$vo = $mun_dao->Get($id);
			echo "<tr class='fila_lista'><td>$vo->nombre</td><td align='center'>$num</td>";
		}
		?>

			</table>
			</td>
			</tr>		
			<tr><td><a href="#top">^ Subir</a></td></tr>
			<tr class='titulo_lista'><td align='center' colspan=4><a name="gra_resum"><b> :: DETALLE GRAFICAS Y RESUMENES :: </b></a></td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
			<td valign="top">
			<table cellspacing=1 cellpadding="3">
			<tr class='titulo_lista'><td align='center'>M&oacute;dulo</td><td width='100'># Consultas</td></tr>
			<tr class="fila_lista"><td>Organizaciones</td><td align="center"><?=$this->getNumGraResumen('Organizaciones',$ini,$fin);?></td>
			<tr class="fila_lista"><td>Desplazamiento</td><td align="center"><?=$this->getNumGraResumen('Desplazamiento',$ini,$fin);?></td>
			<tr class="fila_lista"><td>Accidentes con Mina</td><td align="center"><?=$this->getNumGraResumen('Mina',$ini,$fin);?></td>
			<tr class="fila_lista"><td>Datos Sectoriales</td><td align="center"><?=$this->getNumGraResumen('Datos Sectoriales',$ini,$fin);?></td>
			</table>
			</td>
			<?
			$PG->title     = "";
		//$PG->axis_x    = 'Módulo';
		$PG->axis_y    = 'Consultas';
		$PG->skin      = 1;
		$PG->type      = 5;
		$PG->credits   = 0;

		$PG->x[0] = 'Organizaciones';
		$PG->x[1] = 'Desplazamiento';
		$PG->x[2] = 'Accidentes con Mina';
		$PG->x[3] = 'Datos Sectoriales';

		$PG->y[0] = $this->getNumGraResumen('Organizaciones',$ini,$fin);
		$PG->y[1] = $this->getNumGraResumen('Desplazamiento',$ini,$fin);
		$PG->y[2] = $this->getNumGraResumen('Mina',$ini,$fin);
		$PG->y[3] = $this->getNumGraResumen('Datos Sectoriales',$ini,$fin);

		?>
			<td>
			<?
			if (max($PG->y) > 0){ 
				imagepng($PG->create_graphic_minificha(),'../../tmp/log_1.png');
				echo "<img src='../../tmp/log_1.png'>";
			}
		?>
			</td>		    	
			</tr>		
			<tr><td><a href="#top">^ Subir</a></td></tr>
			<tr class='titulo_lista'><td align='center' colspan=4><a name="reportes"><b> :: DETALLE REPORTES :: </b></a></td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
			<td valign="top">
			<table cellspacing=1 cellpadding="3">
			<tr class='titulo_lista'><td align='center'>M&oacute;dulo</td><td width='100'># Consultas</td></tr>
			<tr class="fila_lista"><td>Organizaciones</td><td align="center"><?=$this->getNum('org',$ini,$fin);?></td>
			<tr class="fila_lista"><td>Proyectos</td><td align="center"><?=$this->getNum('proyecto',$ini,$fin);?></td>
			<tr class="fila_lista"><td>Datos Sectoriales</td><td align="center"><?=$this->getNum('dato_sectorial',$ini,$fin);?></td>
			<tr class="fila_lista"><td>Desplazamiento</td><td align="center"><?=$this->getNum('desplazamiento',$ini,$fin);?></td>
			<tr class="fila_lista"><td>Accidentes con Mina</td><td align="center"><?=$this->getNum('mina',$ini,$fin);?></td>
			</table>
			</td>
			<?
			$PG->title     = "";
		//$PG->axis_x    = 'Módulo';
		$PG->axis_y    = 'Consultas';
		$PG->skin      = 1;
		$PG->type      = 5;
		$PG->credits   = 0;

		$PG->x[0] = 'Organizaciones';
		$PG->x[1] = 'Proyectos';
		$PG->x[2] = 'Datos Sectoriales';
		$PG->x[3] = 'Desplazamiento';
		$PG->x[4] = 'Accidentes con Mina';

		$PG->y[0] = $this->getNum('org',$ini,$fin);
		$PG->y[1] = $this->getNum('proyecto',$ini,$fin);
		$PG->y[2] = $this->getNum('dato_sectorial',$ini,$fin);
		$PG->y[3] = $this->getNum('desplazamiento',$ini,$fin);
		$PG->y[4] = $this->getNum('mina',$ini,$fin);

		?>
			<td>
			<?
			if (max($PG->y) > 0){ 
				imagepng($PG->create_graphic_minificha(),'../../tmp/log_2.png');
				echo "<img src='../../tmp/log_2.png'>";
			}
		?>
			</td>		    	
			</tr>				    
			<tr><td><a href="#top">^ Subir</a></td></tr>
			<tr class='titulo_lista'><td align='center' colspan=4><a name="usuarios"><b> :: LOG DE USUARIO PARA EL MODULO DE CONSULTA :: </b></a></td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
			<td valign="top" align="center" colspan="2">
			<table cellspacing=1 cellpadding="3" width="700">
			<tr class='titulo_lista'><td align='center'>Tipo de Usuario</td><td width='100'># Visitas</td></tr>
			<?
			$condicion = ($id_tipo != '') ? "ID_TIPO_USUARIO = $id_tipo" : "";
		$tipos_usu = $tipo_dao->GetAllArray($condicion);
		$t = 0;
		foreach ($tipos_usu as $tipo){
			$num_por_tipo[$tipo->id] = $this->getNumUsuario('tipo_usuario',$tipo->id,$ini,$fin);

			$usuarios = $usuario_dao->GetAllArray("ID_TIPO_USUARIO = $tipo->id");
			foreach ($usuarios as $usuario){
				$num_por_usuario[$tipo->id][$usuario->id] = $this->getNumUsuario('usuario',$usuario->id,$ini,$fin);
			}
		}

		//ORDENA EL NUMERO DE VISITAS POR USUARIO
		arsort($num_por_tipo);

		foreach ($num_por_tipo as $id_tipo => $num_t){

			$tipo = $tipo_dao->Get($id_tipo);

			if ($num_t > 0){
				echo '<tr class="titulo_lista"><td>'.$tipo->nombre.'</td><td align="center">'.$num_t.'</td><td>Org</td>';
				foreach ($num_por_usuario[$id_tipo] as $id_usuario => $num_u){
					if ($num_u > 0){
						$usuario = $usuario_dao->Get($id_usuario);
						echo '<tr class="fila_lista"><td>'.$usuario->nombre.'</td><td align="center">'.$num_u.'</td><td align="center">'.$usuario->org.'</td>';
					}
				}
				//Grafica
				$PG->x[$t] = $tipo->nombre;
				$PG->y[$t] = $num_por_tipo[$tipo->id];
				$t++;
			}
		}
		?>
			</table>
			</td>
			</tr>
			<tr>
			<td align="center" colspan="2">
			<?
			if (max($PG->y) > 0){ 
				imagepng($PG->create_graphic_minificha(),'../../tmp/log_3.png');
				echo "<img src='../../tmp/log_3.png'>";
			}
		?>
			</td>		    	
			</tr>				    
			</table>

			<?
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
	 * Imprime en pantalla los datos del Log
	 * @access public
	 * @param object $vo Log que se va a imprimir
	 * @param string $formato Formato en el que se listarán los Log, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del Log que será selccionado cuando el formato es ComboSelect
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
	 * Carga un VO de Log con los datos de la consulta
	 * @access public
	 * @param object $vo VO de Log que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de Log con los datos
	 */			
	function GetFromResult ($vo,$Result){



		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};

		return $vo;
	}

	/**
	 * Registra la accion en el módulo de administración
	 * @access public
	 */		
	function RegistrarAdmin($id=0,$m_e=''){

		$accion = (isset($_POST["accion"])) ? $_POST["accion"] : $_GET["accion"];
		$id_modulo_minificha = (isset($_POST["id_modulo"])) ? $_POST["id_modulo"] : 0;

		if ($accion == 'borrar' && !isset($_GET["id"])){
			$id = $_GET["param"];
		}
		else{
			if ($id == 0){
				if (isset($_POST["id"]) && $_POST["id"] != "" && $_POST["id"] > 0){
					$id = $_POST["id"];
				}
			}

			if ($id == 0 && isset($_POST["id_dato"]))	$id = $_POST["id_dato"];
			/*$modulos_minificha = array("",
			  "General",
			  "Datos Sectoriales Generales & Demografía",
			  "Desplazamiento",
			  "Minas",
			  "Indice de Riesgo de Situación Humanitaria",
			  "Organizaciones"
			  );*/

		}

		if ($m_e != '')	$this->m_e = $m_e;

		$sql =  "INSERT INTO ".$this->tabla."_admin (ID_USUARIO,m_e,accion,fecha,id,id_modulo_minificha) VALUES ($this->id_usuario,'$this->m_e','$accion',now(),$id,$id_modulo_minificha)";
		$this->conn->Execute($sql);

		if ($accion == 'borrar'){

			$class = $_GET["class"];

			$obj = New $class();
			$columna_nombre = $obj->columna_nombre;
			$columna_id = $obj->columna_id;
			$tabla = $obj->tabla;

			$sql = "SELECT $columna_nombre FROM $tabla WHERE $columna_id = $id";
			//echo $sql;
			$rs = $this->conn->OpenRecordset($sql);
			$row_rs = $this->conn->FetchRow($rs);

			$this->setNombre($row_rs[0]);
		}		
	}

	/**
	 * Registra la accion en el módulo de usuario final
	 * @param int $reporte Reporte en el módulo de gráficas y resumenes
	 * @param string $modulo Módulo consultado en gráficas y resumenes
	 * @access public
	 */		
	function RegistrarFrontend($reporte = 0,$modulo=''){

		$id_depto = (isset($_POST["id_depto"])) ? $_POST["id_depto"] : array();

		$id_mun = "";
		if (isset($_POST["id_muns"])){
			$id_mun = $_POST["id_muns"];

			$id_mun = implode("|",$id_mun);
		}

		$id_depto = implode("|",$id_depto);

		$f = 0;
		$fil = '';
		$variables_excluir = array("accion","submit","nombre","s","donde","buscar_donante","borrar_p","dontantes");
		foreach ($_POST as $filtro => $valor) {

			if (!in_array($filtro,$variables_excluir)){
				if (is_array($valor))	$valor = implode(",",$valor);

				if ($valor != ""){
					($f == 0) ?  $fil = "$filtro=$valor" : $fil .= "|$filtro=$valor"; 
					$f++;
				}
			}
		}

		//No para Rubas
		if ($this->id_usuario != 2){
			$sql =  "INSERT INTO $this->tabla (ID_USUARIO,m_e,fecha,id_depto,id_mun,filtros,reporte,modulo) 
				VALUES ($this->id_usuario,'$this->m_e',now(),'$id_depto','$id_mun','$fil','$reporte','$modulo')";

			$this->conn->Execute($sql);

		}
		//die($sql);
	}	

	function RegistrarFrontendMapa($id_depto,$caso){

		$id_mun = '';
		$fil = '';
		$reporte = 0;
		$modulo = $caso;
		if ($id_depto == 0)	$id_depto = '';

		//No para Rubas
		if ($this->id_usuario != 2){
			$sql =  "INSERT INTO $this->tabla (ID_USUARIO,m_e,fecha,id_depto,id_mun,filtros,reporte,modulo) 
				VALUES ($this->id_usuario,'$this->m_e',now(),'$id_depto','$id_mun','$fil','$reporte','$modulo')";

			$this->conn->Execute($sql);

		}
		//die($sql);
	}	


	/**
	 * Retorna el max ID
	 * @access public
	 * @return int
	 */
	function GetMaxID(){
		$sql = "SELECT max($this->columna_id) as maxid FROM ".$this->tabla."_admin";
		$rs = $this->conn->OpenRecordset($sql);
		if($row_rs = $this->conn->FetchRow($rs)){
			return $row_rs[0];
		}
		else{
			return 0;
		}
	}	

	/**
	 * Registra el ID en el Log cuando la accion es insertar
	 * @access public
	 */		
	function setNewID($id){
		$id_l = $this->GetMaxID();

		$sql = "UPDATE ".$this->tabla."_admin SET id = $id WHERE id_log_usuario = $id_l";
		//		die($sql);
		$this->conn->Execute($sql);
	}

	/**
	 * Registra la sentencia SQL en el Log
	 * @access public
	 */		
	function setNombre($nombre){
		$id_l = $this->GetMaxID();

		$sql = "UPDATE ".$this->tabla."_admin SET nombre = '$nombre' WHERE id_log_usuario = $id_l";
		$this->conn->Execute($sql);
	}	

	/**
	 * Registra una accion el un log fisico
	 * @access public
	 * @param $dir Directorio del log, relativo a sissh/logs/
	 * @param $string Texto a registrar
	 */		
	function insertarLogFisico($dir,$string=''){

		include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/common/archivo.class.php");

		$archivo = new Archivo();
		$path_log_dir = $_SERVER["DOCUMENT_ROOT"]."/sissh/_sgol/$dir";
		$hoy = getdate();

		$hora = $hoy["hours"].":".$hoy["minutes"].":".$hoy["seconds"];

		$linea = "\n".$hora."|".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']."|".$string;

		//El log se maneja mensualmente
		$archivo_log = $hoy["mday"]."_".$hoy["mon"]."_".$hoy["year"];

		$fp = $archivo->Abrir("$path_log_dir/$archivo_log","a+");
		$archivo->Escribir($fp,$linea);
		$archivo->Cerrar($fp);	
	}	
}

?>
