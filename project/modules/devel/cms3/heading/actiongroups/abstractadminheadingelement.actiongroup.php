<?php
/**
 * @package cms
 * @subpackage heading
 * @copyright CopixTeam
 * @link http://copix.org
 * @license	http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 * @author Gérald Croës
 */

/**
 * ActionGroup qui contient les méthodes de base pour gérer la modification des contenus de rubrique
 *
 * @package cms
 * @subpackage heading
 */
abstract class ActionGroupAbstractAdminHeadingElement extends CopixActionGroup {
    /**
     * La classe de service sur les informations générales a l'élément.
     *
     * @var HeadingElementInformationServices
     */
    protected $_headingElementInformationServices;

    /**
     * Vérification des droits sur l'élément en cours de modification
     *
     * @param string $pActionName le nom de l'action demandée
     */
    protected function _beforeAction ($pActionName) {
        //On est obligé de systématiquement donner le paramètre editId
        CopixRequest::assert ('editId');
        $editId = _request ('editId');

        CopixPage::add ()->setIsAdmin (true);
		if (CopixConfig::get('heading|usecmstheme') && CopixTheme::getInformations(CopixConfig::get('heading|cmstheme')) !==false){
        	CopixTPL::setTheme (CopixConfig::get('heading|cmstheme'));
        } 

        //On ne peut rentrer dans les écrans d'administration spécifiques QUE si heading|element nous en a donné l'ordre
        if (! CopixSession::namespaceExists ($editId)) {
            throw new CopixException ('Vous ne pouvez pas modifier un élément directement. Il est obligatoire de passer par l\'administration des contenus au préalable.');
        }

        //On édite les contenus dans le thème de l'élément courant (ou de sa rubrique pour les nouveaux éléments)
        $this->_headingElementInformationServices = new HeadingElementInformationServices ();
        $public_id = $this->_getPublicId ();

        $theme = $this->_headingElementInformationServices->getTheme ($public_id, $fooParameterIn);
        if ($theme && CopixConfig::get ('heading|useDefinedThemeForAdmin') == 1) {
            CopixTpl::setTheme ($theme);
        }

        //une fois le theme chargé on appel les css
        CopixHTMLHeader::addCSSLink(_resource('heading|css/cms.css'));
        //CopixHTMLHeader::addCSSLink(_resource ('heading|css/mycmscssconfig.css'));

		try {
			$element = $this->_getEditedElement ();
			ZoneHeadingBookmarks::setHeading ($element->parent_heading_public_id_hei);
			ZoneHeadingAdminBreadcrumb::setHeading ($element->parent_heading_public_id_hei);
		} catch (Exception $e) {}

        $this->_headingElementInformationServices->breadcrumbAdmin ();
    }

    /**
     * Retourne l'identifiant public de l'élément en cours de modif, ou du parent si il n'en a pas
     *
     * @return int
     */
    protected function _getPublicId () {
        $editId = _request ('editId');
        if (CopixSession::exists ('id_helt', $editId) && CopixSession::exists ('type_hei', $editId)) {
            return $this->_headingElementInformationServices->getPublicId (CopixSession::get ('id_helt', $editId), CopixSession::get ('type_hei', $editId));
        } else {
            return CopixSession::get ('heading', $editId);
        }
    }

    /**
     * Annulation de la modification en cours
     */
    public function processCancel () {
        $element = $this->_getEditedElement ();
        return _arRedirect (_url ('heading|element|finalizeEdit', array ('editId' => _request ('editId'), 'result'=>'canceled', 'selected' => array ($element->id_helt . '|' . $element->type_hei))));
    }

    /**
     * Vérifie s'il existe un élément en cours de modification dans la session.
     * Si ce n'est pas le cas, lance une exception
     *
     * @return CopixDAORecord
     */
    protected function _getEditedElement () {
        $editId = _request ('editId');
        $type = CopixSession::get ('type_hei', $editId);

        $headingElementType = new HeadingElementType ();
        $typeInformations = $headingElementType->getInformations ($type);

        if (!$element =	CopixSession::get ($type.'|edit|record', _request ('editId'))) {
            throw new CopixException ('Elément de type ' . $typeInformations['caption'].' en cours de modification perdu');
        }
        return $element;
    }

    /**
     * Préparation des données à éditer
     */
    public function processPrepareEdit () {
        //récupération de l'identifiant de modification
        $editId = _request ('editId');

        //Récupération de la classe de service a utiliser
        $type = CopixSession::get ('type_hei', $editId);
        $headingElementType = new HeadingElementType ();
        $typeInformations = $headingElementType->getInformations ($type);

        //on regarde le type d'action que l'on souhaite effectuer (création ou modification)
        if (CopixSession::exists ('id_helt', $editId)) {
            $toEdit = _class ($typeInformations['classid'])->getById (CopixSession::get ('id_helt', $editId));
        } else {
            $toEdit = HeadingElementServices::call ($type, 'getNew');
            $toEdit->parent_heading_public_id_hei = CopixSession::get ('heading', $editId);
        }

        //on met l'information à modifier en session
        CopixSession::set ($type.'|edit|record', $toEdit, $editId);

        //redirection vers l'écran de modification
        return _arRedirect (_url ('admin|edit', array ('editId'=>$editId)));
    }
}