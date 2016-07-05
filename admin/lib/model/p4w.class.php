<?
/**
 * Maneja todas las propiedades del Objeto Proyecto
 * Valores de Objeto VO
 * @author Ruben A. Rojas C.
 */

Class P4w {
    /**
     * Identificador del proyecto, se auto incrementa.
     * @var int
     */
    var $id_proy;

    /**
     * Almacena el identificador de la moneda
     * @var int
     */
    var $id_mon;

    /**
     * Almacena el identificador del estado del proyecto
     * @var int
     */
    var $id_estp;

    /**
     * Almacena el identificador de la emergencia
     * @var int
     */
    var $id_emergencia;

    /**
     * Almacena el identificador del contacto en terreno
     * @var int
     */
    var $id_con;

    /**
     * Nombre del Proyecto
     * @var string
     */
    var $nom_proy;

    /**
     * Código del proyecto,  este es asignado por el proponente del proyecto
     * @var string
     */
    var $cod_proy = '';

    /**
     * Descripción del proyecto, alcances, metodología, cronograma. se puede escribir toda la información que el proponente del proyecto suministre.
     * @var string
     */
    var $des_proy;

    /**
     * Objetivos del Proyecto
     * @var string
     */
    var $obj_proy;

    /**
     * Fecha de iniciación del proyecto
     * @var string
     */
    var $inicio_proy;

    /**
     * Fecha de finalización del proyecto
     * @var string
     */
    var $fin_proy;
    
    /**
     * Hace parte del SRP
     * @var int
     */
    var $srp_proy;

    /**
     * Fecha de creación
     * @var string
     */
    var $creac_proy;

    /**
     * Fecha de última actualización del proyecto en OCHA
     * @var string
     */
    var $actua_proy;

    /**
     * Almacena el valor del costo total del proyecto
     * @var int
     */
    var $costo_proy;

    /**
     * Duración en meses del proyecto, si existe Fecha Inicial y Fecha Final debe coincidir
     * @var int
     */
    var $duracion_proy;

    /**
     * La información del proyecto está confirmada?
     * @var int
     */
    var $info_conf_proy;

    /**
     * Staff nacional dedicado al proyecto (número de personas)
     * @var int
     */
    var $staff_nal_proy;

    /**
     * Staff internacional dedicado al proyecto (número de personas)
     * @var int
     */
    var $staff_intal_proy;

    /**
     * El proyecto es de cobertura nacional 
     * @var int
     */
    var $cobertura_nal_proy;

    /**
     * Descripcion de beneficiarios del proyecto
     * @var string
     */
    var $cant_benf_proy;
    
    /**
     * Cantidad total de beneficiarios del proyecto por tipo relacion(directo,indirecto), genero y edad
     * @var string
     */
    var $benf_proy;

    /**
     * Valor total de las agencias dondantes, se usa cuando no se discrimina el aporte por agencia
     * @var string
     */
    var $valor_aporte_donantes = '';
    
    /**
     * Codigo de proycto las agencias dondantes
     * @var string
     */
    var $codigo_donantes = '';

    /**
     * Valor total de lsocios, se usa cuando no se discrimina el aporte por socio
     * @var string
     */
    var $valor_aporte_socios = '';

    /**
     * ID de la organizacion desde la que se cubre el proyecto
     * @var int
     */
    var $id_orgs_cubre = Array();

    /**
     * ID de los  Departamentos que cubre el Proyecto
     * @var Array
     */	
    var $id_deptos = Array();

    /**
     * ID de los Municipios que cubre el Proyecto
     * @var Array
     */	
    var $id_muns = Array();

    /**
     * ID de las Regiones que cubre el Proyecto
     * @var Array
     */	
    var $id_regiones = Array();

    /**
     * ID de los Contáctos del Proyecto
     * @var Array
     */	
    //var $id_contactos = Array();

    /**
     * ID de los Temas Cluster, UNDAF, DGR
     * @var Array
     */	
    var $id_temas = Array();
    
    /**
     * Tema principal cluster
     * @var int
     */	
    var $id_tema_p;

    /**
     * Texto extra asociado a cada tema, las claves del arreglo son los id de los temas
     * @var Array
     */	
    var $texto_extra_tema = Array();
    
    /**
     * Presupuesto asociado a cada tema, las claves del arreglo son los id de los temas
     * @var Array
     */	
    var $temas_presupuesto = Array();

    /**
     * ID de las Poblaciones beneficiadas directas del Proyecto
     * @var Array
     */	
    var $id_beneficiarios = Array();
    
    /**
     * Cantidade de las Poblaciones beneficiadas directas del Proyecto
     * @var Array
     */	
    var $cant_bd = Array();

    /**
     * Otro cual Poblacion beneficiada directas del Proyecto
     * @var string
     */	
    var $otro_cual_benf_proy = '';

    /**
     * ID de las Poblaciones beneficiadas indirectas del Proyecto
     * @var Array
     */	
    var $id_beneficiarios_indirectos = Array();

    /**
     * Cantidade de las Poblaciones beneficiadas iddirectas del Proyecto
     * @var Array
     */	
    var $cant_bi = Array();

    /**
     * ID de las Organizaciones vinculadas al Proyecto como Ejecutoras
     * @var Array
     */	
    var $id_orgs_e = Array();

    /**
     * ID de las Organizaciones vinculadas al Proyecto como Donantes
     * @var Array
     */	
    var $id_orgs_d = Array();

    /**
     * ID de las Organizaciones - Socios
     * @var Array
     */	
    var $id_orgs_s = Array();

    /**
     * ID de las Organizaciones que tienen trabajo coordinado con la agencia que ejecuta el proyecto
     * @var Array
     */	
    var $id_orgs_coor = Array();

    /**
     * Valor del aporte de las Organizaciones vinculadas al Proyecto como Donantes
     * @var Array
     */	
    //var $id_orgs_d_valor_ap = Array();

    /**
     * Valor del aporte de los socios
     * @var Array
     */	
    var $id_orgs_s_valor_ap = Array();
    
    /**
     * Valor del aporte de los donantes
     * @var Array
     */	
    var $id_orgs_d_valor_ap = Array();

    /**
     * Valor del aporte de las orgs. coord
     * @var Array
     */	
    var $id_orgs_coor_valor_ap = Array();

    /**
     * ID de los tipos de vinulación de las Organizaciones en el Proyecto
     * @var Array
     */	
    //var $id_tipo_vinc_orgs;

    /**
     * Información extra de los donantes
     * @var string
     */
    var $info_extra_donantes;

    /**
     * Información extra de los socios
     * @var string
     */
    var $info_extra_socios;

    /**
     * Se ha firmado un Joint programme?
     * @var string
     */
    var $joint_programme_proy;

    /**
     * Es la coordinacion respaldada por un MOU con otra(s) agencia(s)
     * @var string
     */
    var $mou_proy;

    /**
     * Hay un acuerdo de cooperacion
     * @var string
     */
    var $acuerdo_coop_proy;

    /**
     * Intervencion independiente de una agencia
     * @var string
     */
    var $interv_ind_proy;

    /**
     * SI desde donde se crea el proyecto, ejemplo, undaf, sidih
     * @var string
     */
    var $si_proy;
    
    /**
     * Estado validación por parte del Cluster
     * @var int
     */
    var $validado_cluster_proy = 0;

    /**
     * ID de los albergues que cubre el Proyecto
     * @var Array
     */	
    var $id_albergues = Array();
}

?>
