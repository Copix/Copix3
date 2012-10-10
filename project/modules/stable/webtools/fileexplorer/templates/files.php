<?php
CopixHTMLHeader::addJSLink (_resource ('|js/fileexplorer.js'));
if ($ppo->error) {
	?>
	<div class="errorMessage">
		<h1><?php echo _i18n ('copix:common.messages.error'); ?></h1>
		<p><?php _eTag ('ulli', array ('values'=>$ppo->error)); ?></p>
	</div>
	<?php
}
?>
<p>
<?php 
echo _tag ('copixicon', array ('type' => 'home', 'href' => _url ('default', array ('path'=>'./'))));
echo '&nbsp;'; 
echo _tag ('copixicon', array ('type' => 'refresh', 'href' => _url ('default', array ('path'=>$ppo->basePath))));
echo '&nbsp;'; 
echo CopixZone::process ('PathExplore', array ('path' => $ppo->basePath)); 
?>
</p>

<p>Libre : <?php echo $ppo->freeSpace; ?> / <?php echo $ppo->totalSpace; ?></p>
<form action="<?php echo _url ('delete') ?>" method="POST">
<table class="CopixTable">
	<thead>
		<tr>
			<th>
				<a onclick="checkUncheck()" title="Tout Sélectionner / Tout Déselectioner"><?php echo _etag('copixicon', array ('type' => 'select', 'alt'=>"Tout Sélectionner / Tout Déselectioner")) ?></a>
				<a href="<?php echo _url ('setSortParams', array ('path'=>$ppo->basePath, 'sortby'=>FileSortParams::NAME_INSENSITIVE));?>">Nom</a>
			</th>
			<th><a href="<?php echo _url ('setSortParams', array ('path'=>$ppo->basePath, 'sortby'=>FileSortParams::SIZE));?>">Taille</a></th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
	<?php
	foreach ($ppo->arFiles as $fileInformations) {
		?>
		<tr <?php _eTag ('cycle', array ('values'=>',class="alternate"')); ?>>
			<td>
				<input name="file[]" type="checkbox" value="<?php echo $fileInformations->getFilepath (); ?>" />
				<?php if ($fileInformations->isFile ()) { ?>
				<a href="<?php echo _url ('show', array ('file' => $ppo->basePath . $fileInformations)); ?>">
					<img src="<?php echo _resource ($fileInformations->getFileIcon ()); ?>" alt="<?php echo $fileInformations->getFileExtension (); ?>" />
					<?php echo $fileInformations; ?>
				</a>
				<?php
				}
				if ($isDir = is_dir ($ppo->basePath.$fileInformations)) {
				?>
				<a href="<?php echo _url ('#', array ('path' => $ppo->basePath . $fileInformations)); ?>">
					<img src="<?php echo _resource ('fileexplorer|img/mimetypes/folder.png'); ?>" alt="Répertoire" />
					<?php echo $fileInformations; ?>
				</a>
				<?php } ?>
			</td>
			<td><?php echo $fileInformations->getSize (); ?></td>
			<td>
				<?php
				if (!$isDir) {
					?>
					<a href="<?php echo _url ('download', array ('file' => $fileInformations->getFilepath ())); ?>" ><img src="<?php echo _resource ('img/tools/download.png'); ?>" /></a>
					<?php
				}
				if ($fileInformations->isWritable ()) {
					?>
					<a href="<?php echo _url ('delete', array ('file' => $fileInformations->getFilepath ())); ?>" ><img src="<?php echo _resource ('img/tools/delete.png'); ?>" /></a>
					<?php
				}
				_eTag ('popupinformation', array ('zone'=>'fileexplorer|fileproperties', 'img' => _resource ('img/tools/properties.png'), 'file' => $fileInformations->getFilepath ()));
				?>
			</td>
		</tr>
		<?php
	}
	?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="3">
				<img src="<?php echo _resource ('|img/with_selection.png'); ?>" alt="-&gt;" /><input type="image" alt="delete" src="<?php echo _resource ('img/tools/delete.png'); ?>" />
			</td>
		</tr> 
	</tfoot>
</table>
</form>

<?php
if (CopixConfig::get ('compressEnabled')) {
	?>
	<p><a href="<?php echo _url ('compress', array ('path' => $ppo->basePath)); ?>"><img src="<?php echo _resource ('img/mimetypes/archive.png'); ?>" alt="" />&nbsp;<?php echo _i18n ('fileexplorer.getArchive'); ?></a></p>
	<?php
}

if ($ppo->basePathDescription->isWritable ()) {
	?>
	<form action="<?php echo _url ('createDir', array ('path'=>$ppo->basePath)); ?>" method="POST">
		<p>
			<label for="dirname">
				<?php echo _tag ('copixicon', array ('type'=>'newfolder')), '&nbsp;', _i18n ('fileexplorer.createdir'); ?>
			</label>
		</p>
		<input type="text" name="dirname" id="dirname" />
		<input type="submit" value="<?php echo _i18n ('copix:common.buttons.ok'); ?>" />
	</form>
	
	<form action="<?php echo _url ('createFile', array ('path'=>$ppo->basePath)); ?>" method="POST" enctype="multipart/form-data">
		<p>
			<label for="filename">
				<?php echo _tag ('copixicon', array ('type'=>'newdocument')), '&nbsp;', _i18n ('fileexplorer.createfile'); ?>
			</label>
		</p>
	   <input type="text" name="filename" id="filename" />
	   <input type="submit" value="<?php echo _i18n ('copix:common.buttons.ok'); ?>" />
	</form>
	
	<form action="<?php echo _url ('UploadFile', array ('path'=>$ppo->basePath)); ?>" method="POST" enctype="multipart/form-data">
		<p>
			<label for="upload">
				<?php echo _tag ('copixicon', array ('type'=>'upload')), '&nbsp;', _i18n ('fileexplorer.uploadfile'); ?>
			</label>
		</p>
	   <input type="file" name="upload" id="upload" />
	   <input type="submit" value="<?php echo _i18n ('copix:common.buttons.ok'); ?>" />
	</form>
	<?php
}
echo CopixZone::process('fileexplorer|permission', array('file' => $ppo->basePath));
?>