<h2>{i18n key='Updating Tag [%s]' param1=$ppo->nameTag}</h2>

<div class="menu-onglet"> <!-- début de la boite contenant les onglets -->
    <a class="onglet" href="{copixurl dest='admin|edit' namespace=$ppo->namespace name_tag=$ppo->nameTag}">{i18n key='tags.onglet.general'}</a>
    <a class="onglet" href="{copixurl dest='admin|editMetaInformations' namespace=$ppo->namespace name_tag=$ppo->nameTag}">{i18n key='tags.onglet.attached'}</a>
    <span class="onglet-actif">{i18n key='tags.onglet.associations'}</span>
</div>

<br />


{if count ($ppo->arAsociation) ne 0 }
<table class="CopixVerticalTable">
    <tr>
        <th>{i18n key='tags.kindobject'}</th>
        <th>{i18n key='tags.idobject'}</th>
    </tr>
    {foreach from=$ppo->arAsociation item=association} 
    <tr>
        <td>{$association->kindobj_tag|escape}</td>
        <td>{$association->idobj_tag|escape}</td>
    </tr>
    {/foreach}
</table>
<br />
{/if}

<form action="{copixurl dest='admin|update' namespace=$ppo->namespace name_tag=$ppo->nameTag}" id="formGeneral" method="post" > 
    <div>
            <input type="image" name="update" src="{copixresource path='img/tools/ok.png'}" value="{i18n key='copix:common.buttons.ok'}" alt="{i18n key='copix:common.buttons.ok'}" />
            <a>{i18n key='copix:common.buttons.ok'}</a>
        <a href="{copixurl dest='admin|view' namespace=$ppo->namespace}" ><img src="{copixresource path='img/tools/cancel.png'}" alt="{i18n key='copix:common.buttons.cancel'}"/>{i18n key='copix:common.buttons.cancel'}</a>
    </div>
</form>