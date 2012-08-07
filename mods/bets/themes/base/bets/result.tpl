<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
 <tr>
  <td class="headb">{lang:mod} - {lang:head_result}</td>
 </tr>
 <tr>
  <td class="leftc">{lang:body_result}</td>
 </tr>
</table>
<br />

<form name="result" method="post" id="bets_result" action="{url:bets_result}">
<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
  <tr>
  <td class="leftc">{icon:package_games} {lang:title} :</td>
  <td class="leftb"><b>{bets:title}</b></td>
 </tr>
 <tr>
  <td class="leftc" valign="top">{icon:kdmconfig} {lang:contestant} :</td>
  <td class="leftb">
     {loop:contestants_list} 
     {icon:yast_user_add} <b>{contestants_list:name}</b> - {lang:bets_quote}: {contestants_list:bets_quote}<br />
     {stop:contestants_list}
  </td>
 </tr>
 <tr>
  <td class="leftc" valign="top">{icon:smallcal} {lang:winner} :</td>
  <td class="leftb">
    <select name="result" tabindex="1">
     {loop:contestants}<option value="{contestants:id}">{contestants:name}</option>{stop:contestants}
    </select>
  </td>
 </tr>
  <tr>
  <td class="leftc">{icon:ksysguard} {lang:options}</td>
  <td class="leftb">
    <input type="hidden" name="bets_id" value="{bets:id}"  />
    <input type="submit" name="submit" value="{lang:head_result}"  />
  </td>
 </tr>
</table>
</form>
