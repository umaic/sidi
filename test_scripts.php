<?

die(getenv('GDFONTPATH')."xxx");

$valores = array('85' => 3,'23' => 7 );

$formula = "([85] + 5) / ([23] * 3)";
$fin_formula = strlen($formula);

$formula_tmp = $formula;

$tags = 1;
while ($tags > 0){
	$id_dato = "";
	$ini = strpos($formula_tmp,'[');
	$fin = strpos($formula_tmp,']');
echo "ini=$ini - fin=$fin<br>";
	if ($ini && $fin ){
		$id_dato = substr($formula_tmp,$ini+1,$fin-1-$ini);
		echo "tmp=$formula_tmp<br>";
		echo "dato=$id_dato<br>";

		$formula_tmp = substr($formula_tmp,$fin+1,$fin_formula-$fin-1);

		$formula = str_replace("[$id_dato]",$valores[$id_dato],$formula);

		$tags = 1;
	}
	else{
		$tags = 0;
	}

}
eval("\$r = ".$formula.";");

echo $r;
?>