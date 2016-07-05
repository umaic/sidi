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

if (empty($_GET['a']))  die('Falta el parametro <b>a</b> en la url (a&ntilde;os a totalizar), (ej. a=2009,2010)');

foreach(explode(',', $_GET['a']) as $_a){
    if (!is_numeric($_a)) die('El parmetro <b>a</b> no es correcto');
}

//id_fuedes = 1 => codhes
$fuentes = array(1);
$tipos = $tipo_dao->GetAllArrayID('id_tipo_despla=3');

$num_fuentes = count($fuentes);

//$aaaa = array(1999,2000,2001,2002,2003,2004,2005,2006,2008);
$aaaa = explode(',', $_GET['a']);
$id_clase = 3;

$sql = "TRUNCATE ocha_sissh_despla_import.registro_consulta_tmp";
$conn->Execute($sql);

echo 'Comienza el proceso de totalizar....<br />Procesando.....';


//CONSULTA LOS ID DE LOS PERIODOS QUE CORRESPONDEN A LOS AñOS SELECCIONADOS

//CODHES
foreach($aaaa as $a){
	$per = $periodo_dao->GetAllArray("DESC_PERIO like '%".$a."%'");

	if (count($per) > 0){
		foreach ($per as $i=>$p){
			$id_periodo[1][$a][$i] = $p->id;
		}
	}
	else{
		$id_periodo[1][$a] = Array(0);
	}
}

/*
print_r($id_periodo[1]);
echo "dsfadfdf<br>";
die;
*/
			
$id_deptos = $depto_dao->GetAllArrayID('');
//$id_deptos = array('15');
foreach($id_deptos as $id_depto){
	//echo $id_depto."<br>";

	foreach($aaaa as $a){
		//echo "$a<br>";
		foreach ($fuentes as $id_fuente){

			$id_periodo_s = implode(",",$id_periodo[$id_fuente][$a]);
			$acumulado_depto_exp[$id_fuente] = 0;
			$acumulado_mun_exp[$id_fuente] = 0;

			foreach ($tipos as $id_tipo){
                $sql = "SELECT VALOR FROM registro 
                    WHERE ID_DEPTO = '$id_depto' AND 
                          ID_PERIO IN (".$id_periodo_s.") AND 
                          ID_FUEDES = ".$id_fuente." AND 
                          ID_TIPO_DESPLA = ".$id_tipo." AND 
                          ID_CLASE_DESPLA IN ($id_clase)";

				//die($sql);
		    	$rs = $conn->OpenRecordset($sql);
		    	$row_rs = $conn->FetchRow($rs);
		    	$valor = $row_rs[0];

				if (!is_null($valor)){
					$acumulado_depto_exp[$id_fuente] += $valor;
				  	
                    $sql = "INSERT INTO registro_consulta_tmp 
                        (ID_DEPTO,AAAA,ID_FUENTE,ID_TIPO,VALOR,EXP) 
                        VALUES
                        ('$id_depto',$a,$id_fuente,$id_tipo,$valor,1)";
				  	$conn->Execute($sql);
				}
				else{
					//para el mpio 00000 del depto 00
					if ($id_depto == '00'){
                        $sql = "SELECT VALOR,registro.ID_MUN FROM registro 
                            WHERE registro.ID_MUN = '00000' AND 
                                  ID_PERIO IN (".$id_periodo_s.") AND 
                                  ID_FUEDES = ".$id_fuente." AND 
                                  ID_TIPO_DESPLA = ".$id_tipo." AND 
                                  ID_CLASE_DESPLA IN ($id_clase)";
					}
					else{
                        $sql = "SELECT VALOR, ID_MUN FROM registro 
                                WHERE SUBSTRING(ID_MUN,1,2) = '$id_depto' AND 
                                      ID_PERIO IN (".$id_periodo_s.") AND 
                                      ID_FUEDES = ".$id_fuente." AND 
                                      ID_TIPO_DESPLA = ".$id_tipo." AND 
                                      ID_CLASE_DESPLA IN ($id_clase)";						
					}
					//echo "$sql<br>";
					$rs = $conn->OpenRecordset($sql);
                    $valor = 0;
					while($row_rs = $conn->FetchRow($rs)){
						if ($row_rs[0] != ""){
							$valor_mun = $row_rs[0];
							$id_mun = $row_rs[1];
							
							$valor += $valor_mun;
							$acumulado_mun_exp[$id_fuente] += $valor;

							//MUNICIPIO
                            $sql = "INSERT INTO registro_consulta_tmp 
                                    (ID_DEPTO,ID_MUN,AAAA,ID_FUENTE,ID_TIPO,VALOR,REC) 
                                    VALUES 
                                    ('$id_depto','$id_mun',$a,$id_fuente,$id_tipo,$valor_mun,1)";
							//echo "$sql<br>";
						  	$conn->Execute($sql);
						}
					}
					if ($valor > 0){
						$acumulado_depto_exp[$id_fuente] += $valor;
                        $sql = "INSERT INTO registro_consulta_tmp 
                            (ID_DEPTO,AAAA,ID_FUENTE,ID_TIPO,VALOR,REC) 
                            VALUES 
                            ('$id_depto',$a,$id_fuente,$id_tipo,$valor,1)";
					  	$conn->Execute($sql);
					}
				}
			}
		}
	}
}

$sql = "INSERT INTO ocha_sissh.registro 
    (ID_TIPO_DESPLA,ID_MUN,ID_DEPTO,ID_CLASE_DESPLA,ID_MUN_ID_MUN,ID_FUEDES,ID_PERIO,ID_POBLA,ID_CONP,ID_DEPTO_ID_DEPTO,VALOR,FECHA_CORTE) 
    SELECT ID_TIPO_DESPLA,ID_MUN,ID_DEPTO,ID_CLASE_DESPLA,ID_MUN_ID_MUN,ID_FUEDES,ID_PERIO,ID_POBLA,ID_CONP,ID_DEPTO_ID_DEPTO,VALOR,FECHA_CORTE 
    FROM ocha_sissh_despla_import.registro";
$conn->Execute($sql);
$sql = "INSERT INTO ocha_sissh.registro_consulta_tmp SELECT * FROM ocha_sissh_despla_import.registro_consulta_tmp";
$conn->Execute($sql);

echo 'Listo!';
?>
