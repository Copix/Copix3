<?php
/**
 * @package devtools
 * @subpackage moduleeditor
 * @copyright CopixTeam
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 * @link http://www.copix.org
 */

/**
 * Classe capable de récupérer les informations sur le serveur MySql 
 * @package devtools
 * @subpackage moduleeditor 
 */
class MysqlServerInfos {
	
	/**
	 * Remplit le tableau $pArSections avec les informations sur le serveur MySql
	 *
	 * @param array	$pArSections	tableau dans lequel on va remplir les informations disponibles sur le serveur
	 */
	public function fillInformations (& $pArSections){
		$section = _i18n ('copix.section.globalInfos');
		$result = CopixDB::getConnection ()->doQuery ('SELECT VERSION() mysql_version');
		$pArSections[$section][_i18n ('copix.dbserver.version')] = $result[0]->mysql_version;
		$result = CopixDB::getConnection ()->doQuery ('SELECT NOW() time');
		$pArSections[$section][_i18n ('copix.dbserver.date')] = $result[0]->time;
		$status = CopixDB::getConnection ()->doQuery ('SHOW STATUS');
		$uptime = $this->_getMySQLStatusVariable ($status, 'Uptime');
		$days = floor ($uptime / (3600 * 24));
		$uptime -= $days * 3600 * 24;
		$hours = floor ($uptime / 3600);
		$uptime -= $hours * 3600;
		$minutes = floor ($uptime / 60);
		$uptime -= $minutes * 60;
		$seconds = $uptime;
		$uptimeStr = '';
		if ($days > 0) {
			$uptimeStr .= $days . ' ' . _i18n ('copix.days') . ' ';
		}
		if ($hours > 0) {
			$uptimeStr .= $hours . ' ' . _i18n ('copix.hours') . ' ';
		}
		if ($minutes > 0) {
			$uptimeStr .= $minutes . ' ' . _i18n ('copix.minutes') . ' ';
		}
		if ($seconds > 0) {
			$uptimeStr .= $seconds . ' ' . _i18n ('copix.seconds') . ' ';
		}
		$pArSections[$section][_i18n ('copix.dbserver.uptime')] = $uptimeStr;
		$pArSections[$section][_i18n ('copix.dbserver.questions')] = $this->_getMySQLStatusVariable ($status, 'Questions');
		$pArSections[$section][_i18n ('copix.dbserver.lastquerycost')] = $this->_getMySQLStatusVariable ($status, 'Last_query_cost') . ' ' . _i18n ('copix.seconds');
		
		$section = _i18n ('copix.section.config');
		$result = CopixDB::getConnection ()->doQuery ('SHOW VARIABLES');
		foreach ($result as $variable) {
			$pArSections[$section][$variable->Variable_name] = $variable->Value;
		}		
	}

	/**
	 * Recherche un nom de variable et sa valeur dans un résultat de SHOW STATUS MySQL
	 * @param	array	$pResult	l'ensemble de résultat du SHOW STATUS MySQL
	 * @param	string	$pVarName	la variable de status que l'on souhaite connaitre
	 * @return 	string 
	 */
	private function _getMySQLStatusVariable ($pResult, $pVarName) {
		foreach ($pResult as $item) {
			if ($item->Variable_name == $pVarName) {
				return $item->Value;
			}
		}
	}
}
?>