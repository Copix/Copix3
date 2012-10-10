<h2>Tableau simple</h2>
<p>{ldelim}ulli values="premier;second;troisième"|toarray{rdelim}</p>
<p>{ulli values="premier;second;troisième"|toarray}</p>

<h2>Chaine simple</h2>
<p>{ldelim}ulli values="test"{rdelim}</p>
<p>{ulli values="test"}</p>

<h2>Tableau de tableaux</h2>
<p>{ldelim}ulli values=$ppo->arULLI{rdelim}</p>
<p>avec $ppo->arULLI déclaré comme suit : array ('1', array ('21', '22', '23'), '3') </p>
<p>{ulli values=$ppo->arULLI}</p>

<p><a href="{copixurl dest="default"}">Retour à la page de liste</a></p>