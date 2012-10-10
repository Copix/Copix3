<?php 
$typeServices = _ioClass ('heading|headingelementtype');
$heiServices = _ioClass ('heading|HeadingElementInformationServices');
$hemServices = _ioClass ('heading|HeadingElementMenuServices');

if (!empty($listElements)){ ?>
Cette <?php echo $currentElement->type_hei; ?> utilise les éléments suivants : 
<table class="CopixTable">
		<tr>
			<th>Nom</th>
			<th style="width: 60px">Statut</th>
			<th colspan="3">Actions</th>
		</tr>
		<?php
		$draft = false;
		$lastPublicId = -1;
		foreach ($listElements as $element) {
			if ($element->status_hei == HeadingElementStatus::DRAFT){
				$draft = true;
			}
			$infos = $typeServices->getInformations ($element->type_hei);
			$actions = $heiServices->getActions ($element->id_helt, $element->type_hei);
			?>
			<tr <?php echo $element->public_id_hei != $lastPublicId ? _tag ('trclass', array ('id' => 'dependencies')) : ''; ?>>
				<td style="text-align: left">
					<?php if ($element->public_id_hei != $lastPublicId){?>
					<img src="<?php echo _resource ($infos['icon']) ?>" alt="<?php echo $infos['caption'] ?>" title="<?php echo $infos['caption'] ?>" style="vertical-align: middle" />
					<?php echo $element->caption_hei;
					} else {
						echo "<em>En cours de modification par ".$element->author_caption_update_hei."</em>";
					}
					?>
				</td>
				<td><span class="status<?php echo $element->status_hei ?>"><?php echo $status[$element->status_hei] ?></span></td>
				<td class="action">
					<?php if ($actions->show) { ?>
						<a href="<?php echo _url ('heading||', array ('public_id' => $element->public_id_hei)) ?>" target="_blank">
							<img src="<?php echo _resource ('heading|img/actions/show.png') ?>" alt="Afficher" title="Afficher" />
						</a>
					<?php } else if($element->status_hei == HeadingElementStatus::DRAFT){ ?>
						<a href="<?php echo _url ('heading|element|publish', array ('heading' => $element->parent_heading_public_id_hei, 'elements[]' => $element->id_helt."|".$element->type_hei)) ?>">
							<img src="<?php echo _resource ('heading|img/actions/publish.png') ?>" alt="Publier" title="Publier" />
						</a>
					<?php }?>
				</td>
				<td class="action">
					<?php if ($actions->edit) { ?>
						<a href="<?php echo _url ('heading|element|prepareEdit', array ('type' => $element->type_hei, 'id' => $element->id_helt, 'heading' => $element->parent_heading_public_id_hei)) ?>" target="_blank">
							<img src="<?php echo _resource ('img/tools/update.png') ?>" alt="Modifier" title="Modifier" />
						</a>
					<?php } ?>
				</td>
				<td class="action">
					<a href="<?php echo _url ('heading|element|', array ('heading' => $element->parent_heading_public_id_hei, "selected[0]"=>$element->id_helt."|".$element->type_hei)) ?>" target="_blank">
						<img src="<?php echo _resource("heading|img/headings.png") ?>" height="16" width="16" alt="Aller dans la rubrique de l'élément" title="Aller dans la rubrique de l'élément"/>
					</a>
				</td>
				
			</tr>
		<?php 
		$lastPublicId = $element->public_id_hei;
		} ?>
	</table>
	<?php if ($draft){?>
	<a href="<?php echo _url('portal|default|PublishElementsIn' . $currentElement->type_hei , array('public_id_hei'=>$currentElement->public_id_hei)); ?>">Publier les éléments au statut brouillon</a><br />
<?php }
echo "<br />";
}?>

<?php if (count ($dependencies) == 0) { ?>
	Cet élément n'est pas utilisé.
<?php } else { ?>
	Cet élément est utilisé par <?php echo (count ($dependencies) == 1) ? '1 autre élément' : count ($dependencies) . ' autres éléments' ?> :
	<table class="CopixTable">
		<tr>
			<th>Nom</th>
			<th style="width: 60px">Statut</th>
			<th colspan="3">Actions</th>
		</tr>
		<?php
		foreach ($dependencies as $dependencie) {
			if (isset($dependencie->type_hei)){
				$infos = $typeServices->getInformations ($dependencie->type_hei);
				$actions = $heiServices->getActions ($dependencie->id_helt, $dependencie->type_hei);
				?>
				<tr <?php _eTag ('trclass', array ('id' => 'dependencies')) ?>>
					<td style="text-align: left">
						<img src="<?php echo _resource ($infos['icon']) ?>" alt="<?php echo $infos['caption'] ?>" title="<?php echo $infos['caption'] ?>" style="vertical-align: middle" />
						<?php echo $dependencie->caption_hei ?>
					</td>
					<td><span class="status<?php echo $dependencie->status_hei ?>"><?php echo $status[$dependencie->status_hei] ?></span></td>
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
			<?php 
			} else { 
				$target = $heiServices->get ($dependencie->public_id_hei);
				?>
				<tr <?php _eTag ('trclass', array ('id' => 'dependencies')) ?>>
					<td style="text-align: left" colspan="5">
						Menu <?php echo "<em>".$hemServices->getCaption ($dependencie->type_hem, $target->theme_id_hei)."</em>"; ?> sur l'élément <a href="<?php echo _url("heading|element|", array('heading'=>$target->parent_heading_public_id_hei, 'selected[]'=>$target->id_helt.'|'.$target->type_hei)); ?>"><?php echo $target->caption_hei; ?></a>					
					</td>
					
				</tr>
		<?php }
		}?>
	</table>
<?php } ?>