<?php

class ActionGroupDefault extends CopixActionGroup {
	public function beforeAction (){
		_currentUser ()->assertCredential ('basic:admin');
	}

	public function processDefault (){
		$ppo = new CopixPPO (array ('TITLE_PAGE'=>'Requêtes en cours'));

		$ppo->arCurrentRequests = _ioDao ('servermonitorrequest')->findBy (_daoSp ()->addCondition ('datetime_smr', '>=', date ('YmdHis', time ()-60))->addCondition ('closed_smr', '=', 0)); 
		$ppo->arLostRequests    = _ioDao ('servermonitorrequest')->findBy (_daoSp ()->addCondition ('datetime_smr', '<=', date ('YmdHis', time ()-60))->addCondition ('closed_smr', '=', 0));
		$ppo->arSummary = CopixDB::getConnection ()->doQuery ('select count(id_smr) count_smr, module_smr, group_smr, action_smr, AVG(duration_smr) avgduration_smr, MIN(duration_smr) quickestduration_smr , MAX(duration_smr) longuestduration_smr from servermonitorrequest where closed_smr = 1 group by module_smr, group_smr, action_smr order by module_smr, group_smr, action_smr');

		return _arPpo ($ppo, 'lostrequests.tpl');
	}
}
?>