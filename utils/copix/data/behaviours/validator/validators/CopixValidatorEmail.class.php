<?php
/**
 * @package		copix
 * @subpackage	validator
 * @author		Salleyron Julien, Brice Favre
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Validation d'un email
 * @package		copix
 * @subpackage	validator
 */
class CopixValidatorEmail extends CopixAbstractValidator {

	/**
	 * Attribut privé contenant toutes les extensions par pays
	 *
	 * @var array
	 */
	private static $_countryCodeTLD = array(        // 252 countries @ Thu May 15 09:01:43 CEST 2008
	'ac' => "Ascension Island"
        , 'ad' => "Andorra"
        , 'ae' => "United Arab Emirates"
        , 'af' => "Afghanistan"
        , 'ag' => "Antigua and Barbuda"
        , 'ai' => "Anguilla"
        , 'al' => "Albania"
        , 'am' => "Armenia"
        , 'an' => "Netherlands Antilles"
        , 'ao' => "Angola"
        , 'aq' => "Antarctica"
        , 'ar' => "Argentina"
        , 'as' => "American Samoa"
        , 'at' => "Austria"
        , 'au' => "Australia"
        , 'aw' => "Aruba"
        , 'ax' => "Aland Islands"
        , 'az' => "Azerbaijan"
        , 'ba' => "Bosnia and Herzegovina"
        , 'bb' => "Barbados"
        , 'bd' => "Bangladesh"
        , 'be' => "Belgium"
        , 'bf' => "Burkina Faso"
        , 'bg' => "Bulgaria"
        , 'bh' => "Bahrain"
        , 'bi' => "Burundi"
        , 'bj' => "Benin"
        , 'bl' => "Saint Barthelemy"
        , 'bm' => "Bermuda"
        , 'bn' => "Brunei Darussalam"
        , 'bo' => "Bolivia"
        , 'br' => "Brazil"
        , 'bs' => "Bahamas"
        , 'bt' => "Bhutan"
        , 'bv' => "Bouvet Island"
        , 'bw' => "Botswana"
        , 'by' => "Belarus"
        , 'bz' => "Belize"
        , 'ca' => "Canada"
        , 'cc' => "Cocos (Keeling) Islands"
        , 'cd' => "Congo, The Democratic Republic of the"
        , 'cf' => "Central African Republic"
        , 'cg' => "Congo"
        , 'ch' => "Switzerland"
        , 'ci' => "Cote d'Ivoire"
        , 'ck' => "Cook Islands"
        , 'cl' => "Chile"
        , 'cm' => "Cameroon"
        , 'cn' => "China"
        , 'co' => "Colombia"
        , 'cr' => "Costa Rica"
        , 'cu' => "Cuba"
        , 'cv' => "Cape Verde"
        , 'cx' => "Christmas Island"
        , 'cy' => "Cyprus"
        , 'cz' => "Czech Republic"
        , 'de' => "Germany"
        , 'dj' => "Djibouti"
        , 'dk' => "Denmark"
        , 'dm' => "Dominica"
        , 'do' => "Dominican Republic"
        , 'dz' => "Algeria"
        , 'ec' => "Ecuador"
        , 'ee' => "Estonia"
        , 'eg' => "Egypt"
        , 'eh' => "Western Sahara"
        , 'er' => "Eritrea"
        , 'es' => "Spain"
        , 'et' => "Ethiopia"
        , 'eu' => "European Union"
        , 'fi' => "Finland"
        , 'fj' => "Fiji"
        , 'fk' => "Falkland Islands (Malvinas)"
        , 'fm' => "Micronesia, Federated States of"
        , 'fo' => "Faroe Islands"
        , 'fr' => "France"
        , 'ga' => "Gabon"
        , 'gb' => "United Kingdom"
        , 'gd' => "Grenada"
        , 'ge' => "Georgia"
        , 'gf' => "French Guiana"
        , 'gg' => "Guernsey"
        , 'gh' => "Ghana"
        , 'gi' => "Gibraltar"
        , 'gl' => "Greenland"
        , 'gm' => "Gambia"
        , 'gn' => "Guinea"
        , 'gp' => "Guadeloupe"
        , 'gq' => "Equatorial Guinea"
        , 'gr' => "Greece"
        , 'gs' => "South Georgia and the South Sandwich Islands"
        , 'gt' => "Guatemala"
        , 'gu' => "Guam"
        , 'gw' => "Guinea-Bissau"
        , 'gy' => "Guyana"
        , 'hk' => "Hong Kong"
        , 'hm' => "Heard Island and McDonald Islands"
        , 'hn' => "Honduras"
        , 'hr' => "Croatia"
        , 'ht' => "Haiti"
        , 'hu' => "Hungary"
        , 'id' => "Indonesia"
        , 'ie' => "Ireland"
        , 'il' => "Israel"
        , 'im' => "Isle of Man"
        , 'in' => "India"
        , 'io' => "British Indian Ocean Territory"
        , 'iq' => "Iraq"
        , 'ir' => "Iran, Islamic Republic of"
        , 'is' => "Iceland"
        , 'it' => "Italy"
        , 'je' => "Jersey"
        , 'jm' => "Jamaica"
        , 'jo' => "Jordan"
        , 'jp' => "Japan"
        , 'ke' => "Kenya"
        , 'kg' => "Kyrgyzstan"
        , 'kh' => "Cambodia"
        , 'ki' => "Kiribati"
        , 'km' => "Comoros"
        , 'kn' => "Saint Kitts and Nevis"
        , 'kp' => "Korea, Democratic People's Republic of"
        , 'kr' => "Korea, Republic of"
        , 'kw' => "Kuwait"
        , 'ky' => "Cayman Islands"
        , 'kz' => "Kazakhstan"
        , 'la' => "Lao People's Democratic Republic"
        , 'lb' => "Lebanon"
        , 'lc' => "Saint Lucia"
        , 'li' => "Liechtenstein"
        , 'lk' => "Sri Lanka"
        , 'lr' => "Liberia"
        , 'ls' => "Lesotho"
        , 'lt' => "Lithuania"
        , 'lu' => "Luxembourg"
        , 'lv' => "Latvia"
        , 'ly' => "Libyan Arab Jamahiriya"
        , 'ma' => "Morocco"
        , 'mc' => "Monaco"
        , 'md' => "Moldova, Republic of"
        , 'me' => "Montenegro"
        , 'mf' => "Saint Martin (French part)"
        , 'mg' => "Madagascar"
        , 'mh' => "Marshall Islands"
        , 'mk' => "Macedonia, The Former Yugoslav Republic of"
        , 'ml' => "Mali"
        , 'mm' => "Myanmar"
        , 'mn' => "Mongolia"
        , 'mo' => "Macao"
        , 'mp' => "Northern Mariana Islands"
        , 'mq' => "Martinique"
        , 'mr' => "Mauritania"
        , 'ms' => "Montserrat"
        , 'mt' => "Malta"
        , 'mu' => "Mauritius"
        , 'mv' => "Maldives"
        , 'mw' => "Malawi"
        , 'mx' => "Mexico"
        , 'my' => "Malaysia"
        , 'mz' => "Mozambique"
        , 'na' => "Namibia"
        , 'nc' => "New Caledonia"
        , 'ne' => "Niger"
        , 'nf' => "Norfolk Island"
        , 'ng' => "Nigeria"
        , 'ni' => "Nicaragua"
        , 'nl' => "Netherlands"
        , 'no' => "Norway"
        , 'np' => "Nepal"
        , 'nr' => "Nauru"
        , 'nu' => "Niue"
        , 'nz' => "New Zealand"
        , 'om' => "Oman"
        , 'pa' => "Panama"
        , 'pe' => "Peru"
        , 'pf' => "French Polynesia"
        , 'pg' => "Papua New Guinea"
        , 'ph' => "Philippines"
        , 'pk' => "Pakistan"
        , 'pl' => "Poland"
        , 'pm' => "Saint Pierre and Miquelon"
        , 'pn' => "Pitcairn"
        , 'pr' => "Puerto Rico"
        , 'ps' => "Palestinian Territory, Occupied"
        , 'pt' => "Portugal"
        , 'pw' => "Palau"
        , 'py' => "Paraguay"
        , 'qa' => "Qatar"
        , 're' => "Reunion"
        , 'ro' => "Romania"
        , 'rs' => "Serbia"
        , 'ru' => "Russian Federation"
        , 'rw' => "Rwanda"
        , 'sa' => "Saudi Arabia"
        , 'sb' => "Solomon Islands"
        , 'sc' => "Seychelles"
        , 'sd' => "Sudan"
        , 'se' => "Sweden"
        , 'sg' => "Singapore"
        , 'sh' => "Saint Helena"
        , 'si' => "Slovenia"
        , 'sj' => "Svalbard and Jan Mayen"
        , 'sk' => "Slovakia"
        , 'sl' => "Sierra Leone"
        , 'sm' => "San Marino"
        , 'sn' => "Senegal"
        , 'so' => "Somalia"
        , 'sr' => "Suriname"
        , 'st' => "Sao Tome and Principe"
        , 'su' => "Soviet Union (being phased out)"
        , 'sv' => "El Salvador"
        , 'sy' => "Syrian Arab Republic"
        , 'sz' => "Swaziland"
        , 'tc' => "Turks and Caicos Islands"
        , 'td' => "Chad"
        , 'tf' => "French Southern Territories"
        , 'tg' => "Togo"
        , 'th' => "Thailand"
        , 'tj' => "Tajikistan"
        , 'tk' => "Tokelau"
        , 'tl' => "Timor-Leste"
        , 'tm' => "Turkmenistan"
        , 'tn' => "Tunisia"
        , 'to' => "Tonga"
        , 'tp' => "Portuguese Timor (being phased out)"
        , 'tr' => "Turkey"
        , 'tt' => "Trinidad and Tobago"
        , 'tv' => "Tuvalu"
        , 'tw' => "Taiwan"
        , 'tz' => "Tanzania, United Republic of"
        , 'ua' => "Ukraine"
        , 'ug' => "Uganda"
        , 'uk' => "United Kingdom"
        , 'um' => "United States Minor Outlying Islands"
        , 'us' => "United States"
        , 'uy' => "Uruguay"
        , 'uz' => "Uzbekistan"
        , 'va' => "Holy See (Vatican City State)"
        , 'vc' => "Saint Vincent and the Grenadines"
        , 've' => "Venezuela"
        , 'vg' => "Virgin Islands, British"
        , 'vi' => "Virgin Islands, U.S."
        , 'vn' => "Viet Nam"
        , 'vu' => "Vanuatu"
        , 'wf' => "Wallis and Futuna"
        , 'ws' => "Samoa"
        , 'ye' => "Yemen"
        , 'yt' => "Mayotte"
        , 'yu' => "Yugoslavia"
        , 'za' => "South Africa"
        , 'zm' => "Zambia"
        , 'zw' => "Zimbabwe"
    );

