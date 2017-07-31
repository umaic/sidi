<?
//LIBRERIAS
include_once("consulta/lib/libs_p4w.php");

// Check cache
$sissh = FactoryDAO::factory('sissh');
$cache = true;

$si_proy = $_GET['si_proy'];
$_SESSION['si_proy'] = $si_proy;

$desarrollo = ( $si_proy == 'des') ? true : false ;

if ($desarrollo) {
    $cluster_label = 'Resultado UNDAF';
    $ejecutor_label = 'agencias';
    $cluster_case = 'r_undaf';
}
else {
    $cluster_label = 'Cluster/Sector';
    $ejecutor_label = 'ejecutores';
    $cluster_case = 's';
}

$path_file = $sissh->dir_cache_static.'4w/home_'.$si_proy.'.html';

unset($_SESSION['4w_f']);

//INICIALIZACION DE VARIABLES

?>
<script type="text/javascript" src="js/p4w/consulta.js"></script>
<script type="text/javascript" src="admin/js/p4w/url_parser.min.js"></script>
<script type="text/javascript" src="js/OpenLayers.min.js"></script>
<script type="text/javascript" src="js/LoadingPanel.js"></script>

<link href="style/openlayers/theme/default/style.min.css" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="style/bootstrap.min.css">
<link rel="stylesheet" href="style/font-awesome/css/font-awesome.min.css">

