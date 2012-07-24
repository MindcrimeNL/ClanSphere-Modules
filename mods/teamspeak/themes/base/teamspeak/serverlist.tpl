<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
 <tr>
  <td class="headb" colspan="3">{lang:mod_name} - {link:teamspeak_manage}</td>
 </tr>
 <tr>
  <td class="leftb">{icon:editpaste} {link:teamspeak_serveradd}</td>
  <td class="leftb">{icon:contents} {lang:total}: {head:teamspeak_count}</td>
  <td class="rightb">{head:pages}</td>
 </tr>
</table>
<br />
{head:message}

<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
 <tr>
  <td class="headb">{sort:teamspeak_ip} {lang:ip}</td>
  <td class="headb">{sort:teamspeak_udp} {lang:udp}</td>
  <td class="headb">{lang:tcp}</td>
  <td class="headb">{lang:version}</td>
  <td class="headb" colspan="2">{lang:traffic}</td>
  <td class="headb" colspan="3">{lang:options}</td>
 </tr>
 {loop:teamspeak}
 <tr>
  <td class="leftc"><a href="{teamspeak:link_view}">{teamspeak:ip}</a></td>
  <td class="leftc">{teamspeak:udp}</td>
  <td class="leftc">{teamspeak:tcp}</td>
  <td class="leftc">{teamspeak:version}</td>
  <td class="rightc">{teamspeak:traffic_in}</td>
  <td class="rightc">{teamspeak:traffic_out}</td>
  <td class="leftc">{teamspeak:active}</td>
  <td class="leftc" colspan="2">{teamspeak:options}</td>
 </tr>
 {stop:teamspeak}
</table>
