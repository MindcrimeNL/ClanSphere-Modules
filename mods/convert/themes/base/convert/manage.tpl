<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
 <tr>
  <td class="headb" colspan="3">{lang:mod_name}</td>
 </tr>
</table>
<br />

<form method="post" id="convert_manage" action="{url:convert_manage}">
<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
 <tr>
  <td class="leftb">{lang:fake}</td>
  <td class="leftb"><input type="checkbox" name="fake" value="1" {convert:fake}></td>
 </tr>
 <tr>
	<td class="leftc" colspan="2"><hr /></td>
 </td>
 <tr>
  <td class="leftb">{lang:convert_board}</td>
  <td class="leftb"><input type="checkbox" name="convert_board" value="1" {convert:board}></td>
 </tr>
 <tr>
	<td class="leftc" colspan="2"><hr /></td>
 </td>
 <tr>
  <td class="leftb">{lang:convert_news}</td>
  <td class="leftb"><input type="checkbox" name="convert_news" value="1" {convert:news}></td>
 </tr>
 <tr>
  <td class="leftb">{lang:preferred_language}</td>
  <td class="leftb">{languages:lang}</td>
 </tr>
 <tr>
	<td class="leftc" colspan="2"><hr /></td>
 </td>
 <tr>
  <td class="leftb">{lang:convert_members}</td>
  <td class="leftb"><input type="checkbox" name="convert_members" value="1" {convert:members}></td>
 </tr>
 <tr>
	<td class="leftc" colspan="2"><hr /></td>
 </td>
 <tr>
  <td class="leftb">{lang:convert_wars}</td>
  <td class="leftb"><input type="checkbox" name="convert_wars" value="1" {convert:wars}></td>
 </tr>
 <tr>
  <td class="leftb">{lang:category} {lang:wars}</td>
  <td class="leftb">{categories:wars}</td>
 </tr>
 <tr>
  <td class="leftb">{lang:category} {lang:games}</td>
  <td class="leftb">{categories:games}</td>
 </tr>
 <tr>
	<td class="leftc" colspan="2"><hr /></td>
 </td>
 <tr>
  <td class="leftb">{lang:url}</td>
  <td class="leftb"><input type="text" name="url" value="{convert:url}" size="50"></td>
 </tr>
 <tr>
  <td class="leftb">{lang:user}</td>
  <td class="leftb"><input type="text" name="user" value="{convert:user}"></td>
 </tr>
 <tr>
  <td class="leftb">{lang:pass}</td>
  <td class="leftb"><input type="password" name="pass" value="{convert:pass}"></td>
 </tr>
 <tr>
  <td class="leftb">{lang:name}</td>
  <td class="leftb"><input type="text" name="name" value="{convert:name}"></td>
 </tr>
 <tr>
  <td class="leftb">{lang:prefix}</td>
  <td class="leftb"><input type="text" name="prefix" value="{convert:prefix}"></td>
 </tr>
 <tr>
  <td class="leftb">{lang:charset}</td>
  <td class="leftb"><input type="text" name="charset" value="{convert:charset}"></td>
 </tr>
 <tr>
  <td class="leftb">{lang:host}</td>
  <td class="leftb"><input type="text" name="host" value="{convert:host}"></td>
 </tr>
 <tr>
  <td class="leftb">{lang:port}</td>
  <td class="leftb"><input type="text" name="port" value="{convert:port}"></td>
 </tr>
 <tr>
  <td class="leftb">{lang:type}</td>
  <td class="leftb">{databases:type}</td>
 </tr>
 <tr>
  <td class="leftc">{icon:ksysguard}</td>
  <td class="leftc">
   <input type="submit" name="submit" value="{lang:convert}" />
  </td>
 </tr>
</table>
</form>
<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
 <tr>
	<td class="headb">{lang:conversion_errors}</td>
 </tr>
{loop:errors}
 <tr>
  <td class="leftc" valign="top">{errors:message}</td>
 </tr>
{stop:errors}
</table>
<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
 <tr>
  <td class="headb" colspan="2" valign="top">{lang:statistics}</td>
 </tr>
 <tr>
  <td class="leftb">{lang:users}</td>
  <td class="leftb">{statistics:users}</td>
 </tr>
 <tr>
  <td class="leftb">{lang:board}</td>
  <td class="leftb">{statistics:board}</td>
 </tr>
 <tr>
  <td class="leftb">{lang:threads}</td>
  <td class="leftb">{statistics:threads}</td>
 </tr>
 <tr>
  <td class="leftb">{lang:news}</td>
  <td class="leftb">{statistics:news}</td>
 </tr>
 <tr>
  <td class="leftb">{lang:squads}</td>
  <td class="leftb">{statistics:squads}</td>
 </tr>
 <tr>
  <td class="leftb">{lang:members}</td>
  <td class="leftb">{statistics:members}</td>
 </tr>
 <tr>
  <td class="leftb">{lang:games}</td>
  <td class="leftb">{statistics:games}</td>
 </tr>
 <tr>
  <td class="leftb">{lang:wars}</td>
  <td class="leftb">{statistics:wars}</td>
 </tr>
 <tr>
  <td class="leftb">{lang:clans}</td>
  <td class="leftb">{statistics:clans}</td>
 </tr>
 <tr>
  <td class="leftb">{lang:categories}</td>
  <td class="leftb">{statistics:categories}</td>
 </tr>
</table>
