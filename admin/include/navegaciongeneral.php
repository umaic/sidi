<?
//CONSULTA EL PERFIL DE USUARIO
if (isset($_SESSION["id_tipo_usuario_s"])){
	$perfil_dao = New PerfilUsuarioDAO();
	$perfil = $perfil_dao->GetAllArray('ID_TIPO_USUARIO = '.$_SESSION["id_tipo_usuario_s"]);
}

if ($_SESSION["m_g"] == "admin"){

	echo '<ul class="menulist" id="listMenuRoot">';
	?>
		<li><a href="/sissh/admin/index.php?accion=logout">Logout</a>
		<li><a href="https://sidi.umaic.org">Inicio</a></li>
		<li>|</li>
		<?
		if (in_array(28,$perfil->id_modulo)){
			?>
			<li>
				<a href='#'>LOG</a>
				<ul>
					<li>
						<a href="#" onclick="addTabAccion('log_admin','listar','ListarTablaAdmin'); return false;">Administraci&oacute;n</a>
					</li>
					<li>
						<a href="#" onclick="addTabAccion('log_consulta','listar','ListarTablaConsulta'); return false;">Consultas</a>
					</li>
				</ul>
			</li>
			<?
		}
	if (in_array(27,$perfil->id_modulo)){
		?>
		<li>
			<a href='#'>Perfil</a>
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
		</li>	
		<?
	}
	//CONTACTO
	if (in_array(33,$perfil->id_modulo)){
		?>
		<li>
			<a href='index.php?m_e=minificha&accion=insertar'>Contactos</a>
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
		<?
	}
	if (in_array(26,$perfil->id_modulo)){
		?>
			<!--<li>
			<a href='index.php?m=cnrr_admin'>CNRR</a>
			<a href='#'>CNRR</a>
			<ul>
			<li><a href="index.php?m_e=cnrr&accion=listar&class=CnrrDAO&method=ListarTablaEPST&param=">Enfoque-Pob.-Sector-Tipo</a></li>
			<li><a href="index.php?m_e=cnrr&accion=listar&class=CnrrDAO&method=ListarTablaPerfil&param=">Permisos por Tipo de Usuario</a></li>
			</ul>
			</li>
			--><?
	}
	if (in_array(23,$perfil->id_modulo)){
		//echo "<li><a href='index.php?m=mina_admin'>Mina</a></li>";
	}

	if (in_array(20,$perfil->id_modulo)){
		?>
		<li>
			<a href='#'>Desplazamiento</a>
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
		</li>
		<?
	}
	if (in_array(12,$perfil->id_modulo)){
		?>
		<li>
			<a href='#'>D. Sectorial</a>
			<ul>
				<li>
					<a href="#" onclick="addTab('cat_d_s'); return false;">Categoria</a>
				</li>
				<li>
					<a href="#" onclick="addTab('u_d_s'); return false;">Unidad de Dato Sectorial</a>
				</li>
				<li>
					<a href="#" onclick="addTab('dato_sectorial'); return false;">Dato Sectorial</a>
					<ul>
						<li><a href="#" onclick="addTabAccion('dato_sectorial','listar','ReportarAdmin');return false;"><img src="images/home/reportes.png">&nbsp;Reportar</a></li>
					</ul>
				</li>
			</ul>
		</li>
		<?
	}
	if (in_array(6,$perfil->id_modulo)){
		?>
		<li>
			<a href='#'>Organizaci&oacute;n</a>
			<ul>
				<li>
					<a href="#" onclick="addTab('tipo_org'); return false;">Tipo de Organizaci&oacute;n</a>
					<!--
					<ul>
						<li><a href="#" onclick="addTabAccion('tipo_org','listar','Reportar');return false;"><img src="images/home/reportes.png">&nbsp;Reportar</a></li>
					</ul>-->
				</li>
				<li>
					<a href="#" onclick="addTab('enfoque'); return false;">Enfoque</a>
				</li>
			</ul>
		</li>
		<?
	}
	if (in_array(5,$perfil->id_modulo)){
		?>
		<li>
			<a href='#'>Proyecto</a>
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
			</ul>
		</li>
		<?
	}
	if (in_array(4,$perfil->id_modulo)){
		?>
		<li>
			<a href='#'>Evento-D. Natural</a>
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
		</li>
		<?
	}
	if (in_array(29,$perfil->id_modulo)){
		?>
		<li>
			<a href='#'>Evento-Conflicto</a>
			<ul>
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
		</li>
		<?
	}
	if (in_array(19,$perfil->id_modulo)){
		?>
			<li>
				<a href='#'>M. Geográfico</a>
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
			</li>
			<?
	}
	if (in_array(3,$perfil->id_modulo)){
		?>
			<li>
				<a href='#'>Usuario</a>
				<ul>
					<li>
						<a href="#" onclick="addTab('tipo_usuario'); return false;">Tipo Usuarios</a>
					</li>
					<li>
						<a href="#" onclick="addTab('perfil_usuario'); return false;">Acceso a m&oacute;dulos por tipo de usuario</a>
					</li>
					<li>
						<a href="#" onclick="addTab('usuario'); return false;">Usuarios</a>
						<ul>
							<li><a href="#" onclick="addTabAccion('usuario','listar','Reportar'); return false;"><img src="images/home/reportes.png">&nbsp;Reportar</a></li>
						</ul>
					</li>
				</ul>
			</li>
			<?
	}
	if (in_array(2,$perfil->id_modulo)){
		?>
			<li>
			<!--<a href='index.php?m=sistema_admin'>Sistema</a>-->
			<a href='#'>Sistema</a>
			<ul>
			<li>
				<a href="#" onclick="addTab('modulo'); return false;">M&oacute;dulos</a>
			</li>
			<li>
				<a href="#" onclick="addTab('sector'); return false;">Sectores</a>
				<ul>
					<li><a href="#" onclick="addTabAccion('sector','listar','Reportar'); return false;"><img src="images/home/reportes.png">&nbsp;Reportar</a></li>
				</ul>
			</li>
			<li>
				<a href="#" onclick="addTab('poblacion'); return false;">Poblaci&oacute;n</a>
				<ul>
					<li><a href="#" onclick="addTabAccion('poblacion','listar','Reportar'); return false;"><img src="images/home/reportes.png">&nbsp;Reportar</a></li>
				</ul>	
			</li>
			<li>
				<a href="#" onclick="addTabAccion('info_ficha','actualizar',''); return false;">Fichas de Información para Gr&aacute;ficas y Resumenes</a>
			</li>
			<li>
				<a href="#" onclick="addTab('sugerencia'); return false;">Sugerencia</a>
			</li>
			</ul>
			</li>
			<?
	}
	?>
		</ul>
		<?
}
else if ($_SESSION["m_g"] == "alimentacion"){

	echo '<ul class="menulist" id="listMenuRoot">';
	?>
		<li><a href="/sissh/admin/index.php?accion=logout">Logout</a>
		<li><a href="https://sidi.umaic.org">Inicio</a></li>
		<li><a href="/sissh/admin/index.php?m_g=alimentacion">Inicio Alimentaci&oacute;n</a></li>
		<li>|</li>
		<?

		if (in_array(25,$perfil->id_modulo)){
			//echo "<td><a href='index.php?m_e=mina&accion=listar&class=MinaDAO&method=ListarTabla&param='>Mina</a></td>";
			?>
				<li>
				<a href='#'>Mina</a>
				<ul>
				<li><a href="index.php?m_e=mina&accion=listar&class=MinaDAO&method=ListarTabla&param=">Consultar</a></li>
				<li><a href="index.php?m_e=mina&accion=importar">Importar</a></li>
				</ul>
				</li>
				<?
		}
	if (in_array(8,$perfil->id_modulo)){
		//echo "<td><a href='index.php?m_e=evento&accion=listar&class=EventoDAO&method=ListarTabla&param='>Eventos(D. Natural)</a></td>";
		?>
			<li>
			<a href='#'>Eventos-D. Natural</a>
			<ul>
			<li><a href="index.php?m_e=evento&accion=listar&class=EventoDAO&method=ListarTabla&param=">Listar</a></li>
			<li><a href="index.php?m_e=evento&accion=insertar">Insertar</a></li>
			</ul>
			</li>
			<?
	}
	if (in_array(29,$perfil->id_modulo)){
		//echo "<td><a href='index.php?m_e=evento_c&accion=insertar'>Eventos(Conflicto)</a></td>";
		?>
			<li>
			<a href='#'>Eventos-Conflicto</a>
			<ul>
			<li><a href="index.php?m_e=evento_c&accion=listar&class=EventoConflictoDAO&method=ListarTabla&param=">Listar</a></li>
			<li><a href="index.php?m_e=evento_c&accion=insertar">Insertar</a></li>
			<li><a href="index.php?m_e=evento_c&accion=reportar_admin">Reporte General</a></li>
			</ul>
			</li>
			<?
	}
	if (in_array(10,$perfil->id_modulo)){
		?>
			<li>
			<a href='#'>Proyectos</a>
			<ul>
			<li><a href="index.php?m_e=proyecto&accion=listar&class=ProyectoDAO&method=ListarTabla&param=">Consultar</a></li>
			<li><a href="index.php?m_e=proyecto&accion=insertar">Insertar</a></li>
			</ul>
			</li>
			<?
	}
	if (in_array(11,$perfil->id_modulo)){
		//		echo "<td><a href='index.php?m_e=org&accion=listar&class=OrganizacionDAO&method=ListarTabla&param='>Organizaciones</a></td>";
		?>
			<li>
			<a href='#'>Organizaciones</a>
			<ul>
			<li><a href="index.php?m_e=org&accion=listar&class=OrganizacionDAO&method=ListarTabla&param=">Consultar</a></li>
			<li><a href="index.php?m_e=org&accion=insertar">Insertar</a></li>
			<li><a href="index.php?m_e=org&accion=importar">Importar</a></li>
			<li><a href="index.php?m_e=org&accion=publicar&class=OrganizacionDAO&method=ListarOrgPublicar&param=">Publicar</a></li>
			<li>
			<a href='#'>Reportes</a>
			<ul>
			<li><a href="index.php?m_e=org&accion=reportar_admin">Conteo de Orgs.</a></li>
			<li><a href="index.php?m_e=org&accion=reportar_admin&reporte=2">Listado por sector, etc</a></li>
			</ul>
			</li>
			<li><a href="index.php?m_e=org&accion=sincro_cnrr&class=OrganizacionDAO&method=ListarOrgSincronizarCNRR&param=">Sincronizar con CNRR</a></li>
			</ul>
			</li>
			<?
	}
	if (in_array(13,$perfil->id_modulo)){
		//echo "<td><a href='index.php?m_e=dato_s_valor&accion=actualizarDatoValor&class=DatoSectorialDAO&method=ListarTabla&param='>Dato Sectorial</a></td>";
		?>
			<li>
			<a href='#'>Dato Sectorial</a>
			<ul>
			<!--<li><a href="index.php?m_e=dato_s_valor&accion=insertarDatoValor">Insertar</a></li> -->
			<li><a href="index.php?m_e=dato_s_valor&accion=importar">Importar</a></li>
			<li><a href="#" onclick="if(confirm('Esta opción calculará los totales Departamentales y Nacionales con los datos actuales en el sistema, esta seguro que desea ejecutar esta opción ?\n\nEl proceso tardará varios minutos...')){window.open('../cron_jobs/totalizar_d_sectorial.php','','top=200,left=250,width=400,height=150');}">Totalizar</a></li>
			<!--<li><a href="index.php?m_e=dato_s_valor&accion=actualizarDatoValor&class=DatoSectorialDAO&method=ListarTabla&param=">Actualizar Valor de Dato Sectorial</a></li> -->
			</ul>
			</li>
			<?
	}
	if (in_array(21,$perfil->id_modulo)){
		//echo "<td><a href='index.php?m_e=desplazamiento&accion=listar&class=DesplazamientoDAO&method=ListarTabla&param='>Desplazamiento</a></td>";
		?>
			<li>
			<a href='#'>Desplazamiento</a>
			<ul>
			<li><a href="index.php?m_e=desplazamiento&accion=listar&class=DesplazamientoDAO&method=ListarTabla&param=">Consultar</a></li>
			<li><a href="index.php?m_e=desplazamiento&accion=insertar">Insertar</a></li>
			<li><a href="index.php?m_e=desplazamiento&accion=importar">Importar CODHES</a></li>
			<li><a href="index.php?m_e=desplazamiento&accion=importar_sipod">Importar Sipod</a></li>
			<li><a href="index.php?m_e=desplazamiento&accion=fechaCorte">Actualizar Fecha de Corte</a></li>
			</ul>
			</li>
			<?
	}

	if (in_array(32,$perfil->id_modulo)){ ?>
		<li>
			<!--<a href='index.php?m=desplazamiento_admin'>Desplazamiento</a>-->
			<a href='#'>Contactos</a>
			<ul>
			<li><a href="index.php?m_e=contacto&accion=insertar">Insertar</a></li>
			<li><a href="index.php?m_e=contacto&accion=listar&class=ContactoDAO&method=ListarTabla&param=">Listar</a></li>
			</ul>
			</li>
			<?

	}
	//CODIGO_USUARIO_CNRR
	if ($_SESSION["id_tipo_usuario_s"] == 21){
		echo "<td><a href='index.php?m_e=org&accion=listar&class=OrganizacionDAO&method=ListarTabla&param='>Organizaciones</a></td>";
	}
	?>
		</ul>
		<?
}
else if ($_SESSION["m_g"] == "consulta"){
	?>
	<ul class="menulist" id="listMenuRoot">
        <?php
        if (in_array(34,$perfil->id_modulo)){	?>
            
            <!-- 4W -->
            <li><a href="/sissh/admin/index.php?m_e=p4w&accion=listar&class=P4wDAO&method=Dashboard&si_proy=<?php echo $_SESSION['si_proy']; ?>">Dashboard Alimentaci&oacute;n</a></li>
        <?php
        } ?>
		<li>|</li>
		<li><a href="https://sidi.umaic.org">Inicio</a></li>
		<li><a href="index.php?accion=logout">Cerrar sesión</a>
    </ul>
    <?php
}
?>
