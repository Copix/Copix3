<?php

class ZoneScheduler extends CopixZone {
	
	public function _createContent (&$toReturn){
		if ($this->getParam('clicker', null)){
			$tpl = new CopixTpl();				
			$published_date = $this->getParam("published_date");
			if($published_date){
				$tpl->assign ('published_date', CopixDateTime::yyyymmddhhiissToFormat($published_date, 'd/m/Y'));
				$tpl->assign ('published_hour', CopixDateTime::yyyymmddhhiissToFormat($published_date, 'H'));
				$tpl->assign ('published_min', CopixDateTime::yyyymmddhhiissToFormat($published_date, 'i'));
			}		
			$end_published_date = $this->getParam("end_published_date");			
			if($end_published_date){
				$tpl->assign ('end_published_date', CopixDateTime::yyyymmddhhiissToFormat($end_published_date, 'd/m/Y'));
				$tpl->assign ('end_published_hour', CopixDateTime::yyyymmddhhiissToFormat($end_published_date, 'H'));
				$tpl->assign ('end_published_min', CopixDateTime::yyyymmddhhiissToFormat($end_published_date, 'i'));
			}
			$tpl->assign('clicker', $this->getParam('clicker'));
			$tpl->assign('id', $this->getParam('id'));
			$toReturn = _tag("copixwindow", array('fixed'=>true, 'id'=>'schedulerzone_copiwindow'.$this->getParam('id'), 'modal'=>true, 'domready'=>true, 'clicker'=>$this->getParam('clicker'), 'title'=>'Publication différée'),  $tpl->fetch ("scheduler.zone.php"));
		} else {
			$toReturn = "Veuillez renseigner un clicker pour la zone Scheduler";
		}
	}
	
}