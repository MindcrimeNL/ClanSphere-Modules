<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
 <tr>
  <td class="headb" colspan="2">{lang:mod} - {lang:details}</td>
 </tr>
 <tr>
  <td class="leftc">{head:message}</td>
  <td class="rightc"><a href="{url:bets_list}">{lang:overview}</a></td>
 </tr>
</table>
<br />

<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
  <tr>
  <td class="leftc" width="150px" valign="top">{icon:package_games} {lang:bet} :</td>
  <td class="leftb">
    {bets:bets_title}
    <hr style="border:0px;border-top:1px solid #CCC;"/>
    {bets:details}
  </td>
 </tr>
 <tr>
  <td class="leftc" valign="top">{icon:folder_yellow} {lang:category} :</td>
  <td class="leftb"><b>{bets:cat_name}</b></td>
 </tr>
 <tr>
  <td class="leftc" valign="top">{icon:bets} {lang:quote_type} :</td>
  <td class="leftb">{bets:quote_type_clip}</td>
 </tr>
 <tr>
  <td class="leftc" valign="top">{icon:bets} {lang:win_quote} :</td>
  <td class="leftb"><b>{bets:win_quote}</b></td>
 </tr>
  <tr>
	  <td class="leftc" valign="top">{icon:kdmconfig} {lang:contestant} :</td>
	  <td class="leftb">
            <table width="100%">
            	<tr>
            	<th>{lang:contestant}</th>
            	<th align="right">{lang:bets_quote}</th>
            	<th align="right">{lang:bet_amount}</th>
            	<th></th>
            	</tr>
                {loop:contestants}
                <tr>
                   	<td>{if:clan}{contestants:country}{stop:clan}{if:name}{icon:yast_user_add}{stop:name}{contestants:name}</td>
                    <td align="right"><b>{contestants:bets_quote}</b></td>
                   	<td align="right">{contestants:placed} {bets:pointsname}</td>
                   	<td align="right" width="10%">({contestants:placed_perc}%)</td>
                </tr>
                {if:draw}
                <tr>
                	<td height="15" colspan="4"><hr style="border:0px;border-top:1px solid #CCC;"/></td>
                </tr>
                {stop:draw}
                {stop:contestants}
            </table>
        </td>
	</tr>
	<tr>
    	<td class="leftc">
     	   {icon:1day} {lang:ende} :
    	</td>
    	<td class="leftb">
        	{bets:date} - ({bets:status})
    	</td>
	</tr>
    <tr>
    	<td class="leftc">
     	   {icon:favorites1} {lang:credits} :
    	</td>
    	<td class="leftb">
        	{bets:account_balance}
    	</td>
	</tr>
    {if:open}
		{if:fee}
    <tr>
    	<td class="leftc">{icon:bets} {lang:base_fee} :</td>
			<td class="leftb">{bets:base_fee} {bets:pointsname}</td>
		</tr>
		{stop:fee}
    <tr>
    	<td class="leftc">
     	   {icon:forward} {lang:place_bet} :
    	</td>
    	<td class="leftb">
    		{if:cant_bet}
    		{lang:no_user}
    		{stop:cant_bet}
    		{unless:cant_bet}
        	{unless:already_bet}
        		<form name="place_bet" method="post" action="{url:bets_place_bet}">
            <select name="contestant" tabindex="1">
            	<option value="0">---</option>
            	{loop:contestants_drop}
              <option value="{contestants_drop:id}">
              {contestants_drop:name}
              </option>
              {stop:contestants_drop}
              </select>
              <input type="text" name="amount" value="" size="5"  /> {bets:pointsname}
              <input type="hidden" name="bets_id" value="{bets:bets_id}"  />
              <input style="margin-left:20px;" type="submit" name="submit" value="{lang:place_bet}"  />
          	</form>
          {stop:already_bet}
          {if:already_bet}
          	{lang:already_bet}
            <form name="remove_placed_bet" method="post" action="{url:bets_remove_placed_bet}">
            	<input type="hidden" name="bets_id" value="{bets:bets_id}"  />
          		<input style="margin-left:20px;" type="submit" name="submit" value="{lang:remove_placed_bet} - {value:remove_costs}"  />
          	</form>
        	{stop:already_bet}
        {stop:cant_bet}
    	</td>
	</tr>
    {stop:open}
    {if:closed}
    <tr>
    	<td class="leftc">
     	   {icon:favorites} {lang:result} :
    	</td>
    	<td class="leftb">
        	<b>{value:winner}</b>
    	</td>
	</tr>
    {stop:closed}
    <tr>
    	<td class="leftc">
     	   {icon:log} {lang:bidding} :
    	</td>
    	<td class="leftb">
			{value:no_bets}
			{if:users_enable}
			<table width="100%" border="0" cellspacing="2" cellpadding="2">
				  <tr>
            {if:closed}<td width="20"></td>{stop:closed}
					<td class="headb">{lang:participants}</td>
					<td class="headb">{lang:date}</td>
					<td class="headb">{lang:bet_amount}</td>
            {if:closed}<td class="headb">{lang:earned}</td>{stop:closed}
					<td class="headb" width="*">{lang:team}</td>
				  </tr>
				 {loop:users}
				  <tr>
           	{if:closed}
             	<td width="30" align="center">
               	{if:user_win}{icon:submit}{stop:user_win}
               	{if:user_loose}{icon:cancel}{stop:user_loose}
              </td>
            {stop:closed}
					<td class="leftb"><a href="{url:users_view:id={users:id}}" title="profile">{users:name}</a></td>
					<td class="leftb">{users:date}</td>
					<td class="leftb"><font color="#008000">{users:amount}</font> {bets:pointsname}</td>
						{if:closed}
							<td class="leftb">{if:user_win}<font color="#008000">{users:pay_amount}</font> {bets:pointsname}{stop:user_win}</td>
            {stop:closed}
					<td class="leftb">{users:contestant}</td>
				  </tr>
        		  {stop:users}
			</table>
			{stop:users_enable}
    	</td>
	</tr>
</table>
