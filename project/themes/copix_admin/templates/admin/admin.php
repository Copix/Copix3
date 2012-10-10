<div id="adminContent">
	<div class="adminColumn">
	<?php
		$nbGroups = count($ppo->links);
		$nb = 0;
		$nbColonne = 1;
		foreach ($ppo->links as $groupId => $groupInfos) {
			if($nb +1 > $nbGroups/2 && $nbColonne == 1){
				$nbColonne++;
				echo "</div><div class='adminColumn'>";
			}
			_eTag ('beginblock', array ('title' => $groupInfos['caption'], 'isFirst' => true, 'icon' => $groupInfos['icon'], 'id' => 'admin_' . $groupId));
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
			
			$nb++;
		}
	?>
	</div>
	<div class="clear"></div>
</div>

<?php _eTag ('copixtips', array ('tips' => $ppo->tips, 'warning' => $ppo->warning, 'titlei18n' => 'install.tips.title')); ?>