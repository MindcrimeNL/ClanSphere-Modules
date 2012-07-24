<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
	<tr>
		<td class="headb" colspan="2">{lang:mod} - {lang:toplist}</td>	
	</tr>
 	<tr>
  		<td class="leftb">{icon:contents} {lang:total}: {count:all}</td>
  		<td class="rightb">{pages:list}</td>
 	</tr>
</table>
<br />
<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
	<tr>
  		<td class="headb">#</td>
  		<td class="headb">{lang:name}</td>
  		<td class="headb">{sort:paid} {lang:earned}</td>
  		<td class="headb">{sort:count} {lang:bets}</td>
  		<td class="headb">{sort:amount} {lang:bet_amount}</td>
 	</tr>{loop:toplist}
 	<tr>
  	<td class="rightc">{toplist:rank}</td>
 		<td class="leftc">{toplist:user}</td>
  	<td class="rigthc">{toplist:paid}</td>
  	<td class="rigthc">{toplist:count}</td>
  	<td class="rigthc">{toplist:amount}</td>
 	</tr>{stop:toplist}
</table>
