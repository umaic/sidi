<?php
ini_set('max_execution_time', 0);
ini_set('memory_limit','512M');

include_once("../admin/lib/common/mysqldb.class.php");

//MODEL
include_once("../admin/lib/model/evento_c.class.php");
include_once("../admin/lib/model/evento.class.php");
include_once("../admin/lib/model/municipio.class.php");
include_once("../admin/lib/model/depto.class.php");
include_once("../admin/lib/model/actor.class.php");
include_once("../admin/lib/model/cat_evento_c.class.php");
include_once("../admin/lib/model/subcat_evento_c.class.php");
include_once("../admin/lib/model/fuente_evento_c.class.php");
include_once("../admin/lib/model/subfuente_evento_c.class.php");
include_once("../admin/lib/model/edad.class.php");
include_once("../admin/lib/model/rango_edad.class.php");
include_once("../admin/lib/model/sexo.class.php");
include_once("../admin/lib/model/condicion_mina.class.php");
include_once("../admin/lib/model/subcondicion.class.php");
include_once("../admin/lib/model/estado_mina.class.php");
include_once("../admin/lib/model/etnia.class.php");
include_once("../admin/lib/model/subetnia.class.php");
include_once("../admin/lib/model/ocupacion.class.php");

//DAO
include_once("../admin/lib/dao/evento_c.class.php");
include_once("../admin/lib/dao/evento.class.php");
include_once("../admin/lib/dao/municipio.class.php");
include_once("../admin/lib/dao/depto.class.php");
include_once("../admin/lib/dao/actor.class.php");
include_once("../admin/lib/dao/cat_evento_c.class.php");
include_once("../admin/lib/dao/subcat_evento_c.class.php");
include_once("../admin/lib/dao/fuente_evento_c.class.php");
include_once("../admin/lib/dao/subfuente_evento_c.class.php");
include_once("../admin/lib/dao/edad.class.php");
include_once("../admin/lib/dao/rango_edad.class.php");
include_once("../admin/lib/dao/sexo.class.php");
include_once("../admin/lib/dao/condicion_mina.class.php");
include_once("../admin/lib/dao/subcondicion.class.php");
include_once("../admin/lib/dao/estado_mina.class.php");
include_once("../admin/lib/dao/etnia.class.php");
include_once("../admin/lib/dao/subetnia.class.php");
include_once("../admin/lib/dao/ocupacion.class.php");

$conn = MysqlDb::getInstance();
$evento_dao = New EventoConflictoDAO();
$mun_dao = New MunicipioDAO();
$depto_vo = New Depto();
$depto_dao = New DeptoDAO();
$actor_vo = New Actor();
$actor_dao = New ActorDAO();
$fuente_vo = New FuenteEventoConflicto();
$fuente_dao = New FuenteEventoConflictoDAO();
$subfuente_vo = New SubFuenteEventoConflicto();
$subfuente_dao = New SubFuenteEventoConflictoDAO();
$cat_vo = New CatEventoConflicto();
$cat_dao = New CatEventoConflictoDAO();
$subcat_vo = New SubCatEventoConflicto();
$subcat_dao = New SubCatEventoConflictoDAO();
$edad_dao = New EdadDAO();
$redad_dao = New RangoEdadDAO();
$estado_dao = New EstadoMinaDAO();
$condicion_dao = New CondicionMinaDAO();
$subcondicion_dao = New SubCondicionDAO();
$sexo_dao = New SexoDAO();
$etnia_dao = New EtniaDAO();
$setnia_dao = New SubEtniaDAO();
$ocupacion_dao = New OcupacionDAO();

$check_borrados = true;

function utf8deco(&$n) {
    //$n = addslashes($n);
    $n = addslashes(utf8_decode($n));
}

function file_get_contents_curl($url) {
    $ch = curl_init();
     
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
    curl_setopt($ch, CURLOPT_URL, $url);
     
    $data = curl_exec($ch);
    curl_close($ch);
     
    return $data;
}

$ayer = (empty($_GET['desde'])) ? date('Y-m-d',strtotime('-1 day')) : $_GET['desde'];
//$ayer = '2015-1-1';
$fn = "http://violenciaarmada.salahumanitaria.co/av/api/listar/sidih/$ayer";
//$fn = "http://sidih.salahumanitaria.co/sissh/sidih.json";
//$fn = "http://sidih.local/sidih.json";

file_get_contents_curl($fn);

// Esta linea se usa cuando sidih no trae el json directamente, error desconocido
$fn = "http://violenciaarmada.salahumanitaria.co/media/uploads/sidih_sync.json";

$json = file_get_contents_curl($fn);
$incs = json_decode($json);


