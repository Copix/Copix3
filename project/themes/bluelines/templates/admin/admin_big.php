<?php
$groupsCount = array ();
foreach ($ppo->links as $id => $group) {
	$groupsCount[$id] = count ($group['links']);
}
asort ($groupsCount);
$groupsReverseCount = $groupsCount;
arsort ($groupsReverseCount);

$groups = array ();
$groupsUsed = array ();
$nbrIcons = (CopixMobile::isMobileAgent ()) ? 4 : 7;
foreach ($groupsReverseCount as $idReverse => $countReverse) {
	if (!in_array ($idReverse, $groupsUsed)) {
		$index = count ($groups);
		$groups[$index] = array ();
		$groups[$index][] = $ppo->links[$idReverse];
		$groupsUsed[] = $idReverse;
		$currentCount = $countReverse;
		foreach ($groupsReverseCount as $idNormal => $countNormal) {
			if (!in_array ($idNormal, $groupsUsed) && ($currentCount + $countNormal) <= $nbrIcons) {
				$groups[$index][] = $ppo->links[$idNormal];
				$groupsUsed[] = $idNormal;
				$currentCount += $countNormal;
			}
		}
	}
}
?>

<?php foreach ($groups as $indexGroup => $group) { ?>
	<table style="margin-left: auto; margin-right: auto">
		<tr>
			<?php foreach ($group as $index => $infos) { ?>
				<td <?php if ($index > 0) echo 'style="padding-left: 20px"' ?>>
					<?php _eTag ('beginblock', array ('title' => $infos['caption'], 'isFirst' => ($indexGroup == 0))); ?>
					<table style="margin-left: auto; margin-right: auto" class="AdminBig">
						<tr>
							<?php foreach ($infos['links'] as $index => $link) { ?>
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
					<?php _eTag ('endblock') ?>
				</td>
			<?php } ?>
		</tr>
	</table>
<?php } ?>