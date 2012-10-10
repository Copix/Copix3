<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Selvi ARIK
 */


_eTag ('mootools', array ('plugin'=> array ('slideshow')));

$bKenBurns = false;
// Ajout des fichiers JavaScript spécifiques à chaque gabarit
if(isset($aExtraJs)) {
    foreach($aExtraJs as $sJs) {
    	_eTag ('mootools', array ('plugin'=> array ($sJs)));
        // il faut utiliser la classe Slideshow.KenBurns
        if(preg_match('/kenburns/', $sJs)) {
            $bKenBurns = true;
        }
    }
}


// Ajout des fichiers CSS spécifiques à chaque gabarit
foreach($aExtraCss as $sCss) {
    CopixHTMLHeader::addCSSLink(_resource($sCss));
}

// Options du template
$aOption = $portlet->getOptions();
if(array_key_exists('template', $aOption)) {
    unset($aOption['template']);
}
// Valeurs par défaut
if ((!array_key_exists('slideshowwidth', $aOption)) || (!is_numeric ($aOption['slideshowwidth']))) {
    $aOption['slideshowwidth'] = 500;
}
if ((!array_key_exists('slideshowheight', $aOption)) || (!is_numeric ($aOption['slideshowheight']))) {
    $aOption['slideshowheight'] = 375;
}
if ((!array_key_exists('thumbwidth', $aOption)) || (!is_numeric ($aOption['thumbwidth']))) {
    $aOption['thumbwidth'] = 80;
}
if ((!array_key_exists('thumbheight', $aOption)) || (!is_numeric ($aOption['thumbheight']))) {
    $aOption['thumbheight'] = 53;
}
if (!array_key_exists('thumbside', $aOption)) {
    $aOption['thumbside'] = 'horizontal';
}


if (array_key_exists('displayThumb', $aOption) && $aOption['displayThumb'] == 'oui') {
	$aOption['displayThumb'] = 'true';
}else{
	$aOption['displayThumb'] = 'false';
}

if (array_key_exists('centerImages', $aOption) && $aOption['centerImages'] == 'oui') {
	$aOption['centerImages'] = 'true';
}else{
	$aOption['centerImages'] = 'false';
}

if (array_key_exists('displayCaption', $aOption) && $aOption['displayCaption'] == 'oui') {
	$aOption['displayCaption'] = 'true';
}else{
	$aOption['displayCaption'] = 'false';
}

if (array_key_exists('controllerTransition', $aOption) && $aOption['controllerTransition'] == 'oui') {
	$aOption['controllerTransition'] = 'false';
}else{
	$aOption['controllerTransition'] = 'true';
}

if (array_key_exists('slideLoop', $aOption) && $aOption['slideLoop'] == 'oui') {
	$aOption['slideLoop'] = 'true';
}else{
	$aOption['slideLoop'] = 'false';
}

if (array_key_exists('slideAutoStart', $aOption) && $aOption['slideAutoStart'] == 'oui') {
	$aOption['slideAutoStart'] = 'false';
}else{
	$aOption['slideAutoStart'] = 'true';
}

if (array_key_exists('slideRandom', $aOption) && $aOption['slideRandom'] == 'oui') {
	$aOption['slideRandom'] = 'true';
}else{
	$aOption['slideRandom'] = 'false';
}

if (array_key_exists('slideController', $aOption) && $aOption['slideController'] == 'non') {
	$aOption['slideController'] = 'false';
}else{
	$aOption['slideController'] = '{transition: \'back:in:out\'}';
}


if (array_key_exists('slideResize', $aOption) && $aOption['slideResize'] == 'oui') {
	$aOption['slideResize'] = 'true';
}else{
	$aOption['slideResize'] = 'false';
}

if ((!array_key_exists('slideshowDelay', $aOption)) || (!is_numeric ($aOption['slideshowDelay'])) || (is_numeric ($aOption['slideshowDelay']) && $aOption['slideshowDelay'] <=1000)) {
    $aOption['slideshowDelay'] = 3000;
}

