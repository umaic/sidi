<?php
include_once 'seguridad.php';
include_once 'lib/libs_perfil_usuario.php';

//CONSULTA EL PERFIL DE USUARIO
$perfil_dao = New PerfilUsuarioDAO();
$perfil = $perfil_dao->GetAllArray('ID_TIPO_USUARIO = '.$_SESSION["id_tipo_usuario_s"]);
?>
<table cellspacing='10' cellpadding='1' align="center" class='tabla_home_admin' border=0 width="950">
    <tr>
        <td colspan="3">
            <ul><li><img src="images/home/info.png">&nbsp;<a href="http://sidih.colombiassh.org/sissh/doc/index.php/" target="_blank">Ayuda</a></li></ul>
        </td>
    </tr>
	<tr>
		<td valign="top">
		<? if (in_array(6,$perfil->id_modulo)){ ?>
			<h1>ORGANIZACIONES</h1>
			<ul>
				<li><a href="#" onclick="addTab('tipo_org'); return false;">Tipo de Organizaci&oacute;n</a></li>
				<li><a href="#" onclick="addTab('enfoque'); return false;">Enfoque</a></li>
			</ul>
		<? } ?>	
		
		<? if (in_array(4,$perfil->id_modulo)){ ?>
			<h1>USUARIOS</h1>
			<ul>
				<li>
					<a href="#" onclick="addTab('tipo_usuario'); return false;">Tipo Usuarios</a>
				</li>
				<li>
					<a href="#" onclick="addTab('perfil_usuario'); return false;">Acceso a m&oacute;dulos por <br /> tipo de usuario</a>
				</li>
				<li>
					<a href="#" onclick="addTab('usuario'); return false;">Usuarios</a>
					<ul>
						<li><a href="#" onclick="addTabAccion('usuario','listar','Reportar'); return false;">Reportar</a></li>
					</ul>
				</li>
			</ul>
		<? } ?>	

		<? if (in_array(20,$perfil->id_modulo)){ ?>
			<h1>DESPLAZAMIENTO</h1>
			<ul>
				<li>
					<a href="#" onclick="addTab('periodo'); return false;">Periodo</a>
				</li>
				<li>
					<a href="#" onclick="addTab('tipo_desplazamiento'); return false;">Tipo</a>
				</li>
				<li>
					<a href="#" onclick="addTab('clase_desplazamiento'); return false;">Clase</a>
				</li>
				<li>
					<a href="#" onclick="addTab('fuente'); return false;">Fuente</a>
				</li>

			</ul>
		<? } ?>	


		</td>

		<td valign="top">

		<? if (in_array(5,$perfil->id_modulo)){ ?>
			<h1>PROYECTOS</h1>
			<ul>
				<li>
					<a href="#" onclick="addTab('moneda'); return false;">Moneda</a>
				</li>
				<li>
					<a href="#" onclick="addTab('clasificacion'); return false;">Clasificaci&oacute;n Temas</a>
				</li>
				<li>
					<a href="#" onclick="addTab('tema'); return false;">Tema</a>
				</li>
				<li>
					<a href="#" onclick="addTab('estado_proyecto'); return false;">Estado Proyecto</a>
				</li>
				<li>
					<a href="_sma/web/" target="_blank">Cach&eacute; 4W</a>
				</li>
			</ul>
		<? } ?>	
		<? if (in_array(12,$perfil->id_modulo)){ ?>
			<h1>DATOS SECTORIALES</h1>
			<ul>
				<li>
					<a href="#" onclick="addTab('cat_d_s'); return false;">Categoria</a>
				</li>
				<li>
					<a href="#" onclick="addTab('u_d_s'); return false;">Unidad de Dato Sectorial</a>
				</li>
				<li>
					<!-- Las fuentes de datos sectoriales se manejan como contactos con espacio = No aplica (id=37) -->
					<a href="#" onclick="addTab('contacto_d_s'); return false;">Fuente</a>
				</li>
				<li>
					<a href="#" onclick="addTab('dato_sectorial'); return false;">Dato Sectorial</a>
					<ul>
						<li><a href="#" onclick="addTabAccion('dato_sectorial','listar','ReportarAdmin');return false;">Reportar</a></li>
					</ul>
				</li>
			</ul>
		<? } ?>	
		<? if (in_array(4,$perfil->id_modulo)){ ?>
			<h1>EVENTOS D. NATURAL</h1>
			<ul>
				<li>
					<a href="#" onclick="addTab('tipo_evento'); return false;">Tipo de Eventos</a>
				</li>
				<li>
					<a href="#" onclick="addTab('riesgo_hum'); return false;">Riesgos Humanitarios</a>
				</li>
				<li>
					<a href="#" onclick="addTab('cons_hum'); return false;">Consecuencias Humanitarias</a>
				</li>
			</ul>
		<? } ?>	
		</td>
		<td valign="top">
		<? if (in_array(29,$perfil->id_modulo)){ ?>
			<h1>EVENTOS <br />CONFLICTO</h1>
			<ul>
				<li>
					<a href="http://sidih.salahumanitaria.co/sissh/cron_jobs/sync_from_monitor.php" target="_blank">Sincronizar desde Monitor</a>
				</li>
				<li>
					<a href="#" onclick="addTab('actor'); return false;">Actor</a>
				</li>
				<li>
					<a href="#" onclick="addTab('sexo'); return false;">Sexo</a>
				</li>
				<li>
					<a href="#" onclick="addTab('condicion_mina'); return false;">Condici&oacute;n</a>
				</li>
				<li>
					<a href="#" onclick="addTab('sub_condicion'); return false;">Sub Condici&oacute;n</a>
				</li>
				<li>
					<a href="#" onclick="addTab('edad'); return false;">Edad</a>
				</li>
				<li>
					<a href="#" onclick="addTab('rango_edad'); return false;">Rango Edad</a>
				</li>
				<li>
					<a href="#" onclick="addTab('ocupacion'); return false;">Ocupaci&oacute;n</a>
				</li>
				<li>
					<a href="#" onclick="addTab('estado_mina'); return false;">Estado</a>
				</li>
				<li>
					<a href="#" onclick="addTab('etnia'); return false;">Etnia</a>
				</li>
				<li>
					<a href="#" onclick="addTab('sub_etnia'); return false;">Sub Etnia</a>
				</li>
				<li>
					<a href="#" onclick="addTab('cat_evento_c'); return false;">Categoria de Eventos</a>
				</li>
				<li>
					<a href="#" onclick="addTab('subcat_evento_c'); return false;">Sub Categoria de Eventos</a>
				</li>
				<li>
					<a href="#" onclick="addTab('fuente_evento_c'); return false;">Fuente de Eventos</a>
				</li>
				<li>
					<a href="#" onclick="addTab('subfuente_evento_c'); return false;">Sub Fuente de Eventos</a>
				</li>
			</ul>
		<? } ?>	
		</td>
		<td valign="top">
		<? if (in_array(19,$perfil->id_modulo)){ ?>
			<h1>MODULO<br /> GEOGRAFICO</h1>
			<ul>
				<li>
					<a href="#" onclick="addTab('pais'); return false;">Pais</a>
				</li>
				<li>
					<a href="#" onclick="addTab('depto'); return false;">Departamentos</a>
				</li>
				<li>
					<a href="#" onclick="addTab('municipio'); return false;">Municipios</a>
				</li>
				<li>
					<a href="#" onclick="addTab('poblado'); return false;">Poblados</a>
				</li>
				<li>
					<a href="#" onclick="addTab('region'); return false;">Regiones</a>
				</li>
				<li>
					<a href="#" onclick="addTab('comuna'); return false;">Comunas</a>
				</li>
				<li>
					<a href="#" onclick="addTab('barrio'); return false;">Barrios</a>
				</li>
				<!--<li>
					<a href="#" onclick="addTab('resguardo'); return false;">Resguardos</a>
				</li>-->
			</ul>
		<? } ?>	

		<? if (in_array(27,$perfil->id_modulo)){ ?>
			<h1>PERFIL</h1>
			<ul>
				<li>
					<a href="#" onclick="addTabAccion('minificha','insertar',''); return false;">Administraci&oacute;n</a>
				</li>	
				<li>
					<a href="#" onclick="window.open('index_parser.php?m_e=minificha_orden&accion=insertar','','width=700,height=500,top=100,left=200'); return false;">Ordenar Categorias</a>
				</li>	
				<li>
					<a href="#" onclick="if(confirm('Esta seguro que desea borrar el cache de perfiles')){window.open('../tmp_scripts/borrar_cache_perfil.php','','width=500,height=200,top=300,left=200'); return false;}else{return false;}">Borrar Cach&eacute;</a>
				</li>	
			</ul>
		<? } ?>	
		
		<? if (in_array(28,$perfil->id_modulo)){ ?>
			<h1>LOG</h1>
			<ul>
				<li>
					<a href="#" onclick="addTabAccion('log_admin','listar','ListarTablaAdmin'); return false;">Administraci&oacute;n</a>
				</li>
				<li>
					<!--<a href="#" onclick="addTabAccion('log_consulta','listar','ListarTablaConsulta'); return false;">Consultas</a>-->
					<a href="index.php?m_g=alimentacion&m_e=log_consulta&accion=listar&class=LOGUsuarioDAO&method=ListarTablaConsulta" target="_blank">Consultas</a>
				</li>
			</ul>
		<? } ?>	
		</td>
		<td valign="top">
			<?php	
			if (in_array(33,$perfil->id_modulo)){
				?>
					<h1>CONTACTOS</h1>
					<ul>
						<li>
							<a href="#" onclick="addTab('espacio'); return false;">Espacios</a>
						</li>
						<li>
							<a href="#" onclick="addTab('espacio_usuario'); return false;">Acceso a espacios por usuario</a>
						</li>
						<li>
							<a href="#" onclick="addTab('contacto_col'); return false;">Caracter&iacute;sticas</a>
						</li>
						<li>
							<a href="#" onclick="addTab('contacto_col_op'); return false;">Opciones Caracter&iacute;sticas</a>
						</li>
					</ul>
				</li>
			<?php
			}
			if (in_array(2,$perfil->id_modulo)){
				?>
					<h1>SISTEMA</h1>
					<ul>
                    <li>
                        <a href="#" onclick="addTab('emergencia'); return false;">Emergencias</a>
                    </li>
					<li>
						<a href="#" onclick="addTab('modulo'); return false;">M&oacute;dulos</a>
					</li>
					<li>
						<a href="#" onclick="addTab('sector'); return false;">Sectores</a>
						<ul>
							<li><a href="#" onclick="addTabAccion('sector','listar','Reportar'); return false;">Reportar</a></li>
						</ul>
					</li>
					<li>
						<a href="#" onclick="addTab('poblacion'); return false;">Poblaci&oacute;n</a>
						<ul>
							<li><a href="#" onclick="addTabAccion('poblacion','listar','Reportar'); return false;">Reportar</a></li>
						</ul>	
					</li>
					<li>
						<a href="#" onclick="addTabAccion('info_ficha','actualizar',''); return false;">Fichas de Informaci&oacute;n para<br /> Gr&aacute;ficas y Resumenes</a>
					</li>
					<li>
						<a href="#" onclick="addTab('sugerencia'); return false;">Sugerencia</a>
					</li>
					</ul>
					</li>
					<?
			}
			?>
		</td>
	</tr>
</table>
