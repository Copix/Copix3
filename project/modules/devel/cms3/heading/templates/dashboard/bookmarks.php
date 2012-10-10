<div class="cmsbloc" id="bookmarks" style="display: <?php echo ($show) ? 'block' : 'none' ?>">
	<div class="cmsbloc_title">		
		<div id="handlebookmarks" class="widgethandle">
			<img src="<?php echo _resource ('img/tools/bookmark.png') ?>" alt="Favoris" title="Favoris" />
			Favoris
		</div>
		<div class="showdivDashboard" id="showdivbookmarks"><?php _eTag ('showdiv', array ('id' => 'dashboardbookmarks', 'userpreference' => 'heading|dashboard|bookmarks')) ?></div>
	</div>
	<div style="display: <?php echo (CopixUserPreferences::get ('heading|dashboard|bookmarks', true)) ? 'block' : 'none' ?>" class="cmsbloc_content" id="dashboardbookmarks">
		<?php if (count ($arElements) == 0) { ?>
			Pour ajouter un favori, naviguez dans le dossier à ajouter, et cliquez sur l'icone des favoris dans la barre de navigation, à gauche du fil d'ariane.
		<?php } else { ?>
			<table class="CopixTable">
				<tr>
					<th colspan="2">Elément</th>
					<th colspan="4">Ajouter</th>
				</tr>
				<?php foreach ($arElements as $element) { ?>
					<tr>
						<td style="width: 1px"><img src="<?php echo _resource ('heading|img/icon_headings.png') ?>" alt="Dossier" title="Dossier" /></td>
						<td><a href="<?php echo _url ('heading|element|', array ('heading' => $element->public_id_hei)) ?>"><?php echo $element->caption_hei ?></a></td>
						<td class="action">
							<a href="<?php echo _url ('heading|element|preparecreate', array ('type' => 'page', 'heading' => $element->public_id_hei)) ?>">
								<img src="<?php echo _resource ('portal|img/icon_page.png') ?>" alt="Page" title="Ajouter une page" />
							</a>
						</td>
						<td class="action">
							<a href="<?php echo _url ('heading|element|preparecreate', array ('type' => 'article', 'heading' => $element->public_id_hei)) ?>">
								<img src="<?php echo _resource ('articles|img/icon_articles.png') ?>" alt="Article" title="Ajouter un article" />
							</a>
						</td>
						<td class="action">
							<a href="<?php echo _url ('heading|element|preparecreate', array ('type' => 'document', 'heading' => $element->public_id_hei)) ?>">
								<img src="<?php echo _resource ('document|img/icon_document.png') ?>" alt="Document" title="Ajouter un document" />
							</a>
						</td>
						<td class="action">
							<a href="<?php echo _url ('heading|element|preparecreate', array ('type' => 'image', 'heading' => $element->public_id_hei)) ?>">
								<img src="<?php echo _resource ('images|img/icon_images.png') ?>" alt="Image" title="Ajouter une image" />
							</a>
						</td>
					</tr>
				<?php } ?>
			</table>
		<?php } ?>
	</div>
</div>