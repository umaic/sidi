<link type="text/css" rel="stylesheet" href="../js/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css" media="screen"></link>
<script type="text/javascript" src="../js/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js"></script>

<!-- TinyMCE -->
<script type="text/javascript" src="js/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
	tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
		plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",

		// Theme options
		theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "css/content.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",

		// Style formats
		style_formats : [
			{title : 'Bold text', inline : 'b'},
			{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
			{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
			{title : 'Example 1', inline : 'span', classes : 'example1'},
			{title : 'Example 2', inline : 'span', classes : 'example2'},
			{title : 'Table styles'},
			{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
		],

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	});
</script>
<!-- /TinyMCE -->

<?php
//INICIALIZACION DE VARIABLES
$dao = New ReporteSemanalDAO();
$vo = New ReporteSemanal();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

$vo->f_ini = date('Y-m-d',strtotime('-1 week last monday'));
$vo->f_fin = date('Y-m-d',strtotime('last sunday'));
$vo->trend_f_ini = date('Y-m-d',strtotime('First monday january'));

//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
    $vo = $dao->Get($id);
}

?>

<form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
<table width="80%" align='center' cellspacing="1" cellpadding="5" border="0" class="tabla_consulta">
	<tr><td class="titulo_lista" align="center" colspan="2">Reporte Semanal</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
	  <td colspan='2'>
		Fecha Inicio:&nbsp;
		<input type="text" id="f_ini" name="f_ini" class="textfield" size="12" value="<?=$vo->f_ini?>">
		<a href="#" onclick="displayCalendar(document.getElementById('f_ini'),'yyyy-mm-dd',this);return false;"><img src="../images/calendar.png" border="0"></a>
		&nbsp;&nbsp;
		Fecha Fin:
		<input type="text" id="f_fin" name="f_fin" class="textfield" size="12" value="<?=$vo->f_fin?>">
		<a href="#" onclick="displayCalendar(document.getElementById('f_fin'),'yyyy-mm-dd',this);return false;"><img src="../images/calendar.png" border="0"></a>
		&nbsp;&nbsp;
		Fecha Inicio Tendencia:&nbsp;
		<input type="text" id="trend_f_ini" name="trend_f_ini" class="textfield" size="12" value="<?=$vo->trend_f_ini?>">
		<a href="#" onclick="displayCalendar(document.getElementById('trend_f_ini'),'yyyy-mm-dd',this);return false;"><img src="../images/calendar.png" border="0"></a>
	</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
        <td>Destacados en Espa&ntilde;ol <br /><textarea class="mceEditor" name="destacado_esp"><?php echo $vo->destacado_esp; ?></textarea></td>
        <td>Destacados en Ingl&eacute;s <br /><textarea class="mceEditor" name="destacado_ing"><?php echo $vo->destacado_ing; ?></textarea></td>
    </tr>	
	<tr>
        <td><b>
            Gr&aacute;fica de eventos por departamento usar: ###dep### (Barras)<br />
            Gr&aacute;fica de eventos por categor&iacute;a usar: ###cat### (Pie)<br />
            Gr&aacute;fica de tendencia usar: ###trend### (L&iacute;neas)
            </b>
        </td>
    </tr>
	<tr>
        <td>Contenido en Espa&ntilde;ol <br /><textarea class="mceEditor" name="contenido_esp" rows="30"><?php echo $vo->contenido_esp; ?></textarea></td>
        <td>Contenido en Ingl&eacute;s <br /><textarea class="mceEditor" name="contenido_ing" rows="30"><?php echo $vo->contenido_ing; ?></textarea></td>
    </tr>
    <tr>
        <td>
            <input type="hidden" name="id" value="<?=$id;?>" />
		    <input type="hidden" name="accion" value="<?=$accion?>" />
		    <input type="submit" name="submit" value="Generar" class="boton" />	
        </td>
    </tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>
			<?
			//SUBMIT FORM
			if (isset($_POST["consultar"])){
				$evento_dao->genRS();
			}
			?>
		</td>
	</tr>
</table>
</form>
