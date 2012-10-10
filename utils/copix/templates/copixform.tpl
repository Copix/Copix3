{foreach from=$fields item=field}
{$field->getLabel()} : {$field->getHTML()}<br />
{/foreach}