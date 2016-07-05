<?php
/**
* Maneja todas las propiedades del Objeto UnicefProductoAwp
* Valores de Objeto VO
* @author Ruben A. Rojas C.
*/

Class UnicefProductoAwp {
	
	/**
	* ID
	* @var int
	*/
	var $id;
	
	/**
	 * ID de la actividad AWP
	 * @var int
	 */
	var $id_actividad;

	/**
	 * ID del donante
	 * @var array
	 */
	var $id_donante = array();

	/**
	 * ID de la organizacion implementadora
	 * @var array
	 */
	var $id_socio_implementador = array();

	/**
	 * ID del funcionario responsable
	 * @var int
	 */
	var $id_funcionario = array();

	/**
	 * ID de fuente FUNDED de los fondos
	 * @var array
	 */
	var $id_fuente_funded = array();

	/**
	 * ID de fuente UNFUNDED de los fondos
	 * @var array
	 */
	var $id_fuente_unfunded = array();

	/**
	 * Nombre del producto AWP
	 * @var string
	 */
	var $nombre;

	/**
	 * Codigo del producto AWP
	 * @var string
	 */
	var $codigo;

	/**
	 * Especifica la cobertura del producto: Nal,Deptal,Mpal
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
	 * Aliados
	 * @var string
	 */
	var $aliados;
	
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
	 * Presupuesto descripcion
	 * @var array
	 */
	var $id_presupuesto_desc = array();

    /**
	 * Año de planeación
	 * @var int
	 */
	var $aaaa;

    /**
	 * Fuente FUNDED
	 * @var int
	 */
	var $funded;

    /**
	 * Fuente UNFUNDED
	 * @var int
	 */
	var $unfunded;

    /**
	 * Cronograma 1er. trimestre
	 * @var int
	 */
	var $cronograma_1_tri;

    /**
	 * Cronograma 2do. trimestre
	 * @var int
	 */
	var $cronograma_2_tri;

    /**
	 * Cronograma 3er. trimestre
	 * @var int
	 */
	var $cronograma_3_tri;

    /**
	 * Cronograma 4to.. trimestre
	 * @var int
	 */
	var $cronograma_4_tri;

	/**
	* Especifica si el resultado le apunta al tema transversal Indigena
	* @var int
	*/
	var $indigena;

	/**
	* Especifica si el resultado le apunta al tema transversal Afro
	* @var int
	*/
	var $afro;

	/**
	* Especifica si el resultado le apunta al tema transversal Equidad de genero
	* @var int
	*/
	var $equidad_genero;

	/**
	* Especifica si el resultado le apunta al tema transversal de Participación
	* @var int
	*/
	var $participacion;

	/**
	* Especifica si el resultado le apunta al tema transversal Movilizacion
	* @var int
	*/
	var $movilizacion;

	/**
	* Especifica si el resultado le apunta al tema transversal Prevencion
	* @var int
	*/
	var $prevencion;
}

?>
