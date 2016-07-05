<?php 

include('admin/lib/dao/factory.class.php');

if (!isset($_GET['id_c']))   die('Falta el ID del componente');
else    $id_componente = $_GET['id_c'];

$a_ini = 2009;
$aaaa = $a_ini;
if (isset($_GET['aaaa'])){
    $aaaa = $_GET['aaaa'];
}

$_SESSION['aaaa'] = $aaaa;

$sub_componente_dao = FactoryDAO::factory('unicef_sub_componente');
$componente_dao = FactoryDAO::factory('unicef_componente');
$resultado_dao = FactoryDAO::factory('unicef_resultado');
$actividad_dao = FactoryDAO::factory('unicef_actividad_awp');
$conn = MysqlDb::getInstance();
$comp = $componente_dao->Get($id_componente);
?>


<script type="text/javascript">

//Retorna el valor de un parametro dado el url y el nombre del parametro
function getUrlParameter(url,parameter){
	url_t = url.split("&");

	for (var i=0;i<url_t.length;i++){

		par = url_t[i].split("=");

		if (par[0] == parameter)	return par[1];	
	}
}

Ext.onReady(function(){
    
    //Ext.QuickTips.init();
    var Tree = Ext.tree;
    var hiddenPkgs  = [];
    var markCount	= [];

    function filterTree(e,t,kb_tree){

        var t = Ext.getCmp('input_filter');
        var text = t.getValue();
        var kb_tree = Ext.getCmp('treePanel');

        Ext.each(hiddenPkgs, function(n){
            n.ui.show();
        });
        
        markCount  = [];	
        hiddenPkgs = [];

        if( text.trim().length > 0 ){
            kb_tree.expandAll();
            
            var re = new RegExp( Ext.escapeRe(text), 'i');
            kb_tree.root.cascade( function( n ){
                if( re.test(n.text) ){
                    markToRoot( n, kb_tree.root );
                }
            });
            
            // hide empty packages that weren't filtered		
            kb_tree.root.cascade(function(n){
                if( ( !markCount[n.id] || markCount[n.id] == 0 ) && n != kb_tree.root ){
                    n.ui.hide();
                    hiddenPkgs.push(n);
                }
            });
        }
    }

    function markToRoot( n, root ){
        
        if( markCount[n.id] )
            return;
            
        markCount[n.id] = true;
        
        if( n.parentNode != null )
            markToRoot( n.parentNode, root );
    }
    
    var btnExpandAll = new Ext.Toolbar.Button({
        iconCls : 'icon-expand-all',
        text    : 'Expander',
        tooltip : 'Expandir Todo',
        handler: function(){ 
            tree.root.expand(true);
        }
    });

    var btnCollapseAll = new Ext.Toolbar.Button({
        iconCls: 'icon-collapse-all',
        tooltip: 'Colapsar Todo',
        text: 'Contraer',
        handler: function(){ 
            tree.root.collapse(true); 
        }
    });
    
    var btnPrintVersion = new Ext.Toolbar.Button({
        iconCls: 'icon-print',
        tooltip: 'Versi&oacute;n para imprimir',
        text: 'Versi&oacute;n para impresi&oacute;n',
        handler: function(){ 
            window.open('<?php echo str_replace('alimentacion','a_print',$_SERVER["REQUEST_URI"]);  ?>');
        }
    });
    
    var input_text = new Ext.form.TextField({
        id        : 'input_filter',
        emptyText : 'Buscar...',
        width     : 200,
        listeners:{
            render: function(f){
                f.el.on('keydown', filterTree, f, {buffer: 350});
            }
        }
    });

    var tool_bar = new Ext.Toolbar({
        //cls:'top-toolbar',  
        items:[{text:'&nbsp;'},input_text,btnExpandAll,btnCollapseAll, btnPrintVersion]
    });

    var tree = new Tree.TreePanel({
        id              : 'treePanel',
        animate         : true,
        rootVisible     : false,
        tbar            : tool_bar, 
        dataUrl         : 't/unicef_proyecto/get_nodes.php',
        root            : new Tree.AsyncTreeNode({
            expanded: true,
            children : [
                <?php
                $subs = $sub_componente_dao->GetAllArray("id_componente=$comp->id",'','');
                $num_subs = $sub_componente_dao->numRecords("id_componente=$comp->id");
                foreach ($subs as $s=>$sub){
                    $sql = "select count(DISTINCT r.id_resultado) as num_r, count(DISTINCT p_cpap.id_producto) as num_p_cpap, count(DISTINCT a_awp.id_actividad) as num_a_awp, count(DISTINCT p_awp.id_producto) as num_p_awp, count(DISTINCT conv.id_convenio) as num_conv  from unicef_sub_componente sub left join unicef_resultado_cpd r using(id_sub_componente) left join unicef_producto_cpap p_cpap using(id_resultado) left join unicef_actividad_awp a_awp using(id_producto) left join unicef_producto_awp p_awp using(id_actividad) left join unicef_convenio conv on a_awp.id_actividad = conv.id_actividad WHERE id_sub_componente = $sub->id AND a_awp.aaaa = $aaaa";
                    $rs = $conn->OpenRecordset($sql);
                    $row = $conn->FetchObject($rs);
                    $resumen_conteo = "[<span style=\'color:#64cf4b\'>$row->num_r Result.</span>] [<span style=\'color:#392110\'>$row->num_p_cpap P. CPAP</span>] [<span style=\'color:#0066ff\'>$row->num_a_awp Activ.</span>] [<span style=\'color:#c7cd7b\'>$row->num_p_awp P. AWP</span>] [<span style=\'color:#d2a1de\'>$row->num_conv Conv.</span>]";
                    
                    $leaf = ($row->num_r == 0) ? 'true' : 'false';
                    echo "{text:'$sub->nombre ".$resumen_conteo."',id:'id=$sub->id&id_papa=1&accion=actualizar&m_e=unicef_sub_componente&caso=sub_comp',leaf:$leaf}";
                    if ($s < $num_subs-1)   echo ",";
                }
                ?>
            ]
        })
    });

    // Render
    tree.render('div_tree');
    
    // Onclick Event
    tree.on('click', function(node, event){
        
        var caso = getUrlParameter(node.id,'caso');
        if (caso != 'sub_comp'){
            var url = 'admin/index_parser_unicef.php?' + node.id;
            addWindowIUTree(url);
        }
    });    
});
</script> 

<div class="div_titulo_tree">
    <?php
    $aaaa_fin = $actividad_dao->GetMaxAAAA();
    echo "$comp->nombre&nbsp;";
    echo '<select id="aaaa" class="textfield" style="font-size: 24px" onchange="location.href=\'index_unicef.php?m_g=alimentacion&id_c='.$id_componente.'&aaaa=\'+this.value">';
    // Consulta el mayor año en el que existen actividades awp
    for($i=$a_ini;$i<=$aaaa_fin;$i++){
        echo "<option value='$i'";
        if ($aaaa == $i)    echo ' selected ';
        echo ">$i</option>";
    }

    echo "</select>";
    ?>
</div>
<div id='div_tree' class="div_tree"></div>
