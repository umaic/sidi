<?
/**
* Maneja todas las propiedades del Objeto Evento
* Valores de Objeto VO
* @author Ruben A. Rojas C.
*/

Class Evento {

  /**
  * Identificador
  * @var int
  */
	var $id;
	
	
  /**
  * ID del tipo de Evento
  * @var int
  */	
	var $id_tipo;

  /**
  * ID de la categoria del Evento
  * @var int
  */	
	var $id_cat;

  /**
  * Nombre del Evento
  * @var string
  */	
	var $nombre;

  /**
  * Fecha en la que sucedió el evento.
  * @var date
  */	
	var $fecha_evento;
	
  /**
  * Fecha se usa la fecha de registro del evento en el sistema.
  * @var date
  */	
	var $fecha_registro;


  /**
  * Descripción del Evento.
  * @var text
  */	
	var $desc;

  /**
  * Descripción de las consecuencias humanitaria que produjo el evento
  * @var text
  */	
	var $desc_cons_hum;
	
  /**
  * Descripción del riesgo humanitario generado por el proyecto
  * @var text
  */	
	var $desc_riesg_hum;
		
  /**
  * Detalla el lugar donde ocurrió el evento en caso de contar con información no codificada al respecto
  * @var string
  */	
	var $lugar;
	
  /**
  * ¿El evento está confirmado?
  * @var boolean
  */	
	var $conf;
	
  /**
  * Fuente de Información del Evento
  * @var string
  */	
	var $fuente;
	
  /**
  * ID de los  Departamentos donde ocurre el Evento
  * @var Array
  */	
	var $id_deptos;

  /**
  * ID de los Municipios donde ocurre el Evento
  * @var Array
  */	
	var $id_muns = Array();
	
  /**
  * ID de las Regiones donde ocurre el Evento
  * @var Array
  */	
	var $id_regiones = Array();
	
  /**
  * ID de los Poblados donde ocurre el Evento
  * @var Array
  */	
	var $id_poblados = Array();
	
  /**
  * ID de los Resguardos donde ocurre el Evento
  * @var Array
  */	
	var $id_resguardos = Array();
	
  /**
  * ID de los Parques Naturales donde ocurre el Evento
  * @var Array
  */	
	var $id_parques = Array();
	
  /**
  * ID de las Divisiones Afro donde ocurre el Evento
  * @var Array
  */	
	var $id_divs_afro = Array();
	
  /**
  * ID de los Actores del Evento
  * @var Array
  */	
	var $id_actores;
	
  /**
  * ID de las Consecuencias Humanitarias del Evento
  * @var Array
  */	
	var $id_cons;
		
  /**
  * ID de los Riesgos Humanitarios del Evento
  * @var Array
  */	
	var $id_riesgos;

	
}

?>