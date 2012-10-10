<p><?php echo $ppo->basePath; ?></p>

<table class="CopixTable">
<tr>
 <th>Nom</th>
 <th>Action</th>
</tr>
<?php foreach ($ppo->arFiles as $fileInformations) { ?>
<tr>
<td>
<?php if ($test = is_dir ($ppo->basePath.$fileInformations)){ 
		echo '<a href="'._url ('#', array ('path'=>$ppo->basePath.$fileInformations)).'">';
	  }
	  echo $fileInformations;
	  if ($test) {
	   	echo "</a>";
	  }?></td>
<td>
    <?php if (!$test){ ?>
    <a href="<?php echo _url ('show', array ('file'=>$ppo->basePath.$fileInformations)); ?>" /><img src="<?php echo _resource ('img/tools/show.png'); ?>" /></a>
	<a href="<?php echo _url ('download', array ('file'=>$ppo->basePath.$fileInformations)); ?>" /><img src="<?php echo _resource ('img/tools/download.png'); ?>" /></a>
	<?php } ?>
</td>
</tr>
<?php } ?>
</table>