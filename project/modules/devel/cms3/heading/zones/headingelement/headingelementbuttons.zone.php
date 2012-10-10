<?php
/**
 * @package cms
 * @subpackage heading
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 */

/**
 * Affiche la zone d'actions (sauvegarder, publier, annuler, etc) lors de l'édition d'un élément
 *
 * @package cms
 * @subpackage heading
 */
class ZoneHeadingElementButtons extends CopixZone {
	/**
	 * Création du contenu
	 *
	 * @param string $pToReturn HTML à retourner
	 * @return boolean
	 */
	protected function _createContent (&$pToReturn) {
		$actions = $this->getParam ('actions');
		if (!is_array ($actions) || count ($actions) == 0) {
			throw new CopixException ('Vous devez indiquer au moins une action via le paramètre actions.');
		}
		$buttons = array ();
		$pToReturn = "";
		$form = $this->getParam ('form');
		CopixHTMLHeader::addJSCode ("var mutexPortal = null;");
		CopixHTMLHeader::addJSDOMReadyCode ("mutexPortal = new Mutex ();");
		
		foreach ($actions as $action) {
			// action personnalisée
			if (is_array ($action)) {
				$buttons[] = $action;
			// action raccoucie
			} else {
				switch ($action) {
					case 'savedraft' :
						$buttons[] = array ('img' => 'img/tools/save.png', 'caption' => 'Enregistrer un brouillon', 'id' => 'actionSaveDraft', 'type' => 'button');					
						CopixHTMLHeader::addJSDOMReadyCode ("
							var onClickSaveDraft =  function () { $ ('" . $form . "').submit ();}
							$ ('actionSaveDraft').addEvent ('click', function () { 
								mutexPortal.executeFunction(onClickSaveDraft);
							});			
						");
						break;
					case 'savepublish' :
						$buttons[] = array ('img' => 'heading|img/actions/publish.png', 'caption' => 'Enregistrer et publier', 'id' => 'actionSavePublish', 'type' => 'button');
						CopixHTMLHeader::addJSDOMReadyCode ("
							var onClickSavePublish =  function () { 
								$ ('publish').value = '1'; 
								$ ('" . $form . "').submit (); 
							}							
							$ ('actionSavePublish').addEvent ('click', function () { 
								mutexPortal.executeFunction(onClickSavePublish);
							});
						");
						break;
					case 'saveplanned' :
						$element = $this->getParam('element');
						$buttons[] = array ('img' => 'heading|img/actions/planned.png', 'caption' => 'Publication différée', 'id' => 'actionSavePlanned', 'type' => 'button');
						$pToReturn = CopixZone::process('heading|scheduler', array('clicker'=>'actionSavePlanned', 'published_date'=>isset($element->published_date_hei) ? $element->published_date_hei : '', 'end_published_date'=>isset($element->end_published_date_hei) ? $element->end_published_date_hei : ''));
						CopixHTMLHeader::addJSDOMReadyCode ("
							var onClickSavePlanned =  function () { 
								$ ('published_date').value = $('scheduler_published_date').value ? $('scheduler_published_date').value + ' ' + $('scheduler_published_hour').value + ':' + $('scheduler_published_minute').value + ':00' : ''; 
								$ ('end_published_date').value = $('scheduler_end_published_date').value ? $('scheduler_end_published_date').value + ' ' + $('scheduler_end_published_hour').value  + ':' + $('scheduler_end_published_minute').value  + ':00': '';
								$ ('" . $form . "').submit (); 
							}							
							$ ('actionPlanned').addEvent ('click', function () { 
								mutexPortal.executeFunction(onClickSavePlanned);
							});
						");
						break;
					case 'next' :
						$buttons[] = array ('img' => 'img/tools/next.png', 'caption' => 'Suivant', 'id' => 'actionNext', 'type' => 'button');
						CopixHTMLHeader::addJSDOMReadyCode ("
							var onClickNext =  function () { $ ('" . $form . "').submit ();}
							$ ('actionNext').addEvent ('click', function () { 
								mutexPortal.executeFunction(onClickNext);
							});		
							");
						break;
					case 'preview' :
						$buttons[] = array ('img' => 'img/tools/show.png', 'caption' => 'Aperçu', 'id' => 'apercu', 'type' => 'button');
						CopixHTMLHeader::addJSDOMReadyCode ("
							var onClickApercu = function () { 
								$ ('preview').value = '1'; 
								$ ('" . $form . "').submit (); 
							}
							$ ('apercu').addEvent ('click', function () { 
								mutexPortal.executeFunction(onClickApercu);
							});									
							");
						break;
					case 'cancel' :
						$buttons[] = array (
							'img' => 'img/tools/cancel.png',
							'caption' => 'Annuler',
							'id' => 'actionCancel',
							'url' => _url (_request ('module') . '|admin|cancel', array ('editId' => _request ('editId'))),
							'confirm' => 'Etes-vous sur de vouloir annuler ? Toutes les modifications en cours seront perdues.'
						);
						break;
					default :
						throw new CopixException ('L\'action "' . $action . '" n\'existe pas.', 0, array ('$pParams' => $this->getParams ()));
				}
			}
		}

		$tpl = new CopixTPL ();
		$tpl->assign ('buttons', $buttons);
		$tpl->assign ('showBack', $this->getParam ('showBack', true));
		$tpl->assign ('backUrl', $this->getParam ('backUrl', _url (_request ('module') . '|admin|cancel', array ('editId' => _request ('editId')))));
		$pToReturn .= $tpl->fetch ('heading|elementeditactions.php');
		return true;
	}
}