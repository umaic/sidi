<?
if (!isset($_GET['id_depto']) || !isset($_GET['x']) ) die('x1');
if (isset($_GET['id_depto']) && !preg_match('/^[0-9]{2}/',$_GET['id_depto']))   die('x2');
if (isset($_GET['x']) && $_GET['x'] != 'w3x')   die('x3');
if (!isset($_GET['x']))   die('x4');

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


// Replacing Microsoft Windows smart quotes, as sgaston demonstrated on 2006-02-13, I replace all other Microsoft Windows characters
function win_replace($str){
    $str = str_replace(chr(130), ',', $str);    // baseline single quote
    $str = str_replace(chr(131), 'NLG', $str);  // florin
    $str = str_replace(chr(132), '"', $str);    // baseline double quote
    $str = str_replace(chr(133), '...', $str);  // ellipsis
    $str = str_replace(chr(134), '**', $str);   // dagger (a second footnote)
    $str = str_replace(chr(135), '***', $str);  // double dagger (a third footnote)
    $str = str_replace(chr(136), '^', $str);    // circumflex accent
    $str = str_replace(chr(137), 'o/oo', $str); // permile
    $str = str_replace(chr(138), 'Sh', $str);   // S Hacek
    $str = str_replace(chr(139), '<', $str);    // left single guillemet
    $str = str_replace(chr(140), 'OE', $str);   // OE ligature
    $str = str_replace(chr(145), "'", $str);    // left single quote
    $str = str_replace(chr(146), "'", $str);    // right single quote
    $str = str_replace(chr(147), '"', $str);    // left double quote
    $str = str_replace(chr(148), '"', $str);    // right double quote
    $str = str_replace(chr(149), '-', $str);    // bullet
    $str = str_replace(chr(150), '-', $str);    // endash
    $str = str_replace(chr(151), '--', $str);   // emdash
    $str = str_replace(chr(152), '~', $str);    // tilde accent
    $str = str_replace(chr(153), '(TM)', $str); // trademark ligature
    $str = str_replace(chr(154), 'sh', $str);   // s Hacek
    $str = str_replace(chr(155), '>', $str);    // right single guillemet
    $str = str_replace(chr(156), 'oe', $str);   // oe ligature
    $str = str_replace(chr(159), 'Y', $str);    // Y Dieresis

    return $str;
}

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

$form_id = 1;
$locale = 'es_AR';
$user_id = 1;
$mode = 1;
$verified = 1;
$active = 1;
$source = 1;  // Source Reliability
$information = 1; // Information Probability
$rating = 0;
$alert = 0;

// Fecha inicial
$f_ini = (isset($_GET['f_ini']) && $_GET['f_ini'] != '') ? $_GET['f_ini'] : '2007-1-1' ;
$id_depto = $_GET['id_depto'];

// Esta se toma del scrip de importacion de categorias cat_eventos_to_ushahidi.php
$id_cat_sidih_to_ushahidi = array(1 => 1, 2 => 10, 3 => 27, 4 => 35, 5 => 39, 6 => 48);
$id_scat_sidih_to_ushahidi = array(5 => 2, 7 => 3, 1 => 4, 2 => 5, 4 => 6, 8 => 7, 3 => 8, 6 => 9, 18 => 11, 15 => 12, 22 => 13, 23 => 14, 12 => 15, 10 => 16, 9 => 17, 11 => 18, 21 => 19, 48 => 20, 13 => 21, 14 => 22, 16 => 23, 20 => 24, 17 => 25, 19 => 26, 28 => 28, 29 => 29, 27 => 30, 25 => 31, 24 => 32, 26 => 33, 30 => 34, 47 => 36, 31 => 37, 32 => 38, 42 => 40, 40 => 41, 38 => 42, 36 => 43, 35 => 44, 39 => 45, 37 => 46, 41 => 47, 46 => 49, 45 => 50, 44 => 51, 43 => 52, 49 => 53);

$sql = "SELECT c.* FROM evento_c c ";
if (isset($_GET['id_depto']))   $sql .= ' JOIN evento_localizacion USING (id_even)';

