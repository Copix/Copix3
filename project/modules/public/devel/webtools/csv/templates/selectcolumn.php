<?php
if (count ($ppo->arErrors)){
	echo '<div class="errorMessage">'.
			'<h1>Erreurs</h1>'.
			 $ppo->arErrors.
	'</div>';
}

/* $nb représente le nombre de champ que contient la table */
$nb = $ppo->nb;

/* nb_colonne représente le nombre de colonne du tableau que l'on affiche sur chaque ligne */
$nb_colonne = $ppo->nb_colonne;

/* $nb_ligne correspond aux nombre de ligne sur lequel sera affiché le tableau*/
$nb_ligne = $ppo->nb_ligne;

/* $tabLigne représente le tableau d'enregistrement extrait du fichier csv */
$tabLigne = $ppo->lignes;

/* $nb_enr représente le nombre d'enregistrement se trouvant dans le tableau tabLigne */
$nb_enr = count($tabLigne);

/* $arrivee represente le nombre pour lequel le pour s'arrete */

if ($ppo->tabLst){
	$tabLst = $ppo->tabLst;
}else{
	$tabLst = null;
}

if ($ppo->tabEnr){
	$tabEnr = $ppo->tabEnr;
}else{
	$tabEnr = null;
}

?>

<br />
<form action="import|import" method="POST" enctype="multipart/form-data">

<?php
/* Pour toutes les lignes que l'on doit afficher */
for ($i=0; $i<$nb_ligne; $i++){
?>

	<table border="1" class="CopixTable">
<?php	
	/* calcul quand doit s'arreter la boucle pour */
	if ( (($i+1)*$nb_colonne) > $ppo->nb_col_csv){
		$arrivee = $ppo->nb_col_csv;
	}else{
		$arrivee = (($i+1)*$nb_colonne);
	}
		
	/* INSERTION DES LISTES DEROULANTES */	
		
	echo '<tr>';
			
	echo '<th width="'.$ppo->taille.'%">';echo _i18n ('csv.selectfield');echo'</th>';
	for ($j=$i*$nb_colonne; $j<$arrivee; $j++){
	
		echo '<td width="'.$ppo->taille.'%">'.
				'<select name='.$j.'>';
					
					/*Insertion d'une option dans la liste déroulante permettant de ne pas selectionner un champ*/
					if (($tabLst) && ($tabLst[$j]===null)){
					 	echo '<option value="null" selected> - - - </option>';
					}else{
						echo '<option value="null"> - - - </option>';
					}
					 foreach ($ppo->tabChamp as $unChamp){
					 	if (($tabLst) && ($tabLst[$j]===$unChamp)){
							echo '<option selected>'.$unChamp.'</option>';
						}else{
							echo '<option>'.$unChamp.'</option>';
						}
					 }
				echo '</select>'.
			'</td>';
	}
	echo '</tr>';
		
		
		/* INSERTION DES CHAMPS */
		
		for ($k=0; $k<$nb_enr; $k++){
		
			$uneLigne = $tabLigne[$k];
			
			echo '<tr>';
			
			echo '<th width="'.$ppo->taille.'%">', _i18n ('csv.exemple'), '</th>';
			
			for ($j=$i*$nb_colonne;$j<$arrivee;$j++){
					echo '<td width="',$ppo->taille,'">',$uneLigne[$j],'</td>';
			}
			echo '</tr>';
		}
		echo '</table>';
		echo '<br />';
}
	echo '<br/>';
	echo '<input type="submit" value="', _i18n ('csv.save'), '" name="save" /> ';	
	echo '<input type="button" value="', _i18n('csv.return'), '" name="Retour" onclick="javascript:document.location.href=\''._url ('import|choosefile').'\';"/>';
    echo '</form>';
?>