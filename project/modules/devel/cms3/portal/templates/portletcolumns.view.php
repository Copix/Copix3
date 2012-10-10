<div id="portletColumn<?php echo $portlet->getRandomId();?>" style="display: block;">
	<div id="portletColumnLeft<?php echo $portlet->getRandomId();?>" class="portletColumn" style="width:<?php echo $portlet->getOption("leftColumn", "49");?>%"><?php echo ${PortletColumns::LEFT_COLUMN}; ?></div>
	<div id="portletColumnRight<?php echo $portlet->getRandomId();?>" class="portletColumn" style="width:<?php echo $portlet->getOption("rightColumn", "49");?>%"><?php echo ${PortletColumns::RIGHT_COLUMN}; ?></div>
	<div style="clear:both;"></div>
</div>