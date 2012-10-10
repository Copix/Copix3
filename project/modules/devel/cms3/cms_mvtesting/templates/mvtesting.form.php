<?php
$imgSrc = _resource('img/tools/help.png');
_eTag ('error', array ('message' => $ppo->errors));
?>

<?php _eTag ('beginblock', array ('title' => 'Informations générales', 'isFirst' => true)) ?>
<form action="<?php echo _url ('cms_mvtesting|admin|valid', array ('editId' => $ppo->editId)); ?>" method="POST" id="mvTestingForm">
<input type="hidden" name="publish" value="0" id="publish" />
<table class="CopixVerticalTable">
	<tr <?php _eTag ('trclass') ?>>
		<th style="width: 140px">Nom</th>
		<th class="help"></th>
		<td>
			<?php
			_eTag ('inputtext', array (
				'name' => 'caption_hei',
				'value' => $ppo->editedElement->caption_hei,
				'style' => 'width: 99%',
				'error' => isset ($ppo->errors['caption_hei'])
			))
			?>
		</td>
	</tr>
	<tr <?php _eTag ('trclass') ?>>
		<th>Visualisation suivante</th>
		<th class="help">
			<?php
			$message = 'Définit comment est choisit l\'élément à afficher :';
			$message .= '<ul><li><b>Aléatoire</b> : choix de l\'élément aléatoirement';
			$message .= '<li><b>Elément suivant</b> : l\'élément suivant le dernier visité sera affiché, tous seront vu le même nombre de fois';
			$message .= '<li><b>Pourcentage spécifique</b> : chaque élément définit quel pourcentage de chance il a d\'être affiché</ul>';
			_eTag ('popupinformation', array ('width' => '420', 'alt' => 'Aide', 'img' => $imgSrc), $message);
			?>
		</th>
		<td>
			<?php
			_eTag ('select', array (
				'name' => 'choice_mvt',
				'selected' => $ppo->editedElement->choice_mvt,
				'values' => _ioClass ('MVTestingServices')->getChoicesList (),
				'emptyShow' => false,
				'extra' => 'onchange="javascript: onChangeChoice (this.value)"',
				'error' => isset ($ppo->errors['choice_mvt'])
			));
			?>
		</td>
	</tr>
	<tr <?php _eTag ('trclass') ?>>
		<th class="last">Elément redondant</th>
		<th class="help last">
			<?php
			$message = 'Indique si pour un même visiteur, on veut conserver l\'élément visualisé :';
			$message .= '<ul><li><b>Non</b> : l\'affichage de l\'élément sera à chaque fois recalculé';
			$message .= '<li><b>Session</b> : l\'élément visualisé sera le même pour la session uniquement';
			$message .= '<li><b>Cookie</b> : l\'élément visualisé sera toujours le même</ul>';
			_eTag ('popupinformation', array ('width' => '420', 'alt' => 'Aide', 'img' => $imgSrc), $message);
			?>
		</th>
		<td>
			<?php
			_eTag ('select', array (
				'name' => 'conserve_mvt',
				'selected' => $ppo->editedElement->conserve_mvt,
				'values' => _ioClass ('MVTestingServices')->getConserveList (),
				'emptyShow' => false,
				'error' => isset ($ppo->errors['conserve_mvt'])
			));
			?>
		</td>
	</tr>
</table>
<?php _eTag ('endblock') ?>

