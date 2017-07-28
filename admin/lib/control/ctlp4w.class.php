<?
/**
 * Maneja todas las acciones de administración de Proyectos
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

    /**
     * VO de Proyecto
     * @var object 
     */
    var $vo;

    /**
     * Variable para el manejo de la clase ProyectoDAO
     * @var object 
     */
    var $proyecto_dao;

    /**
     * Constructor
     * Crea la conexión a la base de datos y ejecuta la accion
     * @access public
     * @param string $accion Variable que indica la accion a realizar
     */	
    function ControladorPagina($accion) {

        $this->vo = new P4w();
        $this->proyecto_dao = new P4wDAO();

        if ($accion == 'insertar') {
            $this->parseForm();
            $this->proyecto_dao->Insertar($this->vo);
            
            // Borra static cache
            require_once "lib/dao/sissh.class.php";
            $si = New SisshDAO();
            $si->borrarCacheStatic('4w');
        }
        else if ($accion == 'actualizar'){
            $this->parseForm();
            $this->proyecto_dao->Actualizar($this->vo);
            
            
            // Borra static cache
            require_once "lib/dao/sissh.class.php";
            $si = New SisshDAO();
            $si->borrarCacheStatic('4w');
        }
        else if ($accion == 'borrar') {
            $this->proyecto_dao->Borrar($_GET["id"]);
        }
    }

    /**
     * Realiza el Parse de las variables de la forma y las asigna al VO de Proyecto (variable de clase) 
     * @access public	
     */	
    function parseForm() {

        $this->vo->id_proy = 0;
        if (!empty($_POST["id"])){
            $this->vo->id_proy = $_POST["id"];
        }

        $this->vo->id_mon = $_POST["id_mon"];
        $this->vo->id_estp = $_POST["id_estp"];
        $this->vo->id_emergencia = (!empty($_POST["id_emergencia"])) ? $_POST["id_emergencia"] : 0;
        $this->vo->nom_proy = $_POST["nom_proy"];
        $this->vo->inicio_proy = $_POST["inicio_proy"];
        $this->vo->srp_proy = $_POST["srp_proy"];
        $this->vo->cod_proy = (!empty($_POST["cod_proy"])) ? $_POST["cod_proy"] : '';
        $this->vo->des_proy = (!empty($_POST["des_proy"])) ? $_POST["des_proy"] : '';
        $this->vo->obj_proy = (!empty($_POST["obj_proy"])) ? $_POST["obj_proy"] : '';
        $this->vo->fin_proy = (!empty($_POST["fin_proy"])) ? $_POST["fin_proy"] : '';
        $this->vo->duracion_proy = (!empty($_POST["duracion_proy"])) ? $_POST["duracion_proy"] : '';
        $this->vo->id_con = (!empty($_POST["id_con"])) ? $_POST["id_con"] : 0;
        $this->vo->info_conf_proy = (isset($this->vo->info_conf_proy)) ? 1 : 0;
        $this->vo->costo_proy = (!empty($_POST["costo_proy"])) ? $_POST["costo_proy"] : 0;
        $this->vo->cobertura_nal_proy = (!empty($_POST["cobertura_nal_proy"])) ? $_POST["cobertura_nal_proy"] : 0;
        
        // Cluster
        $this->vo->validado_cluster_proy = (in_array($_SESSION['id_tipo_usuario_s'], array(1,42,23,27))) ? 1 : 0;
        
        /*
        $this->vo->duracion_proy = 0;
        if (!empty($_POST["duracion_proy"])) {
            $this->vo->duracion_proy = $_POST["duracion_proy"];
            
            //Calcula la fecha fin a partir de f_ini + meses duracion
            $date = new Date();
            $this->vo->fin_proy = $date->sumValorFecha($this->vo->inicio_proy,$this->vo->duracion_proy, 'mes');
        }
        else {
            //Calcula los meses
            $date = new Date();
            $this->vo->duracion_proy = $date->RestarFechas($this->vo->inicio_proy,$this->vo->fin_proy, 'meses');
        }
         */

        $this->vo->cant_benf_proy = (!empty($_POST["cant_benf_proy"])) ? $_POST["cant_benf_proy"] : '';
        $this->vo->benf_proy = $_POST['benf_proy'];
            
        $this->vo->otro_cual_benf_proy = '';
        
        $this->vo->info_conf_proy = 0;
        $this->vo->staff_nal_proy = 0;
        $this->vo->staff_intal_proy = 0;
        // Ahora el marco viene de un combo, es necesario hacer case
        $this->vo->joint_programme_proy = 0;
        $this->vo->mou_proy = 0;
        $this->vo->acuerdo_coop_proy = 0;
        $this->vo->interv_ind_proy =0;
        //Oficina desde la que se cubre
        $this->vo->id_orgs_cubre = Array();

        //Trabajo coordinado
        $this->vo->id_orgs_coor = Array();

        // Trabajo coordinado Aportes
        $this->vo->id_orgs_coor_valor_ap = Array();
        
        // Desde donde se crea el proyecto
        $this->vo->si_proy = (isset($_POST["si_proy"])) ? $_POST["si_proy"] : 'undaf';

        //Temas
        $this->vo->id_tema_p = (isset($_POST['id_tema_p'])) ? $_POST['id_tema_p'] : 0;
        $this->vo->id_temas = Array();
        if (isset($_POST["id_temas"])){
            foreach ($_POST["id_temas"] as $id_tema){
                $this->vo->id_temas[$id_tema] = array();
        
                //Texto extra temas
                if (isset( $_POST["texto_extra_tema_$id_tema"]))	$this->vo->texto_extra_tema[$id_tema] = $_POST["texto_extra_tema_$id_tema"];

                // Presupuesto
                if (isset( $_POST["tema_presupuesto_$id_tema"]))	$this->vo->temas_presupuesto[$id_tema] = $_POST["tema_presupuesto_$id_tema"];

                //Hijos
                $name_field_hijo = "id_tema_$id_tema";
                if (isset($_POST[$name_field_hijo])){
                    $this->vo->id_temas[$id_tema]["hijos"] = $_POST[$name_field_hijo];
                    foreach ($_POST[$name_field_hijo] as $id_subtema){
                        //Nietos
                        $name_field_nieto = "id_subtema_$id_tema";
                        if (isset($_POST[$name_field_nieto])){
                            $this->vo->id_temas[$id_tema]["nietos"] = $_POST[$name_field_nieto];
                        }
                    }
                }
            }
        }
        
        //Ejecutores
        $this->vo->id_orgs_e = Array();
        if (!empty($_POST["id_orgs_e"])){
            $this->vo->id_orgs_e = $_POST["id_orgs_e"];
        }

        //Donantes
        $this->vo->id_orgs_d = Array();
        if (!empty($_POST["id_orgs_d"])){
            $this->vo->id_orgs_d = $_POST["id_orgs_d"];
            array_shift($this->vo->id_orgs_d);
        }
        
        // Aporte donantes
        foreach($this->vo->id_orgs_d as $i => $id_od) {
            $this->vo->id_orgs_d_valor_ap[$id_od] = $_POST['valor_org_d_'.$i];
        }

        // Cod proy donantes
        foreach($this->vo->id_orgs_d as $i => $id_od) {
            $this->vo->id_orgs_d_codigo[$id_od] = $_POST['codigo_org_d_'.$i];
        }

        //Socios
        $this->vo->id_orgs_s = Array();
        if (!empty($_POST["id_orgs_s"])){
            $this->vo->id_orgs_s = $_POST["id_orgs_s"];
            array_shift($this->vo->id_orgs_s);
        }
        
        //Cobertura Geo
        $this->vo->id_deptos = (isset($_POST["id_deptos"])) ? $_POST["id_deptos"] : array();
        $this->vo->id_muns = (isset($_POST["id_muns"])) ? array_unique($_POST["id_muns"]) : array();
        $this->vo->latitude = (isset($_POST["latitude"])) ? $_POST["latitude"] : array();
        $this->vo->longitude = (isset($_POST["longitude"])) ? $_POST["longitude"] : array();

        $this->vo->id_albergues = (isset($_POST["id_albergues"])) ? $_POST["id_albergues"] : array();

	    $this->vo->inter = (intval($_POST["inter"]) == 0 || intval($_POST["inter"]) == 1) ? $_POST["inter"] : 0;

	    $this->vo->cbt_ma = (intval($_POST["cbt_ma"]) > 0) ? $_POST["cbt_ma"] : NULL;
	    $this->vo->cbt_me = (intval($_POST["cbt_me"]) > 0) ? $_POST["cbt_me"] : NULL;
	    $this->vo->cbt_f = (intval($_POST["cbt_f"]) > 0) ? $_POST["cbt_f"] : NULL;
	    $this->vo->cbt_val = (intval($_POST["cbt_val"]) > 0) ? $_POST["cbt_val"] : NULL;

	    $this->vo->tip_proy = (intval($_POST["tip_proy"]) > 0) ? $_POST["tip_proy"] : NULL;
	    $this->vo->ofar =  $_POST["ofar"];

	    $this->vo->costo_proy1 = (!empty($_POST["costo_proy1"])) ? $_POST["costo_proy1"] : 0;
	    $this->vo->costo_proy2 = (!empty($_POST["costo_proy2"])) ? $_POST["costo_proy2"] : 0;
	    $this->vo->costo_proy3 = (!empty($_POST["costo_proy3"])) ? $_POST["costo_proy3"] : 0;
	    $this->vo->costo_proy4 = (!empty($_POST["costo_proy4"])) ? $_POST["costo_proy4"] : 0;
	    $this->vo->costo_proy5 = (!empty($_POST["costo_proy5"])) ? $_POST["costo_proy5"] : 0;

	    //Organizaciones Beneficiarias
	    $this->vo->id_orgs_b = Array();
	    if (!empty($_POST["id_orgs_b"])){
		    $this->vo->id_orgs_b = array_filter($_POST["id_orgs_b"]);
	    }

	    $this->vo->num_vic = (!empty($_POST["num_vic"])) ? $_POST["num_vic"] : 0;
	    $this->vo->num_afe = (!empty($_POST["num_afe"])) ? $_POST["num_afe"] : 0;
	    $this->vo->num_des = (!empty($_POST["num_des"])) ? $_POST["num_des"] : 0;
	    $this->vo->num_afr = (!empty($_POST["num_afr"])) ? $_POST["num_afr"] : 0;
	    $this->vo->num_ind = (!empty($_POST["num_ind"])) ? $_POST["num_ind"] : 0;

	    $this->vo->soportes = (!empty($_POST["soportes"])) ? $_POST["soportes"] : '';
    }
}
?>
