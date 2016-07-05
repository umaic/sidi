<?
session_start();

//SEGURIDAD
if (count($_GET) == 0){
	if(!isset($_SESSION["id_usuario_s"])){
		header("Location: login_unicef.php?m_g=home");
	}
}

// LIBRERIAS
include('admin/lib/common/mysqldb.class.php');
include('admin/lib/dao/factory.class.php');

// INICIALIZACION
$proy_eje = $_GET['proy_eje'];
$filtros['id_filtro'] = $_GET['id_filtro'];
$unicef_dao = FactoryDAO::factory('unicef');

$filtros['filtro'] = $_GET['filtro'];
$filtros['id_filtro'] = $_GET['id_filtro'];

if ($proy_eje == 'proyectado'){
    $filtros['aaaa'] = $_GET['aaaa'];
    $nombre_archivo_xls = 'AWP_'.$filtros['aaaa'];
}
else{
    $filtros['fecha_inicio_ini'] = (isset($_GET['fecha_inicio_ini'])) ? $_GET['fecha_inicio_ini'] : '';
    $filtros['fecha_inicio_fin'] = (isset($_GET['fecha_inicio_fin'])) ? $_GET['fecha_inicio_fin'] : '';
    $filtros['fecha_finalizacion_ini'] = (isset($_GET['fecha_finalizacion_ini'])) ? $_GET['fecha_finalizacion_ini'] : '';
    $filtros['fecha_finalizacion_fin'] = (isset($_GET['fecha_finalizacion_fin'])) ? $_GET['fecha_finalizacion_fin'] : '';

    $nombre_archivo_xls = 'Convenios';
}
?>
<html>
<head>
<title></title>

<link href="style/consulta_unicef.css" rel="stylesheet" type="text/css" />
<link href='t/style/tree-ext3.css' rel='stylesheet' type='text/css' />
<link href='t/style/ext-all.css' rel='stylesheet' type='text/css' />
<script type="text/javascript" src="t/js/ext_3/ext-base.js"></script>
<script type="text/javascript" src="t/js/ext_3/ext-all.js"></script>
<script type="text/javascript" src="t/js/ext_3/ext-lang-es.js"></script>
<script type="text/javascript" src="t/js/ext_3/RowExpander.js"></script>
<script type="text/javascript">
function changeReporteTab(caso,tabs){
    tabs = tabs.split(',');
    var tab_id;
    for (var i=0;i<tabs.length;i++){
        tab_id = 'tab_' + tabs[i];
        if (caso == tabs[i]){
            document.getElementById(tab_id).className = 'selected_tab';
            document.getElementById(tab_id + '_tr').className = 'selected_tab_tr';
        }
        else{
            document.getElementById(tab_id).className = 'unselected_tab';
            document.getElementById(tab_id + '_tr').className = 'unselected_tab_tr';
        }
    }

}

// Grid Panel
Ext.onReady(function(){

    var store = new Ext.data.JsonStore({
        autoLoad : true,
        url: 'unicef_json.php?<?php echo $_SERVER['QUERY_STRING'] ?>',
        root: 'unicef',
        fields: [<?php foreach($unicef_dao->grid_columns[$proy_eje] as $c=>$col){ if ($c > 0)   echo ','; echo "'$col'"; } ?>]
    });
   
    /*
    var expander = new Ext.ux.grid.RowExpander({
        tpl : new Ext.Template('<td>{comp}</td>','<td>{sub_c}</td>')
    });
    */

    var grid = new Ext.grid.GridPanel({
        store: store,
        columns: [
           {header: "#", width: 30, sortable: false, locked:true, 
           renderer:function(v, p, r, rowIndex, i, ds){return (rowIndex + 1)}}, 
            <?php
            echo $unicef_dao->getColumnsGrid($proy_eje);
            ?>
        ],
        stripeRows: true,
        //autoExpandColumn: 'p_awp',
        height: 500,
        //autoWidth: true,
        //columnLines: true,
        loadMask: true,
        //title: 'Grid',
        renderTo: 'grid'
        //plugins: expander,
    });

    /*
    var viewport = new Ext.Viewport({
        layout : 'anchor',
        id : 'unicef-viewport',
        dafaults : {autoScroll: true},
        items : [grid ]
    });
    */
});
</script>
<style type=text/css>
        /* Multiline */
        .multilineColumn .x-grid3-cell-inner {
            white-space:  normal !important;
        }
    </style>
</head>
<body>
<div id="cont">
        <table cellpadding="0" cellspacing="0" id="reporte" width="100%">
            <tr><td class="link_xls"><img src="images/unicef/spacer.gif" width="400" height="1"><a href='unicef_export_data.php?case=xls_session&nombre_archivo=<?php echo $nombre_archivo_xls ?>' target='_blank'><img src="images/unicef/boton_download_xls.png" border="0"></a></td></tr>
            <tr><td>
                <table cellpadding="0" cellspacing="0" width="950">
                    <tr>
                        <?php 
                        foreach (explode(',',$filtros['id_filtro']) as $i => $id_f){
                            $class = ($i == 0) ? 'selected_tab' : 'unselected_tab';
//                            echo '<td id="tab_'.$id_f.'" class="'.$class.'" width="25%"><a href="#" onclick="changeReporteTab(\''.$id_f.'\',\''.$filtros['id_filtro'].'\')">'.strtoupper($this->filtro_titulo[$filtros['filtro']]).' '.$id_f.'</a><br /><span class="nota">'.$this->filtro_exp[$filtros['filtro']][$i].'</span></td>';
  //                          echo '<td style="width:50px;">&nbsp;</td>';
                        }
                        ?>
                    </tr>
                </table></td>
            </tr>
        </table>
<div id="grid"></div>
</div>
</body>
</html>
