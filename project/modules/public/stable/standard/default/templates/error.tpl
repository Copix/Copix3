<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
   <meta content="text/html; charset=utf-8" http-equiv="content-type" />
   <title>{$TITLE_BAR}</title>
   <link rel="stylesheet" href="{copixresource path="styles/styles_copix.css"}" type="text/css"/>
   <link rel="stylesheet" href="{copixresource path="styles/styles_menu.css"}" type="text/css" />
   <script type="text/javascript" src="{copixurl}js/menu/javascript.js"></script>
   {$HTML_HEAD}
</head>
<body style="bgcolor: #FF0000;">
     <div id="all_content">
        <div id="COPIX_TITLE_PAGE"><h1>{$TITLE_PAGE}</h1></div>
        <div id="content">
          <img id="logo_copix" alt="Copix Framework" src="{copixresource path="img/copix/logo-framework.gif"}" />
          <div id="COPIX_MENU">{$MENU}</div>

          <div id="main_layout">
             <div id="COPIX_MAIN">{$MAIN}</div>
          </div>
          <br style="clear:both;" />
          <br style="clear:both;" />
         </div>
      </div>
</body>
</html>