$(window).ready(function() {
	$("form > ul").menuEditor();

	button = $("form input.insert").detach();
	$("form div.btn-group").append(button);
});
