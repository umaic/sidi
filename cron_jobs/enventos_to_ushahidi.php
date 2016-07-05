<?

if (isset($_GET['id_depto']) && !preg_match('/^[0-9]{2}/',$_GET['id_depto']))   die;
else if (!isset($_GET['id_depto'])) die('No ha definido departamento en el url ?id_depto=20');


include_once("../admin/lib/common/mysqldb.class.php");
include_once("../admin/lib/common/archivo.class.php");

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
$archivo = New Archivo();
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

$db_cesar = 'sihcesar';
$form_id = 1;
$locale = 'en_US';
$user_id = 1;
$mode = 1;
$verified = 1;
$active = 1;
$source = 1;  // Source Reliability
$information = 1; // Information Probability
$rating = 0;
$alert = 0;

// Esta se toma del scrip de importacion de categorias cat_eventos_to_ushahidi.php
$id_cat_sidih_to_ushahidi = array(1 => 1, 2 => 10, 3 => 27, 4 => 35, 5 => 39, 6 => 48);
$id_scat_sidih_to_ushahidi = array(5 => 2, 7 => 3, 1 => 4, 2 => 5, 4 => 6, 8 => 7, 3 => 8, 6 => 9, 18 => 11, 15 => 12, 22 => 13, 23 => 14, 12 => 15, 10 => 16, 9 => 17, 11 => 18, 21 => 19, 48 => 20, 13 => 21, 14 => 22, 16 => 23, 20 => 24, 17 => 25, 19 => 26, 28 => 28, 29 => 29, 27 => 30, 25 => 31, 24 => 32, 26 => 33, 30 => 34, 47 => 36, 31 => 37, 32 => 38, 42 => 40, 40 => 41, 38 => 42, 36 => 43, 35 => 44, 39 => 45, 37 => 46, 41 => 47, 46 => 49, 45 => 50, 44 => 51, 43 => 52, 49 => 53);
//$eventos = $evento_dao->GetAllArray("fecha_reg_even > '2007-1-1' AND LENGTH(sintesis_even) > 0");
$sql = "SELECT * FROM evento_c";
if (isset($_GET['id_depto']))   $sql .= ' JOIN evento_localizacion USING (id_even)';

$sql .= "WHERE fecha_reg_even > '2007-1-1' AND LENGTH(sintesis_even) > 0 AND e.id_even NOT IN (SELECT sidih_id FROM $db_cesar.sidih_id_incident_id)";

if (isset($_GET['id_depto']))   $sql .= " AND SUBSTRING(id_mun,1,2) = '".$_GET['id_depto']."'";

$rs = $conn->OpenRecordset($sql);


//foreach ($eventos as $evento){
while ($evento = $conn->FetchObject($rs)){
    //var_dump($evento);
 
    $descripcion = $evento->SINTESIS_EVEN;
    $id_muns = array();
    $descs = array();

    if ($descripcion != ''){

        $id_evento = $evento->ID_EVEN;
        $fecha = $evento->FECHA_REG_EVEN;

        // Caso D.C
        $dc = 0;
        if (strpos($descripcion,'D.C.') !== false){
            $descripcion = str_replace('D.C.','D|C',$descripcion);
            $dc = 1;
        }

        $tmp = explode('.',$descripcion);
        
        if (count($tmp) == 3){
            if (isset($tmp[3])) $titulo = $tmp[0].'.'.$tmp[2].'.'.$tmp[3];
            else                $titulo = $tmp[0].'.'.$tmp[1].'.'.$tmp[2];
        }
        else    $titulo = $descripcion;


        if ($dc == 1)   $descripcion = str_replace('D|C.','D.C.',$descripcion);
            
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

                // Cats
                /*
                foreach ($id_cats as $id_c){
                    $sql = "INSERT INTO $db_cesar.incident_category (incident_id,category_id) VALUES ($incident_id,".$id_cat_sidih_to_ushahidi[$id_c].")";
                    $conn->Execute($sql);
                }
                */

                // Sub Cats
                foreach ($id_subcats as $id_s){
                    $sql = "INSERT INTO $db_cesar.incident_category (incident_id,category_id) VALUES ($incident_id,".$id_scat_sidih_to_ushahidi[$id_s].")";
                    $conn->Execute($sql);
                    
                    $id_inc_subcat = $conn->GetGeneratedID();

                    // Victimas asociadas a incidente-subcat
                    $sql = "SELECT id_sexo,id_subetnia,id_subcondicion,id_estado,id_ocupacion,id_raned,cant_victima,id_condicion,id_edad 
                         FROM ocha_sissh.victima v JOIN ocha_sissh.descripcion_evento d USING (id_deseven) JOIN 
                         ocha_sissh.evento_c e USING(id_even) WHERE e.id_even = $id_evento AND d.id_scateven = $id_s";

                    $rs_v = $conn->OpenRecordset($sql);
                    while ($row_v = $conn->FetchRow($rs_v)){
                     
                        $sql = "INSERT INTO $db_cesar.victim (victim_gender_id,victim_sub_ethnic_group_id,victim_sub_condition_id,victim_status_id,victim_occupation_id,victim_age_group_id,victim_cant,victim_condition_id,victim_age_id,incident_category_id) VALUES
                                                                  (".$row_v[0].",".$row_v[1].",".$row_v[2].",".$row_v[3].",".$row_v[4].",".$row_v[5].",".$row_v[6].",".$row_v[7].",".$row_v[8].",$id_inc_subcat)";
                        $conn->Execute($sql);
                    }

                }
            }
        }
    }
}

?>
