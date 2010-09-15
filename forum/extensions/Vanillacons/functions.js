function changeSmilies(txtSection) {
	var txtContent = "<ul>";
	for(i=0; i<arrSmilies[txtSection].length; i++) {
		txtContent = txtContent + "<li>" + arrSmilies[txtSection][i] + "</li>";
	}
	txtContent = txtContent + "</ul>";
	document.getElementById("VanillaconsSmilies").innerHTML = txtContent;
}

function insertSmilie(txtSmilie) {
	var ComboBox = document.getElementById("CommentBox");
	ComboBox.value = ComboBox.value + ":" + txtSmilie + ":";
}