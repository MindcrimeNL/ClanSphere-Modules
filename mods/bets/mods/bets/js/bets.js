function cs_bet_add_contestant(objButton)
{
    tmpNode=objButton.parentNode.getElementsByTagName('table')[1].cloneNode(true);
	objButton.parentNode.insertBefore(tmpNode,objButton);
	tmpNode.getElementsByTagName('input')[0].value = '';
	tmpNode.getElementsByTagName('input')[1].value = '0';
	extra = tmpNode.getElementsByTagName('input')[2];
	if (extra)
		extra.value = '2';
}

function cs_bet_remove_contestant(objButton)
{
	var divEles = objButton.parentNode.getElementsByTagName('table');
	var numberOpps = divEles.length;
	if(numberOpps > 2) {
		objButton.parentNode.removeChild(divEles[numberOpps-1]);
	}
}

function cs_bet_toggle_quote(objButton)
{
	var quote_type = parseInt(objButton.options[objButton.selectedIndex].value);

	var searchTd = document.getElementById('check_tr');
	var inputEles = searchTd.getElementsByTagName('tr');
	for (i=0; i<inputEles.length; i++)
	{
		switch (quote_type)
		{
		case 0:
			if(inputEles[i].className=='bet_quote')
			{
				inputEles[i].style.visibility = 'collapse';
			}
			break;
		case 1:
		case 2:
			if(inputEles[i].className=='bet_quote')
			{
				inputEles[i].style.visibility = 'visible';
			}
			break;
		}
	}	
}
