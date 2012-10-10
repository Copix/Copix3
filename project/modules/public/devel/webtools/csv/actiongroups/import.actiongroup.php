<?php
/**
 * @package		csv
 * @subpackage	import 
 * @author		Sabine CIMAPONTI
 */

/**
 * Classe permettant d'importer les données à partir d'un fichier csv
 *
 */
class ActionGroupImport extends CopixActionGroup {
	
	/**
	 * Fonction permettant de tester les droits d'accès aux fonctionnalités de ce module
	 *
	 * @param string $pActionName nom de l'action que l'on va executer
	 */
	public function beforeAction ($pActionName){
		//echo "dans Import beforeAction"; 
		CopixAuth::getCurrentUser()->assertCredential('module:import@csv');
	}

	/**
	 * Fonction par défaut permettant de diriger l'utilisateur vers la page de selection
	 * pour choisir sur quelle table il souhaite travailler
	 */
	public function processDefault (){

		$ppo = new CopixPPO ();
	    $ppo->TITLE_PAGE = _i18n ('csv.import.table.list');
		
	    //Récupération de la liste des tables
	    $ct = CopixDb::getConnection ();
	    $tabTable = $ct->getTableList ();
	    
	    $ppo->tabTable = $tabTable;
	    
	    return _arPPO ($ppo, 'tableimport.list.tpl');
		
	}
	
	/**
	 * Fonction permettant de faire choisir à l'utilisateur son fichier csv
	 */
	public function processChooseFile (){

		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('csv.import.selectfile');
		
		//Vérification des erreurs et génération du message d'erreur
		if (_request ('errors')){
			switch (_request ('errors')){
				case 1:
	            	$ppo->arErrors = array ('Contenu'=>_i18n ('csv.import.errorcontent'));
	            	break; 
	            case 2:
	            	$ppo->arErrors = array ('Extension'=>_i18n ('csv.import.errorextension'));
	            	break;
	            case 3: 
	            	$ppo->arErrors = _i18n ('csv.import.errorfile');
	            	break;
	            default :
	            	$ppo->arErrors =  _i18n ('csv.import.error');
	        }
		}else{
			$ppo->errors = null;
		}
		
		
		if( _request ('nomTable')){
			//Mise en session du nom de la table
			CopixSession::set ('import|nomTable', _request ('nomTable'));
		}
		
		//Récupération de la liste des fichiers d'export
		$csvFile = _dao ('csvfile')->findAll ();
		
		$ppo->csvfile = $csvFile;
		
		return _arPPO ($ppo, 'selectfile.tpl');
		
	}
		
