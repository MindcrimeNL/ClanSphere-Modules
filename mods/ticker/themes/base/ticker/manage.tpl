<form method="post" id="ticker_manage" action="{url:ticker_manage}">
<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
 <tr>
  <td class="headb" colspan="2">{lang:mod_name} - {lang:manage}</td>
 </tr>
 <tr>
  <td class="leftb">{icon:editpaste} {link:ticker_new}</td>
  <td class="leftb">{icon:contents} {lang:total}: {head:ticker_count}</td>
 </tr>
</table>
<br />
{head:message}

<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
 <tr>
  <td class="headb" width="10%">{lang:direction}</td>
  <td class="headb" width="10%">{lang:amount}</td>
  <td class="headb" width="10%">{lang:delay}</td>
  <td class="headb">{lang:preview}</td>
  <td class="headb" width="7%">{lang:status}</td>
  <td class="headb" width="8%" colspan="2">{lang:options}</td>
 </tr>
{loop:tickers}
 <tr>
  <td class="leftc">{tickers:direction}</td>
  <td class="leftc">{tickers:amount}</td>
  <td class="leftc">{tickers:delay}</td>
  <td class="leftc">{tickers:preview}</td>
  <td class="leftc">{tickers:status}</td>
  <td class="leftc"><a href="{tickers:url_edit}" title="{lang:edit}">{icon:edit}</a></td>
  <td class="leftc"><a href="{tickers:url_remove}" title="{lang:remove}">{icon:editdelete}</a></td>
 </tr>
{stop:tickers}
</table>
</form>
