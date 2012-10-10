<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<meta content="text/html; charset=UTF-8" http-equiv="content-type" />   
 <title><?php echo isset ($TITLE_BAR) ? $TITLE_BAR : ''; ?></title>
 <link rel="stylesheet" href="<?php echo _resource ("styles/copix.css"); ?>" type="text/css"/>
 <link rel="stylesheet" href="<?php echo _resource ("styles/theme.css"); ?>" type="text/css"/>
 <?php echo $HTML_HEAD; ?>
</head>

<body>
<div id="all_content">
 <div class="banniere">
  <div style="position: absolute; margin-left: 50px; margin-top: 10px;"><?php echo isset ($SEARCH_FORM) ? $SEARCH_FORM : ''; ?></div>
  <h1>Copix 3.0, framework pour PHP</h1>
 </div>

 <!--Code pour le bloc menu -->
 <div id="Mainmenu">
  <div class="cadre">
   <div class="cadre2">
    <div class="haut">
     <div class="hautdroit">
     </div>
     <div class="hautgauche">
     </div>
    </div>
    <div class="contenu">
<ul>
<?php
if (isset ($menuItems)){
	foreach ($menuItems as $menuCaption=>$menuUrl){
		echo '<li><a href="'.$menuUrl.'">'.$menuCaption.'</a></li>';
	}
}
?>
</ul>
</div>
    <div class="bas">
     <div class="basdroit">
     </div>
     <div class="basgauche">
     </div>
    </div>
   </div>
  </div>
 </div>
 <!-- fin code bloc menu -->

 <div id="container">
   <!--Code pour le bloc Contenu principal ($MAIN) --> 
   <div id="MainContentFull">
    <div class="cadre">
     <div class="cadre2">
      <div class="haut">
       <div class="hautdroit">
       </div>
       <div class="hautgauche">
       </div>
      </div>
      <div class="contenu">
       <h1><?php echo isset ($TITLE_PAGE) ? $TITLE_PAGE : ''; ?></h1><?php echo isset ($MAIN) ? $MAIN : $MAIN; ?>
      </div>
      <div class="bas">
       <div class="basdroit">
       </div>
       <div class="basgauche">
       </div>
      </div>
     </div>
    </div>
   </div>
  </div>

  <!--Code pour le bloc Footer -->
  <div id="Footer">
   <div class="cadre">
    <div class="cadre2">
     <div class="haut">
      <div class="hautdroit">
      </div>
      <div class="hautgauche">
      </div>
     </div>
     <div class="contenu">
	 <p>Site réalisé avec <a href="http://www.copix.org">Copix 3</a></p>
     </div>
     <div class="bas">
      <div class="basdroit">
      </div>
      <div class="basgauche">
      </div>
     </div>
    </div>
   </div>
  </div>
  <!--Fin code bloc Footer-->
 </div><!-- container -->
</div><!-- all content -->
<!--  Site réalisé avec Copix 3 http://www.copix.org  -->
</body>
</html>