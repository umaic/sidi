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

	$this->vo = new Proyecto();
	$this->proyecto_dao = new ProyectoDAO();

	if ($accion == 'insertar') {
	    $this->parseForm();
	    $this->proyecto_dao->Insertar($this->vo);
	}
	else if ($accion == 'actualizar'){
	    $this->parseForm();
	    $this->proyecto_dao->Actualizar($this->vo);
	}
	else if ($accion == 'borrar') {
	    $this->proyecto_dao->Borrar($_GET["id"]);
	}
	else if ($accion == 'importar') {
	    $import = (isset($_POST["import"])) ? $_POST["import"] : 0;
	    $this->proyecto_dao->ImportarCSV($_FILES['archivo_csv'],$import);
	}
    }

    /**
     * Realiza el Parse de las variables de la forma y las asigna al VO de Proyecto (variable de clase) 
     * @access public	
     */	
    function parseForm() {

	$this->vo->id_proy = 0;
	if (isset($_POST["id"]) && strlen($_POST["id"]) > 0)	$this->vo->id_proy = $_POST["id"];

	$this->vo->id_mon = 0;
	if (isset($_POST["id_mon"]) && strlen($_POST["id_mon"]) > 0)	$this->vo->id_mon = $_POST["id_mon"];

	$this->vo->id_estp = 0;
	if (isset($_POST["id_estp"]) && strlen($_POST["id_estp"]) > 0)	$this->vo->id_estp = $_POST["id_estp"];
	if (isset($_POST["nom_proy"]))	$this->vo->nom_proy = $_POST["nom_proy"];
	if (isset($_POST["cod_proy"]))	$this->vo->cod_proy = $_POST["cod_proy"];
	if (isset($_POST["des_proy"]))	$this->vo->des_proy = $_POST["des_proy"];
	if (isset($_POST["obj_proy"]))	$this->vo->obj_proy = $_POST["obj_proy"];
	if (isset($_POST["inicio_proy"]))	$this->vo->inicio_proy = $_POST["inicio_proy"];
	if (isset($_POST["fin_proy"]))	$this->vo->fin_proy = $_POST["fin_proy"];
	if (isset($_POST["actua_proy"]))	$this->vo->actua_proy = $_POST["actua_proy"];

	$this->vo->costo_proy = 0;
	if (isset($_POST["costo_proy"]) && strlen($_POST["costo_proy"]) > 0)	            $this->vo->costo_proy = $_POST["costo_proy"];
	else if (isset($_POST["costo_proy_ind"]) && strlen($_POST["costo_proy_ind"]) > 0)	$this->vo->costo_proy = $_POST["costo_proy_ind"];

	$this->vo->duracion_proy = 0;
	if (isset($_POST["duracion_proy"]) && strlen($_POST["duracion_proy"]) > 0)	$this->vo->duracion_proy = $_POST["duracion_proy"];

	$this->vo->info_conf_proy = 0;
	if (isset($_POST["info_conf_proy"]) && strlen($_POST["info_conf_proy"]) > 0)	$this->vo->info_conf_proy = $_POST["info_conf_proy"];

	$this->vo->staff_nal_proy = 0;
	if (isset($_POST["staff_nal_proy"]) && strlen($_POST["staff_nal_proy"]) > 0)	$this->vo->staff_nal_proy = $_POST["staff_nal_proy"];

	$this->vo->staff_intal_proy = 0;
	if (isset($_POST["staff_intal_proy"]) && strlen($_POST["staff_intal_proy"]) > 0)	$this->vo->staff_intal_proy = $_POST["staff_intal_proy"];

	$this->vo->cobertura_nal_proy = 0;
	if (isset($_POST["cobertura_nal_proy"]) && strlen($_POST["cobertura_nal_proy"]) > 0)	$this->vo->cobertura_nal_proy = $_POST["cobertura_nal_proy"];

	$this->vo->cant_benf_proy = 0;
	if (isset($_POST["cant_benf_proy"]) && strlen($_POST["cant_benf_proy"]) > 0)	$this->vo->cant_benf_proy = $_POST["cant_benf_proy"];
        
        $id_pob_otros = 44;
	$this->vo->otro_cual_benf_proy = (isset($_POST["otro_cual_benf_proy"]) && isset($_POST["id_beneficiarios"]) && (in_array($id_pob_otros,$_POST["id_beneficiarios"]) || (isset($_POST["id_beneficiarios_indirectos"]) && in_array($id_pob_otros,$_POST["id_beneficiarios_indirectos"])))) ? $_POST["otro_cual_benf_proy"] : '';
	
	
	if (isset($_POST["valor_aporte_donantes"]))	$this->vo->valor_aporte_donantes = $_POST["valor_aporte_donantes"];
	if (isset($_POST["valor_aporte_socios"]))	$this->vo->valor_aporte_socios = $_POST["valor_aporte_socios"];
	if (isset($_POST["info_extra_donantes"]))	$this->vo->info_extra_donantes = $_POST["info_extra_donantes"];
	if (isset($_POST["info_extra_socios"]))	$this->vo->info_extra_socios = $_POST["info_extra_socios"];
	
        // Ahora el marco viene de un combo, es necesario hacer case
        $this->vo->joint_programme_proy = 0;
        $this->vo->mou_proy = 0;
        $this->vo->acuerdo_coop_proy = 0;
        $this->vo->interv_ind_proy =0;
        
        switch ($_POST['marco_proy']){
            case 1:
                $this->vo->joint_programme_proy = 1;
                break;
            
            case 2:
                $this->vo->mou_proy = 1;
            break;

            case 3:
                $this->vo->interv_ind_proy = 1;
            break;
        }

	// Desde donde se crea el proyecto
	$this->vo->si_proy = (isset($_POST["si_proy"])) ? $_POST["si_proy"] : 'undaf';

	//Temas
	$this->vo->id_temas = Array();
	if (isset($_POST["id_temas"])){
		foreach ($_POST["id_temas"] as $id_tema){
			$this->vo->id_temas[$id_tema] = array();
	
			//Texto extra temas
			if (isset( $_POST["texto_extra_tema_$id_tema"]))	$this->vo->texto_extra_tema[$id_tema] = $_POST["texto_extra_tema_$id_tema"];

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
	

	//Beneficiarios directos
	$this->vo->id_beneficiarios = Array();
	if (isset($_POST["id_beneficiarios"]))	$this->vo->id_beneficiarios = $_POST["id_beneficiarios"]; 

	//Beneficiarios indirectos
	$this->vo->id_beneficiarios_indirectos = Array();
	if (isset($_POST["id_beneficiarios_indirectos"]))	$this->vo->id_beneficiarios_indirectos = $_POST["id_beneficiarios_indirectos"]; 

	//Ejecutores
	$this->vo->id_orgs_e = Array();
	if (isset($_POST["id_orgs_e"]) && strlen($_POST["id_orgs_e"]) > 0){
	    $this->vo->id_orgs_e = explode("|",$_POST["id_orgs_e"]);
	}

	//Donantes
	$this->vo->id_orgs_d = Array();
	if (isset($_POST["id_orgs_d"]) && strlen($_POST["id_orgs_d"]) > 0){
	    $this->vo->id_orgs_d = explode("|",$_POST["id_orgs_d"]);
	}

	//Socios
	$this->vo->id_orgs_s = Array();
	if (isset($_POST["id_orgs_s"]) && strlen($_POST["id_orgs_s"]) > 0){
	    $this->vo->id_orgs_s = explode("|",$_POST["id_orgs_s"]);
	}
	
        //Oficina desde la que se cubre
	$this->vo->id_orgs_cubre = Array();
	if (isset($_POST["id_orgs_cubre"])){
	    $this->vo->id_orgs_cubre = $_POST["id_orgs_cubre"];
	}

	//Trabajo coordinado
	$this->vo->id_orgs_coor = Array();
	if (isset($_POST["id_orgs_coor"]) && $this->vo->interv_ind_proy == 0){
	    $this->vo->id_orgs_coor = $_POST["id_orgs_coor"];
	}

	// Trabajo coordinado Aportes
	$this->vo->id_orgs_coor_valor_ap = Array();
        if (isset($_POST["id_orgs_coor_valor_ap"]) && $this->vo->interv_ind_proy == 0){
            foreach ($_POST["id_orgs_coor_valor_ap"] as $valor){
                if (isset($valor[1]))
	            $this->vo->id_orgs_coor_valor_ap[] = $valor;
            }
        }
        
        //Cobertura Geo
	$this->vo->id_deptos = Array();
	if (isset($_POST["id_depto"])){
	    $this->vo->id_deptos = $_POST["id_depto"];
	}

	$this->vo->id_muns = Array();
	if (isset($_POST["id_muns"])){
	    $this->vo->id_muns = $_POST["id_muns"];
	}


	//Calcula la fecha fin a partir de f_ini + meses duracion
	$date = new Date();
	$this->vo->fin_proy = $date->sumValorFecha($this->vo->inicio_proy,$this->vo->duracion_proy,'mes');

	//Completa los campos que se tenian en la version 1, pero que no se eliminan de la tabla
	if(!isset($this->vo->cod_proy))	$this->vo->cod_proy = '';
	if(!isset($this->vo->des_proy))	$this->vo->des_proy = '';
	if(!isset($this->vo->obj_proy))	$this->vo->obj_proy = '';
	if(!isset($this->vo->info_conf_proy))	$this->vo->info_conf_proy = 1;
    }
}
?>
