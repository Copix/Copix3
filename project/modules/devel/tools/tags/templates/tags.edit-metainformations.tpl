{error message=$ppo->errors}

<h2>{i18n key='Updating Tag [%s]' param1=$ppo->nameTag}</h2>
<div class="menu-onglet"> <!-- début de la boite contenant les onglets -->
    <a class="onglet" href="{copixurl dest='admin|edit' namespace=$ppo->namespace name_tag=$ppo->nameTag}">{i18n key='tags.onglet.general'}</a>
    <span class="onglet-actif">{i18n key='tags.onglet.attached'}</span>
{if $ppo->admin}
    <a class="onglet" href="{copixurl dest='admin|editAssociations' namespace=$ppo->namespace name_tag=$ppo->nameTag}">{i18n key='tags.onglet.associations'}</a>
{/if}
</div>

<br />

<form action="{copixurl dest='admin|addMeta' namespace=$ppo->namespace name_tag=$ppo->nameTag}" method="post" >
    <h3>{i18n key='copix:common.buttons.link'} :</h3>
    <table class="CopixVerticalTable">
        <tr>
            <th>{i18n key='copix:common.messages.title'}</th>
            <th>{i18n key='tags.title.relation'}</th>
            <th>{i18n key='copix:common.buttons.link'}</th>
            <th style="width:50px;">{i18n key='copix:common.actions.title'}</th>
        </tr>
{foreach from=$ppo->arLink key=id item=link}
    {if $ppo->link_id ne $id}
        <tr>
            <td>{$link->title|escape}</td>
            <td>{$link->rel|escape}</td>
            <td>{$link->link|escape}</td>
            <td>
                <a href="{copixurl dest='admin|editMetaInformations' link_id=$id namespace=$ppo->namespace name_tag=$ppo->nameTag}" ><img src="{copixresource path='img/tools/update.png'}" alt="{i18n key='copix:common.buttons.update'}"/></a>
                <a href="{copixurl dest='admin|deleteMeta' link_id=$id namespace=$ppo->namespace name_tag=$ppo->nameTag}" ><img src="{copixresource path='img/tools/delete.png'}" alt="{i18n key='copix:common.buttons.delete'}"/></a>
            </td>
        </tr>
    {else}
        <tr>
            <td><input type="text" name="editTitle" value="{if $ppo->nTitle === null}{$link->title}{else}{$ppo->nTitle}{/if}" style="width:98%;" /></td>
            <td><input type="text" name="editRel" value="{if $ppo->nRel === null}{$link->rel}{else}{$ppo->nRel}{/if}" style="width:98%;" /></td>
            <td><input type="text" name="editLink" value="{if $ppo->nLink === null}{$link->link}{else}{$ppo->nLink}{/if}" style="width:98%;" /></td>
            <td>
                <input type="image" name="link_id" src="{copixresource path='img/tools/ok.png'}" alt="{i18n key='copix:common.buttons.ok'}" value="{$id}"/>
                <a href="{copixurl dest='admin|editMetaInformations' namespace=$ppo->namespace name_tag=$ppo->nameTag}" ><img src="{copixresource path='img/tools/cancel.png'}" alt="{i18n key='copix:common.buttons.cancel'}"/></a>
            </td>
        </tr>
    {/if}
{/foreach}
{foreach from=$ppo->arNewLink key=id item=link}
    {if $ppo->link_nid ne $id}
        <tr>
            <td>{$link->title}</td>
            <td>{$link->rel}</td>
            <td>{$link->link}</td>
            <td>
                <a href="{copixurl dest='admin|editMetaInformations' link_nid=$id namespace=$ppo->namespace name_tag=$ppo->nameTag}" ><img src="{copixresource path='img/tools/update.png'}" alt="{i18n key='copix:common.buttons.update'}"/></a>
                <a href="{copixurl dest='admin|deleteMeta' link_nid=$id namespace=$ppo->namespace name_tag=$ppo->nameTag}" ><img src="{copixresource path='img/tools/delete.png'}" alt="{i18n key='copix:common.buttons.delete'}"/></a>
            </td>
        </tr>
    {else}
        <tr>
            <td> <input type="text" name="editTitle" value="{if $ppo->nTitle === null}{$link->title}{else}{$ppo->nTitle}{/if}" style="width:98%;" /></td>
            <td> <input type="text" name="editRel" value="{if $ppo->nRel === null}{$link->rel}{else}{$ppo->nRel}{/if}" style="width:98%;" /></td>
            <td> <input type="text" name="editLink" value="{if $ppo->nLink === null}{$link->link}{else}{$ppo->nLink}{/if}" style="width:98%;" /></td>
            <td>
                 <input type="image" name="link_nid" src="{copixresource path='img/tools/ok.png'}" alt="{i18n key='copix:common.buttons.ok'}" value="{$id}"/>
                <a href="{copixurl dest='admin|editMetaInformations' namespace=$ppo->namespace name_tag=$ppo->nameTag}" ><img src="{copixresource path='img/tools/cancel.png'}" alt="{i18n key='copix:common.buttons.cancel'}"/></a>
            </td>
        </tr>
    {/if}
 {/foreach}
        <tr>
            <td> <input type="text" name="title" value="{$ppo->title}" style="width:98%;" /></td>
            <td> <input type="text" name="rel" value="{$ppo->rel}" style="width:98%;" /></td>
            <td> <input type="text" name="link" value="{$ppo->link}" style="width:98%;" /></td>
            <td> <input type="image" name="addLink" value="{i18n key='copix:common.buttons.ok'}" alt="{i18n key='copix:common.buttons.ok'}" src="{copixresource path='img/tools/add.png'}" /></td>
        </tr>
    </table>
