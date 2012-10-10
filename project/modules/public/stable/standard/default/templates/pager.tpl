<div style="text-align:center;">
{$NBRECORD} {i18n key=copix:pager.messages.resultFound} ({i18n key=copix:pager.messages.currentOfTotal CURRENT_PAGE=$CURRENT_PAGE TOTAL_PAGE=$TOTAL_PAGE})
<br />
<!--{$LIMIT} {i18n key=copix:pager.messages.resultPerPage}<br />-->
<table style="border:none;" align="center">
<tr>
  <td>{$FIRST_PAGE}</td>
  <td>{$PREVIOUS_PAGE}</td>
  <td>{$LOOP}</td>
  <td>{$NEXT_PAGE}</td>
  <td>{$LAST_PAGE}</td>
</tr>
</table>
</div>
<!--{i18n key=copix:pager.messages.dislpayResultFromTo FROM=$FROM TO=$TO}>-->