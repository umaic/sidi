<?

if (isset($_POST["submit"])){
	
	$que = $_POST["que"];
	
	include_once("lib/common/archivo.class.php");
	include_once("lib/libs_mapa_i.php");
	
	$conn = MysqlDb::getInstance();
	
	$mpio_dao = New MunicipioDAO();
	$file = New Archivo();
	
	$muns = $mpio_dao->GetAllArray('','','');
	
	if (in_array('orgs',$que)){
		
		$starttime;
		$mtime = microtime();
		$mtime = explode(" ",$mtime);
		$mtime = $mtime[1] + $mtime[0];
		$starttime = $mtime;
		
	
		$org_dao = New OrganizacionDAO();
		$tipo_org_dao = New TipoOrganizacionDAO();
		$sector_dao = New SectorDAO();
		$poblacion_dao = New PoblacionDAO();
		
		$dato_dao = New DatoSectorialDAO();
		
		$nom_archivo = 'arcims/orgs_todo.txt';
		$fp_todo = $file->Abrir($nom_archivo,'w');
		
		$nom_archivo = 'arcims/orgs_relacional.txt';
		$fp_rel = $file->Abrir($nom_archivo,'w');
		
		$nom_archivo = 'arcims/org_tabla.txt';
		$fp_tabla = $file->Abrir($nom_archivo,'w');
		
		
		$tipos_org = $tipo_org_dao->GetAllArray('');
		$sectores = $sector_dao->GetAllArray('');
		$poblaciones = $poblacion_dao->GetAllArray('',"","");
		
		$header = "NOM_MUN|COD_MUN|ORG_TOTAL";
		$header_rel = "NOM_MUN|COD_MUN|ORG_TOTAL";
		
		$i = 1;
		foreach ($tipos_org as $vo){
			$header .= "|$vo->nombre_es";
			$header_rel .= "|O_TIPO_$i";
			$file->Escribir($fp_tabla,"O_TIPO_$i|$vo->nombre_es\n");
			$i++;
			
		}
		
		$i = 1;
		foreach ($sectores as $vo){
			$header .= "|$vo->nombre_es";
			$header_rel .= "|O_SEC_$i";
			$file->Escribir($fp_tabla,"O_SEC_$i|$vo->nombre_es\n");
			$i++;
			
		}
		
		$i = 1;
		foreach ($poblaciones as $vo){
			$header .= "|$vo->nombre_es";
			$header_rel .= "|O_POB_$i";
			$file->Escribir($fp_tabla,"O_POB_$i|$vo->nombre_es\n");
			$i++;
			
		}
		
		echo "$header<br>";
		
		$file->Escribir($fp_todo,$header."\n");
		$file->Escribir($fp_rel,$header_rel."\n");
		
		foreach ($muns as $mun){
			$linea = "$mun->nombre|$mun->id";
			
			//ORG TOTAL
			$num_org_total = $org_dao->numOrgsConteo('municipio',$mun->id,0,$mun->id,'','');
			$linea .= "|".$num_org_total['total'];
			
			//TIPOS
			foreach ($tipos_org as $vo){
				$num_org = $org_dao->numOrgsConteo('tipo',$vo->id,0,$mun->id,'','');
				
				$linea .= "|".$num_org['total'];
			}
		
			//SECTORES
			foreach ($sectores as $vo){
				$num_org = $org_dao->numOrgsConteo('tipo',$vo->id,0,$mun->id,'','');
				
				$linea .= "|".$num_org['total'];
			}
			
			//POBLACIONES
			foreach ($poblaciones as $vo){
				$num_org = $org_dao->numOrgsConteo('tipo',$vo->id,0,$mun->id,'','');
				
				$linea .= "|".$num_org['total'];
			}
			
			echo "$linea<br>";
			
			$file->Escribir($fp_todo,$linea."\n");
			$file->Escribir($fp_rel,$linea."\n");
			
			$mtime = microtime();
			$mtime = explode(" ",$mtime);
			$mtime = $mtime[1] + $mtime[0];
			echo ("Tiempo en Orgs: ".$mtime - $starttime);
			
		}
	}
	
	if (in_array('desplazamiento',$que)){
		
		$starttime;
		$mtime = microtime();
		$mtime = explode(" ",$mtime);
		$mtime = $mtime[1] + $mtime[0];
		$starttime = $mtime;
		
		//INICIALIZACION DE VARIABLES
		$periodo_dao = New PeriodoDAO();
		$ini = 1994;
		
		//Año actual
		$fecha = getdate();
		$fin = $fecha["year"];

		$nom_archivo = 'arcims/desplazamiento.txt';
		$fp_todo = $file->Abrir($nom_archivo,'w');
		
		//CONSULTA LOS ID DE LOS PERIODOS QUE CORRESPONDEN A LOS AÑOS SELECCIONADOS
		for ($a=$ini;$a<=$fin;$a++){
			$per = $periodo_dao->GetAllArray("DESC_PERIO like '%".$a."%'");
		
			if (count($per) > 0){
				$i = 0;
				foreach ($per as $p){
					$id_periodo[$a][$i] = $p->id;
					$i++;
				}
			}
			else{
				$id_periodo[$a] = Array(0);
			}
		}
		
		$header = "NOM_MUN|COD_MUN";
		for ($a=$ini;$a<=$fin;$a++){
			$header .= "|EXP_$a|REC_$a";
		}

		$file->Escribir($fp_todo,$header."\n");
			
		foreach($muns as $mun){
			
			$id_mun = $mun->id;
			$linea = "$mun->nombre|$mun->id";
			
			for ($a=$ini;$a<=$fin;$a++){
		
				$id_periodo_s = implode(",",$id_periodo[$a]);
				
				//EXPULSION
				$sql = "SELECT sum(VALOR) FROM registro WHERE ID_MUN_ID_MUN = '$id_mun' AND ID_PERIO IN (".$id_periodo_s.") AND ID_CLASE_DESPLA IN (1)";
				$rs = $conn->OpenRecordset($sql);
				$row_rs = $conn->FetchRow($rs);
				$linea .= "|$row_rs[0]";
				
				//RECEPCION
				$sql = "SELECT sum(VALOR) FROM registro WHERE ID_MUN = '$id_mun' AND ID_PERIO IN (".$id_periodo_s.") AND ID_CLASE_DESPLA IN (2,3)";
				$rs = $conn->OpenRecordset($sql);
				$row_rs = $conn->FetchRow($rs);
				$linea .= "|$row_rs[0]";
						
			}
			
			$file->Escribir($fp_todo,$linea."\n");
			
			$mtime = microtime();
			$mtime = explode(" ",$mtime);
			$mtime = $mtime[1] + $mtime[0];
			echo ("Tiempo en Desplazamiento: ".$mtime - $starttime);
			
		}		
	}
	
	if (in_array('mina',$que)){
		
		$starttime;
		$mtime = microtime();
		$mtime = explode(" ",$mtime);
		$mtime = $mtime[1] + $mtime[0];
		$starttime = $mtime;
		
		$id_tipo = array(107,109);
		
		$sql = "SELECT min(YEAR(FECHA)) FROM mina";
		$rs = $conn->OpenRecordset($sql);
		$row_rs = $conn->FetchRow($rs);
				
		$ini = $row_rs[0];
		
		//Año actual
		$fecha = getdate();
		$fin = $fecha["year"];

		$nom_archivo = 'arcims/mina.txt';
		$fp_todo = $file->Abrir($nom_archivo,'w');
		
		$header = "NOM_MUN|COD_MUN";
		for ($a=$ini;$a<=$fin;$a++){
			$header .= "|MAP_$a|MUSE_$a";
		}

		$file->Escribir($fp_todo,$header."\n");
			
		foreach($muns as $mun){
			
			$id_mun = $mun->id;
			$linea = "$mun->nombre|$mun->id";
			
			for ($a=$ini;$a<=$fin;$a++){
		
				foreach ($id_tipo as $id_t){
					$sql = "SELECT sum(CANTIDAD) FROM mina WHERE ID_MUN = '$id_mun' AND YEAR(FECHA) = $a AND ID_TIPO_EVE = $id_t";
					$rs = $conn->OpenRecordset($sql);
					$row_rs = $conn->FetchRow($rs);
					$linea .= "|$row_rs[0]";
				}
			}
			
			$file->Escribir($fp_todo,$linea."\n");
			
			$mtime = microtime();
			$mtime = explode(" ",$mtime);
			$mtime = $mtime[1] + $mtime[0];
			echo ("Tiempo en Mina: ".$mtime - $starttime);

		}		
	}

	if (in_array('dato',$que)){
		
		$starttime;
		$mtime = microtime();
		$mtime = explode(" ",$mtime);
		$mtime = $mtime[1] + $mtime[0];
		$starttime = $mtime;
		
		$d_s_dao = New DatoSectorialDAO();
		
		$nom_archivo = 'arcims/datos_sectoriales.txt';
		$fp_todo = $file->Abrir($nom_archivo,'w');

		$nom_archivo = 'arcims/datos_sectoriales_tabla.txt';
		$fp_tabla = $file->Abrir($nom_archivo,'w');

		$header = "NOM_MUN|COD_MUN";

		$d = 0;
		$sql = "SELECT ID_DATO FROM minificha_datos_resumen";
		$rs = $conn->OpenRecordset($sql);
		while ($row_rs = $conn->FetchRow($rs)){
			$id_datos_resumen[$d] = $row_rs[0];
			
			$dato = $d_s_dao->Get($row_rs[0]);
			
			$d++;
			
			$header .= "|DATO_$d";
			
			$file->Escribir($fp_tabla,"DATO_$d|$dato->nombre\n");
		}		
		
		$file->Escribir($fp_todo,$header."\n");
			
		foreach($muns as $mun){
			
			$id_mun = $mun->id;
			$linea = "$mun->nombre|$mun->id";
			
			foreach ($id_datos_resumen as $id_dato){
				$dato = $d_s_dao->Get($id_dato);

				//CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
				$fecha_val = $d_s_dao->GetMaxFecha($id_dato);

				/*$sumado = 0;
				if ($id_dato == 3)	$sumado = 0;	//POBLACION TOTAL
				if ($id_dato == 9)	$sumado = 2;	//Afiliados Regimen Contributivo Del Sistema General De Seguridad Social En Salud
				*/
				
				//VALOR DATO
				$val = $d_s_dao->GetValorToReport($id_dato,$id_mun,$fecha_val['ini'],$fecha_val['fin'],2);
				$valor = $val['valor'];
				$id_unidad = $val['id_unidad'];

				//CHECK DE DECIMALES DE PORCENTAJES
				if ($id_unidad == 4 || $id_unidad == 9){
					//(is_float($valor)) ? $decimals = 2 : $decimals = 0;
					$decimals = 2;

					if ($valor != "N.D."){
						$valor = number_format($valor,$decimals,'.',',');
					}

				}
				//UNIDAD
				else if ($id_unidad == 2){
					$valor = number_format($valor,2,'.',',');
					if ($valor_nacional != "N.D.")	$valor_nacional = number_format($valor_nacional,2,'.',',');
				}
				else{
					if ($valor != "N.D.")			$valor = number_format($valor,0,'',',');
					if ($valor_nacional != "N.D.")	$valor_nacional = number_format($valor_nacional,0,'',',');
				}


				$linea .= "|$valor";
				
			}
			echo "$linea<br>";
			$file->Escribir($fp_todo,$linea."\n");
		}		
		
		$mtime = microtime();
		$mtime = explode(" ",$mtime);
		$mtime = $mtime[1] + $mtime[0];
		echo ("Tiempo en Datos: ".$mtime - $starttime);
	}	
}


?>

<form method="POST">
<input type="checkbox" name="que[]" value="orgs">&nbsp;Orgs<br>
<input type="checkbox" name="que[]" value="desplazamiento">&nbsp;Desplazamiento<br>
<input type="checkbox" name="que[]" value="mina">&nbsp;Mina<br>
<input type="checkbox" name="que[]" value="dato">&nbsp;Datos<br>
<input type="submit" name="submit" value="Hagale!">
</form>