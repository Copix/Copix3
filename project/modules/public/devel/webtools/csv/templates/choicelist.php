<?php

//$href contient les url de destination des icones
$href = "";

//Contient touts les enregistrements des fichiers
$lesFichier = $ppo->csvFile;

//Variable qui contient tout ce qui doit se trouver dans la popuinformation
$popup = "";

//$date contient le tableau contenant les dates et heures de création des fichiers
$date = $ppo->tabDate;

//$heure contient le tableau d'heures passé en paramètre
//$heure = $ppo->tabHeure;

//tabEnr contient les 3 premiers enregistrements de chaque fichier qui vont être insérer dans la popupinformation
$tabEnr = $ppo->tabEnr;

/* Nom du champ sur lequel est effecuté le tri */
$tri = $ppo->tri;

/* Sens du tri (ASC ou DESC) */
$sens = $ppo->sens;

/*Nombre total d'enregistrement retournés par la requete*/
$nbTotal = $ppo->nbTotal;
?>
<br />
<?php if ($nbTotal > 0) {?>
	<form action="import|SelectColumn" method="POST" enctype="multipart/form-data">
	
		<h2><?php echo _i18n ("csv.import.choose.csvfile");?></h2>
				<br />
				<table class="CopixTable">
				<thead>
					<tr>
						<th></th>
						<th>
							<?php echo _i18n ('csv.nomfichier');?>&nbsp;<?php
							if ( ($tri == 'nomfichier_csvfile') && ($sens == 'ASC') ){
								$href = CopixUrl::get('import|list', array ('tri'=>'nomfichier_csvfile', 'sens'=>'DESC'));
								echo _tag ('copixicon', array('href'=>$href, 'type'=>'down', 'title'=>"Trier par ordre décroissant")); 
							}else{
								$href = CopixUrl::get('import|list', array ('tri'=>'nomfichier_csvfile', 'sens'=>'ASC'));
								echo _tag ('copixicon', array ('href'=>$href, 'type'=>'up', 'title'=>'Trier par ordre croissant'));
							}
							?>
						</th>
						<th>
							<?php echo _i18n ('csv.datehourcreation');?>&nbsp;<?php
							if ( ($tri == 'date_csvfile') && ($sens == 'ASC') ){
								$href = CopixUrl::get('import|list', array ('tri'=>'date_csvfile', 'sens'=>'DESC'));
								echo _tag ('copixicon', array ('href'=>$href, 'type'=>'down', 'title'=>'Trier par ordre décroissant'));
							}else{
								$href = CopixUrl::get('import|list', array ('tri'=>'date_csvfile', 'sens'=>'ASC'));
								echo _tag ('copixicon', array ('href'=>$href, 'type'=>'up', 'title'=>'Trier par ordre croissant'));
							}
							?>
						</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				<?php 
						
						for ($i=0; $i<count($lesFichier); $i++){
							
							//Initialisation de popup a vide
							$popup = "";
							
							//Récupération du fichier que l'on va traiter
							$unFichier = $lesFichier[$i];
							
							//Récupération de l'id du fichier
							$id = $unFichier->id_csvfile;
							
							//récupération des enregistrements de ce fichiers à insérer dans le tableau
							$enr = $tabEnr[$id];
	
							//Génération du contenu de la popuinformation
							for ($j=0; $j<count($enr);$j++){
								for($k=0; $k<count($enr[$j]); $k++){
									$popup = $popup." ".$enr[$j][$k];
								}
								$popup = $popup."<br />";
							}
							
							//Mise en place de couleur alternante sur les lignes
							if ($i%2 == 1){ 
							?><tr class="alternate"><?php
							}else{
							?><tr><?php
							}
								echo "<td>",_tag ('popupinformation', array (), $popup),"</td>";
								echo "<td>",$unFichier->nomfichier_csvfile,"</td>";
								echo "<td>",$date[$unFichier->id_csvfile],"</td>";
								//Récupération de l'url de destination avec les différents paramètres requis
								$href = CopixUrl::get('import|selectcolumn', array ('id_fichier'=>$id));
								echo "<td>",_tag ('copixicon', array ('href'=>$href, 'type'=>'select')),"</td>";
							?>	
							</tr>
							<?php
						}
			?>
			</tbody>
		</table>
		<br />
	</form>
	
	<div id="pagination" align="center">
	<?php 
	
		/* Nombre de page total */ 
		$nbPage = $ppo->nbPage;
		/* Tableau contenant le numéro du premier enregistrement a afficher pour chaque page */
		$min = $ppo->tabMin;
		/* Numéro de la page sur laquelle on se trouve actuellement */
		$numPage = $ppo->numPage;
		/* Numéro de la page suivante */
		$numPageSuivante = $numPage+1;
		/* Numéro de la page precedente */
		$numPagePrecedente = $numPage-1;
	
	
		/* Flèche à gauche pour premier et précédent */
		if ($numPage >1){
			/* Double flèche à gauche pour se positionner sur les premiers enregistrements */
			$href = CopixUrl::get('import|list', array ('numpage'=>'1', 'min'=>'0', 'tri'=>$tri, 'sens'=>$sens));
			echo _tag ('copixicon', array ('href'=>$href, 'type'=>'first')),'|';
			/* Flèche simple à gauche pour se positionner sur la page précédente 
			 On ne peut aller sur la page précédente que si l'on ne se trouve pas sur la première page */
			$href = CopixUrl::get('import|list', array ('numpage'=>$numPagePrecedente, 'min'=>$min[$numPagePrecedente], 'tri'=>$tri, 'sens'=>$sens));
			echo _tag ('copixicon', array ('href'=>$href, 'type'=>'previous')),'|';
		}else{
			echo _tag ('copixicon', array('type'=>'first')),"|";
			echo _tag ('copixicon', array('type'=>'previous')),"|";
		}
		
		
		/* Numéro de page */
		foreach ($ppo->tabPage as $page){
			/* Le numéro de page qu'on affiche */
			$page_a_afficher = $page;
			/* Si le numéro de page que l'on va afficher est le meme que celui sur laquelle on se situe */
			if ($numPage == $page_a_afficher){
				echo $page,"|";
			}else{
				?><a href="<?php echo _url ('import|list', array ('numpage'=>$page, 'min'=>$min[$page], 'tri'=>$tri, 'sens'=>$sens));?>"><?php echo $page; ?></a>|<?php
			}
		} 
		
		
		/* Flèches à droite pour suivant et dernier */
		if ($numPage == $nbPage){
			echo _tag ('copixicon', array('type'=>'next')),"|";
			echo _tag ('copixicon', array('type'=>'last'));
		}else{
			//{assign var=minPageSuivante value=$min.$numPageSuivante}
			/* Flèche simple à droite pour se positionner sur la page suivante */
			$href = CopixUrl::get('import|list', array ('numpage'=>$numPageSuivante, 'min'=>$min[$numPageSuivante], 'tri'=>$tri, 'sens'=>$sens));
			echo _tag ('copixicon', array ('href'=>$href, 'type'=>'next')),'|';
			/* Double flèche à droite pour se positionner sur les derniers enregistrements */
			$href = CopixUrl::get('import|list', array ('numpage'=>$nbPage, 'min'=>$min[$nbPage], 'tri'=>$tri, 'sens'=>$sens));
			echo _tag ('copixicon', array ('href'=>$href, 'type'=>'last'));
		}
		
	?>
	</div>
<?php
}else{
	
	echo "<h2>",_i18n ("csv.export.list.nofile"),"</h2>";
	echo "<br />";
	
}
	echo '<input type="button" value="', _i18n('csv.return'), '" name="Retour" onclick="javascript:document.location.href=\''._url ('import|choosefile').'\';"/>';
?>

