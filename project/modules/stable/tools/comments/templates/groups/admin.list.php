<?php _eTag ('beginblock', array ('title' => _i18n ('comments|commentsgroups.template.adminList.elementsList'), 'isFirst' => true)) ?>
<?php if (count ($ppo->elements) <= 0) { ?>
	<?php echo _i18n ('comments|commentsgroups.template.adminList.noElements') ?><br />
<?php } else { ?>
	<table class="CopixTable">
		<tr>
			<th><?php echo _i18n ('comments|commentsgroups.template.adminList.caption') ?></th>
			<th class="last" colspan="2"></th>
		</tr>
		<?php foreach ($ppo->elements as $element) { ?>
			<tr <?php _eTag ('trclass', array ('id' => 'elements', 'highlight' => ($element->getId () == $ppo->highlight))) ?>>
			<td><a href="<?php echo _url ('comments|groups|edit', array ('id' => $element->getId ())) ?>"><?php echo $element->getCaption () ?></a></td>
			<td class="action">
				<a href="<?php echo _url ('comments|groups|edit', array ('id' => $element->getId ())) ?>"><?php _eTag ('copixicon', array ('type' => 'update'))?></a>
			</td>
			<td class="action">
				<a href="<?php echo _url ('comments|groups|delete', array ('id' => $element->getId ())) ?>"
					><?php _eTag ('copixicon', array ('type' => 'delete')) ?>
				</a>
			</td>
			</tr>
		<?php } ?>
	</table>
<br />
<center><?php echo CopixPager::getHTML ($ppo->countElements, $ppo->countPerPage, _url ('comments|groups|', array ('page' => '__page__')), $ppo->page) ?></center>
<?php } ?>
<?php _eTag ('endblock') ?>

<br />
<table style="width: 100%">
	<tr>
		<td style="width: 50%">
			<a href="<?php echo _url ('comments|groups|edit') ?>"><?php _eTag ('copixicon', array ('type' => 'add')) ?> <?php echo _i18n ('comments|commentsgroups.template.adminList.addElement') ?></a>
		</td>
		<td><?php _eTag ('back', array ('url' => 'admin||')) ?></td>
	</tr>
</table>
