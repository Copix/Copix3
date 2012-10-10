<ul class="tag_list">
<?php
foreach ($arrTags AS $lineTag) {
	if ($doGetLink) {
	?>
		<li><a href="<?php echo CopixUrl::appendToUrl($linkCorpse,array($linkParam=>$lineTag)); ?>"><?php echo $lineTag; ?></a></li>
	<?php 
	} else {
	?>
		<li><?php echo $lineTag; ?></li>
	<?php 
	} 
}
?>
</ul>