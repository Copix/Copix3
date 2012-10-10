<?php
/**
 * @package		menu
 * @author		Steevan BARBOYON
 * @copyright 	CopixTeam
 * @link 		http://copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

class ItemsServices {
	private $_cannot_paste = array();
	private $_cut_id_item = null;
	
	/**
	 * Rechercher les ID de rubriques ou l'on ne peut pas copier
	 */
	private function _setCannotPaste () {
		// si on a un ID à coller en session
		if (!is_null ($this->_cut_id_item)) {
			// recherche de ID enfants
    		$this->_cannot_paste = array_keys ($this->_getChildsIds ($this->_cut_id_item));
    		// ID à copier
	    	$this->_cannot_paste[] = (int) $this->_cut_id_item;
	    	// ID "niveau au dessus" de celui à copier
			$item = _ioDAO ('menusitems')->get ($this->_cut_id_item);
			$this->_cannot_paste[] = (int) $item->id_parent_item;
		
		// si on n'a pas d'ID à coller en session
		} else {
			$this->_cannot_paste = array();
		}
	}
	
	/**
	 * Modifie la propriété privée $_cut_id_item
	 */
	public function setCutIdItem ($id_item) {
		$this->_cut_id_item = $id_item;
	}
	
	/**
	 * Retourne l'HTML d'un menu
	 * @param	int	$id_menu	ID du menu à afficher
	 * @param	bool	$mode_edit	Mode edition ou affichage
	 */
    public function getItemsHTML ($id_menu, $mode_edit = false) {
    	$toReturn = '';    	
    	
    	$this->_setCannotPaste();
	    		    	
    	if ($mode_edit) {
    		$toReturn .= '<table border="0" cellspacing="0" cellpadding="0">';
    		$toReturn .= $this->_getItemHTML ($id_menu, null, $mode_edit);
    		$toReturn .= '</table>';
    	} else {
    		$toReturn = $this->_getItemHTML ($id_menu, null, $mode_edit);
    	}
		
		if ($mode_edit) {
			$toReturn .= '<a href="' . _url ('menu|adminitems|edit', array('id_menu' => _request ('id_menu'), 'id_parent' => 'null')) . '">';
			$toReturn .= '<img src="' . _resource ('img/tools/add.png') . '" alt="' . _i18n ('admin.add') . '" title="' . _i18n ('admin.add') . '" />';
			$toReturn .= '</a><br />';
		}
		
        return $toReturn;
    }
	
    /**
     * Fonction recursive qui retourne u niveau de rubrique en HTML
     */
    private function _getItemHTML ($id_menu, $id_parent, $mode_edit = false, $spaces = 0) {
    	$toReturn = '';
    	$spaces_html = '';
    	for ($boucle = 0; $boucle < $spaces; $boucle++) {
    		$spaces_html .= '&nbsp;&nbsp;&nbsp;';
    	} // for
    	
    	// recherche des rubriques
		$criteres = _daoSp ();
		$criteres->addCondition ('id_menu', '=', $id_menu);
		$criteres->addCondition ('id_parent_item', '=', (int)$id_parent);
		$criteres->orderBy ('order_item');		
    	$items = _ioDao ('menusitems')->findBy ($criteres);
    	
    	// si on a au moins une rubrique
    	if (is_object($items) && count($items) > 0) {
    		if (!$mode_edit) {
    			$toReturn .= '<ul>';
    		}
	    	foreach ($items as $item_index => $item_infos) {
	    		if ($mode_edit) {
	    			$toReturn .= '<tr><td>' . $spaces_html . $item_infos->name_item . '</td><td>';
	    			
	    			// image editer
					$toReturn .= '&nbsp;<a href="' . _url ('menu|adminitems|edit', array('id_menu' => _request ('id_menu'), 'id_item' => $item_infos->id_item)) . '">';
					$toReturn .= '<img src="' . _resource ('img/tools/select.png') . '" alt="' . _i18n ('admin.edit') . '" title="' . _i18n('admin.edit') . '" />';
					$toReturn .= '</a>';
					// image ajouter
					$toReturn .= '&nbsp;<a href="' . _url ('menu|adminitems|edit', array('id_menu' => _request ('id_menu'), 'id_parent' => $item_infos->id_item)) . '">';
					$toReturn .= '<img src="' . _resource ('img/tools/add.png') . '" alt="' . _i18n ('admin.add') . '" title="' . _i18n('admin.add') . '" />';
					$toReturn .= '</a>';
					// image supprimer
					$toReturn .= '&nbsp;<a href="' . _url ('menu|adminitems|delete', array('id_menu' => _request ('id_menu'), 'id_item' => $item_infos->id_item)) . '">';
					$toReturn .= '<img src="' . _resource ('img/tools/delete.png') . '" alt="' . _i18n ('admin.delete') . '" title="' . _i18n('admin.delete') . '" />';
					$toReturn .= '</a>';
					
					// image couper
					if ($this->_cut_id_item == null) {
						$toReturn .= '&nbsp;<a href="' . _url ('menu|adminitems|cut', array('id_menu' => _request ('id_menu'), 'id_item' => $item_infos->id_item)) . '">';
						$toReturn .= '<img src="' . _resource ('img/tools/cut.png') . '" alt="' . _i18n ('admin.cut') . '" title="' . _i18n('admin.cut') . '" />';
						$toReturn .= '</a>';
					// image coller
					} else {
						// si l'element à coller fait partie de cette hierarchie d'elements, on met l'icone grisée
						if (in_array ($item_infos->id_item, $this->_cannot_paste)) {
							$toReturn .= '&nbsp;<img src="' . _resource ('img/tools/paste_disabled.png') . '" />';
						// si il ne fait pas partie de cette hierarchie, on autorise le coller
						} else {
							$toReturn .= '&nbsp;<a href="' . _url ('menu|adminitems|paste', array('id_menu' => _request ('id_menu'), 'paste_id_parent' => $item_infos->id_item)) . '">';
							$toReturn .= '<img src="' . _resource ('img/tools/paste.png') . '" alt="' . _i18n ('admin.paste') . '" title="' . _i18n('admin.paste') . '" />';
							$toReturn .= '</a>';
						}
					}
								
					// image monter
					if ($item_index > 0) {
						$toReturn .= '&nbsp;<a href="' . _url ('menu|adminitems|up', array('id_menu' => _request ('id_menu'), 'id_item' => $item_infos->id_item)) . '">';
						$toReturn .= '<img src="' . _resource ('img/tools/up.png') . '" alt="' . _i18n ('admin.up') . '" title="' . _i18n('admin.up') . '" />';
						$toReturn .= '</a>';
					}
					// image descendre rubrique
					if ($item_index < count ($items) - 1) {
						$toReturn .= '&nbsp;<a href="' . _url ('menu|adminitems|down', array('id_menu' => _request ('id_menu'), 'id_item' => $item_infos->id_item)) . '">';
						$toReturn .= '<img src="' . _resource ('img/tools/down.png') . '" alt="' . _i18n ('admin.down') . '" title="' . _i18n('admin.down') . '" />';
						$toReturn .= '</a>';
					}
					$toReturn .= '</td></tr>';
	    		} else {
	    			$toReturn .= '<li><a href="' . _url ($item_infos->link_item) . '">' . $item_infos->rub_name . '</a></li>';
	    		}
				
				$toReturn .= $this->_getItemHTML ($id_menu, $item_infos->id_item, $mode_edit, ($spaces + 1));
	    	}
	    	if (!$mode_edit) {
				$toReturn .= '</ul>';
	    	}
    	}
		
    	return $toReturn;    	
    }

    /**
     * Renvoie un tableau contenant les ID des rubriques enfants à celle passée en paramètre
     */
	private function _getChildsIds ($id_parent) {		
		$toReturn = array();
		
		$item = _ioDAO ('menusitems')->findBy (_daoSP ()->addCondition ('id_parent_item', '=', $id_parent));		
		foreach ($item as $item_index => $item_infos) {			
			$toReturn[$item_infos->id_item] = true;
			$toReturn = array_fill_keys (array_merge (array_keys ($toReturn), array_keys (self::_getChildsIds ($item_infos->id_item))), true);
		}

		return $toReturn;
	}
	
	/**
	 * Ajoute un élément à un menu
	 * 
	 * @param record $item
	 * @return bool
	 */	
	public function add ($item) {
		CopixRequest::assert ('id_menu', 'id_parent', 'name_item', 'link_item');
		
		$id_menu = _request('id_menu');
		$id_parent = _request('id_parent');
		
		$sql_id_parent = ($id_parent == 'null') ? null : $id_parent;
				
		// recherche du classement maximum de ce niveau
		$item_parent = _ioDao ('menusitems')->findBy (
			_daoSp ()
				->addCondition ('id_parent_item', '=', $sql_id_parent)
				->addCondition ('id_menu', '=', $id_menu)
				->orderBy (array('order_item', 'DESC'))
		);
		
		$order = (!isset ($item_parent[0])) ? 1 : $item_parent[0]->order_item + 1;

		// ajout de l'élément
		$item = _record('menusitems');
		$item->name_item = _request('name_item');
		$item->id_parent_item = $sql_id_parent;
		$item->id_menu = $id_menu;
		$item->order_item = $order;
		$item->link_item = _request ('link_item');
		
		CopixSession::set ('menu|items|edit', $item);		
		// verifications des erreurs de saisie
		if (_ioDAO ('menusitems')->check ($item) !== true) {
			return false;
		}
		
		_ioDao('menusitems')->insert($item);
		
		return true;
	}
	
	/**
	 * Modifie un élément d'un menu
	 * 
	 * @param record $item
	 * @return bool
	 */
	public function edit ($item) {
		CopixRequest::assert ('id_menu', 'id_item', 'name_item', 'link_item');
		
		$item = _ioDao ('menusitems')->get (_request('id_item'));				
		$item->name_item = _request ('name_item');
		$item->link_item = _request ('link_item');		
		
		CopixSession::set ('menu|items|edit', $item);		
		if (_ioDAO ('menusitems')->check ($item) !== true) {
			return false;
		}
		
		_ioDao ('menusitems')->update ($item);
		
		return _arRedirect (_url ('menu|adminitems|', array ('id_menu' => _request ('id_menu'))));
	}
	
	/**
	 * Supprime un élément (et ses enfants)
	 * 
	 * @param record $item
	 * @return bool
	 */
	public function delete ($id_item) {
		// recherche des infos sur la rubrique à supprimer
		$item = _ioDao ('menusitems')->findBy (_daoSp ()->addCondition ('id_item', '=', $id_item));
		
		// recherche des items enfants
		$items_enfants = _ioDao ('menusitems')->findBy (_daoSp ()->addCondition ('id_parent_item', '=', $id_item));
		// si on a trouvé des enfants, on rappelle delete
		if (is_object ($items_enfants) && count ($items_enfants) > 0) {
			for ($boucle = 0; $boucle < count($items_enfants); $boucle++) {
				$this->delete ($items_enfants[$boucle]->id_item);
			} // for
		}
		
		// suppression de cette rubrique
		_ioDao ('menusitems')->delete ($id_item);
		
		// modification des classements "au dessous"
		$sql_id_parent = (is_null($item[0]->id_parent_item)) ? 'AND id_parent_item IS NULL' : 'AND id_parent_item = ' . $item[0]->id_parent_item;
		$sql = 'UPDATE menusitems
				SET order_item = order_item - 1
				WHERE
					id_menu = ' . $item[0]->id_menu . '
					' . $sql_id_parent . '
					AND order_item > ' . $item[0]->order_item;
		CopixDB::getConnection ()->doQuery ($sql);
		
		return true;
	}
	
	/**
	 * Copie l'item contenu dans $this->_cut_id_item "en dessous" du parametre $id_parent
	 */
	public function paste ($id_menu, $id_parent) {
		// recherche d'infos sur la rubrique à copier
		$item = _ioDAO ('menusitems')->get ($this->_cut_id_item);		
		if (!$item) {
			return false;
		}

		// recherche d'infos sur l'element parent de celui qu'on veut copier

		// si l'element de destination n'est pas la racine
		if ($id_parent != null) {
			$item_parent = _ioDAO ('menusitems')->get ($id_parent);
		
			if (!$item_parent) {
				return false;
			}
		
		// si l'element de destination est la racine
		} else {
			$item_parent = _record('menusitems');
			$item_parent->id_item_menu = $id_menu;
		}

		// recherche du classement de l'element qu'on va copier
		$item_order = _ioDAO ('menusitems')->findBy (
			_daoSP()
				->addCondition ('id_parent_item', '=', $id_parent)
				->addCondition ('id_menu', '=', $id_menu)
				->orderBy (array ('order_item', 'DESC'))
		); 
		
		$order = (is_object ($item_order) && count ($item_order) > 0) ? count ($item_order) : 1;
		
		// modification des classement des elements "après" celui que l'on veut déplacer
		$sql_id_parent = (is_null ($item->id_parent_item)) ? 'AND id_parent_item IS NULL' : 'AND id_parent_item = ' . $item->id_parent_item;
		$sql = 'UPDATE menusitems
				SET order_item = order_item - 1
				WHERE
					id_menu = ' . $id_menu . '
					' . $sql_id_parent . '
					AND order_item > ' . $item->order_item;
		CopixDB::getConnection ()->doQuery ($sql);
				
		// modification de l'element à déplacer
		$item->order_item = $order;
		$item->id_parent_item = $item_parent->id_item;
		_ioDAO ('menusitems')->update ($item);
		
		return true;
	}

	/**
	 * Monte un element dans le classement
	 */
	public function up ($id_item) {
		$item = _ioDao ('menusitems')->get ($id_item);
		
		// si l'element n'a pas été trouvé
		if ($item === false) {
			return false;
		}
		
		// si le classement est déja à 1, on ne peut pas monter cette rubrique
		if ($item->order_item == 1) {
			return false;
		}
		
		// on "descend" d'un cran l'element précédent
		$item2 = _ioDao ('menusitems')->findBy (
			_daoSp ()
				->addCondition ('order_item', '=', $item->order_item - 1)
				->addCondition ('id_parent_item', '=', $item->id_parent_item)
		);
		
		// element non trouvé
		if (!is_object ($item2) || count ($item2) == 0) {
			return false;
		}
		
		$item2[0]->order_item++;
		_ioDao('menusitems')->update ($item2[0]);
		
		// on "monte" d'un cran l'element que l'on veut déplacer
		$item->order_item--;
		_ioDao('menusitems')->update ($item);
		
		return true;
	}
	
	/**
	 * Descend un element dans le classement
	 */
	public function down ($id_item) {
		$item = _ioDao ('menusitems')->get ($id_item);		
		
		// si l'element n'a pas ete trouve
		if ($item === false) {
			return false;
		}
		
		// recherche du classement max pour ce niveau d'element
		$item_max = _ioDao ('menusitems')->findBy(
			_daoSP()
				->addCondition('id_parent_item', '=', $item->id_parent_item)
				->orderBy(array('order_item', 'DESC'))
		);
		
		// si on n'a pas trouvé le classement max
		if (!is_object ($item_max) || count ($item_max) == 0) {
			return false;
		}
		
		// si le classement est déja au max, on ne peut pas descendre cet element
		if ($item->order_item == $item_max[0]->order_item) {
			return false;
		}
		
		// on "monte" d'un cran l'element suivant
		$item2 = _ioDao ('menusitems')->findBy (
			_daoSp ()
				->addCondition ('order_item', '=', $item->order_item + 1)
				->addCondition ('id_parent_item', '=', $item->id_parent_item)
		);
		
		// element non trouvé
		if (!is_object ($item2) || count ($item2) == 0) {
			return false;
		}
		
		$item2[0]->order_item--;
		_ioDao('menusitems')->update ($item2[0]);
		
		// on "descend" d'un cran l'element que l'on veut deplacer
		$item->order_item++;
		_ioDao('menusitems')->update ($item);
		
		return true; 
	}

}
?>