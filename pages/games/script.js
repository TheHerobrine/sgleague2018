function validateMail(mail)
{
    var regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return regex.test(mail.toLowerCase());
}

function debounce(func, wait=0, immediate=false)
{
	var timeout;
	return function() {
		var context = this, args = arguments;
		clearTimeout(timeout);
		timeout = setTimeout(function() {
			timeout = null;
			if (!immediate) func.apply(context, args);
		}, wait);
		if (immediate && !timeout) func.apply(context, args);
	};
}

function checkMail(element,type)
{
	return function ()
	{
		displayAdd = false;
		mail = element.value;

		if(validateMail(mail))
		{
			displayAdd = true;
		}

		parent = element.parentNode;

		if(displayAdd && (d3.select(parent).selectAll(".addbutton").node() == null))
		{			
			d3.select(parent)
				.append("span")
				.classed("addbutton", true)
				.node().innerHTML = '[ <span onclick="sendMail(this, '+type+')">Ajouter</span> ]';
		}
		else if (!displayAdd && (d3.select(parent).selectAll(".addbutton").node() != null))
		{
			d3.select(parent).selectAll(".addbutton").remove();
		}
	}
}

function addPlayer(element, type)
{
	parent = d3.select(element).node().parentNode;
	d3.select(element).remove();
	element = d3.select(parent)
		.append("input")
		.classed("addplayer", true)
		.attr("placeholder", "Adresse mail")
		.node();

	eventCheck = debounce(checkMail(element, type), 300);

	element.addEventListener('keydown', eventCheck);
	element.addEventListener('focus', eventCheck);
	element.focus();
}

function sendMail(element, type)
{
	mail = d3.select(element.parentNode.parentNode).selectAll(".addplayer").node().value;
	window.location = window.location+"&action=add&type="+type+"&mail="+mail;
}