	/**
	 * Fonction permettant de relier les colonnes du fichier aux champs de la table
	 *
	 */
	public function processSelectColumn (){

		/* Si le nom de la table dans laquelle va être réalisée l'import de données 
		 * n'est pas renseignée, alors on redirige vers l'interface permettant 
		 * à l'utilisateur de sélectionner une table
		 */
		
		if(!CopixSession::get ('import|nomTable')){
			return _arRedirect (_url ('import|'));
		}
		
		//Initialisation des variables
		$nb = "";		$tabLine = "";		$nbColCsv = "";
		
		//Récupération de la connexion
		$ct = CopixDb::getConnection();
		
		//Si il n'y a pas d'erreur dans la selection des colonnes
		if( ! _request ('errors_import')){
			
			/*** SI UPLOAD D'UN FICHIER ***/
			if ( (($fichier = CopixUploadedFile::get ('fichier_csv')) !== false)){
				//Je copie le fichier dans un repertoire temporaire
				copy ($fichier->getTempPath (), COPIX_VAR_PATH.CopixConfig::get ('import_temp').$fichier->getName ());
				$file = new CopixFile ();
				
				//Test si l'extension du fichier correspond bien à .csv
				if ( (strcmp ($file->extractFileExt (COPIX_VAR_PATH.CopixConfig::get ('import_temp').$fichier->getName ()), '.csv')) === 0 ){
	
					/* TRAITEMENT UPLOAD DE FICHIER*/

					//récupération du chemin du fichier
					$path = COPIX_VAR_PATH.CopixConfig::get ('import_temp').$fichier->getName ();
					
					//Je mets le nom du fichier en session et le chemin
					CopixSession::set ('import|fichier', $fichier->getName ());
					CopixSession::set ('import|chemin', $path);
										
					}else{
						//Erreur extension
						return _arRedirect (_url ('import|choosefile', $params = array ('errors'=>2)));
					}

			/*** SINON SI SELECTION D'UN FICHIER ***/
			}elseif(_request ('id_fichier')){
				
				/* TRAITEMENT SELECTION DE FICHIER */
				
				//Récupération de l'enregistrement en base correspondant au fichier selectionné
				$csvfile = _dao ('csvfile')->get (_request ('id_fichier'));
								
				//Création du chemin ou se trouve le fichier
				$path = COPIX_VAR_PATH.copixconfig::get ('export').$csvfile->nomfichier_csvfile;
				
				//Je mets le nom du chemin en session
				CopixSession::set ('import|chemin', $path);
								
				//Je mets le nom du fichier en session 
				CopixSession::set ('import|fichier', $csvfile->nomfichier_csvfile);

			}else{
				//Erreur aucun fichier uploadé ou selectionné
				return _arRedirect (_url ('import|choosefile', $params = array ('errors'=>3)));
					
			}
			
			//Arrivé ici, il y a obligatoirement un fichier qui a été uploadé ou selectionné --> Traitement commun aux 2 parties
			/*** TRAITEMENT COMMUN ***/
			
			//Création de l'object csv sur le fichier csv déclaré
			$objectCsv = new CopixCSV ($path, ';', '"');
			
			//Récupération de l'iterateur 
			$csvIterator = $objectCsv-> getIterator ();
			$erreur = false;
			
			$i=0;
			
			//Récupéation du nombre de ligne que l'on souhaite visualiser
			$nbLigneVisualiser = copixconfig::get ('nb_ligne_visualiser');

			
			for ($k=0; $k<$nbLigneVisualiser; $k++){
				
				$line = "";
				
				//On récupère l'enregistrement sur lequel se trouve l'iterator
				$line = $csvIterator->current ();
				
				//Si il y a un enregistrement qui a bien été récupéré
				if ($line != ""){
					
					$tabLine [$k] = $line;
					$nbColCsv = count ($line);
					CopixSession::set ('import|nb_col_csv', $nbColCsv);
				
				}
				
				//On passe à l'enregistrement suivant
				$csvIterator->next();
			}
			
			/*foreach ($csvIterator as $line){
				
				echo "<pre>";print_r($line);echo"</pre>";
				
				//Récupération de n enregistrements
				if($i<$nbLigneVisualiser){
					$tabLine [$i] = $line;
					$i++;	
				}
				
				$nbColCsv = count ($line);
				CopixSession::set ('import|nb_col_csv', $nbColCsv);
								
				//Pour toutes les lignes du fichiers
				foreach ($line as $occurence){
	
					//Test si c'est bien un fichier csv
					//Test si c'est uniquement des caractères blanc, alphanumériques, majuscule, espacement, ponctuation
					/*if ( ! ereg("^[[:blank:][:alnum:][:upper:][:space:][:punct:]\n\r\@]*$",nl2br($occurence))){
						$erreur = true;							
					}*/
			/*	}
			}*/
			
			
			//Mise en session des 3 lignes dans le cas ou il y aurait une erreur dans la selection des champs
			CopixSession::set ('import|tabLigne', $tabLine);
	
			//Si il n'y a pas d'erreurs dans le fichier csv
			if ($erreur === false){
	
				//Récupération du nom de la table en session
				$nomTable = CopixSession::get ('import|nomTable');
	
				//récupération des champs de la table
	   			$arColumns = $ct->getFieldList ($nomTable);	
	   			
				$nb = count ($arColumns);
			
				//Mise en session du nombre de champ que contient la table
				CopixSession::set ('import|nb_champ',$nb);
	
			}else{
				//Erreur dans le fichier csv
				return _arRedirect (_url ('import|choosefile', $params = array ('errors'=>1)));
			}
			
					
			/*** FIN TRAITEMENT COMMUN ***/		
		
			
		}
		
		//Récupération du nom de la table en session
		$nomTable = CopixSession::get ('import|nomTable');

		//récupération des champs de la table
   		$arColumns = $ct->getFieldList ($nomTable);	
   			
		$nb = count ($arColumns);
		
		//Récupération du nombre de colonne du tableau que l'on affichera par ligne
		$nbColonne = CopixConfig::get ('nb_colonne');
		
		if(!$nb){
			$nb = CopixSession::get ('import|nb_champ');
		}
		
		if (!$nbColCsv){
			$nbColCsv = CopixSession::get ('import|nb_col_csv');
		}
		
		//Calcul du nombre de ligne sur lesquelles sera affiché le tableau
		$nb_ligne = ceil ($nbColCsv/$nbColonne);

		//Construction du tableau contenant la liste des champs de la table
		$i = 0;
		foreach ($arColumns as $uneColonne){
			$tabChamp [$i++] = $uneColonne->name;
		}

		$ppo = new CopixPPO ();
	    $ppo->TITLE_PAGE = _i18n ('csv.import.selectcolumn');
		
		//récupération des erreurs
		if (_request ('errors_import') === '1'){
			$ppo->arErrors = _i18n ('csv.import.errorselectchamp');
			$ppo->tabLst = CopixSession::get ('import|TabLst');
		}else{
			//pas d'erreur
			$ppo->arErrors = null;
			$ppo->tabLst = null;
		}
		
		
		//Récupération du tableau contenant les n enregistrements du ficheir csv
		if (!$tabLine){
			$tabLine = Copixsession::get ('import|tabLigne');
		}
		
		//définition de la taille des colonnes en fonction du nombre de colonne a afficher
		$taille = 100 / ($nbColonne+1);
		
		
	    $ppo->tabChamp = $tabChamp;
		$ppo->nb = $nb;
		$ppo->nb_colonne = $nbColonne;
		$ppo->nb_ligne = $nb_ligne;
		$ppo->lignes = $tabLine;
		$ppo->nb_col_csv = $nbColCsv;
		$ppo->taille = $taille;
		
		return _arPPO ($ppo, 'selectcolumn.php');
		
	}
	
