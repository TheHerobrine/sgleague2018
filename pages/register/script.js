if (!window.GLOBALS)
{
	const GLOBALS = {
		currentDropdownSuggestionNode: null
	};
	window.GLOBALS = GLOBALS;
}

function queryParams(params)
{
	return Object.keys(params)
		.map(k => encodeURIComponent(k) + '=' + encodeURIComponent(params[k]))
		.join('&');
}

function doRequest(url, options={})
{
	options = Object.assign({}, {
		credentials: 'same-origin',
		redirect: 'error'
	}, options);

	if (options.queryParams) {
		url += (url.indexOf('?') == -1 ? '?' : '&') + queryParams(options.queryParams);
		delete options.queryParams;
	}

	return fetch(url, options);
}

function getAutocompletionForSchool(school)
{
	return doRequest('api.php', {
		queryParams: {
			'type': 'search_school',
			'school': school
		}
	})
	.then(response => response.json())
}

function debounce(func, wait=0, immediate=false) {
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

function buildPrettyTextNode(main, desc)
{
	const loginNode = document.createTextNode(main);
	const schoolNode = document.createTextNode(desc);

	const container = document.createElement('div');
	const loginContainer = document.createElement('span');
	const schoolContainer = document.createElement('span');
	schoolContainer.setAttribute('class', 'subdrop');

	loginContainer.appendChild(loginNode);
	schoolContainer.appendChild(schoolNode);

	container.appendChild(loginContainer);
	container.appendChild(schoolContainer);

	return container;
}

function selectCurrentSuggestion(school, textInput)
{
	return evt => {
		console.log(school);
		textInput.value = school.S_NAME;

		setTimeout(() => {
				textInput.parentNode.removeChild(GLOBALS.currentDropdownSuggestionNode);
				GLOBALS.currentDropdownSuggestionNode = null;
		});
	}
}

function createOrChangeCurrentSuggestions(parentNode, textInput, currentContent, schools)
{
	const container = GLOBALS.currentDropdownSuggestionNode || document.createElement('ul');
	d3.select(container).classed("dropdown", true).style("width", "522px");

	const nodes = schools ? schools.map(school => {
		const node = document.createElement('li');
		node.appendChild(buildPrettyTextNode(school.S_NAME, school.S_COUNTRY));
		node.onmousedown = selectCurrentSuggestion(school, textInput);
		return node
	}) : [];

	// Inject to parent.
	// Reset the HTML.
	container.innerHTML = "";
	if ((currentContent !== "") && (nodes.length > 0))
	{
		if (!GLOBALS.currentDropdownSuggestionNode)
		{
			parentNode.insertBefore(container, textInput.nextSibling);
		}

		GLOBALS.currentDropdownSuggestionNode = container;

		for (let i = 0 ; i < nodes.length ; i++)
		{
			container.appendChild(nodes[i]);
		}
	}
	else
	{
		if (GLOBALS.currentDropdownSuggestionNode != null)
		{
			setTimeout(() => {
				textInput.parentNode.removeChild(GLOBALS.currentDropdownSuggestionNode);
				GLOBALS.currentDropdownSuggestionNode = null;
			});
		}
	}
}

function fillAutocompletion(getTextContent, parentNode, textInput) {
	return function ()
	{
		getAutocompletionForSchool(getTextContent()).then(schools => {
			createOrChangeCurrentSuggestions(parentNode, textInput, getTextContent(), schools);
		});
	}
}

window.onload = function()
{
	textInput = d3.select("#school_form").node();

	getText = () => textInput.value;

	autocomplete = debounce(fillAutocompletion(getText, textInput.parentNode, textInput), 300);

	textInput.addEventListener('keydown', autocomplete);
	textInput.addEventListener('focus', autocomplete);
}