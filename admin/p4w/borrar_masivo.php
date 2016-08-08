<script type="text/javascript" src="js/p4w/borrar_masivo.js"></script>
<div id="alim_importar" class="borrar_4w">    
    <div id="p1">
        <p>
            <h1>Borrar proyectos por ejecutor</h1>
            <br />Permite borrar proyectos de un ejecutor en los a&ntilde;os seleccionados
        </p>
        <hr>
        <h2>1. Seleccione la organizaci&oacute;n encargada </h2>
        <p>
            <select id="org_id" class="select">
                <option value="">---- Seleccione alguna ----</option>
                <?php 
                foreach($encargados as $encargado) {
                    echo '<option value="'.$encargado['id_org'].'">'.$encargado['nom_org'].'</option>';
                } 
                ?>
            </select>
        
        </p>
    </div>
    <div id="p2" class="hide ys">
        <h2>2. Seleccione los a&ntilde;os de los que desea borrar proyectos</h2>
        <ul id="p2_ul">
        </ul>
    </div>
    <div id="bottom" class="hide">
        <div class="left">
            <input type="button" id="submit" value="Borrar proyectos" class="boton" />
        </div>
        <div id="exito" class="left hide">
            Proyectos borrados con &eacute;xito.. :)
        </div>
        <div class="clear"></div>
    </div>
</div>
