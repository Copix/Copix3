<?php
/**
 * @package		csv
 * @subpackage	export 
 * @author		Sabine CIMAPONTI
 */

/**
 * Classe gérant l'export de données d'une table d'une base de données choisie à un fichier csv
 *
 */
class ActionGroupExport extends CopixActionGroup {

	public function beforeAction ($pActionName){
		//echo "<pre>";print_r(CopixAuth::getCurrentUser()->testCredential('module:export@csv'));echo"</pre>";
		CopixAuth::getCurrentUser()->assertCredential('module:export@csv');
	}
	
	/**
	 * Fonction par défaut permettant de diriger l'utilisateur vers la page de selection
	 * pour choisir sur quelle table il souhaite travailler
	 */
	public function processDefault (){

		$ppo = new CopixPPO ();
	    $ppo->TITLE_PAGE = _i18n ('csv.export.table.title');
	 
	    //Récupération de la liste des tables
	    $tabTable = CopixDb::getConnection (_request ('connection'))->getTableList ();
	    $ppo->connection = _request ('connection');
	    $ppo->requete = _request ('requete'); 
	    $ppo->tabTable = $tabTable;
	    
	    return _arDirectPpo ($ppo, 'tableexport.list.tpl');
		
	}
	
	/**
	 * Fonction permettant de lister tous les fichiers d'export
	 */
	public function processList (){
	
	//tabDate contient les dates de création des fichiers
	$tabDate = "";
	//tabHour contient les heures de création des fichiers
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
	$nb = CopixConfig::get ('pagination');
	
	//Si le tri choisi par l'utilisateur est date, alors on tri sur la date mais aussi sur l'heure
	if ((strcmp($tri,'date_csvfile'))==0){
		$criteres = _daoSp () ->orderBy (array ('date_csvfile',$sens))->orderBy (array ('heure_csvfile',$sens))->setLimit ($min, $nb);
	}else{
		$criteres = _daoSp () ->orderBy (array ($tri,$sens))->setLimit ($min, $nb);
	}
	
	$csvFile = _dao ('csvfile')->findBy ($criteres);
	
	//Récupération du nombre total d'enregistrement de la requete
	$nb_total = count (_dao('csvfile')->findAll ());
	
	$ppo = new CopixPPO ();
	$ppo->TITLE_PAGE = _i18n ('csv.export.list');
	$ppo->nbTotal = $nb_total;
	
	//Si la requête a bien récupérée des enregistrements
	if ($nb_total > 0){
	
		//Calcul du nombre de page total a afficher arroondi à l'entier supérieur
		$nb_pages = ceil ($nb_total/$nb);
		
		for ($i=1;$i<=$nb_pages;$i++){
		    	$tabPage [$i] = $i;
		    	$tabMin [$i] = ($i*$nb)-$nb;
		}
	
		//Création du tableau contenant les dates et les heures
		foreach ( $csvFile as $unfichier ){
			$year = substr ($unfichier->date_csvfile, 0 ,4);
			$month = substr ($unfichier->date_csvfile, 4 ,2);
			$day = substr ($unfichier->date_csvfile, 6, 2);
			
			$hour = substr ($unfichier->heure_csvfile, 0, 2);
			$min = substr ($unfichier->heure_csvfile, 2, 2);
			$sec = substr ($unfichier->heure_csvfile, 4, 2);
			
			$tabDate[$unfichier->id_csvfile] = $day."/".$month."/".$year ;
			$tabHour[$unfichier->id_csvfile] = $hour.':'.$min.':'.$sec;
		}
	

		$ppo->csvFile = $csvFile;
		$ppo->tabDate = $tabDate;
		$ppo->tabHeure = $tabHour;
	    $ppo->tri = $tri;
	    $ppo->sens = $sens;
	    $ppo->tabPage = $tabPage;
	    $ppo->tabMin = $tabMin;
	    $ppo->numPage = $numPage;	
	    $ppo->nbPage = $nb_pages;
	
	}
	    
	return _arPPO ($ppo, 'export.list.tpl');	
	
	}

