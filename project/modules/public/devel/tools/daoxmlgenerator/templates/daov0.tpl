{if !$raw}
<h3>{$tname}.daodefinition.xml <input type="checkbox" name="table[]" value="{$tname}" checked="true" /> {i18n key="daoxmlgenerator.daofile.download"}</h3>
{highlighter language='xml'}
{if $xmlheader == 1}<?xml version="1.0" {if $iso}encoding="{$iso}"{/if}?>
{/if}
<daodefinition version="0">
<general>
   <table name="{$tname}" />
   <connection name="" />
</general>;

<fields>;
{foreach from=$arrtable item=table key=varKey}
   <field
      name="{$table->Field}"
      captionI18N="dao{$tname}.field.{$table->Field|strtolower}"
      pk="{$table->tpk}"
      type="{$table->type}"
      required="{$table->required}"
      maxlength="{$table->maxlength}"
   />
{/foreach}
</fields>
</daodefinition>

<br />
{else}
<daodefinition version="0">
<general>
   <table name="{$tname}" />
   <connection name="" />
</general>;

<fields>;
{foreach from=$arrtable item=table key=varKey}
   <field
      name="{$table->Field}"
      captionI18N="dao{$tname}.field.{$table->Field|strtolower}"
      pk="{$table->tpk}"
      type="{$table->type}"
      required="{$table->required}"
      maxlength="{$table->maxlength}"
   />
{/foreach}
</fields>
</daodefinition>

{/if}