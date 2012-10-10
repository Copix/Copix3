<?php
if (!function_exists ('logLevelToCaption')){
	function logLevelToCaption ($level){
		switch ($level){
			case CopixLog::INFORMATION:
				return _i18n ('copix:log.INFORMATION');
			case CopixLog::NOTICE:
				return _i18n ('copix:log.NOTICE');
			case CopixLog::WARNING:
				return _i18n ('copix:log.WARNING');
			case CopixLog::ERROR:
				return _i18n ('copix:log.ERROR');
			case CopixLog::EXCEPTION:
				return _i18n ('copix:log.EXCEPTION');
			case CopixLog::FATAL_ERROR:
				return _i18n ('copix:log.FATAL_ERROR');
		}
		return '';		
	}
}
?>
<table class="CopixTable" width="100%">
<thead>
 <tr>
	<th><a
		href="<?php echo _url ('log|delete', array ('profile'=>$profil)); ?>">
		<img
		src="<?php echo _resource ('/img/tools/delete.png'); ?>"
		alt="<?php echo _i18n ('logs.action.emptyLog'); ?>" /></a>
	</th>
	<th colspan="4"><a
		href="<?php echo _url ('log|delete', array ('profile'=>$profil)); ?>">
		<?php echo _i18n ('logs.action.emptyLog'); ?>
		</a>
	</th>
 </tr>
 <tr>
	<th>&nbsp;</th>
	<th><?php echo _i18n ('logs.type'); ?></th>
	<th><?php echo _i18n ('logs.level'); ?></th>
	<th><?php echo _i18n ('logs.message'); ?></th>
	<th><?php echo _i18n ('logs.date'); ?></th>
 </tr>
</thead>
<tbody>
<?php
if (isset ($logs)){
	$alternate = false;
	foreach ($logs as $log){
		if ($alternate){
			echo '<tr class="alternate">';
		}else{
			echo '<tr>';
		}
		$alternate = !$alternate;
		$more = _i18n ('logs.line').' : '.$log->line.'<br />';
		$more .= _i18n ('logs.user').' : '.$log->user.'<br />';
		$more .= _i18n ('logs.file').' : '.$log->file.'<br />';
		$more .= _i18n ('logs.function').' : '.$log->functionname.'<br />';
		$more .= _i18n ('logs.class').' : '.$log->classname;
		echo '<td>', _tag ('popupinformation', array (), $more), '</td>';
		echo "<td>".$log->type."</td><td>".logLevelToCaption ($log->level)."</td>";
		echo '<td>'.$log->message."</td><td>".CopixDateTime::yyyymmddhhiissToDateTime ($log->date)."</td>";
		echo "</tr>";
	}
}
?>
<tr>
<td colspan="5">
<?php
$nbPage = CopixSession::get ('log|nbpage');
$numPage = CopixSession::get ('log|numpage');

for ($i = 1; $i <= $nbPage ; $i++) {
     if ($i != $numPage ) {
        echo ' <a href="'._url("admin|log|show", array("page"=>$i, 'profile'=>$profil)).'">'.$i.'</a> ';
     } else {
        echo '&nbsp;'.$i.'&nbsp;';
     }
}

?>
</td>
</tr>
</tbody>
</table>