$(document).ready(function() {
	$('.show-message-link').magnificPopup({
		type: 'inline',
		preloader: false
	});
	$("#search-input").keyup(function(event){
		if(event.keyCode == 13){
			event.preventDefault();
			$("#search-btn").click();
		}
	});
	$("#comment-input").keydown(function(event){
		if(event.keyCode == 13){
			if (event.shiftKey==0) {
				event.preventDefault();
				$("#comment-btn").click();
			}
		}
	});
});
function getNewComms(response) {
	let commsOutput = document.querySelector("#comment-output");
	commsOutput.outerHTML = response;
}
// Добавление комментария
let commentForm = document.querySelector("#comment-form");
commentForm.onsubmit = function(event){
	event.preventDefault();
	let method = commentForm.method;
	let url = commentForm.action;
	let data = serialize(this);
	let response = ajaxGetResponse(url, method, data, getNewComms);
	document.querySelector("#comment-input").value = "";
}
// Удаление комментария
function delCommFormsAddEvent() {
	let delCommForms = document.querySelectorAll(".comment-delete-form");
	for (let i = 0; i < delCommForms.length; i++) {
		delCommForms[i].onsubmit = function(event){
			event.preventDefault();
			let method = this.method;
			let url = this.action;
			let data = serialize(this);
			let response = ajaxGetResponse(url, method, data, getNewComms);
		}
	}
}
delCommFormsAddEvent();
document.querySelector(".comments").addEventListener("DOMSubtreeModified", function() {
	delCommFormsAddEvent();
});