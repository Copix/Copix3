{if $standalone=="non"}
<h2>{i18n key=copix:common.buttons.search}</h2>
{else}
<h1 class="hascorners">{i18n key=copix:common.buttons.search}<span class="cornertopleft"></span><span class="cornerbottomright"></span></h1>
{/if}
<form action="{$form_action}" method="get">
<input type="hidden" name="path" value="{$path}" />
<input type="hidden" name="theme" value="{$theme}" />
	<div class="span-10">
		<div class="span-3">
			<label for="main_criteria" class="obligatoire">Texte à rechercher</label>&nbsp;:
		</div>
		<div class="span-3">
			<input type="text" class="text" size="16" name="criteria" id="main_criteria" value="{$criteria|escape}" />
		</div>
		<div class="span-4 last">
			<label for="main_recherche" class="rollover">
				<input type="submit" class="submit rollover" id="main_recherche" value="{i18n key=copix:common.buttons.search}" />
				<span>{i18n key=copix:common.buttons.search}</span>
			</label>
		</div>
	</div>
</form>