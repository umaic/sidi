<?
/**
 * DAO de Ajax
 *
 * Contiene los métodos de la clase Ajax
 * @author Ruben A. Rojas C.
 */

Class AjaxDAO {

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
	function AjaxDAO (){
        $dir = $_SERVER["DOCUMENT_ROOT"].dirname($_SERVER['SCRIPT_NAME']);
		require_once $dir."/lib/common/mysqldb.class.php";
		$this->conn = MysqlDb::getInstance();
	}

	/**
	* Grafica datos de Enfermedades
	* @access public
	* @param int $case
	* @param int $id_dato
	* @param int $click_state
	*/
	function graficar($case,$id_dato,$click_state){
		switch ($case){
			case 'enfermedades';
				require_once "lib/common/graphic.class.php";
				require_once "lib/dao/dato_sectorial.class.php";
				require_once "lib/model/dato_sectorial.class.php";

				$PG = new PowerGraphic;
				$dato_dao = New DatoSectorialDAO();

				$datos = $_SESSION["id_datos_click"];
				$valor_nacional = $_SESSION["valor_nacional"];
				$valor = $_SESSION["valor"];
				$ubicacion = $_SESSION["ubicacion"];

				if ($click_state == 'true')	array_push($datos,$id_dato);
				else 						$datos = array_values(array_diff($datos,array($id_dato)));

				$_SESSION["id_datos_click"] = $datos;

				$PG->title = $_SESSION['title'];
				$PG->axis_y    = 'Casos presentados';
				$PG->skin      = 1;
				$PG->type      = 2;
				$PG->credits   = 0;
				$PG->graphic_1 = 'Nacional';

				if ($ubicacion != "")	$PG->graphic_2 = $ubicacion;

				$d = 0;
				foreach($datos as $id_dato){
					$dato = $dato_dao->Get($id_dato);

					$PG->x[$d] = $dato->nombre;
					$PG->y[$d] = $valor_nacional[$id_dato];
					if (isset($valor[$id_dato]))	$PG->z[$d] = $valor[$id_dato];
					$d++;
				}
				echo '<img src="admin/lib/common/graphic.class.php?' . $PG->create_query_string() . '" border="1" alt="" /> <br /><br />';
			break;
		}
	}


	/**
	* Adiciona un dato sectorial a la tabla de resumen
	* @access public
	* @param int $id_cat
	* @param int $id_dato
	* @param int $f_ini
	* @param int $f_fin
	* @param int $accion
	*/
	function agregarDatoResumen($id_cat,$id_dato,$f_ini,$f_fin,$accion){

		require_once "lib/common/archivo.class.php";
		require_once "lib/dao/dato_sectorial.class.php";
		require_once "lib/dao/cat_d_s.class.php";
		require_once "lib/dao/contacto.class.php";
		require_once "lib/dao/depto.class.php";
		require_once "lib/dao/municipio.class.php";
		require_once "lib/model/dato_sectorial.class.php";
		require_once "lib/model/cat_d_s.class.php";
		require_once "lib/model/contacto.class.php";
		require_once "lib/model/depto.class.php";
		require_once "lib/model/municipio.class.php";

		$cat_dao = New CategoriaDatoSectorDAO();
		$dato_dao = New DatoSectorialDAO();
		$contacto_dao = New ContactoDAO();
		$depto_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();

		$cats = $_SESSION["id_cats_resumen"];
		$datos = $_SESSION["id_datos_resumen"];
		$id_ubicacion = $_SESSION["id_ubicacion"];
		$dato_para = $_SESSION["dato_para"];

		$ubicacion = "";
		if ($dato_para != 3){
			if ($dato_para == 1){
				$depto = $depto_dao->Get($id_ubicacion);
				$ubicacion = $depto->nombre;
			}
			else if ($dato_para == 2){
				$mun = $mun_dao->Get($id_ubicacion);
				$ubicacion = $mun->nombre;
			}
		}

		$file = New Archivo();
		$nom_archivo_tipo = '../consulta/csv/reporte_sissh.txt';

		$fp = $file->Abrir($nom_archivo_tipo,'w');

		if ($accion == 'add'){
			if (!in_array($id_cat,$cats)){
				array_push($cats,$id_cat);
				$datos[$id_cat] = array();
			}
			if (!in_array($id_dato,$datos[$id_cat]))	array_push($datos[$id_cat],$id_dato);

		}
		else{
			$datos[$id_cat] = array_values(array_diff($datos[$id_cat],array($id_dato)));

			//ELIMINA LA CATEGORIA
			if (count($datos[$id_cat]) == 0){
				$cats = array_values(array_diff($cats,array($id_cat)));
			}
		}

		$_SESSION["id_cats_resumen"] = $cats;
		$_SESSION["id_datos_resumen"] = $datos;

		if (count($datos) > 0){
			echo "<table cellpadding=5 cellspacing=1 class='tabla_dato_resumen'><tr><td><b>Indicadores Sectoriales</b></td>";
			$linea = "Indicadores Sectoriales";
			if ($ubicacion != ''){
				echo "<td><b>$ubicacion</b></td>";
				$linea .= "|$ubicacion";
			}
			echo "<td><b>Nacional</b></td><td><b>Fuente</b></td>";
			$linea .= "|Nacional|Fuente";

			$file->Escribir($fp,"$linea\n");

		}

		foreach ($cats as $id_cat){
			$cat = $cat_dao->Get($id_cat);

			echo "<tr><td><b>$cat->nombre</b></td>";
			$linea = $cat->nombre;

			if ($ubicacion != ''){
				echo "<td></td>";
				$linea .= "|";
			}

			echo "<td></td><td></td></tr>";
			$linea .= "||";

			$file->Escribir($fp,"$linea\n");

			foreach($datos[$id_cat] as $id_dato){

				$dato = $dato_dao->Get($id_dato);
				$fuente = $contacto_dao->Get($dato->id_contacto);

				$val = $dato_dao->GetValorToReport($id_dato,0,$f_ini,$f_fin,3);
				$valor_nacional = $val['valor'];
				if ($valor_nacional == 0){
					$valor_nacional = "No existe dato para el periodo";
				}

				if ($dato_para != 3){
					$val = $dato_dao->GetValorToReport($id_dato,$id_ubicacion,$f_ini,$f_fin,$dato_para);
					$valor = $val['valor'];
					if ($valor == 0){
						$valor = "No existe dato para el periodo";
					}
				}


				echo "<tr><td><a href=\"javascript:getDataV1('eliminarDatoResumen','admin/ajax_data.php?object=datoTablaResumen&accion=delete&id_cat=$id_cat&id_dato=$id_dato&f_ini=$f_ini&f_fin=$f_fin','tabla_resumen')\" title='Eliminar de la tabla'><img src='images/delete.gif' border=0></a>&nbsp;&nbsp;$dato->nombre</td>";
				$linea = "$dato->nombre";

				if ($ubicacion != ''){
					echo "<td align='right'>".$valor."</td>";
					$linea .= "|".$valor;
				}

				echo "<td align='right'>".$valor_nacional."</td>";
				echo "<td>$fuente->nombre</td></tr>";

				$linea .= "|".$valor_nacional."|".$fuente->nombre;

				$file->Escribir($fp,"$linea\n");

			}
		}

		$file->Cerrar($fp);
	}

	/**
	* Crea un ComboBox de datos
	* @access public
	* @param int $case
	* @param int $where
	*/
	function comboGeneral ($case,$where){
		switch ($case){
			case 'DatoSectorial':
				require_once "lib/dao/dato_sectorial.class.php";
				require_once "lib/model/dato_sectorial.class.php";

				$d_s = New DatoSectorialDAO();

				echo "<b>2. Dato</b>&nbsp;<select id='id_dato' name='id_dato'>";
				echo "<option>Seleccione alguno</option>";
				echo $d_s->ListarCombo('combo','',$where);
				echo "</select>
				<input type='button' value='Agregar -->' onclick=\"getDataV1('agregarDatoResumen','admin/ajax_data.php?object=datoTablaResumen&accion=add','tabla_resumen')\">";
			break;
		}
	}

	/**
	* Crea un ComboBox de Datos Sectoriales para la opcion de Resumen en Minificha
	* @access public
	* @param int $id_cat
	*/
	function comboDatoSectorialResumen($id_cat){

		require_once "lib/dao/dato_sectorial.class.php";
		require_once "lib/model/dato_sectorial.class.php";

		$d_s = New DatoSectorialDAO();


		$datos_en_fecha = $d_s->GetAllArray("ID_CATE = $id_cat",'','');

		if (count($datos_en_fecha) > 0){
			echo "<select id='id_dato' name='id_dato' class='select'>";

			foreach($datos_en_fecha as $dato){
				echo "<option value=".$dato->id.">".$dato->nombre."</option>";
			}

			echo "</select>&nbsp;<input type='button' value='Agregar -->' class='boton' onclick=\"if (document.getElementById('f-calendar-field-1').value != '' && document.getElementById('f-calendar-field-2').value != ''){getDataV1('agregarDatoResumen','admin/ajax_data.php?object=datoTablaResumen&accion=add','tabla_resumen');}else{alert('Falta el Periodo');}\">";
		}
		else{
			echo "<b>*** No hay Datos de la Categoria ***</b>";
		}
	}

	/**
	* Muestra las ocurrencias por nombre o sigla de una organizacion
	* @access public
	* @param int $s
	* @param int $case
	*/
	function ocurrenciasOrg($s,$case,$busqueda=0,$donde='comience'){

		require_once "lib/dao/org.class.php";
		require_once "lib/dao/municipio.class.php";
		require_once "lib/model/org.class.php";
		require_once "lib/model/municipio.class.php";

		$org_dao = New OrganizacionDAO();
		$mun_dao = New MunicipioDAO();

		$ok = 0;
		if ($case == 'nombre' && strlen($s) > 1)	$ok = 1;
		else if ($case == 'sigla' && strlen($s) >= 1)	$ok = 1;

		if ($ok == 1){

			if ($case == 'nombre'){
				$donde == 'comience' ? $condicion = "NOM_ORG like '$s%'" : $condicion = "NOM_ORG like '%$s%'";
			}
			else{
				$donde == 'comience' ? $condicion = "SIG_ORG like '$s%'" : $condicion = "SIG_ORG like '%$s%'";
			}

			$path = "/sissh/admin";
			/*if ($_SESSION["cnrr"] == 1){
				$condicion .= ' AND CNRR = 1';
				$path = "/admin";
			}*/

			$orgs = $org_dao->GetAllArray($condicion,'','');
			$num_orgs = count($orgs);

			echo "<table cellpadding=4 cellspacing=1>";

			if ($busqueda == 1)	echo "<tr><td width='300'><b>Hay ".count($orgs)." Organizacion (es) ...</b></td>";
			else{
				if ($num_orgs > 0)	echo "<tr><td width='300'><b>!! LA ORGANIZACION YA EXISTE !!</b></td>";
				else 				echo "<tr><td width='300'><b>Nombre Ok, pulse Cerrar y haga click en siguiente</b></td>";
			}

			echo "<td width='100'></td><td></td><td width='200'></td>";

			if ($busqueda == 1){
				echo "<td align='right'><a href='#' onclick=\"document.getElementById('ocurrenciasOrg').style.display = 'none';document.getElementById('table_filtros').style.display = '';return false;\">Cerrar</a></div>";
			}
			else{
				if ($num_orgs == 0){
					echo "<td align='right'><a href='#' onclick=\"document.getElementById('ocurrenciasOrg').style.display = 'none';return false;\">Cerrar</a></div>";
				}
			}
			echo "</tr>";

			if ($num_orgs > 0){
				echo "<tr class='titulo_ocurrenciasOrg'><td><b>NOMBRE</td><td><b>SIGLA</td><td><b>SEDE</td><td><b>PERTENECE A</td></tr>";
			}

			foreach ($orgs as $org){
				//ORG. A LA QUE PERTENECE
				$id_papa = $org->id_papa;
				if ($id_papa != 0 ){
					$org_papa = $org_dao->Get($id_papa);
				}
				else{
					$org_papa->nom = "-";
				}

				//MUN. SEDE
				$nom_sede = "";
				if ($org->id_mun_sede != ""){
					$mun_sede = $mun_dao->Get($org->id_mun_sede);
					$nom_sede = $mun_sede->nombre;
				}

				echo "<tr class='fila_ocurrenciasOrg'>";

				echo "<td>$org->nom</td>";
				echo "<td>$org->sig</td>";
				echo "<td>$nom_sede</td>";
				echo "<td>$org_papa->nom</td>";



				echo "<td width='70'>[ <a href='#' onclick=\"window.open('$path/ver.php?class=OrganizacionDAO&method=Ver&param=$org->id','','top=30,left=30,height=900,width=900,scrollbars=1');return false;\">Ver</a> ]";

				//Link editar org
				if (isset($_SESSION["mapp_oea"]) && $_SESSION["mapp_oea"] == 1){
					$link_edit = "/sissh/t/index_mo.php?m_e=org&accion=actualizar_mo&id=$org->id";
				}
				else{
					$link_edit = "$path/index.php?accion=actualizar&id=$org->id";
				}
				if ($busqueda == 0)	echo "<br>[ <a href='$link_edit'>Editar</a> ]<br>";
				echo "</td>";

				echo "</tr>";
			}
		}
	}
	
    /**
	* Muestra las ocurrencias de Orgs en 4w
	* @access public
	* @param string $s
	* @param string $inom Id of input text DOM element
	* @param string $iid Id of input  id DOM element
	* @param string $inner Results Div Id
	*/
	function ocurrenciasOrg4wA($s, $inom, $iid, $inner) {

		require_once "lib/dao/org.class.php";
		require_once "lib/dao/municipio.class.php";
		require_once "lib/model/org.class.php";
		require_once "lib/model/municipio.class.php";

		$org_dao = New OrganizacionDAO();
		$mun_dao = New MunicipioDAO();

        $condicion = "nom_org LIKE '%$s%' OR sig_org LIKE '%$s%'";
        $path = "/sissh/admin";

        $orgs = $org_dao->GetAllArray($condicion,'','');
        $num_orgs = count($orgs);

        ?>
        <div>
			<tr><td width='300'><b>Hay <?php echo count($orgs) ?> Organizacion (es) ...</b></td>
        </div>
        <div class="ocurr_4w_list">
            <table>
        <?php

        if ($num_orgs > 0){
            echo "<tr><th><b>NOMBRE</th><th><b>SEDE</th></tr>";
        }

        foreach ($orgs as $org){

            //MUN. SEDE
            $nom_sede = "";
            if ($org->id_mun_sede != ""){
                $mun_sede = $mun_dao->Get($org->id_mun_sede);
                $nom_sede = $mun_sede->nombre;
            }

            echo "<tr class='fila_ocurrenciasOrg'>";

            if (!empty($iid)) {
                echo "<td><a href='#' onclick='setValuesOcurr($org->id, \"$iid\", \"$org->nom\", \"$inom\", \"$inner\"); return false;'>$org->nom</a></td>";
                echo "<td><a href='#' onclick='setValuesOcurr($org->id, \"$iid\", \"$org->nom\", \"$inom\", \"$inner\"); return false;'>$nom_sede</a></td>";
            }
            else {
                echo "<td>$org->nom</td>";
                echo "<td>$nom_sede</td>";
            }

            echo "</tr>";
        }
	}
    
    /**
	* Muestra las ocurrencias de Contactos en 4w
	* @access public
	* @param string $s
	* @param string $inom Id of input text DOM element
	* @param string $iid Id of input  id DOM element
	* @param string $inner Results Div Id
	*/
	function ocurrenciasCon4wA($s, $inom, $iid, $inner) {

		require_once "lib/dao/contacto.class.php";
		require_once "lib/model/contacto.class.php";

		$con_dao = New ContactoDAO();
        $cons = array();

        $sql = "SELECT id_con, CONCAT(nom_con,' ',ape_con) AS nom_con, tel_con, sig_org, email_con
                FROM contacto 
                LEFT JOIN contacto_org USING(id_con)
                LEFT JOIN organizacion USING(id_org)
                WHERE CONCAT(nom_con,' ',ape_con) LIKE '%$s%' OR email_con LIKE '%$s%'";
        $rs = $this->conn->OpenRecordset($sql);
        while ($row = $this->conn->FetchObject($rs)) {
            $cons[] = $row;
        }

        $num_cons = count($cons);

        ?>
        <div>
			<tr><td width='300'><b>Hay <?php echo count($cons) ?> Contactos ...</b></td>
        </div>
        <div class="ocurr_4w_list">
            <table>
        <?php

        if ($num_cons > 0){
            $r = "<tr><th><b>Nombre</th><th>Email</th><th>Tel</th><th><b>ORG</th></tr>";
        }

        foreach ($cons as $con){

            $r .= "<tr class='fila_ocurrenciasOrg' onclick='setValuesOcurr($con->id_con, \"$iid\", \"".htmlentities($con->nom_con, ENT_QUOTES)."\", \"$inom\", \"$inner\"); return false;'>";

            $r .= "<td><a href='#'>$con->nom_con</a></td>";
            $r .= "<td><a href='#'>$con->email_con</a></td>";
            $r .= "<td><a href='#'>$con->tel_con</a></td>";
            $r .= "<td><a href='#'>$con->sig_org</a></td>";
            $r .= "</tr>";
        }


        echo $r;
	}

	/**
	* Muestra las ocurrencias por nombre o sigla de una organizacion para mapas-mapserver
	* @access public
	* @param int $s
	* @param int $case
	*/
	function ocurrenciasOrgMapa($s,$case,$busqueda=0,$donde='comience'){

		require_once "lib/dao/org.class.php";
		require_once "lib/dao/municipio.class.php";
		require_once "lib/model/org.class.php";
		require_once "lib/model/municipio.class.php";

		$org_dao = New OrganizacionDAO();
		$mun_dao = New MunicipioDAO();

		$ok = 0;
		if ($case == 'nombre' && strlen($s) > 1)	$ok = 1;
		else if ($case == 'sigla' && strlen($s) >= 1)	$ok = 1;

		if ($ok == 1){

			if ($case == 'nombre'){
				$donde == 'comience' ? $condicion = "NOM_ORG like '$s%'" : $condicion = "NOM_ORG like '%$s%'";
			}
			else{
				$donde == 'comience' ? $condicion = "SIG_ORG like '$s%'" : $condicion = "SIG_ORG like '%$s%'";
			}
			
			$path = "/sissh/admin";
			/*if ($_SESSION["cnrr"] == 1){
				$condicion .= ' AND CNRR = 1';
				$path = "/admin";
			}*/

			$orgs = $org_dao->GetAllArray($condicion,'','');
			$num_orgs = count($orgs);

			echo "<table cellpadding=2 cellspacing=1>";

			if ($busqueda == 1)	echo "<tr><td colspan=2><b>Hay ".count($orgs)." Organizacion (es) ...</b></td>";

			if ($busqueda == 1){
				echo "<td align='right'><a href='#' onclick=\"cerrarDivOcurrenciasOrg();return false;\">Cerrar</a></div>";
			}
			echo "</tr>";

			if ($num_orgs > 0){
				echo "<tr class='titulo_ocurrenciasOrg'><td>&nbsp</td><td width='200'><b>NOMBRE</td><td width='60'><b>SIGLA</td</tr>";
			}

			foreach ($orgs as $org){
				//ORG. A LA QUE PERTENECE
				$id_papa = $org->id_papa;
				if ($id_papa != 0 ){
					$org_papa = $org_dao->Get($id_papa);
				}
				else{
					$org_papa->nom = "-";
				}

				//MUN. SEDE
				$mun_sede = "";
				if ($org->id_mun_sede != "")
				$mun_sede = $mun_dao->Get($org->id_mun_sede);

				echo "<tr class='fila_ocurrenciasOrg'>";

				echo "<td><input type='radio' value='".$org->id."' id='id_org' name='id_org'></td>";
				echo "<td>$org->nom</td>";
				echo "<td>$org->sig</td>";

				echo "</tr>";
				
			}
		}

	}	
    
    /**
	* Muestra las ocurrencias de Orgs usando Jquery + Json
	* @access public
	* @param string $s
    *
    * @return string $json
	*/
	function ocurrenciasOrgJson($s) {

		require_once "lib/dao/org.class.php";
		require_once "lib/dao/municipio.class.php";
		require_once "lib/model/org.class.php";
		require_once "lib/model/municipio.class.php";

		$org_dao = New OrganizacionDAO();
		$mun_dao = New MunicipioDAO();

        $condicion = "nom_org LIKE '$s%' OR sig_org LIKE '%$s%'";

        $orgs = $org_dao->GetAllArrayFields($condicion,'','','id_org,nom_org,sig_org,id_mun_sede');

        $orgs_r = array();
        foreach ($orgs as $org){

            //MUN. SEDE
            $mun = "";
            if ($org[3] != ""){
                $mun_sede = $mun_dao->Get($org[3]);
                $mun = utf8_encode($mun_sede->nombre);
            }
            
            $id = $org[0];
            $nom = utf8_encode($org[1]);
            $sig = (empty($org[2])) ? '' : utf8_encode($org[2]);

            $orgs_r[] = compact('id','nom','sig','mun');  
        }

        return json_encode($orgs_r);
    }

	/**
	* //Lista los tipos, enfoques, poblaciones o sectores (teps)
	* @access public
	* @param string $graficar_por Tipo o Enfoque o Poblacion o Sector
	*/
	function teps($graficar_por){
		switch ($graficar_por){
			case 'tipo':
				//LIBRERIAS
				require_once "lib/dao/tipo_org.class.php";
				require_once "lib/model/tipo_org.class.php";

				$dao = New TipoOrganizacionDAO();

				echo "<select id='filtro_graficar_por' name='filtro_graficar_por' class='select'>";
				echo "<option value=''>[ Seleccione ".ucfirst($graficar_por)."]</option>";
				$dao->ListarCombo('combo','','');
				echo "</select>";

			break;
			case 'enfoque':
				//LIBRERIAS
				require_once "lib/dao/enfoque.class.php";
				require_once "lib/model/enfoque.class.php";

				$dao = New EnfoqueDAO();

				echo "<select id='filtro_graficar_por' name='filtro_graficar_por' class='select'>";
				echo "<option value=''>[ Seleccione ".ucfirst($graficar_por)."]</option>";
				$dao->ListarCombo('combo','','');
				echo "</select>";

			break;
			case 'poblacion':
				//LIBRERIAS
				require_once "lib/dao/poblacion.class.php";
				require_once "lib/model/poblacion.class.php";

				$dao = New PoblacionDAO();

				echo "<select id='filtro_graficar_por' name='filtro_graficar_por' class='select'>";
				echo "<option value=''>[ Seleccione ".ucfirst($graficar_por)."]</option>";
				$dao->ListarCombo('combo','','');
				echo "</select>";

			break;
			case 'sector':
				//LIBRERIAS
				require_once "lib/dao/sector.class.php";
				require_once "lib/model/sector.class.php";

				$dao = New SectorDAO();

				echo "<select id='filtro_graficar_por' name='filtro_graficar_por' class='select'>";
				echo "<option value=''>[ Seleccione ".ucfirst($graficar_por)."]</option>";
				$dao->ListarCombo('combo','','');
				echo "</select>";

			break;
		}
	}
    
    /**
	* Crea una opcion en el formulario de insertar contacto
	* @access public
	* @param int $id_col ID de de contacto_col
    * @param string $val Valor del nuevo contacto_col_opcion
    *
    * @return int $id_col_opcion ID de la nueva opcion
	*/
	function crearContactoColOpcion($id_col,$val){
		require_once "lib/model/contacto_col_op.class.php";
		require_once "lib/dao/contacto_col_op.class.php";
        
        $vo = New ContactoColOp();
        $dao = New ContactoColOpAjax();
        
        $vo->id_contacto_col = $id_col;
        $vo->nombre = $val;

        return $dao->Insertar($vo);
    }
}
