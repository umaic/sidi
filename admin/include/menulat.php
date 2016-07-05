<?
switch ($_SESSION["m_e"]){
	case "tipo_evento": ?>
		<ul id="menulat">
		<li>TIPO DE EVENTOS</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=TipoEventoDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		<li><img src="images/home/reportes.png">&nbsp;<a href="index.php?accion=listar&class=TipoEventoDAO&method=Reportar&param=">Reportar</a></li>
		</ul>
		<?
		break;

	case "actor": ?>
		<ul id="menulat">
		<li>ACTORES</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=ActorDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		<li><img src="images/home/reportes.png">&nbsp;<a href="index.php?accion=listar&class=ActorDAO&method=Reportar&param=">Reportar</a></li>
		</ul>
		<?
		break;

	case "riesgo_hum": ?>
		<ul id="menulat">
		<li>RIESGOS HUMANITARIOS</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=RiesgoHumDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		<li><img src="images/home/reportes.png">&nbsp;<a href="index.php?accion=listar&class=RiesgoHumDAO&method=Reportar&param=">Reportar</a></li>
		</ul>
		<?
		break;

	case "cons_hum": ?>
		<ul id="menulat">
		<li>CONSECUENCIAS HUMANITARIAS</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=ConsHumDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		<li><img src="images/home/reportes.png">&nbsp;<a href="index.php?accion=listar&class=ConsHumDAO&method=Reportar&param=">Reportar</a></li>
		</ul>
		<?
		break;

	case "evento":
		?>
		<ul id="menulat">
		<li>EVENTOS (D. Natural)</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=EventoDAO&method=ListarTabla&param=">Consultar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;

	case "evento_c":
		?>
		<ul id="menulat">
		<li>EVENTOS (Conflicto)</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=EventoConflictoDAO&method=ListarTabla&param=">Consultar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;

	case "tipo_usuario":
		?>
		<ul id="menulat">
		<li>TIPO DE USUARIO</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=TipoUsuarioDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;

	case "perfil_usuario":
		?>
		<ul id="menulat">
		<li>PERFIL DE USUARIO</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=PerfilUsuarioDAO&method=ListarTabla&param=">Modificar</a></li>
		</ul>
		<?
		break;

	case "usuario":
		?>
		<ul id="menulat">
		<li>USUARIO</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=UsuarioDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		<li><img src="images/home/reportes.png">&nbsp;<a href="index.php?accion=listar&class=UsuarioDAO&method=Reportar&param=">Reportar</a></li>
		</ul>
		<?
		break;

	case "modulo":
		?>
		<ul id="menulat">
		<li>MODULO</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=ModuloDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;

	case "contacto":
		?>
		<ul id="menulat">
		<li>CONTACTOS</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=ContactoDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;

	case "sector":
		?>
		<ul id="menulat">
		<li>SECTOR</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=SectorDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		<li><img src="images/home/reportes.png">&nbsp;<a href="index.php?accion=listar&class=SectorDAO&method=Reportar&param=">Reportar</a></li>
		</ul>
		<?
		break;

	case "org":
		?>
		<ul id="menulat">
		<li>ORGANIZACIONES</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=OrganizacionDAO&method=ListarTabla&param=">Consultar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>

		<?
		//CODIGO_USUARIO_CNRR
		if ($_SESSION["id_tipo_usuario_s"] != 21){ ?>
			<li><img src="images/home/importar.png">&nbsp;<a href="index.php?accion=importar">Importar</a></li>
				<li><img src="images/home/reportes.png">&nbsp;<a href="index.php?accion=reportar_admin">Reportes</a></li>
				<li><img src="images/home/publicar.png">&nbsp;<a href="index.php?m_e=org&accion=publicar&class=OrganizacionDAO&method=ListarOrgPublicar&param=">Publicar Org.</a></li>
				<? } ?>
				</ul>
				<?
				break;
	case "tipo_org":
		?>
		<ul id="menulat">
		<li>TIPO DE ORGANIZACION</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=TipoOrganizacionDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		<li><img src="images/home/reportes.png">&nbsp;<a href="index.php?accion=listar&class=TipoOrganizacionDAO&method=Reportar&param=">Reportar</a></li>
		</ul>
		<?
		break;
	case "enfoque":
		?>
		<ul id="menulat">
		<li>ENFOQUES</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=EnfoqueDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		<li><img src="images/home/reportes.png">&nbsp;<a href="index.php?accion=listar&class=EnfoqueDAO&method=Reportar&param=">Reportar</a></li>
		</ul>
		<?
		break;
	case "poblacion":
		?>
		<ul id="menulat">
		<li>POBLACION</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=PoblacionDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		<li><img src="images/home/reportes.png">&nbsp;<a href="index.php?accion=listar&class=PoblacionDAO&method=Reportar&param=">Reportar</a></li>
		</ul>
		<?
		break;
	case "moneda":
		?>
		<ul id="menulat">
		<li>MONEDA</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=MonedaDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;

	case "tema":
		?>
		<ul id="menulat">
		<li>TEMA PROYECTO</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=TemaDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;
	case "estado_proyecto":
		?>
		<ul id="menulat">
		<li>ESTADO</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=EstadoProyectoDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;
	case "clasificacion":
		?>
		<ul id="menulat">
		<li>CLASIFICACION</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=ClasificacionDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;


	case "proyecto":
		?>
		<ul id="menulat">
		<li>PROYECTO</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=ProyectoDAO&method=ListarTabla&param=">Consultar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;
	case "cat_d_s":
		?>
		<ul id="menulat">
		<li>CAT. DATO SECTORIAL</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=CategoriaDatoSectorDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;
	case "u_d_s":
		?>
		<ul id="menulat">
		<li>UNIDAD DATO SECTORIAL</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=UnidadDatoSectorDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;
	case "dato_sectorial":
		?>
		<ul id="menulat">
		<li>DATO SECTORIAL</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=DatoSectorialDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		<li><img src="images/home/reportes.png">&nbsp;<a href="index.php?accion=listar&class=DatoSectorialDAO&method=ReportarAdmin&param=">Reportar</a></li>
		</ul>
		<?
		break;
	case "dato_s_valor":
		?>
		<ul id="menulat">
		<li>DATO SECTORIAL</li>
		</ul>
		<ul id="menulat">
		<!--<li><img src="images/home/actualizar.png">&nbsp;<a href="index.php?accion=actualizarDatoValor">Actualizar</a></li>-->
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertarDatoValor">Insertar</a></li>
		<li><img src="images/home/importar.png">&nbsp;<a href="index.php?m_e=dato_s_valor&accion=importar">Importar</a></li>
		</ul>
		<?
		break;
	case "pais":
		?>
		<ul id="menulat">
		<li>PAIS</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=PaisDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;
	case "depto":
		?>
		<ul id="menulat">
		<li>DEPARTAMENTO</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=DeptoDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;
	case "municipio":
		?>
		<ul id="menulat">
		<li>MUNICIPIO</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=MunicipioDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;
	case "poblado":
		?>
		<ul id="menulat">
		<li>POBLADO</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=PobladoDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;
	case "region":
		?>
		<ul id="menulat">
		<li>REGION</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=RegionDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;
	case "comuna":
		?>
		<ul id="menulat">
		<li>COMUNA</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=ComunaDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;
	case "barrio":
		?>
		<ul id="menulat">
		<li>BARRIO</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=BarrioDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;
	case "periodo":
		?>
		<ul id="menulat">
		<li>PERIODO</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=PeriodoDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;
	case "tipo_desplazamiento":
		?>
		<ul id="menulat">
		<li>TIPO DESPLAZAMIENTO</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=TipoDesplazamientoDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;
	case "clase_desplazamiento":
		?>
		<ul id="menulat">
		<li>CLASE DESPLAZAMIENTO</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=ClaseDesplazamientoDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;
	case "fuente":
		?>
		<ul id="menulat">
		<li>FUENTE DE INFORMACION</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=FuenteDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;
	case "desplazamiento":
		?>
		<ul id="menulat">
		<li>DESPLAZAMIENTO</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=DesplazamientoDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		<li><img src="images/home/importar.png">&nbsp;<a href="index.php?m_e=desplazamiento&accion=importar">Importar CODHES</a></li>
		<li><img src="images/home/importar.png">&nbsp;<a href="index.php?m_e=desplazamiento&accion=importar_sipod">Importar SIPOD</a></li>
		<li><img src="images/home/fecha.png">&nbsp;<a href="index.php?m_e=desplazamiento&accion=fechaCorte">Fecha de Corte</a></li>
		</ul>
		<?
		break;
	case "sexo":
		?>
		<ul id="menulat">
		<li>SEXO</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=SexoDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;
	case "condicion_mina":
		?>
		<ul id="menulat">
		<li>CONDICION</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=CondicionMinaDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;
	case "edad":
		?>
		<ul id="menulat">
		<li>EDAD</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=EdadDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;
	case "rango_edad":
		?>
		<ul id="menulat">
		<li>RANGO EDAD</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=RangoEdadDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;  	  
	case "estado_mina":
		?>
		<ul id="menulat">
		<li>ESTADO</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=EstadoMinaDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;
	case "mina":
		?>
		<ul id="menulat">
		<li>MINA</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=MinaDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/importar.png">&nbsp;<a href="index.php?m_e=mina&accion=importar">Importar</a></li>
		</ul>
		<?
		break;
	case "minificha":
		?>
		<ul id="menulat">
		<li>MINIFICHA</li>
		</ul>
		<ul id="menulat">
		<li>Seleccione la información que va a ser incluida en la generación de la Minificha Departamental o Municipal</li>
		</ul>
		<?
		break;
	case "cat_evento_c":
		?>
		<ul id="menulat">
		<li>CATEGORIA EVENTOS</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=CatEventoConflictoDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;
	case "subcat_evento_c":
		?>
		<ul id="menulat">
		<li>SUB CATEGORIA EVENTOS</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=SubCatEventoConflictoDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;
	case "fuente_evento_c":
		?>
		<ul id="menulat">
		<li>FUENTE EVENTOS</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=FuenteEventoConflictoDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;
	case "subfuente_evento_c":
		?>
		<ul id="menulat">
		<li>SUB FUENTE EVENTOS</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=SubFuenteEventoConflictoDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;
	case "etnia":
		?>
		<ul id="menulat">
		<li>ETNIA</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=EtniaDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;  	  
	case "sub_condicion":
		?>
		<ul id="menulat">
		<li>SUB CONDICION</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=SubCondicionDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;  	  
	case "sub_etnia":
		?>
		<ul id="menulat">
		<li>SUB ETNIA</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=SubEtniaDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;  	  
	case "ocupacion":
		?>
		<ul id="menulat">
		<li>OCUPACION</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=OcupacionDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;  	  
	case "log_consulta":
		?>
		<ul id="menulat">
		<li>LOG MODULO CONSULTA</li>
		</ul>
		<ul id="menulat">
		<li><a href="#est_gen">Estadisticas Generales</a></li>
		<li><a href="#perfiles">Detalle Perfiles</a></li>
		<li><a href="#gra_resum">Detalle Gr&aacute;ficas y Resumenes</a></li>
		<li><a href="#reportes">Detalle Reportes</a></li>
		<li><a href="#usuarios">Detalle Usuarios</a></li>
		</ul>
		<?
		break;  	  
	case "espacio":
		?>
		<ul id="menulat">
		<li>ESPACIO</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=EspacioDAO&method=ListarTabla&param=">Listar</a></li>
		<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?accion=insertar">Insertar</a></li>
		</ul>
		<?
		break;  	  
	
	case "espacio_usuario":
		?>
		<ul id="menulat">
		<li>ESPACIO USUARIO</li>
		</ul>
		<ul id="menulat">
		<li><img src="images/home/listar.png">&nbsp;<a href="index.php?accion=listar&class=EspacioUsuarioDAO&method=ListarTabla&param=">Modificar</a></li>
		</ul>
		<?
		break;  	  
}
?>
