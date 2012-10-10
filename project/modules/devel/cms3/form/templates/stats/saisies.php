<?php

$pagination = CopixZone::process('default|pagination', array(
	'linkBase' => _url('#', array('page'=>'')),
	'current' => $ppo->options->page,
	'max' => $ppo->options->nbrPages,
	'surround' => 2
));

?>
<?php _eTag ('beginblock', array ('title' => 'Séléction du formulaire', 'isFirst' => true)); ?>
	<div id="div" >
		<div>		
			<form id="cmsform" action="<?php echo _url('form|stats|getstats'); ?>" method="post">
				<table class="CopixVerticalTable">
					<tr>
						<th width="30%"><label for="form_id">Type de formulaire : &nbsp;</label></th>
						<td>
							<select name="form_id">
								<?php foreach ($ppo->arCMSForms as $id=>$arForm){
									$label = _ioClass('heading/headingelementinformationservices')->get($id)->caption_hei;
									?>
									<optgroup label="<?php echo $label; ?>">
									<?php foreach ($arForm as $idForm=>$form) { ?>
										<option <?php echo $ppo->options->selectedForm == $idForm ? 'selected="selected"' : ''; ?> value="<?php echo $idForm; ?>"><?php echo $form; ?></option>
									<?php }?>
									</optgroup>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr class="alternate">
						<th>
							<label for="nbrParPage">Depuis le</label>
						</th>
						<td>
							<?php echo _tag('calendar2', array('name'=>'dateDebut', 'value'=>$ppo->options->dateDebut)); ?>
						</td>
					</tr>
					<tr>
						<th>
							<label for="nbrParPage">Jusqu'au</label>
						</th>
						<td>
							<?php echo _tag('calendar2', array('name'=>'dateFin', 'value'=>$ppo->options->dateFin)); ?>
						</td>
					</tr>
					<tr class="alternate">
						<th>
							<label for="nbrParPage">Nombre de résultats</label>
						</th>
						<td>
							<input type="text" class="text numeric" name="nbrParPage" id="nbrParPage" size="3" value="<?php echo $ppo->options->nbrParPage ?>" /> par page
						</td>
					</tr>
				</table>
				<p class="sbumit"><input type="submit" value="Envoyer" /></p>
			</form>
		</div>
	</div>
<?php _eTag ('endblock'); ?>
<?php 
if ($ppo->options->selectedForm){
	_eTag ('beginblock', array ('title' => 'Liste des saisies du formulaire', 'isFirst' => true));
	echo count($ppo->datesEnvois). " résultat(s)."; 
	if (count($ppo->datesEnvois)){
		echo "<br /><a href='"._url('form|stats|getstats', array('csv'=>true, 'nbrParPage'=>_request('nbrParPage', 20)))."'>Télécharger les resultats de cette page au format CSV</a><br />";
		echo "<br /><a href='"._url('form|stats|getstats', array('csv'=>true, 'all'=>true))."'>Télécharger tous les resultats au format CSV</a><br /><br />";
		echo $pagination;
		?>
		<br />
		<table class="CopixTable">
			<tr>
			<th>Date</th>
			<?php
			if (!empty($ppo->formFields)){
				foreach ($ppo->formFields as $field){
					echo "<th>".$field->cfe_label."</th>";
				}
			}
			?>
			</tr>
			<?php
			$i = 0;
			if (!empty($ppo->datesEnvois)){
				foreach ($ppo->datesEnvois as $envoi){
					echo "<tr class='".($i%2 == 0 ? '' : 'alternate')."'>";
					echo "<td>".$envoi->cfv_date."</td>";
					$values = _ioClass('form|form_service')->getValues($envoi->cfv_date);
					foreach ($ppo->formFields as $field){
						if (array_key_exists($field->cfe_id, $values)){
							echo "<td>".(is_array($values[$field->cfe_id]->cfv_value) ? implode(" - ", $values[$field->cfe_id]->cfv_value) : $values[$field->cfe_id]->cfv_value)."</td>";
						} else {
							echo "<td></td>";
						}
					}
					
					echo "</tr>";
					$i++;
				}
			}
			?>
		</table>
		<?php echo $pagination;
		
	}
	_eTag ('endblock'); 
}
/*
?>
<div style="text-align: right; width: 100%;">
	<a href="<?php echo _url('admin||'); ?>">
		<img alt="Retour" src="<?php echo _resource('img/tools/back.png') ?>"/> Retour
	</a>
</div>
*/ ?>