<?php
$hostname = 'http://'.$_SERVER['SERVER_NAME'];
?>
<link href="style/home_sidih_bt.css" rel="stylesheet" type="text/css" />
<link href="style/bootstrap.min.css" rel="stylesheet" type="text/css" />
<div id="home_bt" class="container-fluid">
    <div id="products">
        <div id="monitor" class="p">
            <div><a href="http://monitor.umaic.org" target="_blank">Monitor</a></div>
            <div>Doble afectaciónn</div>
        </div>
        <?php if (in_array($_SESSION['id_tipo_usuario_s'], array(1,2,15,23))) { ?>
        <div id="monitoreo" class="p">
            <a href="<?php echo $hostname ?>/monitoreo_medios/v2" target="_blank">Monitor medios</a>
            <br />Máss en menos tiempo
        </div>
        <div id="gpx" class="p">
            <a href="<?php echo $hostname ?>/im/terrenogps/traces.php?task=gpx" target="_blank">Misiones terreno</a>
            <br />Visualizador de misiones
        </div>
        <?php } ?>
        <div id="divipolador" class="p">
            <a href="<?php echo $hostname ?>/im/divipolaLH/" target="_blank">Divipolador</a>
            <br />Nombre a divipola
        </div>
        <div id="qr" class="p">
            <a href="<?php echo $hostname ?>/im/qr/" target="_blank">Código QR</a>
            <br />Identificador único
        </div>
        <?php if (in_array($_SESSION['id_tipo_usuario_s'], array(1,2,15,23,16))) { ?>
        <div id="contactos" class="p">
            <a href="<?php echo $hostname ?>/sissh/admin/index.php?m_e=contacto&accion=listar&class=ContactoDAO&method=ListarTabla&param=">Contactos</a>
            <br />Lista de contactos
        </div>
        <?php } ?>
    </div>
    <div id="home_item" class="">
    <div class="row">
        <!-- div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                     <img src="images/p4w/icn_4w_pin.png" width="17" height="20" alt="4W" class="pull-left" />
                    <h2 class="panel-title">
                        4W, Who What Where When
                    </h2>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="d2 col-md-6">
                            <h3> Humanitario </h3>
                                <p align='justify'>
                                    Proyectos de respuesta a
                                    desastres naturales y otras intervenciones humanitarias
                                </p>
                                <a href="index.php?m_g=consulta&m_e=p4w&accion=consultar&class=P4W&si_proy=4w">
                                    &raquo; Ingresar humanitario
                                </a>
                       </div>
                       <div class="d2 col-md-6">
                           <h3>Desarrollo y Paz</h3>
                               <p align='justify'>
                                   Proyectos en construcción de paz y desarrollo sostenible
                               </p>
                               <a href="index.php?m_g=consulta&m_e=p4w&accion=consultar&class=P4W&si_proy=des">
                                    <br />&raquo; Ingresar desarrollo y paz
                               </a>
                       </div>
                   </div>
                    <p>&nbsp;</p>
                   <p>
                       <?php
                       if (in_array(34,$perfil->id_modulo)){ ?>
                           <a href="admin/index.php?m_e=p4w&accion=listar&class=P4wDAO&method=Dashboard&param=&si_proy=4w" class="">
                               &raquo; Ingresar al dashboard de alimentación
                           </a>
                       <?php } ?>
                  </p>
                  <p>
                       <a href="p4w.php?x=!5tgbHU8765*&titulo=Emergencia%20Venezuela%202018&resumen=0&filtros=ejecutora,cluster,ubicacion&html=brand&c=emergencia&id=2&si_proy=4w">
                          ** Emergencia Venezuela 2018
                      </a>
                  </p>
               </div>
            </div>
        </div -->
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <img src="images/home/icn_perfil.png" alt="Perfiles Departamentales y Municipales" class="pull-left" width="20" height="20">
                    <h2 class="panel-title">Perfiles Departamentales y Municipales</h2>
                </div>
                <div class="panel-body">
                    <p align='justify'>
                        Es un resumen de cifras estadísticas y gráficas de un departamento o municipio. Incluye un mapa geográfico de la zona.
                        <br /><br />
                        <a href="index.php?m_g=consulta&m_e=minificha&accion=generar&class=Minificha" class="">
                            &raquo; Ingrese aquí para generar el perfil
                        </a>
                    </p>
                    <hr>
                    <p align='justify'>
                        Si desea, puede consultar un perfil municipal resumido en nuestro
                        <a href='#' onclick="window.open('mapa_perfil.php','','top=0,left=0,width=800,height=600')">Sistema de información geogrráfica</a>
                    </p>
                    <p>&nbsp;</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <img src='images/home/icn_gra_resumen.png' class="pull-left" width="20" height="20">
                    <h2 class="panel-title">
                        Gráficas, resumenes y Reportes
                    </h2>
                </div>
                <div class="panel-body">
                    <div class="d2">
                        <h3>Gráficas y Resúmenes</h3>
                        <p align='justify'>
                            Haga sus propias gráficas estadísticas y tablas resúmenes, basadas en sus propios criterios de selección de información.
                        </p>
                        <p>
                            <a href="index.php?m_g=consulta&m_e=tabla_grafico&accion=consultar&class=TablaGrafico" class="">
                                &raquo; Ingrese aquí para generar gráficas y resumenes
                            </a>
                        </p>
                    </div>
                    <hr />
                    <div class="item_h d2">
                        <h3>Reportes</h3>
                        <p align='justify'>
                            &#191;Quiere un listado por tema, localizaciónn geográfica, demográfica o por rangos de tiempo?
                        </p>
                        <p>
                            <a href="index.php?m_g=consulta" class="">
                                &raquo; Ingrese aquí para generar reportes
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <img src='images/home/mapas.png' alt='Mapas Temáticos' class="pull-left" width="20" height="20">
                    <h2 class="panel-title">Mapas Temáticos</h2>
                </div>
                <div class="panel-body">
                    <p align='justify'>
                        El Sistema de Informaciónn Geográfica (GIS) de SIDI, permite generar mapas a nivel
                        municipal para los siguientes temas:
                        <br><br>
                            &nbsp;&nbsp;&nbsp;&raquo;&nbsp;Organizaciones<br>
                            &nbsp;&nbsp;&nbsp;&raquo;&nbsp;Datos Sectoriales<br>
                            &nbsp;&nbsp;&nbsp;&raquo;&nbsp;Eventos del Conflicto<br>
                            &nbsp;&nbsp;&nbsp;&raquo;&nbsp;Desplazamiento
                    </p>
                    <p>
                        <a href='#' onclick="window.open('mapa.php','','top=0,left=0,width=1024,height=700,scrolbars=1')" class="">
                            &raquo; Ingrese aquí para generar mapas
                        </a>
                        <br />&nbsp;
                    </p>
                </div>
            </div>
		</div>
    </div>
</div>
<div id="novedad">
	<div class="sugerencia"><a href="#" onclick="showSugerencias('mostrar',event)">Sugerencias</a></div>
    SIDI EN NUMEROS<br>
    <? echo $_SESSION['footer_n'] ?>
</div>