<script type="text/javascript">
$j(function() {
    $j(document)
    .ajaxStart(function(){ $j('#loading').show(); })
    .ajaxStop(function(){ $j('#loading').hide(); });

    si = '<?php echo $si_proy; ?>';

    var h = new Date();
    var y = h.getFullYear();
    si = '<?php echo $si_proy ?>';

    addEventos();
    addEventosGrupos();

    $j('#map').addClass('mapfull');

    initMap('f');
    $j('#map').hide();

    // --- Inicio aplicar filtro periodo inicial
    //addFiltertoList('Vigentes en: ' + y, 'Periodo', 'periodo', y);
    //changeTotales();

    <?php
    // Se incluye 4W desde sitio externo
    if (!empty($web_externa)) {
        if (!empty($_GET['c']) && !empty($_GET['id'])) {
            $c = $_GET['c'];
            $id = $_GET['id'];
            ?>
            addFiltertoList('', '', '<?php echo $c ?>', <?php echo $id ?>);
            <?php
        }
        ?>

        //var val = 'f_grupo_otros';
        //grupoProys(val, '4w_todos');

        <?php
        // Variable resumen que viene de api para incluir 4W en sitios externos
        if (isset($_GET['resumen']) && $_GET['resumen'] == 0 && !empty($_GET['x'])) { ?>
            $j('div#grupos').hide();
            $j('#map').show();
        <?php
        }

        // Que filtros mostrar
        if (isset($_GET['filtros'])) {
            $fs = explode(',', $_GET['filtros']);

            ?>
            $j('#filtros').find('div.g').hide();

            <?php

            foreach($fs as $f) {
                ?>
                $j('a[rel="<?php echo $f ?>"]').closest('div.g').show();
                <?php
            }
        }
        ?>

        $j('div#todo').show();
        $j('div#reportes, div#m_grupos, div#reportes, div#wiki').hide(); // Barra Gris con filtros aplicados + Icono reportes

        <?php
    }
    ?>

    //Tabs
    $j('#tabs').tabs();

});
</script>
<?php
if ($cache && $sissh->existsFileCache($path_file)) {
    echo $sissh->getFileCache($path_file);
}
else {
    $depto_dao = New DeptoDAO();
    $mun_dao = New MunicipioDAO();
    $org_dao = New OrganizacionDAO();
    $proy_dao = New P4wDAO();
    $tema_dao = New TemaDAO();
    $estado_dao = New EstadoProyectoDAO();
    $rs = $proy_dao->resumenMapa();
        $undaf = (in_array($_SESSION['id_tipo_usuario_s'], array(100)) || $_SESSION['undaf'] == 1 || (!empty($_SESSION['id_tipo_org']) && $_SESSION['id_tipo_org'] == 4)) ? true : false;

    $yyyy = date('Y');
    $yyyy_ant = $yyyy - 1;
    $yyyys = array($yyyy_ant, $yyyy);

    $clusters = array('Agua, saneamiento e higiene (WASH)','Seguridad Alimentaria y Nutrición (SAN)');
    $clusters_short = array('WASH','SAN');

    ?>

        <?php

        if ($desarrollo) {
            $title = 'Proyectos de Desarrollo y Paz en Colombia';
            $id_c = 4;
            $tsp = $tema_dao->GetAllArray("id_clasificacion = $id_c AND id_papa=0");
            foreach($tsp as $tp) {
                $ts = $tema_dao->GetAllArray("id_papa=".$tp->id);
                $_grps = array();
                $todos_id = array();
                $todos_nom = array();
                foreach($ts as $_t => $t) {
                    $_grps['r_'.$_t] =  array(
                                        'si' => $t->id,
                                        'h1' => $t->nombre,
                                        'div' => '',
                                        'filtro' => $t->nombre
                                    );

                    $todos_id[] = $t->id;
                    $todos_nom[] = $t->nombre;
                }

                $_grps['todos'] = array(
                            'si' => implode(',',$todos_id),
                            'h1' => 'TOTAL',
                            'div' => '&nbsp',
                            'filtro' => implode(',',$todos_nom)
                        );

                $_papas[$tp->nombre] = $_grps;
            }


        }
        else {
            $title = 'Proyectos humanitarios en Colombia';
                $_grps = array(
                    'ehp' => array(
                                'si' => 'ehp',
                                'h1' => 'EHP',
                                'div' => 'Equipo Humanitario de País',
                                'wiki' => array('Que es el Equipo Humanitario de Pa&iacute;s?','EHP')),
                    'otros' => array(
                                'si' => 'otros',
                                'h1' => 'No presentes en EHP',
                                'div' => '&nbsp'),
                    'todos' => array(
                                'si' => 'todos',
                                'h1' => 'Totales',
                                'div' => '&nbsp')
                            );
                $_papas[''] = $_grps;


        }
        // Solo para usuarios de SNU
        if ($undaf) {
            $_clg = 't';
            $_grps['undaf'] = array(
                        'si' => 'undaf',
                        'h2' => 'Proyectos<br /> UNDAF',
                        'h1' => 'snu',
                        'div' => 'Sistema Naciones Unidas');
        }

        $tls = array(
                     'pres' => 'Presupuesto <span class="nota">USD</span>',
                     'b' => 'Beneficiairos <span class="nota">Directos</span>',
                     'p' => 'Proyectos <span class="nota">En ejecuci&oacute;n</span>',
                     'o' => ucfirst($ejecutor_label),
                 );

        $icons = array(
                     'pres' => 'fund',
                     'b' => 'affected_population',
                     'p' => 'reporting',
                     'o' => 'house',
                 );

        ob_start();
        ?>
    <div id="grupos">
        <div id="p4w_header">
            <h1><?php echo $title ?></h1>
            <?php if ($desarrollo) { ?>
                Informaci&oacute;n de proyectos en ejecuci&oacute;n <?php echo $yyyy ?>
            <?php } ?>

        </div>
        <?php
        if ($desarrollo) {
            ?>
            <div id="total_des"><table width="900" align="center"><tr>
            <?php
            foreach ($icons as $i => $icon) {
                echo '<td>
                        <div class="pull-left">
                            <img src="images/ocha_icons/'.$icon.'.png" />
                        </div>
                        <div>
                            <h2>'.number_format($rs[$si_proy]['eje'][$i][1]).'</h2>
                        </div>
                        <div class="clearfix">
                        <h3>'.$tls[$i].'</h3>
                        </div>
                    </td>';
            }
            echo '<td width="200" align="right">
            <a id="des" class="ingresar btn btn-primary btn-sm" data-filtro="'.$si_proy.'">
                Ingresar por esta opci&oacute;n <i class="fa fa-arrow-circle-right"></i>
            </a></td>';

            ?>
            </tr>
            <tr><td colspan="5" align="center"><br /><small>* Los totales de Desarrollo y Paz, no corresponden a la suma de los valores de cada &aacute;rea,
                dado que 1 mismo proyecto puede pertenecer a las 2 &aacute;reas</small>
            </td></tr>
            </table>
            </div>
            <?php
        }
        ?>

        <div id="p4w_body" class="">
            <?php
            $block = '<div class="g pull-left"><table class="table table-condensed %s grupos_'.$si_proy.'">
                    <thead>
                    <tr><th colspan="2" align="center">%s</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-right">'.$yyyy_ant.'</td>
                            <td class="text-right"><b>'.$yyyy.'</b></td>
                        </tr>
                        %s
                        <tr><td colspan="2" algin="center">
                        <a id="%s" class="ingresar %s" data-filtro="%s">
                            Ingresar por esta opci&oacute;n <i class="fa fa-arrow-circle-right"></i>
                        </a>
                        <br />%s
                        </td>
                        </tr>
                    </tbody>
                        </table></div>';

            $fila = '<tr>
            <td class="text-right">%s</td>
            <td class="text-right"><b>%s</b></td>
            </tr>';

            foreach($_papas as $_papa => $_grps) {

                // Tabla iz. con iconos
                $html = '<div class="pull-left">
                                <table class="table table-condensed grupos_'.$si_proy.'">
                                <thead><tr><th>RESULTADO</th></tr></thead>
                                <tbody>
                                <tr><td></td></tr>';
                foreach ($icons as $i => $icon) {
                    $html .= '<tr><td><img src="images/ocha_icons/'.$icon.'.png" />'.$tls[$i].'</td></tr>';
                }
                $html .= '<tr><td>&nbsp;</td></tr></tbody></table></div>';

                //print_r($rs);
                //unset($todo);
                foreach($_grps as $_g => $_a) {
                    if ($_g != 'todos') {
                        $_gr = $_a['si'];
                        $_id = "grupo_$_g";

                        $wiki = '';
                        if (!empty($_a['wiki'])) {
                            $wiki  = '<a href="http://wiki.umaic.org/index.php/'.$_a['wiki'][1].'" target="_blank">'.$_a['wiki'][0].'</a>';
                        }

                        $valores = '';
                        $n = 0;
                        foreach($tls as $_t => $tl) {
                            $v1 = $rs[$_gr]['eje'][$_t][0];
                            $v2 = $rs[$_gr]['eje'][$_t][1];

                            if (isset($todo[$_t])) {
                                $todo[$_t][0] += $v1;
                                $todo[$_t][1] += $v2;
                            }
                            else {
                                $todo[$_t][0] = $v1;
                                $todo[$_t][1] = $v2;
                            }

                            $valores .= sprintf($fila,
                                number_format($v1),
                                number_format($v2)
                            );

                            // Check valores en cero
                            $n += $v1 + $v2;
                        }

                        // Oculta boton ingresar
                        $ing_css = ($n == 0) ? 'hide' : '';
                        $filtro = (empty($_a['filtro'])) ? '' : $_a['filtro'];
                        $html .= sprintf($block,'',$_a['h1'],
                                $valores,
                                $_gr,$ing_css,$filtro,$wiki);
                    }
                }

                if (!empty($_papa)) {
                    echo '<div class="papa">'.$_papa.'</div>';
                }

                // Bloque de todos
                $_g = 'todos';
                $_a = $_grps[$_g];
                $_gr = $_a['si'];
                $filtro = (empty($_a['filtro'])) ? '' : $_a['filtro'];
                $valores = '';
                $n = 0;

                foreach($tls as $_t => $tl) {

                    $v1 = ($si_proy == '4w') ? $todo[$_t][0] : $rs[$_gr]['eje'][$_t][0];
                    $v2 = ($si_proy == '4w') ? $todo[$_t][1] : $rs[$_gr]['eje'][$_t][1];

                    $valores .= sprintf($fila,
                        number_format($v1),
                        number_format($v2)
                    );

                    // Check valores en cero
                    $n += $v1 + $v2;
                }

                // Oculta boton ingresar
                $ing_css = ($n == 0) ? 'hide' : '';

                $html .= sprintf($block,'todos',$_a['h1'],
                        $valores,
                            $_gr,$ing_css,$filtro,'');

                echo "<div class='fila'><table id='fila_totales' align='center'><tr><td>$html<div class='clearfix'></div></td></tr></table>
                <table width='900' align='center'>";

                if ($desarrollo) {
                    echo "<tr><td>* Los totales de proyectos y $ejecutor_label por &aacute;rea no corresponden a la suma de cada resultado, dado que un
                    proyecto puede pertenecer a 1 o varios resultados
                    </td></tr>";
                }

                echo "</table></div>";

            } ?>
        </div>
    </div>
    <div id="todo">
        <div id="loading" class="alpha60">
            <img src="images/p4w/ajax-loader.png" />
        </div>
        <div id="filtros">
            <!--<div class="g t">filtros</div>-->
            <?php
            $fs = array(
                'ejecutora' => array('Ejecutor', 'f_o', 'Ejecutor'),
                'donante' => array('Donante', 'f_d', 'Donante'),
                'implementadora' => array('Implementador', 'f_i', 'Implementador'),
                'cluster' => array($cluster_label, 'f_s', $cluster_label),
                'estado' => array('Estado', 'f_e', 'Estado'),
                'periodo' => array('Periodo', 'f_p', 'Periodo'),
                'departamento' => array('Departamento', 'f_u', 'Departamento'),
                'municipio' => array('Municipio', 'f_u', 'Municipio'),
                'modalidad_asistencia' => array('Modalidad Asistencia', 'f_d', 'Modalidad Asistencia'),
                'mecanismo_entrega' => array('Mecanismo de Entrega', 'f_d', 'Mecanismo de Entrega'),
            );

            foreach($fs as $_f => $_a) {

                $cls = ($_f == 'depto') ? 'ubif' : '';
                echo '
                <div class="g">
                    <a href="#" class="ft" rel="'.$_f.'">
                        <img src="images/p4w/'.$_a[1].'.png">'.$_a[0].'
                    </a>
                    <div id="'.$_f.'" class="f '.$cls.'">
                        <div class="t">
                            <h1>'.$_a[2].'</h1>
                        </div>';

                        if ($_f == 'ejecutora' || $_f == 'implementadora' || $_f == 'donante') {
                            ?>
                            <div class="buscar center">
                                <input type="text" id="" class="buscar" onkeydown="filterList(event, '<?php echo $_f ?>')" placeholder="Buscar por nombre, sigla, tipo" />
                            </div>
                            <?php
                        }

                        if ($_f == 'periodo') { ?>
                            <div>&nbsp;<input type="radio" id="periodo_que_i" name="periodo_que" value="i" />&nbsp;<label for="periodo_que_i">Proyectos que <b>inicien</b> en:</label></div>
                            <div>&nbsp;<input type="radio" id="periodo_que_f" name="periodo_que" value="f" />&nbsp;<label for="periodo_que_f">Proyectos que <b>finalicen</b> en:</label></div>
                            <div>&nbsp;<input type="radio" id="periodo_que_v" name="periodo_que" value="v" checked />&nbsp;<label for="periodo_que_v">Proyectos con <b>vigencia</b> en:</label></div>
                            <div>&nbsp;&nbsp;&nbsp;<i>Vigente=Finalizaci&oacute;n igual o mayor al a&ntilde;o selecciondo)</i></div>
                            <div>&nbsp;</div>
                        <?php
                        }
                        ?>
                        <div class="c">
                            <?php
                            //echo $proy_dao->getFiltrosConsulta($_f);
                            ?>
                        </div>
                    </div>
                </div>
                    <?php
            }
            ?>
            <!-- Hidden para mantener depto como input para no cambiar js -->
            <input type="hidden" id="depto" name="depto" value="00" />

            <div class="r" id="reportes">
                <a href="#" class="ft" rel="reportes">
                    <img src="images/p4w/reportes.png">Reportes
                </a>
                <div class="f c">
                    <div class="tit_tipo"><i class="fa fa-calculator"></i> Reportes de conteo</div>
                    <div>
                        <div class="left col" id="rrow">
                            <div class="t">Filas</div>
                            <div class="fila" title='d'>Departamental</div>
                            <div class="fila" title='m'>Municipal</div>
                            <?php
                            echo "<div class='fila' title='$cluster_case'>$cluster_label</div>";

                            if ($desarrollo) { ?>
                                <div class="fila" title='a_undaf'>Area UNDAF</div>
                            <?php
                            }
                            ?>
                            <div class="fila" title='o'><?php echo ucfirst($ejecutor_label) ?></div>
                        </div>
                        <div class="left col" id="rcol">
                            <div class="t">Columnas</div>
                            <?php
                            echo "<div class='fila' title='$cluster_case'>$cluster_label</div>";
                            if ($desarrollo) { ?>
                                <div class="fila" title='a_undaf'>Area UNDAF</div>
                            <?php
                            }
                            ?>
                            <div class="fila" title='o'><?php echo ucfirst($ejecutor_label) ?></div>
                            <div class="fila" title='d'>Donante</div>
                        </div>
                        <div class="left col" id="rque">
                            <div class="t">Contar</div>
                            <div class="fila" title='p'>No. Proyectos</div>
                            <div class="fila" title='b'>No. Beneficiarios</div>
                            <div class="fila" title='pre'>Presupuesto</div>
                        </div>
                        <div class="clear"></div>
                        <div id="sub">
                            <!--<div class="ssheet left boton icon">Hoja C&aacute;lculo</div>-->
                            <div class="csv boton icon">Descargar CSV</div>
                        </div>
                    </div>
                    <div class="clear tit_tipo"><i class="fa fa-list"></i> Otros reportes</div>
                    <div><br />
                        &nbsp;&nbsp;&nbsp;<i class="fa fa-download"></i>
                        <a href="ajax_data.php?object=reporteXMesPresBenef4w">
                            Descargar reporte por MES de presupuesto y beneficiarios
                        </a>
                    </div>
                    <div class="clear nota"><hr>
                        &raquo;Para los reportes aplicaran los filtros de periodo y ubicaci&oacute;n seleccionado en el mapa
                        <br /><br />&raquo;Los reportes a nivel municipal toman un tiempo considerable
                    </div>
                </div>
            </div> <!-- Fin Reportes -->

            <?php
            if (strpos($si_proy, '4w') !== false) { ?>
            <div id="m_grupos" class="right">
                <select id="m_grupos_select">
                    <option value="ehp"> EHP</option>
                    <option value="otros">No EHP</option>
                    <option value="todos">Todos</option>
                </select>
                &nbsp;<a  id="applyFiltros" href="#" onclick="">Refrescar</a>
            </div>
            <?php
            }
            ?>

        </div> <!-- Fin Filtros -->
        <div class="clear"></div>
        <div id="titulo">
            <div class="inline tt">
                <h2></h2><span></span><h1></h1>
            </div>
        </div>
        <div class="clear"></div>

        <div class="clear"></div>

        <!-- Wiki -->
        <div id="wiki" class="right">
            <div class="left">
                <a href="https://wiki.umaic.org/wiki/4w" target="_blank">Informaci&oacute;n acerca de 4W</a>
            </div>
            <div class="left">
                &nbsp;|<a id="dashboard" href="#" onclick="$j('div#grupos').show();$j('#todo, #map').hide(); return false;">Dashboard</a>
            </div>
        </div>

        <div id="fleft" class="hide">
            <div id="mapas_o">
                <div class="save left hide"></div>
                <div class="mapa_t mapa_o"><h3>Impresi&oacute;n</h3></div>
                <div class="mapa_f mapa_o active"><h3>Consulta</h3></div>
            </div>
        </div>
        <div id="p4w">
            <div id="s_listap" class="mr right">
                <!--
                <div id="applyFiltros_div" class="">
                    <div id="applyFiltros" class="boton">
                        Refrescar
                    </div>
                </div>
                -->
                <div id="tabs">
                  <ul>
                    <li><a href="#resumen_div">Resumen</a></li>
                    <li><a href="#proys">Lista proyectos</a></li>
                  </ul>
                  <div id="resumen_div">
                      <?php if (!$desarrollo) { ?>
                      <div class="srp_off boton">
                          <a href="#" id="btn_srp">Solo proyectos que hacen parte del SRP</a>  <a href="https://wiki.umaic.org/wiki/Plan_de_Respuesta_Humanitaria" target="_blank"> [?]</a>
                      </div>
                      <?php } else {?>
                          <div class="inter_off boton">
                              <a href="#" id="btn_inter">Solo proyectos interagenciales</a>  <a href="https://wiki.umaic.org/wiki/Agencia_de_las_Naciones_Unidas" target="_blank"> [?]</a>
                          </div>
                      <?php }?>
                    <div id="resumen">
                        <div id="resumen_titulo"></div>
                        <div>
                            <div class="left nbenef">
                                <div class="left img"><img src="images/ocha_icons/affected_population_red.png"></div>
                                <div class="left">
                                    <h2 id="nbenef_h2"></h2>
                                    <h3>beneficiarios directos</h3>
                                </div>
                            </div>
                            <div class="left ">
                                <div class="left img"><img src="images/ocha_icons/house_red.png"></div>
                                <div class="left">
                                    <h2 id="norg_h2"></h2>
                                    <h3><?php echo $ejecutor_label ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div>
                            <div class="left npro npres">
                                <div class="left img"><img src="images/ocha_icons/reporting_red.png"></div>
                                <div class="left">
                                    <h2 id="npro_h2"></h2>
                                    <h3>proyectos</h3>
                                </div>
                            </div>
                            <div class="left norg">
                                <div class="left img"><img src="images/ocha_icons/house_red.png"></div>
                                <div class="left">
                                    <h2 id="nimp_h2"></h2>
                                    <h3>Implemen.</h3>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="title">
                            <h3>PRESUPUESTO USD</h3>
                            <h2 id="npres_total_h2"></h2>
                        </div>
                        <div id="npres_detalle">
                            <div class="left npres_detalle">
                                <div class="left">
                                    <h2 id="npres_h2"></h2>
                                    <h3>Internacional</h3>
                                </div>
                            </div>
                            <div class="left npres_detalle">
                                <div class="left">
                                    <h2 id="npres_gob_h2"></h2>
                                    <h3>Nacional</h3>
                                </div>
                            </div>
                            <div class="left npres_detalle">
                                <div class="left">
                                    <h2 id="npres_sin_donante_h2"></h2>
                                    <h3>Sin identificar</h3>
                                </div>
                            </div>
                        </div>
                        <div class="clear nota"><br />
                            * Una persona puede ser beneficiaria de varios proyectos <br />
                            * El presupuesto est&aacute; en dolares americanos, los presupuestos <br />
                            en pesos colombianos han sido convertidos a la TRM del a&ntilde;o <br />
                            * <a href="https://wiki.umaic.org/wiki/Sistema_de_Informaci%C3%B3n_4W#Algoritmo_de_distribuci.C3.B3n_del_presupuesto_por_beneficiarios_directos.2C_cluster.2C_departamento_y_a.C3.B1os" target="_blank">Como se calcula el presupuesto y beneficiarios?</a>
                        </div>
                      </div>
                      <div id="top_ejecutoras" class="top_cluster"></div>
                      <div id="top_cluster" class="top_cluster"></div>
                      <div id="top_donantes" class="top_cluster"></div>
                      <div id="top_deptos" class="top_cluster"></div>
                    </div>
                    <div id="proys" class="mr">
                        <div id="m">
                            <select id="proys_order">
                                <option value="desc">Recientes primero</option>
                                <option value="asc">Antiguos primero</option>
                            </select>
                            <!--<div id="todos" class="right">Limpiar Filtros</div>
                            <div class="right">&nbsp;</div> -->
                            <div id="proys_limit" class="right">Mostrar todos</div>
                        </div>
                        <div id="e">
                            <div id="btn_fpdf" class="pdf boton icon">
                                <a href="ajax_data.php?object=fichaProyectos4w" target="_blank">Ficha Nacional</a>
                            </div>
                            <div class="csv boton icon">Descargar proyectos</div>
                        </div>
                        <div class="nota">
                            La opci&oacute;n descargar proyectos generar&aacute; un excel con
                            los proyectos de la lista, si quiere descargar todos los proyectos, use primero la
                            opci&oacute;n Mostrar todos y luego descarguelos. Recuerde que el tiempo depende del n&uacute;mero de proyectos!</div>
                        <div id="c"><div class="text_center"><br /><br />No existen proyectos</div></div>
                        <div class="clear masp"><a class="boton">M&aacute;s Proyectos</a></div>
                    </div>
                </div> <!-- Fin Lista Proyectos -->
            </div> <!-- Fin Tabs -->
        </div> <!-- Fin Lista Derecha -->
    </div>
    <div id="map"></div>

    <?php
    $html = ob_get_contents();

    ob_flush();
    ob_end_clean();

    // Guarda cache
    $sissh->createFileCache($html, $path_file);

}
?>
