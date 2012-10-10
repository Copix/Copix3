<ul class="documents clearfix">
{foreach from=$elementsList item=element key=elementIndex}
	<li>
		<div>
			<a href="{copixurl dest='heading||' public_id=$element->public_id_hei}">
				<span class="content {$element->file_document|substr:-3}">
					{$element->caption_hei}
						{if $element->description_hei}
						<br />{$element->description_hei}
						{/if}
					<br />{$filesizes.$elementIndex}
				</span>
			</a>
		</div>
	</li>
{/foreach}
</ul>