<?
session_start();

//Si viene de la opcion showAllInfo, solo muestra el contenido de la sesion
if (isset($_GET["show_all_info"])){
	die("<table align='right'><tr><td align='right' style='background-color:#f1f1f1'>[ <a href='#' onclick=\"document.getElementById('div_all_info').style.display='none'\">Cerrar</a> ]&nbsp;&nbsp;</td></tr></table><br>".$_SESSION["html_info_mapserver"]);
}

include("../admin/lib/common/mysqldb.class.php");	
include("../admin/lib/common/postgresdb.class.php");	
include("../admin/lib/common/mapserver.class.php");
include("../admin/lib/common/cadena.class.php");

include("../admin/lib/dao/cat_d_s.class.php");
include("../admin/lib/dao/contacto.class.php");
include("../admin/lib/dao/dato_sectorial.class.php");
include("../admin/lib/dao/depto.class.php");
include("../admin/lib/dao/municipio.class.php");
include("../admin/lib/dao/desplazamiento.class.php");
include("../admin/lib/dao/clase_desplazamiento.class.php");
include("../admin/lib/dao/tipo_desplazamiento.class.php");
include("../admin/lib/dao/fuente.class.php");
include("../admin/lib/dao/periodo.class.php");
include("../admin/lib/dao/u_d_s.class.php");
include("../admin/lib/dao/org.class.php");
include("../admin/lib/dao/tipo_org.class.php");
include("../admin/lib/dao/sector.class.php");
include("../admin/lib/dao/enfoque.class.php");
include("../admin/lib/dao/poblacion.class.php");
include("../admin/lib/dao/evento_c.class.php");
include("../admin/lib/dao/cat_evento_c.class.php");
include("../admin/lib/dao/subcat_evento_c.class.php");
include("../admin/lib/dao/mina.class.php");
include("../admin/lib/dao/proyecto.class.php");
include("../admin/lib/dao/moneda.class.php");
include("../admin/lib/dao/estado_proyecto.class.php");
include("../admin/lib/dao/tipo_vinculorgpro.class.php");
include("../admin/lib/dao/tema.class.php");

include("../admin/lib/model/cat_d_s.class.php");
include("../admin/lib/model/contacto.class.php");
include("../admin/lib/model/dato_sectorial.class.php");
include("../admin/lib/model/depto.class.php");
include("../admin/lib/model/municipio.class.php");
include("../admin/lib/model/desplazamiento.class.php");
include("../admin/lib/model/clase_desplazamiento.class.php");
include("../admin/lib/model/tipo_desplazamiento.class.php");
include("../admin/lib/model/fuente.class.php");
include("../admin/lib/model/periodo.class.php");
include("../admin/lib/model/u_d_s.class.php");
include("../admin/lib/model/org.class.php");
include("../admin/lib/model/tipo_org.class.php");
include("../admin/lib/model/sector.class.php");
include("../admin/lib/model/enfoque.class.php");
include("../admin/lib/model/poblacion.class.php");
include("../admin/lib/model/evento_c.class.php");
include("../admin/lib/model/cat_evento_c.class.php");
include("../admin/lib/model/subcat_evento_c.class.php");
include("../admin/lib/model/mina.class.php");
include("../admin/lib/model/proyecto.class.php");
include("../admin/lib/model/moneda.class.php");
include("../admin/lib/model/estado_proyecto.class.php");
include("../admin/lib/model/tipo_vinculorgpro.class.php");
include("../admin/lib/model/tema.class.php");

