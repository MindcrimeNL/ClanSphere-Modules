<table cellpadding="0" cellspacing="{page:cellspacing}" style="width:100%">
{if:replay}
  {loop:replays}
	<tr>
    <td rowspan="2">{replays:game_icon}</td>
    <td>{replays:date}</td>
  </tr>
  <tr>
    <td><a href="{replays:view_url}" title="{if:wc3}{replays:team1_race}v{replays:team2_race}{if:team3}v{replays:team3_race}{stop:team3}{if:team4}v{replays:team4_race}{stop:team4}: {stop:wc3}{if:sc2}{replays:team1_race}v{replays:team2_race}{if:team3}v{replays:team3_race}{stop:team3}{if:team4}v{replays:team4_race}{stop:team4}: {stop:sc2}{replays:title}">{replays:title_short}</a></td>
  </tr>
	{stop:replays}
{stop:replay}
{unless:replay}
 <tr>
  <td>{noreplay:nodata}</td>
 </tr>
{stop:replay}
</table>
