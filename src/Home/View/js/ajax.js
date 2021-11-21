function ajaxGet(url, callback) {
	var request = new XMLHttpRequest();

	request.addEventListener('load', function() {
		if (request.status >= 200 && request.status < 400) {
			callback(request.responseText);
		} else {
			console.error(request.status + ' ' + request.statusText + ' ' + url);
		}
	});

	request.addEventListener('error', function(){
		console.error('Network error for URL ' + url);
	});

	request.open('GET', url);
	request.send(null);
}

function ajaxPost(url, data, callback, isJson) {
	var request = new XMLHttpRequest();

	request.addEventListener('load', function() {
		if (request.status >= 200 && request.status < 400) {
			callback(request.responseText);
		} else {
			console.error(request.status + ' ' + request.statusText + ' ' + url);
		}
	});

	request.addEventListener('error', function() {
		console.error('Network error for URL ' + url);
	});

	if (isJson) {
		request.setRequestHeader('Content-Type', 'application/json');
		data = JSON.stringify(data);
	}

	request.open('POST', url);
	request.send(data);
}