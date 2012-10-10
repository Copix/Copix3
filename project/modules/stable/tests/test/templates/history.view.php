<?php if ($ppo->showErrors == false) { ?>
<a href="?showfailuresonly=1"> <?php _etag('i18n', array ('key'=>'test.history.showerrorsonly')) ?> </a>
<?php } else { ?>
<a href="?showfailuresonly=0" }"> <?php _etag('i18n', array ('key'=>'test.history.showall')) ?> </a>
<?php } ?>
<br /><br />
<?php
if ($ppo->arData[0] == null) {
	echo _etag('i18n', array('key' => 'test.history.noresult'));
} else {
?>

<table class="CopixTable">
	<thead>
		<tr>
			<th> <?php _etag ('i18n', array('key'=>'test.history.id')) ?> </th>
			<th><?php _etag ('i18n', array('key'=>'test.history.caption')) ?> </th>
			<th> <?php _etag('i18n', array('key'=>'test.history.exception')) ?> </th>
			<th> <?php _etag('i18n', array('key'=>'test.history.connexionTime')) ?> </th>
			<th><?php _etag('i18n', array('key'=>'test.history.calcTime')) ?> </th>
			<th><?php _etag('i18n', array('key'=>'test.history.totalTime')) ?> </th>
			<th><div align="right"> <?php _etag('i18n', array('key'=>'test.history.result')) ?> </div></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($ppo->arData as $cle => $element) { ?> 
		<?php 
		if (isset($element->timing)) {
			$arTimings = explode ('|', $element->timing);
			if (!isset($arTimings[0], $arTimings[1], $arTimings[2])) {
				$arTimings[0] = '-';
				$arTimings[1] = '-';
				$arTimings[2] = '-';
			}
		}
		?>
		<tr>
			<td> <?php echo $element->id_test ?> </td>
			<td> <?php echo CopixDateTime::yyyymmddhhiissToText ($element->time_date); ?> </td>
			
			<td>
				<?php if ($element->exception == '' || $element->exception == 'NULL') {
					echo 'aucune erreur';
				} else {
					echo htmlspecialchars($element->exception);
				}
				?>
				
			 </td>			 
			 <td>
			 	<?php if(isset($arTimings[0])) {echo $arTimings[0];} ?>
			 </td>
			 <td>
			 	<?php if(isset($arTimings[1])) {echo $arTimings[1];} ?>
			 </td>
			 <td>
			 	<?php if(isset($arTimings[2])) {echo $arTimings[2];} ?>
			 </td>
			<td> 
			<div align="right">
					<?php 
					if ($element->result == true) {
						_etag('copixicon', array('type'=>'valid'));
					} else {
						_etag('copixicon', array('type'=>'delete'));
					}
					?>
				</div>
			  </td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<br />

<input type="button" style="width:100px" onclick=location.href='<?php echo _resourcePath('|admin') ?>' value="<?php _etag('i18n', array('key' => 'test.historyback')); ?>">
<?php } ?>