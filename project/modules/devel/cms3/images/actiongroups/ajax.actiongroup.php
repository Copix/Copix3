<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Sylvain Vuidart
 */

/**
 * Actions ajax dans le processus d'édition d'une portlet
 * 
 * @package cms
 * @subpackage images
 */
class ActionGroupAjax extends CopixActionGroup {

	public function processGetImage (){
		$ppo = new CopixPPO ();
		$toReturn = '';
		$portletElement = null;
		$public_id = _request ('id_image');
		$portlet = $this->_getEditedElement ();
		$portlet->setEtat (Portlet::UPDATED);
		$image = $public_id != null ? _ioClass('images|imageservices')->getByPublicId ($public_id) : null;

		//on ajoute une image à la portlet, on renvoie le contenu de l'image en fonction des options
		if($public_id != null){
			$options = CopixRequest::asArray(
				"title_image", 
				"alt_image",
				'classe_image',
				'style_image',
				'align_image',
				'thumb_width',
				'thumb_height',
				'thumb_enabled',
				'thumb_keep_proportions',
				'thumb_show_image',
				'thumb_galery_id',
				'vspace',
				'hspace',
				'legend_image',
				'link'
			);

			$listeImage = array();
			if ($image->type_hei == "heading"){
				$results = _ioClass('heading|headingelementinformationservices')->getChildrenByType ($public_id, 'image');
				$children = _ioClass('heading|headingelementchooserservices')->orderChildren ($results);
				foreach ($children as $child){
					$listeImage[] = _ioClass('images|imageservices')->getByPublicId ($child->public_id_hei);
				} 				
			} else {
				$listeImage[] = $image;			
				$width = "";
				$height = "";
				$oImage = CopixImage::load (COPIX_VAR_PATH.ImageServices::IMAGE_PATH . $image->file_image);
				if ($oImage != null) {
					$width = $oImage->getWidth ();
					$height = $oImage->getHeight();
				}
				$options['thumb_width'] = isset($options['thumb_width']) && $options['thumb_width'] ? $options['thumb_width'] : $width;
				$options['thumb_height'] =  isset($options['thumb_height']) && $options['thumb_height'] ? $options['thumb_height'] : $height;
			}
			
			$tpl = new CopixTpl();
			$tpl->assign('listeImage', $listeImage);
			$tpl->assign('options', $options);
			$tpl->assign('identifiantFormulaire', _request('formId'));
			$toReturn = $tpl->fetch('imageformadminview'._request('editionMode').'.php');
				
			//si l'image n'est pas encore ajouté
			if(($portletElement = $portlet->getPortletElementAt (_request('position'))) == null){
				$portletElement = $portlet->attach ($image->public_id_hei);
			}
			$portletElement->setOptions ($options);
		}
		
		//si oldImage = new Image on est en modification on ne supprime pas, sinon on supprime
		if ($portletElement == null || ($portletElement != null && $portletElement->getHeadingElement ()->public_id_hei != $public_id)){
			if($image == null){
				try{
					$portlet->dettach (_request('position'));
				}catch (CopixException $e){
					//si on arrive ici c'est qu'on a voulu faire des modifs dans les options alors qu'on n'a pas selectionné 
					CopixLog::log($e->getMessage(), 'errors', CopixLog::EXCEPTION);
				}
			}
			else{
				$portletElement->setHeadingElement ($image);
			}
		}
		
		if ($public_id == null && count($portlet->getElements()) == 0) {
			$toReturn = "<a href='javascript:void(0)' id='imgClicker"._request('formId')."' ><img src='"._resource('images|img/choisirimage.png')."' /></a>";	
			$toReturn .= "<script>$('imgClicker"._request('formId')."').addEvent('click', function(){ $('clicker"._request('formId')."').fireEvent('click');});</script>";
		}
		
		$ppo->MAIN = $toReturn;
		return _arDirectPPO ($ppo, 'generictools|blank.tpl');
	}
	
	public function processAddEmptyImage (){
		$lite = _request('islite') ;
		$portlet = $this->_getEditedElement ();
		$identifiantFormulaire = $portlet->getRandomId ()."_pos_"._request ('idImageVide');
		//on renvoie le template en indiquant un newImageVide : on n'affichera pas la div qui contient l'image => deja cree par l'appel javascript
		$tpl = new CopixTpl ();		
		$tpl->assign ('portlet', $portlet);
		$tpl->assign ('editionMode', _request('editionMode'));
		$tpl->assign ('newImageVide', true);
		$tpl->assign ('position', _request ('position'));
		$tpl->assign ('justAddImage', false);
		
		if($lite !== 'true'){
			$toReturn = $tpl->fetch ('images|portletimage.form.php');
		}else{
			$toReturn = $tpl->fetch ('images|portletdiaporama.form.php');
		}		
		
		$ppo = new CopixPPO ();
		$ppo->MAIN = $toReturn;
		return _arDirectPPO ($ppo, 'generictools|blank.tpl');
	}
	
