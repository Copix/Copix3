<?php echo CopixZone::process ('heading|headingelement/HeadingElementInformationTitle', array ('title' => 'Versions', 'icon' => _resource ('heading|img/togglers/versions.png'))) ?>

<div class="element">
	<div class="elementContent">
		<table class="CopixTable">
			<tr>
				<th style="width: 10px"></th>
				<th>Statut</th>
				<th>Date Création</th>
				<th>Utilisateur</th>
				<th>Voir</th>
			</tr>
			<?php
			foreach ($versions as $version) {
				$parentOk = ($version->parent_heading_public_id_hei >= 0);
				?>
				<tr <?php _eTag ('trclass') ?>>
					<td align="center">
						<?php if ($parentOk) { ?>
							<a href="<?php echo _url ('heading|element|prepareEdit', array ('type' => $version->type_hei, 'id' => $version->id_helt, 'heading' => $version->parent_heading_public_id_hei)) ?>">
						<?php } ?>
						#<?php echo $version->version_hei ?>
						<?php if ($parentOk) { ?>
							</a>
						<?php } ?>
					<td align="center"><span class="status<?php echo $version->status_hei ?>"><?php echo $arElementStatus[$version->status_hei] ?></span></td>
					<td align="center"><?php echo CopixDateTime::yyyymmddhhiissToDateTime ($version->date_create_hei) ?></td>
					<td align="center"><?php echo $version->author_caption_update_hei ?></td>
					<td align="center">
						<?php if ($parentOk) { ?>
							<a href="<?php echo _url ('heading|element|prepareEdit', array ('type' => $version->type_hei, 'id' => $version->id_helt, 'heading' => $version->parent_heading_public_id_hei)) ?>">
								<img title="Voir l'élément" src="<?php echo _resource ('heading|img/generalicons/cms_show.png') ?>" />
							</a>
						<?php } ?>
					</td>
				</tr>
			<?php } ?>
		</table>
	</div>
</div>