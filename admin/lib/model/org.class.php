<?
/**
 * Maneja todas las propiedades del Objeto Organizacion
 * Valores de Objeto VO
 * @author Ruben A. Rojas C.
 */

Class Organizacion {

    /**
     * Identificador
     * @var int
     */
    var $id;

    /**
     * ID del tipo de Organizacion
     * @var int
     */
    var $id_tipo;

    /**
     * ID del Municipio Sede
     * @var string
     */
    var $id_mun_sede;
    
    /**
     * Pais/Ciudad Fuera de COL
     * @var string
     */
    var $pais_ciudad;

    /**
     * ID de la Organización Papa
     * @var int
     */
    var $id_papa;

    /**
     * Nombre de la Organización
     * @var string
     */
    var $nom;

    /**
     * Sigla de la Organización
     * @var string
     */
    var $sig;

    /**
     * Descripción de la Organización
     * @var string
     */
    var $des;

    /**
     * Año de iniciación de labores de la Organización en Colombia
     * @var int
     */
    var $naci;

    /**
     * NIT de la Organización
     * @var varchar
     */
    var $nit;

    /**
     * Especifica cual será el nombre de mostrar en los reportes. Primero Sigla, luego Nombre o Primero Nombre, luego Sigla.
     * 1 = Sigla, 2 = Nombre
     * @var boolean
     */
    var $view;

    /**
     * La organización crea Bases de Datos de Organizaciones?
     * @var boolean
     */
    var $bd = 0;

    /**
     * Dirección de la Organización
     * @var string
     */
    var $dir;

    /**
     * Primer teléfono de la Organización
     * @var string
     */
    var $tel1;

    /**
     * Segundo teléfono de la Organización
     * @var string
     */
    var $tel2;

    /**
     * Fax de la Organización
     * @var string
     */
    var $fax;

    /**
     * Dirección de correo electrónico de la Organización. Para uso exclusivo del Sistema de Naciones Unidas.
     * @var string
     */
    var $un_email;

    /**
     * Dirección de correo electrónico de la Organización. Para uso público.
     * @var string
     */
    var $pu_email;

    /**
     * Dirección de página Web de la Organización
     * @var string
     */
    var $web;

    /**
     * Ruta física del Logotipo que identifica a la Organización
     * @var string
     */
    var $logo;

    /**
     * Especifica si es donante
     * @var int
     */
    var $dona = 0;

    /**
     * Nombre del representante de la Organización
     * @var string
     */
    var $n_rep;

    /**
     * Titulo del Representante de la Organización
     * @var string
     */
    var $t_rep;

    /**
     * Teléfono del representante de la Organización
     * @var string
     */
    var $tel_rep;

    /**
     * Email del Representante de la Organización
     * @var string
     */
    var $email_rep;

    /**
     * Especifica si está confirmada la info. de la Organización
     * @var string
     */
    var $info_confirmada;

    /**
     * Espacio de coordinación de la Organización
     * @var string
     */
    var $esp_coor;

    /**
     * Participa en espacio social
     * @var int
     */
    var $consulta_social;

    /**
     * Organizacion de la base de datos CNRR
     * @var int
     */
    var $cnrr = 0;

    /**
     * Define si la org fue cargada desde registro
     * @var int
     */
    var $publicada;	

    /**
     * Ultima fecha de actualizacion
     * @var date
     */
    var $fecha_update;	

    /**
     * ID de las poblaciones objetivo de la Organización
     * @var Array
     */
    var $id_poblaciones = array();

    /**
     * ID de los sectores de la Organización
     * @var Array
     */
    var $id_sectores = array();

    /**
     * ID de los enfoques de la Organización
     * @var Array
     */
    var $id_enfoques = array();

    /**
     * ID de los contactos de la Organización
     * @var Array
     */
    var $id_contactos;

    /**
     * ID de los  Departamentos - Cobertura
     * @var Array
     */
    var $id_deptos = Array();

    /**
     * ID de los  Municipios - Cobertura
     * @var Array
     */
    var $id_muns = Array();


    /**
     * ID de las Regiones - Cobertura
     * @var Array
     */
    var $id_regiones = Array();

    /**
     * ID de los Poblados - Cobertura
     * @var Array
     */
    var $id_poblados = Array();

    /**
     * ID de los Resguardos - Cobertura
     * @var Array
     */
    var $id_resguardos = Array();

    /**
     * ID de los Parques - Cobertura
     * @var Array
     */
    var $id_parques = Array();

    /**
     * ID de las Divisiones Afro - Cobertura
     * @var Array
     */
    var $id_divisiones_afro;

    /**
     * ID de las Organizaciones que donan recursos a la org.
     * @var Array
     */
    var $id_donantes = array();

    /**
     * Activo
     * @var int
     */
    var $activo;

    /**
     * Locked, si se esta editando la organizcion, locked=1 para manejar transacciones
     * @var int
     */
    var $locked = 0;

    /**
     * Organizacion de la base de datos mapp-oea
     * @var int
     */
    var $mapp_oea = 0;

}

//CLASE EXCLUSIVA PARA EL MANEJO DE INFO. DE REGISTRO EXTERNO DE ORG
Class OrganizacionRegistro extends Organizacion{

    /**
     * Nombre de la persona que registra la Org
     * @var String
     */
    var $ingresa_nombre;

    /**
     * Tel de la persona que registra la Org
     * @var String
     */
    var $ingresa_tel;

    /**
     * Email de la persona que registra la Org
     * @var String
     */
    var $ingresa_email;

    /**
     * Donantes
     * @var Array
     */
    var $donantes = array();

    /**
     * Nombre de Orgs Población Vulnerable
     * @var Array
     */
    var $org_pob_vul_nombre = array();

    /**
     * Tel de Orgs Población Vulnerable
     * @var Array
     */
    var $org_pob_vul_tel = array();

    /**
     * Email de Orgs Población Vulnerable
     * @var Array
     */
    var $org_pob_vul_email = array();

}

//////////////////////////////////
/* EXCLUSIVO PARA MAPP-OEA */
//////////////////////////////////
Class OrganizacionMO extends Organizacion{

    /**
     * Nombre de Orgs Población Vulnerable
     * @var Array
     */
    var $org_conoce_nombre = array();

    /**
     * Tel de Orgs Población Vulnerable
     * @var Array
     */
    var $org_conoce_tel = array();

    /**
     * Email de Orgs Población Vulnerable
     * @var Array
     */
    var $org_conoce_email = array();

    /**
     * Nombre de Orgs Población Vulnerable
     * @var Array
     */
    var $org_trabaja_nombre = array();

    /**
     * Tel de Orgs Población Vulnerable
     * @var Array
     */
    var $org_trabaja_tel = array();

    /**
     * Email de Orgs Población Vulnerable
     * @var Array
     */
    var $org_trabaja_email = array();
}

?>
