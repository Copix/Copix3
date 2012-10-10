Définit par
					<?php if ($theme_inherited_from === null) { ?>
						<a href="<?php echo _url ('admin|theme|') ?>">configuration de Copix</a>
					<?php } else if ($theme_inherited_from === false) { ?>
						cet élément
					<?php } else { ?>
						<a href="<?php echo _url ('heading|element|', array ('heading' => $theme_inherited_from->public_id_hei)) ?>"><?php echo $theme_inherited_from->caption_hei ?></a>
					<?php } ?>
<br />

<?php _eTag ('themechooser', array ('input' => 'theme_id', 'selected' => $theme->getId (), 'template'=>'portal|taglib/themechooser.php')); ?>