<?
/**
* Maneja todas las propiedades del Objeto UnicefConvenio
* Valores de Objeto VO
* @author Ruben A. Rojas C.
*/

Class UnicefConvenio {

  /**
  * Identificador
  * @var int
  */
	var $id;

    /**
     * ID del funcionario responsable
     * @var int
     */
    var $id_funcionario;

    /**
     * ID del Estado
     * @var int
     */
    var $id_estado;
    
    /**
     * ID de la actividad AWP
     * @var int
     */
    var $id_actividad;

    /**
     * ID de la organizacion implementadora
     * @var array
     */
    var $id_socio_implementador = array();

    /**
     * Codigo alfa-numerico
     * @var string
     */
    var $codigo;

    /**
     * Nombre del proyecto
     * @var string
     */
    var $nombre;

    /**
     * Aliados del convenio
     * @var string
     */
    var $aliados;

    /**
     * Fecha de inicio del convenio
     * @var string
     */
    var $fecha_ini;

    /**
     * Fecha de finalización del convenio
     * @var string
     */
    var $fecha_fin;

    /**
     * Duración en meses del convenio
     * @var int
     */
    var $duracion_meses;

    /**
     * Número de avances programados
     * @var int
     */
    var $numero_avances;
    
	/**
	 * Presupuesto moneda nacional
	 * @var int
	 */
	var $presupuesto_cop;
    
    /**
	 * Presupuesto moneda extranjera
	 * @var int
	 */
	var $presupuesto_ex;

    /**
	 * ID de la moneda extranjera
	 * @var int
	 */
	var $id_mon_ex;
    
	/**
	 * Aporte UNICEF moneda nacional
	 * @var int
	 */
	var $aporte_unicef_cop;
    
    /**
	 * Aporte UNICEF moneda extranjera
	 * @var int
	 */
	var $aporte_unicef_ex;

    /**
	 * ID de la moneda extranjera del avance UNICEF
	 * @var int
	 */
	var $id_mon_ex_aporte_unicef;
	
	/**
	 * Valor avances moneda nacional
	 * @var array
	 */
	var $avances_cop = array();
    
    /**
	 * Valor avances moneda extranjera
	 * @var array
	 */
	var $avances_ex = array();

    /**
	 * Fecha de los avances
	 * @var array
	 */
	var $avances_fecha = array();

    /**
	 * ID de la moneda extranjera de los avances
	 * @var arrat
	 */
	var $id_mon_ex_avances = array();

	/**
	 * Fuente de los avances
	 * @var array
	 */
	var $id_fuente_avances = array();
    
    /**
	 * Donante de los avances
	 * @var array
	 */
	var $id_donante_avances = array();

	/**
     * Otros fondos ejecutados COP
     * @var int
     */
    var $otros_fondos_cop;
    
	/**
     * Otros fondos ejecutados extranjera
     * @var int
     */
    var $otros_fondos_ex;
    
	/**
     * Moneda de Otros fondos ejecutados extranjera
     * @var int
     */
    var $id_mon_ex_otros_fondos;
	
	/**
     * Fuente de Otros fondos ejecutados
     * @var array
     */
    var $id_fuente_otros_fondos = array();
	
	/**
     * Donante de Otros fondos ejecutados
     * @var array
     */
    var $id_donante_otros_fondos = array();

	/**
	 * Especifica la cobertura del convenio: Nal,Deptal,Mpal
	 * @var string
	 */
	var $cobertura;

	/**
	 * Divipola de Deptos
	 * @var array
	 */
	var $id_depto = array();

	/**
	 * Divipola de Muns
	 * @var array
	 */
	var $id_mun = array();

    /**
	 * Fecha Update
	 * @var string
	 */
	var $fecha_update;
}

?>
