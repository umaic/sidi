<table id='navegaciongeneral'><tr>

<? 
if ($_SESSION["m"] == "evento_admin"){ ?>
    <td><a href="index.php?m_e=tipo_evento&accion=listar&class=TipoEventoDAO&method=ListarTabla&param=">Tipo de Eventos</a></td>
    <td><a href="index.php?m_e=actor&accion=listar&class=ActorDAO&method=ListarTabla&param=">Actores</a></td>
	<td><a href="index.php?m_e=riesgo_hum&accion=listar&class=RiesgoHumDAO&method=ListarTabla&param=">Riesgos Humanitarios</a></td>
	<td><a href="index.php?m_e=cons_hum&accion=listar&class=ConsHumDAO&method=ListarTabla&param=">Consecuencias Humanitarias</a></td>
<?
}
if ($_SESSION["m"] == "evento_c_admin"){ ?>
    <td><a href="index.php?m_e=sexo&accion=listar&class=SexoDAO&method=ListarTabla&param=">Sexo</a></td>
    <td><a href="index.php?m_e=condicion_mina&accion=listar&class=CondicionMinaDAO&method=ListarTabla&param=">Condición</a></td>
    <td><a href="index.php?m_e=sub_condicion&accion=listar&class=SubCondicionDAO&method=ListarTabla&param=">Sub Condición</a></td>
    <td><a href="index.php?m_e=edad&accion=listar&class=EdadDAO&method=ListarTabla&param=">Edad</a></td>
    <td><a href="index.php?m_e=rango_edad&accion=listar&class=RangoEdadDAO&method=ListarTabla&param=">Rango Edad</a></td>
    <td><a href="index.php?m_e=ocupacion&accion=listar&class=OcupacionDAO&method=ListarTabla&param=">Ocupaci&oacute;n</a></td>
    <td><a href="index.php?m_e=estado_mina&accion=listar&class=EstadoMinaDAO&method=ListarTabla&param=">Estado</a></td>
    <td><a href="index.php?m_e=etnia&accion=listar&class=EtniaDAO&method=ListarTabla&param=">Etnia</a></td>
    <td><a href="index.php?m_e=sub_etnia&accion=listar&class=SubEtniaDAO&method=ListarTabla&param=">Sub Etnia</a></td>
    <td><a href="index.php?m_e=cat_evento_c&accion=listar&class=CatEventoConflictoDAO&method=ListarTabla&param=">Categoria de Eventos</a></td>
    <td><a href="index.php?m_e=subcat_evento_c&accion=listar&class=SubCatEventoConflictoDAO&method=ListarTabla&param=">Sub Categoria de Eventos</a></td>
    <td><a href="index.php?m_e=fuente_evento_c&accion=listar&class=FuenteEventoConflictoDAO&method=ListarTabla&param=">Fuente de Eventos</a></td>
    <td><a href="index.php?m_e=subfuente_evento_c&accion=listar&class=SubFuenteEventoConflictoDAO&method=ListarTabla&param=">Sub Fuente de Eventos</a></td>
<?
}
else if ($_SESSION["m"] == "usuario_admin"){ ?>
    <td><a href="index.php?m_e=tipo_usuario&accion=listar&class=TipoUsuarioDAO&method=ListarTabla&param=">Tipo de Usuario</a></td>
    <td><a href="index.php?m_e=perfil_usuario&accion=listar&class=PerfilUsuarioDAO&method=ListarTabla&param=">Acceso a módulos por Tipo de Usuario</a></td>
	<td><a href="index.php?m_e=usuario&accion=listar&class=UsuarioDAO&method=ListarTabla&param=">Usuarios</a></td>
<?
}
else if ($_SESSION["m"] == "sistema_admin"){ ?>
    <td><a href="index.php?m_e=modulo&accion=listar&class=ModuloDAO&method=ListarTabla&param=">Módulos</a></td>
    <td><a href="index.php?m_e=contacto&accion=listar&class=ContactoDAO&method=ListarTabla&param=">Contáctos</a></td>
    <td><a href="index.php?m_e=sector&accion=listar&class=SectorDAO&method=ListarTabla&param=">Sectores</a></td>
    <td><a href="index.php?m_e=poblacion&accion=listar&class=PoblacionDAO&method=ListarTabla&param=">Población</a></td>

<?
}
else if ($_SESSION["m"] == "geo_admin"){ ?>
    <td><a href="index.php?m_e=pais&accion=listar&class=PaisDAO&method=ListarTabla&param=">Pais</a></td>
    <td><a href="index.php?m_e=depto&accion=listar&class=DeptoDAO&method=ListarTabla&param=">Departamentos</a></td>
    <td><a href="index.php?m_e=municipio&accion=listar&class=MunicipioDAO&method=ListarTabla&param=">Municipios</a></td>
    <td><a href="index.php?m_e=poblado&accion=listar&class=PobladoDAO&method=ListarTabla&param=">Poblados</a></td>
    <td><a href="index.php?m_e=region&accion=listar&class=RegionDAO&method=ListarTabla&param=">Regiones</a></td>
    <td><a href="index.php?m_e=comuna&accion=listar&class=ComunaDAO&method=ListarTabla&param=">Comunas</a></td>
    <td><a href="index.php?m_e=barrio&accion=listar&class=BarrioDAO&method=ListarTabla&param=">Barrios</a></td>

<?
}
else if ($_SESSION["m"] == "org_admin"){ ?>
    <td><a href="index.php?m_e=tipo_org&accion=listar&class=TipoOrganizacionDAO&method=ListarTabla&param=">Tipo de Organización</a></td>
    <td><a href="index.php?m_e=enfoque&accion=listar&class=EnfoqueDAO&method=ListarTabla&param=">Enfoques</a></td>
<?
}
else if ($_SESSION["m"] == "proyecto_admin"){ ?>
    <td><a href="index.php?m_e=moneda&accion=listar&class=MonedaDAO&method=ListarTabla&param=">Moneda</a></td>
    <td><a href="index.php?m_e=tema&accion=listar&class=TemaDAO&method=ListarTabla&param=">Tema de Proyecto</a></td>
    <td><a href="index.php?m_e=estado_proyecto&accion=listar&class=EstadoProyectoDAO&method=ListarTabla&param=">Estado de Proyecto</a></td>
<?
}
else if ($_SESSION["m"] == "d_s_admin"){ ?>
    <td><a href="index.php?m_e=cat_d_s&accion=listar&class=CategoriaDatoSectorDAO&method=ListarTabla&param=">Categoria</a></td>
    <td><a href="index.php?m_e=u_d_s&accion=listar&class=UnidadDatoSectorDAO&method=ListarTabla&param=">Unidad de Dato Sectorial</a></td>
    <td><a href="index.php?m_e=dato_sectorial&accion=listar&class=DatoSectorialDAO&method=ListarTabla&param=">Dato Sectorial</a></td>
<?
}
else if ($_SESSION["m"] == "desplazamiento_admin"){ ?>
    <td><a href="index.php?m_e=periodo&accion=listar&class=PeriodoDAO&method=ListarTabla&param=">Periodo</a></td>
    <td><a href="index.php?m_e=tipo_desplazamiento&accion=listar&class=TipoDesplazamientoDAO&method=ListarTabla&param=">Tipo</a></td>
    <td><a href="index.php?m_e=clase_desplazamiento&accion=listar&class=ClaseDesplazamientoDAO&method=ListarTabla&param=">Clase</a></td>
    <td><a href="index.php?m_e=fuente&accion=listar&class=FuenteDAO&method=ListarTabla&param=">Fuente</a></td>
<?
}
else if ($_SESSION["m"] == "mina_admin"){ ?>
    <td><a href="index.php?m_e=sexo&accion=listar&class=SexoDAO&method=ListarTabla&param=">Sexo</a></td>
    <td><a href="index.php?m_e=condicion_mina&accion=listar&class=CondicionMinaDAO&method=ListarTabla&param=">Condición</a></td>
    <td><a href="index.php?m_e=edad&accion=listar&class=EdadDAO&method=ListarTabla&param=">Edad</a></td>
    <td><a href="index.php?m_e=estado_mina&accion=listar&class=EstadoMinaDAO&method=ListarTabla&param=">Estado</a></td>
<?
}
else if ($_SESSION["m"] == "cnrr_admin"){ ?>
    <td><a href="index.php?m_e=cnrr&accion=listar&class=CnrrDAO&method=ListarTablaEPST&param=">Enfoques-Población-Sector-Tipo</a></td>
    <td><a href="index.php?m_e=cnrr&accion=listar&class=CnrrDAO&method=ListarTablaPerfil&param=">Permisos por Tipo de Usuario</a></td>
<?
}
else if ($_SESSION["m"] == "evento_consulta"){ ?>
    <td><a href="index_consulta.php?m_e=evento&accion=reportar&class=EventoDAO&method=ReporteDiario&param=">Informe Diario</a></td>
		<td><a href="index_consulta.php?m_e=evento&accion=reportar&class=EventoDAO&method=ReporteSemanal&param=">Informe Semanal</a></td>
<?
}
else if ($_SESSION["m"] == "org_consulta"){ ?>
    <td><a href="index_consulta.php?m_e=org&accion=reportar&class=OrganizacionDAO&method=ReporteCoberturaGeografica">Listar por Cobertura Geográfica</a></td>
	<td><a href="index_consulta.php?m_e=org&accion=reportar&class=OrganizacionDAO&method=Listar&param=tipo">Listar por Tipo de Organización</a></td>
<?
}
else if ($_SESSION["m"] == "log_admin"){ ?>
	<td><a href="index.php?m_e=log_admin&accion=listar&class=LogUsuarioDAO&method=ListarTablaAdmin&param=">Administraci&oacute;n</a></td>
	<td><a href="index.php?m_e=log_consulta&accion=listar&class=LogUsuarioDAO&method=ListarTablaConsulta&param=">Consultas</a></td>
<?
}
?>

</tr></table>