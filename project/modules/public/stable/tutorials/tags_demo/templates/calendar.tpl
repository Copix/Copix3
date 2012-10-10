  <h2>En standard</h2>
  <p>{ldelim}calendar name=a{rdelim}</p>
  <p>{calendar name=a}</p>
 
  <h2>Avec le paramètre image</h2>
  <p>{ldelim}copixresource path=default/img/tools/loupe.png assign=path{rdelim}{ldelim}calendar name=b image=$path{rdelim}</p>
  {copixresource path=img/tools/loupe.png assign=path}
  <p>{calendar name=b image=$path}</p>
 
  <h2>Avec le paramètre size à 10</h2>
  <p>{ldelim}calendar name=c size=10{rdelim}</p>
  <p>{calendar name=c size=10}</p>
 
  <h2>Avec le paramètre format à YYYYMMDD</h2>
  <p>{ldelim}calendar name=d format=YYYYMMDD{rdelim}</p>
  <p>{calendar name=d format=YYYYMMDD}</p>
 
  <h2>Avec le paramètre langue à en</h2>
  <p>{ldelim}calendar name=e lang=en{rdelim}</p>
  <p>{calendar name=e lang=en}</p>

  <h2>Avec le paramètre sizeday à 1</h2>
  <p>{ldelim}calendar name=f sizeday=1{rdelim}</p>
  <p>{calendar name=f sizeday=1}</p>

  <h2>Avec le paramètre value à 25/12/1976</h2>
  <p>{ldelim}calendar name=g value=25/12/1976{rdelim}</p>
  <p>{calendar name=g value=25/12/1976}</p>

  <h2>Avec le paramètre yyyymmdd à 20070102</h2>
  <p>{ldelim}calendar name=h yyyymmdd=20070102{rdelim}</p>
  <p>{calendar name=h yyyymmdd=20070102}</p>

  <h2>Avec le paramètre timestamp à 0</h2>
  <p>{ldelim}calendar name=i timestamp=0{rdelim}</p>
  <p>{calendar name=i timestamp=0}</p>

  <h2>Avec le paramètre beforeyear à 5 </h2>
  <p>{ldelim}calendar name=j beforeyear=5{rdelim}</p>
  <p>{calendar name=j beforeyear=5}</p>

  <h2>Avec le paramètre afteryear à 5 </h2>
  <p>{ldelim}calendar name=k afteryear=5{rdelim}</p>
  <p>{calendar name=k afteryear=5}</p>

  <h2>Avec le paramètre duration à 0 </h2>
  <p>{ldelim}calendar name=l duration=0{rdelim}</p>
  <p>{calendar name=l duration=0}</p>

  <h2>Avec le paramètre tabindex à 1 </h2>
  <p>{ldelim}calendar name=m tabindex=1{rdelim}</p>
  <p>{calendar name=m tabindex=1}</p>

  <h2>Avec le paramètre extra à style="background-color: #ccc;" </h2>
  <p>{ldelim}calendar name=n extra='style="background-color: #ccc;"'{rdelim}</p>
  <p>{calendar name=n extra='style="background-color: #ccc;"'}</p>
  
  <h2>Avec une autre classe </h2>
  <p>{ldelim}calendar name=o classe="ma_class_calendar"{rdelim}</p>
  <p>Le style défini dans le paramètre classe est appliqué à la table contenant tout le calendrier.
  <br/>Les différentes parties modifiables au niveau de la css sont entre-autre :
  <br/>- la couleur de la case du jour : .ma_class_calendar td.calendar_today dans la css et "ma_class_calendar" sera passé en paramètre au calendrier
  <br/>- la couleur des case des autres jours : .ma_class_calendar td.calendar_day 
  <br/>- la couleur de la case de la valeur passée en paramètre : .ma_class_calendar td.calendar_value (si la case du jour est aussi la case de la valeur, la classe du td sera "calendar_today calendar_value"
  <br/>- la couleur des case sans rien (remplissant les "trous") : .ma_class_calendar td.calendar_noday
  <br/>- la couleur du haut du calendrier : .ma_class_calendar tr.calendar_header
  <br/>
  <p>{calendar name=o classe="ma_class_calendar"}</p>

<p><a href="{copixurl dest="default"}">Retour à la page de liste</a></p>
<br /><br /><br /><br /><br /><br /><br /><br /><br /><br />