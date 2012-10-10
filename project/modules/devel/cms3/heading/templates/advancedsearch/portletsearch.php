<?php
if(empty($ppo->arPage)){
	echo "Pas de résultats pour ce type de portlet";
} else { ?>
	<table class='CopixTable'>
		<tr>
			<th>Nom</th>
			<th>Url</th>
		</tr>
	<?php 
	foreach ($ppo->arPage as $page){
		echo "<tr "._tag('trclass').">";
		echo "<td>" . $page->caption_hei ."</td>";
		echo "<td><a href='" . _url('heading||', array('public_id'=>$page->public_id_hei))."'>" . _url('heading||', array('public_id'=>$page->public_id_hei))."</a></td>";
		echo "</tr>";
	}
	?>
	</table>
<?php } ?>