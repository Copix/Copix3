<?php if($id_helt){ 
	$service = new RSSServices();
	$url = $service->getURL($id_helt);
	?>
	<a href="<?php echo $url; ?>">
	<img src="<?php echo _resource('cms_rss|img/icon_rss.png'); ?>" />
	<?php
	echo $portlet->getOption('caption_rss', "S'abonner au flux");
	?>
	</a>
<?php } else if($isAdmin){
	echo "Vous n'avez pas séléctionné de Flux RSS";
}
?>