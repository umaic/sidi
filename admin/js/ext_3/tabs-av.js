var index_file = 'index_parser.php';
var titulos = {
	'usuario' 				: 'Usuarios', 
	'tipo_usuario' 			: 'Tipo Usuarios', 
	'moneda' 				: 'Monedas', 
	'clasificacion' 		: 'Clasificaci&oacute;n de Temas', 
	'tema' 					: 'Temas', 
	'estado_proyecto' 		: 'Estado de Proyecto',
    'tipo_proyecto' 		: 'Tipo de Proyecto',
    'mecanismo_entrega'		: 'Mecanismo de Entrega',
    'modalidad_asistencia'	: 'Modalidad de Asistencia',
    'pais' 					: 'Paises',
	'depto' 				: 'Departamentos', 
	'municipio' 			: 'Municipios', 
	'poblado' 				: 'Poblados', 
	'region' 				: 'Regiones', 
	'comuna' 				: 'Comunas', 
	'barrio' 				: 'Barrios',
	'resguardo'				: 'Resguardos',
	'actor' 				: 'Actores',
	'subfuente_evento_c'	: 'Sub Fuentes',
	'fuente_evento_c' 		: 'Fuentes',
	'subcat_evento_c' 		: 'Sub Categorias',
	'cat_evento_c' 			: 'Categorias',
	'sub_etnia' 			: 'Sub Etnias',
	'etnia' 				: 'Etnias',
	'ocupacion' 			: 'Ocupaciones',
	'edad' 					: 'Edades',
	'rango_edad' 			: 'Rango de Edades',
	'sub_condicion' 		: 'Sub Condiciones',
	'condicion_mina' 		: 'Condiciones',
	'sexo' 					: 'Sexos',
	'estado_mina' 			: 'Estados',
	'periodo' 				: 'Periodos',
	'tipo_desplazamiento' 	: 'Tipos de Desplazamiento',
	'clase_desplazamiento' 	: 'Clases de Desplazamiento',
	'fuente' 				: 'Fuentes',
	'tipo_org' 				: 'Tipos de Organizaci&oacute;n',
	'enfoque' 				: 'Enfoques',
	'cat_d_s' 				: 'Categorias Dato Sectorial',
	'u_d_s' 				: 'Unidad Dato Sectorial',
	'contacto_d_s' 			: 'Fuente',
	'dato_sectorial'		: 'Dato Sectorial',
	'perfil_usuario'		: 'Perfil Usuario',
	'modulo'				: 'M&oacute;dulo',
	'sector'				: 'Sector',
	'poblacion'				: 'Poblaci&oacute;n',
	'sugerencia'			: 'Sugerencia',
	'info_ficha'			: 'Ficha Informativa',
	'espacio'				: 'Espacio',
	'contacto_col'			: 'Caracter&iacute;sticas Contactos',
	'contacto_col_op'		: 'Opciones Caracter&iacute;sticas Contactos',
	'espacio_usuario'		: 'Acceso a espacios por Usuario',
	'minificha'				: 'Perfil',
	'log_admin'				: 'LOG Admin',
	'log_consulta'			: 'LOG Consulta',
	'tipo_evento'			: 'Tipo de Eventos',
	'riesgo_hum'			: 'Riesgos Humanitarios',
	'cons_hum'				: 'Consecuencias Humanitarias',
    'emergencia'			: 'Emergencias',
};

var clases = {
	'usuario' 				: 'UsuarioDAO',
	'tipo_usuario' 			: 'TipoUsuarioDAO',
	'moneda' 				: 'MonedaDAO',
	'clasificacion' 		: 'ClasificacionDAO',
	'tema' 					: 'TemaDAO',
	'estado_proyecto' 		: 'EstadoProyectoDAO',
    'tipo_proyecto' 		: 'TipoProyectoDAO',
    'mecanismo_entrega'		: 'MecanismoEntregaDAO',
    'modalidad_asistencia'	: 'ModalidadAsistenciaDAO',
	'pais' 					: 'PaisDAO', 
	'depto' 				: 'DeptoDAO', 
	'municipio' 			: 'MunicipioDAO', 
	'poblado' 				: 'PobladoDAO', 
	'region' 				: 'RegionDAO', 
	'comuna'				: 'ComunaDAO', 
	'barrio' 				: 'BarrioDAO', 
	'resguardo'				: 'ResguardoDAO',
	'actor' 				: 'ActorDAO',
	'subfuente_evento_c' 	: 'SubFuenteEventoConflictoDAO',
	'fuente_evento_c' 		: 'FuenteEventoConflictoDAO',
	'subcat_evento_c' 		: 'SubCatEventoConflictoDAO',
	'cat_evento_c' 			: 'CatEventoConflictoDAO',
	'sub_etnia' 			: 'SubEtniaDAO',
	'etnia' 				: 'EtniaDAO',
	'ocupacion' 			: 'OcupacionDAO',
	'edad' 					: 'EdadDAO',
	'rango_edad' 			: 'RangoEdadDAO',
	'sub_condicion' 		: 'SubCondicionDAO',
	'condicion_mina' 		: 'CondicionMinaDAO',
	'sexo' 					: 'SexoDAO',
	'estado_mina' 			: 'EstadoMinaDAO',
	'periodo' 				: 'PeriodoDAO',
	'tipo_desplazamiento' 	: 'TipoDesplazamientoDAO',
	'clase_desplazamiento' 	: 'ClaseDesplazamientoDAO',
	'fuente' 				: 'FuenteDAO',
	'tipo_org' 				: 'TipoOrganizacionDAO',
	'enfoque' 				: 'EnfoqueDAO',
	'cat_d_s' 				: 'CategoriaDatoSectorDAO',
	'u_d_s' 				: 'UnidadDatoSectorDAO',
	'contacto_d_s' 			: 'ContactoDatoSectorDAO',
	'dato_sectorial'		: 'DatoSectorialDAO',
	'perfil_usuario'		: 'PerfilUsuarioDAO',
	'modulo'				: 'ModuloDAO',
	'sector'				: 'SectorDAO',
	'poblacion'				: 'PoblacionDAO',
	'sugerencia'			: 'SugerenciaDAO',
	'info_ficha'			: '',
	'espacio'				: 'EspacioDAO',
	'contacto_col'			: 'ContactoColDAO',
	'contacto_col_op'		: 'ContactoColOpDAO',
	'espacio_usuario'		: 'EspacioUsuarioDAO',
	'minificha'				: 'MinifichaDAO',
	'log_admin'				: 'LOGUsuarioDAO',
	'log_consulta'			: 'LOGUsuarioDAO',
	'tipo_evento'			: 'TipoEventoDAO',
	'riesgo_hum'			: 'RiesgoHumDAO',
	'cons_hum'				: 'ConsHumDAO',
    'emergencia'			: 'EmergenciaDAO',
	};

