<?php
/**
 * @package tools
 * @subpackage bayes
 * @author Patrice Ferlet - <metal3d@copix.org>
 * @copyright CopixTeam
 * @licence GNU/GPL
 */

interface IBayesMethods {
	public function train($pCat,$pText);
	public function untrain($pCat,$pText);
	public function getProba($A,$B);
	public function setCategoriesProbas();
}

/**
 * CopixBayes Class
 * Used to get Bayesian values of probabilities
 *
 * @package tools
 * @subpackage bayes
 */
class BayesFactory {

	/**
	 * Get Bayes object from datamode
	 *
	 * @param const $datamode
	 * @param string $dataset name
	 * @param string $connectionname
	 * @return Bayes object cast for datamode
	 */
	public function create($datamode = Bayes::STATIC_DATAMODE,$dataset=null,$connectionname=null){
		if($datamode == Bayes::DB_DATAMODE){
			$b = new DBBayes();
		}
		else{
			$b = new StaticBayes();
		}
		$b->setDataMode($datamode,$dataset,$connectionname);
		return $b;
	}

}


abstract class Bayes implements IBayesMethods {
	/**
	 * Array of categories
	 *
	 * @var array
	 */
	protected $categories = array();
	
	/**
	 * Weight for all categories, in fact it's the whole data count
	 *
	 * @var integer
	 */
	protected $numcat=0;
	
	/**
	 * Bayes mode, database or static
	 *
	 * @var const int
	 */
	protected $mode=self::STATIC_DATAMODE;
	
	/**
	 * Dataset name for database mode
	 *
	 * @var string
	 */
	protected $dataset="";
	
	/**
	 * Static mode
	 *
	 */
	const  STATIC_DATAMODE = 0;
	
	/**
	 * Database mode
	 *
	 */
	const  DB_DATAMODE = 1;
	
	/**
	 * Connection name to strore dataset
	 *
	 * @var string
	 */
	protected $connectionName = null;
	
	/**
	 * True if calcul is done
	 *
	 * @var boolean
	 */
	private $calculated = false;
	
	/**
	 * Human readable operation done
	 *
	 * @var string
	 */
	private $operation = "";
	
	/**
	 * Constructor
	 *
	 */
	public function __construct(){
		$this->setDataMode();
	}
	

	/**
	 * Set the data mode
	 * @param string $dataset_name
	 * @param string $mode Bayes::STATIC_DATAMODE or Bayes::DB_DATAMODE
	 * @param string connection_name
	 */
	public function setDataMode($pMode=self::STATIC_DATAMODE ,$pDataset=null,$pConnectionName=null){
		if(empty($pDataset) && $pMode==self::DB_DATAMODE){
			throw (new CopixException("No dataset given for CopixBayesian datas used with database"));
		}
		$this->dataset="bayesiantable_".$pDataset;
		$this->mode = ($pMode==self::DB_DATAMODE) ? self::DB_DATAMODE :self::STATIC_DATAMODE;
		
		//get tables
		if($this->mode==self::DB_DATAMODE){
			$this->connectionName = $pConnectionName;
			$ct = CopixDB::getConnection($pConnectionName);
			if(!in_array($this->dataset,$ct->getTableList())){
				if($ct instanceof CopixDBConnectionMySQL || $ct instanceof CopixDBConnectionPDO_MySQL){
					$sql = CopixFile::read(dirname(__FILE__)."/../install/template_scripts/install.pdo_mysql.sql");
				}
				elseif($ct instanceof CopixDBConnectionPDO_SQLite){
					$sql = CopixFile::read(dirname(__FILE__)."/../install/template_scripts/install.pdo_sqlite.sql");
				}
				elseif($ct instanceof CopixDBConnectionPDO_PgSQL){
					$sql = CopixFile::read(dirname(__FILE__)."/../install/template_scripts/install.pdo_pgsql.sql");
				}
				else{
					//throw new CopixException("Data type: ".get_class($ct)." not currently supported");
				}
				$sql = str_replace('%TABLENAME%',$this->dataset,$sql);
				_doQuery($sql,array(),$pConnectionName);
			}
		}

	}



	/**
	 * Get the Bayesian value for a category
	 *
	 * @param string $category
	 * @param string $data_to_test
	 * @param boolean $naive to give same chance for each category
	 * @return float $bayesian_value (in percent %)
	 */
	public function getBayes($B,$A,$pNaive=false){
		$A = strtolower($A);
		$this->setCategoriesProbas();
		//P(B|A)
		//numerator:  P(B) * P(A|B)...
		//P(B) => getCategoriesProbas for B
		//P(A|B) => find A in B
		if(!isset($this->categories[$B])){
			return 0;
		}
		if(!$pNaive){
			$PB = $this->categories[$B]->percent;
		} else {
			$PB = (100/count($this->categories));
		}
		$PAB = $this->getProba($A,$B);
		$numerator = $PB * $PAB;
		$this->operation = $PB.'*'.$PAB;
		$this->operation .="\n";
		//denominator: ( P(B)*(P(A|B) + P(B2)*P(A|B2) + ... )
		//so for every categories, we have to check
		$den = 0;
		foreach($this->categories as $name=>$B){
			if(!$pNaive){
				$PB = $B->percent;
			}
			$PAB = $this->getProba($A,$name);
			$den += ($PB*$PAB);
			$this->operation.= '+('.$PB.'*'.$PAB.')';
		}

		if($den==0) return 0;
		$this->calculated = true;
		return 100*$numerator/$den;
	}


