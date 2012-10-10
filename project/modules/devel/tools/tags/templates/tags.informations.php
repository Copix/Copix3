<?php if (is_array ($tags)) { 
	echo CopixZone::process ('heading|headingelement/HeadingElementInformationTitle', array ('title' => 'Tags', 'icon' => _resource ('tags|img/icon/tags.toggler.png'))) ?>
	<div class="element">
		<dl>
			<dt>Tags parents</dt>
				<dd>
					<input type="checkbox" name="tags[inherited]" id="tags_inherited" value="1" <?php if ($record->tags_parent_inherited) { ?>checked="checked"<?php } ?> />
					<label for="tags_inherited">Hérite les tags <?php echo (isset($record->tags_parent_caption) && $record->tags_parent_caption) ? 'de "' . $record->tags_parent_caption . '"' : 'de ses parents' ?></label>
				</dd>
				<dd>
					<?php if ($record->tags_parent_tags_inherited) { ?>
						Tags parents : <?php echo implode (', ', $record->tags_parent_tags_inherited) ?>.
					<?php } else { ?>
						Aucun tag hérité.
					<?php } ?>
				</dd>
			<dt>
				<label for="tags_new">Nouveaux tags :</label>
			</dt>
			<dd>
				<input type="text" class="text" name="tags[new]" id="tags_new" />
			</dd>
			<dt>Tags disponibles :</dt>
				<dd>
					<?php
					foreach ($tags as $i => $tag) { ?>
						<label for="tags_selected_<?php echo $i ?>" <?php if (array_key_exists('class', $tag)) echo 'class="' . $tag['class'] . '"'; ?>></label>
							<input type="checkbox" name="tags[selected][]" id="tags_selected_<?php echo $i ?>" value="<?php echo $tag['caption'] ?>"
								<?php if ($tag['checked'] == 'true') { ?>checked="checked"<?php } ?>
							/>
							<?php echo $tag['caption'] ?>,
					<?php } ?>
				</dd>
		</dl>
	</div>
<?php } ?>