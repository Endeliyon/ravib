function show_threats() {
	$("#threat").addClass("selected");
	$("#measure").removeClass("selected");

	$(".threats").show();
	$(".measures").hide();
}

function show_measures() {
	$("#threat").removeClass("selected");
	$("#measure").addClass("selected");

	$(".threats").hide();
	$(".measures").show();
}
