function show_info(graph_id, text, value) {
	if ((text_div = document.getElementById('text_' + graph_id)) == undefined) {
		return;
	}
	if ((value_div = document.getElementById('value_' + graph_id)) == undefined) {
		return;
	}

	text_div.innerHTML = text;
	value_div.innerHTML = value;
}
