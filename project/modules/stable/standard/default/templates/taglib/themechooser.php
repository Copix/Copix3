<div id="<?php echo $clicker ?>" style="cursor: pointer" title="Sélectionner un thème">
	<?php if ($showName) { ?>
		<span id="<?php echo $clicker ?>Name"><?php echo ($selected !== false) ? $selected->getName () : null ?></span>
		<br />
	<?php } ?>
	<img src="<?php echo ($selected !== false) ? $selected->getImage () : _resource ('img/selectTheme.png') ?>" alt="Sélectionner un thème" title="Sélectionner un thème" width="200px" id="<?php echo $clicker ?>Image" />
</div>
<input type="hidden" name="<?php echo $input ?>" id="<?php echo $input ?>" value="<?php if ($selected !== false) echo $selected->getId () ?>" />

<?php ob_start () ?>
<div style="overflow: auto !important; overflow: scroll; overflow-y: scroll; height: auto !important; height: 450px; max-height: 450px; padding-right: 10px">
<table class="CopixTable">
	<tr>
		<th style="width: 1px">Miniature</th>
		<th class="last">Informations</th>
	</tr>
	<?php foreach ($themes as $theme) { ?>
		<tr <?php _eTag ('trclass', array ('id' => 'themes')) ?>>
			<td><img src="<?php echo $theme->getImage () ?>" alt="<?php echo $theme->getName () ?>" title="<?php echo $theme->getName () ?>" /></td>
			<td style="text-align: center">
				<strong><?php echo $theme->getName () ?></strong>
				<br />
				<?php echo $theme->getDescription () ?>
				<br /><br />
				Auteur : <?php echo $theme->getAuthor () ?>
				<?php if ($theme->getWEBSite () != null) { ?>
					<br />
					Site WEB : <a href="<?php echo $theme->getWEBSite () ?>" target="_blank"><?php echo $theme->getWEBSite () ?></a>
				<?php } ?>
				<br /><br />
				<center>
					<?php
					_eTag ('button', array (
						'caption' => 'Sélectionner ce thème',
						'img' => 'heading|img/togglers/theme.png',
						'type' => 'button',
						'id' => 'selectTheme' . $theme->getId ()
					));

					$id = $theme->getId ();
					$nameJS = str_replace ("'", '\'', $theme->getName ());
					$idNameJS = $clicker . 'Name';
					$idImageJS = $clicker . 'Image';
					$srcImage = $theme->getImage ();
					$idWindowJS = $clicker . 'Window';
					$js = <<<JS
					$ ('selectTheme$id').addEvent ('click', function (pEl) {
						$ ('$input').value = '$id';
						$ ('$input').fireEvent ('change', {target: $ ('$input')});
						if ($ ('$idNameJS') != undefined) {
							$ ('$idNameJS').innerHTML = '$nameJS';
						}
						$ ('$idImageJS').src = '$srcImage';
						Copix.get_copixwindow ('$idWindowJS').close ();
					});
JS;
					CopixHTMLHeader::addJSDOMReadyCode ($js);
					?>

				</center>
			</td>
		</tr>
	<?php } ?>
</table>
</div>
<?php
$content = ob_get_clean ();
_etag ('copixwindow', array ('clicker' => $clicker, 'fixed' => 1, 'title' => 'Sélectionner un thème', 'id' => $clicker . 'Window'), $content);
?>