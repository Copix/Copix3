<?php _eTag ('beginblock', array ('title' => _i18n ('mysvn|repositories.template.adminList.elementsList'), 'isFirst' => true)) ?>
<?php if (count ($ppo->elements) <= 0) { ?>
	<?php echo _i18n ('mysvn|repositories.template.adminList.noElements') ?><br />
<?php } else { ?>
	<table class="CopixTable">
		<tr>
			<th style="width: 250px"><?php echo _i18n ('mysvn|repositories.template.adminList.caption') ?></th>
			<th>Adresse</th>
			<th class="last" colspan="3"></th>
		</tr>
		<?php foreach ($ppo->elements as $element) { ?>
			<tr <?php _eTag ('trclass', array ('id' => 'elements', 'highlight' => ($element->getId () == $ppo->highlight))) ?>>
			<td><a href="<?php echo _url ('mysvn|repositories|edit', array ('id' => $element->getId ())) ?>"><?php echo $element->getCaption () ?></a></td>
			<td><a href="<?php echo $element->getURL () ?>" target="_blank"><?php echo $element->getURL () ?></a></td>
			<td class="action">
				<a href="<?php echo _url ('mysvn|repositories|edit', array ('id' => $element->getId ())) ?>"><?php _eTag ('copixicon', array ('type' => 'update'))?></a>
			</td>
			<td class="action">
				<a href="<?php echo _url ('mysvn|commits|', array ('repository' => $element->getId ())) ?>"><img src="<?php echo _resource ('|img/commit.png') ?>" alt="Commits" title="Commits" /></a>
			</td>
			<td class="action">
				<a href="<?php echo _url ('mysvn|repositories|delete', array ('id' => $element->getId ())) ?>"
					><?php _eTag ('copixicon', array ('type' => 'delete')) ?>
				</a>
			</td>
			</tr>
		<?php } ?>
	</table>
<br />
<center><?php echo CopixPager::getHTML ($ppo->countElements, $ppo->countPerPage, _url ('mysvn|repositories|', array ('page' => '__page__')), $ppo->page) ?></center>
<?php } ?>
<?php _eTag ('endblock') ?>

<br />
<table style="width: 100%">
	<tr>
		<td style="width: 50%">
			<a href="<?php echo _url ('mysvn|repositories|edit') ?>"><?php _eTag ('copixicon', array ('type' => 'add')) ?> <?php echo _i18n ('mysvn|repositories.template.adminList.addElement') ?></a>
		</td>
		<td><?php _eTag ('back', array ('url' => 'admin||')) ?></td>
	</tr>
</table>
