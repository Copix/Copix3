<?php
$isFirst = true;
foreach ($ppo->links as $groupId => $groupInfos) {
	?>
	<h2<?php if ($isFirst) { echo ' class="first"'; } ?>>
		<?php if ($groupInfos['icon'] != null) { ?>
			<img src="<?php echo $groupInfos['icon'] ?>" alt="" />
		<?php } ?>
		<?php echo $groupInfos['caption'] ?>
	</h2>

	<table class="CopixVerticalTable">
		<?php foreach ($groupInfos['links'] as $link) { ?>
			<tr <?php _eTag ('trclass') ?>>
				<td style="width: 16px">
					<?php if ($link->getIcon () != null) { ?>
						<a href="<?php echo $link->getURL () ?>" class="adminLink">
							<img src="<?php echo $link->getIcon () ?>" alt="<?php echo $link->getCaption () ?>" />
						</a>
					<?php } ?>
				</td>
				<td>
					<a href="<?php echo $link->getURL () ?>" class="adminLink"><?php echo $link->getCaption () ?></a>
				</td>
			</tr>
		<?php } ?>
	</table>
	<?php $isFirst = false ?>
<?php } ?>

<?php _eTag ('copixtips', array ('tips' => $ppo->tips, 'warning' => $ppo->warning, 'titlei18n' => 'install.tips.title')) ?>