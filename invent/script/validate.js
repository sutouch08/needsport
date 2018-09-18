// JavaScript Document
function addError(el, label, message){
	el.addClass("has-error");
	label.addClass("red");
	label.text(message);
}


function removeError(el, label, message){
	el.removeClass("has-error");
	label.removeClass("red");
	label.text(message);	
}