	/**
	 * Fonction permettant de supprimer un fichier d'export de données
	 *
	 */
	public function processDelete (){
		
		//Récupération du nom du fichier et de son identifiant
		$fileName = _request ('nomfichier');
		$id_csvFile = _request ('id_csvfile');
		
		if (! _request ('confirm', false, true)){
			return CopixActionGroup::process ('generictools|Messages::getConfirm',
  				array ('message'=>_i18n ('csv.export.confirmdelete')." : ".$fileName.'?', 'confirm'=>_url ('export|delete', 
  					   array ('nomfichier'=>$fileName, 'id_csvfile'=>$id_csvFile, 'confirm'=>1)), 'cancel'=>_url ('export|list')));
		}else{
			//Suppression de l'enregistrement en base de données
			_ioDAO ('csvfile')->delete ($id_csvFile);
			//Suppression du fichier dnas le repertoire
			unlink (COPIX_VAR_PATH.copixconfig::get ('export').$fileName);
			return _arRedirect (_url ('export|list'));
		}
	}

	/**
	 * Fonction permettant de télécharger un fichier d'export de données
	 *
	 */
	public function processDownload (){
		
		$fileName = _request ('nomfichier');
		
		return _arFile (COPIX_VAR_PATH.copixconfig::get ('export').$fileName, array ('filename'=>$fileName));
		
	}

	/**
	 * Fonction permettant de voir le contenu d'une table
	 *
	 */
	public function processSeeContent (){
			
		$tableName = _request ('nomTable');
		
   		$ppo = new CopixPpo ();
   		$ppo->TITLE_PAGE = _i18n ('csv.export.content.table').' '.$tableName;
   		$ppo->connection = _request ('connection');
		$ppo->nomTable = $tableName;   		
   		
   		return _arPpo ($ppo,'contenttable.tpl');
		
	}

	/**
	 * Fonction permettant de récupérer les connexions existantess
	 */
	public function processGetConnection (){
		
		$ppo = new CopixPpo ();
		
		$requete = "";
		
		if (_request ('errors')){
			switch (_request ('errors')){
				case 1:
					$ppo->arErrors = array ('Contenu'=>_i18n ('csv.export.error.selecttable'));
					break;
				case 2:
					$ppo->arErrors = array ('Contenu'=>_i18n ('csv.export.error.sql'));
					break;
				case 3:
					$ppo->arErrors = array ('Contenu'=>_i18n ('csv.export.error.syntaxe.sql'));
					$requete = _request ('requete');
					break;
				default : 
					$ppo->arErrors = array ('Contenu'=>_i18n ('csv.export.error'));
					break;
			}
		}else{
			$ppo->errors = null;
		}
		
		//Récupération de la liste des connexions existantes
		$connections = CopixConfig::instance ()->copixdb_getProfiles ();

		foreach ($connections as $connection){
			//$tabConnections[$connection] = CopixConfig::instance()->copixdb_getProfile ($connection);
			$profil = CopixConfig::instance ()->copixdb_getProfile ($connection);
			
			$tabConnections [$connection]->name = $profil->getName ();
			$tabConnections [$connection]->user = $profil->getUser ();
			$tabConnections [$connection]->driverName = $profil->getDriverName ();
			$tabConnections [$connection]->connectionString = $profil->getConnectionString ();
			
		}
		
		//Pour toutes les connexions, on les mets par défaut en non visibles
		foreach ($connections as $connection){
			$tabSelected[$connection] = false;
		}
		
		//Si une connexion est passée en paramètre, alors on la sélectionne
		if (_request ('connection')){
			$tabSelected[_request ('connection')] = true;
		}
		
		//Si une requete a été récupérée, alors on la passe en paramètre
		if ($requete){
			$ppo->requete = $requete;
		}
		
		$ppo->TITLE_PAGE = _i18n ('csv.export.title');
		$ppo->connections = $connections;
		$ppo->tabConnections = $tabConnections;
		$ppo->tabSelected = $tabSelected;
		
		return _arPpo ($ppo,'chooseconnection.tpl');
	}
	
