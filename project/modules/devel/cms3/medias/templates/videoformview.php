<?php
$params = new CopixParameterHandler();
$params->setParams($options);
$width = $params->getParam ('x', '50');
$height = $params->getParam ('y', '50');
?>
<div id="media_<?php echo $media->id_media; ?>"></div>
<?php echo $include_media_code; ?>