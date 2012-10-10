<?php
$FlashContent = '';
$HTMLContent = array();
foreach ($arTags as $tag=>$url){
	$FlashContent .= "<a href='".$url."' style='12'>".$tag."</a>";
	$HTMLContent[] = "<a href='".$url."'>'".$tag."</a>";
}
$HTMLContent = implode( ', ', $HTMLContent );
_eTag( 'mootools' );
$tagcloudURL = _resource ('portal|swf/tagcloud.swf');

$options = $portlet->getOptions ();

$sColorPattern = '/([a-f]|[A-F]|[0-9]){6}/';
$height      = (array_key_exists ('height', $options)) ? $options['height'] : '170';
if (true !== _validator ('numeric', array ('min'=>1))->check ($height)) {
    $height = '170';
}
$width       = (array_key_exists ('width', $options)) ? $options['width'] : '170';
if (true !== _validator ('numeric', array ('min'=>1))->check ($width)) {
    $width = '170';
}
$color       = ((array_key_exists ('color', $options)) && (false !== preg_match($sColorPattern, $options['color']))) ? $options['color'] : '000000';
$rollover    = ((array_key_exists ('rollover', $options)) && (false !== preg_match($sColorPattern, $options['rollover']))) ? $options['rollover'] : '000000';
$bgcolor     = ((array_key_exists ('bgcolor', $options)) && (false !== preg_match($sColorPattern, $options['bgcolor']))) ? $options['bgcolor'] : 'ffffff';
$wmode       = ((array_key_exists ('transparent', $options)) && ($options['transparent'] == 1)) ? 'transparent' : 'window';
$speed       = (array_key_exists ('speed', $options)) ? $options['speed'] : '100';
// entre 25 et 500
if (true !== _validator ('numeric', array ('min'=>25, 'max'=>500))->check ($speed)) {
    $speed = 100;
}

$jsCode = <<<EOF
if( window.ie && $('nav') ){
	$('nav').getChildren().each( function(el){
		el.addEvent('mouseover', function(){ $(this).addClass('over') });
		el.addEvent('mouseout', function(){ $(this).removeClass('over') });
	});
}
if( $('tagcloud') ){
	var flashvars = {
		distr: "true",
		tcolor : "0x{$color}", // Color
		hicolor : "0x{$rollover}", // Rollover
		tspeed : "{$speed}",
		mode : 'tags',
		tagcloud : "<tags>{$FlashContent}</tags>"
	};

	if (Browser.Plugins.Flash.version >= 9) {
        var mySwiff = new Swiff('{$tagcloudURL}', {
            id: "tagcloud",
            width: "{$width}",
            height: "{$height}",
            params: {
                bgcolor: "#{$bgcolor}",
                allowScriptAccess: "always",
                allowFullScreen: "false",
                quality: "high",
                wmode: "{$wmode}"
            },
            vars: flashvars,
            container: "tagcloud"
        });
    }
}
EOF;

CopixHTMLHeader::AddJSDomReadyCode($jsCode);
?>
<div id="tagcloud">
	<?php echo $HTMLContent; ?>
</div>