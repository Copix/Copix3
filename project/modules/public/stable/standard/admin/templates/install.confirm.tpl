 {if count($toAdd)}
    <h2>{i18n key="install.title.addDependenciesModules"}</h2>
    <ul>
    {foreach from=$toAdd item=module}
       <li>{$module->description} [{$module->name}]</li>
    {/foreach}
    </ul>
    <input type="button" value="{i18n key="install.confirmInstall.button"}" onclick="javascript:window.location='{copixurl dest="admin|install|confirmInstall" todo="add"}'" />
    <input type="button" value="{i18n key="install.cancel.button"}" onclick="javascript:window.location='{copixurl dest="admin|install|manageModules"}'" />

 {/if}
 {if count($toDelete)}
    <h2>{i18n key="install.title.deleteDependenciesModules"}</h2>
    <ul>
    {foreach from=$toDelete item=module}
       <li>{$module->description} [{$module->name}]</li>
    {/foreach}
    </ul>
    <input type="button" value="{i18n key="install.confirmInstall.button"}" onclick="javascript:window.location='{copixurl dest="admin|install|confirmInstall" todo="remove"}'" />
    <input type="button" value="{i18n key="install.cancel.button"}" onclick="javascript:window.location='{copixurl dest="admin|install|manageModules"}'" />
 {/if}