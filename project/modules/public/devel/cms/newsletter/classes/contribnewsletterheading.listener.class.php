<?php
/**
* @package	cms
* @subpackage newsletter
* @author	Bertrand Yan
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package	cms
* @subpackage newsletter
* Listener for CopixHeadings and the newsletter.
*/
class ListenerContribNewsletterHeading extends CopixListener {
	/**
    * handles the admin of a given CopixHeading
    */
	function performHeadingAdminBrowsing ($event, & $eventResponse){
		$newsletterModerateEnabled = CopixUserProfile::valueOf ('modules|cms|newsletter', 'newsletter') >= PROFILE_CCV_MODERATE;
		$newsletterSendEnabled     = CopixUserProfile::valueOf ('modules|cms|newsletter', 'newsletter') >= PROFILE_CCV_PUBLISH;
		if ($newsletterModerateEnabled || $newsletterSendEnabled) {
			$eventResponse->add (array ("module"=>"newsletter", "icon"=>CopixUrl::getResource ("img/modules/newsletter/newsletter.png"), 'shortDescription'=>CopixI18N::get ('newsletter|newsletter.shortDescription'), 'longDescription'=>CopixI18N::get ('newsletter|newsletter.longDescription')));
		}
	}
}
?>