//INICIALIZACION DE VARIABLES
$pgConn = New PgDBConn(); 
$myConn = MysqlDb::getInstance(); 
$mapserver = New Mapserver();
$desplazamiento_dao = New DesplazamientoDAO();
$case = $_GET["case"];
$depto_dao = New DeptoDAO();
$mpio_dao = New MunicipioDAO();
$mpios = $mpio_dao->GetAllArrayID("","");
$unidad_dao = New UnidadDatoSectorDAO();
$d_s_dao = New DatoSectorialDAO();
$org_dao = New OrganizacionDAO();
$sector_dao = New SectorDAO();
$tipo_org_dao = New TipoOrganizacionDAO();
$pob_dao = New PoblacionDAO();
$enfoque_dao = New EnfoqueDAO();
$evento_c_dao = New EventoConflictoDAO();
$cat_evento_c_dao = New CatEventoConflictoDAO();
$scat_evento_c_dao = New SubCatEventoConflictoDAO();
$cat_dao = New CategoriaDatoSectorDAO();
$fuente_dao = New ContactoDAO();
$fuente_des_dao = New FuenteDAO();
$tipo_des_dao = New TipoDesplazamientoDAO();
$mina_dao = New MinaDAO();
$proy_dao = New ProyectoDAO();
$tema_dao = New TemaDAO();
$tasa = 0;
$meses = array ("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
$link_reporte_html = "";

//Point
$x = $_GET["x_real"];
$y = $_GET["y_real"];

//$sql = "SELECT codane2,municipio FROM mpio WHERE st_dwithin(the_geom, 'POINT($x $y)',5)";
$sql = "SELECT codane2,municipio FROM mpio WHERE st_dwithin(the_geom, ST_Transform(ST_PointFromText('POINT($x $y)', 32618), 32618),5)";
$rs = $pgConn->OpenRecordset($sql);

if ($pgConn->RowCount($rs) > 0){

	$row = $pgConn->FetchRow($rs);
	
	$id_mpio = $row[0];
	$nombre = $row[1];
	
	$mun_vo = $mpio_dao->Get($id_mpio);
	$id_depto = $mun_vo->id_depto;
	
	switch ($case){
		case 'desplazamiento':
	
			$id_fuente = $_GET["id_fuente"];
			$id_clase = $_GET["id_clase"];
			$id_tipo = $id_tipo = split(",",$_GET["id_tipo"]);
			$id_periodo = $_GET["id_periodo"];
			$id_tipo_periodo = $_GET["id_tipo_periodo"];
			$variacion = $_GET["variacion"];
			$tasa = $_GET["tasa"];
		
			if ($variacion == 1){
				$periodo = split(",",$id_periodo);
				
				//(incidencia periodo corriente-incidencia periodo anterior)/(incidencia periodo anterior)*100= % variación
				
				$p_corr = $periodo[1];
				$p_ant = $periodo[0];
				
				$val_corr = 0;
				$val_ant = 0;
				$corr_null = 1;
				$ant_null = 1;
				foreach($id_tipo as $id_t){
					
					$v = $desplazamiento_dao->getValorToMapa($id_mpio,2,$id_t,$id_clase,$id_fuente,$p_corr,$id_tipo_periodo);
					if (!is_null($v)){
						$val_corr += $v;
						$corr_null = 0;
					}
					
					$v = $desplazamiento_dao->getValorToMapa($id_mpio,2,$id_t,$id_clase,$id_fuente,$p_ant,$id_tipo_periodo);
					if (!is_null($v)){
						$val_ant += $v;
						$ant_null = 0;
					}
					
				}
				
				if ($corr_null == 1 && $ant_null == 1){
					$val = " Variaci&oacute;n: --";
				}
				else{
					
					//Si alguno de los 2 valores es 0, se reemplaza con 0.1 para mostrar variación
					if ($val_corr == 0)	$val_corr = 0.1;
					if ($val_ant == 0)	$val_ant = 0.1;
				
					$val = intval(($val_corr - $val_ant)/($val_ant)*100);
					$val = " Variaci&oacute;n ".$val ." %";
				}
				$unidad_html = "";
				
			}
			else if ($tasa == 1){
					
					$val_despla = 0;
					foreach($id_tipo as $id_t){
						$val_despla += $desplazamiento_dao->getValorToMapa($id_mpio,2,$id_t,$id_clase,$id_fuente,$id_periodo,$id_tipo_periodo);
					}
				
					//CONSULTA EL TOTAL DE LA POBLACION EN EL MISMO PERIODO DEL DATO PARA LA UBIACION
					$id_dato_pob = 3;
					$f_ini = "$id_periodo-1-1";
					$f_fin = "$id_periodo-12-31";
					$val = $d_s_dao->GetValorToReport($id_dato_pob,$id_mpio,$f_ini,$f_fin,2);
					$total_poblacion = $val['valor'];
					
					if ($total_poblacion > 0 && $total_poblacion != "N.D."){
						$val_tasa = intval(($val_despla/$total_poblacion)*100000);
						
						$val = "$val_tasa";
					}
					else{
						$val = " --";
					}
					
					$unidad_html = "";
			}
			else{
				$val = 0;
				foreach($id_tipo as $id_t){
					$val += $desplazamiento_dao->getValorToMapa($id_mpio,2,$id_t,$id_clase,$id_fuente,$id_periodo,$id_tipo_periodo,$variacion);
				}
				$unidad_html = "Personas";
			}
			
			//echo "Municipio:$id_mpio :: Valor = $val<br>";
			$valor = $val;

			$titulo_html = "DESPLAZAMIENTO";
			
			$class_titulo = "titulo_query";
			
		break;
		
		case 'dato_sectorial':
			$d_s_dao = New DatoSectorialDAO();
			$variacion = $_GET["variacion"];
			$tasa = $_GET["tasa"];
			$aaaa = $_GET["aaaa"];
			$id_dato = $_GET["id_dato"];
			
			$dato_vo = $d_s_dao->Get($id_dato);
			
			if ($variacion == 1){
				$periodo = split(",",$aaaa);
				
				//(incidencia periodo corriente-incidencia periodo anterior)/(incidencia periodo anterior)*100= % variación
				
				$a_corr = $periodo[1];
				$f_ini_corr = $a_corr."-1-1";
				$f_fin_corr = $a_corr."-12-31";
		
				$a_ant = $periodo[0];
				$f_ini_ant = $a_ant."-1-1";
				$f_fin_ant = $a_ant."-12-31";
				
				$val_corr = $d_s_dao->GetValorToReport($id_dato,$id_mpio,$f_ini_corr,$f_fin_corr,2);
				$val_ant = $d_s_dao->GetValorToReport($id_dato,$id_mpio,$f_ini_ant,$f_fin_ant,2);
				
				$val_corr = $val_corr['valor'];
				$val_ant = $val_ant['valor'];
				
				if ($val_corr != 'N.D.' && $val_ant > 0 && $val_ant != 'N.D.'){
					$val = ($val_corr - $val_ant)/($val_ant)*100;
					
					if (is_float($val))	$val = number_format($val,2);
					
					$valor = "Variaci&oacute;n ".$val ." %";
					
					
				}
				else{
					$valor = "Variaci&oacute;n: --";
				}
				
				$unidad_html = "";
			}
			else if ($tasa == 1){
				
					$f_ini = $aaaa."-1-1";
					$f_fin = $aaaa."-12-31";
				
					$val = $d_s_dao->GetValorToReport($id_dato,$id_mpio,$f_ini,$f_fin,2);
					$val_d_s = $val['valor'];
					
					//CONSULTA EL TOTAL DE LA POBLACION EN EL MISMO PERIODO DEL DATO PARA LA UBIACION
					$id_dato_pob = 3;
					$val = $d_s_dao->GetValorToReport($id_dato_pob,$id_mpio,$f_ini,$f_fin,2);
					$total_poblacion = $val['valor'];
					
					if ($total_poblacion > 0 && $total_poblacion != "N.D."){
						$val_tasa = intval(($val_d_s/$total_poblacion)*100000);
						
						$valor = $val_tasa;
					}
					
					$unidad_html = "";
			}
			else{
				
				$periodo = split(",",$aaaa);
				$hy = 0;
				$valor = 0;
				foreach ($periodo as $aaaa_t){
					$f_ini = $aaaa_t."-1-1";
					$f_fin = $aaaa_t."-12-31";
					
					$val = $d_s_dao->GetValorToReport($id_dato,$id_mpio,$f_ini,$f_fin,2);
					
					if ($val['valor'] != 'N.D.'){
						$valor += $val['valor'];
						$hy = 1;
					}
					
				}
				
				//APLICA FORMATO
				$id_unidad = $val['id_unidad'];
				$valor = $d_s_dao->formatValor($id_unidad,$valor,0);
				
				if ($hy == 1){
					
					if ($id_unidad != 4 && $id_unidad != 9){
						$unidad_vo = $unidad_dao->Get($id_unidad);
						$unidad_html = $unidad_vo->nombre;
					}
					else{
						$unidad_html = "";
					}
				
				}
				else{
					$unidad_html = "";
					$valor = "No Definido";
				}
			}
			
			$titulo_html = strtoupper($dato_vo->nombre);
			$class_titulo = "titulo_query_12";
			
		break;
		
	case 'org':
		
		$caso = 'municipio';
		$id = 0;

		if ($_GET["id_tipo"] != ''){
			$id = $_GET["id_tipo"];
			$caso = 'tipo';
			$vo = $tipo_org_dao->Get($id);
		}
		
  		if ($_GET["id_sector"] != ''){
			$id = $_GET["id_sector"];
			$caso = 'sector';
			$vo = $sector_dao->Get($id);
		}
		
  		if ($_GET["id_pob"] != ''){
			$id = $_GET["id_pob"];
			$caso = 'poblacion';
			$vo = $pob_dao->Get($id);
		}
		
  		if ($_GET["id_enfoque"] != ''){
			$id = $_GET["id_enfoque"];
			$caso = 'enfoque';
			$vo = $enfoque_dao->Get($id);
		}
		
		$una_org = 0;
  		if ($_GET["id_org"] != ''){
			$id_org = $_GET["id_org"];
			$una_org = 1;
			$caso = "1 Org";
		}
		
		if ($una_org == 0){
			$valor_t = $org_dao->numOrgsConteo($caso,$id,0,$id_mpio,'','');
			$val = $valor_t['total'];
			$titulo_html = "Presencia Organizaciones";

			//Caso nacional
			if ($caso != 'municipio')	$titulo_html .= '<br> '.ucfirst($caso).': '.$vo->nombre_es;
			
			$valor = $val;
			$unidad_html = "Orgs";

			if ($caso == 'tipo')	$caso = 'tipo_org';
			$link_reporte_html = "&#187;&nbsp;<a href='#' onclick=\"alert('El reporte se genera en la página principal de SIDIH, es decir, debe minimizar esta ventana para verlo');reporteOrg('$id_depto','$id_mpio','id_".$caso."','$id');\">Generar listado de Orgs</a>";
		}
		else{
			$nom_org = $org_dao->GetName($id_org);
			$titulo_html = $nom_org;
			$valor = "";
			$unidad_html = "";
		}
		
		$class_titulo = "titulo_query_12";
		
	break;
	
	case 'proyecto_undaf':
		
		$una_proy = 0;
		
		$id_filtro = $_GET["id_filtro"];
		$filtro = $_GET["filtro"];
		$un_proy = 0;
		
		switch ($filtro){
			case 'tema':
				$vo = $tema_dao->Get($id_filtro);
				$nom_vo = $vo->nombre;
			break;
			case 'poblacion':
				$vo = $pob_dao->Get($id_filtro);
				$nom_vo = $vo->nombre_es;
			break;
			case 'agencia':
				$vo = $org_dao->Get($id_filtro);
				$nom_vo = $vo->nom;
			break;
		}

		if ($_GET["id_proy"] != ''){
			$id_proy = $_GET["id_proy"];
			$un_proy = 1;
			$caso = "1 Proy";
		}

		if ($un_proy == 0){
			$valor = $proy_dao->numProyectos($filtro,$id_filtro,0,$id_mpio);

			$titulo_html = "Cobertura Proyectos <br> ".ucfirst($filtro).": ".$nom_vo;
			$valor = $valor;
			$unidad_html = "Proyectos";
			$link_reporte_html = "";
		}
		else{
			$nom = $proy_dao->GetName($id_proy);
			$titulo_html = $nom;
			$valor = "";
			$unidad_html = "";
		}
		
		$class_titulo = "titulo_query_12";
		
	break;

	case 'evento_c':

		$reporte = $_GET["reporte"];
		$id_cats = $_GET["id_cats"];
		$meses = array ("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
		
		if ($_GET["id_subcats"] == ''){
			$id_scats = $scat_evento_c_dao->GetAllArrayID("id_cateven IN ($id_cats)");
		}
		else{
			$id_scats = split(",",$_GET["id_subcats"]);
		}
		
		$f_ini = $_GET["f_ini"];
		$f_ini_s = split("[-]",$f_ini);
		
		$f_fin = $_GET["f_fin"];
		$f_fin_s = split("[-]",$f_fin);
		
		$id_actor = 0;  //Por ahora no está en los filtros
		
		$title_reporte = array("","NUMERO DE EVENTOS","NUMERO DE VICTIMAS");
		
		$num = 0;
		foreach ($id_scats as $id_scat){
			if ($reporte == 1){
				$num += $evento_c_dao->numEventosReporte($id_mpio,$id_scat,$id_actor,$f_ini,$f_fin);
			}
			else{
				$num += $evento_c_dao->numVictimasReporte($id_mpio,array("id_mun"=>1,"f_ini"=>$f_ini,"f_fin"=>$f_fin,"id_scat"=>$id_scat));
			}
		}

		$titulo_html = ucwords($title_reporte[$reporte]);
		$valor = $num;
		$unidad_html = ($reporte == 1) ? "Eventos" : "V&iacute;ctimas";
		$class_titulo = "titulo_query_12";
	
		break;
		
	case 'perfil':
		
		//Variables para las funciones
		$id_ubicacion = $id_mpio;
		$dato_para = 2;  //Mpio
		
		ob_start();
		?>
		
		<table cellpadding=0 cellspacing=0>
			<tr>
				<td class='titulo_query' width='350'>PERFIL <?=strtoupper($nombre)?> <font style="font-size:10px;font-weight:normal">(resumido)</font></td>
				<td align='right'>[<a href='#' onclick="document.getElementById('div_info').style.display='none'">Cerrar</a>]&nbsp;&nbsp;
			</tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td colspan=2 align='center' valign='top'>
				<table cellpadding=0 cellspacing=0>
					<tr>
						<td><a href='#' onclick="swapTabs('resumen')"><img id='img_resumen' src='images/mscross/tabs/resumen.gif' border=0></a></td>
						<td><a href='#' onclick="swapTabs('desplazamiento')"><img id='img_desplazamiento' src='images/mscross/tabs/desplazamiento_off.gif' border=0></a></td>
						<td><a href='#' onclick="swapTabs('mina')"><img id='img_mina' src='images/mscross/tabs/mina_off.gif' border=0></a></td>
						<td><a href='#' onclick="swapTabs('irsh')"><img id='img_irsh' src='images/mscross/tabs/irsh_off.gif' border=0></a></td>
						<td><a href='#' onclick="swapTabs('acc_belicas')"><img id='img_acc_belicas' src='images/mscross/tabs/acc_belicas_off.gif' border=0></a></td>
					</tr>
				</table>
		
			</td>
		</tr>
		<tr>
			<td style="background-color:#FFFFFF;height:400px;" colspan=2 valign='top'>
				<table cellpadding=0 cellspacing=0 width=420>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td colspan=2>
							<table id='resumen' cellpadding="5" cellspacing="1" class='tabla_mapserver_perfil' align="center">
								<tr>
									<td><b>Indicadores Sectoriales</b></td>
									<td><b><?=$nombre?></b></td>
									<td><b>Nacional</b></td>
									<td><b>Fuente</b></td>
									<td><b>A&ntilde;o</b></td>
								</tr>
								<?
								$d = 0;
								$id_cate = 0;
								$sql = "SELECT ID_CATE, ID_DATO FROM minificha_datos_resumen ORDER BY ID_CATE";
								$rs = $myConn->OpenRecordset($sql);
								while ($row_rs = $myConn->FetchRow($rs)){
									if ($id_cate != $row_rs[0]){
										$id_cate = $row_rs[0];
									}
					
									$id_datos_resumen[$id_cate][$d] = $row_rs[1];
									$d++;
								}
							
								foreach ($id_datos_resumen as $categoria => $datos_m){
				
								$cat = $cat_dao->Get($categoria);
								echo "<tr><td colspan=5><b>".strtoupper($cat->nombre)."</b></td></tr>";
				
								foreach ($datos_m as $id_dato){
									$dato = $d_s_dao->Get($id_dato);
									$fuente = $fuente_dao->Get($dato->id_contacto);
				
									//CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
									$fecha_val = $d_s_dao->GetMaxFecha($id_dato);
				
									$sumado = 0;
									if ($id_dato == 3)	$sumado = 0;	//POBLACION TOTAL
									if ($id_dato == 9)	$sumado = 2;	//Afiliados Regimen Contributivo Del Sistema General De Seguridad Social En Salud
				
									//VALOR DATO
									$val = $d_s_dao->GetValorToReport($id_dato,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],$dato_para);
									$valor = $val['valor'];
									$id_unidad = $val['id_unidad'];
				
									//APLICA FORMATO
									$valor = $d_s_dao->formatValor($id_unidad,$valor);
				
									//VALOR DATO NACIONAL
									$val = $d_s_dao->GetValorToReport($id_dato,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],3,$sumado);
									$valor_nacional = $val['valor'];
									$id_unidad = $val['id_unidad'];
									
									//APLICA FORMATO
									$valor_nacional = $d_s_dao->formatValor($id_unidad,$valor_nacional);
				
									//FECHA
									$fecha = $d_s_dao->GetMaxFecha($id_dato);
									$a = split("-",$fecha["fin"]);
				
									echo "<tr><td>$dato->nombre</td>";
									echo "<td align='right'>$valor</td>";
									echo "<td align='right'>$valor_nacional</td>";
									echo "<td>$fuente->nombre</td>";
									echo "<td>$a[0]</td></tr>";
								}
							}
							 ?>

				</table>
				
				<!-- DESPLAZAMIENTO -->
				<table id='desplazamiento' cellpadding="5" cellspacing="1" class='tabla_mapserver_perfil' align="center" style="display:none">
					<?
					$fecha = getdate();
					$a_actual = $fecha['year'];
					$a = $a_actual - 1;
										
					$fuentes = $fuente_des_dao->GetAllArray('ID_FUEDES IN (2)');  //Accion Social
					$tipos = $tipo_des_dao->GetAllArray('');
					foreach ($fuentes as $fuente){
						
						$valor_exp = 0;
						foreach ($tipos as $tipo){
							$valor_exp += $desplazamiento_dao->GetValorToReportTotalAAAA(1,$fuente->id,$tipo->id,$a,$id_ubicacion,$dato_para);
						}

						$valor_rec = 0;
						foreach ($tipos as $tipo){
							$valor_rec += $desplazamiento_dao->GetValorToReportTotalAAAA(0,$fuente->id,$tipo->id,$a,$id_ubicacion,$dato_para);
						}
						
						$f_c = $desplazamiento_dao->GetFechaCorte($fuente->id);
						$f_corte = " : $fuente->nombre $f_c";

					}					
					
					?>
					<tr><td><b>RECEPCION</b>: <?=$valor_rec ?> personas</td></tr> 
					<tr><td><b>EXPULSION</b>: <?=$valor_exp ?> personas</td></tr> 
					<tr><td><b>Fuente</b>: Accion Social - <b>Periodo</b>: 1 Enero <?=$a ?> - 31 Dic <?=$a?></td></tr>
				</table>

				<!-- MINA -->
				<table id='mina' cellpadding="5" cellspacing="1" class='tabla_mapserver_perfil' align="center" style="display:none">
					<tr>
						<td style="background-color:#FFFFFF"><b>Valores para: <?=$a ?></b></td>
					</tr>
				
					<?
					//TOTAL
					$condicion = "YEAR(FECHA_REG_EVEN) = '$a'";
					$num_total = $mina_dao->GetValor($condicion,$id_ubicacion,$dato_para);
					
					//CIVIL
					$id_condicion = 2;
					$condicion = "ID_CONDICION = $id_condicion AND YEAR(FECHA_REG_EVEN) = '$a'";
					$num_civil = $mina_dao->GetValor($condicion,$id_ubicacion,$dato_para);
					
					
					//MILITARES
					$id_condicion = 4;
					$condicion = "ID_CONDICION = $id_condicion AND YEAR(FECHA_REG_EVEN) = '$a'";
					$num_militar = $mina_dao->GetValor($condicion,$id_ubicacion,$dato_para);
					
					//HERIDOS
					$id = 1;
					$condicion = "ID_ESTADO = $id AND YEAR(FECHA_REG_EVEN) = '$a'";
					$num_herido = $mina_dao->GetValor($condicion,$id_ubicacion,$dato_para);

					//MUERTOS
					$id = 2;
					$condicion = "ID_ESTADO = $id AND YEAR(FECHA_REG_EVEN) = '$a'";
					$num_muerto = $mina_dao->GetValor($condicion,$id_ubicacion,$dato_para);
					
					if ($num_total > 0){
						
						echo "<tr><td colspan=2><b>TOTAL</b>: $num_total personas</td></tr>";
						
						$por_civil = number_format(($num_civil/$num_total),2) * 100;
						$por_militar = number_format(($num_militar/$num_total),2) * 100;
						$por_herido = number_format(($num_herido/$num_total),2) * 100;
						$por_muerto = number_format(($num_muerto/$num_total),2) * 100;
						?>
						<tr>
							<td><b>CIVILES</b>: <?=$num_civil ?> personas - <?=$por_civil ?> %</td>
							<td><b>MILITARES</b>: <?=$num_militar ?>personas - <?=$por_militar ?> %</td>
						</tr>
						<tr>
							<td><b>HERIDOS</b>: <?=$num_herido ?> personas - <?=$por_herido ?> %</td>
							<td><b>MUERTOS</b>: <?=$num_muerto ?> personas - <?=$por_muerto ?> %</td>
						</tr>  
					<?
					} 
					else{
						echo "<tr><td colspan=2><b>NO HAY ACCIDENTES REGISTADOS</td></tr>";		
					}
					?>
				</table>
				
				<!-- IRSH -->
				<table id='irsh' cellpadding="5" cellspacing="1" class='tabla_mapserver_perfil' align="center" style="display:none">
					<tr><td><b>DEFINICION</b><br>
					<?
					$dato = $d_s_dao->get(232);
					echo $dato->definicion;
					?>
					</td></tr>
					<?
					//IRSH
					$id_datos = array(232,235,236,234,233);

					foreach($id_datos as $id_dato){
						//CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
						$fecha_val = $d_s_dao->GetMaxFecha($id_dato);
						
						$val = $d_s_dao->GetValorToReport($id_dato,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],$dato_para);
						$valor = $val['valor'];
						$id_unidad = $val['id_unidad'];
						
						//APLICA FORMATO
						$valor = $d_s_dao->formatValor($id_unidad,$valor);
						
						$valores[] = $valor;
						
						$vos[] = $d_s_dao->Get($id_dato);
					}
					
					$f_tmp = split("-",$fecha_val['ini']);
					$periodo_ini = ($f_tmp[2]*1)." ".$meses[$f_tmp[1]*1]." ".$f_tmp[0];
					
					$f_tmp = split("-",$fecha_val['fin']);
					$periodo_fin = ($f_tmp[2]*1)." ".$meses[$f_tmp[1]*1]." ".$f_tmp[0];
					
					?>
					
					<tr>
						<td style="background-color:#FFFFFF"><b>Valores para el periodo: <?=$periodo_ini ?> a <?=$periodo_fin ?></b></td>
					</tr>
					<tr>
						<td><b>&#187;&nbsp;<?=$vos[0]->nombre ?></b>: <?=$valores[0]?></td>
					</tr>
					<tr>
						<td><b>&#187;&nbsp;<?=str_replace(" - Indice de Riesgo de Situación Humanitaria","",$vos[1]->nombre) ?></b>: <?=$valores[1]?></td>
					</tr>
					<tr>
						<td><b>&#187;&nbsp;<?=str_replace(" - Indice de Riesgo de Situación Humanitaria","",$vos[2]->nombre) ?></b>: <?=$valores[2]?></td>
					</tr>
					<tr>
						<td><b>&#187;&nbsp;<?=str_replace(" - Indice de Riesgo de Situación Humanitaria","",$vos[3]->nombre) ?></b>: <?=$valores[3]?></td>
					</tr>
					<tr>
						<td><b>&#187;&nbsp;<?=str_replace(" - Indice de Riesgo de Situación Humanitaria","",$vos[4]->nombre) ?></b>: <?=$valores[4]?></td>
					</tr>
				</table>			
				
				<!-- ACCIONES BELICAS -->
				<table id="acc_belicas" cellpadding="5" cellspacing="1" align="center" style="display:none">
					<tr>
						<td>
							<table class='tabla_mapserver_perfil' cellpadding="5" cellspacing="1" align="center">
								<tr>
									<td style="background-color:#FFFFFF"><b>Valores para: <?=$a ?></b></td>
								</tr> 
								<tr>
									<td>&nbsp;</td>
									<td align="center"><b>Num. de Eventos</b></td>
									<td align="center"><b>Num. de V&iacute;ctimas</b></td>
								</tr>
								<?
								$subcat_dao = New SubCatEventoConflictoDAO();
								
								$subcats = $subcat_dao->GetAllArray('id_cateven = 1');
								$id_actor = 0;  //Por ahora no está en los filtros
								$f_ini = "$a-1-1";
								$f_fin = "$a-12-31";
								
								foreach ($subcats as $subcat){
									
									$id_scat = $subcat->id;
									
									echo "<tr><td><b>".$subcat->nombre."</b></td>";
									
									//# eventos
									$num = $evento_c_dao->numEventosReporte($id_mpio,$id_scat,$id_actor,$f_ini,$f_fin);
									
									if ($num > 0) $num = "<b>$num</b>";
									echo "<td align='center'>$num</td>";
									
									//# victimas
									$num = $evento_c_dao->numVictimasReporte($id_mpio,array("id_mun"=>1,"f_ini"=>$f_ini,"f_fin"=>$f_fin,"id_scat"=>$id_scat));
									if ($num > 0) $num = "<b>$num</b>";
									echo "<td align='center'>$num</td>";
									
									echo "</tr>";	
								}
								
								?>
							</table>
				</table>	
		<?
		
		$html = ob_get_contents(); 
		
		ob_end_clean();
	break;
	}
}
else{
	$titulo_html = "Error!";
	$nombre = "Lugar NO v&aacute;lido, por favor haga click sobre alg&uacute;n municipio.";
	$valor = "";
	$unidad_html = "";
}

if ($case != 'perfil'){
	?>
	<table class='query' cellpadding=0 border=0>
		<tr>
			<td class='<?=$class_titulo?>' style="width:180px"><?=$titulo_html ?></td>
			<td align="right" style="width:20px" valign='top'>[ <a href='#' onclick="document.getElementById('div_info').style.display='none'">X</a> ]</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<?
		if ($tasa == 1)	echo "<tr><td><b>Tasa por 100.000 habitantes</b></td></tr>";
		
		?>
		<tr>
			<td colspan=2><?=$nombre?>&nbsp;<b><?=$valor?> <?=$unidad_html?></b><br><?=$link_reporte_html; ?></td>
		</tr>
	</table>
<?
}
else{
	echo $html;	
}

?>
