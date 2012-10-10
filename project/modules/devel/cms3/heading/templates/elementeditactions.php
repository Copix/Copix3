<div class="elementEditActionsMenu">
	<?php 
		// affichage bas de page 
		if ($showBack){ ?>
		<table style="width: 100%">
			<tr>
				<td style="width: 15%">
					<div id="loading_img" class="loading_img" style="display:none;">
						<img src="<?php echo _resource ('img/tools/load.gif'); ?>" />
					</div>
				</td>
				<td style="text-align: center">
					<?php
					foreach ($buttons as $button) {
						_eTag ('button', $button);
						echo '&nbsp;';
					}
					?>
				</td>
				<td style="width: 15%; text-align: right"><?php if ($showBack) _eTag ('back', array ('url' => $backUrl)) ?></td>
			</tr>
		</table>
	<?php } 
		//affichage haut de page
		else {?>
		<div style="float:left;<?php if ($showBack){ echo 'text-align: center;';} ?>" >
		<?php
			foreach ($buttons as $button) {
				_eTag ('button', $button);
				echo '&nbsp;';
			}
		?>
		</div>
		<div id="loading_img" class="loading_img" style="display:none;">
			<img src="<?php echo _resource ('img/tools/load.gif'); ?>" />
		</div>
		<div style="clear:both" ></div>
		<?php } ?>
</div>