	/**
	 * Fonction permettant d'importer des données d'un fichier csv vers une table choisie
	 *
	 */
	public function processImport (){
		
		//Initialisation du tableau d'erreur et du tableau d'enregistrements qui ne sont pas conformes à null
		$tabErrors = "";		$tabEnr = "";
		
		//récupération du nom de la table passée en parametre
		$tableName = CopixSession::get ('import|nomTable');
		
		//Récupération du fichier uploadé par l'utilisateur
		$fileName = CopixSession::get ('import|fichier');
		
		//Récupération du nombre de champ que contient la table
		$nbFields = CopixSession::get ('import|nb_champ');
		
		//récupération du nombre de colonne se trouvant dans le fichier csv
		$nbColCsv = CopixSession::get ('import|nb_col_csv');
		
		/*TEST QUE LES CHAMPS SOIT SELECTIONNE UNIQUEMENT UNE SEULE FOIS*/
		$errors = false;
		/*Pour tous les champs passés en parametre*/
		for ($i=0; $i<$nbColCsv; $i++){
			for($j=0; $j<$nbColCsv;$j++){
				if($i != $j){
					//Si les 2 représente la même selection dans la liste déroulante et que ce n'est pas null
					if ( (strcmp(_request($i),_request($j))===0) && (strcmp(_request($i),"null")!=0) && (strcmp(_request($j),"null")!=0)) {
						$errors = true;
					}
				}
			}
		}

		//Récupération du nombre de colonne se trouvant dans le fichier csv que l'on souhaite importer
		$nbColCsv = CopixSession::get ('import|nb_col_csv');
		
		//On recupère le positionnement des listes deroulante
		$tabLst="";
		for($i=0; $i<$nbColCsv;$i++){
			$tabLst [$i] = _request ($i);
		}
		CopixSession::set ('import|TabLst', $tabLst);
			
		
		//Si il y a 1 champ utilisé plusieurs fois
		if ($errors === true){
			return _arRedirect (_url ('import|selectcolumn', $params = array ('errors_import'=>1)));
		}
		
		
		//Récupération du chemin dans lequel se situe le fichier
		$path = CopixSession::get ('import|chemin');
		
		//remplacement de \ par / pour ne pas avoir de problème en javascript
		$path = str_replace ('\\' , '/' , $path );
		
		$objectCsv = new CopixCSV ($path, ';', '"');
		
		$nbEnrTotal = count ($objectCsv-> getIterator());
		
		$tabEnrDebut = "";		$tabEnrFin = "";		$tabEnr = "";		$k=0;
		
		while ( ($k*200)< $nbEnrTotal ){
			$tabEnr [$k] ['debut'] = ($k*200)+1; 
			$tabEnr [$k] ['fin'] = ( ( ($k+1)*200) < $nbEnrTotal) ? (($k+1)*200)+1 : $nbEnrTotal+1 ;
			$k++; 
		}
		

		//Variable session contenant le nombre d'enregistrement qui ont déjà été importés
		CopixSession::set ('import|nbEnrInsert',0);
		CopixSession::set ('import|nbEnrInsert',null);

		//Variable session contenant les enregistrements qui n'ont pu être insérés
		CopixSession::set ('import|tabEnr',null);
		//Variable session contenant la liste des erreurs correspondant au enregistrements qui n'ont pu être insérés
		CopixSession::set ('import|tabErrors',null);
		//Variable session correspondant au nombre d'erreur total pour l'import des données
		CopixSession::set ('import|nbErreur',0);
		
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('csv.import.confirmation');
		$ppo->tableName = $tableName;
		$ppo->path = $path;
		$ppo->tabEnr = $tabEnr;
		$ppo->nbEnrTotal = $nbEnrTotal;
		
		return _arPpo ($ppo, 'progressbarimport.tpl');
		
		
		//$tabReturn = _class ('CSVOperations')->import ($tableName, $path, $nbColCsv);
		
		//Initialisation du tableau contenant les libellés des erreurs retournés par la classe
		//$tabErrors = $tabReturn['Erreur'];
		
		//Initialisation du tableau contenant les champs qui n'ont pas été insérés
		//$tabEnr = $tabReturn['Enr'];
		
	
		
		//Libération des variables session
		/*CopixSession::set('import|nomTable', null);
		CopixSession::set('import|fichier', null);
		CopixSession::set('import|tabLigne', null);
		CopixSession::set('import|chemin', null);
		CopixSession::set('import|nb_col_csv', null);
		CopixSession::set('import|TabLst',null);
		
		//Redirection vers un template de confirmation
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n('csv.import.confirmation');
		$ppo->nbInserer = $tabReturn['Inserer'];*/

		
	}