// Borra los eventos que no existan en monitor
$borrados = 0;
if ($check_borrados) {
    $my_monitor = mysqli_connect('192.168.1.3','sissh','mjuiokm','violencia_armada');
    //$my_monitor = mysqli_connect('localhost','monitor','!7ujmmju7!','violencia_armada');

    $sql_borrar_sidih = "SELECT incident_id, sidih_id 
                         FROM evento_c_monitor ecm
                         INNER JOIN evento_c ec ON ecm.sidih_id = ec.id_even";
    $rs = $conn->OpenRecordset($sql_borrar_sidih);
    while ($row = $conn->FetchRow($rs)) {
        $incident_id = $row[0];
        $sidih_id = $row[1];

        $sql_borrar = "SELECT id FROM incident WHERE id = $incident_id";
        mysqli_real_query($my_monitor,$sql_borrar);
        $result = mysqli_fetch_row(mysqli_use_result($my_monitor));

        if (empty($result[0])) {
            // Borra en la tabla de equivalencias de ID's
            $evento_dao->Borrar($sidih_id);
            $borrados++;
        }
     
    }
}

foreach ($incs as $inc) {
		
    // Si se actualizó en monitor se borra en sidih y se vuelve a crear
    $incident_id = $inc->incident_id;

    //if ($inc->fecha_update != 'null') {
        $sql_sidih = "SELECT id, sidih_id 
                    FROM evento_c_monitor
                    WHERE incident_id = $incident_id";
        $rs_sidih = $conn->OpenRecordset($sql_sidih);

        while ($row_sidih = $conn->FetchObject($rs_sidih)) {

            $evento_dao->Borrar($row_sidih->sidih_id);
            
            // Borra en la tabla de equivalencias de ID's
            $sql = "DELETE FROM evento_c_monitor WHERE id = ".$row_sidih->id;
            $conn->Execute($sql);
        }
    //}

    $evento = new EventoConflicto;

    // UTF8 decode
    array_walk($inc->desc_fuente, 'utf8deco');
    array_walk($inc->refer_fuente, 'utf8deco');
    array_walk($inc->lugar, 'utf8deco');

    $evento->fecha_evento = $inc->fecha_evento;
    $evento->sintesis = addslashes(utf8_decode($inc->sintesis));

    $evento->id_cat = $inc->id_cat;
    $evento->id_subcat = $inc->id_subcat;

    //$evento->id_fuente = $inc->id_fuente;
    $evento->id_subfuente = $inc->id_subfuente;
    $evento->fecha_fuente = $inc->fecha_fuente;
    $evento->desc_fuente = $inc->desc_fuente;
    $evento->refer_fuente = $inc->refer_fuente;

    $evento->id_actor_0 = $inc->id_actor_0;
    $evento->id_actor = $inc->id_actor;
    $evento->id_subactor =  $inc->id_subactor;
    $evento->id_subsubactor =  $inc->id_subsubactor;

    $evento->id_edad = $inc->id_edad;
    $evento->id_rango_edad = $inc->id_rango_edad;
    $evento->id_sexo = $inc->id_sexo;
    $evento->id_condicion = $inc->id_condicion;
    $evento->id_subcondicion = $inc->id_sub_cond;
    $evento->id_estado = $inc->id_estado;
    $evento->id_etnia = $inc->id_etnia;
    $evento->id_subetnia = $inc->id_sub_etnia;
    $evento->id_ocupacion = $inc->id_ocupacion;
    $evento->num_victimas = $inc->num_victimas;

    $evento->id_mun = $inc->id_muns;
    $evento->lugar = $inc->lugar;
    
    $evento_dao = new EventoConflictoDAO();

    $evento_dao->Insertar($evento,0,$inc->num_vict_desc,$inc->num_actores_0_desc,$inc->num_actores_desc,$inc->num_subactores_desc,$inc->num_subsubactores_desc);
    
    $sql_id = 'SELECT MAX(id_even) FROM evento_c';
    $rs_id = $conn->OpenRecordset($sql_id);
    $row = $conn->FetchRow($rs_id);
    $sidih_id = $row[0];

    if (!empty($sidih_id)) {
        $sqli = "INSERT INTO evento_c_monitor (sidih_id, incident_id,import_date) VALUES ($sidih_id, ".$inc->incident_id.",now())";
        $conn->Execute($sqli);
    }
}

echo 'Borrados: '.$borrados.'- Importados: '.count($incs);

//ENVIA EMAIL
$from = "rojasr@un.org";

require($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/common/class.phpmailer.php");

$mail = new PHPMailer();

$mail->IsSMTP(); // set mailer to use SMTP

$mail->From = $from;
$mail->FromName = "SIDIH OCHA";
$mail->AddAddress("rubasrojas@gmail.com", "Ruben Rojas");

$mail->WordWrap = 50;                                 // set word wrap to 50 characters
$mail->IsHTML(true);                                  // set email format to HTML

$mail->Subject = "Sync Monitor :: Sidih";
$mail->Body    = date('d-F-Y').', Borrados: '.$borrados.', Importados: '.count($incs);

$mail->Send();
?>
