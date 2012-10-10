<?php
function _getActionIcon ($pAction, $pElement) {
	switch ($pAction) {
		case HeadingActionsService::ARCHIVE : $src = 'heading|img/actions/archive.png'; break;
		case HeadingActionsService::COMMENT_CHANGE : $src = 'heading|img/togglers/comments.png'; break;
		case HeadingActionsService::COPY : $src = 'heading|img/actions/copy.png'; break;

		case HeadingActionsService::CREDENTIAL_DELETE_GROUP :
		case HeadingActionsService::CREDENTIAL_INHERITED :
		case HeadingActionsService::CREDENTIAL_SAVE :
			$src = 'heading|img/togglers/credentials.png';
			break;

		case HeadingActionsService::DELETE : $src = 'heading|img/actions/delete.png'; break;
		case HeadingActionsService::INSERT : $src = 'heading|img/actions/draft_add.png'; break;

		case HeadingActionsService::MENU_CHANGE :
		case HeadingActionsService::MENU_INHERITED :
		case HeadingActionsService::MENU_NONE :
			$src = 'heading|img/togglers/menu.png';
			break;

		case HeadingActionsService::MOVE : $src = 'heading|img/actions/cut.png'; break;
		case HeadingActionsService::POSITION_CHANGE : $src = 'heading|img/actions/move_up_down.png'; break;
		case HeadingActionsService::PUBLISH : $src = 'heading|img/actions/publish.png'; break;
		case HeadingActionsService::TARGET_CHANGE : $src = 'heading|img/togglers/target.png'; break;
		case HeadingActionsService::THEME_CHANGE : $src = 'heading|img/togglers/theme.png'; break;
		case HeadingActionsService::UPDATE :
			$src = ($pElement->status_hei == HeadingElementStatus::PUBLISHED) ? 'heading|img/actions/publish_save.png': 'heading|img/actions/draft_save.png';
			break;
	
		case HeadingActionsService::URL_CHANGE :
		case HeadingActionsService::URL_INHERITED :
			$src = 'heading|img/togglers/url.png';
			break;

		case HeadingActionsService::VERSION : $src = 'heading|img/actions/draft_add.png'; break;
		
		default : $src = null;
	}
	if ($src != null) {
		return '<img src="' . _resource ($src) . '" />';
	}
}

function _getPopupExtras ($pLog) {
	$html = '<table class="CopixVerticalTable">';
	foreach ($pLog->getExtras () as $name => $value) {
		if (is_string ($value) || is_numeric ($value)) {
			$value = htmlentities ($value, ENT_COMPAT, 'UTF-8');
		} else {
			$value = htmlentities (var_export ($value, true), ENT_COMPAT, 'UTF-8');
		}
		$html .= '<tr ' . _tag ('trclass', array ('id' => $pLog->getProfile ())) . '><th>' . $name . '</th><td>' . $value . '</td></tr>';
	}
	$html .= '</table>';
	return _tag ('popupinformation', array ('img' => _resource ('img/tools/information.png'), 'handler' => 'clickdelay'), $html);
}
?>

<?php _eTag ('beginblock', array ('title' => 'Recherche', 'isFirst' => true)) ?>
<form action="<?php echo _url ('heading|actionslogs|') ?>" method="GET" id="logsSearch">
<table class="CopixVerticalTable">
	<tr <?php _eTag ('trclass') ?>>
		<th style="width: 15%">Rubrique</th>
		<td style="width: 35%">
			<?php echo CopixZone::process ('heading|headingelement/headingelementchooser', array ('inputElement' => 'hierarchy_hei', 'linkOnHeading' => true, 'arTypes' => array ('heading'), 'selectedIndex' => $ppo->search->hierarchy_hei)) ?>
		</td>
		<th style="width: 15%">Identifiant publique</th>
		<td style="width: 35%"><?php _eTag ('inputtext', array ('name' => 'public_id_hei', 'value' => $ppo->search->public_id_hei, 'size' => 7)) ?></td>
	</tr>
	<tr <?php _eTag ('trclass') ?>>
		<th>Auteur</th>
		<td><?php _eTag ('multipleselect', array ('name' => 'users', 'values' => $ppo->users, 'selected' => $ppo->search->users)) ?></td>
		<th>Période</th>
		<td>
			Du <?php _eTag ('calendar2', array ('name' => 'date_from', 'value' => $ppo->search->date_from)) ?>
			&nbsp;&nbsp;&nbsp;au <?php _eTag ('calendar2', array ('name' => 'date_to', 'value' => $ppo->search->date_to)) ?>
		</td>
	</tr>
	<tr <?php _eTag ('trclass') ?>>
		<th class="last">Types d'action</th>
		<td colspan="3"><?php _eTag ('multipleselect', array ('name' => 'types', 'values' => $ppo->types, 'selected' => $ppo->search->types, 'strict' => false)) ?></td>
	</tr>
