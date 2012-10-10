<?php
/**
 * @package		copix
 * @subpackage	taglib
 * @author		Gérald Croës
 * @copyright	2000-2006 CopixTeam
 * @link			http://www.copix.org
 * @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagLinkBar {
    public function process($pParams) {
        extract($pParams);
        if (empty ($pageNum)){
        	$pageNum = 1;
        }
        if (empty ($nbTotalPage)){
        	$nbTotalPage = 1;
        }

        if (empty ($url)){
            throw new CopixTemplateTagException("[LinkBar] Missing url parameter");
            return;
        }

        if (empty ($nbLink)){
            $nbLink = 5;
        }

        if ($pageNum <> 1){
            $toReturn='<a href="' . $url . ($pageNum-1) . '">&lt;</a>';
        }
        $nbLinkShow = ($nbTotalPage)>$nbLink ? $nbLink : $nbTotalPage;

        $show = true;
        for ($i=0; $i<$nbLinkShow; $i++){
            $toReturn .= '&nbsp;';

            if (($pageNum+$nbLinkShow/2)>($nbTotalPage)){
                $nextpage =$nbTotalPage+$i-$nbLinkShow+1;
                if (($nextpage)<=($nbTotalPage)){
                    if (($nextpage)==($pageNum)){
                        $toReturn.=$nextpage;
                        if (($nextpage)==($nbTotalPage)){
                            $show=false;
                        }
                    }else{
                        $toReturn.='<a href="' . $url . $nextpage . '">' . $nextpage . '</a>';
                    }
                }
            }else{
                if (($pageNum-$nbLinkShow/2)<=0){
                    $nextpage=$i+1;
                    if (($nextpage)<=($nbTotalPage)){
                        if (($nextpage)==($pageNum)){
                            $toReturn.=$nextpage;
                            if (($nextpage)==($nbTotalPage)){
                                $show=false;
                            }
                        }else{
                            $toReturn.='<a href="' .$url . $nextpage .'">' . $nextpage . '</a>';
                        }
                    }
                }else{
                    $nextpage=round($i+$pageNum-$nbLinkShow/2);
                    if (($nextpage)<=($nbTotalPage)){
                        if (($nextpage)==($pageNum)){
                            $toReturn.=$nextpage;
                            if ($nextpage==($nbTotalPage)){
                                $show = false;
                            }
                        }else{
                            $toReturn.='<a href="' . $url . $nextpage . '">' . $nextpage . '</a>';
                        }
                    }
                }
            }
        }

        if ($show){
            $toReturn .= '&nbsp;<a href="' . $url . ($pageNum+1) . '">&gt;</a>';
        }
        return $toReturn;
    }
}
?>