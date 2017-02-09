<?php
include_once 'seguridad.php';
include_once 'lib/libs_perfil_usuario.php';

//CONSULTA EL PERFIL DE USUARIO
$perfil_dao = New PerfilUsuarioDAO();
$perfil = $perfil_dao->GetAllArray('ID_TIPO_USUARIO = '.$_SESSION["id_tipo_usuario_s"]);
?>
<div id="home_alimentacion">
	<?
	if (in_array(11,$perfil->id_modulo)){	?>
        <!-- ORGS -->
        <div class="modulo">
            <h1>Organizaciones</a></h1>
            <ul>
                <li><img src="images/home/consultar.png">&nbsp;<a href="index.php?m_e=org&accion=listar&class=OrganizacionDAO&method=ListarTabla&param=">Consultar</a></li>
                <li><img src="images/home/insertar.png">&nbsp;<a href="index.php?m_e=org&accion=insertar">Insertar</a></li>
                <li><img src="images/home/importar.png">&nbsp;<a href="index.php?m_e=org&accion=importar">Importar</a></li>
                <li><img src="images/home/publicar.png">&nbsp;<a href="index.php?m_e=org&accion=publicar&class=OrganizacionDAO&method=ListarOrgPublicar&param=">Publicar</a></li>
                <li><img src="images/home/sincro.png">&nbsp;<a href="index.php?m_e=org&accion=sincro_cnrr&class=OrganizacionDAO&method=ListarOrgSincronizarCNRR&param=">Sincronizar con CNRR</a></li>
                <li>
                    <ul>
                        <li><b>REPORTES</b></li>
                        <li><a href="index.php?m_e=org&accion=reportar_admin">Conteo de Organizaciones</a></li>
                        <li><a href="index.php?m_e=org&accion=reportar_admin&reporte=2">Listado por sector, enfoque y cobertura</a><br> &nbsp;&nbsp;&nbsp; 
                        <font class="nota">[ Listado a manera de Directorio ]</font></li>
                    </ul>
                </li>
            </ul>
        </div>
    <? } ?>
    <?
    if (in_array(2,$perfil->id_modulo)){	?>
        <!-- DESPLAZAMIENTO -->
        <div class="modulo">
            <h1>Desplazamiento</h1>
            <ul>
                <li><img src="images/home/consultar.png">&nbsp;<a href="index.php?m_e=desplazamiento&accion=listar&class=DesplazamientoDAO&method=ListarTabla&param=">Consultar</a></li>
                <li><img src="images/home/insertar.png">&nbsp;<a href="index.php?m_e=desplazamiento&accion=insertar">Insertar</a></li>
                <li><img src="images/home/importar.png">&nbsp;<a href="index.php?m_e=desplazamiento&accion=importar">Importar CODHES</a></li>
                <li><img src="images/home/importar.png">&nbsp;<a href="index.php?m_e=desplazamiento&accion=importar_sipod">Importar DPS</a></li>
                <li><img src="images/home/fecha.png">&nbsp;<a href="index.php?m_e=desplazamiento&accion=fechaCorte">Actualizar Fecha de Corte</a></li>
                <li><img src="images/home/info.png">&nbsp;<a href="http://sidih.colombiassh.org/sissh/doc/index.php/Alimentacion:Desplazamiento" target="_blank">Ayuda</a></li>
            </ul>
        </div>
    <? } ?>
	<?
	if (in_array(13,$perfil->id_modulo)){	?>
		<!-- DATO SECTORIAL -->
        <div class="modulo">
            <h1>Datos Sectoriales</h1>
            <ul>
                <li><img src="images/home/importar.png">&nbsp;<a href="index.php?m_e=dato_s_valor&accion=importar">Importar</a></li>
                <li><img src="images/home/totalizar.png">&nbsp;<a href="#" onclick="if(confirm('Esta opción calculará los totales Departamentales y Nacionales con los datos actuales en el sistema, esta seguro que desea ejecutar esta opción ?\n\nEl proceso tardará varios minutos...')){window.open('../cron_jobs/totalizar_d_sectorial.php','','top=200,left=250,width=800,height=600,scrollbars=1');}">Totalizar</a></li>
                <li><img src="images/home/info.png">&nbsp;<a href="http://sidih.colombiassh.org/sissh/doc/index.php/Alimentacion:Datos_Sectoriales" target="_blank">Ayuda</a></li>
            </ul>
        </div>
    <? } ?>

    <?
    if (in_array(30,$perfil->id_modulo)){	?>
        <!-- EVENTOS-CONFLICTO -->
        <div class="modulo">
            <h1>Eventos Conflicto</h1>
            <ul>
                <li><img src="images/home/listar.png">&nbsp;<a href="index.php?m_e=evento_c&accion=listar&class=EventoConflictoDAO&method=ListarTabla&param=">Listar</a></li>
                <!--<li><img src="images/home/insertar.png">&nbsp;<a href="index.php?m_e=evento_c&accion=insertar">Insertar</a></li>-->
                <li><img src="images/home/reportes.png">&nbsp;<a href="index.php?m_e=evento_c&accion=reportar_admin">Reporte General</a></li>
                    <ul>
                        <li><b>REPORTE SEMANAL</b></li>
                        <li><img src="images/home/listar.png">&nbsp;<a href="index.php?m_e=rp_s&accion=listar&class=ReporteSemanalDAO&method=ListarTabla&param=">Listar</a></li>
                        <li><img src="images/home/insertar.png">&nbsp;<a href="index.php?m_e=rp_s&accion=insertar">Generar</a></li>
                    </ul>
                </li>
                <li><img src="images/home/info.png">&nbsp;<a href="http://sidih.colombiassh.org/sissh/doc/index.php/Alimentacion:Eventos_Conflicto" target="_blank">Ayuda</a></li>
            </ul>
        </div>
    <? } ?>
	<?
	if (in_array(8,$perfil->id_modulo)){	?>
		<!-- EVENTOS-D.NATURAL -->
        <div class="modulo">
            <h1>Ev. Desastre Natural</a></h1>
            <ul>
                <li><img src="images/home/listar.png">&nbsp;<a href="index.php?m_e=evento&accion=listar&class=EventoDAO&method=ListarTabla&param=">Listar</a></li>
                <li><img src="images/home/insertar.png">&nbsp;<a href="index.php?m_e=evento&accion=insertar">Insertar</a></li>
            </ul>
        </div>
    <? } ?>
    <?
    if (in_array(25,$perfil->id_modulo)){	?>
        <!-- MINA -->
        <div class="modulo">
            <h1>Eventos Mina</h1>
            <ul>
                <li><img src="images/home/consultar.png">&nbsp;<a href="index.php?m_e=mina&accion=listar&class=MinaDAO&method=ListarTabla&param=">Consultar</a></li>
                <li><img src="images/home/importar.png">&nbsp;<a href="index.php?m_e=mina&accion=importar">Importar</a></li>
            </ul>
        </div>
    <? } ?>
	
    <?
	if (in_array(32,$perfil->id_modulo)){	?>
		<!-- CONTACTOS -->
        <div class="modulo">
            <h1>Contactos</h1>
            <ul>
                <li><img src="images/home/insertar.png">&nbsp;<a href="index.php?m_e=contacto&accion=insertar">Insertar</a></li>
                <li><img src="images/home/consultar.png">&nbsp;<a href="index.php?m_e=contacto&accion=listar&class=ContactoDAO&method=ListarTabla&param=">Consultar</a></li>
                <li><img src="images/home/importar.png">&nbsp;<a href="index.php?m_e=contacto&accion=importar">Importar</a></li>
            </ul>
        </div>
    <? } ?>
	<?
	if (in_array(10,$perfil->id_modulo)){	?>
		<!-- PROYECTOS -->
        <div class="modulo">
            <h1>Proyectos</h1>
            <ul>
                <li><img src="images/home/insertar.png">&nbsp;<a href="index.php?m_e=p4w&accion=insertar">Insertar</a>
                <li><img src="images/home/consultar.png">&nbsp;<a href="index.php?m_e=p4w&accion=listar&class=P4wDAO&method=Dashboard&param=&si_proy=4w">Consultar</a></li>
            </ul>
        </div>
    <? } ?>
    <div class="clear"></div>
</div>