	/**
	 * Fonction permettant d'afficher la barre de chargement et de lancer l'export des tables
	 */
	public function processExportBarProgression (){
		
		//Si un des 2 parametres n'est pas renseigné
		if ( (_request ('nomTable')) && (_request ('connection'))){
		
			//Récupération de la liste des table a exporter	
			$tables = _request ('nomTable');
			$connection = _request ('connection');
			
			$tabTable = "";
			
			//Préparation du tableau contenant l'etat de la table (2 = en attente , 1 = en cours , 0 = exportée)
			foreach ($tables as $table){
					$tabDetails = "";
					$tabDetails = array ('tableName'=>$table, 'statut'=>2);
					$tabTable [$table] = $tabDetails;
			}
			
			//Rédirection vers la barre de progression
			$ppo = new CopixPpo ();
			$ppo->tables = $tables;
			$ppo->connection = $connection;
			$ppo->tabTable = $tabTable;
			$ppo->TITLE_PAGE = _i18n('csv.export.underway');
			
			return _arPpo ($ppo, 'progressbar.tpl');
			
		}else{
			return _arRedirect(_url('export|getconnection', $params = array ('errors'=>1, 'connection'=>_request('connection'))));
		}
			
	}

	/**
	 * Fonction permettant d'exporter les tables selectionnées
	 */
	public function processExport (){
				
		//tabFileName sert a stocker les nom des fichiers ou a été effectué l'export
		$tableName = "";	$fileName = "";		$error = "" ;	$tabChamp="";	$i = 0;
		
		//Si des tables ont bien été selectionné pour être exportées vers des fichiers csv
		if ( (_request ('tableName')) && (_request ('connection')) ){
			
			$tableName = _request ('tableName');

			//récupération de la date du jour et de l'heure
			$date = date ('Ymd');	$hour = date ('His');
				
			//Génération du nom du fichier avec le nom de la table et la date du jour
			$fileName = COPIX_VAR_PATH.CopixConfig::get ('export').'export_'.$tableName.'_'.$date.'_'.$hour.'.csv';
			
			//Création d'un record pour l'insertion en base de données
			$toInsert = _record ('csvfile');
			$toInsert->date_csvfile = $date;
			$toInsert->heure_csvfile = $hour;
			$toInsert->nomfichier_csvfile = 'export_'.$tableName.'_'.$date.'_'.$hour.'.csv';
				
			//récupération des champs de la table
			$ct = CopixDb::getConnection (_request ('connection'));
	   		$arColumns = $ct->getFieldList ($tableName);				
				
			//Construction du tableau contenant la liste des champs de la table
			foreach ($arColumns as $uneColonne){
				$tabChamp[$i++] = $uneColonne->name;
			}
			 
			//Récupération de toutes les données de la table par rapport à la connexion choisie
			$arResults = CopixDb::getConnection (_request ('connection'))->doQuery("select * from ".$tableName);
			
			//Si il y a des enregistrements dans la table
			if (count ($arResults) > 0 ){
					
					$fileNameCourt = 'export_'.$tableName.'_'.$date.'_'.$hour.'.csv';

					//Export des données dans le fichier
					$export = _class ('CSVOperations')->Export ($fileName, $arResults, $tabChamp);
	
					//Insertion en base de données
					if ((_ioDao ('csvfile')->check ($toInsert) === true)) {
						_ioDAO ('csvfile')->insert ($toInsert);
					}
			}else{
				$error =$tableName;				
			}
			
		$ppo = new CopixPpo ();
		
		if ($error){
			$ppo->error = $error;
		}else{
			$ppo->nomTable = $tableName;
			$ppo->nomFichier = $fileNameCourt;
		}
		
		return _arDirectPpo ($ppo, 'confirmexport.tpl');
		
		}
	}
	
}
?>