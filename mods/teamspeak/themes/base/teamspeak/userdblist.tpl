<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
 <tr>
  <td class="headb" colspan="3">{lang:mod_name} - {link:teamspeak_manage}</td>
 </tr>
 <tr>
  <td class="leftb">{lang:total}: {head:count}</td>
 </tr>
</table>
<br />
{head:message}

<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
 <tr>
  <td class="headb" colspan="5">{lang:sname} {server:server_name}</td>
 </tr>
 <tr>
  <td class="headb">{lang:userid}</td>
  <td class="headb">{lang:user}</td>
  <td class="headb">{lang:is_superadmin}</td>
  <td class="headb">{lang:registered}</td>
  <td class="headb">{lang:lastlogin}</td>
 </tr>
 {loop:users}
 <tr>
  <td class="leftc">{users:userid}</td>
  <td class="leftc">{users:user}</td>
  <td class="leftc">{users:sadmin}</td>
  <td class="leftc">{users:registered}</td>
  <td class="leftc">{users:lastlogin}</td>
 </tr>
 {stop:users}
</table>
