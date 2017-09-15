function open_text(id) {
	for (i = 1; i <= 75; i++) {
		if (i != id) {
			$(".extra_" + i).slideUp(0);
		}
	}

	$(".extra_" + id).slideToggle(0);
}


var jqxhr;
function save_input(case_id, field_id) {
	if ((field = document.getElementById(field_id)) == undefined) {
		alert("Unknown input field: " + field_id);
		return;
	}

	jqxhr = $.post("/dreigingen", {
			case_id: case_id,
			key: field_id,
			value: field.value
		}, save_input_oke).error(function() {
			save_failed();
		});
}

function save_input_oke(data, text_status, jq_xhr) {
	if ((key = $(data).find("key")) == undefined) {
		save_failed();
	} else if (key.text() == "") {
		save_failed();
	} else if ((result = $(data).find("result")) == undefined) {
		save_failed();
	} else if (result.text() != "oke") {
		alert("Door een fout op de server kon de informatie niet worden opgeslagen!");
	} else if ((field = document.getElementById(key.text())) == undefined) {
		save_failed();
	} else {
		$(field).effect("highlight", {color:"#a0ffa0"}, 500);
	}
}

function save_bia_threat(case_id, bia_id, threat_id) {
	jqxhr = $.post("/dreigingen", {
			case_id: case_id,
			bia_id: bia_id,
			threat_id: threat_id
		}, save_bia_threat_oke).error(function() {
			save_failed();
		});
}

function save_bia_threat_oke(data, text_status, jq_xhr) {
	if ((key = $(data).find("key")) == undefined) {
		save_failed();
	} else if (key.text() == "") {
		save_failed();
	} else if ((result = $(data).find("result")) == undefined) {
		save_failed();
	} else if (result.text() != "oke") {
		alert("Door een fout op de server kon de informatie niet worden opgeslagen!");
	} else if ((field = document.getElementById(key.text())) == undefined) {
		save_failed();
	} else {
		$(field).effect("highlight", {color:"#a0ffa0"}, 500);
	}
}

function save_failed() {
	alert("Door een verbindingsfout kon de informatie niet worden opgeslagen!");
}

$(document).ready(function() {
	$("div.js_warning").hide();
});
