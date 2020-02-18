function ajaxGetResponse(url, method, data, functionName, async = true) {
	let xhttp = new XMLHttpRequest();
	xhttp.open(method, url, async);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(data);
	xhttp.onreadystatechange = function(){
		if (this.readyState == 4 && this.status == 200) {
			functionName(this.responseText);
		} else return false;
	}
}