	public function getOperation(){
		if(!$this->calculated){
			throw new CopixException("Calcul is not done yet");
		}
		$op = $this->operation;
		list($num,$den) = explode("\n",$this->operation);
		$den = preg_replace('/^\+/','',$den);
		return '<table>
			<tr>
			<td style="text-align: center; border-bottom: 1px solid #000">'.$num.'</td>
			</tr>
			<tr><td "text-align: center;">'.$den.'
			</tr></td>
</table>';
	}


	/**
	 * Prepare and split text to be registered into dataset
	 *
	 * @param strign $pText
	 * @return array of strings
	 */
	protected function prepareText ($pText){
		$text = $this->remove_accents($pText);
		$t=preg_split('/\W/is',$text);
		$texts=array();
		foreach ($t as $text){
			if(strlen(trim($text))>0){
				$texts[]=strtolower(trim($text));
			}
		}
		return $texts;
	}

	/**
	 * By derernst at gmx dot ch: http://fr3.php.net/manual/fr/function.strtr.php#56973
	 */
	protected function remove_accents($pString, $german=false) {
		// Single letters
		//$string=utf8_encode($string);
		$single_fr = explode(" ", "À Á Â Ã Ä Å &#260; &#258; Ç &#262; &#268; &#270; &#272; Ð È É Ê Ë &#280; &#282; &#286; Ì Í Î Ï &#304; &#321; &#317; &#313; Ñ &#323; &#327; Ò Ó Ô Õ Ö Ø &#336; &#340; &#344; Š &#346; &#350; &#356; &#354; Ù Ú Û Ü &#366; &#368; Ý Ž &#377; &#379; à á â ã ä å &#261; &#259; ç &#263; &#269; &#271; &#273; è é ê ë &#281; &#283; &#287; ì í î ï &#305; &#322; &#318; &#314; ñ &#324; &#328; ð ò ó ô õ ö ø &#337; &#341; &#345; &#347; š &#351; &#357; &#355; ù ú û ü &#367; &#369; ý ÿ ž &#378; &#380;");
		$single_to = explode(" ", "A A A A A A A A C C C D D D E E E E E E G I I I I I L L L N N N O O O O O O O R R S S S T T U U U U U U Y Z Z Z a a a a a a a a c c c d d e e e e e e g i i i i i l l l n n n o o o o o o o o r r s s s t t u u u u u u y y z z z");
		$single = array();
		for ($i=0; $i<count($single_fr); $i++) {
			$single[$single_fr[$i]] = $single_to[$i];
		}
		// Ligatures
		$ligatures = array("Æ"=>"Ae", "æ"=>"ae", "Œ"=>"Oe", "œ"=>"oe", "ß"=>"ss");
		// German umlauts
		$umlauts = array("Ä"=>"Ae", "ä"=>"ae", "Ö"=>"Oe", "ö"=>"oe", "Ü"=>"Ue", "ü"=>"ue");
		// Replace
		$replacements = array_merge($single, $ligatures);
		if ($german) $replacements = array_merge($replacements, $umlauts);
		$pString = strtr($pString, $replacements);
		return $pString;
	}



	/**
	 * Add category to the data set
	 *
	 * @param unknown_type $cat
	 */
	protected function addCategory ($pCat){
		$this->categories[$pCat]=new stdClass;
		$this->categories[$pCat]->texts=array();
		$this->categories[$pCat]->counter=0;
		$this->categories[$pCat]->percent = 0;
	}
}

/**
 * StaticBayes is a "one shot" class to process datas and
 * get bayesian probabilities
 *
 */
class StaticBayes extends Bayes  {

	/**
	 * Prepare categories
	 *
	 */
	public function setCategoriesProbas(){
		foreach($this->categories as $name=>$cat){			
			$cat->percent = $cat->counter * 100 / $this->numcat;			
		}
		
	}

	/**
	 * Get probability of A is in B
	 *
	 * @param string $category  category name
	 * @param string $data_to_check  data to check
	 * @return float $proba (in percent %)
	 */
	public function getProba($A,$B){
		$A = $this->remove_accents($A);
		$A = preg_split('/\W/is',$A);
		$numwords = 0;
		$found = 0;
		foreach($this->categories[$B]->texts as $words){
			$numwords += count($words);
			foreach($words as $word){
				foreach($A as $find){
					if($find==$word){
						$found++;
					}
				}
			}
		}
			
		return $found * 100 / $numwords;
	}

