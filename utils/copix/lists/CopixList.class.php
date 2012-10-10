<?php

class CopixList {
    
    private $_id = null;
    
    /**
     * Le datasource
     * @var StdClass un datasource
     */
    private $_datasource = null;
    
    /**
     * Le datasource (nom)
     */
    private $_datasourceName = null;
    
    /**
     * Liste des paramètres passer a gettable
     */
    private $_params = array ();
    
    /**
     * Url pour appel non ajax
     */
    private $_url        = null;
    
    /**
     * Variable pour savoir si un bouton search a deja été défini
     */
    private $_searchButton = false;

    /**
     * Variable pour savoir si un bouton reset a deja été défini
     */
    private $_resetButton = false;
    
    private $_nextButton = false;
    
    private $_previousButton = false;
    
    private $_firstButton = false;
    
    private $_lastButton = false;
    
    private $_pager = false;
    
    private $_tplvars = null;
    
    private $_paging = false;
    
    private $_page = 0;
    
    private $_editLink = null;
    
    private $_fields = array ();
    
    private $_conditions = array ();
    
    private $_startupSearch = true;
    
    private $_delete      = true;
        
    private $_tpl = 'copix:templates/copixlist.php';
    
    private $_sens = 'ASC';
    
    private $_currentOrder = null;
    
    private $_max = null;
    
    private $_pagerTpl = null;
    
    private $_jsCode = false;
    
    private $_jsLoad = false;
    
    /**
     * On ne veut pas que le datasource passe en session
     * @return array 
     */
	public function __sleep () {
	    if ($this->_datasourceName !== null) {
    	    if ($this->_datasourceName != 'dao') {
    	        $this->_datasource = new CopixSessionObject($this->_datasource, $this->_datasourceName);
    	    }
	    }
	    return array_keys (get_object_vars ($this));
	}
    
	/**
	 * on recrée le datasource qui etait en sessions
	 */
	public function __wakeup () {
	    //Remise a zero des flags pour les boutons
	    $this->_searchButton   = false;
	    $this->_resetButton    = false;
	    $this->_nextButton     = false;
	    $this->_previousButton = false;
	    $this->_firstButton    = false;
	    $this->_lastButton     = false;
	    $this->_pager          = false;
	    $this->_jsLoad         = false;
	    if ($this->_datasourceName !== null) {
    	    if ($this->_datasourceName != 'dao') {
                $this->_datasource = $this->_datasource->getSessionObject ();
    	    }
	    }
	}
	
    /**
     * Constructeur
     *
     * @param string $pId l'identifiant de ce formulaire
     */
    public function __construct ($pId) {
        $this->_id = $pId;
    }

    /**
     * Commence un formulaire de recherche
     * @param $pUrl string (facultatif) permet de ne pas passer par Ajax et donne l'url de submit de la recherche 
     * @return string Le HTML généré
     */
    public function start ($pUrl = null) {
        $this->_url = $pUrl;
        if ($pUrl === null) {
            return '<form id="searchform'.$this->_id.'" name="searchform'.$this->_id.'" method="post" action="" >'."\n";
        } else {
            return '<form id="searchform'.$this->_id.'" name="searchform'.$this->_id.'" method="post" action="'._url ('generictools|copixlist|getTable',array('table_id'=>$this->_id,'url'=>$pUrl)).'" >'."\n";
        }
    }
    
    /**
     * Fini le formulaire et mets les boutons si nécéssaire
     */
    public function end ($pButton = true) {
        
        //Insert les javascripts nécessaire
	    $this->_doJavascript ();
        
        //On affiche les boutons de recherche et empty ect...
	    $toReturn = '';
	    if ($pButton) {
	        $toReturn .= $this->getButton ('submit').$this->getButton ('reset');
	    }
		return $toReturn.'</form>';
    }
    
