<tr class="bookmark<?php echo $element->public_id_hei . " " . _tag ('trclass', array ('nameOnly' => true, 'id' => 'bookmark')) ?>">
	<td style="width: 1px"><img src="<?php echo _resource ('heading|img/icon_headings.png') ?>" alt="Dossier" title="Dossier" /></td>
	<td>
		<?php if ($treeId) { ?>
			<a href="javascript:bookMarkSelectTree (<?php echo $element->public_id_hei ?>, '<?php echo $treeId ?>', '<?php echo implode (':', $filters) ?>')">
		<?php } else { ?>
			<a href="<?php echo _url ('heading|element|', array ('heading' => $element->public_id_hei)) ?>">
		<?php } ?>
		<?php echo $element->caption_hei ?>
		</a>
	</td>
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
	<td class="action">&nbsp;</td>
	<td class="action"><a href="javascript:void (0);" onClick="deleteBookmark ('<?php echo $element->public_id_hei ?>')"><?php _eTag ('copixicon', array ('type' => 'delete')) ?></a></td>
</tr>