	/**
	 * Add/update category and datas for this category
	 *
	 *
	 * @param string $category
	 * @param string $text
	 */
	public function train($pCat,$pText){
		$texts = $this->prepareText($pText);
		
		if(!isset($this->categories[$pCat])) $this->addCategory($pCat);
		
		$this->categories[$pCat]->texts[]=$texts;
		$this->categories[$pCat]->counter+=count($texts);
		$this->numcat+=count($texts);

	}

	/**
	 * Untrain/remove the data from dataset
	 *
	 * @param string $categoryname
	 * @param string $text
	 */

	public function untrain($pCat,$pText){
		$texts = $this->prepareText($pText);

		if(isset($this->categories[$pCat]) && isset($this->categories[$pCat]->texts)){
			$i=0;
			foreach($this->categories[$pCat]->texts as $t){
				if($texts == $t){					
					//remove one word for numcat:
					$this->numcat--;
					$this->categories[$cat]->counter--;	
					unset($this->categories[$pCat]->texts[$i]);
					break;
				}
				$i++;
			}
			if(count($this->categories[$pCat]->texts)<1){
				unset($this->categories[$pCat]);
			}
		}

	}

}


/**
 * DBBayes store datas into database
 *
 */
class DBBayes extends Bayes {

	/**
	 * Prepare categories
	 *
	 */
	public function setCategoriesProbas(){
		//categories counter
		$cats = _doQuery('select distinct category_bayes from '.$this->dataset,array(),$this->connectionName);
		
		//words counter
		$acount = _doQuery("select sum(numdatas_bayes) count from ".$this->dataset,array(),$this->connectionName);
				
		foreach($cats as $cat){
			$this->addCategory($cat->category_bayes);
			$count = _doQuery("select sum(numdatas_bayes) count from ".$this->dataset." where category_bayes='".$cat->category_bayes."'",array(),$this->connectionName);
			$this->categories[$cat->category_bayes]->percent= $count[0]->count * 100 / $acount[0]->count;
			
			$name=$cat->category_bayes;
		}
	}

	/**
	 * Get probability of A is in B
	 *
	 *
	 * @param string $category  category name
	 * @param string $data_to_check  data to check
	 * @return float $proba (in percent %)
	 */
	public function getProba($A,$B){
		$A = $this->remove_accents($A);
		$A = preg_split('/\W/is',$A);
		$numwords = 0;
		$found = 0;

		$numwords = _doQuery('select sum (numdatas_bayes) numwords from '.$this->dataset.' where category_bayes="'.$B.'"',array(),$this->connectionName);
		$numwords = $numwords[0]->numwords;
		foreach($A as $find){
			$sets = _doQuery("select numdatas_bayes from ".$this->dataset." where category_bayes=\"$B\" AND datas_bayes ='$find'",array(),$this->connectionName);
			foreach($sets as $set){
				$found += $set->numdatas_bayes;
			}
		}
		return $found * 100 / $numwords;
	}

	/**
	 * Add/update category and datas for this category
	 *
	 *
	 * @param string $category
	 * @param string $text
	 */
	public function train($pCat,$pText){
		$texts = $this->prepareText($pText);
		foreach ($texts as $word){
			$res = _ioDao($this->dataset,$this->connectionName)->findBy(_daoSP()
			->addCondition('datas_bayes','=',$word)
			->addCondition('category_bayes','=',$pCat)
			->addCondition('dataset_bayes','=',$this->dataset)
			,$this->connectionName);
			$method="update";
			if (count($res)<1 || !isset($res[0])) {
				$rec = CopixDAOFactory::createRecord($this->dataset,$this->connectionName,$this->connectionName);
				$rec->category_bayes = $pCat;
				$rec->numdatas_bayes = 1;
				$rec->datas_bayes = $word;
				$rec->dataset_bayes=$this->dataset;
				$method = "insert";
			} else {
				$rec = $res[0];
				$rec->numdatas_bayes ++;
			}
			_ioDao($this->dataset,$this->connectionName)->$method($rec);
		}

	}

	/**
	 * Untrain, remove the data from dataset
	 *
	 * @param string $categoryname
	 * @param string $text
	 */
	public function untrain($pCat,$pText){
		$texts = $this->prepareText($pText);
	
		foreach($texts as $text){
			$rec = _ioDao($this->dataset,$this->connectionName)->findBy(_daoSp()
				->addCondition('datas_bayes','=',$text)
				->addCondition('category_bayes','=',$pCat)
			);
		
			if(count($rec)){
				//decrements the data number for this word
				if($rec[0]->numdatas_bayes>1) {
					$rec[0]->numdatas_bayes--;
					_ioDao($this->dataset,$this->connectionName)->update($rec[0]);
				}else{
					//this word have to be forget
					_ioDao($this->dataset,$this->connectionName)->delete($rec[0]->id_bayes);	
				}				
			}	
		}
		

	}


}