	/**
	 * Fonction permettant d'afficherl'interface permettant de choisir le fichier à partir duquel on souhaite importer des données
	 */
	public function processUpload (){
		
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('csv.import.selectfile');
		return  _arPpo ($ppo, 'choiceupload.tpl');
	}
	
	/**
	 * Fonction permettant d'afficher la liste de fichier exporté
	 */
	public function processList (){
		
		//tabDate contient les dates de création des fichiers
		$tabDate = "";
		//tabHeure contient les heures de création des fichiers
		$tabHour = "";
		
		
		//Tableau contenant tous les numéros de page
		$tabPage = ""; 
		
		//Tableau contenant le numéro de l'enregistrement qui doit être affiché en premier
		$tabMin = "";
			
		// Si un critere de tri a été renseigné, alors on tri par ce critere, sinon par défaut on tri par le nom du courtier
		$tri = (_request ('tri')) ? _request ('tri') :  'nomfichier_csvfile';			
	
		// Si un sens de tri est renseigné, alors on tri selon le sens passé en parametre, sinon on effectue un tri ascendant
		$sens = (_request ('sens')) ? _request ('sens') : 'ASC';
		
		//Le premiere enregistrement a afficher par défaut 0
		$min = (_request ('min')) ? _request ('min') : 0;
		
		//Le numéro de page sur lequel on se situe par défaut 1
		$numPage = ( _request ('numpage')) ? _request ('numpage') : 1;
			
		//Récupération du nombre d'enregistrement total à afficher par page
		$nb = CopixConfig::get('pagination');
		
		
		//Si le tri choisi par l'utilisateur est date, alors on tri sur la date mais aussi sur l'heure
		if ( (strcmp ($tri, 'date_csvfile') )==0){
			$criteres = _daoSp () ->orderBy (array ('date_csvfile', $sens) )->orderBy (array ('heure_csvfile', $sens) )->setLimit ($min, $nb);
		}else{
			$criteres = _daoSp () ->orderBy (array ($tri, $sens) )->setLimit ($min, $nb);
		}
		
		$csvFile = _dao ('csvfile')->findBy ($criteres);
		
		//Récupération du nombre total d'enregistrement de la requete
		$nb_total = count(_dao('csvfile')->findAll ());
		
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('csv.import.list');
		$ppo->nbTotal = $nb_total;
		
		//Si la requête a bien récupérée des enregistrements
		if ($nb_total > 0){
		
			//Calcul du nombre de page total a afficher arroondi à l'entier supérieur
			$nb_pages = ceil ($nb_total / $nb);
			
			for ($i=1;$i<=$nb_pages;$i++){
			    	$tabPage [$i] = $i;
			    	$tabMin [$i] = ($i*$nb) - $nb;
			}
			
			
			$tabEnr = "";
			
			//Création du tableau contenant les dates et les heures
			foreach ( $csvFile as $unfichier ){
				$year = substr ($unfichier->date_csvfile, 0 ,4);
				$month = substr ($unfichier->date_csvfile, 4 ,2);
				$day = substr ($unfichier->date_csvfile, 6, 2);
				
				$our = substr ($unfichier->heure_csvfile, 0, 2);
				$min = substr ($unfichier->heure_csvfile, 2, 2);
				$sec = substr ($unfichier->heure_csvfile, 4, 2);
				
				$tabDate [$unfichier->id_csvfile] = $day."/".$month."/".$year." ".$our.':'.$min.':'.$sec;
				
				//Création de l'object csv sur le fichier csv déclaré
				$objectCsv = new CopixCSV (COPIX_VAR_PATH.CopixConfig::get ("export").$unfichier->nomfichier_csvfile, ';', '"');
				
				//Récupération de l'iterateur 
				$csvIterator = $objectCsv-> getIterator();
				
				//Pour 3 enregistrements	
				for ($i=0; $i<3; $i++){
					//On récupère l'enregistrement sur lequel se trouve l'iterator
					$tabEnr [$unfichier->id_csvfile] [$i] = $csvIterator->current ();
					//On passe à l'enregistrement suivant
					$csvIterator->next ();
				}
				
			}
			
			$ppo->csvFile = $csvFile;
			$ppo->tabDate = $tabDate;
			$ppo->tabHeure = $tabHour;
			$ppo->tabEnr = $tabEnr;
			$ppo->tri = $tri;
		    $ppo->sens = $sens;
		    $ppo->tabPage = $tabPage;
		    $ppo->tabMin = $tabMin;
		    $ppo->numPage = $numPage;	
		    $ppo->nbPage = $nb_pages;
		}
		    
		return  _arPpo ($ppo, 'choicelist.php');
		
	}
	
