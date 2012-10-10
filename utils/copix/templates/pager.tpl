<table border="0">
  <tr>
    <td colspan="3" align="center">
    <table border="0">
      <tr>
        <td>{copixlist_button type="first"}{/copixlist_button}</td>
        <td>{copixlist_button type="previous"}{/copixlist_button}</td>
        <td>{if $TOTAL_PAGE != 0}(Page {$CURRENT_PAGE} sur {$TOTAL_PAGE}){else}(Page 0 sur {$TOTAL_PAGE}){/if}</td>
        <td>{copixlist_button type="next"}{/copixlist_button}</td>
        <td>{copixlist_button type="last"}{/copixlist_button}</td>
        <td>{if $NB_RECORD}( {$NB_RECORD} Résultats trouvés ){/if}</td>
      </tr>
    </table>
    </td>
  </tr>
  <!--<tr>
    <td colspan="3" align="center">Affichage des enregistrements {$FROM} à {$TO}</td>
  </tr>-->
</table>

