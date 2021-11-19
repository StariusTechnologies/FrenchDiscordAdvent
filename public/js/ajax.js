function ajaxGet(url, callback){
	let request = new XMLHttpRequest();
	request.open("GET", url);
	request.addEventListener("load", function(){
		if(request.status >= 200 && request.status < 400){
			callback(request.responseText);
		}else{
			console.error(request.status + " " + request.statusText + " " + url);
		}
	});
	request.addEventListener("error", function(){
		console.error("erreur réseau avec l'url : " + url);
	});
	request.send(null);
}

function ajaxPost(url, data, callback, isJson){
	let request = new XMLHttpRequest();
	request.open("POST", url);
	request.addEventListener("load", function(){
		if(request.status >= 200 && request.status < 400){
			callback(request.responseText);
		}else{
			console.error(request.status + " " + request.statusText + " " + url);
		}
	});
	request.addEventListener("error", function(){
		console.error("erreur réseau avec l'url : " + url);
	});
	if(isJson){
		request.setRequestHeader("Content-Type", "application/json");
		data= JSON.stringify(data);
	}
	request.send(data);
}