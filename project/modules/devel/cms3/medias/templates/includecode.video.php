<?php $id = isset($privateId) ? $privateId : uniqid(); ?>
<div id="media_<?php echo $id; ?>"></div>
<?php 

$url_player = _resource('medias|player_flv_maxi.swf');
$url_media = isset($admin) && $admin ? _url('medias|mediafront|getmedia', array('id_media'=>$media->id_media)) : _url('heading||', array('public_id'=>$media->public_id_hei));
$url_start_image = $options->getParam('imagePresentation', false) ? ", startimage:'"._url('heading||', array('public_id'=>$options->getParam('imagePresentation')))."'" : '';
$jsCode = <<<JS_CODE_FLASH
var flashparams = {
	allowScriptAccess : 'always',
	allowFullScreen : 'false',
	wMode : 'window',
	quality : 'high'
};
var flashvars = {
	flv: "$url_media"
	$url_start_image
};
var options = {
	width: $width,
	height: $height,
	container: $('media_$id'),
	params: flashparams,
	vars: flashvars
};
new Swiff('{$url_player}', options);
JS_CODE_FLASH;
// http://svn.mootools.net/trunk/Docs/Utilities/Swiff.md

echo '<script type="text/javascript">' . $jsCode . '</script>';
?>