$sql .= " WHERE fecha_reg_even > '$f_ini' AND LENGTH(sintesis_even) > 0";

if (isset($_GET['id_depto']))   $sql .= " AND id_mun LIKE '$id_depto%'";

$sql .= ' ORDER BY fecha_reg_even';

$rs = $conn->OpenRecordset($sql);

//foreach ($eventos as $evento){
while ($evento = $conn->FetchObject($rs)){
    //var_dump($evento);
 
    $sintesis = $evento->SINTESIS_EVEN;
    $id_muns = array();
    $descs = array();

    if (strlen($sintesis) > 5){

        $id_evento = $evento->ID_EVEN;
        $fecha = $evento->FECHA_REG_EVEN;

        $fuente = $evento_dao->getFuenteEvento($id_evento);
        // Al menos una fuente
        if (isset($fuente['desc'][0]) && strlen($fuente['desc'][0]) > 10){
            
            // Eliminar comillas left y right de la desc. fuente y demas chars
            $descripcion = utf8_encode(win_replace($fuente['desc'][0]));
        }
        else
            $descripcion = $sintesis;

        // Caso D.C
        $dc = 0;
        if (strpos($sintesis,'D.C.') !== false){
            $sintesis = str_replace('D.C.','D|C',$sintesis);
            $dc = 1;
        }

        $tmp = explode('.',$sintesis);
        
        if (count($tmp) == 3){
            if (isset($tmp[3])) $titulo = $tmp[0].'.'.$tmp[2].'.'.$tmp[3];
            else                $titulo = $tmp[0].'.'.$tmp[1].'.'.$tmp[2];
        }
        else    $titulo = $sintesis;


        //if ($dc == 1)   $descripcion = str_replace('D|C.','D.C.',$descripcion);
            
        $descs = $evento_dao->getDescripcionEvento($id_evento);
        $id_cats = array_unique($descs['id_cat']);
        $id_subcats = array_unique($descs['id_scat']);
        //var_dump($id_cats);
        //var_dump($id_subcats);

        // Continua si el evento está categorizado
        if (count($id_subcats) > 0){

            //echo '*******'.$descripcion.'<br />';
            //echo $titulo.'<br />';
            $localizacion = $evento_dao->getLocalizacionEvento($id_evento);
            $id_muns = $localizacion['mpios'];
            //var_dump($id_muns);
            
            // Si tiene varios mpios, crea varios eventos

            foreach ($id_muns as $id_mun){
                
                $mun = $mun_dao->Get($id_mun);
                $incident = array();
                $category = array();
                $victim = array();

                /*
                $sql_c = "SELECT country_id,city,city_lat,city_lon,1,now() FROM $db_cesar.city WHERE divipola = '$id_mun'";
                $rs_c = $conn->OpenRecordset($sql_c);
                $row_c = $conn->FetchRow($rs_c);

                $location['country_id'] = $row_c[0];
                $location['city'] = $row_c[1];
                $location['city_lat'] = $row_c[2];
                $location['city_lon'] = $row_c[3];
                */
                
                $incident['form_id'] = $form_id;
                $incident['locale'] = $locale;
                $incident['user_id'] = $user_id;
                $incident['incident_title'] = utf8_encode($titulo);
                $incident['incident_description'] = utf8_encode($descripcion);
                $incident['incident_date'] = $fecha;
                $incident['incident_mode'] = $mode;
                $incident['incident_active'] = $active;
                $incident['incident_verified'] = $verified;
                $incident['incident_source'] = $source;
                $incident['incident_information'] = $information;
                $incident['incident_rating'] = $rating;
                $incident['incident_alert_status'] = $alert;
                $incident['mun_id'] = $id_mun; 
                $incident['sidih_id'] = $id_evento; 
                


                
                /*
                $sql = "INSERT INTO $db_cesar.location (country_id,location_name,latitude,longitude,location_visible,location_date) SELECT country_id,city,city_lat,city_lon,1,now() FROM $db_cesar.city WHERE divipola = '$id_mun'";
                $conn->Execute($sql);

                $location_id = $conn->GetGeneratedID();
                
                $sql = "INSERT INTO $db_cesar.incident (location_id,form_id,locale,user_id,incident_title,incident_description,incident_date,incident_mode,incident_active,incident_verified,incident_source,incident_information,incident_rating,incident_dateadd,incident_alert_status) 
                        VALUES 
                                             ($location_id,$form_id,'$locale',$user_id,'".addslashes($titulo)."','".addslashes($descripcion)."','$fecha',$mode,$active,$verified,'$source','$information','$rating',now(),$alert)";
                
                $conn->Execute($sql);
                $incident_id = $conn->GetGeneratedID();

                // Log
                $sql = "INSERT INTO $db_cesar.sidih_id_incident_id (sidih_id,incident_id) VALUES ($id_evento,$incident_id)";
                $conn->Execute($sql);
                
                // Verificado
                $sql = "INSERT INTO $db_cesar.verified (incident_id,user_id,verified_date,verified_status) VALUES ($incident_id,$user_id,$fecha,1)";
                $conn->Execute($sql);
                */

                // Sub Cats
                $category = array();
                $num_v = 0;
                foreach ($id_subcats as $id_s){
                    
                    $category[] = $id_scat_sidih_to_ushahidi[$id_s];

                    /*
                    $sql = "INSERT INTO $db_cesar.incident_category (incident_id,category_id) VALUES ($incident_id,".$id_scat_sidih_to_ushahidi[$id_s].")";
                    $conn->Execute($sql);
                                     
                    $id_inc_subcat = $conn->GetGeneratedID();
                    */

                    // Victimas asociadas a incidente-subcat
                    $sql = "SELECT id_sexo,id_subetnia,id_subcondicion,id_estado,id_ocupacion,id_raned,cant_victima,id_condicion,id_edad,cant_victima
                         FROM sidi.victima v JOIN sidi.descripcion_evento d USING (id_deseven) JOIN 
                         sidi.evento_c e USING(id_even) WHERE e.id_even = $id_evento AND d.id_scateven = $id_s AND cant_victima > 0";

                    $rs_v = $conn->OpenRecordset($sql);
                    $victim = array();
                    while ($row_v = $conn->FetchRow($rs_v)){
                        
                        $vict = array();
                        $vict['victim_gender_id'] = $row_v[0];
                        $vict['victim_sub_ethnic_group_id'] = $row_v[1];
                        $vict['victim_sub_condition_id'] = $row_v[2];
                        $vict['victim_status_id'] = $row_v[3];
                        $vict['victim_occupation_id'] = $row_v[4];
                        $vict['victim_age_group_id'] = $row_v[5];
                        $vict['victim_cant'] = $row_v[6];
                        $vict['victim_condition_id'] = $row_v[7];
                        $vict['victim_age_id'] = $row_v[8];
                        $vict['victim_cant'] = $row_v[9];

                        $victim[] = $vict;

                        /*
                        $sql = "INSERT INTO $db_cesar.victim (victim_gender_id,victim_sub_ethnic_group_id,victim_sub_condition_id,victim_status_id,victim_occupation_id,victim_age_group_id,victim_cant,victim_condition_id,victim_age_id,incident_category_id) VALUES
                                                                  (".$row_v[0].",".$row_v[1].",".$row_v[2].",".$row_v[3].",".$row_v[4].",".$row_v[5].",".$row_v[6].",".$row_v[7].",".$row_v[8].",$id_inc_subcat)";
                        $conn->Execute($sql);
                        */

                        $num_v++;
                        
                    }

                }
                
                //echo "$id_evento tiene <b><font style='font-size:26px'>$num_v</font></b> víctimas <br />";
                
                $json[] = array('incident' => $incident, 'category' => $category, 'victim' => $victim);
            }
        }
    }
}

//$json['num_total'] = count($json);

header('Content-type: application/json');
echo json_encode($json);

?>