	/**
	 * Fonction permettant d'appeler la fonction import de la classe csv opération pour importer les données
	 */
	public function processCallImport (){
			
		//Récupération des parametres
		$path = _request ('path');
		$tableName = _request ('tableName');
		$numEnrDepart = _request ('numEnrDepart');
		$numEnrFin = _request ('numEnrFin');
		
		//Récupération du nombre de colonne se trouvant dans le fichier csv en session
		$nbColCsv = CopixSession::get ('import|nb_col_csv');
		
		//Récupération du tableau d'enregistrement erroné
		$tabEnr = CopixSession::get ('import|tabEnr');
		//Récupération du tableau d'erreurs des enregistrements
		$tabErrors = CopixSession::get ('import|tabErrors');
		
		//Import de ces données
		$tabReturn = _class ('CSVOperations')->import ($tableName, $path, $nbColCsv, $numEnrDepart, $numEnrFin);
	
		//Initialisation du tableau contenant les libellés des erreurs retournés par la classe
		$newErrors = $tabReturn ['Erreur'];
		$newEnr = $tabReturn ['Enr'];
		
		//Initialisation du tableau contenant les champs qui n'ont pas été insérés
		
		//Si il y a des erreurs qui ont été générée
		if ($newErrors){
			//Si tabErrors contient déjà des enregistrements
			if (CopixSession::get ('import|tabErrors')){
				CopixSession::set ('import|tabErrors', array_merge ($tabErrors, $newErrors));
			}else{
				CopixSession::set ('import|tabErrors', $newErrors);
			}
		}
		
		if ($newEnr){
			if (CopixSession::get ('import|tabEnr')){
				//Si un tableau contenant
				CopixSession::set ('import|tabEnr', array_merge ($tabEnr, $newEnr));
			}else{
				CopixSession::set ('import|tabEnr', $newEnr);
			}
		}
		
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('csv.import.confirmation');
		$ppo->tableName = $tableName;
		$ppo->path = $path;
		$ppo->tabEnr = $tabEnr;
		
		
		//Mise à jour du nombre d'enregistrement total qui ont été importé
		$nbEnrInsert = CopixSession::get ('import|nbEnrInsert');
		CopixSession::set ('import|nbEnrInsert', $nbEnrInsert + $tabReturn ['Inserer']);
		$ppo->nbInserer = CopixSession::get ('import|nbEnrInsert');
		
		if(CopixSession::get ('import|tabErrors')){
			
			//Tableau d'erreur
			$ppo->tabErreur = CopixSession::get ('import|tabErrors');
			
			//Tableau des enregistrements qui ont générés les erreurs
			$ppo->tabEnr = CopixSession::get ('import|tabEnr');			
					
			//Nombre d'enregistrement n'ayant pu être insérés
			$nbError = CopixSession::get ('import|nbErreur');
			//Mise à jour de ce nombre
			CopixSession::set ('import|nbErreur', count ($ppo->tabErreur));
			//passage au template du nombre
			$ppo->nbErreur = CopixSession::get ('import|nbErreur');
		}	
		
		return _arDirectPpo ($ppo, 'confirmimport.tpl');
		
	}

