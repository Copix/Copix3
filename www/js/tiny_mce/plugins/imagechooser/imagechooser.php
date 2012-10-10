<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Choix d'une image</title>
	<script type="text/javascript" src="../../tiny_mce_popup.js"></script>
	<script type="text/javascript" src="../../utils/mctabs.js"></script>
	<script type="text/javascript" src="js/imagechooser.js"></script>
	<link href="css/imagechooser.css" rel="stylesheet" type="text/css" />
</head>
<body>

<form onsubmit="ImageChooser.insert ();return false;" action="#" name="imageChooser">
<input type="hidden" name="public_id" value="<?php echo $_GET['public_id'] ?>" />
	<div class="tabs">
		<ul>
			<li id="image_tab" class="current"><span><a href="javascript:mcTabs.displayTab('image_tab','image_panel');" onmousedown="return false;">Image</a></span></li>
			<li id="thumbnail_tab"><span><a href="javascript:mcTabs.displayTab('thumbnail_tab','thumbnail_panel');" onmousedown="return false;">Miniature</a></span></li>
			<li id="appearance_tab"><span><a href="javascript:mcTabs.displayTab('appearance_tab','appearance_panel');" onmousedown="return false;">Apparence</a></span></li>
		</ul>
	</div>

	<div class="panel_wrapper">
		<div id="image_panel" class="panel current">
			<fieldset>
				<legend>Image</legend>
				<table style="width: 100%">
					<tr id="tr_source">
						<td style="width: 100px">Source</td>
						<td><?php echo $_GET['name'] ?></td>
					</tr>
					<tr>
						<td>Texte alternatif</td>
						<td><input type="text" name="alt" style="width: 99%" /></td>
					</tr>
					<tr>
						<td>Titre</td>
						<td><input type="text" name="title" style="width: 99%" /></td>
					</tr>
				</table>
			</fieldset>
			
			<br />
			<fieldset>
				<legend>Prévisualisation</legend>
				<div style="overflow: auto; width: 450px; height: 170px"><img src="<?php echo $_GET['src'] ?>" name="src" /></div>
			</fieldset>
		</div>

		<div id="thumbnail_panel" class="panel">
			<fieldset>
				<legend>Miniature</legend>
				<table style="width: 100%">
					<tr>
						<td style="width: 145px">Afficher une miniature</td>
						<td>
							<input type="radio" name="thumb_enabled" value="yes" id="thumb_enabled_yes" /><label for="thumb_enabled_yes">Oui</label>
							<input type="radio" name="thumb_enabled" value="no" id="thumb_enabled_no" checked="checked" /><label for="thumb_enabled_no">Non</label>
						</td>
					</tr>
					<tr>
						<td style="width: 120px">Dimensions</td>
						<td>
							<input type="text" name="thumb_width" style="width: 30px" />
							x <input type="text" name="thumb_height" style="width: 30px" /> px
						</td>
					</tr>
					<tr>
						<td style="width: 120px">Conserver les proportions</td>
						<td>
							<input type="radio" name="thumb_keep_proportions" value="yes" id="thumb_keep_proportions_yes" checked="checked" /><label for="thumb_keep_proportions_yes">Oui</label>
							<input type="radio" name="thumb_keep_proportions" value="no" id="thumb_keep_proportions_no"  /><label for="thumb_keep_proportions_no">Non</label>
						</td>
					</tr>
					<tr>
						<td>Affichage taille réelle</td>
						<td>
							<select name="thumb_show_image" id="thumb_show_image" onchange="javascript: showGalery ()">
								<option value="none">-- Aucun --</option>
								<option value="smoothbox">Galerie d'images (SmoothBox)</option>
								<option value="_blank">Nouvelle fenêtre</option>
							</select>
						</td>
					</tr>
				</table>
			</fieldset>

			<div id="galery">
				<br />
				<fieldset>
					<legend>Galerie d'images</legend>
					Pour créer une galerie d'images, vous devez donner le même identifiant de galerie à toutes les images que vous voulez afficher dans la même galerie.
					<br />
					Si aucun identifiant n'est spécifié, aucune galerie n'est créée, seule l'image en taille réelle sera affichée.
					<br /><br />
					<table style="width: 100%">
						<tr>
							<td style="width: 145px">Identifiant de la galerie</td>
							<td><input type="text" name="thumb_galery_id" style="width: 99%" /></td>
						</tr>
					</table>
				</fieldset>
			</div>
		</div>

		<div id="appearance_panel" class="panel">
			<fieldset>
				<legend>Apparence</legend>
				<table style="width: 100%">
					<tr>
						<td style="width: 120px">Alignement du texte</td>
						<td>
							<select name="align">
								<option value="">-- Aucun --</option>
								<option value="baseline">baseline</option>
								<option value="top">top</option>
								<option value="middle">middle</option>
								<option value="bottom">bottom</option>
								<option value="text-top">texttop</option>
								<option value="text-bottom">textbottom</option>
								<option value="left">left</option>
								<option value="right">right</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>Espacement vertical</td>
						<td><input type="text" name="vspace" style="width: 30px" /></td>
					</tr>
					<tr>
						<td>Espacement horizontal</td>
						<td><input type="text" name="hspace" style="width: 30px" /></td>
					</tr>
				</table>
			</fieldset>

			<br />
			<fieldset>
				<legend>Style</legend>
				<table style="width: 100%">
					<tr>
						<td style="width: 60px">Classe</td>
						<td><input type="text" name="classes" style="width: 99%" /></td>
					</tr>
					<tr>
						<td>Styles</td>
						<td><input type="text" name="stylesStr" style="width: 99%" /></td>
					</tr>
				</table>
			</fieldset>
		</div>
	</div>

	<div class="mceActionPanel">
		<div style="float: left">
			<input type="submit" id="insert" name="insert" value="{#insert}" />
		</div>

		<div style="float: right">
			<input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
		</div>
	</div>
</form>

<script type="text/javascript">
function showGalery () {
	document.getElementById ('galery').style.display = (document.getElementById ('thumb_show_image').value == 'smoothbox') ? '' : 'none';
}
showGalery ();
</script>

</body>
</html>