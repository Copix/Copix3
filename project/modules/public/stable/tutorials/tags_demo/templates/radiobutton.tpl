<h2>Utilisation standard</h2>
{ldelim}radiobutton name=radio values="1;2;3;4;5"|toarray{rdelim}
<p>{radiobutton name=radio values="1;2;3;4;5"|toarray}</p>

<h2>Spécifier un id différent du name</h2>
{ldelim}radiobutton id=radio name=radio1 values="1;2;3;4;5"|toarray{rdelim}
<p>{radiobutton id=radio name=radio1 values="1;2;3;4;5"|toarray}</p>

<h2>Spécifier les clefs/valeurs</h2>
{ldelim}radiobutton name=radio2 values="1=>MySQL;2=>Postgres;3=>SQLite;4=>SQLServer;5=>Oracle"|toarray{rdelim}
<p>{radiobutton name=radio2 values="1=>MySQL;2=>Postgres;3=>SQLite;4=>SQLServer;5=>Oracle"|toarray}</p>

<h2>Indiquer l'élément sélectionné avec selected</h2>
{ldelim}radiobutton name=radio3 selected=2 values="1=>MySQL;2=>Postgres;3=>SQLite;4=>SQLServer;5=>Oracle"|toarray{rdelim}
<p>{radiobutton name=radio3 selected=2 values="1=>MySQL;2=>Postgres;3=>SQLite;4=>SQLServer;5=>Oracle"|toarray}</p>

<h2>Utiliser un tableau d'objet et spécifier les clefs / valeurs</h2>
<p>Ici nous avons un tableau d'objet avec les propriétés id/caption. Nous allons indiquer
à la balise que id est la valeur de l'option et que caption est son libellé dans le paramètre
objectMap.</p>

<p>le tableau d'objet est déclaré comme suit :</p> 

<pre>
$arObjects = array ();

$obj = new StdClass ();
$obj-&gt;id = '1';
$obj-&gt;caption = 'libellé 1';
$arObjects[] = $obj;

$obj = new StdClass ();
$obj-&gt;id = '2';
$obj-&gt;caption = 'libellé 2';
$arObjects[] = $obj;
</pre>


{ldelim}radiobutton name=radio4 values=$ppo->arObjects objectMap="id;caption"{rdelim}
<p>{radiobutton name=radio4 values=$ppo->arObjects objectMap="id;caption"}</p>

<h2>Paramètre extra pour rajouter des informations à la balise</h2>
Les styles sur les radio button ne marche pas très bien :`(<br/>
{ldelim}radiobutton extra='style="background-color: #ccc;"' name=radio5 values="1;2;3;4;5"|toarray{rdelim}
<p>{radiobutton extra='style="background-color: #ccc;"' name=radio5 values="1;2;3;4;5"|toarray}</p>

<p><a href="{copixurl dest="default"}">Retour à la page de liste</a></p>