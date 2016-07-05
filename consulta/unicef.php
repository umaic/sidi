<html>
<head>
<link href="style/consulta_unicef.css" type="text/css" rel="stylesheet">
</head>
<body>
<div id="cont">
<?php 

if (!$_GET['caso'])  die;

// LIBRERIAS
include('../admin/lib/common/mysqldb.class.php');
include('../admin/lib/dao/factory.class.php');

// INICIALIZACION
$caso = $_GET['caso'];
$actividad_dao = FactoryDAO::factory('unicef_actividad_awp');
$a_ini = 2009;
$meses = array('','Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic');
?>
<table cellpadding="10" cellspacing="10" style="font-size:11px" width="100%" id="filtros">
    <tr>
        <td align="center" colspan="2">
            <table cellpadding="0" cellspacing="10" id="filtros_top">
                <tr>
                    <td id="proyectado_td" class='selected'><a href="#" onclick="changeProyEje('proy')">PROYECTADO</a></td>
                    <td id="ejecutado_td" class="unselected"><a href="#" onclick="changeProyEje('eje')">EJECUTADO</a></td>
                    <input type="hidden" id="proy_eje" value="proyectado">
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td valign="top">
            <table>
                <tr id="proyectado_fecha">
                    <td>
                        <fieldset style="width:300px">
                            <legend>FILTRO POR A&Ntilde;OS</legend>
                            <?php 
                                // Consulta el mayor año en el que existen actividades awp
                                $aaaa_fin = $actividad_dao->GetMaxAAAA();
                                for($i=$a_ini;$i<=$aaaa_fin;$i++){
                                    $checked = ($i == $aaaa_fin) ? ' checked ' : '';

                                    echo "<label><input type='radio' name='aaaa' value='$i' $checked>&nbsp;$i</label>"; 
                                }    
                                ?>
                        </fieldset>
                    </td>
                </tr>
                <tr id="ejecutado_fecha" style="display:none">
                    <td>
                        <fieldset>
                            <legend>FECHA INICIO</legend>
                                Entre
                                    <select id="ejecutado_fecha_inicio_dia_ini">
                                        <option value=''>D&iacute;a</option>
                                        <?php
                                        for ($i=1;$i<32;$i++)   echo "<option value='$i'>$i</option>";
                                        ?>
                                    </select>
                                    <select id="ejecutado_fecha_inicio_mes_ini">
                                        <option value=''>Mes</option>
                                        <?php
                                        for ($i=1;$i<13;$i++)   echo "<option value='$i'>$meses[$i]</option>";
                                        ?>
                                    </select>
                                    <select id="ejecutado_fecha_inicio_aaaa_ini">
                                        <option value=''>A&ntilde;o</option>
                                        <?php
                                        // Consulta el mayor año en el que existen convenios iniciados
                                        $aaaa_fin = $actividad_dao->GetMaxAAAA('ini');
                                        for ($i=$a_ini;$i<=$aaaa_fin;$i++)   echo "<option value='$i'>$i</option>";
                                        ?>
                                    </select>&nbsp;y&nbsp;
                                    <select id="ejecutado_fecha_inicio_dia_fin">
                                        <option value=''>D&iacute;a</option>
                                        <?php
                                        for ($i=1;$i<32;$i++)   echo "<option value='$i'>$i</option>";
                                        ?>
                                    </select>
                                    <select id="ejecutado_fecha_inicio_mes_fin">
                                        <option value=''>Mes</option>
                                        <?php
                                        for ($i=1;$i<13;$i++)   echo "<option value='$i'>$meses[$i]</option>";
                                        ?>
                                    </select>
                                    <select id="ejecutado_fecha_inicio_aaaa_fin">
                                        <option value=''>A&ntilde;o</option>
                                        <?php
                                        for ($i=$a_ini;$i<=$aaaa_fin;$i++)   echo "<option value='$i'>$i</option>";
                                        ?>
                                    </select>
                                </fieldset>
                                <fieldset>
                                <legend>FECHA FINALIZACI&Oacute;N</legend>
                                    Entre
                                    <select id="ejecutado_fecha_finalizacion_dia_ini">
                                        <option value=''>D&iacute;a</option>
                                        <?php
                                        for ($i=1;$i<32;$i++)   echo "<option value='$i'>$i</option>";
                                        ?>
                                    </select>
                                    <select id="ejecutado_fecha_finalizacion_mes_ini">
                                        <option value=''>Mes</option>
                                        <?php
                                        for ($i=1;$i<13;$i++)   echo "<option value='$i'>$meses[$i]</option>";
                                        ?>
                                    </select>
                                    <select id="ejecutado_fecha_finalizacion_aaaa_ini">
                                        <option value=''>A&ntilde;o</option>
                                        <?php
                                        // Consulta el mayor año en el que existen convenios iniciados
                                        $aaaa_fin = $actividad_dao->GetMaxAAAA('fin');
                                        for ($i=$a_ini;$i<=$aaaa_fin;$i++)   echo "<option value='$i'>$i</option>";
                                        ?>
                                    </select>&nbsp;y&nbsp;
                                    <select id="ejecutado_fecha_finalizacion_dia_fin">
                                        <option value=''>D&iacute;a</option>
                                        <?php
                                        for ($i=1;$i<32;$i++)   echo "<option value='$i'>$i</option>";
                                        ?>
                                    </select>
                                    <select id="ejecutado_fecha_finalizacion_mes_fin">
                                        <option value=''>Mes</option>
                                        <?php
                                        for ($i=1;$i<13;$i++)   echo "<option value='$i'>$meses[$i]</option>";
                                        ?>
                                    </select>
                                    <select id="ejecutado_fecha_finalizacion_aaaa_fin">
                                        <option value=''>A&ntilde;o</option>
                                        <?php
                                        for ($i=$a_ini;$i<=$aaaa_fin;$i++)   echo "<option value='$i'>$i</option>";
                                        ?>
                                    </select>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr><td>&nbsp;</td></tr>
    <tr>
        <td>
            <table width="100%">
                <tr>
                    <td width="50%" id="filtros_p">
                        <fieldset>
                            <legend>
                            <a href="#" id="comps_filtro_a" onclick="changeFiltroP('comps')" class="selected_top">COMPONENTES</a>&nbsp;&nbsp;&nbsp;
                            <a href="#" id="odm_filtro_a" onclick="changeFiltroP('odm')" class="unselected_top">ODM</a>&nbsp;&nbsp;&nbsp;
                            <a href="#" id="mtsp_filtro_a" onclick="changeFiltroP('mtsp')" class="unselected_top">MTSP-F. AREA</a>&nbsp;&nbsp;&nbsp;
                            <a href="#" id="mtsp_key_filtro_a" onclick="changeFiltroP('mtsp_key')" class="unselected_top">MTSP-KEY RESULT</a>
                            <input type="hidden" id="filtro" value='comps'>
                            </legend>
                            <div class="table_filtro">
                            <table class="selected" id="comps_filtro">
                                            <tr><td><input type="checkbox" name="id_comps" value="1" checked>&nbsp;Supervivencia y desarrollo infantil</td></tr>
                                            <tr><td><input type="checkbox" name="id_comps" value="2" checked>&nbsp;Educaci&oacute;n con Calidad, Desarrollo del Adolescente y Prevenci&oacute;n del VIH/SIDA</td></tr>
                                            <tr><td><input type="checkbox" name="id_comps" value="3" checked >&nbsp;Protecci&oacute;n y Acci&oacute;n Humanitaria</td></tr>
                                            <tr><td><input type="checkbox" name="id_comps" value="4" checked >&nbsp;Pol&iacute;ticas P&uacute;blicas Basadas en Evidencia </td></tr>
                            </table>
                            <table class="unselected" id="odm_filtro">
                                            <tr><td><input type="checkbox" name="id_odm" value="1" checked>&nbsp;Erradicar la pobreza extrema y el hambre</td></tr>
                                            <tr><td><input type="checkbox" name="id_odm" value="2" checked>&nbsp;Logar la Educaci&oacute;n Primaria Universal</td></tr>
                                            <tr><td><input type="checkbox" name="id_odm" value="3" checked>&nbsp;Promover la Igualdad Entre los Sexos y la Autonomia de la Mujer</td></tr>
                                            <tr><td><input type="checkbox" name="id_odm" value="4" checked>&nbsp;Reducir la Mortalidad Infantil</td></tr>
                                            <tr><td><input type="checkbox" name="id_odm" value="5" checked>&nbsp;Mejorar la Salud Materna</td></tr>
                                            <tr><td><input type="checkbox" name="id_odm" value="6" checked>&nbsp;Combatir el VIH/SIDA, Paludismo y Otras Enfermedades End&eacute;micas</td></tr>
                                            <tr><td><input type="checkbox" name="id_odm" value="7" checked>&nbsp;Garantizar la Sostenibilidad Ambiental</td></tr>
                                            <tr><td><input type="checkbox" name="id_odm" value="8" checked>&nbsp;Fomentar una Asociaci&oacute;n Mundial para el Desarrollo, con Metas para la Asistencia, el Comercio, el Buen Gobierno y el Alivio a la Deuda</td></tr>
                            </table>
                            <table class="unselected" id="mtsp_filtro">
                                            <tr><td><input type="checkbox" name="id_mtsp" value="1" checked>&nbsp;Focus Area 1</td></tr>
                                            <tr><td><input type="checkbox" name="id_mtsp" value="2" checked>&nbsp;Focus Area 2</td></tr>
                                            <tr><td><input type="checkbox" name="id_mtsp" value="3" checked>&nbsp;Focus Area 3</td></tr>
                                            <tr><td><input type="checkbox" name="id_mtsp" value="4" checked>&nbsp;Focus Area 4</td></tr>
                            </table>
                            <table class="unselected" id="mtsp_key_filtro">
                                            <tr><td><input type="checkbox" name="id_mtsp_key" value="1" checked>&nbsp;MTSP Key Result 1</td></tr>
                                            <tr><td><input type="checkbox" name="id_mtsp_key" value="2" checked>&nbsp;MTSP Key Result 2</td></tr>
                                            <tr><td><input type="checkbox" name="id_mtsp_key" value="3" checked>&nbsp;MTSP Key Result 3</td></tr>
                                            <tr><td><input type="checkbox" name="id_mtsp_key" value="4" checked>&nbsp;MTSP Key Result 4</td></tr>
                            </table>
                        </fieldset>
                    </td>
                    <?php 
                    // Filtro Socio
                    if ($caso == 'socio'){
                        $socio_dao = FactoryDAO::factory('unicef_socio');
                        ?>
                        <td width="50%" id="filtros_p">
                        <fieldset>
                        <legend>SOCIOS IMPLEMENTADORES</legend>
                        <div class="table_filtro">
                        <table cellspacing="0" cellpadding="0" width="95%">
                            <?php
                            echo '<tr><td align="center">';
                            $indice = $socio_dao->getLetrasIndice();
                            foreach($indice as $letra){
                                $class = ($letra == $indice[0]) ? 'inicial_selected' : 'inicial_unselected';
                                $id_a = "inicial_filtro_socio_$letra";
                                echo "<span id='$id_a' class='$class'><a href='#' onclick=\"indiceFiltro('$letra','socio','".implode(',',$indice)."');\">$letra</a></span>&nbsp; ";
                            }
                            
                            echo '</td></tr><tr><td>&nbsp;</td></tr><tr><td> ';
                                
                            foreach($indice as $letra){
                                $display = ($letra == $indice[0]) ? '' : 'none';
                                echo '<ul id="ul_filtro_socio_'.$letra.'"  style="display:'.$display.'">';
                                $socios = $socio_dao->GetAllArray("nombre LIKE '$letra%'");
                                foreach ($socios as $socio){
                                    echo "<li><label><input type='radio' id='id_socio' name='id_socio' value='$socio->id' />&nbsp;$socio->nombre</label></li>";
                                }
                                
                                echo '</ul>';
                            }
                            
                        echo '</td></tr></table></fieldset></td>';
                    }
                    // Filtro Donante
                    else if ($caso == 'donante'){
                        $donante_dao = FactoryDAO::factory('unicef_donante');
                        ?>
                        <td width="50%" id="filtros_p">
                        <fieldset>
                        <legend>DONANTES</legend>
                        <div class="table_filtro">
                        <table cellspacing="0" cellpadding="0" width="95%">
                            <?php
                        
                            echo '<tr><td align="center">';
                            $indice = $donante_dao->getLetrasIndice();
                            foreach($indice as $letra){
                                $class = ($letra == $indice[0]) ? 'inicial_selected' : 'inicial_unselected';
                                $id_a = "inicial_filtro_donante_$letra";
                                echo "<span id='$id_a' class='$class'><a href='#' id='$id_a' onclick=\"indiceFiltro('$letra','donante','".implode(',',$indice)."');\" >$letra</a></span>&nbsp;";
                            }
                            
                            echo '</td></tr><tr><td>&nbsp;</td></tr><tr><td> ';
                
                            foreach($indice as $letra){
                                $display = ($letra == $indice[0]) ? '' : 'none';
                                echo '<ul id="ul_filtro_donante_'.$letra.'"  style="display:'.$display.'">';
                                $donantes = $donante_dao->GetAllArray("nombre LIKE '$letra%'");
                                foreach ($donantes as $donante){
                                    echo "<li><label><input type='radio' id='id_donante' name='id_donante' value='$donante->id' />&nbsp;$donante->nombre</label></li>";
                                }
                                
                                echo '</ul>';
                            }
                            
                        echo '</td></tr></table></div></fieldset></td>';
                    }
                    ?>
                </td>

