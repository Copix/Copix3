<h2>Utilisation standard</h2>
{ldelim}i18n key="tags_demo.message.standard"{rdelim}
<p>{i18n key="tags_demo.message.standard"}</p>

<h2>Avec un argument</h2>
{ldelim}i18n key="tags_demo.message.argument" param1="argument"{rdelim}
<p>{i18n key="tags_demo.message.argument" param1="argument"}</p>

<h2>Avec plusieurs arguments</h2>
{ldelim}i18n key="tags_demo.message.arguments" param1="message" param2="arguments"{rdelim}
<p>{i18n key="tags_demo.message.arguments" param1="message" param2="arguments"}</p>

<h2>Avec choix de la langue : ici "en" pour anglais</h2>
{ldelim}i18n key="tags_demo.message.english" lang="en"{rdelim}
<p>{i18n key="tags_demo.message.english" lang="en_US"}</p>

<h2>Avec choix de la langue : ici "fr" pour français</h2>
{ldelim}i18n key="tags_demo.message.french" lang="fr"{rdelim}
<p>{i18n key="tags_demo.message.french" lang="fr"}</p>

<h2>Sans le paramètre noEscape</h2>
{ldelim}i18n key="tags_demo.message.escape" param1="5" param2="3"{rdelim}
<p>{i18n key="tags_demo.message.escape" param1="5" param2="3"}</p>
<p> Dans le code source de la page : 5 &amp;lt; 3 ?</p>

<h2>Avec le paramètre noEscape</h2>
{ldelim}i18n key="tags_demo.message.escape" noEscape=1 param1="5" param2="3"{rdelim}
<p>{i18n key="tags_demo.message.escape" noEscape="1" param1="5" param2="3"}</p>
<p> Dans le code source de la page : 5 &lt; 3 ?</p>

<h3>Les fichiers contenant les phrases à afficher</h3>
<p>Les références tags_demo.message.standard, tags_demo.message.argument et aux autres clés <br/>
sont enregistrées dans le fichier tags_demo_fr.properties ou tags_demo_en.properties<br/>
qui se situent dans le répertoire resources du module</p>
<p>Voila le contenu du fichier tags_demo_fr.properties <br/><br/>
tags_demo.message.standard = Mon message simple<br/>
tags_demo.message.argument = Mon message avec %s<br/>
tags_demo.message.arguments = Ceci est un %s avec plusieurs %s<br/>
tags_demo.message.english = En anglais<br/>
tags_demo.message.french = En français<br/>
</p>
<p>Et le contenu du fichier tags_demo_en.properties <br/><br/>
tags_demo.message.standard = My simple message<br/>
tags_demo.message.argument = My message with %s<br/>
tags_demo.message.arguments = This is a %s with many %s<br/>
tags_demo.message.english = In English<br/>
tags_demo.message.french = In French<br/>
</p>
<p><a href="{copixurl dest="default"}">Retour à la page de liste</a></p>