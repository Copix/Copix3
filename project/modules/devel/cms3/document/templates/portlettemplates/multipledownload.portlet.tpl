<form id="multipleDownload{$portlet->getRandomId()}" onsubmit="return validSelected('{$portlet->getRandomId()}');" action="{copixurl dest='heading||' content_disposition=attachement}" class="clearfix">
	<p>
		<a href="javascript:void(0);" onclick="selectAll('{$portlet->getRandomId()}');">Tout selectionner</a>
	</p>
	<ul class="documents">
	{foreach from=$elementsList item=element key=elementIndex}
		<li>
			<div>
				<input type="checkbox" name="public_id[]" id="doc_{$element->id_hei}" value="{$element->public_id_hei}" />
				<label for="doc_{$element->id_hei}" title="Ajouter {$element->caption_hei} dans le dossier compressé" title="Ajouter {$element->caption_hei} ({$element->file_document|substr:-3}, {$filesizes.$elementIndex}) aux téléchargements ">Ajouter ce document</label>
				<a href="{copixurl dest='heading||' public_id=$element->public_id}">
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
	<p>
		<a href="javascript:void(0);" onclick="selectAll('{$portlet->getRandomId()}');">Tout selectionner</a> <input type="submit" class="submit" value="Télécharger" />
	</p>
</form>
{literal}
<script type="text/javascript">
if( typeof selectAll == 'undefined' ){
	function selectAll( form ){
		$(form).getElements('input[type=checkbox]').each(
			function(el){
				el.checked = true;
			}
		);
	}
	
	function validSelected(form){
		var liste = $(form).getElements('input[type=checkbox]');
		for(i=0 ; i <liste.length ; i++){
			if(liste[i].checked == true){
				return true;
			}
		}
		return false;
	}
}
</script>
{/literal}