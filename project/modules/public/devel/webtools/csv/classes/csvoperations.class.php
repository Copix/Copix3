<?php
/**
 * @package		csv
 * @author		Sabine CIMAPONTI
 */

/**
 * Classe de manipulation des fichiers CSV
 */
class CsvOperations {
	
	
	/**
	 * Fonction permettant d'importer des données dans une table à partir d'un fichier csv
	 *
	 * @param	string 	$tableName 		nom de la table dans laquelle va être importé les données
	 * @param	string 	$fileName 		chemin et nom du fichier csv à partir duquel on récupère les données à insérer
	 * @param	int    	$nbColCsv 		nombre de colonne du fichier 
	 * @param	int		$numEnrDepart	numéro de l'enregistrement à partir duquel on commence a exporter les données
	 * @param 	int		$numEnrFin		numéro de l'enregistrement à partir duquel on arrête d'exporter les données
	 * @return	tab	 	$tabReturn		Inserer = tableau contenant le nombre d'enregistrement qui ont été importé,
	 * 									Erreur = les erreurs pour chaque ligne du fichier qui n'a pu être importée,
	 * 									Enr = les enregistrements qui ont causés des erreurs lors de l'import
	 * 						
	 */
	public function import ($tableName, $fileName, $nbColCsv, $numEnrDepart, $numEnrFin){
		
		//Récupération du tableau contenant l'ordre d'enregistrement des champs
		$tabCol = CopixSession::get ('import|TabLst');

		//Récupération du nom de la base de données par défaut
		$dataBaseName = CopixConfig::instance()->copixdb_getProfile()->getName();
		
		//Nombre d'enregistrements importés / nombre d'enregistrements non importés
		$nbInsert = 0; 	$nbNoInsert = 0;
		
		//Création de l'object csv sur le fichier csv déclaré
		$objectCsv = new CopixCSV ($fileName, ';', '"');
		
		//Récupération de l'iterateur 
		$csvIterator = $objectCsv-> getIterator ();

		//Initialisation du tableau d'erreur et d'enregistrements erronés à null
		$tabErrors = ""; $tabEnr = "";
		
		$csvIterator->rewind ();
		//Pour toutes les lignes qui sont dans le fichier csv

		//On se positionne sur le bon enregistrement
		$csvIterator->seek ($numEnrDepart);
		
		for ($k=$numEnrDepart; $k<$numEnrFin; $k++){
			
			//On récupère l'enregistrement sur lequel se trouve l'iterator
			$line = $csvIterator->current ();
			//Création d'un record sur la table concernée
			$toSave = _record ($tableName);
			
			//Pour tous les champs du fichier
			for($i=0; $i<$nbColCsv; $i++){
				//Si ce n'est pas un champ que l'utilisateur ne souhaitait pas insérer dans la base
				if (strcmp (_request ($i), "null") != 0){
					
					//Récupération du nom du champ correspondant a la colonne du tableau csv 
					$field = $tabCol [$i];

					//Insertion dans le record 
					$toSave->$field = $line [$i];
				}
			}
			
			if (_ioDAO ($tableName, $dataBaseName)->check ($toSave) === true){
				//Enregistrement en base de données
				_ioDAO ($tableName, $dataBaseName)->insert ($toSave);
				$nbInsert++;
			}else{
				$nbNoInsert++;
				$tabEnr [$nbNoInsert] = $toSave;		
				$tabErrors [$nbNoInsert] = _ioDAO ($tableName)->check ($toSave);
			}
			
			//On passe à l'enregistrement suivant
			$csvIterator->next();
		}
		$tabReturn ['Inserer'] = $nbInsert;
		$tabReturn ['Erreur'] = $tabErrors;
		$tabReturn ['Enr'] = $tabEnr;

		return $tabReturn;
	}

	 /**
	  * Fonction permettant d'exporter des données d'une table dans un fichier csv
	  *
	  * @param  string	fileName		chemin et nom du fichier que l'on va créer
	  * @param  string	arResults		chemin dans lequel on va enregistrer le fichier
	  * @param  tab		tabFields		tableau contenant les champs que l'utilisateur souhaite exporter. Si null, tous les champs sont exportés	
	  */
	public function export ($fileName, $arResults, $tabFields=null){

		//Création d'un objet csv par rapport au chemin et au nom du fichier
		$objectCsv = new CopixCSV ($fileName,';','"');
		
		//Pour toutes les résultats
		foreach ( $arResults as $resultat ){
			$i=0; $tabContent = "";
			
			//Si un tableau de champs est passé en paramètre
			if ($tabFields){
				//Création du tableau de clé du résultat de la requête
				$tabKeys = array_keys(get_object_vars($resultat));
				
				//Pour tous les champs que l'utilisateur souhaite exporter
				foreach ($tabFields as $field){
						//Je récupère la valeur de ce champs dans le résultat parcouru
						$tabContent [$i++] = $resultat->$field;
				}
			}else{
				//Pour tous les champs de la table
				foreach ($resultat as $unChamp){
						$tabContent [$i++] = $unChamp;
				}
			}
			//Ajout de la ligne dans le fichier
			$objectCsv->addLine($tabContent);
		}
		
		return true;
		
	}

}
?>