<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
  <tr>
    <td class="headb">{lang:mod_name} - {lang:match}</td>
  </tr>
  <tr>
    <td class="leftc">{lang:matchdetails}</td>
  </tr>
</table>
<br />
{get:message}
<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
  <tr>
    <td class="leftc">{icon:personal} {lang:team} 1</td>
    <td class="leftb">{match:team1}{if:adminconfirm}{if:confirm1} ({lang:confirmed}: {match:time1}){stop:confirm1}{stop:adminconfirm}</td>
  </tr>
  <tr>
    <td class="leftc">{icon:personal} {lang:team} 2</td>
    <td class="leftb">{match:team2}{if:adminconfirm}{if:confirm2} ({lang:confirmed}: {match:time2}){stop:confirm2}{stop:adminconfirm}</td>
  </tr>
  <tr>
    <td class="leftc">{icon:kreversi} {lang:cup}</td>
    <td class="leftb"><a href="{url:cups_view:id={match:cups_id}}">{match:cups_name}</a> (<a href="{url:cups_matchlist:where={match:cups_id}&amp;round={match:cupmatches_round}}">{lang:matchlist}</a>)</td>
  </tr>
  <tr>
    <td class="leftc">{icon:package_games} {lang:game}</td>
    <td class="leftb"><a href="{url:games_view:id={match:games_id}}">{match:games_name}</a>
    </td>
  </tr>
  <tr>
    <td class="leftc">{icon:smallcal} {lang:result}</td>
    <td class="leftb">{if:showscore}{match:cupmatches_score1} : {match:cupmatches_score2}{stop:showscore}{unless:showscore}-{stop:showscore}</td>
  </tr>
  <tr>
    <td class="leftc">{icon:demo} {lang:status}</td>
    <td class="leftb">{match:status}</td>
  </tr>
</table>{if:participator}
<br />

<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
  <tr>
    <td class="centerb">
      <form method="post" id="matchadmin" action="{url:cups_matchedit}">
        <input type="hidden" name="cupmatches_id" value="{match:id}" />
        {if:nothingyet}<input type="hidden" name="team" value="{match:teamnr}" />
        <input type="submit" name="result" value="{lang:enter_result}" />{stop:nothingyet}
        {if:accept}<input type="submit" name="accept{match:teamnr}" value="{lang:accept_result}" />{stop:accept}
        {if:confirmed}{lang:both_confirmed}{stop:confirmed}
        {if:waiting}{lang:waiting}{stop:waiting}
        {if:admin}<input type="submit" name="adminedit" value="{lang:adminedit}" />{stop:admin}
      </form>
    </td>
  </tr>
</table>{stop:participator}
<br />
