<b>Attention</b> : l'élément d'identifiant publique <?php echo $ppo->public_id; ?> n'existe pas.
<br />
<br />
<?php if (count ($ppo->dependencies)){ ?>
Cet identifiant publique est référencé dans <?php echo count ($dependencies) == 1 ? '1 autre élément' : count ($ppo->dependencies) . ' autre(s) élément(s)' ?> :
<table class="CopixTable">
	<tr>
		<th>Nom</th>
		<th style="width: 60px">Statut</th>
		<th colspan="3">Actions</th>
	</tr>
	<?php
	$typeServices = _ioClass ('heading|headingelementtype');
	$heiServices = _ioClass ('heading|HeadingElementInformationServices');
	foreach ($ppo->dependencies as $dependencie) {
		$infos = $typeServices->getInformations ($dependencie->type_hei);
		$actions = $heiServices->getActions ($dependencie->id_helt, $dependencie->type_hei);
		?>
		<tr <?php _eTag ('trclass', array ('id' => 'dependencies')) ?>>
			<td style="text-align: left">
				<img src="<?php echo _resource ($infos['icon']) ?>" alt="<?php echo $infos['caption'] ?>" title="<?php echo $infos['caption'] ?>" style="vertical-align: middle" />
				<?php echo $dependencie->caption_hei ?>
			</td>
			<td><span class="status<?php echo $dependencie->status_hei ?>"><?php echo $ppo->status[$dependencie->status_hei] ?></span></td>
			<td class="action">
				<?php if ($actions->show) { ?>
					<a href="<?php echo _url ('heading||', array ('public_id' => $dependencie->public_id_hei)) ?>" target="_blank">
						<img src="<?php echo _resource ('heading|img/actions/show.png') ?>" alt="Afficher" title="Afficher" />
					</a>
				<?php } ?>
			</td>
			<td class="action">
				<?php if ($actions->edit) { ?>
					<a href="<?php echo _url ('heading|element|prepareEdit', array ('type' => $dependencie->type_hei, 'id' => $dependencie->id_helt, 'heading' => $dependencie->parent_heading_public_id_hei)) ?>" target="_blank">
						<img src="<?php echo _resource ('img/tools/update.png') ?>" alt="Modifier" title="Modifier" />
					</a>
				<?php } ?>
			</td>
			<td class="action">
				<a href="<?php echo _url ('heading|element|', array ('heading' => $dependencie->parent_heading_public_id_hei, "selected[0]"=>$dependencie->id_helt."|".$dependencie->type_hei)) ?>" target="_blank">
					<img src="<?php echo _resource("heading|img/headings.png") ?>" height="16" width="16" alt="Aller dans la rubrique de l'élément" title="Aller dans la rubrique de l'élément"/>
				</a>
			</td>
			
		</tr>
	<?php } ?>
</table>
<?php } else {
echo "Cet identifiant publique n'est référencé dans aucun élément du CMS";
}
?>