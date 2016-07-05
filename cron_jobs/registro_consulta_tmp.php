 <?
/**
* Cron Job para Desplazamiento
*
* Realizar la pre-consulta de Desplazamiento, popula la tabla registro_consulta_tmp - Se corre diario en un CronJob
* @author Ruben A. Rojas C.
*/

set_time_limit(0);

//COMMON
//include_once("../admin/lib/common/mysqldb.class.php");
include_once("../admin/lib/common/mysqldb_despla_import.class.php");

//MODEL
include_once("../admin/lib/model/desplazamiento.class.php");
include_once("../admin/lib/model/tipo_desplazamiento.class.php");
include_once("../admin/lib/model/clase_desplazamiento.class.php");
include_once("../admin/lib/model/periodo.class.php");
include_once("../admin/lib/model/municipio.class.php");
include_once("../admin/lib/model/depto.class.php");
include_once("../admin/lib/model/fuente.class.php");

//DAO
include_once("../admin/lib/dao/desplazamiento.class.php");
include_once("../admin/lib/dao/tipo_desplazamiento.class.php");
include_once("../admin/lib/dao/clase_desplazamiento.class.php");
include_once("../admin/lib/dao/municipio.class.php");
include_once("../admin/lib/dao/depto.class.php");
include_once("../admin/lib/dao/periodo.class.php");
include_once("../admin/lib/dao/fuente.class.php");

//INICIALIZACION DE VARIABLES
$depto_dao = New DeptoDAO();
$municipio_dao = New MunicipioDAO();
$clase_dao = New ClaseDesplazamientoDAO();
$tipo_dao = New TipoDesplazamientoDAO();
$fuente_dao = New FuenteDAO();
$periodo_dao = New PeriodoDAO();
$num_desplazados = array();
$conn = MysqlDb::getInstance();

//id_fuedes = 2 => accion social
$fuentes = array(2);
//$fuente_dao->GetAllArrayID('id_fuedes = 2');
$tipos = $tipo_dao->GetAllArrayID('');

$num_fuentes = count($fuentes);

$ini = 1985;
$corregimientos_vichada = "'99752','99760','99572'";

//Año actual
$fecha = getdate();
$fin = $fecha["year"];

// Variables para exp y rec
$er = array(array('ID_DEPTO_ID_DEPTO','ID_MUN_ID_MUN','1','EXP'),
            array('ID_DEPTO','ID_MUN','2,3','REC')
        );

$sql = "TRUNCATE ocha_sissh_despla_import.registro_consulta_tmp";
$conn->Execute($sql);

echo 'Comienza el proceso de totalizar....<br />Procesando.....';

//CONSULTA LOS ID DE LOS PERIODOS QUE CORRESPONDEN A LOS AÃ±OS SELECCIONADOS

//Comentado CODHES porque solo se actualiza Accion Social
//CODHES
/*for ($a=$ini;$a<=$fin;$a++){
	$per = $periodo_dao->GetAllArray("DESC_PERIO like '%".$a."%'");

	if (count($per) > 0){
		$i = 0;
		foreach ($per as $p){
			$id_periodo[1][$a][$i] = $p->id;
			$i++;
		}
	}
	else{
		$id_periodo[1][$a] = Array(0);
	}
}*/

//ACCION SOCIAL
for ($a=$ini;$a<=$fin;$a++){
	
	$condicion = "DESC_PERIO = 'Enero ".$a."' or DESC_PERIO = 
													'Febrero ".$a."' or DESC_PERIO = 
													'Marzo ".$a."' or DESC_PERIO = 
													'Abril ".$a."' or DESC_PERIO = 
													'Mayo ".$a."' or DESC_PERIO = 
													'Junio ".$a."' or DESC_PERIO = 
													'Julio ".$a."' or DESC_PERIO = 
													'Agosto ".$a."' or DESC_PERIO = 
													'Septiembre ".$a."' or DESC_PERIO = 
													'Octubre ".$a."' or DESC_PERIO = 
													'Noviembre ".$a."' or DESC_PERIO = 
													'Diciembre ".$a."'";
	
	$per = $periodo_dao->GetAllArray($condicion);
	
//	echo $condicion;

	
	if (count($per) > 0){
		$i = 0;
		foreach ($per as $p){
			$id_periodo[2][$a][$i] = $p->id;
			$i++;
		}
	}
	else{
		$id_periodo[2][$a] = Array(0);
	}
}


