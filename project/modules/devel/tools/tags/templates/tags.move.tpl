{error message=$ppo->errors}

<p>{i18n key='tags.confirmDeleteTag' param1=$ppo->nameTag}</p>
<form action="{copixurl dest='admin|delete' namespace=$ppo->namespace name_tag=$ppo->nameTag}" method="post" >
    <div>
        <label>{i18n key='tags.reassociation'}</label>{select name="newTags" values=$ppo->arTags}
        <br />
        <input type="image" id="yes_button" name="yes" src="{copixresource path='img/tools/valid.png'}" alt="{i18n key='copix:common.buttons.yes'}" value="{i18n key='copix:common.buttons.yes'}"/><label for="yes_button">{i18n key='copix:common.buttons.yes'}</label></a>
        &nbsp;
        <a href="{copixurl dest='admin|view' namespace=$ppo->namespace}" ><img src="{copixresource path='img/tools/cancel.png'}" alt="{i18n key='copix:common.buttons.no'}" />&nbsp;{i18n key='copix:common.buttons.no'}</a>
    </div>
</form>