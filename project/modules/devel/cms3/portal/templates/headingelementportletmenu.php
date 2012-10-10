<div class="portalGeneralMenu highlight">
	<ul class="portalGeneralMenuList">
		<?php if ($isEdition) { ?>
			<li>
				<a href="<?php echo _url ('adminportlet|edit', array ('editId' => $editId)); ?>">
					<img src="<?php echo _resource ('img/tools/information.png') ?>" />
					Informations générales
				</a>
			</li>
		<?php } ?>
		<?php if (!$isEdition || $renderContext != RendererContext::UPDATED) { ?>
			<li>
				<a href="<?php echo _url ('portal|adminportlet|DisplayPortlet', array ('editId' => _request ('editId'), 'etat' => Portlet::UPDATED)); ?>">
					<img src="<?php echo _resource ('heading|img/edit_mode.png') ?>" />
					Edition
				</a>
			</li>
		<?php } ?>
		<?php if ($isEdition) { ?>
			<li>
				<a href="<?php echo _url ('portal|adminportlet|valid', array ('editId' => _request('editId'))); ?>">
					<img src="<?php echo _resource ('img/tools/save.png'); ?>" alt="" />
					Sauvegarder
				</a>
			</li>
			<li>
				<a onclick="return window.confirm('Annuler ? Les modifications en cours seront perdues !')" href="<?php echo _url ('portal|adminportlet|cancel', array ('editId' => _request ('editId'))); ?>">
					<img src="<?php echo _resource ('img/tools/undo.png'); ?>" alt="" />
					Annuler
				</a>
			</li>
			<?php if ($renderContext == RendererContext::UPDATED) { ?>
				<li>
					<a href="<?php echo _url ('portal|adminportlet|DisplayPortlet', array ('editId' => _request ('editId'), 'etat' => Portlet::DISPLAYED)); ?>">
						<img src="<?php echo _resource ('img/tools/show.png'); ?>" alt="" />
						Aperçu
					</a>
				</li>
			<?php } ?>
		<?php } else { ?>
			<li>
				<a href="<?php echo _url ('portal|adminportlet|DisplayPortlet', array ('editId' => _request ('editId'))); ?>">
					<img src="<?php echo _resource ('img/tools/show.png'); ?>" alt="" />
					Aperçu
				</a>
			</li>
		<?php } ?>
	</ul>
	<div id="loading_img" class="loading_img" style="display:none;">
		<img src="<?php echo _resource ('img/tools/load.gif'); ?>" />
	</div>
	<div class="clear"></div>
</div>