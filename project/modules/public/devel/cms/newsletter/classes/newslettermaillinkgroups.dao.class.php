<?php

/**
* @package	cms
* @subpackage newsletter
* @author	???
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package	cms
* @subpackage newsletter
*/

class DAONewsletterMailLinkGroups {
	function moveGroup ($from, $to){
		//séléction des mail à la fois dans le group 1 et 2
		$query  = 'select N1.id_nlm as id_nlm from newslettermaillinkgroups N1, newslettermaillinkgroups N2';
		$query .= ' where N1.id_nlg='.$to.' and N2.id_nlg='.$from;
		$query .= ' and N1.mail_nlm=N2.mail_nlm';
		$dbWidget = & CopixDBFactory::getDbWidget ($this->_connectionName);
		$arIdMail = $dbWidget->fetchAll ($query);
		//mise à jour des mails qui ne sont pas dans les 2 groupes
		$query  = 'update newslettermaillinkgroups set id_nlg='.$to.' where id_nlg='.$from;
/*
 		if (count($arMail)) {
			$query .= ' and mail_nlm NOT IN '.$this->_getInQuery($arMail);
		}
* 
 */
	    $ct = CopixDBFactory::getConnection ($this->_connectionName);
		$ct->doQuery ($query);
		//suppressiondes mails réstant du groupe 2
		$query = 'delete from newslettermaillinkgroups where id_nlg='.$from;
		$ct->doQuery ($query);
	}

	function deleteByGroup ($id){
		$query = 'delete from newslettermaillinkgroups where id_nlg='.$id;
		$ct = CopixDBFactory::getConnection ($this->_connectionName);
		$ct->doQuery ($query);

		$dao = & CopixDAOFactory::getInstanceOf ('NewsletterGroups');
		$dao->delete ($id);
	}

	function deleteByMail ($mail){
		$ct = CopixDBFactory::getConnection ($this->_connectionName);
		$query = 'delete from newslettermaillinkgroups where mail_nlm='.$ct->quote ($mail).'';
		$ct->doQuery ($query);
	}

	function _getInQuery($arIdMail) {
		$first = true;
		$query = '(';
		foreach ($arIdMail as $mail){
			if (!$first) {
				$query .= ',';
			}
			$query .= $mail->mail_nlm;
		}
		$query .= ')';
		return $query;
	}
}
?>