<?php $leftGroups = array ('admin', 'auth', 'dbhandlers', 'devtools', 'soap_server'); ?>
<table style="width: 100%">
	<tr>
		<?php for ($x = 1; $x <= 2; $x++) { ?>
			<td style="width: 50%; vertical-align: top; <?php if ($x == 1) { echo 'padding-right: 10px;'; } else { echo 'padding-left: 10px;'; } ?>">
				<?php
				$isFirst = true;
				foreach ($ppo->links as $groupId => $groupInfos) {
					if (($x == 1 && in_array ($groupId, $leftGroups)) || ($x == 2 && !in_array ($groupId, $leftGroups))) {
						_eTag ('beginblock', array ('title' => $groupInfos['caption'], 'isFirst' => $isFirst, 'icon' => $groupInfos['icon'], 'id' => 'admin_' . $groupId));
						?>
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
						<?php
						_eTag ('endblock');
						$isFirst = false;
					}
				}
				?>
			</td>
		<?php } ?>
	</tr>
</table>

<?php _eTag ('copixtips', array ('tips' => $ppo->tips, 'warning' => $ppo->warning, 'titlei18n' => 'install.tips.title')); ?>
