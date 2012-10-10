<?php echo CopixZone::process ('heading|headingelement/HeadingElementInformationTitle', array ('title' => 'Droits', 'icon' => _resource ('heading|img/togglers/credentials.png')));?>

<div class="element" style="height: auto;" id="elementCredentialsMenu">
	<div class="elementContent" id="elementCredentialsMenuContent">
		<?php if ($record->public_id_hei == 0) { ?>
			<input type="hidden" name="rights_inherited" id="rights_inherited" value="" />
		<?php } else { ?>
			<input type="checkbox" name="rights_inherited" id="rights_inherited" value="ok"
				<?php if ($record->credentials_inherited_hei && $record->public_id_hei != 0) { ?>checked="checked"<?php } ?>
				<?php if ($record->public_id_hei == 0 && $uniqueElement) { ?>disabled="disabled"<?php } ?>
			/>
			<label for="rights_inherited">Utiliser les droits de la rubrique parente</label>
			<br /><br />
		<?php } ?>
		<?php $indexRow = 0;?>
		<div id="divrights" style="display: <?php echo ($record->credentials_inherited_hei) ? 'none':'block';?>">
			
			<?php foreach ($groups as $groupHandler => $arGroups) { ?>
				<div class="titleHeadingMenu"><?php echo $groupHandler ?></div>
				<table class="CopixTable">
					<thead>
						<tr>
							<th style="width: 100px">Groupe</th>
							<th>Droit</th>
							<th class="last"></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($arGroups as  $stdclass) {	?>
							<tr <?php _eTag ('trclass', array ('id' => 'credentials')) ?> id="credentials<?php echo $stdclass->id_group;?>">
								<td><?php echo array_key_exists($stdclass->id_group, $arListGroups[$stdclass->group_handler]) ? $arListGroups[$stdclass->group_handler][$stdclass->id_group] : "Ce groupe n'existe plus"; ?></td>
								<td>
									<?php
									_eTag ('select', array (
										'extra' => 'class="rightSelect"',
										'emptyShow' => true,
										'emptyValues' => '-- Droits de la rubrique parente --',
										'name' => 'credentials['.$stdclass->group_handler.'][' . $stdclass->id_group . ']',
										'id' => 'rights' . $stdclass->id_group,
										'values' => $arRights,
										'selected' => $stdclass->right,
										'strict' => true
									));
									?>
								</td>
								<td>
								<a href="javascript:;" class="deleteCredential" onclick="window.deleteGroupCredential('<?php echo $stdclass->id_group;?>','<?php echo $stdclass->group_handler;?>', <?php echo $record->public_id_hei;?>); return false;"><img src="<?php echo _resource('img/tools/delete.png')?>" alt="Supprimer ce droit" title="Supprimer ce droit" /></a></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php } ?>
			<br />
			<a href="javascript:void (0);" onclick="window.addGroupCredential (); return false;" ><img src="<?php echo _resource ('img/tools/add.png') ?>" alt="Ajouter un droit" title="Ajouter un droit" /> Ajouter un droit</a>
			<?php 
				$configurationFile = new useConfigurationFile ('credential');
				$credentialConfig = $configurationFile->get();
				if(!isset($credentialConfig['heading|headingelementcredentialhandler'])){?>
					<p>
						Pour que les droits soient pris en compte, vous devez activer le gestionnaire de droits <strong>"heading|headingelementcredentialhandler"</strong>. 
						<?php if(_currentUser()->testCredential('basic:admin')){?>
							<br/>		
							Vous pouvez le faire depuis le lien suivant:
							<br/>
							<a href="<?php echo _url('heading|default|RegisterCredentialHandler')?>">Activer le gestionnaire de droits</a>
						<?php }?>
					</p>  					
				<?php }
				
			?>
			
			
		</div>
		<div id="divrightsparent" style="display: <?php echo ($record->credentials_inherited_hei) ? 'block':'none';?>">

			<?php foreach ($parentGroups as $groupHandler => $arGroups) { ?>
				<div class="titleHeadingMenu"><?php echo $groupHandler ?></div>
				<table class="CopixTable">
					<thead>
						<tr>
							<th style="width: 100px">Groupe</th>
							<th class="last">Droit</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($arGroups as  $stdclass) {	?>
							<tr <?php _eTag ('trclass', array ('id' => 'credentials')) ?> id="credentials<?php echo $stdclass->id_group;?>">
								<td><?php echo array_key_exists($stdclass->id_group, $arListGroups[$stdclass->group_handler]) ? $arListGroups[$stdclass->group_handler][$stdclass->id_group] : "Ce groupe n'existe plus"; ?></td>
								<td><?php echo $arRights[$stdclass->right] ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php } ?>
		</div>
	</div>
</div>

<?php

$urlNewGroup = _url ('heading|elementinformation|NewGroupSelect', array('width' => 84));
$urlNewCredential = _url ('heading|elementinformation|NewCredentialsSelect');
$urlDeleteCredential = _url ('heading|elementinformation|deleteCredential');

$js = <<<JS


window.addGroupCredential = function(){
	// IE ne supporte pas le .innerHTML sur un tr
	// solution retenue : une zone par td, et un appel ajax par zone
	var table = $$ ('#divrights table')[0];
	var row = table.insertRow (table.rows.length);
	var groupsCell = row.insertCell (0);
	var divGroups = new Element ('div');
	
	divGroups.injectInside (groupsCell);
	new Request.HTML ({
		url:'$urlNewGroup',
		update: divGroups
	}).post();
	
	var credentialsCell = row.insertCell (1);
	var divCredentials = new Element ('div');
	divCredentials.injectInside (credentialsCell);
	new Request.HTML ({
		url:'$urlNewCredential',
		update: divCredentials
	}).post();
	$('elementCredentialsMenu').setStyle('height', 'auto');
}

window.deleteGroupCredential = function(groupId,groupHandler, publicId) {
	if (confirm ('Etes-vous sur de vouloir supprimer ce droit ?')) {
		new Request ({
			url:'$urlDeleteCredential',
			data : 'group_id='+groupId+'&groupHandler='+groupHandler+'&public_id='+publicId,
			onComplete: function(){
				var table = $$ ('#divrights table')[0];
				for(var i = 0; i<table.rows.length; i++){
					var row = table.rows[i];
					if(row.id == 'credentials'+groupId){
						table.deleteRow(i);
						$('elementCredentialsMenu').setStyle('height', 'auto');
						break;
					}
				}
			}
		}).post();
		
	}
}

if ($ ('rights_inherited') != null) {
	$ ('rights_inherited').addEvent ('click', function () {
		$('elementCredentialsMenu').setStyle('height', 'auto');
		if($('rights_inherited').checked){
			$('divrights').setStyle('display','none');
			$('divrightsparent').setStyle('display','block');
		}else{
			$('divrights').setStyle('display','block');
			$('divrightsparent').setStyle('display','none');
		}
	});

}

$ ('rights_inherited').addEvent ('change', function (pEl) {
	$ ('elementCredentialsMenuContent').setStyle ('opacity', (pEl.target.checked) ? 0.6 : 1);
});
JS;
CopixHTMLHeader::addJSDOMReadyCode ($js);