/* Crea Tab para listado*/
function addTab(m_e){
	var url = index_file+'?m_e='+m_e+'&accion=listar&class='+clases[m_e]+'&method=ListarTabla';
	var tabs = Ext.getCmp('tabPanel');
	
	tabs.add({
		title		: titulos[m_e],
		iconCls		: 'tabs',
		autoLoad	: { url: url, callback: this.initSearch, scope: this },
		closable	: true,
		autoScroll	: true
	}).show();
}

/* Crea Tab para accion especifica */
function addTabAccion(m_e,accion,method){

	var url = index_file+'?m_e='+m_e+'&accion='+accion+'&class='+clases[m_e]+'&method='+method;
	var tabs = Ext.getCmp('tabPanel');

	tabs.add({
		title		: titulos[m_e],
		iconCls		: 'tabs',
		autoLoad	: { url: url, callback: this.initSearch, scope: this },
		closable	: true,
		autoScroll	: true
	}).show();
}

/* Muestra ventana con el formulario de Insert/Update */
function addWindowIU(m_e,accion,id){
	var url = index_file+'?m_e='+m_e+'&accion='+accion+'&id='+id;
	new Ext.Window({
				id			: 'windowIU',
				autoLoad 	: url,
				region 		: 'center',
				width		: 750,
				height		: 400,
				autoScroll	: true,
				closeable	: true,
				bodyCssClass: 'body',
				title		: accion.charAt(0).toUpperCase() + accion.substring(1) + ' ' + titulos[m_e]
			}

			).show();

			return false;
}

/*Procesa las formas */
function submitForm(e){
	var form = e.target;
	var f_elements = form.elements;
	var tabsCmp = Ext.getCmp('tabPanel');

	Ext.Ajax.request({
						url		: index_file,
						method	: 'POST',
						form 	: form,
						headers : {
									'content' : 'text/html; charset=iso-8859-1'
						},
						success	: function (result,request){
									//Refresh el listado, getUpdater funciona, porque el tab se cargo con autoLoad.

									if (Ext.isDefined(Ext.getCmp('windowIU')))	Ext.getCmp('windowIU').close();

									// Alerta con minWidh para que no quede en 2 lineas el mensaje
									Ext.Msg.show({
										msg		:result.responseText,
										minWidth: 250,
										buttons : Ext.Msg.OK
									});
									
									tabsCmp.activeTab.getUpdater().refresh();
								}
					});
}

/* Borra un registro de la parrilla */
function borrarRegistro(class_v,id){

	var tabsCmp = Ext.getCmp('tabPanel');
	Ext.Ajax.request({
						url		: index_file,
						method	: 'GET',
						params 	: { accion : 'borrar',
									class  : class_v,
									method : 'Borrar',
									param  : id
								  },
						success	: function (result,request){
									//Refresh el listado, getUpdater funciona, porque el tab se cargo con autoLoad.
									
									// Alerta con minWidh para que no quede en 2 lineas el mensaje
									Ext.Msg.show({
										msg		:result.responseText,
										minWidth: 250,
										buttons : Ext.Msg.OK
									});
									tabsCmp.activeTab.getUpdater().refresh();
								}
					});
}

/* Refresca el contenido de un tab, dado los parametros del url */
function refreshTab(url){
	
	var tabsCmp = Ext.getCmp('tabPanel');
	
	tabsCmp.activeTab.getUpdater().update({ url: url });
}

/* Refresca el contenido de una window, dado los parametros del url */
function refreshWindow(url){
	
	var win = Ext.getCmp('windowIU');
	
	win.getUpdater().update({ url: url });
}

Ext.onReady(function(){
// Menu containing actions
 
    // Main (Tabbed) Panel
    var tabPanel = new Ext.TabPanel({
		id: 'tabPanel',
		region:'center',
		deferredRender:false,
		autoScroll: true, 
		margins:'100 0 4 0',
		activeTab:0,
		items:[{
			id:'inicio',
			contentEl:'tabs',
    		title: 'Inicio',
    		closable:false,
    		autoScroll:true,
			autoLoad: {url: 'home_admin.php', callback: this.initSearch, scope: this},
		}]
    });
 
    // Configure viewport
    viewport = new Ext.Viewport({
           layout:'border',
           items:[tabPanel]});

});


