function getNewComms(response) {
	document.querySelector("#comment-output").outerHTML = response;
}

function updateCommentsFetch(event) {
	event.preventDefault();

	let method = this.method;
	let url = this.action;
	let data = serialize(this);

	fetch(url, {
		method: method,
		headers: {
			"Content-type": "application/x-www-form-urlencoded"
		},
		body: data
	})
	.then(response => response.text())
	.then(result => {
		getNewComms(result);
	});

	if (this[0].nodeName == "TEXTAREA" && this[0].id == "comment-input") {
		this[0].value = "";
	}
}

function delCommFormsAddEvent() {
	let delCommForms = document.querySelectorAll(".comment-delete-form");
	for (let i = 0; i < delCommForms.length; i++) {
		delCommForms[i].onsubmit = updateCommentsFetch;
	}
}

// Добавление комментария
let commentForm = document.querySelector("#comment-form");
commentForm.onsubmit = updateCommentsFetch;

commentForm.onkeypress = function(event) {
	if (event.keyCode == 13){
		if (event.shiftKey == 0) {
			event.preventDefault();
			document.querySelector("#comment-btn").click();
		}
	}
}

document.querySelector("#search-input").onkeypress = function(event) {
	if (event.keyCode == 13){
		event.preventDefault();
		document.querySelector("#search-btn").click();
	}
}