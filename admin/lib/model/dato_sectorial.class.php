<?
/**
* Maneja todas las propiedades del Objeto DatoSectorial
* Valores de Objeto VO
* @author Ruben A. Rojas C.
*/

Class DatoSectorial {

	/**
  * Identificador
  * @var int
  */
	var $id;

	/**
  * ID del contácto
  * @var int
  */
	var $id_contacto;

	/**
  * ID del sector
  * @var int
  */
	var $id_sector;

	/**
  * ID de la categoria
  * @var int
  */
	var $id_cat;

	/**
  * ID de la unidad
  * @var int
  */
	var $id_unidad;

	/**
  * Nombre del DatoSectorial
  * @var string
  */
	var $nombre;

	/**
  * Fecha de inicio de vigencia del dato
  * @var string
  */
	var $fecha_ini;

	/**
  * Fecha de fin de vigencia del dato
  * @var string
  */
	var $fecha_fin;

	/**
  * Desagregacion Geografica
  * @var string
  */
	var $desagreg_geo;

	/**
  * Formula para datos calculados
  * @var string
  */
	var $formula;
	
	/**
	* Tipo de calculo para valor nacional
	* @var string
	*/
	var $tipo_calc_nal;

	/**
	* Tipo de calculo para valor departamental
	* @var string
	*/
	var $tipo_calc_deptal;
	
	/**
	* Definición
	* @var string
	*/
	var $definicion;

	/**
	* Valor del Dato
	* @var float
	*/
	var $valor;

	/**
  * Id del Departamento
  * @var Array
  */
	var $id_depto;

	/**
  * Id del Municipio
  * @var Array
  */
	var $id_mun;

	/**
  * Id del Poblado
  * @var Array
  */
	var $id_poblado;

}

?>