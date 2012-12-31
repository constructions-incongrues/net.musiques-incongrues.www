var input, BBBarColorPicker, BBBarFontPicker;

var opentag = '[';
var closetag = ']';

function hide_and_focus(givefocus){
	if( !input ) input = document.getElementById("CommentBox");	
	if( !BBBarColorPicker ) BBBarColorPicker = document.getElementById("ForcedBBBarColorPicker");
	if( !BBBarFontPicker ) BBBarFontPicker = document.getElementById("ForcedBBBarFontPicker");
	
	BBBarColorPicker.style.display = 'none';
	BBBarFontPicker.style.display = 'none';
	if( givefocus ) input.focus();	
}

function insert(aTag, eTag) {
	hide_and_focus(1);
	
	/* für Internet Explorer */
	if(typeof document.selection != 'undefined') {
		/* Einfügen des Formatierungscodes */
		var range = document.selection.createRange();
		var insText = range.text;
		range.text = opentag + aTag + closetag + insText + opentag + eTag + closetag;
		/* Anpassen der Cursorposition */
		range = document.selection.createRange();
		if (insText.length == 0) {
			range.move('character', -eTag.length);
		}
		else {
			range.moveStart('character', aTag.length + insText.length + eTag.length);      
		}
		range.select();
	}
	/* für neuere auf Gecko basierende Browser */
	else if(typeof input.selectionStart != 'undefined') {
		/* Einfügen des Formatierungscodes */
		var start = input.selectionStart;
		var end = input.selectionEnd;
		var insText = input.value.substring(start, end);
		input.value = input.value.substr(0, start) + opentag + aTag + closetag + insText + opentag + eTag + closetag + input.value.substr(end) ;
		/* Anpassen der Cursorposition */
		var pos;
		if (insText.length == 0) {
			pos = start + aTag.length+opentag.length+closetag.length;
		} else {
			pos = start + aTag.length + insText.length + eTag.length;
		}
		input.selectionStart = pos;
		input.selectionEnd = pos;
	}
	/* für die übrigen Browser */
	else {
		/* Abfrage der Einfügeposition */
		var pos;
		var re = new RegExp('^[0-9]{0,3}$');
		while(!re.test(pos)) {
			pos = prompt("Einfügen an Position (0.." + input.value.length + "):", "0");
		}
		if(pos > input.value.length) {
			pos = input.value.length;
		}
		/* Einfügen des Formatierungscodes */
		var insText = prompt("Please insert the text that should be formatted:");
		input.value = input.value.substr(0, pos) + openTag + aTag + closeTag + insText + openTag + eTag + closeTag + input.value.substr(pos);
	}
}

function insertMail(){
	hide_and_focus(1);
	var start = input.selectionStart;
	var end   = input.selectionEnd;
	var insText = input.value.substring(start, end);
	linkZiel = 'mailto://' + prompt("Input the E-Mail Address","");
	if (insText == '') linkBeschreibung = prompt("Input the title for this Mail Link","");
	if (linkBeschreibung == '') linkBeschreibung = linkZiel;  
	if (linkZiel != '')	{
		if (linkZiel != '' & linkZiel != 'mailto:' & linkZiel != 'NULL'){ 
			if (insText != '') input.value = input.value.substr(0, start) + '[url=' + linkZiel + ']'+ insText + '[/url]' + input.value.substr(end);
			if (insText == '') input.value = input.value.substr(0, start) + '[url=' + linkZiel + ']'+ linkBeschreibung + '[/url]' + input.value.substr(end);
		}
	}
}

function insertURL(){
	hide_and_focus(1);
	var start = input.selectionStart;
	var end   = input.selectionEnd;
	var insText = input.value.substring(start, end);
	var link_http = prompt("Input the destination for this Link","http://");
	if (link_http != '' & link_http != 'http://' & link_http != 'NULL') {
		if( insText != '' ) {
			input.value = input.value.substr(0, start) + '[url=' + link_http + ']'+ insText + '[/url]' + input.value.substr(end);
		} else {
			var link_text = prompt("Input the Title of this Link","");
			if (link_text == '') link_text = link_http;
			input.value = input.value.substr(0, start) + '[url=' + link_http + ']'+ link_text + '[/url]' + input.value.substr(end);
		}
	}
}


function insertImage(){
	hide_and_focus(1);
	image = prompt("Input the link where the image is hosted:","http://www.");
    var start = input.selectionStart;
    var end = input.selectionEnd;
	var insText = input.value.substring(start, end);
	if (image != 'http://www.' & image != '' & image != 'NULL')
		input.value = input.value.substr(0, start) + '[img]' + image + '[/img]' + input.value.substr(end);
}

function list(type){
	hide_and_focus(1);
    var start = input.selectionStart;
    var end = input.selectionEnd;
	var insText = input.value.substring(start, end);
	if (type == 'u')
		input.value = input.value.substr(0, start) + '[ulist]\n[li]'+ insText + '[/li]\n[/ulist]' + input.value.substr(end);
	if (type == 'o')
		input.value = input.value.substr(0, start) + '[list]\n[li]'+ insText + '[/li]\n[/list]' + input.value.substr(end);
}

function showColor(){
	hide_and_focus(0);
	BBBarColorPicker.style.display = '';
}

function writeColor(color){
	hide_and_focus(1);
	insert('color=#'+color, '/color');
}

function showFont(){
	hide_and_focus(0);
	BBBarFontPicker.style.display = '';
}

function writeFont(font){
	hide_and_focus(1);
	insert('font=#'+font, '/font');
}