<?php _eTag ('beginblock', array ('title' => 'Eléments à visualiser')) ?>
<table class="CopixTable">
	<tr>
		<th style="width: 20px"></th>
		<th style="width: 270px">
			Type
			<?php
			$message = 'Type de l\'élément à afficher :';
			$message .= '<ul><li><b>Elément du CMS</b> : affiche un élément créé dans le CMS (page, lien, etc)';
			$message .= '<li><b>Module Copix</b> : trigramme d\'une module Copix (exemple : module|group|action?param1=1&param2=2)</ul>';
			_eTag ('popupinformation', array ('width' => '450', 'alt' => 'Aide', 'img' => $imgSrc), $message);
			?>
			
		</th>
		<th>Elément</th>
		<th style="width: 60px">Affichage</th>
		<th class="last"></th>
	</tr>
	<?php
	foreach ($ppo->editedElement->elements as $index => $element) {
		$id = 'element' . $index . '_';
		?>
		<tr <?php _eTag ('trclass', array ('id' => 'mvtelements')) ?>>
			<td>#<?php echo ($index + 1) ?></td>
			<td>
				<input type="hidden" name="<?php echo $id ?>show" value="<?php echo $element->show_element ?>" />
				<?php
				_eTag ('radiobutton', array (
					'name' => $id . 'type',
					'separator' => '&nbsp;&nbsp;',
					'values' => _ioClass ('MVTestingServices')->getTypeList (),
					'selected' => $element->type_element,
					'extra' => 'onchange="javascript: onChangeType (this.value, ' . $index . ')"'
				))
				?>
			</td>
			<td>
				<div id="mvelement<?php echo $index ?>_cms" <?php echo ($element->type_element != MVTestingServices::TYPE_CMS) ? 'style="display: none"' : '' ?>>
					<?php
					echo CopixZone::process ('heading|headingelement/headingelementchooser', array (
						'selectedIndex' => ($element->type_element == MVTestingServices::TYPE_CMS) ? $element->value_element : null,
						'inputElement' => $id . 'cms',
						'linkOnHeading' => false,
						'showAnchor' => true,
						'arTypes' => array ('page')
					))
					?>
				</div>
				<div id="mvelement<?php echo $index ?>_module" <?php echo ($element->type_element != MVTestingServices::TYPE_MODULE) ? 'style="display: none"' : '' ?>>
					<?php
					_eTag ('inputtext', array (
						'name' => $id . 'module',
						'value' => ($element->type_element == MVTestingServices::TYPE_MODULE) ? $element->value_element : null,
						'style' => 'width: 99%',
						'error' => isset ($ppo->errors[$id . 'module'])
					))
					?>
				</div>
			</td>
			<td>
				<div class="mvtelement_random" <?php echo ($ppo->editedElement->choice_mvt != MVTestingServices::CHOICE_RANDOM) ? 'style="display: none"' : '' ?>>~ <?php echo $ppo->random ?> %</div>
				<div class="mvtelement_next" <?php echo ($ppo->editedElement->choice_mvt != MVTestingServices::CHOICE_NEXT) ? 'style="display: none"' : '' ?>><?php echo $ppo->random ?> %</div>
				<div class="mvtelement_percent" <?php echo ($ppo->editedElement->choice_mvt != MVTestingServices::CHOICE_PERCENT) ? 'style="display: none"' : '' ?>>
					<?php
					_eTag ('inputtext', array (
						'name' => $id . 'percent',
						'maxlength' => 2,
						'style' => 'width: 30px',
						'value' => $element->percent_element,
						'error' => isset ($ppo->errors[$id . 'percent']) || isset ($ppo->errors['elements_percents'])
					))
					?>
					%
				</div>
			</td>
			<td class="action">
				<img src="<?php echo _resource ('img/tools/delete.png') ?>" alt="Supprimer" style="cursor: pointer" onclick="javascript: deleteElement (<?php echo $index ?>)" />
			</td>
		</tr>
	<?php } ?>
</table>
<br />
<a href="#" onclick="javascript: addElement ()"><img src="<?php echo _resource ('img/tools/add.png') ?>" alt="Ajouter" /> Ajouter un élément</a>
<?php _eTag ('endblock') ?>
<?php echo CopixZone::process ('heading|headingelement/HeadingElementButtons', array ('form' => 'mvTestingForm', 'actions' => array ('savedraft', 'savepublish'))) ?>

</form>

<script type="text/javascript">
function onChangeChoice (pValue) {
	$$ ('.mvtelement_random').each (function (pElement) {
		pElement.setStyle ('display', (pValue == <?php echo MVTestingServices::CHOICE_RANDOM ?>) ? '' : 'none');
	})
	$$ ('.mvtelement_next').each (function (pElement) {
		pElement.setStyle ('display', (pValue == <?php echo MVTestingServices::CHOICE_NEXT ?>) ? '' : 'none');
	})
	$$ ('.mvtelement_percent').each (function (pElement) {
		pElement.setStyle ('display', (pValue == <?php echo MVTestingServices::CHOICE_PERCENT ?>) ? '' : 'none');
	})
}

function onChangeType (pValue, pIndex) {
	$ ('mvelement' + pIndex + '_cms').setStyle ('display', (pValue == <?php echo MVTestingServices::TYPE_CMS ?>) ? '' : 'none');
	$ ('mvelement' + pIndex + '_module').setStyle ('display', (pValue == <?php echo MVTestingServices::TYPE_MODULE ?>) ? '' : 'none');
}

function addElement () {
	$ ('mvTestingForm').action = '<?php echo _url ('cms_mvtesting|admin|AddElement', array ('editId' => $ppo->editId)) ?>';
	$ ('mvTestingForm').submit ();
}

function deleteElement (pIndex) {
	if (confirm ('Etes-vour sur de vouloir supprimer l\'élément ' + (pIndex + 1) + ' ?')) {
		$ ('mvTestingForm').action = Copix.getActionURL ('cms_mvtesting|admin|DeleteElement', {
			'editId': '<?php echo $ppo->editId ?>',
			'index': pIndex
		});
		$ ('mvTestingForm').submit ();
	}
}
</script>