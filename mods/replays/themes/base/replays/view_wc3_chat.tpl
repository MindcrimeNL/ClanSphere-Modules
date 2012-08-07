<table class="forum" cellpadding="0" cellspacing="0" style="width: 100%;">
	{loop:chats}
	<tr>
		<td class="leftc">{chats:time}</td>
		<td class="leftc">{chats:mode}</td>
		<td class="leftc"><font color="{chats:player_color}">{chats:player}</font></td>
		<td class="leftb">{chats:text}</td>
	</tr>
	{stop:chats}
</table>
