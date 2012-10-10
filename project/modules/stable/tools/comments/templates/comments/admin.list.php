<?php _eTag ('beginblock', array ('title' => _i18n ('comments|comments.template.adminList.elementsList'), 'isFirst' => true)) ?>
<?php if (count ($ppo->elements) <= 0) { ?>
	<?php echo _i18n ('comments|comments.template.adminList.noElements') ?><br />
<?php } else { ?>
	<table class="CopixTable">
		<tr>
			<th>Auteur</th>
			<th>Commentaire</th>
			<th>Groupe</th>
			<th>Date</th>
			<th class="last" colspan="2"></th>
		</tr>
		<?php foreach ($ppo->elements as $element) { ?>
			<tr <?php _eTag ('trclass', array ('id' => 'elements', 'highlight' => ($element->getId () == $ppo->highlight))) ?>>
				<td><a href="<?php echo _url ('comments|comments|edit', array ('id' => $element->getId ())) ?>"><?php echo $element->getAuthor () ?></a></td>
				<td><?php echo substr ($element->getComment (), 0, 30) ?>...</td>
				<td><?php echo $element->getGroup ()->getCaption () ?></td>
				<td><?php echo $element->getDate () ?></td>
				<td class="action">
					<a href="<?php echo _url ('comments|comments|edit', array ('id' => $element->getId ())) ?>"><?php _eTag ('copixicon', array ('type' => 'update'))?></a>
				</td>
				<td class="action">
					<a href="<?php echo _url ('comments|comments|delete', array ('id' => $element->getId ())) ?>"
						><?php _eTag ('copixicon', array ('type' => 'delete')) ?>
					</a>
				</td>
			</tr>
		<?php } ?>
	</table>
	<br />
	<center><?php echo CopixPager::getHTML ($ppo->countElements, $ppo->countPerPage, _url ('comments|comments|', array ('page' => '__page__')), $ppo->page) ?></center>
<?php } ?>
<?php _eTag ('endblock') ?>

<br />
<?php _eTag ('back', array ('url' => 'admin||')) ?>