</form>
<br />

<form action="{copixurl dest='admin|addMeta' namespace=$ppo->namespace name_tag=$ppo->nameTag}" method="post" >
    <h3>{i18n key='tags.title.keyword'} :</h3>
    <table class="CopixVerticalTable">
        <tr>
            <th>{i18n key='tags.title.keyword'}</th>
            <th style="width:50px;">{i18n key='copix:common.actions.title'}</th>
        </tr>
{foreach from=$ppo->arKeyword key=id item=keyword}
    {if $ppo->keyword_id ne $id}
        <tr>
            <td>{$keyword->texte|escape}</td>
            <td>
                <a href="{copixurl dest='admin|editMetaInformations' keyword_id=$id namespace=$ppo->namespace name_tag=$ppo->nameTag}" ><img src="{copixresource path='img/tools/update.png'}" alt="{i18n key='copix:common.buttons.update'}"/></a>
                <a href="{copixurl dest='admin|deleteMeta' keyword_id=$id namespace=$ppo->namespace name_tag=$ppo->nameTag}" ><img src="{copixresource path='img/tools/delete.png'}" alt="{i18n key='copix:common.buttons.delete'}"/></a>
            </td>
        </tr>
    {else}
        <tr>
            <td> <input type="text" name="editKeyword" value="{if $ppo->nText === null}{$keyword->texte}{else}{$ppo->nText}{/if}" style="width:98%;" /></td>
            <td>
                 <input type="image" name="keyword_id" src="{copixresource path='img/tools/ok.png'}" alt="{i18n key='copix:common.buttons.ok'}" value="{$id}"/>
                <a href="{copixurl dest='admin|editMetaInformations' namespace=$ppo->namespace name_tag=$ppo->nameTag}" ><img src="{copixresource path='img/tools/cancel.png'}" alt="{i18n key='copix:common.buttons.cancel'}"/></a>
            </td>
        </tr>
    {/if}
{/foreach}
{foreach from=$ppo->arNewKeyword key=id item=keyword}
    {if $ppo->keyword_nid ne $id}
        <tr>
            <td>{$keyword->texte}</td>
            <td>
                <a href="{copixurl dest='admin|editMetaInformations' keyword_nid=$id namespace=$ppo->namespace name_tag=$ppo->nameTag}" ><img src="{copixresource path='img/tools/update.png'}" alt="{i18n key='copix:common.buttons.update'}"/></a>
                <a href="{copixurl dest='admin|deleteMeta' keyword_nid=$id namespace=$ppo->namespace name_tag=$ppo->nameTag}" ><img src="{copixresource path='img/tools/delete.png'}" alt="{i18n key='copix:common.buttons.delete'}"/></a>
            </td>
        </tr>
    {else}
        <tr>
            <td> <input type="text" name="editKeyword" value="{if $ppo->nText === null}{$keyword->texte}{else}{$ppo->nText}{/if}" style="width:98%;" /></td>
            <td>
                 <input type="image" name="keyword_nid" src="{copixresource path='img/tools/ok.png'}" alt="{i18n key='copix:common.buttons.ok'}" value="{$id}"/>
                <a href="{copixurl dest='admin|editMetaInformations' namespace=$ppo->namespace name_tag=$ppo->nameTag}" ><img src="{copixresource path='img/tools/cancel.png'}" alt="{i18n key='copix:common.buttons.cancel'}"/></a>
            </td>
        </tr>
    {/if}
 {/foreach}
        <tr>
            <td> <input type="text" name="textKeyword" value="{$ppo->textKeyword}" style="width:98%;" /></td>
            <td> <input type="image" name="addKeyword" value="{i18n key='copix:common.buttons.ok'}" alt="{i18n key='copix:common.buttons.ok'}" src="{copixresource path='img/tools/add.png'}" /></td>
        </tr>
    </table>
</form>

<br />
<form action="{copixurl dest='admin|update' namespace=$ppo->namespace name_tag=$ppo->nameTag}" method="post" >
    <div>
         
            <input type="image" name="update" src="{copixresource path='img/tools/ok.png'}" value="{i18n key='copix:common.buttons.ok'}" alt="{i18n key='copix:common.buttons.ok'}" />
            <a>{i18n key='copix:common.buttons.ok'}</a>
        
        <a href="{copixurl dest='admin|view' namespace=$ppo->namespace}" ><img src="{copixresource path='img/tools/cancel.png'}" alt="{i18n key='copix:common.buttons.cancel'}"/>{i18n key='copix:common.buttons.cancel'}</a>
    </div>
</form>