	/**
	 * Attribut privé contenant toutes les extensions génériques
	 *
	 * @var array
	 */
	private static $_genericTLD = array (	// 21 gtld @ Thu May 15 09:01:43 CEST 2008
	'aero' => "Air-transport industry"
        ,'arpa' => "Internet Architecture Board"
        ,'asia' => "Pan-Asia and Asia Pacific community"
        ,'biz' => "Business"
        ,'cat' => "Catalan linguistic and cultural community"
        ,'com' => "Generic top-level domain"
        ,'coop' => "Cooperative associations"
        ,'edu' => "Department of Education's list"
        ,'gov' => "United States Government"
        ,'info' => "Generic top-level domain"
        ,'int' => "International treaties between governments"
        ,'jobs' => "Human resource managers"
        ,'mil' => "United States Military"
        ,'mobi' => "Mobile products and services"
        ,'museum' => "Museums"
        ,'name' => "Individuals"
        ,'net' => "Generic top-level domain"
        ,'org' => "Generic top-level domain"
        ,'pro' => "Professionals and related entities"
        ,'tel' => "Businesses and individuals contact data"
        ,'travel' => "Travel industry");

	/**
	 * Methode qui fait les tests sur la $pValue
	 *
	 * @param mixed $pValue La valeur
	 */
	protected function _validate ($pValue) {
		if (filter_var ($pValue, FILTER_VALIDATE_EMAIL)){
			if ($this->getParam ('tld')) {
				$arValue = explode ('.', $pValue);
				$pExt = $arValue [count ($arValue)-1];
				if ($this->_isTldValid ($pExt)) {
					return true;
				}
			}else{			
				return true;
			}
		}
		return _i18n ('copix:copixformatter.error.badmail', $pValue);
	}

	/**
	 * Vérification de l'extension
	 *
	 * @param string $pExt
	 * @return boolean
	 */
	private function _isTldValid ($pExt) {
		if (isset (self::$_countryCodeTLD[$pExt]) || isset (self::$_genericTLD[$pExt])) {
			return true;
		}
		return false;
	}

}