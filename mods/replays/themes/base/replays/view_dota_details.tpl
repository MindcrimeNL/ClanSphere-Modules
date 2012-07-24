<table class="forum" cellpadding="1" cellspacing="0" style="width: 100%;">
	<tr>
	<td width="5%"></td>
	<td width="45%">{lang:team}</td>
	<td width="45%">{lang:details_name}</td>
	<td width="10%">{lang:details_level}</td>
	<td width="10%">{lang:details_apm}</td>
	<td align="right" width="3%">{lang:details_hk}</td>
	<td width="3%">/</td>
	<td align="right" width="3%">{lang:details_hd}</td>
	<td width="3%">/</td>
	<td align="right" width="3%">{lang:details_ha}</td>
	<td align="right" width="3%">{lang:details_ck}</td>
	<td width="3%">/</td>
	<td align="right" width="3%">{lang:details_cd}</td>
	<td width="3%">/</td>
	<td align="right" width="3%">{lang:details_n}</td>
	</tr>
	{loop:details}
	<tr>
	<td><img width="20" height="20" src="{details:player_himage}" alt="{details:player_hname}" title="{details:player_hname}" style="margin-left: 2px; vertical-align: middle; border-bottom: 2px {details:player_hcolor} ridge;"></td>
	<td><font color="{details:player_hcolor}">{details:player_team}</font></td>
	<td><font color="{details:player_color}">{details:player_name}</font></td>
	<td>{details:player_level}</td>
	<td>{details:player_apm}</td>
	<td align="right" width="3%">{details:player_hk}</td>
	<td width="3%">/</td>
	<td align="right" width="3%">{details:player_hd}</td>
	<td width="3%">/</td>
	<td align="right" width="3%">{details:player_ha}</td>
	<td align="right" width="3%">{details:player_ck}</td>
	<td width="3%">/</td>
	<td align="right" width="3%">{details:player_cd}</td>
	<td width="3%">/</td>
	<td align="right" width="3%">{details:player_n}</td>
	</tr>
	<tr>
	<td colspan="14">{details:player_details}</td>
	</tr>
	{stop:details}
	<tr>
	<td colspan="14">{lang:details_bans}</td>
	</tr>
	<tr>
	<td colspan="14">{detail:bans}</td>
	</tr>
	<tr>
	<td colspan="14">{lang:details_picks}</td>
	</tr>
	<tr>
	<td colspan="14">{detail:picks}</td>
	</tr>
</table>