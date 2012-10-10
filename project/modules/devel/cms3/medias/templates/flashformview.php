<?php
$params = new CopixParameterHandler();
$params->setParams($options);
$width = $params->getParam ('x', '50');
$height = $params->getParam ('y', '50');

if ($params->getParam ('file_media', true)){ 
	echo $include_media_code; 
}?>