    public function getButton ($pType, $pContent = null) {
        switch($pType) {
            case 'submit':
                if ($this->_searchButton) {
                    return '';
                }
                $this->_searchButton = true;
                if ($pContent === null) {
                    $pContent = '<input type="button" value="'._i18n ('copix:copixlist.button.search').'" />';
                }
                return '<span id="submit_'.$this->_id.'" >'.$pContent.'</span>';
            case 'reset':
                if ($this->_resetButton) {
                    return '';
                }
                $this->_resetButton = true;
                if ($pContent == null) {
                    $pContent = '<input type="reset" value="'._i18n ('copix:copixlist.button.reset').'" />';
                }
                return '<span id="reset_'.$this->_id.'" >'.$pContent.'</span>';
            case 'next':
                if ($this->_nextButton || $this->_page>=($this->_datasource->getNbPage ()-1)) {
                    return '';
                }
                $this->_nextButton = true;
                if ($pContent == null) {
                    $pContent = '<img src="'._resource('img/tools/next.png').'" />'; 
                }
                return '<span class="next_'.$this->_id.'" >'.$pContent.'</span>';
            case 'previous':
                if ($this->_previousButton || $this->_page<=0) {
                    return '';
                }
                $this->_previousButton = true;
                if ($pContent == null) {
                    $pContent = '<img src="'._resource('img/tools/previous.png').'" />'; 
                }
                return '<span class="previous_'.$this->_id.'" >'.$pContent.'</span>';
            case 'first':
                if ($this->_firstButton || $this->_page<=0) {
                    return '';
                }
                $this->_firstButton = true;
                if ($pContent == null) {
                    $pContent = '<img src="'._resource('img/tools/first.png').'" />'; 
                }
                return '<span class="first_'.$this->_id.'" >'.$pContent.'</span>';
            case 'last':
                if ($this->_lastButton || $this->_page>=($this->_datasource->getNbPage ()-1)) {
                    return '';
                }
                $this->_lastButton = true;
                if ($pContent == null) {
                    $pContent = '<img src="'._resource('img/tools/last.png').'" />'; 
                }
                return '<span class="last_'.$this->_id.'" >'.$pContent.'</span>';
            default:
                throw new CopixListException (_i18n ('copix:copixlist.button.notexist', $pType));               
        }
    }
    
    public function getField ($pField, $pParams) {
                
        if (!isset($pParams['type'])) {
            $pParams['type'] = 'varchar';
        }
        
        $id = md5(serialize($pField).$pParams['type']);
        
        if (!isset($this->_conditions[$id])) {
            $this->_conditions[$id] = new CopixField ($pField, $pParams,$this->_id,'list');
        } else {
            foreach ($pParams as $key=>$params) {
                $this->_conditions[$id]->setParams($key,$params);
            }
        }
               
        $field = $this->_conditions[$id];         

        return $field->getHTML();
    }
    
    private function _makeConditions () {
        foreach ($this->_conditions as $key=>$field) {
            if (!$field->isEmpty ()) {
                switch ($field->getType ()) {
        	        case 'bool':
                    case 'select':
                    case 'hidden':
                        if ($field->value != null) {
        	                $this->_datasource->addCondition ($field->field, '=', $field->value);
                        }
        		        break;
                    case 'multipleselect':        		        
                    case 'checkbox':
                        if (isset($field->value) && is_array($field->value)) {
                            CopixLog::log('isset');
                            $this->_datasource->startGroup ();
                            foreach ($field->value as $value) {
                                $this->_datasource->addCondition($field->field, '=', $value,'or');
                            }
                            $this->_datasource->endGroup();
                        }
                        break;
        		    case 'hiddendif':
        		        $this->_datasource->addCondition ($field->field, '!=', $field->value);
        		    case 'varchar':
        		    case 'autocomplete':
        		        $this->_datasource->addCondition ($field->field, 'like', $field->value.'%');
        		        break;
        		    case 'sup':
        		        $this->_datasource->addCondition ($field->field, '>', $field->value);
        		        break;
        		    case 'inf':
        		        $this->_datasource->addCondition ($field->field, '<', $field->value);
        		        break;
        		    case 'default':
        		        $this->_datasource->addCondition ($field->field, '=', $field->value);
        		        break;
        		    default:
        		        $arClasses = explode('::',$field->getType ());
        		        if (count($arClasses)==2) {
        		            $Class = CopixClassesFactory::create($arClasses[0]);
        		            $method = $arClasses[1].'Condition';
        		            $Class->$method($field,$this->_datasource);
        		        } else {
                            throw new CopixListException(_i18n('copix:copixlist.message.unknownType',$field->getType ()));
        		        }
                }
            }
        }
    }
    
    
    public function getList ($pDatasourceName, $pParams) {
        //CopixLog::log('??? '.$pDatasourceName);
        $this->_datasourceName = $pDatasourceName;
	    $this->_params         = $pParams;
	    $this->_datasource     = CopixDatasourceFactory::get ($this->_datasourceName, $this->_params);
        
	    if (isset($pParams['pagerTpl'])) {
	        $this->_pagerTpl = $pParams['pagerTpl'];
	    }
	    
	    //Pour la paging
    	if (isset($pParams['max'])) {
    	    $this->_max = $pParams['max'];
	    	$this->_paging = true;
	    }
        
        //Lien pour accéder à l'édition (fonctionne dans le cas d'un mapping seulement)
        if (isset ($pParams['edit'])) {
            $this->_editLink = $pParams['edit']; 
        }
        
        if (isset ($pParams['delete'])) {
            $this->_delete = $pParams['delete']; 
        }
    
        
        //Ajout de variables pour le template supplémentaire (fonctionne dans le cas d'un tpl externe seulement)
	    //Cette variable contient également (dans le cas d'un appel par un tpl) les variables passées au template d'origine
    	if (isset ($pParams['tplvars'])) {
            $this->_tplvars = $pParams['tplvars'];
        }
        
        //Template externe plutot que mapping
        if (isset ($pParams['tpl'])) {
            $this->_tpl = $pParams['tpl'];
        }
        
        //Si ni tpl ni mapping alors mapping=all pour prendre tout les champs du datasource
        if (!isset ($pParams['tpl']) && !isset ($pParams['mapping'])) {
              $this->_tplvars['mapping'] = 'all';
        }        
        
        if (isset ($pParams['more'])) {
              $arMore = explode('|',$pParams['more']);
              if (count($arMore)>1) {
                  $this->_tplvars['more'] = $arMore[1];
                  $this->_tplvars['moretitle'] = $arMore[0];
              } else {
                  $this->_tplvars['moretitle'] = '';
                  $this->_tplvars['more'] = $pParams['more'];
              }
        }
        
        if (isset ($pParams['moreLink'])) {
            $arMore = explode('@',$pParams['moreLink']);
            $this->_tplvars['moreLink'] = $arMore[1];
            $this->_tplvars['moreLinkTitle'] = $arMore[0];
        }
        //Indique si l'on souhaite que la recherche soit lancée au premier affichage
		//@TODO trouver un nom explicite
        if (isset ($pParams['startupSearch'])) {
            $this->_startupSearch = $pParams['startupSearch']; 
        }
        
        //Pour préciser la classe css du tableau généré (par défaut CopixTable)
        if (isset ($pParams['class'])) {
            $this->_tplvars['class'] = $pParams['class'];
        } else {
            $this->_tplvars['class'] = 'CopixTable';
        }
        
        //Mapping au format champ=>titre
        if (isset ($pParams['mapping'])) {
            if (!is_array ($pParams['mapping'])) {
                 $pParams['mapping'] = array ($pParams['mapping']);
            }
            $this->_tplvars['mapping'] = $pParams['mapping'];
        }
        
        $this->_doJavascript ();
        
        //Si on demande la recherche au démarrage on lance la génération du tableau 
        if ($this->_startupSearch) {
         	return '<div id="divlist_'.$this->_id.'">'.$this->generateTable ().'</div>';
        }
        return '<div id="divlist_'.$this->_id.'"></div>';
        
    }

