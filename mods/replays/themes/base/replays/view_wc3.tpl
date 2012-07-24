	<tr>
		<td class="leftc" colspan="2">{plugin:fullname}</td>
	</tr>
	<tr>
		<td class="leftc" valign="top">{icon:package_games} {lplugin:version}</td>
		<td class="leftb">{plugin:version}</td>
	</tr>
	<tr>
		<td class="leftc" valign="top">{icon:kdmconfig} {lplugin:team1}</td>
		<td class="leftb">{plugin:team1}</td>
	</tr>
	<tr>
		<td class="leftc" valign="top">{icon:kdmconfig} {lplugin:team2}</td>
		<td class="leftb">{plugin:team2}</td>
	</tr>
	{if:team3}
	<tr>
		<td class="leftc" valign="top">{icon:kdmconfig} {lplugin:team3}</td>
		<td class="leftb">{plugin:team3}</td>
	</tr>
	{stop:team3}
	{if:team4}
	<tr>
		<td class="leftc" valign="top">{icon:kdmconfig} {lplugin:team4}</td>
		<td class="leftb">{plugin:team4}</td>
	</tr>
	{stop:team4}
	<tr>
		<td class="leftc" valign="top">{icon:package_games} {lplugin:mode}</td>
		<td class="leftb">{plugin:mode}</td>
	</tr>
	<tr>
		<td class="leftc" valign="top">{icon:openterm} {lplugin:map}</td>
		<td class="leftb">{plugin:map}<br />{plugin:mapimage}</td>
	</tr>
	<tr>
		<td class="leftc" valign="top">{icon:personal} {lplugin:winner}</td>
		<td class="leftb">{plugin:winner}</td>
	</tr>
	<tr>
		<td class="leftc" valign="top">{icon:ktimer} {lplugin:length}</td>
		<td class="leftb">{plugin:length}</td>
	</tr>
	<tr>
		<td class="leftc" valign="top">{icon:package_games} {lplugin:details}</td>
		<td class="leftb">{plugin:details}</td>
	</tr>
	{if:apmdiagram}
	<tr>
		<td class="leftc" valign="top">{icon:package_games} {lplugin:apmdiagram}</td>
		<td class="leftb">{plugin:apmdiagram}<br>
			<table>
			{loop:apmplayers}
			<tr>
				<td>{apmplayers:player_raceicon}</td>
				<td><font color="{apmplayers:player_color}">{apmplayers:player}</font></td>
				<td>{apmplayers:player_apm} {lplugin:apm}</td>
			</tr>				
			{stop:apmplayers}
			</table></td>
	</tr>
	{stop:apmdiagram}
	<tr>
		<td class="leftc" valign="top">{icon:package_games} {lplugin:chat}</td>
		<td class="leftb">{plugin:chat}</td>
	</tr>
	<tr>
		<td class="leftc" colspan="2"></td>
	</tr>
	