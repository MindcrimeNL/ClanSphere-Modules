<form method="post" action="{url:coins_create}">
<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
 <tr>
  <td class="headb" colspan="2">{lang:mod_name} - {lang:manage}</td>
  </tr>
	<tr>
		<td class="leftc" colspan="2">{search:message}</td>
	</tr>
  <tr>
    <td class="leftb">{lang:user}</td>
    <td class="leftb">
			<input type="text" name="users_nick" id="users_nick" value="{search:users_nick}" autocomplete="off" onkeyup="Clansphere.ajax.user_autocomplete('users_nick', 'search_users_result', '{page:path}')" size="50" maxlength="100" />
      <div id="search_users_result"></div>
    </td>
  </tr>
	<tr>
		<td class="leftc"></td>
		<td class="leftc"><input type="submit" value="{lang:create}" name="submit"></td>
	</tr>
</table>
</form>
