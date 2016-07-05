<?php
foreach($proys as $p => $pr) {
    echo '<tr class="grid prj">
        <td>'.($sr+$p+1).'</td>
        <td class="sigla">
            <div class="ops">
            <a href="#" onclick="borrarP('.$pr->id_proy.'); return false;" class="del" title="Borrar"></a>
            </div>
            <div><a href="#" title="'.$pr->nom_org.'">'.$pr->sig_org.'</a></div>
            <div class="sig hide">'.$pr->sig_org.' - '.$pr->nom_org.'</div>
        </td>
        <td class="nom" id="'.$pr->id_proy.'">'.$pr->nom_proy.'<br />
            <div class="nota">ID: '.$pr->id_proy.', C&oacute;digo:'.((!empty($pr->cod_proy)) ? $pr->cod_proy : '---').', 
            Inicio: '.$pr->inicio_proy.', Fin: '.$pr->fin_proy.' | Creaci&oacute;n: '.$pr->creac_proy.' | Actualizaci&oacute;n: '.$pr->actua_proy.' | '.$pr->usuario.'</div>
        </td>
    </tr>';
}
?>