//print_r($id_periodo[1]);
//echo "dsfadfdf<br>";
//print_r($id_periodo[2][2008]);
//die;
//
			
$id_deptos = $depto_dao->GetAllArrayID('');
//$id_deptos = array('15');
foreach($id_deptos as $id_depto){
	//echo $id_depto."<br>";

	for ($a=$ini;$a<=$fin;$a++){

		foreach ($fuentes as $id_fuente){

			$id_periodo_s = implode(",",$id_periodo[$id_fuente][$a]);
			$acumulado_depto_exp[$id_fuente] = 0;
			$acumulado_depto_rec[$id_fuente] = 0;
			$acumulado_mun_exp[$id_fuente] = 0;
			$acumulado_mun_rec[$id_fuente] = 0;
            
            // Individual - Masivo
            foreach ($tipos as $id_tipo){
                
                // Expu - Recep
                foreach($er as $e) {
                    $col_depto = $e[0];
                    $col_mun = $e[1];
                    $clase = $e[2];
                    $col_exp_rec = $e[3];
        
                    $sql = "SELECT VALOR,PERSONAS 
                            FROM registro 
                            WHERE $col_depto = '$id_depto' AND 
                            ID_PERIO IN (".$id_periodo_s.") AND 
                            ID_FUEDES = ".$id_fuente." AND 
                            ID_TIPO_DESPLA = ".$id_tipo." AND 
                            ID_CLASE_DESPLA IN ($clase)";

                    $rs = $conn->OpenRecordset($sql);
                    $row_rs = $conn->FetchRow($rs);
                    $valor = $row_rs[0];
                    $personas = $row_rs[1];
                    
                    if (!is_null($valor)){
                        $acumulado_depto_exp[$id_fuente] += $valor;
                        
                        $row_rs = $conn->FetchRow($rs);
                        $sql = "INSERT INTO registro_consulta_tmp 
                                (ID_DEPTO,AAAA,ID_FUENTE,ID_TIPO,VALOR,$col_exp_rec,PERSONAS) 
                                VALUES 
                                ('$id_depto',$a,$id_fuente,$id_tipo,$valor,1,$personas)";

                        $conn->Execute($sql);
                    }
                    else{
                        //para el mpio 00000 del depto 00
                        if ($id_depto == '00'){
                            $sql = "SELECT VALOR,registro.$col_mun,PERSONAS
                                FROM registro 
                                WHERE registro.$col_mun = '00000' AND 
                                ID_PERIO IN (".$id_periodo_s.") AND 
                                ID_FUEDES = ".$id_fuente." AND 
                                ID_TIPO_DESPLA = ".$id_tipo." AND 
                                ID_CLASE_DESPLA IN ($clase)";
                        }
                        else{
                            $sql = "SELECT VALOR, $col_mun, PERSONAS
                                    FROM registro 
                                    WHERE SUBSTRING($col_mun,1,2) = '$id_depto' AND 
                                    ID_PERIO IN (".$id_periodo_s.") AND 
                                    ID_FUEDES = ".$id_fuente." AND 
                                    ID_TIPO_DESPLA = ".$id_tipo." AND 
                                    ID_CLASE_DESPLA IN ($clase)";						
                        }
                        //echo "$sql<br>";
                        $rs = $conn->OpenRecordset($sql);
                        $valor = 0;
                        $personas = 0;
                        while($row_rs = $conn->FetchRow($rs)){
                            if ($row_rs[0] != ""){
                                $valor_mun = $row_rs[0];
                                $id_mun = $row_rs[1];
                                $personas_mun = $row_rs[2];
                                
                                $valor += $valor_mun;
                                $personas += $personas_mun;
                                $acumulado_mun_exp[$id_fuente] += $valor;

                                //MUNICIPIO
                                $sql = "INSERT INTO registro_consulta_tmp 
                                    (ID_DEPTO,ID_MUN,AAAA,ID_FUENTE,ID_TIPO,VALOR,$col_exp_rec,PERSONAS) 
                                    VALUES 
                                    ('$id_depto','$id_mun',$a,$id_fuente,$id_tipo,$valor_mun,1,$personas_mun)";
                                //echo "$sql<br>";
                                $conn->Execute($sql);
                            }
                        }
                        if ($valor > 0){
                            $acumulado_depto_exp[$id_fuente] += $valor;
                            $sql = "INSERT INTO registro_consulta_tmp 
                                (ID_DEPTO,AAAA,ID_FUENTE,ID_TIPO,VALOR,$col_exp_rec,PERSONAS) 
                                VALUES 
                                ('$id_depto',$a,$id_fuente,$id_tipo,$valor,1,$personas)";
                            $conn->Execute($sql);
                        }
                    }
                } // fin exp - rec
			} // fin ind - mas
		} // fin fuentes
	}
	
	//TOTALIZA PARA EL PERIODO = SIN FECHA = AÑO:1899
	$id_periodo_s = 66;
	$a = 1899;
	foreach ($fuentes as $id_fuente){
		foreach ($tipos as $id_tipo){
			//EXPULSION
			$sql = "SELECT VALOR FROM registro WHERE ID_DEPTO_ID_DEPTO = '$id_depto' AND ID_PERIO IN (".$id_periodo_s.") AND ID_FUEDES = ".$id_fuente." AND ID_TIPO_DESPLA = ".$id_tipo." AND ID_CLASE_DESPLA IN (1)";
	    	$rs = $conn->OpenRecordset($sql);
	    	$row_rs = $conn->FetchRow($rs);
	    	$valor = $row_rs[0];
	    	$acumulado_depto_exp[$id_fuente] += $valor;
			if (!is_null($valor)){
			  	$row_rs = $conn->FetchRow($rs);
			  	$sql = "INSERT INTO registro_consulta_tmp (ID_DEPTO,AAAA,ID_FUENTE,ID_TIPO,VALOR,EXP) VALUES ('$id_depto',$a,$id_fuente,$id_tipo,$valor,1)";
			  	$conn->Execute($sql);
			}
			else{
				//EXPULSION
				if ($id_depto == '00'){
					$sql = "SELECT VALOR,registro.ID_MUN_ID_MUN FROM registro WHERE registro.ID_MUN_ID_MUN = '00000' AND ID_PERIO IN (".$id_periodo_s.") AND ID_FUEDES = ".$id_fuente." AND ID_TIPO_DESPLA = ".$id_tipo." AND ID_CLASE_DESPLA IN (1)";
				}
				else{
					$sql = "SELECT VALOR, municipio.ID_MUN FROM registro INNER JOIN municipio ON registro.ID_MUN_ID_MUN = municipio.ID_MUN WHERE municipio.ID_DEPTO = '$id_depto' AND ID_PERIO IN (".$id_periodo_s.") AND ID_FUEDES = ".$id_fuente." AND ID_TIPO_DESPLA = ".$id_tipo." AND ID_CLASE_DESPLA IN (1)";
				}
				//echo "$sql<br>";
				$rs = $conn->OpenRecordset($sql);
			    $valor = 0;
				while($row_rs = $conn->FetchRow($rs)){
					if ($row_rs[0] != ""){
						$valor_mun = $row_rs[0];
						$valor += $valor_mun;
						$id_mun = $row_rs[1];
						$acumulado_mun_exp[$id_fuente] += $valor;

						//MUNICIPIO
						$sql = "INSERT INTO registro_consulta_tmp (ID_DEPTO,ID_MUN,AAAA,ID_FUENTE,ID_TIPO,VALOR,EXP) VALUES ('$id_depto','$id_mun',$a,$id_fuente,$id_tipo,$valor_mun,1)";
						//echo "$sql<br>";
					  	$conn->Execute($sql);
					}
				}
				if ($valor > 0){
					$acumulado_depto_exp[$id_fuente] += $valor;
				  	$sql = "INSERT INTO registro_consulta_tmp (ID_DEPTO,AAAA,ID_FUENTE,ID_TIPO,VALOR,EXP) VALUES ('$id_depto',$a,$id_fuente,$id_tipo,$valor,1)";
				  	$conn->Execute($sql);
				}
			}
			
			
			//RECEPCION
	    	$sql = "SELECT VALOR FROM registro WHERE ID_DEPTO = '$id_depto' AND  ID_PERIO IN (".$id_periodo_s.") AND ID_FUEDES = ".$id_fuente." AND ID_TIPO_DESPLA = ".$id_tipo." AND ID_CLASE_DESPLA IN (2,3)";
	    	$rs = $conn->OpenRecordset($sql);
			$valor = $row_rs[0];
			$acumulado_depto_rec[$id_fuente] += $valor;
			if (!is_null($valor)){
			  	$row_rs = $conn->FetchRow($rs);
			  	$sql = "INSERT INTO registro_consulta_tmp (ID_DEPTO,AAAA,ID_FUENTE,ID_TIPO,VALOR,REC) VALUES ('$id_depto',$a,$id_fuente,$id_tipo,$valor,1)";
			  	$conn->Execute($sql);
			}
			else{
				//RECEPCION
				if ($id_depto == '00'){
					$sql = "SELECT VALOR, registro.ID_MUN FROM registro WHERE registro.ID_MUN = '00000' AND ID_PERIO IN (".$id_periodo_s.") AND ID_FUEDES = ".$id_fuente." AND ID_TIPO_DESPLA = ".$id_tipo." AND ID_CLASE_DESPLA IN (2,3)";
				}
				else{
					$sql = "SELECT VALOR, municipio.ID_MUN FROM registro INNER JOIN municipio ON registro.ID_MUN = municipio.ID_MUN WHERE municipio.ID_DEPTO = '$id_depto' AND ID_PERIO IN (".$id_periodo_s.") AND ID_FUEDES = ".$id_fuente." AND ID_TIPO_DESPLA = ".$id_tipo." AND ID_CLASE_DESPLA IN (2,3)";
				}
				
			    $rs = $conn->OpenRecordset($sql);
			    $valor = 0;
				while($row_rs = $conn->FetchRow($rs)){
					$valor_mun = $row_rs[0];
					$valor += $valor_mun;
					$id_mun = $row_rs[1];
					$acumulado_mun_rec[$id_fuente] += $valor;

					//MUNICIPIO
					$sql = "INSERT INTO registro_consulta_tmp (ID_DEPTO,ID_MUN,AAAA,ID_FUENTE,ID_TIPO,VALOR,REC) VALUES ('$id_depto','$id_mun',$a,$id_fuente,$id_tipo,$valor_mun,1)";
				  	$conn->Execute($sql);
					
				}
				if ($valor > 0){
					$acumulado_depto_rec[$id_fuente] += $valor;
				  	$sql = "INSERT INTO registro_consulta_tmp (ID_DEPTO,AAAA,ID_FUENTE,ID_TIPO,VALOR,REC) VALUES ('$id_depto',$a,$id_fuente,$id_tipo,$valor,1)";
				  	//echo $sql;
				  	$conn->Execute($sql);
				}
			}				
		}
	}
	
}

