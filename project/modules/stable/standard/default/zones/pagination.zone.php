<?php
/**
 * @package Copix
 * @author Goulven CHAMPENOIS
 * @date 5 aout 2009
 * @tutorial http://svn.copix.org/wiki/CopixZone
 */
class ZonePagination extends CopixZone {
	
	/**
	 * Pagination : permet de générer une liste de liens de pagination
	 * @param Int     max        le nombre total de pages
	 * @param Int     current    la page actuellement affichée (entre 1 et max)
	 * 
	 * Paramètres facultatifs :
	 * @param String  linkBase   URL de base du lien (ex: /recherche?page=). Par défaut, '?page='
	 * @param Int     surround   le nombre de liens avant et après la page courante. Par défaut, 2    
	 * @param Boolean showBounds si on doit afficher les liens "Première page" et "Dernière page". Vrai par défaut
	 * 
	 * Exemple d'utilisation : echo CopixZone::process('default|pagination', array( 'current' => $ppo->pageCourante, 'max' => $ppo->totalPages )); 
	 */
	function _createContent (&$toReturn){
		// Vérification des paramètres obligatoires
		$this->assertParams('max', 'current');
		
		// Stockage des paramètres utilisés pour le calcul
		$lastPage = $this->getParam ('max');
		$currentPage = $this->getParam ('current');
		$surround = (int)abs($this->getParam ('surround', 2)); // Nombre de liens autour de la page courante

		$ppo = new CopixPPO ();
		$ppo->currentPage = $currentPage;
		$ppo->linkBase = $this->getParam('linkBase', '?page=');
		
		// "Première page" et "Dernière page"
		$ppo->showBounds = $this->getParam ('showBounds', true);
		$ppo->firstPage = _i18n ('&lt;&lt; Première page');
		$ppo->lastPage = _i18n ('Dernière page &gt;&gt;');
		// "Page précédente" et "Page suivante"
		$ppo->showNext = $this->getParam ('showNext', false);
		$ppo->previousPage = _i18n ('&lt; Page précédente');
		$ppo->nextPage = _i18n ('Page suivante &gt;');
		
		$ppo->loopStart = ($currentPage - $surround) < 1 ? 1 : $currentPage - $surround;
		$ppo->loopEnd = ($currentPage + $surround) > $lastPage ? $lastPage : $currentPage + $surround;
		$ppo->max = $lastPage;
		
		$toReturn = $this->_usePpo ($ppo, 'pagination.php');
		return true;
	}
}