if ((!array_key_exists('slideshowDuration', $aOption)) || (!is_numeric ($aOption['slideshowDuration']))) {
    $aOption['slideshowDuration'] = 1000;
}
           


// Paramètres communs à tous les SlideShow
// 'replace' => "[/^(.*)$/, '$1?width=".$aOption['thumbwidth']."&height=".$aOption['thumbheight']."&keepProportions=1']",
$aCommonParams = array(
	// taille du diaporama
    'width'   => $aOption['slideshowwidth'],
    'height'  => $aOption['slideshowheight'],
	// afficher les vignettes
	'thumbnails' => $aOption['displayThumb'],
	// center les images
	'center' => $aOption['centerImages'],
	// afficher le nom de l'image
	'captions' => $aOption['displayCaption'],
	// ne pas afficher les transitions lors de la navigation non automatique
	'fast' => $aOption['controllerTransition'],
	// boucler sur le diaporama
	'loop' => $aOption['slideLoop'],
	// ne pas démarrer automatiquement le diaporama
	'paused' => $aOption['slideAutoStart'],
	// afficher les images dans un ordre aléatoire
	'random' => $aOption['slideRandom'],
	// redimentionner pour tenir toute la largeur
	'resize ' => $aOption['slideResize'],
	// délai ente chaque image
	'delay' => $aOption['slideshowDelay'],
	// durée de l'effet de transition
	'duration' => $aOption['slideshowDuration'],
	
    'classes' => "['', '', '', '', '', '', '', '', '', 'alternate-controller']",
	
    'controller' => $aOption['slideController'],
	
    'loader'  => '{\'animate\': [\''.str_replace('loader-1.png', 'loader-#.png', _resource('js/mootools/img/loader-1.png')).'\', 12]}'
);
// s'il y a des paramètres spécifiques au gabarit,
// on les ajoute aux paramètres communs à tous les gabarits
if(isset($aExtraParams)) {
    $aCommonParams = array_merge($aCommonParams, $aExtraParams);
}
$bLightbox = ( array_key_exists('linked', $aCommonParams) && ($aCommonParams['linked'] == 'true') ) ? true : false;

$randomId  = $portlet->getRandomId();
$aImage    = array();
$i         = 0;
$sFirstImg = '';

// Paramètres d'affichage des images
$aImgParams = array(
    'width'           => $aOption['slideshowwidth'],
    'height'          => $aOption['slideshowheight'],
    'keepProportions' => '1'
);

// Paramètres d'affichage des vignettes
$aTumbParams = array(
    /*'width'           => $aOption['thumbwidth'],
    'height'          => $aOption['thumbheight']/*,
    'keepProportions' => '1'*/
);

// Parcours des images
foreach ($elementsList as $oPortletImage) {
	if($oPortletImage){
		$oDAOHeadingImage = $oPortletImage->getHeadingElement ();
	    $aTempParam = array('public_id' => $oDAOHeadingImage->public_id_hei);
	    $sJSImgCode  = "'"._url('heading||', array_merge($aImgParams, $aTempParam))."' : {";
	    $sJSImgCode .= "  caption: '".addslashes($oDAOHeadingImage->caption_hei)."',";
	    $sJSImgCode .= "  thumbnail: '"._url('heading||', array_merge($aTumbParams, $aTempParam))."'";
	    $sJSImgCode .= "}";
		$aImage[]  = $sJSImgCode;
	
	    // récupération de la première image
	    if($i == 0) {
	        $sFirstImg = _url('heading||', array_merge($aImgParams, $aTempParam));
	    }
	    $i++;
	    unset($aTempParam);
	}
}
unset($aImgParams);
unset($aTumbParams);

$sJSDOMReadyCode = "
var data".$randomId." = { ".implode(",\n\t", $aImage)."};
var myShow".$randomId." = new Slideshow".( $bKenBurns ? '.KenBurns' : "")."('slideshow".$randomId."', data".$randomId.","
.str_replace('"', '', stripslashes(json_encode($aCommonParams)))."
);";
if($bLightbox) {
    $sJSDOMReadyCode .= "
    var box".$randomId." = new Lightbox({
      'onClose': function(){ this.pause(false); }.bind(myShow".$randomId."),
      'onOpen': function(){ this.pause(true); }.bind(myShow".$randomId.")
    });";
}
//_dump($sJSDOMReadyCode);
CopixHTMLHeader::addJSDOMReadyCode($sJSDOMReadyCode);