	/**
	 * Fonction appellée quand l'utilisateur appuie sur le bouton retour.
	 * Elle permet de le rediriger vers l'interface d'administration tout en libérant les différentes variables session
	 */
	public function processBeforeReturn () {
		
		//Controle que si un fichier temporaire existe, et suppression de celui-ci
		if ((file_exists (COPIX_VAR_PATH.copixconfig::get ('import_temp').CopixSession::get ('import|fichier')))){
			unlink (COPIX_VAR_PATH.copixconfig::get ('import_temp').CopixSession::get ('import|fichier'));
		}
		
		//Libération des variables session
		CopixSession::set ('import|nomTable', null);
		CopixSession::set ('import|fichier', null);
		CopixSession::set ('import|tabLigne', null);
		CopixSession::set ('import|chemin', null);
		CopixSession::set ('import|nb_col_csv', null);
		CopixSession::set ('import|TabLst',null);
		CopixSession::set ('import|nb_champ', null);
		CopixSession::set ('import|nbErreur', null);
		CopixSession::set ('import|tabErrors', null);
		CopixSession::set ('import|tabEnr', null);
		CopixSession::set ('import|nbEnrInsert', null);
		
		//Redirection sur la page d'administration
		return _arRedirect (_url ('admin|default|'));
	}

}
?>