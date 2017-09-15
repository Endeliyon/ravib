var allow_edit = true;

function edit_progress(case_id, id) {
	if (allow_edit) {
		document.location = '/voortgang/'+case_id+'/'+id;
	}

	allow_edit = true;
}

function show_dialog(id) {
	allow_edit = false;

	$('div#info_'+id).dialog({
		width:350,
		height:250
	});
}