$mtime = microtime();
$mtime = explode(" ",$mtime);
$mtime = $mtime[1] + $mtime[0];
//echo ($mtime - $starttime);	

// Copiar información de temporal a principal

$sql = "DELETE FROM ocha_sissh.registro WHERE id_fuedes = 2";
$conn->Execute($sql);
$sql = "DELETE FROM ocha_sissh.registro_consulta_tmp WHERE id_fuente = 2";
$conn->Execute($sql);
$sql = "INSERT INTO ocha_sissh.registro (ID_TIPO_DESPLA,ID_MUN,ID_DEPTO,ID_CLASE_DESPLA,ID_MUN_ID_MUN,ID_FUEDES,ID_PERIO,ID_POBLA,ID_CONP,ID_DEPTO_ID_DEPTO,VALOR,FECHA_CORTE) SELECT ID_TIPO_DESPLA,ID_MUN,ID_DEPTO,ID_CLASE_DESPLA,ID_MUN_ID_MUN,ID_FUEDES,ID_PERIO,ID_POBLA,ID_CONP,ID_DEPTO_ID_DEPTO,VALOR,FECHA_CORTE FROM ocha_sissh_despla_import.registro";
$conn->Execute($sql);
$sql = "INSERT INTO ocha_sissh.registro_consulta_tmp SELECT * FROM ocha_sissh_despla_import.registro_consulta_tmp";
$conn->Execute($sql);

echo 'Listo!';


?>
