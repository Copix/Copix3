<table width="100%">
<?php
$nbMaxCol = $params['columns'];
$i = 0;
foreach ($rows as $row){
	if ($i == 0){
		echo "<tr>";
	}
	echo "<td>".$row."</td>";
	$i++;
	if ($i == $nbMaxCol){
		echo "</tr>";
		$i = 0;
	}
}
//on termine le tableau
if ($i > 0){
	for ( ;$i<$nbMaxCol;$i++){
		echo "<td>&nbsp;</td>";
	}
	echo "</tr>";
}
?>
</table>