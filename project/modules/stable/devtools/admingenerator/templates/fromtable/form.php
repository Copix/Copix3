<form action="<?php echo _url ('admingenerator|fromtable|generate') ?>" method="POST" id="formGenerate">

<table style="width: 100%">
	<tr>
		<td style="width: 50%; padding-right: 5px; vertical-align: top">
			<?php _eTag ('beginblock', array ('title' => 'Général', 'isFirst' => true)) ?>
			<table class="CopixVerticalTable">
				<tr>
					<th style="width: 150px">Module <span class="required">*</span></th>
					<td><?php _eTag ('select', array ('name' => 'moduleName', 'values' => $ppo->modules, 'emptyShow' => false, 'selected' => CopixSession::get ('moduleName', 'generatorfromtable'))) ?></td>
				</tr>
				<tr <?php _eTag ('trclass', array ('id' => 'general')) ?>>
					<th>Chaine de droit</th>
					<td><?php _eTag ('inputtext', array ('name' => 'credentials', 'value' => CopixSession::get ('credentials', 'generatorfromtable', 'basic:admin'), 'style' => 'width: 99%')) ?></td>
				</tr>
				<tr <?php _eTag ('trclass', array ('id' => 'general')) ?>>
					<th>Préfixe <span class="required">*</span></th>
					<td><?php _eTag ('inputtext', array ('name' => 'prefix', 'value' => CopixSession::get ('prefix', 'generatorfromtable'), 'style' => 'width: 99%')) ?></td>
				</tr>
				<tr <?php _eTag ('trclass', array ('id' => 'general')) ?>>
					<th>Fil d'ariane <span class="required">*</span></th>
					<td><?php _eTag ('inputtext', array ('name' => 'breadcrumb', 'value' => CopixSession::get ('breadcrumb', 'generatorfromtable'), 'style' => 'width: 99%')) ?></td>
				</tr>
				<tr <?php _eTag ('trclass', array ('id' => 'general')) ?>>
					<th>Traduction</th>
					<td><?php _eTag ('radiobutton', array ('name' => 'i18n', 'values' => array ('none' => 'Aucune', 'fr' => 'fr', 'default' => 'default'), 'selected' => CopixSession::get ('i18n', 'generatorfromtable', 'none'), 'separator' => '&nbsp;&nbsp;')) ?></td>
				</tr>
				<tr <?php _eTag ('trclass', array ('id' => 'general')) ?>>
					<th>Moteur de recherche</th>
					<td><?php _eTag ('radiobutton', array ('name' => 'search', 'values' => array ('yes' => 'Oui', 'no' => 'Non'), 'selected' => CopixSession::get ('search', 'generatorfromtable', 'yes'), 'separator' => '&nbsp;&nbsp;')) ?></td>
				</tr>
				<tr <?php _eTag ('trclass', array ('id' => 'general')) ?>>
					<th class="last">Menus</th>
					<td><?php _eTag ('radiobutton', array ('name' => 'menus', 'values' => array ('yes' => 'Oui', 'no' => 'Non'), 'selected' => CopixSession::get ('menus', 'generatorfromtable', 'yes'), 'separator' => '&nbsp;&nbsp;')) ?></td>
				</tr>
				<tr <?php _eTag ('trclass', array ('id' => 'general')) ?>>
					<th class="last">Liens en boutons</th>
					<td><?php _eTag ('radiobutton', array ('name' => 'linksButtons', 'values' => array ('yes' => 'Oui', 'no' => 'Non'), 'selected' => CopixSession::get ('linksButtons', 'generatorfromtable', 'no'), 'separator' => '&nbsp;&nbsp;')) ?></td>
				</tr>
			</table>
			<?php _eTag ('endblock') ?>
		</td>
		<td style="padding-left: 5px">
			<?php _eTag ('beginblock', array ('title' => 'Fichiers', 'isFirst' => true)) ?>
			<table class="CopixTable">
				<tr>
					<th style="width: 90px" colspan="2">Type</th>
					<th>Nom <span class="required">*</span></th>
				</tr>
				<tr <?php _eTag ('trclass', array ('id' => 'classes')) ?>>
					<td><input type="checkbox" name="info_generate" id="info_generate" checked="checked" /></td>
					<td><label for="info_generate">Classe d'infos</label></td>
					<td><?php _eTag ('inputtext', array ('name' => 'info_class', 'value' => CopixSession::get ('info_class', 'generatorfromtable'), 'style' => 'width: 99%')) ?></td>
				</tr>
				<tr <?php _eTag ('trclass', array ('id' => 'classes')) ?>>
					<td style="width: 10px"><input type="checkbox" name="service_generate" id="service_generate" checked="checked" /></td>
					<td><label for="service_generate">Service</label></td>
					<td><?php _eTag ('inputtext', array ('name' => 'service_class', 'value' => CopixSession::get ('service_class', 'generatorfromtable', 'Service'), 'style' => 'width: 99%')) ?></td>
				</tr>
				<tr <?php _eTag ('trclass', array ('id' => 'classes')) ?>>
					<td><input type="checkbox" name="validator_generate" id="validator_generate" checked="checked" /></td>
					<td><label for="validator_generate">Validateur</label></td>
					<td><?php _eTag ('inputtext', array ('name' => 'validator_class', 'value' => CopixSession::get ('validator_class', 'generatorfromtable', 'Validator'), 'style' => 'width: 99%')) ?></td>
					<td></td>
				</tr>
				<tr <?php _eTag ('trclass', array ('id' => 'classes')) ?>>
					<td><input type="checkbox" name="exception_generate" id="exception_generate" checked="checked" /></td>
					<td><label for="exception_generate">Classe d'exception</label></td>
					<td><?php _eTag ('inputtext', array ('name' => 'exception_class', 'value' => CopixSession::get ('exception_class', 'generatorfromtable', 'Exception'), 'style' => 'width: 99%')) ?></td>
				</tr>
				<tr <?php _eTag ('trclass', array ('id' => 'classes')) ?>>
					<td><input type="checkbox" name="actiongroup_generate" id="actiongroup_generate" checked="checked" /></td>
					<td><label for="actiongroup_generate">Actiongroup d'admin</label></td>
					<td><?php _eTag ('inputtext', array ('name' => 'actiongroup_class', 'value' => CopixSession::get ('actiongroup_class', 'generatorfromtable', 'Admin'), 'style' => 'width: 99%')) ?></td>
				</tr>
				<tr <?php _eTag ('trclass', array ('id' => 'classes')) ?>>
					<td><input type="checkbox" name="search_generate" id="search_generate" checked="checked" /></td>
					<td><label for="search_generate">Classe de recherche</label></td>
					<td><?php _eTag ('inputtext', array ('name' => 'search_class', 'value' => CopixSession::get ('search_class', 'generatorfromtable', 'Search'), 'style' => 'width: 99%')) ?></td>
				</tr>
			</table>
			<?php _eTag ('endblock') ?>
		</td>
	</tr>
