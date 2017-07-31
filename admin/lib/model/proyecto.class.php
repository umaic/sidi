<?
/**
 * Maneja todas las propiedades del Objeto Proyecto
 * Valores de Objeto VO
 * @author Ruben A. Rojas C.
 */

Class Proyecto {
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
     * Nombre del Proyecto
     * @var string
     */
    var $nom_proy;

    /**
     * C�digo del proyecto,  este es asignado por el proponente del proyecto
     * @var string
     */
    var $cod_proy;

    /**
     * Descripci�n del proyecto, alcances, metodolog�a, cronograma. se puede escribir toda la informaci�n que el proponente del proyecto suministre.
     * @var string
     */
    var $des_proy;

    /**
     * Objetivos del Proyecto
     * @var string
     */
    var $obj_proy;

    /**
     * Fecha de iniciaci�n del proyecto
     * @var string
     */
    var $inicio_proy;

    /**
     * Fecha de finalizaci�n del proyecto
     * @var string
     */
    var $fin_proy;

    /**
     * Fecha de �ltima actualizaci�n del proyecto en OCHA.
     * @var string
     */
    var $actua_proy;

    /**
     * Almacena el valor del costo total del proyecto
     * @var int
     */
    var $costo_proy;

    /**
     * Duraci�n en meses del proyecto, si existe Fecha Inicial y Fecha Final debe coincidir
     * @var int
     */
    var $duracion_proy;

    /**
     * La informaci�n del proyecto est� confirmada?
     * @var int
     */
    var $info_conf_proy;

    /**
     * Staff nacional dedicado al proyecto (n�mero de personas)
     * @var int
     */
    var $staff_nal_proy;

    /**
     * Staff internacional dedicado al proyecto (n�mero de personas)
     * @var int
     */
    var $staff_intal_proy;

    /**
     * El proyecto es de cobertura nacional 
     * @var int
     */
    var $cobertura_nal_proy;

    /**
     * Cantidad de beneficiarios directos del proyecto
     * @var string
     */
    var $cant_benf_proy;

    /**
     * Valor total de las agencias dondantes, se usa cuando no se discrimina el aporte por agencia
     * @var string
     */
    var $valor_aporte_donantes = '';

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
     * ID de los Cont�ctos del Proyecto
     * @var Array
     */	
    //var $id_contactos = Array();

    /**
     * ID de los Temas, versi�n UNDAF, multinivel
     * @var Array
     */	
    var $id_temas = Array();

    /**
     * Texto extra asociado a cada tema, las claves del arreglo son los id de los temas
     * @var Array
     */	
    var $texto_extra_tema = Array();

    /**
     * ID de las Poblaciones beneficiadas directas del Proyecto
     * @var Array
     */	
    var $id_beneficiarios = Array();

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
     * Valor del aporte de las orgs. coord
     * @var Array
     */	
    var $id_orgs_coor_valor_ap = Array();

    /**
     * ID de los tipos de vinulaci�n de las Organizaciones en el Proyecto
     * @var Array
     */	
    //var $id_tipo_vinc_orgs;

    /**
     * Informaci�n extra de los donantes
     * @var string
     */
    var $info_extra_donantes;

    /**
     * Informaci�n extra de los socios
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
     * SI desde donde se crea el proyecto, ejemplo, undaf, sidi
     * @var string
     */
    var $si_proy;
}

?>