// largeur de la bordure de la vignette
$iThumbBorder = 3;
// padding de la vignette
$iThumbPadding = 3;
$iSlideshowThumbnailHeight = ($iThumbBorder+$iThumbPadding)*2+$aOption['thumbheight'];

$heightSlideShow = ($aOption['displayThumb'] == 'true') ?  $iSlideshowThumbnailHeight + $aOption['slideshowheight'] : $aOption['slideshowheight'];

// vignettes verticales
if($aOption['thumbside'] == 'vertical') {
    $iSlideshowThumbnailWidth = ($iThumbBorder+$iThumbPadding)*2+$aOption['thumbwidth'];
    CopixHTMLHeader::addStyle('#slideshow'.$randomId, '
                                width: '.$aOption['slideshowwidth'].'px;
                                height: '.$aOption['slideshowheight'].'px;
                                left: -'.intval($aOption['thumbwidth']/2).'px;
                            ');
    CopixHTMLHeader::addStyle('#slideshow'.$randomId.' .slideshow-images', '
                                width: '.$aOption['slideshowwidth'].'px;
                                height: '.$aOption['slideshowheight'].'px;
                            ');
    
    if($aOption['displayThumb'] == 'true'){
	    CopixHTMLHeader::addStyle('#slideshow'.$randomId.' .slideshow-thumbnails', '
	                                width: '.$iSlideshowThumbnailWidth.'px;
	                                height: '.$aOption['slideshowheight'].'px;
	                                top: 0;
	                                right: -'.($iSlideshowThumbnailWidth+3).'px;
	                                left: auto;
	                            ');
	    
	    CopixHTMLHeader::addStyle('#slideshow'.$randomId.' .slideshow-thumbnails ul', '
	                                width: '.$aOption['thumbwidth'].'px;
	                                height: '.$aOption['slideshowheight'].'px;
	                            ');
	    
	   	CopixHTMLHeader::addStyle('#slideshow'.$randomId.' .slideshow-thumbnails img', '
	                                width: '.$aOption['thumbwidth'].'px;
	                                
	                            ');

    }
}
// vignettes horizontales
else {
    CopixHTMLHeader::addStyle('#slideshow'.$randomId, '
                                width: '.$aOption['slideshowwidth'].'px;
                                height: '.$heightSlideShow.'px;
                            ');
    CopixHTMLHeader::addStyle('#slideshow'.$randomId.' .slideshow-images', '
                                width: '.$aOption['slideshowwidth'].'px;
                                height: '.$aOption['slideshowheight'].'px;
                            ');
    if($aOption['displayThumb'] == 'true'){
	    CopixHTMLHeader::addStyle('#slideshow'.$randomId.' .slideshow-thumbnails', '
	                                height: '.$iSlideshowThumbnailHeight.'px;
	                                position:relative;
	                            ');
	    CopixHTMLHeader::addStyle('#slideshow'.$randomId.' .slideshow-thumbnails ul', '
	                                height: '.$iSlideshowThumbnailHeight.'px;
	                            ');
	    
	    CopixHTMLHeader::addStyle('#slideshow'.$randomId.' .slideshow-thumbnails img', '
	                                height: '.$aOption['thumbheight'].'px;
	                            ');
    
    	CopixHTMLHeader::addStyle('#slideshow'.$randomId.' .slideshow-captions', '
                                bottom: '.$iSlideshowThumbnailHeight.'px;
                            ');
    }
}
?>
<div id="slideshow<?php echo $randomId;?>" class="slideshow">
    <?php if($bLightbox) {?>
    <a rel="lightbox" href="<?php echo $sFirstImg;?>">
    <?php }?>
        <img src="<?php echo $sFirstImg;?>" alt="" />
    <?php if($bLightbox) {?>
    </a>
    <?php }?>
</div>