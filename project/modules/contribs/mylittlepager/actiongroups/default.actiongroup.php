<?php
class ActionGroupDefault extends CopixActionGroup
{
  //###################################################################
	public function processDefault()
  {
		$ppo = new CopixPpo();

    // NBRE D'ENREGISTREMENTS AFFICHES PAR PAGES : (on le définit ici)
    //--------------------------------------------
    $neapp = 7;

    // Début d'utilisation du pager :
    //-------------------------------
		$base_url_de_la_page = CopixUrl::get('mylittlepager|default|default');
		
    // on prend d'abord le nbre total d'enregistrements :
    //---------------------------------------------------
    $requete = _doQuery('SELECT COUNT(*) as maxi FROM machin');
    $total   = $requete[0]->maxi;

    // s'il y a au moins un enregistrement :
    //--------------------------------------
    if($total>=1)
      {
      if(CopixRequest::exists('page')) $lapage = CopixRequest::getInt('page');
                                  else $lapage = 1;
      // on va préparer notre paginateur, avec un affichage de "$neapp" enregistrements par page :
      $mypager = CopixClassesFactory::create('mylittlepager|pager',
                                             array($total, $lapage, $base_url_de_la_page, $neapp));

      $ppo->donnees = _doQuery('SELECT * FROM machin ORDER BY id_machin DESC LIMIT '.
                               intval($mypager->getdepart()).' , '.intval($mypager->getparpage()) );
      $ppo->navigateur = $mypager->getnavigateur();
      }
    // sinon :
    //--------
    else
      {
      // le query suivant n'est pas en soi nécessaire, mais je le fais quand même
      // pour initialiser le tableau $ppo->donnees
      $ppo->donnees = _doQuery('SELECT * FROM machin ORDER BY id_machin DESC');
      $ppo->navigateur = "Page(s): 0";
      }

    return _arPPO ($ppo, array ('template'=>'machin_liste.tpl', 'mainTemplate'=>'mylittlepagermain.php'));
	}
  //###################################################################
	public function processCreermachin()
  {
		$ppo = new CopixPpo();

    $ppo->retour = CopixUrl::get('mylittlepager|default|default');

    return _arPPO ($ppo, array ('template'=>'creation_machin.tpl', 'mainTemplate'=>'mylittlepagermain.php'));
	}
  //###################################################################
	public function processVerifiernouveaumachin()
  {
		$ppo = new CopixPpo();

    $nom = _request('machin');

    // Vérification que le champ n'a pas été laissé vide :
    if (($nom=="") || ($nom==null))
      {
      $ppo->retour = CopixUrl::get('mylittlepager|default|default');
	    $ppo->contenu = "Vous avez laissé vide le champ nom du machin !";
      return _arPPO ($ppo, array ('template'=>'message_retour.tpl', 'mainTemplate'=>'mylittlepagermain.php'));
      }
       
    // Ajout du nouveau machin dans la BDD :
    _doQuery ('INSERT INTO machin VALUES (null, :valeur)', array(':valeur'=>$nom));

		$ppo->contenu = "Le nouveau machin <b>".$nom."</b> a été enregistré !";
    $ppo->retour = CopixUrl::get('mylittlepager|default|default');

    return _arPPO ($ppo, array ('template'=>'message_retour.tpl', 'mainTemplate'=>'mylittlepagermain.php'));
	}
  //###################################################################
	public function processSupprimermachin()
  {
		$ppo = new CopixPpo();
		$id_a_supp = CopixRequest::getInt('id');

		// Suppression de l'enregistrement :
		_ioDao('machin')->delete($id_a_supp);
		
		$ppo->retour = CopixUrl::get('mylittlepager|default|default');
		$ppo->contenu = "Le machin n°".$id_a_supp." a été supprimé !";

    return _arPPO ($ppo, array ('template'=>'message_retour.tpl', 'mainTemplate'=>'mylittlepagermain.php'));
	}
  //###################################################################
}
?>