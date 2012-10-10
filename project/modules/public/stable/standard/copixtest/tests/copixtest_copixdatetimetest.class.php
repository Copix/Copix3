<?php
/**
* @package		standard
* @subpackage	copixtest
* @author		Croës Gérald
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package standard
 * @subpackage copixtest
 */
class CopixTest_CopixDateTimeTest extends CopixTest {
	function testToXXXX (){
		CopixI18N::setLang ('fr');
		$this->assertEquals ('19761225', CopixDateTime::dateToYYYYMMDD ('25/12/1976'));
		$this->assertFalse (CopixDateTime::dateToYYYYMMDD ('215/a2/1976'));
		$this->assertFalse (CopixDateTime::dateToYYYYMMDD ('215/a276'));
		$this->assertEquals ('19761225150000',CopixDateTime::DateTimeToyyyymmddhhiiss('25/12/1976 15:00:00'));
		$this->assertEquals ('19761225100106',CopixDateTime::timestampToyyyymmddhhiiss(220352466));
		CopixI18N::setLang ('en');
		$this->assertEquals ('19761225', CopixDateTime::dateToYYYYMMDD ('12/25/1976'));
		$this->assertFalse (CopixDateTime::dateToYYYYMMDD ('25/a2/1976'));
		$this->assertNull (CopixDateTime::dateToYYYYMMDD (null));
		$this->assertEquals ('19761225150000',CopixDateTime::DateTimeToyyyymmddhhiiss('12/25/1976 03:00:00 pm'));
		$this->assertEquals ('19761225030000',CopixDateTime::DateTimeToyyyymmddhhiiss('12/25/1976 03:00:00 am'));
		$this->assertEquals ('19761225',CopixDateTime::timestampToyyyymmdd(220316400));
	}
	
	function testToDateTime (){
		CopixI18N::setLang ('fr');
		$this->assertEquals ('25/12/1976',CopixDateTime::timestampToDate(220316400));
		$this->assertEquals ('25/12/1976', CopixDateTime::yyyymmddToDate ('19761225'));
		$this->assertEquals('25/12/1976 15:00:00',CopixDateTime::yyyymmddhhiissToDateTime('19761225150000'));
		CopixI18N::setLang ('en');
		$this->assertEquals ('12/25/1976', CopixDateTime::YYYYMMDDToDate ('19761225'));
		$this->assertNull (null);
		$this->assertFalse (CopixDateTime::YYYYMMDDToDate ('1123761225'));
		$this->assertEquals ('12/25/1976',CopixDateTime::timestampToDate(220316400));
		$this->assertEquals('12/25/1976 15:00:00',CopixDateTime::yyyymmddhhiissToDateTime('19761225150000'));
		$this->assertEquals('12/25/1976 10:00:00',CopixDateTime::yyyymmddhhiissToDateTime('19761225100000'));
	}
	
	function testToText (){
		CopixI18N::setLang ('fr');
		$this->assertEquals ('Samedi 25 Decembre 1976', CopixDateTime::yyyymmddToText ('19761225'));
		CopixI18N::setLang ('en');
		$this->assertContains ('Saturday 25th of December', CopixDateTime::yyyymmddToText ('19761225'));

	}
	
	function testToTimestamp (){
		CopixI18N::setLang ('fr');
		$this->assertEquals (220316400, CopixDateTime::yyyymmddToTimestamp ('19761225'));
		$this->assertEquals (880930800, CopixDateTime::yyyymmddToTimestamp ('19971201'));
		$this->assertEquals (220316400,CopixDateTime::dateTotimestamp('25/12/1976'));
		CopixI18N::setLang ('en');
		$this->assertEquals (220316400,CopixDateTime::dateTotimestamp('12/25/1976'));
		$this->assertEquals (220352466,CopixDateTime::yyyymmddhhiissToTimestamp('19761225100106'));
		$this->assertEquals (880930866, CopixDateTime::yyyymmddhhiissToTimestamp ('19971201000106'));
		CopixI18N::setLang ('fr');
		/*
		
		
		$this->assertEquals ('19761225000000',CopixDateTime::DateTimeToyyyymmddhhiiss('25/12/1976'));
		timeStampToyyyymmddhhiiss
		yyyymmddhhiissToTimeStamp*/
	}
	
	function testTime (){
		//Copix 3.0.1+
		$this->assertEquals ('18:22:45', CopixDateTime::hhiissToTime ('182245'));
		$this->assertEquals ('182245', CopixDateTime::timeTohhiiss ('18:22:45'));
		$this->assertNull (CopixDateTime::timeTohhiiss (null));

		//On laisse les anciennes méthodes pour vérifier les compatibilités ascendantes 
		$this->assertEquals ('18:22:45', CopixDateTime::hhmmssToTime ('182245'));
		$this->assertEquals ('182245', CopixDateTime::timeToHHMMSS ('18:22:45'));
		$this->assertNull (CopixDateTime::timeToHHMMSS (null));
	}
	
	function testToFormat (){
		$this->assertEquals (CopixDateTime::yyyymmddToFormat ('20071201', 'Y-m-d'), '2007-12-01');
		$this->assertEquals (CopixDateTime::yyyymmddhhiissToFormat ('20070213201235', 'Y-m-d H:i:s'), '2007-02-13 20:12:35');
		$this->assertEquals (CopixDateTime::hhiissToFormat ('231436', 'H:i:s'), '23:14:36');
	}
}
?>