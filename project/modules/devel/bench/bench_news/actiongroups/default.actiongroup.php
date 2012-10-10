<?php
/**
 * @package bench
 * @subpackage bench_news
 */

/**
 *
 */
class ActionGroupDefault extends CopixActionGroup {
	function processDefault (){
		$ppo = new CopixPPO (array ('TITLE_PAGE'=>'Liste de nouvelles (bench)'));
		$ppo->arNews = _ioDAO ('bench_news')->findAll ();
		return _arPpo ($ppo, 'news.list.ptpl');
	}
	
	function processWithSmarty (){
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = 'Liste de nouvelles (bench)';
		$ppo->arNews = _ioDAO ('bench_news')->findAll ();
		return _arPpo ($ppo, 'news.list.tpl');		
	}
	
	/**
	 * Simplement la même chose que getDefault mais pour avoir une url différente prise en 
	 * charge par le plugin cache.
	 */
	function processOptimized (){
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = 'Liste de nouvelles (bench)';
		$ppo->arNews = _ioDAO ('bench_news')->findAll ();
		return _arPpo ($ppo, 'news.list.ptpl');
	}
}
?>