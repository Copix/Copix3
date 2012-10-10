<?php
/**
 * @package		csv
 * @subpackage	sql 
 * @author		Sabine CIMAPONTI
 */

/**
 * Classe permettant d'exporter les données à partir d'une requête sql sur une base de données choisie vers un fichier csv
 *
 */
class ActionGroupSQL extends CopixActionGroup {

	/**
	 * Fonction par défaut permettant de diriger l'utilisateur vers
	 * la page lui permettant de saisir sa requete
	 */
	public function processDefault (){
		
		$ppo = new CopixPPO ();
		
		//Récupération de la connexion choisie
		$connection = _request ('connection');

		if (_request ('errors')){
			switch (_request ('errors')){
				case 1:
	            	$ppo->arErrors = array ('Requete'=>_i18n ('csv.export.error.syntaxe.sql'));
	            	break;
	            default :
	            	$ppo->arErrors =  _i18n ('csv.export.error');
			}  
			$requete = _request ('requete');
		}else{
			
			//Test si une requete est bien passée en paramètre
			if (!_request ('requetelibre')){
				//Redirection vers la page pour le choix de la connexion avaec un message d'erreur
				return _arRedirect (_url('export|getconnection', $params = array ('errors'=>2, 'connection'=>$connection)));
			}else{
				$requete = _request ('requetelibre');	
			}
			
			//Si l'execution de la requête retourne une erreur
			try{
				$arResults = CopixDB::getConnection ($connection)->doQuery ($requete);
			}catch (Exception $e)
			{
				//die("arRedirect");
				return _arRedirect (_url('export|getconnection', $params = array ('errors'=>3, 'connection'=>$connection, 'requete'=>$requete)));
			}
		}
		
		//Récupération de la liste des connexions existantes
		$connections = CopixConfig::instance ()->copixdb_getProfiles ();
		
	    $ppo->TITLE_PAGE = _i18n ('csv.sql.requete');
	    $ppo->connections = $connections;
	    $ppo->connection = $connection;
	    $ppo->requete=$requete;
	 
	    return _arPPO ($ppo, 'sql.tpl');
	}

	/**
	 * Fonction permettant d'exporter des données à partir d'une requete
	 *
	 */
	public function processExport (){

		//Récupération des champs que l'utilisateur souhaite exporter
		$tabFields = _request ('fields');
		
		//Récupération de la requête et de la connexion
		$requete = CopixSession::get ('sql|requete') ;
		$connection = CopixSession::get ('sql|connection');
		
		//Récupération du résultat de la requete
		$arResults = CopixDb::getConnection ($connection)->doQuery ($requete);

		//récupération de la date du jour et de l'heure
		$date = date ('Ymd');
		$hour = date ('His');
		
		//Chemin dans lequel sera enregistré le fichier
		$path = COPIX_VAR_PATH.CopixConfig::get ('export');
		
		//Génération du nom du fichier avec le nom de la table et la date du jour
		$filename = $path.'export_requete_'.$date.'_'.$hour.'.csv';
		
		//Création de l'enregistrement en base de données
		$toInsert = _record ('csvfile');
		$toInsert->date_csvfile = $date;
		$toInsert->heure_csvfile = $hour;
		$toInsert->nomfichier_csvfile = 'export_requete_'.$date.'_'.$hour.'.csv';
		
		//Export des données dans le fichier
		$export = _class ('CSVOperations')->Export ($filename, $arResults, $tabFields);

		//Insertion en base de données du fichier
		if ((_ioDao ('csvfile')->check ($toInsert) === true)) {
			_ioDAO ('csvfile')->insert ($toInsert);
		}
		
		//Libération des variables session
		CopixSession::set ('sql|requete', null);
		CopixSession::set ('sql|connection', null);
		
		
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('csv.export.confirmation');
		$ppo->file = 'export_requete_'.$date.'_'.$hour.'.csv';
		
		return _arPPO ($ppo, 'confirmexportsql.tpl');
		
	}

	/**
	 * Fonction permettant d'executer la requête saisie
	 */
	public function processExecute (){

		//echo "<pre>";print_r($_REQUEST);echo"</pre>";
		//die ("dans sql execute");
		
		//Mise en session de la requête saisie par l'utilisateur
		CopixSession::set ('sql|requete', _request ('requete'));
		CopixSession::set ('sql|connection', _request ('connection'));
		
		//Si l'execution de la requête retourne une erreur
		try{
			$arResults = CopixDB::getConnection (CopixSession::get('sql|connection'))->doQuery (_request ('requete'));
		}catch (Exception $e)
		{
			//On ne retourne aucun contenu a afficher
			return _arNone ();
		}
		
		$ppo = new CopixPpo ();
		$ppo->requete = _request ('requete');
		$ppo->connection = _request ('connection');
		
		return  _arDirectPpo ($ppo, 'resultrequete.tpl');
	}

}
?>