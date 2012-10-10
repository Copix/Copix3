<?php
/**
 * Champs de configuration d'un type de sauvegarde
 */
class ZoneListTables extends CopixZone {
	/**
	 * Création du contenu
	 *
	 * @param string $pToReturn Contenu à retourner
	 * @return boolean
	 */
	protected function _createContent (&$pToReturn) {
		$tpl = new CopixTPL ();
		$tables = CopixDb::getConnection ($this->getParam ('dbprofile'))->getTableList ();
		$tplTables = array ('Autres' => array ());
		foreach ($tables as $table) {
			if (strpos ($table, '_') !== false) {
				$prefixe = substr ($table, 0, strpos ($table, '_'));
				if (!isset ($tplTables[$prefixe])) {
					$tplTables[$prefixe] = array ();
				}
				$tplTables[$prefixe][] = $table;
			} else {
				$tplTables['Autres'][] = $table;
			}
		}
		$tpl->assign ('tables', $tplTables);
		$tpl->assign ('selected', $this->getParam ('selected', array ()));
		$pToReturn = $tpl->fetch ('backup|profiles/listtables.zone.php');
		return true;
	} 
}