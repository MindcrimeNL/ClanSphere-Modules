<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
 <tr>
  <td class="headb" colspan="3">{lang:mod_name} - {link:teamspeak_manage}</td>
 </tr>
 <tr>
  <td class="leftb">{lang:info}</td>
 </tr>
</table>
<br />
{head:message}

<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
 <tr>
  <td class="headb" colspan="3">{lang:sname} {server:server_name}</td>
 </tr>
 <tr>
  <td class="headb">{lang:user}</td>
  <td class="headb">{lang:aktchannel}</td>
  <td class="headb">{lang:submit}</td>
 </tr>
 {loop:users}
 <tr>
  <td class="leftc">{users:user}</td>
  <td class="leftc">{users:aktchannel}</td>
  <td class="leftc"><form method="POST" id="teamspeakuser_kick" action="{link:form_action}" enctype="multipart/form-data">
  <input type="hidden" name="clid" value="{users:clid}">
  <input type="submit" name="submit" value="{lang:submit}">
  </form></td>
 </tr>
 {stop:users}
</table>
