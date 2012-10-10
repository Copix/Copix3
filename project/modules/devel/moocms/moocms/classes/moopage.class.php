<?php
/**
 * Services for MooPages
 * 
 * @package MooCMS
 * @subpackage MooCMS
 * @copyright   CopixTeam
 * @link        http://copix.org
 * @license     http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * 
 */
class MooPage {

    public function getBoxInfos($title){
        $page = _ioDao('moocms_pages')->findBy(_daoSP()
                ->addCondition('name_moocmspage','=',$title)
                ->orderBy(array('date_moocmspage','DESC'))
                );

        $page = $page[0];
        //var_dump($page);
        $mooboxes = _ioDao('moocms_boxes')->findBy(_daoSP()
                ->addCondition('date_moocmsbox','=',$page->date_moocmspage)
                ->addCondition("name_moocmspage","=",$page->name_moocmspage)
                ->orderBy('order_moocmsbox')
                );

        $pParams = array();
        foreach($mooboxes as $box){
            //split params
            $params = CopixXMLSerializer::unserialize($box->params_moocmsbox);
            $box->other = $params;
        }
        return $mooboxes;
    }

    public function getPage($title){
        $page = _ioDao('moocms_pages')->findBy(_daoSP()
                ->addCondition('name_moocmspage','=',$title)
                ->orderBy(array('date_moocmspage','DESC'))
                );
        //var_dump($page);
        $page = $page[0];

        $mooboxes = _ioDao('moocms_boxes')->findBy(_daoSP()
                ->addCondition('date_moocmsbox','=',$page->date_moocmspage)
                ->addCondition("name_moocmspage","=",$page->name_moocmspage)
                ->orderBy('order_moocmsbox')
                );
        //var_dump($mooboxes);
        $tpl = new CopixTpl();
        CopixClassesFactory::fileInclude('moobox');
        $params = array();
        $content = array();
        foreach($mooboxes as $box){
            //split params
            //$params = explode('&',$box->params_moocmsbox);
            $pParams = CopixXMLSerializer::unserialize($box->params_moocmsbox);
            //var_dump($pParams);
            foreach($pParams as $key=>$val){
                $params[$key] = $val;
            }
            //var_dump($params);
            $boxname = "moobox_".$params['boxtype'];
            $classname = "moobox".$params['boxtype'];
            //var_dump(_ioClass($boxname.'|'.$classname)->getContent($pParams));
            $zone = $params['zone'];

            if(!isset($content[$zone])) $content[$zone] = "";
            $content[$zone] .= "<div class=\"webBox\" id=\"".$params['id']."\">"._ioClass($boxname.'|'.$classname)->getContent($params)."</div>"; 
        }
        foreach($content as $zonename=>$c){
            $tpl->assign($zonename,$c);         
        }

        $main = new CopixTpl();
        return $tpl->fetch('mootpl|'.$page->template_moocmspage);
    }



}
