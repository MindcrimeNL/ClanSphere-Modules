<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="1">
	<tr>
		<td class="headb" colspan="2">{lang:mod} - {lang:head_list}</td>
	</tr>
	<tr>
		<td class="leftc">{lang:total}: {head:teamspeak_count}</td>
		<td class="rightb">{head:pages}</td>
	</tr>
</table>
<br />
<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="1">
  <tr>
    <td class="headb">{lang:name}</td>
	<td class="headb">{sort:teamspeak_ip} {lang:ip}</td>
	<td class="headb" style="width: 100px;">{sort:teamspeak_udp} {lang:udp}</td>
	<td class="headb" style="width: 50px;">{lang:players}</td>
	<td class="headb">{lang:options}</td>
  </tr>
  {loop:teamspeak}
  <tr>
    {if:nodata}
    <td class="centerb" colspan="5">{teamspeak:no_data}</td>
    {stop:nodata}
	{if:data}
	<td class="leftb">{teamspeak:name}</td>
	<td class="leftb">{teamspeak:ip}</td>
	<td class="leftb">{teamspeak:udp}</td>
	<td class="leftb">{teamspeak:musers}</td>
	<td class="centerb">{teamspeak:view}</td>
	{stop:data}
  </tr>
  {stop:teamspeak}
</table>  
