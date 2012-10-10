<p><a href="<?php echo _url ('unittest|launch', array ('tests'=>'all', 'ajax'=>true)); ?>" />
<?php echo _i18n ('test.all'); ?></a></p>

<form action="<?php echo _url ('unittest|launch', array ('ajax'=>true)); ?>" method="post" />
<table class="CopixTable">
	<?php
	foreach ($ppo->arTests as $moduleName=>$possibleTests){
		echo '<tr><th><input type="checkbox" name="tests[]" value="'.$moduleName.'|" /><a href="'._url ('unittest|launch', array ('tests'=>array ($moduleName.'|'), 'ajax'=>true)).'" />Module '.$moduleName.'</a></th></tr>';
		foreach ($possibleTests as $idTest){
			echo '<tr '._tag ('cycle', array ('values'=>',class="alternate"')).'><td><input type="checkbox" name="tests[]" value="'.$idTest.'" /><a href="'._url ('unittest|launch', array ('tests'=>array ($idTest), 'ajax'=>true)).'" />'.$idTest.'</a></td></tr>';
		}
	}
	?>
</table>
 <input type="submit" value="<?php echo _i18n ('copix:common.buttons.test'); ?>" />
</form>