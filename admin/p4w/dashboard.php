<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" rel="stylesheet" />

<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>
<script type="text/javascript" src="js/p4w/url_parser.js"></script>
<script type="text/javascript" src="js/p4w/dashboard.js"></script>

<?php 
$formato_extra = ($si_proy == '4w') ? '' : 'UNDAF'; 
?>

<div id="alim_dashboard" class="container-fluid">
    <div class="row">
        <div id="sidebar" class="col-md-3">
            <h3>Dashboard 4W</h3>
            <ul id="menu">
                <li>
                    <i class="fa fa-plus-square"></i> <a href="?accion=insertar">Crear proyecto</a>
                </li>
                <li><i class="fa fa-file-excel-o"></i> <a href="../formato4w<?php echo $formato_extra ?>.xlsx">Excel alimentaci&oacute;n 4W</a></li>
                <!--<li><i class="fa fa-file-text"></i> <a href="docs/4w-ManualAlimentacion.pdf">Manual de alimentaci&oacute;n</a></li>-->
                <?php 
                if ($_SESSION['id_tipo_usuario_s'] == 1) { ?>
                    <li><i class="fa fa-download"></i> <a href="?accion=importar">Importar CSV</a></li>
                    <li><i class="fa fa-trash"></i> <a href="?m_e=p4w&accion=listar&class=P4wDAO&method=borrarMasivo&param=">Borrar masivo</a></li>
                <?php
                }
                ?>
            </ul>
            <h2>Buscar por:</h2>
            <ul>
                <li>
                    <input type="" id="buscar_codigo" class="form-control" value="" placeholder="C&oacute;digo" />
                </li>
                <li>
                    <select id="buscar_encargado" class="select"><option></option>
                        <?php 
                        foreach($encargados as $encargado) {
                            $sel = (!empty($_GET['encargado']) && $_GET['encargado'] == $encargado['id_org']) ? 'selected' : '';

                            echo '<option value="'.$encargado['id_org'].'" '.$sel.'>'.$encargado['nom_org'].'</option>';
                        } 
                        ?>
                    </select>
                </li>
                <li>
                    <select id="buscar_donante" class="select"><option></option>
                        <?php 
                        foreach($donantes as $donante) {
                            $sel = (!empty($_GET['donante']) && $_GET['donante'] == $donante['id_org']) ? 'selected' : '';

                            echo '<option value="'.$donante['id_org'].'" '.$sel.'>'.$donante['nom_org'].'</option>';
                        } 
                        ?>
                    </select>
                </li>
                <li>
                    <button type='button' id="buscar_btn" class="btn btn-primary" onclick='aplicarFiltro()'>Buscar ahora</button>
                </li>                
            </ul>
        </div>
        <div id="main" class="col-md-9">
            <div id="ot">
                <?php
                // Validador Cluster
                if (!empty($_SESSION['nom_tema'])) {
                    echo '<h1>Validador Cluster: '.$_SESSION['nom_tema'].'</h1>';
                }
                
                // Alimentador ORG
                else if (!empty($_SESSION['nom_org'])) {
                    echo '<h1>'.$_SESSION['nom_org'].'</h1>';
                }
                ?>
            </div>
            <h1 class="left"><?php echo $titulo; ?> [ <span id="ttp">50 </span> ]</h1>
            <?php 
            if ($si_proy == '4w') { ?>
            <div class="p4w right"><br />
               <i class="fa fa-check-circle"></i> <a href="?m_e=p4w&accion=listar&class=P4wDAO&method=Dashboard&t=1">Validados</a>: <?php echo $p4w['cr'] ?>&nbsp;&nbsp;&nbsp;&nbsp; 
               <?php
               if (empty($p4w['pc'])) {
                    echo '<img src="images/p4w/ok.png"/>&nbsp;Todos clasificados';
               }
               else {
                   ?>
                   <i class="fa fa-exclamation-triangle"></i> <a href="?m_e=p4w&accion=listar&class=P4wDAO&method=Dashboard&t=0">Por validar</a>: <?php echo $p4w['pc'] ?>
                <?php } ?>
                &nbsp;&nbsp;
                [ <?php echo $p4w['po'] ?>% ]
            </div>
            
            <?php
            }
            ?>
            <table class="table table-hover table-condensed">
                <?php 
                if (isset($proys[0])) {
                    if (count($proys) > 10) { ?>
                        <tr>
                            <th colspan="2">
                            </td>
                            <th>
                                <form class="form-inline">
                                <input type="text" id="filtro_nom" onkeydown="fOrgsDashboard(event, 'nom', '')" class="form-control" placeholder="ID, C&oacute;digo o Nombre" />
                               &nbsp;
                                <select id="order" onchange="fOrgsDashboard(event, 'order')" class="form-control">
                                    <option value=''>Ordenar por</option>
                                    <option value="creac_proy+desc">Nuevos primero</option>
                                    <option value="creac_proy+asc">Viejos primero</option>
                                    <option value="nom_proy+asc">A-Z</option>
                                    <option value="nom_proy+desc">Z-A</option>
                                </select>
                                &nbsp;
                                <button id="todos" class="btn btn-primary">Listar todos los proyectos</button>
                                </form>
                            </th>
                        </tr>
                    <?php
                    }
                    $sr = 0;
                    include('p4w/lista_proys.php');
                    ?>
                    <tr><td colspan="3" align="center"><a id="masp" class="boton cursor">M&aacute;s Proyectos</a></td></tr>
                    <?php
                }
                else {
                    echo '<tr><td>No existen proyectos</td></tr>';
                }
                ?>
            </table>
            <div id="scrollLoader"><img src="../images/p4w/loader_pac.gif" />&nbsp;Cargando.....</div>
        </div>

    </div>
</div>