<?php
if ($caso == 'que'){
    ?>
    <td valign="bottom">
    <table cellpadding="10" cellspacing="10" id="que" border="0">
        <tr>
            <td><a href="#" onclick="mostrarReporte(0)" ><img src="images/unicef/boton_gen_rep.png" width="223" height="63" border="0"></a></td>
        </tr>
    </table>
    </td>
    <?php
}
else if ($caso == 'donde'){
    // mostrarMapa en /home_unicef.php
    ?>
    <td valign="bottom">
        <table cellspacing="5" id="donde" border="0">
            <tr>
                <td style="width:200px"><a href="#" onclick="mostrarReporte(1)" id="boton_mapa"><img src="images/unicef/boton_gen_mapa.png" width="223" height="63" border="0"></a></td>
                <td>
                    <table>
                        <th>MAPA A NIVEL</th>
                        <tr>
                            <td>
                                <input type="radio" id="mdgd_deptal" name="mdgd" value='deptal' checked>&nbsp;Departamental
                                <input type="radio" name="mdgd" value='mpal'>&nbsp;Municipal
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br />
        <table>
            <tr>
                <td>
                    <b>PERFILES DEPARTAMENTALES</b><br />
                    <?php
                    $deptos = array("Amazonas", "Antioquia", "Arauca", "Atlantico", "Bogota", "Bolivar", "Boyaca", "Caldas", "Caqueta",
                        "Casanare", "Cauca", "Cesar", "Choco", "Cordoba", "Cundinamarca", "Guainia", "Guaviare", "Huila",
                        "LaGuajira", "Magdalena", "Meta", "Narino", "NorteSantander", "Putumayo", "Quindio", "Risaralda",
                        "SanAndres", "Santander", "Sucre", "Tolima", "Valle", "Vaupes", "Vichada"
                        );
                        
                        foreach ($deptos as $d=>$depto){

                            $file = "$depto.pdf";
                            
                            if ($d > 0) echo " - ";
                            if ($depto == 'Narino') $depto = 'Nari&ntilde;o';

                            echo '<a href="unicef/perfiles/'.$file.'">'.$depto.'</a>&nbsp;';
                        }
                    ?>
                </td>
            </tr>
        </table>
    </td>
    <?php
    //include('unicef_mapserver_ajax.php');
}

else{
    ?>
    </tr><tr>
    <td colspan="2" align="center">
        <table cellpadding="10" cellspacing="10" id="que" border="0" align="center">
        <tr>
            <td align="center"><a href="#" onclick="mostrarReporte(0)" ><img src="images/unicef/boton_gen_rep.png" width="223" height="63" border="0"></a></td>
            <td style="width:200px"><a href="#" id="boton_mapa" onclick="mostrarReporte(1)" ><img src="images/unicef/boton_gen_mapa.png" width="223" height="63" border="0"></a></td>
            <td>
                <table>
                    <th>MAPA A NIVEL</th>
                    <tr>
                        <td>
                            <input type="radio" id="mdgd_deptal" name="mdgd" value='deptal' checked>&nbsp;Departamental
                            <input type="radio" name="mdgd" value='mpal'>&nbsp;Municipal
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    </td>
    <?php
}

?>
</table>
</div>
</body>
</html>
