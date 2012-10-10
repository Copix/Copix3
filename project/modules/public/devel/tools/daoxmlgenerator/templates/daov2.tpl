{if $raw!=true}
<h3>{$tname}.daodefinition.xml <input type="checkbox" name="table[]" value="{$tname}" checked="true" /> {i18n key="daoxmlgenerator.daofile.download"}</h3>
{/if}
{if $xmlheader == 1}<?xml version="1.0" {if $iso}encoding="{$iso}" {/if}?>{/if}

<daodefinition version="2">
<datasource>
   <tables>
      <table name="{$tname}" primary="yes" />
   </tables>
</datasource>

<properties>
{foreach from=$arrtable item=table key=varKey}
   <property
      name="{$table->Field}"
      captionI18N="dao{$tname}.field.{$table->Field|strtolower}"
      pk="{$table->tpk}"
      type="{$table->type}"
      required="{$table->required}"
      maxlength="{$table->maxlength}"
   />
{/foreach}
</properties>
</daodefinition>
{if $raw!=true}
<br />
{/if}