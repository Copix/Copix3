<?php
$isFirst = true;
foreach ($ppo->links as $groupId => $groupInfos) {
	_eTag ('beginblock', array ('title' => $groupInfos['caption'], 'isFirst' => $isFirst));
	?>
	<table class="CopixTableTable">
		<?php
		$isFirst = true;
		$createTr = true;
		foreach ($groupInfos['links'] as $index => $link) {
			if ($createTr) {
				echo ($isFirst) ? '<tr>' : '</tr><tr>';
			}
			$createTr = ($index > 1 && $index % 7 == 0);
			?>
			<td style="text-align: center; vertical-align: bottom; width: 100px">
				<a href="<?php echo $link->getURL () ?>">
					<?php if ($link->getBigIcon () != null) { ?>
						<img src="<?php echo $link->getBigIcon () ?>" alt="<?php echo $link->getCaption () ?>" />
						<br />
					<?php } ?>
					<?php echo $link->getShortCaption () ?>
				</a>
			</td>
		<?php } ?>
		</tr>
	</table>
	<?php
	_eTag ('endblock');
	$isFirst = false;
}