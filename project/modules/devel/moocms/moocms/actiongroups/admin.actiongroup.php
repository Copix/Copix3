<?php
/**
 * Administration for mooCMS
 * 
 * @package MooCMS
 * @subpackage MooCMS
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * 
 */
class ActionGroupAdmin extends CopixActionGroup{

	/**
	 * Before any aciton, moocms needs mootools
	 *
	 * @param action 
	 */
	public function beforeAction($pAction){
		_eTag('mootools');
	}

	/**
	 * Default Action, whow pages and porpose to create a new page
	 *
	 * @return ActionReturn::PPO
	 */
	public function processDefault(){

		$pages = _ioDao('moocms_pages')->findBy(_daoSP()
				->groupBy('name_moocmspage')
				);

		$ppo = new CopixPPO;
		$ppo->pages = $pages;
		/*$templates = array("3 colonnes dont 2 à droite plus petites"=>'display.1large.2smalls.tpl',
		  "2 colonnes de 50%"=>'display.2cols.tpl',
		  "1 colonne en haut, 2 colonnes de 50%, 1 colonne en bas"=>'display.1_2_1.tpl'
		  );*/

		$templates = _ioClass('mootpl|mootpl')->getTemplates();
		$ppo->templates = $templates;
		return _arPPO($ppo,"admin.page.php");

	}

	/**
	 * Edit a MOOcms page
	 *
	 * @param string title
	 * @return ActionReturn::PPO
	 */
	public function processEdit(){
		//get js
		$tpljs = new CopixTpl();
		CopixHTMLHeader::addJSCode($tpljs->fetch('webbox.js.php'));


		$ppo = new CopixPPO;
		if(CopixRequest::get('title',false)){
			$tpl = new CopixTpl();

			$boxes = _ioClass('moopage')->getBoxInfos(CopixRequest::get('title'));
			foreach($boxes as $box){
				//$box->params_moocmsbox = preg_replace('/boxid=(.*?)&/','',$box->params_moocmsbox);
			}


			$tpl = new CopixTpl();
			$tpl->assign('boxes',$boxes);
			$ppo->init = $tpl->fetch('init.admin.js.php');	
			$ppo->pagename = CopixRequest::get('title');
			$page = _ioDao('moocms_pages')->findBy(_daoSP()
					->addCondition('name_moocmspage','=',CopixRequest::get('title'))
					->orderBy(array('date_moocmspage','DESC'))
					);

			$page = $page[0];
			$ppo->template = $page->template_moocmspage;
			$ppo->display = _ioClass('moopage')->getPage(CopixRequest::get('title'));
		}else{
			$tpl = new CopixTpl;
			$ppo->template = CopixRequest::get('template');
			$ppo->display = $tpl->fetch('mootpl|'.CopixRequest::get('template'));
		}

		return _arPPO($ppo,"editor.php");
	}



	/**
	 * Called by ajax method
	 * Get Boxes to put on page
	 * 
	 * @return ActionReturn::PPO
	 */
	public function processGetBoxes(){
		$ppo = new CopixPPO();
		$ppo->boxes = _ioClass('moobox')->getBoxes();
		return _arDirectPPO($ppo,"putbox.php");
	}


	/**
	 * Get box content (by Ajax)
	 *
	 * @param string name of box
	 * @return ActionRetrun::DirectPPO
	 */
	public function processGetBoxContent(){
		CopixClassesFactory::fileInclude('moobox');
		$boxname = "moobox_".CopixRequest::get('name');
		$classname = "moobox".CopixRequest::get('name');
		$ppo = new CopixPPO();

		//if edit is declared, process...
		$edit= _ioClass($boxname.'|'.$classname)->getEdit();
		if($edit && !CopixRequest::get('noedit')){
			$ppo->MAIN = "<!-- MOOCMS_EDITMODE -->\n".$edit;
			return _arDirectPPO($ppo,'generictools|blank.tpl');
		}
		$params = array();
		foreach(CopixRequest::asArray() as $key=>$val){
			$params[$key] = str_replace("__COPIX_add_slashes_COPIX__","/",$val);
		}


		$ppo->MAIN = _ioClass($boxname.'|'.$classname)->getContent($params);
		return _arDirectPPO($ppo,'generictools|blank.tpl');
	}


	/**
	 * Create a new version of page (Called by Ajax)
	 * @param string pagename
	 * @param string template
	 * @return ActionReturn::None
	 */
	public function processCreateVersionPage(){
		try {
			CopixRequest::assert('pagename');
			if(!CopixRequest::get('pagename')){
				throw new CopixException("No pagename given");
			}
		}catch(CopixException $e){
			echo "ERROR: Vous devez donner un nom à votre page";
			return _arNone();
		}


		$page = _record('moocms_pages');
		$page->name_moocmspage = CopixRequest::get('pagename');
		$page->template_moocmspage = CopixRequest::get('template');
		$page->date_moocmspage = date('YmdHis');
		_ioDAO('moocms_pages')->insert($page);
		echo $page->date_moocmspage;
		return _arNone();
	}

	/**
	 * Save edited page
	 *
	 * @param string pagename
	 * @param string pagedate
	 * @param string boxtype
	 * @param string zone nmae on template
	 * @param string order
	 * @return unknown
	 */
	public function processSavePage(){
		$skip = array("module","action","group","Copix");
		$keep = array();
		foreach(CopixRequest::asArray() as $param=>$val){
			if(!in_array($param,$skip)){
				$keep[$param]=str_replace("__COPIX_add_slashes_COPIX__","/",$val);
			}
		}
		//print_r(CopixRequest::asArray());
		$box = _record('moocms_boxes');
		$box->name_moocmspage = CopixRequest::get('pagename');
		$box->date_moocmsbox = CopixRequest::get('pagedate');
		//var_dump(CopixRequest::get('boxdatas'));
		$box->type_moocmsbox = CopixRequest::get('boxtype');
		//$box->params_moocmsbox = "boxid=".CopixRequest::get('id');

		$xml = CopixXMLSerializer::serialize($keep);
		$box->params_moocmsbox = $xml;
		$box->zone_moocmsbox = CopixRequest::get('zone');
		$box->order_moocmsbox = CopixRequest::get('order');
		_ioDAO('moocms_boxes')->insert($box);
		return _arNone();
	}
}
