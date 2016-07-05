<?php
class FactoryDAO
{
    // The parameterized factory method
    public static function factory($type)
    {
		$clases = array(
			'actor' 				=> 'ActorDAO',
			'barrio' 				=> 'BarrioDAO',
			'cat_d_s' 				=> 'CategoriaDatoSectorDAO',
			'cat_evento_c' 			=> 'CatEventoConflictoDAO',
			'clase_desplazamiento' 	=> 'ClaseDesplazamientoDAO',
			'clasificacion' 		=> 'ClasificacionDAO',
			'comuna'				=> 'ComunaDAO',
			'condicion_mina' 		=> 'CondicionMinaDAO',
			'cons_hum'				=> 'ConsHumDAO',
			'contacto'				=> 'ContactoDAO',
			'contacto_col_op'		=> 'ContactoColOpDAO',
			'contacto_col'			=> 'ContactoColDAO',
			'contacto_d_s' 			=> 'ContactoDatoSectorDAO',
			'dato_sectorial'		=> 'DatoSectorialDAO',
			'depto' 				=> 'DeptoDAO',
			'desplazamiento'		=> 'DesplazamientoDAO',
			'edad' 					=> 'EdadDAO',
			'enfoque' 				=> 'EnfoqueDAO',
			'espacio_usuario'		=> 'EspacioUsuarioDAO',
			'espacio'				=> 'EspacioDAO',
			'estado_mina' 			=> 'EstadoMinaDAO',
			'estado_proyecto' 		=> 'EstadoProyectoDAO',
			'etnia' 				=> 'EtniaDAO',
			'evento_c' 				=> 'EventoConflictoDAO',
			'fuente_evento_c' 		=> 'FuenteEventoConflictoDAO',
			'fuente' 				=> 'FuenteDAO',
			'log'					=> 'LOGUsuarioDAO',
			'minificha'				=> 'MinifichaDAO',
			'modulo'				=> 'ModuloDAO',
			'moneda' 				=> 'MonedaDAO',
			'municipio' 			=> 'MunicipioDAO',
			'ocupacion' 			=> 'OcupacionDAO',
			'org' 					=> 'OrganizacionDAO',
			'pais' 					=> 'PaisDAO',
			'perfil_usuario'		=> 'PerfilUsuarioDAO',
			'periodo' 				=> 'PeriodoDAO',
			'poblacion'				=> 'PoblacionDAO',
			'poblado' 				=> 'PobladoDAO',
			'proyecto' 				=> 'ProyectoDAO',
			'p4w'    				=> 'P4wDAO',
			'rango_edad' 			=> 'RangoEdadDAO',
			'region' 				=> 'RegionDAO',
			'resguardo'				=> 'ResguardoDAO',
			'riesgo_hum'			=> 'RiesgoHumDAO',
			'sector'				=> 'SectorDAO',
			'sexo' 					=> 'SexoDAO',
			'sissh' 				=> 'SisshDAO',
			'sub_condicion' 		=> 'SubCondicionDAO',
			'sub_etnia' 			=> 'SubEtniaDAO',
			'subcat_evento_c' 		=> 'SubCatEventoConflictoDAO',
			'subfuente_evento_c' 	=> 'SubFuenteEventoConflictoDAO',
			'sugerencia'			=> 'SugerenciaDAO',
			'tema' 					=> 'TemaDAO',
			'tipo_desplazamiento' 	=> 'TipoDesplazamientoDAO',
			'tipo_evento'			=> 'TipoEventoDAO',
			'tipo_org' 				=> 'TipoOrganizacionDAO',
			'tipo_usuario' 			=> 'TipoUsuarioDAO',
			'u_d_s' 				=> 'UnidadDatoSectorDAO',
			'usuario' 				=> 'UsuarioDAO',
			'unicef_producto_awp'	=> 'UnicefProductoAwpDAO',
			'unicef_actividad_awp'	=> 'UnicefActividadAwpDAO',
			'unicef_producto_cpap'	=> 'UnicefProductoCpapDAO',
			'unicef_resultado'	    => 'UnicefResultadoDAO',
			'unicef_sub_componente'	=> 'UnicefSubComponenteDAO',
			'unicef_componente' 	=> 'UnicefComponenteDAO',
			'unicef_estado'      	=> 'UnicefEstadoDAO',
			'unicef_socio'      	=> 'UnicefSocioDAO',
			'unicef_convenio'      	=> 'UnicefConvenioDAO',
			'unicef_funcionario'   	=> 'UnicefFuncionarioDAO',
			'unicef_fuente_pba'	    => 'UnicefFuentePbaDAO',
			'unicef_donante'   	    => 'UnicefDonanteDAO',
			'unicef_presupuesto_desc'=> 'UnicefPresupuestoDescDAO',
			'unicef'           	    => 'UnicefDAO',
		);

		$lib_dir = $_SERVER['DOCUMENT_ROOT'].'/sissh/admin/lib';

        if (include_once $lib_dir.'/dao/'.$type.'.class.php') {
            
			// Model Class
			if (file_exists($lib_dir.'/model/'.$type.'.class.php'))	include_once $lib_dir.'/model/'.$type.'.class.php';
			
			$classname = $clases[$type];
            return new $classname;

        } 
		else {
            throw new Exception ("$type in DAO Class not found");
        }
    }
}
?>
