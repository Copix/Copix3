<?php
if (!function_exists("_getActionIcon")){
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
}

if (!$justTable){?>
<div class="cmsbloc" id="<?php echo $id;?>" style="display: <?php echo ($show) ? 'block' : 'none' ?>">
	<div class="cmsbloc_title" >
		<div id="handle<?php echo $id;?>" class="widgethandle">
			<img src="<?php echo _resource ($icon) ?>" alt="Actions" title="Actions" />
			<?php echo $title ?>
		</div>
		<div class="showdivDashboard" id="showdiv<?php echo $id;?>">
			<a href="#" id="<?php echo $id;?>options">
				<img src="<?php echo _resource ('img/tools/config.png'); ?>" title="Options des <?php echo strtolower($title); ?>" alt="Options des <?php echo strtolower($title); ?>" />
			</a>
			<?php 
				$content = "<table class='CopixVerticalTable'><tr><th>Afficher les ".strtolower($title)." à partir du dossier : </th>";
				$content .= "<td>".CopixZone::process("headingelementchooser", array('selectedIndex'=>$selectedHeading, 'inputElement'=>'heading'.$id.'option', 'id'=>'elementchooser'.$id.'options', 'identifiantFormulaire'=>$id.'options', 'linkOnHeading'=>true, "arTypes"=>array("heading")))."</td></tr></table>";
				$content .= "<div style='text-align:right'><button onclick='update".$id."();$(\"copixWindow".$id."Option\").fireEvent(\"close\");return false;' class='button' id='submit".$id."options'>Appliquer</button></div>";
				_etag ('copixwindow', array ('id'=>'copixWindow'.$id.'Option', 'clicker'=>$id.'options', 'title'=>"Options des ".strtolower($title)), $content); 
			?>	
			 | 
			<?php _eTag ('showdiv', array ('id' => 'dashboard'.$id, 'userpreference' => 'heading|dashboard|'.$id)) ?>
		</div>
	</div>
	<div style="display: <?php echo (CopixUserPreferences::get ('heading|dashboard|'.$id, true)) ? 'block' : 'none' ?>" class="cmsbloc_content" id="dashboard<?php echo $id;?>">
		<?php 
}
			if ($createLog){
		?>
		<center>
		Pour que les actions du CMS soient enregistrées, vous devez créer un profil de log.
		<br /><br />
		<?php _eTag ('button', array ('caption' => 'Créer le profil', 'img' => 'admin|img/icon/log.png', 'url' => 'heading|actionslogs|doCreateLog')) ?>
		</center>
		<?php 
	} else { ?>
		
		<table class="CopixTable ActionsLogs">
			<tr>
				<th colspan="2">Elément</th>
				<th style="width: 130px">Date</th>
				<?php if ($showMessage) { ?>
					<th colspan="3">Message</th>
				<?php } ?>
				<th class="last" style="width: 100px">Auteur</th>
			</tr>
	<?php
	$exPageId = null;
	$exPublicId = null;
	foreach ($logs as $index => $log) {
		$isDifferent = ($exPageId != $log->getExtra ('page_id') || $exPublicId != $log->getExtra ('public_id_hei'));
		$element = $log->getExtra ('element');
		$url = _url ('heading|element|', array ('heading' => $element->parent_heading_public_id_hei, 'selected' => array ($element->id_helt . '|' . $element->type_hei)));
		$trClass = _tag ('trclass');
		?>
		<tr <?php echo $trClass ?> style="cursor: pointer" id="tr_action_<?php echo $log->getPageId ().$id; ?>">
			<td style="width: 1px"><a href="<?php echo $url ?>"><img src="<?php echo _resource ($elementsTypes[$element->type_hei]['icon']) ?>" width="16px" height="16px" /></a></td>
			<td style="min-width: 100px"><a href="<?php echo $url ?>"><?php echo $element->caption_hei ?></a></td>
			<td><?php echo $log->getDate ('d/m/Y H:i:s') ?></td>
			<?php if ($showMessage) { ?>
				<td style="width: 1px"><?php echo _getActionIcon ($log->getExtra ('action_type'), $element) ?></td>
				<td colspan="2"><?php echo $log->getMessage () ?></td>
			<?php } ?>
			<td>
				<?php
				$users = array ();
				foreach ($log->getExtra ('user') as $user) {
					$users[] = $user[2];
				}
				echo implode (', ', $users);
				?>
			</td>
			
		</tr>
		<?php foreach ($log->getActions () as $action) { ?>
			<tr <?php echo $trClass ?> style="display: none" rel="tr_action_<?php echo $log->getPageId ().$id ?>">
				<td></td>
				<td></td>
				<td></td>
				<?php if ($showMessage) { ?>
					<td></td>
					<td style="width: 1px"><?php echo _getActionIcon ($action->getExtra ('action_type'), $action->getExtra ('element')); ?></td>
					<td><?php echo $action->getMessage (); ?></td>
				<?php } ?>
				<td></td>				
			</tr>
		<?php } ?>
	<?php 
	$pageId = $log->getPageId ().$id;
	$js = <<<JS
	$ ('tr_action_$pageId').addEvent ('click', function () {	
		var realTarget = this;
		$$ ('.ActionsLogs tr').each (function (pSubElement) {
			if (pSubElement.get ('rel') == realTarget.id) {
				pSubElement.setStyle ('display', (pSubElement.getStyle ('display') == 'none') ? '' : 'none');
			}
		});
	});
JS;
	CopixHTMLHeader::addJSDOMReadyCode ($js);
	
	} ?>
	</table>
	<?php if ($link != null) echo '<center>' . $link . '</center>' ?>
	<?php }?>
<?php if (!$justTable){?>
	</div>
</div>
<?php 
CopixHTMLHeader::addJSCode("
function update".$id."(){
	var ".$id."HeadingValue = $('heading".$id."option').value ? $('heading".$id."option').value : 0;
	new Request.HTML({
		url : '"._url('heading|dashboard|get'.ucfirst($id))."',
		update : 'dashboard".$id."'
	}).post({'heading':".$id."HeadingValue});
	Copix.savePreference ('heading|dashboard|heading".$id."option', ".$id."HeadingValue);
}
");
}?>