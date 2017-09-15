function show_answer(answer) {
	document.getElementById('yes').style.display = 'none';
	document.getElementById('no').style.display = 'none';

	document.getElementById(answer).style.display = 'block';

	document.getElementById('next_button').disabled = false;
}
