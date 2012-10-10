<?php
/**
 * Trackback Admin Actiongroup
 * Based on http://silent-strength.com/?articles/php/trackbacks
 * 
 * @author Patrice Ferlet - <metal3d@copix.org>
 * @package webtools
 * @subpackage trackback
 * @copyright Copix Team (c) 2007-2008
 */

/**
 * Actions d'administration pour les tracksback
 * @package webtools
 * @subpackage trackback
 */
class ActionGroupAdmin extends CopixActionGroup{

	public function processDefault (){
		$ppo = new CopixPPO();
		$sp = _daoSp()->addCondition('spam_tb', "=", 0)
					  ->addCondition('valid_tb', "=", 0)
					  ->orderBy('date_tb');

		if (_request('all', false) === "true"){
			$sp = _daoSp()->addCondition('spam_tb',"=",0)
					      ->orderBy('date_tb');
		}
		$ppo->trackbacks = _ioDao ('trackbacks')->findBy ($sp);
											  
		$this->_checkSpam ($ppo->trackbacks);
		$ppo->frompage = "default";
        return _arPPO($ppo,"admin.list.tpl");
	}
	
	public function processSpam (){
		$ppo = new CopixPPO();
		$ppo->trackbacks = _ioDao('trackbacks')->findBy(_daoSp()
											  ->addCondition('spam_tb',"=",1)
											  ->orderBy('date_tb')
											  );
											  
		$this->_checkSpam($ppo->trackbacks);
		$ppo->frompage = "Spam";
		
        return _arPPO($ppo,"admin.list.tpl");
	}
	
	public function processDoAdminActions (){
		foreach ($_POST as $key=>$post){
			if(preg_match('/^validate_(\d+)$/',$key,$matches)){
				if($post!=0 && $post!=1){
					throw new Exception("Value for trackback validation must be 1 or 0");
				}
				$tb = $matches[1];
				$rec = _ioDao('trackbacks')->get($tb);
				$rec->valid_tb = $post;
				_ioDao('trackbacks')->update($rec);
			}
			
			if(preg_match('/^spam_(\d+)$/',$key,$matches)){
				if($post!=0 && $post!=1){
					throw new Exception("Value for trackback spam must be 1 or 0");
				}
				$tb = $matches[1];
				$rec = _ioDao('trackbacks')->get($tb);
				$rec->spam_tb= $post;
				if(_request('lastspam_'.$tb) != $post){
					//only if change
					$this->setSpam($rec);
				}
				_ioDao('trackbacks')->update($rec);
			}
		}
		if(_request('todelete') && is_array(_request('todelete'))){
			foreach(_request('todelete') as $delete){
				$rec = _ioDao('trackbacks')->get($delete);
				_ioDao('trackbacks')->delete($rec);
			}
		}
		
		if(_request('frompage')=="Spam"){
			return _arRedirect(_url("trackback|admin|Spam"));
		}		
		return _arRedirect(_url("trackback|admin|"));
	}
	
	
	public function processgetTbContent(){
		CopixRequest::assert('id_tb');
		$res = _ioDao('trackbacks')->get(_request('id_tb'));
		$ppo = new CopixPPO;
		$ppo->MAIN = $res->excerpt_tb;
		
		return _arDirectPPO($ppo,"generictools|blank.tpl");
	}
	
	private function setSpam($rec){
		$bayes = _ioClass('bayes|bayes');
		$bayes->setDataMode("db","trackbacks_spam");
		$content = $rec->title_tb." ".$rec->excerpt_tb." ".$rec->blogname_tb;
				
		//and set the spam category
		if($rec->spam_tb==0){
			$bayes->untrain("spam",$content);
			$bayes->train("nonspam",$content);
		} else {
			$bayes->untrain("nonspam",$content);
			$bayes->train("spam",$content);
		}
	}
	
	private function _checkSpam (& $tbs){
		$bayes = _ioClass ('bayes|bayes');
		$bayes->setDataMode ("db","trackbacks_spam");
		foreach ($tbs as $tb){
			$content = $tb->title_tb." ".$tb->excerpt_tb." ".$tb->blogname_tb;
			$tb->danger = round ($bayes->getBayes ('spam', $content), 2);
			
		}
	}
}
?>