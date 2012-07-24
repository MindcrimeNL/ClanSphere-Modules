<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="{page:cellspacing}">
	<tr>
		<td class="headb" colspan="3">{lang:mod_name}</td>
	</tr>
	<tr>
		<td class="leftb" colspan="2">{lang:total}: {head:total}</td>
		<td class="rightb">{head:pages}</td>
	</tr>
	<tr>
		<td class="leftb" colspan="3">{head:getmsg}</td>
</table>
<br />

<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
  <tr>
    <td class="headb">{sort:user} {lang:user}</td>
    <td class="headb">{sort:total} {lang:coins_total}</td>
    <td class="headb" colspan="2">{lang:options}</td>
  </tr>
  {loop:coins}
  <tr>
    <td class="leftc" valign="top">{coins:user}</td>
    <td class="rightc" valign="top" style="color: {coins:color};">{coins:total}</td>
    <td class="leftc" valign="top"><a href="{url:coins_edit:id={coins:coins_id}}" title="{lang:edit}">{icon:edit}</a></td>
    <td class="leftc" valign="top"><a href="{url:coins_remove:id={coins:coins_id}}" title="{lang:remove}">{icon:editdelete}</a></td>
	</tr>
  {stop:coins}
</table>