    /**
     * Charge les éléments du POST dans les champs
     */
    public function getFromRequest() {
         foreach ($this->_conditions as $key=>$field) {
             $field->getFromRequest ();
         }
    }
    
    public function getResult ($pGetAll = false) {
		$this->_makeConditions ();
		try {
	        $currentOrder = _request ('order_'.$this->_id, null);
	        if (_request ('order_'.$this->_id, null)===null) {
	            $currentOrder = $this->_currentOrder;
	        }
	        if ($currentOrder!==null && _request ('order_'.$this->_id, null)===$this->_currentOrder) {
	            $this->_sens = ($this->_sens === 'ASC') ? 'DESC' : 'ASC';
	        } else {
	            if (_request ('order_'.$this->_id, null)!==null) {
	                $this->_sens = 'ASC'; 
	            }
	        }
	        $this->_currentOrder=$currentOrder;
	        if (!$pGetAll) {
        	    $results = $this->_datasource->find ($this->_page, $currentOrder,$this->_sens);
	        } else {
	            $results = $this->_datasource->find (-1, $currentOrder,$this->_sens);
	        }
		} catch (CopixDataSourceException $e) {
	        $results = $e->getMessage();
		}
        return $results;
    }
    
    /**
     * Génère le tableau (va chercher les données et les synchronisent avec le HTML
     * @return string le html du tableau
     */
    public function generateTable() {		
		$html = '';
		$results=array ();
		$this->_page = _request ('page_'.$this->_id,1) - 1;
	    $this->_nextButton  = false;
	    $this->_previousButton  = false;
	    $this->_firstButton  = false;
	    $this->_lastButton  = false;
	    $this->_pager        = false;
		
		$this->_makeConditions ();
		try {
		    //var_dump($this->_datasource);
	        $currentOrder = _request ('order_'.$this->_id, null);
	        if (_request ('order_'.$this->_id, null)===null) {
	            $currentOrder = $this->_currentOrder;
	        }
	        _log('ORDER : '.$currentOrder,'ORDER');
	        _log('LAST : '.$this->_currentOrder,'ORDER');
	        if ($currentOrder!==null && _request ('order_'.$this->_id, null)===$this->_currentOrder) {
	            $this->_sens = ($this->_sens === 'ASC') ? 'DESC' : 'ASC';
	        } else {
	            if (_request ('order_'.$this->_id, null)!==null) {
	                $this->_sens = 'ASC'; 
	            }
	        }
	        $this->_currentOrder=$currentOrder;
        	$results = $this->_datasource->find ($this->_page, $currentOrder,$this->_sens);
		} catch (CopixDataSourceException $e) {
			//$html .= '<div id="divlist_'.$this->_id.'" >'.$e->getMessage().'</div>';
	        $results = $e->getMessage();
			//return $html;
		}
        
        $html .= $this->_makeHtml($results);        
        
        //CopixListFactory::popCurrentId();
        
		return $html;
	}
    
	
	public function getRecord ($pNb) {
	    $tabPk = $this->_datasource->getTabPk ();
	    return isset($tabPk[$pNb]) ? $tabPk[$pNb] : null;
	}

