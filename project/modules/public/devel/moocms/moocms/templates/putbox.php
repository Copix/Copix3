<?php
foreach ($ppo->boxes as $box){
	echo "<a href=\"#\" name=\"".$box."\" onClick=\"javascript:addBoxFor(this);\">";
	echo CopixModule::getInformations('moobox_'.$box)->description."<br />";
	echo "</a>";
}
?>