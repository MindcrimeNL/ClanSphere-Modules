function teamspeak_submenu(id,code)
{
  var newcode = code.replace(/&quote;/g, "\"");
  newcode = newcode.replace(/\\'/g, "'");
  newcode = newcode.replace(/\n/g, "<br />");
  document.getElementById(id).innerHTML= newcode;
}