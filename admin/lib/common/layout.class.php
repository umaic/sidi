<?
/**
 * Layaout para modulo de administracion
 *
 * Contiene los métodos de la clase Layout
 * @author Ruben A. Rojas C.
 */

Class Layout {

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
	function Layout (){
	}

	/**
	* Muestra la parrilla de registros para Administracion
	* @access public
	* @param string $cols Nombre de las columnas a mostrar, separadas por ,
	* @param array $cols_rel Arreglo asociativo detallando las columnas relacionadas a mostrar, las cuales necesitan una consulta extra
	* @param array $options Opciones extras, checkForeingKeys
	*/	
	function adminGrid($cols,$cols_rel=array(),$options=array('checkForeignKeys' => true, 'link_new' => true, 'link_edit' => true)){
		// Formato de $cols_rel
		/* array('nombre de la propiedad relacional en el VO' => array ( 'nombre de la columna de la tabla, por si es diferente al key' => ''
															'nombre de la clase DAO' => '', 
															'nombre de la propiedad del VO a mostrar' => '',
															'titulo de la columna para el header' => ',
															'filtro, boolean que determina si se debe colocar un filtro por la columna' => true/false
															)
									); */
		
		// DEFINICION DE VARIABLES
		$index_parser = 'index_parser.php';

		//Viene en el url, dado por addTab en js/ext-3/tabs-adv.js
		$class = $_GET['class'];
		$m_e = $_GET['m_e'];

		// DAO del modulo
		$dao = new $class;

		echo "<table align='center' class='tabla_lista' width='700'>";

		// Filtros
		$aplicar_filtros = false;
		foreach ($cols_rel as $col => $info){
			if ($info['filtro']){
				
				// Valor del filtro
				$valor_filtro = '';
				$condicion = '';
				
				if (isset($_GET[$col]) AND $_GET[$col] != ''){
					$valor_filtro = $_GET[$col];

					//si se envia el nombre de la columna de la tabla, si no es el mismo nombre de la propiedad del VO
					$condicion = (isset($info['tabla_columna'])) ? $info['tabla_columna']." = '$valor_filtro'" : "$col = '$valor_filtro'";
				}

				// Recalcula los VOs
				$vos = $dao->GetAllArray($condicion,'','');

				$dao_rel = new $info['dao'];
				echo "<tr><td colspan='2'><b>Filtrar por ".ucfirst($info['titulo'])."</b>:&nbsp;<select class='select' onchange=\"refreshTab('$index_parser?m_e=$m_e&accion=listar&class=$class&method=ListarTabla&$col='+this.value)\">";
				echo "<option value=''>Todos</option>";
				$dao_rel->ListarCombo('combo',$valor_filtro,'');	
				echo "</select></td></tr>";

				$aplicar_filtros = true;
			}
		}

		//Vos, los consulta si no se han aplicado filtros
		if (!$aplicar_filtros){
			$vos = $dao->GetAllArray('','','');
		}
		
		$this->adminGridLayout($vos,$cols,$cols_rel,$options);

		echo "</table>";
	}

	/**
	* Muestra solo el listado de registros para una Grid
	* @access public
	* @param array $vos Arreglo de VOs a mostrar
	* @param string $cols Nombre de las columnas a mostrar, separadas por ,
	* @param array $cols_rel Arreglo asociativo detallando las columnas relacionadas a mostrar, las cuales necesitan una consulta extra
	* @param array $options Opciones extras, checkForeingKeys
	*/	
	function adminGridLayout($vos,$cols,$cols_rel,$options){
	
		$m_e = $_GET['m_e'];
		$class = $_GET['class'];
		$num_arr = count($vos);
		
		// DAO del modulo
		$dao = new $class;
		
		// Columnas a mostrar en la parrilla
		$num_cols = count($cols) + count($cols_rel) + 1;

		// Titulos para cada modulo
		$titulo = array(
						'pais'                 => 'Pais',
						'depto'                => 'Departamento',
						'municipio'            => 'Municipio',
						'poblado'              => 'Poblado',
						'comuna'               => 'Comuna',
						'barrio'               => 'Barrio',
						'resguardo'            => 'Resguardo',
						'depto'                => 'Departamento',
						'moneda'               => 'Moneda',
						'estado_proyecto'      => 'Estado de Proyecto',
						'tema'                 => 'Tema',
						'clasificacion'        => 'Clasificaci&oacute;n',
						'usuario'              => 'Usuario',
						'tipo_usuario'         => 'Tipos de usuario',
						'actor'                => 'Actor',
						'subfuente_evento_c'   => 'Sub Fuente',
						'fuente_evento_c'      => 'Fuente',
						'subcat_evento_c'      => 'Sub Categoria',
						'cat_evento_c'         => 'Categoria',
						'sub_etnia'            => 'Sub Etnia',
						'etnia'                => 'Etnia',
						'ocupacion'            => 'Ocupacione',
						'edad'                 => 'Edad',
						'rango_edad'           => 'Rango de Edad',
						'sub_condicion'        => 'Sub Condición',
						'condicion_mina'       => 'Condicion',
						'sexo'                 => 'Sexo',
						'estado_mina'          => 'Estado',
						'periodo'              => 'Periodo',
						'tipo_desplazamiento'  => 'Tipo',
						'clase_desplazamiento' => 'Clase',
						'fuente'               => 'Fuente',
						'tipo_org'             => 'Tipo',
						'enfoque'              => 'Enfoque',
                        'emergencia'           => 'Emergencia',
						'cat_d_s'              => 'Categoria',
						'u_d_s'                => 'Unidad',
						'contacto_d_s'         => 'Fuente',
						'dato_sectorial'       => 'Dato Sectorial',
						'perfil_usuario'       => 'Perfil Usuario',
						'modulo'               => 'Modulo',
						'sector'               => 'Sector',
						'poblacion'            => 'Poblaci&oacute;n',
						'sugerencia'           => 'Sugerencia',
						'info_ficha'           => 'Ficha',
						'espacio'              => 'Espacio',
						'contacto_col'         => 'Caracter&iacute;stica',
						'contacto_col_op'      => 'Opcion',
						'espacio_usuario'      => 'Acceso',
						'log_admin'            => 'LOG Admin',
						'log_consulta'         => 'LOG Consulta',
						'tipo_evento'          => 'Tipo de Evento',
						'riesgo_hum'           => 'Riesgo Humanitario',
						'cons_hum'             => 'Consecuencia Humanitaria',
						'unicef_funcionario'   => 'Funcionario UNICEF',
						'unicef_indicador'     => 'Indicador',
						'unicef_fuente_pba'    => 'Fuente',
						'unicef_socio'         => 'Socio',
						'unicef_donante'       => 'Donante',
					   );
		
		if (!isset($options['link_new']) || $options['link_new']){
			echo "<tr>
				<td width='110'><img src='/sissh/admin/images/home/insertar.gif' width='16' height='16'>&nbsp;<a href='#' onclick=\"addWindowIU('$m_e','insertar','');return false;\">Crear</a></td>
				<td colspan='$num_cols' align='right'>[$num_arr Registros]</td>
			</tr>";
		}
		
		echo "<tr class='titulo_lista'>
		<td>ID</td>";

		// Columnas directas
		foreach ($cols as $c => $col){
			echo "<td ";
			if (isset($col['width']))	echo " width='".$col['width']."' ";
			echo ">".ucfirst($col['titulo'])."</td>";
		}

		// Coloca el apuntador al comienzo del arreglo
		reset($cols);
		$nombre = key($cols);

		// Columnas relacionadas
		foreach ($cols_rel as $info){
			echo "<td ";
			if (isset($info['width']))	echo " width=\"".$info['width']."\" ";
			echo ">".ucfirst($info['titulo'])."</td>";
		}

	    echo "</tr>";

		foreach ($vos as $p => $vo){

			echo "<tr class='fila_lista'>";
			echo "<td>";
			
			//define si es el o la.. jejeje
			$el_la = (in_array($titulo[$m_e][strlen($titulo[$m_e]) - 1],array('a','n','e','d'))) ? 'la' : 'el';

			//Check llaves foraneas
			if (!$options['checkForeignKeys'] || ($options['checkForeignKeys'] AND !$dao->checkForeignKeys($vo->id)))
				echo "<a href='#'  onclick=\"if(confirm('Está seguro que desea borrar $el_la ".$titulo[$m_e].": ".$vo->$nombre."')){borrarRegistro('".$class."','".$vo->id."')}else{return false};\"><img src='/sissh/admin/images/trash.gif' border='0' title='Borrar' /></a>&nbsp;";
			
			echo $vo->id."</td>";

			// Columnas directas
			$i = 0;
			foreach ($cols as $c => $col){
				
				if ($i == 0){
					echo "<td>";
					if (!isset($options['link_edit']) || $options['link_edit']){
						echo "<a href='#' onclick=\"addWindowIU('$m_e','actualizar','".$vo->id."');return false;\">".$vo->$c."</a>";
					}
					else{
						echo $vo->$c;
					}
					
					echo "</td>";
				}	
				else{
					echo "<td>".$vo->$c."</td>";		
				}

				$i++;	
			}

			// Columnas relacionadas
			foreach ($cols_rel as $id_col => $info){

				$dao_rel = new $info['dao'];
				$vo_rel = $dao_rel->Get($vo->$id_col);

				echo "<td>".$vo_rel->{$info['nom']}."</td>";
			}
		}

	}
}

?>
