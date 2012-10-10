<?php
/**
* @package	cms
* @subpackage pictures
* @author	Bertrand Yan
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
require_once (COPIX_UTILS_PATH.'CopixPager.class.php');

/**
* @package	cms
* @subpackage pictures
* Zone d'affichage du browser d'images.
*/
class ZonePicturesBrowser extends CopixZone {
    function _createContent (&$toReturn){
        $tpl = & new CopixTpl ();

        //Creation des DAO
        $daoPicture         = CopixDAOFactory::getInstanceOf ('pictures');
        $daoPictureThemes   = CopixDAOFactory::getInstanceOf ('picturesthemes');
        $daoPictureHeadings = CopixDAOFactory::getInstanceOf ('picturesheadings');
        $daoPictureLinkTheme= CopixDAOFactory::getInstanceOf ('pictureslinkthemes');
        $workflow           = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

        //Creation du paramètre de recherche
        $sp                 = CopixDAOFactory::createSearchParams ();
        $spTheme            = CopixDAOFactory::createSearchParams ();

        /*
        * ajout des critères
        * a chaque fois test du paramètres pour savoir si le critère à été renseigné/choisit
        */
        if (($tabIdCat = $this->_params['searchParams']->category) !== array()){
        	$tabIdCat[]=null;
            $sp->addCondition ('id_head', '=', $tabIdCat);
        }

        //theme
        if (($tabIdTheme = $this->_params['searchParams']->theme) !== array()){
            //on recherche celles qui ont les themes spécifiés
            $spTheme->addCondition ('id_tpic', '=', $tabIdTheme);
        }else{
            if (count($arTheme = $this->_getAllowedThemes ()) > 0) {
                $spTheme->addCondition ('id_tpic', '=', $arTheme);
            }else{
                $spTheme->addCondition ('id_tpic', '=', -1);
            }
        }
        $tabIdPict = $daoPictureLinkTheme->findBy ($spTheme);
        $tab = array ();
        foreach ($tabIdPict as $object){
            $tab[] = $object->id_pict;
        }
        if (count ($tab)){
            $sp->addCondition ('id_pict','=',$tab);
        }else{
            $sp->addCondition ('id_pict','=',-1);
        }

        //mot clé
        if ($this->_params['searchParams']->keyWord !== ''){
            $sp->addCondition ('name_pict', 'like', '%'.$this->_params['searchParams']->keyWord.'%');
        }

        //format
        if ($this->_params['searchParams']->format != 'all'){
            $sp->addCondition ('format_pict', '=', $this->_params['searchParams']->format);
        }
        //poid max
        if (($this->_params['searchParams']->maxWeight != 0)&&($this->_params['searchParams']->maxWeight !== null)){
            $sp->addCondition ('weight_pict', '<', $this->_params['searchParams']->maxWeight);
        }
        //largeur max
        if (($this->_params['searchParams']->maxWidth != 0)&&
        ($this->_params['searchParams']->maxWidth !== null)){
            $sp->addCondition ('x_pict', '<', $this->_params['searchParams']->maxWidth);
        }
        //hauteur max
        if (($this->_params['searchParams']->maxHeight != 0)&&
        ($this->_params['searchParams']->maxHeight !== null)){
            $sp->addCondition ('y_pict', '<', $this->_params['searchParams']->maxHeight);
        }
        //on n'affiche que les images validées/Publiées
        $sp->addCondition ('status_pict', '=', $workflow->getPublish());
        $sp->orderBy ('name_pict');

        //recherche des images selon les critères
        var_dump($sp);
        $arPictures = $daoPicture->findBy ($sp);        
        if (count($arPictures)>0) {
            $totalPages = intval($this->_params['searchParams']->rows)*intval($this->_params['searchParams']->cols);
            $params = Array(
            'perPage'    => $totalPages,
            'delta'      => 5,
            'recordSet'  => $arPictures,
            );
            $pager = CopixPager::Load($params);
            $tpl->assign ('pager'    , $pager->GetMultipage());
            $tpl->assign ('pictures' , $pager->data);
        }


        //envoie de tous les themes, categories et formats
        $sp2 = CopixDAOFactory::createSearchParams ();
        $sp2->orderBy ('name_tpic');
        $tpl->assign('themesList', $daoPictureThemes->findBy ($sp2));

        //$tpl->assign('catList'   , $this->compressCategoryProfile ($daoPictureHeadings->findAll (), PROFILE_CCV_READ));
        $tpl->assign('catList'   , $daoPictureHeadings->findAllWithCaptionHead ());
        $tpl->assign('formatList', explode (';', CopixConfig::get ('pictures|format')));

        //envoie des paramètres
        $tpl->assign ('searchParams' , $this->_params['searchParams']);
        $tpl->assign ('maxX'         , $this->_params['maxX']);
        $tpl->assign ('maxY'         , $this->_params['maxY']);
        $tpl->assign ('popup'        , $this->_params['popup']);
        $tpl->assign ('select'       , $this->_params['select']);
        $tpl->assign ('back'         , $this->_params['back']);
        $tpl->assign ('id_head'      , $this->_params['id_head']);

        if (isset($this->_params['id_head'])) {
            $servicesHeading = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
            $tpl->assign ('contribEnabled' , CopixUserProfile::valueOfIn ('pictures', $servicesHeading->getPath ($this->_params['id_head'])) >= PROFILE_CCV_WRITE);
        }

        $toReturn = $tpl->fetch ('browser.tpl');
        return true;
    }

    function _getAllowedThemes () {
        $daoPictureThemes   = CopixDAOFactory::getInstanceOf ('picturesthemes');
        $sp 	            = CopixDAOFactory::createSearchParams ();
        $sp->orderBy ('name_tpic');
        $toReturn = array();
        $arTheme  = $daoPictureThemes->findBy ($sp);
        foreach ($arTheme as $theme){
            $toReturn[] = $theme->id_tpic;
        }
        return $toReturn;
    }
}
?>