</table>

<table style="width: 100%">
	<tr>
		<td style="width: 50%; padding-right: 5px; vertical-align: top">
			<?php _eTag ('beginblock', array ('title' => 'Table')) ?>
			<table class="CopixVerticalTable">
				<tr>
					<th style="width: 120px">Spécifier profil <span class="required">*</span></th>
					<td><?php _eTag ('radiobutton', array ('name' => 'addDAOProfile', 'values' => array ('true' => 'Oui', 'false' => 'Non'), 'selected' => CopixSession::get ('addDAOProfile', 'generatorfromtable', 'false'))) ?></td>
				</tr>
				<tr>
					<th>Profil <span class="required">*</span></th>
					<td><?php _eTag ('select', array ('name' => 'profile', 'values' => $ppo->db_profiles, 'selected' => CopixSession::get ('profile', 'generatorfromtable', $ppo->db_defaultProfile), 'emptyShow' => false)) ?></td>
				</tr>
				<tr class="alternate">
					<th class="last">Table <span class="required">*</span></th>
					<td colspan="3"><div id="tables"></div></td>
				</tr>
			</table>
			<?php _eTag ('endblock') ?>
		</td>
		<td style="padding-left: 5px; vertical-align: top">
			<?php _eTag ('beginblock', array ('title' => 'Mots-clefs')) ?>
			<table class="CopixTable">
				<tr>
					<th style="width: 115px">Mot-clef</th>
					<th class="last">Traduction</th>
				</tr>
				<tr>
					<td>de l'élément <span class="required">*</span></td>
					<td><?php _eTag ('inputtext', array ('name' => 'de_l_element', 'value' => CopixSession::get ('de_l_element', 'generatorfromtable'), 'style' => 'width: 99%')) ?></td>
				</tr>
				<tr>
					<td>des éléments <span class="required">*</span></td>
					<td><?php _eTag ('inputtext', array ('name' => 'des_elements', 'value' => CopixSession::get ('des_elements', 'generatorfromtable'), 'style' => 'width: 99%')) ?></td>
				</tr>
				<tr>
					<td>d'un élément <span class="required">*</span></td>
					<td><?php _eTag ('inputtext', array ('name' => 'd_un_element', 'value' => CopixSession::get ('d_un_element', 'generatorfromtable'), 'style' => 'width: 99%')) ?></td>
				</tr>
				<tr>
					<td>un élément <span class="required">*</span></td>
					<td><?php _eTag ('inputtext', array ('name' => 'un_element', 'value' => CopixSession::get ('un_element', 'generatorfromtable'), 'style' => 'width: 99%')) ?></td>
				</tr>
				<tr>
					<td>l'élément <span class="required">*</span></td>
					<td><?php _eTag ('inputtext', array ('name' => 'l_element', 'value' => CopixSession::get ('l_element', 'generatorfromtable'), 'style' => 'width: 99%')) ?></td>
				</tr>
				<tr>
					<td>aucun élément <span class="required">*</span></td>
					<td><?php _eTag ('inputtext', array ('name' => 'aucun_element', 'value' => CopixSession::get ('aucun_element', 'generatorfromtable'), 'style' => 'width: 99%')) ?></td>
				</tr>
				<tr>
					<td>d'éléments <span class="required">*</span></td>
					<td><?php _eTag ('inputtext', array ('name' => 'd_elements', 'value' => CopixSession::get ('d_elements', 'generatorfromtable'), 'style' => 'width: 99%')) ?></td>
				</tr>
			</table>
			<?php _eTag ('endblock') ?>

		</td>
	</tr>
