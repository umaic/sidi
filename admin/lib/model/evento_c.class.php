<?
/**
* Maneja todas las propiedades del Objeto EventoConflicto
* Valores de Objeto VO
* @author Ruben A. Rojas C.
*/

Class EventoConflicto {

	/**
	* Identificador
	* @var int
	*/
	var $id;

	/**
	* ID de la Categoria
	* @var int
	*/	
	var $id_cat = array();

	/**
	* ID de la SubCategoria
	* @var int
	*/	
	var $id_subcat = array();

	/**
	* ID de la Fuente
	* @var int
	*/	
	var $id_fuente = array();

	/**
	* ID de la SubFuente
	* @var int
	*/	
	var $id_subfuente = array();
	
	/**
	* Almacena la fecha de reporte del evento de la fuente de información
	* @var array
	*/	
	var $fecha_fuente = array();
	
	/**
	* Almacena la descripción del evento proveído por la fuente de información del evento
	* @var array
	*/	
	var $desc_fuente = array();

	/**
	* Almacena la referencia del evento de la fuente de información
	* @var array
	*/	
	var $refer_fuente = array();	
	
	/**
	* Sintesis del Evento
	* @var string
	*/	
	var $sintesis;

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
	* Detalla el lugar donde ocurrió el evento en caso de contar con información no codificada al respecto
	* @var string
	*/	
	var $lugar = array();

	/**
	* ID del Municipio donde sucede el Evento
	* @var string
	*/	
	var $id_mun = array();

	/**
	* ID de los Actores del Evento
	* @var Array
	*/	
	var $id_actor = array();

	/**
	* ID de los SubActores del Evento
	* @var Array
	*/	
	var $id_subactor = array();

	/**
	* ID de los SubSubActores del Evento
	* @var Array
	*/	
	var $id_subsubactor = array();
		
	/**
	* ID de los Estados
	* @var Array
	*/	
	var $id_estado = array();
	
	/**
	* ID de las Edades=Grupos Etareos de los eventos
	* @var Array
	*/	
	var $id_rango_edad = array();

	/**
	* ID de las Edades de los eventos
	* @var Array
	*/	
	var $id_edad = array();
	
	/**
	* ID de las Condiciones
	* @var Array
	*/	
	var $id_condicion = array();

	/**
	* ID de los Sub Condiciones
	* @var Array
	*/	
	var $id_subcondicion = array();	
		
	/**
	* ID de los SExos
	* @var Array
	*/	
	var $id_sexo = array();

	/**
	* ID de las Etnias
	* @var Array
	*/	
	var $id_etnia = array();
	
	/**
	* ID de las Sub Etnias
	* @var Array
	*/	
	var $id_subetnia = array();	

	/**
	* ID de la Ocupación
	* @var Array
	*/	
	var $id_ocupacion = array();	
		
	/**
	* Cantidad de Víctimas
	* @var int
	*/	
	var $num_victimas;

}

?>