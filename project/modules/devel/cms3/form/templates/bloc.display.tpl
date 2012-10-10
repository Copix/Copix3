{*} Affichage des champs {/*}
<div id="{$ppo->idBloc}" class="form_bloc">
    {foreach from=$ppo->fields item=field name=fields}
        {$field->getRow()}
    {/foreach}
</div>