	/**
	 * Retourne la page en cours d'edition
	 *
	 * @return Page
	 */
	protected function _getEditedElement (){
		CopixRequest::assert ('editId');
		$portlet = CopixSession::get('portal|'._request ('portletId'), _request('editId'));
		if ($portlet == null){
			$portlet = CopixSession::get('portlet|edit|record', _request('editId'));			
		}
		if (!$portlet){
			throw new CopixException ('Portlet en cours de modification perdu');
		}
		return $portlet;
	}
	
	public function processGetProportion (){		
		$image_id = _request("id_image");
		$image = _ioClass ('image|imageservices')->getPathByPublicId ($image_id);
        $size = GetImageSize($image);

        $axe = _request('axe');
        $valeur = _request('valeur');
        $dimension = array($axe=>$valeur);
		
        $src_w = $size[0];
        $src_h = $size[1];
        
        //par défaut les tailles originales
        $dst_w = $src_w;
        $dst_h = $src_h;
        
		if (isset ($dimension['height'])){
            $dst_h = $dimension['height'];
            $dst_w = ($dimension['height']/$src_h) * $src_w;
        }else if (isset ($dimension['width'])){
            $dst_w = $dimension['width'];
            $dst_h = ($dimension['width']/$src_w) * $src_h;
        }
        
        $ppo = new CopixPPO();
        $ppo->MAIN = "<script>
        				$('"._request('toUpdate')."').value=".((isset ($dimension['width'])) ? $dst_h : $dst_w).";
        			  </script>";
		return _arDirectPPO($ppo, 'generictools|blanknohead.tpl');
	}
	
	public function processAddImage (){
		$id = _request ('id_image');	
		$lite = _request('islite') ;
		$ppo = new CopixPPO ();
		try{
			$image = _class ('images|imageservices')->getByPublicId ($id);
			if($this->_getEditedElement () instanceof Page){
				$portlet = CopixSession::get('portal|'._request ('portletId'), _request('editId'));
				if ($portlet== null){
					$portlet = $this->_getEditedElement ()->findPortletById (_request('portletId'));
				}
			}else{
				$portlet = $this->_getEditedElement ();
			}
			
			$portlet->setEtat (Portlet::UPDATED);
			
			//on ajoute un image à la portlet, on renvoie le contenu de l'image en fonction des options
			if($id != null){
				$options = array (
								'thumb_width'=>"",
								'thumb_height'=>"",
								'classe_image'=>_request('classe_image'),
								'template'=> ''
							);
				if($lite === 'true') {
					$options['islite'] = true;
				}	
				$tpl = new CopixTpl();
				$tpl->assign('listeImage', array($image));
				$tpl->assign('identifiantFormulaire', _request('formId'));
				$toReturn = $tpl->fetch('imageformadminview'._request('editionMode').'.php');
				//si le image n'est pas encore ajouté
				if(($portletElement = $portlet->getPortletElementAt (_request('position'))) == null){
					$portletElement = $portlet->attach ($image->id_hei, _request('position'));
				}
				$portletElement->setOptions ($options);
			}
			
			//si oldImage = new Image on est en modification on ne supprime pas, sinon on supprime
			if ($portletElement == null || ($portletElement != null && $portletElement->getHeadingElement ()->id_hei != _request ('id_image'))){
				if($image == null){
					$portlet->dettach (_request('position'));
				}
				else{
					$portletElement->setHeadingElement ($image);
				}
			}
		
			//$portlet = $this->_getEditedElement ()->findPortletById (_request('portletId'));
			//on renvoie le template en indiquant un newDocumentVide : on n'affichera pas la div qui contient le document => deja cree par l'appel javascript
			$tpl = new CopixTpl ();		
			$tpl->assign ('editionMode', _request('editionMode'));
			$tpl->assign ('portlet', $portlet);
			$tpl->assign ('position', _request ('position'));
			$tpl->assign ('justAddImage', false);
			$tpl->assign ('image', $image);
			$tpl->assign ('newImageVide', true);
					
			if($lite !== 'true'){
				$toReturn = $tpl->fetch ('images|portletimage.form.php');
			}else{
				$toReturn = $tpl->fetch ('images|portletdiaporama.form.php');
			}	
			$ppo->MAIN = $toReturn;	
		}catch (HeadingElementInformationNotFoundException $e){
			$ppo->MAIN = '';
		}	
		return _arDirectPPO ($ppo, 'generictools|blank.tpl');
	}

	public function processUpdateView (){
		$portlet = $this->_getEditedElement ();
		$xmlPath = $portlet->getXmlPath();
		$editionMode = _class('portal|templateservices')->getInfos ($xmlPath, _request('template'))->editionMode;
		
			$portlet->setEtat (Portlet::UPDATED);
			
			$toReturn = $portlet->getUpdateRender ($editionMode);

			$ppo = _ppo();
			$ppo->MAIN = $toReturn;	
			return _arDirectPPO ($ppo, 'generictools|blank.tpl');
	
	}
}