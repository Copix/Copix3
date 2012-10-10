<?php
/**
 * @package		bugtrax
 * @author		Patrice Ferlet
 * @copyright	2001-2008 CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package		devtools
 * @subpackage	bugtrax
 */
class BugServices {

	/**
	 * Fonction de récupération de la liste des développeurs
	 */
	public function getDeveloppers(){
		$group = _ioDao('dbgroup')->findBy(_daoSp()->addCondition('caption_dbgroup','=',CopixConfig::get('bugtrax|devgroup')));

		// On a un group de développeurs
		if (isset ($group[0])) {
			$developpers= _ioDao('dbgroup_users')->findBy(_daoSp()->addcondition('id_dbgroup', '=', $group[0]->id_dbgroup));
			$usersdao = _ioDao('dbuser');
			$sp = _daoSp();
			foreach($developpers as $dev){
				$sp->addCondition('id_dbuser','=',$dev->user_dbgroup,'or');
			}
			return $usersdao->findBy($sp);
		}

		return array();

	}

	public function getDevelopperById($id){
		$res = _ioDao('dbuser')->findBy(_daoSp()->
		addCondition('id_dbuser','=',$id));
		return $res[0];
	}

	public function assignColors(){
		$severities = explode(";",CopixConfig::get('bugtrax|severity'));
		$numdeg = count($severities);
		$starthex  = 'dbffce';
		$endhex  = 'ff4e45';

		//$grads = self::gradient($starthex,$endhex,$numdeg);
		preg_match('/(.{2})(.{2})(.{2})/',$starthex,$start);
		preg_match('/(.{2})(.{2})(.{2})/',$endhex,$end);
		$reds = self::gradient(hexdec($start[1]),hexdec($end[1]),$numdeg);
		$greens = self::gradient(hexdec($start[2]),hexdec($end[2]),$numdeg);
		$blues = self::gradient(hexdec($start[3]),hexdec($end[3]),$numdeg);

		$colors = array();
		$severities = array_reverse($severities);
		for ($i=0;$i<$numdeg;$i++){
			$col = "#";
			$col.=$reds[$i];
			$col.=$greens[$i];
			$col.=$blues[$i];
			$colors[$severities[$i]]=$col;
		}
		return($colors);
	}


	private function gradient($val1,$val2,$step){
		$min=min($val1,$val2);
		$max=max($val1,$val2);
		$diff = $max - $min;
		$quo = round($diff/$step);
		$arr = array(dechex($min));
		for($i=1;$i<$step-1;$i++){
			$v = $min + (($i+1)*$quo);
			$arr[]=dechex($v);
		}
		$arr[]=dechex($max);
		return $arr;
	}
}