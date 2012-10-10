<list>
{foreach from=$ppo->arData item=data}
<record>
<field name="{$data->type_test}"><![CDATA[{$data->caption_typetest}]]></field>
</record>
{/foreach}
</list>