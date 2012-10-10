<h2 class="first"><?php _etag ('i18n', 'default.officialLinks'); ?></h2>
<ul>
 <li><a href="http://www.copix.org">Copix.org</a> le site officiel.</li>
 <li>Des <a href="http://www.copix.org/index.php/wiki/Tutoriaux">tutoriaux</a> pour comprendre les principes de base.</li>
 <li>La <a href="http://www.copix.org/index.php/wiki/documentation">documentation complète</a> pour comprendre tout Copix.</li>
 <li>La <a href="http://phpdoc.copix.org">documentation technique</a> générée toute les nuits à partir de la version CVS.</li>
</ul>

<h2><?php _etag ('i18n', 'default.quickStart'); ?></h2>
<ul>
 <?php if ($ppo->dbOK === false){ ?>
 <li><a href="<?php echo _url ('admin|database|') ?>"> <?php _etag ('i18n', 'default.configureDB'); ?></a></li>
 <?php }else{ ?>
 <li><a href="<?php echo _url ('admin|database|') ?>"> <?php _etag ('i18n', 'default.configureMoreDB'); ?></a></li>
 <?php } ?>
 <li><a href="<?php echo _url ('admin||') ?>"> <?php _etag ('i18n', 'default.admin'); ?></a></li>
</ul>