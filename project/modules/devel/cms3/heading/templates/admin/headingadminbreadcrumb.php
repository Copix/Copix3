<?php echo CopixZone::process ('heading|headingelementchooser', array ('identifiantFormulaire' => 'headingAdmin', 'clickMod' => 1, 'showSelection' => false, 'fixed' => false, 'canDrag' => true)) ?>
<?php foreach ($breadcrumb as $key => $value) { ?>
	<a href="<?php echo _url ('heading|element|default', array ('heading' => $key)) ?>"><?php echo $value ?></a>
	<?php echo CopixZone::process ('heading|HeadingMenu', array ('heading' => $key)) ?>
<?php } ?>