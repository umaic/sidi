<?
include("consulta/lib/libs_org.php");

$org_dao = new OrganizacionDAO();
$id = 2440;
$color_papa = '0099ff';
$color = '0000ff';
$textcolor = '000000';
$papa = $org_dao->Get($id);
$ofis = $org_dao->GetAllArray('id_org_papa='.$id,'','');

$xml = '<?xml version="1.0"?>
	<graph title="Agencias SNU" bgcolor="ffffff" linecolor="cccccc" viewmode="display" width="725" height="400">
	<node id="n0" text="'.$papa->sig.'" color="'.$color_papa.'" textcolor="'.$textcolor.'"/>';

$o = 1;
foreach ($ofis as $ofi){
	$xml .= '<node id="n'.$o.'" text="'.$ofi->sig.'" link="t/ver.php?class=OrganizacionDAO&method=Ver&param='.$ofi->id.'" color="'.$color.'" textcolor="'.$textcolor.'"/>';
	$o++;
}

for($i=1;$i<$o;$i++){
	$xml .= '<edge sourceNode="n0" targetNode="n'.$i.'" label="" textcolor="555555"/>';
}

$xml .= '</graph>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Grafos SIDIH</title>
<script type="text/javascript" src="js/swfobject.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<link rel="stylesheet" type="text/css" href="style/consulta.css" />

<script language="JavaScript">
	var flashMovie;
	function init() {
		if (document.getElementById) {
		   flashMovie = document.getElementById("graphgear");
		}
		
		setTimeout("jsLiveXML()",500);
	}
	function jsLiveXML() {
		var xml = document.getElementById('liveXmlArea').value;
		flashMovie.liveXML(xml);
	}

	function updateGrafo(id_papa){
		getDataV1('','ajax_data.php?object=grafo&id='+id_papa,'liveXmlArea');
		setTimeout("jsLiveXML()",500);
	}

	function detalleOrg(url){
		alert(url);
	}
	window.onload = init;
</script>
</head>

<body>
	<div id="wrap">
		<!--<h3> Agencias SNU <span>Oficinas</span></h3>-->
		Agencia:&nbsp;<select class="select" onchange="if(this.value!=''){updateGrafo(this.value);}">
		<? $org_dao->ListarCombo('combo',2440,'id_tipo=4 AND id_org_papa=0'); ?>
		</select><br /><br />
		<div id="gearspace">
			<strong>You need to upgrade your Flash Player</strong>
		</div>
		<textarea id="liveXmlArea" style="display:none"><?=$xml?>
		</textarea>

		<script type="text/javascript">
			// <![CDATA[
		
			var so = new SWFObject("consulta/swf/GraphGear.swf", "graphgear", "725", "400", "8");
			//so.addVariable("graphXMLFile", "grafo.xml");
			so.addParam("allowScriptAccess", "always");
			//so.addParam("scale", "noborder");
			so.addParam("salign", "tc");
				      
			so.write("gearspace");
		
			// ]]>
		</script>
</div>
</body>
</html>
