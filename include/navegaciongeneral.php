<ul>
<?
/*
if ($_SESSION["cnrr"] == 1){
	?>
	<li><a href="login.php?m_g=consulta">Reportes</a></li>
	<li><a href="admin/login.php?m_g=alimentacion">Alimentación</a></li>
	<li><a href="admin/login.php?m_g=admin">Administración</a></li>
	<?
}
else{*/
	?>
	<li>&raquo;&nbsp;<a href="index.php?m_g=consulta&m_e=minificha&accion=generar&class=Minificha">Perfiles</a></li>
	<li>&raquo;&nbsp;<a href="index.php?m_g=consulta&m_e=tabla_grafico&accion=consultar&class=TablaGrafico">Gráficas y Resumenes</a></li>
	<li>&raquo;&nbsp;<a href="index.php?m_g=consulta">Reportes</a></li>
	<li>&raquo;&nbsp;<a href="#" onclick="window.open('mapa.php','','top=0,left=0,width=1024,height=600,scrolbars=1')">Mapas Temáticos</a></li>
	<? if (in_array(7,$perfil->id_modulo_papa)){ ?>
		<li>&raquo;&nbsp;<a href="admin/index.php?m_g=alimentacion">Alimentación</a></li>
	<? } ?>
	<? if (in_array(1,$perfil->id_modulo_papa)){ ?>
		<li>&raquo;&nbsp;<a href="admin/index.php?m_g=admin">Administración</a></li>
	<? } ?>
	<li>|</li>
	<li>&raquo;&nbsp;<a href="index.php?m_g=consulta&m_e=home">Inicio</a></li>
	<li>&raquo;&nbsp;<a href="admin/index.php?accion=logout">Cerrar sesión</a></li>
	<?
//}
?>
</ul>
