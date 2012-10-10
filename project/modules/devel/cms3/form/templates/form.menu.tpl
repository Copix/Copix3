<div id="portalGeneralMenu" class="portalGeneralMenu highlight">
	<ul class="portalGeneralMenuList">
		{if $mode != 'main'}
		<li>
			<a href="{copixurl dest='admin|edit' editId='editId'|request}">
				<img src="{copixresource path='heading|img/generalicons/cms_info.png'}" />
				Informations générales
			</a>
		</li>	
		{/if}
		{if $mode != 'content'}
		<li>
			<a href="{copixurl dest='admin|editContent' editId='editId'|request}">
				<img src="{copixresource path='heading|img/generalicons/cms_info.png'}" />
				Edition du contenu
			</a>
		</li>
		{/if}
		<li>
			<a href="{copixurl dest='admin|valid' editId='editId'|request}">
				<img src="{copixresource path='img/tools/save.png'}"/>
				Sauvegarder le formulaire
			</a>
		</li>
		<li>
			<a href="{copixurl dest='admin|cancel' editId='editId'|request}" onclick="if (window.confirm('Annuler ? Les modifications en cours seront perdues !')){ldelim}return true;{rdelim}else{ldelim}return false;{rdelim}">
				<img src="{copixresource path='img/tools/undo.png'}" />
				Annuler
			</a>
		</li>
		{if $mode != 'display'}
		<li>	
			<a href="{copixurl dest='admin|display' editId='editId'|request}">
				<img src="{copixresource path='heading|img/generalicons/cms_show.png'}" />
				Aperçu
			</a>
		</li>
		{/if}
	</ul>
	<div id="loading_img" class="loading_img" style="display:none;">
		<img src="{copixresource path='img/tools/load.gif'}" />
	</div>
	<div class="clear"></div>
</div>
<br/>