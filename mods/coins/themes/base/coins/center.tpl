<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="{page:cellspacing}">
	<tr>
		<td class="headb" colspan="3">{lang:mod_name}</td>
	</tr>
	<tr>
		<td class="leftb" width="20%">{lang:credits}:</td>
		<td class="leftb">{user:points}</td>
		<td class="rightb"></td>
	</tr>
	<tr>
		<td class="leftb" colspan="2">{head:getmsg}</td>
		<td class="rightb"></td>
	</tr>
</table>
<br />

{if:mod}
<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
 {loop:mods}
 <tr>
  <td class="leftc" colspan="2">{mods:icon} {mods:name}</td>
 </tr>
 <tr>
  <td class="leftc" width="20%">{lang:edit_used}:</td>
  <td class="leftb">{mods:used}</td>
 </tr>
 <tr>
  <td class="leftc" width="20%">{lang:edit_received}:</td>
  <td class="leftb">{mods:received}</td>
 </tr>
 {stop:mods}
</table>
{stop:mod}

