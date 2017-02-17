<?php 
session_start();

if (!isset($_GET['accion'])) die;
else    $accion = $_GET['accion'];

$accion = '';
if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

// Librerias
include('../lib/libs_unicef_producto_cpap.php');

//INICIALIZACION DE VARIABLES
$producto_dao = New UnicefProductoCpapDAO();
$producto_vo = New UnicefProductoCpap();
$indicador_dao = new UnicefIndicadorDAO();
$periodo_dao = new UnicefPeriodoDAO();
$resultado_dao = new UnicefResultadoDAO();

$periodo = $periodo_dao->GetAllArray('activo=1');
$aaaa_ini = $periodo[0]->aaaa_ini;
$aaaa_fin = $periodo[0]->aaaa_fin;

$num_indicadores = 1;
$id = '';

// Se define en /admin/unicef_proyecto/get_nodes.php
$id_node_papa = $_SESSION['id_node_papa_click'];

$accion = '';
if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

$id_resultado = (isset($_GET["id_papa"])) ? $_GET["id_papa"] : 0;
$id_papa = $resultado_dao->GetFieldValue($id_resultado,'id_sub_componente');

if ($accion == 'insertar'){
    $codigo_papa = $resultado_dao->getFieldValue($id_resultado,'codigo');
    $num_hermanos = $producto_dao->numRecords("id_resultado = $id_resultado");
    $codigo = $codigo_papa."_".($num_hermanos + 1);
}

//Caso de Actualizacion
else if ($accion == 'actualizar'){
	$id = $_GET["id"];
    $producto_vo = $producto_dao->Get($id);
    $codigo = $producto_vo->codigo;
    
    if (count($producto_vo->id_indicador) > 0)
        $num_indicadores = count($producto_vo->id_indicador);
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link href='/sissh/style/consulta_unicef.css' rel='stylesheet' type='text/css' />
<link href='../style/tree-ext3.css' rel='stylesheet' type='text/css' />
<link href='../style/ext-all.css' rel='stylesheet' type='text/css' />
<script type="text/javascript" src="../js/general.js"></script>
<script type="text/javascript" src="../js/ext_3/tree-av.js"></script>
<script type="text/javascript" src="../js/ext_3/ext-base.js"></script>
<script type="text/javascript" src="../js/ext_3/ext-all.js"></script>
<script type="text/javascript" src="../js/ext_3/ext-lang-es.js"></script>
<style type="text/css">
body{
    background-color: #dfe8f6;
    font-size: 12px;
    text-align: left;
    margin: 0px;
}
.textfield_ind{
    background-color: #dfe8f6;
    border: 0;
    padding: 3px;
}
.new-tab{
    background-image: url(../../images/unicef/tabs.gif) !important;
}
}
</style>

<script type="text/javascript">

Ext.onReady(function(){

    var tabs = new Ext.TabPanel({
        renderTo:'tabs_cpd',
        resizeTabs:true, // turn on tab resizing
        minTabWidth: 115,
        tabWidth:135,
        enableTabScroll:true,
        width:800,
        height:850,
        defaults: {autoScroll:true},
        plain: true,
        activeTab: 0
    });

    // tab generation code
    var index = 0;
    while(index < <?php echo $num_indicadores ?>){
        addTab();
    }
    function addTab(){
        tabs.add({
            title: 'Indicador ' + (++index),
            iconCls: 'tabs',
            autoLoad: 'tab_body.php?i='+index+'&accion=<?=$accion?>&id=<?=$id?>',
            closable:true
        }).show();
    }

    new Ext.Button({
        text: 'Agregar Indicador',
        handler: addTab,
        iconCls:'new-tab'
    }).render(document.body, 'tabs_cpd');
});

</script>

</head>

<body>
<br />

<form method="POST" onsubmit="submitFormIframe(event,'<?=$id_node_papa?>');return false;">
	<table class="tabla_insertar">
        <?php
        if ($accion == 'actualizar' && !$producto_dao->checkForeignKeys($id)){
            ?>
            <tr><td align="right">
				<a class="boton_new" href='#' onclick="if(confirm('Est&aacute; seguro que desea borrar este Producto?')){borrarRegistroIframe('UnicefProductoCpapDAO','<?=$id?>','<?=$id_node_papa?>')}else{return false};"><img src='/sissh/images/unicef/delete.gif' border='0' title='Borrar' />&nbsp;Borrar este Producto CPAP</a>&nbsp;
            </td></tr>
        <? } ?>
		<tr>
			<td><label>Resultado CPD al que pertenece</label></td>
		</tr>
        <tr>
            <td>
                <select name="id_resultado" class="textfield" style="font-size:14px;width:800px;font-weight:normal;padding:0">
                    <?php $resultado_dao->ListarCombo('combo',$id_resultado,"id_sub_componente=$id_papa"); ?>
                </select>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
		<tr>
			<td><label>Producto CPAP</label></td>
		</tr>
		<tr>
			<td><textarea id="nombre" name="nombre" style="width:800px;height:70px" class="textfield"><?=$producto_vo->nombre;?></textarea></td>
		</tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td><label>C&oacute;digo del producto CPAP</td></tr>
        <tr><td><input type="text" id="codigo" name="codigo" class="textfield" style="width:400px" value="<?=$codigo?>" /></td> </tr> 
        <tr><td>&nbsp;</td></tr>
        <tr><td><label>Indicador del producto CPAP</td></tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td><div id="tabs_cpd"></div></td></tr>
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
			    <input type="hidden" name="id" value="<?=$producto_vo->id;?>" />
			    <input type="hidden" name="aaaa_ini" value="<?=$aaaa_ini;?>" />
			    <input type="hidden" name="aaaa_fin" value="<?=$aaaa_fin;?>" />
			    <input type="hidden" name="id_periodo" value="1" /> <!-- ID del quinquenio, esta fijo en id=1 -->
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('nombre,Nombre','');" />
			</td>
		</tr>
	</table>
