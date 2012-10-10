<?php
CopixHTMLHeader::addCSSLink (_resource ('heading|css/headingadmin.css'));
CopixHTMLHeader::addCSSLink (_resource ('heading|css/mycmscssconfig.css'));

_eTag("beginBlock"); ?>
Il existe déjà <?php echo count($drafts) > 1 ? count($drafts) . "brouillons" : 'un brouillon'; ?> 
pour l'élément <strong><?php echo $element->caption_hei; ?></strong> que vous souhaitez modifier.
<br />
<br />
Souhaitez vous éditer un brouillon, ou travailler sur la version publiée ?
<?php _eTag("endBlock"); ?>
<br />
	<div id="draftsHandler">
	<?php _eTag("beginBlock", array ('title' => 'Editer un brouillon existant')); ?>
	<table id="draftsTable" class="CopixVerticalTable draftsTable">
		<?php foreach ($drafts as $draft){ ?>
			<tr rel="<?php echo _url ('#', array ('id'=>$draft->id_helt)); ?>">
				<td class="action"><a href="<?php echo _url ('#', array ('id'=>$draft->id_helt)); ?>"><img src="<?php echo _resource('img/tools/update.png'); ?>" /></a></td>
				<td>
					<span class="status0"><strong><?php echo $draft->caption_hei; ?></strong></span><br />
					<em>Ce brouillon a été créé par <strong><?php echo $draft->author_caption_create_hei; ?></strong> le <strong><?php echo CopixDateTime::yyyymmddhhiissToDateTime ($draft->date_create_hei); ?></strong>
					<?php if ($draft->date_create_hei != $draft->date_update_hei) {?>
						et dernièrement modifié par <strong><?php echo $draft->author_caption_update_hei; ?></strong> le <strong><?php echo CopixDateTime::yyyymmddhhiissToDateTime ($draft->date_update_hei); ?></strong>
					<?php } ?>
					</em>
				</td>
			</tr>
		<?php }?>
	</table>
	<?php _eTag("endBlock"); ?>
	
	<?php _eTag("beginBlock", array ('title' => 'Travailler sur la version publiée')); ?>
	<table class="CopixVerticalTable draftsTable">
		<tr rel="<?php echo _url ('#', array ('id'=>$element->id_helt, 'newDraft'=>1)); ?>">
			<td class="action"><a href="<?php echo _url ('#', array ('id'=>$element->id_helt, 'newDraft'=>1)); ?>"><img src="<?php echo _resource('img/tools/update.png'); ?>" /></a></td>
			<td>
				<span class="status3"><strong><?php echo $element->caption_hei; ?></strong></span><br />
				<em>Cette version publiée a été créée par <strong><?php echo $element->author_caption_create_hei; ?></strong> le <strong><?php echo CopixDateTime::yyyymmddhhiissToDateTime ($element->date_create_hei); ?></strong>
				<?php if ($element->date_create_hei != $element->date_update_hei) {?>
					et dernièrement modifiée par <strong><?php echo $element->author_caption_update_hei; ?></strong> le <strong><?php echo CopixDateTime::yyyymmddhhiissToDateTime ($element->date_update_hei); ?></strong>
				<?php } ?>
				</em>
			</td>
		</tr>
	</table>
	<?php _eTag("endBlock"); ?>
	<br />
</div>
<?php echo _tag("back", array('url'=>_url ('element|', array ('heading'=>$element->parent_heading_public_id_hei)))); 
CopixHTMLHeader::addJSDOMReadyCode("
	$('draftsHandler').getElements('tr').each(function(el){
		el.setStyle('cursor','pointer');
		el.addEvent('click', function(){
			window.location.href = el.get('rel');
		});
		el.addEvent('mouseover', function(){
		console.debug(el);
			el.toggleClass('alternate');
		});
		el.addEvent('mouseout', function(){
			el.toggleClass('alternate');
		}.bind(this));
	});
");
?>