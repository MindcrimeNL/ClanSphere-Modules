<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
  <tr>
    <td class="headb">{lang:mod_name} - {lang:edit}</td>
  </tr>
  <tr>
    <td class="leftc">{head:msg}</td>
  </tr>
</table>
<br />
<form method="post" id="coins_edit" action="{url:coins_edit}" enctype="multipart/form-data">
  <table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
    <tr>
      <td class="leftc">{icon:bets} {lang:startcoins}</td>
      <td class="rightc">+</td>
      <td class="leftb">{coins:startcoins}</td>
    </tr>
    {loop:mods}
    <tr>
      <td class="leftc" colspan="3"><hr /></td>
    </tr>
    <tr>
      <td class="leftc">{icon:kcmdf} {lang:edit_mod}</td>
      <td class="rightc"></td>
      <td class="leftc">{mods:module}</td>
    </tr>
    <tr>
      <td class="leftc">{icon:bets} {lang:edit_received}</td>
      <td class="rightc">+</td>
      <td class="leftb"><input type="text" name="{mods:field_name_received}" value="{mods:field_value_received}" maxlength="10" size="10" /></td>
    </tr>
    <tr>
      <td class="leftc">{icon:bets} {lang:edit_used}</td>
      <td class="rightc">-</td>
      <td class="leftb"><input type="text" name="{mods:field_name_used}" value="{mods:field_value_used}" maxlength="10" size="10" /></td>
    </tr>
    {stop:mods}
    <tr>
      <td class="leftc" colspan="3"><hr /></td>
    </tr>
    <tr>
      <td class="leftc">{icon:bets} {lang:coins_total}</td>
      <td class="rightc">=</td>
      <td class="leftb"><input type="text" name="coins_total" value="{coins:coins_total}" maxlength="15" size="10" /></td>
    </tr>
    <tr>
      <td class="leftc">{icon:ksysguard} {lang:options}</td>
      <td class="rightc"></td>
      <td class="leftb"><input type="hidden" name="id" value="{coins:coins_id}" />
        <input type="submit" name="submit" value="{lang:edit}" />
      </td>
    </tr>
  </table>
</form>
