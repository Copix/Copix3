<?php
/**
 * CopixBayes Class
 * Used to get Bayesian values of probabilities
 * @package tools
 * @subpackage bayes
 * @author Patrice Ferlet - <metal3d@copix.org>
 * @copyright CopixTeam (c) 2007-2008
 * @licence GNU/GPL
 */

class Bayes {

	public $categories = array();
	public $numcat=0;
	public $mode="static";
	public $dataset="";
	
	private $connectionName = null;
	
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
	 * @param strong $mode "static" or "db"
	 */
	public function setDataMode($mode="static",$dataset=null,$connectionName=null){
		if(empty($dataset) && $mode=="db"){
			throw (new Exception("No dataset given for CopixBayesian datas used with database"));
		}
	    $this->dataset="bayesiantable_".$dataset;
		$this->mode = ($mode=="db") ? "db" :"static";
	    //get tables	    
	    if($this->mode=="db"){
	        $this->connectionName = $connectionName;
    	    $ct = CopixDB::getConnection($connectionName);
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
    	         _doQuery($sql,array(),$connectionName);
    	    }
	    }
		
	}


	/**
	 * Add category and datas for this category
	 * @param string $category
	 * @param string $text
	 */
	function train($cat,$text){
		$texts = $this->prepareText($text);
		if($this->mode!="db"){
			$this->categories[$cat]->texts[]=$texts;
			$this->categories[$cat]->counter++;
			$this->numcat++;
		} else {
			$rec = CopixDAOFactory::createRecord($this->dataset,$this->connectionName);
			$rec->category_bayes = $cat;
			$rec->datas_bayes = ';'.implode(';',$texts).';'; //let ";" for a better "like" check
			$rec->numdatas_bayes = count($texts);
			$rec->dataset_bayes=$this->dataset;
			_ioDao($this->dataset,$this->connectionName)->insert($rec);
		}
	}

	/**
	 * Untrain remove the data from dataset
	 * @param string $categoryname
	 * @param string $text
	 */
	function untrain($cat,$text){
		$texts = $this->prepareText($text);
		if($this->mode!="db"){
			if(isset($this->categories[$cat]) && isset($this->categories[$cat]->texts)){
				$i=0;
				foreach($this->categories[$cat]->texts as $t){
					if($texts == $t){
						unset($this->categories[$cat]->texts[$i]);
						break;
					}
					$i++;
				}
				if(count($this->categories[$cat]->texts)<1){
					unset($this->categories[$cat]);
				}
			}
		} else {
			$find = ';'.implode(';',$texts).';';
			$rec = _ioDao($this->dataset,$this->connectionName)->findBy(_daoSp()
										->addCondition('datas_bayes','=',$find)
										->addCondition('category_bayes','=',$cat)
										);
			if(count($rec)){
				_ioDao($this->dataset,$this->connectionName)->delete($rec[0]->id_bayes);
			}
		}
	}



	/**
	 * Get the Bayesian value for a category
	 * @param string $category
	 * @param string $data_to_test
	 * @return float $bayesian_value (in percent %)
	 */
	function getBayes($B,$A,$simpleMode=false){
		$this->setCategoriesProbas();
		//P(B|A)
		//numerator:  P(B) * P(A|B)...
		//P(B) => getCategoriesProbas for B
		//P(A|B) => find A in B
		if(!isset($this->categories[$B])){
			return 0;
		}
		if(!$simpleMode){
			$PB = $this->categories[$B]->percent;
		} else {
			$PB = (100/count($this->categories));
		}
		$PAB = $this->getProba($A,$B);
		$numerator = $PB * $PAB;
		$this->operation = $PB.'*'.$PAB;
		$this->operation .="\n--------\n";
		//denominator: ( P(B)*(P(A|B) + P(B2)*P(A|B2) + ... )
		//so for every categories, we have to check
		$den = 0;
		foreach($this->categories as $name=>$B){
			if(!$simpleMode){
				$PB = $B->percent;
			}
			$PAB = $this->getProba($A,$name);
			$den += ($PB*$PAB);
			$this->operation.= '+('.$PB.'*'.$PAB.')';
		}

		if($den==0) return 0;

		return 100*$numerator/$den;
	}

	/**
	 * Get probability of A is in B
	 * @param string $category  category name
	 * @param string $data_to_check  data to check
	 * @return float $proba (in percent %)
	 */
	function getProba($A,$B){
		$A = $this->remove_accents($A);
		$A = preg_split('/\W/is',$A);
		$numwords = 0;
		$found = 0;
		if($this->mode!="db"){
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
		} else {
			$numwords = _doQuery('select SUM(numdatas_bayes) numwords from '.$this->dataset.' where category_bayes="'.$B.'"',array(),$this->connectionName);
			$numwords = $numwords[0]->numwords;
			foreach($A as $find){
				$sets = _doQuery("select datas_bayes from ".$this->dataset." where category_bayes=\"$B\" AND datas_bayes like \"%;$find;%\"",array(),$this->connectionName);
				foreach($sets as $set){
					$words = explode(';',$set->datas_bayes);
					foreach($words as $word){
						if(strlen($word) && $find==$word){
							$found++;
						}
					}
				}
			}
		}
		return $found * 100 / $numwords;
	}

	//------------------ Privates methods
	
	private function prepareText ($text){
		$text = $this->remove_accents($text);
		$t=preg_split('/\W/is',$text);
		$texts=array();
		foreach ($t as $text){
			if(strlen(trim($text))>0){
				$texts[]=trim($text);
			}
		}
		return $texts;
	}

	/**
	 * By derernst at gmx dot ch: http://fr3.php.net/manual/fr/function.strtr.php#56973
	 */
	private function remove_accents($string, $german=false) {
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
		$string = strtr($string, $replacements);
		return $string;
	}

	private function setCategoriesProbas(){
		if($this->mode!="db"){
			foreach($this->categories as $cat){
				$cat->percent = $cat->counter * 100 / $this->numcat;
			}
		}else{
			$cats = _doQuery('select distinct category_bayes from '.$this->dataset,array(),$this->connectionName);
			$acount = _doQuery("select count(*) count from ".$this->dataset,array(),$this->connectionName);
			foreach($cats as $cat){
				$this->addCategory($cat->category_bayes);
				$count = _doQuery("select count(*) count from ".$this->dataset." where category_bayes='".$cat->category_bayes."'",array(),$this->connectionName);
				$this->categories[$cat->category_bayes]->percent= $count[0]->count * 100 / $acount[0]->count;
			}
				
		}
	}

	private function addCategory ($cat){
		$this->categories[$cat]=new stdClass;
		$this->categories[$cat]->texts=array();
		$this->categories[$cat]->counter=0;
		$this->categories[$cat]->percent = 0;
	}
}

?>