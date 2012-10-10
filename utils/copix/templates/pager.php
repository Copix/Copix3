<?php 
$list = CopixListFactory::get ($listid);
if ($list->getNbPage() > 1) { ?>
<a href="javascript:list.get('<?php echo $listid ;?>').goto('first');" ><img src="<?php echo _resource('img/tools/first.png'); ?>" /></a>
<a href="javascript:list.get('<?php echo $listid ;?>').goto('previous');" ><img src="<?php echo _resource('img/tools/previous.png'); ?>" /></a>
 <?php  echo $list->getCurrentPage().' / '.$list->getNbPage(); ?> 
<a href="javascript:list.get('<?php echo $listid ;?>').goto('next');"><img src="<?php echo _resource('img/tools/next.png'); ?>" /></a>
<a href="javascript:list.get('<?php echo $listid ;?>').goto('last');"><img src="<?php echo _resource('img/tools/last.png'); ?>" /></a>
<?php } ?>