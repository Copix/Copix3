<?php if ($currentPage>1) { ?><a href="<?php echo _url('#', array('page_'.$idlist=>1)); ?>" ><img src="<?php echo _resource('img/tools/first.png'); ?>" /></a><?php } ?>
<?php if ($currentPage>1) { ?><a href="<?php echo _url('#', array('page_'.$idlist=>($currentPage-1))); ?>" ><img src="<?php echo _resource('img/tools/previous.png'); ?>" /></a><?php } ?>
<?php echo $currentPage.'/'.$nbpages; ?>
<?php if ($currentPage < $nbpages) { ?><a href="<?php echo _url('#', array('page_'.$idlist=>($currentPage+1))); ?>"><img src="<?php echo _resource('img/tools/next.png'); ?>" /></a><?php } ?>
<?php if ($currentPage < $nbpages) { ?><a href="<?php echo _url('#', array('page_'.$idlist=>($nbpages))); ?>"><img src="<?php echo _resource('img/tools/last.png'); ?>" /></a><?php } ?>