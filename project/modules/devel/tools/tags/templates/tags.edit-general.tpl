{error message=$ppo->errors}

<h2>{i18n key='Updating Tag [%s]' param1=$ppo->nameTag}</h2>

<div class="menu-onglet"> <!-- début de la boite contenant les onglets -->
    <span class="onglet-actif">{i18n key='tags.onglet.general'}</span>  
    <a class="onglet" onclick="javascript:submitAndSave ('meta')" >{i18n key='tags.onglet.attached'}</a>
{if $ppo->admin}
    <a class="onglet" onclick="javascript:submitAndSave ('asso')" >{i18n key='tags.onglet.associations'}</a>
{/if}
</div>

<br />
<form action="{copixurl dest='admin|update' namespace=$ppo->namespace name_tag=$ppo->nameTag}" id="formGeneral" method="post" > 
    <div>
        <input type="hidden" id="onglet" name="onglet" value="" />
    </div>
    <div>
        <label>
            {i18n key='copix:common.messages.name'} : 
        </label><input type="text" maxlength="50" name="newTag" value="{$ppo->tagWrite|escape}" />
        
        <br />
        
        {i18n key='copix:common.messages.desc'} :<br />
        <textarea name="description" rows="10" cols="50">{$ppo->description|escape}</textarea>
        <br />
        <br />
        
        <label>
            <input type="image" name="update" src="{copixresource path='img/tools/ok.png'}" value="{i18n key='copix:common.buttons.ok'}" alt="{i18n key='copix:common.buttons.ok'}" />
            <a>{i18n key='copix:common.buttons.ok'}</a>
        </label>
        <a href="{copixurl dest='admin|view' name_tag=$ppo->nameTag namespace=$ppo->namespace}" ><img src="{copixresource path='img/tools/cancel.png'}" alt="{i18n key='copix:common.buttons.cancel'}"/>{i18n key='copix:common.buttons.cancel'}</a>
    </div>
</form>

{copixhtmlheader kind=jsCode}
{literal}
var submitAndSave = function(valeur) {
    $('onglet').value = valeur;
    $('formGeneral').submit ();
};
{/literal}
{/copixhtmlheader}