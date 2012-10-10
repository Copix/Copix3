<a href="{copixurl dest=daoxmlgenerator|| iso=$iso xmlheader=$xmlheader xmltype=$xmltype}">{i18n key="daoxmlgenerator.return.to.home"}</a><br />
<form name="dao_form" action="{copixurl dest=daoxmlgenerator||download iso=$iso xmlheader=$xmlheader xmltype=$xmltype}" method="post">
<input type="submit" value="{i18n key="daoxmlgenerator.daofile.getfiles"}" />
<br /><br />
<h2>{i18n key="daoxmlgenerator.tables.title"}</h2>
{$content}
<br />
<h2>{i18n key="daoxmlgenerator.properties.title"}</h2>
{i18n key="daoxmlgenerator.properties.desc"}
{$properties}
</form>
