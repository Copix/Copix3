<?php
$urlPager = _url ('admin|log|show', array ('profile' => $ppo->profile, 'page' => '__page__'));
$pager =  CopixPager::getHTML ($ppo->count, $ppo->countPerPage, $urlPager, $ppo->page);
$delete = '<a href="' . _url ('admin|log|deleteContent', array ('profile' => $ppo->profile)) . '"><img src="' . _resource ('img/tools/delete.png') . '" /> Supprimer le contenu</a>';

if ($ppo->count > 0) {
	echo '<center>' . $delete . '<br />' . $pager . '</center>';
}

$pageId = null;
$sDate = null;
$alternate = null;
$isFirst = true;
foreach ($ppo->logs as $log) {
    if (($pageId != $log->getExtra ('page_id', 'undefined')) || (($pageId == 'undefined') && ($sDate != $log->getDate ('Y/m/d H:i:s')))) {
		$sDate = $log->getDate ('Y/m/d H:i:s');
        if (!$isFirst) {
			echo '</table>';
		}
		$requestUri = $log->getExtra ('request_uri');
		if ($requestUri != null) {
			preg_match ('%[^/]*.php.*%', $requestUri, $uri);
			$title = '[' . $log->getDate () . '] ' . (count ($uri) > 0  ? $uri[0] : $requestUri);
		} else {
			$title = '[' . $log->getDate () . ']';
		}
		?>
		<h2><?php echo $title ?></h2>
		<table class="CopixTable">
		<tr>
			<th style="width: 20px"></th>
			<th><?php echo _i18n ('logs.header.message') ?></th>
		</tr>
		<?php
		$isFirst = false;
	}
	$pageId = $log->getExtra ('page_id', 'undefined');
	$alternate = ($alternate == null) ? ' class="alternate"' : null;
	?>
	<tr<?php echo $alternate ?>>
		<td>
			<?php
			$content = '<table class="CopixVerticalTable">';
			$content .= '<tr><th>' . _i18n ('logs.header.level') . '</th><td>' . CopixLog::getLevel ($log->getLevel ()) . '</td></tr>';
			$alternateExtras = null;
			foreach ($log->getExtras () as $name => $value) {
				$alternateExtras = ($alternateExtras == null) ? 'class="alternate"' : null;
				if (is_string ($value) || is_numeric ($value)) {
					$value = htmlentities ($value, ENT_COMPAT, 'UTF-8');
				} else {
					$value = htmlentities (var_export ($value, true), ENT_COMPAT, 'UTF-8');
				}
				$content .= '<tr ' . $alternateExtras . '><th>' . $name . '</th><td>' . $value . '</td></tr>';
			}
			$content .= '</table>';
			_eTag ('popupinformation', array ('handler' => 'clickdelay'), $content);
			?>
		</td>
		<td><?php echo htmlentities ($log->getMessage (), ENT_COMPAT, 'UTF-8') ?></td>
	</tr>
	<?php
}

if ($ppo->count > 0) {
	echo '</table>';
}

if ($ppo->count > 0) {
	echo '<br /><center>' . $pager . '<br />' . $delete . '</center>';
}

echo '<br />';
_eTag ('back', array ('url' => 'admin|log|'));
?>