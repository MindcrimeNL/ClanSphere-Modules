<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
 <tr>
  <td class="headb" colspan="3">{lang:mod_name} - {lang:manage}</td>
 </tr>
 <tr>
   <td class="leftb">{icon:editpaste} <a href="{url:datacache_manage}">{lang:showall}</a></td>
   <td class="leftb">{icon:contents} {lang:total}: {head:datacache_count}</td>
   <td class="rightb">{head:pages}</td>
 </tr>
</table>
<br />
{head:message}

<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
 <tr>
  <td class="headb" width="10%">{sort:mod} {lang:mod}</td>
  <td class="headb" width="15%">{sort:action} {lang:action}</td>
  <td class="headb" width="35%">{sort:key} {lang:key}</td>
  <td class="headb">{sort:time} {lang:time}</td>
  <td class="headb" width="7%">{lang:timeout_short}</td>
  <td class="headb" width="8%" colspan="2">{lang:options}</td>
 </tr>
{loop:datacache}
 <tr>
  <td class="leftc" valign="top"><a href="{datacache:url_mod}" title="{lang:mod}">{datacache:mod}</a></td>
  <td class="leftc" valign="top">{datacache:action}</td>
  <td class="leftc" valign="top">{datacache:key}</td>
  <td class="leftc" valign="top">{datacache:time}</td>
  <td class="leftc" valign="top">{datacache:timeout}</td>
  <td class="leftc" valign="top"><a href="{datacache:url_view}" title="{lang:view}">{icon:view_text}</a></td>
  <td class="leftc" valign="top"><a href="{datacache:url_remove}" title="{lang:remove}">{icon:editdelete}</a></td>
 </tr>
{stop:datacache}
</table>