</table>
</form>
<br />
<center>
	<?php
	// les boutons de soumission sont en dehors du formulaire pour ne pas avoir le bouton dans les paramètres GET
	_eTag ('button', array ('action' => 'search', 'submit' => 'logsSearch'));
	echo '&nbsp;&nbsp;&nbsp;';
	_eTag ('button', array ('caption' => 'Tout afficher', 'url' => _url ('heading|actionslogs|'), 'img' => 'heading|img/clear_search.png'));
	?>
</center>

<?php _eTag ('endblock') ?>

<?php
$min = ($ppo->page - 1) * $ppo->countPerPage + 1;
$max = min ($ppo->page * $ppo->countPerPage, $ppo->count);
if ($max == 0) {
	$title = 'Pas de résultat';
} else {
	$caption = ($max - $min) > 1 ? 'Résultats' : 'Résultat';
	$title = $caption . ' ' . $min . ' à ' . $max . ' sur ' . $ppo->count;
}
_eTag ('beginblock', array ('title' => 'Historique'));

$urlPager = _url ('heading|actionslogs|', array (
	'page' => '__page__',
	'public_id_hei' => $ppo->search->public_id_hei,
	'hierarchy_hei' => $ppo->search->hierarchy_hei,
	'date_from' => $ppo->search->date_from,
	'date_to' => $ppo->search->date_to,
	'types' => $ppo->search->types,
	'users' => $ppo->search->users
));
$pager = '<center>' . $title . '<br />' . CopixPager::getHTML ($ppo->count, $ppo->countPerPage, $urlPager, $ppo->page) . '</center>';
echo $pager;
?>
<table class="CopixTable ActionsLogs">
	<tr>
		<th colspan="2" style="width: 90px">Elément</th>
		<th colspan="3">Message</th>
		<th style="width: 100px">Auteur</th>
		<th style="width: 130px">Date</th>
		<th class="last"></th>
	</tr>
	<?php
	$exPageId = null;
	$exPublicId = null;
	foreach ($ppo->logs as $index => $log) {
		$isDifferent = ($exPageId != $log->getExtra ('page_id') || $exPublicId != $log->getExtra ('public_id_hei'));
		$element = $log->getExtra ('element');
		$url = _url ('heading|element|', array ('heading' => $element->parent_heading_public_id_hei, 'selected' => array ($element->id_helt . '|' . $element->type_hei)));
		$trClass = _tag ('trclass');
		?>
		<tr <?php echo $trClass ?> style="cursor: pointer" id="tr_action_<?php echo $log->getPageId () ?>">
			<td style="width: 1px"><a href="<?php echo $url ?>"><img src="<?php echo _resource ($ppo->elementsTypes[$element->type_hei]['icon']) ?>" width="16px" height="16px" /></a></td>
			<td style="width: 250px"><a href="<?php echo $url ?>"><?php echo $element->caption_hei ?></a></td>
			<td style="width: 1px"><?php echo _getActionIcon ($log->getExtra ('action_type'), $element) ?></td>
			<td colspan="2"><?php echo $log->getMessage () ?></td>
			<td>
				<?php
				$users = array ();
				foreach ($log->getExtra ('user') as $user) {
					$users[] = $user[2];
				}
				echo implode (', ', $users);
				?>
			</td>
			<td><?php echo $log->getDate ('d/m/Y H:i:s') ?></td>
			<td class="action"><?php echo _getPopupExtras ($log) ?></td>
		</tr>
		<?php foreach ($log->getActions () as $action) { ?>
			<tr <?php echo $trClass ?> style="display: none" rel="tr_action_<?php echo $log->getPageId () ?>">
				<td></td>
				<td></td>
				<td></td>
				<td style="width: 1px"><?php echo _getActionIcon ($action->getExtra ('action_type'), $action->getExtra ('element')); ?></td>
				<td><?php echo $action->getMessage (); ?></td>
				<td></td>
				<td></td>
				<td class="action"><?php echo _getPopupExtras ($action) ?></td>
			</tr>
		<?php } ?>
	<?php } ?>
</table>

<?php
echo $pager;

_eTag ('endblock');

foreach ($ppo->logs as $log) {
	$pageId = $log->getPageId ();
	$js = <<<JS
	$ ('tr_action_$pageId').addEvent ('click', function (pHeadElement) {
		var realTarget = this;
		$$ ('.ActionsLogs tr').each (function (pSubElement) {
			if (pSubElement.get ('rel') == realTarget.id) {
				pSubElement.setStyle ('display', (pSubElement.getStyle ('display') == 'none') ? '' : 'none');
			}
		});
	});
JS;
	CopixHTMLHeader::addJSDOMReadyCode ($js);
}

// pour ne pas avoir le champ input_users dans les paramètres GET
$js = <<<JS
$ ('logsSearch').addEvent ('submit', function () {
	$ ('input_users').disabled = true;
	$ ('input_types').disabled = true;
});
JS;
CopixHTMLHeader::addJSDOMReadyCode ($js);
?>