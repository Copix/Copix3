<?php 
if ($ppo->error){
?>
<div class="errorMessage">
<h1><?php echo _i18n ('copix:common.messages.error'); ?></h1>
<p><?php echo $ppo->error;?></p>
</div>
<?php
}
?>
<p><?php 
echo _tag ('copixicon', array ('type'=>'home', 'href'=>_url ('default', array ('path'=>'./')))), 
 '&nbsp;', 
 _tag ('copixicon', array ('type'=>'refresh', 'href'=>_url ('default', array ('path'=>$ppo->basePath)))),
 '&nbsp;', 
 CopixZone::process ('PathExplore', array ('path'=>$ppo->basePath)); 
?></p>
<p>Libre : <?php echo $ppo->freeSpace;?> / <?php echo $ppo->totalSpace; ?></p>
<table class="CopixTable">
<tr>
 <th><a href="<?php echo _url ('setSortParams', array ('path'=>$ppo->basePath, 'sortby'=>FileSortParams::NAME_INSENSITIVE));?>">Nom</a></th>
 <th><a href="<?php echo _url ('setSortParams', array ('path'=>$ppo->basePath, 'sortby'=>FileSortParams::SIZE));?>">Taille</a></th>
 <th><a href="<?php echo _url ('setSortParams', array ('path'=>$ppo->basePath, 'sortby'=>FileSortParams::TYPE));?>">Type</a></th>
 <th>Action</th>
</tr>
<?php foreach ($ppo->arFiles as $fileInformations) { ?>
<tr <?php _eTag ('cycle', array ('values'=>',class="alternate"')); ?>>
<td>
<?php if ($isDir = is_dir ($ppo->basePath.$fileInformations)){ 
		echo '<img src="'._resource ('img/mimetypes/folder.png').'" /><a href="'._url ('#', array ('path'=>$ppo->basePath.$fileInformations)).'">';
	  }
	  echo  $fileInformations;
	  if ($isDir) {
	   	echo "</a>";
	  }?></td>
	  <td><?php echo $fileInformations->getSize (); ?></td>
	  <td><?php if ($fileInformations->isFile ()) { ?><img src="<?php echo _resource ($fileInformations->getFileIcon ()); ?>" alt="<?php echo $fileInformations->getFileExtension (); ?>" /><?php } ?></td>
<td>
    <?php if (!$isDir){ ?>
    <a href="<?php echo _url ('show', array ('file'=>$fileInformations->getFilepath ())); ?>" /><img src="<?php echo _resource ('img/tools/show.png'); ?>" /></a>
	<a href="<?php echo _url ('download', array ('file'=>$fileInformations->getFilepath ())); ?>" /><img src="<?php echo _resource ('img/tools/download.png'); ?>" /></a>
	<?php } ?>
	<?php if ($fileInformations->isWritable ()){ ?>
	 <a href="<?php echo _url ('delete', array ('file'=>$fileInformations->getFilepath ())); ?>" /><img src="<?php echo _resource ('img/tools/delete.png'); ?>" /></a>
    <?php } ?>
	<?php _eTag ('popupinformation', array ('zone'=>'fileexplorer|fileproperties', 'img'=>_resource ('img/tools/properties.png'), 'file'=>$fileInformations->getFilepath ())); ?>
</td>
</tr>
<?php } ?>
</table>
<?php if (CopixConfig::get ('compressEnabled')) { ?>
<p><a href="<?php echo _url ('compress', array ('path'=>$ppo->basePath)); ?>"><img src="<?php echo _resource ('img/mimetypes/archive.png'); ?>" />&nbsp;<?php echo _i18n ('fileexplorer.getArchive'); ?></a></p>
<?php } ?>
<?php 
if ($ppo->basePathDescription->isWritable ()){
echo '<p>', _tag ('copixicon', array ('type'=>'newfolder')), '&nbsp;', _i18n ('fileexplorer.createdir'), '</p>';
?>
<form action="<?php echo _url ('createDir', array ('path'=>$ppo->basePath)); ?>" method="POST">
   <input type="text" name="dirname" />
   <input type="submit" value="<?php echo _i18n ('copix:common.buttons.ok'); ?>" />
</form>

<?php echo '<p>', _tag ('copixicon', array ('type'=>'newdocument')), '&nbsp;',_i18n ('fileexplorer.createfile'), '</p>';?>
<form action="<?php echo _url ('createFile', array ('path'=>$ppo->basePath)); ?>" method="POST" enctype="multipart/form-data">
   <input type="text" name="filename" />
   <input type="submit" value="<?php echo _i18n ('copix:common.buttons.ok'); ?>" />
</form>

<?php echo '<p>', _tag ('copixicon', array ('type'=>'upload')), '&nbsp;',_i18n ('fileexplorer.uploadfile'), '</p>';?>
<form action="<?php echo _url ('UploadFile', array ('path'=>$ppo->basePath)); ?>" method="POST" enctype="multipart/form-data">
   <input type="file" name="upload" />
   <input type="submit" value="<?php echo _i18n ('copix:common.buttons.ok'); ?>" />
</form>
<?php
}
?>