	public function getTabPk() {
	    return $this->_datasource->getTabPk ();
	}
	
	public function nbRecord () {
	    $tabPk = $this->_datasource->getTabPk ();
	    return count($tabPk);
	}
	
	/**
	 * Fabrique le "HTML"
	 * @param $results array tableau des resultats du find datasource
	 * @return string HTML généré
	 */
    private function _makeHtml ($results) {
        $tpl = new CopixTpl ();
        if (is_string($results) && $results!=null) {
            $tpl->assign ('results',array());
            $tpl->assign ('error',$results);
        } else {
            $tpl->assign ('results',$results);
        }
        $tpl->assign ('idlist',$this->_id);
        $this->_tplvars['_max'] = $this->_max;
        $tpl->assign ('_page',$this->_page);
        $img =array();
        $img[$this->_currentOrder] = ($this->_sens !== 'ASC') ? '<img src="'._resource('img/tools/trihaut.png').'" />' : '<img src="'._resource('img/tools/tribas.png').'" />';
        $img['trivide']='<img src="'._resource('img/tools/trivide.png').'" />';
        $this->_tplvars['img'] = $img;
        if (isset($this->_tplvars['mapping']) && $this->_tplvars['mapping']=='all') {
            $this->_tplvars['mapping'] = array ();
            foreach ($this->_datasource->getFields () as $key=>$result) {
                $this->_tplvars['mapping'][$key]=$key;
            }
        }
        $this->_tplvars['CURRENT_PAGE'] = $this->_page+1;
        $this->_tplvars['NB_RECORD'] = $this->_datasource->getNbRecord ();
        $this->_tplvars['TOTAL_PAGE'] = $this->_datasource->getNbPage ();
        if ($this->_editLink != null) {
            $this->_tplvars['editLink'] = $this->_editLink;
            $this->_tplvars['editLinkPk'] = $this->_datasource->getPk ();
            $this->_tplvars['delete']    = $this->_delete;
        }
        $tpl->assignTemplateVars ($this->_tplvars);
        
        CopixHTMLHeader::addJSLink(_resource('js/taglib/copixlist.js'));
        CopixHTMLHeader::addJSDomReadyCode ("copixlist_event_pager ('$this->_id','"._url ('generictools|copixlist|getTable',array('table_id'=>$this->_id,'submit'=>'false'))."','$this->_page','".($this->_page+2)."',".$this->_datasource->getNbPage ().")");
        
        if ($this->_pagerTpl!==null) {
            $pager = $this->getPager ($this->_pagerTpl);
        } else {
            $pager = $this->getPager ();
        }
        $toReturn = $pager;
        $toReturn .= $tpl->fetch ($this->_tpl);
        $toReturn .= $pager;
        return $toReturn;
    }
    
    public function getPager ($tpl = 'copix:templates/pager.tpl') {
        if ($this->_paging && !$this->_pager) {
            $this->_pager = true;
        	$tplpager = new CopixTpl ();
        	$tplpager->assign ('idlist',$this->_id);
        	$tplpager->assignTemplateVars ($this->_tplvars);
	        return $tplpager->fetch($tpl);
        } else {
            return '';
        }
    }
    
    private function _doJavascript () {
        if ($this->_jsLoad) {
            return true;
        }
        $this->_jsLoad = true;
        _tag ('mootools', array('plugin'=>'overlayfix'));
        CopixHTMLHeader::addJSLink(_resource('js/taglib/copixlist.js'));
        if ($this->_url !== null) {
        	CopixHTMLHeader::addJSDOMReadyCode('copixlist_setnonajax ("'.$this->_id.'","'._url('#',array('submit'=>'false')).'");');
        }
		CopixHTMLHeader::addJSDOMReadyCode('copixlist_launch ("'.$this->_id.'")');
    }
}

?>