</table>

<?php _eTag ('beginblock', array ('title' => 'Champs')) ?>
<div id="fields"></div>
<?php _eTag ('endblock') ?>

</form>

<br />
<center>
	<?php _eTag ('button', array ('caption' => 'Générer', 'img' => 'admingenerator|img/admingenerator.png', 'type' => 'button', 'id' => 'generate')) ?>
</center>

<br />
<div id="generateResults"></div>

<?php _eTag ('back', array ('url' => 'admin||')) ?>

<script type="text/javascript">
$ ('profile').addEvent ('change', function () {
	new Request.HTML ({
		url : '<?php echo _url ('admingenerator|fromtable|getTables') ?>',
		evalScripts: true,
		update : $ ('tables')
	}).post ({'profile' : $ ('profile').value});
});

$ ('profile').fireEvent ('change');

$ ('generate').addEvent ('click', function () {
	Copix.setLoadingHTML ($ ('generateResults'));
	new Request.HTML ({
		url : '<?php echo _url ('admingenerator|fromtable|generate') ?>',
		update : $ ('generateResults')
	}).post ($ ('formGenerate'));
});

function onChangeType (pField, pType) {
	var property = $ ('field_' + pField + '_property');
	var method = $ ('field_' + pField + '_method');
	var caption = $ ('field_' + pField + '_caption');
	switch (pType) {
		case 'position':
			property.value = 'position';
			property.disabled = true;
			method.value = 'Position';
			method.disabled = true;
			caption.value = 'Position';
			break;
		case 'status':
			property.value = 'isEnabled';
			property.disabled = true;
			method.value = 'IsEnabled';
			method.disabled = true;
			caption.value = 'Statut';
			break;
		default:
			property.disabled = false;
			method.disabled = false;
			break;
	}
}
</script>

<?php
$js = <<<JS
new CopixFormObserver ('formGenerate', {
     	onChanged : function () {
        	new Request.HTML ({
				url : Copix.getActionURL ('admingenerator|fromtable|save'),
			}).post ($ ('formGenerate'));
      	},
     	checkIntervall :100
   	});
JS;
CopixHTMLHeader::addJSDOMReadyCode ($js);
_eTag ('mootools', array ('plugin' => array ('copixformobserver')));
